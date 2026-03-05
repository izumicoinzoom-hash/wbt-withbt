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
| **DEPLOY_PATH** | 下記「DEPLOY_PATH の調べ方」を参照 | サーバー内の **public_html フォルダの絶対パス** |

---

### 1.3 DEPLOY_PATH とは？ どうやって調べる？

**DEPLOY_PATH** は、**「Git で送った .htaccess と kiyota を、サーバーのどのフォルダに置くか」** を指定する**サーバー内の絶対パス**です。  
Xserver では、Web で公開しているファイルは **`public_html`** というフォルダに入っているので、**その public_html のフルパス** を DEPLOY_PATH に書きます。

#### サーバー上のフォルダのイメージ

```
/home/
  └── サーバーID/          ← SSH_USER と同じ
        └── ドメイン名/     ← 例: withbt.com
              └── public_html/   ← ここが DEPLOY_PATH
                    ├── .htaccess
                    ├── index.html
                    ├── kiyota/
                    │     └── demo/
                    └── （その他 WordPress など）
```

- **DEPLOY_PATH の例**: `/home/abcd1234/withbt.com/public_html`  
  - `abcd1234` = サーバーID（SSH_USER と同じ）  
  - `withbt.com` = そのサーバーに紐づいているドメインのフォルダ名  

#### 調べ方（3つの方法）

1. **Xserver の「ファイル管理」で確認する**  
   - サーバーパネル → **「ファイル管理」**（または「FTP」）を開く。  
   - ログインすると、最初にいる場所がだいたい `/home/サーバーID/` です。  
   - その中に **ドメイン名のフォルダ**（例: `withbt.com`）があり、その中に **`public_html`** があります。  
   - 画面上で「パス」や「フルパス」が表示されていれば、**public_html を選んだときのパス** が DEPLOY_PATH です。  
   - 表示がなければ、次の形で組み立てます: **`/home/サーバーID/ドメイン名/public_html`**

2. **FTP クライアントで確認する**  
   - FileZilla などで Xserver に接続する。  
   - リモート側で「public_html」まで開いていき、そのフォルダの**フルパス**（リモートのパス表示）をコピーする。  
   - それが DEPLOY_PATH です。  
   - 多くの場合、**`/home/サーバーID/ドメイン名/public_html`** という形です。

3. **ドメイン名がわかっている場合の書き方**  
   - サーバーIDは、Xserver の「サーバー情報」や FTP のユーザー名で確認できる。  
   - デプロイしたいサイトのドメインが **withbt.com** なら、  
     **DEPLOY_PATH = `/home/サーバーID/withbt.com/public_html`**  
   - サーバーIDだけ、実際の英数字に置き換える。

#### 入力例（GitHub の Secret に入れる値）

| あなたの環境 | DEPLOY_PATH に登録する値 |
|--------------|---------------------------|
| サーバーIDが `xyzt1234`、ドメインが `withbt.com` | `/home/xyzt1234/withbt.com/public_html` |
| サーバーIDが `myserver`、ドメインが `example.com` | `/home/myserver/example.com/public_html` |

※ **末尾にスラッシュは付けない**（`/public_html` で終わる）。  
※ ドメイン名のフォルダが **サブドメイン** の場合は、Xserver の「ドメイン設定」で表示されているフォルダ名（例: `withbt.com` や `www.withbt.com`）をそのまま使う。

---

## 2. GitHub リポジトリの準備

### 2.1 リポジトリを作成

1. GitHub で **新しいリポジトリ** を作成する（例: `wbt-withbt`）。
2. **Private** でも **Public** でも可。
3. README や .gitignore は既に WBT 側にあるので、**空のリポジトリ** で作成してよい。

### 2.2 Secrets と Variables の登録

そのリポジトリの **Settings → Secrets and variables → Actions** を開く。

#### 必ず Secrets に登録するもの（機密情報）

**「Secrets」** の **New repository secret** で、次を **1つだけ** 登録する。

| Name | Secret に入れる値 |
|------|-------------------|
| **SSH_PRIVATE_KEY** | 1.1 でダウンロードした秘密鍵の **全文**（`-----BEGIN ... KEY-----` から `-----END ... KEY-----` まで） |

※ 秘密鍵は改行も含めてそのまま貼り付ける。**SSH_PRIVATE_KEY は必ず Secrets に置く**（Variables には入れない）。

#### SSH_HOST などは Secrets でも Variables でもよい

次の 4 つは、**Secrets** に登録しても **Variables** に登録しても動く（ワークフローが両方に対応している）。  
**Xserver のスクリーンショット（サーバー情報・SSH設定）から分かる値の例：**

| Name | 登録する値（withbt 環境の例） |
|------|------------------------------|
| **SSH_USER** | `withbt`（SSH設定の「ユーザー名」） |
| **SSH_HOST** | `sv16802.xserver.jp`（サーバー情報の「ホスト名」。IP の `85.131.209.163` は使わない） |
| **SSH_PORT** | `10022`（SSH設定の「接続ポート」） |
| **DEPLOY_PATH** | `/home/withbt/withbt.xsrv.jp/public_html`（※下記「DEPLOY_PATH について」を確認） |

