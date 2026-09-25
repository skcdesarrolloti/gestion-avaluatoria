    <section id="midas" x-show="tab === 'midas'" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        <div class="flex flex-wrap items-start justify-between gap-5">
            <div>
                <p class="eyebrow">Consulta MIDAS</p>
                <h2 class="mt-2 text-2xl font-semibold">Número predial y opción Uso del suelo</h2>
                <p class="mt-2 text-sm leading-6 text-slate-600">El botón consulta MIDAS con la referencia corta o larga y actualiza el Uso del suelo en este numeral.</p>
                <p class="mt-2 text-xs font-semibold text-teal-800" data-autosave-status>Autoguardado activo</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <button class="btn-primary" type="submit" formaction="<?= e(url('avaluos/' . $record['id'] . '/normatividad-urbana/midas/consultar')) ?>">
                    Actualizar MIDAS
                </button>
                <label class="inline-flex items-center gap-2 rounded-xl bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">
                    <input type="checkbox" name="midas_consulted" value="1" <?= e($checked('midas_consulted')) ?>> MIDAS consultado
                </label>
                <a class="btn-secondary" href="<?= e(url('avaluos/' . $record['id'] . '/bien-sujeto#registro')) ?>">Corregir referencia en módulo 3</a>
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
