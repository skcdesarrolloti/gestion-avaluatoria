<section class="mx-auto mt-6 max-w-md rounded-2xl border border-slate-200 bg-white p-7 shadow-sm sm:mt-12 sm:p-9">
    <p class="eyebrow">Acceso a funcionarios</p>
    <h1 class="mt-3 text-3xl font-semibold tracking-tight">Bienvenido</h1>
    <p class="mt-3 text-sm leading-6 text-slate-600">Ingresa con el usuario que utilizas en las aplicaciones de SuCasa.</p>
    <?php if (!empty($error)): ?><p role="alert" class="mt-5 rounded-lg bg-red-50 p-3 text-sm text-red-800"><?= e($error) ?></p><?php endif; ?>
    <form class="mt-7 space-y-5" method="post" action="<?= e(url('login')) ?>" data-no-fetch
        x-data="{ submitting: false }" @submit="submitting = true">
        <?= csrf_field() ?>
        <div>
            <label for="username" class="label">Usuario</label>
            <input id="username" name="username" class="input" placeholder="Tu usuario de SuCasa" autocomplete="username"
                value="<?= e($username ?? '') ?>" maxlength="190" required>
        </div>
        <div>
            <label for="password" class="label">Contraseña</label>
            <input id="password" name="password" type="password" class="input" placeholder="Escribe tu contraseña"
                autocomplete="current-password" maxlength="1024" required>
        </div>
        <button type="submit" class="btn-primary w-full" :disabled="submitting" x-text="submitting ? 'Ingresando…' : 'Ingresar'">Ingresar</button>
    </form>
</section>
