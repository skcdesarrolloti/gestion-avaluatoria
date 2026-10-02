<?php
$portalName = $source['label'];
$reading = match ($portalName) {
    'FincaRaíz', 'FincaRaiz' => 'Usar filtros de operación, tipo, ciudad y barrio; revisar cada ficha y su código. Incorporar con búsqueda/lector de FincaRaíz o enlace y texto. No tratar tarjetas repetidas como inmuebles distintos.',
    'Metrocuadrado' => 'Usar filtros de operación, tipo, ciudad y sector; verificar la ficha completa y código del aviso. Incorporar con búsqueda/lector de Metrocuadrado; conservar precio, área y contacto propios de la ficha.',
    'Ciencuadras' => 'Para oficinas en venta, Ctrl+A y Ctrl+C en resultados, Ctrl+V normal en el lector Ciencuadras; revisar las sugeridas antes de agregar. Abrir ficha para corroborar componentes que no figuren en la tarjeta.',
    'Properati' => 'Para oficinas en venta, Ctrl+A y Ctrl+C en resultados, Ctrl+V normal en el lector Properati. Separar avisos de la red por URL y código; comprobar posibles republicaciones y abrir ficha original.',
    'Mercado Libre Inmuebles' => 'Para oficinas en venta, Ctrl+A y Ctrl+C en resultados, Ctrl+V normal en el lector Mercado Libre. Conservar código y URL del anuncio, precio y unidad; separar venta y arriendo, verificar ubicación en ficha.',
    default => 'Aplicar filtros disponibles; consultar ficha y vendedor. Capturar enlace y texto del aviso o diligenciar manualmente. No presumir lector de resultados para esta fuente.',
};
$portalPrompt = 'Fuente: ' . $portalName . '. Consulta: ' . ($baseQuery ?: 'Completar operación, tipología y ubicación') . '. ' . $reading
    . "\nUna fila por inmueble. Extraer en columnas: fuente, URL, código, fecha de captura/aviso, operación, tipo, municipio/barrio, dirección y precisión, edificio, precio OFERTADO y unidad, área publicada y su base, administración/IVA, atributos, vendedor y teléfono, evidencia y corroboración."
    . "\nDescuento: no inferirlo del portal. Registrar importe en la misma unidad del precio, tipo otorgado/estimado, fecha, contacto y justificación. Valor negociado = oferta − descuento; vacío si no se confirma."
    . (!empty($guide['is_ph']) ? "\nPH: confirmar régimen; área privada construida y libre, soporte de áreas. Garaje/parqueadero/celda de parqueo: presencia, cantidad, área, incluido en precio o separado, naturaleza jurídica y documento. Depósito/cuarto útil: los mismos campos. Distinguir privado con matrícula propia, privado integrado, común exclusivo y común. No deducir matrícula de un texto publicitario. No presumir privada el área total. Conservar componentes libres/adicionales y fuente de cada dato. Composición semejante no exime de depurar en M4 (art. 19.2.b)." : "\nNPH: terreno, construcción, anexos y cultivos, con áreas y fuentes separados. PH tipo condominio confirmado: tratamiento especial documentado, art. 19.2.c.")
    . "\nNo publicado = dejar vacío y señalar pendiente; No = ausencia confirmada. No inventar áreas, descuento ni precios de anexos. Conservar texto original, URL y fecha; corroborar con vendedor. No trasladar datos entre avisos. La consulta orienta captura; no amplía capacidades del lector ni adopta valor de avalúo.";
?>
<details class="mb-4 rounded-lg border border-teal-200 bg-teal-50 p-3" x-data="{ promptStatus: '' }">
    <summary class="min-h-11 cursor-pointer py-3 font-semibold">Instrucciones precisas para <?= e($portalName) ?> · <?= !empty($guide['is_ph']) ? 'PH' : 'NPH' ?></summary>
    <label class="label">Consulta y campos a extraer de esta fuente
        <textarea class="input mt-2" rows="9" readonly x-ref="portalPrompt" placeholder="Consulta por portal y unidad."><?= e($portalPrompt) ?></textarea>
    </label>
    <button type="button" class="btn-secondary mt-2" @click="navigator.clipboard.writeText($refs.portalPrompt.value).then(() => promptStatus = 'Instrucciones copiadas.').catch(() => promptStatus = 'Selecciona el texto y copia con Ctrl+C.')">Copiar instrucciones de este portal</button>
    <p class="mt-2 text-sm" role="status" x-text="promptStatus"></p>
    <p class="mt-2 text-xs">Guía para filtros, lectura y extracción. El portal no ejecuta este texto como una orden. Pegado de resultados: lector disponible para oficinas en venta en Ciencuadras, Properati y Mercado Libre; luego «Agregar sugeridos» y esperar «Guardado».</p>
</details>
