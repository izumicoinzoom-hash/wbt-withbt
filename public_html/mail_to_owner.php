<?php
/**
 * 資料請求フォーム送信時に、通知先にメールを送信する
 * 通知先: izumi.coinzoom@gmail.com
 */
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$NOTIFY_EMAIL = 'izumi.coinzoom@gmail.com';

$company = isset($_POST['entry.373753415']) ? trim((string)$_POST['entry.373753415']) : '';
$name    = isset($_POST['entry.681605283']) ? trim((string)$_POST['entry.681605283']) : '';
$email   = isset($_POST['entry.521760541']) ? trim((string)$_POST['entry.521760541']) : '';
$tel     = isset($_POST['entry.961659553']) ? trim((string)$_POST['entry.961659553']) : '';
$issue   = isset($_POST['entry.1630095653']) ? trim((string)$_POST['entry.1630095653']) : '';

$subject = '[WithBrightTomorrow] 資料請求: ' . ($company ?: '(会社名なし)');
$body = "資料請求がありました。\n\n";
$body .= "会社名: " . $company . "\n";
$body .= "担当者名: " . $name . "\n";
$body .= "メールアドレス: " . $email . "\n";
$body .= "電話番号: " . $tel . "\n";
$body .= "お困りの点: " . $issue . "\n";
$body .= "\n--\nこのメールは withbt.com の資料請求フォームから送信されました。\n";

$headers = [
    'From: ' . $NOTIFY_EMAIL,
    'Reply-To: ' . $email,
    'Content-Type: text/plain; charset=UTF-8',
    'X-Mailer: PHP/' . phpversion(),
];

$sent = @mail($NOTIFY_EMAIL, $subject, $body, implode("\r\n", $headers));
echo json_encode(['ok' => $sent]);
exit;
