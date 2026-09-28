<?php
declare(strict_types=1);
namespace App\Models;

use App\Core\HttpException;
use App\Services\MidasDocumentStorage;
use PDO;

final class MidasDocumentRepository
{
    public function __construct(private PDO $db) {}

    public static function groups(): array
    {
        return [
            'Barrios / división política' => 'Límites, localidades, barrios y UCG.',
            'POT / ordenamiento territorial' => 'POT, usos, tratamientos y reglamentación urbana.',
            'Circulares urbanísticas' => 'Criterios de Planeación sobre altura, parqueaderos, altillos y reglas complementarias.',
            'Servicios públicos' => 'Cobertura de acueducto, alcantarillado, gas, energía, aseo o alumbrado.',
            'Transporte y movilidad' => 'Vías, transporte masivo, rutas, paraderos y conectividad.',
            'Equipamiento urbano' => 'Educación, salud, cultura, deporte y espacio público.',
            'Ambiente y riesgos' => 'Amenazas, riesgos, protección ambiental y determinantes.',
            'Otro soporte MIDAS' => 'Soporte descargado de MIDAS que no encaja en los grupos anteriores.',
        ];
    }

    public function latest(): array
    {
        $rows = $this->db->query('SELECT * FROM midas_documents ORDER BY updated_at DESC')->fetchAll();
        return array_map(fn (array $row): array => $this->hydrate($row), $rows);
    }

    public function find(string $id): array
    {
        $query = $this->db->prepare('SELECT * FROM midas_documents WHERE id = ?');
        $query->execute([$id]);
        $row = $query->fetch();
        if (!$row) throw new HttpException(404, 'No se encontró el documento MIDAS.');
        return $this->hydrate($row);
    }

    public function findDuplicate(string $code, string $filename): ?array
    {
        $query = $this->db->prepare('SELECT * FROM midas_documents
            WHERE document_code = ? OR source_filename = ? ORDER BY updated_at DESC LIMIT 1');
        $query->execute([$code, $filename]);
        $row = $query->fetch();
        return is_array($row) ? $this->hydrate($row) : null;
    }

    public function store(array $data): void
    {
        $sql = 'INSERT INTO midas_documents
            (id, slug, layer_group, document_code, title, status, practical_use, applies_to,
            source_filename, storage_filename, mime_type, file_size_bytes, file_blob,
            created_by, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
        $this->db->prepare($sql)->execute([
            $data['id'], $data['slug'], $data['layer_group'], $data['document_code'],
            $data['title'], $data['status'], $data['practical_use'], $data['applies_to'],
            $data['source_filename'], $data['storage_filename'], $data['mime_type'],
            $data['file_size_bytes'], $data['file_blob'], $data['created_by'],
            $data['now'], $data['now'],
        ]);
    }

    public function delete(string $id): array
    {
        $document = $this->find($id);
        $this->db->prepare('DELETE FROM midas_documents WHERE id = ?')->execute([$id]);
        return $document;
    }

    public function uniqueSlug(string $base): string
    {
        $slug = $base !== '' ? $base : 'midas';
        $candidate = substr($slug, 0, 110);
        $i = 2;
        $query = $this->db->prepare('SELECT COUNT(*) FROM midas_documents WHERE slug = ?');
        while (true) {
            $query->execute([$candidate]);
            if ((int) $query->fetchColumn() === 0) return $candidate;
            $suffix = '-' . $i++;
            $candidate = substr($slug, 0, 120 - strlen($suffix)) . $suffix;
        }
    }

    public function storageReport(): array
    {
        $rows = $this->db->query('SELECT storage_filename, file_blob IS NOT NULL has_blob FROM midas_documents')->fetchAll();
        $present = 0;
        foreach ($rows as $row) {
            if (is_file(MidasDocumentStorage::path((string) $row['storage_filename'])) || !empty($row['has_blob'])) $present++;
        }
        return ['dir' => MidasDocumentStorage::dir(), 'configured' => MidasDocumentStorage::configured(),
            'writable' => MidasDocumentStorage::writable(), 'present' => $present,
            'total' => count($rows), 'limits' => MidasDocumentStorage::limits()];
    }

    private function hydrate(array $row): array
    {
        $path = MidasDocumentStorage::path((string) $row['storage_filename']);
        return [...$row, 'file_path' => $path, 'has_file' => is_file($path) || is_string($row['file_blob'] ?? null)];
    }
}
