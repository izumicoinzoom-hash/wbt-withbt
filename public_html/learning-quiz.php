<?php require_once __DIR__ . '/auth_check.php'; ?>
<!DOCTYPE html>
<html lang="ja" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>問題 | WithBrightTomorrow</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;700&family=Noto+Sans+JP:wght@400;500;700;900&family=Oswald:wght@400;600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: { navy: '#0F172A', dark: '#020617', orange: '#EA580C', red: '#DC2626', slate: '#F1F5F9' }
                    },
                    fontFamily: { sans: ['"Noto Sans JP"', 'sans-serif'], mono: ['"JetBrains Mono"', 'monospace'], display: ['"Oswald"', 'sans-serif'] },
                    backgroundImage: { 'grid-pattern': "radial-gradient(circle, #334155 1px, transparent 1px)" }
                }
            }
        }
    </script>
    <style>
        body { background-color: #0F172A; color: #F8FAFC; }
        .bg-grid { background-size: 40px 40px; opacity: 0.08; }
        .quiz-option { transition: all 0.2s; }
        .quiz-option:hover { background-color: rgba(234, 88, 12, 0.15); border-color: rgba(234, 88, 12, 0.5); }
        .quiz-option.selected { border-color: #EA580C; background-color: rgba(234, 88, 12, 0.2); }
        .quiz-option.correct { border-color: #22c55e; background-color: rgba(34, 197, 94, 0.2); }
        .quiz-option.incorrect { border-color: #DC2626; background-color: rgba(220, 38, 38, 0.15); }
        .quiz-option.disabled { pointer-events: none; opacity: 0.8; }
    </style>
</head>
<body class="antialiased selection:bg-brand-orange selection:text-white flex flex-col min-h-screen">

    <header class="w-full py-4 border-b border-slate-700/50 bg-brand-navy/95 backdrop-blur-sm sticky top-0 z-30">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <a href="learning.php" class="text-slate-400 hover:text-white text-sm flex items-center gap-1">← 学習トップ</a>
                <span class="text-slate-600">|</span>
                <span id="curriculumTitle" class="text-white font-bold text-sm md:text-base"></span>
            </div>
            <a href="logout.php" class="text-slate-400 hover:text-white text-sm">ログアウト</a>
        </div>
    </header>

    <main class="flex-grow relative pt-6 pb-16">
        <div class="absolute inset-0 bg-grid-pattern bg-grid z-0"></div>
        <div class="max-w-4xl mx-auto px-4 sm:px-6 relative z-10">

            <div class="grid grid-cols-2 gap-4 mb-8">
                <div class="bg-slate-800/80 border border-slate-700 rounded-xl p-4">
                    <div class="text-slate-400 text-xs font-bold mb-1">進捗</div>
                    <div class="flex items-baseline gap-2">
                        <span id="progressCount" class="text-2xl font-bold text-white font-mono">0</span>
                        <span class="text-slate-400 text-sm">/ <span id="totalCount">0</span> 問</span>
                    </div>
                    <div class="mt-2 h-2 bg-slate-700 rounded-full overflow-hidden">
                        <div id="progressBar" class="h-full bg-brand-orange rounded-full transition-all duration-500" style="width: 0%"></div>
                    </div>
                </div>
                <div class="bg-slate-800/80 border border-slate-700 rounded-xl p-4">
                    <div class="text-slate-400 text-xs font-bold mb-1">正答率</div>
                    <div class="flex items-baseline gap-2">
                        <span id="accuracyPercent" class="text-2xl font-bold text-white font-mono">—</span>
                        <span class="text-slate-400 text-sm">%</span>
                    </div>
                    <p id="accuracySub" class="text-slate-500 text-xs mt-1">回答後に表示</p>
                </div>
            </div>

            <div id="quizArea" class="space-y-6">
                <div id="questionCard" class="bg-slate-800/80 border border-slate-700 rounded-2xl p-6 md:p-8 shadow-xl">
                    <div class="text-brand-orange font-mono text-xs font-bold mb-3">QUESTION <span id="currentNum">1</span></div>
                    <h2 id="questionText" class="text-xl md:text-2xl font-bold text-white mb-6 leading-relaxed"></h2>
                    <div id="optionsList" class="space-y-3"></div>
                    <div id="feedbackArea" class="mt-6 hidden">
                        <div id="feedbackMessage" class="p-4 rounded-lg text-sm font-medium"></div>
                        <button type="button" id="nextBtn" class="mt-4 w-full md:w-auto px-8 py-3 bg-brand-orange text-white font-bold rounded-lg hover:bg-orange-600 transition">次の問題へ</button>
                    </div>
                </div>
            </div>

            <div id="completeArea" class="hidden text-center py-12">
                <div class="w-20 h-20 bg-brand-orange/20 text-brand-orange rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <h2 class="text-2xl font-bold text-white mb-2">全問完了</h2>
                <p class="text-slate-400 mb-6">正答率は <span id="finalAccuracy" class="text-brand-orange font-bold font-mono">0</span>% でした。</p>
                <a href="learning.php" class="inline-block px-6 py-3 bg-slate-700 text-white font-bold rounded-lg hover:bg-slate-600 transition mr-3">カリキュラム一覧へ</a>
                <button type="button" id="retryBtn" class="px-6 py-3 bg-brand-orange text-white font-bold rounded-lg hover:bg-orange-600 transition">もう一度挑戦する</button>
            </div>
        </div>
    </main>

    <script>
        (function() {
            var curriculumMeta = {
                '1': { title: '第1回 道路運送車両法と特定整備制度', questions: [
                    { q: '道路運送車両法に基づく「特定整備」とは、どのような整備を指しますか？', options: ['任意の点検のみ', '法定点検（12ヶ月・24ヶ月等）を実施する整備', '車両販売時の整備のみ', '板金・塗装のみ'], correct: 1 },
                    { q: '継続検査（車検）の有効期間は、普通車の場合おおむね何年ですか？', options: ['1年', '2年', '3年', '5年'], correct: 1 },
                    { q: '道路運送車両法で定める自動車の種別として正しいのはどれですか？', options: ['大型自動車、普通自動車、小型自動車など', '大型自動車、小型自動車、軽自動車、大型特殊、小型特殊', '普通自動車、小型自動車、軽自動車、大型特殊、小型特殊', '普通自動車、軽自動車、小型特殊のみ'], correct: 2 },
                    { q: '長さ6m以下の自動車に備える後退灯の数として正しいのはどれですか？', options: ['1個のみ', '1個又は2個', '2個以上必須', '不要'], correct: 1 },
                    { q: '後退灯の灯光の色はどうあるべきですか？', options: ['橙色', '白色', '赤色', '黄色又は橙色'], correct: 1 },
                    { q: '国土交通大臣の検査を受け、自動車検査証の交付が不要なものはどれですか？', options: ['普通自動車', '四輪の小型自動車', '小型特殊自動車', '検査対象軽自動車'], correct: 2 },
                    { q: 'サイドスリップ・テスタで計測した横滑り量が走行1mについて何mmを超えると基準に適合しないですか？', options: ['4mm', '5mm', '6mm', '7mm'], correct: 1 },
                    { q: '燃料タンクの注入口及びガス抜口は、露出した電気端子から何mm以上離れている必要がありますか？', options: ['150mm', '200mm', '250mm', '300mm'], correct: 1 },
                    { q: '尾灯の点灯確認距離の基準として正しいのはどれですか？', options: ['昼間150m', '夜間150m', '昼間300m', '夜間300m'], correct: 3 }
                ]},
                '2': { title: '第2回 整備主任者・責任とコンプライアンス', questions: [
                    { q: '整備主任者の主な責任として適切でないものはどれですか？', options: ['整備作業の監督', '整備記録の保存', '車両の販売目標の達成', '作業者の安全確保'], correct: 2 },
                    { q: '整備記録の保存期間について、法令で定められている目安はどれですか？', options: ['1年', '2年', 'おおむね一定期間の保存が義務', '保存義務なし'], correct: 2 },
                    { q: 'コンプライアンスの基本として適切なのはどれですか？', options: ['法令・社内規程を守り、不正を防ぐ', '売上のみを重視する', '記録は省略してよい', '点検項目を減らしてよい'], correct: 0 },
                    { q: '整備主任者が整備作業を「監督」する主な目的はどれですか？', options: ['作業時間を短くするため', '整備の適正化と品質・安全の確保', '経費削減のみ', '見た目を整えるため'], correct: 1 },
                    { q: '整備業者が違反した場合、法令上考えられるのはどれですか？', options: ['何もない', '指示・処分の対象となり得る', '褒賞のみ', '任意の指導のみ'], correct: 1 },
                    { q: '整備記録に記載すべき内容でないものはどれですか？', options: ['点検・整備の内容', '実施日・担当者', '車両の購入価格', '使用した部品など'], correct: 2 },
                    { q: '分解整備に該当するのはどれですか？', options: ['エンジンオイル交換のみ', 'ブレーキディスクのキャリパーを取り外して行う整備', 'ライト球の交換のみ', 'ワイパーゴムの交換のみ'], correct: 1 }
                ]},
                '3': { title: '第3回 専門用語（日本語）基礎', questions: [
                    { q: '「シリンダーヘッド」の主な役割として適切なのはどれですか？', options: ['エンジン上部を覆い、燃焼室を形成する', 'タイヤの空気を入れる', 'バッテリーを固定する', 'ブレーキペダルに取り付ける'], correct: 0 },
                    { q: '「トルク」の単位として正しいのはどれですか？', options: ['km/h', 'N・m（ニュートンメートル）', 'L（リットル）', 'V（ボルト）'], correct: 1 },
                    { q: '「O/H」とは主にどのような作業を指しますか？', options: ['洗車', 'オーバーホール（分解・点検・組立）', 'オイル交換のみ', '塗装'], correct: 1 },
                    { q: '「クランクシャフト」の主な役割はどれですか？', options: ['ピストンの往復運動を回転運動に変える', '電気を送る', 'ブレーキをかける', '冷却水を送る'], correct: 0 },
                    { q: '「DTC」とは何の略として使われることが多いですか？', options: ['Data Transfer Code', 'Diagnostic Trouble Code（故障コード）', 'Drive Train Control', 'Duration Time Check'], correct: 1 },
                    { q: '「ブレーキフルード」の役割として適切なのはどれですか？', options: ['エンジン冷却', '油圧ブレーキで力を伝える', 'ワイパー洗浄', 'エアコン冷媒'], correct: 1 },
                    { q: '「クラッシュ・ハイト」とは何を指しますか？', options: ['ベアリングの内径', 'ベアリングのハウジングとの密着に必要な高さ', 'ピストンの高さ', 'ボルトの長さ'], correct: 1 },
                    { q: '「平均有効圧力」の計算として正しいのはどれですか？', options: ['行程容積を1サイクルの仕事で除する', '1サイクルの仕事を行程容積で除する', '排気量を仕事量で除する', '仕事量と排気量の和'], correct: 1 }
                ]},
                '4': { title: '第4回 法定点検（12ヶ月・24ヶ月）の手順', questions: [
                    { q: '12ヶ月点検と24ヶ月点検の違いとして正しいのはどれですか？', options: ['内容は同じ', '24ヶ月点検の方が項目が多いなど、内容が異なる', 'どちらも任意', '12ヶ月のみ法定'], correct: 1 },
                    { q: '「自動車点検基準」の自家用乗用自動車等の定期点検（1年）の項目として不適切なものはどれですか？', options: ['バッテリの液量が適当であること', 'かじ取り装置のパワーステアリングベルトの緩み', '制動装置のブレーキペダルの遊び', '原動機の潤滑装置の油漏れ'], correct: 0 },
                    { q: '「バッテリの液量が適当であること」はどの点検に該当しますか？', options: ['1年ごとの定期点検', '日常点検基準', '24ヶ月点検のみ', '任意点検'], correct: 1 },
                    { q: '点検記録に必ず記載すべきでないものはどれですか？', options: ['点検実施日', '点検項目と結果', '担当者', '車両の販売価格'], correct: 3 },
                    { q: '法定点検で使用する測定機器として適切なものはどれですか？', options: ['ノギス、マイクロメーターなど', '体温計', '騒音計のみ', 'カメラのみ'], correct: 0 },
                    { q: '点検で不具合を発見した場合、適切な対応はどれですか？', options: ['記録しない', '記録し、必要に応じて整備・修理を実施する', '無視する', '点検を中止するだけ'], correct: 1 },
                    { q: '「特定整備」に該当するものはどれですか？', options: ['緩衝装置のリーフスプリングを取り外して行う整備', '車輪のみを取り外す整備', '燃料タンクのみの点検', 'ライトの球交換のみ'], correct: 0 }
                ]},
                '5': { title: '第5回 測定機器の取り扱い（ノギス・マイクロメーター）', questions: [
                    { q: 'ノギスで主に測定する単位はどれですか？', options: ['インチのみ', 'mm（ミリメートル）', 'kg（キログラム）', 'A（アンペア）'], correct: 1 },
                    { q: 'マイクロメーターはノギスと比べてどのような特徴がありますか？', options: ['精度が低い', 'より精密な測定に用いる', '長さだけ測れる', '電気のみ測定'], correct: 1 },
                    { q: 'シリンダーブロックの内径測定に適した機器はどれですか？', options: ['タイヤゲージ', 'シリンダーゲージ（内径マイクロメーター）', '水温計', 'ノギス（外径用）'], correct: 1 },
                    { q: '測定値の読み取りで重要なのはどれですか？', options: ['だいたいの値でよい', '目盛りを正しく読み、記録する', '単位は書かなくてよい', '小数点以下は不要'], correct: 1 },
                    { q: 'ノギスの「本尺」と「副尺」を読む目的はどれですか？', options: ['装飾', '内径・外径・深さなどを正確に読むため', '重さを測るため', '時間を測るため'], correct: 1 },
                    { q: '測定前に機器の「ゼロ点」を確認する主な理由はどれですか？', options: ['不要', '誤差を防ぎ、正確な値を得るため', '見た目だけ', '保管のため'], correct: 1 },
                    { q: 'マイクロメーターで「シンブル」を回して測定するとき、注意すべきことはどれですか？', options: ['強く締め付ける', '規定のトルク感で接触させ、過度な締め付けを避ける', '回さない', '逆回転のみ'], correct: 1 }
                ]},
                '6': { title: '第6回 ガソリン・ジーゼルエンジン基礎', questions: [
                    { q: '4サイクルエンジンの行程の順序として正しいのはどれですか？', options: ['排気→燃焼→圧縮→吸気', '吸気→圧縮→燃焼（膨張）→排気', '燃焼→吸気→排気→圧縮', '圧縮→吸気→排気→燃焼'], correct: 1 },
                    { q: 'ディーゼルエンジンとガソリンエンジンの主な違いはどれですか？', options: ['燃料が同じ', 'ディーゼルは圧縮着火、ガソリンは火花点火', '排気量のみ違う', '気筒数だけ違う'], correct: 1 },
                    { q: 'コンロッドの大端部でクランクピンと接触し、往復運動を回転運動に変える部品はどれですか？', options: ['コンロッドベアリング', 'ピストンリング', 'バルブ', 'カムシャフト'], correct: 0 },
                    { q: 'コンロッドベアリングのフリクションロスを小さくするため、油膜の厚さはどうあるべきですか？', options: ['できるだけ厚くする', 'できるだけ薄くする', '厚さは関係ない', '一定に保つ'], correct: 1 },
                    { q: 'スキッシュ・エリアとは何ですか？', options: ['吸気ポート', '燃焼室内でピストンとシリンダーヘッドが最も接近する領域', '排気マニホールド', '冷却水経路'], correct: 1 },
                    { q: 'エンジンの性能向上のための改善として誤っているものはどれですか？', options: ['燃焼室容積の拡大', '吸気抵抗の低減', '排気抵抗の低減', '機械的損失の低減'], correct: 0 },
                    { q: '可変バルブ機構の目的として誤っているものはどれですか？', options: ['吸入空気量の増加', '燃焼室のコンパクト化', '回転数に応じた吸入効率の向上', 'クランクシャフトの軽量化'], correct: 3 },
                    { q: '過給機の使用で得られる効果として正しいのはどれですか？', options: ['出力向上、燃費悪化', '出力向上、燃費向上', '出力低下、燃費向上', '出力低下、燃費悪化'], correct: 1 },
                    { q: '潤滑装置のオイルポンプの油圧は何で決まりますか？', options: ['気温', '回転数', '湿度', '気圧'], correct: 1 },
                    { q: 'スパークプラグの熱価について、冷間型と熱間型の違いは何ですか？', options: ['色の違い', '放熱の良さの違い', '価格の違い', '長さの違い'], correct: 1 },
                    { q: '直列4気筒エンジンの一般的な点火順序はどれですか？', options: ['1-2-3-4', '1-4-2-3 又は 1-3-4-2', '1-1-1-1', '4-3-2-1'], correct: 1 },
                    { q: 'NOxの低減方法として不適切なものはどれですか？', options: ['EGRの使用', '理論空燃比より濃い空燃比で燃焼させる', '希薄燃焼', '三元触媒の使用'], correct: 1 },
                    { q: 'カムシャフトの主な役割はどれですか？', options: ['クランクシャフトを回す', '吸排気バルブの開閉タイミングを制御する', 'オイルを送るだけ', '冷却水を循環させる'], correct: 1 },
                    { q: 'ピストンリングの主な役割でないものはどれですか？', options: ['気密の保持', 'オイルのコントロール', '燃焼室の冷却', 'エアコン制御'], correct: 3 },
                    { q: 'エンジンオイルの主な役割で適切なのはどれですか？', options: ['燃料として燃やす', '潤滑・密封・冷却・洗浄など', 'ブレーキに使う', 'ワイパーに使う'], correct: 1 },
                    { q: '「上死点」「下死点」とは何を基準にした位置ですか？', options: ['ブレーキペダル', 'ピストンがシリンダー内で最も上・下にある位置', 'タイヤ', 'ハンドル'], correct: 1 }
                ]},
                '7': { title: '第7回 動力伝達装置（AT／CVT／MT）', questions: [
                    { q: 'MT（マニュアルトランスミッション）でクラッチの役割はどれですか？', options: ['エンジンと変速機の動力の断続', 'ブレーキのみ', 'ステアリングのみ', '冷却のみ'], correct: 0 },
                    { q: 'トルクコンバータで速度比がゼロのときのトルク比（ストール・トルク比）はどうなりますか？', options: ['最小', '最大', 'ゼロ', '1'], correct: 1 },
                    { q: 'トルクコンバータでクラッチ・ポイントを超えたカップリング・レンジでは、トルク比はいくつになりますか？', options: ['2〜2.5', '最大', '0', '1'], correct: 3 },
                    { q: 'スプラグ式のワンウェイ・クラッチの働きとして正しいのはどれですか？', options: ['両方向に動力伝達', '一定の回転方向にだけ動力が伝えられる', '常にロック状態', '動力伝達しない'], correct: 1 },
                    { q: 'CVTのスチール・ベルトによる動力伝達の方式として正しいのはどれですか？', options: ['ゴムベルト同様の引張り作用', 'エレメントの圧縮作用（押し出し）によって動力が伝達される', '歯車のみで伝達', '油圧のみ'], correct: 1 },
                    { q: 'クラッチの伝達トルク容量がエンジンのトルクに比べて過小であると、どうなりますか？', options: ['操作が難しくなる', 'クラッチ・フェーシングの摩耗量が急増しやすい', 'エンストしやすい', '問題なし'], correct: 1 },
                    { q: 'CVTの特徴として適切なのはどれですか？', options: ['段数が固定', '無段階で変速比が連続的に変化する', 'MTと同じ構造', '電気のみで駆動'], correct: 1 },
                    { q: 'トルクコンバータは主にどの変速機で使われますか？', options: ['MTのみ', 'AT（オートマチックトランスミッション）', 'CVTには不要', 'デフのみ'], correct: 1 },
                    { q: 'デファレンシャル（差動装置）の主な役割はどれですか？', options: ['エンジンを冷やす', '左右の駆動輪に動力を伝え、曲線走行時の回転差を吸収する', 'ブレーキのみ', 'ステアリングのみ'], correct: 1 },
                    { q: 'ATフルードの役割で適切でないものはどれですか？', options: ['トルク伝達', '潤滑', '冷却', 'エンジン燃料としての燃焼'], correct: 3 },
                    { q: '変速比が「大きい」場合、一般的にどのような効果がありますか？', options: ['トルクが増幅され、登坂・発進に有利', 'トルクは減るだけ', '燃費のみ向上', '関係ない'], correct: 0 }
                ]},
                '8': { title: '第8回 制動装置（ブレーキ・ABS）', questions: [
                    { q: 'ブレーキの主な役割として正しいのはどれですか？', options: ['熱エネルギを運動エネルギに変える', '運動エネルギを熱エネルギに変えて制動する', 'エンジン出力を上げる', '燃費を向上させる'], correct: 1 },
                    { q: 'ABSは制動力とコーナリング・フォースの両方を確保するため、タイヤのスリップ率をどの程度に制御しますか？', options: ['50%前後', '100%', '5%前後', '20%前後'], correct: 3 },
                    { q: 'ブレーキのフェード現象とは何ですか？', options: ['ブレーキ液の沸騰による気泡', 'パッドやライニングの過熱により摩擦係数が一時的に低下すること', '配管内のエア抜き不完全', '水分量増加'], correct: 1 },
                    { q: 'ディスクブレーキとドラムブレーキの放熱効果の比較で正しいのはどれですか？', options: ['同じ', 'ディスクの方が放熱効果がよい', 'ドラムの方がよい', '関係ない'], correct: 1 },
                    { q: 'ブレーキ液の沸点は水分量が多いとどうなりますか？', options: ['上昇する', '低下する', '変わらない', '無関係'], correct: 1 },
                    { q: 'ABSの電子制御機構に断線などの故障が発生した場合、どうなりますか？', options: ['ABSが継続作動する', 'ABS制御は作動せず、通常のブレーキとして制動する', 'ブレーキが全く効かなくなる', 'エンジンが止まる'], correct: 1 },
                    { q: 'ディスクブレーキの主な構成部品でないものはどれですか？', options: ['ブレーキパッド', 'ブレーキローター', 'キャリパー', 'ドラム'], correct: 3 },
                    { q: 'ABS（アンチロックブレーキシステム）の主な目的はどれですか？', options: ['燃費向上', '制動時に車輪のロックを防ぎ、操舵性を保つ', 'エンジン出力向上', '乗り心地のみ改善'], correct: 1 },
                    { q: 'ブレーキパッドの摩耗限界の確認で正しいのはどれですか？', options: ['外観のみで判断', '規定の厚さ（リミット）を下回ったら交換', '音がすれば交換', '色が変われば交換'], correct: 1 },
                    { q: 'ブレーキフルードを長期間交換しないと、どのような問題が起きやすいですか？', options: ['問題なし', '吸湿による沸点低下や劣化', '冷たくなるだけ', '色が変わるだけ'], correct: 1 },
                    { q: 'パーキングブレーキ（駐車ブレーキ）の主な目的はどれですか？', options: ['走行中の制動', '駐車時の車両の保持', 'エンジン始動', 'ウィンカー'], correct: 1 },
                    { q: 'ブレーキの「ベーパーロック」とはどのような現象ですか？', options: ['パッドが固くなる', 'フルード内に気泡が生じ、制動力が低下する', 'ローターが光る', 'キャリパーが外れる'], correct: 1 }
                ]},
                '9': { title: '第9回 シリンダーヘッド分解・組立・トルク管理', questions: [
                    { q: 'エンジン分解・組立でトルク管理が重要な理由はどれですか？', options: ['見た目を整えるため', 'ボルトの締め付け力の均一化とガスケットの密封性確保', '作業時間の短縮のため', '工具の摩耗防止'], correct: 1 },
                    { q: 'ヘッドボルトの締め付けで一般的に重要なのはどれですか？', options: ['順番は関係ない', '規定トルクと締め付け順序の遵守', '力任せに締める', '一度だけ締める'], correct: 1 },
                    { q: 'クラッシュ・ハイトが大き過ぎると、ベアリングにどうなりますか？', options: ['密着が良くなる', 'たわみが生じて局部的に荷重が掛かり、早期疲労・破損の原因となる', '焼き付きを起こす', '変化なし'], correct: 1 },
                    { q: 'ガスケットの役割として適切なのはどれですか？', options: ['装飾のみ', '接合面の密封、液・ガスの漏れ防止', '強度向上のみ', '断熱のみ'], correct: 1 },
                    { q: 'トルクレンチを使う主な目的はどれですか？', options: ['速く締める', '規定の締め付けトルクに合わせる', '緩めるだけ', '見た目を整える'], correct: 1 },
                    { q: 'ヘッドガスケットが損傷すると、どのような不具合が起きやすいですか？', options: ['タイヤが磨耗する', '冷却水とオイルの混入、圧縮漏れなど', 'ブレーキが利かなくなる', 'ライトが点かない'], correct: 1 },
                    { q: '締め付け順序を「対角線」や「メーカー指定順」で行う主な理由はどれですか？', options: ['見た目', 'ヘッドの変形を防ぎ、均一に締めるため', '時間短縮', '工具の都合'], correct: 1 }
                ]},
                '10': { title: '第10回 ブレーキキャリパーO/Hと足回り', questions: [
                    { q: 'タイヤのエア圧が過小の場合、どのような摩耗になりやすいですか？', options: ['中央摩耗', '両肩摩耗', '局部摩耗', '段差摩耗'], correct: 1 },
                    { q: 'タイヤのエア圧が過大の場合、どのような摩耗になりやすいですか？', options: ['中央摩耗', '両肩摩耗', '局部摩耗', '段差摩耗'], correct: 0 },
                    { q: 'タイヤのトレッド部が全周にわたってピット状（くぼみ状）に摩耗する主な原因はどれですか？', options: ['エア圧過大', 'ホイール・バランスの不良', '左右フロントホイールの切れ角不良', 'エア圧過小'], correct: 1 },
                    { q: 'タイヤの偏平率を小さくすると、一般的にどうなりますか？', options: ['横剛性が低くなる', '横剛性が高くなり車両の旋回性能が向上する', '縦剛性のみ変化', '変化なし'], correct: 1 },
                    { q: 'ダイナミック・アンバランスとは、タイヤ（ホイール付き）をゆっくり回転させるとどうなることをいいますか？', options: ['軽い部分が下になる', '重い部分が下になって止まる', '回転しない', '変形する'], correct: 1 },
                    { q: 'ロール・センタの位置は、一般に車軸懸架式と独立懸架式のどちらが高くなりますか？', options: ['独立懸架式の方が高い', '車軸懸架式の方が高い', '同じ', '関係ない'], correct: 1 },
                    { q: 'ブレーキキャリパーのO/H（オーバーホール）で行う作業でないものはどれですか？', options: ['分解・清掃・点検・組立', 'パッド・ピストンシールの交換', 'エンジンオイル交換', '規定トルクでの締め付け'], correct: 2 },
                    { q: '足回りの点検で重要なのはどれですか？', options: ['塗装の色のみ', 'ボールジョイント・ブッシュのガタ、タイロッドの状態', 'ホーン音のみ', 'ウィンカー球の色'], correct: 1 },
                    { q: 'ドライブシャフトの点検で重要なのはどれですか？', options: ['塗装の剥がれ', '等速ジョイントのガタ、異音', '長さの寸法のみ', '色の変化'], correct: 1 },
                    { q: 'サスペンションの「ブッシュ」が劣化すると、どのような症状が出やすいですか？', options: ['エンジンがかからない', 'ガタつき・異音・操縦安定性の低下', 'ライトが点かない', 'エアコンが効かない'], correct: 1 },
                    { q: 'タイロッドエンドのガタがあると、どのような影響がありますか？', options: ['燃費のみ', 'ステアリングの遊び・直進安定性の低下', 'ブレーキのみ', 'エンジン出力のみ'], correct: 1 },
                    { q: 'キャリパーのピストンシールを交換する主な理由はどれですか？', options: ['見た目', '油漏れ防止とブレーキ性能の維持', '軽量化', 'コスト削減のみ'], correct: 1 }
                ]},
                '11': { title: '第11回 電気回路の基礎と回路図読解', questions: [
                    { q: 'オームの法則で、電圧V・電流I・抵抗Rの関係として正しいのはどれですか？', options: ['V = I × R', 'V = I ÷ R', 'R = V + I', 'I = V - R'], correct: 0 },
                    { q: 'NAND回路はどの回路の組み合わせですか？', options: ['AND回路にNOR回路を接続', 'AND回路にNOT回路を接続', 'OR回路にAND回路を接続', 'NOR回路にNOT回路を接続'], correct: 1 },
                    { q: 'NOR回路はどの回路の組み合わせですか？', options: ['AND回路にNOT回路を接続', 'OR回路にNOT回路を接続', 'NAND回路にOR回路を接続', 'AND回路にOR回路を接続'], correct: 1 },
                    { q: 'NAND回路で、二つの入力がともに"1"のとき、出力はどうなりますか？', options: ['"1"', '"0"', '不定', '高インピーダンス'], correct: 1 },
                    { q: 'ダイオードの主な特性として正しいのはどれですか？', options: ['両方向に電流を流す', '一方向にしか電流を流さない', '交流のみ流す', '直流を通さない'], correct: 1 },
                    { q: 'CR発振器とは何を利用して発振周期を決めますか？', options: ['コイルとコンデンサの共振', '抵抗とコンデンサを使った放電時間', 'トランスのみ', 'ダイオードのみ'], correct: 1 },
                    { q: '直列回路で、抵抗が2つあるときの合成抵抗はどうなりますか？', options: ['小さくなる', '各抵抗の和になる', '半分になる', 'ゼロになる'], correct: 1 },
                    { q: '回路図で「GND」や「アース」が示すのはどれですか？', options: ['電源の正極', '基準電位（0Vの共通線）', 'スイッチのみ', 'モーターのみ'], correct: 1 },
                    { q: '並列回路で、抵抗が2つあるときの合成抵抗はどうなりますか？', options: ['各抵抗の和', '各抵抗の逆数の和の逆数（小さくなる）', '大きい方だけ', 'ゼロ'], correct: 1 },
                    { q: '直流（DC）と交流（AC）の違いとして正しいのはどれですか？', options: ['同じ', 'DCは向きが一定、ACは向きが周期的に変わる', 'ACのみ車で使う', 'DCは使わない'], correct: 1 },
                    { q: 'サーキットテスターで「導通」を測る目的はどれですか？', options: ['電圧のみ', '断線・接続不良の確認', '温度測定', '速度測定'], correct: 1 }
                ]},
                '12': { title: '第12回 バッテリー・始動・充電系', questions: [
                    { q: '鉛バッテリで、放電電流が大きいほど容量はどうなりますか？', options: ['大きくなる', '小さくなる', '変わらない', 'ゼロになる'], correct: 1 },
                    { q: '鉛バッテリの起電力は、電解液温度が1℃上昇するとどの程度変化しますか？', options: ['0.0002〜0.0003V程度低くなる', '0.0002〜0.0003V程度高くなる', '0.01V以上変わる', '変化しない'], correct: 1 },
                    { q: '直巻式スタータで、ピニオン・ギヤの回転速度がゼロのとき、アーマチュア・コイルに流れる電流はどうなりますか？', options: ['最小', '最大', 'ゼロ', '一定'], correct: 1 },
                    { q: 'スタータの駆動トルクは、ピニオン・ギヤの回転速度がゼロのときどうなりますか？', options: ['最小', '最大', 'ゼロ', '一定'], correct: 1 },
                    { q: 'オルタネータでB端子が外れたとき、IC内の制御回路が異常を検出する条件として正しいのはどれですか？', options: ['B端子の電圧がS端子より規定値より低いとき', 'B端子の電圧がS端子より規定値より高いとき', '回転が止まったとき', 'バッテリが満充電のとき'], correct: 1 },
                    { q: 'バッテリーの液比重が低い場合、どのような状態が考えられますか？', options: ['充電過多', '放電または劣化', '端子のゆるみのみ', '問題なし'], correct: 1 },
                    { q: 'スターターモーターの主な役割はどれですか？', options: ['エンジンを始動する', 'エアコンを動かす', 'ライトを点けるのみ', 'ワイパーを動かす'], correct: 0 },
                    { q: 'オルタネーター（交流発電機）の主な役割はどれですか？', options: ['エンジンをかける', '走行中にバッテリーへ充電し、電装品に電力を供給する', 'ブレーキのみ', 'ステアリングのみ'], correct: 1 },
                    { q: 'バッテリーの「CCA」とは何を表しますか？', options: ['容量（Ah）', '冷間始動電流（Cold Cranking Amps）', '電圧のみ', '重量'], correct: 1 },
                    { q: '充電電圧が異常に高い場合、考えられる原因はどれですか？', options: ['バッテリーのみ', 'レギュレータ（電圧調整）の不具合など', 'ライトの球切れ', 'ワイパー故障'], correct: 1 },
                    { q: 'スターターが「カチカチ」と音だけして回らない場合、考えやすい原因はどれですか？', options: ['エンジンオイル不足', 'バッテリー上がり・接続不良・スターター不良など', 'ブレーキの磨耗', 'タイヤの空気圧'], correct: 1 }
                ]},
                '13': { title: '第13回 センサー・アクチュエーターとCAN通信', questions: [
                    { q: 'バキューム・センサ（インテークマニホールド圧力センサ）で、圧力が高くなると出力電圧はどうなりますか？', options: ['小さくなる', '大きくなる', '変わらない', 'ゼロになる'], correct: 1 },
                    { q: 'ジルコニア式O2センサのジルコニア素子は、高温で内外面の酸素濃度差がどうなると起電力を発生しますか？', options: ['小さいとき', '大きいとき', '同じとき', '関係ない'], correct: 1 },
                    { q: '空燃比センサの出力で、理論空燃比より濃い（小さい）ときはどうなりますか？', options: ['高くなる', '低くなる', 'ゼロ', '一定'], correct: 1 },
                    { q: 'カム角センサの主な役割はどれですか？', options: ['水温を検出', 'クランク角（位相）・気筒判別の検出', '速度のみ検出', '油圧のみ検出'], correct: 1 },
                    { q: 'スロットル・ポジション・センサは何を検出しますか？', options: ['アクセル・ペダルの踏み込み角度', 'スロットル・バルブの開度', 'ブレーキペダルの踏み込み', 'クラッチの踏み込み'], correct: 1 },
                    { q: 'ホール効果を用いて検出を行うセンサはどれですか？', options: ['水温センサ', 'ホール素子式スロットル・ポジション・センサ', 'O2センサ', '油温センサ'], correct: 1 },
                    { q: 'エンジン制御でよく使うセンサーでないものはどれですか？', options: ['クランク角センサー', 'O2センサー（酸素センサー）', '水温センサー', 'タイヤの空気圧のみを測るセンサー（一部車両除く）'], correct: 3 },
                    { q: 'CAN（Controller Area Network）の主な役割はどれですか？', options: ['エンジンオイルを送る', 'ECU間でデジタルデータをやり取りする', '冷却水を流す', 'ブレーキ油を送る'], correct: 1 },
                    { q: 'アクチュエーターの例として適切なのはどれですか？', options: ['燃料ポンプ、インジェクター、アイドル制御弁など', 'センサーのみ', 'バッテリーのみ', 'タイヤのみ'], correct: 0 },
                    { q: 'O2センサー（酸素センサー）の主な役割はどれですか？', options: ['タイヤ空気圧', '排気中の酸素濃度を検知し、燃焼制御に用いる', '水温のみ', '速度のみ'], correct: 1 },
                    { q: 'ECU（Electronic Control Unit）とは何ですか？', options: ['エンジンオイル', '電子制御ユニット（コンピュータ）', 'ブレーキパッド', 'バッテリー'], correct: 1 },
                    { q: 'スロットルポジションセンサーの役割はどれですか？', options: ['ブレーキペダルの位置', 'スロットル開度を検知し、エンジン制御に用いる', 'ドアの開閉', 'ウィンカーの位置'], correct: 1 }
                ]},
                '14': { title: '第14回 故障診断（OBD・スキャンツール実習）', questions: [
                    { q: 'アクティブ・テストとは何ですか？', options: ['ECUの学習値を初期化する機能', '外部診断器からECUに指令を出し、アクチュエータを任意に駆動・停止して機能点検を行う', 'コードを消すだけ', 'バッテリーを外す機能'], correct: 1 },
                    { q: '「作業サポート」とはどのような機能ですか？', options: ['アクチュエータを任意に駆動する', '整備作業の補助やECUの学習値を初期化する', 'DTCのみ消去する', 'フリーズフレームを表示する'], correct: 1 },
                    { q: '外部診断器でダイアグノーシス・コードの消去を行うと、時計やラジオの再設定は必要ですか？', options: ['必要', '必要ない（コードとフリーズフレーム・データのみ消去）', '時計のみ必要', 'ラジオのみ必要'], correct: 1 },
                    { q: 'OBD（On-Board Diagnostics）を用いる主な目的はどれですか？', options: ['燃費の表示のみ', '故障コード（DTC）の読取と原因の切り分け', 'エンターテイメントの操作', 'ナビの更新'], correct: 1 },
                    { q: 'DTCを消したあと、再発する場合に考えるべきことはどれですか？', options: ['コードを消すだけ', '原因（部品・配線・接触不良など）を特定し、修理する', 'バッテリーを外すだけ', '何もしない'], correct: 1 },
                    { q: '診断フローで重要なのはどれですか？', options: ['当てずっぽうで部品交換', 'コード・データを確認し、系統的に切り分ける', '工具を使わない', '記録を残さない'], correct: 1 },
                    { q: 'OBD2のDLC（診断用コネクタ）は、多くの車両でどこにありますか？', options: ['トランク内', 'ダッシュボード付近（運転席側など）', 'エンジンルームの奥のみ', 'タイヤ室内'], correct: 1 },
                    { q: '「フリーズフレームデータ」とは何を指しますか？', options: ['動画', 'DTCが記録されたときの運転状態などのスナップショット', '冷凍庫のデータ', '音声データ'], correct: 1 },
                    { q: 'スキャンツールで「ライブデータ」を確認する主な目的はどれですか？', options: ['過去の記録のみ', 'センサー値・作動状態をリアルタイムで確認し、診断に役立てる', 'コードを消すだけ', '地図表示'], correct: 1 }
                ]},
                '15': { title: '第15回 特定技能2号試験対策（学科・実技の要点）', questions: [
                    { q: '特定技能2号「自動車整備」の学科試験で必要な得点率の目安はどれですか？', options: ['50%以上', '60%以上', '65%以上', 'おおよそ70%以上'], correct: 3 },
                    { q: '実技試験（故障診断）で重視される能力として適切でないものはどれですか？', options: ['系統的な切り分け', '測定・診断機器の正しい使用', '見た目のスピードのみで評価', '手順と記録の正確さ'], correct: 2 },
                    { q: '試験で必要な日本語能力の目安はどれですか？', options: ['不要', 'N5程度', 'N3程度など、業務に必要なレベル', '英語のみ'], correct: 2 },
                    { q: '特定技能2号の在留期間について正しいのはどれですか？', options: ['最長1年のみ', '更新により在留を継続できる場合がある', '一度も更新できない', '在留資格ではない'], correct: 1 },
                    { q: '実技試験で「記録」が重視される主な理由はどれですか？', options: ['見た目だけ', '診断の過程と根拠を残し、再現性・説明力を示すため', '時間稼ぎ', '不要'], correct: 1 },
                    { q: '学科試験の勉強で有効なのはどれですか？', options: ['一夜漬けのみ', '過去問の傾向把握と、用語・法令・構造の理解', '実技だけ', '日本語を無視'], correct: 1 },
                    { q: '整備士2級試験の学科で出題される分野として適切なのはどれですか？', options: ['エンジン、動力伝達、電気、制動、足回りなど', '数学のみ', '英語のみ', '音楽のみ'], correct: 0 }
                ]},
                '16': { title: '第16回 総合演習・模擬試験', questions: [
                    { q: '本番の試験で時間配分で心がけるべきことはどれですか？', options: ['最初の問題に全時間を使う', '全体の時間を意識し、解ける問題から確実に解く', '最後の1問だけ丁寧に解く', '時間は見ない'], correct: 1 },
                    { q: '模擬試験の主な目的でないものはどれですか？', options: ['本番に近い環境で慣れる', '弱点を把握し復習する', '時間配分の練習', '合格を保証する'], correct: 3 },
                    { q: '学科と実技の両方で共通して重要なのはどれですか？', options: ['速さだけ', '正確な理解と手順、記録の正確さ', '見た目だけ', '運だけ'], correct: 1 },
                    { q: '模擬試験の結果を本番に活かすために有効なのはどれですか？', options: ['結果を忘れる', '間違えた問題を復習し、苦手分野を補強する', '見直さない', '実技だけ練習'], correct: 1 },
                    { q: '試験当日に心がけることで適切でないものはどれですか？', options: ['持ち物・会場・時間の確認', '体調管理と適度な休息', '一夜で全範囲を詰め込む', '落ち着いて問題を読む'], correct: 2 },
                    { q: '実技の「故障診断」で、最初に確認すべきこととして適切なのはどれですか？', options: ['部品をすぐ交換する', '症状の確認・コード・データの確認など、手順に沿った切り分け', '工具を全部出す', '記録を書かない'], correct: 1 },
                    { q: '「適切なもの」「不適切なもの」を問う問題で注意すべきことはどれですか？', options: ['「適切」を問われているか「不適切」を問われているかを見落とさない', '全部同じ答えでよい', '最初の選択肢だけ見る', '時間をかけずに適当に選ぶ'], correct: 0 }
                ]}
            };

            var curriculumId = (function() {
                var p = new URLSearchParams(window.location.search);
                var c = p.get('c');
                if (c === null || c === '') return '1';
                var n = parseInt(c, 10);
                if (n >= 1 && n <= 16) return String(n);
                return '1';
            })();

            var quizData = curriculumMeta[curriculumId].questions;
            var curriculumTitle = curriculumMeta[curriculumId].title;
            var state = { current: 0, answered: 0, correct: 0, answers: [] };
            var userEmail = (sessionStorage.getItem('wbt_user_email') || 'guest').replace(/[^a-zA-Z0-9@._-]/g, '_');
            var storageKey = 'wbt_quiz_state_' + userEmail + '_' + curriculumId;

            function persistState() {
                try {
                    sessionStorage.setItem(storageKey, JSON.stringify(state));
                } catch (e) {}
            }

            function loadState() {
                try {
                    var s = sessionStorage.getItem(storageKey);
                    if (s) state = JSON.parse(s);
                } catch (e) {}
            }

            function saveMemberResult() {
                try {
                    var payload = { answered: state.answered, correct: state.correct, total: quizData.length };
                    localStorage.setItem('wbt_quiz_' + userEmail + '_' + curriculumId, JSON.stringify(payload));
                } catch (e) {}
            }

            function updateProgress() {
                var total = quizData.length;
                var done = state.answered;
                var pct = total ? Math.round((done / total) * 100) : 0;
                document.getElementById('totalCount').textContent = total;
                document.getElementById('progressCount').textContent = done;
                document.getElementById('progressBar').style.width = pct + '%';
                var correctRate = state.answered ? Math.round((state.correct / state.answered) * 100) : 0;
                var accEl = document.getElementById('accuracyPercent');
                var subEl = document.getElementById('accuracySub');
                if (state.answered) {
                    accEl.textContent = correctRate;
                    accEl.className = 'text-2xl font-bold font-mono ' + (correctRate >= 70 ? 'text-green-400' : correctRate >= 50 ? 'text-brand-orange' : 'text-red-400');
                    subEl.textContent = state.correct + ' / ' + state.answered + ' 問正解';
                } else {
                    accEl.textContent = '—';
                    subEl.textContent = '回答後に表示';
                }
            }

            function showQuestion() {
                if (state.current >= quizData.length) {
                    document.getElementById('quizArea').classList.add('hidden');
                    document.getElementById('completeArea').classList.remove('hidden');
                    document.getElementById('finalAccuracy').textContent = state.answered ? Math.round((state.correct / state.answered) * 100) : 0;
                    saveMemberResult();
                    sessionStorage.removeItem(storageKey);
                    return;
                }
                var item = quizData[state.current];
                document.getElementById('currentNum').textContent = state.current + 1;
                document.getElementById('questionText').textContent = item.q;
                document.getElementById('optionsList').innerHTML = '';
                document.getElementById('feedbackArea').classList.add('hidden');
                item.options.forEach(function(opt, i) {
                    var div = document.createElement('button');
                    div.type = 'button';
                    div.className = 'quiz-option w-full text-left p-4 rounded-xl border-2 border-slate-600 bg-slate-800/50 text-white font-medium';
                    div.dataset.index = i;
                    div.textContent = opt;
                    div.addEventListener('click', function() { onSelect(div, i, item.correct); });
                    document.getElementById('optionsList').appendChild(div);
                });
                updateProgress();
            }

            function onSelect(clickedEl, chosenIndex, correctIndex) {
                var list = document.getElementById('optionsList');
                var opts = list.querySelectorAll('.quiz-option');
                opts.forEach(function(o) { o.classList.add('disabled'); });
                clickedEl.classList.add('selected');
                if (chosenIndex === correctIndex) {
                    clickedEl.classList.add('correct');
                    state.correct++;
                } else {
                    clickedEl.classList.add('incorrect');
                    if (opts[correctIndex]) opts[correctIndex].classList.add('correct');
                }
                state.answered++;
                state.answers[state.current] = chosenIndex === correctIndex;
                persistState();
                updateProgress();
                var fb = document.getElementById('feedbackArea');
                var msg = document.getElementById('feedbackMessage');
                if (chosenIndex === correctIndex) {
                    msg.className = 'p-4 rounded-lg text-sm font-medium bg-green-500/20 text-green-400 border border-green-500/30';
                    msg.textContent = '正解です。';
                } else {
                    msg.className = 'p-4 rounded-lg text-sm font-medium bg-red-500/20 text-red-400 border border-red-500/30';
                    msg.textContent = '不正解です。正解は「' + quizData[state.current].options[correctIndex] + '」です。';
                }
                fb.classList.remove('hidden');
            }

            document.getElementById('curriculumTitle').textContent = curriculumTitle;
            document.getElementById('nextBtn').addEventListener('click', function() {
                state.current++;
                showQuestion();
            });
            document.getElementById('retryBtn').addEventListener('click', function() {
                state = { current: 0, answered: 0, correct: 0, answers: [] };
                sessionStorage.removeItem(storageKey);
                document.getElementById('completeArea').classList.add('hidden');
                document.getElementById('quizArea').classList.remove('hidden');
                showQuestion();
            });

            loadState();
            showQuestion();
        })();
    </script>
</body>
</html>
