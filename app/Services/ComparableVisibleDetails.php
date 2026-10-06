<?php
declare(strict_types=1);
namespace App\Services;

/** Read bounded property content, never navigation or related advertisements. */
final class ComparableVisibleDetails
{
    public static function parse(\DOMDocument $doc,string $url): ?array
    {
        $xp=new \DOMXPath($doc);
        foreach ($xp->query('//script') as $script) {
            if (!preg_match('/window\.VISUALINMUEBLE_INMUEBLE\s*=\s*(\{.*\})\s*;/s',$script->textContent,$match)) continue;
            $data=json_decode($match[1],true,32);
            if (!is_array($data) || rtrim($data['url']['detail'] ?? '', '/')!==rtrim($url,'/')) continue;
            $facts=[];
            foreach (['n_alcobas'=>'Alcobas','n_baños'=>'Baños','n_garajes'=>'Garajes','area_lote'=>'Área lote',
                'area_construida'=>'Área construida','n_estrato'=>'Estrato','edad'=>'Edad','valor_admin'=>'Administración'] as $key=>$label) {
                if (isset($data[$key]) && is_scalar($data[$key])) $facts[$label]=(string)$data[$key];
            }
            foreach ($data['caracteristicas'] ?? [] as $feature) {
                if (!empty($feature['nombre'])) $facts[$feature['nombre']]=isset($feature['valor'])?(string)$feature['valor']:'Publicado en características';
            }
            $row=self::row(html_entity_decode((string)($data['descripcion'] ?? ''),ENT_QUOTES|ENT_HTML5,'UTF-8'),$facts);
            foreach (['n_alcobas'=>'bedrooms','n_baños'=>'bathrooms','n_garajes'=>'parking_spaces','area_construida'=>'area_m2'] as $key=>$field) {
                if (isset($data[$key]) && is_numeric($data[$key])) $row[$field]=(string)$data[$key];
            }
            $price=$data['gestion']['esVenta'] ?? false ? ($data['valor_venta'] ?? null) : ($data['valor_canon'] ?? null);
            if (is_numeric($price) && $price>0) $row['price_amount']=(string)$price;
            return ['row'=>$row,'title'=>(string)($data['nombre'] ?? 'Inmueble')];
        }
        $identity=false;
        foreach ($xp->query('//link[@rel="canonical"]/@href | //meta[@property="og:url"]/@content') as $value) {
            if (rtrim($value->nodeValue,'/')===rtrim($url,'/')) $identity=true;
        }
        if (!$identity || $xp->query('//h1')->length!==1) return null;
        $scopes=$xp->query('//main | //*[@id="property-details" or @id="property-detail" or @id="inmueble-detalle"]');
        if ($scopes->length!==1) return null;
        $scope=$scopes->item(0);
        foreach (iterator_to_array($xp->query('.//script | .//style | .//nav | .//footer | .//form | .//aside | .//*[contains(@class,"related") or contains(@class,"similar") or contains(@class,"recommend")]', $scope)) as $remove) {
            if ($remove->parentNode) $remove->parentNode->removeChild($remove);
        }
        foreach ($xp->query('.//a[@href]',$scope) as $link) {
            $href=$link->getAttribute('href');
            if (preg_match('~/(?:inmueble|propiedad|property|apartamento|oficina|casa)[^?#]*[\d-]~i',$href) && !str_contains(rtrim($url,'/'),rtrim($href,'/'))) return null;
        }
        $facts=[]; $description=[];
        foreach ($xp->query('.//dt | .//tr | .//li | .//p', $scope) as $node) {
            $text=self::text($node);
            if ($node->nodeName==='dt') {
                $next=$node->nextSibling;
                while ($next && $next->nodeType!==XML_ELEMENT_NODE) $next=$next->nextSibling;
                if ($next && $next->nodeName==='dd') $facts[rtrim($text,':')]=self::text($next);
            } elseif ($node->nodeName==='tr') {
                $cells=$xp->query('./th | ./td',$node);
                if ($cells->length===2) $facts[rtrim(self::text($cells->item(0)),':')]=self::text($cells->item(1));
            } elseif (preg_match('/^([^:]{2,65}):\s*(.{1,500})$/u',$text,$m)) $facts[trim($m[1])]=trim($m[2]);
            elseif ($node->nodeName==='p') $description[]=$text;
            elseif ($node->nodeName==='li' && mb_strlen($text)<100 && preg_match('/caracter|feature|amenit/i',$node->parentNode->getAttribute('class'))) $facts[$text]='Publicado en características';
        }
        if (!$facts && !$description) return null;
        return ['row'=>self::row(implode("\n",$description),$facts),'title'=>self::text($xp->query('//h1')->item(0))];
    }

    private static function text(\DOMNode $node): string
    {
        return trim(preg_replace('/\s+/u',' ',$node->textContent) ?? '');
    }

    private static function row(string $description,array $facts): array
    {
        $lines=[];
        foreach ($facts as $label=>$value) if ($label!=='' && $value!=='') $lines[]=$label.': '.$value.';';
        $row=ComparablePublishedDetails::parse($description."\n".implode("\n",$lines));
        foreach (['Anunciante','Inmobiliaria','Asesor'] as $label) if (!empty($facts[$label])) { $row['contact_name']=mb_substr($facts[$label],0,180); break; }
        $bag=json_decode($row['published_attributes'],true);
        $row['published_attributes']=json_encode((object)array_slice(array_replace($bag,$facts),0,80,true),JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR);
        return $row;
    }
}
