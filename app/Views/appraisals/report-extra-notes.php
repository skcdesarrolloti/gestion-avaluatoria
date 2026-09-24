<?php
$reportNoteRows = is_array($reportNotes ?? null) ? $reportNotes : [];
$reportNoteChapter = (string) ($reportNoteChapter ?? '1');
$reportNoteSections = is_array($reportNoteSections ?? null) ? $reportNoteSections : [];
$reportNoteReturn = (string) ($reportNoteReturn ?? ('avaluos/' . $record['id']));
$nextIndex = count($reportNoteRows);
?>
<section class="mt-8 rounded-2xl border border-indigo-100 bg-indigo-50 p-6 shadow-sm sm:p-8"
    x-data="{customCode: '', customLabel: '', chapter: '<?= e($reportNoteChapter) ?>',
        customReady() { return this.customCode.trim().startsWith(this.chapter + '.') && this.customLabel.trim().length > 0 },
        customText() { return this.customCode.trim() + ' · ' + this.customLabel.trim() }}">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="eyebrow">Ampliaciones del entregable</p>
            <h2 class="mt-2 text-2xl font-semibold text-indigo-950">Agregar información sin romper la numeración</h2>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-indigo-900">
                Usa estos campos cuando el caso necesite una aclaración adicional. Selecciona el numeral,
                escribe el texto y deja la fuente o soporte. El entregable lo insertará debajo del numeral elegido.
            </p>
        </div>
        <span class="rounded-full bg-white px-3 py-1 text-sm font-semibold text-indigo-800">Capítulo <?= e($reportNoteChapter) ?></span>
    </div>
    <?php if ($msg = \App\Core\Session::pullFlash('report_note_message')): ?>
        <p class="mt-4 rounded-xl bg-emerald-100 p-3 text-sm font-semibold text-emerald-800"><?= e($msg) ?></p>
    <?php endif; ?>
    <?php if ($err = \App\Core\Session::pullFlash('report_note_error')): ?>
        <p class="mt-4 rounded-xl bg-red-100 p-3 text-sm font-semibold text-red-800"><?= e($err) ?></p>
    <?php endif; ?>
    <form class="mt-5 grid gap-4" method="post" action="<?= e(url('avaluos/' . $record['id'] . '/notas-entregable')) ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="chapter_code" value="<?= e($reportNoteChapter) ?>">
        <input type="hidden" name="return_to" value="<?= e($reportNoteReturn) ?>">
        <article class="rounded-xl border border-indigo-100 bg-white p-4">
            <p class="text-sm font-semibold text-indigo-900">Crear numeral para este capítulo</p>
            <p class="mt-1 text-xs leading-5 text-slate-600">
                Si necesitas insertar un punto que no existe, escribe el número y su descripción. Al guardar,
                quedará disponible en el desplegable de este avalúo.
            </p>
            <div class="mt-3 grid gap-4 md:grid-cols-3">
                <label class="label">Nuevo numeral
                    <input class="input" name="report_note_sections[0][section_code]" maxlength="20"
                        x-model="customCode"
                        placeholder="Ej. <?= e($reportNoteChapter) ?>.14 o <?= e($reportNoteChapter) ?>.3.5">
                    <span class="help">Debe iniciar por el capítulo <?= e($reportNoteChapter) ?> y seguir la numeración del informe.</span>
                </label>
                <label class="label md:col-span-2">Descripción del numeral
                    <input class="input" name="report_note_sections[0][label]" maxlength="180"
                        x-model="customLabel"
                        placeholder="Ej. Información complementaria de mercado">
                    <span class="help">Este texto aparecerá junto al número en la lista desplegable y en el entregable.</span>
                </label>
            </div>
        </article>
        <?php foreach ($reportNoteRows as $i => $note): ?>
            <article class="rounded-xl border border-indigo-100 bg-white p-4">
                <input type="hidden" name="report_notes[<?= e((string) $i) ?>][id]" value="<?= e((string) ($note['id'] ?? '')) ?>">
                <div class="grid gap-4 md:grid-cols-3">
                    <label class="label">Numeral
                        <select class="input" name="report_notes[<?= e((string) $i) ?>][section_code]">
                            <?php foreach ($reportNoteSections as $code => $label): ?>
                                <option value="<?= e((string) $code) ?>" <?= (string) ($note['section_code'] ?? '') === (string) $code ? 'selected' : '' ?>><?= e($code . ' · ' . $label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <label class="label md:col-span-2">Título corto
                        <input class="input" name="report_notes[<?= e((string) $i) ?>][title]" value="<?= e((string) ($note['title'] ?? '')) ?>" maxlength="180" placeholder="Ej. Aclaración sobre uso, fuente o soporte">
                    </label>
                </div>
                <label class="label mt-4">Texto que debe pasar al entregable
                    <textarea class="input min-h-36" name="report_notes[<?= e((string) $i) ?>][body]" rows="5" maxlength="4000" placeholder="Redacta aquí la ampliación técnica del numeral seleccionado."><?= e((string) ($note['body'] ?? '')) ?></textarea>
                </label>
                <label class="label mt-4">Fuente o soporte
                    <input class="input" name="report_notes[<?= e((string) $i) ?>][source_note]" value="<?= e((string) ($note['source_note'] ?? '')) ?>" maxlength="600" placeholder="Ej. Escritura pública No. __ de fecha __, visita, certificado, MIDAS, fotografía, soporte del expediente">
                </label>
                <div class="mt-3 flex flex-wrap items-center justify-between gap-3">
                    <label class="inline-flex items-center gap-2 text-sm font-semibold text-red-700">
                        <input type="checkbox" name="report_notes[<?= e((string) $i) ?>][delete]" value="1"> Retirar esta ampliación
                    </label>
                    <input class="input max-w-24" type="number" name="report_notes[<?= e((string) $i) ?>][sort_order]" value="<?= e((string) ($note['sort_order'] ?? $i)) ?>" min="0" max="999" aria-label="Orden">
                </div>
            </article>
        <?php endforeach; ?>
        <article class="rounded-xl border border-dashed border-indigo-200 bg-white p-4">
            <p class="text-sm font-semibold text-indigo-900">Nueva ampliación</p>
            <div class="mt-3 grid gap-4 md:grid-cols-3">
                <label class="label">Numeral
                    <select class="input" name="report_notes[<?= e((string) $nextIndex) ?>][section_code]"
                        x-ref="newReportNoteSection" x-effect="customReady() && ($refs.newReportNoteSection.value = customCode.trim())">
                        <option value="" :value="customReady() ? customCode.trim() : ''"
                            :disabled="!customReady()" x-text="customReady() ? customText() : 'Selecciona un numeral o crea uno arriba'"></option>
                        <?php foreach ($reportNoteSections as $code => $label): ?>
                            <option value="<?= e((string) $code) ?>"><?= e($code . ' · ' . $label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label class="label md:col-span-2">Título corto
                    <input class="input" name="report_notes[<?= e((string) $nextIndex) ?>][title]" maxlength="180" placeholder="Ej. Aclaración adicional del caso">
                </label>
            </div>
            <label class="label mt-4">Texto que debe pasar al entregable
                <textarea class="input min-h-36" name="report_notes[<?= e((string) $nextIndex) ?>][body]" rows="5" maxlength="4000" placeholder="Agrega la información que faltaba en este capítulo."></textarea>
            </label>
            <label class="label mt-4">Fuente o soporte
                <input class="input" name="report_notes[<?= e((string) $nextIndex) ?>][source_note]" maxlength="600" placeholder="Fuente exacta o soporte revisado">
            </label>
            <input type="hidden" name="report_notes[<?= e((string) $nextIndex) ?>][sort_order]" value="<?= e((string) $nextIndex) ?>">
        </article>
        <div class="flex justify-end">
            <button class="btn-primary min-h-11" type="submit">Guardar ampliaciones</button>
        </div>
    </form>
</section>
