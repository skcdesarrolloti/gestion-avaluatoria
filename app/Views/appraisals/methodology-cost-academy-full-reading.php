<?php $costAnnexPdf=asset_url('assets/normativa/igac-941-anexo-costo.pdf'); ?>
<details id="costo-anexo-completo" class="mt-4 rounded-xl border bg-white p-4">
    <summary class="min-h-11 cursor-pointer font-semibold">Anexo técnico completo · 2.3 Método del costo · Páginas 22–39</summary>
    <p class="mt-3 text-sm leading-6">Las 18 páginas originales de este apartado se conservan completas: vidas útiles de referencia, vida útil prolongada y remanente, estados de conservación, fórmulas, ejemplos y tablas de Ross–Heideck. Puedes cerrar este bloque cuando termines de consultarlo.</p>
    <div class="mt-3 flex flex-wrap gap-3">
        <a class="inline-flex min-h-11 items-center font-semibold text-blue-800 underline" href="<?= e($costAnnexPdf) ?>" target="_blank" rel="noopener" data-no-fetch>Abrir lectura completa en otra pestaña</a>
        <a class="inline-flex min-h-11 items-center font-semibold text-blue-800 underline" href="<?= e($costAnnexPdf) ?>" download="IGAC-941-anexo-metodo-costo.pdf" data-no-fetch>Descargar apartado completo</a>
    </div>
    <iframe class="mt-4 h-96 w-full rounded-lg border" src="<?= e($costAnnexPdf) ?>#page=1" title="Anexo técnico 2.3 completo: método del costo, páginas originales 22 a 39" loading="lazy"></iframe>
    <p class="mt-3 text-xs leading-5">Extracto de páginas del documento oficial; no es una síntesis ni una transcripción. Conserva la numeración original. Si tu navegador no muestra el lector, usa el enlace para abrirlo o descargarlo. <a class="font-semibold text-blue-800 underline" href="<?= e(\App\Services\CostMethodAcademy::ANNEX_URL) ?>#page=22" target="_blank" rel="noopener" data-no-fetch>Consultar anexo oficial íntegro (62 páginas)</a>.</p>
</details>
