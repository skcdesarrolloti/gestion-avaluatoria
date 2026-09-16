    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="eyebrow">Antes de las fotos</p>
                <h2 class="mt-2 text-2xl font-semibold">Lectura inicial del predio</h2>
                <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                    Define qué familias IGAC vas a revisar y cuántas unidades o anexos existen.
                    Cada unidad podrá tener su propia tipología constructiva.
                </p>
            </div>
            <a class="btn-secondary" href="<?= e(url('tipologias-constructivas-igac')) ?>">Ver tipologías IGAC</a>
        </div>
        <?php if ($preclassMessage): ?>
            <p class="mt-5 rounded-xl bg-emerald-50 p-4 text-sm font-semibold text-emerald-800"><?= e($preclassMessage) ?></p>
        <?php endif; ?>
        <?php if ($preclassError): ?>
            <p class="mt-5 rounded-xl bg-red-50 p-4 text-sm font-semibold text-red-800"><?= e($preclassError) ?></p>
        <?php endif; ?>
        <form class="mt-6 grid gap-5 md:grid-cols-2 xl:grid-cols-4" method="post"
            action="<?= e(url('avaluos/' . $record['id'] . '/capitulo-0/preclasificacion')) ?>"
            x-data="{ busy: false }" @submit="busy = true">
            <?= csrf_field() ?>
            <input type="hidden" name="version" value="<?= e($record['version']) ?>">
            <label class="label">Categoría IGAC probable
                <select class="input" name="igac_category" x-model="igacCategory">
                    <option value="">Por definir</option>
                    <?php foreach ($igacCategories as $category): ?>
                        <option value="<?= e($category['code']) ?>" <?= $selected('igac_category', $category['code']) ?>>
                            <?= e($category['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <span class="mt-1 block text-xs leading-5 text-slate-500">
                    Filtra las sugerencias; no reemplaza la tipología final por unidad.
                </span>
            </label>
            <label class="label">Inmuebles o construcciones
                <input class="input" type="number" name="igac_property_units_count" min="0" max="50"
                    x-model.number="propertyUnits" placeholder="Ej. 1">
                <span class="mt-1 block text-xs leading-5 text-slate-500">Casas, edificios, bodegas u otras unidades principales.</span>
            </label>
            <label class="label">Anexos existentes
                <input class="input" type="number" name="igac_annex_units_count" min="0" max="50"
                    x-model.number="annexUnits" placeholder="Ej. 2">
                <span class="mt-1 block text-xs leading-5 text-slate-500">Kioscos, cerramientos, ramadas, piscinas, depósitos u otros anexos.</span>
            </label>
            <label class="label">Tipología preliminar
                <input class="input" name="igac_typology_hint" maxlength="190" x-model="typologyHint"
                    placeholder="Opcional, se puede ajustar con el comparativo">
            </label>
            <div class="rounded-xl bg-slate-50 p-4 text-sm leading-6 text-slate-600 md:col-span-2 xl:col-span-3">
                Se prepararán <strong x-text="propertyUnits || 0"></strong> unidad(es) principal(es)
                y <strong x-text="annexUnits || 0"></strong> anexo(s) para tipología individual.
            </div>
            <div class="flex items-end">
                <button class="btn-primary min-h-11 w-full" type="submit" :disabled="busy"
                    x-text="busy ? 'Guardando...' : 'Guardar lectura inicial'">Guardar lectura inicial</button>
            </div>
        </form>
    </section>

