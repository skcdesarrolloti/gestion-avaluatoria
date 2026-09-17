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
        if (!$this->allowedUser() && !$this->allowedRole() && !$this->allowsAuthenticated()) {
            throw new HttpException(403, 'No tienes permiso para ejecutar mantenimiento.');
        }
    }

    private function allowedUser(): bool
    {
        $ids = array_filter(array_map('trim', explode(',', Env::get('MAINTENANCE_MIGRATION_USER_IDS'))));
        return $ids && in_array((string) $this->user['id'], $ids, true);
    }

    private function allowedRole(): bool
    {
        $roles = Env::get('MAINTENANCE_MIGRATION_ROLES');
        if (trim($roles) === '') {
            return false;
        }
        $allowed = array_map(static fn (string $role): string => mb_strtolower(trim($role)), explode(',', $roles));
        return in_array(mb_strtolower((string) ($this->user['role'] ?? '')), array_filter($allowed), true);
    }

    private function allowsAuthenticated(): bool
    {
        return Env::bool('MAINTENANCE_MIGRATION_ANY_AUTHENTICATED');
    }

    private function migrator(): Migrator
    {
        return new Migrator($this->db, BASE_PATH . '/database/migrations');
    }
}
