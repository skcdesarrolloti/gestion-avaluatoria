<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Core\{Http, Session};
use App\Models\{AppraisalNarrativeChapterRepository, AppraisalRepository};
use App\Services\AppraisalNarrativeChapterInput;
use App\Support\{AppraisalEconomicCatalog, AppraisalRestrictiveConditionsCatalog};

final class AppraisalNarrativeController
{
    public function __construct(private AppraisalRepository $appraisals,
        private AppraisalNarrativeChapterRepository $chapters, private array $user) {}

    public function economicAspect(string $id): void
    {
        $this->show($id, 'appraisals/economic-aspect', [
            'economicSections' => AppraisalEconomicCatalog::sections(),
            'economicProfile' => $this->chapters->profile($id, $this->user['id'], '6', AppraisalEconomicCatalog::defaults()),
            'economicMessage' => Session::pullFlash('economic_message'),
            'economicError' => Session::pullFlash('economic_error'),
        ], 'Aspecto económico');
    }

    public function restrictiveConditions(string $id): void
    {
        $this->show($id, 'appraisals/restrictive-conditions', [
            'restrictiveSections' => AppraisalRestrictiveConditionsCatalog::sections(),
            'restrictiveProfile' => $this->chapters->profile($id, $this->user['id'], '7', AppraisalRestrictiveConditionsCatalog::defaults()),
            'restrictiveMessage' => Session::pullFlash('restrictive_message'),
            'restrictiveError' => Session::pullFlash('restrictive_error'),
        ], 'Condiciones restrictivas');
    }

    public function saveEconomicAspect(string $id): never
    { $this->save($id, '6', AppraisalEconomicCatalog::sections(), 'economic_message', 'economic_error', 'aspecto-economico'); }
    public function autosaveEconomicAspect(string $id): never
    { $this->autosave($id, '6', AppraisalEconomicCatalog::sections()); }
    public function saveRestrictiveConditions(string $id): never
    { $this->save($id, '7', AppraisalRestrictiveConditionsCatalog::sections(), 'restrictive_message', 'restrictive_error', 'condiciones-restrictivas'); }
    public function autosaveRestrictiveConditions(string $id): never
    { $this->autosave($id, '7', AppraisalRestrictiveConditionsCatalog::sections()); }

    private function show(string $id, string $view, array $data, string $title): void
    {
        $record = $this->appraisals->find($id, $this->user['id']);
        view($view, ['title' => $title, 'record' => $record] + $data);
    }

    private function save(string $id, string $chapter, array $sections, string $ok, string $fail, string $route): never
    {
        try {
            $this->persist($id, $chapter, $sections);
            Session::flash($ok, 'Capítulo ' . $chapter . ' guardado correctamente.');
        } catch (\Throwable $error) { Session::flash($fail, $error->getMessage()); }
        Http::redirect('avaluos/' . $id . '/' . $route);
    }

    private function autosave(string $id, string $chapter, array $sections): never
    { $this->persist($id, $chapter, $sections); Http::json(['ok' => true, 'saved_at' => gmdate('Y-m-d\TH:i:s\Z')]); }

    private function persist(string $id, string $chapter, array $sections): void
    {
        $this->appraisals->find($id, $this->user['id']);
        $this->chapters->save($id, $this->user['id'], $chapter, AppraisalNarrativeChapterInput::data($sections, $_POST));
    }
}
