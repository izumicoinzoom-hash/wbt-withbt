<?php require_once __DIR__ . '/auth_check.php'; ?>
<!DOCTYPE html>
<html lang="ja" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>学習 | WithBrightTomorrow</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;700&family=Noto+Sans+JP:wght@400;500;700;900&family=Oswald:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { brand: { navy: '#0F172A', dark: '#020617', orange: '#EA580C', red: '#DC2626', slate: '#F1F5F9' } },
                    fontFamily: { sans: ['"Noto Sans JP"', 'sans-serif'], mono: ['"JetBrains Mono"', 'monospace'], display: ['"Oswald"', 'sans-serif'] },
                    backgroundImage: { 'grid-pattern': "radial-gradient(circle, #334155 1px, transparent 1px)" }
                }
            }
        }
    </script>
    <style>
        body { background-color: #0F172A; color: #F8FAFC; }
        .bg-grid { background-size: 40px 40px; opacity: 0.08; }
        .card-link { transition: all 0.2s; }
        .card-link:hover { border-color: #EA580C; transform: translateY(-2px); box-shadow: 0 10px 40px -10px rgba(234, 88, 12, 0.25); }
    </style>
</head>
<body class="antialiased selection:bg-brand-orange selection:text-white flex flex-col min-h-screen">

    <header class="w-full py-4 border-b border-slate-700/50 bg-brand-navy/95 backdrop-blur-sm sticky top-0 z-30">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 flex justify-between items-center">
            <a href="index.html" class="flex items-center space-x-2 hover:opacity-80 transition">
                <div class="bg-brand-orange p-1 rounded text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                </div>
                <span class="font-sans font-bold text-lg text-white">学習</span>
            </a>
            <a href="logout.php" class="text-slate-400 hover:text-white text-sm">ログアウト</a>
        </div>
    </header>

    <main class="flex-grow relative pt-8 pb-16">
        <div class="absolute inset-0 bg-grid-pattern bg-grid z-0"></div>
        <div class="max-w-5xl mx-auto px-4 sm:px-6 relative z-10">

            <div class="mb-6">
                <h1 class="text-2xl md:text-3xl font-bold text-white mb-2">カリキュラム別 問題（全16項目）</h1>
                <p class="text-slate-400 text-sm">各項目を選択して、理解度チェックのクイズに挑戦してください。</p>
            </div>

            <a href="exam-quiz.html" class="block mb-6 p-4 rounded-xl border-2 border-amber-500/50 bg-amber-500/10 hover:border-amber-400 hover:bg-amber-500/20 transition">
                <span class="text-amber-400 font-bold text-sm">特定技能2号向け</span>
                <h2 class="text-lg font-bold text-white mt-1">試験問題クイズ（解説：日本語・インドネシア語）</h2>
                <p class="text-slate-400 text-xs mt-1">過去の整備士試験問題。解説をBahasa Indonesiaで表示できます。</p>
            </a>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4" id="cardGrid">
                <a href="learning-quiz.php?c=1" class="card-link block relative bg-slate-800/80 border-2 border-slate-700 rounded-xl p-4 shadow-lg" data-c="1">
                    <div class="absolute top-3 right-3 text-right" id="cardStats1"></div>
                    <span class="inline-block bg-brand-orange text-white w-8 h-8 rounded-lg flex items-center justify-center text-xs font-mono font-bold mb-3">1</span>
                    <h2 class="text-sm font-bold text-white mb-1 leading-snug">道路運送車両法と特定整備制度</h2>
                    <span class="text-brand-orange text-xs font-bold">問題を始める →</span>
                </a>
                <a href="learning-quiz.php?c=2" class="card-link block relative bg-slate-800/80 border-2 border-slate-700 rounded-xl p-4 shadow-lg" data-c="2">
                    <div class="absolute top-3 right-3 text-right" id="cardStats2"></div>
                    <span class="inline-block bg-brand-orange text-white w-8 h-8 rounded-lg flex items-center justify-center text-xs font-mono font-bold mb-3">2</span>
                    <h2 class="text-sm font-bold text-white mb-1 leading-snug">整備主任者・責任とコンプライアンス</h2>
                    <span class="text-brand-orange text-xs font-bold">問題を始める →</span>
                </a>
                <a href="learning-quiz.php?c=3" class="card-link block relative bg-slate-800/80 border-2 border-slate-700 rounded-xl p-4 shadow-lg" data-c="3">
                    <div class="absolute top-3 right-3 text-right" id="cardStats3"></div>
                    <span class="inline-block bg-brand-orange text-white w-8 h-8 rounded-lg flex items-center justify-center text-xs font-mono font-bold mb-3">3</span>
                    <h2 class="text-sm font-bold text-white mb-1 leading-snug">専門用語（日本語）基礎</h2>
                    <span class="text-brand-orange text-xs font-bold">問題を始める →</span>
                </a>
                <a href="learning-quiz.php?c=4" class="card-link block relative bg-slate-800/80 border-2 border-slate-700 rounded-xl p-4 shadow-lg" data-c="4">
                    <div class="absolute top-3 right-3 text-right" id="cardStats4"></div>
                    <span class="inline-block bg-brand-orange text-white w-8 h-8 rounded-lg flex items-center justify-center text-xs font-mono font-bold mb-3">4</span>
                    <h2 class="text-sm font-bold text-white mb-1 leading-snug">法定点検（12ヶ月・24ヶ月）の手順</h2>
                    <span class="text-brand-orange text-xs font-bold">問題を始める →</span>
                </a>
                <a href="learning-quiz.php?c=5" class="card-link block relative bg-slate-800/80 border-2 border-slate-700 rounded-xl p-4 shadow-lg" data-c="5">
                    <div class="absolute top-3 right-3 text-right" id="cardStats5"></div>
                    <span class="inline-block bg-brand-orange text-white w-8 h-8 rounded-lg flex items-center justify-center text-xs font-mono font-bold mb-3">5</span>
                    <h2 class="text-sm font-bold text-white mb-1 leading-snug">測定機器の取り扱い（ノギス・マイクロメーター）</h2>
                    <span class="text-brand-orange text-xs font-bold">問題を始める →</span>
                </a>
                <a href="learning-quiz.php?c=6" class="card-link block relative bg-slate-800/80 border-2 border-slate-700 rounded-xl p-4 shadow-lg" data-c="6">
                    <div class="absolute top-3 right-3 text-right" id="cardStats6"></div>
                    <span class="inline-block bg-brand-orange text-white w-8 h-8 rounded-lg flex items-center justify-center text-xs font-mono font-bold mb-3">6</span>
                    <h2 class="text-sm font-bold text-white mb-1 leading-snug">ガソリン・ジーゼルエンジン基礎</h2>
                    <span class="text-brand-orange text-xs font-bold">問題を始める →</span>
                </a>
                <a href="learning-quiz.php?c=7" class="card-link block relative bg-slate-800/80 border-2 border-slate-700 rounded-xl p-4 shadow-lg" data-c="7">
                    <div class="absolute top-3 right-3 text-right" id="cardStats7"></div>
                    <span class="inline-block bg-brand-orange text-white w-8 h-8 rounded-lg flex items-center justify-center text-xs font-mono font-bold mb-3">7</span>
                    <h2 class="text-sm font-bold text-white mb-1 leading-snug">動力伝達装置（AT／CVT／MT）</h2>
                    <span class="text-brand-orange text-xs font-bold">問題を始める →</span>
                </a>
                <a href="learning-quiz.php?c=8" class="card-link block relative bg-slate-800/80 border-2 border-slate-700 rounded-xl p-4 shadow-lg" data-c="8">
                    <div class="absolute top-3 right-3 text-right" id="cardStats8"></div>
                    <span class="inline-block bg-brand-orange text-white w-8 h-8 rounded-lg flex items-center justify-center text-xs font-mono font-bold mb-3">8</span>
                    <h2 class="text-sm font-bold text-white mb-1 leading-snug">制動装置（ブレーキ・ABS）</h2>
                    <span class="text-brand-orange text-xs font-bold">問題を始める →</span>
                </a>
                <a href="learning-quiz.php?c=9" class="card-link block relative bg-slate-800/80 border-2 border-slate-700 rounded-xl p-4 shadow-lg" data-c="9">
                    <div class="absolute top-3 right-3 text-right" id="cardStats9"></div>
                    <span class="inline-block bg-brand-orange text-white w-8 h-8 rounded-lg flex items-center justify-center text-xs font-mono font-bold mb-3">9</span>
                    <h2 class="text-sm font-bold text-white mb-1 leading-snug">シリンダーヘッド分解・組立・トルク管理</h2>
                    <span class="text-brand-orange text-xs font-bold">問題を始める →</span>
                </a>
                <a href="learning-quiz.php?c=10" class="card-link block relative bg-slate-800/80 border-2 border-slate-700 rounded-xl p-4 shadow-lg" data-c="10">
                    <div class="absolute top-3 right-3 text-right" id="cardStats10"></div>
                    <span class="inline-block bg-brand-orange text-white w-8 h-8 rounded-lg flex items-center justify-center text-xs font-mono font-bold mb-3">10</span>
                    <h2 class="text-sm font-bold text-white mb-1 leading-snug">ブレーキキャリパーO/Hと足回り</h2>
                    <span class="text-brand-orange text-xs font-bold">問題を始める →</span>
                </a>
                <a href="learning-quiz.php?c=11" class="card-link block relative bg-slate-800/80 border-2 border-slate-700 rounded-xl p-4 shadow-lg" data-c="11">
                    <div class="absolute top-3 right-3 text-right" id="cardStats11"></div>
                    <span class="inline-block bg-brand-orange text-white w-8 h-8 rounded-lg flex items-center justify-center text-xs font-mono font-bold mb-3">11</span>
                    <h2 class="text-sm font-bold text-white mb-1 leading-snug">電気回路の基礎と回路図読解</h2>
                    <span class="text-brand-orange text-xs font-bold">問題を始める →</span>
                </a>
                <a href="learning-quiz.php?c=12" class="card-link block relative bg-slate-800/80 border-2 border-slate-700 rounded-xl p-4 shadow-lg" data-c="12">
                    <div class="absolute top-3 right-3 text-right" id="cardStats12"></div>
                    <span class="inline-block bg-brand-orange text-white w-8 h-8 rounded-lg flex items-center justify-center text-xs font-mono font-bold mb-3">12</span>
                    <h2 class="text-sm font-bold text-white mb-1 leading-snug">バッテリー・始動・充電系</h2>
                    <span class="text-brand-orange text-xs font-bold">問題を始める →</span>
                </a>
                <a href="learning-quiz.php?c=13" class="card-link block relative bg-slate-800/80 border-2 border-slate-700 rounded-xl p-4 shadow-lg" data-c="13">
                    <div class="absolute top-3 right-3 text-right" id="cardStats13"></div>
                    <span class="inline-block bg-brand-orange text-white w-8 h-8 rounded-lg flex items-center justify-center text-xs font-mono font-bold mb-3">13</span>
                    <h2 class="text-sm font-bold text-white mb-1 leading-snug">センサー・アクチュエーターとCAN通信</h2>
                    <span class="text-brand-orange text-xs font-bold">問題を始める →</span>
                </a>
                <a href="learning-quiz.php?c=14" class="card-link block relative bg-slate-800/80 border-2 border-slate-700 rounded-xl p-4 shadow-lg" data-c="14">
                    <div class="absolute top-3 right-3 text-right" id="cardStats14"></div>
                    <span class="inline-block bg-brand-orange text-white w-8 h-8 rounded-lg flex items-center justify-center text-xs font-mono font-bold mb-3">14</span>
                    <h2 class="text-sm font-bold text-white mb-1 leading-snug">故障診断（OBD・スキャンツール実習）</h2>
                    <span class="text-brand-orange text-xs font-bold">問題を始める →</span>
                </a>
                <a href="learning-quiz.php?c=15" class="card-link block relative bg-slate-800/80 border-2 border-slate-700 rounded-xl p-4 shadow-lg" data-c="15">
                    <div class="absolute top-3 right-3 text-right" id="cardStats15"></div>
                    <span class="inline-block bg-brand-orange text-white w-8 h-8 rounded-lg flex items-center justify-center text-xs font-mono font-bold mb-3">15</span>
                    <h2 class="text-sm font-bold text-white mb-1 leading-snug">特定技能2号試験対策（学科・実技の要点）</h2>
                    <span class="text-brand-orange text-xs font-bold">問題を始める →</span>
                </a>
                <a href="learning-quiz.php?c=16" class="card-link block relative bg-slate-800/80 border-2 border-slate-700 rounded-xl p-4 shadow-lg" data-c="16">
                    <div class="absolute top-3 right-3 text-right" id="cardStats16"></div>
                    <span class="inline-block bg-brand-orange text-white w-8 h-8 rounded-lg flex items-center justify-center text-xs font-mono font-bold mb-3">16</span>
                    <h2 class="text-sm font-bold text-white mb-1 leading-snug">総合演習・模擬試験</h2>
                    <span class="text-brand-orange text-xs font-bold">問題を始める →</span>
                </a>
            </div>
        </div>
    </main>

    <script>
        (function() {
            var curriculumCounts = { 1:9, 2:7, 3:8, 4:7, 5:7, 6:16, 7:11, 8:12, 9:7, 10:12, 11:11, 12:11, 13:12, 14:9, 15:7, 16:7 };
            var userEmail = (sessionStorage.getItem('wbt_user_email') || 'guest').replace(/[^a-zA-Z0-9@._-]/g, '_');

            function getCardStats(c) {
                var total = curriculumCounts[c] || 1;
                var answered = 0, correct = 0;
                try {
                    var s = sessionStorage.getItem('wbt_quiz_state_' + userEmail + '_' + c);
                    if (s) {
                        var st = JSON.parse(s);
                        answered = st.answered || 0;
                        correct = st.correct || 0;
                    } else {
                        var ls = localStorage.getItem('wbt_quiz_' + userEmail + '_' + c);
                        if (ls) {
                            var d = JSON.parse(ls);
                            answered = d.answered || 0;
                            correct = d.correct || 0;
                        }
                    }
                } catch (e) {}
                var prog = total ? Math.round((answered / total) * 100) : 0;
                var acc = answered ? Math.round((correct / answered) * 100) : 0;
                return { progress: prog, accuracy: acc, answered: answered, total: total };
            }

            function updateCardStats() {
                for (var i = 1; i <= 16; i++) {
                    var el = document.getElementById('cardStats' + i);
                    if (!el) continue;
                    var st = getCardStats(i);
                    if (st.answered > 0) {
                        el.innerHTML = '<span class="text-slate-400 text-xs block">進捗 ' + st.progress + '%</span><span class="text-brand-orange font-mono font-bold text-sm">正答 ' + st.accuracy + '%</span>';
                    } else {
                        el.innerHTML = '<span class="text-slate-500 text-xs">—</span>';
                    }
                }
            }

            updateCardStats();
        })();
    </script>
</body>
</html>
