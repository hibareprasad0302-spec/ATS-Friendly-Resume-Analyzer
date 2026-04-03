<?php

class ScoringEngine
{
    private array $weights;

    public function __construct()
    {
        $this->weights = SCORE_WEIGHTS;
    }

    public function calculate(
        array $keywordResult,
        array $skillResult,
        array $sectionResult,
        array $projectResult,
        array $experienceResult,
        array $educationResult,
        array $formattingResult
    ): array {
        $scores = [
            'keyword'    => $this->scoreKeywords($keywordResult),
            'skills'     => $this->scoreSkills($skillResult),
            'sections'   => $this->scoreSections($sectionResult),
            'projects'   => $this->scoreProjects($projectResult),
            'experience' => $this->scoreExperience($experienceResult),
            'education'  => $this->scoreEducation($educationResult),
            'formatting' => $this->scoreFormatting($formattingResult),
        ];

        $scores['total'] = round(array_sum($scores), 2);

        $scores['maximums'] = $this->weights;
        $scores['maximums']['total'] = 100;

        return $scores;
    }

    private function scoreKeywords(array $result): float
    {
        $max = $this->weights['keyword']; // 30
        $percentage = ($result['percentage'] ?? 0) / 100;

        $base = $percentage * ($max - 5); // Up to 25
        $bonus = $percentage >= 0.8 ? 5 : ($percentage >= 0.6 ? 3 : 0);

        $score = $base + $bonus;

        if (count($result['matched'] ?? []) > 0 && $score < 2) {
            $score = 2;
        }

        return round(min($score, $max), 2);
    }

    private function scoreSkills(array $result): float
    {
        $max = $this->weights['skills']; // 20

        $totalJdSkills = count($result['matched'] ?? []) + count($result['missing'] ?? []);
        if ($totalJdSkills === 0) {
            return round($max * 0.75, 2);
        }

        $percentage = count($result['matched']) / $totalJdSkills;
        return round($percentage * $max, 2);
    }

    private function scoreSections(array $result): float
    {
        $max = $this->weights['sections']; // 15
        $score = 0;

        $coreSections = ['experience', 'education', 'skills', 'contact'];
        $optionalSections = ['projects', 'certifications', 'summary'];

        $detected = $result['detected'] ?? [];

        foreach ($coreSections as $s) {
            if (in_array($s, $detected)) {
                $score += 3;
            }
        }
        foreach ($optionalSections as $s) {
            if (in_array($s, $detected)) {
                $score += 1;
            }
        }

        return round(min($score, $max), 2);
    }

    private function scoreProjects(array $result): float
    {
        $max = $this->weights['projects']; // 10
        $score = 0;

        if ($result['has_section'] ?? false) {
            $score += 3;
        }

        $projectCount = min($result['project_count'] ?? 0, 3);
        $score += $projectCount * 1.5;

        if ($result['has_tech_mentions'] ?? false) {
            $score += 2.5;
        }

        return round(min($score, $max), 2);
    }

    private function scoreExperience(array $result): float
    {
        $max = $this->weights['experience']; // 10
        $score = 0;

        if ($result['has_section'] ?? false) {
            $score += 2;
        }

        $actionVerbs = min($result['action_verbs_count'] ?? 0, 6);
        $score += ($actionVerbs / 6) * 3;

        $quantifiable = min($result['quantifiable_count'] ?? 0, 4);
        $score += ($quantifiable / 4) * 3;

        $positions = min($result['positions_count'] ?? 0, 3);
        $score += ($positions / 3) * 2;

        return round(min($score, $max), 2);
    }

    private function scoreEducation(array $result): float
    {
        $max = $this->weights['education']; // 5
        $score = 0;

        if ($result['has_section'] ?? false) {
            $score += 1;
        }
        if ($result['has_degree'] ?? false) {
            $score += 2;
        }
        if ($result['has_institution'] ?? false) {
            $score += 1;
        }
        if ($result['has_graduation_date'] ?? false) {
            $score += 1;
        }

        return round(min($score, $max), 2);
    }

    private function scoreFormatting(array $result): float
    {
        $max = $this->weights['formatting']; // 10
        $score = 0;

        // Word count (300-1500 words)
        $wordCount = $result['word_count'] ?? 0;
        if ($wordCount >= 300 && $wordCount <= 1500) {
            $score += 3;
        } elseif ($wordCount >= 150 && $wordCount <= 2000) {
            $score += 1.5;
        }

        // Consistent structure
        if ($result['has_consistent_structure'] ?? false) {
            $score += 2;
        }

        // No excessive special characters
        $specialRatio = $result['special_char_ratio'] ?? 0;
        if ($specialRatio < 0.02) {
            $score += 2;
        } elseif ($specialRatio < 0.05) {
            $score += 1;
        }

        // Contact information
        if ($result['has_email'] ?? false) {
            $score += 1;
        }
        if ($result['has_phone'] ?? false) {
            $score += 1;
        }

        // Clean line breaks
        if ($result['has_clean_linebreaks'] ?? false) {
            $score += 1;
        }

        return round(min($score, $max), 2);
    }
}
