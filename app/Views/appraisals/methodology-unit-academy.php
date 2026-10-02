<?php
$orientationPh = (string) ($record['regimen_ph'] ?? '');
$academicMethod = ($item['method'] ?? '') ?: $method;
$academicGuide = array_values(array_filter($methodologyChapter['method_guides'], static fn ($g) => $g['key'] === $academicMethod))[0];
$academyTabs = ['revision' => 'Revisión', 'unidad' => $orientationPh === 'si' ? 'PH: dos decisiones distintas que no deben confundirse' : 'Tratamiento de esta unidad',
    'articulos' => 'Artículos del método', 'reglas' => 'Reglas comunes y casos especiales', 'requisitos' => 'Requisitos y pendientes'];
foreach ($academicGuide['parts'] as $part) $academyTabs['parte-' . $part['key']] = $part['label'];
$academyTabs['continuar'] = 'Antes de continuar';
?>
<section class="mt-5 rounded-xl border border-teal-200 bg-teal-50 p-4" x-data="{ academyPart: 'revision', academicKeys: <?= e(json_encode(array_keys($academyTabs))) ?> }">
    <h4 class="font-semibold">Revisar <?= e($component['label']) ?> · orientación para <?= e($methods[$academicMethod]) ?></h4>
    <?php if (empty($item['method'])): ?><p class="mt-2 text-sm">Método aún por seleccionar. Esta consulta de academia no registra una decisión.</p><?php endif; ?>
    <p class="mt-2 text-sm"><?= count($academyTabs) ?> apartados disponibles. Desplaza la fila para ver todos o usa Anterior y Siguiente.</p>
    <nav class="mt-3 flex gap-2 overflow-x-auto pb-2" aria-label="Apartados académicos de <?= e($component['label']) ?>">
        <?php $academicNumber = 0; foreach ($academyTabs as $tabKey => $tabTitle): $academicNumber++; ?>
        <button type="button" class="btn-secondary shrink-0" :aria-pressed="academyPart === '<?= e($tabKey) ?>'"
            :class="academyPart === '<?= e($tabKey) ?>' ? 'bg-white text-orange-600' : ''" @click="academyPart = '<?= e($tabKey) ?>'">
            <?= e($academicNumber . '. ' . $tabTitle) ?>
        </button>
        <?php endforeach; ?>
    </nav>
    <div x-show="academyPart === 'revision'">
        <?php if ($academicMethod === 'mercado'): require __DIR__ . '/methodology-reading-1.php'; else: ?>
        <p class="mt-4 text-sm"><?= e($academicGuide['summary']) ?></p>
        <p class="mt-3 text-sm">Confirma las áreas, derechos, alcance y componentes incluidos. Consulta los artículos e insumos del método registrado antes de continuar. Leer esta guía no modifica la selección del analista.</p>
        <?php endif; ?>
    </div>
    <div x-show="academyPart === 'unidad'" x-cloak><?php require __DIR__ . '/methodology-reading-2.php'; ?></div>
    <div x-show="academyPart === 'articulos'" x-cloak>
        <h5 class="mt-4 font-semibold"><?= e($academicGuide['articles']) ?> · <?= e($academicGuide['label']) ?></h5>
        <p class="mt-2 text-sm"><?= e($academicGuide['summary']) ?></p>
        <?php $academyCards = $academicGuide['article_cards']; require __DIR__ . '/methodology-article-cards.php'; ?>
    </div>
    <div x-show="academyPart === 'reglas'" x-cloak>
        <?php require __DIR__ . '/methodology-common-reading.php'; require __DIR__ . '/valuation-methodology-normative-review.php'; ?>
        <p class="mt-3 text-sm">Elección del método: documenta la decisión y los soportes para esta unidad (art. 15).</p>
        <?php $readingNumber = 15; require __DIR__ . '/valuation-methodology-article-reading.php'; ?>
        <?php if ($academicMethod === 'mercado'): require __DIR__ . '/methodology-reading-3.php'; endif; ?>
    </div>
    <div x-show="academyPart === 'requisitos'" x-cloak>
        <?php $guideKey = $academicMethod; $methodGuide = $academicGuide; require __DIR__ . '/valuation-methodology-method-review.php'; ?>
    </div>
    <?php foreach ($academicGuide['parts'] as $part): ?>
    <div class="mt-4 rounded-lg bg-white p-4" x-show="academyPart === 'parte-<?= e($part['key']) ?>'" x-cloak>
        <h5 class="font-semibold"><?= e($part['label']) ?></h5>
        <ul class="mt-3 list-disc space-y-2 pl-5 text-sm leading-6"><?php foreach ($part['bullets'] as $bullet): ?><li><?= e($bullet) ?></li><?php endforeach; ?></ul>
    </div>
    <?php endforeach; ?>
    <div x-show="academyPart === 'continuar'" x-cloak>
        <p class="mt-4 text-sm">Comprueba qué incluye cada valor, qué se trata separadamente, qué evidencia lo respalda y qué queda pendiente.</p>
        <a class="btn-secondary mt-3" href="<?= e($flowUrl('2', $academicMethod, $key)) ?>">Revisar método y alcance de <?= e($component['label']) ?></a>
        <?php if (!empty($item['method'])): ?><a class="btn-primary mt-3" href="<?= e($flowUrl('3', $academicMethod, $key)) ?>">Continuar a insumos de <?= e($component['label']) ?></a><?php endif; ?>
    </div>
    <div class="mt-4 flex flex-wrap items-center gap-2">
        <button type="button" class="btn-secondary" :disabled="academicKeys.indexOf(academyPart) === 0" @click="academyPart = academicKeys[academicKeys.indexOf(academyPart) - 1]">Anterior apartado</button>
        <span class="text-sm" x-text="(academicKeys.indexOf(academyPart) + 1) + ' de ' + academicKeys.length"></span>
        <button type="button" class="btn-secondary" :disabled="academicKeys.indexOf(academyPart) === academicKeys.length - 1" @click="academyPart = academicKeys[academicKeys.indexOf(academyPart) + 1]">Siguiente apartado</button>
    </div>
</section>
