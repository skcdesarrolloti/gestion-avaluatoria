<p class="font-semibold">Sugerencia a partir del expediente: <?= e((string) ($methodologyDecision['recommended_method'] ?? 'Pendiente')) ?></p>
<p class="mt-2 text-sm"><?= e((string) ($methodologyDecision['reason'] ?? 'Completa los datos del encargo para consultar la recomendación.')) ?></p>
<p class="mt-2 text-sm">Esta consulta usa los datos registrados; no modifica el método elegido ni requiere volver a escribirlos.</p>
<?php if ($configItems !== []): ?>
<details class="mt-3 rounded-lg border bg-white p-3">
    <summary class="min-h-11 cursor-pointer font-semibold">Datos utilizados · solo consulta</summary>
    <dl class="mt-3 grid gap-3 sm:grid-cols-2">
        <?php foreach ($configItems as $item): ?>
        <div><dt class="text-sm font-semibold"><?= e((string) ($item['label'] ?? 'Dato')) ?></dt><dd class="text-sm"><?= e((string) ($item['value'] ?? 'Pendiente')) ?><?php if (($item['state'] ?? '') !== 'Completo'): ?> · <?= e((string) ($item['state'] ?? 'Pendiente')) ?><?php endif; ?></dd></div>
        <?php endforeach; ?>
    </dl>
</details>
<?php endif; ?>
