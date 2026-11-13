<?php if ($leaseLegacy): ?>
<details class="mt-5 rounded-xl border p-4">
    <summary class="min-h-11 cursor-pointer py-3 font-semibold">Antecedentes de Renta ya registrados · consulta conservada</summary>
    <p class="text-sm">Estas respuestas anteriores se conservan con su recorrido. Contrástalas al completar la ficha; no se convierten automáticamente en fechas, cánones ni ocupación actual.</p>
    <?php foreach ($leaseLegacy as $legacyKey=>$answers): ?>
    <div class="mt-3 rounded-lg bg-slate-50 p-4"><p class="font-semibold"><?= e($components[$legacyKey]['label'] ?? $leaseUnit['label']) ?></p>
        <dl class="mt-3 space-y-3">
        <?php foreach ($answers as $key=>$value): if (!is_scalar($value) || (string)$value==='') continue;
            $question=\App\Services\IncomeConcept::questions()[$key] ?? [];
            $label=$question['label'] ?? \App\Services\IncomeConcept::texts()[$key][0] ?? $key;
            $text=$question['values'][$value] ?? $value;
        ?>
            <div><dt class="text-sm font-semibold"><?= e($label) ?></dt><dd class="whitespace-pre-line text-sm"><?= e((string)$text) ?></dd></div>
        <?php endforeach; ?>
        </dl>
    </div>
    <?php endforeach; ?>
</details>
<?php endif; ?>
