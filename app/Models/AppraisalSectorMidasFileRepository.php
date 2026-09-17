<?php
declare(strict_types=1);
namespace App\Models;
use PDO;
use App\Core\HttpException;
use App\Services\AppraisalMidasFileStorage;

final class AppraisalSectorMidasFileRepository
{
    public function __construct(private PDO $db) {}

    public function forAppraisal(string $appraisalId, int $owner): array
    {
        $query = $this->db->prepare('SELECT * FROM appraisal_sector_midas_files
            WHERE appraisal_id = ? AND owner_id = ? ORDER BY created_at DESC, id DESC');
        $query->execute([$appraisalId, $owner]);
        return array_map(static fn (array $row): array => $row + [
            'file_available' => is_file(AppraisalMidasFileStorage::path((string) $row['storage_filename'])),
        ], $query->fetchAll());
    }

    public function add(string $appraisalId, int $owner, array $file): void
    {
        $now = gmdate('Y-m-d H:i:s');
        $query = $this->db->prepare('INSERT INTO appraisal_sector_midas_files
            (id, appraisal_id, owner_id, neighborhood_id, layer_group, source_filename, storage_filename,
            mime_type, file_size_bytes, notes, file_blob, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $query->execute([$file['id'], $appraisalId, $owner, $file['neighborhood_id'], $file['layer_group'],
            $file['source_filename'], $file['storage_filename'], $file['mime_type'], $file['file_size_bytes'],
            $file['notes'], $file['file_blob'], $now]);
    }

    public function find(string $id, string $appraisalId, int $owner): array
    {
        $query = $this->db->prepare('SELECT * FROM appraisal_sector_midas_files
            WHERE id = ? AND appraisal_id = ? AND owner_id = ?');
        $query->execute([$id, $appraisalId, $owner]);
        $row = $query->fetch();
        if (!$row) throw new HttpException(404, 'No se encontró el soporte MIDAS.');
        return $row;
    }

    public static function path(string $filename): string { return AppraisalMidasFileStorage::path($filename); }
}
