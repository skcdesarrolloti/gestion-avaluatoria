<div class="md:col-span-2 space-y-4">
    <p class="text-sm">Consulta las familias relacionadas con el tipo de inmueble. Puedes ampliar al catálogo completo. La referencia orienta la descripción; contrástala con la visita.</p>
    <input type="hidden" name="units[<?= e($unit['id']) ?>][igac_category]" value="<?= e($unit['igac_category']) ?>" :value="igacCategory">
    <label class="label">Buscar tipología IGAC
        <input class="input" type="search" x-model="igacQuery" @input.stop @change.stop placeholder="Ej. oficina, servicios, multifamiliar, bodega, garaje">
    </label>
    <label class="flex min-h-11 items-center gap-2 text-sm"><input type="checkbox" x-model="igacShowAll" @change.stop> Consultar todo el catálogo IGAC, sin filtro por tipo</label>
    <label class="label">Tipología preliminar de esta unidad
        <select class="input" name="units[<?= e($unit['id']) ?>][igac_typology_hint]" x-model="igacHint" @change="selectIgacCategory()"
            x-init="$el.querySelectorAll('[data-fallback-option]').forEach(option=>option.remove())">
            <option value="">Selecciona una referencia para contrastar</option>
            <?php foreach (\App\Support\IgacTypologySelection::options('', '', (string)$unit['igac_typology_hint'], $typologyOptions, true) as $option): ?>
            <option data-fallback-option value="<?= e($option['value']) ?>" <?= $unit['igac_typology_hint']===$option['value']?'selected':'' ?>><?= e($option['label']) ?></option>
            <?php endforeach; ?>
            <template x-for="item in igacOptions" :key="item.value"><option :value="item.value" x-text="item.label"></option></template>
        </select>
        <span class="text-xs font-normal"><span x-text="igacOptions.length"></span> referencias. La selección guardada se conserva aunque no coincida con la búsqueda.</span>
    </label>
    <p class="text-sm text-amber-900" x-show="!igacOptions.length" role="status">Sin coincidencias. Amplía al catálogo completo o cambia el término de búsqueda.</p>
    <article class="grid gap-4 rounded-xl border bg-slate-50 p-4 sm:grid-cols-[9rem_1fr]" x-show="selectedItem(igacCategory, igacHint)">
        <a class="block" :href="imageUrl(selectedItem(igacCategory, igacHint))" target="_blank" rel="noopener" data-no-fetch>
            <img class="w-full object-contain" :src="imageUrl(selectedItem(igacCategory, igacHint))" alt="Referencia IGAC seleccionada" loading="lazy">
        </a>
        <div class="min-w-0 space-y-3">
            <p class="text-anywhere font-semibold" x-text="selectedItem(igacCategory, igacHint)?.label"></p>
            <p class="text-sm" x-text="selectedItem(igacCategory, igacHint)?.description"></p>
            <details><summary class="min-h-11 cursor-pointer py-3 text-sm font-semibold">Especificaciones de la referencia</summary><p class="text-sm" x-text="selectedItem(igacCategory, igacHint)?.specifications"></p></details>
            <button class="btn-secondary" type="button" @click="notes=descriptionDraft(selectedItem(igacCategory, igacHint)); $dispatch('input')">Usar como descripción base</button>
        </div>
    </article>
    <button class="btn-secondary" type="button" @click="browserOpen=!browserOpen" x-text="browserOpen?'Cerrar opciones con foto':'Ver opciones con foto'">Ver opciones con foto</button>
    <div class="max-h-[28rem] overflow-y-auto rounded-xl border p-3" x-show="browserOpen">
        <template x-if="browserOpen"><div class="grid gap-3 md:grid-cols-2 xl:grid-cols-3">
            <template x-for="item in igacOptions" :key="item.value">
                <button class="rounded-xl border p-3 text-left" type="button" @click="igacHint=item.value; selectIgacCategory(); browserOpen=false; $dispatch('change')">
                    <img class="h-32 w-full object-contain" :src="imageUrl(item)" alt="Referencia IGAC" loading="lazy">
                    <span class="text-anywhere block text-sm font-semibold" x-text="item.label"></span>
                    <span class="mt-2 block text-xs" x-text="item.description"></span>
                </button>
            </template>
        </div></template>
    </div>
</div>
