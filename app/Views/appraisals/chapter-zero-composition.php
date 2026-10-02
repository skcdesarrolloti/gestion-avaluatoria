<div class="mt-5 scroll-mt-6 grid gap-5 md:grid-cols-2" x-show="configTab === 'metodo'" x-cloak>
    <?php foreach (['tipo_negocio', 'tipo_inmueble', 'subtipo_funcional'] as $name) {
        require BASE_PATH . '/app/Views/appraisals/chapter-zero-select-field.php';
    } ?>
    <label class="label">Unidades inmobiliarias principales
        <input class="input" type="number" name="igac_property_units_count" min="0" max="50"
            x-model.number="propertyUnits" placeholder="Ej. 3">
        <span class="mt-1 block text-xs leading-5 text-slate-500">
            Casa principal + 2 apartamentos construidos = 3 unidades principales; después se nombra y clasifica cada una.
        </span>
    </label>
    <label class="label">Anexos existentes
        <input class="input" type="number" name="igac_annex_units_count" min="0" max="50"
            x-model.number="annexUnits" placeholder="Ej. 1">
        <span class="mt-1 block text-xs leading-5 text-slate-500">
            Usa anexos para parqueaderos, depósitos, piscinas, kioscos, ramadas o mejoras accesorias, no para apartamentos independientes.
        </span>
    </label>
    <?php $name = 'estructura_metodo'; require BASE_PATH . '/app/Views/appraisals/chapter-zero-select-field.php'; ?>
    <div class="rounded-xl border border-blue-100 bg-blue-50 p-4 text-sm leading-6 text-blue-950 md:col-span-2">
        Se prepararán <strong x-text="propertyUnits || 0"></strong> unidad(es) principal(es)
        y <strong x-text="annexUnits || 0"></strong> anexo(s). Guarda este bloque para crear los campos
        y describirlos aquí mismo; 3.1, 3.3 y 8 tomarán esta definición como punto de partida.
    </div>
    <div id="unidades-capitulo-1" class="scroll-mt-6 rounded-xl border border-slate-200 bg-white p-4 md:col-span-2"
        x-init="$nextTick(() => { if (<?= ($_GET['from'] ?? '') === 'metodologia' ? 'true' : 'false' ?>) $el.scrollIntoView() })">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <p class="eyebrow">Definición temprana de unidades y anexos</p>
                <h3 class="mt-2 text-lg font-semibold text-slate-950">Qué compone el predio</h3>
                <p class="mt-2 text-sm leading-6 text-slate-600">
                    Esta información alimenta la descripción del numeral 3 y la ruta metodológica del numeral 8. Los anexos pueden integrarse al inmueble principal o valorarse por separado solo cuando el analista lo decida.
                </p>
            </div>
            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600" x-text="(propertyUnits || 0) + ' principal(es) + ' + (annexUnits || 0) + ' anexo(s)'"></span>
        </div>
        <?php if ($unitDefinitionUnits === []): ?>
            <p class="mt-4 rounded-lg border border-dashed border-slate-300 p-4 text-sm text-slate-600" x-show="(propertyUnits || 0) + (annexUnits || 0) > 0">Define las cantidades de unidades y anexos, guarda el expediente, y aquí aparecerán los campos para describirlos.</p>
        <?php else: ?>
            <p class="mt-4 rounded-lg border border-dashed border-slate-300 p-4 text-sm text-slate-600" x-show="(propertyUnits || 0) + (annexUnits || 0) === 0">No hay componentes activos. Si necesitas describir unidades, define la cantidad arriba y guarda para crear los campos.</p>
            <?php foreach (['property' => 'Unidades principales', 'annex' => 'Anexos y mejoras'] as $kind => $groupTitle): ?>
            <section class="mt-6 grid gap-4" x-show="<?= $kind === 'annex' ? 'annexUnits' : 'propertyUnits' ?> > 0">
                <h4 class="border-b border-slate-200 pb-3 text-lg font-semibold"><?= e($groupTitle) ?></h4>
                <?php foreach (array_filter($unitDefinitionUnits, static fn ($u) => $u['unit_kind'] === $kind) as $unit): ?>
                    <?php $key = ($unit['unit_kind'] === 'annex' ? 'annex' : 'property') . '-' . (int) $unit['unit_index']; ?>
                    <?php $igacCategory = $igacCategoryValue($unit); ?>
                    <?php require __DIR__ . '/chapter-zero-unit-fields.php'; ?>
                <?php endforeach; ?>
            </section>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

