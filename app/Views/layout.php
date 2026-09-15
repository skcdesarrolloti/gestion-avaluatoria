<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?= e($_SESSION['csrf'] ?? '') ?>">
    <title><?= e($title ?? 'Gestión avaluatoria') ?> · SuCasa</title>
    <link rel="stylesheet" href="<?= e(url('assets/app.css')) ?>">
    <script type="module" src="<?= e(url('assets/app.js')) ?>"></script>
</head>
<body class="min-h-dvh bg-slate-50 text-slate-900 antialiased">
    <a href="#contenido" class="sr-only focus:not-sr-only focus:block focus:p-4">Saltar al contenido</a>
    <header class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-4 px-5 py-5">
            <a href="<?= e(url()) ?>" class="flex items-center gap-3 font-semibold">
                <span aria-hidden="true" class="grid size-11 place-items-center rounded-xl bg-teal-800 text-sm text-white">SC</span>
                <span>Gestión avaluatoria<span class="block text-xs font-normal tracking-wide text-slate-500">SuCasa Inmobiliaria</span></span>
            </a>
            <?php if (isset($_SESSION['user'])): ?>
                <div class="flex flex-wrap items-center gap-3 text-sm">
                    <nav class="flex flex-wrap items-center gap-2" aria-label="Principal">
                        <a class="rounded-lg px-3 py-2 font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-950" href="<?= e(url()) ?>">Avalúos</a>
                        <a class="rounded-lg px-3 py-2 font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-950" href="<?= e(url('normas-tecnicas-sectoriales')) ?>">Normas Técnicas Sectoriales</a>
                    </nav>
                    <span class="max-w-48 break-words text-slate-500"><?= e($_SESSION['user']['name']) ?></span>
                    <form method="post" action="<?= e(url('logout')) ?>">
                        <?= csrf_field() ?><button class="btn-secondary" type="submit">Salir</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </header>
    <main id="contenido" class="mx-auto max-w-6xl px-5 py-10"><?= $content ?></main>
    <footer class="mx-auto max-w-6xl px-5 py-8 text-xs text-slate-500">SuCasa · Gestión avaluatoria</footer>
</body>
</html>
