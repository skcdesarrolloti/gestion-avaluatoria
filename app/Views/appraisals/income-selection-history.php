<details class="mt-6 rounded-xl border p-4" <?= $selectionEvents?'open':'' ?>>
    <summary class="cursor-pointer font-semibold">Constancia de decisiones · <?= count($selectionEvents) ?></summary>
    <p class="mt-2 text-sm">Conserva fecha, responsable, motivo, soporte, muestra de cálculo y resultados antes y después. La reincorporación agrega un evento; no borra la exclusión anterior.</p>
    <?php if (!$selectionEvents): ?><p class="mt-3">Aún no se han aplicado exclusiones o reincorporaciones.</p><?php else: ?>
    <a class="btn-secondary mt-3" href="<?= e(url('avaluos/'.$record['id'].'/metodologia-valuatoria/renta/constancia?component='.rawurlencode($componentKey))) ?>" download>Descargar constancia completa (JSON)</a>
    <?php foreach (array_slice(array_reverse($selectionEvents),($selectionPage-1)*20,20) as $event):
        $eventRows=array_column($selectionState['datasets'][$event['dataset']] ?? [],null,'id');$eventRow=$eventRows[$event['sample']] ?? [];
        $eventDate=(new \DateTimeImmutable($event['at']))->setTimezone(new \DateTimeZone('America/Bogota'))->format('d/m/Y H:i:s'); ?>
        <article class="mt-4 border-t pt-3">
            <h4 class="font-semibold"><?= $event['operation']==='exclude'?'Exclusión':'Reincorporación' ?> · <?= e($eventRow['label'] ?? $event['sample']) ?></h4>
            <p class="text-sm"><?= e($eventDate) ?> · Colombia · <?= e($event['actor']['name']) ?></p>
            <p class="mt-2"><?= e(\App\Services\IncomeSampleSelection::REASONS[$event['category']] ?? $event['category']) ?>: <?= e($event['reason']) ?></p>
            <p class="mt-1">Soporte: <?= e($event['support']) ?></p>
            <p class="mt-1 text-sm">Anuncios: <?= (int)$event['before']['n'] ?> → <?= (int)$event['after']['n'] ?>.
                Media: <?= e($selectionFormat($event['before']['mean'])) ?> → <?= e($selectionFormat($event['after']['mean'])) ?> COP/m²/mes.
                CV: <?= e($selectionFormat($event['before']['cv'])) ?> → <?= e($selectionFormat($event['after']['cv'])) ?> %.</p>
        </article>
    <?php endforeach; ?>
    <nav class="mt-3 flex gap-3" aria-label="Páginas de constancias">
        <?php if ($selectionPage>1): ?><a class="btn-secondary" href="<?= e($flowUrl('3').'&renta_paso=analysis&decision_page='.($selectionPage-1)) ?>#renta-analisis">Decisiones más recientes</a><?php endif; ?>
        <?php if (count($selectionEvents)>$selectionPage*20): ?><a class="btn-secondary" href="<?= e($flowUrl('3').'&renta_paso=analysis&decision_page='.($selectionPage+1)) ?>#renta-analisis">Decisiones anteriores</a><?php endif; ?>
    </nav>
    <?php endif; ?>
</details>
