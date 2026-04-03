<?php

class ResumeAnalyzer
{
    private TextProcessor $processor;
    private PDO $db;
    private array $sectionHeaders;

    public function __construct(PDO $db)
    {
        $this->processor = new TextProcessor();
        $this->db = $db;
        $this->sectionHeaders = require __DIR__ . '/../data/section_headers.php';
    }

    public function matchKeywords(array $jdKeywords, string $resumeText): array
    {
        $cleanedResume = $this->processor->clean($resumeText);
        $matched = [];
        $missing = [];

        foreach ($jdKeywords as $keyword) {
            if ($this->keywordExistsInText($keyword, $cleanedResume)) {
                $matched[] = $keyword;
            } else {
                $missing[] = $keyword;
            }
        }

        $total = count($jdKeywords);
        $percentage = $total > 0 ? (count($matched) / $total) * 100 : 0;

        return [
            'matched'    => $matched,
            'missing'    => $missing,
            'percentage' => round($percentage, 2),
        ];
    }

    public function matchSkills(string $jdText, string $resumeText): array
    {
        $stmt = $this->db->query('SELECT skill_name, aliases, category FROM skills_master');
        $allSkills = $stmt->fetchAll();

        $jdClean = $this->processor->clean($jdText);
        $jdSkills = [];

        foreach ($allSkills as $skill) {
            $names = [$skill['skill_name']];
            if ($skill['aliases']) {
                $decoded = json_decode($skill['aliases'], true);
                if (is_array($decoded)) {
                    $names = array_merge($names, $decoded);
                }
            }
            foreach ($names as $name) {
                if ($this->keywordExistsInText($name, $jdClean)) {
                    $jdSkills[] = [
                        'name'     => $skill['skill_name'],
                        'category' => $skill['category'],
                        'aliases'  => $names,
                    ];
                    break;
                }
            }
        }

        $resumeClean = $this->processor->clean($resumeText);
        $matched = [];
        $missing = [];

        foreach ($jdSkills as $skill) {
            $found = false;
            foreach ($skill['aliases'] as $alias) {
                if ($this->keywordExistsInText($alias, $resumeClean)) {
                    $found = true;
                    break;
                }
            }
            if ($found) {
                $matched[] = $skill['name'];
            } else {
                $missing[] = $skill['name'];
            }
        }

        $total = count($jdSkills);
        $percentage = $total > 0 ? (count($matched) / $total) * 100 : 0;

        return [
            'matched'    => $matched,
            'missing'    => $missing,
            'percentage' => round($percentage, 2),
        ];
    }

    public function detectSections(string $resumeText): array
    {
        $cleanedText = mb_strtolower($resumeText, 'UTF-8');
        $detected = [];
        $missing  = [];

        foreach ($this->sectionHeaders as $sectionKey => $headers) {
            $found = false;
            foreach ($headers as $header) {
                $pattern = '/(?:^|\n)\s*' . preg_quote($header, '/') . '\s*[:\-–]?\s*(?:\n|$)/i';
                if (preg_match($pattern, $cleanedText)) {
                    $found = true;
                    break;
                }
            }

            // Fallback: check if the section keyword appears anywhere
            if (!$found) {
                foreach ($headers as $header) {
                    if (mb_stripos($cleanedText, $header) !== false) {
                        $found = true;
                        break;
                    }
                }
            }

            if ($found) {
                $detected[] = $sectionKey;
            } else {
                $missing[] = $sectionKey;
            }
        }

        return ['detected' => $detected, 'missing' => $missing];
    }

    public function analyzeExperience(string $resumeText): array
    {
        $text = mb_strtolower($resumeText, 'UTF-8');
        $result = [
            'has_section' => false,
            'action_verbs_count' => 0,
            'quantifiable_count' => 0,
            'positions_count' => 0,
        ];

        // Check if experience section exists
        $expHeaders = $this->sectionHeaders['experience'] ?? [];
        foreach ($expHeaders as $header) {
            if (mb_stripos($text, $header) !== false) {
                $result['has_section'] = true;
                break;
            }
        }

        // Count action verbs
        $actionVerbs = [
            'led', 'developed', 'managed', 'implemented', 'designed',
            'built', 'created', 'improved', 'increased', 'reduced',
            'optimized', 'launched', 'delivered', 'architected', 'mentored',
            'coordinated', 'established', 'streamlined', 'automated',
            'collaborated', 'maintained', 'resolved', 'analyzed',
            'engineered', 'deployed', 'integrated', 'migrated',
            'refactored', 'spearheaded', 'initiated', 'executed',
        ];

        foreach ($actionVerbs as $verb) {
            if (preg_match_all('/\b' . preg_quote($verb, '/') . '\b/i', $text)) {
                $result['action_verbs_count']++;
            }
        }

        // Count quantifiable achievements (numbers, percentages)
        $result['quantifiable_count'] = preg_match_all('/\d+\s*%|\$\s*[\d,]+|\d+\s*(?:users|clients|projects|teams|members|employees)/i', $text);

        // Count positions (look for date patterns that indicate separate roles)
        $result['positions_count'] = preg_match_all('/(?:20\d{2}|19\d{2})\s*[-–]\s*(?:20\d{2}|19\d{2}|present|current)/i', $text);
        if ($result['positions_count'] === 0) {
            $result['positions_count'] = preg_match_all('/(?:jan|feb|mar|apr|may|jun|jul|aug|sep|oct|nov|dec)[a-z]*\s+\d{4}\s*[-–]/i', $text);
        }

        return $result;
    }

