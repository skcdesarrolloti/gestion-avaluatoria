<?php if ($componentKey === ''): ?>
<p class="rounded-xl border bg-white p-5">Selecciona un componente desde «Componentes y métodos». El banco sin asignar conserva las muestras anteriores sin adjudicarlas automáticamente.</p>
<?php else: ?>
<form x-data="{ chosenMethod: <?= e(json_encode(($selected['method'] ?? '') ?: $method)) ?> }" method="post" action="<?= e(url($basePath . '/flujo')) ?>" data-module-autosave data-save-in-place
    data-autosave-endpoint="<?= e(url($basePath . '/flujo')) ?>" class="rounded-2xl border bg-white p-5 sm:p-8">
    <?= csrf_field() ?>
    <input type="hidden" name="component" value="<?= e($componentKey) ?>">
    <input type="hidden" name="version" value="<?= (int) ($record['methodology_version'] ?? 0) ?>">
    <h2 class="text-2xl font-semibold"><?= e($prefix) ?>2 Selección del método · <?= e($componentLabel) ?></h2>
    <p class="mt-3 text-sm text-slate-600">La selección es del perito. Un terreno no se asigna automáticamente a Residual; la evidencia y la finalidad determinan el método.</p>
    <div class="mt-5 grid gap-5 sm:grid-cols-2">
        <label class="block font-semibold">Método del componente
            <select name="method" class="input" @change="chosenMethod = $event.target.value || 'mercado'"><option value="">Selecciona el método</option>
            <?php foreach ($methods as $key => $label): ?><option value="<?= e($key) ?>" <?= ($selected['method'] ?? '') === $key ? 'selected' : '' ?>><?= e($label) ?></option><?php endforeach; ?>
            </select>
        </label>
        <label class="block font-semibold">Tratamiento en la integración
            <select name="treatment" class="input"><option value="">Selecciona el tratamiento</option>
            <?php foreach (['separado' => 'Valor separado', 'integrado' => 'Incluido en otro componente', 'descriptivo' => 'Solo descriptivo'] as $key => $label): ?>
            <option value="<?= e($key) ?>" <?= ($selected['treatment'] ?? '') === $key ? 'selected' : '' ?>><?= e($label) ?></option><?php endforeach; ?>
            </select>
        </label>
    </div>
    <label class="mt-5 block font-semibold">Justificación de la selección
        <textarea name="reason" maxlength="2000" class="input" rows="4" placeholder="Explica la evidencia disponible y por qué este método corresponde al componente."><?= e($selected['reason'] ?? '') ?></textarea>
    </label>
    <label class="mt-5 block font-semibold">Alcance y control de doble conteo
        <textarea name="coverage" maxlength="2000" class="input" rows="4" placeholder="Indica qué incluye y excluye el valor; si está integrado, identifica el componente que lo contiene."><?= e($selected['coverage'] ?? '') ?></textarea>
    </label>
    <p class="mt-3 text-sm" data-autosave-status>Autoguardado activo · máximo 2000 caracteres por explicación.</p>
    <button type="submit" class="btn-primary mt-4">Guardar ahora</button>
    <a class="btn-secondary mt-4" href="<?= e($flowUrl('3')) ?>" :href="<?= e(json_encode(url($basePath) . '?stage=3&component=' . rawurlencode($componentKey) . '&method=')) ?> + chosenMethod">Continuar a insumos del método elegido</a>
</form>
<?php endif; ?>
