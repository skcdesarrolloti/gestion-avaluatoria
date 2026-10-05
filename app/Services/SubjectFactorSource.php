<?php
declare(strict_types=1);
namespace App\Services;
use App\Support\AppraisalFunctionalVariableCatalog as Functional;

/** Live references to chapter 3 observations. Never reads economic ratings or weights. */
final class SubjectFactorSource
{
    public static function resolve(string $key,array $factor,array $unit): array
    {
        $field=$factor['subject'];
        $raw=trim((string)($unit[$field] ?? ''));
        $section=$factor['section']==='surface'?'3.2':'3.3';
        $support=trim((string)($unit['functional_notes'] ?? ''));
        if ($raw==='' && in_array($unit['property_type'] ?? '',['lote','finca'],true) && in_array($key,['frontage','depth'],true)) {
            $field=$key==='frontage'?'front_length_m':'depth_length_m';
            $raw=trim((string)($unit[$field] ?? '')); $section='3.2'; $support='';
        }
        if ($raw==='' && $key==='service_room') {
            $field='functional_service_room_bathroom';
            $raw=match($unit[$field] ?? '') { 'alcoba','alcoba_bano'=>'Sí','bano'=>'No',default=>'' };
        }
        if ($raw!=='') {
            $options=Functional::definitions()[$field]['options'] ?? [];
            $value=$options[$raw] ?? $raw;
            $value=self::translate($key,$value);
            $label=Functional::definitions()[$field]['label'] ?? $factor['label'];
            return self::result($value,$factor,$section,$label,$support);
        }
        foreach (SubjectAttributeResearch::previous($unit,$factor) as $previous) {
            if ($previous['observed']==='Pendiente' || $previous['observed']==='No verificado') continue;
            $value=self::translate($key,$previous['observed']);
            return self::result($value,$factor,'3.4',$previous['label'],$previous['notes']);
        }
        return [];
    }
    private static function translate(string $key,string $value): string
    {
        if ($key==='view') return match($value) {
            'Sin vista relevante'=>'Sin vista', 'Calle'=>'Exterior: calles y avenidas',
            'Paisajística','Mar','Parque'=>'Exterior: paisajística',default=>$value,
        };
        if ($key==='finish_quality' || $key==='finishes') return match($value) {
            'Básico'=>'Básico / económico','Lujo'=>'Superior / lujo',default=>$value,
        };
        return $value;
    }
    private static function result(string $value,array $factor,string $section,string $field,string $support): array
    {
        $valid=empty($factor['customized']) && ($factor['scale_valid'] ?? true)
            && SubjectFactorCapture::validValue($value,$factor);
        return ['value'=>$value,'usable'=>$valid,'section'=>$section,'field'=>$field,
            'support'=>'Registro del módulo '.$section.' · '.$field.($support!==''?' · '.$support:''),
            'hash'=>$section==='3.2'?'#superficies':($section==='3.3'?'#construccion':'#atributos')];
    }
}
