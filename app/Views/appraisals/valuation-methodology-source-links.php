<?php
$fallbackPortals = [
    ['label' => 'FincaRaiz', 'url' => 'https://www.google.com/search?q=' . rawurlencode('site:fincaraiz.com.co ' . $baseQuery)],
    ['label' => 'Metrocuadrado', 'url' => 'https://www.google.com/search?q=' . rawurlencode('site:metrocuadrado.com ' . $baseQuery)],
    ['label' => 'Ciencuadras', 'url' => 'https://www.google.com/search?q=' . rawurlencode('site:ciencuadras.com ' . $baseQuery)],
    ['label' => 'Properati', 'url' => 'https://www.google.com/search?q=' . rawurlencode('site:properati.com.co ' . $baseQuery)],
    ['label' => 'Mercado Libre', 'url' => 'https://www.google.com/search?q=' . rawurlencode('site:inmuebles.mercadolibre.com.co ' . $baseQuery)],
];
$fallbackAgencies = [
    ['label' => 'Araújo & Segovia', 'url' => 'https://www.araujoysegovia.com/'],
    ['label' => 'SuCasa Inmobiliaria', 'url' => 'https://sucasainmobiliaria.com.co/'],
    ['label' => 'Asesorar Inmobiliaria', 'url' => 'https://asesorarinmobiliaria.com/'],
    ['label' => 'Inmobiliaria Cartagena', 'url' => 'https://www.inmobiliariacartagena.com/'],
    ['label' => 'Vélez Palomino', 'url' => 'https://velezpalomino.com/'],
    ['label' => 'Inverfin', 'url' => 'https://inmobiliariainverfin.com/'],
];
$portalLinks = $portalSources ?: $fallbackPortals;
$agencyLinks = $agencySources ?: $fallbackAgencies;
$linkButton = static function (array $source, string $tone = 'blue'): void {
    $label = (string) ($source['label'] ?? 'Fuente');
    $url = (string) ($source['url'] ?? '#');
    $class = $tone === 'emerald' ? 'border-emerald-200 text-emerald-800 hover:bg-emerald-50' : 'border-blue-200 text-blue-800 hover:bg-blue-50';
?>
    <a class="inline-flex min-h-11 items-center rounded-lg border bg-white px-3 py-2 text-sm font-bold <?= e($class) ?>"
        target="_blank" rel="noopener" href="<?= e($url) ?>"><?= e($label) ?></a>
<?php }; ?>
<section class="rounded-xl border border-blue-100 bg-blue-50 p-4">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <p class="text-xs font-bold uppercase text-blue-800">Abrir fuentes de mercado</p>
            <h3 class="mt-1 text-lg font-semibold text-blue-950">Trabaja una fuente a la vez</h3>
        </div>
        <?php if ($baseQuery !== ''): ?>
            <button type="button" class="btn-secondary min-h-11 text-xs"
                x-on:click="navigator.clipboard?.writeText(<?= e(json_encode($baseQuery, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>)">
                Copiar búsqueda
            </button>
        <?php endif; ?>
    </div>
    <code class="text-anywhere mt-3 block rounded-lg bg-white p-3 font-mono text-sm font-semibold text-slate-900">
        <?= e($baseQuery ?: 'Búsqueda base pendiente: completa tipología, operación, ciudad y barrio para afinarla.') ?>
    </code>
    <p class="mt-3 text-sm leading-6 text-blue-950">Empieza por FincaRaíz: abre un aviso, copia su enlace y vuelve a «Leer un aviso por enlace». Revisa e incorpora la muestra antes de continuar con otro aviso o fuente.</p>
    <p class="mt-2 text-sm leading-6 text-blue-950">La frase de arriba resume el inmueble buscado. Cada fuente indica qué filtros aplica y cuáles debes completar. En las demás fuentes, copia el enlace y el texto del aviso para la captura manual.</p>
    <div class="mt-4 grid gap-4 lg:grid-cols-2">
        <div>
            <p class="text-xs font-bold uppercase text-blue-800">1. Portales · uno por uno</p>
            <ol class="mt-2 space-y-2">
                <?php foreach ($portalLinks as $index => $source): ?>
                    <li class="rounded-lg bg-white p-3">
                        <p class="mb-1 text-xs text-slate-600">Portal <?= e((string) ($index + 1)) ?> de <?= e((string) count($portalLinks)) ?></p>
                        <?php $linkButton($source); ?>
                        <p class="mt-1 text-xs font-semibold"><?= e($source['kind'] ?? 'Búsqueda en Google') ?></p>
                        <p class="mt-1 text-sm"><?= e($source['instruction'] ?? 'Abre un aviso y comprueba sus datos.') ?></p>
                    </li>
                <?php endforeach; ?>
            </ol>
        </div>
        <div>
            <p class="text-xs font-bold uppercase text-emerald-800">2. Inmobiliarias · una por una</p>
            <p class="mt-2 text-sm text-emerald-950">Estos enlaces abren el sitio de cada inmobiliaria. Aplica allí los filtros del expediente; no se envía automáticamente la frase completa.</p>
            <ol class="mt-2 space-y-2">
                <?php foreach ($agencyLinks as $index => $source): ?>
                    <li class="rounded-lg bg-white p-3">
                        <p class="mb-1 text-xs text-slate-600">Inmobiliaria <?= e((string) ($index + 1)) ?> de <?= e((string) count($agencyLinks)) ?></p>
                        <?php $linkButton($source, 'emerald'); ?>
                        <details class="mt-1 text-sm">
                            <summary class="min-h-11 cursor-pointer py-3 font-semibold text-emerald-800">Cómo buscar y capturar aquí</summary>
                            <p class="leading-6"><?= e($source['instruction'] ?? 'Selecciona operación, tipo de inmueble, ciudad y barrio en el buscador del sitio.') ?></p>
                            <p class="mt-2 leading-6">Abre la ficha del inmueble. Copia su enlace y texto; vuelve a «Alternativa: pegar texto de avisos o filas» y revisa los datos antes de pasar a la siguiente fuente.</p>
                        </details>
                    </li>
                <?php endforeach; ?>
            </ol>
        </div>
    </div>
</section>
