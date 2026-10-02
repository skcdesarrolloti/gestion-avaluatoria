<?php
declare(strict_types=1);
namespace App\Services;

final class MarketUnitDescriptionCheck
{
    public static function row(array $unit): array
    {
        $name = trim((string) ($unit['label'] ?? ''));
        $description = trim((string) ($unit['notes'] ?? ''));
        $annex = ($unit['unit_kind'] ?? '') === 'annex';
        $type = (string) (($unit['construction_type'] ?? '') ?: ($annex ? '' : ($unit['property_type'] ?? '')));
        $types = \App\Support\AppraisalConstructionTypeCatalog::types()
            + \App\Support\AppraisalCatalog::selectFields()['tipo_inmueble'][4];
        $named = $name !== '' && !preg_match('/^(?:Unidad|Anexo)\s+\d+$/iu', $name);
        $classified = $type !== '' && isset($types[$type]);
        $state = $named && $description !== '' && $classified ? 'ok' : 'missing';
        $missing = [];
        if (!$named) $missing[] = 'nombre propio (3.1)';
        if (!$classified) $missing[] = 'tipo de esta unidad (3.3)';
        if ($description === '') $missing[] = 'descripción física propia (3.1)';
        $message = $state === 'ok' ? 'Nombre, tipo y descripción propios registrados. OK no confirma que un texto base IGAC corresponda a la visita.'
            : 'Falta: ' . implode(', ', $missing) . '.';
        $normalizedName = strtr(mb_strtolower($name), ['á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u','ñ'=>'n']);
        $normalizedName = trim(preg_replace('/[^a-z0-9]+/', ' ', $normalizedName) ?? '');
        $expected = preg_match('/^(garaje|parqueadero|celda de parqueo)\b/', $normalizedName) ? 'parqueo'
            : (preg_match('/^(deposito|cuarto util)\b/', $normalizedName) ? 'deposito' : '');
        $mismatch = $annex && $expected !== '' && $type !== '' && $type !== $expected;
        if ($mismatch) {
            $state = 'difference';
            $message = 'El nombre sugiere ' . $types[$expected] . ' y el tipo registrado es ' . ($types[$type] ?? $type)
                . '. Confronta nombre y clasificación con el soporte; no se cambia automáticamente.';
        }
        $classificationPending = !$classified || $mismatch;
        return ['key'=>'description', 'label'=>'Nombre, tipo y descripción física de esta unidad',
            'value'=>($name ?: 'Sin nombre propio') . ' · ' . ($type !== '' ? ($types[$type] ?? $type) : 'Tipo sin definir')
                . ' · ' . ($description ?: 'Sin descripción propia'),
            'type_label'=>$type !== '' ? ($types[$type] ?? $type) : 'Sin definir', 'type_ready'=>$classified,
            'source'=>'3.1 · Nombre, tipo de inmueble y descripción / 3.3 · Tipo de construcción o anexo',
            'state'=>$state, 'message'=>$message,
            'section'=>$classificationPending ? 'construction' : 'tipologias',
            'detail'=>$classificationPending ? 'basicos' : ''];
    }
}
