<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Models\IgacTypologyRepository;
use App\Support\IgacDocumentLibrary;

final class IgacTypologyController
{
    public function __construct(private IgacTypologyRepository $typologies) {}

    public function index(): void
    {
        $categories = $this->typologies->categories();
        $codes = array_column($categories, 'code');
        $requested = (string) ($_GET['categoria'] ?? '');
        $active = in_array($requested, $codes, true) ? $requested : 'RESIDENCIALES';
        $documentCategories = IgacDocumentLibrary::categories();
        $documentCodes = array_column($documentCategories, 'code');
        $documentCategory = (string) ($_GET['documentos'] ?? 'todos');
        if (!in_array($documentCategory, $documentCodes, true)) $documentCategory = 'todos';
        view('typologies/igac', [
            'title' => 'Biblioteca IGAC',
            'categories' => $categories,
            'activeCategoryCode' => $active,
            'typologies' => $this->typologies->byCategory($active),
            'stats' => $this->typologies->stats(),
            'documentCategories' => $documentCategories,
            'activeDocumentCategory' => $documentCategory,
            'documents' => IgacDocumentLibrary::documents($documentCategory),
            'documentStats' => IgacDocumentLibrary::stats(),
        ]);
    }

    public function document(string $id): void
    {
        $document = IgacDocumentLibrary::find($id);
        if ($document === null) {
            throw new \App\Core\HttpException(404, 'No se encontró el documento IGAC.');
        }
        view('typologies/igac-document', ['title' => (string) $document['codigo'], 'document' => $document]);
    }

    public function download(string $id): void
    {
        $document = IgacDocumentLibrary::find($id);
        if ($document === null) throw new \App\Core\HttpException(404, 'No se encontró el documento IGAC.');
        $url = trim((string) ($document['archivo_descarga'] ?? ''));
        if ($url === '') throw new \App\Core\HttpException(404, 'Este documento aún no tiene archivo descargable.');
        $this->assertOfficialDownload($url);
        $filename = $this->downloadName($document, $url);
        $body = $this->fetchOfficialFile($url);
        header('Content-Type: ' . $this->contentType($filename));
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($body));
        echo $body;
    }

    private function assertOfficialDownload(string $url): void
    {
        $parts = parse_url($url);
        $host = strtolower((string) ($parts['host'] ?? ''));
        $path = (string) ($parts['path'] ?? '');
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $allowed = $host === 'www.igac.gov.co'
            && str_starts_with($path, '/sites/default/files/')
            && in_array($extension, ['pdf', 'docx'], true);
        if (!$allowed) throw new \App\Core\HttpException(400, 'Archivo IGAC no permitido para descarga.');
    }

    private function fetchOfficialFile(string $url): string
    {
        if (function_exists('curl_init')) {
            $curl = curl_init($url);
            curl_setopt_array($curl, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_TIMEOUT => 45,
                CURLOPT_USERAGENT => 'GestionAvaluatoria/1.0',
            ]);
            $body = curl_exec($curl);
            $status = (int) curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
            $error = curl_error($curl);
            curl_close($curl);
            if (is_string($body) && $body !== '' && $status >= 200 && $status < 300) return $body;
            throw new \App\Core\HttpException(502, 'No se pudo descargar el archivo IGAC. ' . ($error ?: 'Estado HTTP ' . $status));
        }
        $context = stream_context_create(['http' => [
            'timeout' => 45,
            'header' => "User-Agent: GestionAvaluatoria/1.0\r\n",
        ]]);
        $body = @file_get_contents($url, false, $context);
        if (is_string($body) && $body !== '') return $body;
        throw new \App\Core\HttpException(502, 'No se pudo descargar el archivo IGAC.');
    }

    private function downloadName(array $document, string $url): string
    {
        $name = trim((string) ($document['archivo_nombre'] ?? ''));
        if ($name === '') $name = rawurldecode(basename((string) parse_url($url, PHP_URL_PATH)));
        $name = preg_replace('/[^A-Za-z0-9 ._\-]/', '', $name) ?: 'documento-igac';
        return str_contains($name, '.') ? $name : $name . '.pdf';
    }

    private function contentType(string $filename): string
    {
        return strtolower(pathinfo($filename, PATHINFO_EXTENSION)) === 'docx'
            ? 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            : 'application/pdf';
    }
}
