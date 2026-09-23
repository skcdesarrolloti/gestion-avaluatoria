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
        $items = $this->commonItems($common, $typology);
        $text = "La copropiedad cuenta con áreas, bienes y servicios comunes de dotación {$level}.";
        if ($items) $text .= ' Entre los elementos identificados se registran ' . implode(', ', $items) . '.';
        $support = $this->usableSummary($support, 900);
        if ($support !== '') $text .= ' ' . $support;
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
