<?php
declare(strict_types=1);
namespace App\Services;

/** Organización del estudio; no define reglas ni ejecuta valoraciones. */
final class IncomeStudyNavigation
{
    public static function steps(): array
    {
        return ['concept'=>'1. Inmueble y arriendo','research'=>'2. Investigación de arriendos',
            'evidence'=>'3. Ingresos y gastos','valuation'=>'4. Valoración por los dos métodos',
            'review'=>'5. Revisión y sustentación'];
    }

    public static function initial(array $query,string $stage): string
    {
        $tab=$query['renta_paso'] ?? '';
        if (is_string($tab) && in_array($tab,['concept','search','capture','evidence','valuation','review'],true)) return $tab;
        return match ($stage) {'4'=>'valuation','5'=>'review',default=>'concept'};
    }

    public static function academyStages(string $topic): array
    {
        return match ($topic) {
            'encargo'=>['concept'], 'ocupacion'=>['concept','valuation'],
            'informacion'=>['research','evidence'], default=>['valuation','review'],
        };
    }
}
