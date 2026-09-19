<?php
declare(strict_types=1);
namespace App\Services;
use App\Models\AppraisalPhRepository;

final class AppraisalPhDocumentUploadService
{
    private const BLOB_BACKUP_BYTES = 52428800;

    public function store(array $files, string $appraisalId, int $owner, string $typology,
        AppraisalPhRepository $repo): array
    {
        $uploads = $this->files($files);
        if (!$uploads) throw new \RuntimeException('Selecciona al menos un soporte PH para analizar.');
        $texts = []; $names = []; $stored = [];
        foreach ($uploads as $file) {
            $name = mb_substr(basename(str_replace('\\', '/', (string) $file['name'])), 0, 220);
            if ((int) $file['error'] !== UPLOAD_ERR_OK) {
                throw new \RuntimeException(($name ?: 'El soporte PH') . ' '
                    . AppraisalPhDocumentStorage::uploadErrorMessage((int) $file['error']));
            }
            $info = AppraisalPhDocumentStorage::inspect((string) $file['tmp_name'], $name);
            $id = bin2hex(random_bytes(16));
            $storageName = 'ph-' . $appraisalId . '-' . $id . '.' . $info['extension'];
            $bytes = AppraisalPhDocumentStorage::storeUploaded((string) $file['tmp_name'],
                AppraisalPhDocumentStorage::path($storageName));
            $path = AppraisalPhDocumentStorage::path($storageName);
            [$text, $readNames] = in_array($info['extension'], ['zip', 'rar'], true)
                ? $this->archiveText($path, $name, $info['extension'])
                : $this->singleText($path, $name, $info['extension']);
            $texts[] = $text; $names = array_merge($names, $readNames);
            $blob = $bytes <= self::BLOB_BACKUP_BYTES ? file_get_contents($path) : null;
            if ($bytes <= self::BLOB_BACKUP_BYTES && !is_string($blob)) {
                throw new \RuntimeException('El soporte PH se guardó, pero no quedó respaldado.');
            }
            $stored[] = ['id' => $id, 'source_filename' => $name, 'storage_filename' => $storageName,
                'mime_type' => $info['mime'], 'file_size_bytes' => $bytes, 'extracted_chars' => mb_strlen($text),
                'file_blob' => $blob];
        }
        $analysis = (new AppraisalPhDocumentAnalyzer())->analyze(trim(implode("\n\n", array_filter($texts))),
            $names, $typology);
        foreach ($stored as $file) {
            $repo->addDocument($appraisalId, $owner, $file + [
                'analysis_status' => 'Lectura preliminar',
                'analysis_message' => $analysis['summary'],
            ]);
        }
        $repo->mergeAnalysis($appraisalId, $owner, $analysis);
        return $analysis;
    }

    private function archiveText(string $path, string $name, string $extension): array
    {
        return $extension === 'zip' ? $this->zipText($path, $name) : $this->rarText($path, $name);
    }

    private function zipText(string $path, string $name): array
    {
        $zip = new \ZipArchive();
        if ($zip->open($path) !== true) throw new \RuntimeException("$name no es un ZIP válido.");
        $texts = []; $names = [];
        for ($i = 0; $i < $zip->numFiles && count($names) < 80; $i++) {
            $entry = $zip->getNameIndex($i);
            if (!is_string($entry) || str_ends_with($entry, '/')) continue;
            $ext = mb_strtolower(pathinfo($entry, PATHINFO_EXTENSION));
            if (!in_array($ext, ['pdf', 'docx', 'txt', 'jpg', 'jpeg', 'png', 'webp', 'tif', 'tiff'], true)) continue;
            $stream = $zip->getStream($entry);
            if (!$stream) continue;
            $tmp = tempnam(sys_get_temp_dir(), 'ga_ph_zip_');
            $out = fopen($tmp, 'wb');
            if (!$out) { fclose($stream); continue; }
            stream_copy_to_stream($stream, $out); fclose($out); fclose($stream);
            $names[] = basename(str_replace('\\', '/', $entry));
            $texts[] = (new LegalCertificateTextExtractor())->extract($tmp, $ext);
            @unlink($tmp);
        }
        $zip->close();
        return [trim(implode("\n\n", array_filter($texts))), $names ?: [$name]];
    }

