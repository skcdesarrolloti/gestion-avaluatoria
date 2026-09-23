<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AppraiserRepository;

use App\Support\RaaCategoryCatalog;



final class AppraiserRaaImportService

{

    public function __construct(private AppraiserRepository $appraisers) {}



    public function import(array $post, array $file): array

    {

        $this->validateUpload($file);

        $parsed = (new AppraiserRaaCertificateParser())->parsePdf((string) $file['tmp_name'], (string) $file['name']);

        $this->validateCertificate($parsed);

        $existing = $this->appraisers->findByRaaIdentity($parsed['identification_number'], $parsed['raa_number']);

        $data = $this->data($post, $file, $parsed, $existing);

        $this->storeFile($data, $file);

        if ($existing) $this->appraisers->updateRaa((string) $existing['id'], $data);

        else $this->appraisers->create($data);

        return ['updated' => (bool) $existing, 'data' => $data];

    }



    private function data(array $post, array $file, array $parsed, ?array $existing): array

    {

        $id = $existing ? (string) $existing['id'] : bin2hex(random_bytes(16));

        $categories = $parsed['raa_categories'] ?: $this->manualCategories($post);

        return [

            'id' => $id,

            'code' => $existing ? (string) $existing['code'] : $this->code((string) ($post['code'] ?? '')),

            'full_name' => $existing ? (string) $existing['full_name'] : $parsed['full_name'],

            'identification_number' => $existing && (string) ($existing['identification_number'] ?? '') !== ''

                ? (string) $existing['identification_number'] : $parsed['identification_number'],

            'email' => $parsed['email'] ?: trim((string) ($post['email'] ?? '')),

            'phone' => $parsed['phone'] ?: trim((string) ($post['phone'] ?? '')),

            'raa_number' => $parsed['raa_number'],

            'raa_categories' => json_encode($categories, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),

            'active' => 'Si', 'notes' => trim((string) ($post['notes'] ?? '')),

            'raa_issued_at' => $parsed['raa_issued_at'] ?: null,

            'raa_expires_at' => $parsed['raa_expires_at'],

            'raa_pin' => $parsed['raa_pin'],

            'raa_source_filename' => basename(str_replace('\\', '/', (string) $file['name'])),

            'raa_storage_filename' => 'raa-' . $id . '.pdf',

            'raa_file_size_bytes' => 0,

        ];

    }



    private function validateUpload(array $file): void

    {

        $code = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);

        if ($code !== UPLOAD_ERR_OK) throw new \InvalidArgumentException('Soporte RAA: ' . AppraiserRaaStorage::uploadErrorMessage($code));

        $source = basename(str_replace('\\', '/', (string) ($file['name'] ?? '')));

        if (!AppraiserRaaStorage::isPdf((string) ($file['tmp_name'] ?? ''), $source)) {

            throw new \InvalidArgumentException('El soporte RAA debe ser un PDF vÃ¡lido.');

        }

    }



    private function validateCertificate(array $parsed): void

    {

        if (($parsed['raa_status'] ?? '') !== 'Activo') throw new \InvalidArgumentException('El certificado RAA no reporta estado Activo.');

        if ((string) ($parsed['raa_expires_at'] ?? '') === '') {

            throw new \InvalidArgumentException('El certificado RAA no permitió determinar la fecha de vencimiento.');

        }

        $expiry = new \DateTimeImmutable((string) $parsed['raa_expires_at'], new \DateTimeZone('America/Bogota'));

        $today = new \DateTimeImmutable('today', new \DateTimeZone('America/Bogota'));

        if ($expiry < $today) {

            throw new \InvalidArgumentException('El RAA estÃ¡ vencido desde ' . $expiry->format('Y-m-d') . '. Carga el certificado vigente antes de continuar.');

        }

    }



    private function manualCategories(array $post): array

    {

        $allowed = array_map('strval', array_keys(RaaCategoryCatalog::all()));

        return array_values(array_intersect($allowed, array_map('strval', (array) ($post['raa_categories'] ?? []))));

    }



    private function code(string $posted): string

    {

        $code = mb_strtoupper(trim($posted));

        if ($code === '') return $this->appraisers->nextCode();

        if (mb_strlen($code) > 10) throw new \InvalidArgumentException('El cÃ³digo del perito permite mÃ¡ximo 10 caracteres.');

        return $code;

    }



    private function storeFile(array &$data, array $file): void

    {

        AppraiserRaaStorage::ensure();

        $data['raa_file_size_bytes'] = AppraiserRaaStorage::storeUploaded((string) $file['tmp_name'],

            AppraiserRaaStorage::path($data['raa_storage_filename']));

    }

}

