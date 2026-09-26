<?php
declare(strict_types=1);
namespace App\Services;

use App\Support\AppraisalCatalog;

final class AppraisalComparableSearchGuide
{
    public function build(array $record, array $subject, array $units, array $phProfile): array
    {
        $type = $this->typeKey((string) ($record['tipo_inmueble'] ?? ''));
        $profile = $this->profile($type, $this->isPh($record, $phProfile));
        return [
            'type_label' => $this->propertyTypeLabel($type),
            'business_label' => $this->label('tipo_negocio', (string) ($record['tipo_negocio'] ?? '')),
            'right_label' => $this->label('tipo_derecho', (string) ($record['tipo_derecho'] ?? '')),
            'criteria' => $profile['criteria'],
            'avoid' => $profile['avoid'],
            'homologation' => $profile['homologation'],
            'captured' => $this->captured($record, $subject, $units, $phProfile),
        ];
    }

    private function profile(string $type, bool $isPh): array
    {
        $base = [
            'criteria' => [
                'Misma ciudad, barrio o microsector comparable, con fecha de oferta o transacción verificable.',
                'Mismo tipo de negocio: venta con venta o arriendo con arriendo.',
                'Mismo derecho o situación jurídica comparable; si cambia, documentar la diferencia.',
            ],
            'avoid' => ['No mezclar inmuebles con uso económico distinto sin justificación técnica.'],
            'homologation' => [
                'Registrar las diferencias que expliquen ajustes: área, localización, estado, vetustez, uso, acceso y soporte documental.',
            ],
        ];
        $typed = match ($type) {
            'lote' => [
                'criteria' => ['Lotes con uso normativo y potencial urbanístico equivalente.',
                    'Área de terreno, frente, fondo, forma, topografía, disponibilidad de servicios y vía de acceso semejantes.'],
                'avoid' => ['No usar habitaciones, baños, acabados interiores ni parqueaderos como criterio principal para un lote sin construcción.'],
                'homologation' => ['Si el comparable tiene construcción, separar si aporta valor o si debe tratarse como mejora no comparable.'],
            ],
            'casa', 'finca' => [
                'criteria' => ['Casas con relación lote-construcción similar, uso residencial o mixto equivalente.',
                    'Área construida, área de lote, habitaciones, baños, parqueaderos, vetustez, estado y acabados comparables.'],
                'avoid' => ['No comparar directamente con apartamentos cuando el lote propio sea un componente relevante del valor.'],
                'homologation' => ['Separar el efecto del terreno, de las mejoras y de anexos cuando el mercado los reconozca de forma distinta.'],
            ],
            'apartamento' => [
                'criteria' => ['Apartamentos en PH con área privada, piso, estrato, vetustez, estado, vista y parqueaderos equivalentes.',
                    'Copropiedades con amenidades, administración, ascensor, seguridad y planta eléctrica de alcance semejante.'],
                'avoid' => ['No asumir como anexo un parqueadero asignado sin matrícula independiente; dejarlo como atributo del sujeto o de la PH.'],
                'homologation' => ['Distinguir parqueadero privado, comunal, asignado o de uso exclusivo antes de comparar precios unitarios.'],
            ],
            'local', 'oficina', 'consultorio' => [
                'criteria' => ['Inmuebles comerciales con ubicación, visibilidad, acceso, frente, vitrina o piso comparable.',
                    'Área útil, área de apoyo, parqueaderos, PH, estado, acabados y flujo peatonal o vehicular semejantes.'],
                'avoid' => ['No mezclar locales a la calle con oficinas interiores sin ajuste por exposición comercial.'],
                'homologation' => ['Documentar diferencias por vitrina, esquina, centro comercial, piso alto, ascensor y administración.'],
            ],
            'bodega' => [
                'criteria' => ['Bodegas con altura libre, área operativa, muelles, patios, acceso de carga y uso industrial comparable.',
                    'Capacidad eléctrica, sistema contra incendio, oficinas de apoyo y estado de cubierta o pisos semejantes.'],
                'avoid' => ['No comparar con locales u oficinas si la renta o precio depende de logística, altura o operación industrial.'],
                'homologation' => ['Ajustar por altura, resistencia de piso, muelles, maniobrabilidad, zonas francas o restricciones de uso.'],
            ],
            'edificio', 'hotel' => [
                'criteria' => ['Activos integrales con unidad económica, uso predominante, ocupación y escala comparable.',
                    'Área construida, niveles, estado, renta potencial, servicios, accesos y componentes complementarios semejantes.'],
                'avoid' => ['No descomponer sin control un edificio en unidades aisladas si el mercado lo negocia como activo integral.'],
                'homologation' => ['Precisar si se compara por m2 construido, renta, habitación, unidad rentable o potencial de reconversión.'],
            ],
            'parqueadero' => [
                'criteria' => ['Parqueaderos con relación jurídica equivalente: matrícula independiente, uso exclusivo, asignado o comunal.',
                    'Ubicación interna, cubierta, facilidad de maniobra, seguridad y demanda del sector comparables.'],
                'avoid' => ['No mezclar parqueaderos independientes con cupos asignados a una unidad privada sin explicar el derecho valorado.'],
                'homologation' => ['Definir si el valor se reconoce como anexo independiente o como atributo del inmueble principal.'],
            ],
            default => [
                'criteria' => ['Inmuebles con tipología, uso, escala, localización y mercado objetivo equivalentes.'],
                'avoid' => ['No activar variables que no pertenecen a la tipología observada.'],
                'homologation' => ['Si la tipología queda pendiente, documentar los criterios usados antes de buscar comparables.'],
            ],
        };
        if ($isPh) $typed['criteria'][] = 'Condiciones de PH equivalentes: administración, zonas comunes, amenidades, ascensor, seguridad y planta eléctrica.';
        return [
            'criteria' => array_merge($base['criteria'], $typed['criteria']),
            'avoid' => array_merge($base['avoid'], $typed['avoid']),
            'homologation' => array_merge($base['homologation'], $typed['homologation']),
        ];
    }

