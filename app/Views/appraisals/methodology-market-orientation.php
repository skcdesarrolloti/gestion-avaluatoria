<?php
$orientationPh = (string) ($record['regimen_ph'] ?? '');
$orientationTabs = [1 => '1. Revisión', 2 => $orientationPh === 'si' ? '2. PH: dos decisiones distintas que no deben confundirse' : '2. Tratamiento de esta unidad', 3 => '3. Otras reglas de Mercado', 4 => '4. Antes de continuar'];
?>
<section class="mt-4 rounded-xl border border-teal-200 bg-teal-50 p-4" x-data="{ orientationTab: 1 }">
    <h4 class="font-semibold text-teal-950">Revisar <?= e($component['label']) ?> · orientación para Mercado</h4>
    <nav class="mt-3 flex flex-wrap gap-2" aria-label="Orientación de <?= e($component['label']) ?>">
        <?php foreach ($orientationTabs as $tabNumber => $tabTitle): ?>
            <button type="button" class="btn-secondary min-h-11 text-left" :aria-pressed="orientationTab === <?= $tabNumber ?>"
                :class="orientationTab === <?= $tabNumber ?> ? 'bg-white text-orange-600' : ''" @click="orientationTab = <?= $tabNumber ?>"><?= e($tabTitle) ?></button>
        <?php endforeach; ?>
    </nav>
    <div x-show="orientationTab === 1">
    <p class="mt-3 text-sm leading-6"><strong>Primero comprende esta unidad.</strong> Esta lectura no selecciona un método ni modifica el expediente. Los datos de la unidad y sus anexos proceden de los capítulos 1 y 3.</p>
    <p class="mt-3 rounded-lg bg-white p-3 text-sm"><strong>Régimen registrado en el expediente:</strong> <?= e(['si' => 'PH', 'no' => 'No PH', 'no_aplica' => 'No aplica'][$orientationPh] ?? 'Por confirmar') ?>. Confirma que corresponde a esta unidad y revisa sus documentos; el nombre «anexo» no determina su naturaleza jurídica.</p>
    <h4 class="mt-4 font-semibold">1. Qué verificar antes de escoger Mercado</h4>
    <ul class="mt-2 list-disc space-y-2 pl-5 text-sm leading-6">
        <li>Identifica qué se valora: unidad principal, terreno, construcción o anexo; qué incluye el precio y qué áreas y derechos están acreditados.</li>
        <li>Comprueba que existan ofertas o transacciones recientes, similares o comparables, con fuente verificable. La cercanía por sí sola no acredita comparabilidad (arts. 16 y 19).</li>
        <li>Conserva precio publicado, áreas, ubicación, fuente, contacto, fecha y soporte. Si falta información, registra el pendiente y corrobóralo; no inventes datos (art. 17).</li>
    </ul>
    </div>
    <div x-show="orientationTab === 2" x-cloak>
    <?php require __DIR__ . '/methodology-unit-reading.php'; ?>
    <?php if ($orientationPh === 'si'): ?>
    <h4 class="mt-4 font-semibold">2. PH: dos decisiones distintas que no deben confundirse</h4>
    <p class="mt-2 text-sm leading-6"><?= $orientationPh === 'si' ? 'El expediente está marcado como PH: revisa estas reglas antes de decidir el tratamiento.' : 'Consulta estas reglas si esta unidad o las muestras están sometidas a PH; confirma primero el régimen.' ?></p>
    <div class="mt-3 space-y-3 text-sm leading-6">
        <section class="rounded-lg border bg-white p-3">
            <h5 class="font-semibold">El inmueble que estás avaluando · artículo 36</h5>
            <p class="mt-2">Valora las áreas privadas legalmente constituidas, considerando los derechos derivados de los coeficientes de copropiedad. El valor se expresa integralmente por m².</p>
            <ul class="mt-2 list-disc space-y-2 pl-5">
                <li><strong>Matrícula independiente:</strong> parqueaderos, garajes, depósitos o áreas libres se valoran globalmente o por m² conforme al mercado; verifica servidumbres y restricciones.</li>
                <li><strong>Bien común de uso exclusivo:</strong> su valor queda implícito en la unidad principal; no lo liquides independientemente ni lo sumes otra vez.</li>
                <li><strong>Área privada construida y libre:</strong> asigna sus valores de manera diferenciada según sus características. Comprueba usos aprobados y concordancia con el reglamento.</li>
                <li><strong>Casos especiales:</strong> condominios con terreno privado y áreas atípicas requieren revisar los parágrafos; no aplicar automáticamente el tratamiento de un apartamento convencional.</li>
            </ul>
            <?php $readingNumber = 36; require __DIR__ . '/valuation-methodology-article-reading.php'; ?>
        </section>
        <section class="rounded-lg border bg-white p-3">
            <h5 class="font-semibold">Las muestras con las que comparas · artículo 19.2</h5>
            <p class="mt-2">El análisis estadístico en PH utiliza valores integrales por m² de área privada. Si el dato incluye garajes, depósitos, áreas libres u otras unidades privadas o comunes de uso exclusivo, el numeral 2.b dispone descontar su valor del precio negociado para permitir la comparación.</p>
            <p class="mt-2"><strong>No omitas esa depuración porque todos los avisos incluyan parqueadero.</strong> Investiga y sustenta los valores de los componentes; la norma no proporciona un porcentaje automático. Este tratamiento de las muestras no autoriza liquidar aparte los comunes de uso exclusivo del inmueble sujeto.</p>
            <p class="mt-2">Para PH tipo condominio o físicamente asimilable a NPH, revisa el numeral 2.c. Si falta evidencia, deja pendiente la decisión técnica.</p>
            <?php $readingNumber = 19; require __DIR__ . '/valuation-methodology-article-reading.php'; ?>
        </section>
    </div>
    <?php endif; ?>
    </div>
    <div x-show="orientationTab === 3" x-cloak>
    <h4 class="mt-4 font-semibold">3. Otras reglas de Mercado para tomar la decisión</h4>
    <p class="mt-2 text-sm leading-6">En NPH, revisa desagregación y comparabilidad de terreno y construcción (arts. 18 y 19.1). No sumes terreno a un valor integral que ya lo contiene. Las herramientas estadísticas son complementarias (art. 20); cumplir un indicador no sustituye la revisión del mercado ni la sustentación del valor (art. 21).</p>
    <details class="mt-3 rounded-lg border bg-white p-3">
        <summary class="min-h-11 cursor-pointer py-3 font-semibold">Consultar los demás artículos de Mercado · 16, 17, 18, 20 y 21</summary>
        <?php foreach ([16, 17, 18, 20, 21] as $readingNumber): require __DIR__ . '/valuation-methodology-article-reading.php'; endforeach; ?>
    </details>
    </div>
    <div x-show="orientationTab === 4" x-cloak>
    <p class="mt-4 rounded-lg bg-white p-3 text-sm leading-6"><strong>Antes de continuar:</strong> debes poder explicar qué incluye cada valor, qué se trata separadamente, qué evidencia lo respalda y qué queda pendiente. Leer esta ayuda no confirma cumplimiento ni adopta Mercado automáticamente.</p>
    <a class="btn-secondary mt-3 min-h-11" href="<?= e($flowUrl('2', ($item['method'] ?? '') ?: 'mercado', $key)) ?>">Continuar a la selección para <?= e($component['label']) ?></a>
    </div>
    <div class="mt-4 flex flex-wrap gap-2"><button type="button" class="btn-secondary min-h-11" @click="orientationTab--" :disabled="orientationTab === 1">Anterior</button><button type="button" class="btn-primary min-h-11" @click="orientationTab++" :disabled="orientationTab === 4">Siguiente apartado</button></div>
</section>
