<?php
$selected = static fn (string $name, string $value): string => (string) ($record[$name] ?? '') === $value ? 'selected' : '';
$field = static fn (string $name): string => (string) ($record[$name] ?? '');
$count = static fn (string $name): int => max(0, (int) ($record[$name] ?? 0));
$currentStep = 'sujeto';
$subjectActionBase = 'avaluos/' . $record['id'] . '/bien-sujeto';
$safeSubjectPartial = static function (string $path, string $label, array $context): void {
    try {
        extract($context, EXTR_SKIP);
        require BASE_PATH . '/app/Views/appraisals/' . $path;
    } catch (Throwable $error) {
        $ref = bin2hex(random_bytes(6));
        error_log('Gestion avaluatoria sujeto parcial [' . $ref . '] ' . $label . ' '
            . get_class($error) . ' code=' . $error->getCode() . ' at ' . basename($error->getFile()) . ':' . $error->getLine());
        echo '<section class="rounded-2xl border border-amber-200 bg-amber-50 p-5 text-sm text-amber-900">';
        echo '<p class="font-semibold">Este bloque no se pudo cargar: ' . e($label) . '.</p>';
        echo '<p class="mt-2">El resto del capítulo 3 queda disponible. Referencia: ' . e($ref) . '.</p>';
        echo '</section>';
    }
};
?>
<a href="<?= e(url('valuaciones')) ?>" class="inline-flex min-h-11 items-center text-sm font-medium text-teal-800">← Valuaciones</a>
<div class="mt-3 flex flex-wrap items-start justify-between gap-5">
    <div>
        <p class="eyebrow">Numeral 3 · Bien sujeto</p>
        <h1 class="mt-2 text-3xl font-semibold tracking-tight">Características y tipologías del inmueble</h1>
        <p class="mt-3 max-w-3xl text-slate-600">
            Aquí se documentan las unidades y anexos del predio, sus tipologías IGAC, fotos y
            descripciones técnicas para alimentar la caracterización del avalúo.
        </p>
    </div>
    <span class="rounded-full bg-emerald-50 px-3 py-1 text-sm font-semibold text-emerald-700">Sujeto del avalúo</span>
</div>
<?php require BASE_PATH . '/app/Views/appraisals/step-nav.php'; ?>

