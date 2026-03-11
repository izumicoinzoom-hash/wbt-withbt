<?php
require_once __DIR__ . '/config.php';

ini_set('session.cookie_samesite', 'Strict');
ini_set('session.cookie_httponly', '1');
session_name(SESSION_NAME);
session_start();

// セッション変数をクリア
$_SESSION = [];

// セッションCookieを削除
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

session_destroy();

header('Location: /login.html');
exit;
