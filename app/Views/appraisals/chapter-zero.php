<?php
use App\Support\AppraisalCatalog;

$selected = static fn (string $name, string $value): string => (string) ($record[$name] ?? '') === $value ? 'selected' : '';
$field = static fn (string $name): string => (string) ($record[$name] ?? '');
$count = static fn (string $name): int => max(0, (int) ($record[$name] ?? 0));
$notes = $catalog['notes'] ?? [];
$initial = ['notes' => $notes];
$currentStep = 'expediente';
$configurationSelects = ['tipo_negocio', 'tipo_inmueble', 'subtipo_funcional', 'destinacion',
    'base_valor', 'aplica_niif', 'regimen_ph', 'estructura_metodo'];
?>
<a href="<?= e(url('valuaciones')) ?>" class="inline-flex min-h-11 items-center text-sm font-medium text-teal-800">← Valuaciones</a>
<div class="mt-3 flex flex-wrap items-start justify-between gap-5">
    <div>
        <p class="eyebrow">Numeral 1</p>
        <h1 class="mt-2 text-3xl font-semibold tracking-tight">Expediente valuatorio</h1>
        <p class="mt-3 max-w-3xl text-slate-600">
            Primero dejamos clara la configuración técnica y la identificación formal del encargo.
            Estos datos alimentan el cuerpo del informe y sus justificaciones.
        </p>
    </div>
    <span class="rounded-full bg-amber-50 px-3 py-1 text-sm font-semibold text-amber-800">Borrador</span>
</div>
<?php require BASE_PATH . '/app/Views/appraisals/step-nav.php'; ?>

