    <?php
    $midasFallbackOpen = isset($urbanError) && str_contains((string) $urbanError, 'MIDAS');
    $midasReferenceDigits = preg_replace('/\D+/', '', $value('cadastral_reference_short') ?: $value('cadastral_reference')) ?? '';
    ?>
    <section id="midas" x-show="tab === 'midas'" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        <input type="hidden" name="midas_query_option" value="<?= e($value('midas_query_option') ?: 'Uso del suelo') ?>">
        <input type="hidden" name="midas_activity" value="<?= e($value('midas_activity')) ?>">
        <div class="flex flex-wrap items-start justify-between gap-5">
            <div>
                <p class="eyebrow">Soporte MIDAS</p>
                <h2 class="mt-2 text-2xl font-semibold">Referencia y evidencia de consulta</h2>
                <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">Esta sección no define el uso ni el potencial del inmueble. Solo deja trazabilidad de que se consultó MIDAS o de que la fuente no respondió. El análisis valuatorio se desarrolla en 5.2 y 5.3.</p>
                <p class="mt-2 text-xs font-semibold text-teal-800" data-autosave-status>Autoguardado activo</p>
            </div>
            <span class="rounded-full bg-slate-100 px-3 py-1 text-sm font-semibold text-slate-700">Soporte, no decisión</span>
        </div>
        <?php if ($midasFallbackOpen): ?>
            <div class="mt-5 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm leading-6 text-amber-950">
                <p class="font-semibold">MIDAS bloqueó la lectura automática desde Hostinger.</p>
                <p class="mt-1">Usa el respaldo manual: abre MIDAS, busca la referencia <strong><?= e($midasReferenceDigits) ?></strong> y pega abajo la lectura o deja constancia de que no respondió.</p>
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
            <label class="label">Referencia larga si MIDAS la devuelve
                <input class="input" type="text" name="cadastral_reference_long"
                    value="<?= e($value('cadastral_reference_long')) ?>" inputmode="numeric" maxlength="120"
                    placeholder="Referencia predial nacional">
                <span class="mt-1 block text-xs font-medium text-slate-500">Campo de respaldo cuando MIDAS informa referencia nacional.</span>
            </label>
            <div class="md:col-span-3 flex flex-wrap gap-2 rounded-xl border border-blue-100 bg-blue-50 p-4">
                <a class="btn-primary" href="https://midas.cartagena.gov.co/#/home" target="_blank" rel="noopener">Abrir MIDAS</a>
                <button class="btn-secondary" type="button" onclick="navigator.clipboard?.writeText('<?= e($midasReferenceDigits) ?>')">Copiar referencia</button>
                <button class="btn-secondary" type="submit" formaction="<?= e(url('avaluos/' . $record['id'] . '/normatividad-urbana/midas/consultar')) ?>">Intentar lectura automática</button>
            </div>
            <label class="label">Estado de la fuente MIDAS
                <select class="input" name="source_status">
                    <?php foreach ($sourceOptions as $key => $label): ?><option value="<?= e($key) ?>" <?= e($selected('source_status', (string) $key)) ?>><?= e($label) ?></option><?php endforeach; ?>
                </select>
            </label>
            <?php $input('midas_consulted_on', 'Fecha de consulta MIDAS', '', 'date'); ?>
            <?php $input('midas_support_reference', 'Soporte o evidencia', 'Ej. captura, PDF, capa, radicado o nota interna'); ?>
            <label class="md:col-span-3 inline-flex items-center gap-2 rounded-xl bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">
                <input type="checkbox" name="midas_consulted" value="1" <?= e($checked('midas_consulted')) ?>> MIDAS fue consultado o revisado manualmente
            </label>
            <div class="md:col-span-3"><?php $textarea('midas_usage_result', 'Lectura o resultado de MIDAS para dejar trazabilidad', 'Pega o resume lo observado: uso, área, zona, tratamiento, restricciones, o deja constancia de No disponible.', 4); ?></div>
            <?php if ($value('midas_predio_raw') !== '' || $value('midas_usage_raw') !== ''): ?>
                <div class="md:col-span-3 rounded-xl border border-emerald-100 bg-emerald-50 p-4 text-sm text-emerald-900">
                    Lectura guardada: <?= $value('midas_predio_raw') !== '' ? 'predio MIDAS para numeral 3' : '' ?><?= $value('midas_predio_raw') !== '' && $value('midas_usage_raw') !== '' ? ' y ' : '' ?><?= $value('midas_usage_raw') !== '' ? 'reglamentación Uso Suelo para numeral 5' : '' ?>.
                </div>
            <?php endif; ?>
            <details id="midas-paste" class="md:col-span-3 rounded-xl border border-slate-200 bg-slate-50 p-4" <?= $midasFallbackOpen ? 'open' : '' ?>>
                <summary class="cursor-pointer text-sm font-semibold text-slate-900">Pegar lectura completa de MIDAS si se necesita procesar</summary>
                <label class="label mt-4">Texto copiado de Predios o Uso Suelo en MIDAS
                    <textarea class="input min-h-40" name="midas_pasted_text" rows="7" maxlength="70000"
                        placeholder="Pega aquí el bloque completo que entrega MIDAS, incluyendo Predios, Uso Suelo o reglamentación."></textarea>
                    <span class="mt-1 block text-xs font-medium text-slate-500">Este bloque es solo respaldo. Si el texto contiene datos reconocibles, puede llenar campos del numeral 3 o dejar evidencia en el numeral 5.</span>
                </label>
                <button class="btn-secondary mt-4" type="submit" formaction="<?= e(url('avaluos/' . $record['id'] . '/normatividad-urbana/midas/procesar')) ?>">
                    Procesar lectura MIDAS pegada
                </button>
            </details>
        </div>
    </section>
