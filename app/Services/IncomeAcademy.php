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
                'connection'=>'En el primer bloque, «¿Qué parte se analizará por renta?» describe la cobertura. Consulta el alcance ya configurado y señala diferencias; esta respuesta no lo modifica.',
                'sources'=>['igac_scope','ivs_scope','ivs_value','nts_scope','nts_value']],
            ['key'=>'ocupacion','title'=>'Ocupación, contratos y derechos',
                'explanation'=>'Reconoce cómo se usa el inmueble y qué derechos y obligaciones existen. La ocupación física no demuestra por sí sola un contrato, y un canon contractual puede diferir de la renta de mercado.',
                'example'=>'Una oficina la ocupa su propietario. Puede tener capacidad de arriendo, aunque hoy no haya arrendatario ni cobros. Esa capacidad se investigará en el mercado.',
                'collect'=>['Situación de ocupación observada y partes ocupadas o vacías.', 'Contrato, modificaciones y términos disponibles.', 'Restricciones, mejoras y responsabilidades de las partes por verificar.'],
                'connection'=>'«¿Cómo se ocupa actualmente?» y «¿Qué soporte está disponible?» describen la situación y la evidencia inicial. Información verbal deja una comprobación pendiente.',
                'sources'=>['igac_contract','ivs_rights','gts_contract']],
            ['key'=>'informacion','title'=>'Qué información necesitamos y cómo verificarla',
                'explanation'=>'Una respuesta necesita procedencia y revisión. Los contratos, anuncios, manifestaciones y antecedentes de operación aportan evidencias diferentes; conserva su origen y sus limitaciones.',
                'example'=>'El propietario informa que el canon incluye administración. Hasta contrastarlo con el contrato o los soportes, registra la afirmación como información por verificar.',
                'collect'=>['Fuente, fecha y soporte de la información del inmueble.', 'Arriendos comparables y condiciones que permitan analizar su pertinencia.', 'Antecedentes de ingresos y gastos y qué conceptos corresponde asumir a cada parte.'],
                'connection'=>'La selección «Contrato disponible» no equivale a «Contrato verificado». Las notas ayudan a identificar el soporte; la investigación y los valores se trabajarán después.',
                'sources'=>['igac_data','ivs_data','nts_data','gts_data']],
            ['key'=>'decisiones','title'=>'Cómo documentar las decisiones',
                'explanation'=>'El estudio debe permitir entender qué se examinó, qué se comprobó, qué se supuso y por qué se decidió. Un formulario lleno o un Excel existente no sustituye esa explicación.',
                'example'=>'El contrato menciona toda la oficina, pero el encargo solo incluye una parte. Conserva la diferencia y solicita aclaración antes de usar sus ingresos para esa cobertura.',
                'collect'=>['Soportes consultados y referencias que permitan localizarlos.', 'Decisiones justificadas, supuestos y restricciones del estudio.', 'Pendientes de corroboración y responsables de aclararlos.'],
                'connection'=>'El resumen del primer bloque es un borrador descriptivo. Más adelante sus soportes, limitaciones y decisiones deberán conservarse en el análisis y el entregable.',
                'sources'=>['igac_report','ivs_model','ivs_report','nts_report']],
        ];
    }
}
