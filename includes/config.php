<?php
if (basename($_SERVER['PHP_SELF']) === 'config.php') {
    http_response_code(403);
    exit('Direct access forbidden.');
}

// Database configuration
define('DB_HOST', '127.0.0.1');
define('DB_PORT', 3306);
define('DB_NAME', 'ats_resume_analyzer');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Application constants
define('APP_NAME', 'ATS Resume Analyzer');
define('APP_URL', 'http://localhost:8080');
define('BASE_PATH', __DIR__ . '/..');

// Upload configuration
define('UPLOAD_DIR', BASE_PATH . '/uploads/');
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10 MB
define('ALLOWED_EXTENSIONS', ['pdf', 'docx']);
define('ALLOWED_MIME_TYPES', [
    'application/pdf',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
]);

// Scoring weights (out of 100)
define('SCORE_WEIGHTS', [
    'keyword'    => 30,
    'skills'     => 20,
    'sections'   => 15,
    'projects'   => 10,
    'experience' => 10,
    'education'  => 5,
    'formatting' => 10,
]);

// Required resume sections
define('REQUIRED_SECTIONS', [
    'education', 'experience', 'skills', 'projects',
    'certifications', 'summary', 'contact'
]);

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('log_errors', '1');
