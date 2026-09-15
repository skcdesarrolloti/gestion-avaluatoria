<?php
declare(strict_types=1);

// method, path expression, controller, action, requires authentication
return [
    ['GET', '#^/login$#', 'auth', 'login', false],
    ['POST', '#^/login$#', 'auth', 'attempt', false],
    ['POST', '#^/logout$#', 'auth', 'logout', true],
    ['GET', '#^/$#', 'appraisals', 'index', true],
    ['POST', '#^/avaluos$#', 'appraisals', 'create', true],
    ['GET', '#^/avaluos/([a-f0-9]{32})$#', 'appraisals', 'edit', true],
    ['POST', '#^/avaluos/([a-f0-9]{32})/borrador$#', 'appraisals', 'save', true],
];
