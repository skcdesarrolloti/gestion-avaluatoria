<?php
declare(strict_types=1);
require dirname(__DIR__) . '/bootstrap.php';

try {
    (new App\Core\Kernel())->run();
} catch (Throwable $error) {
    $known = $error instanceof App\Core\HttpException;
    $status = $known ? $error->status : 503;
    $message = $known ? $error->getMessage() : 'El servicio no está disponible. Intenta nuevamente o contacta al administrador.';
    if (!$known) {
        $reference = bin2hex(random_bytes(6));
        // Avoid logging submitted values, passwords or PDO messages containing credentials.
        error_log("Gestion avaluatoria [$reference] " . get_class($error) . ' code=' . $error->getCode()
            . ' at ' . basename($error->getFile()) . ':' . $error->getLine());
        $message .= ' Referencia: ' . $reference;
    }
    if (App\Core\Http::wantsJson()) {
        App\Core\Http::json(['ok' => false, 'message' => $message, 'errors' => $known ? $error->errors : []], $status);
    }
    http_response_code($status);
    view('error', ['title' => 'Aviso', 'message' => $message]);
}
