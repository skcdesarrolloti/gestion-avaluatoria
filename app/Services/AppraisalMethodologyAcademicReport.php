<?php
declare(strict_types=1);
namespace App\Services;

final class AppraisalMethodologyAcademicReport
{
    public function sections(): array
    {
        return [
            ['8.1 Marco metodológico y normativo de la valuación', $this->introductoryText()],
            ['Referencias que orientan la selección metodológica', $this->referenceText()],
        ];
    }

    public function references(): array
    {
        return [
            ['Norma vigente', 'Resolución IGAC 941 de 2026', 'Métodos y condiciones de elaboración y presentación de avalúos.'],
            ['Antecedente', 'Resolución IGAC 620 de 2008', 'Referencia histórica reemplazada por la Resolución 941.'],
            ['Estándar internacional', 'IVS', 'Alcance, bases de valor, enfoques, datos, modelos, reporte y juicio profesional.'],
            ['Finalidad financiera', 'NIIF', 'Aplica si el encargo exige valor razonable, deterioro, PPE, inversión inmobiliaria o revelaciones contables.'],
            ['Soporte local', 'NTS', 'Estructura del informe, suficiencia documental, salvedades, información examinada y trazabilidad.'],
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
            . 'Para los avalúos comprendidos en su ámbito de aplicación, la Resolución IGAC 941 de 2026 constituye '
            . 'el marco vigente que fija los métodos y las condiciones de elaboración y presentación '
            . 'de avalúos conforme al Decreto 1170 de 2015. Esta resolución actualiza el marco que '
            . 'venía de la Resolución 620 de 2008; el artículo 59 conserva el régimen anterior para los trámites iniciados antes de su vigencia. '
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
}
