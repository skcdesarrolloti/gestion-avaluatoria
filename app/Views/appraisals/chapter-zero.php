<?php
use App\Support\AppraisalCatalog;

$selected = static fn (string $name, string $value): string => (string) ($record[$name] ?? '') === $value ? 'selected' : '';
$field = static fn (string $name): string => (string) ($record[$name] ?? '');
$count = static fn (string $name): int => max(0, (int) ($record[$name] ?? 0));
$defaultAppraiserId = $field('appraiser_id') !== '' ? $field('appraiser_id') : (count($appraisers) === 1 ? (string) $appraisers[0]['id'] : '');
$selectedAppraiser = static fn (string $value): string => $defaultAppraiserId === $value ? 'selected' : '';
$autoAssignAppraiser = $field('appraiser_id') === '' && $defaultAppraiserId !== '';
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

<?php if (!empty($chapterZeroMessage)): ?>
    <p class="mt-6 rounded-xl bg-emerald-50 p-4 text-sm font-semibold text-emerald-800"><?= e($chapterZeroMessage) ?></p>
<?php endif; ?>
<?php if (!empty($chapterZeroError)): ?>
    <p class="mt-6 rounded-xl bg-red-50 p-4 text-sm font-semibold text-red-800"><?= e($chapterZeroError) ?></p>
<?php endif; ?>

