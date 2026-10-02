<?php
declare(strict_types=1);
namespace App\Models;

use App\Core\HttpException;
use App\Services\AppraisalUnitDefinitionInput;
use App\Services\MethodologyWorkflow;
use PDO;

final class AppraisalCompositionSave
{
    public function __construct(private PDO $db, private AppraisalRepository $repo) {}

    public function save(string $id, int $owner, int $version, array $data, array $posted, int $methodVersion): array
    {
        $rows = AppraisalUnitDefinitionInput::rows($posted, $data);
        $this->db->beginTransaction();
        try {
            $lock = $this->db->prepare('SELECT methodology_version, methodology_workflow FROM appraisals WHERE id = ? AND owner_id = ? FOR UPDATE');
            $lock->execute([$id, $owner]);
            $record = $lock->fetch();
            if (!$record) throw new HttpException(404, 'No se encontró el expediente.');
            $flow = MethodologyWorkflow::saved($record);
            $changes = array_filter($rows, static fn ($row) => $row['method_choice'] !== null && $row['method_choice'] !== $row['original_method']);
            if ($changes !== [] && $methodVersion !== (int) $record['methodology_version']) {
                throw new HttpException(409, 'El método cambió en otra pestaña. Conserva tus cambios y recarga antes de continuar.');
            }
            $result = $this->repo->saveChapterZero($id, $owner, $version, $data);
            $this->repo->saveUnitDefinitionsByKey($id, $owner, $rows);
            $units = [];
            foreach ($this->repo->units($id, $owner) as $unit) $units[$unit['unit_kind'] . '-' . $unit['unit_index']] = $unit['id'];
            foreach ($changes as $row) {
                $key = $units[$row['unit_kind'] . '-' . $row['unit_index']] ?? null;
                if ($key === null) throw new HttpException(422, 'Revisa la composición del predio.');
                $flow[$key] = array_replace($flow[$key] ?? [], ['method' => $row['method_choice']]);
            }
            if ($changes !== []) {
                $query = $this->db->prepare('UPDATE appraisals SET methodology_workflow = ?, methodology_version = methodology_version + 1 WHERE id = ? AND owner_id = ?');
                $query->execute([json_encode($flow, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR), $id, $owner]);
            }
            $this->db->commit();
            $savedMethods = [];
            foreach ($changes as $row) $savedMethods[$row['unit_kind'] . '-' . $row['unit_index']] = $row['method_choice'];
            return $result + ['composition_method_version' => $changes !== [] ? (int) $record['methodology_version'] + 1 : $methodVersion,
                'composition_methods' => $savedMethods];
        } catch (\Throwable $error) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            throw $error;
        }
    }
}
