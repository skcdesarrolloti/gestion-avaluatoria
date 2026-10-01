<?php
declare(strict_types=1);
namespace App\Support;
final class JudicialExpertAcademy
{
    public const SOURCE = 'https://www.cancilleria.gov.co/normograma/compilacion/docs/ley_1564_2012.htm';
    public static function articles(): array
    {
        return [
            ['226.1 · Identidad', 'La identidad de quien rinde el dictamen y de quien participó en su elaboración.', 'Identifica al firmante y a cada participante. No confundir el equipo que colabora con varios firmantes indistintos.'],
            ['226.2 · Localización del perito', 'La dirección, el número de teléfono, número de identificación y los demás datos que faciliten la localización del perito.', 'Revisa contacto vigente; el RAA es una fuente, no reemplaza todos los datos.'],
            ['226.3 · Idoneidad y experiencia', 'La profesión, oficio, arte o actividad especial ejercida por quien rinde el dictamen y de quien participó en su elaboración. Deberán anexarse los documentos idóneos que lo habilitan para su ejercicio, los títulos académicos y los documentos que certifiquen la respectiva experiencia profesional, técnica o artística.', 'Relaciona soportes y adjúntalos al dictamen. Escribir una referencia no adjunta el archivo. Incluye los de colaboradores.'],
            ['226.4 · Publicaciones (10 años)', 'La lista de publicaciones, relacionadas con la materia del peritaje, que el perito haya realizado en los últimos diez (10) años, si las tuviere.', 'Registra fecha, título y referencia. La ausencia de registros no demuestra que no existan publicaciones: el perito confirma la integridad del historial.'],
            ['226.5 · Casos (4 años)', 'La lista de casos en los que haya sido designado como perito o en los que haya participado en la elaboración de un dictamen pericial en los últimos cuatro (4) años. Dicha lista deberá incluir el juzgado o despacho en donde se presentó, el nombre de las partes, de los apoderados de las partes y la materia sobre la cual versó el dictamen.', 'Incluye designaciones y participación, aunque no hayas firmado. El periodo se calcula a la fecha del dictamen; los antecedentes externos se registran en el maestro.'],
            ['226.6 · Misma parte o apoderado', 'Si ha sido designado en procesos anteriores o en curso por la misma parte o por el mismo apoderado de la parte, indicando el objeto del dictamen.', 'Declaración específica para este proceso. No está limitada al periodo de cuatro años del numeral 5. Revisa también el archivo histórico completo.'],
            ['226.7 · Causales del artículo 50', 'Si se encuentra incurso en las causales contenidas en el artículo 50, en lo pertinente.', 'El título correcto es declaración de causales, no solo pertenencia a la lista de auxiliares. Estar en el RAA no acredita por sí solo ausencia de causales ni inscripción en una lista judicial.'],
            ['226.8 · Variación frente a peritajes anteriores', 'Declarar si los exámenes, métodos, experimentos e investigaciones efectuados son diferentes respecto de los que ha utilizado en peritajes rendidos en anteriores procesos que versen sobre las mismas materias. En caso de que sea diferente, deberá explicar la justificación de la variación.', 'Compara con tus dictámenes anteriores sobre la misma materia. Si no existen, indícalo expresamente; no declarar que son iguales por defecto.'],
            ['226.9 · Variación frente al ejercicio habitual', 'Declarar si los exámenes, métodos, experimentos e investigaciones efectuados son diferentes respecto de aquellos que utiliza en el ejercicio regular de su profesión u oficio. En caso de que sea diferente, deberá explicar la justificación de la variación.', 'Es una comparación diferente a la del numeral 8. Si hay variación explica su justificación técnica.'],
            ['226.10 · Documentos de fundamento', 'Relacionar y adjuntar los documentos e información utilizados para la elaboración del dictamen.', 'Lista anexo, identificación, fecha, procedencia y ubicación. La exportación de texto no contiene los archivos de soporte: deben acompañar el dictamen.'],
        ];
    }
    public static function related(): array
    {
        return [
            'Art. 226: independencia y real convicción profesional bajo juramento, entendido prestado por la firma. El dictamen debe explicar métodos, investigaciones y fundamentos; esta ficha no reemplaza el dictamen ni su firma.',
            'Art. 50: revisar, en lo pertinente, condenas o sanciones del numeral 1; suspensión/cancelación de matrícula; cargo oficial; fallecimiento/incapacidad; ausencia del distrito; disolución de persona jurídica; manejo de bienes y cuentas; incumplimiento del encargo; rechazo o inasistencia injustificados; retribución indebida; garantía del secuestre. El parágrafo 3 impide designar como perito a quien incurra en causal. Consulta el texto y sus parágrafos, no solo este resumen.',
            'Arts. 227–231: distinguir dictamen de parte y decretado de oficio, oportunidad, presentación, contradicción y comparecencia. Los plazos dependen de la actuación; no se calculan automáticamente aquí.',
            'Art. 232: el juez aprecia fundamentos, claridad, precisión, idoneidad, comportamiento en audiencia y demás pruebas. Completar campos no garantiza admisibilidad ni valoración favorable.',
            'Art. 233: documentar colaboración, acceso y limitaciones. Art. 235: objetividad e imparcialidad; considerar lo favorable y desfavorable a las partes, revisar causales de recusación pertinentes y no pactar honorarios dependientes del resultado del litigio.',
        ];
    }
}
