<?php
$currentStep = 'urbana';
$value = static fn (string $key): string => (string) ($profile[$key] ?? '');
$checked = static fn (string $key): string => !empty($profile[$key]) ? 'checked' : '';
$selected = static fn (string $key, string $val): string => (string) ($profile[$key] ?? '') === $val ? 'selected' : '';
$textarea = static function (string $name, string $label, string $placeholder, int $rows = 4, int $limit = 5000) use ($value): void { ?>
    <label class="label"><?= e($label) ?>
        <textarea class="input min-h-32" name="<?= e($name) ?>" rows="<?= e((string) $rows) ?>" maxlength="<?= e((string) $limit) ?>" placeholder="<?= e($placeholder) ?>"><?= e($value($name)) ?></textarea>
    </label>
<?php };
$input = static function (string $name, string $label, string $placeholder = '', string $type = 'text') use ($value): void { ?>
    <label class="label"><?= e($label) ?>
        <input class="input" type="<?= e($type) ?>" name="<?= e($name) ?>" value="<?= e($value($name)) ?>" maxlength="220" placeholder="<?= e($placeholder) ?>">
    </label>
<?php };
?>
<a href="<?= e(url('valuaciones')) ?>" class="inline-flex min-h-11 items-center text-sm font-medium text-teal-800">← Valuaciones</a>
<div class="mt-3 flex flex-wrap items-start justify-between gap-5">
    <div>
        <p class="eyebrow">Numeral 5 · Normatividad urbana</p>
        <h1 class="mt-2 text-3xl font-semibold tracking-tight">Uso del suelo, POT y determinantes urbanísticas</h1>
        <p class="mt-3 max-w-3xl text-slate-600">Registra MIDAS, POT, cuadros, soportes y salvedades urbanísticas para el entregable.</p>
    </div>
    <span class="rounded-full bg-emerald-50 px-3 py-1 text-sm font-semibold text-emerald-700">Capítulo 5</span>
</div>
<?php require BASE_PATH . '/app/Views/appraisals/step-nav.php'; ?>
<?php if ($urbanMessage): ?><p class="mt-6 rounded-xl bg-emerald-50 p-4 text-sm font-semibold text-emerald-800"><?= e($urbanMessage) ?></p><?php endif; ?>
<?php if ($urbanError): ?><p class="mt-6 rounded-xl bg-red-50 p-4 text-sm font-semibold text-red-800"><?= e($urbanError) ?></p><?php endif; ?>
<div class="mt-7 space-y-6" x-data="{ tab: (location.hash || '#midas').slice(1) }">
<nav class="rounded-xl bg-slate-200/70 p-2" aria-label="Submenú normatividad urbana">
    <div class="flex gap-2 overflow-x-auto">
        <?php foreach ([['midas','5.1 Consulta MIDAS'],['pot','5.2 POT y cuadros'],['determinantes','5.3 Determinantes'],['fuentes','5.4 Archivos y normas'],['cierre','5.5 Cierre']] as [$key, $label]): ?>
            <button class="inline-flex min-h-11 shrink-0 items-center rounded-lg px-4 py-2 text-sm font-semibold"
                :class="tab === '<?= e($key) ?>' ? 'bg-blue-700 text-white shadow-sm' : 'bg-white text-blue-800 hover:bg-blue-50'"
                type="button" @click="tab = '<?= e($key) ?>'; history.replaceState(null, '', '#<?= e($key) ?>')"><?= e($label) ?></button>
        <?php endforeach; ?>
    </div>
