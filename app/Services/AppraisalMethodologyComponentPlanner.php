<?php
declare(strict_types=1);
namespace App\Services;

use App\Support\AppraisalCatalog;

final class AppraisalMethodologyComponentPlanner
{
    public function components(array $record, array $units): array
    {
        $private = array_values(array_filter($units, static fn (array $u): bool => ($u['unit_kind'] ?? '') !== 'common'));
        if ($private === []) return [];
        $out = [];
        foreach ($private as $unit) $out[] = $this->component($record, $unit);
        return $out;
    }

    public function paragraph(array $record, array $units): string
    {
        $components = $this->components($record, $units);
        if ($components === []) return '';
        $lines = [];
        foreach ($components as $component) {
            $lines[] = $component['label'] . ': ' . $component['method'] . ' (' . $component['route'] . ').';
        }
        return 'Cuando el predio se compone de varias unidades, anexos o mejoras, la selección metodológica '
            . 'debe revisarse por componente para no trasladar indebidamente un único mercado a elementos '
            . 'con comportamiento económico distinto. Para este expediente se propone la siguiente lectura: '
            . implode(' ', $lines);
    }

    private function component(array $record, array $unit): array
    {
        $label = $this->unitLabel($unit);
        $text = mb_strtolower($label . ' ' . ($unit['property_type'] ?? '') . ' ' . ($unit['igac_typology_hint'] ?? '') . ' ' . ($unit['notes'] ?? ''));
        $type = (string) (($unit['property_type'] ?? '') ?: ($record['tipo_inmueble'] ?? ''));
        $business = (string) ($record['tipo_negocio'] ?? '');
        $base = (string) ($record['base_valor'] ?? '');
        $isAnnex = ($unit['unit_kind'] ?? '') === 'annex';
        $isImprovement = $this->hasAny($text, ['piscina', 'kiosco', 'ramada', 'cerramiento', 'tanque', 'cancha', 'mejora']);
        if ($business === 'arriendo' || $base === 'renta') {
            return $this->pack($unit, $label, 'Renta o capitalización de ingresos', 'Renta',
                'Canon, administración, IVA, vacancia, gastos no recuperables, ingreso neto y tasa.',
                'El componente produce o se analiza por ingresos; el soporte principal son cánones y condiciones de ocupación.');
        }
        if ($type === 'lote' || $base === 'residual') {
            return $this->pack($unit, $label, 'Técnica residual con contraste de mercado', 'Residual',
                'Norma urbana, producto probable, ventas esperadas, costos, utilidad, tiempo y riesgos.',
                'El suelo o aprovechamiento normativo define el valor y exige lectura urbanística/económica.');
        }
        if ($isImprovement) {
            return $this->pack($unit, $label, 'Costo de reposición depreciado', 'Reposición',
                'Cantidades, costo nuevo, vida útil, edad, estado, depreciación y obsolescencias.',
                'La mejora o anexo no siempre tiene mercado independiente; se mide por costo verificable y estado.');
        }
        if ($type === 'casa' || $type === 'finca') {
            return $this->pack($unit, $label, 'Mercado + reposición por componentes', 'Mixto',
                'Comparables de inmuebles similares, terreno, construcciones, mejoras, edad, estado y depreciación.',
                'La unidad combina evidencia de mercado con lectura independiente de terreno, construcción y anexos.');
        }
        if ($isAnnex && $this->hasAny($text, ['depósito', 'deposito', 'parqueadero', 'garaje'])) {
            return $this->pack($unit, $label, 'Comparación indirecta o ajuste sustentado', 'Mercado ajustado',
                'Derecho del anexo, área, utilidad, restricciones PH, comparables cercanos y soporte fotográfico.',
                'El anexo debe tratarse según su independencia jurídica y vida comercial real.');
        }
        return $this->pack($unit, $label, 'Comparación de mercado', 'Mercado',
            'Comparables verificables, precio, área, ubicación, estado, atributos y soporte documental.',
            'La evidencia principal debe provenir de inmuebles semejantes y fuentes observables.');
    }

    private function pack(array $unit, string $label, string $method, string $route, string $inputs, string $note): array
    {
        return [
            'id' => (string) ($unit['id'] ?? ''),
            'label' => $label,
            'kind' => (string) ($unit['unit_kind'] ?? ''),
            'type_label' => $this->label('tipo_inmueble', (string) ($unit['property_type'] ?? '')),
            'method' => $method,
            'route' => $route,
            'inputs' => $inputs,
            'note' => $note,
        ];
    }

    private function unitLabel(array $unit): string
    {
        $label = trim((string) ($unit['label'] ?? ''));
        if ($label !== '') return $label;
        return (($unit['unit_kind'] ?? '') === 'annex' ? 'Anexo ' : 'Unidad ') . (int) ($unit['unit_index'] ?? 0);
    }

    private function hasAny(string $text, array $needles): bool
    {
        foreach ($needles as $needle) if (str_contains($text, $needle)) return true;
        return false;
    }

    private function label(string $field, string $value): string
    {
        return (string) (AppraisalCatalog::selectFields()[$field][4][$value] ?? $value);
    }
}
