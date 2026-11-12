<?php
declare(strict_types=1);
namespace App\Services;

use App\Core\HttpException;

/** Preparación conceptual del recorrido; no calcula ni acredita cumplimiento. */
final class IncomeConcept
{
    public static function questions(): array
    {
        return [
            'occupancy' => ['label'=>'¿Cómo se ocupa actualmente el inmueble?', 'values'=>[
                'rented'=>'Totalmente arrendado', 'partial'=>'Parcialmente arrendado',
                'vacant'=>'Desocupado', 'owner'=>'Ocupado por el propietario',
                'other'=>'Otra situación', 'pending'=>'Pendiente de verificar'],
                'meaning'=>'Selecciona la situación observada a la fecha del avalúo.',
                'why'=>'Distingue la ocupación actual del potencial de arriendo. Estar desocupado no significa que el inmueble carezca de capacidad para generar renta.',
                'example'=>'Si una parte está arrendada y otra vacía, selecciona «Parcialmente arrendado».',
                'references'=>['IGAC 941 de 2026, arts. 22 y 24', 'IVS 400 (2025), apartados 80 y 150']],
            'scope' => ['label'=>'¿Qué parte se analizará por renta?', 'values'=>[
                'whole'=>'Todo el inmueble de este recorrido', 'part'=>'Una parte identificada',
                'several'=>'Varias partes identificadas', 'pending'=>'Por definir'],
                'meaning'=>'Describe la cobertura dentro de la unidad o parte seleccionada arriba.',
                'why'=>'Permite reconocer qué derecho y qué espacios respaldan el estudio. Esta respuesta no cambia las unidades ni el alcance configurado.',
                'example'=>'Para una oficina completa selecciona «Todo el inmueble de este recorrido». Si estudias solo un espacio, identifícalo debajo.',
                'references'=>['IVS 101 (2025), sección 20', 'IVS 400 (2025), apartado 40', 'NTS S03 (2009), apartado 4.4']],
            'support' => ['label'=>'¿Qué soporte del arriendo está disponible?', 'values'=>[
                'contract_changes'=>'Contrato y modificaciones', 'contract'=>'Solo contrato',
                'other'=>'Otros soportes', 'verbal'=>'Información verbal',
                'none'=>'Sin soporte disponible', 'pending'=>'Por verificar'],
                'meaning'=>'Indica lo que tienes disponible, aunque todavía falte revisarlo.',
                'why'=>'Identificar un documento no confirma su contenido. Se necesita comprobar la procedencia de la información y las condiciones del arriendo.',
                'example'=>'Si el propietario lo explicó por teléfono y aún no entregó documentos, selecciona «Información verbal».',
                'references'=>['IGAC 941 de 2026, art. 24.a–c', 'GTS E02 (2009), apartados 4.9 y 7.11', 'NTS M01 (2016), apartado 7.4.2']],
        ];
    }

    public static function texts(): array
    {
        return [
            'occupancy_detail'=>['Describe la otra situación de ocupación', 'Ej. Uso compartido entre el propietario y un tercero; falta verificar el acuerdo.', "occupancy === 'other'"],
            'scope_detail'=>['Identifica las partes que se estudiarán', 'Ej. Zona del primer piso destinada a oficinas; contrastar con el alcance configurado.', "['part','several'].includes(scope) || occupancy === 'partial'"],
            'support_detail'=>['Identifica el soporte o la procedencia de la información', 'Ej. Contrato archivado en el expediente; o información del propietario pendiente de corroborar.', "['contract_changes','contract','other','verbal'].includes(support)"],
        ];
    }

    public static function input(mixed $post): array
    {
        if (!is_array($post) || array_is_list($post)) throw new HttpException(422, 'Revisa las respuestas de Renta.');
        $out = $errors = [];
        foreach (self::questions() as $key=>$question) {
            if (!array_key_exists($key,$post)) continue;
            $value=$post[$key];
            if (!is_string($value) || ($value!=='' && !isset($question['values'][$value])))
                $errors['income_concept['.$key.']']='Selecciona una opción de la lista.';
            else $out[$key]=$value;
        }
        foreach (self::texts() as $key=>[$label]) {
            if (!array_key_exists($key,$post)) continue;
            if (!is_string($post[$key]) || mb_strlen($post[$key])>1000)
                $errors['income_concept['.$key.']']='Escribe como máximo 1000 caracteres.';
            else $out[$key]=trim($post[$key]);
        }
        if (array_diff(array_keys($post),array_merge(array_keys(self::questions()),array_keys(self::texts()))))
            throw new HttpException(422,'Se recibieron campos conceptuales no admitidos.');
        if ($errors) throw new HttpException(422,'Revisa los campos señalados; tus respuestas siguen en pantalla.',$errors);
        return $out;
    }

    public static function validate(array $changes, array $saved): void
    {
        if (!array_key_exists('income_concept',$changes)) return;
        if (($saved['method'] ?? '')!=='renta' || ($changes['method'] ?? 'renta')!=='renta')
            throw new HttpException(422,'Selecciona Renta en este recorrido antes de guardar sus respuestas.');
        self::input($changes['income_concept']);
    }
}
