<?php
declare(strict_types=1);
namespace App\Services;

/** Only one identified listing entity; no recommendations, navigation or ratings. */
final class ComparableDetailParser
{
    public function parse(string $html,string $url): array
    {
        $url = ComparableDetailReader::canonicalUrl($url);
        $previous=libxml_use_internal_errors(true);
        try {
            $doc=new \DOMDocument(); $doc->loadHTML('<?xml encoding="UTF-8">'.$html,LIBXML_NONET|LIBXML_NOERROR|LIBXML_NOWARNING);
            $nodes=[];
            foreach ((new \DOMXPath($doc))->query('//script[@type="application/ld+json"]') as $script) {
                $decoded=json_decode($script->textContent,true,32);
                if (is_array($decoded)) $this->collect($decoded,$nodes);
            }
        } finally { libxml_clear_errors(); libxml_use_internal_errors($previous); }
        $exact=array_values(array_filter($nodes,static fn ($n)=>rtrim((string)($n['url'] ?? ''),'/')===rtrim($url,'/')));
        $eligible=$exact ?: array_values(array_filter($nodes,static fn ($n)=>empty($n['url'])));
        $visible=ComparableVisibleDetails::parse($doc,$url);
        if (count($eligible)!==1) {
            if (!$visible) throw new \RuntimeException('No se identificó una ficha individual inequívoca. Copia su texto; no se mezclaron anuncios relacionados.');
            $visible['row']['source_url']=$url; $visible['row']['consulted_at']=date('Y-m-d');
            return $visible+['read_scope'=>'Ficha individual: características publicadas y descripción.','warning'=>'Datos publicados pendientes de verificar.'];
        }
        $item=$eligible[0]; $entity=is_array($item['mainEntity'] ?? null)?$item['mainEntity']:$item;
        $description=is_string($item['description'] ?? null)?$item['description']:'';
        if ($entity!==$item && is_string($entity['description'] ?? null)) $description.="\n".$entity['description'];
        $attributes=array_merge(is_array($entity['additionalProperty'] ?? null)?$entity['additionalProperty']:[],is_array($entity['amenityFeature'] ?? null)?$entity['amenityFeature']:[]);
        foreach ($attributes as $attribute) if (is_array($attribute) && is_scalar($attribute['name'] ?? null) && is_scalar($attribute['value'] ?? null)) {
            $description.="\n".$attribute['name'].': '.(is_bool($attribute['value'])?($attribute['value']?'Sí':'No'):$attribute['value']).';';
        }
        $row=ComparablePublishedDetails::parse($description);
        $facts=json_decode($row['published_attributes'],true);
        if ($visible) {
            $row=array_replace($visible['row'],$row);
            $facts=array_replace(json_decode($visible['row']['published_attributes'],true),$facts);
        }
        foreach (['seller','provider'] as $key) {
            $seller=$item[$key] ?? $entity[$key] ?? [];
            if (is_array($seller) && is_string($seller['name'] ?? null)) { $row['contact_name']=$seller['name']; $facts['Anunciante']=$seller['name']; break; }
        }
        $row['published_attributes']=json_encode((object)array_slice($facts,0,80,true),JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR);
        foreach (['bathrooms'=>'numberOfBathroomsTotal','bedrooms'=>'numberOfBedrooms'] as $field=>$key) {
            if (is_numeric($entity[$key] ?? null) && (float)$entity[$key]>=0) $row[$field]=(string)$entity[$key];
        }
        $area=is_array($entity['floorSize'] ?? null)?$entity['floorSize']:[];
        if (($area['unitCode'] ?? '')==='MTK' && is_numeric($area['value'] ?? null) && (float)$area['value']>=0) $row['area_m2']=(string)$area['value'];
        $offer=$item['offers'] ?? $entity['offers'] ?? [];
        if (!is_array($offer)) $offer=[];
        if (($offer['priceCurrency'] ?? '')==='COP' && is_numeric($offer['price'] ?? null) && (float)$offer['price']>0) $row['price_amount']=(string)$offer['price'];
        $row['source_url']=$url; $row['consulted_at']=date('Y-m-d');
        return ['row'=>$row,'title'=>mb_substr(strip_tags((string)($item['name'] ?? 'Ficha individual')),0,180),
            'read_scope'=>'Datos estructurados y descripción. Revisa si la página muestra otros atributos para copiar y pegar.',
            'warning'=>'Ficha leída; datos publicados pendientes de verificar. Los datos ausentes no equivalen a cero.'];
    }

    private function collect(array $data,array &$nodes,int $depth=0): void
    {
        if ($depth>6) return;
        if (array_intersect((array)($data['@type'] ?? []),['RealEstateListing','Product','Apartment','House','Residence','Accommodation'])) { $nodes[]=$data; return; }
        foreach (['@graph','mainEntity'] as $key) if (is_array($data[$key] ?? null)) $this->collect($data[$key],$nodes,$depth+1);
        if (array_is_list($data)) foreach ($data as $child) if (is_array($child)) $this->collect($child,$nodes,$depth+1);
    }
}
