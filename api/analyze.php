<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    jsonResponse(['error' => 'Authentication required'], 401);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['error' => 'Method not allowed'], 405);
}

$jobDescription = trim($_POST['job_description'] ?? '');
$jobRole = trim($_POST['job_role'] ?? '');
$uploadData = $_SESSION['pending_upload'] ?? null;

if (!$uploadData || empty($jobDescription)) {
    jsonResponse(['error' => 'Missing upload data or job description'], 422);
}

if (mb_strlen($jobDescription) < 50) {
    jsonResponse(['error' => 'Job description too short (minimum 50 characters)'], 422);
}

try {
    $db = getDB();
    $filePath = UPLOAD_DIR . $uploadData['stored_filename'];

    // Step 1: Extract text
    $extractor = new TextExtractor();
    $resumeText = $extractor->extract($filePath, $uploadData['file_type']);

    // Step 2: Extract JD keywords
    $kwExtractor = new KeywordExtractor();
    $jdKeywords = $kwExtractor->extractFromJD($jobDescription);

    // Step 2.5: Validate content quality
    $validator = new ContentValidator();
    $validator->validateResume($resumeText);
    $validator->validateJobDescription($jobDescription, $jdKeywords);

    // Step 3: Run analysis
    $analyzer = new ResumeAnalyzer($db);
    $keywordResult    = $analyzer->matchKeywords($jdKeywords, $resumeText);
    $skillResult      = $analyzer->matchSkills($jobDescription, $resumeText);
    $sectionResult    = $analyzer->detectSections($resumeText);
    $experienceResult = $analyzer->analyzeExperience($resumeText);
    $educationResult  = $analyzer->analyzeEducation($resumeText);
    $projectResult    = $analyzer->analyzeProjects($resumeText);
    $formattingResult = $analyzer->analyzeFormatting($resumeText);

    // Step 4: Calculate scores
    $scorer = new ScoringEngine();
    $scores = $scorer->calculate(
        $keywordResult, $skillResult, $sectionResult,
        $projectResult, $experienceResult, $educationResult,
        $formattingResult
    );

    // Step 5: Generate suggestions
    $suggestor = new SuggestionsEngine();
    $suggestions = $suggestor->generate($scores, $keywordResult, $skillResult, $sectionResult);

    // Step 6: Save report
    $reportGen = new ReportGenerator($db);
    $reportId = $reportGen->save([
        'user_id'          => currentUserId(),
        'original_filename' => $uploadData['original_filename'],
        'stored_filename'  => $uploadData['stored_filename'],
        'file_type'        => $uploadData['file_type'],
        'job_description'  => $jobDescription,
        'job_role'         => $jobRole,
        'extracted_text'   => $resumeText,
        'scores'           => $scores,
        'keyword_result'   => $keywordResult,
        'skill_result'     => $skillResult,
        'section_result'   => $sectionResult,
        'suggestions'      => $suggestions,
    ]);

    // Store report ID in session for anonymous access
    $_SESSION['last_report_id'] = $reportId;
    unset($_SESSION['pending_upload']);

    jsonResponse(['success' => true, 'report_id' => $reportId]);

} catch (RuntimeException $e) {
    jsonResponse(['error' => $e->getMessage()], 422);
} catch (Exception $e) {
    error_log('Analysis error: ' . $e->getMessage());
    jsonResponse(['error' => 'An internal error occurred during analysis.'], 500);
}
