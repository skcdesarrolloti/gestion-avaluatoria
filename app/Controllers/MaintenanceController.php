<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Core\{Env, Http, HttpException, Session};
use App\Database\Migrator;
use App\Models\{AppraisalSectorRepository, AppraisalSectorSectionRepository, AppraisalSubjectRepository,
    SectorBankRepository};
use PDO;

final class MaintenanceController
{
    public function __construct(private PDO $db, private array $user) {}

    public function migrations(): void
    {
        $this->authorize();
        $migrator = $this->migrator();
        view('maintenance/migrations', [
            'title' => 'Migraciones',
            'enabled' => Env::bool('MAINTENANCE_MIGRATIONS'),
            'items' => $migrator->status(),
            'checks' => $this->databaseChecks(),
            'sectorProbe' => $this->sectorProbe(),
            'message' => Session::pullFlash('migrations_message'),
            'error' => Session::pullFlash('migrations_error'),
        ]);
    }

    public function runMigrations(): void
    {
        $this->authorize();
        try {
            $applied = $this->migrator()->run();
            $message = $applied ? 'Migraciones aplicadas: ' . implode(', ', $applied)
                : 'No había migraciones pendientes.';
            Session::flash('migrations_message', $message);
        } catch (\Throwable $error) {
            error_log('Gestion avaluatoria migrate ' . get_class($error) . ' ' . $error->getMessage());
            Session::flash('migrations_error', 'No se pudo completar la migración: ' . $error->getMessage());
        }
        Http::redirect('mantenimiento/migraciones');
    }

    private function authorize(): void
    {
        if (!Env::bool('MAINTENANCE_MIGRATIONS')) {
            throw new HttpException(404, 'Página no encontrada.');
        }
    }

    private function migrator(): Migrator
    {
        return new Migrator($this->db, BASE_PATH . '/database/migrations');
    }

    private function databaseChecks(): array
    {
        $expected = [
            'master_sector_profiles' => ['neighborhood_id', 'version', 'updated_at'],
            'master_sector_sources' => ['source_key', 'latest_revision', 'updated_at'],
            'master_sector_profile_sections' => ['neighborhood_id', 'section_code', 'data_json', 'updated_at'],
            'master_sector_section_sources' => ['neighborhood_id', 'section_code', 'source_key'],
            'appraisal_sector_snapshots' => ['appraisal_id', 'snapshot_json', 'updated_at'],
            'appraisal_sector_profile_sections' => ['appraisal_id', 'section_code', 'data_json', 'updated_at'],
        ];
        $checks = [];
        foreach ($expected as $table => $columns) {
            $checks[] = $this->tableCheck($table, $columns);
        }
        return $checks;
    }

    private function tableCheck(string $table, array $expectedColumns): array
    {
        try {
            $columns = $this->columns($table);
            $missing = array_values(array_diff($expectedColumns, $columns));
            return ['table' => $table, 'ok' => $missing === [], 'missing' => $missing, 'error' => ''];
        } catch (\Throwable $error) {
            return ['table' => $table, 'ok' => false, 'missing' => $expectedColumns, 'error' => $error->getMessage()];
        }
    }

    private function columns(string $table): array
    {
        $query = $this->db->query('SHOW COLUMNS FROM ' . $table);
        return array_map(static fn (array $row): string => (string) $row['Field'], $query->fetchAll());
    }

    private function sectorProbe(): array
    {
        $row = $this->latestAppraisal();
        if (!$row) return ['appraisal' => 'Sin avalúos para probar.', 'steps' => []];
        $subject = (new AppraisalSubjectRepository($this->db))->find((string) $row['id'], $this->user['id']);
        $neighborhoodId = (string) ($subject['neighborhood_id'] ?? '');
        $sector = (new AppraisalSectorRepository($this->db))->find((string) $row['id'], $this->user['id']);
        $steps = [];
        $this->probeStep($steps, 'Sujeto y barrio', fn (): string =>
            'Barrio: ' . ($subject['neighborhood_name'] ?: 'sin barrio') . ' · ID: ' . ($neighborhoodId ?: 'sin ID'));
        $bank = new SectorBankRepository($this->db);
        $this->probeStep($steps, 'Banco sectorial', function () use ($bank, $neighborhoodId, $subject, $sector): string {
            $bank->ensureSections($neighborhoodId, $subject, $sector);
            return count($bank->sections($neighborhoodId)) . ' secciones maestras disponibles.';
        });
        $this->probeStep($steps, 'Secciones del avalúo', fn (): string =>
            count((new AppraisalSectorSectionRepository($this->db))->sections((string) $row['id'], $this->user['id']))
            . ' secciones guardadas en el avalúo.');
        return ['appraisal' => (string) $row['id'], 'steps' => $steps];
    }

    private function latestAppraisal(): ?array
    {
        $query = $this->db->prepare('SELECT id FROM appraisals WHERE owner_id = ? ORDER BY updated_at DESC LIMIT 1');
        $query->execute([$this->user['id']]);
        return $query->fetch() ?: null;
    }

    private function probeStep(array &$steps, string $label, callable $callback): void
    {
        try {
            $steps[] = ['label' => $label, 'ok' => true, 'message' => $callback()];
        } catch (\Throwable $error) {
            $steps[] = ['label' => $label, 'ok' => false, 'message' => $error->getMessage()];
        }
    }
}
