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
    <a class="inline-flex min-h-10 items-center rounded-lg border bg-white px-3 py-2 text-sm font-bold <?= e($class) ?>"
        target="_blank" rel="noopener" href="<?= e($url) ?>"><?= e($label) ?></a>
<?php }; ?>
<section class="rounded-xl border border-blue-100 bg-blue-50 p-4">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <p class="text-xs font-bold uppercase text-blue-800">Abrir fuentes de mercado</p>
            <h3 class="mt-1 text-lg font-semibold text-blue-950">Búsquedas adaptadas a cada portal</h3>
        </div>
        <?php if ($baseQuery !== ''): ?>
            <button type="button" class="btn-secondary min-h-9 text-xs"
                x-on:click="navigator.clipboard?.writeText(<?= e(json_encode($baseQuery, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>)">
                Copiar búsqueda
            </button>
        <?php endif; ?>
    </div>
    <code class="text-anywhere mt-3 block rounded-lg bg-white p-3 font-mono text-sm font-semibold text-slate-900">
        <?= e($baseQuery ?: 'Búsqueda base pendiente: completa tipología, operación, ciudad y barrio para afinarla.') ?>
    </code>
    <?php if ($baseQuery !== ''): ?>
        <a class="btn-primary mt-3" target="_blank" rel="noopener"
            href="<?= e('https://www.google.com/search?q=' . rawurlencode($baseQuery . ' (site:fincaraiz.com.co OR site:metrocuadrado.com OR site:ciencuadras.com OR site:properati.com.co OR site:inmuebles.mercadolibre.com.co)')) ?>">Buscar en todos los portales</a>
        <p class="mt-2 text-sm text-blue-950">Abre una consulta conjunta en Google. Los resultados dependen de su índice; no descarga avisos ni confirma su vigencia. Copia varios avisos y cárgalos juntos abajo.</p>
    <?php endif; ?>
    <div class="mt-4 grid gap-4 lg:grid-cols-2">
        <div>
            <p class="text-xs font-bold uppercase text-blue-800">Portales</p>
            <div class="mt-2 flex flex-wrap gap-2">
                <?php foreach ($portalLinks as $source): ?>
                    <div class="w-full rounded-lg bg-white p-3">
                        <?php $linkButton($source); ?>
                        <p class="mt-1 text-xs font-semibold"><?= e($source['kind'] ?? 'Búsqueda en Google') ?></p>
                        <p class="mt-1 text-sm"><?= e($source['instruction'] ?? 'Abre un aviso y comprueba sus datos.') ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div>
            <p class="text-xs font-bold uppercase text-emerald-800">Inmobiliarias</p>
            <div class="mt-2 flex flex-wrap gap-2">
                <?php foreach ($agencyLinks as $source): $linkButton($source, 'emerald'); endforeach; ?>
            </div>
        </div>
    </div>
</section>
