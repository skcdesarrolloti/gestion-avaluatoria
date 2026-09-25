<section class="space-y-7">
    <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wide text-teal-800">Academia valuatoria</p>
            <h1 class="mt-2 text-3xl font-semibold text-slate-950">Glosario de términos valuatorios</h1>
            <p class="mt-3 max-w-3xl text-slate-600">Consulta conceptos base y alimenta la academia con factores o definiciones que ayuden al análisis valuatorio. El buscador revisa término, descripción y fuente.</p>
        </div>
        <div class="flex flex-wrap gap-2 text-xs font-semibold">
            <span class="rounded-full bg-slate-100 px-3 py-1 text-slate-700"><?= e((string) $stats['total']) ?> término(s)</span>
            <span class="rounded-full bg-emerald-50 px-3 py-1 text-emerald-700"><?= e((string) $stats['manual']) ?> manual(es)</span>
        </div>
    </div>
    <?php if ($glossaryMessage): ?><p class="rounded-xl bg-emerald-50 p-4 text-sm font-semibold text-emerald-800"><?= e($glossaryMessage) ?></p><?php endif; ?>
    <?php if ($glossaryError): ?><p class="rounded-xl bg-red-50 p-4 text-sm font-semibold text-red-800"><?= e($glossaryError) ?></p><?php endif; ?>
    <section class="grid gap-5 lg:grid-cols-[1fr_22rem]">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <form class="grid gap-3 md:grid-cols-[1fr_auto_auto] md:items-end" method="get" action="<?= e(url('glosario-valuatorio')) ?>">
                <label class="label">Buscador del glosario
                    <input class="input" type="search" name="q" value="<?= e($query) ?>" placeholder="Busca por término, definición o fuente: valor, depreciación, frente, mayor y mejor uso" autofocus>
                    <span class="mt-1 block text-xs font-medium text-slate-500">Pensado para crecer: filtra por palabras dentro del concepto, la explicación o la fuente.</span>
                </label>
                <button class="btn-secondary" type="submit">Buscar</button>
                <?php if ($query !== ''): ?><a class="btn-secondary text-center" href="<?= e(url('glosario-valuatorio')) ?>">Limpiar</a><?php endif; ?>
            </form>
            <p class="mt-3 text-sm font-semibold text-slate-600"><?= e((string) count($terms)) ?> resultado(s)<?= $query !== '' ? ' para “' . e($query) . '”' : ' visibles' ?>.</p>
            <div class="mt-5 grid gap-3">
                <?php if (!$terms): ?>
                    <p class="rounded-xl bg-amber-50 p-4 text-sm text-amber-900">No hay términos que coincidan con la búsqueda.</p>
                <?php endif; ?>
                <?php foreach ($terms as $term): ?>
                    <article class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <h2 class="text-lg font-semibold text-slate-950"><?= e($term['term']) ?></h2>
                            <?php if ((string) ($term['source_note'] ?? '') !== ''): ?>
                                <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-800"><?= e($term['source_note']) ?></span>
                            <?php endif; ?>
                        </div>
                        <p class="mt-2 text-sm leading-6 text-slate-700"><?= e($term['definition']) ?></p>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
        <aside class="rounded-2xl border border-teal-100 bg-teal-50 p-5 shadow-sm">
            <p class="eyebrow">Alimentar academia</p>
            <h2 class="mt-2 text-xl font-semibold text-slate-950">Agregar factor o concepto</h2>
            <form class="mt-5 space-y-4" method="post" action="<?= e(url('glosario-valuatorio')) ?>">
                <?= csrf_field() ?>
                <label class="label">Factor o concepto
                    <input class="input bg-white" type="text" name="term" maxlength="180" placeholder="Ej. Homogeneización, frente tipo, afectación">
                </label>
                <label class="label">Descripción
                    <textarea class="input min-h-40 bg-white" name="definition" rows="7" maxlength="5000" placeholder="Describe el concepto con lenguaje técnico y claro."></textarea>
                </label>
                <label class="label">Fuente o nota de respaldo
                    <input class="input bg-white" type="text" name="source_note" maxlength="260" placeholder="Ej. Manual, NTS, criterio interno, clase, soporte">
                </label>
                <button class="btn-primary w-full" type="submit">Guardar en glosario</button>
            </form>
        </aside>
    </section>
</section>
