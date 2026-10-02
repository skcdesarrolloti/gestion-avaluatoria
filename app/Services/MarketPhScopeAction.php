<?php
declare(strict_types=1);
namespace App\Services;
use App\Core\{Http, HttpException};
use App\Models\AppraisalRepository;

final class MarketPhScopeAction
{
    public static function save(AppraisalRepository $repo, int $owner, string $id, string $unitId): never
    {
        $version = filter_var($_POST['version'] ?? null, FILTER_VALIDATE_INT);
        if ($version === false || $version === null) throw new HttpException(422, 'Falta la versión del alcance PH.');
        $record = $repo->find($id, $owner);
        if (($record['regimen_ph'] ?? '') !== 'si') throw new HttpException(422, 'Confirma primero el régimen PH del expediente.');
        $next = $repo->saveMarketEvidence($id, $owner, $unitId, $version, MarketPhScope::input($_POST['ph_scope'] ?? null));
        if (str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json')) Http::json(['ok'=>true, 'version'=>$next, 'saved_at'=>gmdate('c')]);
        Http::redirect('avaluos/' . $id . '/metodologia-valuatoria?stage=2&component=' . rawurlencode($unitId) . '#alcance-ph');
    }
}
