<?php
declare(strict_types=1);
return [
    'metrocuadrado'=>['oficina'=>[
        'status'=>'Ficha pública documentada (venta)',
        'basics'=>['Código → identificador del aviso.', 'Precio / administración → importes separados.',
            'Área construida / Área privada → mantener etiquetas.', 'Barrio / ciudad → ubicación anunciada.'],
        'descriptive'=>['Baños / Garajes / Estrato → atributos publicados.', 'Antigüedad → intervalo, p. ej. más de 20 años.',
            'Descripción / características → piso, terraza, cocineta y citófono.'],
        'notes'=>['Área privada no demuestra base privada construida. Piso o terraza pueden estar únicamente en el texto.'],
        'url'=>'https://www.metrocuadrado.com/inmueble/venta-oficina-cartagena-de-indias-boca-grande-1-banos-1-garajes/120-M5487250',
    ]],
    'properati'=>['local'=>[
        'status'=>'Ficha pública documentada (venta)',
        'basics'=>['Precio → importe de la operación anunciada.', 'Tipo de propiedad / ubicación → categoría y zona.',
            'Área construida / Área total / encabezado → conservar bases por separado.'],
        'descriptive'=>['Año de construcción → año, no edad ni intervalo.', 'Estrato / descripción → atributos y condiciones comerciales.'],
        'notes'=>['El anuncio incluye canon en el texto aunque se presenta en venta: no sustituir precio de venta por renta.'],
        'url'=>'https://www.properati.com.co/detalle/14032-32-9274-f994bf200c10-a43b867d-8811-34d9',
    ]],
];
