<?php
// Detalle editable de bienes comunes dentro de la pestaña PH.
?>
            <div class="mt-5">
                <h3 class="text-xl font-bold text-slate-950">Detalle editable de bienes comunes, amenidades y soporte</h3>
                <p class="mt-2 text-sm leading-6 text-slate-600">La vista principal muestra bienes esenciales y elementos prioritarios de la tipología seleccionada. Los demás quedan como apoyo opcional para revisión, sin contaminar el resumen principal.</p>
                <button class="mt-3 rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-bold text-slate-700" type="button" @click="showOthers = !showOthers" x-text="showOthers ? 'Ocultar no prioritarios' : 'Mostrar otros elementos no prioritarios'"></button>
                <div class="mt-3 grid gap-2 text-xs sm:grid-cols-5">
                    <p class="rounded-lg bg-emerald-50 p-2 font-semibold text-emerald-800">Verificado: entra como beneficio.</p>
                    <p class="rounded-lg bg-amber-50 p-2 font-semibold text-amber-800">Por confirmar: queda pendiente.</p>
                    <p class="rounded-lg bg-red-50 p-2 font-semibold text-red-800">Alerta: pasa a salvedad.</p>
                    <p class="rounded-lg bg-slate-100 p-2 font-semibold text-slate-700">No aplica: se excluye.</p>
                    <p class="rounded-lg bg-slate-50 p-2 font-semibold text-slate-600">Pendiente: queda como faltante.</p>
                </div>
                <?php foreach ($phCatalog['commonAreaGroups'] as $groupKey => [$groupTitle, $items]): ?>
                    <?php $groupKeys = array_map('strval', array_keys($items)); $groupJson = e(json_encode($groupKeys, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)); ?>
                    <div class="mt-4 rounded-xl border border-slate-200 bg-white shadow-sm">
                        <button class="w-full rounded-xl bg-slate-900 px-4 py-3 text-left text-white" type="button" @click="toggleGroup('<?= e((string) $groupKey) ?>')">
                            <span class="flex flex-wrap items-center justify-between gap-2">
                                <span class="text-xl font-bold"><?= e($groupTitle) ?></span>
                                <span class="flex items-center gap-2">
                                    <span class="rounded-full bg-white/15 px-3 py-1 text-sm font-semibold" x-text="readyCount(<?= $groupJson ?>) + ' de ' + applicableCount(<?= $groupJson ?>) + ' visibles revisados'"></span>
                                    <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-semibold" x-show="hiddenCount(<?= $groupJson ?>) > 0 && !showOthers" x-text="hiddenCount(<?= $groupJson ?>) + ' no prioritarios ocultos'"></span>
                                    <span class="text-sm font-bold" x-text="isOpen('<?= e((string) $groupKey) ?>') ? 'Cerrar' : 'Abrir'"></span>
                                </span>
                            </span>
                        </button>
                        <div class="overflow-x-auto p-3" x-show="isOpen('<?= e((string) $groupKey) ?>')" x-cloak>
                            <table class="w-full min-w-[64rem] text-left text-sm">
                                <thead class="text-xs uppercase text-slate-500"><tr><th class="py-2 pr-3">Elemento</th><th class="py-2 pr-3">Estado e impacto</th><th class="py-2">Evidencia y observación</th></tr></thead>
                                <tbody class="divide-y divide-slate-100">
                                    <?php foreach ($items as $key => $label): ?>
                                        <?php $current = $phMap('common_areas', (string) $key, 'status'); ?>
                                        <tr class="align-top" x-show="isRelevant('<?= e((string) $key) ?>') || showOthers" :class="{'bg-emerald-50': statuses['<?= e((string) $key) ?>'] === 'ok', 'bg-amber-50': statuses['<?= e((string) $key) ?>'] === 'warn', 'bg-red-50': statuses['<?= e((string) $key) ?>'] === 'risk', 'bg-slate-50': statuses['<?= e((string) $key) ?>'] === 'na' || statuses['<?= e((string) $key) ?>'] === ''}">
                                            <td class="w-72 py-3 pr-3 text-base font-bold text-slate-900">
                                                <?= e($label) ?>
                                                <span class="mt-2 inline-flex rounded-full bg-blue-50 px-2 py-1 text-xs font-bold text-blue-800" x-show="priorities.includes('<?= e((string) $key) ?>')">Prioritario para la tipología</span>
                                                <span class="mt-2 inline-flex rounded-full bg-slate-100 px-2 py-1 text-xs font-bold text-slate-600" x-show="!isRelevant('<?= e((string) $key) ?>')">No prioritario para esta tipología</span>
                                            </td>
                                            <td class="w-80 py-3 pr-3">
                                                <select class="input mt-0 min-h-10 py-2 text-sm" x-model="statuses['<?= e((string) $key) ?>']" @change="updateCommonStatus('<?= e((string) $key) ?>', $event.target.value)" name="ph[common_areas][<?= e($key) ?>][status]">
                                                    <?php foreach ($phCatalog['status'] as $value => $option): ?>
                                                        <option value="<?= e($value) ?>" <?= $current === (string) $value ? 'selected' : '' ?>><?= e($option) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <p class="mt-2 rounded-lg bg-white/80 p-2 text-xs font-semibold text-slate-700" x-text="effectFor('<?= e((string) $key) ?>')"></p>
                                                <p class="mt-2 rounded-lg bg-blue-50 p-2 text-xs font-semibold text-blue-800" x-text="'Origen: ' + originLabel('<?= e((string) $key) ?>')"></p>
                                            </td>
                                            <td class="py-3">
                                                <textarea class="input mt-0 min-h-14 py-2 text-sm" rows="2" x-model="notes['<?= e((string) $key) ?>']" @input="updateCommonNotes('<?= e((string) $key) ?>', $event.target.value)" name="ph[common_areas][<?= e($key) ?>][notes]" placeholder="Página, cláusula, visita, fotografía o salvedad"><?= e($phMap('common_areas', (string) $key, 'notes')) ?></textarea>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

