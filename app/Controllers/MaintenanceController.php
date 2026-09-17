<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Core\{Env, Http, HttpException, Session};
use App\Database\Migrator;
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
}
