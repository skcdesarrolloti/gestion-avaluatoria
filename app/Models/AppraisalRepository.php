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
        $query = $this->db->prepare('SELECT id, appraisal_id, owner_id, unit_id, source_filename, storage_filename,
            mime_type, file_size_bytes, caption, created_at, file_blob IS NOT NULL AS has_blob
            FROM appraisal_photos WHERE appraisal_id = ? AND owner_id = ?
            ORDER BY created_at DESC, id DESC');
        $query->execute([$id, $owner]);
        return array_map(fn (array $row): array => $row + [
            'file_available' => is_file(self::photoPath((string) $row['storage_filename'])),
        ], $query->fetchAll());
    }

    public function units(string $id, int $owner): array
    {
        $query = $this->db->prepare('SELECT * FROM appraisal_units WHERE appraisal_id = ? AND owner_id = ?
            ORDER BY unit_kind = "common" DESC, unit_kind, unit_index');
        $query->execute([$id, $owner]);
        return $query->fetchAll();
    }

    public function findPhoto(string $photoId, int $owner): array
    {
        $query = $this->db->prepare('SELECT * FROM appraisal_photos WHERE id = ? AND owner_id = ?');
        $query->execute([$photoId, $owner]);
        $row = $query->fetch();
        if (!$row) throw new HttpException(404, 'No se encontró la foto.');
        return $row;
    }

    public function deletePhoto(string $id, string $photoId, int $owner): ?string
    {
        $photo = $this->findPhoto($photoId, $owner);
        if ((string) $photo['appraisal_id'] !== $id) throw new HttpException(404, 'No se encontró la foto.');
        $query = $this->db->prepare('DELETE FROM appraisal_photos WHERE id = ? AND appraisal_id = ? AND owner_id = ?');
        $query->execute([$photoId, $id, $owner]);
        return is_file(self::photoPath((string) $photo['storage_filename']))
            ? self::photoPath((string) $photo['storage_filename'])
            : null;
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
        $this->ensureUnits($id, $owner, (int) $data['igac_property_units_count'], (int) $data['igac_annex_units_count']);
    }

    public function saveUnits(string $id, int $owner, array $units): void
    {
        $now = gmdate('Y-m-d H:i:s');
        $query = $this->db->prepare('UPDATE appraisal_units SET label = ?, igac_category = ?,
            igac_typology_hint = ?, notes = ?, updated_at = ? WHERE id = ? AND appraisal_id = ? AND owner_id = ?');
        foreach ($units as $unit) {
            $query->execute([$unit['label'], $unit['igac_category'], $unit['igac_typology_hint'],
                $unit['notes'], $now, $unit['id'], $id, $owner]);
        }
    }

    public function saveUnitSurfaces(string $id, int $owner, array $units): void
    {
        $now = gmdate('Y-m-d H:i:s');
        $query = $this->db->prepare('UPDATE appraisal_units SET area_land_m2 = ?, area_built_m2 = ?,
            area_private_m2 = ?, area_common_m2 = ?, front_length_m = ?, depth_length_m = ?,
            surface_source = ?, surface_notes = ?, lot_shape = ?, topography = ?, boundaries = ?,
            boundary_source = ?, boundary_front = ?, boundary_right = ?, boundary_left = ?, boundary_back = ?,
            boundary_zenith = ?, boundary_nadir = ?, area_manual_m2 = ?, area_midas_m2 = ?, area_tax_m2 = ?,
            area_deed_m2 = ?, area_certificate_m2 = ?, area_other_m2 = ?, area_adopted_m2 = ?,
            area_adopted_source = ?, enclosure = ?, equivalent_depth_m = ?, front_depth_ratio = ?,
            dynamic_surface_notes = ?, dynamic_normative_compatibility = ?, dynamic_environment_conditions = ?,
            dynamic_service_quality = ?, dynamic_service_availability = ?, dynamic_road_condition = ?,
            dynamic_urban_development = ?, dynamic_affectations = ?, dynamic_restrictions = ?,
            surface_report_text = ?, updated_at = ?
            WHERE id = ? AND appraisal_id = ? AND owner_id = ?');
        foreach ($units as $unit) {
            $query->execute([$unit['area_land_m2'], $unit['area_built_m2'], $unit['area_private_m2'],
                $unit['area_common_m2'], $unit['front_length_m'], $unit['depth_length_m'],
                $unit['surface_source'], $unit['surface_notes'], $unit['lot_shape'], $unit['topography'],
                $unit['boundaries'], $unit['boundary_source'], $unit['boundary_front'], $unit['boundary_right'],
                $unit['boundary_left'], $unit['boundary_back'], $unit['boundary_zenith'], $unit['boundary_nadir'],
                $unit['area_manual_m2'], $unit['area_midas_m2'], $unit['area_tax_m2'], $unit['area_deed_m2'],
                $unit['area_certificate_m2'], $unit['area_other_m2'], $unit['area_adopted_m2'],
                $unit['area_adopted_source'], $unit['enclosure'], $unit['equivalent_depth_m'], $unit['front_depth_ratio'],
                $unit['dynamic_surface_notes'], $unit['dynamic_normative_compatibility'], $unit['dynamic_environment_conditions'],
                $unit['dynamic_service_quality'], $unit['dynamic_service_availability'], $unit['dynamic_road_condition'],
                $unit['dynamic_urban_development'], $unit['dynamic_affectations'], $unit['dynamic_restrictions'],
                $unit['surface_report_text'], $now, $unit['id'], $id, $owner]);
        }
    }

    public function ensureUnits(string $id, int $owner, int $propertyCount, int $annexCount): void
    {
        for ($i = 1; $i <= $propertyCount; $i++) $this->ensureUnit($id, $owner, 'property', $i, 'Unidad ' . $i);
        for ($i = 1; $i <= $annexCount; $i++) $this->ensureUnit($id, $owner, 'annex', $i, 'Anexo ' . $i);
    }

    private function ensureUnit(string $id, int $owner, string $kind, int $index, string $label): void
    {
        $now = gmdate('Y-m-d H:i:s');
        $query = $this->db->prepare('INSERT IGNORE INTO appraisal_units
            (id, appraisal_id, owner_id, unit_kind, unit_index, label, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        $query->execute([bin2hex(random_bytes(16)), $id, $owner, $kind, $index, $label, $now, $now]);
    }

    public function addPhoto(string $id, int $owner, array $photo): void
    {
        $now = gmdate('Y-m-d H:i:s');
        $query = $this->db->prepare('INSERT INTO appraisal_photos
            (id, appraisal_id, owner_id, unit_id, source_filename, storage_filename, mime_type,
            file_size_bytes, caption, created_at, file_blob) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $query->execute([$photo['id'], $id, $owner, $photo['unit_id'], $photo['source_filename'], $photo['storage_filename'],
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
