<?php
declare(strict_types=1);
namespace App\Services;

use App\Support\AppraisalCatalog;

final class AppraisalMethodologyChapterReport
{
    public function build(array $record = [], array $subject = []): array
    {
        $decision = $this->decision($record);
        $sections = [
            ['8.1 Marco metodológico y normativo de la valuación', $this->introductoryText()],
            ['Referencias que orientan la selección metodológica', $this->referenceText()],
            ['8.2 Selección y justificación de la metodología aplicada', $this->selectionText($record, $decision)],
        ];

        return [
            'sections' => $sections,
            'text' => implode("\n\n", array_map(
                static fn (array $section): string => $section[0] . "\n" . $section[1],
                $sections
            )),
            'references' => $this->references(),
            'decision' => $decision,
        ];
    }

    private function introductoryText(): string
    {
        return 'La metodología valuatoria se selecciona a partir de la naturaleza del bien, '
            . 'el derecho objeto de valuación, la finalidad del encargo, la base de valor, '
            . 'la información disponible y el comportamiento observable del mercado. El análisis '
            . 'no se limita a aplicar una fórmula: exige identificar el enfoque que mejor representa '
            . 'la forma en que los participantes del mercado formarían precio, con datos verificables, '
            . 'comparables, trazables y suficientes para sustentar el juicio profesional.'
            . "\n\n"
            . 'Para avalúos comerciales en Colombia, la Resolución IGAC 941 de 2026 constituye '
            . 'el marco vigente que fija los métodos y las condiciones de elaboración y presentación '
            . 'de avalúos conforme al Decreto 1170 de 2015. Esta resolución actualiza el marco que '
            . 'venía de la Resolución 620 de 2008, la cual queda como antecedente técnico e histórico. '
            . 'Por tanto, la selección metodológica debe armonizarse con la Resolución 941, el Decreto '
            . '1420 de 1998 cuando resulte aplicable, la Ley 1673 de 2013 y el régimen de autorregulación '
            . 'del avaluador, así como con las Normas Técnicas Sectoriales - NTS que orientan suficiencia, '
            . 'soportes, información examinada, hipótesis, salvedades y presentación del informe.'
            . "\n\n"
            . 'Como referencias técnicas complementarias se consideran las Normas Internacionales de '
            . 'Valuación - IVS, especialmente en alcance del trabajo, bases de valor, enfoques, datos, '
            . 'modelos, documentación y reporte. Si el encargo tiene finalidad contable, financiera o '
            . 'corporativa, las NIIF no sustituyen la valoracion inmobiliaria, pero pueden condicionar '
            . 'la base de medición, las revelaciones y el tratamiento del activo; por ejemplo NIIF 13 '
            . 'para valor razonable, NIC 16 para propiedades, planta y equipo, NIC 40 para propiedades '
            . 'de inversión, NIC 36 para deterioro, NIIF 16 para derechos de uso y arrendamientos, '
            . 'o NIIF 5 cuando exista clasificación como mantenido para la venta.';
    }

    private function referenceText(): string
    {
        return 'Con ese marco, el valuador debe escoger y justificar el enfoque aplicable según '
            . 'la información disponible y la lógica económica del bien: comparación o mercado, '
            . 'renta o capitalización de ingresos, costo, técnica residual u otra técnica admisible '
            . 'cuando el caso lo requiera. La metodología adoptada se desarrolla en los subnumerales '
            . 'siguientes y debe conservar trazabilidad de fuentes, supuestos, limitaciones, datos '
            . 'utilizados, depuración de comparables, memoria de cálculo y conclusión razonada del valor.'
            . "\n\n"
            . 'En consecuencia, este capítulo cumple una función introductoria: ubica al lector en la '
            . 'academia valuatoria aplicable antes de presentar el método seleccionado para el inmueble '
            . 'objeto de estudio. La decisión final no debe depender del nombre del método, sino de la '
            . 'pertinencia de los datos, la consistencia del análisis y la capacidad del informe para '
            . 'explicar por qué el resultado representa razonablemente el valor estimado para la fecha '
            . 'de valoración.';
    }

    private function references(): array
    {
        return [
            ['Norma vigente', 'Resolución IGAC 941 de 2026', 'Métodos y condiciones de elaboración y presentación de avalúos.'],
            ['Antecedente', 'Resolución IGAC 620 de 2008', 'Referencia histórica reemplazada por la Resolución 941.'],
            ['Estándar internacional', 'IVS', 'Alcance, bases de valor, enfoques, datos, modelos, reporte y juicio profesional.'],
            ['Finalidad financiera', 'NIIF', 'Aplica si el encargo exige valor razonable, deterioro, PPE, inversión inmobiliaria o revelaciones contables.'],
            ['Soporte local', 'NTS', 'Estructura del informe, suficiencia documental, salvedades, información examinada y trazabilidad.'],
        ];
    }

