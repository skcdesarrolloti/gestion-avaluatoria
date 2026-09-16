<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    $now = gmdate('Y-m-d H:i:s');
    $departmentId = 'geo-bolivar-00000000000000000000';
    $seedNeighborhood = static function (string $cityId, string $name, string $notes) use ($schema, $now): void {
        $id = substr('geo-neigh-' . sha1($cityId . '|' . $name), 0, 32);
        $schema->db->prepare('INSERT IGNORE INTO master_neighborhoods
            (id, city_id, name, active, notes, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?)')
            ->execute([$id, $cityId, $name, 'Si', $notes, $now, $now]);
    };
    $schema->db->prepare('INSERT IGNORE INTO master_departments
        (id, code, name, active, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?)')
        ->execute([$departmentId, '13', 'Bolívar', 'Si', $now, $now]);

    $cities = [
        ['13001', 'Cartagena de Indias'], ['13006', 'Achí'], ['13030', 'Altos del Rosario'],
        ['13042', 'Arenal'], ['13052', 'Arjona'], ['13062', 'Arroyohondo'],
        ['13074', 'Barranco de Loba'], ['13140', 'Calamar'], ['13160', 'Cantagallo'],
        ['13188', 'Cicuco'], ['13212', 'Córdoba'], ['13222', 'Clemencia'],
        ['13244', 'El Carmen de Bolívar'], ['13248', 'El Guamo'], ['13268', 'El Peñón'],
        ['13300', 'Hatillo de Loba'], ['13430', 'Magangué'], ['13433', 'Mahates'],
        ['13440', 'Margarita'], ['13442', 'María La Baja'], ['13458', 'Montecristo'],
        ['13468', 'Mompós'], ['13473', 'Morales'], ['13490', 'Norosí'], ['13549', 'Pinillos'],
        ['13580', 'Regidor'], ['13600', 'Río Viejo'], ['13620', 'San Cristóbal'],
        ['13647', 'San Estanislao'], ['13650', 'San Fernando'], ['13654', 'San Jacinto'],
        ['13655', 'San Jacinto del Cauca'], ['13657', 'San Juan Nepomuceno'],
        ['13667', 'San Martín de Loba'], ['13670', 'San Pablo'], ['13673', 'Santa Catalina'],
        ['13683', 'Santa Rosa'], ['13688', 'Santa Rosa del Sur'], ['13744', 'Simití'],
        ['13760', 'Soplaviento'], ['13780', 'Talaigua Nuevo'], ['13810', 'Tiquisio'],
        ['13836', 'Turbaco'], ['13838', 'Turbaná'], ['13873', 'Villanueva'], ['13894', 'Zambrano'],
    ];
    foreach ($cities as [$code, $name]) {
        $cityId = 'geo-city-' . $code . str_repeat('0', 18);
        $schema->db->prepare('INSERT IGNORE INTO master_cities
            (id, department_id, code, name, active, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?)')
            ->execute([$cityId, $departmentId, $code, $name, 'Si', $now, $now]);
        $seedNeighborhood($cityId, 'Cabecera municipal', 'Sector base inicial para avanzar en el expediente.');
    }

    $cartagenaId = 'geo-city-13001' . str_repeat('0', 18);
    foreach (['Centro', 'Bocagrande', 'Castillogrande', 'El Laguito', 'Getsemaní', 'Manga',
        'Pie de la Popa', 'Crespo', 'Marbella', 'Torices', 'La Boquilla', 'El Cabrero',
        'La Matuna', 'El Bosque', 'Alto Bosque', 'Ternera', 'Los Alpes', 'Blas de Lezo',
        'Santa Lucía', 'San Fernando', 'Olaya Herrera', 'El Pozón', 'Nelson Mandela',
        'Bayunca', 'Pasacaballos'] as $sector) {
        $seedNeighborhood($cartagenaId, $sector, 'Sector urbano inicial de Cartagena.');
    }
};
