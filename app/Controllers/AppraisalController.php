<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Core\Http;
use App\Models\AppraisalRepository;
use App\Services\AppraisalValidator;
use App\Support\AppraisalCatalog;

final class AppraisalController
{
    public function __construct(private AppraisalRepository $appraisals, private array $user) {}

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
        Http::redirect('avaluos/' . $id);
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
}
