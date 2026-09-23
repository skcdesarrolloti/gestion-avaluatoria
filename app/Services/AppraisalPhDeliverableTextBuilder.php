<?php
declare(strict_types=1);
namespace App\Services;

use App\Support\AppraisalPhCatalog;

final class AppraisalPhDeliverableTextBuilder
{
    public function build(string $name, string $label, string $assets, array $technical, array $common,
        string $support, string $level, string $typology, string $rules, string $admin, string $incidence, string $notes, string $limits): string
    {
        $intro = "El inmueble objeto de medición se localiza en {$name}, copropiedad sometida al régimen de propiedad horizontal y analizada para este avalúo como {$label}.";
        if ($assets !== '') $intro .= ' ' . $assets;
        if (($configuration = $this->configuration($technical)) !== '') $intro .= ' ' . $configuration;

        $paragraphs = [$intro, $this->typologyLens($typology, $technical), $this->commons($typology, $technical, $common, $support, $level)];
        foreach ([$rules, $admin, $incidence, $notes] as $text) {
            $text = $this->usableSummary($text);
            if ($text !== '') $paragraphs[] = $text;
        }
        $paragraphs[] = $limits . ' Esta descripción se soporta, de forma general, en la Ley 675 de 2001 para régimen de propiedad horizontal, bienes comunes, coeficientes y expensas; en el Decreto 1420 de 1998 para la lectura valuatoria de inmuebles sometidos a PH; y en NTS/IVS como criterios de suficiencia, trazabilidad, soporte y limitaciones del informe. La lectura de propiedad horizontal no constituye estudio de títulos ni certificación administrativa; organiza los soportes revisados para sustentar la incidencia técnica en el avalúo.';
        return implode("\n\n", array_values(array_filter($paragraphs)));
    }

    private function configuration(array $technical): string
    {
        $facts = $this->values(['número de pisos' => 'numero_pisos', 'sótanos' => 'numero_sotanos',
            'ascensores' => 'numero_ascensores', 'edad aproximada' => 'edad_aproximada_ph',
            'uso o destinación dominante' => 'uso_dominante', 'unidades privadas' => 'numero_unidades',
            'parqueaderos' => 'numero_parqueaderos'], $technical);
        $text = $facts ? 'La configuración registrada incluye ' . implode('; ', $facts) . '.' : '';
        $distribution = $this->clean($technical['organizacion_interna'] ?? '', 420);
        if ($distribution !== '') $text .= ($text !== '' ? ' ' : '') . 'La distribución funcional reportada indica: ' . $distribution . '.';
        return $text;
    }

    private function typologyLens(string $typology, array $technical): string
    {
        $dominant = $this->clean($technical['uso_dominante'] ?? '', 180);
        $complementary = $this->clean($technical['usos_complementarios'] ?? '', 220);
        $relation = $this->clean($technical['relacion_funcional_usos'] ?? '', 260);
        $base = match ($typology) {
            'residencial' => 'La lectura se orienta a habitabilidad, convivencia, seguridad, amenidades y sostenimiento de zonas comunes propias de vivienda.',
            'oficinas' => 'La lectura se orienta a imagen corporativa, acceso de usuarios, ascensores, parqueaderos, recepción, seguridad y administración común.',
            'comercio' => 'La lectura se orienta a flujo de público, visibilidad, horarios, parqueo, señalización, cargue liviano, residuos y reglas de operación comercial.',
            'bodegas' => 'La lectura se orienta a movilidad interna, patios, muelles, control de acceso pesado, redes, seguridad y continuidad operativa.',
            'mixto' => 'La lectura se orienta por componentes: identifica qué parte es residencial, comercial, corporativa, logística u otra, y evita comparar o concluir con un solo mercado promedio.',
            default => 'La lectura se orienta por la tipología seleccionada y debe precisarse con reglamento, visita y soportes del encargo.',
        };
        $details = [];
        if ($dominant !== '') $details[] = 'uso dominante: ' . $dominant;
        if ($complementary !== '') $details[] = 'usos complementarios: ' . $complementary;
        if ($relation !== '') $details[] = 'relación funcional: ' . $relation;
        return $base . ($details ? ' Se registra ' . implode('; ', $details) . '.' : '');
    }

    private function commons(string $typology, array $technical, array $common, string $support, string $level): string
    {
        $essential = $this->groupNames($common, 'esenciales', 7);
        $operational = $this->groupNames($common, 'soporte_operativo', 9);
        $amenities = $this->groupNames($common, 'no_esenciales', 8);
        $exclusive = $this->groupNames($common, 'uso_exclusivo', 4);
        $priority = $this->commonItems($common, $typology);
        $text = "La copropiedad cuenta con áreas, bienes y servicios comunes de dotación {$level}.";
        if ($essential) $text .= ' Los bienes comunes esenciales identificados incluyen ' . implode(', ', $essential) . ', que soportan existencia, estabilidad, acceso, redes y funcionamiento básico del edificio.';
        if ($operational) $text .= ' Como soporte operativo y técnico se registran ' . implode(', ', $operational) . ', elementos que inciden en seguridad, continuidad, movilidad interna y administración cotidiana.';
        if ($amenities) $text .= ' Además, los bienes comunes no esenciales y amenidades como ' . implode(', ', $amenities) . ' pueden fortalecer imagen, comodidad, permanencia de usuarios y deseabilidad frente a copropiedades con menor dotación.';
        if ($exclusive) $text .= ' También se observan bienes comunes de uso exclusivo o asignado: ' . implode(', ', $exclusive) . ', cuya incidencia debe asociarse al derecho o unidad correspondiente.';
        if ($priority) $text .= ' Para la tipología seleccionada se consideran especialmente relevantes ' . implode(', ', array_slice($priority, 0, 7)) . '.';
        if (($extra = $this->commonImpact($common, $technical)) !== '') $text .= ' ' . $extra;
        $dotation = $this->clean($technical['dotacion_tipologia'] ?? '', 240);
        if ($dotation !== '') $text .= ' ' . $dotation;
        return $text;
    }

