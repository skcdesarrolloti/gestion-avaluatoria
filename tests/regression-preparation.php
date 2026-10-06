<?php
declare(strict_types=1);
use App\Services\ComparableRegressionPreparation;
use App\Core\HttpException;
$code=json_encode(['published:antiguedad','9 a 15 años']);
$prepared=ComparableRegressionPreparation::normalize(['basis'=>'offer','confirmed'=>true,'codes'=>[$code=>'2']]);
expect(((array)$prepared['codes'])[$code]==='2','Preparación conserva código confirmado');
foreach ([['basis'=>'bad','confirmed'=>true,'codes'=>[]],['basis'=>'offer','confirmed'=>'si','codes'=>[]],['basis'=>'offer','confirmed'=>true,'codes'=>[$code=>'texto']]] as $invalid) {
    try {ComparableRegressionPreparation::normalize($invalid);expect(false,'Rechaza preparación inválida');}
    catch(HttpException $error){expect($error->status===422,'Error de preparación');}
}
