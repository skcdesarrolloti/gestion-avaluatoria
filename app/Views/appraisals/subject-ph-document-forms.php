<?php if (!empty($phDocuments)): ?>
    <div class="hidden" aria-hidden="true">
        <?php foreach (array_slice($phDocuments, 0, 8) as $doc): ?>
            <?php
            $docId = (string) $doc['id'];
            $hasFile = !empty($doc['file_available']) || !empty($doc['has_blob']);
            $canLoad = (int) ($doc['extracted_chars'] ?? 0) > 0 && (!empty($doc['has_extracted_text']) || $hasFile);
            ?>
            <?php if ($canLoad): ?>
                <form id="ph-load-<?= e($docId) ?>" method="post" action="<?= e(url($subjectActionBase . '/ph/soportes/' . $docId . '/cargar')) ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="version" value="<?= (int) ($ph['version'] ?? 0) ?>">
                    <input type="hidden" name="return_to" value="<?= e($subjectActionBase . '#ph') ?>">
                    <input type="hidden" name="ph_typology" :value="phTypology">
                </form>
            <?php elseif ($hasFile): ?>
                <form id="ph-ocr-<?= e($docId) ?>" method="post" action="<?= e(url($subjectActionBase . '/ph/soportes/' . $docId . '/ocr-externo')) ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="version" value="<?= (int) ($ph['version'] ?? 0) ?>">
                    <input type="hidden" name="return_to" value="<?= e($subjectActionBase . '#ph') ?>">
                    <input type="hidden" name="ph_typology" :value="phTypology">
                </form>
            <?php endif; ?>
            <form id="ph-delete-<?= e($docId) ?>" method="post" action="<?= e(url($subjectActionBase . '/ph/soportes/' . $docId . '/eliminar')) ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="version" value="<?= (int) ($ph['version'] ?? 0) ?>">
                <input type="hidden" name="return_to" value="<?= e($subjectActionBase . '#ph') ?>">
            </form>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
