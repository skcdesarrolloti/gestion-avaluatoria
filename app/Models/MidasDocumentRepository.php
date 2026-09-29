<?php
declare(strict_types=1);
namespace App\Models;

use App\Core\HttpException;
use App\Services\MidasDocumentStorage;
use PDO;
use PDOException;

final class MidasDocumentRepository
{
    public function __construct(private PDO $db) {}

    public static function groups(): array
    {
        return [
            'Localidades' => 'Localidades oficiales descargadas de MIDAS para ubicar el sector.',
            'Unidades comuneras de gobierno' => 'UCG urbanas y rurales descargadas de MIDAS.',
            'Barrios / división política' => 'Barrios, límites de barrio y división territorial del sector.',
            'Uso del suelo y tratamientos' => 'Capas MIDAS de uso, actividad, tratamiento y clasificación; no reemplaza el POT base.',
            'Circulares MIDAS' => 'Circulares descargadas desde MIDAS o Planeación.',
            'Servicios públicos' => 'Cobertura de acueducto, alcantarillado, gas, energía, aseo o alumbrado.',
            'Transporte y movilidad' => 'Vías, transporte masivo, rutas, paraderos y conectividad.',
            'Equipamiento urbano' => 'Educación, salud, cultura, deporte y espacio público.',
            'Educación' => 'Instituciones educativas y equipamientos de educación.',
            'Salud' => 'Equipamientos de salud y servicios asistenciales.',
            'Seguridad' => 'Estaciones, CAI, inspecciones y soporte institucional de seguridad.',
            'Cultura' => 'Equipamientos culturales, patrimoniales y comunitarios.',
            'Ambiente y riesgos' => 'Amenazas, riesgos, protección ambiental y determinantes.',
            'Cambio climático' => 'Capas ambientales, adaptación climática, amenazas y vulnerabilidad.',
            'Otro soporte MIDAS' => 'Soporte descargado de MIDAS que no encaja en los grupos anteriores.',
        ];
    }

    public static function canonicalGroup(string $group): string
    {
        return match ($group) {
            'Circulares urbanísticas', 'Circulares Midas' => 'Circulares MIDAS',
            'POT / ordenamiento territorial' => 'Uso del suelo y tratamientos',
            default => $group,
        };
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
        try {
            $this->insert($data);
        } catch (PDOException $exception) {
            if (($data['file_blob'] ?? null) === null) throw $exception;
            $data['file_blob'] = null;
            $this->insert($data);
        }
    }

    private function insert(array $data): void
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
        $row['layer_group'] = self::canonicalGroup((string) $row['layer_group']);
        $path = MidasDocumentStorage::path((string) $row['storage_filename']);
        return [...$row, 'file_path' => $path, 'has_file' => is_file($path) || is_string($row['file_blob'] ?? null)];
    }
}
