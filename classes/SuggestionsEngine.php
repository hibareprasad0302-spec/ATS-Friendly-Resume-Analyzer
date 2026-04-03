<?php

class SuggestionsEngine
{
    public function generate(
        array $scores,
        array $keywordResult,
        array $skillResult,
        array $sectionResult
    ): array {
        $suggestions = [];

        // Keyword suggestions
        $kwPercent = $keywordResult['percentage'] ?? 0;
        if ($kwPercent < 50) {
            $topMissing = array_slice($keywordResult['missing'] ?? [], 0, 10);
            $suggestions[] = [
                'category' => 'keywords',
                'priority' => 'high',
                'message'  => 'Your resume matches less than 50% of the job description keywords. Consider incorporating these missing terms: ' . implode(', ', $topMissing) . '.',
            ];
        } elseif ($kwPercent < 75) {
            $topMissing = array_slice($keywordResult['missing'] ?? [], 0, 5);
            $suggestions[] = [
                'category' => 'keywords',
                'priority' => 'medium',
                'message'  => 'Good keyword coverage, but you could strengthen it by adding: ' . implode(', ', $topMissing) . '.',
            ];
        }

        // Skill suggestions
        $missingSkills = $skillResult['missing'] ?? [];
        if (count($missingSkills) > 0) {
            $suggestions[] = [
                'category' => 'skills',
                'priority' => count($missingSkills) > 3 ? 'high' : 'medium',
                'message'  => 'The job requires these skills not found in your resume: ' . implode(', ', $missingSkills) . '. Add them to your skills section if you possess them.',
            ];
        }

        // Section suggestions
        $coreSections = ['experience', 'education', 'skills'];
        foreach ($sectionResult['missing'] ?? [] as $section) {
            $priority = in_array($section, $coreSections) ? 'high' : 'medium';
            $suggestions[] = [
                'category' => 'sections',
                'priority' => $priority,
                'message'  => "Your resume is missing a '" . ucfirst($section) . "' section. ATS systems expect this section to be clearly labeled.",
            ];
        }

        // Experience suggestions
        $expMax = $scores['maximums']['experience'] ?? 10;
        if (($scores['experience'] ?? 0) < $expMax * 0.5) {
            $suggestions[] = [
                'category' => 'experience',
                'priority' => 'medium',
                'message'  => 'Strengthen your experience section with action verbs (led, developed, implemented, optimized) and quantifiable achievements (increased revenue by 20%, reduced load time by 40%).',
            ];
        }

        // Formatting suggestions
        $fmtMax = $scores['maximums']['formatting'] ?? 10;
        if (($scores['formatting'] ?? 0) < $fmtMax * 0.5) {
            $suggestions[] = [
                'category' => 'formatting',
                'priority' => 'medium',
                'message'  => 'Improve your resume formatting: use clear section headers, maintain consistent bullet points, and keep length between 1-2 pages (300-1500 words).',
            ];
        }

        // Education suggestions
        $eduMax = $scores['maximums']['education'] ?? 5;
        if (($scores['education'] ?? 0) < $eduMax * 0.5) {
            $suggestions[] = [
                'category' => 'education',
                'priority' => 'low',
                'message'  => 'Ensure your education section includes your degree name, institution, and graduation date.',
            ];
        }

        // Projects suggestions
        $projMax = $scores['maximums']['projects'] ?? 10;
        if (($scores['projects'] ?? 0) < $projMax * 0.5) {
            $suggestions[] = [
                'category' => 'projects',
                'priority' => 'low',
                'message'  => 'Add a Projects section with 2-3 relevant projects. Include the technologies used and a brief description of each.',
            ];
        }

        // Overall score suggestion
        $total = $scores['total'] ?? 0;
        if ($total >= 70) {
            $suggestions[] = [
                'category' => 'overall',
                'priority' => 'low',
                'message'  => 'Great job! Your resume has strong ATS compatibility. Focus on the specific gaps above to push your score even higher.',
            ];
        } elseif ($total < 40) {
            array_unshift($suggestions, [
                'category' => 'overall',
                'priority' => 'high',
                'message'  => 'Your resume needs significant improvements for ATS compatibility. Focus on adding missing keywords, skills, and ensuring all major sections are present.',
            ]);
        }

        // Sort by priority
        usort($suggestions, fn($a, $b) =>
            ['high' => 0, 'medium' => 1, 'low' => 2][$a['priority']]
            <=> ['high' => 0, 'medium' => 1, 'low' => 2][$b['priority']]
        );

        return $suggestions;
    }
}
