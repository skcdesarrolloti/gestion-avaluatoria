<?php if (!empty($phDocuments)): ?>
    <ul class="mt-2 space-y-2 text-sm text-slate-700">
        <?php foreach (array_slice($phDocuments, 0, 8) as $doc): ?>
            <?php
            $hasFile = !empty($doc['file_available']) || !empty($doc['has_blob']);
            $canLoad = (int) ($doc['extracted_chars'] ?? 0) > 0 && (!empty($doc['has_extracted_text']) || $hasFile);
            ?>
            <li class="rounded-lg border border-slate-100 bg-slate-50 px-3 py-2">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <span><?= e($doc['source_filename']) ?> · <?= e((string) $doc['extracted_chars']) ?> caracteres</span>
                    <div class="flex flex-wrap gap-2">
                    <?php if ($canLoad): ?>
                        <a class="btn-secondary min-h-11 px-3 text-xs" target="_blank" rel="noopener"
                            href="<?= e(url($subjectActionBase . '/ph/soportes/' . $doc['id'] . '/texto')) ?>">Texto por páginas</a>
                        <button class="btn-secondary min-h-9 px-3 py-1 text-xs" type="submit"
                            form="ph-load-<?= e((string) $doc['id']) ?>"
                            title="Usa el texto OCR ya guardado para recalcular la ficha y la matriz por tipología.">
                            Recalcular matriz
                        </button>
                    <?php elseif ($hasFile): ?>
                        <button class="btn-secondary min-h-9 px-3 py-1 text-xs" type="button" disabled
                            title="No hay texto extraído para cargar en la ficha.">Cargar</button>
                        <button class="btn-secondary min-h-9 px-3 py-1 text-xs" type="submit"
                            form="ph-ocr-<?= e((string) $doc['id']) ?>" onclick="this.textContent='Leyendo IA/OCR...';"
                            title="<?= empty($phExternalOcr) ? 'Falta configurar PH_EXTERNAL_OCR_ENDPOINT en el servidor.' : 'Enviar este soporte al OCR/IA configurado.' ?>">
                            Leer con IA/OCR
                        </button>
                    <?php else: ?>
                        <button class="btn-secondary min-h-9 px-3 py-1 text-xs" type="button" disabled
                            title="El archivo físico ya no está disponible. Elimina este soporte y vuelve a subirlo.">Archivo no disponible</button>
                    <?php endif; ?>
                    <button class="btn-secondary min-h-9 px-3 py-1 text-xs text-red-700" type="submit"
                        form="ph-delete-<?= e((string) $doc['id']) ?>"
                        onclick="return confirm('¿Eliminar este soporte PH del avalúo? Los campos ya diligenciados se conservarán.');">Eliminar</button>
                    </div>
                </div>
                <?php if (($phActionDocumentId ?? '') === (string) $doc['id'] && !empty($phActionMessage)): ?>
                    <p class="mt-2 rounded-lg px-3 py-2 text-xs font-semibold <?= ($phActionTone ?? '') === 'error' ? 'bg-red-50 text-red-800' : 'bg-emerald-50 text-emerald-800' ?>">
                        <?= e($phActionMessage) ?>
                    </p>
                <?php endif; ?>
                <p class="mt-2 text-xs text-slate-600"><?= e((string) ($doc['analysis_message'] ?? '')) ?></p>
            </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p class="mt-2 text-sm text-slate-600">Sin soportes cargados.</p>
<?php endif; ?>
