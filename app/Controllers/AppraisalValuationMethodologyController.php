<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Models\AppraisalPhRepository;
use App\Models\AppraisalRepository;
use App\Models\AppraisalSubjectRepository;
use App\Services\AppraisalComparableSearchGuide;

final class AppraisalValuationMethodologyController
{
    public function __construct(private AppraisalRepository $appraisals,
        private AppraisalSubjectRepository $subjects, private AppraisalPhRepository $ph,
        private AppraisalComparableSearchGuide $guide, private array $user) {}

    public function show(string $id): void
    {
        $record = $this->appraisals->find($id, $this->user['id']);
        $subject = $this->subjects->find($id, $this->user['id']);
        $units = $this->appraisals->units($id, $this->user['id']);
        $phProfile = $this->ph->profile($id, $this->user['id']);
        view('appraisals/valuation-methodology', ['title' => 'Metodología valuatoria',
            'record' => $record, 'subject' => $subject, 'units' => $units, 'phProfile' => $phProfile,
            'guide' => $this->guide->build($record, $subject, $units, $phProfile)]);
    }
}
