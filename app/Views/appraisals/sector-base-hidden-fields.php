<?php
use App\Support\AppraisalSectorCatalog;
foreach (AppraisalSectorCatalog::keys() as $field):
    $value = (string) ($sector[$field] ?? '');
?>
    <input form="<?= e($sectorFormId) ?>" type="hidden" name="<?= e($field) ?>" value="<?= e($value) ?>">
<?php endforeach; ?>
