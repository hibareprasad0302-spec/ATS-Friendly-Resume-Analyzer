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

$fullName = trim($_POST['full_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$passwordConfirm = $_POST['password_confirm'] ?? '';

if (empty($fullName) || empty($email) || empty($password)) {
    jsonResponse(['error' => 'All fields are required.'], 422);
}

if ($password !== $passwordConfirm) {
    jsonResponse(['error' => 'Passwords do not match.'], 422);
}

try {
    $db = getDB();
    $userModel = new User($db);
    $userId = $userModel->register($fullName, $email, $password);

    // Auto-login after registration
    $user = $userModel->getById($userId);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['full_name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['role'] = $user['role'];

    jsonResponse(['success' => true, 'redirect' => '/analyzer.php']);

} catch (InvalidArgumentException $e) {
    jsonResponse(['error' => $e->getMessage()], 422);
} catch (RuntimeException $e) {
    jsonResponse(['error' => $e->getMessage()], 409);
} catch (Exception $e) {
    error_log('Registration error: ' . $e->getMessage());
    jsonResponse(['error' => 'An error occurred. Please try again.'], 500);
}
