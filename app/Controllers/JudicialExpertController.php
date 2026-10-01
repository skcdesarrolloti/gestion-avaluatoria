<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Core\Http;
use App\Core\HttpException;
use App\Models\AppraisalRepository;
use App\Models\AppraiserRepository;
use App\Models\JudicialExpertRepository;
use App\Services\JudicialExpertInput as Input;
use App\Services\JudicialExpertReport as Report;

final class JudicialExpertController
{
    public function __construct(private JudicialExpertRepository $records, private AppraisalRepository $appraisals,
        private AppraiserRepository $experts, private array $user) {}
    public function profile(string $id): void
    {
        $expert = $this->expert($id); $stored = $this->records->profile($id, $this->user['id']);
        view('judicial/profile', ['title' => 'Perito · antecedentes judiciales', 'expert' => $expert,
            'stored' => $stored, 'formData' => $stored['data'], 'history' => $this->records->history($id, $this->user['id'])]);
    }
    public function saveProfile(string $id): never
    {
        $this->expert($id);
        $version = $this->records->saveProfile($id, $this->user['id'], Input::profile($_POST), $this->version());
        if (Http::wantsJson()) Http::json(['ok' => true, 'version' => $version]);
        Http::redirect('maestros/peritos/' . $id . '/judicial');
    }
    public function show(string $id): void
    {
        [$record, $expert, $stored, $profile] = $this->context($id);
        $data = $stored['data']; $eligible = ($record['finalidad'] ?? '') === 'judicial' && $expert !== null;
        $snapshot = !empty($stored['snapshot']) ? json_decode($stored['snapshot'], true, 32, JSON_THROW_ON_ERROR) : null;
        $sections = $snapshot['sections'] ?? ($expert ? Report::build($expert, $profile['data'], $data,
            $this->records->history($expert['id'], $this->user['id'], $id)) : []);
        view('judicial/dossier', compact('record', 'expert', 'stored', 'profile', 'eligible', 'sections') + ['formData' => $data, 'title' => 'Anexo judicial · CGP']);
    }
    public function save(string $id): never
    {
        [$record, $expert] = $this->context($id); $this->eligible($record, $expert);
        $version = $this->records->saveDossier($id, $this->user['id'], $expert['id'], Input::dossier($_POST), $this->version());
        if (Http::wantsJson()) Http::json(['ok' => true, 'version' => $version]);
        Http::redirect('avaluos/' . $id . '/judicial');
    }
    public function present(string $id): never
    {
        [$record, $expert, $stored, $profile] = $this->context($id); $this->eligible($record, $expert);
        if ((int) $stored['version'] !== $this->version() || (int) $profile['version'] !== $this->version('profile_version'))
            throw new HttpException(409, 'Cambió el borrador o el historial del perito. Actualiza la vista previa antes de registrar.');
        $data = $stored['data']; Report::validatePresentation($expert, $profile['data'], $data);
        $date = Input::text($_POST['presented_on'] ?? ''); Input::date($date);
        $today = (new \DateTimeImmutable('now', new \DateTimeZone('America/Bogota')))->format('Y-m-d');
        if ($date > $today || $date < $data['report_date']) throw new HttpException(422, 'La presentación debe estar entre la fecha del dictamen y hoy.');
        if (($_POST['confirm_presented'] ?? '') !== 'si') throw new HttpException(422, 'Confirma que el dictamen ya fue presentado al juzgado.');
        $sections = Report::build($expert, $profile['data'], $data, $this->records->history($expert['id'], $this->user['id'], $id));
        $this->records->present($id, $this->user['id'], $expert['id'], $this->version(), $date,
            ['case' => $data, 'sections' => $sections, 'expert' => $expert['full_name'], 'profile_version' => (int) $profile['version']]);
        Http::redirect('avaluos/' . $id . '/judicial');
    }
    public function export(string $id): never
    {
        [$record, $expert, $stored, $profile] = $this->context($id); $this->eligible($record, $expert);
        if (($stored['data']['directed'] ?? '') !== 'si') throw new HttpException(422, 'Marca y guarda «Dirigido a un juzgado» antes de exportar.');
        $snapshot = !empty($stored['snapshot']) ? json_decode($stored['snapshot'], true, 32, JSON_THROW_ON_ERROR) : null;
        $sections = $snapshot['sections'] ?? Report::build($expert, $profile['data'], $stored['data'], $this->records->history($expert['id'], $this->user['id'], $id));
        $key = Input::text($_GET['campo'] ?? 'todos', 30);
        if ($key !== 'todos') {
            if (!isset($sections[$key])) throw new HttpException(404, 'Campo no encontrado.');
            $sections = [$key => $sections[$key]];
        }
        header('Content-Type: text/plain; charset=UTF-8'); header('Cache-Control: private, no-store');
        header('Content-Disposition: attachment; filename="anexo-judicial-' . $key . '.txt"');
        echo "\xEF\xBB\xBF" . ($snapshot ? 'Copia del registro de presentación' : 'BORRADOR · revisar y firmar por el perito') . "\n\n";
        echo 'Perito: ' . ($snapshot['expert'] ?? $expert['full_name']) . "\nJuzgado: " . ($stored['data']['court'] ?? '')
            . "\nProceso: " . ($stored['data']['docket'] ?? '') . "\nFecha del dictamen: " . ($stored['data']['report_date'] ?? '') . "\n\n";
        foreach ($sections as $number => [$label, $text]) echo "$number. $label\n$text\n\n";
        echo "Los soportes deben acompañar el dictamen. Esta exportación contiene texto y no adjunta archivos ni constituye radicación judicial.\n";
        exit;
    }
    private function context(string $id): array
    {
        $record = $this->appraisals->find($id, $this->user['id']);
        $expert = empty($record['appraiser_id']) ? null : $this->expert($record['appraiser_id']);
        $stored = $this->records->dossier($id, $this->user['id']);
        if (isset($stored['appraiser_id']) && $stored['appraiser_id'] !== ($expert['id'] ?? null))
            throw new HttpException(409, 'El anexo pertenece a otro perito. Restablece el perito asignado para consultar su registro; no se trasladan declaraciones entre personas.');
        return [$record, $expert, $stored, $expert ? $this->records->profile($expert['id'], $this->user['id']) : ['data' => [], 'version' => 0]];
    }
    private function expert(string $id): array
    { return $this->experts->find($id); }
    private function eligible(array $record, ?array $expert): void
    {
        if (($record['finalidad'] ?? '') !== 'judicial' || !$expert)
            throw new HttpException(422, 'Guarda la finalidad Judicial y asigna el perito responsable en el expediente.');
    }
    private function version(string $key = 'version'): int
    {
        $value = filter_var($_POST[$key] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
        if ($value === false || $value === null) throw new HttpException(422, 'Falta una versión válida. Recarga conservando tus cambios.');
        return $value;
    }
}