<div class="mt-7"
    x-data="{ activeSubject: 'basic', syncSubject() { this.activeSubject = location.hash === '#superficies' ? 'surface' : (location.hash === '#construccion' ? 'construction' : (location.hash === '#atributos' ? 'attributes' : (location.hash === '#ph' ? 'ph' : (location.hash === '#obsolescencias' ? 'obsolescence' : (location.hash.startsWith('#fotos') ? 'photos' : 'basic'))))) } }"
    x-init="syncSubject()" @hashchange.window="syncSubject()">
    <div class="mb-3 flex flex-wrap justify-end">
        <button class="btn-secondary" type="button" aria-disabled="true"
            title="Disponible cuando se cierre la revisión técnica del bien sujeto.">
            Imprimir documento de Inspección Bien Sujeto
        </button>
    </div>
    <div class="rounded-2xl border border-slate-200 bg-white p-3 shadow-sm">
        <div class="flex gap-2 overflow-x-auto rounded-xl bg-slate-100 p-2" role="tablist">
            <button class="min-h-12 shrink-0 rounded-lg px-5 py-3 text-left font-semibold" type="button"
                @click="activeSubject = 'basic'; history.replaceState(null, '', '#ficha-basica')"
                :class="activeSubject === 'basic' ? 'bg-blue-700 text-white shadow-sm' : 'bg-white text-blue-800 hover:border-blue-700'">
                <span class="block text-base">3.1 Ficha básica del sujeto</span>
                <span class="block text-xs font-medium opacity-80">Identificación y características</span>
            </button>
            <button class="min-h-12 shrink-0 rounded-lg px-5 py-3 text-left font-semibold" type="button"
                @click="activeSubject = 'surface'; history.replaceState(null, '', '#superficies')"
                :class="activeSubject === 'surface' ? 'bg-blue-700 text-white shadow-sm' : 'bg-white text-blue-800 hover:border-blue-700'">
                <span class="block text-base">3.2 Datos de la superficie</span>
                <span class="block text-xs font-medium opacity-80">Áreas, fondo y variables del terreno</span>
            </button>
            <button class="min-h-12 shrink-0 rounded-lg px-5 py-3 text-left font-semibold" type="button"
                @click="activeSubject = 'construction'; history.replaceState(null, '', '#construccion')"
                :class="activeSubject === 'construction' ? 'bg-blue-700 text-white shadow-sm' : 'bg-white text-blue-800 hover:border-blue-700'">
                <span class="block text-base">3.3 Datos de la construcción</span>
                <span class="block text-xs font-medium opacity-80">Áreas, vetustez, estado y conservación</span>
            </button>
            <button class="min-h-12 shrink-0 rounded-lg px-5 py-3 text-left font-semibold" type="button"
                @click="activeSubject = 'attributes'; history.replaceState(null, '', '#atributos')"
                :class="activeSubject === 'attributes' ? 'bg-blue-700 text-white shadow-sm' : 'bg-white text-blue-800 hover:border-blue-700'">
                <span class="block text-base">3.4 Diferenciales valuatorios</span>
                <span class="block text-xs font-medium opacity-80">Atributos y deméritos por unidad</span>
            </button>
            <button class="min-h-12 shrink-0 rounded-lg px-5 py-3 text-left font-semibold" type="button"
                @click="activeSubject = 'ph'; history.replaceState(null, '', '#ph')"
                :class="activeSubject === 'ph' ? 'bg-blue-700 text-white shadow-sm' : 'bg-white text-blue-800 hover:border-blue-700'">
                <span class="block text-base">3.5 Propiedad horizontal</span>
                <span class="block text-xs font-medium opacity-80">Copropiedad y zonas comunes</span>
            </button>
            <button class="min-h-12 shrink-0 rounded-lg px-5 py-3 text-left font-semibold" type="button"
                @click="activeSubject = 'obsolescence'; history.replaceState(null, '', '#obsolescencias')"
                :class="activeSubject === 'obsolescence' ? 'bg-blue-700 text-white shadow-sm' : 'bg-white text-blue-800 hover:border-blue-700'">
                <span class="block text-base">3.6 Obsolescencias</span>
                <span class="block text-xs font-medium opacity-80">Diagnóstico IEO</span>
            </button>
            <button class="min-h-12 shrink-0 rounded-lg px-5 py-3 text-left font-semibold" type="button"
                @click="activeSubject = 'photos'; history.replaceState(null, '', '#fotos')"
                :class="activeSubject === 'photos' ? 'bg-blue-700 text-white shadow-sm' : 'bg-white text-blue-800 hover:border-blue-700'">
                <span class="block text-base">3.7 Registro fotográfico</span>
                <span class="block text-xs font-medium opacity-80">Fotos para el entregable</span>
            </button>
        </div>
    </div>
    <div class="mt-7" x-show="activeSubject === 'basic'">
        <?php $safeSubjectPartial('subject-basic.php', '3.1 Ficha básica', get_defined_vars()); ?>
    </div>
    <div class="mt-7" x-show="activeSubject === 'surface'">
        <?php $safeSubjectPartial('subject-surface.php', '3.2 Superficies', get_defined_vars()); ?>
    </div>
    <div class="mt-7" x-show="activeSubject === 'construction'">
        <?php $safeSubjectPartial('subject-construction.php', '3.3 Construcción', get_defined_vars()); ?>
    </div>
    <div class="mt-7" x-show="activeSubject === 'attributes'">
        <?php $safeSubjectPartial('subject-attributes.php', '3.4 Diferenciales valuatorios', get_defined_vars()); ?>
    </div>
    <div class="mt-7" x-show="activeSubject === 'ph'">
        <?php $safeSubjectPartial('subject-ph.php', '3.5 Propiedad horizontal', get_defined_vars()); ?>
    </div>
    <div class="mt-7" x-show="activeSubject === 'obsolescence'">
        <?php $safeSubjectPartial('subject-obsolescence.php', '3.6 Obsolescencias', get_defined_vars()); ?>
    </div>
    <div class="mt-7" x-show="activeSubject === 'photos'">
        <?php $safeSubjectPartial('subject-photos.php', '3.7 Registro fotográfico', get_defined_vars()); ?>
    </div>
</div>
<?php $safeSubjectPartial('report-extra-notes.php', 'Ampliaciones del entregable', get_defined_vars()); ?>
