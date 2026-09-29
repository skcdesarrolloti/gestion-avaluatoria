<?php
declare(strict_types=1);
namespace App\Services;

final class AppraisalUnitCompositionNarrator
{
    public function status(array $record, array $units): string
    {
        $properties = $this->properties($units);
        $annexes = $this->annexes($units);
        $propertyCount = $properties !== [] ? count($properties) : max(0, (int) ($record['igac_property_units_count'] ?? 0));
        $annexCount = $annexes !== [] ? count($annexes) : max(0, (int) ($record['igac_annex_units_count'] ?? 0));
        return $propertyCount . ' unidad(es) principal(es) y ' . $annexCount . ' anexo(s)';
    }

    public function paragraph(array $record, array $units): string
    {
        $properties = $this->properties($units);
        $annexes = $this->annexes($units);
        $propertyCount = $properties !== [] ? count($properties) : max(0, (int) ($record['igac_property_units_count'] ?? 0));
        $annexCount = $annexes !== [] ? count($annexes) : max(0, (int) ($record['igac_annex_units_count'] ?? 0));
        if ($propertyCount === 0 && $annexCount === 0) return '';
        $text = 'La configuración del encargo registra ' . $propertyCount . ' unidad(es) inmobiliaria(s) principal(es)';
        if ($annexCount > 0) $text .= ' y ' . $annexCount . ' anexo(s)';
        $text .= '.';
        $named = $this->named($properties, 'unidad principal');
        $namedAnnexes = $this->named($annexes, 'anexo');
        if ($named !== '') $text .= ' Desde el numeral 1.1 se identifica(n) la(s) unidad(es) principal(es) como ' . $named . '.';
        if ($namedAnnexes !== '') $text .= ' Los anexos se identifican como ' . $namedAnnexes . '.';
        return $text . ' Esta composición debe conservarse en la metodología: los anexos pueden quedar integrados al comparable del inmueble principal o valorarse por separado únicamente cuando el analista lo justifique.';
    }

    private function properties(array $units): array
    {
        return array_values(array_filter($units, static fn (array $unit): bool => ($unit['unit_kind'] ?? '') === 'property'));
    }

    private function annexes(array $units): array
    {
        return array_values(array_filter($units, static fn (array $unit): bool => ($unit['unit_kind'] ?? '') === 'annex'));
    }

    private function named(array $units, string $fallback): string
    {
        $names = [];
        foreach ($units as $unit) $names[] = $this->unitName($unit, $fallback);
        return $this->join(array_slice(array_filter($names), 0, 8));
    }

    private function unitName(array $unit, string $fallback): string
    {
        $default = (($unit['unit_kind'] ?? '') === 'annex' ? 'Anexo ' : 'Unidad ') . (int) ($unit['unit_index'] ?? 0);
        $name = trim((string) ($unit['label'] ?? ''));
        if ($name === '' || $name === $default) $name = $fallback . ' ' . (int) ($unit['unit_index'] ?? 0);
        $type = trim((string) ($unit['property_type'] ?? ''));
        return $type !== '' ? $name . ' (' . str_replace('_', ' ', $type) . ')' : $name;
    }

    private function join(array $values): string
    {
        if (count($values) <= 1) return $values[0] ?? '';
        $last = array_pop($values);
        return implode(', ', $values) . ' y ' . $last;
    }
}