**DEPLOY_PATH について**  
スクリーンショットには出ていません。Xserver の **「ファイル管理」** で、公開用の `public_html` フォルダを開いたときの**フルパス**を確認してください。  
ドメインが `withbt.xsrv.jp` の場合は、多くの場合 **`/home/withbt/withbt.xsrv.jp/public_html`** です。別ドメイン（例: withbt.com）の場合は **`/home/withbt/ドメイン名/public_html`** の形になります。

**GitHub の Variables にそのまま貼る値（コピー用）**

| Name | Value |
|------|--------|
| SSH_USER | `withbt` |
| SSH_HOST | `sv16802.xserver.jp` |
| SSH_PORT | `10022` |
| DEPLOY_PATH | `/home/withbt/withbt.xsrv.jp/public_html` |

※ DEPLOY_PATH は、ファイル管理で確認した実際のパスに合わせて書き換えてください。

- **Secrets に登録する場合**: 上記の **New repository secret** で、Name と値を登録する。
- **Variables に登録する場合**: 同じ画面で **「Variables」** タブを開き、**New repository variable** で、**同じ Name**（SSH_USER, SSH_HOST, SSH_PORT, DEPLOY_PATH）で登録する。

**Repository variables にしておくと**、値の確認・編集がしやすく、ログにも *** でマスクされずに変数名だけ出るので、どの設定が使われているか分かりやすい。機密ではないので Variables で問題ない。

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

1. GitHub の **Actions** タブで **Deploy to Xserver**（または **Deploy kiyota demo via FTP**）が **緑（成功）** になっているか確認する。
2. ブラウザで **https://withbt.com/kiyota/demo/** を開き、デモ画面が表示されるか確認する。

---

## 403 Forbidden が出る場合の確認

**https://withbt.com/kiyota/demo/** や **https://withbt.com/demodeta/demo/** で「403 Forbidden」になる場合は、次を順に確認してください。

### 1. FTP デプロイが実行されているか

- GitHub の **Actions** タブで **「Deploy kiyota demo via FTP」** が **緑（成功）** で実行されているか確認する。
- **paths** で `public_html/kiyota/demo/**` や `public_html/.htaccess` を変更した push のときだけワークフローが動く。`.htaccess` だけの変更でもトリガーされるようにしてある。
- 一度も動いていない場合は、**Actions** から **「Run workflow」** で手動実行する。

### 2. サーバーにファイルがあるか

- Xserver の **ファイル管理**（または FTP）で、**ドキュメントルート**（例: `withbt.com/public_html/`）の下に **`kiyota/demo/`** や **`demodeta/demo/`** があるか確認する。
- その中に **`index.html`** と **`assets`** フォルダがあるか確認する。
- 無い場合は、FTP の **FTP_REMOTE_DIR**（Secret）が誤っている可能性がある。withbt.com のドキュメントルートが `withbt.com/public_html/` なら、**FTP_REMOTE_DIR** は **`withbt.com/public_html/`**（末尾スラッシュ可）にする。

### 3. パーミッション（統一推奨）

- **フォルダ（ディレクトリ）**: **705** に統一
- **ファイル**（index.html など）: **604** に統一
- Xserver のファイル管理で、次のディレクトリとその中身を上記に揃えると 403 になりにくい。  
  **`kiyota`** と **`kiyota/demo`** 以下、**`demodeta`** と **`demodeta/demo`** 以下。

### 4. ルートの .htaccess が反映されているか

- サーバー上の **ドキュメントルート直下の .htaccess** に、`/kiyota` と `/demodeta` を WordPress に渡さない設定が入っているか確認する（手順書の `public_html/.htaccess` と同じ内容）。

---

## 「Test SSH connection」の結果を確認する方法

デプロイが失敗したときに、**SSH 接続まで成功しているか** を確認する手順です。

### 1. GitHub のリポジトリを開く

ブラウザで **https://github.com/izumicoinzoom-hash/wbt-withbt** を開く（自分のリポジトリの URL でも可）。

### 2. Actions タブを開く

画面上方のメニューで **「Actions」** をクリックする。

### 3. 直近のワークフロー実行を開く

一覧に **「Deploy to Xserver」** という名前のワークフローが並んでいるので、**いちばん上（いちばん新しい）** の行をクリックする。

- **左端が緑の ✓** → その実行はすべて成功
- **左端が赤の ✗** → その実行のどこかで失敗している

### 4. 各ステップの結果を見る

クリックすると、その実行の詳細が開く。**左側にステップ名**が並んでいる。

