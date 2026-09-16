<?php
declare(strict_types=1);

namespace App\Controllers;

final class MasterDataController
{
    public function index(): void
    {
        view('masters/index', ['title' => 'Creación de Maestros']);
    }
}
