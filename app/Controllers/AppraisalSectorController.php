<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Core\Http;
use App\Core\Session;
use App\Models\AppraisalRepository;
use App\Models\AppraisalSectorRepository;
use App\Models\AppraisalSubjectRepository;
use App\Services\AppraisalSectorInput;
use App\Support\AppraisalSectorCatalog;

final class AppraisalSectorController
{
    public function __construct(
        private AppraisalRepository $appraisals,
        private AppraisalSectorRepository $sectors,
        private AppraisalSubjectRepository $subjects,
        private array $user
    ) {}

    public function show(string $id): void
    {
        $record = $this->appraisals->find($id, $this->user['id']);
        view('appraisals/sector', [
            'title' => 'Sector y entorno',
            'record' => $record,
            'sector' => $this->sectors->find($id, $this->user['id']),
            'subject' => $this->subjects->find($id, $this->user['id']),
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
            $this->sectors->save($id, $this->user['id'], AppraisalSectorInput::data($_POST));
            Session::flash('sector_message', 'Numeral 2 guardado correctamente.');
        } catch (\Throwable $error) {
            Session::flash('sector_error', $error->getMessage());
        }
        $hash = preg_match('/^[a-z_]+$/', (string) ($_POST['active_sector'] ?? ''))
            ? '#' . (string) $_POST['active_sector']
            : '';
        Http::redirect('avaluos/' . $id . '/sector' . $hash);
    }
}