    private function values(array $map, array $technical): array
    {
        $out = [];
        foreach ($map as $label => $key) {
            $value = $this->clean($technical[$key] ?? '', 120);
            if ($value !== '') $out[] = $label . ': ' . $value;
        }
        return $out;
    }

    private function commonItems(array $common, string $typology): array
    {
        $labels = AppraisalPhCatalog::commonAreas();
        $priority = array_fill_keys(AppraisalPhCatalog::typologyPriorities()[$typology] ?? [], true);
        $items = $other = [];
        foreach ($common as $key => $row) {
            $status = is_array($row) ? (string) ($row['status'] ?? '') : '';
            $notes = is_array($row) ? mb_strtolower((string) ($row['notes'] ?? '')) : '';
            if (!in_array($status, ['ok', 'warn', 'risk'], true) || str_starts_with($notes, 'no identificado')) continue;
            $label = mb_strtolower((string) ($labels[(string) $key] ?? str_replace('_', ' ', (string) $key)));
            isset($priority[(string) $key]) ? $items[] = $label : $other[] = $label;
        }
        return array_slice(array_values(array_unique(array_merge($items, $other))), 0, 14);
    }

    private function groupNames(array $common, string $group, int $limit): array
    {
        $groups = AppraisalPhCatalog::commonAreaGroups();
        $labels = AppraisalPhCatalog::commonAreas();
        $keys = array_keys($groups[$group][1] ?? []);
        $out = [];
        foreach ($keys as $key) {
            if (!$this->hasCommon($common, (string) $key)) continue;
            $out[] = mb_strtolower((string) ($labels[(string) $key] ?? str_replace('_', ' ', (string) $key)));
        }
        return array_slice(array_values(array_unique($out)), 0, $limit);
    }

    private function commonImpact(array $common, array $technical): string
    {
        $parts = [];
        if ($this->hasCommon($common, 'planta_electrica')) $parts[] = 'la planta eléctrica favorece continuidad operativa';
        if ($this->hasCommon($common, 'red_incendio')) $parts[] = 'la red contra incendio aporta seguridad y cumplimiento operativo';
        if ($this->hasCommon($common, 'cctv_control') || $this->hasCommon($common, 'vigilancia')) $parts[] = 'el control y vigilancia refuerzan percepción de seguridad';
        if ($this->multipleElevators($technical) || $this->hasCommon($common, 'ascensores')) $parts[] = 'la presencia de ascensores mejora accesibilidad y circulación vertical';
        if ($this->hasCommon($common, 'coworking_salas')) $parts[] = 'las salas comunes o coworking agregan flexibilidad de uso';
        if ($this->hasCommon($common, 'piscina') || $this->hasCommon($common, 'gimnasio')) $parts[] = 'las amenidades recreativas pueden mejorar deseabilidad residencial';
        return $parts ? ucfirst(implode('; ', array_slice($parts, 0, 4))) . '.' : '';
    }

    private function hasCommon(array $common, string $key): bool
    {
        $row = $common[$key] ?? null;
        $status = is_array($row) ? (string) ($row['status'] ?? '') : '';
        $notes = is_array($row) ? mb_strtolower((string) ($row['notes'] ?? '')) : '';
        return in_array($status, ['ok', 'warn', 'risk'], true) && !str_starts_with($notes, 'no identificado');
    }

    private function multipleElevators(array $technical): bool
    {
        return preg_match('/\b([2-9]|[1-9]\d+)\b/u', (string) ($technical['numero_ascensores'] ?? '')) === 1;
    }

    private function usableSummary(string $text, int $limit = 700): string
    {
        $text = $this->clean($text, $limit);
        foreach (['aún requieren depuración', 'requieren soporte vigente', 'completar normas pertinentes'] as $marker) {
            if (str_contains(mb_strtolower($text), $marker)) return '';
        }
        return $text;
    }

    private function clean(mixed $value, int $limit): string
    {
        $text = trim(preg_replace('/\s+/u', ' ', preg_replace('/\[[^\]]+\]/u', ' ', (string) $value) ?? '') ?? '');
        $text = trim(preg_replace('/[-_=]{2,}|\s+\|\s+|\bcontin[uú]a\b/iu', ' ', $text) ?? '', ' .;:-—');
        if ($this->contaminated($text)) return '';
        return mb_strlen($text) > $limit ? mb_substr($text, 0, max(0, $limit - 3)) . '…' : $text;
    }
    private function contaminated(string $text): bool
    {
        $fold = strtr(mb_strtolower($text), ['á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u']);
        if ($text === '' || str_contains($text, '?') || str_contains($fold, 'fq ii')) return true;
        return preg_match('/\\b(articulo|capitulo|tribunal|conciliacion|notaria|protocolizacion|antecedentes)\\b/u', $fold) === 1;
    }
}
