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

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse(['error' => 'Method not allowed'], 405);
}

$page = max(1, (int) ($_GET['page'] ?? 1));
$perPage = min(50, max(1, (int) ($_GET['per_page'] ?? 10)));

try {
    $db = getDB();
    $reportGen = new ReportGenerator($db);
    $result = $reportGen->getByUser(currentUserId(), $page, $perPage);

    jsonResponse(['success' => true, ...$result]);

} catch (Exception $e) {
    error_log('History error: ' . $e->getMessage());
    jsonResponse(['error' => 'Failed to load history'], 500);
}
