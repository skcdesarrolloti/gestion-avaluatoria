        <div x-show="usePane === 'indices'" class="mt-6 grid gap-4 rounded-xl border border-amber-100 bg-amber-50 p-4">
            <div><h3 class="font-semibold text-amber-950">Índices y áreas</h3><p class="mt-1 text-sm leading-6 text-amber-900">Datos normativos para orientar método y potencial; no son diseño ni valor.</p></div>
            <div class="grid gap-4 md:grid-cols-4">
                <label class="label">Área del terreno m²<input class="input" type="text" name="land_area_normative_m2" x-model="land" inputmode="decimal" maxlength="40" value="<?= e($value('land_area_normative_m2')) ?>" placeholder="Ej. 370"></label>
                <label class="label">Retiros / afectaciones %<input class="input" type="text" name="setback_area_percent" x-model="affect" inputmode="decimal" maxlength="40" value="<?= e($value('setback_area_percent')) ?>" placeholder="Ej. 40 o 0,40"></label>
                <label class="label">Área neta normativa m²<input class="input bg-white" type="text" name="net_land_area_m2" x-model="net" :placeholder="fmt(netArea()) || 'Ej. 222'" inputmode="decimal" maxlength="40" value="<?= e($value('net_land_area_m2')) ?>"></label>
                <label class="label">Índice de ocupación<input class="input" type="text" name="occupancy_index" x-model="occ" inputmode="decimal" maxlength="40" value="<?= e($value('occupancy_index')) ?>" placeholder="Ej. 0,60"></label>
                <label class="label">Altura máxima pisos<input class="input" type="text" name="max_floors" x-model="floors" inputmode="decimal" maxlength="40" value="<?= e($value('max_floors')) ?>" placeholder="Ej. 2"></label>
                <label class="label">Índice de construcción<input class="input" type="text" name="construction_index" x-model="ci" inputmode="decimal" maxlength="40" value="<?= e($value('construction_index')) ?>" placeholder="Opcional; vacío usa ocupación × pisos"></label>
                <label class="label">Área máxima construible m²<input class="input bg-white" type="text" name="normative_max_built_area_m2" x-model="maxBuilt" :placeholder="fmt(maxBuild()) || 'Ej. 300'" inputmode="decimal" maxlength="40" value="<?= e($value('normative_max_built_area_m2')) ?>"></label>
                <label class="label">Factor área vendible<input class="input" type="text" name="sellable_area_factor" x-model="sellFactor" inputmode="decimal" maxlength="40" value="<?= e($value('sellable_area_factor')) ?>" placeholder="Ej. 0,75 o 1,20"></label>
                <label class="label md:col-span-2">Área vendible ref. m²<input class="input bg-white" type="text" name="sellable_area_m2" x-model="sellable" :placeholder="fmt(sellableArea()) || 'Solo si aplica'" inputmode="decimal" maxlength="40" value="<?= e($value('sellable_area_m2')) ?>"></label>
                <label class="label">Área construida actual m²<input class="input" type="text" name="actual_built_area_m2" x-model="actual" inputmode="decimal" maxlength="40" value="<?= e($value('actual_built_area_m2')) ?>" placeholder="Ej. 230"></label>
                <label class="label">Potencial adicional m²<input class="input bg-white" type="text" name="buildable_difference_m2" :value="fmt(potential()) || '<?= e($value('buildable_difference_m2')) ?>'" inputmode="decimal" maxlength="40" placeholder="Norma vs existente"></label>
                <label class="label md:col-span-4">Frente, fondo, forma, topografía o restricción que afecte el cálculo<textarea class="input min-h-24" name="norm_physical_base_text" rows="3" maxlength="5000" placeholder="Solo lo que incide: frente insuficiente, forma irregular, pendiente, servidumbre, cesión, afectación o restricción."><?= e($value('norm_physical_base_text')) ?></textarea></label>
            </div>
        </div>
        <div x-show="usePane === 'potencial'" class="mt-6 grid gap-4 rounded-xl border border-blue-100 bg-blue-50 p-4">
            <div><h3 class="font-semibold text-blue-950">Lectura del potencial</h3><p class="mt-1 text-sm leading-6 text-blue-900">Define si el dato orienta mercado, reposición o futuro residual; en PH puede quedar solo como soporte.</p></div>
            <label class="label">Estado pericial del potencial<select class="input" name="constructive_potential_status">
                <?php foreach ([''=>'Selecciona estado si aplica','viable'=>'Viable con soporte básico','limitado'=>'Limitado o condicionado','no_viable'=>'No viable / sin potencial individual','requiere_arquitecto'=>'Requiere arquitecto o cabida','requiere_concepto'=>'Requiere concepto oficial'] as $key => $label): ?><option value="<?= e($key) ?>" <?= e($selected('constructive_potential_status', (string) $key)) ?>><?= e($label) ?></option><?php endforeach; ?>
            </select></label>
            <div class="grid gap-4 md:grid-cols-2">
                <?php $textarea('norm_max_height_text', 'Altura o pisos permitidos', 'Pisos, altura o condición aplicable.', 3, 70000); ?>
                <?php $textarea('norm_construction_index_text', 'Índice, ocupación o área construible', 'Índice, ocupación, área vendible preliminar o fórmula.', 3, 70000); ?>
                <?php $textarea('norm_free_area_text', 'Área libre, aislamientos y retiros', 'Área libre, antejardín, retiro lateral/posterior y restricciones físicas.', 3, 70000); ?>
                <?php $textarea('norm_min_lot_front_text', 'Área y frente mínimos', 'AML, frente mínimo y regla de lote.', 3, 70000); ?>
                <div class="md:col-span-2"><?php $textarea('constructive_potential_notes', 'Conclusión del potencial', 'Ej. potencial adicional aproximado; se analiza si es legal, viable y relevante. O: PH sin potencial individual verificable.', 4); ?></div>
                <div class="md:col-span-2"><?php $textarea('norm_other_potential_text', 'Otros parámetros útiles', 'Parqueaderos, cesiones, usos mixtos, servicios, tiempos y riesgos. Mezanines o balcones requieren validación técnica.', 4, 70000); ?></div>
            </div>
        </div>
