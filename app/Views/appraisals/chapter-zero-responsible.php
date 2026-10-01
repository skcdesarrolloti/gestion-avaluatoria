    <label class="label">Perito responsable
        <select class="input" name="appraiser_id" <?= !empty($_SESSION['user']['analyst_id']) ? 'disabled' : '' ?>>
            <option value="">Selecciona perito</option>
            <?php foreach ($appraisers as $appraiser): ?>
                <option value="<?= e($appraiser['id']) ?>" <?= $selectedAppraiser($appraiser['id']) ?>>
                    <?= e($appraiser['code'] . ' · ' . $appraiser['full_name']) ?>
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
                No hay peritos con RAA vigente. Actualiza el RAA en Maestros para asignar expediente.
            </span>
        <?php endif; ?>
    </label>