    public function analyzeEducation(string $resumeText): array
    {
        $text = mb_strtolower($resumeText, 'UTF-8');
        $result = [
            'has_section' => false,
            'has_degree' => false,
            'has_institution' => false,
            'has_graduation_date' => false,
        ];

        $eduHeaders = $this->sectionHeaders['education'] ?? [];
        foreach ($eduHeaders as $header) {
            if (mb_stripos($text, $header) !== false) {
                $result['has_section'] = true;
                break;
            }
        }

        // Check for degree mentions
        $degrees = [
            'bachelor', 'master', 'phd', 'doctorate', 'associate',
            'b\.s\.', 'b\.a\.', 'm\.s\.', 'm\.a\.', 'mba', 'b\.tech',
            'm\.tech', 'b\.e\.', 'm\.e\.', 'bsc', 'msc', 'bca', 'mca',
            'diploma', 'degree', 'engineering', 'computer science',
        ];
        foreach ($degrees as $degree) {
            if (preg_match('/\b' . $degree . '\b/i', $text)) {
                $result['has_degree'] = true;
                break;
            }
        }

        // Check for institution (university/college/institute)
        $institutions = ['university', 'college', 'institute', 'school', 'academy'];
        foreach ($institutions as $inst) {
            if (mb_stripos($text, $inst) !== false) {
                $result['has_institution'] = true;
                break;
            }
        }

        // Check for graduation date
        if (preg_match('/(?:20\d{2}|19\d{2})/i', $text)) {
            $result['has_graduation_date'] = true;
        }

        return $result;
    }

    public function analyzeProjects(string $resumeText): array
    {
        $text = mb_strtolower($resumeText, 'UTF-8');
        $result = [
            'has_section' => false,
            'project_count' => 0,
            'has_tech_mentions' => false,
        ];

        $projHeaders = $this->sectionHeaders['projects'] ?? [];
        foreach ($projHeaders as $header) {
            if (mb_stripos($text, $header) !== false) {
                $result['has_section'] = true;
                break;
            }
        }

        // Count projects by looking for bullet points or numbered items after project headers
        $result['project_count'] = max(
            preg_match_all('/(?:^|\n)\s*(?:[-•*]|\d+[.\)])\s*[A-Z]/m', $resumeText),
            preg_match_all('/(?:project\s*(?:name|title)?:\s*)/i', $text),
            1 * (int)$result['has_section']
        );
        $result['project_count'] = min($result['project_count'], 10);

        // Check for technology mentions in projects
        $techTerms = ['built with', 'using', 'technologies', 'tech stack', 'developed using', 'implemented in'];
        foreach ($techTerms as $term) {
            if (mb_stripos($text, $term) !== false) {
                $result['has_tech_mentions'] = true;
                break;
            }
        }

        return $result;
    }

    public function analyzeFormatting(string $resumeText): array
    {
        $result = [
            'word_count' => 0,
            'has_email' => false,
            'has_phone' => false,
            'has_consistent_structure' => false,
            'has_clean_linebreaks' => false,
            'special_char_ratio' => 0.0,
        ];

        $words = preg_split('/\s+/', trim($resumeText));
        $result['word_count'] = count($words);

        // Check for email
        $result['has_email'] = (bool) preg_match('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', $resumeText);

        // Check for phone number
        $result['has_phone'] = (bool) preg_match('/(?:\+?\d{1,3}[-.\s]?)?\(?\d{3}\)?[-.\s]?\d{3}[-.\s]?\d{4}/', $resumeText);

        // Check structure consistency (has multiple sections with proper formatting)
        $sectionCount = 0;
        foreach ($this->sectionHeaders as $headers) {
            foreach ($headers as $header) {
                if (mb_stripos($resumeText, $header) !== false) {
                    $sectionCount++;
                    break;
                }
            }
        }
        $result['has_consistent_structure'] = $sectionCount >= 3;

        // Check for clean line breaks
        $lines = explode("\n", $resumeText);
        $nonEmptyLines = array_filter($lines, fn($l) => trim($l) !== '');
        $avgLineLength = count($nonEmptyLines) > 0
            ? array_sum(array_map('strlen', $nonEmptyLines)) / count($nonEmptyLines)
            : 0;
        $result['has_clean_linebreaks'] = $avgLineLength < 200 && count($nonEmptyLines) > 10;

        // Special character ratio
        $totalChars = strlen($resumeText);
        if ($totalChars > 0) {
            $specialChars = preg_match_all('/[^a-zA-Z0-9\s\.\,\;\:\-\(\)\/\@\+\#]/', $resumeText);
            $result['special_char_ratio'] = round($specialChars / $totalChars, 4);
        }

        return $result;
    }

    private function keywordExistsInText(string $keyword, string $text): bool
    {
        $escaped = preg_quote($keyword, '/');
        return (bool) preg_match('/\b' . $escaped . '\b/i', $text);
    }
}
