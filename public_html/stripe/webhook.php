<?php
/**
 * Stripe Webhook ハンドラ
 * 処理イベント:
 *   - checkout.session.completed  → ユーザー作成・パスワード送信
 *   - customer.subscription.updated → ステータス更新
 *   - customer.subscription.deleted → ステータス更新
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/../config.php'; // getPDO()

// --- Webhookシグネチャ検証 ---
$payload   = file_get_contents('php://input');
$sigHeader = isset($_SERVER['HTTP_STRIPE_SIGNATURE']) ? $_SERVER['HTTP_STRIPE_SIGNATURE'] : '';

if (!verifyStripeSignature($payload, $sigHeader, STRIPE_WEBHOOK_SECRET)) {
    http_response_code(400);
    echo 'Invalid signature';
    exit;
}

$event = json_decode($payload, true);
if (!$event || empty($event['type'])) {
    http_response_code(400);
    echo 'Invalid payload';
    exit;
}

http_response_code(200);
header('Content-Type: application/json');

switch ($event['type']) {
    case 'checkout.session.completed':
        handleCheckoutCompleted($event['data']['object']);
        break;
    case 'customer.subscription.updated':
        handleSubscriptionUpdated($event['data']['object']);
        break;
    case 'customer.subscription.deleted':
        handleSubscriptionDeleted($event['data']['object']);
        break;
}

echo json_encode(['received' => true]);
exit;

// ============================================================
// イベントハンドラ
// ============================================================

/**
 * checkout.session.completed
 * 新規ユーザーを作成し、ランダムパスワードをメール送信
 */
function handleCheckoutCompleted(array $session): void
{
    $email      = isset($session['customer_email']) ? $session['customer_email'] : '';
    $customerId = isset($session['customer'])       ? $session['customer']       : '';

    if (!$email) {
        error_log('[Stripe Webhook] checkout.session.completed: customer_email is empty');
        return;
    }

    $pdo = getPDO();

    // 既存ユーザーチェック
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        // 既存ユーザーの場合はステータスを有効化
        $stmt = $pdo->prepare('UPDATE users SET stripe_customer_id = ?, subscription_status = ? WHERE email = ?');
        $stmt->execute([$customerId, 'active', $email]);
        return;
    }

    // ランダムパスワード生成（12文字）
    $password     = generateRandomPassword(12);
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // ユーザー作成
    $stmt = $pdo->prepare(
        'INSERT INTO users (email, password_hash, stripe_customer_id, subscription_status, created_at)
         VALUES (?, ?, ?, ?, NOW())'
    );
    $stmt->execute([$email, $passwordHash, $customerId, 'active']);

    // パスワードをメール送信
    sendPasswordEmail($email, $password);
}

/**
 * customer.subscription.updated
 */
function handleSubscriptionUpdated(array $subscription): void
{
    $customerId = isset($subscription['customer']) ? $subscription['customer'] : '';
    $status     = isset($subscription['status'])   ? $subscription['status']   : '';

    if (!$customerId) {
        return;
    }

    $pdo  = getPDO();
    $stmt = $pdo->prepare('UPDATE users SET subscription_status = ? WHERE stripe_customer_id = ?');
    $stmt->execute([$status, $customerId]);
}

/**
 * customer.subscription.deleted
 */
function handleSubscriptionDeleted(array $subscription): void
{
    $customerId = isset($subscription['customer']) ? $subscription['customer'] : '';

    if (!$customerId) {
        return;
    }

    $pdo  = getPDO();
    $stmt = $pdo->prepare('UPDATE users SET subscription_status = ? WHERE stripe_customer_id = ?');
    $stmt->execute(['canceled', $customerId]);
}

// ============================================================
// ヘルパー関数
// ============================================================

/**
 * Stripe Webhookシグネチャを検証する
 * Stripe-Signature ヘッダの t= と v1= を使って HMAC-SHA256 で検証
 */
function verifyStripeSignature(string $payload, string $sigHeader, string $secret): bool
{
    if (empty($sigHeader)) {
        return false;
    }

    // ヘッダをパース
    $parts = [];
    foreach (explode(',', $sigHeader) as $item) {
        $kv = explode('=', $item, 2);
        if (count($kv) === 2) {
            $parts[trim($kv[0])] = trim($kv[1]);
        }
    }

    if (empty($parts['t']) || empty($parts['v1'])) {
        return false;
    }

    $timestamp = $parts['t'];
    $signature = $parts['v1'];

    // タイムスタンプが古すぎる場合は拒否（5分以内）
    if (abs(time() - (int)$timestamp) > 300) {
        error_log('[Stripe Webhook] Signature timestamp too old');
        return false;
    }

    // 署名を計算
    $signedPayload   = $timestamp . '.' . $payload;
    $expectedSig     = hash_hmac('sha256', $signedPayload, $secret);

    return hash_equals($expectedSig, $signature);
}

/**
 * ランダムパスワードを生成
 */
function generateRandomPassword(int $length = 12): string
{
    $chars = 'abcdefghijkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    $password = '';
    $max = strlen($chars) - 1;
    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[random_int(0, $max)];
    }
    return $password;
}

/**
 * パスワードをメールで送信
 */
function sendPasswordEmail(string $email, string $password): void
{
    $subject = '[WithBrightTomorrow] 会員登録完了のお知らせ';

    $body  = "WithBrightTomorrow 学習システムへの登録が完了しました。\n\n";
    $body .= "以下の情報でログインしてください。\n\n";
    $body .= "ログインページ: https://YOUR_DOMAIN/login.html\n";
    $body .= "メールアドレス: " . $email . "\n";
    $body .= "パスワード: " . $password . "\n\n";
    $body .= "※パスワードは大切に保管してください。\n";
    $body .= "※このメールに心当たりがない場合は、お手数ですがこのメールを削除してください。\n\n";
    $body .= "--\n";
    $body .= "WithBrightTomorrow Inc.\n";

    $headers = [
        'From: noreply@withbt.com',
        'Reply-To: noreply@withbt.com',
        'Content-Type: text/plain; charset=UTF-8',
        'X-Mailer: PHP/' . phpversion(),
    ];

    $sent = @mail($email, $subject, $body, implode("\r\n", $headers));
    if (!$sent) {
        error_log('[Stripe Webhook] Failed to send password email to: ' . $email);
    }
}
