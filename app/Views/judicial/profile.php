<?php $endpoint = url('maestros/peritos/' . $expert['id'] . '/judicial'); ?>
<section class="mx-auto max-w-5xl space-y-6">
    <a class="btn-secondary" href="<?= e(url('maestros')) ?>">Volver a Maestros</a>
    <header><p class="eyebrow">Maestros · Perito responsable</p><h1 class="text-2xl font-semibold">Antecedentes y requisitos judiciales</h1><p class="mt-2"><?= e($expert['full_name']) ?> · identificación <?= e($expert['identification_number']) ?></p></header>
    <p class="rounded-xl bg-blue-50 p-4">Conserva aquí publicaciones y designaciones anteriores. Las presentaciones registradas desde tus expedientes se añaden al historial automáticamente. Estos antecedentes se conservan en tu cuenta; no se comparten con otros usuarios.</p>
    <?php require __DIR__ . '/academy.php'; ?>
    <form id="judicial-profile" method="post" action="<?= e($endpoint) ?>" data-module-autosave data-save-in-place data-autosave-endpoint="<?= e($endpoint) ?>"
        class="space-y-5 rounded-xl border bg-white p-5" x-data="{ rows: <?= e(json_encode($formData['history'] ?? [], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT)) ?> }">
        <?= csrf_field() ?><input type="hidden" name="version" value="<?= e($stored['version']) ?>">
        <?php foreach (['contact' => 'Dirección, ciudad, teléfono, identificación y datos de localización (226.2)', 'profession' => 'Profesión, oficio o actividad especial (226.3)', 'experience' => 'Experiencia profesional pertinente (226.3)', 'credentials' => 'Relación de títulos y documentos de idoneidad que acompañarán el dictamen (226.3)'] as $key => $label): ?>
            <label class="label"><?= e($label) ?><textarea class="input" name="<?= e($key) ?>" rows="3" maxlength="12000" placeholder="Registra información verificable y la referencia de sus soportes."><?= e($formData[$key] ?? '') ?></textarea></label>
        <?php endforeach; ?>
        <p class="text-sm">Los datos RAA se conservan en el maestro. Esta ficha complementa su información; relacionar un soporte no adjunta su archivo al dictamen.</p>
        <h2 class="text-xl font-semibold">Publicaciones y casos anteriores</h2>
        <p class="text-sm">Conserva el historial completo. Cada dictamen usa su fecha para consultar publicaciones de 10 años y casos de 4 años. El numeral 6 exige revisar también procesos anteriores y en curso sin ese límite temporal.</p>
        <input type="hidden" name="history_json" value="<?= e(json_encode($formData['history'] ?? [])) ?>" :value="JSON.stringify(rows)">
        <template x-for="(row, index) in rows" :key="index">
            <fieldset class="space-y-3 rounded-xl border p-4">
                <legend class="font-semibold" x-text="'Antecedente ' + (index + 1)"></legend>
                <label class="label">Tipo<select class="input" x-model="row.kind"><option value="">Selecciona tipo</option><option value="publication">Publicación</option><option value="case">Designación o participación en un dictamen</option></select></label>
                <label class="label">Fecha de publicación, designación o participación<input class="input" type="date" x-model="row.date"><span class="text-xs">Usa la fecha documentada del antecedente.</span></label>
                <?php foreach (['title' => 'Título de publicación o identificación del caso', 'court' => 'Juzgado o despacho', 'parties' => 'Nombre de las partes', 'lawyers' => 'Nombre de los apoderados de las partes', 'matter' => 'Materia y objeto del dictamen o publicación', 'reference' => 'Referencia o ubicación del soporte'] as $key => $label): ?>
                    <label class="label" <?= in_array($key, ['court', 'parties', 'lawyers']) ? 'x-show="row.kind === \'case\'"' : '' ?>><?= e($label) ?><textarea class="input" x-model="row.<?= e($key) ?>" rows="2" maxlength="4000" placeholder="<?= e($label) ?>; aclara si no aplica."></textarea></label>
                <?php endforeach; ?>
                <button class="btn-secondary" type="button" @click="rows.splice(index, 1); $nextTick(() => $dispatch('input'))">Retirar este antecedente manual</button>
            </fieldset>
        </template>
        <button class="btn-secondary" type="button" @click="rows.push({kind: 'publication', date: '', title: '', court: '', parties: '', lawyers: '', matter: '', reference: ''}); $nextTick(() => $dispatch('input'))">Agregar antecedente</button>
        <div class="flex flex-wrap items-center gap-3"><button class="btn-primary" type="submit">Guardar ahora</button><span role="status" data-autosave-status>Autoguardado activo</span></div>
    </form>
    <section class="space-y-3 rounded-xl border bg-white p-5"><h2 class="text-xl font-semibold">Presentaciones registradas desde tus avalúos</h2>
        <?php if (!$history): ?><p>No hay presentaciones registradas. Un borrador no se cuenta como presentado.</p><?php endif; ?>
        <?php foreach ($history as $item): ?><article class="rounded-lg border p-3"><p><?= e($item['date']) ?> · <?= e($item['court']) ?> · <?= e($item['docket']) ?></p><p><?= e($item['matter']) ?></p><a class="btn-secondary mt-2" href="<?= e(url('avaluos/' . $item['appraisal_id'] . '/judicial')) ?>">Consultar registro y anexo</a></article><?php endforeach; ?>
    </section>
</section>
