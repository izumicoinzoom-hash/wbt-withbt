<?php
/**
 * 管理者API
 * - GET    ?action=list           → 全ユーザー一覧
 * - POST   action=create          → ユーザー作成
 * - POST   action=update          → ユーザー更新（名前・メール・レッスン）
 * - POST   action=reset_password  → パスワードリセット
 * - POST   action=delete          → ユーザー削除
 * - POST   action=advance_all     → 全生徒のレッスンを+1
 */

require_once __DIR__ . '/../config.php';

header('Content-Type: application/json; charset=utf-8');

ini_set('session.cookie_samesite', 'Strict');
ini_set('session.cookie_httponly', '1');
session_name(SESSION_NAME);
session_start();

// 管理者チェック
if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'ログインしてください。']);
    exit;
}

try {
    $pdo = getPDO();

    // 管理者権限チェック
    $stmt = $pdo->prepare('SELECT is_admin FROM users WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $me = $stmt->fetch();
    if (!$me || !$me['is_admin']) {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => '管理者権限がありません。']);
        exit;
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'DB接続エラー']);
    exit;
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

try {
    switch ($action) {

        // ── 一覧 ──
        case 'list':
            $rows = $pdo->query('SELECT id, name, email, current_lesson, is_admin, subscription_status, created_at, last_login FROM users ORDER BY id')->fetchAll();
            echo json_encode(['success' => true, 'users' => $rows]);
            break;

        // ── 作成 ──
        case 'create':
            $name  = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $pass  = $_POST['password'] ?? '';
            $lesson = (int)($_POST['current_lesson'] ?? 1);

            if ($email === '' || $pass === '') {
                echo json_encode(['success' => false, 'error' => 'メールとパスワードは必須です。']);
                break;
            }

            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $ins = $pdo->prepare('INSERT INTO users (name, email, password_hash, current_lesson, subscription_status) VALUES (?, ?, ?, ?, ?)');
            $ins->execute([$name, $email, $hash, $lesson, 'active']);
            echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
            break;

        // ── 更新 ──
        case 'update':
            $id     = (int)($_POST['id'] ?? 0);
            $name   = trim($_POST['name'] ?? '');
            $email  = trim($_POST['email'] ?? '');
            $lesson = (int)($_POST['current_lesson'] ?? 1);
            $status = $_POST['subscription_status'] ?? 'active';

            if ($id === 0) {
                echo json_encode(['success' => false, 'error' => 'IDが必要です。']);
                break;
            }

            $upd = $pdo->prepare('UPDATE users SET name = ?, email = ?, current_lesson = ?, subscription_status = ? WHERE id = ?');
            $upd->execute([$name, $email, $lesson, $status, $id]);
            echo json_encode(['success' => true]);
            break;

        // ── パスワードリセット ──
        case 'reset_password':
            $id   = (int)($_POST['id'] ?? 0);
            $pass = $_POST['password'] ?? '';

            if ($id === 0 || $pass === '') {
                echo json_encode(['success' => false, 'error' => 'IDとパスワードが必要です。']);
                break;
            }

            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $upd = $pdo->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
            $upd->execute([$hash, $id]);
            echo json_encode(['success' => true]);
            break;

        // ── 削除 ──
        case 'delete':
            $id = (int)($_POST['id'] ?? 0);
            if ($id === 0) {
                echo json_encode(['success' => false, 'error' => 'IDが必要です。']);
                break;
            }
            // 自分自身は削除不可
            if ($id === (int)$_SESSION['user_id']) {
                echo json_encode(['success' => false, 'error' => '自分自身は削除できません。']);
                break;
            }
            $del = $pdo->prepare('DELETE FROM users WHERE id = ?');
            $del->execute([$id]);
            echo json_encode(['success' => true]);
            break;

        // ── 全生徒のレッスンを+1 ──
        case 'advance_all':
            $pdo->exec('UPDATE users SET current_lesson = LEAST(current_lesson + 1, 24) WHERE is_admin = 0');
            echo json_encode(['success' => true]);
            break;

        default:
            echo json_encode(['success' => false, 'error' => '不明なアクション: ' . $action]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    $msg = $e->getMessage();
    if (strpos($msg, 'Duplicate entry') !== false) {
        echo json_encode(['success' => false, 'error' => 'そのメールアドレスは既に登録されています。']);
    } else {
        echo json_encode(['success' => false, 'error' => 'DBエラー: ' . $msg]);
    }
}
