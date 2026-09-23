<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Http;
use App\Core\HttpException;
use App\Core\Session;
use App\Models\AppraiserRepository;
use App\Services\AppraiserRaaImportService;
use App\Support\RaaCategoryCatalog;
use PDOException;

final class MasterDataController
{
    public function __construct(private AppraiserRepository $appraisers) {}

    public function index(): void
    {
        view('masters/index', [
            'title' => 'Creación de Maestros',
            'appraisers' => $this->appraisers->all(),
            'categories' => RaaCategoryCatalog::all(),
            'message' => Session::pullFlash('masters_message'),
            'error' => Session::pullFlash('masters_error'),
        ]);
    }

    public function createAppraiser(): never
    {
        try {
            $result = (new AppraiserRaaImportService($this->appraisers))->import($_POST, $_FILES['raa_file'] ?? []);
            Session::flash('masters_message', $result['updated']
                ? 'RAA actualizado. Se conservaron código, nombre y cédula del perito.'
                : 'Perito creado desde el certificado RAA.');
        } catch (PDOException $error) {
            $message = str_contains($error->getMessage(), 'uq_valuation_appraisers_code')
                ? 'Ya existe un perito con ese código.' : 'No se pudo guardar el perito.';
            Session::flash('masters_error', $message);
        } catch (\InvalidArgumentException|\RuntimeException $error) {
            Session::flash('masters_error', $error->getMessage());
        }
        Http::redirect('maestros');
    }

    public function raaFile(string $id): never
    {
        $appraiser = $this->appraisers->find($id);
        $filename = (string) ($appraiser['raa_storage_filename'] ?? '');
        $path = AppraiserRepository::raaPath($filename);
        if ($filename === '' || !is_file($path)) throw new HttpException(404, 'No se encontró el soporte RAA.');
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . basename((string) $appraiser['raa_source_filename']) . '"');
        readfile($path);
        exit;
    }
}
