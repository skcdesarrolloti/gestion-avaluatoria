<?php
declare(strict_types=1);
namespace App\Services;
use App\Core\HttpException;
use SimpleXMLElement;
use ZipArchive;

final class ComparableExcelArchive
{
    private ZipArchive $zip;
    public function __construct(string $path)
    {
        if (!class_exists(ZipArchive::class)) throw new HttpException(503, 'El servidor requiere la extensión ZIP de PHP para leer Excel.');
        $this->zip = new ZipArchive();
        if ($this->zip->open($path) !== true) throw new HttpException(422, 'El archivo no es un Excel .xlsx válido.');
        $size = 0;
        if ($this->zip->numFiles > 2000) $this->fail();
        for ($i = 0; $i < $this->zip->numFiles; $i++) {
            $entry = $this->zip->statIndex($i); $size += $entry['size'];
            if ($entry['size'] > 8000000 || $size > 20000000 || str_contains($entry['name'], '..')) $this->fail();
        }
        if ($this->zip->locateName('xl/vbaProject.bin') !== false) $this->fail();
    }
    public function __destruct() { if (isset($this->zip)) $this->zip->close(); }
    private function fail(): never { throw new HttpException(422, 'Excel no admitido: tamaño, estructura o contenido inválido. Usa el archivo exportado por el módulo.'); }
    public function xml(string $path): SimpleXMLElement
    {
        $text = $this->zip->getFromName($path);
        if ($text === false || stripos($text, '<!DOCTYPE') !== false || stripos($text, '<!ENTITY') !== false) $this->fail();
        $previous = libxml_use_internal_errors(true);
        try { $xml = simplexml_load_string($text, SimpleXMLElement::class, LIBXML_NONET); }
        finally { libxml_clear_errors(); libxml_use_internal_errors($previous); }
        if (!$xml) $this->fail();
        return $xml;
    }
    public function sheets(): array
    {
        $targets = [];
        foreach ($this->xml('xl/_rels/workbook.xml.rels')->xpath('//*[local-name()="Relationship"]') as $relation) {
            $target = (string) $relation['Target'];
            if (($relation['TargetMode'] ?? '') === 'External' || str_contains($target, '..')) continue;
            $targets[(string) $relation['Id']] = str_starts_with($target, '/') ? ltrim($target, '/') : 'xl/' . $target;
        }
        $sheets = [];
        foreach ($this->xml('xl/workbook.xml')->xpath('//*[local-name()="sheet"]') as $sheet) {
            $id = (string) $sheet->attributes('http://schemas.openxmlformats.org/officeDocument/2006/relationships')['id'];
            if (isset($targets[$id])) $sheets[(string) $sheet['name']] = $targets[$id];
        }
        return $sheets;
    }
    public function rows(string $path): array
    {
        $strings = [];
        if ($this->zip->locateName('xl/sharedStrings.xml') !== false)
            foreach ($this->xml('xl/sharedStrings.xml')->xpath('//*[local-name()="si"]') as $item) $strings[] = self::text($item);
        $rows = []; $cells = 0;
        foreach ($this->xml($path)->xpath('//*[local-name()="sheetData"]/*[local-name()="row"]') as $row) {
            $values = [];
            foreach ($row->xpath('./*[local-name()="c"]') as $cell) {
                if (++$cells > 100000) $this->fail();
                $reference = (string) $cell['r'];
                if (!preg_match('/^([A-Z]{1,3})\d+$/D', $reference, $match)) $this->fail();
                $number = 0; foreach (str_split($match[1]) as $letter) $number = $number * 26 + ord($letter) - 64;
                $v = $cell->xpath('./*[local-name()="v"]')[0] ?? null;
                $value = (string) $v;
                $type = (string) $cell['t'];
                if ($type === 's') $value = $strings[(int) $value] ?? '';
                if ($type === 'inlineStr') $value = self::text($cell);
                $values[$number - 1] = ['value' => $value, 'formula' => count($cell->xpath('./*[local-name()="f"]')) > 0, 'type' => $type];
            }
            if ($values) $rows[] = $values;
        }
        return $rows;
    }
    private static function text(SimpleXMLElement $element): string
    { return implode('', array_map(static fn ($node) => (string) $node, $element->xpath('.//*[local-name()="t"]'))); }
}
