<?php
declare(strict_types=1);
(static function():void {
    $catalog=\App\Services\ComparablePortalProfiles::all();
    foreach ($catalog as $portal=>$types) foreach ($types as $type=>$profile) {
        expect(isset(\App\Services\ComparablePortalProfiles::portals()[$portal],\App\Services\ComparablePortalProfiles::types()[$type]), 'evidencia pertenece a portal y tipo reconocidos');
        expect(str_starts_with($profile['url'],'https://') && $profile['basics']!==[] && $profile['notes']!==[], 'perfil observado conserva fuente y límites');
    }
    expect(str_contains(\App\Services\ComparablePortalProfiles::profile('ciencuadras','lote')['notes'][0],'171'), 'lote preserva diferencia terreno/construcción documentada');
    expect(str_contains(\App\Services\ComparablePortalProfiles::profile('fincaraiz','oficina')['notes'][0],'piso 19'), 'conserva contradicción interna de la oficina');
    foreach ([['mercadolibre','oficina'],['ciencuadras','consultorio'],['fincaraiz','deposito']] as [$portal,$type]) {
        $unknown=\App\Services\ComparablePortalProfiles::profile($portal,$type);
        expect($unknown['url']==='' && $unknown['basics']===[] && str_contains($unknown['status'],'Pendiente'), 'combinación sin evidencia no hereda esquema');
    }
    expect(\App\Services\ComparablePortalProfiles::defaultType('Depósito / cuarto útil')==='deposito'
        && \App\Services\ComparablePortalProfiles::defaultType('Celda de parqueo')==='parqueadero'
        && \App\Services\ComparablePortalProfiles::defaultType('Tipología pendiente')==='', 'default reconoce anexos sin inventar tipo');
    $guide=['type_label'=>'Oficina'];
    ob_start(); require BASE_PATH.'/app/Views/appraisals/methodology-portal-profiles.php'; $html=ob_get_clean();
    expect(str_contains($html,'2026-10-04') && str_contains($html,'Consultar ficha') && !str_contains($html,'x-html'), 'referencia fechada y enlazada se presenta sin html externo');
    expect(!str_contains($html,'<form') && !str_contains($html,'name="'), 'consulta no escribe en captura ni autoguarda configuración de referencia');
})();