| ステップ名 | 意味 |
|------------|------|
| **Checkout** | リポジトリの取得 |
| **Setup SSH key** | 秘密鍵の準備 |
| **Test SSH connection** | サーバーへ SSH で接続できるかテスト |
| **Deploy .htaccess ...** | .htaccess のアップロード |
| **Deploy kiyota ...** | kiyota フォルダのアップロード |

- 各ステップの左に **緑の ✓** が出ていれば、そのステップは成功。
- **赤の ✗** が出ているステップが、失敗しているところ。

### 5. 「Test SSH connection」の結果の見方

- **「Test SSH connection」に緑の ✓**  
  → SSH 接続・認証は成功している。失敗しているのはその後の「Deploy」のどちらか。scp やパスの問題の可能性がある。

- **「Test SSH connection」に赤の ✗**  
  → SSH 接続または認証で失敗している。秘密鍵・SSH_USER・SSH_HOST・Xserver の「すべてのアクセスを許可」などを確認する。

### 6. 失敗したステップのログを見る

失敗しているステップ（赤 ✗）を **クリック** すると、そのステップのログが下に開く。  
**赤いエラーメッセージ** の近くに、原因のヒントが書いてある。

---

---

## 5. トラブル時

### 「Test SSH connection」で Connection closed になる場合（いちばん多いパターン）

**症状**: **Test SSH connection** の step で `Connection closed by 85.131.209.163 port ***` や `exit code 255` になる。  
＝サーバーに届いたあと、認証前に接続を閉じられている状態です。

**次の 4 点を、上から順に確認してください。**

---

#### ① SSH_HOST は「ホスト名」か？（IP アドレスではないこと）

- **SSH_HOST** には **IP アドレス（85.131.209.163 など）は入れないでください。**
- 必ず **ホスト名** を入れます。例: **`sv1234.xserver.jp`**
- 確認場所: Xserver サーバーパネル → **「サーバー情報」** の **FTP ホスト名** または **SSH 接続情報**。
- GitHub の **Settings → Secrets → SSH_HOST** を開き、値が **`sv○○○○.xserver.jp`** の形になっているか確認し、IP だけの場合はホスト名に書き換えて保存。

---

#### ② Xserver の「公開鍵認証の許可」が「すべてのアクセスを許可」か

- サーバーパネル → **「SSH設定」** を開く。
- **「公開鍵認証の許可」** を **「ON（すべてのアクセスを許可）※非推奨」** にしているか確認。
- 「ON（パスワード認証を許可）」だけなど、ほかの選択肢だと GitHub Actions からの接続が **Connection closed** になりやすいです（2024年以降の Xserver でよくある原因）。  
  → 変更したら保存し、数分待ってからもう一度 Actions を実行。

---

#### ③ 秘密鍵は「Xserver でダウンロードしたそのファイル」か

- **SSH_PRIVATE_KEY** に登録しているのは、**Xserver の「公開鍵認証用鍵ペアの生成」で作成し、その場でダウンロードした秘密鍵**の中身であること。
- 別の PC や別サービス用に作った鍵や、自分で `ssh-keygen` した鍵は、Xserver 側に登録した公開鍵とペアになっていないので使えません。
- 不安な場合は、Xserver の SSH 設定で **いったん鍵を削除し、あらためて「鍵ペアの生成」で新規作成** → ダウンロードした秘密鍵の全文を、GitHub の **SSH_PRIVATE_KEY** に貼り直す。

---

#### ④ 秘密鍵の貼り方（改行・パスフレーズ）

- 秘密鍵は **「-----BEGIN ... KEY-----」** で始まり、**「-----END ... KEY-----」** で終わる形のまま、**改行も含めて全文** をコピーして Secret に貼る。1 行にまとまっていないこと。
- パスフレーズは **空（なし）** で作成した鍵を使う。パスフレーズを付けてしまった場合は、上記 ③ のように鍵を新規作成し直す。

---

ここまで直したうえで、GitHub の **Actions** から **「Run workflow」** で手動実行し、**Test SSH connection** が緑になるか確認してください。

---

### 「Connection closed」が Deploy の step で出る場合

**Test SSH connection** は **緑（成功）** なのに、その後の **Deploy .htaccess** や **Deploy kiyota** で Connection closed になる場合は、scp の送り先やサーバー側の制限の可能性があります。DEPLOY_PATH が正しいか、手順書の「1.3 DEPLOY_PATH」を再確認してください。

### その他のエラー（rsync/scp error 255 など）

上記「Test SSH connection で Connection closed」の ①〜④ を確認したうえで、次も確認してください。

- **SSH_USER**: サーバーID（英数字）。FTP のユーザー名と同じ。
- **SSH_PORT**: `10022`（数値のみ。引用符は不要）。
- 秘密鍵の改行・余計な空白が入っていないか。

---

### その他のトラブル

- **SSH で接続できない**: 上記「Connection closed」の対処のほか、秘密鍵の改行・余計な空白、SSH_USER / SSH_HOST / SSH_PORT が正しいか確認する。
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
