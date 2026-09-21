<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Core\Http;
use App\Core\HttpException;
use App\Core\Session;
use App\Models\AppraisalPhRepository;
use App\Models\AppraisalRepository;
use App\Models\AppraiserRepository;
use App\Models\IgacTypologyRepository;
use App\Services\AppraisalChapterZeroInput;
use App\Services\AppraisalValidator;
use App\Support\AppraisalCatalog;

final class AppraisalController
{
    public function __construct(private AppraisalRepository $appraisals, private array $user,
        private AppraiserRepository $appraisers, private IgacTypologyRepository $typologies,
        private ?AppraisalPhRepository $ph = null) {}

    public function index(): void
    {
        $page = filter_var($_GET['page'] ?? 1, FILTER_VALIDATE_INT) ?: 1;
        $page = max(1, min(100000, $page));
        $rows = $this->appraisals->recent($this->user['id'], $page);
        view('appraisals/index', ['title' => 'Mis avalúos', 'rows' => array_slice($rows, 0, 20),
            'hasNext' => count($rows) > 20, 'page' => $page]);
    }

    public function create(): never
    {
        $id = $this->appraisals->create($this->user['id']);
        Http::redirect('avaluos/' . $id . '/expediente');
    }

    public function chapterZero(string $id): void
    {
        $record = $this->appraisals->find($id, $this->user['id']);
        view('appraisals/chapter-zero', ['title' => 'Expediente valuatorio', 'record' => $record,
            'appraisers' => $this->appraisers->all(),
            'chapterZeroMessage' => Session::pullFlash('chapter_zero_message'),
            'chapterZeroError' => Session::pullFlash('chapter_zero_error'),
            'catalog' => ['selects' => AppraisalCatalog::selectFields(), 'notes' => AppraisalCatalog::notes()]]);
    }

    public function sector(string $id): void { view('appraisals/sector', ['title' => 'Sector y entorno', 'record' => $this->appraisals->find($id, $this->user['id'])]); }
    public function deliverable(string $id): void
    {
        $record = $this->appraisals->find($id, $this->user['id']);
        view('appraisals/deliverable', ['title' => 'Entregable', 'record' => $record,
            'phProfile' => $this->ph?->profile($id, $this->user['id'])]);
    }

    public function saveChapterZero(string $id): never
    {
        try {
            $data = AppraisalChapterZeroInput::chapterZeroData((int) ($_POST['version'] ?? 0),
                $this->igacCodes(), $this->appraiserIds());
            $this->appraisals->saveChapterZero($id, $this->user['id'], (int) ($_POST['version'] ?? 0), $data);
            Session::flash('chapter_zero_message', 'Expediente guardado correctamente.');
            Http::redirect('avaluos/' . $id . ((string) ($_POST['next'] ?? '') === 'sector' ? '/sector' : ((string) ($_POST['next'] ?? '') === 'subject' ? '/bien-sujeto#atributos' : ((string) ($_POST['next'] ?? '') === 'deliverable' ? '/entregable' : '/expediente'))));
        } catch (\Throwable $error) {
            Session::flash('chapter_zero_error', $this->chapterZeroErrorMessage($error));
            Http::redirect('avaluos/' . $id . '/expediente');
        }
    }
    public function autosaveChapterZero(string $id): never
    {
        $data = AppraisalChapterZeroInput::chapterZeroData((int) ($_POST['version'] ?? 0),
            $this->igacCodes(), $this->appraiserIds());
        $result = $this->appraisals->saveChapterZero($id, $this->user['id'], (int) ($_POST['version'] ?? 0), $data);
        Http::json(['ok' => true] + $result);
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
    private function appraiserIds(): array { return array_column($this->appraisers->all(), 'id'); } private function igacCodes(): array { return array_column($this->typologies->categories(), 'code'); }
    private function chapterZeroErrorMessage(\Throwable $error): string
    {
        $reference = substr(hash('sha256', 'chapter_zero|' . $error->getMessage() . '|' . microtime(true)), 0, 12);
        error_log('Gestion avaluatoria expediente [' . $reference . '] '
            . get_class($error) . ' code=' . $error->getCode() . ' message=' . $error->getMessage());
        return $error instanceof HttpException
            ? $error->getMessage()
            : 'No fue posible guardar el expediente. Referencia: ' . $reference . '.';
    }
}
