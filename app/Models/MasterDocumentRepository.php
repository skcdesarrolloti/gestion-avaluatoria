<?php
declare(strict_types=1);
namespace App\Models;

use App\Core\HttpException;
use App\Services\MasterDocumentStorage;
use PDO;

final class MasterDocumentRepository
{
    public function __construct(private PDO $db) {}

    public static function destinations(): array
    {
        return [
            'normas_tecnicas' => 'Normas técnicas sectoriales',
            'marco_juridico' => 'Marco jurídico nacional',
            'internacionales' => 'Normas internacionales IVS',
            'niif' => 'Normas NIIF',
            'igac' => 'IGAC',
            'normatividad_urbana' => 'Normatividad urbana',
            'academia_obsolescencias' => 'Academia 3.6 obsolescencias',
            'otros' => 'Otros soportes normativos',
        ];
    }

    public static function modules(): array
    {
        return [
            'capitulo_1' => '1. Expediente valuatorio',
            'capitulo_2' => '2. Sector y entorno',
            'capitulo_3' => '3. Bien sujeto',
            'obsolescencias' => '3.6 Obsolescencias',
            'juridico' => '4. Características jurídicas',
            'normatividad_urbana' => '5. Normatividad urbana',
            'aspecto_economico' => '6. Aspecto económico',
            'conservacion' => '7. Conservación',
            'metodologia' => '8. Metodología valuatoria',
            'entregable' => 'Entregable',
        ];
    }

    public static function storagePath(string $filename): string
    {
        return MasterDocumentStorage::path($filename);
    }

    public function latest(int $limit = 12): array
    {
        $query = $this->db->prepare('SELECT * FROM master_documents
            ORDER BY updated_at DESC LIMIT ?');
        $query->bindValue(1, $limit, PDO::PARAM_INT);
        $query->execute();
        return array_map(fn (array $row): array => $this->hydrate($row), $query->fetchAll());
    }

    public function find(string $id): array
    {
        $query = $this->db->prepare('SELECT * FROM master_documents WHERE id = ?');
        $query->execute([$id]);
        $row = $query->fetch();
        if (!$row) throw new HttpException(404, 'No se encontró el documento maestro.');
        return $this->hydrate($row);
    }

    public function store(array $data): void
    {
        $sql = 'INSERT INTO master_documents
            (id, slug, destination, document_code, title, document_type, version,
            effective_at, status, source_url, summary, topics_json, modules_json,
            source_filename, storage_filename, file_size_bytes, pdf_blob, created_by,
            created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
        $this->db->prepare($sql)->execute([
            $data['id'], $data['slug'], $data['destination'], $data['document_code'],
            $data['title'], $data['document_type'], $data['version'],
            $data['effective_at'] ?: null, $data['status'], $data['source_url'],
            $data['summary'], json_encode($data['topics'], JSON_UNESCAPED_UNICODE),
            json_encode($data['modules'], JSON_UNESCAPED_UNICODE), $data['source_filename'],
            $data['storage_filename'], $data['file_size_bytes'], $data['pdf_blob'],
            $data['created_by'], $data['now'], $data['now'],
        ]);
    }

    public function uniqueSlug(string $base): string
    {
        $slug = $base !== '' ? $base : 'documento';
        $candidate = substr($slug, 0, 110);
        $i = 2;
        $query = $this->db->prepare('SELECT COUNT(*) FROM master_documents WHERE slug = ?');
        while (true) {
            $query->execute([$candidate]);
            if ((int) $query->fetchColumn() === 0) return $candidate;
            $suffix = '-' . $i++;
            $candidate = substr($slug, 0, 120 - strlen($suffix)) . $suffix;
        }
    }

    public function storageReport(): array
    {
        $rows = $this->db->query('SELECT storage_filename, file_size_bytes, pdf_blob IS NOT NULL has_blob
            FROM master_documents')->fetchAll();
        $present = 0;
        foreach ($rows as $row) {
            $path = self::storagePath((string) $row['storage_filename']);
            if (is_file($path) || !empty($row['has_blob'])) $present++;
        }
        return ['dir' => MasterDocumentStorage::dir(), 'configured' => MasterDocumentStorage::configured(),
            'writable' => MasterDocumentStorage::writable(), 'present' => $present,
            'total' => count($rows), 'limits' => MasterDocumentStorage::limits()];
    }

    private function hydrate(array $row): array
    {
        $topics = json_decode((string) ($row['topics_json'] ?? '[]'), true);
        $modules = json_decode((string) ($row['modules_json'] ?? '[]'), true);
        $path = self::storagePath((string) $row['storage_filename']);
        return [
            ...$row,
            'topics' => is_array($topics) ? $topics : [],
            'modules' => is_array($modules) ? $modules : [],
            'has_file' => is_file($path) || is_string($row['pdf_blob'] ?? null),
            'file_path' => $path,
        ];
    }
}
