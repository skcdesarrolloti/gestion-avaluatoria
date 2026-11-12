<?php
declare(strict_types=1);
namespace App\Services;

/** Síntesis de fragmentos pertinentes; no transcripciones ni reglas automáticas. */
final class IncomeAcademySources
{
    public static function all(): array
    {
        $ivs='normas-internacionales-valuacion/';
        $nts='normas-tecnicas-sectoriales/';
        $igac='igac/documento/resolucion-igac-941-2026';
        return [
            'igac_scope'=>['family'=>'IGAC','name'=>'Resolución 0941','edition'=>'2026',
                'section'=>'Arts. 1–3, 7, 22 y 38','url'=>$igac,
                'fragment'=>'El marco del encargo determina la aplicación de la resolución. El método de renta estima el valor del inmueble desde sus ingresos; determinar el canon tiene un tratamiento específico.',
                'use'=>'Antes de reunir contratos, aclara si buscas el valor del inmueble o su canon y qué comprende el encargo.'],
            'ivs_scope'=>['family'=>'IVS','name'=>'IVS 101 · Alcance del trabajo','edition'=>'2025',
                'section'=>'Sección 20','url'=>$ivs.'ivs-101-scope/archivo',
                'fragment'=>'El alcance identifica el activo, cliente, uso y usuarios previstos, fecha, base de valor, extensión de la investigación, fuentes y supuestos.',
                'use'=>'Revisa primero la información del expediente. Si faltan el derecho o la finalidad, déjalos por aclarar; no los deduzcas del canon.'],
            'ivs_value'=>['family'=>'IVS','name'=>'IVS 102 · Bases de valor','edition'=>'2025',
                'section'=>'Sección 20; apéndices A10, A20 y A40','url'=>$ivs.'ivs-102-bases/archivo',
                'fragment'=>'Valor de mercado, renta de mercado y valor de inversión responden a bases distintas. La base adoptada debe ser adecuada para el uso previsto.',
                'use'=>'La expectativa particular del propietario no demuestra por sí sola lo que reconocería el mercado.'],
            'nts_scope'=>['family'=>'NTS/GTS','name'=>'NTS S03 · Contenido de informes','edition'=>'2009-09-10',
                'section'=>'Apartado 4.4','url'=>$nts.'nts-s03-contenido-informe-valuacion',
                'fragment'=>'Las especificaciones del encargo identifican bien, derechos, uso previsto, base, fechas, alcance y condiciones restrictivas.',
                'use'=>'Describe exactamente la unidad o parte estudiada y sus derechos; no confundir el edificio físico con el derecho objeto del avalúo.'],
            'nts_value'=>['family'=>'NTS/GTS','name'=>'NTS S01 y NTS S02 · Bases de valor','edition'=>'2010-11-29 / 2010-09-17',
                'section'=>'Objeto y bases de valor de cada norma','url'=>$nts.'nts-s01-bases-valor-mercado',
                'extra_url'=>$nts.'nts-s02-bases-valor-distinto-mercado','extra_label'=>'Consultar NTS S02',
                'fragment'=>'Estas normas distinguen la base de mercado de otras bases. La definición adoptada y sus condiciones deben reconocerse en el trabajo.',
                'use'=>'Consulta S01 para mercado; S02 cuando el encargo requiera otra base. No mezcles conclusiones con bases diferentes.'],
            'igac_contract'=>['family'=>'IGAC','name'=>'Resolución 0941','edition'=>'2026',
                'section'=>'Art. 24, literales a–c','url'=>$igac,
                'fragment'=>'La capitalización directa requiere investigar contratos y arriendos similares y contrastar los cánones contractuales con el mercado investigado.',
                'use'=>'Registrar «Contrato disponible» inicia la revisión; no significa que sus condiciones ya estén verificadas.'],
            'ivs_rights'=>['family'=>'IVS','name'=>'IVS 400 · Derechos sobre inmuebles','edition'=>'2025',
                'section'=>'Apartados 40, 80.1, 140 y 150 del cuerpo','url'=>$ivs.'ivs-400-real-property/archivo',
                'fragment'=>'Hay que identificar los derechos y las restricciones. Cuando el derecho está sujeto a arriendo se consideran las condiciones contractuales y la renta de mercado; la ocupación propia puede analizarse con renta imputada de mercado.',
                'use'=>'Separa ocupación actual, derecho valorado y condiciones del contrato. Renta potencial no equivale a ingreso efectivamente cobrado.'],
            'gts_contract'=>['family'=>'NTS/GTS','name'=>'GTS E02 · Derechos de arrendamiento','edition'=>'2009-09-10',
                'section'=>'Apartado 4.9 y sección 7, especialmente 7.9 y 7.11','url'=>$nts.'gts-e02-valuacion-arrendamiento',
                'fragment'=>'La renta contractual puede diferir de la de mercado. El análisis considera duración, restricciones, mejoras y obligaciones de las partes y requiere los contratos o sus términos disponibles.',
                'use'=>'Reúne contrato, modificaciones y antecedentes de mejoras. No sumes derechos separados como si fueran el dominio libre de cargas.'],
            'igac_data'=>['family'=>'IGAC','name'=>'Resolución 0941','edition'=>'2026',
                'section'=>'Arts. 23 y 24','url'=>$igac,
                'fragment'=>'Las rentas deben corresponder a la tenencia y uso del inmueble. Se investigan arriendos comparables y se sustenta el origen de los datos que respaldarán el análisis.',
                'use'=>'Identifica qué incluye cada arriendo y separa los ingresos del inmueble de los propios de un negocio o intangible.'],
            'ivs_data'=>['family'=>'IVS','name'=>'IVS 104 · Datos e insumos','edition'=>'2025',
                'section'=>'Secciones 30–50','url'=>$ivs.'ivs-104-data-inputs/archivo',
                'fragment'=>'Los datos se evalúan por precisión, completitud, oportunidad y trazabilidad. La selección y el uso de insumos significativos requieren explicación y documentación.',
                'use'=>'Conserva fuente, fecha, documento y resultado de la verificación. Si no puedes corroborar un dato, identifica la limitación.'],
            'nts_data'=>['family'=>'NTS/GTS','name'=>'NTS M01 · Inmuebles urbanos','edition'=>'2016-02-12',
                'section'=>'Apartados 6.9, 7.4 y anexo D, D1','url'=>$nts.'nts-m01-metodologias-valuacion',
                'fragment'=>'La investigación de renta considera inmuebles comparables, contratos y soporte de los ingresos y gastos vinculados a la operación del inmueble.',
                'use'=>'Esta norma tiene requisitos específicos para la ruta urbana. Sus mínimos de proyección y muestra se contrastarán con el encargo antes de programar el modelo; no se aplican como valores por defecto.'],
            'gts_data'=>['family'=>'NTS/GTS','name'=>'GTS E01 · Valuación de inmuebles','edition'=>'2010-09-17',
                'section'=>'Apartados 6.12.2–6.12.6','url'=>$nts.'gts-e01-valuacion-inmuebles',
                'fragment'=>'El análisis de renta necesita investigación de mercado y revisión de antecedentes de operación. La precisión matemática no reemplaza la calidad de la información y el juicio cualitativo.',
                'use'=>'Compara la experiencia del propietario con la evidencia de mercado; documenta sus diferencias antes de hacer cálculos.'],
            'igac_report'=>['family'=>'IGAC','name'=>'Resolución 0941','edition'=>'2026',
                'section'=>'Art. 14, numerales 3 y 10','url'=>$igac,
                'fragment'=>'El informe relaciona soportes, fechas y fuentes y explica la investigación, los métodos, las cifras adoptadas y su memoria de cálculo.',
                'use'=>'Desde ahora registra de dónde proviene cada respuesta y qué falta comprobar, para poder reconstruir el estudio después.'],
            'ivs_model'=>['family'=>'IVS','name'=>'IVS 105 · Modelos de valuación','edition'=>'2025',
                'section'=>'Secciones 30–50','url'=>$ivs.'ivs-105-models/archivo',
                'fragment'=>'El valuador debe entender el modelo utilizado, evaluar su adecuación, probar su funcionamiento y documentar entradas, resultados, limitaciones y controles.',
                'use'=>'Cuando lleguemos a los Excel, revisaremos sus supuestos y funcionamiento. Tener una hoja formulada no acredita su idoneidad.'],
            'ivs_report'=>['family'=>'IVS','name'=>'IVS 106 · Documentación e informes','edition'=>'2025',
                'section'=>'Secciones 20–30','url'=>$ivs.'ivs-106-reporting/archivo',
                'fragment'=>'El expediente y el informe deben permitir entender el alcance, el trabajo realizado, el juicio profesional y las bases de las conclusiones.',
                'use'=>'Distingue hechos comprobados, supuestos, información pendiente y decisiones del analista.'],
            'nts_report'=>['family'=>'NTS/GTS','name'=>'NTS S03 y NTS I01 · Informes','edition'=>'2009-09-10 / 2009-11-17',
                'section'=>'S03: 7.1.5–7.1.10; I01: 15–17 y 21','url'=>$nts.'nts-s03-contenido-informe-valuacion',
                'extra_url'=>$nts.'nts-i01-contenido-informe-urbanos','extra_label'=>'Consultar NTS I01',
                'fragment'=>'Se explican alcance, hipótesis, restricciones, métodos, argumentos y soportes. I01 detalla metodología, justificación, memorias y anexos para informes urbanos.',
                'use'=>'La información de esta preparación alimentará el informe; no genera una declaración automática de cumplimiento.'],
        ];
    }
}
