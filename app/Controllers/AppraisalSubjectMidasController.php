<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Core\{Http, Session};
use App\Models\{AppraisalRepository, AppraisalSubjectRepository, AppraisalUrbanNormRepository};
use App\Services\{MidasPredioSearch, UrbanNormMidasTextParser, UrbanNormMidasUsageSearch};

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
            $predio = (new MidasPredioSearch())->consult($reference);
            $urbanFields = [];
            if (!empty($predio['predio'])) {
                $this->subjects->applyMidasPredio($id, $this->user['id'], $predio['predio']);
                $this->appraisals->applyMidasAreasToFirstUnit($id, $this->user['id'], $predio['predio']);
                $urbanFields = $this->urbanFields($predio['predio']);
                $reference = (string) (($predio['predio']['usage_reference'] ?? '') ?: (($predio['predio']['cadastral_reference'] ?? '') ?: ($predio['predio']['national_cadastral_reference'] ?? $reference)));
            }
            $usage = (new UrbanNormMidasUsageSearch())->consult($reference);
            $urbanFields = array_replace($urbanFields, $usage['fields'] ?? []);
            if ($urbanFields !== []) $this->saveUrbanFields($id, $urbanFields);
            $ok = ($predio['ok'] ?? false) || ($usage['ok'] ?? false);
            Session::flash($ok ? 'subject_message' : 'subject_error', $this->consultMessage($predio, $usage));
        } catch (\Throwable $error) { Session::flash('subject_error', $error->getMessage()); }
        Http::redirect('avaluos/' . $id . '/bien-sujeto#registro');
    }

    public function process(string $id): never
    {
        $this->appraisals->find($id, $this->user['id']);
        try {
            $parsed = (new UrbanNormMidasTextParser())->parse((string) ($_POST['midas_pasted_text'] ?? ''));
            $predio = $parsed['predio']; $usage = $parsed['usage']; $raw = (string) $parsed['raw'];
            $unmapped = $parsed['unmapped'] ?? [];
            if ($raw === '' || ($predio === [] && $usage === [])) throw new \RuntimeException('Pega la lectura completa de MIDAS antes de procesarla.');
            if ($predio !== []) {
                $predio['_raw'] = $raw;
                $this->subjects->applyMidasPredio($id, $this->user['id'], $predio);
                $this->appraisals->applyMidasAreasToFirstUnit($id, $this->user['id'], $predio);
            }
            if ($usage !== [] || $predio !== []) $this->saveUrban($id, $predio, $usage, $raw);
            $this->subjects->saveMidasUnmapped($id, $this->user['id'], $this->unmappedText($unmapped));
            $message = $this->message($predio, $usage, $unmapped);
            if (Http::wantsJson()) Http::json(['ok' => true, 'message' => $message,
                'updated' => ['subject' => $this->updatedSubject($predio), 'urban' => $this->updatedUrban($usage, $predio)],
                'unmapped' => $unmapped]);
            Session::flash('subject_message', $message);
        } catch (\Throwable $error) {
            if (Http::wantsJson()) Http::json(['ok' => false, 'message' => $error->getMessage()], 422);
            Session::flash('subject_error', $error->getMessage());
        }
        Http::redirect('avaluos/' . $id . '/bien-sujeto#registro');
    }

    private function consultMessage(array $predio, array $usage): string
    {
        $messages = [];
        if (($predio['ok'] ?? false)) $messages[] = 'ficha Predios guardada en el numeral 3';
        elseif (($predio['message'] ?? '') !== '') $messages[] = (string) $predio['message'];
        if (($usage['ok'] ?? false)) $messages[] = 'Uso Suelo enviado al numeral 5';
        elseif (($usage['message'] ?? '') !== '') $messages[] = (string) $usage['message'];
        return $messages ? 'Consulta MIDAS: ' . implode('; ', $messages) . '.' : 'MIDAS no devolvió información para guardar.';
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
        if (($predio['risk'] ?? '') !== '') {
            $fields['risk_context'] = (string) $predio['risk'];
            $fields['restrictions'] = (string) $predio['risk'];
            $fields['legal_urban_affectations'] = (string) $predio['risk'];
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

    private function message(array $predio, array $usage, array $unmapped = []): string
    {
        $message = $predio !== [] && $usage !== []
            ? 'Lectura MIDAS procesada desde el numeral 3: ficha del predio actualizada y reglamentación enviada al numeral 5.'
            : ($predio !== [] ? 'Lectura MIDAS del predio guardada en el numeral 3.' : 'Reglamentación de Uso Suelo enviada al numeral 5.');
        if ($unmapped !== []) $message .= ' Quedaron ' . count($unmapped) . ' dato(s) en el registro de no actualizados para revisión del analista.';
        return $message;
    }

    private function updatedSubject(array $predio): array
    {
        return ['label' => 'Numeral 3', 'count' => count($this->fields($predio, [
            'national_cadastral_reference' => 'Número predial nacional',
            'property_registry' => 'Matrícula inmobiliaria', 'address' => 'Dirección MIDAS',
            'territory' => 'Territorio / barrio', 'locality' => 'Localidad',
            'commune_ucg' => 'UCG', 'land_use' => 'Uso de suelo', 'urban_treatment' => 'Tratamiento',
            'risk' => 'Riesgos', 'land_classification' => 'Clasificación del suelo',
            'land_area_m2' => 'Área de terreno', 'built_area_m2' => 'Área construida',
            'updated_on' => 'Fecha de actualización',
        ])), 'fields' => $this->fields($predio, [
            'national_cadastral_reference' => 'Número predial nacional',
            'property_registry' => 'Matrícula inmobiliaria', 'address' => 'Dirección MIDAS',
            'territory' => 'Territorio / barrio', 'locality' => 'Localidad',
            'commune_ucg' => 'UCG', 'land_use' => 'Uso de suelo', 'urban_treatment' => 'Tratamiento',
            'risk' => 'Riesgos', 'land_classification' => 'Clasificación del suelo',
            'land_area_m2' => 'Área de terreno', 'built_area_m2' => 'Área construida',
            'updated_on' => 'Fecha de actualización',
        ])];
    }

    private function updatedUrban(array $usage, array $predio): array
    {
        $fields = $this->fields(array_replace($predio, $usage), [
            'use_principal_text' => 'Uso principal', 'use_compatible_text' => 'Uso compatible',
            'use_complementary_text' => 'Uso complementario', 'use_restricted_text' => 'Uso restringido',
            'use_prohibited_text' => 'Uso prohibido', 'norm_unit_basic_text' => 'Unidad básica',
            'norm_free_area_text' => 'Área libre', 'norm_min_lot_front_text' => 'Área y frente mínimos',
            'norm_max_height_text' => 'Altura máxima', 'norm_construction_index_text' => 'Índice de construcción',
            'occupancy_index' => 'Índice de ocupación calculado', 'norm_isolation_text' => 'Aislamientos',
            'land_use' => 'Uso de suelo base', 'urban_treatment' => 'Tratamiento base',
        ]);
        return ['label' => 'Numeral 5', 'count' => count($fields), 'fields' => $fields];
    }

    private function fields(array $data, array $labels): array
    {
        $out = [];
        foreach ($labels as $key => $label) {
            $value = trim((string) ($data[$key] ?? ''));
            if ($value !== '') $out[] = ['label' => $label, 'value' => mb_substr($value, 0, 220)];
        }
        return $out;
    }

    private function unmappedText(array $items): string
    {
        if ($items === []) return '';
        return implode("\n\n", array_map(static fn (array $item): string => trim(
            (string) ($item['section'] ?? 'Dato MIDAS') . ' - ' . (string) ($item['label'] ?? '')
            . ":\n" . (string) ($item['value'] ?? '')), $items));
    }
}

