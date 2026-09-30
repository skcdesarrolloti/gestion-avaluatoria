<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Core\{Http, Session};
use App\Models\AppraisalComparableRepository;
use App\Models\AppraisalPhRepository;
use App\Models\AppraisalRepository;
use App\Models\AppraisalSubjectRepository;
use App\Services\AppraisalComparableInput;
use App\Services\AppraisalComparableSearchGuide;
use App\Services\AppraisalMethodologyChapterReport;

final class AppraisalValuationMethodologyController
{
    public function __construct(private AppraisalRepository $appraisals,
        private AppraisalSubjectRepository $subjects, private AppraisalPhRepository $ph,
        private AppraisalComparableRepository $comparables,
        private AppraisalComparableSearchGuide $guide, private array $user) {}

    public function show(string $id): void
    {
        $record = $this->appraisals->find($id, $this->user['id']);
        $subject = $this->subjects->find($id, $this->user['id']);
        $units = $this->appraisals->units($id, $this->user['id']);
        $phProfile = $this->ph->profile($id, $this->user['id']);
        $comparableRows = $this->comparables->forAppraisal($id, $this->user['id']);
        $methodologyChapter = (new AppraisalMethodologyChapterReport())->build($record, $subject, $units);
        view('appraisals/valuation-methodology', ['title' => 'Metodología valuatoria',
            'record' => $record, 'subject' => $subject, 'units' => $units, 'phProfile' => $phProfile,
            'methodologyChapter' => $methodologyChapter, 'comparableRows' => $comparableRows,
            'guide' => $this->guide->build($record, $subject, $units, $phProfile)]);
    }

    public function saveComparables(string $id): never
    {
        try {
            $this->persistComparables($id);
            Session::flash('methodology_message', 'Comparables guardados correctamente.');
        } catch (\Throwable $error) {
            Session::flash('methodology_error', $error->getMessage());
        }
        Http::redirect('avaluos/' . $id . '/metodologia-valuatoria');
    }

    public function autosaveComparables(string $id): never
    {
        $this->persistComparables($id);
        Http::json(['ok' => true, 'saved_at' => gmdate('Y-m-d\TH:i:s\Z')]);
    }

    private function persistComparables(string $id): void
    {
        $this->appraisals->find($id, $this->user['id']);
        $this->comparables->saveAll($id, $this->user['id'], AppraisalComparableInput::rows($_POST));
    }

    public function readComparable(string $id): never
    {
        $this->appraisals->find($id, $this->user['id']);
        (new \App\Services\RateLimiter(BASE_PATH . '/storage/rate-limits'))->consume('comparable-reader:' . $this->user['id'], 60, 900);
        try {
            $url = $_POST['source_url'] ?? '';
            if (!is_string($url)) throw new \InvalidArgumentException('El enlace debe ser texto.');
            $result = (new \App\Services\FincaraizListingReader())->read($url);
            Http::json(['ok' => true] + $result);
        } catch (\InvalidArgumentException $error) {
            Http::json(['ok' => false, 'message' => $error->getMessage()], 422);
        } catch (\RuntimeException $error) {
            Http::json(['ok' => false, 'message' => $error->getMessage()], 502);
        }
    }
}
