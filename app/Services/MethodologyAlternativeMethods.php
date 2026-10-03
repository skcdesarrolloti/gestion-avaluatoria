<?php
declare(strict_types=1);
namespace App\Services;
use App\Core\HttpException;

/** Independent studies of the same scope: they are contrasted, never added. */
final class MethodologyAlternativeMethods
{
    public static function input(mixed $value): array
    {
        if (!is_array($value) || count($value)>4) throw new HttpException(422, 'Revisa los métodos de contraste.');
        foreach ($value as $method) if (!is_string($method) || !isset(MethodologyWorkflow::METHODS[$method]))
            throw new HttpException(422, 'Método de contraste inválido.');
        return array_values(array_unique($value));
    }

    public static function expand(array $components, array $saved): array
    {
        $result=[];
        foreach ($components as $key=>$component) {
            $result[$key]=$component;
            if (!empty($component['container'])) continue;
            foreach (($saved[$key]['additional_methods'] ?? []) as $method) {
                if (!isset(MethodologyWorkflow::METHODS[$method]) || $method===($saved[$key]['method'] ?? '')) continue;
                $result[$key.':metodo:'.$method]=$component + ['comparison_key'=>$key, 'alternate_method'=>$method];
                $result[$key.':metodo:'.$method]['label']=$component['label'].' · contraste '.$method;
                $result[$key.':metodo:'.$method]['parent_key']=$component['unit']['id'];
            }
        }
        return $result;
    }

    public static function save(array $saved, string $key, array $changes): array
    {
        if (isset($changes['additional_methods'])) {
            $method=$changes['method'] ?? $saved[$key]['method'] ?? '';
            if ($method==='' && $changes['additional_methods']!==[]) throw new HttpException(422, 'Selecciona el método principal antes de añadir contrastes.');
            $changes['additional_methods']=array_values(array_diff($changes['additional_methods'],[$method]));
            foreach ($changes['additional_methods'] as $other) {
                $studyKey=$key.':metodo:'.$other;
                $saved[$studyKey]=array_replace($saved[$studyKey] ?? [], ['method'=>$other]);
            }
        }
        $saved[$key]=array_replace($saved[$key] ?? [],$changes);
        return $saved;
    }
}
