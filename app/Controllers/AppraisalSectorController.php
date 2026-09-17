<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Core\Http;
use App\Core\Session;
use App\Models\AppraisalRepository;
use App\Models\AppraisalSectorRepository;
use App\Models\AppraisalSubjectRepository;
use App\Models\GeoMasterRepository;
use App\Models\NeighborhoodSectorRepository;
use App\Models\SectorBankRepository;
use App\Services\AppraisalSectorInput;
use App\Services\AppraisalSectorPrefill;
use App\Services\AppraisalPhotoUploadService;
use App\Support\AppraisalSectorCatalog;
use App\Support\SectorBankCatalog;

final class AppraisalSectorController
{
    public function __construct(
        private AppraisalRepository $appraisals,
        private AppraisalSectorRepository $sectors,
        private AppraisalSubjectRepository $subjects,
        private NeighborhoodSectorRepository $neighborhoodSectors,
        private SectorBankRepository $sectorBank,
        private GeoMasterRepository $geo,
        private array $user
    ) {}

    public function show(string $id): void
    {
        $record = $this->appraisals->find($id, $this->user['id']);
        $subject = $this->subjects->find($id, $this->user['id']);
        $sectorError = Session::pullFlash('sector_error');
        $neighborhoodId = (string) ($subject['neighborhood_id'] ?? '');
        $master = $this->safeMasterSector($neighborhoodId, $sectorError);
        $hasSector = $this->sectors->exists($id, $this->user['id']);
        if ($hasSector) {
            $sector = $this->sectors->find($id, $this->user['id']);
            [$source, $updatedAt] = ['expediente', $sector['updated_at'] ?? null];
        } else {
            [$sector, $source, $updatedAt] = $this->initialSector($id, $subject, $master);
        }
        $bankSections = [];
        $bankSummary = ['total' => 0, 'ready' => 0, 'percent' => 0, 'level' => 'ROJO'];
        try {
            if ($neighborhoodId !== '') {
                $this->sectorBank->ensureSections($neighborhoodId, $subject, $sector);
            }
            $bankSections = $this->sectorBank->sections($neighborhoodId);
            $bankSummary = $this->sectorBank->summary($neighborhoodId);
        } catch (\Throwable $error) {
            $sectorError = $this->sectorWarning($sectorError, $error, 'sector_bank_show');
        }
        view('appraisals/sector', [
            'title' => 'Sector y entorno',
            'record' => $record,
            'sector' => $sector,
            'subject' => $subject,
            'sectorPrefilled' => $source !== 'expediente' && array_filter($sector) !== [],
            'sectorPrefillSource' => $source,
            'sectorUpdatedAt' => $updatedAt,
            'sectorNeighborhoods' => $this->geo->neighborhoods(),
            'sectorHasNeighborhoodBank' => (bool) $master,
            'sectorNeighborhoodUpdatedAt' => $master['updated_at'] ?? null,
            'sectorBankSections' => $bankSections,
            'sectorBankSummary' => $bankSummary,
            'sectorBankSources' => SectorBankCatalog::sources(),
            'photos' => $this->appraisals->photos($id, $this->user['id']),
            'photoMessage' => Session::pullFlash('sector_photo_message'),
            'photoError' => Session::pullFlash('sector_photo_error'),
            'sectorSections' => AppraisalSectorCatalog::sections(),
            'sectorOptions' => AppraisalSectorCatalog::options(),
            'sectorHelps' => AppraisalSectorCatalog::helps(),
            'sectorMessage' => Session::pullFlash('sector_message'),
            'sectorError' => $sectorError,
        ]);
    }

    public function save(string $id): never
    {
        $this->appraisals->find($id, $this->user['id']);
        try {
            $data = AppraisalSectorInput::data($_POST);
            $subject = $this->subjects->find($id, $this->user['id']);
            $this->sectors->save($id, $this->user['id'], $data);
            $this->neighborhoodSectors->save((string) ($subject['neighborhood_id'] ?? ''), $this->user['id'], $id, $data);
            $this->sectorBank->ensureSections((string) ($subject['neighborhood_id'] ?? ''), $subject, $data, true);
            $this->sectorBank->saveSnapshot($id, $this->user['id'], (string) ($subject['neighborhood_id'] ?? ''), $data);
            Session::flash('sector_message', 'Numeral 2 guardado y banco barrial actualizado.');
        } catch (\Throwable $error) {
            Session::flash('sector_error', $error->getMessage());
        }
        $hash = preg_match('/^[a-z_]+$/', (string) ($_POST['active_sector'] ?? ''))
            ? '#' . (string) $_POST['active_sector']
            : '';
        Http::redirect('avaluos/' . $id . '/sector' . $hash);
    }

