<?php
declare(strict_types=1);
namespace App\Services;

use App\Support\AppraisalPhCatalog;

final class AppraisalPhDeliverableTextBuilder
{
    public function build(string $name, string $label, string $assets, array $technical, array $common,
        string $support, string $level, string $rules, string $admin, string $incidence, string $notes, string $limits): string
    {
        $intro = "El inmueble objeto de medición se localiza en {$name}, copropiedad sometida al régimen de propiedad horizontal y analizada para este avalúo como {$label}.";
        if ($assets !== '') $intro .= ' ' . $assets;
        if (($configuration = $this->configuration($technical)) !== '') $intro .= ' ' . $configuration;

        $paragraphs = [$intro, $this->commons($technical, $common, $support, $level)];
        foreach ([$rules, $admin, $incidence, $notes] as $text) {
            $text = $this->usableSummary($text);
            if ($text !== '') $paragraphs[] = $text;
        }
        $paragraphs[] = $limits . ' La lectura de propiedad horizontal no constituye estudio de títulos ni certificación administrativa; organiza los soportes revisados para sustentar la incidencia técnica en el avalúo.';
        return implode("\n\n", array_values(array_filter($paragraphs)));
    }

    private function configuration(array $technical): string
    {
        $facts = $this->values(['número de pisos' => 'numero_pisos', 'sótanos' => 'numero_sotanos',
            'ascensores' => 'numero_ascensores', 'edad aproximada' => 'edad_aproximada_ph',
            'uso o destinación dominante' => 'uso_dominante', 'unidades privadas' => 'numero_unidades',
            'oficinas' => 'numero_oficinas', 'locales' => 'numero_locales',
            'parqueaderos' => 'numero_parqueaderos', 'depósitos' => 'numero_depositos'], $technical);
        $text = $facts ? 'La configuración registrada incluye ' . implode('; ', $facts) . '.' : '';
        $distribution = $this->clean($technical['organizacion_interna'] ?? '', 420);
        if ($distribution !== '') $text .= ($text !== '' ? ' ' : '') . 'La distribución funcional reportada indica: ' . $distribution . '.';
        return $text;
    }

    private function commons(array $technical, array $common, string $support, string $level): string
    {
        $items = $this->commonItems($common);
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

    private function commonItems(array $common): array
    {
        $labels = AppraisalPhCatalog::commonAreas();
        $items = [];
        foreach ($common as $key => $row) {
            $status = is_array($row) ? (string) ($row['status'] ?? '') : '';
            $notes = is_array($row) ? mb_strtolower((string) ($row['notes'] ?? '')) : '';
            if (!in_array($status, ['ok', 'warn', 'risk'], true) || str_starts_with($notes, 'no identificado')) continue;
            $items[] = mb_strtolower((string) ($labels[(string) $key] ?? str_replace('_', ' ', (string) $key)));
        }
        return array_slice(array_values(array_unique($items)), 0, 14);
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
        return mb_strlen($text) > $limit ? mb_substr($text, 0, max(0, $limit - 3)) . '…' : $text;
    }
}
