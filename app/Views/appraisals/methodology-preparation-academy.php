<details class="mt-4 rounded-xl border p-3"><summary class="min-h-11 cursor-pointer font-semibold">Academia y sustento · datos, descuento y comparabilidad</summary>
    <div class="mt-3 space-y-3">
        <p>El descuento modifica el precio de oferta; no es un atributo explicativo del inmueble. Vacío indica pendiente; cero debe registrarse expresamente. El valor por m² requiere que precio, área y componentes correspondan a la misma base.</p>
        <p class="font-mono">Valor con descuento = oferta × (1 − descuento % / 100)<br>Valor unitario preliminar = valor con descuento / área publicada</p>
        <?php $preparationAcademy=\App\Support\MarketStatisticsAcademy::steps()[0]; ?>
        <p><strong>Ejemplo:</strong> <?= e($preparationAcademy['example']) ?></p>
        <p><strong>Fuente y alcance:</strong> <?= e($preparationAcademy['basis']) ?></p>
        <div class="flex flex-wrap gap-3"><?php foreach ($preparationAcademy['articles'] as $number): $preparationArticle=\App\Services\Resolution941Reading::article($number); ?><a class="inline-flex min-h-11 items-center underline" href="<?= e($preparationArticle['url']) ?>" target="_blank" rel="noopener" data-no-fetch>Resolución 0941 · artículo <?= (int)$number ?> · fuente</a><?php endforeach; ?></div>
        <details><summary class="min-h-11 cursor-pointer font-semibold">Encargo y unidad de análisis</summary><?php require __DIR__.'/methodology-analysis-preparation.php'; ?></details>
    </div>
</details>
