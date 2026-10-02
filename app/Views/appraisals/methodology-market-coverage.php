<?php
$coverage = [
    ['16, 19', 'Similaridad, actualidad y justificación de comparabilidad', 'Tipo, sector, fechas, atributos y observación técnica', 'Captura disponible; selección y suficiencia por sustentar en M4'],
    ['17 a–e', 'Ubicación, precio, negociación, áreas, fuente y fecha', 'Tabla, mapas, áreas por régimen, contactos y descuento con soporte', 'Verificar cada dato y guardar; amarillo indica campos por confirmar'],
    ['17 (análisis b–c)', 'Negociación justificada antes de depurar', 'Descuento, tipo y fuente; oferta menos descuento', 'Cálculo disponible; soporte y confirmación a cargo del analista'],
    ['17 (análisis e–h)', 'Evidencia, corroboración y cuadro de investigación', 'Fotos/soportes, enlace, contacto, evidencia, corroboración y descarga Excel', 'Captura disponible; conservar soporte verificable y confirmar datos'],
    ['17, 19.2 a–b', 'Área privada y depuración PH de componentes', 'Áreas privadas; garajes y depósitos con cantidad, área, inclusión, naturaleza y soporte', 'Captura disponible; valorar y descontar componentes en M4 sigue pendiente'],
    ['19.1, 19.2 c', 'Desagregación NPH o condominio con fundamento', 'Áreas de terreno/construcción y anexos; tratamiento especial', 'Captura disponible; desagregación monetaria por desarrollar en M4'],
    ['19', 'Servicios, accesos, entorno y norma urbanística', 'Atributos: campos con descripción y fuente; factores de análisis', 'Corroborar semejanza y documentar diferencias en M4'],
    ['20–21 y anexo técnico', 'Métodos estadísticos, suficiencia, cálculos y resultados', 'M4 dispone de estadísticos descriptivos iniciales', 'Falta memoria completa de depuración, validación, conclusión y adopción'],
    ['Anexo técnico: estudio de mercado', 'Tipo de dato, % de negociación, componentes y valor integral unitario', 'Tipo de dato y % calculado en M3; cantidad/área/naturaleza de componentes', 'Valores globales/unitarios de anexos y COP/m² privado depurado pendientes de M4'],
    ['Anexo técnico: nota 2 del estudio', 'No aplicar homologación/homogeneización por factores', 'Factor M4 identifica variable de estudio; no multiplica el precio', 'Conservar esta restricción al desarrollar cálculos de M4'],
    ['17, 27–28', 'Fuentes y costo de construcciones cuando la depuración lo requiere', 'Referencias IGAC y soportes disponibles en módulos de costo', 'Costos actualizados y cálculo sustentado pendientes; tipología no prueba precio comercial'],
    ['36–37', 'Derechos, áreas legales y tratamiento del sujeto PH', 'Capítulos 1/3, verificación M1 y alcance/vínculo M2', 'Confrontar documentos; M3 describe comparables y no reemplaza el sujeto'],
];
?>
<details class="my-4 rounded-xl border border-amber-200 bg-amber-50 p-4">
    <summary class="min-h-11 cursor-pointer py-3 font-semibold">Control de cobertura · Resolución 941 de 2026 · Mercado y PH</summary>
    <p class="my-2 text-sm">Revisión de cobertura del flujo, no certificación de cumplimiento. M1/M2 verifican el sujeto, M3 captura el mercado, M4 debe sustentar cálculos y M5 presentar el informe. Los otros métodos y requisitos generales del informe se revisan en su etapa.</p>
    <div class="overflow-x-auto">
        <table class="min-w-[760px] text-left text-sm">
            <thead><tr><th class="p-2">Artículo</th><th class="p-2">Exigencia</th><th class="p-2">Dónde se contempla</th><th class="p-2">Estado y siguiente paso</th></tr></thead>
            <tbody><?php foreach ($coverage as $control): ?><tr class="border-t border-amber-200"><?php foreach ($control as $text): ?><td class="p-2 align-top"><?= e($text) ?></td><?php endforeach; ?></tr><?php endforeach; ?></tbody>
        </table>
    </div>
    <p class="mt-3 text-sm">No se aplica un descuento fijo por anexos ni se adopta un valor sin evidencia. La falta de ofertas independientes requiere investigación y método sustentado; no convierte el componente en cero.</p>
    <a class="mt-2 inline-block min-h-11 py-3 font-semibold text-blue-800" target="_blank" rel="noopener" href="https://camacol.co/sites/default/files/descargables/IGAC-Resolucion-2026-N0000941_20260731_Diario_Oficial-N053573_20260801.pdf">Consultar publicación de la Resolución 941 y su anexo técnico</a>
</details>
