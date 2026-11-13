<?php
[$normArticle,$normText]=\App\Services\IncomeNormHelp::get($normKey);
$normId='renta-norma-'.bin2hex(random_bytes(5));
?>
<button type="button" class="btn-secondary text-sm" popovertarget="<?= $normId ?>" <?= !empty($normDynamic)?':popovertarget="\''.$normId.'-\'+index"':'' ?> aria-label="Ayuda Resolución 941, artículo <?= e($normArticle) ?>" title="Ver explicación del artículo <?= e($normArticle) ?>">ⓘ Art. <?= e($normArticle) ?></button>
<div id="<?= $normId ?>" <?= !empty($normDynamic)?':id="\''.$normId.'-\'+index"':'' ?> popover class="m-auto rounded-xl border bg-white p-5 text-slate-900" style="max-width:min(34rem,calc(100vw - 2rem))">
    <strong>Resolución IGAC 941 de 2026 · Art. <?= e($normArticle) ?></strong>
    <p class="mt-3"><?= e($normText) ?></p><p class="mt-2 text-xs">Síntesis de orientación; consulta el texto oficial para su alcance completo.</p>
    <div class="mt-3 flex flex-wrap gap-3"><a class="btn-secondary" href="https://www.igac.gov.co/node/53595" target="_blank" rel="noopener">Documento oficial · nueva pestaña</a><button type="button" class="btn-secondary" popovertarget="<?= $normId ?>" <?= !empty($normDynamic)?':popovertarget="\''.$normId.'-\'+index"':'' ?> popovertargetaction="hide">Cerrar ayuda</button></div>
</div>
<?php unset($normDynamic); ?>
