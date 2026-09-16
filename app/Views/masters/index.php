<section class="space-y-8">
    <div class="flex flex-wrap items-start justify-between gap-6">
        <div class="max-w-3xl">
            <p class="eyebrow">Administración base</p>
            <h1 class="mt-2 text-3xl font-semibold tracking-tight">Creación de Maestros</h1>
            <p class="mt-3 text-base leading-6 text-slate-600">
                Administra los catálogos que alimentan Valuaciones: peritos y ubicación nacional
                por departamento, ciudad o municipio, y barrio, vereda o sector.
            </p>
        </div>
        <a class="btn-primary" href="<?= e(url('#maestros-geograficos')) ?>">Crear maestro</a>
    </div>

    <?php if ($message): ?>
        <div class="rounded-xl border border-emerald-50 bg-emerald-50 p-4 text-sm font-medium text-emerald-700"><?= e($message) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="rounded-xl border border-red-50 bg-red-50 p-4 text-sm font-medium text-red-700"><?= e($error) ?></div>
    <?php endif; ?>

    <?php require BASE_PATH . '/app/Views/masters/locations.php'; ?>

    <section id="crear-perito" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="eyebrow">Maestro actual</p>
                <h2 class="mt-2 text-2xl font-semibold">Peritos</h2>
            </div>
            <span class="rounded-full bg-teal-50 px-3 py-1 text-sm font-semibold text-teal-800">
                <?= e(count($appraisers)) ?> registro(s)
            </span>
        </div>
        <form class="mt-6 grid gap-5 md:grid-cols-2" method="post" enctype="multipart/form-data"
            action="<?= e(url('maestros/peritos')) ?>" x-data="{ busy: false }" @submit="busy = true">
            <?= csrf_field() ?>
            <label class="label">Código del perito
                <input class="input" name="code" maxlength="10" placeholder="Ej. 01" required>
            </label>
            <label class="label">Nombre completo
                <input class="input" name="full_name" maxlength="160" placeholder="Nombre del perito" required>
            </label>
            <label class="label">Correo
                <input class="input" type="email" name="email" maxlength="190" placeholder="correo@dominio.com">
            </label>
            <label class="label">Teléfono
                <input class="input" name="phone" maxlength="50" placeholder="Teléfono de contacto">
            </label>
            <label class="label">Registro RAA
                <input class="input" name="raa_number" maxlength="80" placeholder="Número o referencia RAA" required>
            </label>
            <label class="label">Vencimiento del RAA
                <input class="input" type="date" name="raa_expires_at" required>
            </label>
            <label class="label">Soporte RAA en PDF
                <input class="input" type="file" name="raa_file" accept="application/pdf,.pdf" required>
            </label>
            <label class="label">Estado
                <select class="input" name="active">
                    <option value="Si">Activo</option>
                    <option value="No">Inactivo</option>
                </select>
            </label>
            <fieldset class="md:col-span-2">
                <legend class="label">Categorías RAA autorizadas</legend>
                <div class="mt-3 grid gap-3 md:grid-cols-2">
                    <?php foreach ($categories as $code => $label): ?>
                        <label class="flex items-start gap-3 rounded-lg border border-slate-200 bg-white p-3 text-sm text-slate-700">
                            <input class="mt-1 size-4 shrink-0" type="checkbox" name="raa_categories[]" value="<?= e($code) ?>">
                            <span><strong><?= e($code) ?>.</strong> <?= e($label) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </fieldset>
            <label class="label md:col-span-2">Observaciones
                <textarea class="input min-h-11" name="notes" rows="3"
                    placeholder="Datos adicionales que debamos conservar mientras definimos el maestro."></textarea>
            </label>
            <div class="md:col-span-2">
                <button class="btn-primary" type="submit" :disabled="busy"
                    x-text="busy ? 'Guardando...' : 'Guardar perito'">Guardar perito</button>
            </div>
        </form>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        <h2 class="text-2xl font-semibold">Peritos registrados</h2>
        <?php if (!$appraisers): ?>
            <p class="mt-4 rounded-xl border border-dashed border-slate-300 p-5 text-sm text-slate-600">
                Aún no hay peritos creados.
            </p>
        <?php else: ?>
            <div class="mt-5 grid gap-4 md:grid-cols-2">
                <?php foreach ($appraisers as $appraiser): ?>
                    <?php
                    $expires = (string) ($appraiser['raa_expires_at'] ?? '');
                    $today = new DateTimeImmutable('today', new DateTimeZone('America/Bogota'));
                    $expiry = $expires !== '' ? new DateTimeImmutable($expires) : null;
                    $days = $expiry ? (int) $today->diff($expiry)->format('%r%a') : null;
                    $expired = $days !== null && $days < 0;
                    $soon = $days !== null && $days >= 0 && $days <= 30;
                    $labels = App\Support\RaaCategoryCatalog::labels((string) ($appraiser['raa_categories'] ?? ''));
                    ?>
                    <article class="rounded-xl border border-slate-200 p-5">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <p class="text-sm font-semibold text-teal-800"><?= e($appraiser['code']) ?></p>
                                <h3 class="mt-1 text-lg font-semibold"><?= e($appraiser['full_name']) ?></h3>
                            </div>
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                <?= e($appraiser['active'] === 'Si' ? 'Activo' : 'Inactivo') ?>
                            </span>
                        </div>
                        <p class="mt-3 text-sm text-slate-600"><?= e($appraiser['email'] ?: 'Correo pendiente') ?></p>
                        <p class="mt-1 text-sm text-slate-600"><?= e($appraiser['raa_number'] ?: 'RAA pendiente') ?></p>
                        <p class="mt-3 text-sm font-semibold <?= $expired ? 'text-red-700' : ($soon ? 'text-amber-700' : 'text-emerald-700') ?>">
                            <?= e($expired ? 'RAA vencido' : ($soon ? 'RAA por vencer' : 'RAA vigente')) ?>
                            <?= $expires !== '' ? ' · ' . e($expires) : '' ?>
                        </p>
                        <?php if ($labels): ?>
                            <p class="mt-3 text-xs leading-5 text-slate-600"><?= e(implode(' · ', $labels)) ?></p>
                        <?php endif; ?>
                        <?php if (($appraiser['raa_storage_filename'] ?? '') !== ''): ?>
                            <a class="btn-secondary mt-4" target="_blank" rel="noopener"
                                href="<?= e(url('maestros/peritos/' . $appraiser['id'] . '/raa')) ?>">Abrir soporte RAA</a>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</section>
