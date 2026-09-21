<?php
declare(strict_types=1);
namespace App\Services;

use App\Support\AppraisalPhCatalog;

final class AppraisalPhReportBuilder
{
    public function build(array $core, array $technical, array $common, array $documents,
        array $risks, array $photos, string $typology, string $sourceSummary, array $findings): array
    {
        $label = AppraisalPhCatalog::typologies()[$typology] ?? 'propiedad horizontal';
        $name = $this->cleanName($core['ph_name'] ?? '') ?: 'la copropiedad analizada';
        $assets = $this->assets($core);
        $use = $this->dominantUse($technical, $typology);
        [$advantages, $support] = $this->advantages($common, $typology);
        $level = $this->dotationLevel($technical);
        $missing = $this->missingIdentity($core);
        $config = $this->configuration($technical);
        $rules = $this->rules($technical, $core);
        $admin = $this->administration($core, $technical);
        $limits = 'La lectura proviene del reglamento y soportes cargados; debe cruzarse con visita, fotografías, '
            . 'certificado de tradición, paz y salvo y certificación vigente de administración; no reemplaza estudio de títulos.';
        $trace = $this->traceSummary($technical, $limits);

        $tab = [
            'resumen_base_ph' => "Lectura comparativa: {$name} se analiza como {$label}. La dotación común se clasifica como {$level}. "
                . "Para el inmueble, la copropiedad aporta {$advantages}; esto favorece operación, seguridad, acceso de usuarios y percepción comercial frente a inmuebles aislados o PH menos dotadas.",
            'resumen_trazabilidad_ph' => $trace,
            'resumen_identificacion_ph' => "Identificación: el soporte reconoce {$name}. {$assets}"
                . ($missing ? " Falta confirmar {$missing} para amarrar plenamente la unidad al bien sujeto." : ' La identificación básica queda trazable.'),
            'resumen_tipologia_ph' => "Tipología y régimen: {$name} corresponde preliminarmente a {$label}. {$use} "
                . 'La tipología orienta la comparación: oficinas contra PH corporativas, no contra residencial, logística o comercio de otra escala.',
            'resumen_configuracion_ph' => "Configuración predial: {$config} Esta información ayuda a entender escala, organización interna y soporte de funcionamiento del edificio.",
            'resumen_comunes_ph' => "Bienes comunes y soporte: {$support} En términos valuatorios, estos elementos aportan funcionalidad, control, comodidad para usuarios y respaldo operativo.",
            'resumen_reglas_ph' => "Reglas de uso y operación: {$rules} Estas reglas inciden en imagen, convivencia, uso permitido, adecuaciones y comercialización de la unidad.",
            'resumen_administracion_ph' => "Administración y cargas: {$admin} Para valor, liquidez y negociación se requiere confirmar expensas, paz y salvo, pólizas y estado administrativo actual.",
            'resumen_incidencia_ph' => "Incidencia valuatoria: la ubicación del bien dentro de {$name} aporta representatividad corporativa, seguridad, soporte común y servicios compartidos. "
                . "Estos atributos pueden mejorar deseabilidad, funcionalidad y comparabilidad frente a unidades en copropiedades con menor dotación, sujeto al estado real observado.",
            'resumen_notas_ph' => 'Notas normativas: Ley 675 soporta la lectura de bienes comunes, coeficientes y expensas; Decreto 1420, Resolución IGAC 941 e IVS orientan suficiencia, trazabilidad y salvedades del informe.',
        ];
        return ['diagnosis_text' => $tab['resumen_incidencia_ph'],
            'report_text' => $this->report($name, $label, $assets, $use, $support, $level, $limits), 'technical' => $tab];
    }

    private function traceSummary(array $technical, string $limits): string
    {
        $legal = $this->cleanName($technical['trazabilidad_juridica_ph'] ?? '');
        if ($legal !== '') return 'Condición especial PH: ' . $legal . ' ' . $limits;
        return 'Trazabilidad documental: para el análisis de propiedad horizontal se tuvo como soporte '
            . $this->source($technical) . '. La revisión permite ubicar referencias al reglamento, antecedentes, reformas o aclaraciones, bienes comunes, reglas de uso y administración. '
            . 'El texto del avalúo debe incorporar únicamente hechos verificados y redactados por el analista. ' . $limits;
    }
    private function report(string $name, string $label, string $assets, string $use,
        string $support, string $level, string $limits): string
    {
        return "El inmueble objeto de medición se localiza en {$name}, copropiedad analizada preliminarmente como {$label}. {$assets} "
            . "{$use} La copropiedad cuenta con una dotación común {$level}; {$support} "
            . 'Desde el punto de vista valuatorio, esta plataforma común aporta seguridad, control de acceso, soporte operativo, '
            . 'representatividad corporativa y mejores condiciones de uso para ocupantes y visitantes, lo que puede favorecer '
            . 'la funcionalidad, la deseabilidad comercial y la comparación con unidades ubicadas en copropiedades de menor dotación. '
            . "{$limits}";
    }

    private function advantages(array $common, string $typology): array
    {
        $sets = [
            'seguridad y control' => ['porteria', 'cctv_control', 'cerramiento', 'equipos_seguridad_vida'],
            'movilidad interna y acceso' => ['ascensores', 'circulaciones_esenciales', 'parqueaderos_visitantes', 'parqueaderos_privados', 'vias_internas'],
            'soporte técnico y continuidad' => ['redes_servicios', 'planta_electrica', 'tanques_bombeo', 'subestacion', 'basuras_residuos'],
            'representatividad y atención' => ['lobby', 'coworking_salas', 'zonas_verdes'],
            'apoyo operativo especializado' => ['patios_maniobra', 'muelles_bahias'],
        ];
        $found = [];
        foreach ($sets as $title => $keys) if ($this->hasAny($common, $keys)) $found[] = $title;
        $support = $this->labels($common);
        $advantage = $found ? implode(', ', $found) : 'soporte común por confirmar';
        return [$advantage, $support ?: 'los bienes comunes específicos deben confirmarse con visita y soportes actuales.'];
    }

