# public_html  HTML構造メモ

## トップ階層

| ファイル | 役割 |
|----------|------|
| **index.html** | 会社トップ（WBT MVV・サービス一覧・各ページへのリンク） |
| **training.html** | 特定技能2号自動車整備研修 LP（旧 index の内容） |
| campaign.html | キャンペーン・お申し込み |
| download.html | 資料ダウンロード |
| login.html | 会員ログイン |
| curriculum.html | カリキュラム概要 |
| curriculum-16.html | カリキュラム16項目 |
| learning.html | 学習・クイズ一覧（会員向け） |
| brightboard-demo.html / brightboard-demo-live.html | ブライトボードデモ |
| brightboard-kouteikanri.html | ブライトボード工程管理 |
| tokushoho.html | 特商法表記 |

## サブディレクトリ

- **kiyota/** … 工程管理アプリ（清田自動車向け）
- **demodeta/demo/** … デモ用ビルド

## リンクの流れ

- **トップ (index.html)** → 私たちについて（MVV）／サービス（training, campaign, download, curriculum, kiyota/, brightboard-demo など）
- **各ページ** → ロゴ／「TOP」クリックで **index.html**（会社トップ）に戻る
- **研修LP** → training.html から campaign.html, download.html, login.html などへ

## 参考

- 会社の Mission / Vision / Value は `WBT/WBTMVV.md` を参照して index.html に反映済み。
