<?php
declare(strict_types=1);
namespace App\Models;
use App\Support\AppraisalObsolescenceCatalog;
use PDO;

final class AppraisalObsolescenceRepository
{
    public function __construct(private PDO $db) {}
    public function find(string $appraisalId, int $owner): array
    {
        $query = $this->db->prepare('SELECT * FROM appraisal_obsolescence_profiles WHERE appraisal_id = ? AND owner_id = ?');
        $query->execute([$appraisalId, $owner]);
        $row = $query->fetch();
        if (!$row) return AppraisalObsolescenceCatalog::defaults();
        $defaults = AppraisalObsolescenceCatalog::defaults();
        $factors = json_decode((string) ($row['factors_json'] ?? ''), true);
        $defaults['factors'] = is_array($factors) ? array_replace_recursive($defaults['factors'], $factors) : $defaults['factors'];
        return array_replace($defaults, ['summary_text'=>(string) ($row['summary_text'] ?? ''),
            'diagnosis_text'=>(string) ($row['diagnosis_text'] ?? ''), 'quantification_text'=>(string) ($row['quantification_text'] ?? ''),
            'normative_text'=>(string) ($row['normative_text'] ?? ''), 'updated_at'=>$row['updated_at'] ?? null]);
    }
    public function save(string $appraisalId, int $owner, array $data): void
    {
        $now = gmdate('Y-m-d H:i:s');
        $json = json_encode($data['factors'] ?? [], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        $query = $this->db->prepare('INSERT INTO appraisal_obsolescence_profiles
            (appraisal_id, owner_id, summary_text, diagnosis_text, quantification_text, normative_text, factors_json, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE summary_text = VALUES(summary_text),
            diagnosis_text = VALUES(diagnosis_text), quantification_text = VALUES(quantification_text),
            normative_text = VALUES(normative_text), factors_json = VALUES(factors_json), updated_at = VALUES(updated_at)');
        $query->execute([$appraisalId, $owner, $data['summary_text'], $data['diagnosis_text'],
            $data['quantification_text'], $data['normative_text'], $json, $now]);
    }
}