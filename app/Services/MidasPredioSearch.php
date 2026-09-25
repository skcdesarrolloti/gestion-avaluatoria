<?php
declare(strict_types=1);
namespace App\Services;

final class MidasPredioSearch
{
    private const ENDPOINT = 'https://midas.cartagena.gov.co:2083/api/Search/Criterio';

    public function consult(string $reference): array
    {
        $reference = preg_replace('/\D+/', '', $reference) ?? '';
        if ($reference === '') throw new \InvalidArgumentException('Primero registra la referencia catastral en Registro y catastro.');
        $json = $this->request($reference);
        if (!is_array($json)) return ['ok' => false, 'message' => 'MIDAS no respondió la ficha Predios.', 'predio' => []];
        $error = $this->errorMessage($json);
        if ($error !== '') return ['ok' => false, 'message' => 'MIDAS no respondió la ficha Predios: ' . $error, 'predio' => []];
        $result = $this->predioResult($json);
        $info = is_array($result['data']['INFORMACION'] ?? null) ? $result['data']['INFORMACION'] : [];
        if ($info === []) return ['ok' => false, 'message' => 'MIDAS respondió, pero no devolvió la capa Predios.', 'predio' => []];
        $predio = $this->mapped($info);
        $usageReference = $this->usageReference($result);
        if ($usageReference !== '') $predio['usage_reference'] = $usageReference;
        $predio['_raw'] = $this->rawText($info);
        return ['ok' => $predio !== [], 'message' => 'Ficha Predios de MIDAS cargada en el numeral 3.', 'predio' => $predio];
    }

    public function mappedFromInfo(array $info): array
    {
        return $this->mapped($info);
    }

    private function request(string $reference): ?array
    {
        return (new MidasHttpClient())->postJson(self::ENDPOINT, ['criterio' => $reference, 'layers_visible' => []]);
    }

    private function errorMessage(array $json): string
    {
        if (mb_strtolower((string) ($json['estado'] ?? '')) !== 'error') return '';
        return trim((string) ($json['mensaje'] ?? 'Error reportado por MIDAS.'));
    }

    private function predioResult(array $json): array
    {
        foreach ($json['datos'] ?? [] as $group) foreach ($group['capas'] ?? [] as $layer) {
            if ($this->key((string) ($layer['capa'] ?? '')) !== 'predios') continue;
            foreach ($layer['resultado'] ?? [] as $result) {
                $info = $result['data']['INFORMACION'] ?? null;
                if (is_array($info)) return is_array($result) ? $result : [];
            }
        }
        return [];
    }

    private function usageReference(array $result): string
    {
        foreach ($result['tools'] ?? [] as $tool) {
            if (!is_array($tool) || $this->key((string) ($tool['type'] ?? $tool['name'] ?? '')) !== 'usosuelo') continue;
            return preg_replace('/\D+/', '', (string) ($tool['field'] ?? '')) ?? '';
        }
        return '';
    }

    private function mapped(array $info): array
    {
        $fields = ['national_cadastral_reference' => '01 Número Predial Nacional',
            'property_registry' => '02 Matrícula Inmobiliaria', 'address' => '03 Dirección',
            'territory' => '04 Territorio', 'locality' => '05 Localidad',
            'commune_ucg' => '06 Unidad Comunera De Gobierno', 'land_use' => '07 Uso De Suelo',
            'urban_treatment' => '08 Tratamiento', 'risk' => '09 Riesgos',
            'land_classification' => '10 Clasificación Del Suelo', 'dane_block_code' => '11 Código Manzana Dane',
            'dane_block_side' => '12 Lado Manzana Dane', 'block_number' => '13 Número De Manzana',
            'property_number' => '14 Número De Predio', 'stratum' => '15 Estrato Socioeconómico',
            'stratum_record' => '16 Acta Estratificación', 'stratum_atypical' => '17 Atipicidad Estratificación',
            'stratum_observation' => '18 Observación Estratificación', 'building_name' => '19 Nombre Edificación',
            'land_area_m2' => '20 Área Terreno (M2)', 'built_area_m2' => '21 Área Construida (M2)',
            'cadastral_reference' => '22 Referencia Catastral', 'updated_on' => '23 Fecha Actualización'];
        $out = [];
        foreach ($fields as $target => $label) {
            $value = $this->findValue($info, $label);
            if ($value !== '') $out[$target] = $value;
        }
        return $out;
    }

    private function rawText(array $info): string
    {
        $lines = [];
        foreach ($info as $label => $payload) {
            $value = is_array($payload) ? (string) ($payload['valor'] ?? '') : (string) $payload;
            $lines[] = trim((string) $label) . ":\n" . trim($value);
        }
        return trim(implode("\n", $lines));
    }

    private function findValue(array $info, string $label): string
    {
        $needle = $this->key($label);
        foreach ($info as $source => $payload) {
            if ($this->key((string) $source) !== $needle) continue;
            $value = is_array($payload) ? (string) ($payload['valor'] ?? '') : (string) $payload;
            $value = trim(preg_replace('/\s+/', ' ', $value) ?? '');
            return preg_match('/^-+$/', $value) ? '' : $value;
        }
        return '';
    }

    private function key(string $value): string
    {
        $text = strtr(mb_strtolower(trim($value)), ['á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u','ü'=>'u','ñ'=>'n']);
        return preg_replace('/[^a-z0-9]+/', '', $text) ?? '';
    }
}

