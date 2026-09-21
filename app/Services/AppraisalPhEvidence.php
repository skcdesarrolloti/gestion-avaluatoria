<?php
declare(strict_types=1);
namespace App\Services;

final class AppraisalPhEvidence
{
    private array $pages = [];
    public function __construct(string $text)
    {
        $source = 'Soporte PH'; $page = 0;
        foreach (preg_split('/(\[Documento: [^\]]+\]|\[Página \d+\])/u', $text, -1, PREG_SPLIT_DELIM_CAPTURE) ?: [] as $part) {
            if (preg_match('/^\[Documento: (.+)\]$/u', $part, $m)) { $source = $m[1]; continue; }
            if (preg_match('/^\[Página (\d+)\]$/u', $part, $m)) { $page = (int) $m[1]; continue; }
            $part = trim(preg_replace('/\s+/u', ' ', $part) ?? '');
            if ($part !== '') $this->pages[] = [$source, $page, $part, self::fold($part)];
        }
    }

    public static function fold(string $text): string
    {
        return strtr(mb_strtolower($text), ['á'=>'a', 'é'=>'e', 'í'=>'i', 'ó'=>'o', 'ú'=>'u', 'ü'=>'u']);
    }

    public function excerpts(array $terms, int $limit = 1200): string
    {
        $hits = [];
        foreach ($this->pages as $pageIndex => [$source, $page, $original, $plain]) {
            foreach ($terms as $priority => $term) {
                $offset = 0;
                while (($pos = mb_strpos($plain, self::fold($term), $offset)) !== false) {
                    $start = max(0, $pos - 60);
                    $extended = $original;
                    $next = $this->pages[$pageIndex + 1] ?? null;
                    if ($next && $next[0] === $source && $next[1] === $page + 1) {
                        $extended .= " [continúa p. {$next[1]}] " . mb_substr($next[2], 0, 500);
                    }
                    $quote = trim(mb_substr($extended, $start, 440));
                    $prefix = $page ? "[$source · p. $page] " : "[$source] ";
                    $id = $source . ':' . $page . ':' . (int) ($pos / 250);
                    $score = 1000 - $priority * 10;
                    if (!isset($hits[$id]) || $hits[$id]['score'] < $score) {
                        $hits[$id] = ['score'=>$score, 'page'=>"$source:$page", 'text'=>$prefix . '…' . $quote . '…'];
                    }
                    $offset = $pos + max(1, mb_strlen($term));
                }
            }
        }
        if (!$hits) return '';
        usort($hits, static fn ($a, $b) => $b['score'] <=> $a['score']);
        $selected = []; $seen = [];
        foreach ($hits as $hit) {
            if (isset($seen[$hit['page']])) continue;
            $seen[$hit['page']] = true; $selected[] = $hit['text'];
            if (count($selected) === 2) break;
        }
        return mb_substr(implode("\n", $selected), 0, $limit);
    }
}
