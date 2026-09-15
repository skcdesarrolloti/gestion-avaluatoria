<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Models\LegalDocumentRepository;

final class LegalFrameworkController
{
    public function __construct(private LegalDocumentRepository $documents) {}

    public function index(): void
    {
        $categories = $this->documents->categoriesWithDocuments();
        $codes = array_column($categories, 'code');
        $requested = (string) ($_GET['categoria'] ?? '');
        $active = in_array($requested, $codes, true) ? $requested : (string) ($codes[0] ?? 'A');
        view('legal/index', [
            'title' => 'Marco Jurídico Nacional',
            'categories' => $categories,
            'activeCategoryCode' => $active,
            'legalStats' => $this->documents->stats($categories),
        ]);
    }
}
