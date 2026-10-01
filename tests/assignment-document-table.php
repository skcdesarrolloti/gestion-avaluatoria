<?php
declare(strict_types=1);
use App\Services\{AppraisalDocumentTable, AppraisalObjectText, AppraisalChapterOneReport};

$legacyDocuments = ['source_documents_json' => '["escritura_publica","fotografias"]', 'source_documents' => 'Observación histórica.'];
$legacyRows = AppraisalDocumentTable::rows($legacyDocuments);
expect(str_contains($legacyRows['escritura_publica']['text'], 'registro anterior') && $legacyRows['planos']['text'] === '', 'tabla conserva selección anterior sin inventar documentos faltantes');
$newDocuments = $legacyDocuments + ['source_document_details' => AppraisalDocumentTable::input([
    'escritura_publica' => 'Escritura 894, 29-04-2026, Notaría 37 de Bogotá.', 'planos' => 'No suministrado', 'tipo_adquisicion' => '0151 Resciliación',
])];
expect(AppraisalDocumentTable::rows($newDocuments)['escritura_publica']['text'] === 'Escritura 894, 29-04-2026, Notaría 37 de Bogotá.', 'detalle confirmado prevalece sobre marca histórica');
expectStatus(422, fn () => AppraisalDocumentTable::input(['planos' => str_repeat('a', 1001)]), 'tabla rechaza detalle excesivo sin truncarlo');
$newDocuments += ['base_valor' => 'mercado', 'finalidad' => 'negociacion', 'tipo_inmueble' => 'oficina', 'direccion' => 'Calle de prueba', 'municipio' => 'Cartagena'];
$report = (new AppraisalChapterOneReport())->build($newDocuments, [], []);
$objectSection = array_values(array_filter($report['sections'], fn ($section) => str_starts_with($section[0], '1.5 ')))[0];
expect($objectSection[1] === AppraisalObjectText::build($newDocuments), 'vista previa y entregable usan idéntico generador del objeto');
expect(str_contains($report['text'], 'No suministrado') && str_contains($report['text'], 'Observación histórica.'), 'entregable conserva detalle nuevo y observaciones históricas');
expect(!str_contains(AppraisalObjectText::build([]), 'valor de mercado'), 'objeto incompleto no inventa base de valor');
