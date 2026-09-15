<?php
declare(strict_types=1);
namespace App\Database;
use PDO;

final class Migrator
{
    public function __construct(private PDO $db, private string $directory) {}

    public function run(): array
    {
        $files = glob($this->directory . '/*.php');
        sort($files, SORT_STRING);
        if (!$this->pending($files)) {
            return [];
        }
        $lockName = 'ga_migrate_' . substr(hash('sha256', (string) $this->db->query('SELECT DATABASE()')->fetchColumn()), 0, 40);
        $lock = $this->db->prepare('SELECT GET_LOCK(?, 15)');
        $lock->execute([$lockName]);
        if ((int) $lock->fetchColumn() !== 1) {
            throw new \RuntimeException('Otra actualización está en curso. Intenta nuevamente.');
        }
        try {
            $this->db->exec('CREATE TABLE IF NOT EXISTS schema_migrations (
                version VARCHAR(190) PRIMARY KEY, checksum CHAR(64) NOT NULL,
                applied_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');
            $applied = [];
            foreach ($this->pending($files) as $file) {
                $checksum = hash_file('sha256', $file);
                $migration = require $file;
                $migration(new Schema($this->db));
                $query = $this->db->prepare('INSERT INTO schema_migrations (version, checksum) VALUES (?, ?)');
                $query->execute([basename($file), $checksum]);
                $applied[] = basename($file);
            }
            return $applied;
        } finally {
            $release = $this->db->prepare('SELECT RELEASE_LOCK(?)');
            $release->execute([$lockName]);
        }
    }

    private function pending(array $files): array
    {
        try {
            $rows = $this->db->query('SELECT version, checksum FROM schema_migrations')->fetchAll(PDO::FETCH_KEY_PAIR);
        } catch (\PDOException $error) {
            if (($error->errorInfo[1] ?? null) !== 1146) {
                throw $error;
            }
            return $files;
        }
        $available = array_map('basename', $files);
        if (array_diff(array_keys($rows), $available)) {
            throw new \RuntimeException('Faltan migraciones ya aplicadas. Restaura el historial.');
        }
        $pending = [];
        foreach ($files as $file) {
            $version = basename($file);
            if (!isset($rows[$version])) {
                $pending[] = $file;
            } elseif (!hash_equals($rows[$version], hash_file('sha256', $file))) {
                throw new \RuntimeException('Migración aplicada modificada: ' . $version);
            }
        }
        return $pending;
    }
}
