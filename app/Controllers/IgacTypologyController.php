<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Models\IgacTypologyRepository;
use App\Support\IgacDocumentLibrary;

final class IgacTypologyController
{
    public function __construct(private IgacTypologyRepository $typologies) {}

    public function index(): void
    {
        $categories = $this->typologies->categories();
        $codes = array_column($categories, 'code');
        $requested = (string) ($_GET['categoria'] ?? '');
        $active = in_array($requested, $codes, true) ? $requested : 'RESIDENCIALES';
        $documentCategories = IgacDocumentLibrary::categories();
        $documentCodes = array_column($documentCategories, 'code');
        $documentCategory = (string) ($_GET['documentos'] ?? 'todos');
        if (!in_array($documentCategory, $documentCodes, true)) $documentCategory = 'todos';
        view('typologies/igac', [
            'title' => 'Biblioteca IGAC',
            'categories' => $categories,
            'activeCategoryCode' => $active,
            'typologies' => $this->typologies->byCategory($active),
            'stats' => $this->typologies->stats(),
            'documentCategories' => $documentCategories,
            'activeDocumentCategory' => $documentCategory,
            'documents' => IgacDocumentLibrary::documents($documentCategory),
            'documentStats' => IgacDocumentLibrary::stats(),
        ]);
    }

    public function document(string $id): void
    {
        $document = IgacDocumentLibrary::find($id);
        if ($document === null) {
            throw new \App\Core\HttpException(404, 'No se encontró el documento IGAC.');
        }
        view('typologies/igac-document', ['title' => (string) $document['codigo'], 'document' => $document]);
    }
}
