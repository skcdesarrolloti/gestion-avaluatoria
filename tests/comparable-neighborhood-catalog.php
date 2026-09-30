<?php
declare(strict_types=1);
(static function (): void {
 $db=new PDO('sqlite::memory:'); $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_ASSOC);
 $db->exec("CREATE TABLE master_cities(id TEXT, active TEXT); CREATE TABLE master_neighborhoods(id TEXT, name TEXT, city_id TEXT, active TEXT);
 INSERT INTO master_cities VALUES ('a','Si'),('b','Si');
 INSERT INTO master_neighborhoods VALUES ('1','Bocagrande','a','Si'),('2','Castillogrande','a','Si'),('3','Inactivo','a','No'),('4','Bocagrande','b','Si');");
 $repo=new \App\Models\GeoMasterRepository($db);
 expect(count($repo->activeNeighborhoodsForCity('a'))===2,'catalogo de busqueda solo barrios activos de la ciudad');
 expect($repo->marketNeighborhood('1','a')==='Bocagrande','busqueda resuelve nombre canonico desde ID');
 expectStatus(422,fn()=>$repo->marketNeighborhood('4','a'),'rechaza barrio de otra ciudad');
 expectStatus(422,fn()=>$repo->marketNeighborhood('3','a'),'rechaza barrio inactivo');
 expectStatus(422,fn()=>$repo->marketNeighborhood('Bocagrande','a'),'rechaza texto libre como ID de barrio');
})();
