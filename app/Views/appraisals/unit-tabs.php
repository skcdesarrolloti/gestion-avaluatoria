<?php
$unitLabel = static function (array $unit): string {
    if ($unit['unit_kind'] === 'common') return 'Común';
    return ($unit['unit_kind'] === 'annex' ? 'Anexo ' : 'Unidad ') . (int) $unit['unit_index'];
};
$typologyOptions = $igacTypologiesByCategory ?? [];
?>
<section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8"
    x-data="{
        active: '<?= e($units[0]['id'] ?? '') ?>',
        typologies: <?= e(json_encode($typologyOptions, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>
    }">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="eyebrow">Unidades del predio</p>
            <h2 class="mt-2 text-2xl font-semibold">Tipología por unidad</h2>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                La primera pestaña conserva la información común. Luego cada inmueble o anexo recibe su propia
                clasificación preliminar para no mezclar componentes distintos.
            </p>
        </div>
        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600"><?= count($units) ?> pestaña(s)</span>
    </div>
    <form class="mt-6" method="post" action="<?= e(url('avaluos/' . $record['id'] . '/capitulo-0/unidades')) ?>"
        x-data="{ busy: false }" @submit="busy = true">
        <?= csrf_field() ?>
        <div class="flex gap-2 overflow-x-auto rounded-xl bg-slate-100 p-2" role="tablist">
            <?php foreach ($units as $unit): ?>
                <button class="min-h-11 shrink-0 rounded-lg px-4 py-2 text-sm font-semibold"
                    type="button" @click="active = '<?= e($unit['id']) ?>'"
                    :class="active === '<?= e($unit['id']) ?>' ? 'bg-white text-orange-600 shadow-sm' : 'text-slate-600 hover:bg-white/70'">
                    <?= e($unitLabel($unit)) ?>
                </button>
            <?php endforeach; ?>
        </div>
        <?php foreach ($units as $unit): ?>
            <div class="mt-5 rounded-xl border border-slate-200 p-5" x-show="active === '<?= e($unit['id']) ?>'"
                x-data="{
                    category: <?= e(json_encode((string) $unit['igac_category'], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>,
                    hint: <?= e(json_encode((string) $unit['igac_typology_hint'], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>
                }">
                <div class="grid gap-5 md:grid-cols-2">
                    <label class="label">Nombre de la pestaña
                        <input class="input" name="units[<?= e($unit['id']) ?>][label]" maxlength="120"
                            value="<?= e($unit['label']) ?>" placeholder="<?= e($unitLabel($unit)) ?>">
                    </label>
                    <label class="label">Categoría IGAC de esta unidad
                        <select class="input" name="units[<?= e($unit['id']) ?>][igac_category]" x-model="category"
                            @change="if (!(typologies[category] || []).some(item => item.value === hint)) hint = ''">
                            <option value="">Por definir</option>
                            <?php foreach ($igacCategories as $category): ?>
                                <option value="<?= e($category['code']) ?>" <?= (string) $unit['igac_category'] === (string) $category['code'] ? 'selected' : '' ?>>
                                    <?= e($category['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <label class="label md:col-span-2">Tipología preliminar de esta unidad
                        <select class="input" name="units[<?= e($unit['id']) ?>][igac_typology_hint]"
                            x-model="hint" :disabled="!category">
                            <option value="">Selecciona primero la categoría IGAC</option>
                            <template x-for="item in (typologies[category] || [])" :key="item.value">
                                <option :value="item.value" x-text="item.label"></option>
                            </template>
                        </select>
                        <span class="mt-1 block text-xs leading-5 text-slate-500" x-show="category">
                            <span x-text="(typologies[category] || []).length"></span>
                            tipologías disponibles para esta categoría.
                        </span>
                        <span class="mt-1 block text-xs leading-5 text-slate-500" x-show="!category">
                            Selecciona una categoría para reducir la búsqueda del catálogo IGAC.
                        </span>
                    </label>
                    <label class="label md:col-span-2">Notas de la unidad
                        <textarea class="input" name="units[<?= e($unit['id']) ?>][notes]" rows="3" maxlength="2000"
                            placeholder="Describe rasgos propios de esta unidad o anexo."><?= e((string) $unit['notes']) ?></textarea>
                    </label>
                </div>
            </div>
        <?php endforeach; ?>
        <div class="mt-5 flex justify-end">
            <button class="btn-primary" type="submit" :disabled="busy"
                x-text="busy ? 'Guardando...' : 'Guardar unidades'">Guardar unidades</button>
        </div>
    </form>
</section>
