<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Core\{Http, Session};
use App\Models\{AppraisalRepository, AppraisalSubjectRepository, AppraisalUrbanNormRepository};
use App\Services\{UrbanNormMidasTextParser, UrbanNormMidasUsageSearch};

final class AppraisalSubjectMidasController
{
    public function __construct(private AppraisalRepository $appraisals,
        private AppraisalSubjectRepository $subjects, private AppraisalUrbanNormRepository $urban, private array $user) {}

    public function consult(string $id): never
    {
        $this->appraisals->find($id, $this->user['id']);
        $subject = $this->subjects->find($id, $this->user['id']);
        try {
            $reference = $this->reference($_POST, $subject);
            $result = (new UrbanNormMidasUsageSearch())->consult($reference);
            if (!empty($result['fields'])) $this->saveUrbanFields($id, $result['fields']);
            $key = ($result['ok'] ?? false) ? 'subject_message' : 'subject_error';
            Session::flash($key, (string) ($result['message'] ?? 'Consulta MIDAS finalizada.'));
        } catch (\Throwable $error) { Session::flash('subject_error', $error->getMessage()); }
        Http::redirect('avaluos/' . $id . '/bien-sujeto#midas');
    }

    public function process(string $id): never
    {
        $this->appraisals->find($id, $this->user['id']);
        try {
            $parsed = (new UrbanNormMidasTextParser())->parse((string) ($_POST['midas_pasted_text'] ?? ''));
            $predio = $parsed['predio']; $usage = $parsed['usage']; $raw = (string) $parsed['raw'];
            if ($raw === '' || ($predio === [] && $usage === [])) throw new \RuntimeException('Pega la lectura completa de MIDAS antes de procesarla.');
            if ($predio !== []) {
                $predio['_raw'] = $raw;
                $this->subjects->applyMidasPredio($id, $this->user['id'], $predio);
                $this->appraisals->applyMidasAreasToFirstUnit($id, $this->user['id'], $predio);
            }
            if ($usage !== [] || $predio !== []) $this->saveUrban($id, $predio, $usage, $raw);
            Session::flash('subject_message', $this->message($predio, $usage));
        } catch (\Throwable $error) { Session::flash('subject_error', $error->getMessage()); }
        Http::redirect('avaluos/' . $id . '/bien-sujeto#midas');
    }

    private function saveUrbanFields(string $id, array $fields): void
    {
        $profile = $this->urban->profile($id, $this->user['id']);
        $fields = array_filter($fields, static fn ($value): bool => trim((string) $value) !== '');
        $this->urban->save($id, $this->user['id'], (int) ($profile['version'] ?? 0), array_replace($profile, $fields));
    }

    private function saveUrban(string $id, array $predio, array $usage, string $raw): void
    {
        $profile = $this->urban->profile($id, $this->user['id']);
        $data = array_replace($profile, $usage, $this->urbanFields($predio));
        if ($predio !== []) $data['midas_predio_raw'] = $raw;
        if ($usage !== []) $data['midas_usage_raw'] = $raw;
        $data['midas_consulted'] = '1'; $data['source_status'] = 'midas';
        $this->urban->save($id, $this->user['id'], (int) ($profile['version'] ?? 0), $data);
    }

    private function reference(array $input, array $subject): string
    {
        foreach (['midas_national_cadastral_reference', 'midas_cadastral_reference', 'cadastral_reference'] as $key) {
            $digits = preg_replace('/\D+/', '', (string) ($input[$key] ?? $subject[$key] ?? '')) ?? '';
            if ($digits !== '') return $digits;
        }
        throw new \RuntimeException('Registra primero la referencia catastral en Registro y catastro del numeral 3.');
    }

    private function urbanFields(array $predio): array
    {
        $fields = [];
        foreach (['cadastral_reference_long' => 'national_cadastral_reference', 'cadastral_reference_short' => 'cadastral_reference',
            'current_use' => 'land_use', 'land_classification' => 'land_classification', 'urban_treatment' => 'urban_treatment'] as $target => $source) {
            if (($predio[$source] ?? '') !== '') $fields[$target] = (string) $predio[$source];
        }
        if (($predio['land_use'] ?? '') !== '') { $fields['midas_activity'] = (string) $predio['land_use']; $fields['midas_usage_result'] = $this->summary($predio); }
        return $fields;
    }

    private function summary(array $predio): string
    {
        $parts = [];
        foreach (['land_use' => 'Uso de suelo', 'urban_treatment' => 'Tratamiento', 'risk' => 'Riesgos',
            'land_classification' => 'Clasificación', 'updated_on' => 'Actualización'] as $key => $label) {
            if (($predio[$key] ?? '') !== '') $parts[] = $label . ': ' . $predio[$key];
        }
        return implode(' | ', $parts);
    }

    private function message(array $predio, array $usage): string
    {
        return $predio !== [] && $usage !== []
            ? 'Lectura MIDAS procesada desde el numeral 3: ficha del predio actualizada y reglamentación enviada al numeral 5.'
            : ($predio !== [] ? 'Lectura MIDAS del predio guardada en el numeral 3.' : 'Reglamentación de Uso Suelo enviada al numeral 5.');
    }
}
