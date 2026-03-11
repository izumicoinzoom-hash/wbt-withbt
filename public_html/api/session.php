<?php
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json; charset=utf-8');

ini_set('session.cookie_samesite', 'Strict');
ini_set('session.cookie_httponly', '1');
session_name(SESSION_NAME);
session_start();

if (!empty($_SESSION['user_id'])) {
    echo json_encode([
        'loggedIn' => true,
        'email'    => $_SESSION['email'],
    ]);
} else {
    echo json_encode([
        'loggedIn' => false,
    ]);
}
