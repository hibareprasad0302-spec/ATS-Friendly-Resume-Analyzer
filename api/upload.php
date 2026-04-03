<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    jsonResponse(['error' => 'Authentication required'], 401);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['error' => 'Method not allowed'], 405);
}

$uploader = new FileUploader();
$result = $uploader->upload($_FILES['resume'] ?? []);

if ($result === false) {
    jsonResponse(['error' => 'Upload failed', 'details' => $uploader->getErrors()], 422);
}

$_SESSION['pending_upload'] = $result;

jsonResponse(['success' => true, 'data' => $result]);
