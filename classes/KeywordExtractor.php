<?php

class KeywordExtractor
{
    private TextProcessor $processor;

    public function __construct()
    {
        $this->processor = new TextProcessor();
    }

    public function extractFromJD(string $jobDescription): array
    {
        $cleaned  = $this->processor->clean($jobDescription);
        $tokens   = $this->processor->tokenize($cleaned);
        $filtered = $this->processor->removeStopwords($tokens);
        $freq     = $this->processor->wordFrequency($filtered);

        $bigrams    = $this->processor->extractNgrams($tokens, 2);
        $bigramFreq = $this->processor->wordFrequency($bigrams);

        $jdStopwords = $this->getJDStopwords();

        $keywords = [];
        foreach ($freq as $word => $count) {
            if (!isset($jdStopwords[$word]) && strlen($word) > 2) {
                $keywords[$word] = $count;
            }
        }
        foreach ($bigramFreq as $bigram => $count) {
            if ($count >= 2) {
                $keywords[$bigram] = $count * 2;
            }
        }

        arsort($keywords);
        return array_keys(array_slice($keywords, 0, 50));
    }

    private function getJDStopwords(): array
    {
        return array_flip([
            'required', 'preferred', 'responsibilities', 'qualifications',
            'ability', 'team', 'work', 'looking', 'role', 'position',
            'company', 'including', 'must', 'strong', 'excellent',
            'good', 'years', 'experience', 'working', 'knowledge',
            'understanding', 'skills', 'etc', 'well', 'related',
            'requirements', 'description', 'candidate', 'ideal',
            'opportunity', 'join', 'apply', 'please', 'submit',
            'resume', 'cover', 'letter', 'salary', 'benefits',
            'equal', 'employer', 'diversity',
        ]);
    }
}
