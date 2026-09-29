<?php
$currentStep = 'restrictivas';
$narrative = [
    'chapter' => '7',
    'eyebrow' => 'Numeral 7 · Condiciones restrictivas',
    'title' => 'Condiciones restrictivas',
    'badge' => 'Numeral 7',
    'intro' => 'Consolida salvedades físicas, ambientales, sociales, viales y jurídicas que puedan incidir en el valor o la comercialización.',
    'formAction' => url('avaluos/' . $record['id'] . '/condiciones-restrictivas'),
    'autosave' => url('avaluos/' . $record['id'] . '/condiciones-restrictivas/autoguardar'),
    'sections' => $restrictiveSections ?? [],
    'data' => $restrictiveProfile ?? [],
    'message' => $restrictiveMessage ?? '',
    'error' => $restrictiveError ?? '',
];
require BASE_PATH . '/app/Views/appraisals/narrative-chapter-form.php';
