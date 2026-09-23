<?php
declare(strict_types=1);
namespace App\Services;

final class AppraisalSectorChapterDetails
{
    public function roadState(array $sector): string
    { $state = $this->label('public_space_state', $sector['public_space_state'] ?? ''); return $state !== '' ? 'El estado de conservación del espacio público y la infraestructura vial se registra como ' . mb_strtolower($state) . '.' : 'El estado de conservación de vías principales, internas o secundarias queda pendiente de verificación.'; }

    public function urbanFurniture(array $sector, array $advanced): string
    { $a = $advanced['07'] ?? []; $parts = [];
        if (($items = $this->list($a['amoblamiento_seleccionado'] ?? [])) !== '') $parts[] = 'Amoblamiento observado: ' . $this->end($items);
        if (($facilities = $this->list($a['equipamientos_seleccionados'] ?? [])) !== '') $parts[] = 'Equipamientos presentes: ' . $this->end($facilities);
        if (($comment = $this->first($a['comentario_amoblamiento'] ?? '', $sector['nearby_facilities'] ?? '')) !== '') $parts[] = $this->end($comment);
        return $parts ? implode(' ', $parts) : $this->selectSentence('El estado del espacio público se registra como ', 'public_space_state', $sector['public_space_state'] ?? '', '.'); }

    public function stratum(array $sector, array $advanced): string
    { $a = $advanced['08'] ?? []; $stratum = $this->first($a['estrato_predominante'] ?? '', $sector['socioeconomic_profile'] ?? '');
        $base = $stratum !== '' ? 'La estratificación o perfil socioeconómico predominante se registra como ' . mb_strtolower($this->value($stratum)) . '.' : 'La estratificación socioeconómica queda pendiente de confirmar con fuente oficial o lectura del sector.';
        $comment = $this->first($a['comentario_estratificacion'] ?? '', $a['porcentajes_estrato'] ?? ''); return $comment !== '' ? $base . ' ' . $this->end($comment) : $base; }

    public function legality(array $advanced): string
    { $a = $advanced['09'] ?? []; return $this->first($a['comentario_legalidad'] ?? '', $a['observacion_legalidad'] ?? '') ?: 'La legalidad urbanística o constructiva del sector se registra como lectura general; no sustituye licencias, certificados ni verificación jurídica individual del inmueble.'; }

    public function topography(array $sector, array $advanced): string
    { $a = $advanced['10'] ?? []; return $this->first($a['comentario_topografia'] ?? '', $a['observacion_topografia'] ?? '', $sector['mitigation_notes'] ?? '') ?: 'La topografía del sector queda pendiente de descripción con relieve, pendientes, drenaje, riesgos o soporte ambiental disponible.'; }

    public function transport(array $sector, array $advanced): string
    { $a = $advanced['11'] ?? []; $base = $this->first($a['comentario_transporte'] ?? '', $sector['mobility_notes'] ?? '');
        return $base !== '' ? $this->end($base) : $this->selectSentence('El servicio de transporte público se califica como ', 'public_transport', $sector['public_transport'] ?? '', ', sujeto a validación de rutas, cobertura y frecuencia.'); }

    public function transportType(array $advanced): string
    { $a = $advanced['11'] ?? []; $types = $this->list($a['tipos_transporte_identificados'] ?? []); $service = $this->text($a['servicio_transporte_predominante'] ?? '');
        if ($types !== '') return 'El transporte público identificado incluye ' . $this->end($types); return $service !== '' ? 'El servicio predominante registrado es ' . mb_strtolower($this->value($service)) . '.' : 'Los tipos de transporte público quedan pendientes de completar.'; }

    public function transportCoverage(array $sector, array $advanced): string
    { $a = $advanced['11'] ?? []; $routes = $this->first($a['detalle_rutas_transporte'] ?? '', $sector['public_transport'] ?? ''); return $routes !== '' ? $this->end($routes) : 'El cubrimiento del transporte público queda pendiente de precisar con rutas, paraderos o corredores principales.'; }

    public function transportFrequency(array $advanced): string
    { $a = $advanced['11'] ?? []; return $this->first($a['detalle_paraderos_transporte'] ?? '') ?: 'La frecuencia del transporte público debe validarse en campo o con fuente operativa cuando incida en la accesibilidad.'; }

    public function transportQuality(array $sector): string
    { $connectivity = $this->label('connectivity', $sector['connectivity'] ?? ''); return $connectivity !== '' ? 'La conectividad urbana del sector se registra como ' . mb_strtolower($connectivity) . '.' : 'La calidad del servicio de transporte queda pendiente de calificación por accesibilidad, disponibilidad y condición de operación.'; }

    public function importantBuildings(array $sector, array $advanced): string
    { $a = $advanced['12'] ?? []; $parts = [];
        if (($anchors = $this->first($a['edificaciones_ancla'] ?? '', $sector['activity_anchors'] ?? '', $sector['nearby_facilities'] ?? '')) !== '') $parts[] = $this->end($anchors);
        if (($categories = $this->list($a['categorias_edificaciones'] ?? [])) !== '') $parts[] = 'Categorías presentes: ' . $this->end($categories);
        if (($comment = $this->first($a['comentario_edificaciones'] ?? '')) !== '') $parts[] = $this->end($comment);
        return $parts ? implode(' ', $parts) : 'Las edificaciones importantes, hitos o anclas de actividad del sector quedan pendientes de identificar.'; }

    public function buildingTypes(array $sector, array $advanced): string
    { $a = $advanced['12'] ?? []; $types = $this->first($a['comentario_edificaciones'] ?? '', $sector['daily_dynamics'] ?? '', $sector['sector_report_text'] ?? ''); return $types !== '' ? $this->end($types) : 'Los tipos de edificación del entorno deben describirse según la observación de campo, usos predominantes, altura, estado y función urbana.'; }

    public function normative(): string
    { return 'La lectura del sector se soporta en NTS I 01 para localización, entorno, servicios, usos, vías, topografía, transporte, edificaciones y demás condiciones que inciden en el inmueble; en NTS S 03 para informar alcance, fuentes, supuestos, limitaciones y salvedades; y en IVS 104 e IVS 106 como criterios de suficiencia de datos, documentación, trazabilidad y reporte. Las fuentes MIDAS, POT, cartografía, registro fotográfico y visita sirven como soporte de revisión; no reemplazan certificaciones urbanísticas, licencias ni estudios especializados cuando sean requeridos.'; }

    private function selectSentence(string $prefix, string $field, mixed $value, string $suffix): string { $label = $this->label($field, $value); return $label !== '' ? $prefix . mb_strtolower($label) . $suffix : 'Dato pendiente de completar o validar.'; }
    private function label(string $field, mixed $value): string { $key = $this->text($value); return $key === '' ? '' : (\App\Support\AppraisalSectorCatalog::options()[$field][$key] ?? $this->value($key)); }
    private function list(mixed $value): string { return is_array($value) ? implode(', ', array_map([$this, 'value'], array_filter(array_map('strval', $value)))) : $this->value($value); }
    private function value(mixed $value): string { return str_replace('_', ' ', $this->text($value)); }
    private function first(mixed ...$values): string { foreach ($values as $v) if ($this->text($v) !== '') return $this->text($v); return ''; }
    private function text(mixed $value): string { return trim(preg_replace('/\s+/u', ' ', (string) $value) ?? ''); }
    private function end(string $text): string { return rtrim($text, ' .') . '.'; }
}
