<?php
$urbanUseTip = static fn (string $text): string => '<span class="help-dot" title="' . e($text) . '">?</span>';
$moduleOnePurposeOptions = \App\Support\AppraisalCatalog::selectFields()['finalidad'][4] ?? [];
$moduleOnePurposeKey = (string) ($record['finalidad'] ?? '');
$moduleOnePurposeLabel = (string) ($moduleOnePurposeOptions[$moduleOnePurposeKey] ?? $moduleOnePurposeKey);
$moduleOneUseText = trim((string) ($record['intended_use'] ?? ''));
$urbanUseFromModuleOne = trim($moduleOneUseText !== '' ? $moduleOneUseText : $moduleOnePurposeLabel);
?>
    <section id="uso" x-show="tab === 'uso'"
        x-data="{
            usePane: (() => {
                const pane = (location.hash || '').slice(1).replace(/^uso-/, '');
                return ['decision','indices','potencial','informe','catalogo'].includes(pane) ? pane : 'decision';
            })(),
            land: '<?= e($value('land_area_normative_m2')) ?>', affect: '<?= e($value('setback_area_percent')) ?>', net: '<?= e($value('net_land_area_m2')) ?>',
            occ: '<?= e($value('occupancy_index')) ?>', floors: '<?= e($value('max_floors')) ?>', ci: '<?= e($value('construction_index')) ?>',
            maxBuilt: '<?= e($value('normative_max_built_area_m2')) ?>', actual: '<?= e($value('actual_built_area_m2')) ?>',
            sellFactor: '<?= e($value('sellable_area_factor')) ?>', sellable: '<?= e($value('sellable_area_m2')) ?>',
            number(v) { const n = parseFloat(String(v || '').replace(',', '.').replace(/[^0-9.-]/g, '')); return Number.isFinite(n) ? n : null },
            rate(v) { const n = this.number(v); return n === null ? null : (n > 1 ? n / 100 : n) },
            fmt(n) { return Number.isFinite(n) ? n.toFixed(2) : '' },
            netArea() { const manual = this.number(this.net); if (manual !== null) return manual; const land = this.number(this.land), r = this.rate(this.affect); return land === null ? null : Math.max(0, land - (land * (r || 0))) },
            buildIndex() { const manual = this.number(this.ci); if (manual !== null) return manual; const o = this.number(this.occ), f = this.number(this.floors); return o === null || f === null ? null : o * f },
            maxBuild() { const manual = this.number(this.maxBuilt); if (manual !== null) return manual; const net = this.netArea(), index = this.buildIndex(); return net === null || index === null ? null : net * index },
            potential() { const max = this.maxBuild(), actual = this.number(this.actual); return max === null || actual === null ? null : Math.max(0, max - actual) },
            sellableArea() { const manual = this.number(this.sellable); if (manual !== null) return manual; const max = this.maxBuild(), factor = this.number(this.sellFactor); return max === null || factor === null ? null : max * factor }
        }"
        class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        <input type="hidden" name="document_slug" value="<?= e($value('document_slug')) ?>">
        <input type="hidden" name="table_slug" value="<?= e($value('table_slug')) ?>">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="eyebrow">Reglamentación y potencial urbano</p>
                <h2 class="mt-2 text-2xl font-semibold">Norma, índices y potencial</h2>
                <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">Sustenta NTS e índices básicos; no reemplaza cabida ni valor.</p>
            </div>
            <span class="rounded-full bg-amber-50 px-3 py-1 text-sm font-semibold text-amber-800">Criterio del perito</span>
        </div>
        <div class="mt-5 grid gap-3 md:grid-cols-3">
            <div class="rounded-xl border border-teal-100 bg-teal-50 p-4">
                <p class="text-xs font-semibold uppercase text-teal-800">Tipo del numeral 1 <?= $urbanUseTip('Viene del módulo 1. Sirve para orientar el análisis, pero la norma urbana puede permitir rutas adicionales, sobre todo en lotes o inmuebles transformables.') ?></p>
                <p class="mt-1 text-lg font-semibold text-teal-950"><?= e((string) ($propertyTypeLabel ?? 'No definido')) ?></p>
                <p class="mt-1 text-xs leading-5 text-teal-900">Si está mal, se corrige en el encargo.</p>
            </div>
            <div class="rounded-xl border border-blue-100 bg-blue-50 p-4 md:col-span-2">
                <p class="text-xs font-semibold uppercase text-blue-800">Pregunta del numeral 5</p>
                <p class="mt-1 text-sm leading-6 text-blue-950">Primero se deja la norma que rige el predio. Luego se revisa si esa norma solo confirma el uso actual o si permite mayor potencial constructivo. En un lote, esta lectura orienta el mayor y mejor uso y puede preparar el método residual.</p>
            </div>
        </div>
        <nav class="mt-6 rounded-xl bg-slate-100 p-2" aria-label="Subsecciones de uso del suelo">
            <div class="flex gap-2 overflow-x-auto">
                <?php foreach ([['decision','1. Norma que rige'],['indices','2. Índices y áreas'],['potencial','3. Lectura pericial'],['informe','4. Texto para informe'],['catalogo','Catálogo']] as [$key, $label]): ?>
                    <button class="inline-flex min-h-10 shrink-0 items-center rounded-lg px-4 py-2 text-sm font-semibold" type="button"
                        :class="usePane === '<?= e($key) ?>' ? 'bg-teal-700 text-white shadow-sm' : 'bg-white text-teal-800 hover:bg-teal-50'"
                        @click="usePane = '<?= e($key) ?>'; history.replaceState(null, '', '<?= e($key === 'decision' ? '#uso' : '#uso-' . $key) ?>')">
                        <?= e($label) ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </nav>
        <?php if ($value('use_regulation_table') !== '' || $value('applicable_activity') !== '' || $value('permitted_use') !== ''): ?>
            <div class="mt-4 rounded-xl border border-emerald-100 bg-emerald-50 p-4 text-sm leading-6 text-emerald-950">
                <p class="font-semibold">Ruta aplicada al numeral 5</p>
                <p class="mt-1">Se cargó <?= e($value('applicable_activity') ?: 'la actividad seleccionada') ?> desde <?= e($value('use_regulation_table') ?: 'el cuadro urbano seleccionado') ?>. Revísala en <strong>Texto para informe</strong>; los parámetros físicos quedan en <strong>Índices y áreas</strong>.</p>
            </div>
        <?php endif; ?>
        <div x-show="usePane === 'decision'" class="mt-6 grid gap-4 md:grid-cols-2">
            <div class="md:col-span-2 rounded-xl border border-amber-100 bg-amber-50 p-4 text-sm leading-6 text-amber-950">
                <p class="font-semibold">Academia rápida: ¿qué hago en esta pantalla?</p>
                <ol class="mt-2 list-decimal space-y-1 pl-5">
                    <li><strong>Revisa MIDAS solo como evidencia.</strong> Si dice “NO DISPONIBLE”, no pasa nada: deja la constancia en 5.1 y continúa manual.</li>
                    <li><strong>Escoge la vía normativa a probar.</strong> Es la actividad del cuadro POT que quieres revisar: residencial, institucional, comercial, mixta, etc.</li>
                    <li><strong>Marca el resultado.</strong> Define si esa actividad es principal, compatible, complementaria, restringida o prohibida según el cuadro o concepto.</li>
                    <li><strong>Aplica la ruta.</strong> El sistema copia al formulario las reglas del cuadro para que luego calcules índices y potencial.</li>
                </ol>
            </div>
            <label class="label md:col-span-2">Vía normativa a probar <?= $urbanUseTip('Esta no es necesariamente la tipología física del inmueble. Es la ruta del POT que el perito quiere probar: residencial, institucional, comercial, industrial, turística, portuaria o mixta.') ?>
                <select class="input" name="category_slug"><option value="">Selecciona la ruta: residencial, institucional, comercial, industrial, turística, portuaria o mixta</option>
                    <?php foreach (($urbanRouteGroups ?? $urbanCategoryGroups ?? []) as $groupLabel => $cats): ?>
                        <optgroup label="<?= e((string) $groupLabel) ?>">
                            <?php foreach ($cats as $cat): ?><option value="<?= e($cat['slug']) ?>" <?= e($selected('category_slug', (string) $cat['slug'])) ?>><?= e($cat['code'] . ' · ' . $cat['name'] . ' · ' . $cat['table_code']) ?></option><?php endforeach; ?>
                        </optgroup>
                    <?php endforeach; ?>
                </select>
                <span class="mt-1 block text-xs font-medium text-slate-500">Puede diferir del uso actual si es legal, físicamente posible y aporta más valor.</span>
            </label>
            <label class="label">Resultado de la ruta <?= $urbanUseTip('Sale del cuadro o concepto: principal, compatible, complementario, restringido o prohibido. Esto sustenta si la vía puede usarse o debe descartarse.') ?>
                <select class="input" name="use_cross_result">
                    <?php foreach ($useResults as $key => $label): ?><option value="<?= e($key) ?>" <?= e($selected('use_cross_result', (string) $key)) ?>><?= e($label) ?></option><?php endforeach; ?>
                </select>
            </label>
            <label class="label">Uso previsto del informe, traído del módulo 1 <?= $urbanUseTip('Este dato viene del encargo valuatorio. No define por sí solo la norma urbana; solo recuerda para qué se pidió el avalúo. La ruta normativa se escoge en el campo anterior.') ?>
                <textarea class="input min-h-24 bg-slate-50" name="intended_use" rows="3" maxlength="1200" readonly placeholder="Se toma del módulo 1"><?= e($value('intended_use') ?: $urbanUseFromModuleOne) ?></textarea>
                <span class="mt-1 block text-xs font-medium text-slate-500">Si está mal, corrígelo en el módulo 1. Aquí solo se muestra para no perder el contexto del encargo.</span>
            </label>
            <div class="md:col-span-2 flex flex-wrap items-center gap-3 rounded-xl border border-blue-100 bg-blue-50 p-4 text-sm text-blue-950">
                <p class="grow"><strong>Cuándo oprimir este botón:</strong> después de escoger la vía normativa y su resultado. Carga el cuadro y sus parámetros; luego el perito ajusta, adopta o descarta la ruta.</p>
                <button class="btn-primary" type="submit" formaction="<?= e(url('avaluos/' . $record['id'] . '/normatividad-urbana/cuadro/aplicar')) ?>">Aplicar ruta probada</button>
            </div>
            <div class="md:col-span-2 rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm leading-6 text-slate-700">
                <p class="font-semibold text-slate-900">De dónde salen los siguientes campos</p>
                <p class="mt-1">Clasificación del suelo, área de actividad, tratamiento, zona y licencia salen del POT, ficha normativa, concepto de Planeación, licencia, MIDAS si respondió o del cuadro urbano cargado. Si no existe soporte, déjalo pendiente o explica la limitación.</p>
            </div>
            <?php $input('land_classification', 'Clasificación del suelo', 'Urbano, expansión, rural, suburbano...'); ?>
            <?php $input('activity_area', 'Área de actividad'); ?>
            <?php $input('current_use', 'Uso normativo identificado'); ?>
            <?php $input('applicable_activity', 'Actividad específica aplicada'); ?>
            <?php $input('normative_zone', 'Zona normativa'); ?>
            <?php $input('urban_treatment', 'Tratamiento urbanístico'); ?>
            <?php $input('pot_state', 'Instrumento normativo usado', 'POT vigente, plan parcial, resolución, licencia...'); ?>
            <?php $input('urban_license', 'Licencia, acto o soporte urbanístico', 'Licencia, reconocimiento, concepto o No reporta con fuente'); ?>
        </div>
        <?php include __DIR__ . '/urban-normative-use-potential.php'; ?>
        <div x-show="usePane === 'informe'" class="mt-6 grid gap-4">
            <div class="rounded-xl border border-emerald-100 bg-emerald-50 p-4 text-sm leading-6 text-emerald-950">Bloque para informe: norma, compatibilidad y conclusión. El valor va en el capítulo valuatorio.</div>
            <?php $input('use_regulation_table', 'Cuadro y fuente aplicados', 'Se llena al aplicar la ruta normativa'); ?>
            <?php $textarea('permitted_use', 'Uso permitido / compatibilidad sustentada', 'Principal, compatible, complementario, restringido o prohibido, con fuente.', 4); ?>
            <?php $textarea('urban_norms_applied', 'Normas urbanísticas pertinentes aplicadas', 'POT, Decreto 0977, Decreto 1077, Ley 388, resolución, plan parcial, licencia o acto aplicable.', 4); ?>
            <?php $textarea('conclusion', 'Conclusión para el entregable', 'Indica la norma que rige, la ruta adoptada y si existe o no potencial constructivo relevante.', 4); ?>
            <details class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <summary class="cursor-pointer text-sm font-semibold text-slate-900">Ver texto completo del cuadro aplicado</summary>
                <div class="mt-4 grid gap-4 md:grid-cols-2">
                    <?php $textarea('use_principal_text', 'Uso principal del cuadro', 'Actividades principales.', 4, 70000); ?>
                    <?php $textarea('use_compatible_text', 'Uso compatible del cuadro', 'Actividades compatibles.', 4, 70000); ?>
                    <?php $textarea('use_complementary_text', 'Uso complementario del cuadro', 'Actividades complementarias.', 4, 70000); ?>
                    <?php $textarea('use_restricted_text', 'Uso restringido del cuadro', 'Actividades restringidas.', 4, 70000); ?>
                    <div class="md:col-span-2"><?php $textarea('use_prohibited_text', 'Uso prohibido del cuadro', 'Actividades prohibidas.', 4, 70000); ?></div>
                </div>
            </details>
        </div>
        <div x-show="usePane === 'catalogo'" class="mt-6 rounded-xl border border-slate-200 bg-slate-50 p-4">
            <h3 class="font-semibold text-slate-950">Rutas cargadas</h3>
            <p class="mt-1 text-sm leading-6 text-slate-600">Consulta de apoyo; la ruta adoptada se define en 5.3.</p>
            <div class="mt-4 grid gap-3 md:grid-cols-2">
                <?php foreach (($urbanRouteGroups ?? $urbanCategoryGroups ?? []) as $groupLabel => $cats): ?>
                    <div class="rounded-lg border border-slate-200 bg-white p-3">
                        <p class="text-sm font-semibold text-slate-950"><?= e((string) $groupLabel) ?></p>
                        <p class="mt-1 text-xs leading-5 text-slate-600"><?= e(implode(', ', array_map(static fn (array $cat): string => (string) $cat['code'], $cats))) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
