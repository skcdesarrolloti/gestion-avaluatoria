<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    foreach ([
        'tipo_derecho' => "VARCHAR(40) NOT NULL DEFAULT ''",
        'tipo_negocio' => "VARCHAR(30) NOT NULL DEFAULT ''",
        'destinacion' => "VARCHAR(40) NOT NULL DEFAULT ''",
        'tipo_inmueble' => "VARCHAR(40) NOT NULL DEFAULT ''",
        'subtipo_funcional' => "VARCHAR(50) NOT NULL DEFAULT ''",
        'finalidad' => "VARCHAR(40) NOT NULL DEFAULT ''",
        'base_valor' => "VARCHAR(40) NOT NULL DEFAULT ''",
        'aplica_niif' => "VARCHAR(10) NOT NULL DEFAULT ''",
        'regimen_ph' => "VARCHAR(20) NOT NULL DEFAULT ''",
        'estructura_metodo' => "VARCHAR(40) NOT NULL DEFAULT ''",
    ] as $column => $definition) {
        $schema->addColumn('appraisals', $column, $definition);
    }

    $now = gmdate('Y-m-d H:i:s');
    $rows = [
        ['tipo', 'Tipo de avalúo', 'Derivado metodológico', 'NTS, IGAC e instrucciones del encargo', 'Clasifica el alcance profesional: comercial, posesión, mejoras, remate, negociación, conciliación, seguro, catastral, hipotecario o análisis interno.', 'Solo se conecta con NIIF cuando la finalidad sea contable o financiera.', 1],
        ['tipo_derecho', 'Tipo de derecho', 'Normativo directo', 'Código Civil, Ley 675, contratos, fiducia, IVS 400 y NIIF 16', 'Precisa si se valora dominio, posesión, usufructo, mejoras, arrendamiento, derecho fiduciario u otro derecho económico.', 'Clave para derechos de uso y arrendamientos.', 2],
        ['tipo_negocio', 'Tipo de negocio', 'Operativo interno', 'Contrato, oferta o instrucción del cliente', 'Separa venta y arriendo para no mezclar mercados ni evidencia económica.', 'Relaciona NIC 40 o NIIF 16 según el caso.', 3],
        ['destinacion', 'Destinación', 'Derivado normativo', 'POT, uso económico, NTS, IGAC e IVS', 'Ubica el uso principal: residencial, comercial, institucional, industrial, dotacional, lote o mixto.', 'Impacta NIC 16, NIC 40, NIIF 5 y deterioro.', 4],
        ['tipo_inmueble', 'Tipo de inmueble', 'Operativo interno', 'Categoría RAA, ficha técnica y tipología IGAC', 'Activa atributos y controles esperados para casa, apartamento, lote, local, oficina, bodega, finca, edificio u otros.', 'Ayuda a seleccionar la norma NIIF aplicable.', 5],
        ['subtipo_funcional', 'Subtipo funcional', 'Operativo interno', 'Uso real, uso permitido y clasificación interna del activo', 'Afina la captura de lotes urbanos, rurales, comerciales, industriales o institucionales.', 'Apoya segmentación de mercado y unidad de cuenta.', 6],
        ['finalidad', 'Finalidad', 'Derivado normativo', 'Contrato, proceso judicial, garantía, contabilidad o decisión corporativa', 'Aclara el uso del informe y el nivel de soporte requerido.', 'Determina si NIIF es principal, complementaria o no aplicable.', 7],
        ['base_valor', 'Base/tipo de valor', 'Normativo directo', 'NTS, IGAC, IVS, NIIF 13 y NIC 36', 'Define mercado, razonable, depreciable, renta, catastral o residual.', 'Campo central de medición financiera.', 8],
        ['aplica_niif', '¿Aplica NIIF?', 'Operativo de control', 'Finalidad contable, auditoría o instrucción del encargo', 'Indica si el expediente debe consultar normas de información financiera.', 'Activa consulta NIIF por medición, deterioro, revelación o arrendamiento.', 9],
        ['regimen_ph', 'Régimen PH', 'Normativo directo', 'Ley 675 de 2001', 'Separa unidad privada, bienes comunes y coeficiente cuando hay propiedad horizontal.', 'Puede afectar unidad de cuenta y medición.', 10],
        ['estructura_metodo', 'Estructura del método', 'Derivado metodológico', 'NTS, IGAC e IVS sobre alcance, informe y métodos', 'Ordena si se valora área privada, lote más construcción, solo terreno o un caso por definir.', 'Ayuda a justificar supuestos si aplica NIIF.', 11],
    ];
    $sql = "INSERT INTO valuation_field_considerations (field_key, field_label, classification,
        normative_basis, operational_use, ifrs_relation, sort_order, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE field_label = VALUES(field_label),
        classification = VALUES(classification), normative_basis = VALUES(normative_basis),
        operational_use = VALUES(operational_use), ifrs_relation = VALUES(ifrs_relation),
        sort_order = VALUES(sort_order), updated_at = VALUES(updated_at)";
    $query = $schema->db->prepare($sql);
    foreach ($rows as $row) {
        $query->execute([...$row, $now, $now]);
    }
};
