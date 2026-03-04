# Git で Xserver にデプロイする手順

Git に push するだけで、Xserver の `public_html` に **.htaccess** と **kiyota/**（デモ含む）が自動反映されるようにする手順です。

---

## 前提

- WBT リポジトリを GitHub に push する
- GitHub Actions が動いたときに、Xserver へ SSH で接続し **rsync** で `public_html/.htaccess` と `public_html/kiyota/` を送る
- Xserver 側では **SSH 接続** が有効で、**公開鍵認証** が設定されていること

---

## 1. Xserver の準備

### 1.1 SSH を有効化し、秘密鍵を取得

1. **Xserver のサーバーパネル** にログインする。
2. **「SSH設定」** を開く。
3. **SSH を ON** にする。
4. **「公開鍵認証用鍵ペアの生成」** で鍵を作成する。
   - **パスフレーズは空** のまま（GitHub Actions で使うため）。
   - 注意: 「**ON（すべてのアクセスを許可）**」を選ぶ（Xserver の推奨設定。2024年以降はこちらでないと rsync が失敗する場合あり）。
5. 作成後、**秘密鍵（.pem など）をダウンロード** し、中身をメモ帳などで開いて **全文コピー** しておく（GitHub の Secret に登録するため）。

※ 詳細は [Xserver マニュアル｜SSH](https://www.xserver.ne.jp/manual/man_server_ssh.php) を参照。

### 1.2 サーバー情報の確認

次の値を控えておく。

| 項目 | 例 | どこで確認するか |
|------|-----|------------------|
| **SSH_HOST** | `sv1234.xserver.jp` | サーバーパネル「サーバー情報」の FTP ホスト名 |
| **SSH_USER** | サーバーID（英数字） | 同じく「FTP アカウント」のユーザー名 |
| **SSH_PORT** | `10022` | Xserver の SSH ポート（通常 10022） |
| **DEPLOY_PATH** | `/home/サーバーID/withbt.com/public_html` | デプロイ先。ドメインごとの `public_html` の**絶対パス** |

※ DEPLOY_PATH は、Xserver の「ドメイン設定」や FTP でログインしたときのホームの下、`ドメイン名/public_html` です。

---

## 2. GitHub リポジトリの準備

### 2.1 リポジトリを作成

1. GitHub で **新しいリポジトリ** を作成する（例: `wbt-withbt`）。
2. **Private** でも **Public** でも可。
3. README や .gitignore は既に WBT 側にあるので、**空のリポジトリ** で作成してよい。

### 2.2 Secrets の登録

そのリポジトリの **Settings → Secrets and variables → Actions** を開き、**New repository secret** で次を登録する。

| Name | Secret に入れる値 |
|------|-------------------|
| **SSH_PRIVATE_KEY** | 1.1 でダウンロードした秘密鍵の **全文**（`-----BEGIN ... KEY-----` から `-----END ... KEY-----` まで） |
| **SSH_USER** | 1.2 の SSH_USER（サーバーID） |
| **SSH_HOST** | 1.2 の SSH_HOST（例: `sv1234.xserver.jp`） |
| **SSH_PORT** | `10022` |
| **DEPLOY_PATH** | 1.2 の DEPLOY_PATH（例: `/home/サーバーID/withbt.com/public_html`） |

※ 秘密鍵は改行も含めてそのまま貼り付ける。

---

## 3. WBT を Git リポジトリにして push する

### 3.1 リポジトリの初期化（初回のみ）

WBT フォルダで次を実行する。

```powershell
cd "c:\Users\ken\新しいフォルダー\WBT"
git init
git add .github/workflows/deploy-xserver.yml
git add public_html/.htaccess
git add public_html/kiyota/
git add .gitignore
# 必要に応じて他のファイルも add
git status
```

※ まずはデプロイに必要なものだけ add してもよい。全部入れる場合は `git add .` で可（.gitignore で除外されたものは入らない）。

### 3.2 初回コミットとリモート設定

```powershell
git commit -m "Add Xserver deploy workflow and demo files"
git branch -M main
git remote add origin https://github.com/あなたのユーザー名/リポジトリ名.git
git push -u origin main
```

※ `あなたのユーザー名/リポジトリ名` は 2.1 で作ったリポジトリの URL に合わせる。

### 3.3 デプロイの動き

- **main** に push し、**変更があったパス** が次のいずれかのときだけ、ワークフローが動く。
  - `public_html/.htaccess`
  - `public_html/kiyota/**`
- 手動で実行したい場合は、GitHub の **Actions** タブで **Deploy to Xserver** を選び **Run workflow** を押す。

---

## 4. 動作確認

1. GitHub の **Actions** タブで **Deploy to Xserver** が **緑（成功）** になっているか確認する。
2. ブラウザで **https://withbt.com/kiyota/demo/** を開き、デモ画面が表示されるか確認する。

---

## 5. トラブル時

- **SSH で接続できない**: 秘密鍵の改行や余計な空白が入っていないか、SSH_USER / SSH_HOST / SSH_PORT が正しいか確認する。Xserver の SSH 設定で「すべてのアクセスを許可」になっているかも確認する。
- **rsync で Permission denied**: DEPLOY_PATH が、その SSH_USER で書き込めるパスか確認する（通常は `/home/サーバーID/ドメイン名/public_html`）。
- **push はできるが反映されない**: Actions のログで、どの step で失敗しているか確認する。

---

## まとめ

| やること | 場所 |
|----------|------|
| SSH 鍵取得・サーバー情報のメモ | Xserver 管理画面 |
| Secrets 登録 | GitHub リポジトリ Settings → Actions |
| git init / add / commit / push | ローカル WBT フォルダ |
| デプロイ実行 | main への push または Actions の手動実行 |

これで、**Git で push するだけで Xserver に .htaccess と kiyota（デモ）がアップロード**されます。
