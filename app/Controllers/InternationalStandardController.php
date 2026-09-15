<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Models\InternationalStandardRepository;

final class InternationalStandardController
{
    public function __construct(private InternationalStandardRepository $standards) {}

    public function index(): void
    {
        $groups = $this->standards->groupsWithStandards();
        $codes = array_column($groups, 'code');
        $requested = (string) ($_GET['grupo'] ?? '');
        $active = in_array($requested, $codes, true) ? $requested : (string) ($codes[0] ?? 'G');
        view('international/index', [
            'title' => 'Normas Internacionales de Valuación',
            'groups' => $groups,
            'activeGroupCode' => $active,
            'stats' => $this->standards->stats($groups),
        ]);
    }
}
