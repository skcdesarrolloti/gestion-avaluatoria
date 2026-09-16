<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Http;
use App\Core\Session;
use App\Models\AppraiserRepository;
use PDOException;

final class MasterDataController
{
    public function __construct(private AppraiserRepository $appraisers) {}

    public function index(): void
    {
        view('masters/index', [
            'title' => 'Creación de Maestros',
            'appraisers' => $this->appraisers->all(),
            'message' => Session::pullFlash('masters_message'),
            'error' => Session::pullFlash('masters_error'),
        ]);
    }

    public function createAppraiser(): never
    {
        try {
            $this->appraisers->create($this->validatedAppraiser());
            Session::flash('masters_message', 'Perito creado correctamente.');
        } catch (PDOException $error) {
            $message = str_contains($error->getMessage(), 'uq_valuation_appraisers_code')
                ? 'Ya existe un perito con ese código.'
                : 'No se pudo guardar el perito.';
            Session::flash('masters_error', $message);
        } catch (\InvalidArgumentException $error) {
            Session::flash('masters_error', $error->getMessage());
        }
        Http::redirect('maestros');
    }

    private function validatedAppraiser(): array
    {
        $data = [];
        foreach (['code', 'full_name', 'email', 'phone', 'raa_number', 'raa_categories', 'notes'] as $field) {
            $data[$field] = trim((string) ($_POST[$field] ?? ''));
        }
        $data['code'] = mb_strtoupper($data['code']);
        $data['active'] = ($_POST['active'] ?? 'Si') === 'No' ? 'No' : 'Si';
        if ($data['code'] === '' || mb_strlen($data['code']) > 10) {
            throw new \InvalidArgumentException('El código del perito es obligatorio y máximo de 10 caracteres.');
        }
        if ($data['full_name'] === '' || mb_strlen($data['full_name']) > 160) {
            throw new \InvalidArgumentException('El nombre del perito es obligatorio y máximo de 160 caracteres.');
        }
        if ($data['email'] !== '' && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('El correo del perito no tiene un formato válido.');
        }
        return $data;
    }
}
