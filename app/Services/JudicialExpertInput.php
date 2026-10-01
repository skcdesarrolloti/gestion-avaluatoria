<?php
declare(strict_types=1);
namespace App\Services;
use App\Core\HttpException;
final class JudicialExpertInput
{
    public const PROFILE = ['profession', 'credentials', 'experience', 'contact'];
    public const DOSSIER = ['court', 'docket', 'parties', 'lawyers', 'matter', 'participants', 'same_party_detail',
        'exclusion_detail', 'previous_detail', 'regular_detail', 'methods', 'documents', 'independence', 'receipt'];
    public static function text(mixed $value, int $limit = 12000): string
    {
        if (!is_scalar($value) && $value !== null) throw new HttpException(422, 'Formato de campo inválido.');
        $text = trim((string) $value);
        if (mb_strlen($text) > $limit) throw new HttpException(422, 'Un campo supera el tamaño permitido; conserva el texto y redúcelo.');
        return $text;
    }
    public static function profile(array $posted): array
    {
        $data = []; foreach (self::PROFILE as $key) $data[$key] = self::text($posted[$key] ?? '');
        try { $rows = json_decode(self::text($posted['history_json'] ?? '[]', 500000), true, 12, JSON_THROW_ON_ERROR); }
        catch (\JsonException) { throw new HttpException(422, 'No se pudo leer el historial. No se guardaron cambios.'); }
        if (!is_array($rows) || !array_is_list($rows)) throw new HttpException(422, 'Historial inválido.');
        $data['history'] = [];
        foreach ($rows as $row) {
            if (!is_array($row)) throw new HttpException(422, 'Registro de historial inválido.');
            $item = [];
            foreach (['kind', 'date', 'title', 'court', 'parties', 'lawyers', 'matter', 'reference'] as $key) $item[$key] = self::text($row[$key] ?? '', 4000);
            if (!in_array($item['kind'], ['publication', 'case'], true)) throw new HttpException(422, 'Tipo de antecedente inválido.');
            if ($item['date'] !== '') self::date($item['date']);
            $data['history'][] = $item;
        }
        return $data;
    }
    public static function dossier(array $posted): array
    {
        $data = []; foreach (self::DOSSIER as $key) $data[$key] = self::text($posted[$key] ?? '');
        foreach (['directed', 'history_reviewed', 'annexes_reviewed'] as $key) $data[$key] = ($posted[$key] ?? '') === 'si' ? 'si' : '';
        foreach (['same_party', 'exclusion', 'previous', 'regular'] as $key) {
            $v = self::text($posted[$key] ?? '');
            $allowed = $key === 'previous' ? ['', 'si', 'no', 'sin_anteriores'] : ['', 'si', 'no'];
            if (!in_array($v, $allowed, true)) throw new HttpException(422, 'Selecciona una declaración válida.');
            $data[$key] = $v;
        }
        $data['report_date'] = self::text($posted['report_date'] ?? '');
        if ($data['report_date'] !== '') self::date($data['report_date']);
        $refs = $posted['publication_refs'] ?? [];
        if (!is_array($refs) || count($refs) > 1000) throw new HttpException(422, 'Selección de publicaciones inválida.');
        foreach ($refs as $ref) if (!is_string($ref) || !preg_match('/^[a-f0-9]{64}$/D', $ref)) throw new HttpException(422, 'Referencia de publicación inválida.');
        $data['publication_refs'] = array_values(array_unique($refs));
        return $data;
    }
    public static function date(string $value): void
    {
        $date = \DateTimeImmutable::createFromFormat('!Y-m-d', $value);
        if (!$date || $date->format('Y-m-d') !== $value) throw new HttpException(422, 'La fecha no es válida.');
    }
}
