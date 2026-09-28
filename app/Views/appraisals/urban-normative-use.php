<?php
$urbanUseTip = static fn (string $text): string => '<span class="help-dot" title="' . e($text) . '">?</span>';
$moduleOnePurposeOptions = \App\Support\AppraisalCatalog::selectFields()['finalidad'][4] ?? [];
$moduleOnePurposeKey = (string) ($record['finalidad'] ?? '');
$moduleOnePurposeLabel = (string) ($moduleOnePurposeOptions[$moduleOnePurposeKey] ?? $moduleOnePurposeKey);
$moduleOneUseText = trim((string) ($record['intended_use'] ?? ''));
$urbanUseFromModuleOne = trim($moduleOneUseText !== '' ? $moduleOneUseText : $moduleOnePurposeLabel);
$residentialNormJson = e(json_encode(\App\Support\UrbanResidentialNormCatalog::standards(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '{}');
$residentialModeOptions = \App\Support\UrbanResidentialNormCatalog::modalities();
$potentialRoutes = \App\Support\UrbanNormPotentialCatalog::routesForProfile($profile);
$potentialRoutesJson = e(json_encode($potentialRoutes, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '[]');
$urbanJs = static fn (string $key): string => e(json_encode($value($key), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: "''");
$propertyTypeKey = (string) ($record['tipo_inmueble'] ?? '');
$propertyTypeText = mb_strtolower($propertyTypeKey . ' ' . (string) ($propertyTypeLabel ?? ''));
$isLotSubject = str_contains($propertyTypeText, 'lote') || str_contains($propertyTypeText, 'terreno');
$isLotSubjectJson = $isLotSubject ? 'true' : 'false';
?>
    <section id="uso" x-show="tab === 'uso'"
        x-data="<?php require __DIR__ . '/urban-normative-use-state.php'; ?>"
        class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        <input type="hidden" name="document_slug" value="<?= e($value('document_slug')) ?>">
        <input type="hidden" name="table_slug" value="<?= e($value('table_slug')) ?>">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="eyebrow">Reglamentación y edificabilidad</p>
                <h2 class="mt-2 text-2xl font-semibold">Norma, usos y condiciones factibles</h2>
                <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">Primero conserva el cuadro completo de uso del suelo. En este numeral se dejan los parámetros urbanísticos y condiciones de factibilidad; la cuantificación se desarrolla en el módulo 8.</p>
            </div>
            <span class="rounded-full bg-amber-50 px-3 py-1 text-sm font-semibold text-amber-800">Criterio del perito</span>
        </div>
        <div class="mt-5">
            <?php require __DIR__ . '/urban-normative-use-table.php'; ?>
        </div>
        <?php require __DIR__ . '/urban-normative-route-matrix.php'; ?>
        <div class="mt-5 grid gap-3 md:grid-cols-3">
            <div class="rounded-xl border border-teal-100 bg-teal-50 p-4">
                <p class="text-xs font-semibold uppercase text-teal-800">Tipo del numeral 1 <?= $urbanUseTip('Viene del módulo 1. Orienta, pero la norma puede permitir rutas adicionales.') ?></p>
                <p class="mt-1 text-lg font-semibold text-teal-950"><?= e((string) ($propertyTypeLabel ?? 'No definido')) ?></p>
                <p class="mt-1 text-xs leading-5 text-teal-900"><?= $isLotSubject ? 'Activa lectura de edificabilidad y factibilidad normativa.' : 'Se conserva como soporte de uso; los índices no son obligatorios para esta tipología.' ?></p>
            </div>
            <div class="rounded-xl border border-blue-100 bg-blue-50 p-4 md:col-span-2">
                <p class="text-xs font-semibold uppercase text-blue-800">Pregunta del numeral 5</p>
                <p class="mt-1 text-sm leading-6 text-blue-950"><?= $isLotSubject ? 'Para lote se revisan principal, compatible y complementario con área mínima, frente, ocupación, construcción, altura, aislamientos y condiciones. Sin cuantificar desarrollo en este capítulo.' : 'Para inmueble construido se pega completo el uso del suelo y se incorpora al informe; cualquier análisis cuantitativo se desarrolla después en metodología.' ?></p>
            </div>
        </div>
        <nav class="mt-6 rounded-xl bg-slate-100 p-2" aria-label="Subsecciones de uso del suelo">
            <div class="flex gap-2 overflow-x-auto">
                <?php foreach ([['decision','1. Norma que rige'],['indices','2. Parámetros'],['potencial','3. Lectura pericial'],['informe','4. Texto para informe'],['catalogo','Catálogo']] as [$key, $label]): ?>
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
                <p class="mt-1">Se cargó <?= e($value('applicable_activity') ?: 'la actividad seleccionada') ?> desde <?= e($value('use_regulation_table') ?: 'el cuadro urbano seleccionado') ?>. Revísala en <strong>Texto para informe</strong>; los parámetros físicos quedan en <strong>Parámetros</strong>.</p>
            </div>
        <?php endif; ?>
        <?php include __DIR__ . '/urban-normative-use-decision.php'; ?>
        <?php include __DIR__ . '/urban-normative-use-potential.php'; ?>
        <div x-show="usePane === 'informe'" class="mt-6 grid gap-4">
            <div class="rounded-xl border border-emerald-100 bg-emerald-50 p-4 text-sm leading-6 text-emerald-950">Bloque para informe: norma, compatibilidad y conclusión. El valor se analiza en valoración.</div>
            <?php require __DIR__ . '/urban-normative-use-table.php'; ?>
            <?php $input('use_regulation_table', 'Cuadro y fuente aplicados', 'Se llena al aplicar la ruta normativa'); ?>
            <?php $textarea('permitted_use', 'Uso permitido / compatibilidad sustentada', 'Principal, compatible, complementario, restringido o prohibido, con fuente.', 4); ?>
            <?php $textarea('urban_norms_applied', 'Normas urbanísticas pertinentes aplicadas', 'POT, Decreto 0977, Decreto 1077, Ley 388, resolución, plan parcial, licencia o acto aplicable.', 4); ?>
            <?php $textarea('conclusion', 'Conclusión para el entregable', 'Indica la norma que rige, la ruta adoptada, condiciones de edificabilidad y salvedades. El cálculo se deja para el módulo 8.', 4); ?>
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
            <p class="mt-1 text-sm leading-6 text-slate-600">Consulta de apoyo; la factibilidad normativa se documenta en 5.3.</p>
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
