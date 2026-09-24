<?php
declare(strict_types=1);
namespace App\Support;

final class AppraisalReportNoteCatalog
{
    public static function sections(string $chapter): array
    {
        return self::all()[$chapter] ?? self::all()['1'];
    }

    public static function withCustom(string $chapter, array $custom): array
    {
        $sections = self::sections($chapter);
        foreach ($custom as $code => $label) {
            if (is_string($code) && is_string($label) && $code !== '' && $label !== '') $sections[$code] = $label;
        }
        uksort($sections, 'strnatcmp');
        return $sections;
    }

    public static function withNoteSections(string $chapter, array $notes): array
    {
        $base = self::sections($chapter); $custom = [];
        foreach ($notes as $note) {
            $code = (string) ($note['section_code'] ?? '');
            $title = trim((string) ($note['title'] ?? ''));
            if ($code !== '' && !isset($base[$code]) && $title !== '') $custom[$code] = $title;
        }
        return self::withCustom($chapter, $custom);
    }

    public static function noteSectionLabels(string $chapter, array $notes): array
    {
        return array_diff_key(self::withNoteSections($chapter, $notes), self::sections($chapter));
    }

    public static function all(): array
    {
        return [
            '1' => [
                '1.1'=>'Solicitud del avalúo','1.2'=>'Razón social e identificación del solicitante','1.3'=>'Encargo valuatorio',
                '1.3.1'=>'Identificación del activo','1.3.2'=>'Derechos objeto de valuación','1.3.3'=>'Uso previsto de la valuación',
                '1.3.4'=>'Base o tipo de valor','1.3.5'=>'Fecha de aplicación del valor','1.3.6'=>'Ámbito del informe',
                '1.3.7'=>'Condiciones restrictivas','1.4'=>'Localización y dirección','1.5'=>'Objeto del avalúo',
                '1.6'=>'Destinatario','1.7'=>'Tipo de avalúo','1.8'=>'Tipo de derecho o activo','1.9'=>'Destinación actual',
                '1.10'=>'Fechas','1.11'=>'Documentos aportados o insumos',
            ],
            '2' => [
                '2.1'=>'Localización','2.2'=>'Delimitación y soporte cartográfico','2.3'=>'Servicios públicos',
                '2.4'=>'Usos predominantes','2.5'=>'Normatividad urbanística','2.6'=>'Vías de acceso',
                '2.6.1'=>'Elementos de las vías','2.6.2'=>'Estado de conservación','2.7'=>'Amoblamiento urbano',
                '2.8'=>'Estratificación socioeconómica','2.9'=>'Legalidad de la urbanización','2.10'=>'Topografía',
                '2.11'=>'Servicio de transporte público','2.11.1'=>'Tipo de transporte público','2.11.2'=>'Cubrimiento',
                '2.11.3'=>'Frecuencia','2.11.4'=>'Calidad del servicio','2.12'=>'Edificaciones importantes','2.13'=>'Tipos de edificación',
            ],
            '3' => [
                '3'=>'Descripción general del activo','3.1'=>'Identificación y características','3.2'=>'Terreno, superficies y linderos',
                '3.3'=>'Construcciones y mejoras','3.3.1'=>'Construcciones y descripción documental',
                '3.3.2'=>'Aspectos generales de la construcción','3.3.3'=>'Materiales y conservación',
                '3.3.4'=>'Áreas construidas','3.4'=>'Diferenciales valuatorios','3.5'=>'Propiedad horizontal',
                '3.6'=>'Obsolescencias','3.7'=>'Registro fotográfico y soportes',
            ],
            '4' => [
                '4'=>'Identificación de las características jurídicas',
                '4.1'=>'Certificado, folio y titularidad',
                '4.2'=>'Identificación registral, catastral y física',
                '4.3'=>'Propiedad horizontal y derechos vinculados',
                '4.4'=>'Tradición, gravámenes, limitaciones y medidas cautelares',
                '4.5'=>'Salvedades y conclusión jurídica',
            ],
        ];
    }
}
