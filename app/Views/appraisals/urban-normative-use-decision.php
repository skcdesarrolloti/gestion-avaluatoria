        <div x-show="usePane === 'decision'" class="mt-6 grid gap-4 md:grid-cols-2">
            <div class="md:col-span-2 rounded-xl border border-amber-100 bg-amber-50 p-4 text-sm leading-6 text-amber-950"><strong>Uso simple:</strong> escoge la vía normativa. Si es residencial, abajo aparece el cuadro de opciones constructivas para decidir con área, frente e índice.</div>
            <label class="label md:col-span-2">Vía normativa a probar <?= $urbanUseTip('Es la ruta POT que se quiere probar; puede diferir de la tipología física.') ?>
                <select class="input" name="category_slug" x-model="categorySlug"><option value="">Selecciona la ruta: residencial, institucional, comercial, industrial, turística, portuaria o mixta</option>
                    <?php foreach (($urbanRouteGroups ?? $urbanCategoryGroups ?? []) as $groupLabel => $cats): ?>
                        <optgroup label="<?= e((string) $groupLabel) ?>">
                            <?php foreach ($cats as $cat): ?><option value="<?= e($cat['slug']) ?>" <?= e($selected('category_slug', (string) $cat['slug'])) ?>><?= e($cat['code'] . ' · ' . $cat['name'] . ' · ' . $cat['table_code']) ?></option><?php endforeach; ?>
                        </optgroup>
                    <?php endforeach; ?>
                </select>
                <span class="mt-1 block text-xs font-medium text-slate-500">Puede diferir del uso actual si es legal, físicamente posible y aporta más valor.</span>
            </label>
            <div class="md:col-span-2 overflow-x-auto rounded-xl border border-slate-200" x-show="residential[categorySlug]">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs uppercase text-slate-600"><tr><th class="px-3 py-2">Opción</th><th class="px-3 py-2">Área mín.</th><th class="px-3 py-2">Frente mín.</th><th class="px-3 py-2">Cumple</th><th class="px-3 py-2">Índice</th><th class="px-3 py-2">Máx.</th><th class="px-3 py-2">Potencial</th><th class="px-3 py-2">Acción</th></tr></thead>
                    <tbody class="divide-y divide-slate-100 bg-white"><template x-for="(rule, mode) in residential[categorySlug]?.data || {}" :key="mode"><tr><td class="px-3 py-2 font-semibold" x-text="rule.label"></td><td class="px-3 py-2" x-text="rule.min_area_m2 + ' m²'"></td><td class="px-3 py-2" x-text="rule.min_front_m + ' m'"></td><td class="px-3 py-2"><span class="rounded-full px-2 py-1 text-xs font-semibold" :class="optClass(rule)" x-text="optStatus(rule)"></span></td><td class="px-3 py-2" x-text="rule.construction_index"></td><td class="px-3 py-2" x-text="fmt(optMax(rule))"></td><td class="px-3 py-2 font-semibold text-teal-800" x-text="fmt(optPot(rule))"></td><td class="px-3 py-2"><button class="btn-secondary" type="button" @click="adoptMode(mode, rule)">Usar</button></td></tr></template></tbody>
                </table>
            </div>
            <p class="md:col-span-2 rounded-xl bg-slate-50 p-3 text-sm text-slate-700" x-show="categorySlug && !residential[categorySlug]">Esta ruta aún no tiene índices estructurados para cálculo automático. Déjala como soporte normativo y registra el análisis manual o concepto oficial.</p>
            <label class="label">Resultado de la ruta <?= $urbanUseTip('Sale del cuadro o concepto y sustenta si la vía se usa o descarta.') ?>
                <select class="input" name="use_cross_result">
                    <?php foreach ($useResults as $key => $label): ?><option value="<?= e($key) ?>" <?= e($selected('use_cross_result', (string) $key)) ?>><?= e($label) ?></option><?php endforeach; ?>
                </select>
            </label>
            <label class="label">Uso previsto del informe, traído del módulo 1 <?= $urbanUseTip('Viene del encargo; no define la norma urbana. La ruta se escoge arriba.') ?>
                <textarea class="input min-h-24 bg-slate-50" name="intended_use" rows="3" maxlength="1200" readonly placeholder="Se toma del módulo 1"><?= e($value('intended_use') ?: $urbanUseFromModuleOne) ?></textarea>
                <span class="mt-1 block text-xs font-medium text-slate-500">Si está mal, corrígelo en el módulo 1. Aquí solo se muestra para no perder el contexto del encargo.</span>
            </label>
            <div class="md:col-span-2 flex flex-wrap items-center gap-3 rounded-xl border border-blue-100 bg-blue-50 p-4 text-sm text-blue-950">
                <p class="grow"><strong>Cuándo oprimir este botón:</strong> después de escoger vía y resultado. Carga reglas; luego ajusta, adopta o descarta.</p>
                <button class="btn-primary" type="submit"
                    name="return_to" value="<?= e('avaluos/' . $record['id'] . '/normatividad-urbana#uso-indices') ?>"
                    formaction="<?= e(url('avaluos/' . $record['id'] . '/normatividad-urbana/cuadro/aplicar')) ?>">Aplicar ruta probada</button>
            </div>
            <div class="md:col-span-2 rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm leading-6 text-slate-700">
                <p class="font-semibold text-slate-900">De dónde salen los siguientes campos</p>
                <p class="mt-1">Clasificación, área, tratamiento, zona y licencia salen del POT, ficha normativa, Planeación, licencia, MIDAS o cuadro cargado. Sin soporte, déjalo pendiente o explica la limitación.</p>
            </div>
            <?php $input('land_classification', 'Clasificación del suelo', 'Urbano, expansión, rural, suburbano...'); ?>
            <?php $input('activity_area', 'Área de actividad'); ?>
            <?php $input('current_use', 'Uso normativo identificado'); ?>
            <?php $input('applicable_activity', 'Actividad específica aplicada'); ?>
            <?php $input('normative_zone', 'Zona normativa'); ?>
            <?php $input('urban_treatment', 'Tratamiento urbanístico'); ?>
            <?php $input('pot_state', 'Instrumento normativo usado', 'POT vigente, plan parcial, resolución, licencia...'); ?>
            <?php $input('urban_license', 'Licencia, acto o soporte urbanístico', 'Licencia, reconocimiento, concepto o No reporta con fuente'); ?>
        </div>
