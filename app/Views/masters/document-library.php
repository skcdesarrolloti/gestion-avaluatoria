<section id="biblioteca-documental" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
    <div class="grid gap-5 lg:grid-cols-[1fr_22rem]">
        <div>
            <p class="eyebrow">Biblioteca documental maestra</p>
            <h2 class="mt-2 text-2xl font-semibold">Crear ficha y subir soporte normativo</h2>
            <p class="mt-3 text-sm leading-6 text-slate-600">
                Usa esta puerta única para cargar documentos que todavía no estén en las bibliotecas superiores. La ficha conserva un solo PDF y registra dónde debe consultarse o citarse.
            </p>
        </div>
        <div class="rounded-xl border border-blue-100 bg-blue-50 p-4 text-xs leading-5 text-blue-950">
            <strong>Almacenamiento:</strong> <?= e((string) ($documentStorage['present'] ?? 0)) ?> de <?= e((string) ($documentStorage['total'] ?? 0)) ?> documento(s) con respaldo.
            <span class="mt-1 block">Destino privado: <?= e((string) ($documentStorage['dir'] ?? '')) ?></span>
        </div>
    </div>
    <form class="mt-6 grid gap-5 md:grid-cols-2" method="post" enctype="multipart/form-data"
        action="<?= e(url('maestros/documentos')) ?>" x-data="{ busy: false }" @submit="busy = true">
        <?= csrf_field() ?>
        <label class="label">Dónde alojarlo
            <select class="input" name="destination" required>
                <option value="">Selecciona biblioteca o módulo</option>
                <?php foreach ($documentDestinations as $code => $label): ?>
                    <option value="<?= e($code) ?>"><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label class="label">Estado
            <select class="input" name="status">
                <option value="vigente">Vigente</option>
                <option value="historico">Histórico</option>
                <option value="derogado">Derogado</option>
            </select>
        </label>
        <label class="label">Código o referencia
            <input class="input" name="document_code" maxlength="120" placeholder="Ej. IVS 400, Decreto 556 de 2014, Resolución 620 de 2008">
        </label>
        <label class="label">Tipo de documento
            <input class="input" name="document_type" maxlength="40" placeholder="Resolución, decreto, norma, anexo, guía">
        </label>
        <label class="label md:col-span-2">Nombre del documento
            <input class="input" name="title" maxlength="240" required placeholder="Nombre completo con el que se consultará en la biblioteca">
        </label>
        <label class="label">Versión o vigencia textual
            <input class="input" name="version" maxlength="80" placeholder="Ej. 2026, V1, efectivo 31/01/2025">
        </label>
        <label class="label">Fecha de vigencia o emisión
            <input class="input" type="date" name="effective_at">
        </label>
        <label class="label md:col-span-2">Fuente oficial o URL de consulta
            <input class="input" type="url" name="source_url" maxlength="500" placeholder="https://...">
        </label>
        <label class="label md:col-span-2">Para qué es útil
            <textarea class="input min-h-11" name="summary" rows="3" maxlength="600" placeholder="Explica en lenguaje del analista qué sustenta y cuándo se cita."></textarea>
        </label>
        <label class="label md:col-span-2">Temas relacionados
            <input class="input" name="topics" maxlength="500" placeholder="obsolescencia, costo, depreciación, reporte, evidencia">
            <span class="mt-1 block text-xs leading-5 text-slate-500">Separa temas con coma o punto y coma.</span>
        </label>
        <fieldset class="md:col-span-2 rounded-xl border border-slate-200 bg-slate-50 p-4">
            <legend class="label">Módulos que lo podrán citar</legend>
            <div class="mt-3 grid gap-3 md:grid-cols-3">
                <?php foreach ($documentModules as $code => $label): ?>
                    <label class="flex items-start gap-3 rounded-lg border border-slate-200 bg-white p-3 text-sm text-slate-700">
                        <input class="mt-1 size-4 shrink-0" type="checkbox" name="modules[]" value="<?= e($code) ?>">
                        <span><?= e($label) ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
        </fieldset>
        <label class="label md:col-span-2">PDF del documento
            <input class="input" type="file" name="document_file" accept="application/pdf,.pdf" required>
            <span class="mt-1 block text-xs leading-5 text-slate-500">Se guarda una sola copia física y un respaldo interno para evitar pérdidas.</span>
        </label>
        <div class="md:col-span-2 flex flex-wrap items-center gap-3 rounded-xl border border-teal-100 bg-teal-50 p-4">
            <button class="btn-primary" type="submit" :disabled="busy" x-text="busy ? 'Cargando documento...' : 'Crear ficha documental'">Crear ficha documental</button>
            <span class="text-sm leading-6 text-teal-900">Luego el módulo podrá citar esta ficha sin duplicar el PDF.</span>
        </div>
    </form>
    <?php require BASE_PATH . '/app/Views/masters/document-list.php'; ?>
</section>
