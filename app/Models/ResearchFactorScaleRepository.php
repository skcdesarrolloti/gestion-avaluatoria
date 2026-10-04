<?php
declare(strict_types=1);
namespace App\Models;
use App\Core\HttpException;
use PDO;
final class ResearchFactorScaleRepository
{
    public function __construct(private PDO $db) {}
    public function all(int $owner): array
    {
        $query=$this->db->prepare('SELECT * FROM research_factor_scales WHERE owner_id=?');
        $query->execute([$owner]); $out=[];
        foreach ($query->fetchAll() as $row) $out[$row['factor_key']]=json_decode($row['definition_json'],true,8,JSON_THROW_ON_ERROR)+['version'=>(int)$row['version']];
        return $out;
    }
    public function save(int $owner,string $key,int $version,array $scale): int
    {
        $json=json_encode($scale,JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR);
        if ($version===0) {
            try {
                $query=$this->db->prepare('INSERT INTO research_factor_scales VALUES(?,?,?,1,UTC_TIMESTAMP())');
                $query->execute([$owner,$key,$json]); return 1;
            } catch (\PDOException $error) {
                if ($error->getCode()!=='23000') throw $error;
                throw new HttpException(409,'La escala ya cambió. Conserva tus cambios y recarga el catálogo.');
            }
        }
        $query=$this->db->prepare('UPDATE research_factor_scales SET definition_json=?,version=version+1,updated_at=UTC_TIMESTAMP() WHERE owner_id=? AND factor_key=? AND version=?');
        $query->execute([$json,$owner,$key,$version]);
        if ($query->rowCount()!==1) throw new HttpException(409,'La escala cambió. Conserva tus cambios y recarga el catálogo.');
        return $version+1;
    }
}
