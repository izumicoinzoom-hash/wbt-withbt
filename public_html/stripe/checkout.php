<?php
/**
 * Stripe Checkout セッション作成
 * Composerなし・curl直接呼び出しでStripe APIを利用
 */
require_once __DIR__ . '/config.php';

// GETパラメータからメールアドレスを取得（任意）
$email = isset($_GET['email']) ? filter_var($_GET['email'], FILTER_VALIDATE_EMAIL) : null;

// Checkout Session作成用パラメータ
$params = [
    'mode'                => 'subscription',
    'payment_method_types[0]' => 'card',
    'line_items[0][price]'    => STRIPE_PRICE_ID,
    'line_items[0][quantity]' => 1,
    'success_url'         => STRIPE_SUCCESS_URL . '?session_id={CHECKOUT_SESSION_ID}',
    'cancel_url'          => STRIPE_CANCEL_URL,
];

if ($email) {
    $params['customer_email'] = $email;
}

// Stripe API呼び出し（curl）
$ch = curl_init('https://api.stripe.com/v1/checkout/sessions');
curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => http_build_query($params),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER     => [
        'Authorization: Bearer ' . STRIPE_SECRET_KEY,
    ],
    CURLOPT_TIMEOUT        => 30,
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($curlError) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Stripe APIへの接続に失敗しました。';
    error_log('[Stripe Checkout] curl error: ' . $curlError);
    exit;
}

$data = json_decode($response, true);

if ($httpCode !== 200 || empty($data['url'])) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=UTF-8');
    echo '決済ページの作成に失敗しました。しばらく経ってからお試しください。';
    error_log('[Stripe Checkout] API error: ' . $response);
    exit;
}

// Stripe Checkoutページへリダイレクト
header('Location: ' . $data['url']);
exit;
