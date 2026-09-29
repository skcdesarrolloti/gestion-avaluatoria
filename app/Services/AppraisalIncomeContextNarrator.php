<?php
declare(strict_types=1);
namespace App\Services;

final class AppraisalIncomeContextNarrator
{
    public function status(array $record): string
    {
        return match ((string) ($record['income_producing'] ?? '')) {
            'si' => 'Produce renta',
            'no' => 'No produce renta',
            'pendiente' => 'Pendiente por verificar',
            default => 'No informado',
        };
    }

    public function paragraph(array $record, string $subjectName): string
    {
        if ((string) ($record['income_producing'] ?? '') !== 'si') return '';
        $parts = [];
        if (($rent = $this->money($record['rent_amount'] ?? null)) !== '') {
            $period = $this->period((string) ($record['rent_period'] ?? ''));
            $parts[] = 'canon informado: ' . $rent . ($period !== '' ? ' (' . $period . ')' : '');
        }
        if (($admin = $this->money($record['ph_admin_fee_amount'] ?? null)) !== '') {
            $parts[] = 'administración PH informada: ' . $admin;
        }
        if (($vat = $this->vat((string) ($record['rent_charges_vat'] ?? ''))) !== '') $parts[] = $vat;
        $detail = $parts !== [] ? ' Se reporta ' . implode('; ', $parts) . '.' : '';
        return 'Como antecedente económico del ' . $subjectName . ', se deja constancia de que genera renta. '
            . 'Esta información se considera para comprender su comportamiento económico y la consistencia del análisis, '
            . 'sin sustituir el método principal salvo que el alcance del encargo exija una valoración por renta.'
            . $detail;
    }

    private function money(mixed $value): string
    {
        return is_numeric($value) && (float) $value > 0 ? '$' . number_format((float) $value, 0, ',', '.') : '';
    }

    private function period(string $value): string
    {
        return ['mensual' => 'mensual', 'trimestral' => 'trimestral', 'anual' => 'anual', 'otro' => 'indicada por el solicitante'][$value] ?? '';
    }

    private function vat(string $value): string
    {
        return ['si' => 'IVA: sí, el canon causa o cobra IVA', 'no' => 'IVA: no registrado en el canon informado',
            'no_aplica' => 'IVA: no aplica', 'pendiente' => 'IVA: pendiente por confirmar'][$value] ?? '';
    }
}
