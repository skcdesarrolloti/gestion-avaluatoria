<?php
declare(strict_types=1);
namespace App\Support;

final class IgacDocumentLibrary
{
    private const DATA_FILE = BASE_PATH . '/resources/data/igac_document_library.json';

    public static function categories(): array
    {
        return self::data()['categories'];
    }

    public static function documents(?string $category = null): array
    {
        $documents = self::data()['documents'];
        if ($category === null || $category === '' || $category === 'todos') {
            return $documents;
        }
        return array_values(array_filter($documents, static fn (array $document): bool =>
            in_array($category, $document['categoria_igac'] ?? [], true)
        ));
    }

    public static function find(string $id): ?array
    {
        foreach (self::documents() as $document) {
            if (($document['id'] ?? '') === $id) return $document;
        }
        return null;
    }

    public static function urlFor(string $id): string
    {
        return url('igac/documento/' . rawurlencode($id));
    }

    public static function stats(): array
    {
        $documents = self::documents();
        $topics = [];
        foreach ($documents as $document) {
            foreach (($document['temas_relacionados'] ?? []) as $topic) $topics[$topic] = true;
        }
        return ['documents' => count($documents), 'topics' => count($topics)];
    }

    private static function data(): array
    {
        static $data = null;
        if ($data !== null) return $data;
        if (!is_file(self::DATA_FILE)) {
            throw new \RuntimeException('No se encontró la biblioteca documental IGAC.');
        }
        $decoded = json_decode((string) file_get_contents(self::DATA_FILE), true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($decoded)) throw new \RuntimeException('La biblioteca IGAC no tiene un formato válido.');
        $data = [
            'categories' => is_array($decoded['categories'] ?? null) ? $decoded['categories'] : [],
            'documents' => array_map([self::class, 'hydrate'], is_array($decoded['documents'] ?? null) ? $decoded['documents'] : []),
        ];
        return $data;
    }

    private static function hydrate(array $document): array
    {
        foreach (['categoria_igac', 'temas_relacionados', 'modulos_que_lo_utilizan'] as $key) {
            $document[$key] = array_values(array_filter(array_map('strval',
                is_array($document[$key] ?? null) ? $document[$key] : [])));
        }
        return $document;
    }
}
