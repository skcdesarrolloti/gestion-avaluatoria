<div id="nuevo-avaluo" class="flex flex-wrap items-end justify-between gap-5">
    <div><p class="eyebrow">Espacio de trabajo</p><h1 class="mt-2 text-3xl font-semibold tracking-tight">Mis avalúos</h1>
        <p class="mt-3 text-slate-600">Organiza tus fichas y continúa donde quedaste.</p></div>
    <form method="post" action="<?= e(url('avaluos')) ?>" x-data="{ busy: false }" @submit="busy = true">
        <?= csrf_field() ?><button class="btn-primary" type="submit" :disabled="busy" x-text="busy ? 'Creando…' : 'Crear ficha'">Crear ficha</button>
    </form>
</div>
<?php if (!$rows): ?>
    <section class="mt-9 rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-16 text-center">
        <h2 class="text-lg font-semibold">Aún no hay fichas en esta página</h2>
        <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-600">Crea una ficha para registrar la información inicial del inmueble. Puedes completarla poco a poco.</p>
    </section>
<?php else: ?>
    <div class="mt-9 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <?php foreach ($rows as $row): ?>
            <a href="<?= e(url('avaluos/' . $row['id'] . '/expediente')) ?>" class="rounded-xl border border-slate-200 bg-white p-6 transition hover:border-teal-700">
                <span class="rounded-md bg-teal-50 px-2 py-1 text-xs font-medium text-teal-800">Borrador</span>
                <h2 class="mt-4 break-words text-lg font-semibold"><?= e($row['titulo'] ?: 'Ficha sin título') ?></h2>
                <p class="mt-2 break-words text-sm text-slate-600"><?= e($row['municipio'] ?: 'Municipio por definir') ?></p>
                <p class="mt-5 text-xs text-slate-500">Actualizado <?= e((new DateTimeImmutable($row['updated_at'], new DateTimeZone('UTC')))->setTimezone(new DateTimeZone('America/Bogota'))->format('d/m/Y H:i')) ?></p>
            </a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<nav aria-label="Paginación" class="mt-7 flex gap-3">
    <?php if ($page > 1): ?><a class="btn-secondary" href="<?= e(url('?page=' . ($page - 1))) ?>">Anterior</a><?php endif; ?>
    <?php if ($hasNext): ?><a class="btn-secondary" href="<?= e(url('?page=' . ($page + 1))) ?>">Siguiente</a><?php endif; ?>
</nav>
