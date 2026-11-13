<?php
declare(strict_types=1);
namespace App\Services;

final class IncomeAcademy
{
    public static function topics(): array
    {
        return [
            ['key'=>'encargo','title'=>'Qué vamos a valorar y para qué',
                'explanation'=>'Antes de estudiar el arriendo, identifica la finalidad, la fecha y el derecho que se valora. Estimar el valor del inmueble y determinar su canon son encargos distintos.',
                'example'=>'El propietario quiere conocer el valor comercial de una oficina arrendada. El contrato aporta información, pero no cambia el objetivo del encargo a «calcular el canon».',
                'collect'=>['Encargo o solicitud y finalidad del avalúo.', 'Identificación de la unidad, parte y derecho objeto del estudio.', 'Fecha de valoración y base de valor acordada.'],
                'connection'=>'En la etapa 1, Inmueble y arriendo, consulta el encargo y el capítulo 3 y contrasta qué comprende el contrato. Completa los datos del sujeto en su origen.',
                'sources'=>['igac_scope','ivs_scope','ivs_value','nts_scope','nts_value']],
            ['key'=>'ocupacion','title'=>'Ocupación, contratos y derechos',
                'explanation'=>'Reconoce cómo se usa el inmueble y qué derechos y obligaciones existen. La ocupación física no demuestra por sí sola un contrato, y un canon contractual puede diferir de la renta de mercado.',
                'example'=>'Una oficina la ocupa su propietario. Puede tener capacidad de arriendo, aunque hoy no haya arrendatario ni cobros. Esa capacidad se investigará en el mercado.',
                'collect'=>['Situación de ocupación observada y partes ocupadas o vacías.', 'Contrato, modificaciones y términos disponibles.', 'Restricciones, mejoras y responsabilidades de las partes por verificar.'],
                'connection'=>'La etapa 1 consulta la ocupación y reúne vigencia y condiciones del arriendo. La etapa 4 utilizará esos antecedentes para ambos métodos. La información verbal queda por verificar.',
                'sources'=>['igac_contract','ivs_rights','gts_contract']],
            ['key'=>'informacion','title'=>'Qué información necesitamos y cómo verificarla',
                'explanation'=>'Una respuesta necesita procedencia y revisión. Los contratos, anuncios, manifestaciones y antecedentes de operación aportan evidencias diferentes; conserva su origen y sus limitaciones.',
                'example'=>'El propietario informa que el canon incluye administración. Hasta contrastarlo con el contrato o los soportes, registra la afirmación como información por verificar.',
                'collect'=>['Fuente, fecha y soporte de la información del inmueble.', 'Arriendos comparables y condiciones que permitan analizar su pertinencia.', 'Antecedentes de ingresos y gastos y qué conceptos corresponde asumir a cada parte.'],
                'connection'=>'La etapa 2 reúne la investigación de arriendos. La etapa 3 consulta el contrato y sustenta ingresos, gastos y proyección; la etapa 4 compara tasas y calcula cuando el analista lo solicita.',
                'sources'=>['igac_data','ivs_data','nts_data','gts_data']],
            ['key'=>'decisiones','title'=>'Cómo documentar las decisiones',
                'explanation'=>'El estudio debe permitir entender qué se examinó, qué se comprobó, qué se supuso y por qué se decidió. Un formulario lleno o un Excel existente no sustituye esa explicación.',
                'example'=>'El contrato menciona toda la oficina, pero el encargo solo incluye una parte. Conserva la diferencia y solicita aclaración antes de usar sus ingresos para esa cobertura.',
                'collect'=>['Soportes consultados y referencias que permitan localizarlos.', 'Decisiones justificadas, supuestos y restricciones del estudio.', 'Pendientes de corroboración y responsables de aclararlos.'],
                'connection'=>'La etapa 4 calcula capitalización directa y flujo descontado con los supuestos guardados. La etapa 5 conserva resultados, fuentes y limitaciones; el cálculo no verifica documentos ni adopta un avalúo automáticamente.',
                'sources'=>['igac_report','ivs_model','ivs_report','nts_report']],
        ];
    }
}
