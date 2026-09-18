<?php
declare(strict_types=1);
namespace App\Services;

final class LegalCertificateTitleSanitizer
{
    public function valid(string $value): string
    {
        $clean = $this->clean(preg_split('/\s+(?:valor\s+acto|especificaci|personas\s+que|anotaci)/iu', $value)[0] ?? $value);
        return $this->isBad($clean) ? '' : $clean;
    }

    public function isBad(string $value): bool
    {
        $value = trim($value);
        return $value === ''
            || mb_strlen($value) < 5
            || preg_match('/\b(linderos?|cabida|[áa]rea|coeficiente|hect[aá]reas|cent[ií]metros|metros|construida|privada)\b/iu', $value)
            || preg_match('/^(y\s+)?a\s+los?\b/iu', $value)
            || !preg_match('/[A-Za-zÁÉÍÓÚÑ]{3}/u', $value);
    }

    private function clean(string $value): string
    {
        return mb_substr(trim(preg_replace('/\s+/', ' ', $value) ?? $value), 0, 500);
    }
}
