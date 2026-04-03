<?php

class ContentValidator
{
    private array $sectionHeaders;

    public function __construct()
    {
        $this->sectionHeaders = require __DIR__ . '/../data/section_headers.php';
    }

    /**
     * Validate that extracted text looks like a resume.
     */
    public function validateResume(string $resumeText): void
    {
        // Check minimum word count
        if (str_word_count($resumeText) < 80) {
            throw new RuntimeException(
                'The uploaded file does not appear to be a resume. It contains too little text.'
            );
        }

        // Check for core resume sections
        $coreSections = ['experience', 'education', 'skills', 'summary', 'projects'];
        $foundCount = 0;
        $lowerText = mb_strtolower($resumeText, 'UTF-8');

        foreach ($coreSections as $section) {
            if (!isset($this->sectionHeaders[$section])) {
                continue;
            }
            foreach ($this->sectionHeaders[$section] as $header) {
                if (mb_stripos($lowerText, $header) !== false) {
                    $foundCount++;
                    break;
                }
            }
        }

        if ($foundCount < 2) {
            throw new RuntimeException(
                'The uploaded file does not appear to be a resume. Please upload a document containing standard resume sections (experience, education, skills, etc.).'
            );
        }
    }

    /**
     * Validate that the job description is a real job listing.
     */
    public function validateJobDescription(string $jdText, array $extractedKeywords): void
    {
        $wordCount = str_word_count($jdText);

        // Must have at least 40 words
        if ($wordCount < 40) {
            throw new RuntimeException(
                'The job description is too short. Please paste a complete job listing (at least 40 words).'
            );
        }

        // Must yield enough meaningful keywords
        if (count($extractedKeywords) < 8) {
            throw new RuntimeException(
                'The job description does not contain enough meaningful content. Please paste a real job listing with role requirements and qualifications.'
            );
        }

        // Must match phrases from at least 3 of 5 distinct job-listing categories
        $lowerJD = mb_strtolower($jdText, 'UTF-8');
        $categories = [
            // Responsibilities / duties
            ['responsibilities', 'responsible for', 'you will', 'your role', 'key duties', 'day to day', 'what you\'ll do'],
            // Requirements / qualifications
            ['requirements', 'qualifications', 'must have', 'required skills', 'what we\'re looking for', 'who you are', 'you should have', 'minimum qualifications'],
            // Skills / tech
            ['proficiency in', 'proficient in', 'experience with', 'experience in', 'knowledge of', 'familiarity with', 'hands-on experience', 'technical skills', 'years of experience'],
            // Education / credentials
            ['bachelor', 'master', 'degree in', 'certification', 'certified', 'diploma', 'graduate', 'computer science', 'engineering'],
            // Employment terms
            ['full-time', 'part-time', 'salary', 'benefits', 'remote', 'hybrid', 'onsite', 'on-site', 'compensation', 'paid time off', 'health insurance', 'we offer', 'nice to have', 'preferred'],
        ];

        $categoriesMatched = 0;
        foreach ($categories as $phrases) {
            foreach ($phrases as $phrase) {
                if (mb_strpos($lowerJD, $phrase) !== false) {
                    $categoriesMatched++;
                    break; // one match per category is enough
                }
            }
        }

        if ($categoriesMatched < 3) {
            throw new RuntimeException(
                'The text does not appear to be a job description. A valid job listing should include sections like responsibilities, requirements, skills, or qualifications. Please paste a real job posting.'
            );
        }
    }
}
