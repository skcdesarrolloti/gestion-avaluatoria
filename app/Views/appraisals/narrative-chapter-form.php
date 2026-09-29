<?php
$n = is_array($narrative ?? null) ? $narrative : [];
$sections = is_array($n['sections'] ?? null) ? $n['sections'] : [];
$data = is_array($n['data'] ?? null) ? $n['data'] : [];
$firstCode = (string) ($sections[0]['code'] ?? '');
$activeJson = json_encode($firstCode, JSON_THROW_ON_ERROR);
$fieldValue = static fn (string $key): string => (string) ($data[$key] ?? '');
$textValue = static fn (array $field): string => $fieldValue((string) ($field['key'] ?? '')) !== ''
    ? $fieldValue((string) ($field['key'] ?? ''))
    : (string) ($field['prefill'] ?? '');
?>
<a href="<?= e(url('valuaciones')) ?>" class="inline-flex min-h-11 items-center text-sm font-medium text-teal-800">← Valuaciones</a>
<div class="mt-3 flex flex-wrap items-start justify-between gap-5">
    <div>
        <p class="eyebrow"><?= e((string) ($n['eyebrow'] ?? 'Capítulo')) ?></p>
        <h1 class="mt-2 text-3xl font-semibold"><?= e((string) ($n['title'] ?? 'Capítulo')) ?></h1>
        <p class="mt-3 max-w-3xl text-slate-600"><?= e((string) ($n['intro'] ?? '')) ?></p>
    </div>
    <span class="rounded-full bg-emerald-50 px-3 py-1 text-sm font-semibold text-emerald-800"><?= e((string) ($n['badge'] ?? '')) ?></span>
</div>
<?php require BASE_PATH . '/app/Views/appraisals/step-nav.php'; ?>
<?php if (!empty($n['message'])): ?><p class="mt-6 rounded-xl bg-emerald-50 p-4 text-sm font-semibold text-emerald-800"><?= e((string) $n['message']) ?></p><?php endif; ?>
<?php if (!empty($n['error'])): ?><p class="mt-6 rounded-xl bg-red-50 p-4 text-sm font-semibold text-red-800"><?= e((string) $n['error']) ?></p><?php endif; ?>
<form class="mt-8 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8"
    method="post" action="<?= e((string) ($n['formAction'] ?? '#')) ?>" data-module-autosave
    data-autosave-endpoint="<?= e((string) ($n['autosave'] ?? '')) ?>" x-data="{ tab: <?= e($activeJson) ?> }">
    <?= csrf_field() ?>
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="eyebrow">Estructura del capítulo</p>
            <h2 class="mt-2 text-2xl font-semibold">Diligenciamiento por subnumeral</h2>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                Trabaja una pestaña a la vez. Los textos guardados pasan al entregable; las ayudas solo orientan y no se copian automáticamente.
            </p>
        </div>
        <button class="btn-primary min-h-11" type="submit">Guardar ahora</button>
    </div>
    <p class="mt-3 text-xs font-semibold text-slate-500" data-autosave-status>Autoguardado activo</p>
    <nav class="mt-5 flex gap-2 overflow-x-auto rounded-lg bg-slate-200/70 p-2" aria-label="Subnumerales">
        <?php foreach ($sections as $section): ?>
            <?php $code = (string) ($section['code'] ?? ''); ?>
            <button type="button" class="min-h-11 shrink-0 rounded-md px-3 py-2 text-xs font-semibold transition"
                :class="tab === <?= e(json_encode($code, JSON_THROW_ON_ERROR)) ?> ? 'bg-white text-orange-600 shadow-sm' : 'text-slate-600 hover:bg-white/70'"
                @click="tab = <?= e(json_encode($code, JSON_THROW_ON_ERROR)) ?>">
                <?= e($code . ' ' . (string) ($section['title'] ?? '')) ?>
            </button>
        <?php endforeach; ?>
    </nav>
    <?php foreach ($sections as $section): ?>
        <?php $code = (string) ($section['code'] ?? ''); ?>
        <section class="mt-6 grid gap-5 lg:grid-cols-[1fr_18rem]" x-show="tab === <?= e(json_encode($code, JSON_THROW_ON_ERROR)) ?>">
            <div class="grid gap-4">
                <h3 class="text-xl font-semibold text-slate-950"><?= e($code . ' ' . (string) ($section['title'] ?? '')) ?></h3>
                <?php foreach (($section['fields'] ?? []) as $field): ?>
                    <?php $key = (string) ($field['key'] ?? ''); $type = (string) ($field['type'] ?? 'textarea'); ?>
                    <label class="label"><?= e((string) ($field['label'] ?? $key)) ?>
                        <?php if ($type === 'select'): ?>
                            <select class="input mt-2" name="<?= e($key) ?>">
                                <?php foreach (($field['options'] ?? []) as $value => $label): ?>
                                    <option value="<?= e((string) $value) ?>" <?= $fieldValue($key) === (string) $value ? 'selected' : '' ?>><?= e((string) $label) ?></option>
                                <?php endforeach; ?>
                            </select>
                        <?php else: ?>
                            <textarea class="input mt-2 min-h-28" name="<?= e($key) ?>" maxlength="<?= e((string) ($field['max'] ?? 2200)) ?>"><?= e($textValue($field)) ?></textarea>
                        <?php endif; ?>
                    </label>
                <?php endforeach; ?>
            </div>
            <aside class="rounded-xl border border-blue-100 bg-blue-50 p-4 text-sm leading-6 text-blue-950 lg:self-start">
                <p class="font-semibold">Guía de redacción</p>
                <?php foreach (($section['fields'] ?? []) as $field): ?>
                    <?php if (empty($field['help'])) continue; ?>
                    <p class="mt-3"><span class="font-semibold"><?= e((string) ($field['label'] ?? 'Campo')) ?>:</span> <?= e((string) $field['help']) ?></p>
                <?php endforeach; ?>
            </aside>
        </section>
    <?php endforeach; ?>
    <div class="mt-6 flex justify-end"><button class="btn-primary min-h-11" type="submit">Guardar ahora</button></div>
</form>
