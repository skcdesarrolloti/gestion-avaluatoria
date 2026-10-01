<?php
declare(strict_types=1);
namespace App\Models;
use PDO;
final class AppraisalListing {
    public static function recent(PDO $db, ?array $actor, int $owner, int $page, string $search = '', bool $createdOnly = false): array {
        $offset = (max(1, $page) - 1) * 20; $search = trim($search);
        $where = 'owner_id = ?'; $params = [$owner];
        if ($createdOnly) $where .= " AND expediente_number IS NOT NULL AND expediente_number <> ''";
        if ($search !== '') { $where .= ' AND (expediente_number LIKE ? OR titulo LIKE ? OR municipio LIKE ? OR property_owner_name LIKE ? OR client_name LIKE ? OR requester_name LIKE ? OR requester_capacity LIKE ? OR report_recipient LIKE ?)'; $needle = '%' . $search . '%'; $params = array_merge([$owner], array_fill(0, 8, $needle)); }
        if (!empty($actor['analyst_id'])) { $where .= ' AND analyst_account_id = ? AND appraiser_id = ?'; $params[]=$actor['analyst_id']; $params[]=$actor['responsible_appraiser_id']; }
        $query = $db->prepare("SELECT id, expediente_number, titulo, tipo, municipio, property_owner_name, client_name, updated_at
            FROM appraisals WHERE $where ORDER BY updated_at DESC, id DESC LIMIT 21 OFFSET $offset");
        $query->execute($params);
        return $query->fetchAll();
    }

}
