<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Models\IgacTypologyRepository;

final class IgacTypologyController
{
    public function __construct(private IgacTypologyRepository $typologies) {}

    public function index(): void
    {
        $categories = $this->typologies->categories();
        $codes = array_column($categories, 'code');
        $requested = (string) ($_GET['categoria'] ?? '');
        $active = in_array($requested, $codes, true) ? $requested : 'RESIDENCIALES';
        view('typologies/igac', [
            'title' => 'Tipologías Constructivas IGAC',
            'categories' => $categories,
            'activeCategoryCode' => $active,
            'typologies' => $this->typologies->byCategory($active),
            'stats' => $this->typologies->stats(),
        ]);
    }
}
