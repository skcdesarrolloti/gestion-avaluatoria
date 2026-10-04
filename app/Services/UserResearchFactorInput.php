<?php
declare(strict_types=1);
namespace App\Services;
use App\Core\HttpException;
final class UserResearchFactorInput
{
    public static function input(array $post,string $key=''): array
    {
        $base=$key!==''?(ResearchFactorCatalog::all()[$key] ?? null):null;
        if ($key!=='' && (!$base || in_array($key,['area','land','built','destination'],true))) throw new HttpException(422,'Este dato es base de cálculo o filtro, no un factor editable.');
        $data=[];
        foreach (['label'=>100,'why'=>600,'unit'=>30,'categories'=>1200,'group'=>40,'kind'=>20] as $field=>$limit) {
            $value=$post[$field] ?? '';
            if (!is_string($value) || mb_strlen($value)>$limit) throw new HttpException(422,'Revisa '.$field.': texto demasiado largo o inválido.');
            $data[$field]=trim(str_replace(["\r\n","\r"],"\n",$value));
        }
        if ($data['label']==='' || $data['why']==='' || $data['unit']==='') throw new HttpException(422,'Completa nombre, definición y unidad de medida.');
        foreach (ResearchFactorCatalog::all() as $existingKey=>$existing) if ($existingKey!==$key && mb_strtolower($existing['label'])===mb_strtolower($data['label'])) throw new HttpException(422,'Ya existe un factor con ese nombre. Edita o asigna el existente.');
        if (!in_array($data['kind'],['numeric','binary','ordinal','categorical'],true)) throw new HttpException(422,'Elige el tipo de dato.');
        if ($base && ($base['kind']==='numeric' || $data['kind']==='numeric') && ($base['kind']!==$data['kind'] || $base['unit']!==$data['unit'])) throw new HttpException(422,'Conserva la medida original del factor numérico; crea otro factor si necesitas otra medida.');
        if (!in_array($data['group'],['Unidad privada','Celdas de parqueo','Copropiedad PH','Terreno'],true)) throw new HttpException(422,'Elige el alcance del atributo.');
        $types=$post['types'] ?? [];
        if (!is_array($types) || $types===[] || array_filter($types,static fn($type)=>!is_string($type) || !isset(ComparablePortalProfiles::types()[$type]))) throw new HttpException(422,'Selecciona al menos un tipo de inmueble válido.');
        $data['types']=array_values(array_unique($types));
        $data['previous_types']=array_values(array_unique(array_merge($base['previous_types'] ?? [],$key!==''?UserResearchFactors::types($key):[],$data['types'])));
        if ($data['kind']==='numeric') $data['categories']='';
        elseif ($data['kind']==='binary') $data['categories']="No\nSí";
        else {
            $lines=array_map('trim',explode("\n",$data['categories']));
            $labels=array_map('mb_strtolower',$lines);
            if (count($lines)<2 || count($lines)>15 || in_array('',$lines,true) || count(array_unique($labels))!==count($labels) || array_intersect($labels,['desconocido','pendiente','no publicado','no verificado'])) throw new HttpException(422,'Define entre 2 y 15 clases únicas; desconocido queda pendiente y no es un nivel.');
            foreach ($lines as $line) if (mb_strlen($line)>120) throw new HttpException(422,'Cada clase admite hasta 120 caracteres.');
            $data['categories']=implode("\n",$lines);
        }
        return $data+['subject'=>$base['subject'] ?? 'research_'.$key,'sample'=>$base['sample'] ?? 'research_'.$key,'section'=>$base['section'] ?? 'attributes'];
    }
}
