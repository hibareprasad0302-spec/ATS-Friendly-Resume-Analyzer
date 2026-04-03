<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse(['error' => 'Method not allowed'], 405);
}

$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) {
    jsonResponse(['error' => 'Invalid report ID'], 400);
}

try {
    $db = getDB();
    $reportGen = new ReportGenerator($db);

    // If logged in, scope to user; otherwise allow session-based access
    if (isLoggedIn()) {
        $report = $reportGen->getById($id, currentUserId());
        // Admin can see all reports
        if (!$report && isAdmin()) {
            $report = $reportGen->getById($id);
        }
    } else {
        // Allow anonymous access to last report via session
        $lastReportId = $_SESSION['last_report_id'] ?? null;
        if ($lastReportId === $id) {
            $report = $reportGen->getById($id);
        } else {
            $report = null;
        }
    }

    if (!$report) {
        jsonResponse(['error' => 'Report not found'], 404);
    }

    // Remove extracted text from response (too large)
    unset($report['extracted_text']);
    unset($report['job_description']);
    unset($report['full_report']);

    jsonResponse(['success' => true, 'report' => $report]);

} catch (Exception $e) {
    error_log('Report fetch error: ' . $e->getMessage());
    jsonResponse(['error' => 'Failed to load report'], 500);
}
