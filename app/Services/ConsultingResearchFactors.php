<?php
declare(strict_types=1);
namespace App\Services;
final class ConsultingResearchFactors
{
    public static function all(): array
    {
        return ['access_ramp'=>[
            'label'=>'Rampa de acceso','kind'=>'binary','unit'=>'sí/no',
            'subject'=>'research_access_ramp','sample'=>'research_access_ramp',
            'why'=>'Verificar si una rampa forma parte del recorrido de entrada al consultorio. 0 No; 1 Sí. Desconocido queda pendiente. Su presencia no acredita por sí sola accesibilidad completa ni cumplimiento técnico.',
            'section'=>'attributes','categories'=>"No\nSí",
        ]];
    }
    public static function keys(): array
    {
        return array_merge(OfficeResearchFactors::keys(),['access_ramp']);
    }
    public static function preserve(array $catalog,array $saved): array
    {
        return OfficeResearchFactors::preserve($catalog,$saved);
    }
    public static function group(string $key): string
    {
        return $key==='access_ramp'?'Acceso al consultorio':OfficeResearchFactors::group($key);
    }
}
