<?php
declare(strict_types=1);
namespace App\Services;
use App\Core\HttpException;

final class MarketPhScope
{
    public const NATURES = [''=>'Selecciona según soporte', 'privada'=>'Unidad privada con matrícula independiente',
        'integrada'=>'Parte privada en la misma matrícula de la principal', 'comun_exclusivo'=>'Bien común de uso exclusivo'];
    public const AREAS = [''=>'Por verificar', 'incluida'=>'Incluida en el área privada registrada de la principal',
        'excluida'=>'Excluida del área privada registrada de la principal', 'no_aplica'=>'No es área privada: común de uso exclusivo'];
    public const KEYS = ['legal_nature', 'legal_source', 'parent_unit_id', 'area_in_parent', 'parent_area_source', 'parent_area_note'];

    public static function input(mixed $post): array
    {
        if (!is_array($post)) throw new HttpException(422, 'Diligencia el alcance PH.');
        $out = [];
        foreach (self::KEYS as $key) {
            if (!array_key_exists($key, $post)) continue;
            if (!is_string($post[$key]) || mb_strlen($post[$key]) > 1200) throw new HttpException(422, 'Revisa el alcance PH: máximo 1200 caracteres por campo.');
            $out[$key] = trim($post[$key]);
        }
        if (isset($out['legal_nature']) && !array_key_exists($out['legal_nature'], self::NATURES)) throw new HttpException(422, 'Naturaleza jurídica inválida.');
        if (isset($out['area_in_parent']) && !array_key_exists($out['area_in_parent'], self::AREAS)) throw new HttpException(422, 'Relación del área inválida.');
        if (!empty($out['parent_unit_id']) && !preg_match('/^[a-f0-9]{32}$/D', $out['parent_unit_id'])) throw new HttpException(422, 'Unidad principal inválida.');
        return $out;
    }

    public static function parent(array $data, array $unit, array $units): ?array
    {
        foreach ($units as $candidate) {
            if (($candidate['id'] ?? '') === ($data['parent_unit_id'] ?? '') && ($candidate['id'] ?? '') !== ($unit['id'] ?? '')
                && ($candidate['unit_kind'] ?? '') === 'property') return $candidate;
        }
        return null;
    }

    public static function validateParent(array $patch, array $unit, array $units): void
    {
        if (!empty($patch['parent_unit_id']) && (($unit['unit_kind'] ?? '') !== 'annex' || self::parent($patch, $unit, $units) === null))
            throw new HttpException(422, 'Vincula el anexo con una unidad principal activa de este expediente.');
    }

    public static function row(array $unit, array $units): array
    {
        $data = MarketSubjectEvidence::decode($unit);
        $nature = $data['legal_nature'] ?? '';
        $source = trim((string) ($data['parent_area_source'] ?? ''));
        $state = 'missing'; $value = 'Vínculo y composición por definir';
        $message = 'Define en M2 la unidad principal, la relación del área y su soporte. No se suman áreas automáticamente.';
        if (($unit['unit_kind'] ?? '') === 'property') {
            $children = array_filter($units, static fn ($child) => (MarketSubjectEvidence::decode($child)['parent_unit_id'] ?? '') === ($unit['id'] ?? ''));
            $pending = array_filter($units, static fn ($child) => ($child['unit_kind'] ?? '') === 'annex' && self::parent(MarketSubjectEvidence::decode($child), $child, $units) === null);
            $bad = array_filter($children, static fn ($child) => self::row($child, $units)['state'] !== 'ok');
            $state = $nature === '' || $pending || $bad ? 'missing' : 'ok';
            if (in_array($nature, ['integrada','comun_exclusivo'], true) || !empty($data['parent_unit_id'])) $state = 'difference';
            $value = count($children) . ' anexo(s) vinculados · ' . count($pending) . ' sin principal · ' . count($bad) . ' vínculo(s) por revisar';
            $message = $state === 'ok' ? 'Anexos vinculados con composición documentada. Cada unidad conserva su área; no se hace una suma automática.'
                : 'Revisa en M2 la naturaleza de la principal y el vínculo y composición de los anexos; no se presume cuál oficina los contiene.';
            $source = 'Composición PH de los anexos en M2';
        } else {
            $parent = self::parent($data, $unit, $units);
            $area = $data['area_in_parent'] ?? '';
            $value = ($parent['label'] ?? 'Sin principal válida') . ' · ' . (self::AREAS[$area] ?? 'Por verificar');
            if ($parent && $source !== '' && $nature !== '' && $area !== '') {
                $state = 'ok'; $message = 'Relación y soporte registrados. La inclusión del área no autoriza sumar nuevamente el anexo.';
                if (($nature === 'comun_exclusivo' && $area !== 'no_aplica') || ($nature !== 'comun_exclusivo' && $area === 'no_aplica')
                    || ($nature === 'privada' && $area === 'incluida')) {
                    $state = 'difference'; $message = 'Naturaleza y composición del área requieren revisión: un común no es área privada y una unidad independiente no debe duplicarse en el área de la principal.';
                }
            }
        }
        return ['key'=>'ph_scope', 'label'=>'Vínculo PH y composición del área', 'value'=>$value, 'source'=>'M2 · ' . ($source ?: 'Sin soporte'),
            'state'=>$state, 'message'=>$message, 'section'=>'methodology', 'detail'=>''];
    }
}