<div class="mt-8 space-y-7">
    <form id="expediente-form" class="grid gap-7 lg:grid-cols-[1fr_18rem]" method="post"
        action="<?= e(url('avaluos/' . $record['id'] . '/expediente')) ?>"
        x-data="{
            busy: false, active: 'configuracion',
            notes: <?= e(json_encode($initial['notes'], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>,
            subtypeByProperty: <?= e(json_encode(AppraisalCatalog::subtypesByPropertyType(), JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>,
            selectedPropertyType: <?= e(json_encode($field('tipo_inmueble'), JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>,
            selectedSubtype: <?= e(json_encode($field('subtipo_funcional'), JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>,
            subtypeOptions() { return this.subtypeByProperty[this.selectedPropertyType] || {} },
            syncSubtype() { if (this.selectedSubtype && !this.subtypeOptions()[this.selectedSubtype]) this.selectedSubtype = '' },
            academy(field, value) { return value && this.notes[field] ? this.notes[field][value] : null }
        }" @submit="busy = true">
        <?= csrf_field() ?>
        <input type="hidden" name="version" value="<?= e($record['version']) ?>">
        <input type="hidden" name="igac_category" value="<?= e($field('igac_category')) ?>">
        <input type="hidden" name="igac_typology_hint" value="<?= e($field('igac_typology_hint')) ?>">
        <input type="hidden" name="igac_property_units_count" value="<?= e((string) $count('igac_property_units_count')) ?>">
        <input type="hidden" name="igac_annex_units_count" value="<?= e((string) $count('igac_annex_units_count')) ?>">
        <input type="hidden" name="direccion" value="<?= e($field('direccion')) ?>">
        <input type="hidden" name="municipio" value="<?= e($field('municipio')) ?>">

        <section class="scroll-mt-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="eyebrow">Expediente valuatorio</p>
                    <h2 class="mt-2 text-2xl font-semibold">Configuración e identificación del encargo</h2>
                </div>
                <div class="rounded-full bg-blue-50 px-3 py-1 text-sm font-semibold text-blue-800">1.1 / 1.2</div>
            </div>
            <nav class="mt-6 flex gap-2 overflow-x-auto rounded-xl bg-slate-100 p-2" aria-label="Subsecciones del expediente">
                <button type="button" class="min-h-11 shrink-0 rounded-lg px-4 py-2 text-sm font-semibold"
                    :class="active === 'configuracion' ? 'bg-blue-700 text-white shadow-sm' : 'bg-white text-blue-800'"
                    @click="active = 'configuracion'">1.1 Configuración</button>
                <button type="button" class="min-h-11 shrink-0 rounded-lg px-4 py-2 text-sm font-semibold"
                    :class="active === 'identificacion' ? 'bg-blue-700 text-white shadow-sm' : 'bg-white text-blue-800'"
                    @click="active = 'identificacion'">1.2 Identificación del encargo</button>
            </nav>

            <div class="mt-6 rounded-xl border border-blue-100 bg-blue-50 p-4 text-sm leading-6 text-blue-950"
                x-show="active === 'identificacion'">
                Esta sección deja explícito quién solicita el informe, para qué se usa, cuál es su alcance y
                cuáles salvedades deben mencionarse en el entregable conforme al marco normativo aplicable.
            </div>

            <div class="mt-6 grid gap-5 md:grid-cols-2" x-show="active === 'configuracion'">
                <label class="label">Perito responsable
                    <select class="input" name="appraiser_id">
                        <option value="">Selecciona perito</option>
                        <?php foreach ($appraisers as $appraiser): ?>
                            <option value="<?= e($appraiser['id']) ?>" <?= $selected('appraiser_id', $appraiser['id']) ?>>
                                <?= e($appraiser['code'] . ' · ' . $appraiser['full_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <?php foreach ($configurationSelects as $name) {
                    require BASE_PATH . '/app/Views/appraisals/chapter-zero-select-field.php';
                } ?>
                <label class="label md:col-span-2">Notas de inspección y configuración
                    <textarea class="input" name="inspection_notes" rows="4" maxlength="2000"
                        placeholder="Anota dudas de campo, componentes del bien o alertas técnicas."><?= e($field('inspection_notes')) ?></textarea>
                </label>
            </div>

            <div class="mt-6 grid gap-5 md:grid-cols-2" x-show="active === 'identificacion'">
                <label class="label md:col-span-2">Nombre del avalúo
                    <input class="input" name="titulo" maxlength="160" value="<?= e($field('titulo')) ?>"
                        placeholder="Ej. Avalúo comercial Lote Bruselas">
                </label>
                <label class="label">Cliente / contratante
                    <input class="input" name="client_name" maxlength="160" value="<?= e($field('client_name')) ?>"
                        placeholder="Persona o entidad que contrata el encargo">
                </label>
                <label class="label">Solicitante
                    <input class="input" name="requester_name" maxlength="160" value="<?= e($field('requester_name')) ?>"
                        placeholder="Quien pide o radica el avalúo">
                </label>
                <label class="label">Propietario del inmueble
                    <input class="input" name="property_owner_name" maxlength="160" value="<?= e($field('property_owner_name')) ?>"
                        placeholder="Nombre del propietario, si se conoce">
                </label>
                <label class="label">Destinatario del informe
                    <input class="input" name="report_recipient" maxlength="160" value="<?= e($field('report_recipient')) ?>"
                        placeholder="A quien va dirigido el entregable">
                </label>
                <?php $name = 'tipo'; require BASE_PATH . '/app/Views/appraisals/chapter-zero-select-field.php'; ?>
                <?php $name = 'tipo_derecho'; require BASE_PATH . '/app/Views/appraisals/chapter-zero-select-field.php'; ?>
                <?php $name = 'finalidad'; require BASE_PATH . '/app/Views/appraisals/chapter-zero-select-field.php'; ?>
                <label class="label md:col-span-2">Uso previsto del informe
                    <input class="input" name="intended_use" maxlength="220" value="<?= e($field('intended_use')) ?>"
                        placeholder="Ej. negociación, garantía, conciliación, decisión interna o proceso judicial">
                </label>
                <label class="label">Fecha de visita
                    <input class="input" type="date" name="visit_date" value="<?= e($field('visit_date')) ?>">
                </label>
                <label class="label">Fecha de valor
                    <input class="input" type="date" name="value_date" value="<?= e($field('value_date')) ?>">
                </label>
                <label class="label">Fecha del informe
                    <input class="input" type="date" name="report_date" value="<?= e($field('report_date')) ?>">
                </label>
                <label class="label md:col-span-2">Alcance del encargo
                    <textarea class="input" name="assignment_scope" rows="3" maxlength="2000"
                        placeholder="Define qué cubre el avalúo, fuentes consultadas y unidad de análisis."><?= e($field('assignment_scope')) ?></textarea>
                </label>
                <label class="label md:col-span-2">Limitaciones y salvedades
                    <textarea class="input" name="assignment_limitations" rows="3" maxlength="2000"
                        placeholder="Registra documentos faltantes, restricciones de acceso o información no verificada."><?= e($field('assignment_limitations')) ?></textarea>
                </label>
                <label class="label md:col-span-2">Hipótesis de trabajo
                    <textarea class="input" name="assignment_hypotheses" rows="3" maxlength="2000"
                        placeholder="Supuestos razonables usados para producir el informe, si aplican."><?= e($field('assignment_hypotheses')) ?></textarea>
                </label>
                <label class="label md:col-span-2">Observaciones generales
                    <textarea class="input" name="observaciones" rows="4" maxlength="4000"
                        placeholder="Notas generales del encargo que deban quedar disponibles para el informe."><?= e($field('observaciones')) ?></textarea>
                </label>
            </div>

        </section>

        <aside class="space-y-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="font-semibold">Guardar expediente</h2>
                <p class="mt-3 text-sm leading-6 text-slate-600">
                    Guarda 1.1 y 1.2 antes de continuar con Sector o Bien sujeto.
                </p>
                <button class="btn-primary mt-5 w-full" type="submit" :disabled="busy"
                    x-text="busy ? 'Guardando...' : 'Guardar expediente'">Guardar expediente</button>
            </div>
            <p class="px-2 text-xs leading-5 text-slate-500">
                El consecutivo técnico se asignará cuando el expediente quede formalmente configurado.
            </p>
            <button class="btn-secondary w-full" type="submit" name="next" value="sector">
                Guardar y continuar a Sector
            </button>
        </aside>
    </form>
</div>