    public function selectNeighborhood(string $id): never
    {
        $this->appraisals->find($id, $this->user['id']);
        try {
            $neighborhoodId = $this->resolveNeighborhoodId();
            if ($neighborhoodId === '') {
                throw new \InvalidArgumentException('Selecciona un barrio válido.');
            }
            $subject = $this->subjects->find($id, $this->user['id']);
            $subject['neighborhood_id'] = $neighborhoodId;
            $this->subjects->save($id, $this->user['id'], $subject);
            $subject = $this->subjects->find($id, $this->user['id']);
            $warning = null;
            $master = $this->safeMasterSector($neighborhoodId, $warning);
            $data = $master ? AppraisalSectorInput::data($master)
                : AppraisalSectorInput::data(AppraisalSectorPrefill::fromSubject($subject));
            $this->sectors->save($id, $this->user['id'], $data);
            try {
                $this->sectorBank->ensureSections($neighborhoodId, $subject, $data);
                $this->sectorBank->saveSnapshot($id, $this->user['id'], $neighborhoodId, $data);
            } catch (\Throwable $error) {
                $warning = $this->sectorWarning($warning, $error, 'sector_bank_select');
            }
            Session::flash('sector_message', $master
                ? 'Barrio cargado desde el banco barrial.'
                : 'Barrio sin ficha guardada: se preparó una generación inicial para completar.');
            if ($warning) Session::flash('sector_error', $warning);
        } catch (\Throwable $error) {
            Session::flash('sector_error', $error->getMessage());
        }
        Http::redirect('avaluos/' . $id . '/sector');
    }

    public function uploadPhotos(string $id): never
    {
        $record = $this->appraisals->find($id, $this->user['id']);
        try {
            $caption = mb_substr(trim((string) ($_POST['photo_caption'] ?? 'sector')), 0, 190);
            $displayName = mb_substr(trim((string) ($_POST['photo_name'] ?? '')), 0, 190);
            $count = (new AppraisalPhotoUploadService())->store($_FILES['photos'] ?? [], $record['id'],
                $this->user['id'], $this->appraisals, null, $caption, $displayName);
            Session::flash('sector_photo_message', $count === 1
                ? 'Imagen sectorial cargada correctamente.'
                : $count . ' imágenes sectoriales cargadas correctamente.');
        } catch (\Throwable $error) {
            Session::flash('sector_photo_error', $error->getMessage());
        }
        Http::redirect($this->safeReturn($id));
    }

    private function initialSector(string $id, array $subject, ?array $master): array
    {
        $empty = $this->sectors->find($id, $this->user['id']);
        if ($master) {
            return [array_replace($empty, $master), 'banco_barrial', $master['updated_at'] ?? null];
        }
        return [array_replace($empty, AppraisalSectorPrefill::fromSubject($subject)), 'bien_sujeto', null];
    }

    private function resolveNeighborhoodId(): string
    {
        $neighborhoodId = trim((string) ($_POST['neighborhood_id'] ?? ''));
        if ($neighborhoodId !== '') return mb_substr($neighborhoodId, 0, 80);

        $query = mb_strtolower(trim((string) ($_POST['neighborhood_query'] ?? '')));
        if ($query === '') return '';

        $matches = [];
        foreach ($this->geo->neighborhoods() as $item) {
            $label = mb_strtolower(trim(implode(' · ', array_filter([
                $item['name'] ?? '',
                $item['locality_name'] ?? '',
                $item['commune_ucg'] ?? '',
                $item['city_name'] ?? '',
            ]))));
            $name = mb_strtolower((string) ($item['name'] ?? ''));
            if ($label === $query || $name === $query) return (string) $item['id'];
            if (str_contains($label, $query) || str_contains($name, $query)) {
                $matches[(string) $item['id']] = (string) $item['id'];
            }
        }

        return count($matches) === 1 ? array_key_first($matches) : '';
    }

    private function safeMasterSector(string $neighborhoodId, ?string &$sectorError): ?array
    {
        try {
            return $this->neighborhoodSectors->find($neighborhoodId);
        } catch (\Throwable $error) {
            $sectorError = $this->sectorWarning($sectorError, $error, 'sector_master_profile');
            return null;
        }
    }

    private function safeReturn(string $id): string
    {
        $target = (string) ($_POST['return_to'] ?? '');
        return preg_match('#^avaluos/' . preg_quote($id, '#') . '/sector(?:\#[a-z_]+)?$#', $target)
            ? $target
            : 'avaluos/' . $id . '/sector#localizacion';
    }

    private function sectorWarning(?string $current, \Throwable $error, string $context): string
    {
        $reference = substr(hash('sha256', $context . '|' . $error->getMessage() . '|' . microtime(true)), 0, 12);
        error_log('Gestion avaluatoria sector [' . $reference . '] ' . $context . ' '
            . get_class($error) . ' code=' . $error->getCode() . ' message=' . $error->getMessage());
        $message = 'No fue posible cargar completamente el banco sectorial avanzado. '
            . 'La ficha base del sector sigue disponible. Referencia: ' . $reference . '.';
        return $current ? $current . ' ' . $message : $message;
    }
}
