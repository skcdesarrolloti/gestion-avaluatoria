<?php
// Detalle editable de bienes comunes dentro de la pestaña PH.
?>
            <div class="mt-5" x-data='{statusEffects:{"":"Faltante: no alimenta el texto y queda pendiente en la matriz.",ok:"Entra al Entregable como soporte o beneficio verificado.",warn:"Queda en el Entregable como aspecto por confirmar.",risk:"Entra como alerta o salvedad para revisión del analista.",na:"Se excluye del conteo y no se incorpora al Entregable."}}'>
                <h3 class="text-xl font-bold text-slate-950">Detalle editable de bienes comunes, amenidades y soporte</h3>
                <p class="mt-2 text-sm leading-6 text-slate-600">Diligencia o depura cada campo. El encabezado del acordeón cambia de inmediato; al guardar, la matriz superior y el resumen depurado quedan recalculados para el Entregable.</p>
                <div class="mt-3 grid gap-2 text-xs sm:grid-cols-5">
                    <p class="rounded-lg bg-emerald-50 p-2 font-semibold text-emerald-800">Verificado: entra como beneficio.</p>
                    <p class="rounded-lg bg-amber-50 p-2 font-semibold text-amber-800">Por confirmar: queda pendiente.</p>
                    <p class="rounded-lg bg-red-50 p-2 font-semibold text-red-800">Alerta: pasa a salvedad.</p>
                    <p class="rounded-lg bg-slate-100 p-2 font-semibold text-slate-700">No aplica: se excluye.</p>
                    <p class="rounded-lg bg-slate-50 p-2 font-semibold text-slate-600">Pendiente: queda como faltante.</p>
                </div>
                <?php foreach ($phCatalog['commonAreaGroups'] as $groupKey => [$groupTitle, $items]): ?>
                    <?php $groupKeys = array_map('strval', array_keys($items)); $groupStatuses = []; foreach ($groupKeys as $statusKey) $groupStatuses[$statusKey] = $phCommonStatus($statusKey); ?>
                    <details class="mt-4 rounded-xl border border-slate-200 bg-white shadow-sm" x-data="{ statuses: <?= e(json_encode($groupStatuses, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>, readyCount() { return Object.values(this.statuses).filter((status) => ['ok', 'warn', 'risk'].includes(status)).length }, applicableCount() { return Object.values(this.statuses).filter((status) => status !== 'na').length } }" open>
                        <summary class="cursor-pointer list-none rounded-xl bg-slate-900 px-4 py-3 text-white">
                            <span class="flex flex-wrap items-center justify-between gap-2">
                                <span class="text-xl font-bold"><?= e($groupTitle) ?></span>
                                <span class="rounded-full bg-white/15 px-3 py-1 text-sm font-semibold" x-text="readyCount() + ' de ' + applicableCount() + ' aplicables revisados'"></span>
                            </span>
                        </summary>
                        <div class="overflow-x-auto p-3">
                            <table class="w-full min-w-[64rem] text-left text-sm">
                                <thead class="text-xs uppercase text-slate-500"><tr><th class="py-2 pr-3">Elemento</th><th class="py-2 pr-3">Estado e impacto</th><th class="py-2">Evidencia y observación</th></tr></thead>
                                <tbody class="divide-y divide-slate-100">
                                    <?php foreach ($items as $key => $label): ?>
                                        <?php $current = $phMap('common_areas', (string) $key, 'status'); ?>
                                        <tr class="align-top" :class="{'bg-emerald-50': statuses['<?= e($key) ?>'] === 'ok', 'bg-amber-50': statuses['<?= e($key) ?>'] === 'warn', 'bg-red-50': statuses['<?= e($key) ?>'] === 'risk', 'bg-slate-50': statuses['<?= e($key) ?>'] === 'na' || statuses['<?= e($key) ?>'] === ''}">
                                            <td class="w-72 py-3 pr-3 text-base font-bold text-slate-900"><?= e($label) ?></td>
                                            <td class="w-80 py-3 pr-3">
                                                <select class="input mt-0 min-h-10 py-2 text-sm" x-model="statuses['<?= e($key) ?>']" name="ph[common_areas][<?= e($key) ?>][status]">
                                                    <?php foreach ($phCatalog['status'] as $value => $option): ?>
                                                        <option value="<?= e($value) ?>" <?= $current === (string) $value ? 'selected' : '' ?>><?= e($option) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <p class="mt-2 rounded-lg bg-white/80 p-2 text-xs font-semibold text-slate-700" x-text="statusEffects[statuses['<?= e($key) ?>']] || statusEffects['']"></p>
                                            </td>
                                            <td class="py-3">
                                                <textarea class="input mt-0 min-h-14 py-2 text-sm" rows="2" name="ph[common_areas][<?= e($key) ?>][notes]" placeholder="Página, cláusula, visita o salvedad"><?= e($phMap('common_areas', (string) $key, 'notes')) ?></textarea>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </details>
                <?php endforeach; ?>
            </div>

