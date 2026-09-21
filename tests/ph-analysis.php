<?php
// Included by run.php; fixtures are synthetic and use its in-memory database.
$analyzer = new \App\Services\AppraisalPhDocumentAnalyzer();
$source = "[Documento: reglamento.pdf]\n[Cobertura: 244 páginas procesadas]\n[Página 1]\n"
    . "NOTARIA 61. Teléfono: 1234567. EDIFICIO PRUEBA.\n"
    . "[Página 100]\nOficina 101 coeficiente 2.5%\nOficina 102 coeficiente 3.5%\n"
    . "[Página 244]\nLa piscina y los ascensores son bienes comunes. Prohibiciones: no se permite cambiar la fachada.";
$result = $analyzer->analyze($source, ['reglamento.pdf'], 'oficinas', ['private_unit'=>'Oficina 102']);
expect(($result['core']['coefficient'] ?? '') === '3.5%', 'PH asocia coeficiente solo a unidad expresa');
expect(!isset($analyzer->analyze($source, ['reglamento.pdf'], '')['core']['coefficient']), 'PH no toma primer coeficiente sin bien sujeto');
expect(!isset($result['core']['administration_phone']), 'PH no confunde teléfono de notaría con administración');
expect(str_contains($result['common_areas']['piscina']['notes'], 'p. 244'), 'PH encuentra evidencia en última página y conserva fuente');
expect($result['common_areas']['piscina']['status'] === 'warn' && $result['photos']['porteria']['status'] === '',
    'PH no certifica estado físico ni fotografías por una mención');
$ambiguous = $analyzer->analyze($source . "\nOficina 102 coeficiente 4.5%", [], '', ['private_unit'=>'Oficina 102']);
expect(!isset($ambiguous['core']['coefficient']), 'PH no decide entre coeficientes incompatibles');
$client = ['name'=>'a.pdf', 'size'=>10, 'total'=>2, 'pages'=>[
    ['page'=>1, 'text'=>'Reglamento de propiedad horizontal con contenido suficiente.', 'confidence'=>95],
    ['page'=>2, 'text'=>'', 'confidence'=>0]]];
$clientText = \App\Services\AppraisalPhClientText::text($client, 'a.pdf', 10);
expect(str_contains($clientText, '[Página 2]') && str_contains($clientText, 'revisar original): 2'), 'PH registra páginas vacías sin afirmar lectura perfecta');
$rejected = false;
try { \App\Services\AppraisalPhClientText::text($client, 'otro.pdf', 10); } catch (RuntimeException) { $rejected = true; }
expect($rejected, 'PH impide mezclar texto entre documentos');
$client['total'] = 3; $rejected = false;
try { \App\Services\AppraisalPhClientText::text($client, 'a.pdf', 10); } catch (RuntimeException) { $rejected = true; }
expect($rejected, 'PH rechaza cobertura incompleta');
$blank = "[Documento: EDIFICIO INVENTADO.pdf]\n[Cobertura: 2 páginas procesadas]\n"
    . "Páginas con lectura baja o sin texto (revisar original): 1, 2\n[Página 1]\n[Página 2]\n";
expect(!$analyzer->analyze($blank, ['EDIFICIO INVENTADO.pdf'], '')['has_text'], 'PH no trata metadatos ni avisos como texto del documento');
$prior = $phRepo->profile(str_repeat('d', 32), 1);
$version = (int) $prior['version'];
$phRepo->save(str_repeat('d', 32), 1, array_replace($prior, ['ph_name'=>'Edición del analista']), $version);
expectStatus(409, fn () => $phRepo->save(str_repeat('d', 32), 1, $prior, $version), 'PH rechaza autoguardado obsoleto');
expectStatus(409, fn () => $phRepo->mergeAnalysis(str_repeat('d', 32), 1, $result, $version), 'PH rechaza análisis con versión obsoleta');
expect($phRepo->profile(str_repeat('d', 32), 1)['ph_name'] === 'Edición del analista', 'PH conserva edición tras conflicto');
