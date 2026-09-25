    <section id="uso" x-show="tab === 'uso'" x-data="{ usePane: 'seleccion' }" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="eyebrow">Reglamentación del uso del suelo</p>
                <h2 class="mt-2 text-2xl font-semibold">Lectura manual del POT y cuadro de usos</h2>
                <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">Cuando MIDAS no entregue la ficha, selecciona manualmente el cuadro y el uso principal. Para inmuebles construidos se deja la norma copiada y sustentada; para lotes se alimentan los campos que luego soportan el método residual.</p>
            </div>
            <span class="rounded-full bg-amber-50 px-3 py-1 text-sm font-semibold text-amber-800">Manual por el perito</span>
        </div>
        <nav class="mt-6 rounded-xl bg-slate-100 p-2" aria-label="Subsecciones de uso del suelo">
            <div class="flex gap-2 overflow-x-auto">
                <?php foreach ([['seleccion','Selección'],['norma','Norma para informe'],['potencial','Potencial / residual'],['catalogo','Catálogo']] as [$key, $label]): ?>
                    <button class="inline-flex min-h-10 shrink-0 items-center rounded-lg px-4 py-2 text-sm font-semibold" type="button"
                        :class="usePane === '<?= e($key) ?>' ? 'bg-teal-700 text-white shadow-sm' : 'bg-white text-teal-800 hover:bg-teal-50'"
                        @click="usePane = '<?= e($key) ?>'">
                        <?= e($label) ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </nav>
        <div x-show="usePane === 'seleccion'" class="mt-6 grid gap-4 md:grid-cols-2">
            <label class="label">Documento normativo fuente
                <select class="input" name="document_slug"><option value="">Selecciona fuente si aplica</option>
                    <?php foreach ($urbanDocuments as $doc): ?><option value="<?= e($doc['slug']) ?>" <?= e($selected('document_slug', (string) $doc['slug'])) ?>><?= e($doc['title']) ?></option><?php endforeach; ?>
                </select>
            </label>
            <label class="label">Cuadro POT aplicable
                <select class="input" name="table_slug"><option value="">Selecciona cuadro si aplica</option>
                    <?php foreach ($urbanDocuments as $doc): foreach (($doc['tables'] ?? []) as $table): ?><option value="<?= e($table['slug']) ?>" <?= e($selected('table_slug', (string) $table['slug'])) ?>><?= e($table['table_code'] . ' · ' . $table['title']) ?></option><?php endforeach; endforeach; ?>
                </select>
            </label>
            <label class="label">Uso principal o vía normativa a aplicar
                <select class="input" name="category_slug"><option value="">Selecciona el uso principal o escenario normativo</option>
                    <?php foreach (($urbanCategoryGroups ?? []) as $groupLabel => $cats): ?>
                        <optgroup label="<?= e((string) $groupLabel) ?>">
                            <?php foreach ($cats as $cat): ?><option value="<?= e($cat['slug']) ?>" <?= e($selected('category_slug', (string) $cat['slug'])) ?>><?= e($cat['code'] . ' · ' . $cat['name']) ?></option><?php endforeach; ?>
                        </optgroup>
                    <?php endforeach; ?>
                </select>
            </label>
            <label class="label">Resultado frente al uso consultado
                <select class="input" name="use_cross_result">
                    <?php foreach ($useResults as $key => $label): ?><option value="<?= e($key) ?>" <?= e($selected('use_cross_result', (string) $key)) ?>><?= e($label) ?></option><?php endforeach; ?>
                </select>
            </label>
            <div class="md:col-span-2 flex flex-wrap items-center gap-3 rounded-xl border border-blue-100 bg-blue-50 p-4 text-sm text-blue-950">
                <p class="grow">Al aplicar, la biblioteca llena reglas y parámetros. Luego revisa y edita solo lo que el caso necesite.</p>
                <button class="btn-primary" type="submit" formaction="<?= e(url('avaluos/' . $record['id'] . '/normatividad-urbana/cuadro/aplicar')) ?>">Aplicar uso seleccionado</button>
            </div>
            <?php $input('land_classification', 'Clasificación del suelo', 'Urbano, expansión, rural, suburbano...'); ?>
            <?php $input('activity_area', 'Área de actividad'); ?>
            <?php $input('normative_zone', 'Zona normativa'); ?>
            <?php $input('urban_treatment', 'Tratamiento urbanístico'); ?>
            <?php $input('current_use', 'Uso normativo identificado'); ?>
            <?php $input('intended_use', 'Uso pretendido o finalidad del encargo'); ?>
            <?php $input('applicable_activity', 'Actividad aplicable en el cuadro'); ?>
            <?php $input('pot_state', 'Estado del POT o instrumento usado', 'POT vigente, proyecto, resolución especial...'); ?>
            <div class="md:col-span-2"><?php $input('urban_license', 'Licencia, acto o soporte urbanístico', 'Licencia, reconocimiento, concepto o No reporta con fuente'); ?></div>
        </div>
        <div x-show="usePane === 'norma'" class="mt-6 grid gap-4">
            <div class="rounded-xl border border-emerald-100 bg-emerald-50 p-4 text-sm leading-6 text-emerald-950">Para apartamento, oficina o local construido, esta pestaña puede bastar: deja copiada la norma aplicable, la compatibilidad y la fuente para el entregable. El análisis profundo queda para lotes o inmuebles con cambio de uso.</div>
            <?php $textarea('permitted_use', 'Uso permitido / compatibilidad sustentada', 'Principal, compatible, complementario, restringido o prohibido, con fuente.', 4); ?>
            <?php $textarea('urban_norms_applied', 'Normas urbanísticas pertinentes aplicadas', 'POT, Decreto 0977, Decreto 1077, Ley 388, resolución, plan parcial, licencia o acto aplicable.', 4); ?>
            <?php $input('use_regulation_table', 'Cuadro de reglamentación identificado', 'Ej. Cuadro No. 7 · Actividad mixta'); ?>
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
                <h3 class="font-semibold text-amber-950">Campos para potencial constructivo y método residual</h3>
                <p class="mt-1 text-sm leading-6 text-amber-900">Para lotes, revisa estos campos con cuidado: son la base futura para estimar cabida, edificabilidad, área vendible preliminar y restricciones antes de valorar por residual.</p>
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
            <h3 class="font-semibold text-slate-950">Catálogo de cuadros cargados</h3>
            <p class="mt-1 text-sm leading-6 text-slate-600">Consulta rápida de las rutas disponibles. El perito decide cuál aplica al inmueble y puede evaluar varias en 5.3 antes de adoptar mayor y mejor uso.</p>
            <div class="mt-4 grid gap-3 md:grid-cols-2">
                <?php foreach (($urbanCategoryGroups ?? []) as $groupLabel => $cats): ?>
                    <div class="rounded-lg border border-slate-200 bg-white p-3">
                        <p class="text-sm font-semibold text-slate-950"><?= e((string) $groupLabel) ?></p>
                        <p class="mt-1 text-xs leading-5 text-slate-600"><?= e(implode(', ', array_map(static fn (array $cat): string => (string) $cat['code'], $cats))) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
