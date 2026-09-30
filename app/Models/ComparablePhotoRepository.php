<?php
declare(strict_types=1);
namespace App\Models;

use App\Core\HttpException;
use PDO;

final class ComparablePhotoRepository
{
    public function __construct(private PDO $db) {}

    public function requireComparable(string $appraisal, string $comparable, int $owner): void
    {
        $query = $this->db->prepare('SELECT id FROM appraisal_comparables WHERE id = ? AND appraisal_id = ? AND owner_id = ?');
        $query->execute([$comparable, $appraisal, $owner]);
        if (!$query->fetchColumn()) throw new HttpException(404, 'La muestra no está guardada. Guarda la matriz y vuelve a intentar.');
    }

    public function listing(string $appraisal, string $comparable, int $owner): array
    {
        $this->requireComparable($appraisal, $comparable, $owner);
        $query = $this->db->prepare('SELECT id, source_filename, caption, created_at FROM appraisal_comparable_photos
            WHERE appraisal_id = ? AND comparable_id = ? AND owner_id = ? ORDER BY created_at, id');
        $query->execute([$appraisal, $comparable, $owner]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function store(string $appraisal, string $comparable, int $owner, array $photo): void
    {
        $this->requireComparable($appraisal, $comparable, $owner);
        $query = $this->db->prepare('SELECT id FROM appraisal_comparable_photos WHERE appraisal_id = ? AND comparable_id = ? AND owner_id = ? AND file_hash = ?');
        $query->execute([$appraisal, $comparable, $owner, $photo['hash']]);
        if ($query->fetchColumn()) return;
        $query = $this->db->prepare('INSERT INTO appraisal_comparable_photos
            (id, appraisal_id, comparable_id, owner_id, source_filename, mime_type, caption, file_hash, file_blob, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $query->execute([bin2hex(random_bytes(16)), $appraisal, $comparable, $owner,
            $photo['name'], $photo['mime'], $photo['caption'], $photo['hash'], $photo['blob'], gmdate('Y-m-d H:i:s')]);
    }

    public function photo(string $appraisal, string $comparable, int $owner, string $photo): array
    {
        $this->requireComparable($appraisal, $comparable, $owner);
        $query = $this->db->prepare('SELECT * FROM appraisal_comparable_photos WHERE id = ? AND appraisal_id = ? AND comparable_id = ? AND owner_id = ?');
        $query->execute([$photo, $appraisal, $comparable, $owner]);
        return $query->fetch(PDO::FETCH_ASSOC) ?: throw new HttpException(404, 'Foto no encontrada.');
    }
}
