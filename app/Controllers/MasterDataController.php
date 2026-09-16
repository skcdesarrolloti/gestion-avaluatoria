<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Http;
use App\Core\HttpException;
use App\Core\Session;
use App\Models\AppraiserRepository;
use App\Models\GeoMasterRepository;
use App\Services\AppraiserRaaStorage;
use App\Support\RaaCategoryCatalog;
use PDOException;

final class MasterDataController
{
    public function __construct(private AppraiserRepository $appraisers, private GeoMasterRepository $geo) {}

    public function index(): void
    {
        view('masters/index', [
            'title' => 'Creación de Maestros',
            'appraisers' => $this->appraisers->all(),
            'departments' => $this->geo->departments(),
            'cities' => $this->geo->cities(),
            'neighborhoods' => $this->geo->neighborhoods(),
            'categories' => RaaCategoryCatalog::all(),
            'message' => Session::pullFlash('masters_message'),
            'error' => Session::pullFlash('masters_error'),
        ]);
    }

    public function createAppraiser(): never
    {
        $storedFile = '';
        try {
            $data = $this->validatedAppraiser();
            $storedFile = $data['raa_storage_filename'];
            $this->storeRaaFile($data);
            $this->appraisers->create($data);
            Session::flash('masters_message', 'Perito creado correctamente.');
        } catch (PDOException $error) {
            if ($storedFile !== '') @unlink(AppraiserRaaStorage::path($storedFile));
            $message = str_contains($error->getMessage(), 'uq_valuation_appraisers_code')
                ? 'Ya existe un perito con ese código.'
                : 'No se pudo guardar el perito.';
            Session::flash('masters_error', $message);
        } catch (\InvalidArgumentException $error) {
            if ($storedFile !== '') @unlink(AppraiserRaaStorage::path($storedFile));
            Session::flash('masters_error', $error->getMessage());
        } catch (\RuntimeException $error) {
            if ($storedFile !== '') @unlink(AppraiserRaaStorage::path($storedFile));
            Session::flash('masters_error', $error->getMessage());
        }
        Http::redirect('maestros');
    }

    public function createDepartment(): never
    {
        $this->createGeo('Departamento', fn () => $this->geo->createDepartment($this->geoData(['code', 'name'])));
    }

    public function createCity(): never
    {
        $this->createGeo('Ciudad / municipio', fn () => $this->geo->createCity($this->geoData(['department_id', 'code', 'name'])));
    }

    public function createNeighborhood(): never
    {
        $this->createGeo('Barrio / sector', fn () => $this->geo->createNeighborhood($this->geoData(['city_id', 'name', 'notes'])));
    }

    public function raaFile(string $id): never
    {
        $appraiser = $this->appraisers->find($id);
        $filename = (string) ($appraiser['raa_storage_filename'] ?? '');
        $path = AppraiserRepository::raaPath($filename);
        if ($filename === '' || !is_file($path)) {
            throw new HttpException(404, 'No se encontró el soporte RAA.');
        }
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . basename((string) $appraiser['raa_source_filename']) . '"');
        readfile($path);
        exit;
    }

    private function validatedAppraiser(): array
    {
        $data = [];
        foreach (['code', 'full_name', 'email', 'phone', 'raa_number', 'notes'] as $field) {
            $data[$field] = trim((string) ($_POST[$field] ?? ''));
        }
        $data['id'] = bin2hex(random_bytes(16));
        $data['code'] = mb_strtoupper($data['code']);
        $data['active'] = ($_POST['active'] ?? 'Si') === 'No' ? 'No' : 'Si';
        $selectedCategories = array_values(array_intersect(array_keys(RaaCategoryCatalog::all()),
            array_map('strval', (array) ($_POST['raa_categories'] ?? []))));
        $data['raa_categories'] = json_encode($selectedCategories, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        if ($data['code'] === '' || mb_strlen($data['code']) > 10) {
            throw new \InvalidArgumentException('El código del perito es obligatorio y máximo de 10 caracteres.');
        }
        if ($data['full_name'] === '' || mb_strlen($data['full_name']) > 160) {
            throw new \InvalidArgumentException('El nombre del perito es obligatorio y máximo de 160 caracteres.');
        }
        if ($data['email'] !== '' && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('El correo del perito no tiene un formato válido.');
        }
        if ($data['raa_number'] === '' || mb_strlen($data['raa_number']) > 80) {
            throw new \InvalidArgumentException('El registro RAA es obligatorio y máximo de 80 caracteres.');
        }
        if ($selectedCategories === []) {
            throw new \InvalidArgumentException('Marca al menos una categoría RAA autorizada.');
        }
        $expiresAt = trim((string) ($_POST['raa_expires_at'] ?? ''));
        $expiry = \DateTimeImmutable::createFromFormat('!Y-m-d', $expiresAt);
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $expiresAt) || !$expiry
            || $expiry->format('Y-m-d') !== $expiresAt) {
            throw new \InvalidArgumentException('Indica la fecha de vencimiento del RAA.');
        }
        $data['raa_expires_at'] = $expiresAt;
        $file = $_FILES['raa_file'] ?? null;
        if (!is_array($file) || (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            $code = is_array($file) ? (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE) : UPLOAD_ERR_NO_FILE;
            throw new \InvalidArgumentException('Soporte RAA: ' . AppraiserRaaStorage::uploadErrorMessage($code));
        }
        $source = basename(str_replace('\\', '/', (string) $file['name']));
        if (!AppraiserRaaStorage::isPdf((string) $file['tmp_name'], $source)) {
            throw new \InvalidArgumentException('El soporte RAA debe ser un PDF válido.');
        }
        $data['raa_source_filename'] = $source;
        $data['raa_storage_filename'] = 'raa-' . $data['id'] . '.pdf';
        $data['raa_file_size_bytes'] = 0;
        return $data;
    }

    private function createGeo(string $label, callable $create): never
    {
        try {
            $create();
            Session::flash('masters_message', $label . ' creado correctamente.');
        } catch (PDOException $error) {
            Session::flash('masters_error', str_contains($error->getMessage(), 'Duplicate')
                ? $label . ' ya existe en ese nivel.' : 'No se pudo guardar el maestro geográfico.');
        } catch (\Throwable $error) {
            Session::flash('masters_error', $error->getMessage());
        }
        Http::redirect('maestros#maestros-geograficos');
    }

    private function geoData(array $fields): array
    {
        $data = ['active' => ($_POST['active'] ?? 'Si') === 'No' ? 'No' : 'Si'];
        foreach ($fields as $field) $data[$field] = trim((string) ($_POST[$field] ?? ''));
        foreach (['name' => 160, 'code' => 20, 'notes' => 240] as $field => $limit) {
            if (isset($data[$field])) $data[$field] = mb_substr($data[$field], 0, $limit);
        }
        if (($data['name'] ?? '') === '') throw new \InvalidArgumentException('El nombre es obligatorio.');
        return $data;
    }

    private function storeRaaFile(array &$data): void
    {
        $file = $_FILES['raa_file'];
        AppraiserRaaStorage::ensure();
        $data['raa_file_size_bytes'] = AppraiserRaaStorage::storeUploaded((string) $file['tmp_name'],
            AppraiserRaaStorage::path($data['raa_storage_filename']));
    }
}
