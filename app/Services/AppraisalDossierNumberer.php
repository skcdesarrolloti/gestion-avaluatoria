<?php
declare(strict_types=1);
namespace App\Services;
use PDO;

final class AppraisalDossierNumberer
{
    public function __construct(private PDO $db) {}

    public function assignIfMissing(string $appraisalId, int $owner): ?string
    {
        $record = $this->record($appraisalId, $owner);
        if (!$record || (string) ($record['expediente_number'] ?? '') !== '') return $record['expediente_number'] ?? null;
        if ((string) ($record['appraiser_id'] ?? '') === '') return null;
        $code = $this->appraiserCode((string) $record['appraiser_id']);
        if ($code === '') return null;
        $prefix = $code . '-' . $this->period($record) . '-';
        for ($tries = 0; $tries < 5; $tries++) {
            $number = $prefix . str_pad((string) ($this->lastConsecutive($prefix) + 1 + $tries), 3, '0', STR_PAD_LEFT);
            $update = $this->db->prepare('UPDATE appraisals SET expediente_number = ? WHERE id = ? AND owner_id = ? AND (expediente_number IS NULL OR expediente_number = ?)');
            try {
                $update->execute([$number, $appraisalId, $owner, '']);
                if ($update->rowCount() === 1) return $number;
            } catch (\PDOException) {}
        }
        return $this->record($appraisalId, $owner)['expediente_number'] ?? null;
    }

    private function record(string $id, int $owner): ?array
    {
        $query = $this->db->prepare('SELECT id, owner_id, expediente_number, appraiser_id, value_date, report_date, created_at FROM appraisals WHERE id = ? AND owner_id = ?');
        $query->execute([$id, $owner]);
        return $query->fetch() ?: null;
    }

    private function appraiserCode(string $id): string
    {
        $query = $this->db->prepare('SELECT code FROM valuation_appraisers WHERE id = ? AND active = ? AND raa_expires_at >= ?');
        $query->execute([$id, 'Si', (new \DateTimeImmutable('today', new \DateTimeZone('America/Bogota')))->format('Y-m-d')]);
        $raw = $query->fetchColumn();
        if ($raw === false) return '';
        $code = preg_replace('/[^A-Za-z0-9]/', '', (string) $raw) ?? '';
        return $code === '' ? '' : substr(str_pad(strtoupper($code), 2, '0', STR_PAD_LEFT), 0, 2);
    }

    private function period(array $record): string
    {
        foreach (['value_date', 'report_date', 'created_at'] as $field) {
            if (preg_match('/^(\d{4})-(\d{2})/u', (string) ($record[$field] ?? ''), $m)) return $m[1] . '-' . $m[2];
        }
        return gmdate('Y-m');
    }

    private function lastConsecutive(string $prefix): int
    {
        $query = $this->db->prepare('SELECT expediente_number FROM appraisals WHERE expediente_number LIKE ? ORDER BY expediente_number DESC LIMIT 1');
        $query->execute([$prefix . '%']);
        return (int) substr((string) $query->fetchColumn(), -3);
    }
}
