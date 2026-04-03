<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['error' => 'Method not allowed'], 405);
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    jsonResponse(['error' => 'Email and password are required.'], 422);
}

try {
    $db = getDB();
    $userModel = new User($db);
    $user = $userModel->authenticate($email, $password);

    if (!$user) {
        jsonResponse(['error' => 'Invalid email or password.'], 401);
    }

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['full_name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['role'] = $user['role'];

    $redirect = $_POST['redirect'] ?? '/analyzer.php';
    jsonResponse(['success' => true, 'redirect' => $redirect, 'user' => $user]);

} catch (Exception $e) {
    error_log('Login error: ' . $e->getMessage());
    jsonResponse(['error' => 'An error occurred. Please try again.'], 500);
}
