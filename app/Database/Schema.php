<?php
declare(strict_types=1);
namespace App\Database;
use App\Core\Database;
use PDO;

final class Schema
{
    public function __construct(public readonly PDO $db) {}

    public function addColumn(string $table, string $column, string $definition): void
    {
        $query = $this->db->prepare('SELECT COUNT(*) FROM information_schema.columns
            WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?');
        $query->execute([$table, $column]);
        if ((int) $query->fetchColumn() === 0) {
            // definition comes exclusively from versioned migration code, never request data.
            $this->db->exec('ALTER TABLE ' . Database::identifier($table)
                . ' ADD COLUMN ' . Database::identifier($column) . ' ' . $definition);
        }
    }
}
