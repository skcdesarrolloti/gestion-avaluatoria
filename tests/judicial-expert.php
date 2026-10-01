<?php
declare(strict_types=1);
use App\Services\JudicialExpertInput as JudicialInput;
use App\Services\JudicialExpertReport as JudicialReport;

$emptyJudicial = JudicialInput::dossier([]);
$judicialSections = JudicialReport::build([], [], $emptyJudicial, []);
expect(str_contains($judicialSections['1.15'][1], 'pendiente') && $emptyJudicial['exclusion'] === '', 'CGP no inventa declaraciones negativas ni ausencia de antecedentes');
$judicialHistory = [
    ['kind' => 'publication', 'date' => '2016-10-01', 'title' => 'Incluida', 'reference' => 'Ref A'],
    ['kind' => 'publication', 'date' => '2016-09-30', 'title' => 'Antigua'],
    ['kind' => 'publication', 'date' => '2026-10-02', 'title' => 'Futura'],
    ['kind' => 'case', 'date' => '2022-10-01', 'court' => 'Despacho A', 'parties' => 'A contra B', 'lawyers' => 'C y D', 'matter' => 'Otra materia'],
    ['kind' => 'case', 'date' => '2022-09-30', 'court' => 'Fuera de ventana'],
];
expect(count(JudicialReport::recent($judicialHistory, 'publication', '2026-10-01', 10)) === 1, 'CGP ventana de diez años incluye borde y excluye futuro');
expect(count(JudicialReport::recent($judicialHistory, 'case', '2026-10-01', 4)) === 1, 'CGP ventana de cuatro años incluye borde');
$judicialCase = JudicialInput::dossier(['report_date' => '2026-10-01', 'history_reviewed' => 'si', 'publication_refs' => [JudicialReport::publicationKey($judicialHistory[0])], 'previous' => 'sin_anteriores', 'regular' => 'si', 'regular_detail' => 'Técnica diferente justificada']);
$judicialSections = JudicialReport::build(['full_name' => 'Perito', 'identification_number' => '123'], ['history' => $judicialHistory], $judicialCase, []);
expect(str_contains($judicialSections['1.15'][1], 'Incluida') && !str_contains($judicialSections['1.15'][1], 'Antigua'), 'CGP exporta publicaciones pertinentes dentro del período');
expect(str_contains($judicialSections['1.16'][1], 'Otra materia') && str_contains($judicialSections['1.16'][1], 'C y D'), 'CGP casos de cuatro años conserva partes, apoderados y otras materias');
expect(str_contains($judicialSections['1.19'][1], 'no tener peritajes anteriores') && str_contains($judicialSections['1.19'][1], 'Técnica diferente'), 'CGP numerales ocho y nueve independientes');
expectStatus(422, fn () => JudicialInput::dossier(['report_date' => '2026-02-30']), 'CGP rechaza fecha inexistente');
expectStatus(422, fn () => JudicialInput::profile(['history_json' => '{"bad":1}']), 'CGP rechaza historial no listado');
expectStatus(422, fn () => JudicialInput::dossier(['publication_refs' => ['invalid']]), 'CGP rechaza referencias malformadas');
expectStatus(422, fn () => JudicialReport::validatePresentation(['identification_number' => '123'], [], $emptyJudicial), 'CGP no registra presentación de borrador incompleto');
$completeJudicial = array_fill_keys(JudicialInput::DOSSIER, 'Dato revisado');
$completeJudicial += ['report_date' => '2026-10-01', 'directed' => 'si', 'history_reviewed' => 'si', 'annexes_reviewed' => 'si', 'same_party' => 'no', 'exclusion' => 'no', 'previous' => 'sin_anteriores', 'regular' => 'no'];
$judicialProfile = array_fill_keys(JudicialInput::PROFILE, 'Verificado');
JudicialReport::validatePresentation(['identification_number' => '123'], $judicialProfile, $completeJudicial);
expectStatus(422, fn () => JudicialReport::validatePresentation(['identification_number' => '123'], $judicialProfile, array_replace($completeJudicial, ['regular' => 'si', 'regular_detail' => ''])), 'CGP exige justificar variación');
expectStatus(422, fn () => JudicialReport::validatePresentation(['identification_number' => '123'], $judicialProfile, array_replace($completeJudicial, ['exclusion' => 'si'])), 'CGP requiere resolver causales antes de confirmar presentación');
expect(count(App\Support\JudicialExpertAcademy::articles()) === 10, 'CGP academia abarca diez numerales');
$stalePublicationCase = $completeJudicial + ['publication_refs' => [str_repeat('a', 64)]];
expectStatus(409, fn () => JudicialReport::validatePresentation(['identification_number' => '123'], $judicialProfile, $stalePublicationCase), 'CGP detecta publicación modificada después de seleccionarla');
expect(str_contains(JudicialReport::build([], $judicialProfile, $stalePublicationCase, [])['1.15'][1], 'pendiente'), 'CGP referencia obsoleta no genera declaración falsa de ausencia');
