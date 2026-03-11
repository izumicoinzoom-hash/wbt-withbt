<?php
require_once __DIR__ . '/config.php';

header('Content-Type: application/json; charset=utf-8');

// セッション設定
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_lifetime', (string)SESSION_LIFETIME);
session_name(SESSION_NAME);
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'POSTメソッドのみ受け付けます。']);
    exit;
}

$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '') {
    echo json_encode(['success' => false, 'error' => 'メールアドレスとパスワードを入力してください。']);
    exit;
}

try {
    $pdo  = getPDO();
    $stmt = $pdo->prepare('SELECT id, email, password_hash, subscription_status FROM users WHERE email = :email LIMIT 1');
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        echo json_encode(['success' => false, 'error' => 'メールアドレスまたはパスワードが正しくありません。']);
        exit;
    }

    if (!in_array($user['subscription_status'], ['active', 'trialing'], true)) {
        echo json_encode(['success' => false, 'error' => '有効なサブスクリプションがありません。']);
        exit;
    }

    // セッション固定攻撃対策
    session_regenerate_id(true);

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['email']   = $user['email'];

    // last_login 更新
    $update = $pdo->prepare('UPDATE users SET last_login = NOW() WHERE id = :id');
    $update->execute([':id' => $user['id']]);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'サーバーエラーが発生しました。']);
}
