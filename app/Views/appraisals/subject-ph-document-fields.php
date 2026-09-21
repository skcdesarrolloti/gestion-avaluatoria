<?php
$phStatusPill = static function (string $state): string {
    return match ($state) {
        'ok' => '<button type="button" disabled class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-800">Completo</button>',
        'warn' => '<button type="button" disabled class="rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-800">Revisar</button>',
        default => '<button type="button" disabled class="rounded-full bg-red-100 px-3 py-1 text-xs font-bold text-red-800">Falta</button>',
    };
};
$docRows = [];
$docRows[] = ['Texto editable para Entregable', $technicalValue('resumen_trazabilidad_ph') !== '' ? 'Texto construido' : 'Sin texto construido', $technicalValue('resumen_trazabilidad_ph') !== '' ? 'ok' : 'missing', 'Construir condición especial.'];
$docRows[] = ['Documento fuente', $phDocumentCount > 0 ? $phDocumentCount . ' soporte(s) cargado(s)' : 'Sin soporte cargado', $phDocumentCount > 0 ? 'ok' : 'missing', 'Cargar reglamento o soporte PH.'];
$docRows[] = ['Cobertura de lectura', $phCoverage, str_contains($phCoverage, '244') || str_contains(mb_strtolower($phCoverage), 'procesadas') ? 'ok' : ($phCoverage !== 'Pendiente' ? 'warn' : 'missing'), 'Revisar páginas omitidas o baja lectura.'];
$docRows[] = ['Contenido registrado', number_format($phExtractedChars, 0, ',', '.') . ' caracteres', $phExtractedChars > 10000 ? 'ok' : ($phExtractedChars > 0 ? 'warn' : 'missing'), 'Verificar que el texto sea suficiente.'];
$docRows[] = ['Condición especial / uso en informe', 'Condición especial y hechos verificados', $technicalValue('resumen_trazabilidad_ph') !== '' ? 'ok' : 'missing', 'Redactar condición especial.'];
$docRows[] = ['Verificación documental', $phLowPages, $phLowPages === 'Sin observaciones pendientes' ? 'ok' : 'warn', 'Cotejar apartes contra original.'];
$docRows[] = ['Soporte del reglamento PH', $phText('regulation_document') !== '' ? 'Con soporte' : 'Sin soporte', $phText('regulation_document') !== '' ? 'ok' : 'missing', 'Ubicar acto constitutivo o reglamento.'];
$docRows[] = ['Reformas y antecedentes', $phText('reform_documents') !== '' ? 'Con soporte' : 'Sin soporte', $phText('reform_documents') !== '' ? 'ok' : 'warn', 'Buscar reformas, aclaraciones o salvedades.'];
?>
<div class="mt-4 rounded-xl border border-slate-200 bg-white p-4">
    <h4 class="font-semibold">Campos documentales para construir el Entregable</h4>
    <div class="mt-3 overflow-x-auto">
        <table class="w-full min-w-[44rem] text-left text-sm">
            <thead class="text-xs uppercase text-slate-500"><tr><th class="py-2 pr-3">Campo</th><th class="py-2 pr-3">Valor detectado</th><th class="py-2 pr-3">Estado</th><th class="py-2">Qué falta</th></tr></thead>
            <tbody class="divide-y divide-slate-100">
                <?php foreach ($docRows as [$field, $value, $state, $missing]): ?>
                    <tr><td class="py-2 pr-3 font-semibold text-slate-800"><?= e($field) ?></td><td class="py-2 pr-3 text-slate-700"><?= e($value) ?></td><td class="py-2 pr-3"><?= $phStatusPill($state) ?></td><td class="py-2 text-slate-600"><?= e($state === 'ok' ? 'Listo para texto.' : $missing) ?></td></tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
