<?php
declare(strict_types=1);
use App\Database\Schema;

return static function (Schema $schema): void {
    $schema->db->exec("CREATE TABLE IF NOT EXISTS valuation_glossary_terms (
        slug VARCHAR(160) PRIMARY KEY,
        term VARCHAR(180) NOT NULL,
        definition TEXT NOT NULL,
        source_note VARCHAR(260) NOT NULL DEFAULT '',
        created_by INT NULL,
        sort_order SMALLINT UNSIGNED NOT NULL DEFAULT 0,
        active TINYINT(1) NOT NULL DEFAULT 1,
        created_at DATETIME NOT NULL,
        updated_at DATETIME NOT NULL,
        INDEX idx_valuation_glossary_active (active, sort_order),
        INDEX idx_valuation_glossary_term (term)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    $now = gmdate('Y-m-d H:i:s');
    $source = 'Tomado de la Norma Técnica Sectorial NTS M 01 – Procedimiento y Metodologías para la realización de Avalúos de Bienes Inmuebles Urbanos a Valor de Mercado.';
    $rows = [
        ['Altura Permitida', 'Distancia vertical comprendida entre la cota rasante del nivel del piso a la cumbrera de la edificación determinada por las normas urbanísticas del sector. Puede expresarse en número de pisos, metros o coeficientes.'],
        ['Área', 'Área: Corresponde a la cabida superficiaria del terreno o de la construcción objeto de valuación.'],
        ['Área Privada Construida', 'Corresponde a la cabida superficiaria del terreno o de la construcción objeto de valuación.'],
        ['Área Privada Libre', 'Extensión superficiaria privada semi-descubierta o descubierta, excluyendo los bienes comunes localizados dentro de sus linderos, de conformidad con las normas legales.'],
        ['Bienes Comunes', 'Partes del edificio o conjunto sometido al régimen de propiedad horizontal pertenecientes en proindiviso a todos los propietarios de bienes privados, que por su naturaleza o destinación permiten o facilitan la existencia,estabilidad, funcionamiento, conservación, seguridad, uso, goce o explotación de los bienes de dominio particular.'],
        ['Costo de Reposición', 'Es la cantidad monetaria necesaria para adquirir un bien sustituto, es decir, un bien inmueble que represente un grado de satisfacción similar y comparable al bien objeto de valuación.'],
        ['Costo de Reposición a Nuevo', 'Costo actual del bien (edificación) similar nuevo que represente la utilidad equivalente más cercana al que sea objeto de valuación.'],
        ['Defectos Constructivos', 'Anomalías que pueden causar daños efectivos o representar amenazas potenciales a la salud y la seguridad del usuario, derivados de fallas de la construcción, o del material aplicado en su ejecución.'],
        ['Depreciación', 'Pérdida del valor del inmueble en función del paso del tiempo y del uso, resultante del desgaste de las partes constructivas de las edificaciones, del deterioro o mutilación y de cualquier forma de obsolescencia.'],
        ['Dominio', 'Propiedad o derecho real en una cosa corporal para gozar y disponer de ella arbitrariamente, no siendo contra la ley o contra el derecho ajeno.'],
        ['Edad Aparente', 'Edad atribuida a un inmueble de modo que represente la intensidad de uso, funcionalidad, arquitectura, materiales empleados, y condiciones observadas entre otros. Puede llegar a diferir de la edad real.'],
        ['Edad Real', 'Tiempo transcurrido desde la conclusión de la construcción hasta la fecha de visita o inspección de referencia debidamente soportada en documentos legales.'],
        ['Edificio', 'Construcción de uno o varios pisos levantados sobre un lote o terreno, cuya estructura comprende un número plural de unidades independientes, aptas para ser usadas de acuerdo con su destino natural o convencional, además de áreas y servicios de uso y utilidad general. Una vez sometido al régimen de propiedad horizontal, se conforma por bienes privados o de dominio particular y por bienes comunes.'],
        ['Enfoque Valuatorio', 'Se refiere al marco conceptual dentro del cual se ha de abordar la valuación (Mercado, Costos o Renta).'],
        ['Equipamiento Comunal Privado', 'Es el conjunto de áreas de uso restringido de una comunidad, que suplen o complementan las necesidades de un desarrollo.'],
        ['Equipamiento Comunal Público', 'Es el conjunto de áreas de uso público de la comunidad, supliendo o complementando sus necesidades.'],
        ['Especificaciones de la Valuación', 'Consiste en la descripción o en la determinación de las características de la valuación y está relacionada tanto con el desempeño (compromiso) del valuado, como con el mercado y la información que pueda ser extraída de él.'],
        ['Estado de Conservación', 'Descripción del estado físico actual de los elementos que conforman la construcción (estructura, instalaciones, cubierta, acabados, entre otros), en concordancia con su mantenimiento.'],
        ['Frente', 'Lindero de un terreno sobre una vía.'],
        ['Frente Tipo', 'Lindero de un terreno sobre una vía cuya dimensión es la más frecuente o que con mayor frecuencia se repite en un entorno especifico.'],
        ['Habitabilidad', 'Es un conjunto de condiciones físicas y no físicas que permiten la permanencia humana en un lugar, su supervivencia y en un grado u otro la gratificación de la existencia.'],
        ['Infraestructura Física', 'Son las obras de servicios públicos como acueducto, alcantarillado, energía, vías, teléfonos, gas, etc.'],
        ['Inmueble Tipo', 'Inmueble representativo de un sector, cuyas características son adoptadas como patrón o como referencia de la valuación.'],
        ['Inmueble Urbano', 'Predio situado dentro del perímetro urbano definido por la ley.'],
        ['Intervalo de Proyección', 'Estimativa de un intervalo de valores, a partir de los datos de mercado observados, dentro del cual nuevos datos dentro del mismo contexto estarán contenidos, con una determinada probabilidad.'],
        ['Localización', 'Nombre de la zona, comuna o localidad en donde se ubica el bien inmueble objeto de la valuación de acuerdo con la división política del municipio.'],
        ['Lote de Terreno', 'Porción de terreno resultante del fraccionamiento del suelo urbano, debidamente identificado e individualizado física y jurídicamente.'],
        ['Lote Urbanizable', 'Lote urbano susceptible de un desarrollo urbanístico o adecuación para usos urbanos según las normas urbanísticas del sector.'],
        ['Mantenimiento', 'Acciones preventivas o correctivas necesarias para preservar las condiciones normales de utilización del bien.'],
        ['Mayor y Mejor Uso', 'Principio valuatorio que consiste en seleccionar dentro de un conjunto de usos, el uso más probable de un bien, físicamente posible, justificado adecuadamente, jurídica y legalmente permitido, financieramente viable y que da como resultado el mayor valor del bien valuado.'],
        ['Mercado', 'Entorno en el que se intercambian materias primas, bienes elaborados y servicios entre compradores y vendedores a través de un mecanismo de establecimiento de precios.'],
        ['Metodologías Valuatorias', 'Se refiere a los procedimientos particulares que dentro de un enfoque se pueden utilizar para llevar a cabo el proceso de estimación de valor.'],
        ['Modelo Estático', 'Modelo que utiliza formulas simplificadas y que no se tiene en cuenta el tiempo de concurrencia de los ingresos y gastos.'],
        ['Nivel de Fundamentación', 'Debe entenderse como el grado de solidez de la valuación en cuanto a los principios y cimentación que la sustentan y los datos que la respaldan.'],
        ['Nivel de Precisión', 'Corresponde al grado de precisión del resultado de la valuación, depende exclusivamente de las características de la investigación, de los datos disponibles y la muestra recolectada; por consiguiente, no es posible su fijación a priori.'],
        ['Normas Urbanísticas', 'Están definidas según las unidades de actuación urbana, y en total concordancia con la estructura general del suelo, cuyo objetivo fundamental es el de regular y encausar el desarrollo físico del municipio en su contexto urbano, buscando el mejoramiento de la calidad de vida de sus gentes, estableciendo los procedimientos y requisitos que se deben cumplir cuando se pretenda urbanizar, construir, ampliar, modificar, adecuar, reparar o demoler edificaciones y determinar las sanciones que se impondrán por su incumplimiento.'],
        ['Outlier', 'Punto atípico, identificado como extraño de la masa de datos, que, al ser retirado de la muestra, mejora la calidad de ajuste del modelo.'],
        ['Patrón Constructivo', 'Características de las edificaciones en función de las especificaciones técnicas de los proyectos, de los materiales, la ejecución y mano de obra efectivamente utilizados en la construcción, además de las características culturales y socioeconómicas de la ciudad y del sector en el cual se encuentran levantadas.'],
        ['Polo de Influencia', 'Lugar que, por sus características, influye en los valores de los inmuebles, en la medida de su proximidad.'],
        ['Predio', 'Es un inmueble no separado por otro predio público o privado, con o sin construcciones y/o edificaciones, perteneciente a personas naturales o jurídicas. El predio mantiene su unidad, aunque este atravesado por corrientes de agua.'],
        ['Proyectado Hipotético', 'Consiste en la formulación del proyecto urbanístico a partir del cual se formulan las hipótesis y los modelos a ser desarrollados en la técnica de Desarrollo Potencial aplicada al inmueble que ha de valuarse.'],
        ['Punto Influyente', 'Punto atípico que, cuando es retirado de la muestra, altera significativamente los parámetros estimados en la estructura lineal del modelo.'],
        ['Régimen de Propiedad Horizontal', 'Sistema jurídico que regula el sometimiento a propiedad horizontal de un edificio o conjunto, construido o por construirse.'],
        ['Tendencia Central', 'Son indicadores estadísticos que muestran hacia qué valor (o valores) se agrupan los datos.'],
        ['Valor', 'Representa el precio más probable que compradores y vendedores establecerán para un bien o servicio que está disponible para su compra. El valor asigna un precio hipotético o teórico, que será el que con mayor probabilidad acogerán los compradores y vendedores para el bien o servicio. De modo que el valor no es un hecho, sino una estimación del precio más probable que se pagará por un bien o servicio disponible para su compra en un momento determinado.'],
        ['Variable Clave', 'Variable que es importante para la formación del valor del inmueble.']
    ];
    $query = $schema->db->prepare('INSERT INTO valuation_glossary_terms
        (slug, term, definition, source_note, sort_order, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE term = VALUES(term), definition = VALUES(definition),
        source_note = VALUES(source_note), sort_order = VALUES(sort_order), active = 1, updated_at = VALUES(updated_at)');
    foreach ($rows as $index => $row) {
        $query->execute([valuationGlossarySlug($row[0]), $row[0], $row[1], $source, ($index + 1) * 10, $now, $now]);
    }
};

if (!function_exists('valuationGlossarySlug')) {
function valuationGlossarySlug(string $term): string
{
    $ascii = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', mb_strtolower($term)) ?: mb_strtolower($term);
    $slug = trim(preg_replace('/[^a-z0-9]+/', '-', $ascii) ?? '', '-');
    return $slug !== '' ? substr($slug, 0, 150) : substr(hash('sha256', $term), 0, 32);
}
}
