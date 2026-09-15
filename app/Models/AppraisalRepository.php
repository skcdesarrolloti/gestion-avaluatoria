<?php
declare(strict_types=1);
namespace App\Models;
use PDO;
use App\Core\HttpException;
use App\Support\AppraisalCatalog;

final class AppraisalRepository
{
    public function __construct(private PDO $db) {}

    public function recent(int $owner, int $page): array
    {
        $offset = (max(1, $page) - 1) * 20;
        $query = $this->db->prepare("SELECT id, titulo, tipo, municipio, updated_at
            FROM appraisals WHERE owner_id = ? ORDER BY updated_at DESC, id DESC LIMIT 21 OFFSET $offset");
        $query->execute([$owner]);
        return $query->fetchAll();
    }

    public function create(int $owner): string
    {
        $id = bin2hex(random_bytes(16));
        $now = gmdate('Y-m-d H:i:s');
        $query = $this->db->prepare('INSERT INTO appraisals (id, owner_id, created_at, updated_at) VALUES (?, ?, ?, ?)');
        $query->execute([$id, $owner, $now, $now]);
        return $id;
    }

    public function find(string $id, int $owner): array
    {
        $query = $this->db->prepare('SELECT * FROM appraisals WHERE id = ? AND owner_id = ?');
        $query->execute([$id, $owner]);
        $row = $query->fetch();
        if (!$row) {
            throw new HttpException(404, 'No se encontró la ficha.');
        }
        $row = array_replace(AppraisalCatalog::defaults(), $row);
        $row['version'] = (int) $row['version'];
        return $row;
    }

    public function save(string $id, int $owner, int $version, array $data): array
    {
        $now = gmdate('Y-m-d H:i:s');
        $query = $this->db->prepare('UPDATE appraisals SET titulo = ?, tipo = ?, direccion = ?, municipio = ?,
            observaciones = ?, tipo_derecho = ?, tipo_negocio = ?, destinacion = ?, tipo_inmueble = ?,
            subtipo_funcional = ?, finalidad = ?, base_valor = ?, aplica_niif = ?, regimen_ph = ?,
            estructura_metodo = ?, version = version + 1, updated_at = ?
            WHERE id = ? AND owner_id = ? AND version = ?');
        $query->execute([$data['titulo'], $data['tipo'], $data['direccion'], $data['municipio'],
            $data['observaciones'], $data['tipo_derecho'], $data['tipo_negocio'], $data['destinacion'],
            $data['tipo_inmueble'], $data['subtipo_funcional'], $data['finalidad'], $data['base_valor'],
            $data['aplica_niif'], $data['regimen_ph'], $data['estructura_metodo'], $now, $id, $owner, $version]);
        if ($query->rowCount() !== 1) {
            $this->find($id, $owner);
            throw new HttpException(409, 'Esta ficha cambió en otra pestaña. Copia tus cambios antes de recargar.');
        }
        return ['version' => $version + 1, 'saved_at' => str_replace(' ', 'T', $now) . 'Z'];
    }
}
