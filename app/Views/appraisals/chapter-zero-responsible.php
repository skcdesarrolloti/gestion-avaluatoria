    <label class="label">Perito responsable
        <select class="input" name="appraiser_id" <?= !empty($_SESSION['user']['analyst_id']) ? 'disabled' : '' ?>>
            <option value="">Selecciona perito</option>
            <?php foreach ($appraisers as $appraiser): ?>
                <option value="<?= e($appraiser['id']) ?>" <?= $selectedAppraiser($appraiser['id']) ?>>
                    <?= e(trim($appraiser['code'] . ' · ' . $appraiser['full_name'], ' ·')) ?><?= !empty($appraiser['assignment_notice']) ? ' · Revisar registro RAA' : '' ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if (!empty($_SESSION['user']['analyst_id'])): ?><input type="hidden" name="appraiser_id" value="<?= e($_SESSION['user']['responsible_appraiser_id']) ?>"><span class="text-xs">Perito responsable asignado por el titular. Tu cuenta registra quién diligencia.</span><?php endif; ?>
        <?php if (count($appraisers) === 1 && $field('appraiser_id') === ''): ?>
            <span class="mt-1 block text-xs leading-5 text-emerald-700">
                Se selecciona automáticamente porque solo hay un perito vigente.
            </span>
        <?php elseif (count($appraisers) === 0): ?>
            <span class="mt-1 block text-xs leading-5 text-red-700">
                No hay peritos disponibles para una nueva asignación con los filtros actuales de actividad y vigencia del certificado RAA. El titular debe revisar los registros en Maestros.
            </span>
        <?php endif; ?>
    </label>
    <?php foreach ($appraisers as $appraiser): if (empty($appraiser['assignment_notice'])) continue; ?>
        <p role="status" class="rounded-lg border border-amber-200 bg-amber-50 p-3 text-sm text-amber-900 md:col-span-2">
            <strong><?= e($appraiser['full_name']) ?>:</strong> <?= e($appraiser['assignment_notice']) ?>
            Puedes guardar el avance del expediente sin borrar al responsable ni su consecutivo.
        </p>
    <?php endforeach; ?>
