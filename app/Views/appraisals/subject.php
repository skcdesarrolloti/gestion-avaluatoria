<?php
$selected = static fn (string $name, string $value): string => (string) ($record[$name] ?? '') === $value ? 'selected' : '';
$field = static fn (string $name): string => (string) ($record[$name] ?? '');
$count = static fn (string $name): int => max(0, (int) ($record[$name] ?? 0));
$currentStep = 'sujeto';
$subjectActionBase = 'avaluos/' . $record['id'] . '/bien-sujeto';
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
    x-data="{ activeSubject: 'basic', syncSubject() { this.activeSubject = location.hash === '#superficies' ? 'surface' : (location.hash === '#construccion' ? 'construction' : (location.hash === '#atributos' ? 'attributes' : (location.hash.startsWith('#fotos') ? 'photos' : 'basic'))) } }"
    x-init="syncSubject()" @hashchange.window="syncSubject()">
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
                <span class="block text-base">3.4 Atributos y deméritos</span>
                <span class="block text-xs font-medium opacity-80">Diferenciales del sujeto por unidad</span>
            </button>
            <button class="min-h-12 shrink-0 rounded-lg px-5 py-3 text-left font-semibold" type="button"
                @click="activeSubject = 'photos'; history.replaceState(null, '', '#fotos')"
                :class="activeSubject === 'photos' ? 'bg-blue-700 text-white shadow-sm' : 'bg-white text-blue-800 hover:border-blue-700'">
                <span class="block text-base">3.6 Registro fotográfico</span>
                <span class="block text-xs font-medium opacity-80">Fotos para el entregable</span>
            </button>
        </div>
    </div>
    <div class="mt-7" x-show="activeSubject === 'basic'">
        <?php require BASE_PATH . '/app/Views/appraisals/subject-basic.php'; ?>
    </div>
    <div class="mt-7" x-show="activeSubject === 'surface'">
        <?php require BASE_PATH . '/app/Views/appraisals/subject-surface.php'; ?>
    </div>
    <div class="mt-7" x-show="activeSubject === 'construction'">
        <?php require BASE_PATH . '/app/Views/appraisals/subject-construction.php'; ?>
    </div>
    <div class="mt-7" x-show="activeSubject === 'attributes'">
        <?php require BASE_PATH . '/app/Views/appraisals/subject-attributes.php'; ?>
    </div>
    <div class="mt-7" x-show="activeSubject === 'photos'">
        <?php require BASE_PATH . '/app/Views/appraisals/subject-photos.php'; ?>
    </div>
</div>
