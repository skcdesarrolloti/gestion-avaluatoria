<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Core\Env;
use App\Core\Http;
use App\Core\HttpException;
use App\Services\AuthDiagnostics;

final class DiagnosticController
{
    public function login(): void
    {
        if (!Env::bool('APP_DIAGNOSTICS')) {
            throw new HttpException(404, 'Página no encontrada.');
        }
        Http::json(['ok' => true, 'checks' => (new AuthDiagnostics())->run()]);
    }
}
