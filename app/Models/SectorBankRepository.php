<?php
declare(strict_types=1);
namespace App\Models;
use App\Support\AppraisalSectorAdvancedCatalog;
use App\Models\AppraisalSectorSectionRepository;
use App\Support\SectorBankCatalog;
use PDO;

final class SectorBankRepository
{
    public function __construct(private PDO $db) {}

    public function seedSources(): void
    {
        $now = gmdate('Y-m-d H:i:s');
        $sql = 'INSERT INTO master_sector_sources
            (source_key, group_name, source_name, responsible_entity, access_url, automatable, latest_revision, status, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE group_name = VALUES(group_name), source_name = VALUES(source_name),
                responsible_entity = VALUES(responsible_entity), access_url = VALUES(access_url),
                automatable = VALUES(automatable), latest_revision = VALUES(latest_revision),
                status = VALUES(status), updated_at = VALUES(updated_at)';
        $query = $this->db->prepare($sql);
        foreach (SectorBankCatalog::sources() as $row) {
            $query->execute([$row[0], $row[1], $row[2], $row[3], $row[4], $row[5], $row[6], 'Activo', $now]);
        }
    }

    public function ensureSections(string $neighborhoodId, array $subject, array $sector, bool $replace = false): void
    {
        if ($neighborhoodId === '') return;
        $this->seedSources();
        $now = gmdate('Y-m-d H:i:s');
        $sql = 'INSERT INTO master_sector_profile_sections
            (neighborhood_id, section_code, section_title, status, source_name, source_updated_at,
             requires_field_validation, requires_photo_support, content_text, data_json, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE section_title = VALUES(section_title),
                data_json = CASE WHEN ? = 1 THEN VALUES(data_json)
                    WHEN data_json IS NULL OR data_json = "" THEN VALUES(data_json) ELSE data_json END,
                content_text = CASE WHEN ? = 1 THEN VALUES(content_text)
                    WHEN content_text IS NULL OR content_text = "" THEN VALUES(content_text) ELSE content_text END,
                source_name = CASE WHEN ? = 1 THEN VALUES(source_name) ELSE source_name END,
                updated_at = CASE WHEN ? = 1 THEN VALUES(updated_at) ELSE updated_at END,
                version = CASE WHEN ? = 1 THEN version + 1 ELSE version END';
        $query = $this->db->prepare($sql);
        foreach (SectorBankCatalog::sections() as $code => [$title]) {
            $data = SectorBankCatalog::defaultData($code, $subject, $sector);
            $query->execute([$neighborhoodId, $code, $title, 'Generada',
                $this->sourceFor($code), $now, 'SI', in_array($code, ['02', '06', '07', '12', '13', '14'], true) ? 'SI' : 'NO',
                SectorBankCatalog::defaultText($code, $data),
                json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), $now,
                $replace ? 1 : 0, $replace ? 1 : 0, $replace ? 1 : 0, $replace ? 1 : 0, $replace ? 1 : 0]);
            $this->linkSource($neighborhoodId, $code, $this->sourceKeyFor($code), $now);
        }
    }

    public function sections(string $neighborhoodId): array
    {
        if ($neighborhoodId === '') return [];
        $query = $this->db->prepare('SELECT * FROM master_sector_profile_sections
            WHERE neighborhood_id = ? ORDER BY section_code ASC');
        $query->execute([$neighborhoodId]);
        return $query->fetchAll() ?: [];
    }

    public function summary(string $neighborhoodId): array
    {
        $sections = $this->sections($neighborhoodId);
        $total = count($sections);
        $ready = count(array_filter($sections, static fn (array $row): bool =>
            in_array((string) ($row['status'] ?? ''), ['Validada', 'Aprobada', 'Disponible'], true)));
        return ['total' => $total, 'ready' => $ready, 'percent' => $total > 0 ? (int) round($ready * 100 / $total) : 0,
            'level' => $ready >= 15 ? 'VERDE' : ($ready >= 8 ? 'AMARILLO' : 'ROJO')];
    }

    public function saveSnapshot(string $appraisalId, int $owner, string $neighborhoodId, array $sector): void
    {
        $now = gmdate('Y-m-d H:i:s');
        $snapshot = ['sector' => $sector, 'sections' => $this->sections($neighborhoodId)];
        $json = json_encode($snapshot, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $profileVersion = $this->profileVersion($neighborhoodId);
        $sql = 'INSERT INTO appraisal_sector_snapshots
            (appraisal_id, owner_id, neighborhood_id, profile_version, snapshot_json, copied_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE neighborhood_id = VALUES(neighborhood_id),
                profile_version = VALUES(profile_version), snapshot_json = VALUES(snapshot_json),
                updated_at = VALUES(updated_at)';
        $this->db->prepare($sql)->execute([$appraisalId, $owner, $neighborhoodId ?: null,
            $profileVersion, $json, $now, $now]);
    }

    public function saveAdvancedSections(string $neighborhoodId, array $sections): void
    {
        if ($neighborhoodId === '') return;
        if ($this->db->getAttribute(PDO::ATTR_DRIVER_NAME) === 'sqlite') {
            $this->saveAdvancedSectionsSqlite($neighborhoodId, $sections);
            return;
        }
        $now = gmdate('Y-m-d H:i:s');
        $sql = 'INSERT INTO master_sector_profile_sections
            (neighborhood_id, section_code, section_title, status, source_name, source_updated_at,
             requires_field_validation, requires_photo_support, content_text, data_json, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE section_title = VALUES(section_title), status = VALUES(status),
                content_text = VALUES(content_text), data_json = VALUES(data_json),
                source_name = VALUES(source_name), source_updated_at = VALUES(source_updated_at),
                version = version + 1, updated_at = VALUES(updated_at)';
        $query = $this->db->prepare($sql);
        foreach (AppraisalSectorAdvancedCatalog::sections() as $code => [$title]) {
            $code = (string) $code;
            $data = $sections[$code] ?? [];
            $query->execute([$neighborhoodId, $code, $title, $this->sectionStatus($data),
                $this->sourceFor($code), $now, 'SI',
                in_array($code, ['02', '06', '07', '11', '12', '13', '14'], true) ? 'SI' : 'NO',
                AppraisalSectorSectionRepository::summary($title, $data),
                json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR),
                $now]);
            $this->linkSource($neighborhoodId, $code, $this->sourceKeyFor($code), $now);
        }
    }

    private function saveAdvancedSectionsSqlite(string $neighborhoodId, array $sections): void
    {
        $now = gmdate('Y-m-d H:i:s');
        $update = $this->db->prepare('UPDATE master_sector_profile_sections SET section_title = ?,
            status = ?, source_name = ?, source_updated_at = ?, requires_field_validation = ?,
            requires_photo_support = ?, content_text = ?, data_json = ?, version = version + 1,
            updated_at = ? WHERE neighborhood_id = ? AND section_code = ?');
        $insert = $this->db->prepare('INSERT INTO master_sector_profile_sections
            (neighborhood_id, section_code, section_title, status, source_name, source_updated_at,
             requires_field_validation, requires_photo_support, content_text, data_json, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        foreach (AppraisalSectorAdvancedCatalog::sections() as $code => [$title]) {
            $code = (string) $code;
            $data = $sections[$code] ?? [];
            $support = in_array($code, ['02', '06', '07', '11', '12', '13', '14'], true) ? 'SI' : 'NO';
            $payload = [$title, $this->sectionStatus($data), $this->sourceFor($code), $now, 'SI',
                $support, AppraisalSectorSectionRepository::summary($title, $data),
                json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR),
                $now, $neighborhoodId, $code];
            $update->execute($payload);
            if ($update->rowCount() === 0) {
                $insert->execute([$neighborhoodId, $code, ...array_slice($payload, 0, 9)]);
            }
            $this->linkSource($neighborhoodId, $code, $this->sourceKeyFor($code), $now);
        }
    }

    private function linkSource(string $neighborhoodId, string $code, string $source, string $now): void
    {
        if ($this->db->getAttribute(PDO::ATTR_DRIVER_NAME) === 'sqlite') {
            $this->db->prepare('INSERT OR IGNORE INTO master_sector_section_sources
                (neighborhood_id, section_code, source_key, relation_status, updated_at) VALUES (?, ?, ?, ?, ?)')
                ->execute([$neighborhoodId, $code, $source, 'Preparada', $now]);
            return;
        }
        $this->db->prepare('INSERT IGNORE INTO master_sector_section_sources
            (neighborhood_id, section_code, source_key, relation_status, updated_at) VALUES (?, ?, ?, ?, ?)')
            ->execute([$neighborhoodId, $code, $source, 'Preparada', $now]);
    }

    private function sectionStatus(array $data): string
    {
        foreach ($data as $value) {
            if (is_array($value) ? $value !== [] : trim((string) $value) !== '') return 'En construcción';
        }
        return 'Pendiente';
    }

    private function sourceKeyFor(string $code): string
    {
        return match ($code) {
            '01', '02' => 'barrios_cartagena',
            '03' => 'planeacion_cartagena',
            '05' => 'midas_normatividad',
            '06', '11' => 'transcaribe',
            '08', '13' => 'epa_cartagena',
            '09' => 'ipcc_pemp',
            '10', '15' => 'investigacion_mercado',
            '12' => 'imagenes_apoyo',
            '14' => 'registro_fotografico',
            default => 'campo_analista',
        };
    }

    private function sourceFor(string $code): string
    {
        return match ($this->sourceKeyFor($code)) {
            'barrios_cartagena' => 'Datos Abiertos Cartagena - Barrios',
            'planeacion_cartagena' => 'Secretaría de Planeación / POT',
            'midas_normatividad' => 'MIDAS Cartagena / POT',
            'transcaribe' => 'Transcaribe / movilidad',
            'epa_cartagena' => 'EPA Cartagena / riesgos ambientales',
            'ipcc_pemp' => 'IPCC / PEMP',
            'investigacion_mercado' => 'Investigación del mercado',
            'imagenes_apoyo' => 'Imágenes viales y satelitales de apoyo',
            'registro_fotografico' => 'Registro fotográfico de campo',
            default => 'Validación del analista',
        };
    }

    private function profileVersion(string $neighborhoodId): int
    {
        if ($neighborhoodId === '') return 1;
        $query = $this->db->prepare('SELECT version FROM master_sector_profiles WHERE neighborhood_id = ?');
        $query->execute([$neighborhoodId]);
        return max(1, (int) ($query->fetchColumn() ?: 1));
    }
}
