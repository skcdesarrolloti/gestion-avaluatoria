<?php
$currentStep = 'economico';
$narrative = [
    'chapter' => '6',
    'eyebrow' => 'Numeral 6 · Aspecto económico',
    'title' => 'Aspecto económico',
    'badge' => 'Numeral 6',
    'intro' => 'Estructura la dinámica económica, mercado objetivo y señales de valorización sin mezclarla con cálculos de valor.',
    'formAction' => url('avaluos/' . $record['id'] . '/aspecto-economico'),
    'autosave' => url('avaluos/' . $record['id'] . '/aspecto-economico/autoguardar'),
    'sections' => $economicSections ?? [],
    'data' => $economicProfile ?? [],
    'midasSupport' => $midasNarrativeSupport ?? [],
    'message' => $economicMessage ?? '',
    'error' => $economicError ?? '',
];
require BASE_PATH . '/app/Views/appraisals/narrative-chapter-form.php';
