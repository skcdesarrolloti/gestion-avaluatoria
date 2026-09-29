<article class="rounded-xl border border-blue-100 bg-blue-50 p-5">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h2 class="text-lg font-semibold text-blue-950">Qué se aloja aquí</h2>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-blue-950">
                MIDAS guarda capas comunes. Los soportes propios de un avalúo se anexan en el numeral 2.
            </p>
        </div>
        <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-blue-800">Separado de Normatividad Urbana</span>
    </div>
    <div class="mt-4 flex gap-2 overflow-x-auto rounded-lg bg-white/60 p-2" role="tablist" aria-label="Familias MIDAS">
        <?php foreach ($sections as $sectionTitle => $sectionGroups): ?>
            <button type="button"
                class="min-h-10 shrink-0 rounded-md px-3 py-2 text-xs font-semibold transition"
                :class="guide === <?= e(json_encode($sectionTitle, JSON_THROW_ON_ERROR)) ?> ? 'bg-white text-orange-600 shadow-sm' : 'text-blue-900 hover:bg-white/70'"
                @click="guide = <?= e(json_encode($sectionTitle, JSON_THROW_ON_ERROR)) ?>">
                <?= e($sectionTitle) ?>
            </button>
        <?php endforeach; ?>
    </div>
    <?php foreach ($sections as $sectionTitle => $sectionGroups): ?>
        <div class="mt-4 grid gap-3 sm:grid-cols-2" x-show="guide === <?= e(json_encode($sectionTitle, JSON_THROW_ON_ERROR)) ?>">
            <?php foreach ($sectionGroups as $group): ?>
                <?php if (!isset($groups[$group])) { continue; } ?>
                <div class="rounded-lg bg-white/80 p-3 text-xs">
                    <p class="text-anywhere font-semibold uppercase text-blue-800"><?= e($group) ?></p>
                    <p class="mt-1 line-clamp-2 leading-5 text-blue-900"><?= e((string) $groups[$group]) ?></p>
                    <p class="mt-2 rounded-md bg-blue-100/70 p-2 font-semibold leading-4 text-blue-800">
                        Se inserta en: <?= e($targets[$group] ?? 'Pendiente de clasificar por el analista.') ?>
                    </p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
</article>