<div class="mt-8 space-y-7">
    <form id="expediente-form" class="grid gap-7 lg:grid-cols-[1fr_18rem]" method="post"
        action="<?= e(url('avaluos/' . $record['id'] . '/expediente')) ?>"
        data-module-autosave
        <?= $autoAssignAppraiser ? 'data-autosave-on-load' : '' ?>
        data-autosave-endpoint="<?= e(url('avaluos/' . $record['id'] . '/expediente/autoguardar')) ?>"
        x-data="{
            busy: false, active: window.location.hash === '#identificacion' ? 'identificacion' : 'configuracion',
            notes: <?= e(json_encode($initial['notes'], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>,
            subtypeByProperty: <?= e(json_encode(AppraisalCatalog::subtypesByPropertyType(), JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>,
            selectedPropertyType: <?= e(json_encode($field('tipo_inmueble'), JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>,
            selectedSubtype: <?= e(json_encode($field('subtipo_funcional'), JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)) ?>,
            propertyUnits: <?= e((string) $count('igac_property_units_count')) ?>,
            annexUnits: <?= e((string) $count('igac_annex_units_count')) ?>,
            subtypeOptions() { return this.subtypeByProperty[this.selectedPropertyType] || {} },
            syncSubtype() { if (this.selectedSubtype && !this.subtypeOptions()[this.selectedSubtype]) this.selectedSubtype = '' },
            academy(field, value) { return value && this.notes[field] ? this.notes[field][value] : null },
            setActive(section) { this.active = section; history.replaceState(null, '', '#' + section) }
        }" @submit="busy = true">
        <?= csrf_field() ?>
        <input type="hidden" name="version" value="<?= e($record['version']) ?>">
        <input type="hidden" name="igac_category" value="<?= e($field('igac_category')) ?>">
        <input type="hidden" name="igac_typology_hint" value="<?= e($field('igac_typology_hint')) ?>">
        <input type="hidden" name="direccion" value="<?= e($field('direccion')) ?>">
        <input type="hidden" name="municipio" value="<?= e($field('municipio')) ?>">
        <input type="hidden" name="active_section" :value="active">

        <section class="scroll-mt-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="eyebrow">Expediente valuatorio</p>
                    <h2 class="mt-2 text-2xl font-semibold">Configuración e identificación del encargo</h2>
                </div>
                <div class="rounded-full bg-blue-50 px-3 py-1 text-sm font-semibold text-blue-800">1.1 / 1.2</div>
            </div>
            <?php require BASE_PATH . '/app/Views/appraisals/chapter-one-academy.php'; ?>
            <nav class="mt-6 flex gap-2 overflow-x-auto rounded-xl bg-slate-100 p-2" aria-label="Subsecciones del expediente">
                <button type="button" class="min-h-11 shrink-0 rounded-lg px-4 py-2 text-sm font-semibold"
                    :class="active === 'configuracion' ? 'bg-blue-700 text-white shadow-sm' : 'bg-white text-blue-800'"
                    @click="setActive('configuracion')">1.1 Configuración</button>
                <button type="button" class="min-h-11 shrink-0 rounded-lg px-4 py-2 text-sm font-semibold"
                    :class="active === 'identificacion' ? 'bg-blue-700 text-white shadow-sm' : 'bg-white text-blue-800'"
                    @click="setActive('identificacion')">1.2 Identificación del encargo</button>
            </nav>

            <div class="mt-6 rounded-xl border border-blue-100 bg-blue-50 p-4 text-sm leading-6 text-blue-950"
                x-show="active === 'identificacion'">
                Esta sección deja explícito quién solicita el informe, para qué se usa, cuál es su alcance y
                cuáles salvedades deben mencionarse en el entregable conforme al marco normativo aplicable.
            </div>

            <div class="mt-6 grid gap-5 md:grid-cols-2" x-show="active === 'configuracion'">
                <label class="label">Perito responsable
                    <select class="input" name="appraiser_id" data-autosave-now>
                        <option value="">Selecciona perito</option>
                        <?php foreach ($appraisers as $appraiser): ?>
                            <option value="<?= e($appraiser['id']) ?>" <?= $selectedAppraiser($appraiser['id']) ?>>
                                <?= e($appraiser['code'] . ' · ' . $appraiser['full_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (count($appraisers) === 1 && $field('appraiser_id') === ''): ?>
                        <span class="mt-1 block text-xs leading-5 text-emerald-700">
                            Se selecciona automáticamente porque solo hay un perito vigente.
                        </span>
                    <?php elseif (count($appraisers) === 0): ?>
                        <span class="mt-1 block text-xs leading-5 text-red-700">
                            No hay peritos con RAA vigente. Actualiza el RAA en Maestros para asignar expediente.
                        </span>
                    <?php endif; ?>
                </label>
                <?php require BASE_PATH . '/app/Views/appraisals/appraisal-dossier-field.php'; ?>
                <?php foreach ($configurationSelects as $name) {
                    require BASE_PATH . '/app/Views/appraisals/chapter-zero-select-field.php';
                    if ($name === 'subtipo_funcional'): ?>
                        <label class="label">Unidades inmobiliarias principales
                            <input class="input" type="number" name="igac_property_units_count" min="0" max="50"
                                x-model.number="propertyUnits" placeholder="Ej. 3">
                            <span class="mt-1 block text-xs leading-5 text-slate-500">
                                Casa + 2 apartamentos = 3. Cada unidad se nombrará y clasificará en 3.1.
                            </span>
                        </label>
                        <label class="label">Anexos existentes
                            <input class="input" type="number" name="igac_annex_units_count" min="0" max="50"
                                x-model.number="annexUnits" placeholder="Ej. 1">
                            <span class="mt-1 block text-xs leading-5 text-slate-500">
                                Parqueaderos, depósitos, kioscos, piscinas, ramadas u otros anexos.
                            </span>
                        </label>
                        <div class="rounded-xl border border-blue-100 bg-blue-50 p-4 text-sm leading-6 text-blue-950 md:col-span-2">
                            Se prepararán <strong x-text="propertyUnits || 0"></strong> unidad(es) principal(es)
                            y <strong x-text="annexUnits || 0"></strong> anexo(s). En 3.1 nombras cada una,
                            eliges su tipo de inmueble y desde ahí se alimentan superficies, construcción,
                            diferenciales, fotos y PH cuando aplique.
                        </div>
                    <?php endif;
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
                <label class="label">Identificación del solicitante
                    <input class="input" name="requester_identification" maxlength="80" value="<?= e($field('requester_identification')) ?>"
                        placeholder="NIT, cédula o identificación reportada">
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
                <label class="label md:col-span-2">Documentos aportados o insumos
                    <textarea class="input" name="source_documents" rows="4" maxlength="3000"
                        placeholder="Ej. Escritura pública, certificado de tradición, predial, RUT, reglamento PH, fotografías o soportes del encargo."><?= e($field('source_documents')) ?></textarea>
                </label>
                <label class="label md:col-span-2">Texto adicional para memoria descriptiva
                    <textarea class="input" name="assignment_report_text" rows="4" maxlength="5000"
                        placeholder="Ajustes narrativos del capítulo 1 que no estén cubiertos por los campos anteriores."><?= e($field('assignment_report_text')) ?></textarea>
                </label>
                <label class="label md:col-span-2">Observaciones generales
                    <textarea class="input" name="observaciones" rows="4" maxlength="4000"
                        placeholder="Notas generales del encargo que deban quedar disponibles para el informe."><?= e($field('observaciones')) ?></textarea>
                </label>
            </div>

            <?php require BASE_PATH . '/app/Views/appraisals/chapter-zero-actions.php'; ?>

        </section>

        <?php require BASE_PATH . '/app/Views/appraisals/chapter-zero-aside.php'; ?>
    </form>
</div>
