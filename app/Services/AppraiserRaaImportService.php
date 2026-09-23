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
        try {
            if ($existing) $this->appraisers->updateRaa((string) $existing['id'], $data);
            else $this->appraisers->create($data);
        } catch (\Throwable $error) {
            if (!$existing) @unlink(AppraiserRaaStorage::path($data['raa_storage_filename']));
            throw $error;
        }
        return ['updated' => (bool) $existing, 'data' => $data];
    }

    private function data(array $post, array $file, array $parsed, ?array $existing): array
    {
        $id = $existing ? (string) $existing['id'] : bin2hex(random_bytes(16));
        $categories = $this->categories($parsed, $post, $existing);
        $postEmail = trim((string) ($post['email'] ?? ''));
        $postPhone = trim((string) ($post['phone'] ?? ''));
        return [
            'id' => $id,
            'code' => $existing ? (string) $existing['code'] : $this->code((string) ($post['code'] ?? '')),
            'full_name' => $existing ? (string) $existing['full_name'] : $parsed['full_name'],
            'identification_number' => $existing && (string) ($existing['identification_number'] ?? '') !== ''
                ? (string) $existing['identification_number'] : $parsed['identification_number'],
            'email' => $this->email($this->keep($parsed['email'] ?: $postEmail, $existing, 'email')),
            'phone' => $this->keep($parsed['phone'] ?: $postPhone, $existing, 'phone'),
            'raa_number' => $parsed['raa_number'],
            'raa_categories' => json_encode($categories, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
            'active' => 'Si', 'notes' => $this->keep(trim((string) ($post['notes'] ?? '')), $existing, 'notes'),
            'raa_issued_at' => $parsed['raa_issued_at'] ?: ($existing['raa_issued_at'] ?? null),
            'raa_expires_at' => $parsed['raa_expires_at'],
            'raa_pin' => $this->keep($parsed['raa_pin'], $existing, 'raa_pin'),
            'raa_contact_city' => $this->keep($parsed['raa_contact_city'] ?? '', $existing, 'raa_contact_city'),
            'raa_contact_department' => $this->keep($parsed['raa_contact_department'] ?? '', $existing, 'raa_contact_department'),
            'raa_contact_address' => $this->keep($parsed['raa_contact_address'] ?? '', $existing, 'raa_contact_address'),
            'raa_source_filename' => basename(str_replace('\\', '/', (string) $file['name'])),
            'raa_storage_filename' => 'raa-' . $id . '.pdf',
            'raa_file_size_bytes' => 0,
        ];
    }


    private function categories(array $parsed, array $post, ?array $existing): array
    {
        $categories = $parsed['raa_categories'] ?: $this->manualCategories($post);
        if ($categories === [] && $existing && (string) ($existing['raa_categories'] ?? '') !== '') {
            $stored = json_decode((string) $existing['raa_categories'], true);
            $categories = is_array($stored) ? array_map('strval', $stored) : [];
        }
        if ($categories === []) throw new \InvalidArgumentException('El certificado RAA no reporta categorías autorizadas legibles. Marca las categorías manuales como respaldo.');
        return array_values(array_unique(array_map('strval', $categories)));
    }

    private function keep(string $value, ?array $existing, string $field): string
    {
        $value = trim($value);
        if ($value !== '') return $value;
        return $existing ? (string) ($existing[$field] ?? '') : '';
    }

    private function validateUpload(array $file): void
    {
        $code = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);
        if ($code !== UPLOAD_ERR_OK) throw new \InvalidArgumentException('Soporte RAA: ' . AppraiserRaaStorage::uploadErrorMessage($code));
        $source = basename(str_replace('\\', '/', (string) ($file['name'] ?? '')));
        if (!AppraiserRaaStorage::isPdf((string) ($file['tmp_name'] ?? ''), $source)) {
            throw new \InvalidArgumentException('El soporte RAA debe ser un PDF válido.');
        }
    }

    private function validateCertificate(array $parsed): void
    {
        if (($parsed['raa_status'] ?? '') !== 'Activo') throw new \InvalidArgumentException('El certificado RAA no reporta estado Activo.');
        if ((string) ($parsed['raa_expires_at'] ?? '') === '') throw new \InvalidArgumentException('El certificado RAA no permitió determinar la fecha de vencimiento.');
        $expiry = new \DateTimeImmutable((string) $parsed['raa_expires_at'], new \DateTimeZone('America/Bogota'));
        $today = new \DateTimeImmutable('today', new \DateTimeZone('America/Bogota'));
        if ($expiry < $today) {
            throw new \InvalidArgumentException('El RAA está vencido desde ' . $expiry->format('Y-m-d') . '. Carga el certificado vigente antes de continuar.');
        }
    }

    private function manualCategories(array $post): array
    {
        $allowed = array_map('strval', array_keys(RaaCategoryCatalog::all()));
        return array_values(array_intersect($allowed, array_map('strval', (array) ($post['raa_categories'] ?? []))));
    }

    private function email(string $value): string
    {
        $value = trim($value);
        if ($value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) throw new \InvalidArgumentException('El correo del perito no tiene un formato válido.');
        return $value;
    }

    private function code(string $posted): string
    {
        $code = mb_strtoupper(trim($posted));
        if ($code === '') return $this->appraisers->nextCode();
        if (mb_strlen($code) > 10) throw new \InvalidArgumentException('El código del perito permite máximo 10 caracteres.');
        return $code;
    }

    private function storeFile(array &$data, array $file): void
    {
        AppraiserRaaStorage::ensure();
        $data['raa_file_size_bytes'] = AppraiserRaaStorage::storeUploaded((string) $file['tmp_name'],
            AppraiserRaaStorage::path($data['raa_storage_filename']));
    }
}
