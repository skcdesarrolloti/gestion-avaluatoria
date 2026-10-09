<?php $statisticsAcademy = \App\Support\MarketStatisticsAcademy::steps(); ?>
<?php foreach ($statisticsAcademy as $academyIndex=>$academyStep): ?>
<div x-show="courseStep===<?= $academyIndex ?>">
    <div class="mt-3 space-y-3">
        <h4 class="font-semibold"><?= e($academyStep['title']) ?></h4>
        <div class="mt-3 space-y-3">
            <p class="text-sm">Ejemplo didáctico; no se agrega a tus muestras ni constituye un precio del mercado actual.</p>
            <p><strong>Con un ejemplo: </strong><?= e($academyStep['example']) ?></p>
            <p><strong>Qué significa: </strong><?= e($academyStep['meaning']) ?></p>
            <p><strong>Qué revisamos: </strong><?= e($academyStep['next']) ?></p>
            <details class="rounded-lg border bg-white p-3">
                <summary class="min-h-11 cursor-pointer font-semibold">Fundamento y alcance · exigencia normativa frente a herramienta académica</summary>
                <p class="mt-3"><?= e($academyStep['basis']) ?></p>
                <p class="mt-3 text-sm">Paráfrasis de orientación. Abre el original para cotejar el texto; la cita no acredita por sí sola cumplimiento del expediente.</p>
                <div class="mt-3 flex flex-wrap gap-3">
                    <?php foreach ($academyStep['articles'] as $number): $academyArticle=\App\Services\Resolution941Reading::article($number); ?>
                    <a class="inline-flex min-h-11 items-center underline" href="<?= e($academyArticle['url']) ?>" target="_blank" rel="noopener" data-no-fetch>Resolución 0941 · artículo <?= $number ?> · original</a>
                    <?php endforeach; ?>
                </div>
            </details>
        </div>
    </div>
</div>
<?php endforeach; ?>
