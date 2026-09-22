<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Core\{Http, Session};
use App\Models\{AppraisalObsolescenceRepository, AppraisalRepository};
use App\Services\AppraisalObsolescenceInput;

final class AppraisalObsolescenceController
{
    public function __construct(private AppraisalRepository $appraisals,
        private AppraisalObsolescenceRepository $obsolescence, private array $user) {}
    public function save(string $id): never
    {
        $this->appraisals->find($id, $this->user['id']);
        try {
            $this->obsolescence->save($id, $this->user['id'], AppraisalObsolescenceInput::data($_POST));
            Session::flash('subject_message', 'Obsolescencias guardadas correctamente.');
        } catch (\Throwable $error) { Session::flash('subject_error', $error->getMessage()); }
        Http::redirect('avaluos/' . $id . '/bien-sujeto#obsolescencias');
    }
    public function autosave(string $id): never
    {
        $this->appraisals->find($id, $this->user['id']);
        $this->obsolescence->save($id, $this->user['id'], AppraisalObsolescenceInput::data($_POST));
        Http::json(['ok' => true, 'saved_at' => gmdate('Y-m-d\TH:i:s\Z')]);
    }
}
