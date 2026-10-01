<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    $cityId = 'geo-city-13001' . str_repeat('0', 18);
    $exists = $schema->db->prepare('SELECT id FROM master_neighborhoods WHERE city_id = ? AND LOWER(TRIM(name)) = ?');
    $exists->execute([$cityId, 'mamonal']);
    if ($exists->fetchColumn()) return;
    $now = gmdate('Y-m-d H:i:s');
    $schema->db->prepare('INSERT INTO master_neighborhoods
        (id, city_id, locality_id, name, commune_ucg, zone_sector, active, notes, created_at, updated_at)
        VALUES (?, ?, NULL, ?, ?, ?, ?, ?, ?, ?)')->execute([
            substr('geo-neigh-' . sha1($cityId . '|Mamonal'), 0, 32), $cityId, 'Mamonal', '', '', 'Si',
            'Sector incorporado a solicitud del responsable. Delimitación, localidad y UCG pendientes de verificar para cada predio.', $now, $now,
        ]);
};
