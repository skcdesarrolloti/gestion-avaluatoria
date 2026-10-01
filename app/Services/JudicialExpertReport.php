<?php
declare(strict_types=1);
namespace App\Services;
use App\Core\HttpException;
final class JudicialExpertReport
{
    public static function build(array $expert, array $profile, array $case, array $automatic): array
    {
        $date = $case['report_date'] ?? ''; $history = array_merge($profile['history'] ?? [], $automatic);
        $publications = array_values(array_filter(self::recent($history, 'publication', $date, 10),
            static fn ($row) => in_array(self::publicationKey($row), $case['publication_refs'] ?? [], true)));
        $cases = self::recent($history, 'case', $date, 4);
        $reviewed = ($case['history_reviewed'] ?? '') === 'si' && !self::missingPublications($profile, $case);
        $sections = [
            '1.12' => ['Identidad del perito y participantes (226.1)', trim(($expert['full_name'] ?? '') . ' · Identificación: ' . ($expert['identification_number'] ?? '') . "\n" . ($case['participants'] ?? ''))],
            '1.13' => ['Datos de localización del perito (226.2)', ($profile['contact'] ?? '') ?: 'Pendiente de completar y verificar.'],
            '1.14' => ['Profesión, idoneidad y experiencia (226.3)', trim(($profile['profession'] ?? '') . "\n" . ($profile['experience'] ?? '') . "\nSoportes: " . ($profile['credentials'] ?? ''))],
            '1.15' => ['Publicaciones relacionadas con la materia (226.4)', self::list($publications, $reviewed, 'publicaciones', $date, 10)],
            '1.16' => ['Designaciones y participación en dictámenes (226.5)', self::list($cases, $reviewed, 'casos', $date, 4)],
            '1.17' => ['Procesos de la misma parte o apoderado (226.6)', self::statement($case, 'same_party', 'Declara designaciones por la misma parte o apoderado.', 'Declara no haber sido designado por la misma parte ni por el mismo apoderado.')],
            '1.18' => ['Declaración de causales del artículo 50 (226.7)', self::statement($case, 'exclusion', 'Declara circunstancias relacionadas con causales del artículo 50; requiere revisión.', 'Declara no encontrarse incurso en las causales del artículo 50, en lo pertinente.')],
            '1.19' => ['Métodos e investigaciones (226.8 y 226.9)', "Métodos empleados:\n" . ($case['methods'] ?? '') . "\n\nFrente a peritajes anteriores (226.8):\n" . self::methods($case, 'previous') . "\n\nFrente al ejercicio habitual (226.9):\n" . self::methods($case, 'regular')],
            '1.20' => ['Documentos e información de fundamento (226.10)', ($case['documents'] ?? '') ?: 'Pendiente de relacionar y adjuntar.'],
            'juramento' => ['Independencia, convicción e imparcialidad (226 y 235)', ($case['independence'] ?? '') ?: 'Declaración pendiente de revisión y firma del perito.'],
        ];
        return $sections;
    }
    public static function recent(array $history, string $kind, string $date, int $years): array
    {
        if ($date === '') return [];
        $from = (new \DateTimeImmutable($date))->modify("-$years years")->format('Y-m-d');
        return array_values(array_filter($history, static fn ($r) => ($r['kind'] ?? 'case') === $kind &&
            ($r['date'] ?? '') >= $from && ($r['date'] ?? '') <= $date));
    }
    private static function list(array $rows, bool $reviewed, string $label, string $date, int $years): string
    {
        if (!$reviewed || $date === '') return 'Historial pendiente de revisión por el perito; no se declara ausencia de antecedentes.';
        if (!$rows) return $label === 'publicaciones'
            ? "El perito declara no tener publicaciones relacionadas con esta materia en los últimos $years años, a fecha $date."
            : "El perito declara no haber sido designado ni haber participado en la elaboración de dictámenes periciales en los últimos $years años, a fecha $date.";
        return "Relación a fecha $date (últimos $years años):\n" . implode("\n\n", array_map(static fn ($r) =>
            ($r['date'] ?? '') . ' · ' . ($r['title'] ?? $r['docket'] ?? '') . "\n" .
            implode("\n", array_filter([isset($r['court']) && $r['court'] !== '' ? 'Despacho: ' . $r['court'] : '',
            isset($r['parties']) && $r['parties'] !== '' ? 'Partes: ' . $r['parties'] : '',
            isset($r['lawyers']) && $r['lawyers'] !== '' ? 'Apoderados: ' . $r['lawyers'] : '',
            'Materia: ' . ($r['matter'] ?? ''), 'Referencia: ' . ($r['reference'] ?? $r['receipt'] ?? '')])), $rows));
    }
    private static function statement(array $r, string $key, string $yes, string $no): string
    {
        return match ($r[$key] ?? '') { 'si' => $yes, 'no' => $no, default => 'Declaración pendiente.' }
            . "\n" . ($r[$key . '_detail'] ?? '');
    }
    private static function methods(array $r, string $key): string
    {
        return match ($r[$key] ?? '') { 'si' => 'Declara que son diferentes. Justificación:',
            'no' => 'Declara que no son diferentes.', 'sin_anteriores' => 'Declara no tener peritajes anteriores sobre la misma materia.',
            default => 'Declaración pendiente.' } . "\n" . ($r[$key . '_detail'] ?? '');
    }
    public static function validatePresentation(array $expert, array $profile, array $case): void
    {
        foreach (['court' => 'juzgado', 'docket' => 'radicado', 'parties' => 'partes', 'lawyers' => 'apoderados', 'matter' => 'materia', 'report_date' => 'fecha del dictamen', 'participants' => 'participantes', 'methods' => 'métodos', 'documents' => 'documentos', 'independence' => 'independencia', 'receipt' => 'constancia de presentación'] as $key => $label)
            if (trim($case[$key] ?? '') === '') throw new HttpException(422, "Completa el campo: $label. Usa «No aplica» con explicación cuando corresponda.");
        foreach (JudicialExpertInput::PROFILE as $key) if (trim($profile[$key] ?? '') === '') throw new HttpException(422, 'Completa contacto, profesión, experiencia y soportes en el maestro del perito.');
        foreach (['directed', 'history_reviewed', 'annexes_reviewed'] as $key) if (($case[$key] ?? '') !== 'si') throw new HttpException(422, 'Confirma destinatario judicial, revisión del historial y anexos.');
        foreach (['same_party', 'exclusion', 'previous', 'regular'] as $key) {
            if (($case[$key] ?? '') === '') throw new HttpException(422, 'Completa las declaraciones de los numerales 6 a 9.');
            if (($case[$key] ?? '') === 'si' && trim($case[$key . '_detail'] ?? '') === '') throw new HttpException(422, 'Explica las designaciones, causales o variaciones declaradas.');
        }
        if (($case['exclusion'] ?? '') !== 'no') throw new HttpException(422, 'La declaración del artículo 50 requiere resolver las causales antes de registrar la presentación.');
        if (self::missingPublications($profile, $case)) throw new HttpException(409, 'Cambió una publicación seleccionada. Actualiza la vista y revisa la selección antes de registrar.');
        foreach ($profile['history'] ?? [] as $row) {
            foreach (($row['kind'] ?? '') === 'case' ? ['date', 'court', 'parties', 'lawyers', 'matter'] : ['date', 'title', 'matter', 'reference'] as $key)
                if (trim($row[$key] ?? '') === '') throw new HttpException(422, 'Completa fechas y datos de los antecedentes del maestro antes de confirmar el historial.');
        }
        if (empty($expert['identification_number'])) throw new HttpException(422, 'Falta identificación del perito en el maestro RAA.');
    }
    public static function publicationKey(array $row): string
    { return hash('sha256', ($row['date'] ?? '') . '|' . ($row['title'] ?? '') . '|' . ($row['reference'] ?? '')); }
    private static function missingPublications(array $profile, array $case): bool
    {
        $keys = array_map(self::publicationKey(...), array_filter($profile['history'] ?? [], static fn ($r) => ($r['kind'] ?? '') === 'publication'));
        return (bool) array_diff($case['publication_refs'] ?? [], $keys);
    }
}
