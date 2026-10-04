<?php
declare(strict_types=1);
namespace App\Models;
use App\Core\HttpException;
use App\Services\{UserResearchFactorInput,UserResearchFactors};
use PDO;
final class UserResearchFactorRepository
{
    public function __construct(private PDO $db) {}
    public function all(int $owner): array
    {
        $query=$this->db->prepare('SELECT factor_key,definition_json,version FROM user_research_factors WHERE owner_id=?');
        $query->execute([$owner]); $out=[];
        foreach ($query->fetchAll() as $row) $out[$row['factor_key']]=json_decode($row['definition_json'],true,16,JSON_THROW_ON_ERROR)+['catalog_version'=>(int)$row['version']];
        return $out;
    }
    public function save(int $owner,array $post): string
    {
        UserResearchFactors::load($this->all($owner));
        $key=$post['factor_key'] ?? '';
        if (!is_string($key)) throw new HttpException(422,'Factor inválido.');
        $version=filter_var($post['version'] ?? null,FILTER_VALIDATE_INT);
        if ($version===false || $version===null || $version<0) throw new HttpException(422,'Versión inválida.');
        $data=UserResearchFactorInput::input($post,$key);
        if ($key==='') { $key='u_'.bin2hex(random_bytes(12)); $data['subject']=$data['sample']='research_'.$key; }
        $json=json_encode($data,JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR);
        if ($version===0) {
            try { $query=$this->db->prepare('INSERT INTO user_research_factors VALUES(?,?,?,1,UTC_TIMESTAMP())'); $query->execute([$owner,$key,$json]); }
            catch (\PDOException $error) { if ($error->getCode()!=='23000') throw $error; throw new HttpException(409,'El factor cambió en otra pestaña. Recarga antes de guardar.'); }
        } else {
            $query=$this->db->prepare('UPDATE user_research_factors SET definition_json=?,version=version+1,updated_at=UTC_TIMESTAMP() WHERE owner_id=? AND factor_key=? AND version=?');
            $query->execute([$json,$owner,$key,$version]);
            if ($query->rowCount()!==1) throw new HttpException(409,'El factor cambió en otra pestaña. Conserva tus cambios y recarga.');
        }
        UserResearchFactors::load($this->all($owner));
        return $key;
    }
}
