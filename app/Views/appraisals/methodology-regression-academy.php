<input type="hidden" data-regression-normative-support value="<?= e(\App\Support\MarketRegressionAcademy::reportSupport()) ?>">
<details class="rounded-xl border p-4">
    <summary class="min-h-11 cursor-pointer font-semibold">Academia paso a paso · entender la regresión con ejemplos</summary>
    <div class="mt-3 space-y-3">
        <p>En «3. Construir el modelo» estudia los pasos 1–5. En «4. Revisar el modelo» estudia los pasos 6–9. Los ejemplos didácticos no modifican tus datos.</p>
        <?php foreach (\App\Support\MarketRegressionAcademy::topics() as [$tab, $title, $example, $meaning, $basis]): ?>
            <details x-show="regressionTab==='<?= e($tab) ?>'" class="rounded-lg border p-3">
                <summary class="min-h-11 cursor-pointer font-semibold"><?= e($title) ?></summary>
                <div class="mt-3 space-y-3">
                    <p><?= e($example) ?></p>
                    <p><?= e($meaning) ?></p>
                    <p class="text-sm"><strong>Fuente y alcance:</strong> <?= e($basis) ?></p>
                </div>
            </details>
        <?php endforeach; ?>
    </div>
</details>
<details class="rounded-xl border p-4">
    <summary class="min-h-11 cursor-pointer font-semibold">Fundamento nacional, sectorial e internacional · versiones y pendientes</summary>
    <div class="mt-3 space-y-3">
        <p><strong>Resolución IGAC 0941 de 2026:</strong> art. 20, parágrafo 2: documentación auditable de supuestos y límites, datos suficientes y de calidad, evaluación del error y validación profesional para las herramientas allí contempladas. El modelo por sí solo no determina el valor final. Verificar ámbito del encargo; art. 19 y anexo 2.1 respaldan comparabilidad y trazabilidad.</p>
        <div class="flex flex-wrap gap-3">
            <?php foreach ([19,20,21] as $number): $article = \App\Services\Resolution941Reading::article($number); ?>
                <a class="inline-flex min-h-11 items-center underline" href="<?= e($article['url']) ?>" target="_blank" rel="noopener" data-no-fetch>Consultar art. <?= e((string) $number) ?> · documento fuente</a>
            <?php endforeach; ?>
        </div>
        <p><strong>NTS M 01 (Norma Técnica Sectorial), edición examinada 12/02/2016:</strong> anexo B informativo, B.3–B.4 (páginas PDF 35–36), B.5 y B.6–B.7 (37–38), B.8–B.13 (39 y siguientes). Apoya modelo, supuestos, significancia, códigos y presentación; no obliga de forma general a usar regresión. NTS S 03, edición examinada 2009, 7.1.8 y 7.1.10: datos, análisis y razones. Confirmar edición y aplicabilidad; no trasladar automáticamente el tratamiento por factores del anexo A de M 01.</p>
        <a class="inline-flex min-h-11 items-center underline" href="<?= e(url('normas-tecnicas-sectoriales')) ?>" target="_blank" rel="noopener" data-no-fetch>Consultar biblioteca sectorial</a>
        <p><strong>IVS (Normas Internacionales de Valuación), referencia de estudio 2025:</strong> IVS 104, 30 y 50, datos e insumos; IVS 105, 10, 30, 40 y 50, selección, evaluación y documentación del modelo (páginas PDF 59–61); IVS 106, 20 y 30, documentación e informe. La traducción aportada es preliminar: cotejo con el original oficial inglés pendiente antes de declarar conformidad. La referencia internacional no sustituye el marco colombiano.</p>
        <a class="inline-flex min-h-11 items-center underline" href="<?= e(url('normas-internacionales-valuacion')) ?>" target="_blank" rel="noopener" data-no-fetch>Consultar biblioteca internacional</a>
        <p><strong>Apoyo académico:</strong> curso y NIST (Instituto Nacional de Estándares y Tecnología de Estados Unidos). Explican herramientas; no establecen obligaciones jurídicas colombianas. Señales y mínimos operativos son criterios del ejercicio.</p>
        <p class="text-amber-900">Este apartado explica lo implementado y sus pendientes. No certifica cumplimiento integral ni adopta automáticamente el valor del sujeto.</p>
    </div>
</details>
