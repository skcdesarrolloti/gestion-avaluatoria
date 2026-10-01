<?php
declare(strict_types=1);
namespace App\Models;
use PDO;
final class AppraisalCreation
{
    public static function create(PDO $db, int $owner, ?array $actor): string {
        $id=bin2hex(random_bytes(16)); $now=gmdate('Y-m-d H:i:s');
        $q=$db->prepare('INSERT INTO appraisals (id, owner_id, expediente_number, created_at, updated_at, analyst_account_id, appraiser_id) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $q->execute([$id,$owner,null,$now,$now,$actor['analyst_id']??null,$actor['responsible_appraiser_id']??null]); return $id;
    }
}
