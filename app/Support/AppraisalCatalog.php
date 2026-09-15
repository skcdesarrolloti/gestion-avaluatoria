<?php
declare(strict_types=1);
namespace App\Support;

final class AppraisalCatalog
{
    public static function textFields(): array
    {
        return [
            'titulo' => ['Título de la ficha', 'Ej. Apartamento en Laureles', 160],
            'direccion' => ['Dirección', 'Ej. Calle 10 # 43-20', 220],
            'municipio' => ['Municipio', 'Ej. Medellín', 120],
            'observaciones' => ['Observaciones', 'Describe el encargo, información pendiente o alertas iniciales.', 4000],
        ];
    }

    public static function selectFields(): array
    {
        return [
            'tipo' => ['Tipo de avalúo', 'Selecciona tipo de avalúo', 40, 'Define qué se valora metodológicamente.', [
                'comercial' => 'Avalúo comercial', 'posesion' => 'Avalúo de posesión',
                'mejoras' => 'Avalúo de mejoras', 'remate' => 'Avalúo para remate',
                'negociacion' => 'Avalúo para negociación', 'conciliacion' => 'Avalúo para conciliación',
                'zona_franca' => 'Avalúo en zona franca', 'catastral' => 'Avalúo catastral',
                'seguro' => 'Avalúo de seguro', 'hipotecario' => 'Avalúo hipotecario',
                'interno' => 'Avalúo interno de análisis',
            ]],
            'tipo_derecho' => ['Tipo de derecho', 'Selecciona tipo de derecho', 40, 'Precisa el derecho jurídico objeto de valoración.', [
                'dominio_pleno' => 'Dominio pleno', 'nuda_propiedad' => 'Nuda propiedad',
                'usufructo' => 'Usufructo', 'posesion' => 'Posesión', 'mejoras' => 'Mejoras',
                'derecho_fiduciario' => 'Derecho fiduciario', 'arrendatario' => 'Arrendatario',
                'otro' => 'Otro',
            ]],
            'tipo_negocio' => ['Tipo de negocio', 'Selecciona tipo de negocio', 30, 'Separa referencias de venta y arriendo.', [
                'venta' => 'Venta', 'arriendo' => 'Arriendo',
            ]],
            'destinacion' => ['Destinación', 'Selecciona destinación', 40, 'Ubica el uso económico principal del bien.', [
                'residencial' => 'Residencial', 'comercial' => 'Comercial',
                'institucional' => 'Institucional', 'industrial' => 'Industrial',
                'dotacional' => 'Dotacional', 'lote_suelo' => 'Lote / suelo', 'mixto' => 'Mixto',
            ]],
            'tipo_inmueble' => ['Tipo de inmueble', 'Selecciona tipo de inmueble', 40, 'Activa atributos y ficha técnica por tipología.', [
                'casa' => 'Casa', 'apartamento' => 'Apartamento', 'lote' => 'Lote', 'local' => 'Local',
                'oficina' => 'Oficina', 'bodega' => 'Bodega', 'consultorio' => 'Consultorio',
                'edificio' => 'Edificio', 'finca' => 'Finca', 'hotel' => 'Hotel / hospedaje',
                'parqueadero' => 'Parqueadero',
            ]],
            'subtipo_funcional' => ['Subtipo funcional', 'Selecciona subtipo', 50, 'Afina la clasificación interna del activo.', [
                'lote_urbano' => 'Lote urbano', 'lote_rural' => 'Lote rural',
                'lote_comercial' => 'Lote comercial', 'lote_industrial' => 'Lote industrial',
                'lote_institucional' => 'Lote institucional',
            ]],
            'finalidad' => ['Finalidad', 'Selecciona finalidad', 40, 'Indica para qué será usado el informe.', [
                'judicial' => 'Judicial', 'extrajudicial' => 'Extrajudicial', 'interna' => 'Interna',
                'patrimonial' => 'Patrimonial', 'negociacion' => 'Negociación',
                'decision_junta' => 'Decisión de junta', 'garantia' => 'Garantía', 'remate' => 'Remate',
            ]],
            'base_valor' => ['Base/tipo de valor', 'Selecciona base de valor', 40, 'Fija el criterio de medición.', [
                'mercado' => 'Valor de mercado', 'razonable' => 'Valor razonable',
                'depreciable' => 'Valor depreciable', 'renta' => 'Valor de renta',
                'catastral' => 'Valor catastral', 'residual' => 'Valor residual',
            ]],
            'aplica_niif' => ['¿Aplica NIIF?', 'Selecciona si aplica NIIF', 10, 'Conecta el encargo con medición contable.', [
                'no' => 'No', 'si' => 'Sí',
            ]],
            'regimen_ph' => ['Régimen de propiedad horizontal (PH)', 'Selecciona régimen PH', 20, 'Define si aplica Ley 675 y área privada.', [
                'no_aplica' => 'No aplica', 'si' => 'Sí', 'no' => 'No',
            ]],
            'estructura_metodo' => ['Estructura del método', 'Selecciona estructura', 40, 'Ordena la unidad que se valorará.', [
                'area_privada' => 'Solo área privada', 'lote_construccion' => 'Lote + construcción',
                'solo_terreno' => 'Solo terreno', 'por_definir' => 'Por definir',
            ]],
        ];
    }

