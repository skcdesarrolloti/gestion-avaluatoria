<?php
declare(strict_types=1);
namespace App\Services;

use App\Support\AppraisalFunctionalVariableCatalog;

final class AppraisalSubjectConstructionNarrator
{
    public function general(array $units, string $recordType = ''): string
    {
        $rows = [];
        foreach ($this->privateUnits($units) as $unit) {
            $facts = $this->facts($unit, $recordType);
            if ($this->text($unit['construction_state'] ?? '') !== '') $facts[] = 'estado de obra: ' . $this->label($unit['construction_state']);
            if ($this->text($unit['functional_notes'] ?? '') !== '') $facts[] = 'notas funcionales: ' . $this->text($unit['functional_notes']);
            $rows[] = $this->unitName($unit) . ($facts ? ': ' . implode('; ', $facts) . '.' : ': aspectos generales pendientes de completar.');
        }
        return $rows ? implode("\n", $rows) : 'No se han definido unidades constructivas para describir aspectos generales.';
    }

    private function facts(array $unit, string $recordType): array
    {
        $facts = [];
        foreach ($this->fields() as [$key, $label, $format]) {
            if (str_starts_with($key, 'functional_') && !$this->functionalFieldApplies($unit, $recordType, $key)) continue;
            if ($this->text($unit[$key] ?? '') === '') continue;
            $facts[] = $label . ': ' . $this->format($unit[$key], $format);
        }
        return $facts;
    }

    private function fields(): array
    {
        return [
            ['construction_floors', 'niveles', 'text'],
            ['construction_basements', 'sótanos', 'text'],
            ['construction_age_years', 'edad aproximada', 'text'],
            ['construction_useful_life_years', 'vida útil', 'text'],
            ['construction_remaining_life_years', 'vida útil remanente', 'text'],
            ['construction_rentable_units', 'unidades rentables', 'text'],
            ['functional_bedrooms_count', 'habitaciones', 'text'],
            ['functional_bathrooms_count', 'baños', 'text'],
            ['functional_service_room_bathroom', 'servicio', 'label'],
            ['functional_parking_spaces_count', 'celdas de parqueo', 'text'],
            ['functional_loading_bays_count', 'muelles o puntos de cargue', 'text'],
            ['functional_clear_height_m', 'altura libre', 'm'],
            ['functional_office_area_m2', 'área de oficina o apoyo', 'm2'],
            ['functional_access_type', 'tipo de acceso', 'label'],
            ['functional_view', 'vista', 'label'],
            ['functional_finish_quality', 'acabados', 'label'],
        ];
    }

    private function format(mixed $value, string $format): string
    {
        return match ($format) {
            'm' => $this->meters($value),
            'm2' => $this->m2($value),
            'label' => $this->label($value),
            default => $this->text($value),
        };
    }

    private function privateUnits(array $units): array
    {
        return array_values(array_filter($units, static fn (array $u): bool => ($u['unit_kind'] ?? '') !== 'common'));
    }

    private function functionalFieldApplies(array $unit, string $recordType, string $key): bool
    {
        $type = $this->first($unit['property_type'] ?? '', $recordType);
        return in_array($key, AppraisalFunctionalVariableCatalog::profile($type), true);
    }

    private function unitName(array $unit): string
    {
        return $this->first($unit['label'] ?? '', (($unit['unit_kind'] ?? '') === 'annex' ? 'Anexo ' : 'Unidad ') . (int) ($unit['unit_index'] ?? 0));
    }

    private function m2(mixed $value): string
    {
        return $this->text($value) !== '' ? rtrim(rtrim(number_format((float) $value, 2, ',', '.'), '0'), ',') . ' m²' : 'pendiente';
    }

    private function meters(mixed $value): string
    {
        return $this->text($value) !== '' ? rtrim(rtrim(number_format((float) $value, 2, ',', '.'), '0'), ',') . ' m' : 'pendiente';
    }

    private function label(mixed $value): string { return str_replace('_', ' ', $this->text($value)); }
    private function first(mixed ...$values): string { foreach ($values as $v) if ($this->text($v) !== '') return $this->text($v); return ''; }
    private function text(mixed $value): string { return trim(preg_replace('/\s+/u', ' ', (string) $value) ?? ''); }
}
