<?php
(function (): void {
    $fixture=require __DIR__.'/course-execution-fixture.php';
    $normalized=\App\Services\ComparableCaptureDetail::normalize(['analysis_course_execution'=>$fixture]);
    $saved=json_decode($normalized['analysis_course_execution'],false,8,JSON_THROW_ON_ERROR);
    expect($saved->step===3 && $saved->result->summary->q1===125 && $saved->notes instanceof stdClass,'ejecución conserva punto de avance, resultados y notas editables');
    expect(!array_key_exists('analysis_course_execution',\App\Services\ComparableCaptureDetail::normalize([])),'lector anterior no transforma ejecución omitida en borrado');
    foreach (['v'=>2,'step'=>7,'confidence'=>.8] as $key=>$value) {
        $bad=json_decode($fixture,true);$bad[$key]=$value;
        expectStatus(422,fn()=>\App\Services\ComparableCaptureDetail::normalize(['analysis_course_execution'=>json_encode($bad)]),'ejecución rechaza '.$key.' inválido');
    }
})();
