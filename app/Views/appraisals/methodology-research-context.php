<?php
$researchContext=\App\Services\ComparableSearchContext::forMethod($record,$units ?? [],$componentKey,$method ?? 'mercado');
$researchType=\App\Services\ComparablePortalProfiles::defaultType((string)($researchContext['tipo_inmueble'] ?? ''));
$researchCatalog=\App\Services\ResearchFactorScaleInput::catalog(\App\Services\ResearchFactorCatalog::forType($researchType,$components[$componentKey]['part'] ?? '',false,true),$factorScales ?? []);
foreach (['landscape_view','panoramic_view'] as $previousViewKey) if (!isset($selected['research_plan']['factors'][$previousViewKey])) unset($researchCatalog[$previousViewKey]);
if ($researchType==='apartamento') $researchCatalog=\App\Services\ApartmentResearchFactors::preserve($researchCatalog,$selected['research_plan']['factors'] ?? []);
if ($researchType==='casa') $researchCatalog=\App\Services\HouseResearchFactors::preserve($researchCatalog,$selected['research_plan']['factors'] ?? []);
if ($researchType==='oficina') $researchCatalog=\App\Services\OfficeResearchFactors::preserve($researchCatalog,$selected['research_plan']['factors'] ?? []);
if ($researchType==='local') $researchCatalog=\App\Services\LocalResearchFactors::preserve($researchCatalog,$selected['research_plan']['factors'] ?? []);
$researchCatalog=\App\Services\UserResearchFactors::preserve($researchCatalog,$selected['research_plan']['factors'] ?? []);
$researchUnit=$components[$componentKey]['unit'] ?? [];
$researchPart=$components[$componentKey]['part'] ?? '';
if (($researchContext['regimen_ph'] ?? '')!=='si' && isset($researchCatalog['area'])) {
    $researchCatalog['area']['label']='Área construida de comparación';
    $researchCatalog['area']['subject']='built_area_adopted_m2';
}
$researchPlan=['target_ratio'=>$selected['research_plan']['target_ratio'] ?? 10,'factors'=>[],'assessments'=>$selected['research_plan']['assessments'] ?? []];
foreach ($researchCatalog as $key=>$factor) $researchPlan['factors'][$key]=array_replace(
    ['decision'=>$key==='destination'?'filter':'','kind'=>$factor['kind'],'collection'=>'mixed','reason'=>'','definition'=>$factor['why'],'categories'=>$factor['categories']],$selected['research_plan']['factors'][$key] ?? []);
foreach (['area','built','land'] as $areaKey) if (isset($researchPlan['factors'][$areaKey])) $researchPlan['factors'][$areaKey]['decision']='defer';
$researchFactors=array_diff_key($researchCatalog,array_flip(['destination','area','built','land']));
$researchEvidence=\App\Services\ResearchPlanEvidence::build($researchCatalog,$researchUnit,$comparableRows,$researchContext,$subject,$researchPart);
$researchEvidence['scalePolicies']=$researchCatalog;
$researchConfig=['plan'=>$researchPlan,'catalog'=>$researchCatalog,'evidence'=>$researchEvidence];
?>
