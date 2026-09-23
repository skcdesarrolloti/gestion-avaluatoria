<?php
$chapterOneAcademy = [
    ['NTS S 03', 'Identificar solicitante y calidad en que actúa, activo, derechos valuados, uso previsto, base de valor, fecha de valoración, alcance, hipótesis, condiciones restrictivas e información examinada.', 'Cada campo del expediente alimenta una subsección de la memoria descriptiva; lo no soportado queda como pendiente o salvedad.'],
    ['NTS I 01', 'El informe debe presentar identificación suficiente del solicitante, objeto, documentos, insumos y condiciones del encargo.', 'Los documentos aportados se relacionan en 1.11 y los datos base pasan al texto consolidado sin copiar soportes completos.'],
    ['Decreto 1420 de 1998 · arts. 21 y 22', 'La lectura debe considerar localización, destinación, características físicas, jurídicas y económicas del inmueble.', 'La localización, tipo de bien, destinación, PH y derecho valuado quedan vinculados con la ficha del bien sujeto.'],
    ['IVS · alcance y reporte', 'El valuador debe comunicar alcance, base de valor, fecha, propósito, supuestos, limitaciones y datos relevantes.', 'El capítulo separa encargo, uso del informe, base de valor, fechas, alcance, limitaciones, hipótesis y soporte documental.'],
];
?>
<details class="mt-5 rounded-xl border border-indigo-100 bg-indigo-50 p-4 text-sm leading-6 text-indigo-950">
    <summary class="cursor-pointer list-none">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div><h3 class="font-semibold">Academia normativa aplicada al numeral 1</h3><p class="mt-1 text-indigo-800">Toca para consultar norma, aplicación y efecto en el entregable.</p></div>
            <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-indigo-800">NTS + Decreto 1420 + IVS</span>
        </div>
    </summary>
    <div class="mt-4 overflow-x-auto rounded-lg border border-indigo-100 bg-white">
        <table class="min-w-full text-left text-xs md:text-sm">
            <thead class="bg-indigo-50 uppercase text-indigo-800"><tr><th class="p-3">Referencia</th><th class="p-3">Qué pide</th><th class="p-3">Cómo se aplica aquí</th></tr></thead>
            <tbody class="divide-y divide-indigo-100"><?php foreach ($chapterOneAcademy as [$ref, $asks, $use]): ?><tr><td class="p-3 font-semibold text-indigo-900"><?= e($ref) ?></td><td class="p-3"><?= e($asks) ?></td><td class="p-3"><?= e($use) ?></td></tr><?php endforeach; ?></tbody>
        </table>
    </div>
</details>
