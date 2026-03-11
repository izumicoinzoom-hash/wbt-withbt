<!DOCTYPE html>
<html lang="ja" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登録完了 | WithBrightTomorrow</title>

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
        .bg-grid { background-size: 40px 40px; opacity: 0.1; }
    </style>
</head>
<body class="antialiased selection:bg-brand-orange selection:text-white flex flex-col min-h-screen">

    <header class="w-full py-4 lg:py-6 absolute top-0 z-20 bg-brand-navy/90 backdrop-blur-sm border-b border-slate-700/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center">
            <a href="../index.html" class="flex items-center space-x-2 hover:opacity-80 transition">
                <div class="bg-brand-orange p-1 rounded text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                </div>
                <span class="font-sans font-bold text-xl text-white">WithBrightTomorrow</span>
            </a>
            <a href="../index.html" class="text-sm text-slate-400 hover:text-white transition flex items-center gap-1 border border-slate-600 rounded px-3 py-1">TOPへ</a>
        </div>
    </header>

    <main class="flex-grow flex items-center justify-center relative pt-32 pb-12">
        <div class="absolute inset-0 bg-grid-pattern bg-grid z-0"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-brand-navy/50 to-brand-navy z-0"></div>

        <div class="relative z-10 w-full max-w-md px-4">
            <div class="bg-slate-800/80 backdrop-blur-md border border-slate-700 rounded-2xl shadow-2xl overflow-hidden">
                <div class="h-2 bg-green-500 w-full"></div>
                <div class="p-8 md:p-10 text-center">

                    <div class="mx-auto w-16 h-16 bg-green-500/20 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>

                    <h1 class="text-2xl font-bold text-white mb-3">登録完了！</h1>
                    <p class="text-slate-300 text-sm leading-relaxed mb-6">
                        お支払いが正常に処理されました。<br>
                        ご登録のメールアドレスに<strong class="text-white">ログイン情報</strong>をお送りしました。<br>
                        メールをご確認のうえ、ログインしてください。
                    </p>

                    <div class="bg-slate-900/60 border border-slate-600 rounded-lg p-4 mb-6 text-left">
                        <p class="text-xs text-slate-400 mb-2">次のステップ</p>
                        <ol class="text-sm text-slate-300 space-y-2 list-decimal list-inside">
                            <li>メールボックスを確認する</li>
                            <li>記載されたパスワードでログインする</li>
                            <li>学習を始める</li>
                        </ol>
                    </div>

                    <a href="../login.html"
                       class="inline-block w-full bg-brand-orange text-white font-bold py-4 rounded-lg shadow-lg hover:bg-orange-600 transition-all transform hover:scale-[1.02] active:scale-95">
                        ログインページへ
                    </a>

                    <p class="mt-4 text-xs text-slate-500">
                        メールが届かない場合は迷惑メールフォルダもご確認ください。
                    </p>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-brand-dark py-6 border-t border-slate-800 relative z-10">
        <div class="max-w-7xl mx-auto px-4 text-center text-slate-500 text-xs">&copy; 2026 WithBrightTomorrow Inc.</div>
    </footer>

</body>
</html>
