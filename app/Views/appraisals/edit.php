<?php
$fields = array_intersect_key($record, array_flip(['titulo', 'tipo', 'direccion', 'municipio', 'observaciones']));
$initial = ['fields' => $fields, 'version' => $record['version'], 'endpoint' => url('avaluos/' . $record['id'] . '/borrador')];
?>
<a href="<?= e(url()) ?>" class="inline-flex min-h-11 items-center text-sm font-medium text-teal-800">← Mis avalúos</a>
<div class="mt-3"><p class="eyebrow">Ficha inicial · Borrador</p><h1 class="mt-2 text-3xl font-semibold tracking-tight">Información del avalúo</h1>
    <p class="mt-3 text-slate-600">Puedes dejar campos pendientes. Tus cambios se guardan mientras trabajas.</p></div>
<noscript><p role="alert" class="mt-6 text-red-800">Activa JavaScript para editar y guardar esta ficha.</p></noscript>
<form x-cloak x-data="appraisalForm" data-initial="<?= e(json_encode($initial, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>"
    @submit.prevent="save()" @input="changed()" @change="changed()" class="mt-8 grid gap-6 lg:grid-cols-[1fr_280px]">
    <section class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8" aria-labelledby="datos-heading">
        <h2 id="datos-heading" class="text-lg font-semibold">Datos generales</h2>
        <div class="mt-6 grid gap-5 sm:grid-cols-2">
            <?php require BASE_PATH . '/app/Views/appraisals/fields.php'; ?>
        </div>
    </section>
    <aside class="space-y-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-6">
            <h2 class="font-semibold">Guardado de la ficha</h2>
            <p class="mt-3 text-sm leading-6" role="status" aria-live="polite" x-text="message">Lista para editar</p>
            <p class="mt-2 text-xs text-slate-500" x-show="savedAt" x-text="savedAt"></p>
            <button class="btn-primary mt-5 w-full" type="submit" :disabled="saving || blocked || !dirty"
                x-text="saving ? 'Guardando…' : 'Guardar ahora'">Guardar ahora</button>
            <p class="mt-4 text-xs leading-5 text-slate-500">Al salir con cambios pendientes, recibirás un aviso. Espera la confirmación de guardado.</p>
        </div>
        <p class="px-2 text-xs leading-5 text-slate-500">Esta ficha es un borrador. No constituye un informe técnico ni un avalúo aprobado.</p>
    </aside>
</form>
