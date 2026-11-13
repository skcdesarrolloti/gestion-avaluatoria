<?php
declare(strict_types=1);
namespace App\Services;

final class IncomePreparationNotes
{
    public static function all(): array
    {
        return [
            'rental_scope_detail'=>['Partes incluidas o excluidas del arriendo',
                'Ej. el depósito se arrienda por separado; identifica la unidad y el soporte.', "values.rental_scope==='different'"],
            'readiness_detail'=>['Condición pendiente y soporte',
                'Ej. reparación necesaria o restricción de uso; indica el soporte y lo pendiente.', "['works','restriction'].includes(values.readiness)"],
            'occupation_source'=>['Fuente y fecha de la ocupación',
                'Ej. visita o contrato, fecha y unidad a la que corresponde; explica diferencias entre fuentes.', 'true'],
            'occupied_parts'=>['Partes arrendadas y partes con otra ocupación',
                'Identifica los espacios arrendados, vacíos o de uso propio y su soporte; no estimes porcentajes.', "['partial','rented_unspecified'].includes(occupancy)"],
            'vacant_reason'=>['Período y causa de desocupación o adecuación',
                'Describe desde cuándo y por qué, según los soportes disponibles; deja por verificar lo desconocido.', "['vacant','works'].includes(occupancy)"],
            'lease_term'=>['Vigencia, renovación y terminación conocidas',
                'Identifica inicio, vencimiento, renovaciones y condiciones de entrega; cita documento y cláusula.', 'hasLease()'],
            'lease_changes'=>['Reajustes, períodos de gracia, mejoras y condiciones especiales',
                'Describe los acuerdos y dónde consultarlos; deja pendiente lo desconocido, sin proyectar valores.', 'hasLease()'],
            'canon_inclusions'=>['Conceptos comprendidos en el canon y soporte',
                'Ej. administración y parqueadero incluidos; servicios separados. Identifica la fuente.', "['yes','partial'].includes(values.canon_scope)"],
            'cost_source'=>['Soporte, reparto y recuperación de cada gasto',
                'Relaciona cada concepto con su cláusula o documento. Explica responsabilidades repartidas y quién reintegra pagos.', 'true'],
            'history_source'=>['Período cubierto, documentos y faltantes de los antecedentes',
                'Ej. ocupación con fechas, liquidaciones de cobros o facturas; relaciona cada antecedente con su fuente.', 'true'],
            'review_notes'=>['Pendientes concretos y documento o persona a consultar',
                'Ej. confirmar si el depósito se incluye en el contrato; solicitar liquidaciones de administración.', 'true'],
        ];
    }

    public static function group(string $name): string
    {
        if (in_array($name,['scope_detail','rental_scope_detail','readiness_detail'],true)) return 'subject';
        if (in_array($name,['cost_source','history_source'],true)) return 'evidence';
        return $name==='review_notes'?'summary':'lease';
    }

    public static function parent(string $name): string
    {
        return ['scope_detail'=>'scope','occupancy_detail'=>'occupancy','support_detail'=>'support',
            'rental_scope_detail'=>'rental_scope','readiness_detail'=>'readiness','canon_inclusions'=>'canon_scope'][$name] ?? '';
    }
}
