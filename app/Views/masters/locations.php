<section id="maestros-geograficos" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="eyebrow">Ubicación del inmueble</p>
            <h2 class="mt-2 text-2xl font-semibold">Crear opciones de ubicación</h2>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                Estos datos solo sirven para escoger la ubicación del inmueble en Bien sujeto:
                departamento, ciudad o municipio, localidad y barrio, vereda o sector.
            </p>
        </div>
        <span class="rounded-full bg-teal-50 px-3 py-1 text-sm font-semibold text-teal-800">
            Maestro de ubicación
        </span>
    </div>

    <div class="mt-6 grid gap-5 xl:grid-cols-4">
        <form class="rounded-xl border border-slate-200 p-5" method="post"
            action="<?= e(url('maestros/departamentos')) ?>" x-data="{ busy: false }" @submit="busy = true">
            <?= csrf_field() ?>
            <h3 class="font-semibold">Departamento</h3>
            <label class="label mt-4">Código DANE opcional
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
            <label class="label mt-4">Código DANE opcional
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

    <p class="mt-5 rounded-xl bg-slate-50 p-4 text-sm leading-6 text-slate-600">
        Jerarquía usada por el módulo: Departamento → Ciudad/Municipio → Localidad → Barrio/Sector.
    </p>
</section>
