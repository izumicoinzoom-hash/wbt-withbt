<?php
/**
 * ユーザー進捗API
 * GET → { loggedIn: true, email, name, currentLesson }
 */
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json; charset=utf-8');

ini_set('session.cookie_samesite', 'Strict');
ini_set('session.cookie_httponly', '1');
session_name(SESSION_NAME);
session_start();

if (empty($_SESSION['user_id'])) {
    echo json_encode(['loggedIn' => false]);
    exit;
}

try {
    $pdo  = getPDO();
    $stmt = $pdo->prepare('SELECT name, email, current_lesson, is_admin FROM users WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    if (!$user) {
        echo json_encode(['loggedIn' => false]);
        exit;
    }

    echo json_encode([
        'loggedIn'       => true,
        'name'           => $user['name'],
        'email'          => $user['email'],
        'currentLesson'  => (int)$user['current_lesson'],
        'isAdmin'        => (bool)$user['is_admin'],
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['loggedIn' => false, 'error' => 'DBエラー']);
}
