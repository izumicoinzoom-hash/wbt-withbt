<?php
require_once __DIR__ . '/config.php';

ini_set('session.cookie_samesite', 'Strict');
ini_set('session.cookie_httponly', '1');
session_name(SESSION_NAME);
session_start();

if (empty($_SESSION['user_id'])) {
    header('Location: /login.html');
    exit;
}
