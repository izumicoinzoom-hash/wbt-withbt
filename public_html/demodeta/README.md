# demodeta（デモ用ビルドの配置先）

このフォルダは **BrightBoard デモ** のビルド成果物を配置する場所です。

## 含まれるもの

| パス | 内容 |
|------|------|
| `demo/` | デモ用 SPA（index.html + assets/*.js, *.css） |
| `demo/estimator/` | 見積もりチェッカー等 |
| `demo/デモ_ログインについて.md` | デモでのログインに関する説明 |

## ソースコードについて

**このリポジトリには、デモアプリのソースコード（React / Vite / TypeScript 等）は含まれていません。**

- `demo/assets/*.js` はビルド済みのバンドル（React + Firebase 等が含まれる）です。
- デモの元になるアプリは、別リポジトリまたは別フォルダでビルドされ、その出力がここにコピーされている想定です。
- 本番用の同系統アプリは `public_html/kiyota/` に別ビルドとして配置されています。

## デモで Google ログインを外すには

1. **元のソースを用意する**  
   BrightBoard（工程管理アプリ）の React ソースがどこにあるか確認し、そのプロジェクトを開く。

2. **デモモードを実装する**  
   例: パスが `/demodeta/demo/` のとき、または環境変数 `VITE_DEMO=1` のときは  
   - Firebase の **匿名認証**（`signInAnonymously()`）で自動ログインする、または  
   - 認証をスキップしてローカル／ダミーデータのみで動作させる。

3. **デモ用にビルドして配置する**  
   ```bash
   npm run build
   # または base を /demodeta/demo/ にしたビルド
   ```  
   生成された `dist/` の中身を、この `demodeta/demo/` に上書きする。

4. **Git にコミット・プッシュ**  
   `demodeta/demo/index.html` と `demodeta/demo/assets/*` をコミットすれば、FTP デプロイでサーバーに反映されます。

## 実施済みの変更（このリポジトリ内で可能な範囲）

- `demo/index.html` から Google GSI（`accounts.google.com/gsi/client`）の読み込みを削除済みです。  
  完全にログインを外すには、上記のとおり**ソース側でのデモモード実装と再ビルド**が必要です。
