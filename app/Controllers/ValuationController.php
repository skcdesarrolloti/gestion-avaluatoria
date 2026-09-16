<?php
declare(strict_types=1);

namespace App\Controllers;

final class ValuationController
{
    public function index(): void
    {
        view('valuations/index', ['title' => 'Valuaciones']);
    }
}
