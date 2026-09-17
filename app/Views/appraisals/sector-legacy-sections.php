<details class="mt-6 rounded-xl border border-slate-200 bg-slate-50 p-5">
    <summary class="cursor-pointer text-sm font-semibold text-slate-800">
        Ficha base heredada del informe
    </summary>
    <p class="mt-2 text-sm leading-6 text-slate-600">
        Conserva los campos originales del numeral 2 mientras la ficha avanzada se consolida.
    </p>
    <nav class="mt-5 flex gap-2 overflow-x-auto rounded-xl bg-white p-2" aria-label="Subsecciones de sector">
        <?php foreach ($sectorSections as $key => [$number, $label]): ?>
            <button type="button" class="min-h-12 shrink-0 rounded-lg px-4 py-2 text-left text-sm font-semibold"
                @click="activeSector = '<?= e($key) ?>'; history.replaceState(null, '', '#<?= e($key) ?>')"
                :class="activeSector === '<?= e($key) ?>' ? 'bg-blue-700 text-white shadow-sm' : 'bg-white text-blue-800 hover:bg-slate-50'">
                <span class="block text-xs opacity-80"><?= e($number) ?></span>
                <?= e($label) ?>
            </button>
        <?php endforeach; ?>
    </nav>

    <?php foreach ($sectorSections as $key => [$number, $label, $fields]): ?>
        <section id="<?= e($key) ?>" class="mt-6 rounded-xl border border-slate-200 bg-white p-5 scroll-mt-6"
            x-show="activeSector === '<?= e($key) ?>'">
            <p class="eyebrow"><?= e($number) ?></p>
            <h3 class="mt-2 text-xl font-semibold"><?= e($label) ?></h3>
            <div class="mt-5 grid gap-5 md:grid-cols-2">
                <?php foreach ($fields as [$field, $fieldLabel, $type]): ?>
                    <?php require BASE_PATH . '/app/Views/appraisals/sector-field.php'; ?>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endforeach; ?>
</details>
