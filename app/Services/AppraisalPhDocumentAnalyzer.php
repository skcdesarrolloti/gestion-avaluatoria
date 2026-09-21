<?php
declare(strict_types=1);
namespace App\Services;
use App\Support\AppraisalPhCatalog;

final class AppraisalPhDocumentAnalyzer
{
    public function analyze(string $text, array $fileNames, string $typology, array $context = []): array
    {
        $content = preg_replace('/\[(?:Documento: [^\]]+|Cobertura: [^\]]+|Página \d+)\]|^Páginas con lectura baja[^\n]*/mu', '', $text) ?? '';
        $hasText = trim($content) !== '';
        $evidence = new AppraisalPhEvidence($text);
        $technical = [];
        foreach (AppraisalPhExtractionRules::technical() as $key => $terms) {
            // Financial impact and visit conclusions belong to the analyst.
            if (str_starts_with($key, 'incidencia_') || in_array($key, ['lectura_valuatoria', 'salvedades_visita'])) continue;
            $value = $evidence->excerpts($terms);
            if ($value !== '') $technical[$key] = $value;
        }
        $core = (new AppraisalPhIdentityExtractor())->extract($text, $context);
        if (!empty($core['ph_name'])) $technical['naturaleza_conjunto'] = $core['ph_name'];
        if (isset(AppraisalPhCatalog::typologies()[$typology])) $core['ph_typology'] = $typology;
        foreach ([
            'regulation_document' => ['reglamento de propiedad horizontal', 'constitucion de propiedad horizontal'],
            'reform_documents' => ['reforma del reglamento', 'reforma al reglamento', 'modificacion del reglamento', 'actualizacion'],
            'restrictions_text' => ['prohibiciones', 'prohibido', 'no podran', 'restricciones', 'destinacion'],
        ] as $key => $terms) {
            $value = $evidence->excerpts($terms, 1500);
            if ($value !== '') $core[$key] = $value;
        }
        $technical['fuente_documental'] = implode(', ', $fileNames);
        $reserve = $evidence->excerpts(['fondo de imprevistos', 'fondo de reserva'], 120);
        if ($reserve !== '') $core['reserve_fund'] = $reserve;
        $technical['ciudad_municipio'] = $evidence->excerpts(['ubicado en', 'situado en', 'localizado en', 'municipio de']);
        $common = $this->map($evidence, AppraisalPhCatalog::commonAreas(), [
            'porteria'=>['porteria','vigilancia'], 'lobby'=>['lobby','recepcion','vestibulo'],
            'ascensores'=>['ascensor','ascensores'], 'circulaciones'=>['escaleras','circulaciones'],
            'parqueaderos_visitantes'=>['visitantes'], 'zonas_verdes'=>['zonas verdes','jardines'],
            'salon_social'=>['salon social'], 'piscina'=>['piscina'], 'gimnasio'=>['gimnasio'],
            'juegos'=>['juegos','recreacion'], 'vias_internas'=>['vias internas'],
            'planta_electrica'=>['planta electrica'], 'tanques'=>['tanques','bombeo'],
            'red_incendio'=>['contra incendio','hidrantes'], 'basuras'=>['basuras','residuos'],
            'cerramiento'=>['cerramiento','perimetral'],
        ]);
        $documents = $this->map($evidence, AppraisalPhCatalog::documents(), [
            'reglamento'=>['reglamento de propiedad horizontal'], 'reformas'=>['reforma','actualizacion'],
            'paz_salvo'=>['paz y salvo'], 'recibo_administracion'=>['recibo de administracion'],
            'certificado_administracion'=>['representacion legal'], 'presupuesto'=>['presupuesto'],
            'estados_financieros'=>['estados financieros'], 'actas'=>['acta','asamblea'],
            'manual_convivencia'=>['manual de convivencia'], 'polizas'=>['poliza','seguros'],
            'planos_coeficientes'=>['planos','coeficientes'], 'mantenimiento'=>['mantenimiento'],
        ]);
        $risks = $this->map($evidence, AppraisalPhCatalog::risks(), [
            'mora_expensas'=>['mora','expensas pendientes'], 'cuotas_extraordinarias'=>['extraordinarias'],
            'cartera_copropiedad'=>['cartera'], 'conflictos'=>['conflictos','litigios'],
            'deterioro_comunes'=>['deterioro'], 'mantenimiento_diferido'=>['mantenimiento diferido'],
            'restricciones_uso'=>['prohibido','prohibiciones','usos restringidos'],
            'seguridad'=>['seguridad','vigilancia'], 'seguros'=>['seguro','poliza'],
            'afectaciones_fisicas'=>['afectaciones','danos estructurales'],
        ]);
        $photos = [];
        foreach (AppraisalPhCatalog::photos() as $key => $label) {
            $photos[$key] = ['status'=>'', 'notes'=>'Pendiente de evidencia fotográfica y visita del bien sujeto.'];
        }
        $notes = 'Extractos documentales por confirmar; una mención no acredita existencia actual, cumplimiento ni riesgo materializado.';
        $core['diagnosis_text'] = 'Lectura documental de ' . ($core['ph_name'] ?? 'la copropiedad') . '. ' . $notes;
        $core['report_text'] = $core['diagnosis_text'] . ' '
            . ($core['regulation_document'] ?? '')
            . ' El criterio final corresponde al analista. Este apoyo no reemplaza estudio de títulos, certificación de administración ni visita.';
        $technical['salvedades_visita'] = 'Verificar estado, funcionamiento y mantenimiento de zonas comunes mediante visita y fotografías.';
        $technical['observaciones_extraccion'] = $notes;
        $findings = [$notes, 'Unidad, coeficiente y cuota: requieren vinculación expresa al bien sujeto; no se toma el primer dato del reglamento.',
            'Administración, pagos y pólizas vigentes: confirmar con soportes actuales.',
            'Incidencia en valor y diagnóstico final: pendientes del criterio del analista.'];
        preg_match_all('/\[Cobertura: ([^\]]+)\]|Páginas con lectura baja[^\n]*/u', $text, $coverage);
        foreach ($coverage[0] as $line) $findings[] = $line;
        if (!$coverage[0] && preg_grep('/\.pdf$/i', $fileNames)) {
            $findings[] = 'Cobertura de páginas no verificada en esta lectura del servidor. Sube el PDF directamente con el lector del navegador.';
        }
        $summary = $hasText ? count(array_filter($technical)) . ' campos técnicos con apoyo documental. '
            . 'Se recorrió todo el texto recibido; revisa los extractos y las páginas con lectura baja.'
            : 'No se extrajo texto útil. El soporte se conserva; revisa el OCR antes de diligenciar.';
        return ['has_text'=>$hasText, 'core'=>$hasText ? $core : [],
            'linkage'=>empty($core['ph_name']) ? [] : ['coproperty_name'=>$core['ph_name']],
            'technical'=>$hasText ? array_filter($technical) : [], 'common_areas'=>$hasText ? $common : [],
            'documents'=>$hasText ? $documents : [], 'risks'=>$hasText ? $risks : [],
            'photos'=>$hasText ? $photos : [], 'summary'=>$summary, 'findings'=>$findings];
    }

    private function map(AppraisalPhEvidence $evidence, array $catalog, array $rules): array
    {
        $map = [];
        foreach ($catalog as $key => $label) {
            $text = $evidence->excerpts($rules[$key] ?? [], 410);
            $map[$key] = ['status'=>$text !== '' ? 'warn' : '',
                'notes'=>$text !== '' ? 'Mención documental; confirmar situación actual. ' . $text : 'No identificado en el texto leído. Pendiente de soporte.'];
        }
        return $map;
    }
}