    public static function fieldKeys(): array
    {
        return array_merge(array_keys(self::textFields()), array_keys(self::selectFields()));
    }

    public static function defaults(): array
    {
        return array_fill_keys(self::fieldKeys(), '');
    }

    public static function allowedValues(string $field): array
    {
        return array_keys(self::selectFields()[$field][4] ?? []);
    }

    public static function notes(): array
    {
        return [
            'tipo' => [
                'comercial' => self::note('Avalúo orientado al precio probable de mercado.', 'Compraventa, patrimonio, soporte judicial o garantía.', 'Base principal: valor de mercado; soporte NTS/IGAC.'),
                'posesion' => self::note('Valora una tenencia material sin dominio pleno consolidado.', 'Procesos de pertenencia, ocupaciones o controversias.', 'Exige advertir riesgo jurídico y no concluir como dominio.'),
                'mejoras' => self::note('Valora construcciones, adecuaciones o inversiones sobre un predio.', 'Cuando la mejora es separable del derecho sobre el suelo.', 'Debe separar terreno, construcción y soporte jurídico.'),
                'remate' => self::note('Soporta una actuación de venta forzada o subasta.', 'Procesos ejecutivos o liquidaciones con finalidad judicial.', 'Debe reforzar trazabilidad, fecha y supuestos.'),
                'negociacion' => self::note('Sirve como insumo para pactar precio o canon.', 'Acuerdos privados, ofertas o renegociaciones.', 'No equivale por sí solo a una orden judicial o contable.'),
                'conciliacion' => self::note('Apoya una solución acordada entre partes.', 'Audiencias, arreglos directos o controversias patrimoniales.', 'Debe explicar alcance y límites del acuerdo.'),
                'zona_franca' => self::note('Avalúa bienes bajo régimen o entorno especial.', 'Inmuebles o activos ubicados en zona franca.', 'Requiere revisar norma especial, uso y restricciones.'),
                'catastral' => self::note('Se relaciona con información o finalidad catastral.', 'Referencias administrativas y consistencia predial.', 'No debe confundirse con valor comercial ordinario.'),
                'seguro' => self::note('Busca base para cobertura, reposición o indemnización.', 'Pólizas, siniestros o administración de riesgo.', 'Normalmente exige costo de reposición y exclusiones claras.'),
                'hipotecario' => self::note('Soporta garantía crediticia sobre el bien o derecho.', 'Crédito, garantía real o análisis de riesgo.', 'Debe diferenciar valor comercial, realizable y restricciones.'),
                'interno' => self::note('Ordena análisis interno sin destinatario externo principal.', 'Control patrimonial, decisiones internas o escenarios.', 'Debe marcarse como uso interno y no informe final.'),
            ],
            'tipo_derecho' => [
                'dominio_pleno' => self::note('Derecho completo de propiedad sobre el bien.', 'Cuando el certificado y títulos respaldan propiedad plena.', 'Base civil e inmobiliaria; verificar gravámenes y limitaciones.'),
                'nuda_propiedad' => self::note('Propiedad separada del goce o usufructo.', 'Cuando el uso económico pertenece temporalmente a otro.', 'Se valora el derecho limitado, no el inmueble completo.'),
                'usufructo' => self::note('Derecho de uso y disfrute sobre bien ajeno.', 'Cuando existe usufructuario vigente.', 'Separar duración, rentas, cargas y extinción del derecho.'),
                'posesion' => self::note('Tenencia material con ánimo de señor y dueño.', 'Cuando no hay dominio pleno inscrito o hay controversia.', 'Requiere advertir riesgo, realizabilidad y prueba disponible.'),
                'mejoras' => self::note('Derecho económico sobre obras o inversiones.', 'Cuando la construcción pertenece a quien no domina el suelo.', 'Valorar solo lo reconocible y soportado.'),
                'derecho_fiduciario' => self::note('Derecho derivado de una fiducia o patrimonio autónomo.', 'Proyectos, encargos fiduciarios o derechos económicos.', 'Leer contrato fiduciario antes de concluir valor.'),
                'arrendatario' => self::note('Posición económica de quien ocupa por contrato.', 'Arrendamientos, derechos de uso o ventajas contractuales.', 'Conecta con NIIF 16 si el encargo es contable.'),
                'otro' => self::note('Derecho no encajado en categorías base.', 'Casos especiales o figuras contractuales particulares.', 'Debe documentarse con fuente jurídica específica.'),
            ],
            'finalidad' => [
                'judicial' => self::note('Dictamen con finalidad probatoria.', 'Juzgados, procesos ejecutivos, pertenencia o controversias.', 'Prima trazabilidad, replicabilidad y soporte documental.'),
                'extrajudicial' => self::note('Informe para uso fuera de proceso judicial.', 'Negociaciones, consultas o acuerdos privados.', 'Dejar claro destinatario y uso permitido.'),
                'interna' => self::note('Análisis para gestión de la entidad solicitante.', 'Control, planeación, auditoría interna o inventario.', 'No se presenta como dictamen para terceros.'),
                'patrimonial' => self::note('Soporta lectura del patrimonio o activos propios.', 'Inventarios, sucesiones, sociedades o decisiones familiares.', 'Separar valor de mercado de valores contables.'),
                'negociacion' => self::note('Orienta una decisión de compra, venta o arriendo.', 'Mesas de negociación y análisis de oportunidad.', 'La fecha y condiciones de mercado son críticas.'),
                'decision_junta' => self::note('Insumo para órgano directivo o societario.', 'Junta, comité, asamblea o aprobación interna.', 'Debe ser claro, comparable y trazable.'),
                'garantia' => self::note('Soporta respaldo económico de una obligación.', 'Crédito, cupo, fiducia o garantía real.', 'Revisar exigencias del acreedor y realizabilidad.'),
                'remate' => self::note('Apoya precio base o referencia de subasta.', 'Procesos de ejecución, liquidación o venta forzada.', 'Debe evidenciar restricciones y condición de venta.'),
            ],
            'base_valor' => [
                'mercado' => self::note('Precio probable en mercado abierto entre partes informadas.', 'Compraventa, garantía, conciliación o patrimonio.', 'Respaldo NTS/IGAC e IVS cuando aplique.'),
                'razonable' => self::note('Medición contable basada en participantes de mercado.', 'Estados financieros, auditoría o revelación.', 'Soporte principal NIIF 13.'),
                'depreciable' => self::note('Base para costo, vida útil, deterioro o reposición.', 'Activos construidos, maquinaria o seguros.', 'Conecta con NIC 16, NIC 36 y método de costo.'),
                'renta' => self::note('Valor derivado de ingresos o cánones esperados.', 'Arriendos, inversión y explotación económica.', 'No mezclar rentas del negocio con renta del inmueble.'),
                'catastral' => self::note('Valor usado para fines catastrales o administrativos.', 'Catastro, impuestos o consistencia oficial.', 'No reemplaza automáticamente el valor comercial.'),
                'residual' => self::note('Valor estimado a partir de potencial de desarrollo.', 'Suelo, proyectos, renovación o expansión.', 'Debe soportar usos, costos, tiempos y norma urbana.'),
            ],
            'aplica_niif' => [
                'no' => self::note('El encargo no se formula bajo medición contable.', 'Avalúos comerciales, judiciales o internos no financieros.', 'Usar NTS, marco jurídico e IVS según alcance.'),
                'si' => self::note('El encargo requiere lenguaje y soporte contable NIIF.', 'Estados financieros, auditoría, deterioro o valor razonable.', 'Revisar NIIF 13, NIC 16, NIC 36, NIC 38, NIC 40 o NIIF 16.'),
            ],
            'regimen_ph' => [
                'no_aplica' => self::note('La pregunta no corresponde a la tipología elegida.', 'Bienes no inmobiliarios o activos sin PH.', 'No forzar área privada ni coeficientes.'),
                'si' => self::note('El inmueble está sometido a propiedad horizontal.', 'Apartamentos, locales, oficinas, parqueaderos o unidades PH.', 'Ley 675: diferenciar bien privado, comunes y coeficiente.'),
                'no' => self::note('No existe sometimiento a propiedad horizontal.', 'Casas, lotes o inmuebles independientes.', 'Separar terreno y construcción cuando corresponda.'),
            ],
            'estructura_metodo' => [
                'area_privada' => self::note('La unidad de valoración es el área privada.', 'Unidades sometidas a PH.', 'Evita duplicar bienes comunes no individualizados.'),
                'lote_construccion' => self::note('Se analiza terreno y construcción por separado.', 'Inmuebles no PH con edificación.', 'Útil para costo, depreciación y explicación del valor.'),
                'solo_terreno' => self::note('La unidad de valoración es únicamente el suelo.', 'Lotes, suelo bruto o predios sin construcción relevante.', 'No cargar construcción inexistente o no reconocible.'),
                'por_definir' => self::note('La estructura depende de datos pendientes.', 'Cuando faltan PH, tipología, títulos o inspección.', 'Debe resolverse antes de cerrar el informe.'),
            ],
        ];
    }

    private static function note(string $what, string $when, string $basis): array
    {
        return ['what' => $what, 'when' => $when, 'basis' => $basis];
    }
}
