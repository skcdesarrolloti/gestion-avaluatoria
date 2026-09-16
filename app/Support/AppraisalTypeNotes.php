<?php
declare(strict_types=1);
namespace App\Support;

final class AppraisalTypeNotes
{
    public static function all(): array
    {
        return ['tipo' => [
            'comercial' => self::note('Estima valor comercial o de mercado del bien o derecho.',
                'Cuando se requiere una conclusión objetiva de intercambio probable.',
                'Puede usar mercado, renta, reposición o residual según activo y evidencia.',
                'Se elige porque el encargo requiere concluir un valor comercial bajo condiciones objetivas de mercado.'),
            'posesion' => self::note('Valora el interés económico asociado a una posesión material.',
                'Cuando no hay dominio pleno inscrito, o existen ocupación, tenencia o controversia.',
                'No equivale a valorar propiedad plena; exige advertir riesgo jurídico.',
                'Se elige porque el objeto valorado es la posesión y no el dominio completo del inmueble.'),
            'mejoras' => self::note('Valora construcciones, adecuaciones o inversiones separables del suelo.',
                'Cuando quien hizo la mejora no necesariamente es dueño del terreno.',
                'Debe separar terreno, construcción, soportes y reconocimiento jurídico.',
                'Se elige porque el encargo limita el análisis a mejoras identificables y no al predio completo.'),
            'remate' => self::note('Soporta precio base o referencia en venta forzada o subasta.',
                'Procesos ejecutivos, liquidaciones, adjudicaciones o actuaciones judiciales.',
                'Requiere fecha clara, trazabilidad reforzada y advertencia de condiciones de venta.',
                'Se elige porque el informe servirá dentro de un escenario de realización forzada o remate.'),
            'negociacion' => self::note('No es método diferente; define el uso del avalúo para pactar condiciones.',
                'Compra, venta, arriendo, renegociación, oferta o contraoferta entre partes.',
                'La base puede ser mercado, renta, reposición o residual; no impone precio final.',
                'Se elige porque el resultado será referencia técnica para negociar, sin obligar a cerrar por ese monto.'),
            'conciliacion' => self::note('Apoya una solución económica acordada entre partes.',
                'Audiencias, arreglos directos, controversias patrimoniales o conciliación.',
                'Debe explicar alcance, supuestos, fecha y límites del acuerdo.',
                'Se elige porque el informe busca facilitar una solución consensuada sobre una diferencia económica.'),
            'zona_franca' => self::note('Valora bienes sujetos a régimen o entorno especial de zona franca.',
                'Inmuebles, mejoras o activos ubicados dentro de zona franca.',
                'Requiere revisar régimen aplicable, usos, restricciones y condiciones operativas.',
                'Se elige porque la ubicación o régimen especial puede incidir en uso, mercado y valor.'),
            'catastral' => self::note('Se orienta a lectura, revisión o soporte de información catastral.',
                'Trámites administrativos, consistencia predial, catastro o impuestos.',
                'No reemplaza automáticamente un avalúo comercial ordinario.',
                'Se elige porque la finalidad principal está conectada con información o gestión catastral.'),
            'seguro' => self::note('Busca base para cobertura, reposición o indemnización.',
                'Contratación de pólizas, siniestros, administración de riesgos o actualización asegurada.',
                'Suele exigir costo de reposición, exclusiones, depreciación y bienes cubiertos.',
                'Se elige porque el valor servirá para una decisión aseguradora o de reposición ante riesgo.'),
            'hipotecario' => self::note('Soporta una garantía crediticia sobre bien o derecho.',
                'Crédito, garantía real, análisis de riesgo, cupo o respaldo financiero.',
                'Debe diferenciar valor comercial, realizabilidad, restricciones y vida económica.',
                'Se elige porque el informe será insumo para garantía y evaluación de riesgo del acreedor.'),
            'interno' => self::note('Ordena un análisis interno sin destinatario externo principal.',
                'Planeación, control patrimonial, decisiones preliminares o escenarios de gestión.',
                'Debe marcarse como uso interno y evitar presentarlo como dictamen para terceros.',
                'Se elige porque el resultado apoyará una decisión interna y no una certificación externa.'),
        ]];
    }

    private static function note(string $what, string $when, string $basis, string $report): array
    {
        return ['what' => $what, 'when' => $when, 'basis' => $basis, 'report' => $report];
    }
}
