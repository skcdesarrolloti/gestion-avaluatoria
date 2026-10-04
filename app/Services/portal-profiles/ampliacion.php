<?php
declare(strict_types=1);
// Public indexed listings: evidence of fields, not current availability or a publisher schema.
$indexed='Referencia pública indexada · confirmar vigencia del aviso';
return [
    'properati'=>[
        'oficina'=>[
            'status'=>$indexed,
            'basics'=>['Precio, operación, categoría y ubicación anunciada.', 'Superficie del encabezado, construida y total; fecha y anunciante.'],
            'descriptive'=>['Baños, año de construcción, estado y nivel.', 'Vista y ascensor en características; distribución y uso en la descripción.'],
            'notes'=>['La ficha dice oficina; el texto describe un consultorio arrendado. Verificar uso y operación.', 'Nivel 2 no acredita por sí solo el piso de ubicación. No se documentó área privada construida.'],
            'url'=>'https://www.properati.com.co/detalle/14032-32-e1ea-eb3d7c57a5b1-f2a253e4-8c3f-4b90',
        ],
        'apartamento'=>[
            'status'=>$indexed,
            'basics'=>['Precio, categoría, ubicación, operación y anunciante.', 'Superficies del encabezado, construida y total.'],
            'descriptive'=>['Habitaciones, baños completos y medio baño; año de construcción y estrato.', 'Piso, vista, ascensor, depósito y parqueaderos también aparecen en texto; equipamiento del edificio.'],
            'notes'=>['Separar medio baño de baños completos. Dos parqueaderos lineales no son dos independientes.', 'Las áreas publicadas no prueban la base privada ni la naturaleza jurídica del depósito.'],
            'url'=>'https://www.properati.com.co/detalle/14032-32-ce8-729e997e9fd3-19953b0-b18e-72a8',
        ],
        'casa'=>[
            'status'=>$indexed,
            'basics'=>['Precio, ubicación, categoría, operación y anunciante.', 'Área construida en casilla y terreno en la descripción.'],
            'descriptive'=>['Habitaciones, baños, año de construcción, nivel y estrato.', 'Composición de unidades, jardín y posibilidades de almacenamiento en texto.'],
            'notes'=>['La casa se describe con dos apartamentos. Conservar esa composición antes de comparar.', 'Nivel no equivale automáticamente a número de pisos; terreno y construcción son superficies diferentes.'],
            'url'=>'https://www.properati.com.co/detalle/14032-32-d3f5-7c5d618f9454-19998d3-81a2-789e',
        ],
        'lote'=>[
            'status'=>$indexed,
            'basics'=>['Precio, ubicación, tipo, operación y anunciante.', 'Superficie del terreno y encabezado.'],
            'descriptive'=>['Estrato y servicios anunciados; accesos y distancias en la descripción.', 'Puede aparecer año de construcción aunque la categoría sea lote.'],
            'notes'=>['No convertir el año rotulado como construcción en edad del terreno.', 'Distancias y ubicación anunciada no acreditan coordenadas del predio ni edificabilidad.'],
            'url'=>'https://www.properati.com.co/detalle/14032-32-868a-91739603b49e-18ff08e-9669-75f4',
        ],
    ],
    'metrocuadrado'=>[
        'apartamento'=>[
            'status'=>$indexed,
            'basics'=>['Código, precio, administración, barrio y ciudad.', 'Superficies construida y privada con etiquetas separadas.'],
            'descriptive'=>['Habitaciones, baños, garajes, estrato y antigüedad por intervalo.', 'Piso, vista, ascensor, depósito, acabados y tipo de parqueadero en características o texto.'],
            'notes'=>['Área privada no acredita automáticamente área privada construida.', 'La oferta consultada menciona un posible cargo adicional según forma de pago: conservar y corroborar.'],
            'url'=>'https://www.metrocuadrado.com/inmueble/venta-apartamento-chia-1-habitaciones-1-banos-1-garajes/22563-M6656496',
        ],
        'casa'=>[
            'status'=>$indexed,
            'basics'=>['Código, precio, administración, barrio y ciudad.', 'Superficies construida y privada; composición en descripción.'],
            'descriptive'=>['Habitaciones, baños, garajes, estrato y intervalo de antigüedad.', 'Pisos, niveles, jardín, balcones, cuarto de servicio y depósito en texto.'],
            'notes'=>['169 m² en casilla frente a 138 m² construidos en texto: diferencia por verificar.', 'Tres pisos y cinco niveles no son el mismo factor. No asignar una edad exacta al intervalo.'],
            'url'=>'https://www.metrocuadrado.com/inmueble/venta-casa-bogota-casa-blanca-3-habitaciones-3-banos-2-garajes/603-M6013779',
        ],
        'lote'=>[
            'status'=>$indexed,
            'basics'=>['Código, precio, ubicación y superficie publicada como privada.', 'Categoría y estrato anunciados.'],
            'descriptive'=>['Construcciones restantes, servicios y restricciones descritos en texto.'],
            'notes'=>['La etiqueta privada en un lote no demuestra régimen PH.', 'La ficha menciona muros, demolición y patrimonio cultural: requieren soporte; no inferir permiso ni costo.'],
            'url'=>'https://www.metrocuadrado.com/inmueble/venta-lote-bogota-atanasio-girardot/3222-M6048719',
        ],
        'local'=>[
            'status'=>$indexed,
            'basics'=>['Código, precio, administración, barrio y ciudad.', 'Áreas construida y privada.'],
            'descriptive'=>['Baño, cocina, piso, acabados, seguridad y aire acondicionado.', 'Estrato comercial y antigüedad rotulada como remodelado.'],
            'notes'=>['Comercial no es un estrato numérico; remodelado no es una edad.', 'Revisar administración y condiciones comerciales en todas las secciones, no sólo el encabezado.'],
            'url'=>'https://www.metrocuadrado.com/inmueble/venta-local-comercial-medellin-la-candelaria-1-banos/10710-M4196692',
        ],
        'bodega'=>[
            'status'=>$indexed,
            'basics'=>['Código, precio, barrio, ciudad y superficies construida y privada.', 'Terreno y distribución de áreas en la descripción.'],
            'descriptive'=>['Estrato, intervalo de antigüedad, tipo de bodega, oficinas y piso resistente.', 'Alturas a viga y cumbrera, portón, potencia eléctrica y accesos en texto.'],
            'notes'=>['600 m² construidos, 540 m² de lote y 492 m² libres tienen bases diferentes.', 'Distinguir altura a viga de cumbrera. Un cero en número de bodega no acredita ausencia física.'],
            'url'=>'https://www.metrocuadrado.com/inmueble/venta-bodega-bogota-estacion-central/3561-M6191732',
        ],
    ],
    'mercadolibre'=>[
        'apartamento'=>[
            'status'=>'Referencia parcial indexada · confirmar vigencia y campos faltantes',
            'basics'=>['Identificador de publicación, categoría, operación y sector anunciado.'],
            'descriptive'=>['Estacionamiento en ficha; superficie, habitaciones, baños, reforma, acabados y cuarto útil en texto.'],
            'notes'=>['Se observaron 72 m², tres habitaciones y dos baños en la descripción. La base privada no está acreditada.', 'Esta evidencia parcial no certifica precio, administración, edad ni completitud de todos los campos.'],
            'url'=>'https://inmueble.mercadolibre.com.co/MCO-3843758466-apartamento-en-venta-sector-la-ayura-en-envigado-reformado-_JM',
        ],
        'casa'=>[
            'status'=>$indexed,
            'basics'=>['Número de publicación, anunciante, precio, ubicación y fecha relativa.', 'Áreas total y construida por separado.'],
            'descriptive'=>['Habitaciones, baños, estacionamientos, edad, ambientes, depósitos y cantidad de pisos.', 'Jardín, conjunto cerrado y composición de apartamentos en texto.'],
            'notes'=>['77 m² totales en casilla frente a lote de 72 m² en descripción: conservar la discrepancia.', '216 m² construidos no son el área de terreno. La condición jurídica de la construcción requiere soporte.'],
            'url'=>'https://casa.mercadolibre.com.co/MCO-2137412815-venta-de-casa-portal-del-divino-puerta-al-llano-usme-bogota-de-3-pisos-con-apartamentos-independientes-_JM',
        ],
        'local'=>[
            'status'=>$indexed,
            'basics'=>['Publicación, precio, operación, ubicación, anunciante y fecha relativa.', 'Superficie total y construida; administración.'],
            'descriptive'=>['Baños, estacionamientos y edad publicada.', 'Niveles, mezanine, oficinas, cocineta y disposición comercial en texto.'],
            'notes'=>['Edad cero requiere confirmación, no asumir obra nueva.', 'El texto describe dos locales unidos físicamente pero independientes jurídicamente: registrar composición.', '264 m² en casilla y 264,2 m² en texto se conservan sin sustituir automáticamente.'],
            'url'=>'https://inmueble.mercadolibre.com.co/MCO-1686703707-local-comercial-en-venta-sector-chapinero-bogota-_JM',
        ],
        'oficina'=>[
            'status'=>$indexed,
            'basics'=>['Número de publicación y código interno del anunciante; conservar ambos.', 'Precio, operación, ubicación, anunciante, fecha relativa, área total y construida.'],
            'descriptive'=>['Baños, estacionamientos y antigüedad en años.', 'Ascensor y recepción como equipamiento; estado, iluminación y seguridad en texto.'],
            'notes'=>['181 m² totales y construidos no acreditan área privada PH.', 'Publicación 1891003707 y código C62610 identifican fuentes distintas; no son una clave común entre portales.', 'Fecha relativa y edad pertenecen a la versión consultada; confirmar vigencia.'],
            'url'=>'https://inmueble.mercadolibre.com.co/MCO-1891003707-oficina-en-venta-bocagrande-cartagena-_JM',
        ],
        'edificio'=>[
            'status'=>$indexed,
            'basics'=>['Identificador de publicación, operación, ubicación, anunciante y áreas total y construida.'],
            'descriptive'=>['Baños, estacionamientos, antigüedad, administración, estrato y orientación.', 'Cantidad y composición de unidades, accesos y servicios en descripción.'],
            'notes'=>['La suma de áreas de unidades descritas no explica por sí sola los 680 m² publicados.', 'No convertir piso de una unidad en cantidad de pisos del edificio.'],
            'url'=>'https://inmueble.mercadolibre.com.co/MCO-4337513868-edificio-en-venta-en-el-guamo-_JM',
        ],
        'finca'=>[
            'status'=>$indexed,
            'basics'=>['Número de publicación y referencia interna; precio, operación y ubicación.', 'Superficie total y construida; administración y estrato.'],
            'descriptive'=>['Habitaciones, baños, estacionamientos, depósito y antigüedad.', 'Acceso, distancia al asfalto, forma del terreno y construcción descrita.'],
            'notes'=>['El título dice casa campestre y la categoría finca: conservar ambos.', 'Cuatro estacionamientos en casilla frente a ocho en texto requieren aclaración.', 'Antigüedad publicada no se actualiza automáticamente; total no sustituye área construida.'],
            'url'=>'https://inmueble.mercadolibre.com.co/MCO-3173284708-casa-campestre-unidad-cerrada-en-el-retiro-_JM',
        ],
    ],
];
