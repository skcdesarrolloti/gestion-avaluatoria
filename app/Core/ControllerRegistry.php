<?php
declare(strict_types=1);
namespace App\Core;
use App\Controllers\{AppraisalController, AppraisalLegalController, AppraisalSubjectController, AuthController, DiagnosticController, IgacTypologyController, IfrsStandardController, MidasLibraryController, ValuationGlossaryController, InternationalStandardController, LegalFrameworkController, MaintenanceController, MasterDataController, StandardController, UrbanNormativeLibraryController, ValuationController};
use App\Database\Migrator;
use App\Models\{AppraisalLegalRepository, AppraisalRepository, AppraisalSectorMidasFileRepository, AppraisalSubjectRepository, AppraiserRepository, FuncionarioRepository, GeoMasterRepository, IgacTypologyRepository, IfrsStandardRepository, InternationalStandardRepository, LegalDocumentRepository, MasterDocumentRepository, MidasDocumentRepository, ValuationGlossaryRepository, ValuationStandardRepository};
use App\Services\AuthService;
final class ControllerRegistry
{
    public static function make(string $controller,?\PDO $db,?array $user,AuthService $auth): object
    {
            return match ($controller) {
                'userResearchFactors'=>new \App\Controllers\UserResearchFactorController(new AppraisalRepository($db,$user),new \App\Models\UserResearchFactorRepository($db),$user),
                'analystAccess' => new \App\Controllers\AnalystAccessController(new \App\Models\AnalystAccountRepository($db), new AppraiserRepository($db), new FuncionarioRepository(Database::connection('auth')), $user),
                'auth' => new AuthController($auth),
                'ifrs' => new IfrsStandardController(new IfrsStandardRepository($db)),
                'urbanNorms' => new UrbanNormativeLibraryController(new \App\Models\UrbanNormativeRepository($db)),
                'typologies' => new IgacTypologyController(new IgacTypologyRepository()),
                'midas' => new MidasLibraryController(new MidasDocumentRepository($db), $user),
                'glossary' => new ValuationGlossaryController(new ValuationGlossaryRepository($db), $user),
                'international' => new InternationalStandardController(new InternationalStandardRepository($db)),
                'legal' => new LegalFrameworkController(new LegalDocumentRepository($db)),
                'maintenance' => new MaintenanceController($db, $user),
                'masters' => new MasterDataController(new AppraiserRepository($db), new MasterDocumentRepository($db)),
                'judicial' => new \App\Controllers\JudicialExpertController(new \App\Models\JudicialExpertRepository($db), new AppraisalRepository($db, $user), new AppraiserRepository($db), $user),
                'standards' => new StandardController(new ValuationStandardRepository($db)),
                'reportNotes' => new \App\Controllers\AppraisalReportNoteController(
                    new AppraisalRepository($db, $user), new \App\Models\AppraisalReportNoteRepository($db), $user),
                'sector' => new \App\Controllers\AppraisalSectorController(new AppraisalRepository($db, $user),
                    new \App\Models\AppraisalSectorRepository($db), new \App\Models\AppraisalSectorSectionRepository($db), new AppraisalSubjectRepository($db),
                    new \App\Models\NeighborhoodSectorRepository($db), new \App\Models\SectorBankRepository($db),
                    new GeoMasterRepository($db), new AppraisalSectorMidasFileRepository($db),
                    new \App\Models\AppraisalReportNoteRepository($db), $user),
                'sectorMidas' => new \App\Controllers\AppraisalSectorMidasController(new AppraisalRepository($db, $user),
                    new \App\Models\AppraisalSectorRepository($db), new \App\Models\AppraisalSectorSectionRepository($db),
                    new AppraisalSubjectRepository($db), new \App\Models\SectorBankRepository($db), new AppraisalSectorMidasFileRepository($db), $user),
                'legalCharacteristics' => new AppraisalLegalController(new AppraisalRepository($db, $user),
                    new AppraisalLegalRepository($db), new AppraisalSubjectRepository($db), $user, new \App\Models\AppraisalReportNoteRepository($db)),
                'urbanNormative' => new \App\Controllers\AppraisalUrbanNormController(new AppraisalRepository($db, $user),
                    new \App\Models\AppraisalUrbanNormRepository($db), new \App\Models\UrbanNormativeRepository($db), new AppraisalSubjectRepository($db), $user, new \App\Models\AppraisalReportNoteRepository($db)),
                'comparablePhotos' => new \App\Controllers\ComparablePhotoController(new AppraisalRepository($db, $user), new \App\Models\ComparablePhotoRepository($db), $user),
                'valuationMethodology' => new \App\Controllers\AppraisalValuationMethodologyController(new AppraisalRepository($db, $user), new AppraisalSubjectRepository($db), new \App\Models\AppraisalPhRepository($db), new \App\Models\AppraisalComparableRepository($db), new \App\Services\AppraisalComparableSearchGuide(), $user, new GeoMasterRepository($db), new \App\Models\MethodologyWorkflowRepository($db), new \App\Models\ResearchFactorScaleRepository($db)),
                'narrativeChapters' => new \App\Controllers\AppraisalNarrativeController(new AppraisalRepository($db, $user), new \App\Models\AppraisalNarrativeChapterRepository($db), $user, new MidasDocumentRepository($db)),
                'subject' => new AppraisalSubjectController(new AppraisalRepository($db, $user), $user,
                    new IgacTypologyRepository(), new AppraisalSubjectRepository($db), new GeoMasterRepository($db),
                    new \App\Models\AppraisalPhRepository($db), new \App\Models\AppraisalObsolescenceRepository($db),
                    new \App\Models\AppraisalReportNoteRepository($db), new \App\Models\ResearchFactorScaleRepository($db)),
                'subjectFactors' => new \App\Controllers\SubjectFactorController(new \App\Models\SubjectFactorRepository($db), $user),
                'subjectMidas' => new \App\Controllers\AppraisalSubjectMidasController(new AppraisalRepository($db, $user), new AppraisalSubjectRepository($db), new \App\Models\AppraisalUrbanNormRepository($db), $user),
                'subjectPh' => new \App\Controllers\AppraisalPhController(new AppraisalRepository($db, $user), new \App\Models\AppraisalPhRepository($db), $user),
                'obsolescence' => new \App\Controllers\AppraisalObsolescenceController(new AppraisalRepository($db, $user), new \App\Models\AppraisalObsolescenceRepository($db), $user),
                'valuations' => new ValuationController(),
                default => new AppraisalController(new AppraisalRepository($db, $user), $user, new AppraiserRepository($db), new IgacTypologyRepository(),
                    new \App\Models\AppraisalPhRepository($db), new AppraisalSubjectRepository($db),
                    new \App\Models\AppraisalObsolescenceRepository($db), new \App\Services\AppraisalDossierNumberer($db),
                    new \App\Models\AppraisalSectorRepository($db), new \App\Models\AppraisalSectorSectionRepository($db),
                    new \App\Models\AppraisalReportNoteRepository($db), new AppraisalLegalRepository($db),
                    new \App\Models\AppraisalUrbanNormRepository($db), new MidasDocumentRepository($db),
                    new \App\Models\AppraisalNarrativeChapterRepository($db)),
            };
    }
}
