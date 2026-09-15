<?php
declare(strict_types=1);
namespace App\Services;
use App\Core\HttpException;

final class RateLimiter
{
    public function __construct(private string $directory) {}

    public function consume(string $key, int $limit = 30, int $window = 900): void
    {
        if (!is_dir($this->directory) && !mkdir($this->directory, 0700, true) && !is_dir($this->directory)) {
            throw new \RuntimeException('No se pudo preparar el control de acceso.');
        }
        $handle = fopen($this->directory . '/' . hash('sha256', $key) . '.json', 'c+');
        if (!$handle || !flock($handle, LOCK_EX)) {
            throw new \RuntimeException('No se pudo verificar el limite de acceso.');
        }
        try {
            $state = json_decode(stream_get_contents($handle), true) ?: ['start' => time(), 'count' => 0];
            if (time() - $state['start'] >= $window) {
                $state = ['start' => time(), 'count' => 0];
            }
            if ($state['count'] >= $limit) {
                header('Retry-After: ' . max(1, $window - (time() - $state['start'])));
                throw new HttpException(429, 'Demasiados intentos. Intenta de nuevo en unos minutos.');
            }
            $state['count']++;
            rewind($handle);
            ftruncate($handle, 0);
            fwrite($handle, json_encode($state, JSON_THROW_ON_ERROR));
            fflush($handle);
        } finally {
            flock($handle, LOCK_UN);
            fclose($handle);
        }
    }
}
