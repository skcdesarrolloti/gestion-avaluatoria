<?php
$generalTopics=[
    'principios'=>['Principios generales','Orienta las decisiones del avalúo; la lectura de la norma no acredita su cumplimiento.',[5]],
    'parametros'=>['Parámetros mínimos','Confronta el encargo, las características y los soportes antes de desarrollar cualquier método.',[11]],
    'metodos'=>['Elección y alcance','El artículo 15 reúne los métodos. El 27 explica Costo y su composición; no obliga a aplicarlo a todas las unidades.',[15,27]],
    'areas'=>['Áreas, derechos y PH','Los artículos 36 y 37 son reglas especiales de PH y casos asimilables. Verifica su aplicabilidad y evita duplicar componentes.',[36,37]],
    'informe'=>['Informe y sustentación','Comprueba qué debe quedar explicado y respaldado en el informe del avalúo.',[14]],
];
?>
<section class="mt-5 rounded-xl border border-indigo-100 bg-white p-4" x-data="{ generalTopic: 'principios' }">
    <p class="text-xs font-bold uppercase text-indigo-700">Guía amplia para el analista</p>
    <h3 class="mt-2 text-xl font-semibold">Academia General · Resolución 941</h3>
    <p class="mt-2 text-sm leading-6">Consulta compartida antes de entrar a Mercado, Costo, Renta o Residual. Abre cada artículo para leerlo completo y ciérralo al terminar.</p>
    <?php require __DIR__.'/valuation-methodology-normative-review.php'; ?>
    <nav class="mt-4 flex flex-wrap gap-2" aria-label="Temas de academia general">
        <?php foreach ($generalTopics as $topicKey=>[$topicLabel]): ?>
        <button type="button" class="btn-secondary" :aria-pressed="generalTopic === '<?= e($topicKey) ?>'"
            :class="generalTopic === '<?= e($topicKey) ?>' ? 'bg-white text-indigo-800 shadow-sm' : 'text-slate-600'"
            @click="generalTopic = '<?= e($topicKey) ?>'">
            <?= e($topicLabel) ?>
        </button>
        <?php endforeach; ?>
    </nav>
    <?php foreach ($generalTopics as $topicKey=>[$topicLabel,$topicHelp,$topicArticles]): ?>
    <section class="mt-4 rounded-xl border bg-slate-50 p-4" x-show="generalTopic === '<?= e($topicKey) ?>'" <?= $topicKey!=='principios'?'x-cloak':'' ?>>
        <h4 class="font-semibold"><?= e($topicLabel) ?></h4>
        <p class="mt-2 text-sm leading-6"><?= e($topicHelp) ?></p>
        <?php if ($topicKey==='areas' && ($record['regimen_ph'] ?? '')==='si'):
            $orientationPh='si'; $sharedAcademyContext=true;
            require __DIR__.'/methodology-reading-2.php'; $sharedAcademyContext=false;
            $topicArticles=[37];
        endif; ?>
        <?php foreach ($topicArticles as $readingNumber): require __DIR__.'/valuation-methodology-article-reading.php'; endforeach; ?>
    </section>
    <?php endforeach; ?>
    <p class="mt-4 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm leading-6"><strong>Control para el análisis:</strong> si una decisión contradice un artículo aplicable o un requisito queda sin cumplir o sin soporte, debe advertirse indicando el artículo, la discrepancia y su incidencia. La academia es consulta; no certifica cumplimiento automático.</p>
</section>
