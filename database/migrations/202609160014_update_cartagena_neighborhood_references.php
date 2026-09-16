<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    $now = gmdate('Y-m-d H:i:s');
    $cartagenaId = 'geo-city-13001' . str_repeat('0', 18);
    $updates = [
        ['Bocagrande', 'UCG 1', 'Residencial, turístico y comercial'],
        ['Castillogrande', 'UCG 1', 'Residencial de alta densidad'],
        ['El Laguito', 'UCG 1', 'Residencial, turístico y hotelero'],
        ['Centro', 'UCG 1', 'Histórico, institucional, turístico y comercial'],
        ['Getsemaní', 'UCG 1', 'Histórico, turístico y comercial'],
        ['La Matuna', 'UCG 1', 'Comercial y de servicios'],
        ['El Cabrero', 'UCG 1', 'Residencial, histórico y turístico'],
        ['Marbella', 'UCG 1', 'Residencial y turístico'],
        ['Crespo', 'UCG 1', 'Residencial y servicios aeroportuarios'],
        ['Manga', 'UCG 1', 'Residencial, comercial y portuario'],
        ['Pie de la Popa', 'UCG 1', 'Residencial y comercial'],
        ['Bruselas', 'UCG 9', 'Zona residencial consolidada'],
        ['El Bosque', 'UCG 10', 'Industrial, portuario y comercial'],
        ['Alto Bosque', 'UCG 10', 'Residencial, comercial e industrial'],
    ];
    $query = $schema->db->prepare('UPDATE master_neighborhoods
        SET commune_ucg = ?, zone_sector = ?, updated_at = ?
        WHERE city_id = ? AND name = ? AND active = ?');
    foreach ($updates as [$name, $ucg, $zone]) {
        $query->execute([$ucg, $zone, $now, $cartagenaId, $name, 'Si']);
    }
};
