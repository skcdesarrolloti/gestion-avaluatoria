<?php
declare(strict_types=1);
namespace App\Services;
use App\Core\{Http, HttpException};
use App\Models\AnalystAccountRepository;
final class AnalystAccessPolicy
{
    public static function enforce(AnalystAccountRepository $accounts, array $user, string $path, string $method): void {
        if (empty($user['analyst_id'])) return;
        if (!empty($user['must_change']) && $path !== '/acceso/clave') {
            if (Http::wantsJson()) throw new HttpException(403,'Cambia la contraseña inicial antes de trabajar.');
            Http::redirect('acceso/clave');
        }
        if ($path === '/acceso/clave' || ($method === 'GET' && in_array($path,['/','/valuaciones'],true)) || ($method === 'POST' && $path === '/avaluos')) return;
        if (preg_match('#^/avaluos/([a-f0-9]{32})(?:/|$)#D',$path,$m)) {
            $accounts->requireRecord($m[1],$user);
            // Preparing the file never authorizes declarations or presentation by the responsible expert.
            if (str_contains($path,'/judicial')) throw new HttpException(403,'El anexo judicial y su presentación corresponden al perito responsable.');
            if (isset($_POST['appraiser_id']) && $_POST['appraiser_id'] !== $user['responsible_appraiser_id'])
                throw new HttpException(403,'El perito responsable asignado solo lo puede cambiar el titular.');
            if ($method === 'POST' && preg_match('#/(expediente|capitulo-0)(/autoguardar)?$#',$path)) $_POST['appraiser_id']=$user['responsible_appraiser_id'];
            return;
        }
        if ($method==='GET' && preg_match('#^/(normas-tecnicas-sectoriales|marco-juridico-valuatorio|normas-internacionales-valuacion|normas-niif|normatividad-urbana|igac|glosario-valuatorio)(/|$)#',$path)) return;
        throw new HttpException(403,'Este acceso permite diligenciar tus avalúos asignados. La administración corresponde al titular.');
    }
}
