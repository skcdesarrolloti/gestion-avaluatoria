<?php
$pending = array_values(array_filter($items, static fn (array $item): bool => $item['status'] === 'Pendiente'));
$changed = array_values(array_filter($items, static fn (array $item): bool => $item['changed']));
?>
<section class="space-y-6">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="eyebrow">Mantenimiento</p>
            <h1 class="mt-2 text-3xl font-semibold">Migraciones de base de datos</h1>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                Aplica cambios versionados del esquema sin pegar SQL manual. Úsalo durante despliegues y apágalo al terminar.
            </p>
        </div>
        <span class="rounded-full border border-amber-300 bg-amber-50 px-4 py-2 text-sm font-semibold text-amber-800">
            <?= count($pending) ?> pendientes
        </span>
    </div>

    <?php if ($message): ?>
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm font-semibold text-emerald-800">
            <?= e($message) ?>
        </div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm font-semibold text-red-700">
            <?= e($error) ?>
        </div>
    <?php endif; ?>
    <?php if ($changed): ?>
        <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            Hay migraciones aplicadas cuyo archivo cambió. Revisa el historial antes de ejecutar nuevas migraciones.
        </div>
    <?php endif; ?>

    <div class="card p-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-semibold">Estado del esquema</h2>
                <p class="mt-1 text-sm text-slate-600">
                    El bloqueo de concurrencia evita que dos procesos migren la base al mismo tiempo.
                </p>
            </div>
            <form method="post" action="<?= e(url('mantenimiento/migraciones/ejecutar')) ?>">
                <?= csrf_field() ?>
                <button class="btn-primary" type="submit" <?= $changed ? 'disabled' : '' ?>>
                    Aplicar migraciones
                </button>
            </form>
        </div>

        <div class="mt-6 overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="border-b border-slate-200 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="py-3 pr-4">Archivo</th>
                        <th class="py-3 pr-4">Estado</th>
                        <th class="py-3">Checksum</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td class="py-3 pr-4 font-medium text-slate-800"><?= e($item['version']) ?></td>
                            <td class="py-3 pr-4">
                                <span class="rounded-full px-3 py-1 text-xs font-semibold <?= $item['status'] === 'Pendiente' ? 'bg-amber-50 text-amber-800' : 'bg-emerald-50 text-emerald-800' ?>">
                                    <?= e($item['changed'] ? 'Revisar' : $item['status']) ?>
                                </span>
                            </td>
                            <td class="py-3 font-mono text-xs text-slate-500"><?= e(substr($item['checksum'], 0, 16)) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
