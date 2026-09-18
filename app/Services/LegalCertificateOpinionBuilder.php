<?php
declare(strict_types=1);
namespace App\Services;

final class LegalCertificateOpinionBuilder
{
    public function build(array $data, array $tradition, array $debts, array $limits, array $measures, array $ph, array $alerts): array
    {
        $activeDebts = $this->active($debts);
        $activeLimits = $this->active($limits);
        $activeMeasures = $this->active($measures);
        $closed = $this->closed(array_merge($debts, $limits, $measures));
        $folioIssue = !$this->activeFolio($data);
        $level = ($activeMeasures || $folioIssue) ? 'Crítico'
            : (($activeDebts || $activeLimits || $alerts) ? 'Atención' : 'Normal');
        $classification = $level === 'Crítico' ? 'Requiere estudio jurídico especializado'
            : ($level === 'Atención' ? 'Con alertas para revisión jurídica' : 'Sin alertas automáticas relevantes');
        $diagnosis = $this->diagnosis($level, $activeDebts, $activeLimits, $activeMeasures, $folioIssue, $closed);
        return [
            'level' => $level,
            'classification' => $classification,
            'conclusion' => $diagnosis,
            'integrated' => $this->integrated($data, $tradition, $activeDebts, $activeLimits, $activeMeasures, $closed, $ph, $alerts, $diagnosis),
        ];
    }

    private function diagnosis(string $level, array $debts, array $limits, array $measures, bool $folioIssue, array $closed): string
    {
        $closedText = $closed ? ' ' . $this->closedSample($closed) : '';
        if ($level === 'Normal') {
            return 'Situación jurídica preliminar aparentemente saneada para fines del avalúo: en la lectura automática no se observan gravámenes, limitaciones al dominio ni medidas cautelares vigentes concluyentes.' . $closedText . ' La compra o venta podría avanzar documentalmente, siempre que el analista confirme el certificado completo, identidad del titular, paz y salvo, tradición inmediata y soportes urbanísticos.';
        }
        $issues = [];
        if ($folioIssue) $issues[] = 'estado del folio no confirmado como activo';
        if ($debts) $issues[] = 'gravámenes o hipotecas por confirmar/cancelar';
        if ($limits) $issues[] = 'limitaciones al dominio o servidumbres por validar';
        if ($measures) $issues[] = 'medidas cautelares, embargos o actuaciones judiciales registradas';
        $issueText = implode(', ', $issues);
        if ($level === 'Crítico') {
            return 'Situación jurídica preliminar no saneada para cierre sin revisión especializada: se observan ' . $issueText . '.' . $closedText . ' No se recomienda perfeccionar compra, venta, hipoteca o transferencia hasta que un abogado de títulos verifique vigencia, cancelaciones, levantamientos, partes intervinientes y posibilidad real de disposición del inmueble.';
        }
        return 'Situación jurídica preliminar comercializable con condiciones: se observan ' . $issueText . '.' . $closedText . ' La operación puede estudiarse, pero antes de prometer, comprar o vender debe verificarse que las cargas estén canceladas o sean aceptables para las partes y que no impidan la libre disposición.';
    }

    private function integrated(array $data, array $tradition, array $debts, array $limits, array $measures, array $closed, array $ph, array $alerts, string $diagnosis): string
    {
        $parts = [];
        $parts[] = $this->identity($data);
        $parts[] = $this->title($data, $tradition);
        $parts[] = $this->risk('Gravámenes', $debts, 'No se observan gravámenes vigentes concluyentes en la lectura automática.');
        $parts[] = $this->risk('Limitaciones al dominio', $limits, 'No se observan limitaciones al dominio vigentes concluyentes en la lectura automática.');
        $parts[] = $this->risk('Medidas cautelares o judiciales', $measures, 'No se observan medidas cautelares vigentes concluyentes en la lectura automática.');
        if ($closed) $parts[] = $this->closedSample($closed);
        if ($ph) $parts[] = 'Propiedad horizontal: el certificado contiene referencias a régimen de propiedad horizontal, reglamento, coeficientes o unidad privada; debe cotejarse con reglamento, matrícula matriz y expensas si aplica.';
        if ($alerts) $parts[] = 'Alertas de lectura: ' . implode(' ', $alerts);
        $parts[] = 'Conclusión preliminar: ' . $diagnosis;
        return implode("\n\n", array_values(array_filter($parts)));
    }

    private function identity(array $data): string
    {
        $matricula = trim((string) ($data['matricula_inmobiliaria'] ?? ''));
        $orip = trim((string) (($data['orip'] ?? '') ?: ($data['circulo_registral'] ?? '')));
        $folio = trim((string) ($data['estado_folio'] ?? ''));
        if ($matricula === '') return 'Identificación registral: no se identificó matrícula inmobiliaria en la lectura automática.';
        return 'Identificación registral: matrícula inmobiliaria ' . $matricula . ($orip !== '' ? ', ORIP ' . $orip : '') . ($folio !== '' ? ', estado del folio ' . $folio : '') . '.';
    }

    private function title(array $data, array $tradition): string
    {
        $owner = trim((string) ($data['titular_actual'] ?? ''));
        $last = $tradition ? end($tradition) : [];
        $text = $owner !== '' ? 'Titularidad: figura como titular leido ' . $owner . '.' : 'Titularidad: debe confirmarse el titular inscrito con el folio completo.';
        if (($last['documento'] ?? '') !== '') $text .= ' Ultimo acto de tradición identificado: ' . $last['documento'] . '.';
        return $text;
    }

    private function risk(string $title, array $rows, string $empty): string
    {
        if (!$rows) return $title . ': ' . $empty;
        return $title . ': ' . count($rows) . ' hallazgo(s) activo(s) para revisión. ' . $this->sample($rows);
    }

    private function sample(array $rows): string
    {
        $items = array_slice($rows, 0, 3);
        return implode(' ', array_map(static function (array $row): string {
            $parts = ['Anotación ' . ($row['orden'] ?? '')];
            if (($row['descripcion_acto'] ?? '') !== '') $parts[] = (string) $row['descripcion_acto'];
            if (($row['documento'] ?? '') !== '') $parts[] = 'soporte ' . $row['documento'];
            return trim(implode(' - ', array_filter($parts))) . '.';
        }, $items));
    }

    private function active(array $rows): array
    {
        return array_values(array_filter($rows, static fn (array $row): bool => ($row['estado_juridico'] ?? '') !== 'solucionada'));
    }

    private function closed(array $rows): array
    {
        return array_values(array_filter($rows, static fn (array $row): bool => ($row['estado_juridico'] ?? '') === 'solucionada'
            && (($row['cancelada_por'] ?? '') !== '' || ($row['cancelacion_de'] ?? '') !== '')));
    }

    private function closedSample(array $rows): string
    {
        $items = array_slice($rows, 0, 5);
        return 'Afectaciones cerradas por pareo registral: ' . implode(' ', array_map(static function (array $row): string {
            if (($row['cancelada_por'] ?? '') !== '') {
                return 'la anotación ' . ($row['orden'] ?? '') . ' se cancela con la anotación ' . $row['cancelada_por'] . ' y queda cerrada.';
            }
            return 'la anotación ' . ($row['orden'] ?? '') . ' cancela la anotación ' . ($row['cancelacion_de'] ?? '') . '.';
        }, $items));
    }

    private function activeFolio(array $data): bool
    {
        $state = mb_strtolower(trim((string) ($data['estado_folio'] ?? '')));
        return $state === '' || str_contains($state, 'activo');
    }
}
