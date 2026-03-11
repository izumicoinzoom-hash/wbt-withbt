<?php
/**
 * WBT セットアップスクリプト
 *
 * 使い方:
 *   1. config.php に DB 情報を入力済みであること
 *   2. ブラウザで https://ドメイン/setup.php にアクセス
 *   3. テーブル作成完了後、このファイルは自動削除されます
 *
 * ⚠️ 本番環境でのみ使用し、完了後は必ず削除されたことを確認してください
 */

// 直接アクセス以外はブロック（CLIからの実行は許可）
if (PHP_SAPI !== 'cli' && !isset($_SERVER['HTTP_HOST'])) {
    exit('Direct access only.');
}

require_once __DIR__ . '/config.php';

// ── セキュリティチェック ──────────────────────────────
// setup.php は一度だけ実行するもの。念のためパスワード保護
$SETUP_PASSWORD = 'wbt-setup-2026'; // 実行前に変更推奨

if (PHP_SAPI !== 'cli') {
    $input_pass = $_POST['setup_pass'] ?? '';
    if ($input_pass !== $SETUP_PASSWORD) {
        showForm();
        exit;
    }
}

// ── テーブル作成 SQL ─────────────────────────────────
$sql = <<<SQL
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    stripe_customer_id VARCHAR(255) DEFAULT NULL,
    subscription_status ENUM('active', 'inactive', 'trialing', 'past_due', 'canceled') NOT NULL DEFAULT 'inactive',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL;

// ── 実行 ────────────────────────────────────────────
$results = [];
$success = true;

try {
    $pdo = getPDO();
    $pdo->exec($sql);
    $results[] = ['status' => 'ok', 'msg' => 'usersテーブル: 作成完了（または既存）'];
} catch (PDOException $e) {
    $results[] = ['status' => 'error', 'msg' => 'usersテーブル作成失敗: ' . $e->getMessage()];
    $success = false;
}

// ── 自己削除 ────────────────────────────────────────
$selfDeleted = false;
if ($success) {
    $selfDeleted = @unlink(__FILE__);
}

// ── 結果表示 ────────────────────────────────────────
if (PHP_SAPI === 'cli') {
    foreach ($results as $r) {
        echo '[' . strtoupper($r['status']) . '] ' . $r['msg'] . PHP_EOL;
    }
    if ($success) {
        echo $selfDeleted ? 'setup.php を削除しました。' : '⚠ setup.php の自動削除に失敗しました。手動で削除してください。';
        echo PHP_EOL;
    }
    exit($success ? 0 : 1);
}

showResult($results, $success, $selfDeleted);

// ────────────────────────────────────────────────────
// HTML出力関数
// ────────────────────────────────────────────────────

function showForm(): void {
    ?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>WBT セットアップ</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-900 text-white min-h-screen flex items-center justify-center p-4">
<div class="w-full max-w-md">
    <div class="bg-slate-800 border border-slate-700 rounded-2xl p-8 shadow-2xl">
        <div class="h-1 bg-orange-500 rounded-full mb-6 -mx-8 -mt-8"></div>
        <h1 class="text-xl font-bold mb-2">WBT セットアップ</h1>
        <p class="text-slate-400 text-sm mb-6">データベースのテーブルを作成します。<br>セットアップパスワードを入力してください。</p>
        <form method="POST">
            <input type="password" name="setup_pass" required placeholder="セットアップパスワード"
                class="w-full bg-slate-900 border border-slate-600 rounded-lg px-4 py-3 text-sm mb-4 focus:outline-none focus:border-orange-500">
            <button type="submit"
                class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 rounded-lg transition">
                セットアップ実行
            </button>
        </form>
        <p class="text-slate-500 text-xs mt-4">⚠️ このスクリプトは実行後に自動削除されます</p>
    </div>
</div>
</body>
</html>
    <?php
}

function showResult(array $results, bool $success, bool $selfDeleted): void {
    ?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>WBT セットアップ結果</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-900 text-white min-h-screen flex items-center justify-center p-4">
<div class="w-full max-w-md">
    <div class="bg-slate-800 border border-slate-700 rounded-2xl p-8 shadow-2xl">
        <div class="h-1 <?= $success ? 'bg-green-500' : 'bg-red-500' ?> rounded-full mb-6 -mx-8 -mt-8"></div>
        <h1 class="text-xl font-bold mb-4">
            <?= $success ? '✅ セットアップ完了' : '❌ セットアップ失敗' ?>
        </h1>
        <ul class="space-y-2 mb-6">
            <?php foreach ($results as $r): ?>
            <li class="flex items-start gap-2 text-sm <?= $r['status'] === 'ok' ? 'text-green-400' : 'text-red-400' ?>">
                <span><?= $r['status'] === 'ok' ? '✓' : '✗' ?></span>
                <span><?= htmlspecialchars($r['msg']) ?></span>
            </li>
            <?php endforeach; ?>
        </ul>

        <?php if ($success): ?>
        <div class="bg-slate-900/50 border border-slate-600 rounded-lg p-4 text-sm text-slate-300 mb-6">
            <?php if ($selfDeleted): ?>
            <p class="text-green-400 font-bold mb-1">🗑️ setup.php を自動削除しました</p>
            <p class="text-slate-400 text-xs">セキュリティ上、このURLには二度とアクセスできません。</p>
            <?php else: ?>
            <p class="text-yellow-400 font-bold mb-1">⚠️ setup.php の自動削除に失敗しました</p>
            <p class="text-slate-400 text-xs">FTPまたはSSHで <code>public_html/setup.php</code> を手動削除してください。</p>
            <?php endif; ?>
        </div>
        <div class="space-y-3">
            <p class="text-slate-400 text-sm font-bold">次のステップ:</p>
            <ol class="text-slate-400 text-sm space-y-1 list-decimal list-inside">
                <li><code class="text-orange-400">stripe/config.php</code> に Stripe キーを入力</li>
                <li>Stripe ダッシュボードで Webhook を登録</li>
                <li><code class="text-orange-400">login.html</code> のフォーム送信先を <code class="text-orange-400">login.php</code> に変更</li>
            </ol>
        </div>
        <a href="login.html" class="mt-6 block text-center bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 rounded-lg transition">
            ログインページへ
        </a>
        <?php else: ?>
        <div class="bg-red-900/30 border border-red-700 rounded-lg p-4 text-sm text-red-300 mb-6">
            <p class="font-bold mb-1">よくある原因:</p>
            <ul class="list-disc list-inside space-y-1 text-xs">
                <li>config.php の DB 情報が間違っている</li>
                <li>Xserver でデータベースとユーザーが作成されていない</li>
                <li>ユーザーに適切な権限が付与されていない</li>
            </ul>
        </div>
        <a href="setup.php" class="block text-center bg-slate-700 hover:bg-slate-600 text-white font-bold py-3 rounded-lg transition">
            再試行
        </a>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
    <?php
}
