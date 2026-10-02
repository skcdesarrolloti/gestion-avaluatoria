<?php
declare(strict_types=1);
namespace App\Services;
use App\Core\{Http, HttpException};
use App\Models\AppraisalRepository;

final class MarketSubjectEvidenceAction
{
    public static function save(AppraisalRepository $repo, int $owner, string $id, string $unitId): never
    {
        $version = filter_var($_POST['version'] ?? null, FILTER_VALIDATE_INT);
        if ($version === false || $version === null) throw new HttpException(422, 'Falta la versión del soporte de Mercado.');
        $next = $repo->saveMarketEvidence($id, $owner, $unitId, $version,
            MarketSubjectEvidence::input($_POST['market_evidence'] ?? null));
        if (str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json')) Http::json([
            'ok' => true, 'version' => $next, 'saved_at' => gmdate('c')]);
        Http::redirect('avaluos/' . $id . '/bien-sujeto?section=tipologias&unit=' . rawurlencode($unitId) . '#ficha-basica');
    }
}
