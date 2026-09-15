<?php
use App\Support\AppraisalCatalog;
$fields = array_intersect_key($record, array_flip(AppraisalCatalog::fieldKeys()));
$initial = ['fields' => $fields, 'version' => $record['version'], 'endpoint' => url('avaluos/' . $record['id'] . '/borrador')];
$notes = $catalog['notes'] ?? [];
?>
<a href="<?= e(url()) ?>" class="inline-flex min-h-11 items-center text-sm font-medium text-teal-800">← Mis avalúos</a>
<div class="mt-3"><p class="eyebrow">Ficha inicial · Borrador</p><h1 class="mt-2 text-3xl font-semibold tracking-tight">Información del avalúo</h1>
    <p class="mt-3 text-slate-600">Puedes dejar campos pendientes. Tus cambios se guardan mientras trabajas.</p></div>
<noscript><p role="alert" class="mt-6 text-red-800">Activa JavaScript para editar y guardar esta ficha.</p></noscript>
<form x-cloak x-data="appraisalForm" data-initial="<?= e(json_encode($initial, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>"
    data-notes="<?= e(json_encode($notes, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>"
    @submit.prevent="save()" @input="changed()" @change="changed()" class="mt-8 grid gap-6 lg:grid-cols-[1fr_280px]">
    <section class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8" aria-labelledby="datos-heading">
        <h2 id="datos-heading" class="text-lg font-semibold">Datos generales</h2>
        <div class="mt-6 grid gap-5 sm:grid-cols-2">
            <?php require BASE_PATH . '/app/Views/appraisals/fields.php'; ?>
        </div>
        <section class="mt-6 rounded-xl border border-sky-200 bg-sky-50 p-4">
            <h3 class="text-sm font-semibold text-sky-950">Guía contextual del encargo</h3>
            <p class="mt-2 text-sm leading-6 text-sky-900">Estos campos se leen en conjunto. Cambia una opción y verás su alcance para orientar el expediente sin salir de la pantalla.</p>
            <div class="mt-4 grid gap-3 md:grid-cols-2" aria-live="polite">
                <template x-for="item in selectedNotes()" :key="item.key">
                    <article class="rounded-lg border border-sky-200 bg-white p-4">
                        <h4 class="text-sm font-semibold text-slate-950" x-text="item.label"></h4>
                        <p class="mt-2 text-xs font-semibold uppercase text-slate-500">Qué es</p>
                        <p class="mt-1 text-sm leading-6 text-slate-700" x-text="item.what"></p>
                        <p class="mt-2 text-xs font-semibold uppercase text-slate-500">Cuándo aplica</p>
                        <p class="mt-1 text-sm leading-6 text-slate-700" x-text="item.when"></p>
                        <p class="mt-2 text-xs font-semibold uppercase text-slate-500">Soporte</p>
                        <p class="mt-1 text-sm leading-6 text-teal-800" x-text="item.basis"></p>
                    </article>
                </template>
                <p class="rounded-lg border border-dashed border-sky-200 bg-white p-4 text-sm text-slate-600"
                    x-show="selectedNotes().length === 0">Selecciona tipo de avalúo, derecho, finalidad o base de valor para ver la guía.</p>
            </div>
        </section>
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
