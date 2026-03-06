# public_html  HTML構造メモ

## トップ階層

| ファイル | 役割 |
|----------|------|
| **index.html** | 会社トップ（特定2号・BrightBoard の両軸＋拡張性のあるサービス構成） |
| **about.html** | 会社概要（ミッション→ビジョン→バリュー→会社概要の流れで掲載） |
| **training.html** | 特定技能2号自動車整備研修 LP |
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

- **トップ (index.html)** → ヒーロー（両軸：特定2号・BrightBoard）／サービス（軸1: 資格取得、軸2: 工程管理、その他拡張用）／会社概要は about.html
- **会社概要 (about.html)** → ミッション→ビジョン→バリュー→会社概要の順。ナビ・ページ内リンクも同順。
- **各ページ** → ロゴで **index.html**、ナビに「会社概要」で about.html

## 参考

- 会社の Mission / Vision / Value は `WBT/WBTMVV.md` を参照。全文は about.html に掲載。
