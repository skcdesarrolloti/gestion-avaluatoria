<div class="mt-5 space-y-5" x-show="activeConstructionDetail === 'vetustez'">
    <div class="rounded-xl bg-sky-50 p-4 text-sm leading-6 text-sky-950">
        Si se conoce el año, usa vetustez real. Si no se conoce, registra edad aparente según visita.
        La vida útil puede orientarse con la tipología IGAC, pero el analista adopta el dato final.
    </div>
    <div class="grid gap-5 md:grid-cols-3">
        <label class="label">Año de construcción
            <input class="input" name="unit_constructions[<?= e($unitId) ?>][construction_year]"
                inputmode="numeric" x-model="yearBuilt" value="<?= e($cv($unit, 'construction_year')) ?>" placeholder="Ej. 2010">
        </label>
        <label class="label">Vetustez adoptada (años)
            <input class="input" name="unit_constructions[<?= e($unitId) ?>][construction_age_years]"
                inputmode="numeric" x-model="age" :placeholder="calcAge() || 'Año referencia - año construcción'">
        </label>
        <label class="label">Edad aparente (años)
            <input class="input" name="unit_constructions[<?= e($unitId) ?>][construction_apparent_age_years]"
                inputmode="numeric" x-model="apparentAge" placeholder="Cuando no se conoce la edad real">
        </label>
        <label class="label">Vida útil total (años)
            <input class="input" name="unit_constructions[<?= e($unitId) ?>][construction_useful_life_years]"
                inputmode="numeric" x-model="usefulLife" placeholder="<?= e($igacUsefulLife ?: 'Según tipología o criterio') ?>">
        </label>
        <label class="label">Vida útil remanente (años)
            <input class="input" name="unit_constructions[<?= e($unitId) ?>][construction_remaining_life_years]"
                inputmode="numeric" value="<?= e($cv($unit, 'construction_remaining_life_years')) ?>" :placeholder="calcRemaining() || 'Vida útil - edad'">
        </label>
        <label class="label">Unidades rentables
            <input class="input" name="unit_constructions[<?= e($unitId) ?>][construction_rentable_units]"
                inputmode="numeric" value="<?= e($cv($unit, 'construction_rentable_units')) ?>" placeholder="Ej. 1">
        </label>
        <p class="rounded-xl bg-slate-50 p-4 text-sm leading-6 text-slate-600">
            Fórmulas guía: <strong>Vetustez = año de referencia - año de construcción</strong>.
            <strong>Vida remanente = vida útil - edad adoptada</strong>.
        </p>
    </div>
    <label class="label block">Aspectos generales
        <textarea class="input" name="unit_constructions[<?= e($unitId) ?>][construction_general_aspects]" rows="4" maxlength="1000"
            placeholder="Describe materiales, distribución, niveles, calidad constructiva y observaciones de visita."><?= e($cv($unit, 'construction_general_aspects')) ?></textarea>
    </label>
</div>
