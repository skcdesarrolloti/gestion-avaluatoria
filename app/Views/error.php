<section class="mx-auto max-w-lg rounded-2xl border border-slate-200 bg-white p-8">
    <h1 class="text-2xl font-semibold">No pudimos completar la solicitud</h1>
    <p role="alert" class="mt-4 text-slate-600"><?= e($message) ?></p>
    <?php if (!empty($detail ?? '')): ?><p class="mt-3 rounded-lg bg-slate-50 p-3 text-xs text-slate-500">Detalle técnico: <?= e($detail) ?></p><?php endif; ?>
    <a class="btn-secondary mt-6" href="<?= e(url('login')) ?>">Ir al acceso</a>
</section>
