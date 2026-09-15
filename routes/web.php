<?php
declare(strict_types=1);

// method, path expression, controller, action, requires authentication
return [
    ['GET', '#^/login$#', 'auth', 'login', false],
    ['POST', '#^/login$#', 'auth', 'attempt', false],
    ['GET', '#^/diagnostico/login$#', 'diagnostics', 'login', false],
    ['POST', '#^/logout$#', 'auth', 'logout', true],
    ['GET', '#^/$#', 'appraisals', 'index', true],
    ['GET', '#^/normas-tecnicas-sectoriales$#', 'standards', 'index', true],
    ['GET', '#^/marco-juridico-valuatorio$#', 'legal', 'index', true],
    ['GET', '#^/normas-internacionales-valuacion$#', 'international', 'index', true],
    ['POST', '#^/normas-tecnicas-sectoriales/importar$#', 'standards', 'import', true],
    ['GET', '#^/normas-tecnicas-sectoriales/([a-z0-9-]+)$#', 'standards', 'show', true],
    ['GET', '#^/normas-tecnicas-sectoriales/([a-z0-9-]+)/archivo$#', 'standards', 'file', true],
    ['POST', '#^/avaluos$#', 'appraisals', 'create', true],
    ['GET', '#^/avaluos/([a-f0-9]{32})$#', 'appraisals', 'edit', true],
    ['POST', '#^/avaluos/([a-f0-9]{32})/borrador$#', 'appraisals', 'save', true],
];
