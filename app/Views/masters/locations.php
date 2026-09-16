<section id="maestros-geograficos" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="eyebrow">Cobertura nacional</p>
            <h2 class="mt-2 text-2xl font-semibold">Maestros geográficos</h2>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                Crea departamentos, ciudades o municipios, localidades, y finalmente barrios, veredas o sectores.
                Esta estructura alimentará Bien sujeto sin volver a digitar ubicaciones.
            </p>
        </div>
        <span class="rounded-full bg-teal-50 px-3 py-1 text-sm font-semibold text-teal-800">
            <?= e(count($departments)) ?> depto(s) · <?= e(count($cities)) ?> ciudad(es) · <?= e(count($localities)) ?> localidad(es)
        </span>
    </div>

    <div class="mt-6 grid gap-5 xl:grid-cols-4">
        <form class="rounded-xl border border-slate-200 p-5" method="post"
            action="<?= e(url('maestros/departamentos')) ?>" x-data="{ busy: false }" @submit="busy = true">
            <?= csrf_field() ?>
            <h3 class="font-semibold">Departamento</h3>
            <label class="label mt-4">Código
                <input class="input" name="code" maxlength="20" placeholder="Ej. 05">
            </label>
            <label class="label mt-4">Nombre
                <input class="input" name="name" maxlength="120" placeholder="Ej. Antioquia" required>
            </label>
            <button class="btn-primary mt-5 w-full" type="submit" :disabled="busy"
                x-text="busy ? 'Guardando...' : 'Guardar departamento'">Guardar departamento</button>
        </form>

        <form class="rounded-xl border border-slate-200 p-5" method="post"
            action="<?= e(url('maestros/ciudades')) ?>" x-data="{ busy: false }" @submit="busy = true">
            <?= csrf_field() ?>
            <h3 class="font-semibold">Ciudad / municipio</h3>
            <label class="label mt-4">Departamento
                <select class="input" name="department_id" required>
                    <option value="">Selecciona departamento</option>
                    <?php foreach ($departments as $department): ?>
                        <option value="<?= e($department['id']) ?>"><?= e($department['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label class="label mt-4">Código
                <input class="input" name="code" maxlength="20" placeholder="Ej. 05001">
            </label>
            <label class="label mt-4">Nombre
                <input class="input" name="name" maxlength="140" placeholder="Ej. Medellín" required>
            </label>
            <button class="btn-primary mt-5 w-full" type="submit" :disabled="busy"
                x-text="busy ? 'Guardando...' : 'Guardar ciudad'">Guardar ciudad</button>
        </form>

        <form class="rounded-xl border border-slate-200 p-5" method="post"
            action="<?= e(url('maestros/localidades')) ?>" x-data="{ busy: false }" @submit="busy = true">
            <?= csrf_field() ?>
            <h3 class="font-semibold">Localidad</h3>
            <label class="label mt-4">Ciudad / municipio
                <select class="input" name="city_id" required>
                    <option value="">Selecciona ciudad</option>
                    <?php foreach ($cities as $city): ?>
                        <option value="<?= e($city['id']) ?>"><?= e($city['department_name'] . ' · ' . $city['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label class="label mt-4">Nombre
                <input class="input" name="name" maxlength="160" placeholder="Ej. Histórica y del Caribe Norte" required>
            </label>
            <label class="label mt-4">Observación
                <input class="input" name="notes" maxlength="240" placeholder="Opcional: alcance o referencia">
            </label>
            <button class="btn-primary mt-5 w-full" type="submit" :disabled="busy"
                x-text="busy ? 'Guardando...' : 'Guardar localidad'">Guardar localidad</button>
        </form>

        <form class="rounded-xl border border-slate-200 p-5" method="post"
            action="<?= e(url('maestros/barrios')) ?>" x-data="{ busy: false }" @submit="busy = true">
            <?= csrf_field() ?>
            <h3 class="font-semibold">Barrio / sector</h3>
            <label class="label mt-4">Ciudad / municipio
                <select class="input" name="city_id" required>
                    <option value="">Selecciona ciudad</option>
                    <?php foreach ($cities as $city): ?>
                        <option value="<?= e($city['id']) ?>"><?= e($city['department_name'] . ' · ' . $city['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label class="label mt-4">Localidad
                <select class="input" name="locality_id">
                    <option value="">Sin localidad / por definir</option>
                    <?php foreach ($localities as $locality): ?>
                        <option value="<?= e($locality['id']) ?>"><?= e($locality['city_name'] . ' · ' . $locality['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label class="label mt-4">Nombre
                <input class="input" name="name" maxlength="160" placeholder="Ej. El Poblado, vereda o sector" required>
            </label>
            <label class="label mt-4">Observación
                <input class="input" name="notes" maxlength="240" placeholder="Opcional: comuna, zona o referencia">
            </label>
            <button class="btn-primary mt-5 w-full" type="submit" :disabled="busy"
                x-text="busy ? 'Guardando...' : 'Guardar barrio/sector'">Guardar barrio/sector</button>
        </form>
    </div>

    <div class="mt-6 grid gap-4 lg:grid-cols-4">
        <?php foreach ([['Departamentos', $departments], ['Ciudades', $cities],
            ['Localidades', $localities], ['Barrios / sectores', $neighborhoods]] as [$title, $rows]): ?>
            <div class="rounded-xl bg-slate-50 p-4">
                <h3 class="font-semibold"><?= e($title) ?></h3>
                <?php if (!$rows): ?>
                    <p class="mt-3 text-sm text-slate-600">Sin registros aún.</p>
                <?php else: ?>
                    <ul class="mt-3 space-y-1 text-sm text-slate-700">
                        <?php foreach (array_slice($rows, 0, 8) as $row): ?>
                            <li><?= e(($row['department_name'] ?? '') ? ($row['department_name'] . ' · ') : '') ?><?= e(($row['city_name'] ?? '') ? ($row['city_name'] . ' · ') : '') ?><?= e(($row['locality_name'] ?? '') ? ($row['locality_name'] . ' · ') : '') ?><?= e($row['name']) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</section>
