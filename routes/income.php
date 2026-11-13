<?php
declare(strict_types=1);
return [
    ['POST', '#^/avaluos/([a-f0-9]{32})/metodologia-valuatoria/renta/valoracion$#', 'incomeValuation', 'save', true],
    ['POST', '#^/avaluos/([a-f0-9]{32})/metodologia-valuatoria/renta/origen$#', 'incomeOrigin', 'save', true],
    ['GET', '#^/avaluos/([a-f0-9]{32})/metodologia-valuatoria/renta/memoria$#', 'incomeValuation', 'export', true],
];