    private function source(array $technical): string
    {
        $source = $this->cleanName($technical['fuente_documental'] ?? '');
        return $source !== '' ? 'el documento fuente ' . $source : 'el reglamento o soporte documental cargado';
    }

    private function labels(array $common): string
    {
        $labels = AppraisalPhCatalog::commonAreas();
        $names = [];
        foreach ($common as $key => $row) {
            if (($row['status'] ?? '') !== '') $names[] = mb_strtolower($labels[$key] ?? $key);
            if (count($names) >= 9) break;
        }
        return $names ? 'se identifican menciones de ' . implode(', ', $names) . '.' : '';
    }

    private function dominantUse(array $technical, string $typology): string
    {
        if ($this->hasText($technical['uso_dominante'] ?? '')) {
            return 'El reglamento contiene referencia al uso o destino dominante, que debe resumirse como hecho técnico y verificable.';
        }
        return match ($typology) {
            'oficinas' => 'La lectura debe concentrarse en funcionamiento corporativo, atención de usuarios, parqueo y servicios comunes.',
            'comercio' => 'La lectura debe concentrarse en flujo de público, visibilidad, parqueo, cargue liviano y reglas comerciales.',
            'bodegas' => 'La lectura debe concentrarse en operación logística, circulación pesada, patios, seguridad y soporte técnico.',
            'residencial' => 'La lectura debe concentrarse en habitabilidad, amenidades, seguridad, convivencia y mantenimiento común.',
            default => 'La lectura debe precisar el uso dominante y los usos complementarios antes de cerrar el informe.',
        };
    }

    private function configuration(array $technical): string
    {
        $parts = $this->present(['etapas o sectores' => $technical['etapas_copropiedad'] ?? '',
            'unidades privadas' => $technical['numero_unidades'] ?? '', 'áreas' => $technical['resumen_areas_conjunto'] ?? '',
            'desenglobes o antecedentes prediales' => $technical['desarrollos_relevantes'] ?? '']);
        return $parts ? 'hay soporte para revisar ' . $parts . '.' : 'la escala predial y la organización interna requieren depuración manual.';
    }

    private function rules(array $technical, array $core): string
    {
        $parts = $this->present(['usos permitidos' => $technical['usos_permitidos'] ?? '',
            'restricciones' => $technical['usos_restringidos'] ?? '',
            'reglas constructivas' => $technical['reglas_constructivas'] ?? '',
            'condiciones operativas' => $technical['condiciones_normativas_operativas'] ?? '',
            'texto de restricciones' => $core['restrictions_text'] ?? '']);
        return $parts ? 'se encontraron referencias a ' . $parts . '.' : 'no hay reglas depuradas suficientes; revisar reglamento y visita.';
    }

    private function administration(array $core, array $technical): string
    {
        $parts = $this->present(['coeficientes' => $technical['coeficientes_copropiedad'] ?? '',
            'expensas' => $technical['expensas_cuotas'] ?? '', 'fondo/imprevistos' => $core['reserve_fund'] ?? '',
            'administración vigente' => $core['administration_name'] ?? '']);
        return $parts ? 'el reglamento aporta referencias a ' . $parts . '.' : 'no hay soporte vigente suficiente de administración, cuota o estado de expensas.';
    }

    private function assets(array $core): string
    {
        $unit = $this->cleanName($core['private_unit'] ?? '');
        $coef = $this->cleanName($core['coefficient'] ?? '');
        if ($unit !== '' && $coef !== '') return "La unidad analizada corresponde a {$unit}, con coeficiente {$coef}.";
        if ($unit !== '') return "La unidad analizada corresponde a {$unit}; el coeficiente debe confirmarse.";
        return 'La unidad específica, parqueadero, depósito y coeficiente deben confirmarse contra certificado y reglamento.';
    }

    private function missingIdentity(array $core): string
    {
        return $this->presentMissing(['matrícula matriz' => $core['matrix_registration'] ?? '',
            'unidad privada' => $core['private_unit'] ?? '', 'coeficiente' => $core['coefficient'] ?? '']);
    }

    private function dotationLevel(array $technical): string
    {
        $text = $this->cleanName($technical['nivel_dotacion_comparativa'] ?? '');
        return $text !== '' ? mb_strtolower(strtok($text, '.') ?: $text) : 'por confirmar';
    }

    private function present(array $values): string
    {
        $names = [];
        foreach ($values as $label => $value) if ($this->hasText($value)) $names[] = $label;
        return implode(', ', $names);
    }

    private function presentMissing(array $values): string
    {
        $names = [];
        foreach ($values as $label => $value) if (!$this->hasText($value)) $names[] = $label;
        return implode(', ', $names);
    }

    private function hasAny(array $rows, array $keys): bool
    {
        foreach ($keys as $key) if (($rows[$key]['status'] ?? '') !== '') return true;
        return false;
    }

    private function hasText(mixed $value): bool
    {
        return trim((string) $value) !== '';
    }

    private function cleanName(mixed $value): string
    {
        return trim(preg_replace('/\s+/u', ' ', (string) $value) ?? '');
    }
}
