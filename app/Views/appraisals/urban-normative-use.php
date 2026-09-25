    <section id="uso" x-show="tab === 'uso'"
        x-data="{ usePane: 'decision', actual: '<?= e($value('actual_built_area_m2')) ?>', allowed: '<?= e($value('normative_max_built_area_m2')) ?>', number(v) { const n = parseFloat(String(v || '').replace(',', '.').replace(/[^0-9.-]/g, '')); return Number.isFinite(n) ? n : null }, diff() { const a = this.number(this.actual), b = this.number(this.allowed); return a === null || b === null ? '' : Math.max(0, b - a).toFixed(2) } }"
        class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        <input type="hidden" name="document_slug" value="<?= e($value('document_slug')) ?>">
        <input type="hidden" name="table_slug" value="<?= e($value('table_slug')) ?>">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="eyebrow">Reglamentación y potencial urbano</p>
                <h2 class="mt-2 text-2xl font-semibold">Norma aplicable, edificabilidad y mayor uso</h2>
                <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">El objetivo no es escoger una fuente: es definir qué norma rige el inmueble, qué uso permite y si existe mayor aprovechamiento frente a lo construido. En PH suele quedar como soporte NTS; en lote o suelo desarrollable alimenta el análisis residual.</p>
            </div>
            <span class="rounded-full bg-amber-50 px-3 py-1 text-sm font-semibold text-amber-800">Criterio del perito</span>
        </div>
        <div class="mt-5 grid gap-3 md:grid-cols-3">
            <div class="rounded-xl border border-teal-100 bg-teal-50 p-4">
                <p class="text-xs font-semibold uppercase text-teal-800">Tipo relevante tomado del numeral 1</p>
                <p class="mt-1 text-lg font-semibold text-teal-950"><?= e((string) ($propertyTypeLabel ?? 'No definido')) ?></p>
                <p class="mt-1 text-xs leading-5 text-teal-900">Si el tipo está mal, se corrige en el encargo. Aquí se analiza la norma.</p>
            </div>
            <div class="rounded-xl border border-blue-100 bg-blue-50 p-4 md:col-span-2">
                <p class="text-xs font-semibold uppercase text-blue-800">Pregunta que debe responder el numeral 5</p>
                <p class="mt-1 text-sm leading-6 text-blue-950">¿La norma solo confirma el uso existente o permite un mejor aprovechamiento? Ejemplo: casa de 230 m² con norma para 300 m² deja 70 m² de potencial; lote residencial que permite institucional puede evaluarse por la ruta de mayor y mejor uso.</p>
            </div>
        </div>
        <nav class="mt-6 rounded-xl bg-slate-100 p-2" aria-label="Subsecciones de uso del suelo">
            <div class="flex gap-2 overflow-x-auto">
                <?php foreach ([['decision','1. Norma que rige'],['potencial','2. Potencial constructivo'],['informe','3. Texto para informe'],['catalogo','Catálogo']] as [$key, $label]): ?>
                    <button class="inline-flex min-h-10 shrink-0 items-center rounded-lg px-4 py-2 text-sm font-semibold" type="button"
                        :class="usePane === '<?= e($key) ?>' ? 'bg-teal-700 text-white shadow-sm' : 'bg-white text-teal-800 hover:bg-teal-50'"
                        @click="usePane = '<?= e($key) ?>'">
                        <?= e($label) ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </nav>
        <div x-show="usePane === 'decision'" class="mt-6 grid gap-4 md:grid-cols-2">
            <label class="label md:col-span-2">Uso o vía normativa que vas a probar
                <select class="input" name="category_slug"><option value="">Selecciona la ruta: residencial, institucional, comercial, industrial, turística, portuaria o mixta</option>
                    <?php foreach (($urbanRouteGroups ?? $urbanCategoryGroups ?? []) as $groupLabel => $cats): ?>
                        <optgroup label="<?= e((string) $groupLabel) ?>">
                            <?php foreach ($cats as $cat): ?><option value="<?= e($cat['slug']) ?>" <?= e($selected('category_slug', (string) $cat['slug'])) ?>><?= e($cat['code'] . ' · ' . $cat['name'] . ' · ' . $cat['table_code']) ?></option><?php endforeach; ?>
                        </optgroup>
                    <?php endforeach; ?>
                </select>
                <span class="mt-1 block text-xs font-medium text-slate-500">No tiene que coincidir con el uso actual. En mayor y mejor uso puedes probar una ruta distinta si es legal, físicamente posible y aporta más valor.</span>
            </label>
            <label class="label">Resultado normativo para la ruta probada
                <select class="input" name="use_cross_result">
                    <?php foreach ($useResults as $key => $label): ?><option value="<?= e($key) ?>" <?= e($selected('use_cross_result', (string) $key)) ?>><?= e($label) ?></option><?php endforeach; ?>
                </select>
            </label>
            <?php $input('intended_use', 'Uso analizado o finalidad del encargo', 'Ej. vivienda, oficinas, institucional, comercio, hotel'); ?>
            <div class="md:col-span-2 flex flex-wrap items-center gap-3 rounded-xl border border-blue-100 bg-blue-50 p-4 text-sm text-blue-950">
                <p class="grow">Al aplicar la ruta, el sistema trae el cuadro POT y los parámetros disponibles. Luego el perito ajusta y decide si esa ruta se adopta o se descarta.</p>
                <button class="btn-primary" type="submit" formaction="<?= e(url('avaluos/' . $record['id'] . '/normatividad-urbana/cuadro/aplicar')) ?>">Aplicar ruta probada</button>
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
        <div x-show="usePane === 'potencial'" class="mt-6 grid gap-4 rounded-xl border border-amber-100 bg-amber-50 p-4">
            <div>
                <h3 class="font-semibold text-amber-950">Comparación de edificabilidad</h3>
                <p class="mt-1 text-sm leading-6 text-amber-900">Aquí se estructura lo que después sirve para cabida o residual: cuánto existe, cuánto permite la norma y si la diferencia tiene efecto en valor. En apartamento PH puede quedar como “sin potencial individual verificable”.</p>
            </div>
            <div class="grid gap-4 md:grid-cols-3">
                <label class="label">Área construida actual adoptada m²
                    <input class="input" type="text" name="actual_built_area_m2" x-model="actual" inputmode="decimal" maxlength="40" value="<?= e($value('actual_built_area_m2')) ?>" placeholder="Ej. 230">
                </label>
                <label class="label">Área máxima construible según norma m²
                    <input class="input" type="text" name="normative_max_built_area_m2" x-model="allowed" inputmode="decimal" maxlength="40" value="<?= e($value('normative_max_built_area_m2')) ?>" placeholder="Ej. 300">
                </label>
                <label class="label">Potencial adicional estimado m²
                    <input class="input bg-white" type="text" name="buildable_difference_m2" :value="diff() || '<?= e($value('buildable_difference_m2')) ?>'" inputmode="decimal" maxlength="40" placeholder="Se calcula si diligencias las dos áreas">
                </label>
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                <?php $textarea('norm_max_height_text', 'Altura o número de pisos permitido', 'Pisos, altura o condición aplicable.', 3, 70000); ?>
                <?php $textarea('norm_construction_index_text', 'Índice, ocupación o área construible', 'Índice de construcción, ocupación, área vendible preliminar o fórmula.', 3, 70000); ?>
                <?php $textarea('norm_free_area_text', 'Área libre, aislamientos y retiros relevantes', 'Área libre, antejardín, retiro lateral/posterior y restricciones físicas.', 3, 70000); ?>
                <?php $textarea('norm_min_lot_front_text', 'Área y frente mínimos', 'AML, frente mínimo y regla de lote.', 3, 70000); ?>
                <div class="md:col-span-2"><?php $textarea('constructive_potential_notes', 'Conclusión valuatoria del potencial constructivo', 'Ej. Existe potencial adicional aproximado de 70 m²; se analiza si es legal, viable y relevante para valor. O: PH sin potencial individual verificable.', 4); ?></div>
                <div class="md:col-span-2"><?php $textarea('norm_other_potential_text', 'Otros parámetros para cabida o residual', 'Estacionamientos, cesiones, usos mixtos, afectaciones, servicios, tiempos, riesgos u observaciones.', 4, 70000); ?></div>
            </div>
        </div>
        <div x-show="usePane === 'informe'" class="mt-6 grid gap-4">
            <div class="rounded-xl border border-emerald-100 bg-emerald-50 p-4 text-sm leading-6 text-emerald-950">Este bloque deja lo que pasa al informe: norma aplicable, compatibilidad del uso y conclusión del perito. Para inmueble construido puede bastar este sustento; para lote se complementa con potencial y escenarios.</div>
            <?php $input('use_regulation_table', 'Cuadro y fuente aplicados', 'Se llena al aplicar la ruta normativa'); ?>
            <?php $textarea('permitted_use', 'Uso permitido / compatibilidad sustentada', 'Principal, compatible, complementario, restringido o prohibido, con fuente.', 4); ?>
            <?php $textarea('urban_norms_applied', 'Normas urbanísticas pertinentes aplicadas', 'POT, Decreto 0977, Decreto 1077, Ley 388, resolución, plan parcial, licencia o acto aplicable.', 4); ?>
            <?php $textarea('conclusion', 'Conclusión para el entregable', 'Indica la norma que rige, la ruta adoptada y si existe o no potencial constructivo relevante.', 4); ?>
            <details class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <summary class="cursor-pointer text-sm font-semibold text-slate-900">Ver texto completo del cuadro aplicado</summary>
                <div class="mt-4 grid gap-4 md:grid-cols-2">
                    <?php $textarea('use_principal_text', 'Uso principal del cuadro', 'Actividades principales.', 4, 70000); ?>
                    <?php $textarea('use_compatible_text', 'Uso compatible del cuadro', 'Actividades compatibles.', 4, 70000); ?>
                    <?php $textarea('use_complementary_text', 'Uso complementario del cuadro', 'Actividades complementarias.', 4, 70000); ?>
                    <?php $textarea('use_restricted_text', 'Uso restringido del cuadro', 'Actividades restringidas.', 4, 70000); ?>
                    <div class="md:col-span-2"><?php $textarea('use_prohibited_text', 'Uso prohibido del cuadro', 'Actividades prohibidas.', 4, 70000); ?></div>
                </div>
            </details>
        </div>
        <div x-show="usePane === 'catalogo'" class="mt-6 rounded-xl border border-slate-200 bg-slate-50 p-4">
            <h3 class="font-semibold text-slate-950">Rutas normativas cargadas</h3>
            <p class="mt-1 text-sm leading-6 text-slate-600">Consulta de apoyo. La ruta adoptada se define por criterio valuatorio en 5.3.</p>
            <div class="mt-4 grid gap-3 md:grid-cols-2">
                <?php foreach (($urbanRouteGroups ?? $urbanCategoryGroups ?? []) as $groupLabel => $cats): ?>
                    <div class="rounded-lg border border-slate-200 bg-white p-3">
                        <p class="text-sm font-semibold text-slate-950"><?= e((string) $groupLabel) ?></p>
                        <p class="mt-1 text-xs leading-5 text-slate-600"><?= e(implode(', ', array_map(static fn (array $cat): string => (string) $cat['code'], $cats))) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