</nav>
<?php require BASE_PATH . '/app/Views/appraisals/urban-normative-sources.php'; ?>
<form class="space-y-6" method="post" action="<?= e(url('avaluos/' . $record['id'] . '/normatividad-urbana')) ?>" data-module-autosave data-autosave-endpoint="<?= e(url('avaluos/' . $record['id'] . '/normatividad-urbana/autoguardar')) ?>">
    <?= csrf_field() ?>
    <input type="hidden" name="version" value="<?= e((string) ($profile['version'] ?? 0)) ?>">
    <section id="midas" x-show="tab === 'midas'" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        <div class="flex flex-wrap items-start justify-between gap-5">
            <div>
                <p class="eyebrow">Consulta MIDAS</p>
                <h2 class="mt-2 text-2xl font-semibold">Número predial y opción Uso del suelo</h2>
                <p class="mt-2 text-sm leading-6 text-slate-600">Lectura MIDAS por predial y opción Uso del suelo.</p>
                <p class="mt-2 text-xs font-semibold text-teal-800" data-autosave-status>Autoguardado activo</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a class="btn-primary" href="<?= e(url('avaluos/' . $record['id'] . '/bien-sujeto#registro')) ?>">Consultar desde módulo 3</a>
                <label class="inline-flex items-center gap-2 rounded-xl bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">
                    <input type="checkbox" name="midas_consulted" value="1" <?= e($checked('midas_consulted')) ?>> MIDAS consultado
                </label>
            </div>
        </div>
        <div class="mt-6 grid gap-4 md:grid-cols-3">
            <label class="label">Referencia catastral registrada en 3.1
                <input class="input bg-slate-100 text-slate-700" type="text" name="cadastral_reference"
                    value="<?= e($value('cadastral_reference')) ?>" readonly aria-readonly="true">
                <span class="mt-1 block text-xs font-medium text-slate-500">Campo protegido: se actualiza desde Bien sujeto · Registro y catastro.</span>
            </label>
            <label class="label">Referencia catastral corta para MIDAS
                <input class="input" type="text" name="cadastral_reference_short"
                    value="<?= e($value('cadastral_reference_short')) ?>" inputmode="numeric" maxlength="80"
                    placeholder="Ej. 010206780169901">
                <span class="mt-1 block text-xs font-medium text-amber-700">Digite los números sin separaciones: sin guiones, espacios ni puntos.</span>
            </label>
            <label class="label">Referencia catastral larga para MIDAS
                <input class="input" type="text" name="cadastral_reference_long"
                    value="<?= e($value('cadastral_reference_long')) ?>" inputmode="numeric" maxlength="120"
                    placeholder="Ej. referencia predial nacional si MIDAS la devuelve">
                <span class="mt-1 block text-xs font-medium text-slate-500">Si MIDAS la devuelve, se guarda aquí.</span>
            </label>
            <?php $input('midas_query_option', 'Opción seleccionada en MIDAS', 'Uso del suelo'); ?>
            <?php $input('midas_consulted_on', 'Fecha de consulta MIDAS', '', 'date'); ?>
            <?php $input('midas_activity', 'Actividad o uso consultado', 'Ej. oficina, comercio, vivienda, institucional'); ?>
            <?php $input('midas_support_reference', 'Soporte o evidencia MIDAS', 'Ej. captura, PDF, capa o radicado interno'); ?>
            <label class="label">Estado de la fuente
                <select class="input" name="source_status">
                    <?php foreach ($sourceOptions as $key => $label): ?><option value="<?= e($key) ?>" <?= e($selected('source_status', (string) $key)) ?>><?= e($label) ?></option><?php endforeach; ?>
                </select>
            </label>
            <div class="md:col-span-3"><?php $textarea('midas_usage_result', 'Resultado leído en MIDAS · Uso del suelo', 'Uso, área, zona, tratamiento, restricciones o No disponible.', 5); ?></div>
            <?php if ($value('midas_predio_raw') !== '' || $value('midas_usage_raw') !== ''): ?>
                <div class="md:col-span-3 rounded-xl border border-emerald-100 bg-emerald-50 p-4 text-sm text-emerald-900">
                    Lectura guardada: <?= $value('midas_predio_raw') !== '' ? 'predio MIDAS para numeral 3' : '' ?><?= $value('midas_predio_raw') !== '' && $value('midas_usage_raw') !== '' ? ' y ' : '' ?><?= $value('midas_usage_raw') !== '' ? 'reglamentación Uso Suelo para numeral 5' : '' ?>.
                </div>
            <?php endif; ?>
        </div>
    </section>
    <section id="pot" x-show="tab === 'pot'" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        <p class="eyebrow">Cruce normativo</p><h2 class="mt-2 text-2xl font-semibold">POT, cuadros de uso y normas pertinentes</h2>
        <div class="mt-6 grid gap-4 md:grid-cols-2">
            <label class="label">Documento normativo fuente
                <select class="input" name="document_slug"><option value="">Selecciona fuente si aplica</option>
                    <?php foreach ($urbanDocuments as $doc): ?><option value="<?= e($doc['slug']) ?>" <?= e($selected('document_slug', (string) $doc['slug'])) ?>><?= e($doc['title']) ?></option><?php endforeach; ?>
                </select>
            </label>
            <label class="label">Cuadro POT aplicable
                <select class="input" name="table_slug"><option value="">Selecciona cuadro si aplica</option>
                    <?php foreach ($urbanDocuments as $doc): foreach (($doc['tables'] ?? []) as $table): ?><option value="<?= e($table['slug']) ?>" <?= e($selected('table_slug', (string) $table['slug'])) ?>><?= e($table['table_code'] . ' · ' . $table['title']) ?></option><?php endforeach; endforeach; ?>
                </select>
            </label>
            <label class="label">Categoría o actividad del cuadro
                <select class="input" name="category_slug"><option value="">Selecciona categoría si aplica</option>
                    <?php foreach ($urbanCategories as $cat): ?><option value="<?= e($cat['slug']) ?>" <?= e($selected('category_slug', (string) $cat['slug'])) ?>><?= e($cat['table_code'] . ' · ' . $cat['code'] . ' · ' . $cat['name']) ?></option><?php endforeach; ?>
                </select>
            </label>
            <label class="label">Resultado frente al uso consultado
                <select class="input" name="use_cross_result">
                    <?php foreach ($useResults as $key => $label): ?><option value="<?= e($key) ?>" <?= e($selected('use_cross_result', (string) $key)) ?>><?= e($label) ?></option><?php endforeach; ?>
                </select>
            </label>
            <?php $input('land_classification', 'Clasificación del suelo', 'Urbano, expansión, rural, suburbano...'); ?>
            <?php $input('activity_area', 'Área de actividad'); ?>
            <?php $input('normative_zone', 'Zona normativa'); ?>
            <?php $input('urban_treatment', 'Tratamiento urbanístico'); ?>
            <?php $input('urban_license', 'Licencia, acto o soporte urbanístico', 'Licencia, reconocimiento, concepto o No reporta con fuente'); ?>
            <div class="md:col-span-2"><?php $textarea('permitted_use', 'Uso permitido / compatibilidad sustentada', 'Principal, compatible, complementario, restringido o prohibido, con fuente.', 4); ?></div>
            <?php $input('current_use', 'Uso normativo identificado'); ?>
            <?php $input('intended_use', 'Uso pretendido o finalidad del encargo'); ?>
            <?php $input('applicable_activity', 'Actividad aplicable en el cuadro'); ?>
            <?php $input('pot_state', 'Estado del POT o instrumento usado', 'POT vigente, proyecto, resolución especial...'); ?>
            <div class="md:col-span-2"><?php $textarea('urban_norms_applied', 'Normas urbanísticas pertinentes aplicadas', 'POT, Decreto 0977, Decreto 1077, Ley 388, resolución, plan parcial, licencia o acto aplicable.', 5); ?></div>
            <?php $input('use_regulation_table', 'Cuadro de reglamentación identificado', 'Ej. Cuadro No. 7 · Actividad mixta'); ?>
            <div class="md:col-span-2 grid gap-4">
                <?php $textarea('use_principal_text', 'Uso principal leído en MIDAS', 'Actividades principales.', 5, 70000); ?>
                <?php $textarea('use_compatible_text', 'Uso compatible leído en MIDAS', 'Actividades compatibles.', 5, 70000); ?>
                <?php $textarea('use_complementary_text', 'Uso complementario leído en MIDAS', 'Actividades complementarias.', 5, 70000); ?>
                <?php $textarea('use_restricted_text', 'Uso restringido leído en MIDAS', 'Actividades restringidas.', 5, 70000); ?>
                <?php $textarea('use_prohibited_text', 'Uso prohibido leído en MIDAS', 'Actividades prohibidas.', 5, 70000); ?>
            </div>
            <div class="md:col-span-2 grid gap-4 rounded-xl border border-amber-100 bg-amber-50 p-4">
                <h3 class="font-semibold text-amber-950">Campos para potencial constructivo</h3>
                <?php $textarea('norm_unit_basic_text', 'Unidad básica', 'Valores por alcobas, área mínima de unidad o condición equivalente.', 4, 70000); ?>
                <?php $textarea('norm_free_area_text', 'Área libre', 'Área libre por tipología: unifamiliar, bifamiliar, multifamiliar u otra.', 4, 70000); ?>
                <?php $textarea('norm_min_lot_front_text', 'Área y frente mínimos', 'AML, frente mínimo y reglas por tipología.', 4, 70000); ?>
                <?php $textarea('norm_max_height_text', 'Altura máxima', 'Pisos o regla de altura máxima aplicable.', 3, 70000); ?>
                <?php $textarea('norm_construction_index_text', 'Índice de construcción', 'Índice por tipología o condición normativa.', 4, 70000); ?>
                <?php $textarea('norm_isolation_text', 'Aislamientos', 'Antejardín, retiros laterales, posteriores y demás aislamientos.', 4, 70000); ?>
                <?php $textarea('norm_other_potential_text', 'Otros parámetros urbanísticos', 'Estacionamientos, ocupación, cesiones u observaciones.', 4, 70000); ?>
            </div>
        </div>
    </section>
    <section id="determinantes" x-show="tab === 'determinantes'" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        <p class="eyebrow">Determinantes y concepto</p><h2 class="mt-2 text-2xl font-semibold">Planeación, patrimonio, ambiente y riesgo</h2>
        <div class="mt-6 grid gap-4 md:grid-cols-2">
            <?php $input('planning_concept_number', 'Radicado o número de concepto de uso del suelo'); ?>
            <?php $input('planning_concept_date', 'Fecha del concepto oficial', '', 'date'); ?>
            <?php $textarea('official_concept_scope', 'Alcance del concepto oficial', 'Actividad, respuesta, salvedades y autoridad.', 4); ?>
            <?php $textarea('heritage_context', 'Patrimonio, conservación o Centro Histórico', 'Área de influencia, BIC, conservación o autoridad patrimonial.', 4); ?>
            <?php $textarea('environmental_context', 'Determinantes ambientales o protección', 'Rondas, protección, autoridad ambiental, restricciones o pendientes.', 4); ?>
            <?php $textarea('risk_context', 'Riesgo, amenaza o afectaciones externas', 'Amenaza, riesgo, reserva vial, espacio público, servidumbres urbanísticas o cargas externas.', 4); ?>
            <?php $textarea('legal_urban_affectations', 'Afectaciones jurídicas o urbanísticas sustentadas', 'Afectación vial, reserva, protección, patrimonio, servidumbre o carga con fuente.', 4); ?>
        </div>
    </section>
    <section id="cierre" x-show="tab === 'cierre'" class="rounded-2xl border border-emerald-100 bg-emerald-50 p-6 shadow-sm sm:p-8">
        <p class="eyebrow">Texto para el entregable</p><h2 class="mt-2 text-2xl font-semibold">Conclusión urbanística del analista</h2>
        <div class="mt-6 grid gap-4">
            <?php $textarea('restrictions', 'Restricciones o condiciones urbanísticas', 'Condiciones que limitan el uso o desarrollo.', 4); ?>
            <?php $textarea('conclusion', 'Conclusión que debe pasar al numeral 5', 'Conclusión urbanística con fuente, uso y efecto valuatorio.', 5); ?>
            <?php $textarea('source_limitations', 'Limitaciones de la consulta', 'MIDAS no disponible, falta concepto, contradicción o pendiente de Planeación.', 4); ?>
            <?php $textarea('support_summary', 'Soportes revisados', 'MIDAS, concepto, POT, certificado, plano, licencia, resolución o visita.', 3); ?>
            <?php $textarea('analyst_notes', 'Notas internas del analista', 'Observaciones internas.', 3); ?>
        </div>
    </section>
    <div class="flex flex-wrap justify-end gap-3">
        <button class="btn-secondary" type="submit">Guardar numeral 5</button>
        <button class="btn-primary" type="submit" name="next" value="deliverable">Guardar y pasar a Entregable</button>
    </div>
</form>
</div>
<?php require BASE_PATH . '/app/Views/appraisals/report-extra-notes.php'; ?>
