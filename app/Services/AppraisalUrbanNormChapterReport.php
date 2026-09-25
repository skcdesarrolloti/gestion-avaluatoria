<?php
declare(strict_types=1);
namespace App\Services;

final class AppraisalUrbanNormChapterReport
{
    public function build(array $profile): array
    {
        $sections = [
            ['5 Normatividad urbana', $this->paragraph([
                'La normatividad urbana se revisa con base en la consulta predial, el POT o instrumento aplicable y los soportes disponibles al momento de la valuación.',
                $this->line('Fuente principal', $profile['source_status'] ?? ''),
            ])],
            ['5.1 Consulta MIDAS y referencia predial', $this->paragraph([
                $this->line('Referencia predial o catastral consultada', $profile['cadastral_reference'] ?? ''),
                $this->line('Opción consultada', $profile['midas_query_option'] ?? ''),
                $this->line('Fecha de consulta', $profile['midas_consulted_on'] ?? ''),
                $this->line('Resultado leído en MIDAS', $profile['midas_usage_result'] ?? ($profile['midas_result'] ?? '')),
            ])],
            ['5.2 Reglamentación del uso del suelo', $this->paragraph([
                $this->line('Estado del instrumento', $profile['pot_state'] ?? ''),
                $this->line('Clasificación del suelo', $profile['land_classification'] ?? ''),
                $this->line('Área de actividad', $profile['activity_area'] ?? ''),
                $this->line('Zona normativa', $profile['normative_zone'] ?? ''),
                $this->line('Tratamiento urbanístico', $profile['urban_treatment'] ?? ''),
            ])],
            ['5.3 Escenarios POT y mayor y mejor uso', $this->paragraph([
                $this->line('Vía normativa adoptada', $profile['adopted_normative_route_label'] ?? ''),
                $this->line('Justificación de mayor y mejor uso', $profile['highest_best_use_reason'] ?? ''),
                $this->scenarioSummary($profile),
                $this->line('Uso actual identificado', $profile['current_use'] ?? ''),
                $this->line('Uso pretendido', $profile['intended_use'] ?? ''),
                $this->line('Actividad aplicable', $profile['applicable_activity'] ?? ''),
                $this->line('Resultado del cruce', $profile['use_cross_result'] ?? ''),
                $this->line('Normas urbanísticas aplicadas', $profile['urban_norms_applied'] ?? ''),
            ])],
            ['5.4 Determinantes, restricciones y conceptos', $this->paragraph([
                $this->line('Radicado o concepto', $profile['planning_concept_number'] ?? ''),
                $this->line('Fecha del concepto', $profile['planning_concept_date'] ?? ''),
                $this->line('Alcance', $profile['official_concept_scope'] ?? ''),
                $this->line('Patrimonio o conservación', $profile['heritage_context'] ?? ''),
                $this->line('Determinantes ambientales', $profile['environmental_context'] ?? ''),
                $this->line('Riesgo o afectaciones externas', $profile['risk_context'] ?? ''),
                $this->line('Afectaciones jurídicas o urbanísticas', $profile['legal_urban_affectations'] ?? ''),
            ])],
            ['5.5 Soportes, archivos y normas', $this->paragraph([
                $this->line('Normas urbanísticas aplicadas', $profile['urban_norms_applied'] ?? ''),
                $this->line('Soportes revisados', $profile['support_summary'] ?? ''),
            ])],
            ['5.6 Conclusión urbanística adoptada', $this->paragraph([
                $this->line('Restricciones', $profile['restrictions'] ?? ''),
                $this->line('Conclusión urbanística', $profile['conclusion'] ?? ''),
                $this->line('Limitaciones de la consulta', $profile['source_limitations'] ?? ''),
            ])],
        ];
        return ['sections' => $sections, 'text' => implode("\n\n", array_map(static fn (array $s): string => $s[0] . "\n" . $s[1], $sections))];
    }

    private function scenarioSummary(array $profile): string
    {
        $rows = AppraisalUrbanNormScenarioInput::decode($profile['normative_scenarios_json'] ?? '');
        $enabled = array_filter($rows, static fn (array $row): bool => !empty($row['enabled']));
        if ($enabled === []) return '';
        $labels = array_map(static fn (array $row): string => trim($row['label'] . ' (' . $row['result'] . ')'), $enabled);
        return 'Escenarios evaluados: ' . implode(', ', $labels) . '.';
    }

    private function paragraph(array $parts): string
    {
        $parts = array_values(array_filter(array_map('trim', $parts), static fn (string $v): bool => $v !== ''));
        return $parts ? implode("\n", $parts) : 'Pendiente de diligenciar.';
    }

    private function line(string $label, mixed $value): string
    { $text = trim((string) $value); return $text === '' ? '' : $label . ': ' . $text . '.'; }
}
