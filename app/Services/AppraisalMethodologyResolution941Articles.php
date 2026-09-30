<?php
declare(strict_types=1);
namespace App\Services;

final class AppraisalMethodologyResolution941Articles
{
    public function for(string $method): array
    {
        return match ($method) {
            'renta' => $this->income(),
            'costo' => $this->cost(),
            'residual' => $this->residual(),
            default => $this->market(),
        };
    }

    private function market(): array
    {
        return [
            $this->article('Art. 16', 'Método de comparación o mercado',
                'Define el método desde ofertas o transacciones recientes de inmuebles similares o comparables.',
                ['Mercado comparable', 'ofertas o transacciones recientes', 'similitud verificable']),
            $this->article('Art. 17', 'Datos mínimos y soporte de la muestra',
                'Exige registrar ubicación, valor, áreas, fuente, contacto, fecha y evidencia verificable de cada dato.',
                ['No es homologación', 'soporte de captura', 'fuente verificable']),
            $this->article('Art. 18', 'Valores integrales en NPH',
                'Permite analizar valores unitarios integrales en inmuebles no PH cuando se justifique la relación terreno-construcción.',
                ['NPH', 'valor integral', 'relación terreno-construcción']),
            $this->article('Art. 19', 'Tratamiento PH y NPH',
                'Ordena separar la lectura estadística según régimen jurídico: PH sobre área privada integral y NPH con desagregación cuando aplique.',
                ['PH vs NPH', 'área privada', 'garajes y depósitos']),
            $this->article('Art. 20', 'Herramientas estadísticas y analítica avanzada',
                'Autoriza medidas de tendencia central, dispersión, forma, técnicas robustas y modelos estadísticos como apoyo sustentado.',
                ['mediana y media recortada', 'IQR/MAD', 'modelos auditables']),
            $this->article('Art. 21', 'Adopción del valor y dispersión',
                'Regula el coeficiente de variación y exige justificar la adopción de media, mediana u otro estadístico según la muestra.',
                ['CV urbano 7,50%', 'CV rural 10%', 'justificar el estadístico']),
        ];
    }

    private function income(): array
    {
        return [
            $this->article('Art. 22', 'Método de renta o capitalización',
                'Define el valor desde rentas o beneficios económicos del inmueble o de inmuebles similares.',
                ['renta inmobiliaria', 'beneficio económico', 'soporte de canon']),
            $this->article('Art. 23', 'Capitalización directa',
                'Usa la relación entre renta y tasa de capitalización cuando el ingreso es representativo y estabilizado.',
                ['A = r / i', 'renta estabilizada', 'tasa coherente']),
            $this->article('Art. 24', 'Renta, tasa y deducciones',
                'Exige coherencia entre renta bruta/neta y tasa bruta/neta; excluye rentas de intangibles y verifica topes legales.',
                ['bruta con bruta', 'neta con neta', 'sin intangibles']),
            $this->article('Art. 25', 'Flujo de caja descontado',
                'Permite valorar desde beneficios futuros descontados y valor terminal cuando el ingreso cambia en el tiempo.',
                ['FCD', 'flujos futuros', 'valor presente']),
            $this->article('Art. 26', 'Aplicación del FCD',
                'Ordena estimar ingresos, egresos, periodicidad, tasa de descuento y VPN con soporte técnico.',
                ['vacancia y gastos', 'tasa de descuento', 'VPN']),
        ];
    }

    private function cost(): array
    {
        return [
            $this->article('Art. 27', 'Método del costo',
                'Estima valor desde terreno más construcción o anexos a valor actual, descontando depreciación.',
                ['terreno + construcción', 'anexos constructivos', 'valor actual']),
            $this->article('Art. 28', 'Reposición y reproducción',
                'Distingue reposición con materiales actuales y reproducción para réplicas, con costos directos e indirectos.',
                ['reposición', 'reproducción', 'presupuesto o tipología']),
            $this->article('Art. 29', 'Vida útil y vida útil prolongada',
                'Permite sustentar vida útil remanente cuando la edad supera la referencia y el estado conserva utilidad.',
                ['vida remanente', 'estado de conservación', 'soporte técnico']),
            $this->article('Art. 30', 'Depreciación acumulada',
                'Exige modelos continuos y el sistema Ross-Heideck combinado para construcción y anexos.',
                ['Ross-Heideck', 'modelo continuo', 'sin escalera']),
        ];
    }

    private function residual(): array
    {
        return [
            $this->article('Art. 31', 'Técnica residual',
                'Estima valor a partir del producto inmobiliario final menos costos, cargas, utilidad y riesgos.',
                ['producto final', 'costos y utilidad', 'mayor y mejor uso']),
            $this->article('Art. 32', 'Residual estático y dinámico',
                'Diferencia el modelo estático de corto plazo y el dinámico con ingresos, costos y tiempos de desarrollo.',
                ['estático', 'dinámico', 'tiempos del proyecto']),
            $this->article('Art. 33', 'Aplicación del residual',
                'Exige norma urbana, áreas vendibles, ventas, urbanismo, construcción, indirectos, financiación y utilidad esperada.',
                ['norma urbana', 'áreas vendibles', 'TIR y VPN']),
            $this->article('Art. 34', 'Valor de terreno en bruto',
                'Permite estimar VTB desde valor de terreno urbanizado, área útil, ganancia de urbanizar y costos de urbanismo.',
                ['VTB', 'área útil', 'costos de urbanismo']),
        ];
    }

    private function article(string $number, string $title, string $summary, array $highlights): array
    {
        return compact('number', 'title', 'summary', 'highlights');
    }
}
