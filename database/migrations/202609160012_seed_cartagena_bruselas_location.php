<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    $now = gmdate('Y-m-d H:i:s');
    $cityId = 'geo-city-13001' . str_repeat('0', 18);
    $localityName = 'Histórica y del Caribe Norte';
    $localityId = substr('geo-loc-' . sha1($cityId . '|' . $localityName), 0, 32);
    $schema->db->prepare('INSERT IGNORE INTO master_localities
        (id, city_id, name, active, notes, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, ?, ?)')
        ->execute([$localityId, $cityId, $localityName, 'Si', 'Localidad urbana del Distrito de Cartagena.', $now, $now]);

    $id = substr('geo-neigh-' . sha1($cityId . '|Bruselas'), 0, 32);
    $schema->db->prepare('INSERT INTO master_neighborhoods
        (id, city_id, locality_id, name, commune_ucg, zone_sector, active, notes, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE locality_id = VALUES(locality_id), commune_ucg = VALUES(commune_ucg),
            zone_sector = VALUES(zone_sector), updated_at = VALUES(updated_at)')
        ->execute([$id, $cityId, $localityId, 'Bruselas', 'UCG 9', 'Zona residencial consolidada',
            'Si', 'Referencia inicial tomada del esquema de ubicación de InversKC.', $now, $now]);
};
