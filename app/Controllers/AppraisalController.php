<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Core\Http;
use App\Core\HttpException;
use App\Core\Session;
use App\Models\AppraisalObsolescenceRepository;
use App\Models\AppraisalPhRepository;
use App\Models\AppraisalRepository;
use App\Models\AppraisalReportNoteRepository;
use App\Models\AppraisalSectorRepository;
use App\Models\AppraisalSectorSectionRepository;
use App\Models\AppraisalSubjectRepository;
use App\Models\AppraiserRepository;
use App\Models\IgacTypologyRepository;
use App\Services\AppraisalChapterOneReport;
use App\Services\AppraisalSectorChapterReport;
use App\Services\AppraisalDossierNumberer;
use App\Services\AppraisalChapterZeroInput;
use App\Services\AppraisalSubjectChapterReport;
use App\Services\AppraisalReportNoteIntegrator;
use App\Services\AppraisalValidator;
use App\Support\AppraisalCatalog;
use App\Support\AppraisalReportNoteCatalog;

final class AppraisalController
{
    public function __construct(private AppraisalRepository $appraisals, private array $user,
        private AppraiserRepository $appraisers, private IgacTypologyRepository $typologies,
        private ?AppraisalPhRepository $ph = null, private ?AppraisalSubjectRepository $subjects = null,
        private ?AppraisalObsolescenceRepository $obsolescence = null, private ?AppraisalDossierNumberer $dossiers = null,
        private ?AppraisalSectorRepository $sectors = null, private ?AppraisalSectorSectionRepository $sectorSections = null,
        private ?AppraisalReportNoteRepository $reportNotes = null) {}

    public function index(): void
    {
        $page = filter_var($_GET['page'] ?? 1, FILTER_VALIDATE_INT) ?: 1;
        $page = max(1, min(100000, $page));
        $search = trim((string) ($_GET['q'] ?? ''));
        $rows = $this->appraisals->recent($this->user['id'], $page, $search);
        view('appraisals/index', ['title' => 'Mis avalúos', 'rows' => array_slice($rows, 0, 20),
            'hasNext' => count($rows) > 20, 'page' => $page, 'search' => $search]);
    }

    public function create(): never
    {
        $id = $this->appraisals->create($this->user['id']);
        Http::redirect('avaluos/' . $id . '/expediente');
    }

    public function chapterZero(string $id): void
    {
        $record = $this->appraisals->find($id, $this->user['id']);
        $dossierSearch = trim((string) ($_GET['expediente_q'] ?? ''));
        view('appraisals/chapter-zero', ['title' => 'Expediente valuatorio', 'record' => $record,
            'appraisers' => $this->appraisers->eligibleForAssignment(),
            'dossierSearch' => $dossierSearch,
            'dossierRows' => array_slice($this->appraisals->recent($this->user['id'], 1, $dossierSearch, true), 0, 12),
            'reportNotes' => $this->reportNotes?->byChapter($id, $this->user['id'], '1') ?? [],
            'reportNoteSections' => AppraisalReportNoteCatalog::withCustom('1',
                $this->reportNotes?->customSections($id, $this->user['id'], '1') ?? []),
            'reportNoteChapter' => '1',
            'reportNoteReturn' => 'avaluos/' . $id . '/expediente#identificacion',
            'chapterZeroMessage' => Session::pullFlash('chapter_zero_message'),
            'chapterZeroError' => Session::pullFlash('chapter_zero_error'),
            'catalog' => ['selects' => AppraisalCatalog::selectFields(), 'notes' => AppraisalCatalog::notes()]]);
    }

    public function sector(string $id): void { view('appraisals/sector', ['title' => 'Sector y entorno', 'record' => $this->appraisals->find($id, $this->user['id'])]); }
    public function deliverable(string $id): void
    {
        $record = $this->appraisals->find($id, $this->user['id']);
        $phProfile = $this->ph?->profile($id, $this->user['id']) ?? [];
        $subject = $this->subjects?->find($id, $this->user['id']) ?? [];
        $units = $this->appraisals->units($id, $this->user['id']);
        $obsolescence = $this->obsolescence?->find($id, $this->user['id']) ?? [];
        $notes = $this->reportNotes?->byAppraisal($id, $this->user['id']) ?? [];
        $customSections = $this->reportNotes?->customSectionsByAppraisal($id, $this->user['id']) ?? [];
        $integrator = new AppraisalReportNoteIntegrator();
        $chapterOne = $integrator->apply((new AppraisalChapterOneReport())->build($record, $subject, $units),
            $this->chapterNotes($notes, '1'), $customSections['1'] ?? []);
        $sector = $this->sectors?->find($id, $this->user['id']) ?? [];
        $sectorRows = $this->sectorSections?->sections($id, $this->user['id']) ?? [];
        $sectorChapter = $integrator->apply((new AppraisalSectorChapterReport())->build($record, $subject, $sector, $sectorRows),
            $this->chapterNotes($notes, '2'), $customSections['2'] ?? []);
        $subjectChapter = $integrator->apply((new AppraisalSubjectChapterReport())->build($record, $subject, $units, $phProfile, $obsolescence),
            $this->chapterNotes($notes, '3'), $customSections['3'] ?? []);
        view('appraisals/deliverable', ['title' => 'Entregable', 'record' => $record,
            'phProfile' => $phProfile, 'chapterOne' => $chapterOne, 'sectorChapter' => $sectorChapter, 'subjectChapter' => $subjectChapter]);
    }

