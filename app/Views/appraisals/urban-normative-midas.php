    <?php
    $midasFallbackOpen = isset($urbanError) && str_contains((string) $urbanError, 'MIDAS');
    $midasReferenceDigits = preg_replace('/\D+/', '', $value('cadastral_reference_short') ?: $value('cadastral_reference')) ?? '';
    $midasRawTrace = $value('midas_usage_raw') ?: $value('midas_predio_raw');
    $midasUsageTrace = $value('midas_usage_result') ?: $value('midas_result');
    if ($midasUsageTrace === '' && $midasRawTrace !== '') {
        $midasUsageTrace = mb_substr(trim(preg_replace('/\s+/', ' ', $midasRawTrace) ?? $midasRawTrace), 0, 5000);
    }
    $manualValue = static fn (string $key): string => $value($key);
    $manualField = static function (string $key, string $label, string $placeholder, int $rows = 3) use ($manualValue): void { ?>
        <label class="label"><?= e($label) ?>
            <textarea class="input min-h-24" name="midas_manual[<?= e($key) ?>]" rows="<?= e((string) $rows) ?>" maxlength="70000" placeholder="<?= e($placeholder) ?>"><?= e($manualValue($key)) ?></textarea>
        </label>
    <?php };
    $manualInput = static function (string $key, string $label, string $placeholder) use ($manualValue): void { ?>
        <label class="label"><?= e($label) ?>
            <input class="input" name="midas_manual[<?= e($key) ?>]" value="<?= e($manualValue($key)) ?>" placeholder="<?= e($placeholder) ?>">
        </label>
    <?php };
    ?>
    <section id="midas" x-show="tab === 'midas'" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        <input type="hidden" name="midas_query_option" value="<?= e($value('midas_query_option') ?: 'Uso del suelo') ?>">
        <input type="hidden" name="midas_activity" value="<?= e($value('midas_activity')) ?>">
        <div class="flex flex-wrap items-start justify-between gap-5">
            <div>
                <p class="eyebrow">Soporte MIDAS</p>
                <h2 class="mt-2 text-2xl font-semibold">Lectura MIDAS recibida desde el numeral 2</h2>
                <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">La captura principal de MIDAS se hace en el numeral 2. Al procesarla allí, los datos prediales quedan en el numeral 3 y la reglamentación de uso del suelo llega a este numeral 5 para revisión urbana.</p>
                <p class="mt-2 text-xs font-semibold text-teal-800" data-autosave-status>Autoguardado activo</p>
            </div>
            <span class="rounded-full bg-teal-50 px-3 py-1 text-sm font-semibold text-teal-800">Revisión urbana</span>
        </div>
        <?php if ($midasFallbackOpen): ?>
            <div class="mt-5 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm leading-6 text-amber-950">
                <p class="font-semibold">MIDAS no permitió lectura automática.</p>
                <p class="mt-1">Esto es normal. El flujo seguro es manual asistido desde el numeral 2: abre MIDAS, busca la referencia <strong><?= e($midasReferenceDigits) ?></strong>, copia Predios y Uso Suelo y procesa la lectura en el centro MIDAS.</p>
            </div>
        <?php endif; ?>
        <div class="mt-6 grid gap-4 md:grid-cols-3">
            <label class="label">Referencia catastral tomada del módulo 3
                <input class="input bg-slate-100 text-slate-700" type="text" name="cadastral_reference"
                    value="<?= e($value('cadastral_reference')) ?>" readonly aria-readonly="true">
                <span class="mt-1 block text-xs font-medium text-slate-500">Dato informativo. Para MIDAS se usa la referencia corta normalizada sin guiones.</span>
            </label>
            <label class="label">Referencia corta para buscar en MIDAS
                <input class="input" type="text" name="cadastral_reference_short"
                    value="<?= e($value('cadastral_reference_short')) ?>" inputmode="numeric" maxlength="80"
                    placeholder="Ej. 010206780169901">
                <span class="mt-1 block text-xs font-medium text-amber-700">Solo números, sin guiones ni espacios.</span>
            </label>
            <label class="label">Referencia larga si aparece en MIDAS
                <input class="input" type="text" name="cadastral_reference_long"
                    value="<?= e($value('cadastral_reference_long')) ?>" inputmode="numeric" maxlength="120"
                    placeholder="Referencia predial nacional">
                <span class="mt-1 block text-xs font-medium text-slate-500">Campo de respaldo cuando MIDAS muestra referencia predial nacional.</span>
            </label>
            <div class="md:col-span-3 rounded-xl border border-blue-100 bg-blue-50 p-4">
                <div class="flex flex-wrap gap-2">
                    <a class="btn-primary" href="<?= e(url('avaluos/' . $record['id'] . '/sector#midas-centro')) ?>">Ir a MIDAS del numeral 2</a>
                    <a class="btn-secondary" href="https://midas.cartagena.gov.co/#/home" target="_blank" rel="noopener">Abrir MIDAS externo</a>
                    <button class="btn-secondary" type="button" onclick="navigator.clipboard?.writeText('<?= e($midasReferenceDigits) ?>')">Copiar referencia</button>
                </div>
                <p class="mt-3 text-xs font-semibold leading-5 text-blue-900">El numeral 5 no vuelve a capturar Predios. Aquí se revisa lo que el numeral 3 ya distribuyó y se complementa solo la lectura urbanística necesaria para el informe.</p>
            </div>
            <div class="md:col-span-3 rounded-2xl border border-teal-100 bg-teal-50 p-4">
                <div class="grid gap-4 lg:grid-cols-2">
                    <div class="rounded-xl border border-teal-200 bg-white p-4">
                        <p class="text-sm font-semibold text-teal-950">Datos prediales</p>
                        <p class="mt-2 text-sm leading-6 text-teal-900">Se diligencian y se validan en el numeral 3: identificación, ubicación, áreas, tratamiento, riesgos y trazabilidad de Predios.</p>
                    </div>
                    <div class="rounded-xl border border-teal-200 bg-white p-4">
                        <p class="text-sm font-semibold text-teal-950">Uso del suelo</p>
                        <p class="mt-2 text-sm leading-6 text-teal-900">En este numeral se revisan el cuadro de usos, la clasificación normativa, las restricciones, la factibilidad descriptiva y la conclusión del analista.</p>
                    </div>
                </div>
            </div>
            <input type="hidden" name="source_status" value="<?= e($value('source_status') ?: 'pendiente') ?>">
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm leading-6 text-slate-700">
                <p class="font-semibold text-slate-950">Resultado guardado de MIDAS</p>
                <p>Marca fecha y evidencia. Si MIDAS no responde, deja la constancia en el campo de lectura; la decisión normativa se desarrolla en 5.2 y 5.3.</p>
            </div>
            <?php $input('midas_consulted_on', 'Fecha de consulta MIDAS', '', 'date'); ?>
            <?php $input('midas_support_reference', 'Soporte o evidencia', 'Ej. captura, PDF, capa, radicado o nota interna'); ?>
            <label class="md:col-span-3 inline-flex items-center gap-2 rounded-xl bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">
                <input type="checkbox" name="midas_consulted" value="1" <?= e($checked('midas_consulted')) ?>> MIDAS fue consultado o revisado manualmente
            </label>
            <label class="label md:col-span-3">Lectura o resultado de MIDAS para dejar trazabilidad
                <textarea class="input min-h-32" name="midas_usage_result" rows="4" maxlength="5000" placeholder="Pega o resume lo observado: uso, área, zona, tratamiento, restricciones, o deja constancia de No disponible."><?= e($midasUsageTrace) ?></textarea>
                <span class="mt-1 block text-xs font-medium text-slate-500">Si MIDAS respondió “NO DISPONIBLE”, deja ese texto aquí; sirve como evidencia de consulta y salvedad.</span>
            </label>
            <?php if ($value('midas_predio_raw') !== '' || $value('midas_usage_raw') !== ''): ?>
                <div class="md:col-span-3 rounded-xl border border-emerald-100 bg-emerald-50 p-4 text-sm text-emerald-900">
                    Lectura guardada: <?= $value('midas_predio_raw') !== '' ? 'predio MIDAS recibido desde el 2 para numeral 3' : '' ?><?= $value('midas_predio_raw') !== '' && $value('midas_usage_raw') !== '' ? ' y ' : '' ?><?= $value('midas_usage_raw') !== '' ? 'reglamentación Uso Suelo recibida desde el 2 para numeral 5' : '' ?>.
                </div>
            <?php endif; ?>
            <details class="md:col-span-3 rounded-xl border border-slate-200 bg-slate-50 p-4">
                <summary class="cursor-pointer text-sm font-semibold text-slate-900">Revisión manual de Uso Suelo cuando falte un dato puntual</summary>
                <div class="mt-4 grid gap-4 md:grid-cols-2">
                    <?php $manualInput('use_regulation_table', 'Cuadro de reglamentación de usos del suelo', 'Ej. Cuadro No. 7 - Actividad mixta'); ?>
                    <?php $manualInput('land_area_normative_m2', '20 Área Terreno (M2)', 'Ej. 529.00'); ?>
                    <?php $manualInput('actual_built_area_m2', '21 Área Construida (M2)', 'Ej. 329.00'); ?>
                    <?php $manualInput('lot_front_normative_m', 'Frente del lote si MIDAS/cuadro lo informa', 'Ej. 12.00'); ?>
                    <?php $manualField('use_principal_text', 'USO PRINCIPAL', 'Registra el uso principal exactamente como soporte normativo.', 3); ?>
                    <?php $manualField('use_compatible_text', 'USO COMPATIBLE', 'Registra los usos compatibles pertinentes.', 3); ?>
                    <?php $manualField('use_complementary_text', 'USO COMPLEMENTARIO', 'Registra los usos complementarios pertinentes.', 3); ?>
                    <?php $manualField('use_restricted_text', 'USO RESTRINGIDO', 'Registra los usos restringidos pertinentes.', 3); ?>
                    <div class="md:col-span-2"><?php $manualField('use_prohibited_text', 'USO PROHIBIDO', 'Registra los usos prohibidos pertinentes.', 3); ?></div>
                    <?php $manualField('norm_unit_basic_text', 'UNIDAD BÁSICA', 'Registra unidad básica si el cuadro la muestra.', 3); ?>
                    <?php $manualField('norm_free_area_text', 'ÁREA LIBRE', 'Registra área libre, retiros o aislamientos libres.', 3); ?>
                    <?php $manualField('norm_min_lot_front_text', 'ÁREA Y FRENTE MÍNIMOS', 'Registra AML, frente mínimo y condición de lote.', 3); ?>
                    <?php $manualField('norm_max_height_text', 'ALTURA MÁXIMA', 'Registra altura o pisos permitidos.', 3); ?>
                    <?php $manualInput('occupancy_index', 'ÍNDICE DE OCUPACIÓN INFORMADO / REVISADO', 'Opcional: 0,60 o 60% si el soporte lo informa'); ?>
                    <?php $manualInput('max_floors', 'Número de pisos para estimar IC ÷ pisos', 'Ej. 2'); ?>
                    <?php $manualInput('construction_index', 'ÍNDICE DE CONSTRUCCIÓN', 'Ej. 1,20'); ?>
                    <?php $manualField('norm_construction_index_text', 'TEXTO ÍNDICE / ÁREA CONSTRUIBLE', 'Registra el texto completo del índice de construcción o fórmula.', 3); ?>
                    <div class="md:col-span-2"><?php $manualField('norm_isolation_text', 'AISLAMIENTOS / ESTACIONAMIENTOS / OBSERVACIONES', 'Registra aislamientos, estacionamientos y demás reglas útiles para el análisis.', 4); ?></div>
                    <div class="md:col-span-2"><?php $manualField('norm_other_potential_text', 'ÍNDICE O ÁREA DE OCUPACIÓN EN TEXTO', 'Si el cuadro habla de área de ocupación, plataforma, índice de ocupación o condiciones especiales, regístralo aquí.', 4); ?></div>
                </div>
                <p class="mt-3 rounded-xl bg-amber-50 p-3 text-xs font-semibold leading-5 text-amber-900">Usa esta sección solo para completar o corregir datos urbanos puntuales. La lectura completa de MIDAS se procesa desde el numeral 2; la conversión cuantitativa queda para el módulo 8.</p>
            </details>
        </div>
    </section>
