<?php
declare(strict_types=1);
namespace App\Models;
use App\Core\HttpException;
use App\Services\MethodologyWorkflow;
use PDO;

final class MethodologyWorkflowRepository
{
    public function __construct(private PDO $db) {}

    public function save(string $id, int $owner, int $version, string $key, array $changes): int
    {
        $query = $this->db->prepare('SELECT methodology_workflow FROM appraisals WHERE id = ? AND owner_id = ?');
        $query->execute([$id, $owner]);
        $row = $query->fetch();
        if (!$row) throw new HttpException(404, 'No se encontró el expediente.');
        $saved = MethodologyWorkflow::saved($row);
        \App\Services\CostMethodScope::validate($changes,$saved[$key] ?? []);
        if (isset($changes['cost_scope'])) $changes['cost_scope']=array_replace($saved[$key]['cost_scope'] ?? [],$changes['cost_scope']);
        $saved[$key] = array_replace($saved[$key] ?? [], $changes);
        $update = $this->db->prepare('UPDATE appraisals SET methodology_workflow = ?, methodology_version = methodology_version + 1 WHERE id = ? AND owner_id = ? AND methodology_version = ?');
        $update->execute([json_encode($saved, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR), $id, $owner, $version]);
        if ($update->rowCount() !== 1) throw new HttpException(409, 'La metodología cambió en otro equipo. Conserva tus cambios y recarga antes de continuar.');
        return $version + 1;
    }
}
