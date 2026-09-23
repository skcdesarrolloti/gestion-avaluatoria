<?php
$today = new DateTimeImmutable('today', new DateTimeZone('America/Bogota'));
?>
<section class="space-y-8">
    <div class="flex flex-wrap items-start justify-between gap-6">
        <div class="max-w-3xl">
            <p class="eyebrow">Administración base</p>
            <h1 class="mt-2 text-3xl font-semibold tracking-tight">Creación de Maestros</h1>
            <p class="mt-3 text-base leading-6 text-slate-600">
                Sube el certificado RAA en PDF. El sistema lee el perito, conserva su identidad y actualiza vigencia y categorías.
            </p>
        </div>
        <a class="btn-primary" href="<?= e(url('#crear-perito')) ?>">Cargar RAA</a>
    </div>

    <?php if ($message): ?><div class="rounded-xl border border-emerald-50 bg-emerald-50 p-4 text-sm font-medium text-emerald-700"><?= e($message) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="rounded-xl border border-red-50 bg-red-50 p-4 text-sm font-medium text-red-700"><?= e($error) ?></div><?php endif; ?>

    <section id="crear-perito" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        <div class="grid gap-5 lg:grid-cols-[1fr_22rem]">
            <div>
                <p class="eyebrow">RAA como fuente</p>
                <h2 class="mt-2 text-2xl font-semibold">Perito responsable</h2>
                <p class="mt-3 text-sm leading-6 text-slate-600">
                    El PDF llena nombre, cédula, número AVAL, expedición, vencimiento y categorías. Si cargas un RAA nuevo del mismo perito, se conserva código, nombre y cédula; solo se actualizan datos verificables del certificado.
                </p>
            </div>
            <div class="rounded-xl border border-amber-100 bg-amber-50 p-4 text-sm leading-6 text-amber-900">
                Un RAA vencido no se guarda como usable y no aparecerá en el expediente valuatorio. El certificado se toma como vigente por 30 días calendario desde su expedición.
            </div>
        </div>
        <form class="mt-6 grid gap-5 md:grid-cols-2" method="post" enctype="multipart/form-data"
            action="<?= e(url('maestros/peritos')) ?>" x-data="{ busy: false }" @submit="busy = true">
            <?= csrf_field() ?>
            <label class="label md:col-span-2">Soporte RAA en PDF
                <input class="input" type="file" name="raa_file" accept="application/pdf,.pdf" required>
                <span class="mt-1 block text-xs leading-5 text-slate-500">Selecciona el certificado descargado del RAA. Debe estar vigente.</span>
            </label>
            <label class="label">Código interno del perito
                <input class="input" name="code" maxlength="10" placeholder="Opcional. Si está vacío se asigna 01, 02...">
            </label>
            <label class="label">Correo alterno, si el PDF no lo trae
                <input class="input" type="email" name="email" maxlength="190" placeholder="correo@dominio.com">
            </label>
            <label class="label">Teléfono alterno, si el PDF no lo trae
                <input class="input" name="phone" maxlength="50" placeholder="Teléfono de contacto">
            </label>
            <label class="label md:col-span-2">Observaciones internas
                <textarea class="input min-h-11" name="notes" rows="3" placeholder="Dato administrativo o salvedad sobre el soporte RAA."></textarea>
            </label>
            <div class="md:col-span-2 flex flex-wrap items-center gap-3 rounded-xl border border-teal-100 bg-teal-50 p-4">
                <button class="btn-primary" type="submit" :disabled="busy" x-text="busy ? 'Leyendo RAA...' : 'Cargar RAA'">Cargar RAA</button>
                <span class="text-sm leading-6 text-teal-900">El sistema leerá el PDF, validará vigencia y actualizará el maestro del perito.</span>
            </div>
            <fieldset class="md:col-span-2 rounded-xl border border-slate-200 bg-slate-50 p-4">
                <legend class="label">Categorías manuales solo como respaldo si el PDF no permite lectura</legend>
                <div class="mt-3 grid gap-3 md:grid-cols-2">
                    <?php foreach ($categories as $code => $label): ?>
                        <label class="flex items-start gap-3 rounded-lg border border-slate-200 bg-white p-3 text-sm text-slate-700">
                            <input class="mt-1 size-4 shrink-0" type="checkbox" name="raa_categories[]" value="<?= e($code) ?>">
                            <span><strong><?= e($code) ?>.</strong> <?= e($label) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </fieldset>
        </form>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <h2 class="text-2xl font-semibold">Peritos registrados</h2>
            <span class="rounded-full bg-teal-50 px-3 py-1 text-sm font-semibold text-teal-800"><?= e(count($appraisers)) ?> registro(s)</span>
        </div>
        <?php if (!$appraisers): ?>
            <p class="mt-4 rounded-xl border border-dashed border-slate-300 p-5 text-sm text-slate-600">Aún no hay peritos creados.</p>
        <?php else: ?>
            <div class="mt-5 grid gap-4 md:grid-cols-2">
                <?php foreach ($appraisers as $appraiser): ?>
                    <?php
                    $expires = (string) ($appraiser['raa_expires_at'] ?? '');
                    $expiry = $expires !== '' ? new DateTimeImmutable($expires) : null;
                    $days = $expiry ? (int) $today->diff($expiry)->format('%r%a') : null;
                    $expired = $days !== null && $days < 0;
                    $soon = $days !== null && $days >= 0 && $days <= 30;
                    $labels = App\Support\RaaCategoryCatalog::labels((string) ($appraiser['raa_categories'] ?? ''));
                    ?>
                    <article class="rounded-xl border border-slate-200 p-5 <?= $expired ? 'bg-red-50/40' : 'bg-white' ?>">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <p class="text-sm font-semibold text-teal-800"><?= e($appraiser['code']) ?> · <?= e($appraiser['raa_number'] ?: 'RAA pendiente') ?></p>
                                <h3 class="mt-1 text-lg font-semibold"><?= e($appraiser['full_name']) ?></h3>
                                <p class="mt-1 text-xs text-slate-500">Cédula <?= e($appraiser['identification_number'] ?? 'pendiente') ?></p>
                            </div>
                            <span class="rounded-full px-3 py-1 text-xs font-semibold <?= $expired ? 'bg-red-100 text-red-700' : 'bg-emerald-50 text-emerald-700' ?>">
                                <?= e($expired ? 'No usable' : (($appraiser['active'] ?? '') === 'Si' ? 'Usable' : 'Inactivo')) ?>
                            </span>
                        </div>
                        <div class="mt-4 grid gap-3 rounded-xl border border-slate-100 bg-slate-50 p-4 text-sm leading-6 text-slate-700">
                            <p><strong>Identificación RAA:</strong> cédula <?= e($appraiser['identification_number'] ?? 'pendiente') ?> · <?= e($appraiser['raa_number'] ?: 'RAA pendiente') ?></p>
                            <p class="font-semibold <?= $expired ? 'text-red-700' : ($soon ? 'text-amber-700' : 'text-emerald-700') ?>">
                                <?= e($expired ? 'RAA vencido' : ($soon ? 'RAA por vencer' : 'RAA vigente')) ?><?= $expires !== '' ? ' · vence ' . e($expires) : '' ?>
                            </p>
                            <p>Expedido <?= e($appraiser['raa_issued_at'] ?? 'pendiente') ?><?= ($appraiser['raa_pin'] ?? '') !== '' ? ' · PIN ' . e($appraiser['raa_pin']) : '' ?></p>
                        </div>
                        <div class="mt-3 grid gap-2 rounded-xl border border-blue-100 bg-blue-50 p-4 text-sm leading-6 text-blue-950">
                            <p><strong>Contacto RAA:</strong> <?= e($appraiser['raa_contact_city'] ?: 'Ciudad pendiente') ?><?= ($appraiser['raa_contact_department'] ?? '') !== '' ? ', ' . e($appraiser['raa_contact_department']) : '' ?></p>
                            <p><?= e($appraiser['raa_contact_address'] ?: 'Dirección pendiente') ?></p>
                            <p><?= e($appraiser['phone'] ?: 'Teléfono pendiente') ?> · <?= e($appraiser['email'] ?: 'Correo pendiente') ?></p>
                        </div>
                        <?php if ($labels): ?><p class="mt-3 text-xs leading-5 text-slate-600"><strong>Categorías:</strong> <?= e(implode(' · ', $labels)) ?></p><?php endif; ?>
                        <?php if (($appraiser['raa_storage_filename'] ?? '') !== ''): ?>
                            <a class="btn-secondary mt-4" target="_blank" rel="noopener" href="<?= e(url('maestros/peritos/' . $appraiser['id'] . '/raa')) ?>">Abrir soporte RAA</a>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</section>
