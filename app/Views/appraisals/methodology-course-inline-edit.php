<details x-data="{draft:null}" @toggle.stop="if($el.open)draft=courseInlineEdit(r.id)">
    <summary class="min-h-11 cursor-pointer font-semibold">Editar datos aquí</summary>
    <template x-if="draft"><div class="min-w-64 space-y-2">
        <label class="label">Oferta · COP<input class="input" type="number" min="0.0001" step="any" placeholder="Precio total verificado" x-model="draft.offer"></label>
        <label class="label">Área publicada · m²<input class="input" type="number" min="0.0001" step="any" placeholder="Área verificada" x-model="draft.area"></label>
        <label class="label">Descuento · %<input class="input" type="number" min="0" max="100" step="any" placeholder="Vacío si está pendiente" x-model="draft.percent"></label>
        <template x-for="f in courseInlineFields(draft)" :key="f.key"><label class="label"><span x-text="f.label"></span><input class="input" maxlength="500" placeholder="Dato verificado" x-model="f.value"></label></template>
        <label class="label">Fuente, fecha y motivo<textarea class="input" rows="2" maxlength="1600" placeholder="Indica evidencia y motivo de la corrección" x-model="draft.source"></textarea></label>
        <p role="alert" class="text-rose-800" x-show="draft.error" x-text="draft.error"></p>
        <button type="button" class="btn-secondary" @click="if(await courseInlineApply(r.id,draft))$el.closest('details').open=false">Aplicar corrección</button>
        <p class="text-xs">Conserva el dato anterior y su soporte. Después pulsa Actualizar análisis estadístico.</p>
    </div></template>
</details>
