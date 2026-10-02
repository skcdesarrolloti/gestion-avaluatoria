<?php
declare(strict_types=1);
use App\Services\ComparableExcelInput;
use App\Services\ComparableExcelArchive;
(static function (): void {
    $id = str_repeat('a',32); $aid = str_repeat('b',32);
    $existing = [['id'=>$id,'source_name'=>'Aviso original','price_amount'=>'500000000','negotiation_discount'=>'25000000','private_built_m2'=>'80']];
    $cell = static fn (string $value, bool $formula=false): array => ['value'=>$value,'formula'=>$formula,'type'=>'inlineStr'];
    $columns = [['key'=>'id','label'=>'ID'],['key'=>'source_name','label'=>'Fuente'],['key'=>'price_amount','label'=>'Oferta'],
        ['key'=>'negotiation_discount','label'=>'Descuento'],['key'=>'negotiated_amount','label'=>'Calculado']];
    $header = array_map(static fn ($column) => $cell($column['label']),$columns);
    $row = [$cell($id),$cell('Aviso confirmado'),$cell('500000000'),$cell('30000000'),$cell('1',true)];
    $parsed = ComparableExcelInput::rows([$header,$row],$columns,$existing);
    expect($parsed[0]['source_name']==='Aviso confirmado' && $parsed[0]['negotiation_discount']==='30000000', 'Excel actualiza datos por ID sin leer resultado calculado adulterado');
    expect(!isset($parsed[0]['negotiated_amount']) && !isset($parsed[0]['private_built_m2']), 'Excel no borra columnas omitidas ni importa derivados');
    $wrong=$row; $wrong[0]=$cell(str_repeat('c',32));
    expectStatus(422,fn()=>ComparableExcelInput::rows([$header,$wrong],$columns,$existing),'Excel rechaza ID ajeno a colección');
    expectStatus(422,fn()=>ComparableExcelInput::rows([$header,$row,$row],$columns,$existing),'Excel rechaza ID duplicado en filas');
    $formula=$row; $formula[3]=$cell('30000000',true);
    expectStatus(422,fn()=>ComparableExcelInput::rows([$header,$formula],$columns,$existing),'Excel no ejecuta fórmulas en datos editables');
    $negative=$row; $negative[3]=$cell('-1');
    expectStatus(422,fn()=>ComparableExcelInput::rows([$header,$negative],$columns,$existing),'Excel valida descuento negativo antes de cambiar tabla');
    $unknown=$header; $unknown[1]=$cell('Otro encabezado');
    expectStatus(422,fn()=>ComparableExcelInput::rows([$unknown,$row],$columns,$existing),'Excel no adivina encabezados cambiados');
    $reordered=ComparableExcelInput::rows([[1=>$header[0],0=>$header[1],2=>$header[2],3=>$header[3]],
        [1=>$row[0],0=>$row[1],2=>$row[2],3=>$row[3]]],$columns,$existing);
    expect($reordered[0]['id']===$id && $reordered[0]['source_name']==='Aviso confirmado','Excel admite ordenar columnas conservando encabezados');
    $dateColumns=[['key'=>'id','label'=>'ID'],['key'=>'consulted_at','label'=>'Fecha']];
    $dates=ComparableExcelInput::rows([[$cell('ID'),$cell('Fecha')],[$cell($id),$cell('46297')]],$dateColumns,$existing);
    expect($dates[0]['consulted_at']==='2026-10-02','fecha serial de Excel se convierte sin cambiar día');
    $path=__DIR__.'/.runtime/excel-input-test.xlsx'; $ns='http://schemas.openxmlformats.org/spreadsheetml/2006/main';
    $meta=['format'=>'sucasa-comparables-2','appraisal'=>$aid,'scope'=>'','version'=>4,'sheet'=>'Datos','columns'=>$columns];
    $write=static function(array $context) use($path,$ns,$id): void {
        $zip=new ZipArchive(); $zip->open($path,ZipArchive::CREATE|ZipArchive::OVERWRITE);
        $zip->addFromString('xl/workbook.xml','<workbook xmlns="'.$ns.'" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets><sheet name="Datos" sheetId="1" r:id="rId1"/><sheet name="_SuCasa" sheetId="2" r:id="rId2"/></sheets></workbook>');
        $zip->addFromString('xl/_rels/workbook.xml.rels','<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Target="worksheets/sheet1.xml"/><Relationship Id="rId2" Target="/xl/worksheets/sheet2.xml"/></Relationships>');
        $zip->addFromString('xl/sharedStrings.xml','<sst xmlns="'.$ns.'"><si><t>ID</t></si><si><t>Fuente</t></si><si><t>'.$id.'</t></si><si><r><t>Aviso </t></r><r><t>confirmado</t></r></si></sst>');
        $zip->addFromString('xl/worksheets/sheet1.xml','<worksheet xmlns="'.$ns.'"><sheetData><row r="1"><c r="A1" t="s"><v>0</v></c><c r="B1" t="s"><v>1</v></c></row><row r="2"><c r="A2" t="s"><v>2</v></c><c r="B2" t="s"><v>3</v></c></row></sheetData></worksheet>');
        $zip->addFromString('xl/worksheets/sheet2.xml','<worksheet xmlns="'.$ns.'"><sheetData><row r="1"><c r="A1" t="inlineStr"><is><t>'.htmlspecialchars(json_encode($context),ENT_XML1|ENT_QUOTES,'UTF-8').'</t></is></c></row></sheetData></worksheet>');
        $zip->close();
    };
    try {
        $write($meta); $result=ComparableExcelInput::read($path,$aid,'',4,$existing);
        expect($result[0]['source_name']==='Aviso confirmado','lector ZIP maneja sharedStrings texto enriquecido y rutas guardadas por Excel');
        expectStatus(409,fn()=>ComparableExcelInput::read($path,$aid,'',5,$existing),'Excel antiguo rechaza versión posterior sin sobrescribir');
        expectStatus(422,fn()=>ComparableExcelInput::read($path,$aid,'otra',4,$existing),'Excel de otra unidad no se mezcla');
        expectStatus(422,fn()=>ComparableExcelInput::read($path,'otro','',4,$existing),'Excel de otro expediente se rechaza');
        $zip=new ZipArchive();$zip->open($path);$zip->addFromString('xl/sharedStrings.xml','<!DOCTYPE sst [<!ENTITY x SYSTEM "file:///etc/passwd">]><sst/>');$zip->close();
        expectStatus(422,fn()=>ComparableExcelInput::read($path,$aid,'',4,$existing),'lector Excel rechaza entidades externas');
    } finally { if(is_file($path)) unlink($path); }
})();
