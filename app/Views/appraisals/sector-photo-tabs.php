<section id="soporte-sector" class="mt-8 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8"
    x-data="{ activeSectorPhoto: '02' }">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="eyebrow">Soportes gráficos del sector</p>
            <h2 class="mt-2 text-2xl font-semibold">Fotos, mapas y capturas</h2>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                Pega o sube los soportes que no llegan automáticamente desde fuentes externas.
            </p>
        </div>
    </div>
    <nav class="mt-5 flex gap-2 overflow-x-auto rounded-xl bg-slate-100 p-2" aria-label="Soportes gráficos">
        <?php foreach (['02' => 'Cartografía', '06' => 'Vías', '14' => 'Campo'] as $code => $label): ?>
            <button type="button" class="min-h-12 shrink-0 rounded-lg px-4 py-2 text-left text-sm font-semibold"
                @click="activeSectorPhoto = '<?= e($code) ?>'"
                :class="activeSectorPhoto === '<?= e($code) ?>' ? 'bg-teal-700 text-white shadow-sm' : 'bg-white text-teal-800 hover:bg-white/70'">
                <span class="block text-xs opacity-80"><?= e($code) ?></span>
                <?= e($label) ?>
            </button>
        <?php endforeach; ?>
    </nav>
    <div class="mt-5">
        <div class="grid gap-4 lg:grid-cols-2" x-show="activeSectorPhoto === '02'">
            <?php
            $embedded = true; $photoUploadEmbedded = true; $photoUploadCompact = true;
            $subjectActionBase = 'avaluos/' . $record['id'] . '/sector';
            $photoUploadReturnTo = 'avaluos/' . $record['id'] . '/sector#soporte-sector';
            $photoUploadCaption = 'sector:mapa-delimitacion';
            $photoUploadEyebrow = 'Soporte cartográfico';
            $photoUploadTitle = 'Figura 1 · Mapa delimitado';
            $photoUploadDescription = 'Pega con Ctrl+V o sube la captura del perímetro, mapa oficial o soporte cartográfico.';
            $photoUploadNamePlaceholder = 'Ej. Delimitación del barrio ' . ($neighborhoodLabel ?: 'seleccionado');
            require BASE_PATH . '/app/Views/appraisals/photo-upload.php';
            $photoUploadCaption = 'sector:imagen-satelital';
            $photoUploadTitle = 'Figura 2 · Imagen satelital';
            $photoUploadDescription = 'Pega con Ctrl+V o sube la imagen satelital que soporte la lectura espacial.';
            $photoUploadNamePlaceholder = 'Ej. Imagen satelital del barrio ' . ($neighborhoodLabel ?: 'seleccionado');
            require BASE_PATH . '/app/Views/appraisals/photo-upload.php';
            ?>
        </div>
        <div class="grid gap-4 lg:grid-cols-2" x-show="activeSectorPhoto === '06'">
            <?php
            $photoUploadCaption = 'sector:mapa-vias';
            $photoUploadEyebrow = 'Soporte vial';
            $photoUploadTitle = 'Figura 3 · Mapa vial';
            $photoUploadDescription = 'Pega con Ctrl+V o sube la captura con vías principales, accesos o señalización.';
            $photoUploadNamePlaceholder = 'Ej. Vías y accesos del barrio ' . ($neighborhoodLabel ?: 'seleccionado');
            require BASE_PATH . '/app/Views/appraisals/photo-upload.php';
            ?>
        </div>
        <div class="grid gap-4 lg:grid-cols-2" x-show="activeSectorPhoto === '14'">
            <?php
            $photoUploadCaption = 'sector:registro-campo';
            $photoUploadEyebrow = 'Soporte de campo';
            $photoUploadTitle = 'Figura 4 · Registro de campo';
            $photoUploadDescription = 'Pega con Ctrl+V o sube fotos de visita, equipamientos, externalidades o evidencias.';
            $photoUploadNamePlaceholder = 'Ej. Evidencia de campo del sector';
            require BASE_PATH . '/app/Views/appraisals/photo-upload.php';
            ?>
        </div>
    </div>
</section>