    private function rarText(string $path, string $name): array
    {
        $dir = sys_get_temp_dir() . '/ga_ph_rar_' . bin2hex(random_bytes(4));
        if (!mkdir($dir, 0775, true) && !is_dir($dir)) throw new \RuntimeException('No se pudo preparar extracción RAR.');
        try {
            $this->extractRar($path, $dir, $name);
            $texts = []; $names = [];
            foreach ($this->extractedFiles($dir) as $file) {
                $ext = mb_strtolower(pathinfo($file, PATHINFO_EXTENSION));
                if (!in_array($ext, ['pdf', 'docx', 'txt', 'jpg', 'jpeg', 'png', 'webp', 'tif', 'tiff'], true)) continue;
                $names[] = basename($file); $texts[] = (new LegalCertificateTextExtractor())->extract($file, $ext);
                if (count($names) >= 80) break;
            }
            return [trim(implode("\n\n", array_filter($texts))), $names ?: [$name]];
        } finally {
            foreach (array_reverse($this->extractedFiles($dir, true)) as $file) is_dir($file) ? @rmdir($file) : @unlink($file);
            @rmdir($dir);
        }
    }

    private function extractRar(string $path, string $dir, string $name): void
    {
        if (class_exists('\\RarArchive')) {
            $rar = \RarArchive::open($path);
            if ($rar) {
                foreach ($rar->getEntries() ?: [] as $entry) $entry->extract($dir);
                $rar->close(); return;
            }
        }
        if (!function_exists('exec')) {
            throw new \RuntimeException("$name es RAR, pero PHP no permite ejecutar extractores en este servidor. Sube ZIP o habilita 7z/unrar/unar/bsdtar.");
        }
        foreach ($this->rarCommands($path, $dir) as $command) {
            $output = []; $code = 1; exec($command . ' 2>&1', $output, $code);
            if ($code === 0 && $this->extractedFiles($dir) !== []) return;
        }
        throw new \RuntimeException("$name es RAR, pero este servidor no tiene motor RAR disponible. Sube ZIP o instala 7z/unrar/unar/bsdtar.");
    }

    private function rarCommands(string $path, string $dir): array
    {
        $p = escapeshellarg($path); $d = escapeshellarg($dir);
        $sevenZip = $this->cmd('7z'); $unrar = $this->cmd('unrar'); $unar = $this->cmd('unar');
        $bsdtar = $this->cmd('bsdtar'); $tar = $this->cmd('tar');
        return array_values(array_filter([
            $sevenZip ? "$sevenZip x -y -o$d $p" : '',
            $unrar ? "$unrar x -o+ $p $d" : '',
            $unar ? "$unar -o $d $p" : '',
            $bsdtar ? "$bsdtar -xf $p -C $d" : '',
            $tar ? "$tar -xf $p -C $d" : '',
        ]));
    }

    private function cmd(string $name): string
    {
        if (!function_exists('shell_exec')) return '';
        $probe = stripos(PHP_OS_FAMILY, 'Windows') === 0 ? 'where ' : 'command -v ';
        $found = shell_exec($probe . escapeshellarg($name) . ' 2>' . (stripos(PHP_OS_FAMILY, 'Windows') === 0 ? 'NUL' : '/dev/null'));
        $path = trim(strtok((string) $found, "\r\n") ?: '');
        return $path !== '' ? escapeshellarg($path) : '';
    }

    private function extractedFiles(string $dir, bool $withDirs = false): array
    {
        $iterator = is_dir($dir) ? new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir,
            \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST) : new \ArrayIterator([]);
        $files = [];
        foreach ($iterator as $file) if ($withDirs || $file->isFile()) $files[] = $file->getPathname();
        return $files;
    }

    private function singleText(string $path, string $name, string $extension): array
    {
        return [(new LegalCertificateTextExtractor())->extract($path, $extension), [$name]];
    }

    private function files(array $files): array
    {
        $name = $files['name'] ?? null;
        if (!is_array($name)) {
            return trim((string) $name) === '' ? [] : [[
                'name' => (string) ($files['name'] ?? ''),
                'tmp_name' => (string) ($files['tmp_name'] ?? ''),
                'error' => (int) ($files['error'] ?? UPLOAD_ERR_NO_FILE),
            ]];
        }
        $items = [];
        foreach ($name as $index => $value) {
            if (trim((string) $value) === '' && (int) ($files['error'][$index] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) continue;
            $items[] = ['name' => (string) $value, 'tmp_name' => (string) ($files['tmp_name'][$index] ?? ''),
                'error' => (int) ($files['error'][$index] ?? UPLOAD_ERR_NO_FILE)];
        }
        return array_slice($items, 0, 80);
    }
}
