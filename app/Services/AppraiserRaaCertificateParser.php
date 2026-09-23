<?php
declare(strict_types=1);
namespace App\Services;
use App\Support\RaaCategoryCatalog;

final class AppraiserRaaCertificateParser
{
    public function parsePdf(string $path, string $sourceName): array
    {
        $text = (new LegalCertificateTextExtractor())->extract($path, 'pdf');
        if ($text === '') throw new \InvalidArgumentException('No se pudo leer texto útil del certificado RAA.');
        return $this->parse($text, $sourceName);
    }

    public function parse(string $text, string $sourceName = ''): array
    {
        $text = $this->clean($text);
        $data = [
            'full_name' => $this->name($text), 'identification_number' => $this->idNumber($text),
            'raa_number' => $this->firstMatch('/\bAVAL-\d{5,20}\b/i', $text),
            'email' => $this->firstMatch('/Correo\s+Electr[oó\?]nico:\s*([^\s]+)/iu', $text),
            'phone' => $this->firstMatch('/Tel[eé\?]fono:\s*([0-9 +()\-]{7,30})/iu', $text),
            'raa_categories' => $this->categories($text), 'raa_pin' => $this->pin($text),
            'raa_issued_at' => $this->issuedAt($text, $sourceName), 'raa_status' => $this->status($text),
        ] + $this->contact($text);
        $data['raa_expires_at'] = $data['raa_issued_at'] !== ''
            ? (new \DateTimeImmutable($data['raa_issued_at']))->modify('+30 days')->format('Y-m-d') : '';
        if ($data['full_name'] === '' || $data['identification_number'] === '' || $data['raa_number'] === '') {
            throw new \InvalidArgumentException('El certificado RAA no permitió identificar nombre, cédula y número AVAL.');
        }
        if ($data['raa_categories'] === []) throw new \InvalidArgumentException('El certificado RAA no reporta categorías autorizadas legibles.');
        return $data;
    }

    private function clean(string $text): string
    {
        $text = str_replace("\0", '', $text);
        $text = preg_replace('/[ \t]+/u', ' ', $text) ?? $text;
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;
        return trim($text);
    }

    private function name(string $text): string
    {
        if (preg_match('/se.{0,3}or\s+a.{0,3}\s+([A-ZÁÉÍÓÚÑ ]{5,120}),?\s+identificado/iu', $text, $m)) {
            return $this->title($m[1]);
        }
        return '';
    }

    private function idNumber(string $text): string
    {
        return $this->firstMatch('/C[eé\?]dula\s+de\s+ciudadan[ií\?]a\s+No\.\s*([0-9.]+)/iu', $text)
            ?: $this->firstMatch('/identificado.*?No\.\s*([0-9.]+)/iu', $text);
    }

    private function contact(string $text): array
    {
        $city = $this->firstMatch('/Ciudad:\s*([^,]+),\s*([^D]{2,100})\s+Direcci[oó\?]n:/iu', $text);
        $department = '';
        if (preg_match('/Ciudad:\s*([^,]+),\s*(.*?)\s+Direcci[oó\?]n:/iu', $text, $m)) {
            $city = $this->title($m[1]);
            $department = $this->department($m[2]);
        }
        return [
            'raa_contact_city' => $city,
            'raa_contact_department' => $department,
            'raa_contact_address' => $this->title($this->firstMatch('/Direcci[oó\?]n:\s*(.*?)\s+Tel[eé\?]fono:/iu', $text)),
        ];
    }

    private function categories(string $text): array
    {
        preg_match_all('/Categor[ií\?]a\s+(\d{1,2})\b/iu', $text, $matches);
        $allowed = array_map('strval', array_keys(RaaCategoryCatalog::all()));
        return array_values(array_unique(array_filter(array_map('strval', $matches[1] ?? []),
            static fn (string $code): bool => in_array($code, $allowed, true))));
    }

    private function pin(string $text): string { return $this->firstMatch('/PIN\s+(?:de\s+Validaci[oó\?]n|DE\s+VALIDACI[oó\?]N)\s*:?\s*([A-Za-z0-9]+)/iu', $text); }

    private function issuedAt(string $text, string $sourceName): string
    {
        if (preg_match('/a\s+los\s+.*?\((\d{1,2})\).*?d[ií\?]as\s+del\s+mes\s+de\s+([A-Za-zÁÉÍÓÚáéíóú]+)\s+del\s+(\d{4})/iu', $text, $m)
            || preg_match('/a\s+los\s+(\d{1,2})\s+d[ií\?]as\s+del\s+mes\s+de\s+([A-Za-zÁÉÍÓÚáéíóú]+)\s+del\s+(\d{4})/iu', $text, $m)) {
            return sprintf('%04d-%02d-%02d', (int) $m[3], $this->month($m[2]), (int) $m[1]);
        }
        if (preg_match('/(20\d{2})(\d{2})(\d{2})/', $sourceName, $m)) return $m[1] . '-' . $m[2] . '-' . $m[3];
        return '';
    }

    private function status(string $text): string { return preg_match('/se\s+encuentra\s+Activo/iu', $text) ? 'Activo' : 'Por verificar'; }
    private function firstMatch(string $pattern, string $text): string { return preg_match($pattern, $text, $m) ? trim((string) ($m[1] ?? $m[0])) : ''; }
    private function title(string $text): string { return mb_convert_case(trim(preg_replace('/\s+/', ' ', $text) ?? $text), MB_CASE_TITLE, 'UTF-8'); }
    private function department(string $text): string
    {
        if (preg_match('/BOL.VAR/iu', $text)) return 'Bolívar';
        return $this->title($text);
    }
    private function month(string $name): int
    { $key = mb_strtolower($name); return ['enero'=>1,'febrero'=>2,'marzo'=>3,'abril'=>4,'mayo'=>5,'junio'=>6,'julio'=>7,'agosto'=>8,'septiembre'=>9,'setiembre'=>9,'octubre'=>10,'noviembre'=>11,'diciembre'=>12][$key] ?? 0; }
}
