<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    $schema->db->exec("UPDATE midas_documents
        SET layer_group = 'Unidades comuneras de gobierno'
        WHERE layer_group = 'Localidades'
        AND (
            LOWER(source_filename) LIKE '%comunas_ucg%'
            OR LOWER(source_filename) LIKE '%unidades_comuneras%'
            OR LOWER(source_filename) LIKE '%unidad_comunera%'
            OR LOWER(document_code) LIKE '%comunas_ucg%'
            OR LOWER(document_code) LIKE '%unidades_comuneras%'
            OR LOWER(document_code) LIKE '%unidad_comunera%'
        )");
};
