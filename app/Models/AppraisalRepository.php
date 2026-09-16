<?php
declare(strict_types=1);
namespace App\Models;
use PDO;
use App\Core\HttpException;
use App\Services\AppraisalPhotoStorage;
use App\Support\AppraisalCatalog;

final class AppraisalRepository
{
    public function __construct(private PDO $db) {}

    public function recent(int $owner, int $page): array
    {
        $offset = (max(1, $page) - 1) * 20;
        $query = $this->db->prepare("SELECT id, titulo, tipo, municipio, updated_at
            FROM appraisals WHERE owner_id = ? ORDER BY updated_at DESC, id DESC LIMIT 21 OFFSET $offset");
        $query->execute([$owner]);
        return $query->fetchAll();
    }

    public function create(int $owner): string
    {
        $id = bin2hex(random_bytes(16));
        $now = gmdate('Y-m-d H:i:s');
        $query = $this->db->prepare('INSERT INTO appraisals (id, owner_id, created_at, updated_at) VALUES (?, ?, ?, ?)');
        $query->execute([$id, $owner, $now, $now]);
        return $id;
    }

    public function photos(string $id, int $owner): array
    {
        $query = $this->db->prepare('SELECT id, appraisal_id, owner_id, source_filename, storage_filename,
            mime_type, file_size_bytes, caption, created_at, file_blob IS NOT NULL AS has_blob
            FROM appraisal_photos WHERE appraisal_id = ? AND owner_id = ?
            ORDER BY created_at DESC, id DESC');
        $query->execute([$id, $owner]);
        return array_map(fn (array $row): array => $row + [
            'file_available' => is_file(self::photoPath((string) $row['storage_filename'])),
        ], $query->fetchAll());
    }

    public function findPhoto(string $photoId, int $owner): array
    {
        $query = $this->db->prepare('SELECT * FROM appraisal_photos WHERE id = ? AND owner_id = ?');
        $query->execute([$photoId, $owner]);
        $row = $query->fetch();
        if (!$row) throw new HttpException(404, 'No se encontró la foto.');
        return $row;
    }

    public function find(string $id, int $owner): array
    {
        $query = $this->db->prepare('SELECT * FROM appraisals WHERE id = ? AND owner_id = ?');
        $query->execute([$id, $owner]);
        $row = $query->fetch();
        if (!$row) {
            throw new HttpException(404, 'No se encontró la ficha.');
        }
        $row = array_replace(AppraisalCatalog::defaults(), $row);
        $row['version'] = (int) $row['version'];
        return $row;
    }

    public function saveChapterZero(string $id, int $owner, int $version, array $data): array
    {
        $now = gmdate('Y-m-d H:i:s');
        $query = $this->db->prepare('UPDATE appraisals SET titulo = ?, tipo = ?, direccion = ?, municipio = ?,
            observaciones = ?, tipo_derecho = ?, tipo_negocio = ?, destinacion = ?, tipo_inmueble = ?,
            subtipo_funcional = ?, finalidad = ?, base_valor = ?, aplica_niif = ?, regimen_ph = ?,
            estructura_metodo = ?, appraiser_id = ?, igac_category = ?, igac_typology_hint = ?,
            igac_property_units_count = ?, igac_annex_units_count = ?,
            inspection_notes = ?, configuration_status = ?, version = version + 1, updated_at = ?
            WHERE id = ? AND owner_id = ? AND version = ?');
        $query->execute([$data['titulo'], $data['tipo'], $data['direccion'], $data['municipio'],
            $data['observaciones'], $data['tipo_derecho'], $data['tipo_negocio'], $data['destinacion'],
            $data['tipo_inmueble'], $data['subtipo_funcional'], $data['finalidad'], $data['base_valor'],
            $data['aplica_niif'], $data['regimen_ph'], $data['estructura_metodo'], $data['appraiser_id'],
            $data['igac_category'], $data['igac_typology_hint'], $data['igac_property_units_count'],
            $data['igac_annex_units_count'], $data['inspection_notes'],
            $data['configuration_status'], $now, $id, $owner, $version]);
        if ($query->rowCount() !== 1) {
            $this->find($id, $owner);
            throw new HttpException(409, 'Esta configuración cambió en otra pestaña. Revisa antes de guardar.');
        }
        return ['version' => $version + 1, 'saved_at' => str_replace(' ', 'T', $now) . 'Z'];
    }

    public function savePreclassification(string $id, int $owner, int $version, array $data): void
    {
        $now = gmdate('Y-m-d H:i:s');
        $query = $this->db->prepare('UPDATE appraisals SET igac_category = ?, igac_typology_hint = ?,
            igac_property_units_count = ?, igac_annex_units_count = ?, version = version + 1, updated_at = ?
            WHERE id = ? AND owner_id = ? AND version = ?');
        $query->execute([$data['igac_category'], $data['igac_typology_hint'], $data['igac_property_units_count'],
            $data['igac_annex_units_count'], $now, $id, $owner, $version]);
        if ($query->rowCount() !== 1) {
            $this->find($id, $owner);
            throw new HttpException(409, 'Esta lectura inicial cambió en otra pestaña. Revisa antes de guardar.');
        }
    }

    public function addPhoto(string $id, int $owner, array $photo): void
    {
        $now = gmdate('Y-m-d H:i:s');
        $query = $this->db->prepare('INSERT INTO appraisal_photos
            (id, appraisal_id, owner_id, source_filename, storage_filename, mime_type, file_size_bytes, caption, created_at, file_blob)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $query->execute([$photo['id'], $id, $owner, $photo['source_filename'], $photo['storage_filename'],
            $photo['mime_type'], $photo['file_size_bytes'], $photo['caption'], $now, $photo['file_blob']]);
    }

    public static function photoPath(string $filename): string { return AppraisalPhotoStorage::path($filename); }

    public function save(string $id, int $owner, int $version, array $data): array
    {
        $now = gmdate('Y-m-d H:i:s');
        $query = $this->db->prepare('UPDATE appraisals SET titulo = ?, tipo = ?, direccion = ?, municipio = ?,
            observaciones = ?, tipo_derecho = ?, tipo_negocio = ?, destinacion = ?, tipo_inmueble = ?,
            subtipo_funcional = ?, finalidad = ?, base_valor = ?, aplica_niif = ?, regimen_ph = ?,
            estructura_metodo = ?, version = version + 1, updated_at = ?
            WHERE id = ? AND owner_id = ? AND version = ?');
        $query->execute([$data['titulo'], $data['tipo'], $data['direccion'], $data['municipio'],
            $data['observaciones'], $data['tipo_derecho'], $data['tipo_negocio'], $data['destinacion'],
            $data['tipo_inmueble'], $data['subtipo_funcional'], $data['finalidad'], $data['base_valor'],
            $data['aplica_niif'], $data['regimen_ph'], $data['estructura_metodo'], $now, $id, $owner, $version]);
        if ($query->rowCount() !== 1) {
            $this->find($id, $owner);
            throw new HttpException(409, 'Esta ficha cambió en otra pestaña. Copia tus cambios antes de recargar.');
        }
        return ['version' => $version + 1, 'saved_at' => str_replace(' ', 'T', $now) . 'Z'];
    }
}
