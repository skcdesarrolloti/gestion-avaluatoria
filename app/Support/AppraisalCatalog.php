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
        return AppraisalNotes::all();
    }
}
