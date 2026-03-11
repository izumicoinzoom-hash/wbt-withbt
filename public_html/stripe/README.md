# Stripe決済連携 セットアップ手順

## 1. Stripeダッシュボードでの設定

### 1-1. 商品・Price IDの作成
1. [Stripeダッシュボード](https://dashboard.stripe.com/) にログイン
2. 「商品」>「商品を追加」をクリック
3. 商品名（例: WBT月額会費）を入力
4. 料金モデル: 「定期」を選択し、月額料金を設定
5. 作成後、Price ID（`price_` で始まる文字列）をコピー

### 1-2. Webhook エンドポイントの登録
1. 「開発者」>「Webhook」>「エンドポイントを追加」
2. エンドポイントURL: `https://YOUR_DOMAIN/stripe/webhook.php`
3. リッスンするイベント:
   - `checkout.session.completed`
   - `customer.subscription.updated`
   - `customer.subscription.deleted`
4. 作成後、Webhook署名シークレット（`whsec_` で始まる文字列）をコピー

### 1-3. APIキーの確認
1. 「開発者」>「APIキー」
2. 公開可能キー（`pk_live_`）とシークレットキー（`sk_live_`）を確認

## 2. config.php の設定

`stripe/config.php` を開き、以下のプレースホルダを実際の値に置き換える:

```php
define('STRIPE_SECRET_KEY', 'sk_live_実際のシークレットキー');
define('STRIPE_PUBLISHABLE_KEY', 'pk_live_実際の公開可能キー');
define('STRIPE_WEBHOOK_SECRET', 'whsec_実際のWebhookシークレット');
define('STRIPE_PRICE_ID', 'price_実際のPriceID');
define('STRIPE_SUCCESS_URL', 'https://実際のドメイン/stripe/success.php');
define('STRIPE_CANCEL_URL', 'https://実際のドメイン/training.html');
```

`stripe/webhook.php` 内の `sendPasswordEmail()` 関数で、ログインページURLも実際のドメインに変更してください。

## 3. サーバー要件

- PHP 7.4以上（`random_int`、`hash_hmac` が必要）
- cURL拡張が有効（Xserverでは標準で有効）
- `mail()` 関数が有効（Xserverでは標準で有効）
- SSL証明書（HTTPS必須 — Stripeの要件）

## 4. データベース

`users` テーブルが必要です（別途 `db_schema.sql` を参照）:

```sql
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    stripe_customer_id VARCHAR(255) DEFAULT NULL,
    subscription_status VARCHAR(50) DEFAULT 'inactive',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

## 5. 決済フローの動作

1. ユーザーが `stripe/checkout.php?email=user@example.com` にアクセス
2. Stripe Checkoutページへリダイレクト
3. 決済完了 → `stripe/success.php` へリダイレクト（案内表示）
4. Stripe Webhook → `stripe/webhook.php` が呼ばれる
5. ランダムパスワードを生成、usersテーブルにINSERT
6. パスワードをメールで送信
7. ユーザーがメールのパスワードで `login.html` からログイン

## 6. テスト方法

### テスト用APIキーを使う場合
1. `config.php` で `sk_test_` / `pk_test_` のテストキーを使用
2. Stripeのテストカード番号: `4242 4242 4242 4242`（有効期限・CVC は任意）

### Stripe CLI でのローカルWebhookテスト
```bash
# Stripe CLIをインストール
# https://stripe.com/docs/stripe-cli

# ログイン
stripe login

# Webhookをローカルに転送
stripe listen --forward-to localhost/stripe/webhook.php

# 別ターミナルでテストイベントを送信
stripe trigger checkout.session.completed
```

### 確認ポイント
- checkout.php → Stripe決済ページへリダイレクトされるか
- 決済完了後 → success.php が表示されるか
- Webhook → usersテーブルにレコードが追加されるか
- メール → パスワードが記載されたメールが届くか
- subscription削除 → subscription_status が canceled に更新されるか
