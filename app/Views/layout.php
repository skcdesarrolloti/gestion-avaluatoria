<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?= e($_SESSION['csrf'] ?? '') ?>">
    <title><?= e($title ?? 'Gestión avaluatoria') ?> · SuCasa</title>
    <link rel="stylesheet" href="<?= e(asset_url('assets/app.css')) ?>">
    <script type="module" src="<?= e(asset_url('assets/app.js')) ?>"></script>
</head>
<?php
$logged = isset($_SESSION['user']);
$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$basePath = App\Core\Http::basePath();
$currentPath = '/' . trim(substr($requestPath, strlen($basePath)), '/');
$isActive = static fn (string $path): bool => $path === '/' ? $currentPath === '/' : str_starts_with($currentPath, $path);
$tabs = [
    ['label' => 'Inicio', 'href' => url(), 'active' => $isActive('/')],
    ['label' => 'Normas Técnicas Sectoriales', 'href' => url('normas-tecnicas-sectoriales'), 'active' => $isActive('/normas-tecnicas-sectoriales')],
    ['label' => 'Marco Jurídico Nacional', 'href' => url('marco-juridico-valuatorio'), 'active' => $isActive('/marco-juridico-valuatorio')],
    ['label' => 'Normas Internacionales', 'href' => url('normas-internacionales-valuacion'), 'active' => $isActive('/normas-internacionales-valuacion')],
    ['label' => 'Normas NIIF', 'href' => url('normas-niif'), 'active' => $isActive('/normas-niif')],
    ['label' => 'Tipologías Constructivas IGAC', 'href' => url('tipologias-constructivas-igac'), 'active' => $isActive('/tipologias-constructivas-igac')],
];
?>
<body class="min-h-dvh bg-slate-50 text-slate-900 antialiased">
    <div id="app-loader" class="app-loader" role="status" aria-live="polite" hidden>
        <span class="app-spinner" aria-hidden="true"></span>
        <span data-loader-text>Cargando...</span>
    </div>
    <a href="#contenido" class="sr-only focus:not-sr-only focus:block focus:p-4">Saltar al contenido</a>
    <header class="<?= $logged ? 'bg-white' : 'border-b border-slate-200 bg-white' ?>">
        <?php if ($logged): ?>
            <div class="app-topbar">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <a href="<?= e(url()) ?>" class="brand-frame">
                        <span aria-hidden="true" class="brand-mark">SC</span>
                        <span class="brand-title">Gestión avaluatoria<span class="brand-subtitle">SuCasa Inmobiliaria</span></span>
                    </a>
                    <form method="post" action="<?= e(url('logout')) ?>">
                        <?= csrf_field() ?><button class="text-sm font-medium text-slate-100 hover:text-white" type="submit">Cerrar sesión</button>
                    </form>
                    <span class="max-w-48 break-words text-sm font-semibold text-white"><?= e($_SESSION['user']['name']) ?></span>
                </div>
            </div>
            <div class="app-shell">
                <div class="flex flex-wrap justify-end gap-3">
                    <a class="app-action app-action-blue" href="<?= e(url('#nuevo-avaluo')) ?>">Crear ficha</a>
                    <a class="app-action app-action-orange" href="<?= e(url()) ?>">Mis avalúos</a>
                    <a class="app-action app-action-teal" href="<?= e(url('normas-tecnicas-sectoriales')) ?>">Normas técnicas</a>
                    <a class="app-action app-action-orange" href="<?= e(url('marco-juridico-valuatorio')) ?>">Marco jurídico</a>
                    <a class="app-action app-action-blue" href="<?= e(url('normas-internacionales-valuacion')) ?>">Internacionales</a>
                    <a class="app-action app-action-teal" href="<?= e(url('normas-niif')) ?>">NIIF</a>
                    <a class="app-action app-action-teal" href="<?= e(url('tipologias-constructivas-igac')) ?>">Tipologías IGAC</a>
                </div>
                <nav class="app-tabs" aria-label="Principal">
                    <?php foreach ($tabs as $tab): ?>
                        <a class="app-tab <?= $tab['active'] ? 'app-tab-active' : '' ?>" href="<?= e($tab['href']) ?>"><?= e($tab['label']) ?></a>
                    <?php endforeach; ?>
                </nav>
            </div>
        <?php else: ?>
            <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-4 px-5 py-5">
                <a href="<?= e(url()) ?>" class="flex items-center gap-3 font-semibold">
                    <span aria-hidden="true" class="grid size-11 place-items-center rounded-xl bg-teal-800 text-sm text-white">SC</span>
                    <span>Gestión avaluatoria<span class="block text-xs font-normal tracking-wide text-slate-500">SuCasa Inmobiliaria</span></span>
                </a>
            </div>
        <?php endif; ?>
    </header>
    <main id="contenido" class="mx-auto max-w-6xl px-5 <?= $logged ? 'py-8' : 'py-10' ?>"><?= $content ?></main>
    <footer class="mx-auto max-w-6xl px-5 py-8 text-xs text-slate-500">SuCasa · Gestión avaluatoria</footer>
</body>
</html>
