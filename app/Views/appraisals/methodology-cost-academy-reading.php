<?php
$methodologyGuides=array_values(array_filter(
    (new \App\Services\AppraisalMethodologyResolution941Guide())->methodGuides(),
    static fn($guide)=>$guide['key']==='costo'));
$prefix='C';
require __DIR__.'/valuation-methodology-method-guides.php';