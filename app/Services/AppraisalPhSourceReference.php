<?php
declare(strict_types=1);
namespace App\Services;

final class AppraisalPhSourceReference
{
    public function fromText(string $text): string
    {
        $text = $this->normalize($text);
        if ($text === '' || !preg_match('/escritura|notar[ií]a/iu', $text)) return '';
        $number = $this->match($text, '/escritura\s+(?:p[uú]blica\s*)?(?:nro\.?|no\.?|n[°ºo]\.?|n[uú]mero)?\s*(\d{2,6})/iu');
        if ($number === '') $number = $this->match($text, '/doc\s*:?\s*escritura\s+(\d{2,6})/iu');
        if ($number === '') return '';
        $date = $this->match($text, '/(?:de\s+fecha|fecha|del)\s*(\d{1,2}[-\/]\d{1,2}[-\/]\d{2,4})/iu');
        $notary = $this->notary($text);
        $source = "Escritura Pública No. {$number}" . ($date !== '' ? ' de fecha ' . $date : '');
        return $source . ($notary !== '' ? ', ' . $notary : '');
    }

    public function fromData(array $technical, array $core = []): string
    {
        foreach (['fuente_acto_ph', 'escritura_reforma', 'trazabilidad_juridica_ph', 'fuente_documental'] as $key) {
            $value = $this->fromText((string) ($technical[$key] ?? ''));
            if ($value !== '') return $value;
        }
        foreach (['regulation_document', 'reform_documents'] as $key) {
            $value = $this->fromText((string) ($core[$key] ?? ''));
            if ($value !== '') return $value;
        }
        return $this->fileLabel((string) ($technical['fuente_documental'] ?? ''));
    }

    private function notary(string $text): string
    {
        if (!preg_match('/notar[ií]a\s*(?:n[°ºo]\.?\s*)?(\d{1,3})(?:\s+de\s+|\s+)([A-ZÁÉÍÓÚÑ][A-ZÁÉÍÓÚÑ\s\.]{2,40})?/iu', $text, $m)) return '';
        $city = trim((string) ($m[2] ?? ''));
        $city = preg_replace('/\b(?:GARAJE|CON|AREA|MATRICULA|COEFICIENTE|VALOR|ESPECIFICACION)\b.*$/iu', '', $city) ?? $city;
        $city = $this->title(trim($city, ' .,;:'));
        if ($city === 'Bogota') $city = 'Bogotá';
        return 'Notaría ' . $m[1] . ($city !== '' ? ' de ' . $city : '');
    }

    private function fileLabel(string $text): string
    {
        $text = preg_replace('/\.(pdf|docx?|txt)$/iu', '', $this->normalize($text)) ?? '';
        $value = $this->fromText($text);
        if ($value !== '') return $value;
        if (preg_match('/(escritura\s+p[uú]blica\s*(?:n[°ºo]\.?\s*)?\d{2,6})/iu', $text, $m)) return $this->title($m[1]);
        return '';
    }

    private function normalize(string $text): string
    {
        $text = preg_replace('/\[[^\]]+\]/u', ' ', $text) ?? $text;
        return trim(preg_replace('/\s+/u', ' ', $text) ?? '');
    }

    private function match(string $text, string $pattern): string
    { return preg_match($pattern, $text, $m) ? trim((string) $m[1]) : ''; }

    private function title(string $text): string
    { return mb_convert_case(mb_strtolower($text), MB_CASE_TITLE, 'UTF-8'); }
}
