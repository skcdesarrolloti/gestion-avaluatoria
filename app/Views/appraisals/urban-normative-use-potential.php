<?php
$urbanIndexTip = static fn (string $text): string => '<span class="help-dot" title="' . e($text) . '">?</span>';
?>
        <div x-show="usePane === 'indices' && !isLot" class="mt-6 rounded-xl border border-blue-100 bg-blue-50 p-4 text-sm leading-6 text-blue-950">
            <p class="font-semibold">Para esta tipología basta conservar la lectura normativa.</p>
            <p class="mt-1">Conserva el cuadro completo de usos de MIDAS y deja conclusión urbanística. Si existe una hipótesis de desarrollo, se cuantifica después en el módulo 8.</p>
        </div>
        <div x-show="usePane === 'indices' && isLot" class="mt-6 grid gap-4 rounded-xl border border-amber-100 bg-amber-50 p-4">
            <div class="grid gap-4 lg:grid-cols-[1fr_1.1fr]">
                <div><h3 class="font-semibold text-amber-950">Parámetros de edificabilidad</h3><p class="mt-1 text-sm leading-6 text-amber-900">Aquí se organiza lo que la norma permite o condiciona: uso, área mínima, frente, altura, índices, área libre, retiros y afectaciones. No se cuantifica cabida en este capítulo.</p></div>
                <div class="rounded-xl border border-amber-200 bg-white p-4 text-sm leading-6 text-amber-950">
                    <p class="font-semibold">Academia rápida: ¿de dónde saco los datos?</p>
                    <ul class="mt-2 list-disc space-y-1 pl-5">
                        <li><strong>Terreno:</strong> escritura, certificado catastral, predial, MIDAS, plano o medición adoptada en 3.2.</li>
                        <li><strong>Retiros y aislamientos:</strong> POT, ficha normativa, licencia, plan parcial, riesgo, rondas, vías, servidumbres o concepto oficial.</li>
                        <li><strong>Índices y altura:</strong> se copian como dato normativo, sin convertirlos aquí en área construible.</li>
                        <li><strong>Factibilidad:</strong> se describe si la norma permite, condiciona o limita el desarrollo. La cabida y los análisis económicos van en el módulo 8.</li>
                    </ul>
                </div>
            </div>
            <div class="grid gap-4 rounded-xl border border-sky-100 bg-white p-4 md:grid-cols-3">
                <label class="label">Modalidad residencial a probar <?= $urbanIndexTip('Escoge la subopción del Cuadro No. 1 que quieres verificar. No es lo mismo vivienda unifamiliar, bifamiliar o multifamiliar: cada una tiene área mínima, frente e índice propio.') ?><select class="input" name="normative_modality" x-model="modality">
                    <option value="">Selecciona si aplica</option>
                    <?php foreach ($residentialModeOptions as $key => $label): ?><option value="<?= e($key) ?>" <?= e($selected('normative_modality', (string) $key)) ?>><?= e($label) ?></option><?php endforeach; ?>
                </select></label>
                <label class="label">Frente del lote m <?= $urbanIndexTip('Dato conectado con 3.2. Si está vacío, complétalo en Bien sujeto > superficies; aquí queda bloqueado para no duplicar fuentes.') ?><input class="input bg-slate-50" type="text" name="lot_front_normative_m" x-model="front" readonly value="<?= e($value('lot_front_normative_m')) ?>" placeholder="Viene de 3.2"></label>
                <div class="rounded-xl border border-sky-100 bg-sky-50 p-3 text-sm leading-6 text-sky-950">
                    <p class="font-semibold">Lectura básica según cuadro</p>
                    <p class="whitespace-pre-line" x-text="compliance()"></p>
                    <button class="btn-secondary mt-3" type="button" @click="applyReq()">Usar parámetros de esta modalidad</button>
                </div>
                <label class="label md:col-span-3">Resumen de factibilidad área/frente/parámetros <?= $urbanIndexTip('Guarda aquí la lectura que luego explica por qué una modalidad es factible, condicionada o descartada. Ejemplo: para multifamiliar RD no cumple frente mínimo 25 m si el predio tiene 12 m.') ?><textarea class="input min-h-24" name="normative_compliance_summary" rows="3" maxlength="5000" x-model="complianceSummary" placeholder="Se puede llenar con el botón o escribir criterio del perito."></textarea></label>
            </div>
            <div class="grid gap-4 md:grid-cols-4">
                <label class="label">Área del terreno m² <?= $urbanIndexTip('Dato conectado con el área adoptada de 3.2. Si no aparece, corrige el área del terreno en el módulo 3; aquí no se edita para conservar trazabilidad.') ?><input class="input bg-slate-50" type="text" name="land_area_normative_m2" x-model="land" readonly value="<?= e($value('land_area_normative_m2')) ?>" placeholder="Viene de 3.2"></label>
                <label class="label">Fondo del lote m <?= $urbanIndexTip('Si no se conoce, el sistema lo estima como área ÷ frente. Para predios irregulares, diligencia el fondo adoptado o explica la limitación.') ?><input class="input" type="text" name="lot_depth_normative_m" x-model="depth" inputmode="decimal" maxlength="40" value="<?= e($value('lot_depth_normative_m')) ?>" :placeholder="fmt(depthValue()) || 'Área ÷ frente'"></label>
                <label class="label">Retiro frontal m <?= $urbanIndexTip('Antejardín o retiro frontal exigido por la norma, licencia o ficha.') ?><input class="input" type="text" name="setback_front_m" x-model="frontSetback" inputmode="decimal" maxlength="40" value="<?= e($value('setback_front_m')) ?>" placeholder="Dato básico"></label>
                <label class="label">Retiro posterior m <?= $urbanIndexTip('Retiro posterior exigido. Si no aplica o no está soportado, déjalo vacío y explica.') ?><input class="input" type="text" name="setback_rear_m" x-model="rearSetback" inputmode="decimal" maxlength="40" value="<?= e($value('setback_rear_m')) ?>" placeholder="Dato básico"></label>
                <label class="label">Retiro lateral izquierdo m <?= $urbanIndexTip('Aislamiento lateral izquierdo. Si el cuadro no lo exige, puede quedar vacío.') ?><input class="input" type="text" name="setback_left_m" x-model="leftSetback" inputmode="decimal" maxlength="40" value="<?= e($value('setback_left_m')) ?>" placeholder="Dato básico"></label>
                <label class="label">Retiro lateral derecho m <?= $urbanIndexTip('Aislamiento lateral derecho. Si el cuadro no lo exige, puede quedar vacío.') ?><input class="input" type="text" name="setback_right_m" x-model="rightSetback" inputmode="decimal" maxlength="40" value="<?= e($value('setback_right_m')) ?>" placeholder="Dato básico"></label>
                <label class="label">Afectaciones no edificables % <?= $urbanIndexTip('Solo para cesiones, ronda, servidumbre, vía, riesgo u otra afectación adicional a los retiros. No reemplaza los retiros en metros.') ?><input class="input" type="text" name="setback_area_percent" x-model="affect" inputmode="decimal" maxlength="40" value="<?= e($value('setback_area_percent')) ?>" placeholder="Opcional"></label>
                <label class="label">Área neta normativa m² <?= $urbanIndexTip('Dato fuente cuando la norma o soporte lo informa. Si requiere cálculo de afectaciones, se desarrolla en el módulo 8.') ?><input class="input bg-white" type="text" name="net_land_area_m2" x-model="net" inputmode="decimal" maxlength="40" value="<?= e($value('net_land_area_m2')) ?>" placeholder="Dato soportado si existe"></label>
                <label class="label">Índice de ocupación <?= $urbanIndexTip('Dato normativo si el cuadro lo informa. No se calcula aquí desde huella o retiros.') ?><input class="input" type="text" name="occupancy_index" x-model="occ" inputmode="decimal" maxlength="40" value="<?= e($value('occupancy_index')) ?>" placeholder="Dato normativo"></label>
                <label class="label">Altura máxima pisos <?= $urbanIndexTip('Número de pisos o altura permitida por la norma. Si depende de frente, vía, tratamiento o cesiones, anota la condición en restricciones.') ?><input class="input" type="text" name="max_floors" x-model="floors" inputmode="decimal" maxlength="40" value="<?= e($value('max_floors')) ?>" placeholder="Ej. 2"></label>
                <label class="label">Índice de construcción <?= $urbanIndexTip('Factor total de construcción permitido cuando la norma lo informa. No se convierte aquí en área construible.') ?><input class="input" type="text" name="construction_index" x-model="ci" inputmode="decimal" maxlength="40" value="<?= e($value('construction_index')) ?>" placeholder="Dato normativo"></label>
                <label class="label md:col-span-4">Frente, fondo, forma, topografía o restricción que afecte la factibilidad <?= $urbanIndexTip('Use este campo para explicar lo que puede limitar la edificabilidad: frente insuficiente, forma irregular, pendiente, servidumbre, afectación, retiro, cesión, redes, riesgo o acceso.') ?><textarea class="input min-h-24" name="norm_physical_base_text" rows="3" maxlength="5000" placeholder="Solo lo que incide: frente insuficiente, forma irregular, pendiente, servidumbre, cesión, afectación o restricción."><?= e($value('norm_physical_base_text')) ?></textarea></label>
            </div>
            <input type="hidden" name="actual_built_area_m2" value="<?= e($value('actual_built_area_m2')) ?>">
            <input type="hidden" name="normative_max_built_area_m2" value="<?= e($value('normative_max_built_area_m2')) ?>">
            <input type="hidden" name="buildable_difference_m2" value="<?= e($value('buildable_difference_m2')) ?>">
            <input type="hidden" name="sellable_area_factor" value="<?= e($value('sellable_area_factor')) ?>">
            <input type="hidden" name="sellable_area_m2" value="<?= e($value('sellable_area_m2')) ?>">
        </div>
        <div x-show="usePane === 'potencial' && !isLot" class="mt-6 rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm leading-6 text-slate-700">
            <p class="font-semibold text-slate-950">Lectura pericial de factibilidad</p>
            <p class="mt-1">Para inmueble construido, deja el uso del suelo y las restricciones en el texto del informe. Usa esta sección solo si quieres documentar una salvedad especial.</p>
        </div>
        <div x-show="usePane === 'potencial' && isLot" class="mt-6 grid gap-4 rounded-xl border border-blue-100 bg-blue-50 p-4">
            <div class="grid gap-4 lg:grid-cols-[1fr_1.1fr]"><div><h3 class="font-semibold text-blue-950">Criterio pericial de factibilidad</h3><p class="mt-1 text-sm leading-6 text-blue-900">Aquí no se calcula un proyecto. El perito deja si la norma permite, condiciona o limita una eventual edificabilidad del predio.</p></div><div class="rounded-xl border border-blue-200 bg-white p-4 text-sm leading-6 text-blue-950"><p class="font-semibold">Cómo leerlo</p><p class="mt-1">Compare norma contra terreno, frente, uso, restricciones y soporte oficial. Si faltan soportes o el predio no cumple una condición mínima, deje la salvedad para el informe y reserve los números para el módulo 8.</p></div></div>
            <label class="label">Conclusión urbanística de factibilidad <?= $urbanIndexTip('Seleccione el criterio normativo. No equivale a cabida, proyecto ni valoración residual.') ?><select class="input" name="constructive_potential_status">
                <?php foreach ([''=>'Selecciona conclusión si aplica','viable'=>'Factibilidad normativa favorable','limitado'=>'Factibilidad condicionada por área, frente o restricción','no_viable'=>'No cumple base normativa con la información disponible','requiere_arquitecto'=>'Pendiente de cabida en módulo 8','requiere_concepto'=>'Pendiente de concepto oficial'] as $key => $label): ?><option value="<?= e($key) ?>" <?= e($selected('constructive_potential_status', (string) $key)) ?>><?= e($label) ?></option><?php endforeach; ?>
            </select></label>
            <div class="grid gap-4 md:grid-cols-2">
                <?php $textarea('norm_max_height_text', 'Altura o pisos permitidos', 'Pisos, altura o condición aplicable.', 3, 70000); ?>
                <?php $textarea('norm_construction_index_text', 'Índice u ocupación normativa', 'Índice de ocupación, índice de construcción, intensidad o condición equivalente según la norma.', 3, 70000); ?>
                <?php $textarea('norm_free_area_text', 'Área libre, aislamientos y retiros', 'Área libre, antejardín, retiro lateral/posterior y restricciones físicas.', 3, 70000); ?>
                <?php $textarea('norm_min_lot_front_text', 'Área y frente mínimos', 'AML, frente mínimo y regla de lote.', 3, 70000); ?>
                <div class="md:col-span-2"><?php $textarea('constructive_potential_notes', 'Conclusión pericial de factibilidad', 'Ej. la norma permite revisar una alternativa, pero queda condicionada por frente, área mínima, restricciones o soporte oficial. Los cálculos se hacen en módulo 8.', 4); ?></div>
                <div class="md:col-span-2"><?php $textarea('norm_other_potential_text', 'Otros parámetros útiles', 'Parqueaderos, cesiones, usos mixtos, servicios, tiempos, riesgos o requisitos de concepto. Mezanines o balcones requieren validación técnica.', 4, 70000); ?></div>
            </div>
        </div>
