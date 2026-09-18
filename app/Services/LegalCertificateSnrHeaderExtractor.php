<?php
declare(strict_types=1);
namespace App\Services;

final class LegalCertificateSnrHeaderExtractor
{
    public function extract(string $text): array
    {
        $flat = preg_replace('/\s+/u', ' ', $text) ?? $text;
        $data = [
            'matricula_inmobiliaria' => $this->code($this->match($text, [
                '/Nro\s+Matr(?:i|í|\?)cula\s*:\s*([0-9]{2,4}\s*-\s*[0-9]{3,})/iu',
                '/MATRICULA\s+INMOBILIARIA.*?([0-9]{2,4}\s*-\s*[0-9]{3,})/isu',
            ])),
            'turno' => $this->clean($this->match($text, ['/TURNO\s*:\s*([A-Za-z0-9\-\/\.]{3,50})/iu'])),
            'pin' => $this->clean($this->match($text, ['/Pin\s+No\s*:\s*([A-Za-z0-9\-]{4,40})/iu'])),
            'fecha_expedicion' => $this->clean($this->match($text, ['/Impreso\s+el\s+([^\n\r]{8,80})/iu'])),
            'fecha_apertura' => $this->clean($this->match($text, ['/FECHA\s+APERTURA\s*:\s*([0-9\/\-]{8,20})/iu'])),
            'estado_folio' => $this->clean($this->match($text, ['/ESTADO\s+DEL\s+FOLIO\s*:\s*\R?\s*(ACTIVO|CERRADO|CANCELADO|ABIERTO)/iu'])),
            'tipo_predio' => $this->clean($this->match($text, ['/Tipo\s+Predio\s*:\s*([^\n\r]{3,80})/iu'])),
            'area' => $this->number($this->match($text, ['/area\s+de\s+([0-9\.,]{1,30})\s*M2/iu'])),
            'coeficiente' => $this->clean($this->match($text, ['/COEFICIENTE\s*:?\s*([0-9\.,]+%?)/iu'])),
            'matricula_matriz' => $this->code($this->match($flat, ['/MATRICULA ABIERTA CON BASE EN LA.*?([0-9]{2,4}\s*-\s*[0-9]{3,})/iu'])),
            'cabida_linderos' => $this->block($text, 'DESCRIPCION: CABIDA Y LINDEROS', ['COMPLEMENTACION:', 'DIRECCION DEL INMUEBLE']),
            'direccion' => $this->address($text),
        ];
        if (preg_match('/CIRCULO\s+REGISTRAL\s*:\s*(.*?)\s+DEPTO\s*:\s*(.*?)\s+MUNICIPIO\s*:\s*(.*?)\s+VEREDA\s*:\s*([^\n\r]+)/iu', $flat, $m)) {
            $data['circulo_registral'] = $this->clean((string) $m[1]);
            $data['orip'] = $data['circulo_registral'];
            $data['departamento'] = $this->clean((string) $m[2]);
            $data['municipio'] = $this->clean((string) $m[3]);
            $data['vereda'] = $this->clean((string) $m[4]);
        }
        return array_filter($data, static fn (string $value): bool => trim($value) !== '');
    }

    private function address(string $text): string
    {
        if (!preg_match('/DIRECCION\s+DEL\s+INMUEBLE\s*(.+?)(?:DETERMINACION\s+DEL\s+INMUEBLE|MATRICULA\s+ABIERTA|ANOTACION\s*:|$)/isu', $text, $m)) return '';
        $value = preg_replace('/Tipo\s+Predio\s*:\s*[^\n\r]+/iu', ' ', (string) $m[1]) ?? (string) $m[1];
        $value = preg_replace('/OFICINA DE REGISTRO[\s\S]+$/iu', ' ', $value) ?? $value;
        $value = preg_replace('/^\s*\d+\s+/u', '', $value) ?? $value;
        return $this->clean($value);
    }

    private function block(string $text, string $startLabel, array $endLabels): string
    {
        $pos = mb_stripos($text, $startLabel);
        if ($pos === false) return '';
        $chunk = mb_substr($text, (int) $pos, 2400);
        foreach ($endLabels as $label) {
            $end = mb_stripos($chunk, $label);
            if ($end !== false) $chunk = mb_substr($chunk, 0, (int) $end);
        }
        return $this->clean($chunk);
    }

    private function match(string $text, array $patterns): string
    {
        foreach ($patterns as $pattern) if (preg_match($pattern, $text, $m)) return trim((string) ($m[1] ?? ''));
        return '';
    }

    private function clean(string $value): string
    {
        return mb_substr(trim(preg_replace('/\s+/', ' ', $value) ?? $value, " \t\n\r\0\x0B:-"), 0, 900);
    }

    private function code(string $value): string { return preg_replace('/\s+/', '', $this->clean($value)) ?? ''; }
    private function number(string $value): string { return str_replace(',', '.', $this->clean($value)); }
}
