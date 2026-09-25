    <section id="uso" x-show="tab === 'uso'" x-data="{ usePane: 'seleccion' }" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        <input type="hidden" name="document_slug" value="<?= e($value('document_slug')) ?>">
        <input type="hidden" name="table_slug" value="<?= e($value('table_slug')) ?>">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="eyebrow">Reglamentación del uso del suelo</p>
                <h2 class="mt-2 text-2xl font-semibold">Análisis manual según tipo de inmueble</h2>
                <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">Primero se toma el tipo de inmueble relevante definido en el encargo. Luego el perito escoge la ruta normativa que aporta al valor o al mayor y mejor uso. La fuente y el cuadro se llenan como soporte al aplicar la ruta.</p>
            </div>
            <span class="rounded-full bg-amber-50 px-3 py-1 text-sm font-semibold text-amber-800">Manual por el perito</span>
        </div>
        <div class="mt-5 grid gap-3 md:grid-cols-3">
            <div class="rounded-xl border border-teal-100 bg-teal-50 p-4">
                <p class="text-xs font-semibold uppercase text-teal-800">Tipo relevante del inmueble</p>
                <p class="mt-1 text-lg font-semibold text-teal-950"><?= e((string) ($propertyTypeLabel ?? 'No definido')) ?></p>
                <p class="mt-1 text-xs leading-5 text-teal-900">Viene del numeral 1. Si está mal, se corrige allá.</p>
            </div>
            <div class="rounded-xl border border-amber-100 bg-amber-50 p-4 md:col-span-2">
                <p class="text-xs font-semibold uppercase text-amber-800">Criterio de uso</p>
                <p class="mt-1 text-sm leading-6 text-amber-950">Para inmuebles construidos normalmente basta sustentar la norma aplicable. Para lotes, selecciona la ruta que vas a analizar y revisa potencial constructivo para alimentar el futuro residual.</p>
            </div>
        </div>
        <nav class="mt-6 rounded-xl bg-slate-100 p-2" aria-label="Subsecciones de uso del suelo">
            <div class="flex gap-2 overflow-x-auto">
                <?php foreach ([['seleccion','Ruta de análisis'],['norma','Norma para informe'],['potencial','Potencial / residual'],['catalogo','Catálogo']] as [$key, $label]): ?>
                    <button class="inline-flex min-h-10 shrink-0 items-center rounded-lg px-4 py-2 text-sm font-semibold" type="button"
                        :class="usePane === '<?= e($key) ?>' ? 'bg-teal-700 text-white shadow-sm' : 'bg-white text-teal-800 hover:bg-teal-50'"
                        @click="usePane = '<?= e($key) ?>'">
                        <?= e($label) ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </nav>
        <div x-show="usePane === 'seleccion'" class="mt-6 grid gap-4 md:grid-cols-2">
            <label class="label md:col-span-2">Ruta normativa que el perito quiere analizar
                <select class="input" name="category_slug"><option value="">Selecciona uso o ruta normativa según el mayor y mejor uso</option>
                    <?php foreach (($urbanRouteGroups ?? $urbanCategoryGroups ?? []) as $groupLabel => $cats): ?>
                        <optgroup label="<?= e((string) $groupLabel) ?>">
                            <?php foreach ($cats as $cat): ?><option value="<?= e($cat['slug']) ?>" <?= e($selected('category_slug', (string) $cat['slug'])) ?>><?= e($cat['code'] . ' · ' . $cat['name'] . ' · ' . $cat['table_code']) ?></option><?php endforeach; ?>
                        </optgroup>
                    <?php endforeach; ?>
                </select>
            </label>
            <label class="label">Resultado frente al uso consultado o propuesto
                <select class="input" name="use_cross_result">
                    <?php foreach ($useResults as $key => $label): ?><option value="<?= e($key) ?>" <?= e($selected('use_cross_result', (string) $key)) ?>><?= e($label) ?></option><?php endforeach; ?>
                </select>
            </label>
            <?php $input('intended_use', 'Uso pretendido o finalidad del encargo', 'Ej. vivienda, oficinas, hotel, comercio, institucional'); ?>
            <div class="md:col-span-2 flex flex-wrap items-center gap-3 rounded-xl border border-blue-100 bg-blue-50 p-4 text-sm text-blue-950">
                <p class="grow">No necesitas escoger la fuente. Al aplicar esta ruta, el sistema trae el cuadro POT, las reglas y los parámetros disponibles para el entregable.</p>
                <button class="btn-primary" type="submit" formaction="<?= e(url('avaluos/' . $record['id'] . '/normatividad-urbana/cuadro/aplicar')) ?>">Aplicar ruta normativa</button>
            </div>
            <?php $input('land_classification', 'Clasificación del suelo', 'Urbano, expansión, rural, suburbano...'); ?>
            <?php $input('activity_area', 'Área de actividad'); ?>
            <?php $input('current_use', 'Uso normativo identificado'); ?>
            <?php $input('applicable_activity', 'Actividad específica aplicada'); ?>
            <?php $input('normative_zone', 'Zona normativa'); ?>
            <?php $input('urban_treatment', 'Tratamiento urbanístico'); ?>
            <?php $input('pot_state', 'Estado del POT o instrumento usado', 'POT vigente, proyecto, resolución especial...'); ?>
            <?php $input('urban_license', 'Licencia, acto o soporte urbanístico', 'Licencia, reconocimiento, concepto o No reporta con fuente'); ?>
        </div>
        <div x-show="usePane === 'norma'" class="mt-6 grid gap-4">
            <div class="rounded-xl border border-emerald-100 bg-emerald-50 p-4 text-sm leading-6 text-emerald-950">Aquí queda el texto que se puede llevar al informe. En apartamento, oficina, local o inmueble construido puede ser suficiente copiar la norma, indicar compatibilidad y dejar la fuente.</div>
            <?php $input('use_regulation_table', 'Cuadro y fuente aplicados', 'Se llena al aplicar la ruta normativa'); ?>
            <?php $textarea('permitted_use', 'Uso permitido / compatibilidad sustentada', 'Principal, compatible, complementario, restringido o prohibido, con fuente.', 4); ?>
            <?php $textarea('urban_norms_applied', 'Normas urbanísticas pertinentes aplicadas', 'POT, Decreto 0977, Decreto 1077, Ley 388, resolución, plan parcial, licencia o acto aplicable.', 4); ?>
            <div class="grid gap-4 md:grid-cols-2">
                <?php $textarea('use_principal_text', 'Uso principal del cuadro', 'Actividades principales.', 4, 70000); ?>
                <?php $textarea('use_compatible_text', 'Uso compatible del cuadro', 'Actividades compatibles.', 4, 70000); ?>
                <?php $textarea('use_complementary_text', 'Uso complementario del cuadro', 'Actividades complementarias.', 4, 70000); ?>
                <?php $textarea('use_restricted_text', 'Uso restringido del cuadro', 'Actividades restringidas.', 4, 70000); ?>
                <div class="md:col-span-2"><?php $textarea('use_prohibited_text', 'Uso prohibido del cuadro', 'Actividades prohibidas.', 4, 70000); ?></div>
            </div>
        </div>
        <div x-show="usePane === 'potencial'" class="mt-6 grid gap-4 rounded-xl border border-amber-100 bg-amber-50 p-4">
            <div>
                <h3 class="font-semibold text-amber-950">Campos para lote, potencial constructivo y método residual</h3>
                <p class="mt-1 text-sm leading-6 text-amber-900">Esta pestaña es clave cuando el tipo relevante es lote o suelo desarrollable: deja estructurados los parámetros que después sirven para cabida, edificabilidad, área vendible preliminar y restricciones.</p>
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                <?php $textarea('norm_unit_basic_text', 'Unidad básica', 'Valores por alcobas, área mínima de unidad o condición equivalente.', 4, 70000); ?>
                <?php $textarea('norm_free_area_text', 'Área libre', 'Área libre por tipología: unifamiliar, bifamiliar, multifamiliar u otra.', 4, 70000); ?>
                <?php $textarea('norm_min_lot_front_text', 'Área y frente mínimos', 'AML, frente mínimo y reglas por tipología.', 4, 70000); ?>
                <?php $textarea('norm_max_height_text', 'Altura máxima', 'Pisos o regla de altura máxima aplicable.', 4, 70000); ?>
                <?php $textarea('norm_construction_index_text', 'Índice / área de construcción', 'Índice por tipología, área construible o condición normativa.', 4, 70000); ?>
                <?php $textarea('norm_isolation_text', 'Aislamientos', 'Antejardín, retiros laterales, posteriores y demás aislamientos.', 4, 70000); ?>
                <div class="md:col-span-2"><?php $textarea('norm_other_potential_text', 'Otros parámetros urbanísticos', 'Estacionamientos, ocupación, cesiones, intensidad de uso u observaciones.', 4, 70000); ?></div>
            </div>
        </div>
        <div x-show="usePane === 'catalogo'" class="mt-6 rounded-xl border border-slate-200 bg-slate-50 p-4">
            <h3 class="font-semibold text-slate-950">Catálogo de rutas normativas cargadas</h3>
            <p class="mt-1 text-sm leading-6 text-slate-600">Consulta rápida de las rutas disponibles. Es solo referencia; la decisión valuatoria se toma en “Ruta de análisis”.</p>
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
