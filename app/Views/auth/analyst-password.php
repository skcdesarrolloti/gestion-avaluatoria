<section class="mx-auto max-w-lg rounded-xl bg-white p-6">
    <h1 class="text-2xl font-semibold">Cambia tu contraseña inicial</h1>
    <p class="my-3">Antes de abrir avalúos, elige una contraseña propia de 12 a 72 caracteres, distinta de tu usuario. Después vuelve a iniciar sesión con ella.</p>
    <?php if ($error): ?><p role="alert" class="my-3 text-red-800"><?= e($error) ?></p><?php endif; ?>
    <form method="post" action="<?= e(url('acceso/clave')) ?>" class="space-y-4" data-no-fetch x-data="{ busy:false }" @submit="busy=true">
        <?= csrf_field() ?>
        <label class="label">Nueva contraseña<input class="input" type="password" name="password" autocomplete="new-password" minlength="12" maxlength="72" placeholder="Escribe una contraseña propia" required></label>
        <label class="label">Repite la contraseña<input class="input" type="password" name="confirmation" autocomplete="new-password" minlength="12" maxlength="72" placeholder="Vuelve a escribir la contraseña" required></label>
        <button class="btn-primary" :disabled="busy" x-text="busy ? 'Guardando…' : 'Guardar contraseña e iniciar sesión'">Guardar contraseña e iniciar sesión</button>
    </form>
</section>
