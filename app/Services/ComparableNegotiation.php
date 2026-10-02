<?php
declare(strict_types=1);
namespace App\Services;
use App\Core\HttpException;

final class ComparableNegotiation
{
    public static function fields(): array
    {
        return [
            'negotiation_discount' => ['Descuento de negociación (misma unidad del precio)', 'number', 'shared'],
            'negotiation_kind' => ['Descuento otorgado o estimado', 'choice', 'shared'],
            'negotiation_source' => ['Soporte del descuento: contacto, fecha y justificación', 'text', 'shared'],
            'negotiated_amount' => ['Valor negociado = oferta − descuento', 'calculated', 'shared'],
            'negotiation_percent' => ['Porcentaje de negociación calculado (%)', 'calculated', 'shared'],
        ];
    }
    public static function options(): array
    {
        return ['' => 'Por confirmar', 'otorgado' => 'Otorgado / transacción confirmada', 'estimado' => 'Estimado por el analista; justificar'];
    }
    public static function amount(mixed $value): ?float
    {
        $text = preg_replace('/[^\d,.-]/', '', trim((string) $value)) ?? '';
        if ($text === '') return null;
        if (strrpos($text, ',') > strrpos($text, '.')) $text = str_replace(',', '.', str_replace('.', '', $text));
        else $text = preg_match('/^\d{1,3}(\.\d{3})+$/D', $text) ? str_replace('.', '', $text) : str_replace(',', '', $text);
        return is_numeric($text) && (float) $text >= 0 ? (float) $text : null;
    }
    public static function value(array $row): ?string
    {
        $discount = trim((string) ($row['negotiation_discount'] ?? ''));
        if ($discount === '') return null;
        $offer = self::amount($row['price_amount'] ?? '');
        $deduction = self::amount($discount);
        if ($offer === null || $deduction === null || $deduction > $offer)
            throw new HttpException(422, 'Descuento: registra una oferta válida y un descuento entre cero y el precio ofertado.');
        return number_format($offer - $deduction, 2, '.', '');
    }
}
