<?php
declare(strict_types=1);
namespace App\Services;
use App\Core\HttpException;

final class CostMethodScope
{
    public static function options(): array
    {
        return [
            'objective' => ['label'=>'Objeto del estudio', 'values'=>[
                'construction'=>'Valor de la construcción', 'remaining'=>'Valor del remanente existente',
                'removal'=>'Presupuesto de desmantelamiento / demolición / retiro']],
            'process' => ['label'=>'Costo a nuevo', 'values'=>[
                'replacement'=>'Reposición con materiales y técnicas actuales', 'reproduction'=>'Reproducción de la construcción',
                'not_applicable'=>'No aplica: sólo presupuesto de retiro']],
            'basis' => ['label'=>'Fuente prevista del costo', 'values'=>[
                'apu'=>'Presupuesto y APU', 'igac'=>'Tipología IGAC con costo sustentado',
                'publication'=>'Publicación especializada', 'own_model'=>'Modelo propio sustentado', 'mixed'=>'Fuentes combinadas']],
        ];
    }

    public static function texts(): array
    {
        return [
            'source_notes'=>['Fuente, fecha y localización', 'Identifica edición, región, página y fecha del precio; distingue publicado, estimado y pendiente de verificar.'],
            'direct_scope'=>['Alcance de costos directos', 'Materiales, mano de obra, equipos, transporte y cantidades. Indica partidas ya incluidas en el precio de referencia.'],
            'indirect_scope'=>['Alcance de costos indirectos', 'Estudios, diseños, licencias, administración de obra, interventoría, pólizas u otros rubros pertinentes. Sustenta inclusiones y exclusiones; evita duplicar lo incluido en directos.'],
            'removal_scope'=>['Desmantelamiento, demolición y retiro', 'Distingue pérdida material ya ocurrida de trabajos futuros: cantidades, protecciones, cargue, transporte, disposición y recuperables sustentados. Si no aplica, explica por qué.'],
            'depreciation_notes'=>['Soporte para Ross–Heideck', 'Edad, vida útil, conservación y soporte de la inspección. Identifica vida útil prolongada o excepción patrimonial si corresponde; evita descontar dos veces el mismo daño.'],
            'integration_notes'=>['Integración, terreno y componentes comunes', 'Relaciona el terreno y los anexos. En PH indica derechos y elementos comunes ya contemplados, sin sumarlos nuevamente ni atribuir matrícula independiente.'],
        ];
    }

    public static function input(mixed $post): array
    {
        if (!is_array($post)) throw new HttpException(422, 'Revisa el alcance C2 del costo.');
        $out=[];
        foreach (self::options() as $key=>$option) {
            if (!array_key_exists($key,$post)) continue;
            if (!is_string($post[$key]) || ($post[$key]!=='' && !isset($option['values'][$post[$key]]))) throw new HttpException(422, 'Revisa '.$option['label'].'.');
            $out[$key]=$post[$key];
        }
        foreach (self::texts() as $key=>[$label]) {
            if (!array_key_exists($key,$post)) continue;
            if (!is_string($post[$key]) || mb_strlen($post[$key])>2000) throw new HttpException(422, "$label: máximo 2000 caracteres.");
            $out[$key]=trim($post[$key]);
        }
        return $out;
    }

    public static function validate(array $changes, array $saved): void
    {
        if (isset($changes['cost_scope']) && (($changes['method'] ?? $saved['method'] ?? '')!=='costo'))
            throw new HttpException(422, 'Selecciona Costo antes de guardar su alcance C2.');
    }
}
