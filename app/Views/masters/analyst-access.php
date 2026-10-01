<?php $accessValues=json_decode(\App\Core\Session::pullFlash('access_values') ?: '{}',true) ?: []; ?>
<div id="acceso-analista" x-show="expertTab === 'access'" x-cloak class="space-y-4">
    <h3 class="text-xl font-semibold">Acceso del analista</h3>
    <p>Crea aquí su acceso a este aplicativo. Él diligencia; tú sigues como perito responsable. Sus nuevos avalúos se guardan en tu espacio y podrás verlos y editarlos desde tu cuenta.</p>
    <form method="post" action="<?= e(url('maestros/accesos')) ?>" class="grid gap-4 md:grid-cols-2" x-data="{ busy:false }" @submit="busy=true">
        <?= csrf_field() ?>
        <label class="label">Nombre del analista<input name="full_name" value="<?= e($accessValues['full_name']??'') ?>" class="input" maxlength="160" placeholder="Nombre completo de quien diligenciará" required></label>
        <label class="label">Usuario del analista<input name="username" value="<?= e($accessValues['username']??'') ?>" class="input" autocomplete="off" minlength="3" maxlength="80" pattern="[a-z][a-z0-9._-]{2,79}" placeholder="Inicial del nombre y apellido" required><span class="text-xs">Minúsculas, sin espacios. No uses tu propio usuario si ya existe.</span></label>
        <label class="label md:col-span-2">Tu perito responsable<select name="appraiser_id" class="input" required><option value="">Selecciona tu ficha con RAA vigente</option><?php foreach ($appraisers as $expert): if ($expert['active'] !== 'Si' || empty($expert['raa_expires_at']) || $expert['raa_expires_at'] < $today->format('Y-m-d')) continue; ?><option value="<?= e($expert['id']) ?>" <?= ($accessValues['appraiser_id']??'') === $expert['id'] ? 'selected' : '' ?>><?= e($expert['full_name'].' · '.$expert['raa_number']) ?></option><?php endforeach; ?></select></label>
        <p class="md:col-span-2">Contraseña inicial: igual al usuario. Al ingresar deberá cambiarla antes de acceder a datos. No necesita un RAA propio para diligenciar como analista.</p>
        <p class="md:col-span-2 text-sm">El analista verá únicamente los avalúos que cree con este acceso. Tú podrás revisarlos; no se le concede acceso a tus expedientes anteriores ni a la administración. Crear acceso no firma ni presenta informes.</p>
        <button class="btn-primary" :disabled="busy" x-text="busy ? 'Creando acceso…' : 'Crear acceso del analista'">Crear acceso del analista</button>
    </form>
    <h4 class="text-lg font-semibold">Accesos registrados en tu cuenta (<?= count($analystAccounts ?? []) ?>)</h4>
    <?php if (empty($analystAccounts)): ?><p class="rounded-lg border p-3" role="status">Todavía no hay accesos de analista registrados en tu cuenta. Los campos de arriba son para crear uno; escribirlos no crea ni guarda la cuenta. Si aparece un error al crear, el acceso no quedó registrado.</p><?php endif; ?>
    <?php foreach (($analystAccounts ?? []) as $account): ?>
    <div class="rounded-lg border p-3"><p><?= e($account['full_name']) ?> · <strong><?= e($account['username']) ?></strong> · <?= $account['active'] ? 'Activo' : 'Desactivado' ?></p>
        <?php if ($account['active']): ?><form class="mt-2" method="post" action="<?= e(url('maestros/accesos/'.$account['id'].'/desactivar')) ?>"><?= csrf_field() ?><button class="btn-secondary">Desactivar acceso</button></form><?php endif; ?>
    </div><?php endforeach; ?>
</div>
