<?php
declare(strict_types=1);
namespace App\Services;

use App\Models\MidasDocumentRepository;

final class AppraisalMidasIncorporation
{
    public static function targets(): array
    {
        return [
            'Localidades' => 'Capítulos 2 y 3: mapas de contexto, localidad del predio y fuente territorial.',
            'Unidades comuneras de gobierno' => 'Capítulos 2 y 3: sector, UCG y contexto urbano inmediato.',
            'Circulares MIDAS' => 'Capítulo 5: soporte complementario de Planeación y normatividad urbana.',
            'Educación' => 'Capítulos 2 y 6: equipamientos educativos y actividad institucional del sector.',
            'Cambio climático' => 'Capítulo 7: amenazas, vulnerabilidad, adaptación y salvedades ambientales.',
        ];
    }

    public static function notes(): array
    {
        return [
            'Localidades' => 'El entregable puede mostrar el mapa general y el encuadre de la localidad del inmueble.',
            'Unidades comuneras de gobierno' => 'Deja soporte para ubicar el inmueble dentro de la UCG correspondiente.',
            'Circulares MIDAS' => 'Queda como fuente documental de consulta para respaldar la lectura urbana.',
            'Educación' => 'Alimenta la lectura de actividad institucional, colegios oficiales y privados.',
            'Cambio climático' => 'Alimenta la revisión de riesgos, ambiente y condiciones restrictivas.',
        ];
    }

    public function rows(array $documents): array
    {
        $rows = [];
        $counts = array_fill_keys(array_keys(MidasDocumentRepository::downloadGroups()), 0);
        $samples = array_fill_keys(array_keys($counts), []);
        foreach ($documents as $document) {
            $group = MidasDocumentRepository::canonicalGroup((string) ($document['layer_group'] ?? ''));
            if (!array_key_exists($group, $counts)) continue;
            $counts[$group]++;
            if (count($samples[$group]) < 3) {
                $samples[$group][] = (string) (($document['title'] ?? '') ?: ($document['source_filename'] ?? 'Documento MIDAS'));
            }
        }
        foreach (MidasDocumentRepository::downloadGroups() as $group => $route) {
            $rows[] = [
                'group' => $group,
                'route' => $route,
                'count' => (int) ($counts[$group] ?? 0),
                'target' => self::targets()[$group] ?? 'Pendiente de clasificar.',
                'note' => self::notes()[$group] ?? 'Documento disponible para consulta del analista.',
                'samples' => $samples[$group] ?? [],
                'status' => ($counts[$group] ?? 0) > 0 ? 'Incorporado' : 'Pendiente',
            ];
        }
        return $rows;
    }

    public function deliverable(array $documents): array
    {
        $rows = $this->rows($documents);
        $active = array_values(array_filter($rows, static fn (array $row): bool => (int) $row['count'] > 0));
        $text = $active === []
            ? 'No hay documentos MIDAS comunes cargados para incorporar al entregable.'
            : $this->text($active);
        return ['rows' => $rows, 'active' => $active, 'text' => $text];
    }

    private function text(array $rows): string
    {
        $parts = ['Biblioteca MIDAS incorporada como trazabilidad documental del informe.'];
        foreach ($rows as $row) {
            $parts[] = $row['group'] . ': ' . $row['count'] . ' documento(s). '
                . $row['target'] . ' ' . $row['note'];
        }
        return implode("\n\n", $parts);
    }
}
