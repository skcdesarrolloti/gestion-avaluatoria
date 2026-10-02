<?php
$returnComponent = is_string($_GET['check_component'] ?? null) ? $_GET['check_component'] : '';
$returnQuery = ['stage' => $returnComponent !== '' ? '1' : 'components'];
if ($returnComponent !== '') $returnQuery += ['component' => $returnComponent, 'academy' => 'unidad'];
?>
<div class="mb-4 flex flex-wrap items-center gap-3 rounded-xl bg-teal-50 p-4">
    <a class="btn-secondary" href="<?= e(url('avaluos/' . $record['id'] . '/metodologia-valuatoria?' . http_build_query($returnQuery))) ?>">← Volver al capítulo 8 · <?= $returnComponent !== '' ? 'Verificación de esta unidad' : 'Inmuebles y anexos' ?></a>
    <p class="text-sm">Espera la confirmación de guardado. Al regresar se consultan los datos guardados de estas mismas unidades.</p>
</div>
