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
        if (($configuration = $this->configuration($technical, $typology)) !== '') $intro .= ' ' . $configuration;
        $paragraphs = [$intro, $this->sourceAttribution($technical), $this->typologyLens($typology, $technical), $this->commons($typology, $technical, $common, $support, $level)];
        foreach ([$rules, $admin, $incidence, $notes] as $text) {
            $text = $this->usableSummary($text);
            if ($text !== '') $paragraphs[] = $text;
        }
        $paragraphs[] = $limits . ' Esta descripción se soporta, de forma general, en la Ley 675 de 2001 para régimen de propiedad horizontal, bienes comunes, coeficientes y expensas; en el Decreto 1420 de 1998 para la lectura valuatoria de inmuebles sometidos a PH; y en NTS/IVS como criterios de suficiencia, trazabilidad, soporte y limitaciones del informe. La lectura de propiedad horizontal no constituye estudio de títulos ni certificación administrativa; organiza los soportes revisados para sustentar la incidencia técnica en el avalúo.';
        return implode("\n\n", array_values(array_filter($paragraphs)));
    }

    private function configuration(array $technical, string $typology): string
    {
        $facts = $this->values(['número de pisos' => 'numero_pisos', 'sótanos' => 'numero_sotanos',
            'ascensores' => 'numero_ascensores', 'edad aproximada' => 'edad_aproximada_ph',
            'uso o destinación dominante' => 'uso_dominante', 'unidades privadas' => 'numero_unidades',
            'parqueaderos' => 'numero_parqueaderos'], $technical, $typology);
        $text = $facts ? 'La configuración documental útil para el avalúo registra ' . implode('; ', $facts) . '.' : '';
        $distribution = $this->clean($technical['organizacion_interna'] ?? '', 420);
        if ($distribution !== '') $text .= ($text !== '' ? ' ' : '') . 'La distribución funcional depurada indica: ' . $distribution . '.';
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
        $text = $this->dotationPhrase($level);
        if ($essential) $text .= ' Los bienes comunes esenciales identificados incluyen ' . implode(', ', $essential) . ', que soportan existencia, estabilidad, acceso, redes y funcionamiento básico del edificio.';
        if ($operational) $text .= ' Como soporte operativo y técnico se registran ' . implode(', ', $operational) . ', elementos que inciden en seguridad, continuidad, movilidad interna y administración cotidiana.';
        if ($amenities) $text .= ' Además, los bienes comunes no esenciales y amenidades identificados —' . implode(', ', $amenities) . '— pueden fortalecer imagen, comodidad, permanencia de usuarios y deseabilidad frente a copropiedades con menor dotación.';
        if ($exclusive) $text .= ' También se observan bienes comunes de uso exclusivo o asignado: ' . implode(', ', $exclusive) . ', cuya incidencia debe asociarse al derecho o unidad correspondiente.';
        if ($priority) $text .= ' Para la tipología seleccionada se consideran especialmente relevantes ' . implode(', ', array_slice($priority, 0, 7)) . '.';
        if (($extra = $this->commonImpact($common, $technical, $typology)) !== '') $text .= ' ' . $extra;
        $dotation = $this->clean($technical['dotacion_tipologia'] ?? '', 240);
        if ($dotation !== '') $text .= ' ' . $dotation;
        return $text;
    }

    private function values(array $map, array $technical, string $typology): array
    {
        $out = [];
        foreach ($map as $label => $key) {
            $value = $this->technicalValue((string) $key, $technical[$key] ?? '', $typology);
            if ($value !== '') $out[] = $label . ': ' . $value;
        }
        return $out;
    }

    private function dotationPhrase(string $level): string
    {
        $level = trim($level);
        if ($level === '' || $level === 'por confirmar' || str_contains($level, 'sin evidencia')) return 'La copropiedad cuenta con áreas, bienes y servicios comunes identificados en los soportes revisados.';
        return "La copropiedad cuenta con áreas, bienes y servicios comunes de dotación {$level}.";
    }

    private function technicalValue(string $key, mixed $value, string $typology): string
    {
        $value = $this->clean($value, 120);
        if ($value === '' || preg_match('/\?|mencionado|por confirmar|sin evidencia|verificar vigencia/iu', $value)) return '';
        if (in_array($key, ['numero_pisos', 'numero_sotanos', 'numero_ascensores', 'numero_unidades', 'numero_parqueaderos'], true) && !preg_match('/\b\d+\b/u', $value)) return '';
        if ($key === 'numero_pisos' && $this->doubtfulFloors($value, $typology)) return '';
        return $value;
    }

    private function doubtfulFloors(string $value, string $typology): bool
    {
        if (!preg_match('/\b(\d+)\s+pisos?\b/iu', $value, $m)) return false;
        return (int) $m[1] <= 2 && in_array($typology, ['oficinas', 'comercio', 'mixto'], true);
    }

    private function commonItems(array $common, string $typology): array
    {
        $labels = AppraisalPhCatalog::commonAreas();
        $priority = array_fill_keys(AppraisalPhCatalog::typologyPriorities()[$typology] ?? [], true);
        $items = $other = [];
        foreach ($common as $key => $row) {
            if (!$this->hasCommon($common, (string) $key)) continue;
            $label = mb_strtolower((string) ($labels[(string) $key] ?? str_replace('_', ' ', (string) $key)));
            isset($priority[(string) $key]) ? $items[] = $label : $other[] = $label;
        }
        return array_slice(array_values(array_unique(array_merge($items, $other))), 0, 14);
    }

    private function groupNames(array $common, string $group, int $limit): array
    {
        $groups = AppraisalPhCatalog::commonAreaGroups(); $labels = AppraisalPhCatalog::commonAreas(); $out = [];
        foreach (array_keys($groups[$group][1] ?? []) as $key) {
            if ($this->hasCommon($common, (string) $key)) $out[] = mb_strtolower((string) ($labels[(string) $key] ?? str_replace('_', ' ', (string) $key)));
        }
        return array_slice(array_values(array_unique($out)), 0, $limit);
    }

    private function sourceAttribution(array $technical): string
    {
        $source = (new AppraisalPhSourceReference())->fromData($technical);
        return $source !== '' ? 'La descripción de la copropiedad y de sus bienes comunes se toma del soporte documental cargado: ' . $source . '; por tanto, corresponde a una lectura documentada del reglamento o escritura y no a una afirmación libre del analista.' : '';
    }

    private function commonImpact(array $common, array $technical, string $typology): string
    {
        $parts = [];
        if ($typology === 'bodegas') {
            if ($this->hasCommon($common, 'bascula')) $parts[] = 'la báscula aporta control operativo y trazabilidad de cargas';
            if ($this->hasCommon($common, 'muelles_bahias')) $parts[] = 'los muelles, bahías o rampas facilitan cargue y descargue';
            if ($this->hasCommon($common, 'patios_maniobra')) $parts[] = 'los patios de maniobra mejoran radios de giro y circulación logística';
            if ($this->hasCommon($common, 'control_acceso_pesado')) $parts[] = 'el control de acceso pesado reduce fricción operativa';
            if ($this->hasCommon($common, 'vias_internas')) $parts[] = 'las vías internas fortalecen movilidad y segregación de flujos';
        }
        if ($this->hasCommon($common, 'planta_electrica')) $parts[] = 'la planta eléctrica favorece continuidad operativa';
        if ($this->hasCommon($common, 'red_incendio')) $parts[] = 'la red contra incendio aporta seguridad y cumplimiento operativo';
        if ($this->hasCommon($common, 'cctv_control') || $this->hasCommon($common, 'vigilancia')) $parts[] = 'el control y vigilancia refuerzan percepción de seguridad';
        if ($this->multipleElevators($technical) || $this->hasCommon($common, 'ascensores')) $parts[] = 'la presencia de ascensores mejora accesibilidad y circulación vertical';
        if ($this->hasCommon($common, 'coworking_salas')) $parts[] = 'las salas comunes o coworking agregan flexibilidad de uso';
        if ($this->hasCommon($common, 'piscina') || $this->hasCommon($common, 'gimnasio')) $parts[] = 'las amenidades recreativas pueden mejorar deseabilidad residencial';
        return $parts ? ucfirst(implode('; ', array_slice($parts, 0, 7))) . '.' : '';
    }

    private function hasCommon(array $common, string $key): bool
    {
        $row = $common[$key] ?? null; $status = is_array($row) ? (string) ($row['status'] ?? '') : '';
        $notes = is_array($row) ? mb_strtolower((string) ($row['notes'] ?? '')) : '';
        return in_array($status, ['ok', 'warn', 'risk'], true) && !str_starts_with($notes, 'no identificado') && !$this->sensitiveFalsePositive($key, $notes);
    }

    private function sensitiveFalsePositive(string $key, string $notes): bool
    {
        if ($key === 'juegos' && !preg_match('/juegos? infantiles?|recreación infantil|parque infantil/iu', $notes)) return true;
        if ($key === 'canchas' && !preg_match('/cancha|zona deportiva|escenario deportivo/iu', $notes)) return true;
        if ($key === 'piscina' && !preg_match('/piscina/iu', $notes)) return true;
        if ($key === 'gimnasio' && !preg_match('/gimnasio/iu', $notes)) return true;
        return false;
    }

    private function multipleElevators(array $technical): bool
    { return preg_match('/\b([2-9]|[1-9]\d+)\b/u', (string) ($technical['numero_ascensores'] ?? '')) === 1; }

    private function sourceLabel(array $technical): string
    {
        return (new AppraisalPhSourceReference())->fromData($technical);
    }

    private function usableSummary(string $text, int $limit = 700): string
    {
        $text = $this->clean($text, $limit);
        foreach (['aún requieren depuración', 'requieren soporte vigente', 'completar normas pertinentes', 'comparar con copropiedades'] as $marker) if (str_contains(mb_strtolower($text), $marker)) return '';
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
        if ($text === '' || str_contains($text, '?') || str_contains($fold, 'fq ii') || preg_match('/\b_[a-z]/iu', $text) === 1) return true;
        return preg_match('/\b(articulo|capitulo|tribunal|conciliacion|notaria|protocolizacion|antecedentes)\b/u', $fold) === 1;
    }
}
