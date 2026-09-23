<?php
$dossierSearch = (string) ($dossierSearch ?? '');
$dossierRows = $dossierRows ?? [];
$dossierSearchUrl = url('avaluos/' . $record['id'] . '/expediente?expediente_q=');
?>
<aside class="space-y-4 lg:sticky lg:top-4 lg:self-start">
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="font-semibold">Estado del expediente</h2>
        <p class="mt-3 text-sm leading-6 text-slate-600">
            Completa 1.1 y 1.2. El guardado principal está al cierre del formulario.
        </p>
        <a class="btn-secondary mt-5 w-full text-center" href="#cierre-expediente">Ir al cierre para guardar</a>
        <p class="mt-3 text-xs font-semibold text-slate-500" data-autosave-status>Autoguardado activo</p>
    </div>
    <p class="px-2 text-xs leading-5 text-slate-500">
        El número de expediente se asigna con el perito responsable y permanece visible en todos los módulos.
    </p>
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
        x-data="{ q: <?= e(json_encode($dossierSearch, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>,
            go() { const base = <?= e(json_encode($dossierSearchUrl, JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)) ?>; window.location.href = base + encodeURIComponent(this.q || '') } }">
        <div class="flex items-start justify-between gap-3">
            <div>
                <h2 class="font-semibold">Expedientes creados</h2>
                <p class="mt-1 text-xs leading-5 text-slate-500">Busca por consecutivo, título, propietario, cliente o municipio.</p>
            </div>
            <span class="rounded-full bg-emerald-50 px-2 py-1 text-xs font-semibold text-emerald-700"><?= e((string) count($dossierRows)) ?></span>
        </div>
        <label class="label mt-4">Buscar expediente
            <input class="input" x-model="q" @keydown.enter.prevent="go()" placeholder="01-2026-09-001, título o propietario">
        </label>
        <div class="mt-3 flex gap-2">
            <button class="btn-primary grow" type="button" @click="go()">Buscar</button>
            <?php if ($dossierSearch !== ''): ?><a class="btn-secondary" href="<?= e(url('avaluos/' . $record['id'] . '/expediente')) ?>">Limpiar</a><?php endif; ?>
        </div>
        <div class="mt-4 max-h-96 space-y-3 overflow-y-auto pr-1">
            <?php if (!$dossierRows): ?>
                <p class="rounded-xl bg-slate-50 p-3 text-sm leading-5 text-slate-600">No hay expedientes creados para esta búsqueda.</p>
            <?php endif; ?>
            <?php foreach ($dossierRows as $row): ?>
                <?php $isCurrent = (string) $row['id'] === (string) $record['id']; ?>
                <article class="rounded-xl border <?= $isCurrent ? 'border-blue-200 bg-blue-50' : 'border-slate-200 bg-slate-50' ?> p-3">
                    <p class="text-xs font-bold <?= $isCurrent ? 'text-blue-800' : 'text-emerald-800' ?>"><?= e((string) $row['expediente_number']) ?></p>
                    <h3 class="mt-1 break-words text-sm font-semibold"><?= e((string) ($row['titulo'] ?: 'Ficha sin título')) ?></h3>
                    <p class="mt-1 break-words text-xs leading-5 text-slate-600">
                        <?= e((string) ($row['property_owner_name'] ?: $row['client_name'] ?: $row['municipio'] ?: 'Sin dato de referencia')) ?>
                    </p>
                    <a class="mt-2 inline-flex min-h-9 items-center rounded-lg bg-white px-3 text-xs font-bold text-teal-800"
                        href="<?= e(url('avaluos/' . $row['id'] . '/expediente')) ?>"><?= $isCurrent ? 'Expediente actual' : 'Abrir expediente' ?></a>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</aside>
