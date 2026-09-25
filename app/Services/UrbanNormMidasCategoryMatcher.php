<?php
declare(strict_types=1);
namespace App\Services;

final class UrbanNormMidasCategoryMatcher
{
    public function slug(string $text): string
    {
        $text = $this->words($text);
        if ($text === '') return '';
        $direct = $this->directMatch($text);
        if ($direct !== '') return $direct;
        foreach (['mixto' => 'mixto', 'institucional' => 'inst', 'comercial' => 'com',
            'comercio' => 'com', 'industrial' => 'ind', 'portuario' => 'port'] as $word => $prefix) {
            $number = $this->numberAfter($text, $word);
            if ($number !== '') return $prefix . '-' . $number;
        }
        if (str_contains($text, 'turistic')) return 'tur-bocagrande-boquilla';
        if (str_contains($text, 'parcelacion')) return 'rural-parcelaciones';
        if (str_contains($text, 'agroindustrial')) return 'rural-agroindustrial';
        if (str_contains($text, 'rural') && str_contains($text, 'suburbano')) return 'rural-suburbano-turistico';
        return '';
    }

    private function directMatch(string $text): string
    {
        if (!str_contains($text, 'residencial')) return '';
        foreach (['a' => 'res-a', 'b' => 'res-b', 'c' => 'res-c', 'd' => 'res-d'] as $letter => $slug) {
            if (preg_match('/\b(tipo\s*)?' . $letter . '\b/u', $text)) return $slug;
        }
        foreach (['ra' => 'res-a', 'rb' => 'res-b', 'rc' => 'res-c', 'rd' => 'res-d'] as $code => $slug) {
            if (preg_match('/\b' . $code . '\b/u', $text)) return $slug;
        }
        return '';
    }

    private function numberAfter(string $text, string $word): string
    {
        return preg_match('/\b' . $word . '\s*(?:tipo\s*)?([1-5])\b/u', $text, $match) ? $match[1] : '';
    }

    private function words(string $value): string
    {
        $text = strtr(mb_strtolower(trim($value)), ['á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u','ü'=>'u','ñ'=>'n']);
        return trim(preg_replace('/[^a-z0-9]+/u', ' ', $text) ?? '');
    }
}
