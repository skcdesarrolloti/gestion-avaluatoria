<?php
use App\Support\AppraisalCatalog;

$selected = static fn (string $name, string $value): string => (string) ($record[$name] ?? '') === $value ? 'selected' : '';
$field = static fn (string $name): string => (string) ($record[$name] ?? '');
$count = static fn (string $name): int => max(0, (int) ($record[$name] ?? 0));
$notes = $catalog['notes'] ?? [];
$initial = ['notes' => $notes];
?>
<a href="<?= e(url('valuaciones')) ?>" class="inline-flex min-h-11 items-center text-sm font-medium text-teal-800">← Valuaciones</a>
<div class="mt-3 flex flex-wrap items-start justify-between gap-5">
    <div>
        <p class="eyebrow">Capítulo 0</p>
        <h1 class="mt-2 text-3xl font-semibold tracking-tight">Configuración del avalúo</h1>
        <p class="mt-3 max-w-3xl text-slate-600">
            Primero dejamos clara la evidencia visual, el perito responsable y la ruta técnica antes
            de abrir los capítulos del informe.
        </p>
    </div>
    <span class="rounded-full bg-amber-50 px-3 py-1 text-sm font-semibold text-amber-800">Borrador</span>
</div>

<div class="mt-8 space-y-7" x-data="{
    typologyHint: <?= e(json_encode($field('igac_typology_hint'), JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>,
    igacCategory: <?= e(json_encode($field('igac_category'), JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>,
    propertyUnits: <?= e((string) $count('igac_property_units_count')) ?>,
    annexUnits: <?= e((string) $count('igac_annex_units_count')) ?>
}">
    <?php require BASE_PATH . '/app/Views/appraisals/preclassification.php'; ?>

    <?php require BASE_PATH . '/app/Views/appraisals/unit-tabs.php'; ?>

    <?php require BASE_PATH . '/app/Views/appraisals/photo-upload.php'; ?>
    <?php require BASE_PATH . '/app/Views/appraisals/igac-comparison.php'; ?>

    <form class="grid gap-7 lg:grid-cols-[1fr_18rem]" method="post"
        action="<?= e(url('avaluos/' . $record['id'] . '/capitulo-0')) ?>"
        x-data="{
            busy: false,
            notes: <?= e(json_encode($initial['notes'], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>,
            academy(field, value) { return value && this.notes[field] ? this.notes[field][value] : null }
        }"
        @submit="busy = true">
        <?= csrf_field() ?>
        <input type="hidden" name="version" value="<?= e($record['version']) ?>">
        <input type="hidden" name="igac_category" x-model="igacCategory">
        <input type="hidden" name="igac_typology_hint" x-model="typologyHint">
        <input type="hidden" name="igac_property_units_count" x-model="propertyUnits">
        <input type="hidden" name="igac_annex_units_count" x-model="annexUnits">
        <div id="datos-base-capitulo-0" class="scroll-mt-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            <p class="eyebrow">Configuración</p>
            <h2 class="mt-2 text-2xl font-semibold">Datos base del encargo</h2>
            <div class="mt-6 grid gap-5 md:grid-cols-2">
                <label class="label md:col-span-2">Nombre del inmueble
                    <input class="input" name="titulo" maxlength="160" value="<?= e($field('titulo')) ?>"
                        placeholder="Ej. Lote Bruselas">
                </label>
                <label class="label">Dirección
                    <input class="input" name="direccion" maxlength="220" value="<?= e($field('direccion')) ?>"
                        placeholder="Dirección o referencia de ubicación">
                </label>
                <label class="label">Municipio
                    <input class="input" name="municipio" maxlength="120" value="<?= e($field('municipio')) ?>"
                        placeholder="Municipio">
                </label>
                <label class="label">Perito responsable
                    <select class="input" name="appraiser_id">
                        <option value="">Selecciona perito</option>
                        <?php foreach ($appraisers as $appraiser): ?>
                            <option value="<?= e($appraiser['id']) ?>" <?= $selected('appraiser_id', $appraiser['id']) ?>>
                                <?= e($appraiser['code'] . ' · ' . $appraiser['full_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <?php foreach (['tipo', 'tipo_derecho', 'tipo_negocio', 'finalidad', 'tipo_inmueble',
                    'subtipo_funcional', 'destinacion', 'base_valor', 'aplica_niif', 'regimen_ph',
                    'estructura_metodo'] as $name): ?>
                    <?php [$label, $placeholder, , $help, $options] = AppraisalCatalog::selectFields()[$name]; ?>
                    <?php $label = $name === 'aplica_niif' ? 'Activo empresarial' : $label; ?>
                    <label class="label" x-data="{ selected: <?= e(json_encode($field($name), JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?> }"><?= e($label) ?>
                        <select class="input" name="<?= e($name) ?>" x-model="selected">
                            <option value=""><?= e($placeholder) ?></option>
                            <?php foreach ($options as $value => $text): ?>
                                <option value="<?= e($value) ?>" <?= $selected($name, $value) ?>><?= e($text) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <span class="mt-1 block text-xs leading-5 text-slate-500"><?= e($help) ?></span>
                        <span class="mt-3 block rounded-xl border border-teal-100 bg-teal-50 p-3 text-xs leading-5 text-teal-950"
                            x-show="academy('<?= e($name) ?>', selected)">
                            <strong class="block text-teal-900">Academia del campo</strong>
                            <span class="mt-1 block">
                                <strong>Qué es:</strong>
                                <span x-text="academy('<?= e($name) ?>', selected)?.what"></span>
                            </span>
                            <span class="mt-1 block">
                                <strong>Cuándo aplica:</strong>
                                <span x-text="academy('<?= e($name) ?>', selected)?.when"></span>
                            </span>
                            <span class="mt-1 block">
                                <strong>Soporte:</strong>
                                <span x-text="academy('<?= e($name) ?>', selected)?.basis"></span>
                            </span>
                        </span>
                    </label>
                <?php endforeach; ?>
                <label class="label md:col-span-2">Notas de inspección y configuración
                    <textarea class="input" name="inspection_notes" rows="4" maxlength="2000"
                        placeholder="Anota dudas de campo, tipología probable, componentes o alertas técnicas."><?= e($field('inspection_notes')) ?></textarea>
                </label>
                <label class="label md:col-span-2">Observaciones generales
                    <textarea class="input" name="observaciones" rows="4" maxlength="4000"
                        placeholder="Observaciones generales del encargo."><?= e($field('observaciones')) ?></textarea>
                </label>
            </div>
        </div>
        <aside class="space-y-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="font-semibold">Guardar Capítulo 0</h2>
                <p class="mt-3 text-sm leading-6 text-slate-600">
                    Guarda la configuración y las fotos antes de avanzar al Capítulo 1.
                </p>
                <button class="btn-primary mt-5 w-full" type="submit" :disabled="busy"
                    x-text="busy ? 'Guardando...' : 'Guardar configuración'">Guardar configuración</button>
            </div>
            <p class="px-2 text-xs leading-5 text-slate-500">
                El consecutivo técnico se asignará cuando el expediente quede formalmente configurado.
            </p>
        </aside>
    </form>
</div>
