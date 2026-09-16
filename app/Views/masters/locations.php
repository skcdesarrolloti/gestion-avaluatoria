<section id="maestros-geograficos" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="eyebrow">Ubicación del inmueble</p>
            <h2 class="mt-2 text-2xl font-semibold">Crear opciones de ubicación</h2>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                Estos datos solo sirven para escoger la ubicación del inmueble en Bien sujeto:
                al seleccionar barrio/microsector se podrán traer localidad, comuna/UCG y zona/sector.
            </p>
        </div>
        <span class="rounded-full bg-teal-50 px-3 py-1 text-sm font-semibold text-teal-800">
            Maestro de ubicación
        </span>
    </div>

    <div class="mt-6 grid gap-5 xl:grid-cols-3">
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
            action="<?= e(url('maestros/barrios')) ?>" x-data="{ busy: false }" @submit="busy = true">
            <?= csrf_field() ?>
            <h3 class="font-semibold">Barrio / microsector</h3>
            <label class="label mt-4">Ciudad / municipio
                <select class="input" name="city_id" required>
                    <option value="">Selecciona ciudad</option>
                    <?php foreach ($cities as $city): ?>
                        <option value="<?= e($city['id']) ?>"><?= e($city['department_name'] . ' · ' . $city['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label class="label mt-4">Localidad
                <input class="input" name="locality_name" maxlength="160"
                    placeholder="Ej. Histórica y del Caribe Norte">
            </label>
            <label class="label mt-4">Barrio / microsector
                <input class="input" name="name" maxlength="160" placeholder="Ej. Bruselas" required>
            </label>
            <label class="label mt-4">Comuna / UCG
                <input class="input" name="commune_ucg" maxlength="80" placeholder="Ej. UCG 9">
            </label>
            <label class="label mt-4">Zona / sector
                <input class="input" name="zone_sector" maxlength="120" placeholder="Ej. Residencial consolidada">
            </label>
            <label class="label mt-4">Observación
                <input class="input" name="notes" maxlength="240" placeholder="Opcional: referencia interna">
            </label>
            <button class="btn-primary mt-5 w-full" type="submit" :disabled="busy"
                x-text="busy ? 'Guardando...' : 'Guardar barrio/microsector'">Guardar barrio/microsector</button>
        </form>
    </div>

    <p class="mt-5 rounded-xl bg-slate-50 p-4 text-sm leading-6 text-slate-600">
        Luego, en Bien sujeto, el usuario escogerá el barrio/microsector y el sistema completará
        localidad, comuna/UCG y zona/sector con este maestro.
    </p>
</section>
