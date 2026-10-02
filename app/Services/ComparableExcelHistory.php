<?php
declare(strict_types=1);
namespace App\Services;
use PDO;

final class ComparableExcelHistory
{
    public static function latest(array $record, string $scope): array
    {
        $history=json_decode($record['comparables_excel_history'] ?? '{}',true);
        return is_array($history[$scope] ?? null)?$history[$scope]:[];
    }
    public static function display(array $stamp): string
    {
        if (empty($stamp['saved_at'])) return 'Todavía no se ha importado un Excel en esta unidad/banco.';
        $date=(new \DateTimeImmutable($stamp['saved_at']))->setTimezone(new \DateTimeZone('America/Bogota'));
        return 'Último Excel guardado: '.$date->format('d/m/Y H:i:s').' · hora de Colombia · versión '.($stamp['version'] ?? '').' · '.($stamp['filename'] ?? '');
    }
    // Called inside the matrix transaction, after its owner/version lock.
    public static function record(PDO $db, string $id, int $owner, array $stamp, int $version): void
    {
        $query=$db->prepare('SELECT comparables_excel_history FROM appraisals WHERE id=? AND owner_id=?');
        $query->execute([$id,$owner]);
        $history=json_decode($query->fetchColumn() ?: '{}',true);
        if (!is_array($history)) $history=[];
        $scope=$stamp['scope']; unset($stamp['scope']);
        $history[$scope]=$stamp+['version'=>$version];
        $update=$db->prepare('UPDATE appraisals SET comparables_excel_history=? WHERE id=? AND owner_id=?');
        $update->execute([json_encode($history,JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR),$id,$owner]);
    }
}
