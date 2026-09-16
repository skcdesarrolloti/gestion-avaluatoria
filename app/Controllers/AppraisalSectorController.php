<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Core\Http;
use App\Core\Session;
use App\Models\AppraisalRepository;
use App\Models\AppraisalSectorRepository;
use App\Models\AppraisalSubjectRepository;
use App\Models\NeighborhoodSectorRepository;
use App\Services\AppraisalSectorInput;
use App\Services\AppraisalSectorPrefill;
use App\Support\AppraisalSectorCatalog;

final class AppraisalSectorController
{
    public function __construct(
        private AppraisalRepository $appraisals,
        private AppraisalSectorRepository $sectors,
        private AppraisalSubjectRepository $subjects,
        private NeighborhoodSectorRepository $neighborhoodSectors,
        private array $user
    ) {}

    public function show(string $id): void
    {
        $record = $this->appraisals->find($id, $this->user['id']);
        $subject = $this->subjects->find($id, $this->user['id']);
        $hasSector = $this->sectors->exists($id, $this->user['id']);
        if ($hasSector) {
            $sector = $this->sectors->find($id, $this->user['id']);
            [$source, $updatedAt] = ['expediente', $sector['updated_at'] ?? null];
        } else {
            [$sector, $source, $updatedAt] = $this->initialSector($id, $subject);
        }
        view('appraisals/sector', [
            'title' => 'Sector y entorno',
            'record' => $record,
            'sector' => $sector,
            'subject' => $subject,
            'sectorPrefilled' => $source !== 'expediente' && array_filter($sector) !== [],
            'sectorPrefillSource' => $source,
            'sectorUpdatedAt' => $updatedAt,
            'sectorSections' => AppraisalSectorCatalog::sections(),
            'sectorOptions' => AppraisalSectorCatalog::options(),
            'sectorHelps' => AppraisalSectorCatalog::helps(),
            'sectorMessage' => Session::pullFlash('sector_message'),
            'sectorError' => Session::pullFlash('sector_error'),
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
            Session::flash('sector_message', 'Numeral 2 guardado y banco barrial actualizado.');
        } catch (\Throwable $error) {
            Session::flash('sector_error', $error->getMessage());
        }
        $hash = preg_match('/^[a-z_]+$/', (string) ($_POST['active_sector'] ?? ''))
            ? '#' . (string) $_POST['active_sector']
            : '';
        Http::redirect('avaluos/' . $id . '/sector' . $hash);
    }

    private function initialSector(string $id, array $subject): array
    {
        $empty = $this->sectors->find($id, $this->user['id']);
        $master = $this->neighborhoodSectors->find((string) ($subject['neighborhood_id'] ?? ''));
        if ($master) {
            return [array_replace($empty, $master), 'banco_barrial', $master['updated_at'] ?? null];
        }
        return [array_replace($empty, AppraisalSectorPrefill::fromSubject($subject)), 'bien_sujeto', null];
    }
}