    public function saveChapterZero(string $id): never
    {
        try {
            $data = AppraisalChapterZeroInput::chapterZeroData((int) ($_POST['version'] ?? 0),
                $this->igacCodes(), $this->appraiserIds());
            $this->appraisals->saveChapterZero($id, $this->user['id'], (int) ($_POST['version'] ?? 0), $data);
            $dossier = $this->createDossierIfRequested($id);
            Session::flash('chapter_zero_message', $dossier ? 'Expediente ' . $dossier . ' creado correctamente.' : 'Expediente guardado correctamente.');
            Http::redirect($this->chapterZeroRedirect($id));
        } catch (\Throwable $error) {
            Session::flash('chapter_zero_error', $this->chapterZeroErrorMessage($error));
            Http::redirect('avaluos/' . $id . '/expediente');
        }
    }
    public function autosaveChapterZero(string $id): never
    {
        try {
            $data = AppraisalChapterZeroInput::chapterZeroData((int) ($_POST['version'] ?? 0),
                $this->igacCodes(), $this->appraiserIds());
            $result = $this->appraisals->saveChapterZero($id, $this->user['id'], (int) ($_POST['version'] ?? 0), $data);
            $dossier = $this->createDossierIfRequested($id);
            Http::json(['ok' => true, 'expediente_number' => $dossier] + $result);
        } catch (\Throwable $error) {
            Http::json(['ok' => false, 'message' => $this->chapterZeroErrorMessage($error)],
                $error instanceof HttpException ? $error->status : 500);
        }
    }
    public function edit(string $id): void
    {
        $record = $this->appraisals->find($id, $this->user['id']);
        view('appraisals/edit', ['title' => 'Ficha del avalúo', 'record' => $record,
            'catalog' => ['selects' => AppraisalCatalog::selectFields(), 'notes' => AppraisalCatalog::notes()]]);
    }
    public function save(string $id): never
    {
        $input = Http::input();
        $data = AppraisalValidator::validate($input);
        $result = $this->appraisals->save($id, $this->user['id'], $input['version'], $data);
        Http::json(['ok' => true] + $result);
    }

    private function chapterZeroRedirect(string $id): string
    {
        $next = (string) ($_POST['next'] ?? '');
        if ($next === 'sector') return 'avaluos/' . $id . '/sector';
        if ($next === 'subject') return 'avaluos/' . $id . '/bien-sujeto#atributos';
        if ($next === 'deliverable') return 'avaluos/' . $id . '/entregable';
        $section = (string) ($_POST['active_section'] ?? 'configuracion');
        return 'avaluos/' . $id . '/expediente#' . ($section === 'identificacion' ? 'identificacion' : 'configuracion');
    }
    private function appraiserIds(): array { return array_column($this->appraisers->eligibleForAssignment(), 'id'); }
    private function igacCodes(): array { return array_column($this->typologies->categories(), 'code'); }
    private function wantsDossierCreation(): bool { return (string) ($_POST['create_expediente'] ?? '') === '1'; }
    private function createDossierIfRequested(string $id): ?string
    {
        if (!$this->wantsDossierCreation()) return null;
        $number = $this->dossiers?->assignIfMissing($id, $this->user['id']);
        if (!$number) throw new HttpException(422, 'Selecciona un perito vigente para crear el expediente.');
        return $number;
    }
    private function chapterZeroErrorMessage(\Throwable $error): string
    {
        $reference = substr(hash('sha256', 'chapter_zero|' . $error->getMessage() . '|' . microtime(true)), 0, 12);
        error_log('Gestion avaluatoria expediente [' . $reference . '] '
            . get_class($error) . ' code=' . $error->getCode() . ' message=' . $error->getMessage());
        return $error instanceof HttpException
            ? $error->getMessage()
            : 'No fue posible guardar el expediente. Referencia: ' . $reference . '.';
    }
    private function chapterNotes(array $notes, string $chapter): array
    {
        return array_values(array_filter($notes, static fn (array $note): bool => (string) ($note['chapter_code'] ?? '') === $chapter));
    }
}
