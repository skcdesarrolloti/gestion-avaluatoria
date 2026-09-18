<?php
declare(strict_types=1);
namespace App\Support;

final class AppraisalPhCatalog
{
    public static function statusOptions(): array
    {
        return ['' => 'Pendiente', 'ok' => 'Verificado / adecuado', 'warn' => 'Por confirmar',
            'risk' => 'Alerta relevante', 'na' => 'No aplica'];
    }

    public static function commonAreas(): array
    {
        return [
            'porteria' => 'Portería / acceso controlado', 'lobby' => 'Lobby o recepción',
            'ascensores' => 'Ascensores', 'circulaciones' => 'Circulaciones y escaleras',
            'parqueaderos_visitantes' => 'Parqueaderos de visitantes', 'zonas_verdes' => 'Zonas verdes',
            'salon_social' => 'Salón social', 'piscina' => 'Piscina', 'gimnasio' => 'Gimnasio',
            'juegos' => 'Juegos / recreación', 'vias_internas' => 'Vías internas',
            'planta_electrica' => 'Planta eléctrica', 'tanques' => 'Tanques / bombeo',
            'red_incendio' => 'Red contra incendio', 'basuras' => 'Cuarto de basuras',
            'cerramiento' => 'Cerramiento y seguridad perimetral',
        ];
    }

    public static function documents(): array
    {
        return [
            'reglamento' => 'Reglamento de propiedad horizontal', 'reformas' => 'Reformas al reglamento',
            'paz_salvo' => 'Paz y salvo de administración', 'recibo_administracion' => 'Recibo de administración',
            'certificado_administracion' => 'Certificado de existencia / representación',
            'presupuesto' => 'Presupuesto o cuota aprobada', 'estados_financieros' => 'Estados financieros',
            'actas' => 'Actas de asamblea relevantes', 'manual_convivencia' => 'Manual de convivencia',
            'polizas' => 'Pólizas / seguros comunes', 'planos_coeficientes' => 'Planos, áreas y coeficientes',
            'mantenimiento' => 'Soportes de mantenimiento / inspección',
            'paquete_zip' => 'Paquete ZIP/RAR de soportes PH',
        ];
    }

    public static function risks(): array
    {
        return [
            'mora_expensas' => 'Mora o paz y salvo de expensas', 'cuotas_extraordinarias' => 'Cuotas extraordinarias',
            'cartera_copropiedad' => 'Cartera elevada de la copropiedad', 'conflictos' => 'Conflictos o procesos internos',
            'deterioro_comunes' => 'Deterioro de zonas comunes', 'mantenimiento_diferido' => 'Mantenimiento diferido',
            'restricciones_uso' => 'Restricciones de uso, avisos, actividad u horario',
            'seguridad' => 'Condición de seguridad / control de acceso',
            'seguros' => 'Seguros comunes por confirmar', 'afectaciones_fisicas' => 'Afectaciones físicas comunes',
        ];
    }

    public static function photos(): array
    {
        return [
            'fachada_acceso' => 'Fachada y acceso de la copropiedad', 'porteria' => 'Portería / control',
            'circulaciones' => 'Circulaciones, escaleras y ascensores', 'parqueaderos' => 'Parqueaderos y accesos',
            'zonas_comunes' => 'Zonas comunes relevantes', 'equipos' => 'Equipos, tanques, planta o red incendio',
            'mantenimiento' => 'Estado de mantenimiento o afectaciones',
            'entorno' => 'Entorno inmediato de la copropiedad',
        ];
    }

    public static function defaults(): array
    {
        return [
            'ph_key' => '', 'ph_name' => '', 'administration_name' => '', 'administration_contact' => '',
            'administration_phone' => '', 'administration_email' => '', 'matrix_registration' => '',
            'private_unit' => '', 'coefficient' => '', 'regulation_document' => '', 'reform_documents' => '',
            'monthly_fee' => '', 'fee_status' => '', 'reserve_fund' => '', 'insurance_status' => '',
            'restrictions_text' => '', 'common_areas' => [], 'documents' => [], 'risks' => [], 'photos' => [],
            'diagnosis_text' => '', 'report_text' => '', 'updated_at' => null,
        ];
    }
}
