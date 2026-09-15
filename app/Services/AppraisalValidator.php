<?php
declare(strict_types=1);
namespace App\Services;
use App\Core\HttpException;
use App\Support\AppraisalCatalog;

final class AppraisalValidator
{
    public static function validate(array $input): array
    {
        $limits = [];
        foreach (AppraisalCatalog::textFields() as $field => $definition) {
            $limits[$field] = (int) $definition[2];
        }
        foreach (AppraisalCatalog::selectFields() as $field => $definition) {
            $limits[$field] = (int) $definition[2];
        }
        $errors = [];
        $data = [];
        foreach ($limits as $field => $limit) {
            if (!array_key_exists($field, $input)) {
                $data[$field] = '';
                continue;
            }
            if (!is_string($input[$field]) || !mb_check_encoding($input[$field], 'UTF-8')) {
                $errors[$field] = 'Escribe un texto válido.';
                continue;
            }
            $data[$field] = trim($input[$field]);
            if (mb_strlen($data[$field]) > $limit) {
                $errors[$field] = "Usa un máximo de $limit caracteres.";
            }
        }
        foreach (array_keys(AppraisalCatalog::selectFields()) as $field) {
            if (isset($data[$field]) && $data[$field] !== ''
                && !in_array($data[$field], AppraisalCatalog::allowedValues($field), true)) {
                $errors[$field] = 'Selecciona una opción válida.';
            }
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