    private function captured(array $record, array $subject, array $units, array $ph): array
    {
        $items = [];
        foreach (['neighborhood_name' => 'Barrio', 'locality_name' => 'Localidad', 'commune_ucg' => 'UCG/comuna',
            'stratum' => 'Estrato', 'subject_reference_date' => 'Fecha base del sujeto'] as $key => $label) {
            $this->add($items, $label, (string) ($subject[$key] ?? ''));
        }
        foreach (['regimen_ph' => 'Régimen PH', 'estructura_metodo' => 'Estructura del método',
            'subtipo_funcional' => 'Subtipo funcional'] as $key => $label) {
            $this->add($items, $label, $this->label($key, (string) ($record[$key] ?? '')));
        }
        $this->add($items, 'Unidades registradas', (string) count(array_filter($units,
            static fn (array $unit): bool => ($unit['unit_kind'] ?? '') !== 'common')));
        $this->add($items, 'PH / conjunto', (string) ($ph['ph_name'] ?? ''));
        return $items;
    }

    private function add(array &$items, string $label, string $value): void
    {
        $value = trim($value);
        if ($value !== '') $items[] = ['label' => $label, 'value' => $value];
    }

    private function isPh(array $record, array $ph): bool
    {
        return ($record['regimen_ph'] ?? '') === 'si' || trim((string) ($ph['ph_name'] ?? '')) !== '';
    }

    private function typeKey(string $value): string
    {
        return array_key_exists($value, AppraisalCatalog::selectFields()['tipo_inmueble'][4] ?? []) ? $value : '';
    }

    private function propertyTypeLabel(string $type): string
    {
        return $this->label('tipo_inmueble', $type) ?: 'Tipología pendiente';
    }

    private function label(string $field, string $value): string
    {
        return (string) (AppraisalCatalog::selectFields()[$field][4][$value] ?? '');
    }
}
