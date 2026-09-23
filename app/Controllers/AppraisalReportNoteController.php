<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Core\{Http, Session};
use App\Models\{AppraisalRepository, AppraisalReportNoteRepository};

final class AppraisalReportNoteController
{
    public function __construct(private AppraisalRepository $appraisals, private AppraisalReportNoteRepository $notes, private array $user) {}

    public function save(string $id): never
    {
        $this->appraisals->find($id, $this->user['id']);
        $chapter = $this->chapter((string) ($_POST['chapter_code'] ?? ''));
        $rows = is_array($_POST['report_notes'] ?? null) ? $_POST['report_notes'] : [];
        try {
            $this->notes->saveRows($id, $this->user['id'], $chapter, $rows);
            Session::flash('report_note_message', 'Ampliaciones del entregable guardadas.');
        } catch (\Throwable $error) {
            Session::flash('report_note_error', 'No fue posible guardar la ampliación: ' . $error->getMessage());
        }
        Http::redirect($this->returnTo($id, $chapter));
    }

    private function chapter(string $value): string { return in_array($value, ['1', '2', '3', '4'], true) ? $value : '1'; }
    private function returnTo(string $id, string $chapter): string
    {
        $target = (string) ($_POST['return_to'] ?? '');
        $allowed = ['1' => 'expediente', '2' => 'sector', '3' => 'bien-sujeto', '4' => 'caracteristicas-juridicas'];
        $path = 'avaluos/' . $id . '/' . ($allowed[$chapter] ?? 'entregable');
        return preg_match('#^avaluos/' . preg_quote($id, '#') . '/[a-z0-9-]+(?:\#[a-z0-9_-]+)?$#', $target) ? $target : $path;
    }
}
