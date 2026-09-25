<?php
$catalogTabs = \App\Support\AppraisalSpecialAttributeCatalog::typologyTabs();
$activeCatalogType = \App\Support\AppraisalSpecialAttributeCatalog::catalogTypeKey((string) (($attributeUnits[0]['property_type'] ?? '') ?: ($record['tipo_inmueble'] ?? '')));
$catalogCount = static fn (array $groups): int => array_sum(array_map(static fn (array $group): int => count($group[1]), $groups));
?>
<section class="mt-6 rounded-2xl border border-blue-100 bg-blue-50/40 p-4 text-sm text-blue-950"
    x-data="{ catalogVisible: false, catalogType: '<?= e($activeCatalogType) ?>', catalogExpanded: false }">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <h3 class="text-base font-semibold text-slate-950">Catálogo completo de atributos especiales por tipología</h3>
            <p class="mt-1 max-w-4xl leading-6 text-slate-600">
                Consulta la lista completa antes de seleccionar. La pestaña con borde verde corresponde a la tipología activa del sujeto;
                las demás sirven como referencia académica y no se guardan desde aquí.
            </p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button class="btn-secondary" type="button" @click="catalogVisible = !catalogVisible" x-text="catalogVisible ? 'Ocultar catálogo' : 'Mostrar catálogo'"></button>
            <button class="btn-secondary" type="button" x-show="catalogVisible" @click="catalogExpanded = !catalogExpanded" x-cloak
                x-text="catalogExpanded ? 'Plegar grupos' : 'Expandir grupos'"></button>
        </div>
    </div>
    <div class="mt-3 flex flex-wrap gap-2" x-show="catalogVisible" x-cloak>
        <?php foreach ($catalogTabs as $typeKey => $typeLabel): ?>
            <button class="rounded-full border px-3 py-1 text-xs font-semibold" type="button" @click="catalogType = '<?= e($typeKey) ?>'"
                :class="catalogType === '<?= e($typeKey) ?>' ? 'bg-blue-700 text-white border-blue-700' : 'bg-white text-blue-800 border-blue-200'">
                <?= e($typeLabel) ?>
                <?php if ($typeKey === $activeCatalogType): ?><span class="ml-1 rounded-full border border-emerald-500 px-1 text-[10px] text-emerald-700">ACTIVA</span><?php endif; ?>
            </button>
        <?php endforeach; ?>
    </div>
    <div class="mt-4 space-y-3" x-show="catalogVisible" x-cloak>
        <?php foreach ($catalogTabs as $typeKey => $typeLabel): ?>
            <?php $groups = \App\Support\AppraisalSpecialAttributeCatalog::groups($typeKey); ?>
            <div x-show="catalogType === '<?= e($typeKey) ?>'" class="rounded-xl border border-blue-100 bg-white p-4">
                <p class="font-semibold text-slate-950">
                    <?= e($typeLabel) ?> · <?= $catalogCount($groups) ?> atributos en <?= count($groups) ?> grupo(s)
                    <?php if ($typeKey === $activeCatalogType): ?><span class="ml-2 rounded-full bg-emerald-50 px-2 py-0.5 text-xs text-emerald-700">tipología activa</span><?php endif; ?>
                </p>
                <div class="mt-3 grid gap-3 lg:grid-cols-2">
                    <?php foreach ($groups as [$groupLabel, $attributes]): ?>
                        <details class="rounded-xl border border-slate-200 bg-slate-50 p-3" :open="catalogExpanded">
                            <summary class="cursor-pointer font-semibold text-slate-800"><?= e($groupLabel) ?> · <?= count($attributes) ?></summary>
                            <div class="mt-3 space-y-2">
                                <?php foreach ($attributes as [$label, $help, $options]): ?>
                                    <article class="rounded-lg bg-white p-3 shadow-sm">
                                        <strong class="block text-slate-950"><?= e($label) ?></strong>
                                        <span class="mt-1 block text-xs leading-5 text-slate-600"><?= e($help) ?></span>
                                        <span class="mt-2 block text-[11px] font-semibold text-blue-800">
                                            Opciones: <?= e(implode(' · ', array_slice(array_values($options), 0, 6))) ?><?= count($options) > 6 ? '…' : '' ?>
                                        </span>
                                    </article>
                                <?php endforeach; ?>
                            </div>
                        </details>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
