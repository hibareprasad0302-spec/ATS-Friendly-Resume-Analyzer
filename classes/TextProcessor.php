<?php

class TextProcessor
{
    private array $stopwords;

    public function __construct()
    {
        $this->stopwords = require __DIR__ . '/../data/stopwords.php';
    }

    public function clean(string $text): string
    {
        $text = $this->normalizeWhitespace($text);
        $text = $this->removeSpecialCharacters($text);
        $text = mb_strtolower($text, 'UTF-8');
        return $text;
    }

    public function tokenize(string $text): array
    {
        $words = preg_split('/\s+/', trim($text));
        return array_values(array_filter($words, fn($w) => strlen($w) > 1));
    }

    public function removeStopwords(array $tokens): array
    {
        $stopSet = array_flip($this->stopwords);
        return array_values(array_filter(
            $tokens,
            fn($t) => !isset($stopSet[$t])
        ));
    }

    public function extractNgrams(array $tokens, int $n = 2): array
    {
        $ngrams = [];
        $count = count($tokens);
        for ($i = 0; $i <= $count - $n; $i++) {
            $ngrams[] = implode(' ', array_slice($tokens, $i, $n));
        }
        return $ngrams;
    }

    public function wordFrequency(array $tokens): array
    {
        return array_count_values($tokens);
    }

    private function normalizeWhitespace(string $text): string
    {
        return preg_replace('/\s+/', ' ', trim($text));
    }

    private function removeSpecialCharacters(string $text): string
    {
        return preg_replace('/[^a-zA-Z0-9\s\.\+\#\-\/]/', '', $text);
    }
}
