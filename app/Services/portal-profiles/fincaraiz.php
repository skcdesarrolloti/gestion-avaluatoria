<?php
declare(strict_types=1);
$base=['Código Fincaraíz → identificador del anuncio (no del inmueble entre portales).',
    'Precio de Venta / administración → importes publicados; conservar por separado.',
    'Ubicación Principal → localización anunciada; las ubicaciones asociadas no confirman dirección.',
    'Área Construida / Área Privada → áreas con etiquetas originales; privada no acredita privada construida.'];
$shared=['Baños / Parqueaderos / Estrato → cantidades y estrato declarados.',
    'Antigüedad → intervalo publicado, no edad exacta ni año de construcción.',
    'Estado / Piso N° / Cantidad de Pisos → atributos distintos; ¡Pregúntale! significa no informado.',
    'Descripción / galería / anunciante → conservar texto, fotos y autor con su procedencia.'];
$row=static fn(string $path,array $extra,array $notes):array=>['status'=>'Ficha pública documentada (venta)',
    'basics'=>$base,'descriptive'=>array_merge($shared,$extra),'notes'=>$notes,'url'=>'https://www.fincaraiz.com.co/'.$path];
return ['fincaraiz'=>[
    'oficina'=>$row('oficina-en-venta-en-bocagrande-cartagena/194156871',
        ['Cantidad de Ambientes → espacios, no habitaciones de vivienda.', 'Descripción → edificio, recepción, ascensores, climatización y parqueadero.'],
        ['El aviso muestra 105 m² y piso 18 en ficha; 105,5 m² y piso 19 en descripción. Conservar ambas versiones y solicitar aclaración.']),
    'apartamento'=>$row('apartamento-en-venta-en-el-poblado-medellin/194352749',
        ['Habitaciones → revisar si incluye servicio.', 'Descripción → niveles, terraza y año de construcción.'],
        ['La ficha y la descripción distinguen piso y niveles de un penthouse. No convertir terraza en área privada construida.']),
    'casa'=>$row('casa-en-venta-en-prado-veraniego-bogota/194351329',
        ['Área de Terreno → terreno publicado.', 'Habitaciones → cantidad declarada.', 'Descripción → terraza, jardín, depósito y parqueaderos cubiertos/descubiertos.'],
        ['La descripción aporta área privada no diligenciada en la casilla. No sustituir terreno por construcción aunque tengan cifras iguales.']),
    'lote'=>$row('lote-en-venta-en-galerias-bogota/194255017',
        ['Área de Terreno → superficie del lote.', 'Descripción → construcciones existentes y condiciones urbanísticas anunciadas.'],
        ['Publicado como casa lote: tipo Lote no demuestra terreno vacío. La autorización de pisos anunciada necesita soporte independiente.']),
    'local'=>$row('local-en-venta-en-laureles-medellin/191857783',
        ['Cantidad de Ambientes → espacios comerciales.', 'Descripción → código interno de la inmobiliaria y área anunciada.'],
        ['Área privada 86 m² en ficha y aproximadamente 87 m² en texto. El código del anunciante es diferente del código Fincaraíz.']),
    'bodega'=>$row('bodega-en-venta-en-colombia-bogota/10992121',
        ['Cantidad de Ambientes → espacios de trabajo.', 'Descripción → alturas, portón, energía, niveles, tanques y capacidad del piso.'],
        ['El encabezado presenta 430 m² pero casillas de áreas sin dato. No atribuir a esos 430 m² una base privada o construida no indicada.']),
    'consultorio'=>$row('consultorio-en-venta-en-envigado/193618790',
        ['Cantidad de Ambientes → espacios del consultorio.', 'Descripción → adecuaciones, accesibilidad, portería y parqueadero cubierto.'],
        ['Parqueaderos aparece sin dato en ficha y se describe uno independiente cubierto. Esa palabra no demuestra matrícula independiente.']),
    'edificio'=>$row('edificio-en-venta-en-la-candelaria-medellin/7885452',
        ['Habitaciones / Baños → confirmar si corresponden al total o a cada unidad.', 'Descripción → terreno, pisos y apartamentos por piso.'],
        ['Terreno de 140 m² y cinco pisos figuran en texto. Los baños por apartamento no son automáticamente baños totales del edificio.']),
]];
