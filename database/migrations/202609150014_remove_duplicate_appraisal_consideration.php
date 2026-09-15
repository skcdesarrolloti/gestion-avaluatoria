<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    $schema->db->exec("DELETE FROM valuation_field_considerations WHERE field_key = 'tipo_avaluo'");
};
