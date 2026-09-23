<?php
declare(strict_types=1);
namespace App\Services;
use App\Support\AppraisalCatalog;

final class AppraisalChapterOneReport
{
    public function build(array $record, array $subject, array $units): array
    {
        $sections = [
            ['1.1 Solicitud del avalúo', $this->request($record)],
            ['1.2 Razón social e identificación del solicitante', $this->requester($record)],
            ['1.3 Encargo valuatorio', $this->assignment($record, $subject, $units)],
            ['1.3.1 Identificación del activo objeto del estudio', $this->asset($record, $subject, $units)],
            ['1.3.2 Derechos o intereses objeto de valuación', $this->rights($record)],
            ['1.3.3 Uso que se pretende dar a la valuación', $this->intendedUse($record)],
            ['1.3.4 Base o tipo de valor', $this->valueBasis($record)],
            ['1.3.5 Fecha de aplicación de la estimación del valor', $this->valueDate($record)],
            ['1.3.6 Ámbito o amplitud de la valuación y del informe', $this->scope($record)],
            ['1.3.7 Condiciones contingentes o restrictivas', $this->limitations($record)],
            ['1.4 Localización y dirección del inmueble', $this->location($record, $subject, $units)],
            ['1.5 Objeto del avalúo', $this->object($record)],
            ['1.6 Destinatario de la valuación', $this->recipient($record)],
            ['1.7 Tipo de avalúo', $this->type($record)],
            ['1.8 Tipo de derecho, mueble o inmueble', $this->assetRight($record)],
            ['1.9 Destinación actual del inmueble', $this->destination($record)],
            ['1.10 Fechas', $this->dates($record)],
            ['1.11 Documentos aportados o insumos', $this->documents($record)],
            ['Soporte normativo aplicado al capítulo 1', $this->normative($record)],
        ];
        return ['sections' => $sections, 'text' => $this->plainText($sections)];
    }

    private function request(array $r): string
    {
        $requester = $this->first($r['requester_name'] ?? '', $r['client_name'] ?? 'el solicitante');
        return 'El presente estudio técnico de avalúo fue solicitado por ' . $this->end($requester);
    }
    private function requester(array $r): string
    {
        $name = $this->first($r['client_name'] ?? '', $r['requester_name'] ?? 'solicitante pendiente de precisar');
        $id = $this->text($r['requester_identification'] ?? '');
        return 'Razón social o nombre del solicitante: ' . $this->end($name) . ($id !== '' ? ' Identificación: ' . $id . '.' : ' Identificación pendiente de soporte o diligenciamiento.');
    }
    private function assignment(array $r, array $s, array $u): string
    {
        if ($this->text($r['assignment_scope'] ?? '') !== '') return $this->text($r['assignment_scope']);
        return 'De acuerdo con la solicitud, el encargo valuatorio consiste en realizar el avalúo del activo identificado, con el fin de establecer ' . mb_strtolower($this->basisLabel($r)) . ' para un inmueble con destinación ' . mb_strtolower($this->labelFor('destinacion', $r['destinacion'] ?? 'por definir')) . '.';
    }
    private function asset(array $r, array $s, array $u): string
    {
        $items = $this->unitNames($u); $where = $this->locationText($r, $s);
        $asset = $items ? implode(', ', $items) : $this->first($s['subject_title'] ?? '', $r['titulo'] ?? 'bien objeto de avalúo');
        return 'El bien objeto del avalúo se identifica como ' . $asset . ($where !== '' ? ', localizado en ' . $where : '') . '.';
    }
    private function rights(array $r): string { return 'El análisis valuatorio se orienta a estimar el valor asociado a ' . mb_strtolower($this->labelFor('tipo_derecho', $r['tipo_derecho'] ?? 'derecho pendiente de precisar')) . ' sobre el activo objeto de estudio.'; }
    private function intendedUse(array $r): string
    {
        $use = $this->text($r['intended_use'] ?? '');
        if ($use !== '') return $use;
        return 'El uso previsto del avalúo es servir como soporte técnico para ' . mb_strtolower($this->labelFor('finalidad', $r['finalidad'] ?? 'la finalidad indicada por el solicitante')) . ', conforme al alcance del encargo.';
    }
    private function valueBasis(array $r): string
    {
        $basis = $this->basisLabel($r);
        if (($r['base_valor'] ?? '') === 'mercado') return 'Los criterios empleados se fundamentan en la base de Valor de Mercado, entendida como la cuantía estimada por la que un bien podría intercambiarse en la fecha de valuación entre partes dispuestas, informadas, prudentes y sin coacción, tras una comercialización adecuada.';
        return 'Los criterios empleados se fundamentan en la base de ' . $basis . ', según la finalidad, el alcance y la información disponible del encargo.';
    }
    private function valueDate(array $r): string { return 'La fecha de aplicación de la estimación corresponde a ' . $this->date($r['value_date'] ?? '') . '.'; }
    private function scope(array $r): string { return $this->text($r['assignment_scope'] ?? '') ?: 'El informe puede ser utilizado por el destinatario para los fines indicados en el encargo, dentro del alcance técnico, documental y temporal aquí señalado.'; }
    private function limitations(array $r): string { return $this->text($r['assignment_limitations'] ?? '') ?: 'No se registran condiciones contingentes o restrictivas especiales distintas de las salvedades, soportes y limitaciones expresamente indicadas en el informe.'; }
    private function location(array $r, array $s, array $u): string { return $this->locationText($r, $s) !== '' ? 'El inmueble se localiza en ' . $this->locationText($r, $s) . '.' : 'La localización queda pendiente de precisión en la ficha del bien sujeto.'; }
    private function object(array $r): string { return 'El objeto del avalúo es estimar ' . mb_strtolower($this->basisLabel($r)) . ' del inmueble objeto de valuación, conforme a la finalidad del encargo.'; }
    private function recipient(array $r): string { return 'El destinatario de la valuación corresponde a ' . $this->end($this->first($r['report_recipient'] ?? '', $r['client_name'] ?? 'el solicitante')); }
    private function type(array $r): string { return 'Corresponde a ' . mb_strtolower($this->labelFor('tipo', $r['tipo'] ?? 'avalúo pendiente de clasificar')) . ' para un activo con destinación ' . mb_strtolower($this->labelFor('destinacion', $r['destinacion'] ?? 'por definir')) . '.'; }
    private function assetRight(array $r): string { return 'El activo corresponde a ' . mb_strtolower($this->labelFor('tipo_inmueble', $r['tipo_inmueble'] ?? 'inmueble')) . (($r['regimen_ph'] ?? '') === 'si' ? ', sometido al régimen de propiedad horizontal' : '') . '.'; }
    private function destination(array $r): string { return ucfirst(mb_strtolower($this->labelFor('destinacion', $r['destinacion'] ?? 'destinación pendiente de precisar'))) . '.'; }
    private function dates(array $r): string
    {
        return 'Fecha de solicitud: ' . $this->date($r['created_at'] ?? '') . ";\nFecha de visita o verificación: " . $this->date($r['visit_date'] ?? '') . ";\nFecha del informe: " . $this->date($r['report_date'] ?? '') . ";\nFecha de aplicación del valor: " . $this->date($r['value_date'] ?? '') . '.';
    }
    private function documents(array $r): string
    {
        $docs = $this->blockText($r['source_documents'] ?? '');
        if ($docs !== '') return $docs;
        return 'Documentos e insumos pendientes de relacionar: escritura pública, certificado de tradición y libertad, impuesto predial, documentos de identificación tributaria, reglamento de propiedad horizontal, fotografías y demás soportes aportados según aplique.';
    }
    private function normative(array $r): string
    {
        return 'Este capítulo se estructura con base en NTS S 03 y NTS I 01 para identificar solicitante, activo, derechos valuados, uso previsto, base de valor, fechas, alcance, condiciones restrictivas, información examinada y salvedades. El Decreto 1420 de 1998 soporta la identificación de localización, características físicas, jurídicas y económicas del inmueble. Las IVS se toman como referencia de alcance, base de valor, datos, supuestos, limitaciones y reporte. La información consignada organiza el encargo valuatorio y no sustituye estudio de títulos, certificación administrativa ni verificación jurídica especializada.';
    }

