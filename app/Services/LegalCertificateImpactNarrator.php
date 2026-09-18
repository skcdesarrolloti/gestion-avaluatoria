<?php
declare(strict_types=1);
namespace App\Services;

final class LegalCertificateImpactNarrator
{
    public function narrate(string $text, string $category, string $state): string
    {
        $base = mb_strtolower($text);
        if ($state === 'solucionada') return 'Anotación de cancelación, levantamiento o superación de una afectación previa.';
        if ($category === 'gravamen') return 'Gravamen o garantía real activa: puede limitar financiación, venta o transferencia libre hasta verificar acreedor, saldo, paz y salvo y cancelación registral.';
        if ($category === 'medida_cautelar') return 'Medida cautelar o actuación judicial activa: puede impedir o condicionar actos de disposición, escrituración, hipoteca o venta. Exige verificar juzgado, radicado, vigencia, levantamiento y alcance real sobre el inmueble.';
        if ($category === 'propiedad_horizontal') return 'Régimen de propiedad horizontal: incide en uso, coeficiente, expensas, administración y reglas internas. Debe validarse reglamento, reformas, matrícula matriz, unidad privada y paz y salvo si aplica.';
        if ($category === 'tradicion') return 'Integra la cadena de tradición o el soporte de titularidad; debe contrastarse con titular actual, modo de adquisición, documento soporte y continuidad del tracto registral.';
        if ($category === 'limitacion_dominio') return $this->limitation($base);
        return 'Anotación registral informativa: debe conservarse como antecedente y validarse si afecta uso, titularidad, disponibilidad o valor del inmueble.';
    }

    private function limitation(string $base): string
    {
        if (str_contains($base, 'servidumbre de acueducto') || str_contains($base, 'acueducto activa predio sirviente')) {
            return 'Servidumbre de acueducto activa: el predio actúa como sirviente y soporta una carga real para paso, instalación, mantenimiento o protección de red de acueducto. No impide por sí sola la transferencia, pero limita el uso de la franja afectada y exige validar trazado, área, beneficiario y restricciones constructivas antes de definir aprovechamiento y valor.';
        }
        if (str_contains($base, 'servidumbre')) {
            return 'Servidumbre activa: constituye carga real sobre el predio y puede limitar uso, construcción, cerramiento o aprovechamiento de la zona afectada. Debe validarse trazado, área, beneficiario, obligaciones y efecto en valor.';
        }
        if (str_contains($base, 'usufructo')) {
            return 'Usufructo activo: separa uso y goce de la nuda propiedad; puede limitar entrega material, explotación económica, venta efectiva y garantías hasta definir titular, vigencia y cancelación.';
        }
        if (str_contains($base, 'patrimonio de familia') || str_contains($base, 'vivienda familiar')) {
            return 'Afectación familiar activa: puede restringir enajenación, hipoteca o disposición sin autorizaciones y levantamientos correspondientes. Debe verificarse beneficiarios, consentimiento y trámite de cancelación.';
        }
        return 'Limitación al dominio activa: puede restringir uso, transferencia, financiación o aprovechamiento del inmueble. Debe identificarse alcance, beneficiario, vigencia, área afectada y posibilidad de cancelación o aceptación por las partes.';
    }
}
