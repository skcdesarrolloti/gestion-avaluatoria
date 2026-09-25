    <section id="uso" x-show="tab === 'uso'" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        <p class="eyebrow">Reglamentación del uso del suelo</p><h2 class="mt-2 text-2xl font-semibold">Lectura principal del POT y cuadro de usos</h2>
        <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">Selecciona la vía normativa o uso principal que el perito quiere evaluar. Luego pulsa <strong>Aplicar uso seleccionado</strong> para traer al formulario las reglas del cuadro y los parámetros útiles para potencial constructivo.</p>
        <div class="mt-6 grid gap-4 md:grid-cols-2">
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
                <p class="grow">La lectura se toma de la biblioteca urbana: documento, cuadro, uso principal, reglas y parámetros. Sirve para comparar rutas posibles antes de adoptar mayor y mejor uso.</p>
                <button class="btn-primary" type="submit" formaction="<?= e(url('avaluos/' . $record['id'] . '/normatividad-urbana/cuadro/aplicar')) ?>">Aplicar uso seleccionado</button>
            </div>

            <details class="md:col-span-2 rounded-xl border border-slate-200 bg-slate-50 p-4">
                <summary class="cursor-pointer font-semibold text-slate-900">Ver catálogo de cuadros cargados</summary>
                <div class="mt-4 grid gap-3 md:grid-cols-2">
                    <?php foreach (($urbanCategoryGroups ?? []) as $groupLabel => $cats): ?>
                        <div class="rounded-lg border border-slate-200 bg-white p-3">
                            <p class="text-sm font-semibold text-slate-950"><?= e((string) $groupLabel) ?></p>
                            <p class="mt-1 text-xs leading-5 text-slate-600"><?= e(implode(', ', array_map(static fn (array $cat): string => (string) $cat['code'], $cats))) ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </details>
            <?php $input('land_classification', 'Clasificación del suelo', 'Urbano, expansión, rural, suburbano...'); ?>
            <?php $input('activity_area', 'Área de actividad'); ?>
            <?php $input('normative_zone', 'Zona normativa'); ?>
            <?php $input('urban_treatment', 'Tratamiento urbanístico'); ?>
            <?php $input('urban_license', 'Licencia, acto o soporte urbanístico', 'Licencia, reconocimiento, concepto o No reporta con fuente'); ?>
            <div class="md:col-span-2"><?php $textarea('permitted_use', 'Uso permitido / compatibilidad sustentada', 'Principal, compatible, complementario, restringido o prohibido, con fuente.', 4); ?></div>
            <?php $input('current_use', 'Uso normativo identificado'); ?>
            <?php $input('intended_use', 'Uso pretendido o finalidad del encargo'); ?>
            <?php $input('applicable_activity', 'Actividad aplicable en el cuadro'); ?>
            <?php $input('pot_state', 'Estado del POT o instrumento usado', 'POT vigente, proyecto, resolución especial...'); ?>
            <div class="md:col-span-2"><?php $textarea('urban_norms_applied', 'Normas urbanísticas pertinentes aplicadas', 'POT, Decreto 0977, Decreto 1077, Ley 388, resolución, plan parcial, licencia o acto aplicable.', 5); ?></div>
            <?php $input('use_regulation_table', 'Cuadro de reglamentación identificado', 'Ej. Cuadro No. 7 · Actividad mixta'); ?>
            <div class="md:col-span-2 grid gap-4">
                <?php $textarea('use_principal_text', 'Uso principal del cuadro', 'Actividades principales.', 5, 70000); ?>
                <?php $textarea('use_compatible_text', 'Uso compatible del cuadro', 'Actividades compatibles.', 5, 70000); ?>
                <?php $textarea('use_complementary_text', 'Uso complementario del cuadro', 'Actividades complementarias.', 5, 70000); ?>
                <?php $textarea('use_restricted_text', 'Uso restringido del cuadro', 'Actividades restringidas.', 5, 70000); ?>
                <?php $textarea('use_prohibited_text', 'Uso prohibido del cuadro', 'Actividades prohibidas.', 5, 70000); ?>
            </div>
            <div class="md:col-span-2 grid gap-4 rounded-xl border border-amber-100 bg-amber-50 p-4">
                <h3 class="font-semibold text-amber-950">Campos para potencial constructivo</h3>
                <?php $textarea('norm_unit_basic_text', 'Unidad básica', 'Valores por alcobas, área mínima de unidad o condición equivalente.', 4, 70000); ?>
                <?php $textarea('norm_free_area_text', 'Área libre', 'Área libre por tipología: unifamiliar, bifamiliar, multifamiliar u otra.', 4, 70000); ?>
                <?php $textarea('norm_min_lot_front_text', 'Área y frente mínimos', 'AML, frente mínimo y reglas por tipología.', 4, 70000); ?>
                <?php $textarea('norm_max_height_text', 'Altura máxima', 'Pisos o regla de altura máxima aplicable.', 3, 70000); ?>
                <?php $textarea('norm_construction_index_text', 'Índice de construcción', 'Índice por tipología o condición normativa.', 4, 70000); ?>
                <?php $textarea('norm_isolation_text', 'Aislamientos', 'Antejardín, retiros laterales, posteriores y demás aislamientos.', 4, 70000); ?>
                <?php $textarea('norm_other_potential_text', 'Otros parámetros urbanísticos', 'Estacionamientos, ocupación, cesiones u observaciones.', 4, 70000); ?>
            </div>
        </div>
    </section>
