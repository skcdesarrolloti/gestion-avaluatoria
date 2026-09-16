<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    $now = gmdate('Y-m-d H:i:s');
    $seedLocality = static function (string $cityId, string $name, string $notes) use ($schema, $now): string {
        $id = substr('geo-loc-' . sha1($cityId . '|' . $name), 0, 32);
        $schema->db->prepare('INSERT IGNORE INTO master_localities
            (id, city_id, name, active, notes, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?)')
            ->execute([$id, $cityId, $name, 'Si', $notes, $now, $now]);
        return $id;
    };
    $assignSectors = static function (string $localityId, string $cityId, array $names) use ($schema): void {
        $query = $schema->db->prepare('UPDATE master_neighborhoods SET locality_id = ?
            WHERE city_id = ? AND name = ? AND (locality_id IS NULL OR locality_id = "")');
        foreach ($names as $name) $query->execute([$localityId, $cityId, $name]);
    };
    $cityQuery = $schema->db->query("SELECT id, code FROM master_cities WHERE department_id = 'geo-bolivar-00000000000000000000'");
    foreach ($cityQuery->fetchAll() as $city) {
        if ((string) $city['code'] !== '13001') {
            $seedLocality((string) $city['id'], 'Cabecera municipal', 'Localidad base para municipios sin división cargada.');
        }
    }

    $cartagenaId = 'geo-city-13001' . str_repeat('0', 18);
    $historicId = $seedLocality($cartagenaId, 'Histórica y del Caribe Norte',
        'Localidad urbana del Distrito de Cartagena.');
    $tourismId = $seedLocality($cartagenaId, 'De la Virgen y Turística',
        'Localidad urbana del Distrito de Cartagena.');
    $bayId = $seedLocality($cartagenaId, 'Industrial y de la Bahía',
        'Localidad urbana del Distrito de Cartagena.');

    $assignSectors($historicId, $cartagenaId, ['Centro', 'Bocagrande', 'Castillogrande',
        'El Laguito', 'Getsemaní', 'Manga', 'Pie de la Popa', 'Crespo', 'Marbella', 'Torices',
        'La Boquilla', 'El Cabrero', 'La Matuna']);
    $assignSectors($tourismId, $cartagenaId, ['Olaya Herrera', 'El Pozón', 'Nelson Mandela', 'Bayunca']);
    $assignSectors($bayId, $cartagenaId, ['El Bosque', 'Alto Bosque', 'Ternera', 'Los Alpes',
        'Blas de Lezo', 'Santa Lucía', 'San Fernando', 'Pasacaballos']);
};
