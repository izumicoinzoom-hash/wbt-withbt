<?php
// Stripe API設定
// Stripeダッシュボードから取得した値に置き換えてください
define('STRIPE_SECRET_KEY', 'sk_live_YOUR_SECRET_KEY');
define('STRIPE_PUBLISHABLE_KEY', 'pk_live_YOUR_PUBLISHABLE_KEY');
define('STRIPE_WEBHOOK_SECRET', 'whsec_YOUR_WEBHOOK_SECRET');
define('STRIPE_PRICE_ID', 'price_YOUR_PRICE_ID'); // 月額会費のPrice ID
define('STRIPE_SUCCESS_URL', 'https://YOUR_DOMAIN/stripe/success.php');
define('STRIPE_CANCEL_URL', 'https://YOUR_DOMAIN/training.html');
