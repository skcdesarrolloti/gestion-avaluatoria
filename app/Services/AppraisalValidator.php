<?php
declare(strict_types=1);
namespace App\Services;
use App\Core\HttpException;

final class AppraisalValidator
{
    public static function validate(array $input): array
    {
        $limits = ['titulo' => 160, 'tipo' => 30, 'direccion' => 220, 'municipio' => 120, 'observaciones' => 4000];
        $errors = [];
        $data = [];
        foreach ($limits as $field => $limit) {
            if (!isset($input[$field]) || !is_string($input[$field]) || !mb_check_encoding($input[$field], 'UTF-8')) {
                $errors[$field] = 'Escribe un texto válido.';
                continue;
            }
            $data[$field] = trim($input[$field]);
            if (mb_strlen($data[$field]) > $limit) {
                $errors[$field] = "Usa un máximo de $limit caracteres.";
            }
        }
        if (isset($data['tipo']) && !in_array($data['tipo'], ['', 'urbano', 'posesion'], true)) {
            $errors['tipo'] = 'Selecciona una opción válida.';
        }
        if (!isset($input['version']) || !is_int($input['version']) || $input['version'] < 1 || $input['version'] >= 4294967295) {
            throw new HttpException(422, 'La versión del borrador no es válida.');
        }
        if ($errors) {
            throw new HttpException(422, 'Revisa los campos señalados.', $errors);
        }
        return $data;
    }
}
