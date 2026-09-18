<?php
declare(strict_types=1);
namespace App\Services;

final class LegalCertificateAnnotationExtractor
{
    public function extract(string $text): array
    {
        $safe = function_exists('iconv') ? (@iconv('UTF-8', 'UTF-8//IGNORE', $text) ?: $text) : $text;
        $first = preg_match('/^\s*anotaci(?:o|ó|\?|Ã³)n\s*:/imu', $safe, $m, PREG_OFFSET_CAPTURE)
            ? (int) $m[0][1] : false;
        foreach (['/\bNRO\s+TOTAL\s+DE\s+ANOTACIONES\b/iu', '/\bSALVEDADES\b/iu'] as $limit) {
            if (preg_match($limit, $safe, $m, PREG_OFFSET_CAPTURE) && ($first === false || (int) $m[0][1] > $first)) {
                $safe = substr($safe, 0, (int) $m[0][1]);
            }
        }
        if (!preg_match_all('/^\s*anotaci(?:o|ó|\?|Ã³)n\s*:?\s*(?:nro|no|num(?:ero)?|n[uú]mero)?\.?\s*:?\s*\d+/imu',
            $safe, $matches, PREG_OFFSET_CAPTURE)) return [];
        $rows = [];
        foreach ($matches[0] as $index => $match) {
            $start = (int) $match[1];
            $end = isset($matches[0][$index + 1][1]) ? (int) $matches[0][$index + 1][1] : strlen($safe);
            $block = $this->cleanBlock(substr($safe, $start, $end - $start));
            if ($block === '') continue;
            $rows[] = [
                'orden' => $this->match($block, ['/anotaci(?:o|ó|\?|Ã³)n\s*:?\s*(?:nro|no|num(?:ero)?|n[uú]mero)?\.?\s*:?\s*(\d+)/iu'])
                    ?: (string) ($index + 1),
                'fecha' => $this->match($block, ['/fecha\s*[:#]?\s*([0-9\/\-]{8,20})/iu']),
                'documento' => $this->clean($this->document($block)),
                'valor' => $this->value($block),
                'texto' => mb_substr($block, 0, 1800),
            ];
        }
        return $rows;
    }

    private function cleanBlock(string $block): string
    {
        $patterns = [
            '/OFICINA DE REGISTRO DE INSTRUMENTOS PUBLICOS[^\n\r]*CERTIFICADO DE TRADICION[^\n\r]*/iu',
            '/La validez de este documento podr[aá] verificarse en la p[aá]gina certificados\.supernotariado\.gov\.co/iu',
            '/Certificado generado con el Pin No:\s*[0-9\-]+/iu',
            '/Nro Matr(?:i|í|\?)cula:\s*[0-9\-]+/iu',
            '/Pagina\s+\d+\s+TURNO:\s*[0-9\-]+/iu',
            '/Impreso el\s+[^\n\r"]+/iu',
            '/"ESTE CERTIFICADO REFLEJA LA SITUACION JURIDICA DEL INMUEBLE\s+HASTA LA FECHA Y HORA DE SU EXPEDICION"/iu',
            '/SUPERINTENDENCIA DE NOTARIADO Y REGISTRO LA GUARDA DE LA FE PUBLICA/iu',
        ];
        $clean = trim($block);
        foreach ($patterns as $pattern) $clean = preg_replace($pattern, ' ', $clean) ?? $clean;
        $clean = preg_replace('/Doc\s*\.\s*:/iu', 'Doc:', $clean) ?? $clean;
        $clean = preg_replace('/Doc\s+(OFICIO|RESOLUCION|AUTO|SENTENCIA|ESCRITURA|ACTA|CONTRATO|CARTA|CESION|DOCUMENTO|OTRO)\b/iu', 'Doc: $1', $clean) ?? $clean;
        return $this->clean($clean);
    }

    private function document(string $block): string
    {
        if (preg_match('/doc\s*\.?\s*:\s*(.+?)(?=\s+valor\s+acto|\s+se\s+cancela\s+anotaci|\s+especificaci|\s+personas\s+que\s+intervienen|$)/isu', $block, $m)) {
            return trim((string) ($m[1] ?? ''));
        }
        if (preg_match('/\b(?:OFICIO|RESOLUCION|AUTO|SENTENCIA|ESCRITURA|ACTA|CONTRATO|CARTA|CESION|DOCUMENTO)\s+[A-Z0-9][\s\S]+?(?=\s+VALOR\s+ACTO|\s+ESPECIFICACION|\s+PERSONAS\s+QUE\s+INTERVIENEN|$)/iu', $block, $m)) {
            return trim((string) ($m[0] ?? ''));
        }
        return '';
    }

    private function value(string $block): string
    {
        $normal = $this->clean($block);
        if (preg_match('/valor\s+acto\s*:\s*\$?\s*0(?:[.,]0+)?\b/iu', $normal)) return '0';
        if (preg_match('/valor\s+acto\s*:\s*\$?\s*([0-9][0-9\.,\s]{1,60}?)(?=\s+especificaci|\s+personas\s+que\s+intervienen|\s+se\s+cancela\s+anotaci|\s+anotaci|$)/iu', $normal, $m)) {
            $value = preg_replace('/[^0-9\.,]/', '', preg_replace('/\s+/', '', (string) ($m[1] ?? '')) ?? '') ?? '';
            if ($value !== '') return $value;
        }
        return '';
    }

    private function match(string $text, array $patterns): string
    {
        foreach ($patterns as $pattern) if (preg_match($pattern, $text, $m)) return trim((string) ($m[1] ?? ''));
        return '';
    }

    private function clean(string $value): string
    {
        return trim(preg_replace('/\s+/', ' ', $value) ?? $value);
    }
}
