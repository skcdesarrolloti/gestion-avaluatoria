<?php
declare(strict_types=1);
namespace App\Services;

final class UrbanOccupancyIndexEstimator
{
    public function fromTexts(string $freeArea, string $occupancyText, string $constructionIndex, string $floors): array
    {
        $direct = $this->rateFrom($occupancyText);
        if ($direct !== '') return [$direct, 'calculado desde area o indice de ocupacion informado'];
        $free = $this->rateFrom($freeArea);
        if ($free !== '') return [$this->format(max(0, 1 - (float) $free)), 'calculado como 1 menos area libre normativa'];
        $ci = $this->number($constructionIndex);
        $height = $this->number($floors);
        if ($ci !== null && $height !== null && $height > 0) return [$this->format($ci / $height), 'estimado como indice de construccion dividido por pisos'];
        return ['', ''];
    }

    public function number(string $value): ?float
    {
        if (!preg_match('/([0-9]+(?:[,.][0-9]+)?)/u', $value, $match)) return null;
        return (float) str_replace(',', '.', $match[1]);
    }

    private function rateFrom(string $text): string
    {
        if (!preg_match('/([0-9]+(?:[,.][0-9]+)?)\s*(%)?/u', $text, $match)) return '';
        $number = (float) str_replace(',', '.', $match[1]);
        if ($number >= 1 && ($match[2] ?? '') !== '%') return '';
        if (($match[2] ?? '') === '%') $number /= 100;
        return $this->format($number);
    }

    private function format(float $number): string
    {
        return rtrim(rtrim(number_format($number, 4, '.', ''), '0'), '.');
    }
}
