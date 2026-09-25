<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Core\{Http, Session};
use App\Models\ValuationGlossaryRepository;

final class ValuationGlossaryController
{
    public function __construct(private ValuationGlossaryRepository $terms, private array $user) {}

    public function index(): void
    {
        $query = trim((string) ($_GET['q'] ?? ''));
        view('glossary/index', [
            'title' => 'Glosario valuatorio',
            'terms' => $this->terms->all($query),
            'stats' => $this->terms->stats(),
            'query' => $query,
            'glossaryMessage' => Session::pullFlash('glossary_message'),
            'glossaryError' => Session::pullFlash('glossary_error'),
        ]);
    }

    public function store(): never
    {
        try {
            $this->terms->create($_POST, (int) $this->user['id']);
            Session::flash('glossary_message', 'Concepto agregado al glosario valuatorio.');
        } catch (\Throwable $error) {
            Session::flash('glossary_error', $error->getMessage());
        }
        Http::redirect('glosario-valuatorio');
    }
}
