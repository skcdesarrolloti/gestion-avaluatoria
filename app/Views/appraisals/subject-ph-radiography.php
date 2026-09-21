<section class="mt-5 grid gap-4" x-show="tab === 'base'">
    <div class="rounded-xl border border-blue-100 bg-blue-50 p-4">
        <div class="grid gap-4 lg:grid-cols-[1fr_18rem]">
            <div>
                <h3 class="text-lg font-semibold text-blue-950">Base PH común y matriz comparativa</h3>
                <p class="mt-2 text-sm leading-6 text-blue-950">
                    Llave principal, lectura común, tipología y dotación para comparar solo contra copropiedades similares.
                </p>
            </div>
            <label class="label text-blue-950">Tipología aplicada
                <select class="input bg-white" x-model="phTypology">
                    <option value="">Selecciona tipología PH</option>
                    <?php foreach ($phCatalog['typologies'] as $value => $label): ?>
                        <option value="<?= e($value) ?>"><?= e($label) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
        </div>
    </div>
    <div class="grid gap-4 xl:grid-cols-4">
        <?php foreach ($commonStats as [$groupTitle, $found, $total]): ?>
            <div class="rounded-xl border border-slate-200 bg-white p-4">
                <p class="text-xs font-semibold uppercase text-slate-500"><?= e($groupTitle) ?></p>
                <p class="mt-2 text-2xl font-semibold text-slate-900"><?= (int) $found ?> / <?= (int) $total ?></p>
                <p class="mt-1 text-xs text-slate-600">menciones con soporte documental o manual</p>
            </div>
        <?php endforeach; ?>
    </div>
    <?php require BASE_PATH . '/app/Views/appraisals/subject-ph-base-fields.php'; ?>
    <div class="grid gap-4 lg:grid-cols-2">
        <div class="rounded-xl border border-slate-200 bg-white p-4">
            <h4 class="font-semibold">Clasificación de dotación</h4>
            <p class="mt-2 text-sm leading-6 text-slate-600">
                <?= e((string) ($technical['dotacion_tipologia'] ?? 'Selecciona la tipología y recalcula el soporte para generar la matriz comparativa.')) ?>
            </p>
            <p class="mt-3 rounded-lg bg-slate-50 p-3 text-sm leading-6 text-slate-700">
                <?= e((string) ($technical['nivel_dotacion_comparativa'] ?? 'Sin nivel comparativo calculado para esta lectura.')) ?>
            </p>
            <?php if ($priorityKeys): ?>
                <p class="mt-3 text-xs font-semibold text-slate-500">
                    Prioritarios detectados: <?= count($priorityFound) ?> de <?= count($priorityKeys) ?>.
                </p>
            <?php endif; ?>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4">
            <h4 class="font-semibold">Llave principal</h4>
            <p class="mt-2 text-sm leading-6 text-slate-700">
                <?= e($phText('ph_name') ?: 'Nombre de copropiedad pendiente') ?> ·
                <span x-text="phTypologyLabel()"></span> ·
                <?= e((string) ($technical['ciudad_municipio'] ?? 'ciudad pendiente')) ?>
            </p>
            <p class="mt-3 text-xs leading-5 text-slate-600">
                Matrícula y escritura son validadores auxiliares; la búsqueda debe partir del nombre, tipología y ciudad.
            </p>
        </div>
    </div>
    <?php $renderPhTabSummary('resumen_base_ph', 'Texto editable para Entregable'); ?>
</section>
