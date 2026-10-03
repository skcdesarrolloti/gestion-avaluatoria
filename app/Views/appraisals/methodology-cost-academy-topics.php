<?php
$costTopics=\App\Services\CostMethodAcademy::topics();
$costGroups=[
    'alcance'=>['Alcance y costo a nuevo',['objeto','reposicion']],
    'fuentes'=>['Fuentes e indirectos',['fuentes','indirectos']],
    'vidas'=>['Vidas útiles',['vidas','prolongada','remanente']],
    'depreciacion'=>['Depreciación',['conservacion','ross']],
    'especiales'=>['Situaciones especiales',['especiales','ejecucion','retiro']],
    'cierre'=>['Cierre y documentos',['cierre','documentos']],
];
?>
<section class="mt-5" x-data="{ costGroup: 'alcance', costTopic: 'objeto' }" aria-label="Temas de academia C1">
    <p class="text-sm leading-6">Elige un apartado y después su tema. La academia conserva las tablas, fórmulas, ejemplos y fuentes; consultar un tema no adopta parámetros en el avalúo.</p>
    <nav class="mt-4 flex flex-wrap gap-2" aria-label="Apartados del costo">
        <?php foreach ($costGroups as $groupKey=>[$groupLabel,$topicIds]): ?>
        <button type="button" class="btn-secondary" :aria-pressed="costGroup === '<?= e($groupKey) ?>'"
            :class="costGroup === '<?= e($groupKey) ?>' ? 'bg-white text-indigo-800 shadow-sm' : 'text-slate-600'"
            @click="costGroup = '<?= e($groupKey) ?>'; costTopic = '<?= e($topicIds[0]) ?>'">
            <?= e($groupLabel) ?>
        </button>
        <?php endforeach; ?>
    </nav>
    <?php foreach ($costGroups as $groupKey=>[$groupLabel,$topicIds]): ?>
    <nav class="mt-3 flex flex-wrap gap-2 rounded-xl border border-indigo-100 bg-indigo-50 p-2"
        aria-label="Temas de <?= e($groupLabel) ?>" x-show="costGroup === '<?= e($groupKey) ?>'" <?= $groupKey!=='alcance'?'x-cloak':'' ?>>
        <?php foreach ($costTopics as $topic): if (!in_array($topic['id'],$topicIds,true)) continue; ?>
        <button type="button" class="btn-secondary" :aria-pressed="costTopic === '<?= e($topic['id']) ?>'"
            :class="costTopic === '<?= e($topic['id']) ?>' ? 'bg-white text-indigo-800 shadow-sm' : 'text-slate-600'"
            @click="costTopic = '<?= e($topic['id']) ?>'">
            <?= e($topic['title']) ?>
        </button>
        <?php endforeach; ?>
        <?php if ($groupKey==='cierre'): ?>
        <button type="button" class="btn-secondary" :aria-pressed="costTopic === 'documentos'"
            :class="costTopic === 'documentos' ? 'bg-white text-indigo-800 shadow-sm' : 'text-slate-600'"
            @click="costTopic = 'documentos'">Documentos completos</button>
        <?php endif; ?>
    </nav>
    <?php endforeach; ?>
    <?php foreach ($costTopics as $topic): ?>
    <section id="costo-academia-<?= e($topic['id']) ?>" class="mt-4 rounded-xl border border-indigo-100 bg-white p-4"
        x-show="costTopic === '<?= e($topic['id']) ?>'" <?= $topic['id']!=='objeto'?'x-cloak':'' ?>>
        <?php require __DIR__.'/methodology-cost-academy-topic.php'; ?>
    </section>
    <?php endforeach; ?>
    <section class="mt-4 rounded-xl border border-indigo-100 bg-white p-4" x-show="costTopic === 'documentos'" x-cloak>
        <h5 class="font-semibold">Documentos completos de Costo</h5>
        <p class="mt-2 text-sm leading-6">Los artículos 27–30 se consultan completos en las tarjetas de arriba. Aquí puedes consultar y descargar el apartado íntegro del anexo técnico.</p>
        <?php require __DIR__.'/methodology-cost-academy-full-reading.php'; ?>
    </section>
    <p class="mt-3 text-xs leading-5">Referencia principal: Resolución IGAC 941 de 2026 y anexo técnico 2.3, tablas 2–7. Ejemplos didácticos sin adopción en el expediente. Fuente editorial: <a class="font-semibold text-blue-800 underline" href="https://www.sispac.com.co/empresa" target="_blank" rel="noopener" data-no-fetch>SISPAC</a>.</p>
</section>
