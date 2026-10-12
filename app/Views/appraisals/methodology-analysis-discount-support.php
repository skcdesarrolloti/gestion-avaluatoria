<details><summary class="min-h-11 cursor-pointer text-xs">Soporte del descuento</summary>
    <label class="block text-xs" :for="'discount-kind-'+property.key">Tipo de descuento</label>
    <select class="input w-52" :id="'discount-kind-'+property.key" x-model="analysisRows[property.analysisIndex].negotiation_kind"><?php foreach (\App\Services\ComparableNegotiation::options() as $value=>$label): ?><option value="<?= e($value) ?>"><?= e($label) ?></option><?php endforeach; ?></select>
    <label class="mt-2 block text-xs" :for="'discount-source-'+property.key">Contacto, fecha y justificación</label>
    <textarea class="input w-52" rows="2" maxlength="1600" :id="'discount-source-'+property.key" placeholder="Ej. contacto consultado, fecha y sustento del porcentaje" x-model="analysisRows[property.analysisIndex].negotiation_source"></textarea>
</details>
