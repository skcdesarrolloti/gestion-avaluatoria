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
        $config = $this->configuration($technical);
        $rules = $this->rules($technical, $core);
        $admin = $this->administration($core, $technical);
        $limits = 'La conclusión corresponde al alcance técnico del avalúo y se complementa con el análisis jurídico registrado en el expediente.';
        $trace = $this->traceSummary($technical, $limits);

        $tab = [
            'resumen_base_ph' => $this->baseSummary($name, $label, $advantages, $level),
            'resumen_trazabilidad_ph' => $trace,
            'resumen_identificacion_ph' => $this->identitySummary($name, $assets),
            'resumen_tipologia_ph' => $this->typologySummary($name, $label, $technical, $typology),
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

    private function identitySummary(string $name, string $assets): string
    {
        $summary = "El inmueble objeto de análisis forma parte de {$name}, copropiedad sometida al régimen de propiedad horizontal.";
        if ($assets !== '') $summary .= ' ' . $assets;
        return $summary . ' Esta identificación permite vincular la unidad con el reglamento, el certificado de tradición, la matrícula inmobiliaria y los coeficientes aplicables dentro del análisis valuatorio.';
    }
    private function baseSummary(string $name, string $label, string $advantages, string $level): string
    {
        $benefits = $advantages === 'soporte común por confirmar'
            ? 'La incidencia específica de sus bienes comunes debe completarse con los elementos confirmados por el analista.'
            : "La copropiedad aporta {$advantages}, elementos que fortalecen el funcionamiento del inmueble, el acceso de usuarios, la operación cotidiana y la percepción de organización y seguridad dentro del edificio.";
        return "El inmueble se integra a {$name}, copropiedad sometida al régimen de propiedad horizontal y clasificada para este avalúo como {$label}. "
            . "La dotación común identificada se clasifica como {$level}. {$benefits}";
    }
    private function typologySummary(string $name, string $label, array $technical, string $typology): string
    {
        $summary = "Tipología y régimen: {$name} se analiza como {$label}. " . $this->useProfile($technical, $typology);
        if ($this->hasText($technical['regimen_especial'] ?? '')) {
            $summary .= ' El reglamento contiene referencias a régimen especial, administración u operación específica, aspecto relevante para segmentar comparables y condiciones de ocupación.';
        }
        if ($this->hasText($technical['usos_complementarios'] ?? '') || $this->hasText($technical['relacion_funcional_usos'] ?? '')) {
            $summary .= ' La presencia de usos complementarios exige valorar la unidad dentro de una copropiedad con interacción funcional entre actividades, usuarios y servicios comunes.';
        }
        return $summary . ' Para efectos valuatorios, la comparación se realiza con copropiedades de igual vocación, escala, localización y nivel de soporte común.';
    }
    private function useProfile(array $technical, string $typology): string
    {
        $text = mb_strtolower($this->cleanName(($technical['uso_dominante'] ?? '') . ' ' . ($technical['naturaleza_conjunto'] ?? '')));
        if ($text !== '') {
            if ($this->containsAny($text, ['oficina', 'consultorio', 'corporativ', 'servicios'])) return 'El uso dominante se orienta a actividades corporativas, profesionales o de servicios, con incidencia positiva en representatividad, atención de usuarios y comparabilidad frente a edificios empresariales.';
            if ($this->containsAny($text, ['local', 'comercio', 'comercial'])) return 'El uso dominante incorpora actividad comercial, flujo de usuarios y reglas de ocupación propias de inmuebles con atención al público.';
            if ($this->containsAny($text, ['bodega', 'logistic', 'industrial', 'zona franca'])) return 'El uso dominante se relaciona con actividad industrial, logística o régimen especial, por lo que cobran relevancia accesos, control, movilidad interna y soporte operativo.';
            if ($this->containsAny($text, ['vivienda', 'residencial', 'habitacional'])) return 'El uso dominante corresponde a vivienda, donde pesan habitabilidad, seguridad, amenidades, convivencia y mantenimiento común.';
        }
        return match ($typology) {
            'oficinas' => 'La vocación corresponde a funcionamiento corporativo y de servicios, con énfasis en imagen, acceso de usuarios, parqueo y administración común.',
            'comercio' => 'La vocación corresponde a actividad comercial, con énfasis en visibilidad, flujo de visitantes, parqueo y reglas de uso.',
            'bodegas' => 'La vocación corresponde a operación logística o industrial, con énfasis en movilidad, patios, seguridad y continuidad operativa.',
            'residencial' => 'La vocación corresponde a uso habitacional, con énfasis en seguridad, amenidades, convivencia y sostenimiento común.',
            default => 'La vocación se determina a partir del reglamento, la visita y los soportes del encargo.',
        };
    }
    private function traceSummary(array $technical, string $limits): string
    {
        $legal = $this->cleanName($technical['trazabilidad_juridica_ph'] ?? '');
        if ($legal !== '') return 'Condición especial PH: ' . $legal . ' ' . $limits;
        return 'Trazabilidad documental: para el análisis de propiedad horizontal se tuvo como soporte '
            . $this->source($technical) . '. La revisión permite ubicar referencias al reglamento, antecedentes, reformas o aclaraciones, bienes comunes, reglas de uso y administración. '
            . 'El texto del avalúo incorpora únicamente hechos verificados y redactados por el analista. ' . $limits;
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
            return 'El reglamento contiene referencia al uso o destino dominante, útil para precisar la vocación funcional de la copropiedad.';
        }
        return match ($typology) {
            'oficinas' => 'La lectura se concentra en funcionamiento corporativo, atención de usuarios, parqueo y servicios comunes.',
            'comercio' => 'La lectura se concentra en flujo de público, visibilidad, parqueo, cargue liviano y reglas comerciales.',
            'bodegas' => 'La lectura se concentra en operación logística, circulación pesada, patios, seguridad y soporte técnico.',
            'residencial' => 'La lectura se concentra en habitabilidad, amenidades, seguridad, convivencia y mantenimiento común.',
            default => 'La lectura aporta elementos para precisar el uso dominante y los usos complementarios.',
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
        $linkage = is_array($core['linkage'] ?? null) ? $core['linkage'] : [];
        $registration = $this->cleanName($linkage['legal_registration'] ?? '');
        $unit = $this->cleanName($core['private_unit'] ?? '');
        $coef = $this->cleanName($core['coefficient'] ?? '');
        $parts = [];
        if ($registration !== '') $parts[] = "se identifica registralmente con matrícula inmobiliaria {$registration}";
        if ($unit !== '' && $coef !== '') $parts[] = "la unidad privada analizada corresponde a {$unit}, con coeficiente de copropiedad {$coef}";
        elseif ($unit !== '') $parts[] = "la unidad privada analizada corresponde a {$unit}";
        elseif ($coef !== '') $parts[] = "se registra coeficiente de copropiedad {$coef}";
        return $parts ? ucfirst(implode('; ', $parts)) . '.' : '';
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

    private function hasAny(array $rows, array $keys): bool
    {
        foreach ($keys as $key) if (($rows[$key]['status'] ?? '') !== '') return true;
        return false;
    }

    private function hasText(mixed $value): bool
    {
        return trim((string) $value) !== '';
    }

    private function containsAny(string $text, array $needles): bool
    {
        foreach ($needles as $needle) if (str_contains($text, $needle)) return true;
        return false;
    }
    private function cleanName(mixed $value): string
    {
        return trim(preg_replace('/\s+/u', ' ', (string) $value) ?? '');
    }
}