    private function decision(array $record): array
    {
        $type = (string) ($record['tipo_inmueble'] ?? '');
        $business = (string) ($record['tipo_negocio'] ?? '');
        $base = (string) ($record['base_valor'] ?? '');
        $ph = (string) ($record['regimen_ph'] ?? '');
        $structure = (string) ($record['estructura_metodo'] ?? '');
        $niif = (string) ($record['aplica_niif'] ?? '');
        $title = mb_strtolower((string) ($record['titulo'] ?? ''));
        $isDeposit = str_contains($title, 'depósito') || str_contains($title, 'deposito')
            || str_contains($title, 'san alejo');
        $method = 'Pendiente de selección';
        $reason = 'Falta completar tipología, negocio, base de valor o estructura del método.';
        if ($business === 'arriendo' || $base === 'renta') {
            $method = 'Renta o capitalización de ingresos';
            $reason = 'La base del encargo es renta o el mercado observable corresponde a cánones.';
        } elseif ($type === 'lote' || $structure === 'solo_terreno' || $base === 'residual') {
            $method = 'Técnica residual, con contraste de mercado cuando existan datos';
            $reason = 'El activo principal es suelo y su valor depende del aprovechamiento normativo y económico permitido.';
        } elseif ($type === 'casa') {
            $method = 'Comparación de mercado + costo de reposición depreciado';
            $reason = 'La casa combina mercado de inmuebles similares con lectura independiente de terreno, mejoras y construcción.';
        } elseif ($type === 'apartamento' && $ph === 'si') {
            $method = 'Comparación o mercado';
            $reason = 'El apartamento PH suele tener mercado comparable por unidades privadas semejantes dentro de copropiedades equivalentes.';
        } elseif (in_array($type, ['local', 'oficina', 'consultorio', 'bodega', 'parqueadero', 'edificio', 'hotel'], true)) {
            $method = 'Comparación de mercado, con renta como contraste si el activo produce ingresos';
            $reason = 'La tipología puede contrastarse con mercado; si existe explotación económica, la renta ayuda a validar consistencia.';
        }
        if ($isDeposit && $ph === 'si') {
            $method = 'Homologación por mercado indirecto';
            $reason = 'El depósito o anexo PH no tiene mercado abierto propio y debe homologarse con el bien comparable más cercano y jurídicamente posible.';
        }
        return [
            'recommended_method' => $method,
            'reason' => $reason,
            'rows' => $this->decisionRows($record, $method),
            'niif_note' => $this->niifNote($niif, $base),
            'special_template' => $this->specialTemplate($isDeposit && $ph === 'si'),
        ];
    }

    private function decisionRows(array $record, string $method): array
    {
        return [
            ['Tipo de negocio', $this->label('tipo_negocio', $record['tipo_negocio'] ?? ''), AppraisalCatalog::fieldSupport('tipo_negocio'), 'Renta si es arriendo; mercado si es venta.', $method],
            ['Tipo de inmueble', $this->label('tipo_inmueble', $record['tipo_inmueble'] ?? ''), AppraisalCatalog::fieldSupport('tipo_inmueble'), 'Define si aplica mercado, costo, residual o una combinación.', $method],
            ['Régimen PH', $this->label('regimen_ph', $record['regimen_ph'] ?? ''), AppraisalCatalog::fieldSupport('regimen_ph'), 'En PH se comparan unidades privadas equivalentes y restricciones de copropiedad.', $method],
            ['Estructura del método', $this->label('estructura_metodo', $record['estructura_metodo'] ?? ''), AppraisalCatalog::fieldSupport('estructura_metodo'), 'Evita mezclar suelo, construcción, área privada o anexos sin soporte.', $method],
            ['Base de valor / NIIF', $this->label('base_valor', $record['base_valor'] ?? ''), AppraisalCatalog::fieldSupport('base_valor'), 'La finalidad NIIF condiciona premisa, revelación y fuentes, pero no reemplaza el método valuatorio.', $method],
        ];
    }

    private function selectionText(array $record, array $decision): string
    {
        $type = $this->label('tipo_inmueble', $record['tipo_inmueble'] ?? 'el bien objeto de estudio');
        $business = $this->label('tipo_negocio', $record['tipo_negocio'] ?? 'el mercado analizado');
        $ph = $this->label('regimen_ph', $record['regimen_ph'] ?? 'pendiente');
        $text = 'Teniendo en cuenta la tipología registrada como ' . mb_strtolower($type)
            . ', el tipo de negocio ' . mb_strtolower($business)
            . ' y el régimen de propiedad horizontal ' . mb_strtolower($ph)
            . ', la matriz de decisión metodológica orienta la aplicación de: '
            . $decision['recommended_method'] . '. '
            . $decision['reason'];
        $text .= ' La matriz reutiliza los soportes normativos registrados en el numeral 1.1 y la adopción definitiva debe sustentarse con la calidad de las fuentes, la existencia de datos comparables, la unidad de comparación, las restricciones jurídicas o físicas del activo y la consistencia del resultado frente al mercado.';
        if ((string) ($decision['niif_note'] ?? '') !== '') $text .= "\n\n" . $decision['niif_note'];
        if ((string) ($decision['special_template'] ?? '') !== '') $text .= "\n\n" . $decision['special_template'];
        return $text;
    }

    private function niifNote(string $niif, string $base): string
    {
        if ($niif !== 'si' && !in_array($base, ['razonable', 'depreciable'], true)) return '';
        return 'Cuando el encargo se formula bajo NIIF, la metodología debe distinguir la fuente de información usada: datos observables de mercado, costos verificables, flujos soportados, restricciones del activo y supuestos internos. La referencia NIIF no sustituye el juicio valuatorio; exige explicar la base de medición, la jerarquía o calidad de los datos, las limitaciones y las revelaciones necesarias para que el usuario del informe entienda el alcance del valor estimado.';
    }

    private function specialTemplate(bool $active): string
    {
        if (!$active) return '';
        return 'Caso especial de depósito o anexo PH: si el bien no tiene independencia jurídica, acceso libre a terceros, vida comercial propia o mercado directo verificable, no debe forzarse una comparación con inmuebles autónomos. En ese escenario se justifica una homologación con el bien más cercano a su utilidad real dentro de la copropiedad, por ejemplo celda de parqueo, parqueadero o anexo funcional, siempre que el área, uso, restricciones, destinación de la copropiedad y ausencia de explotación independiente queden expresamente sustentados.';
    }

    private function label(string $field, mixed $value): string
    {
        $value = (string) $value;
        return AppraisalCatalog::selectFields()[$field][4][$value] ?? ($value !== '' ? $value : 'pendiente');
    }
}