    private function locationText(array $r, array $s): string { return $this->first($s['adopted_address'] ?? '', $s['address'] ?? '', $r['direccion'] ?? '') . ($this->first($s['city_name'] ?? '', $r['municipio'] ?? '') !== '' ? ', ' . $this->first($s['city_name'] ?? '', $r['municipio'] ?? '') : ''); }
    private function unitNames(array $units): array { $out = []; foreach ($units as $u) if (($u['unit_kind'] ?? '') !== 'common' && $this->text($u['label'] ?? '') !== '') $out[] = $this->text($u['label']); return array_slice($out, 0, 12); }
    private function basisLabel(array $r): string { return $this->labelFor('base_valor', $r['base_valor'] ?? 'valor de mercado'); }
    private function labelFor(string $field, mixed $value): string
    {
        $key = (string) $value; return AppraisalCatalog::selectFields()[$field][4][$key] ?? str_replace('_', ' ', $key ?: 'por definir');
    }
    private function date(mixed $value): string
    {
        $text = $this->text($value); if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})/u', $text, $m)) return 'pendiente de precisar';
        $months = ['01'=>'enero','02'=>'febrero','03'=>'marzo','04'=>'abril','05'=>'mayo','06'=>'junio','07'=>'julio','08'=>'agosto','09'=>'septiembre','10'=>'octubre','11'=>'noviembre','12'=>'diciembre'];
        return ltrim($m[3], '0') . ' de ' . ($months[$m[2]] ?? $m[2]) . ' de ' . $m[1];
    }
    private function plainText(array $sections): string { return implode("\n\n", array_map(static fn (array $s): string => $s[0] . "\n" . $s[1], $sections)); }
    private function first(mixed ...$values): string { foreach ($values as $v) if ($this->text($v) !== '') return $this->text($v); return ''; }
    private function blockText(mixed $value): string
    {
        $lines = array_map(fn (string $line): string => $this->text($line), preg_split('/\R/u', (string) $value) ?: []);
        return trim(implode("\n", array_values(array_filter($lines, static fn (string $line): bool => $line !== ''))));
    }
    private function text(mixed $value): string { return trim(preg_replace('/\s+/u', ' ', (string) $value) ?? ''); }
    private function end(string $text): string { return rtrim($text, ' .') . '.'; }
}

