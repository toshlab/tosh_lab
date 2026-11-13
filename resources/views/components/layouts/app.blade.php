<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ isset($title) ? $title.'｜'.config('site.name') : config('site.name').'｜Laravelの業務システム開発・サーバー構築' }}</title>
    <meta name="description" content="{{ $description ?? '実務4年目のLaravelエンジニア「tosh」のポートフォリオ。Excelや手作業の業務を使いやすいシステムに。業務システム開発、既存システムの改修・移行、サーバー構築・保守、ホームページ制作のご相談を受け付けています。' }}">
    <meta property="og:site_name" content="{{ config('site.name') }}">
    <meta property="og:title" content="{{ $title ?? config('site.name') }}">
    <meta property="og:type" content="{{ isset($title) ? 'article' : 'website' }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta name="color-scheme" content="light dark">

    {{--
        テーマを描画前に決める（ちらつき防止のため、ここだけインラインで同期実行する）。
        保存した選択 → なければOSの設定。JavaScriptが無いときは CSS の @media がOSの設定に従う。
    --}}
    <script>
        (function () {
            var theme = null;
            try { theme = localStorage.getItem('theme'); } catch (e) {}
            if (theme !== 'light' && theme !== 'dark') {
                theme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            }
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;800&family=JetBrains+Mono:wght@400;500&family=Noto+Sans+JP:wght@400;500;700&display=swap">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <a href="#main" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded focus:bg-bg focus:text-ink focus:px-4 focus:py-2">本文へ移動</a>

    <header id="page-top" class="site-header">
        <div class="wrap header-inner">
            <a href="{{ route('home') }}" class="logo">toshLab<span class="dot">.</span></a>
            <nav aria-label="メインメニュー" class="site-nav mono">
                <a href="{{ route('home') }}#works">Works</a>
                <a href="{{ route('home') }}#about">Profile</a>
                <a href="{{ route('home') }}#contact">Contact</a>

                {{-- 英語版（/en）は ⑪ で追加する。ルートができるまでは表示しない --}}
                @if (Route::has('en.home'))
                    <span class="lang" role="group" aria-label="言語">
                        <a href="{{ route('home') }}" lang="ja" @if (app()->getLocale() === 'ja') aria-current="true" @endif>JA</a>
                        <a href="{{ route('en.home') }}" lang="en" @if (app()->getLocale() === 'en') aria-current="true" @endif>EN</a>
                    </span>
                @endif

                <button type="button" class="theme-btn" data-theme-toggle aria-pressed="false" aria-label="ダークモード">
                    <svg class="icon-moon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8z"/></svg>
                    <svg class="icon-sun" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></svg>
                </button>
            </nav>
        </div>
    </header>

    <main id="main">
        {{ $slot }}
    </main>

    <footer class="site-footer">
        <div class="wrap footer-inner mono muted">
            <span>&copy; {{ now()->year }} {{ config('site.name') }}</span>
            <span class="links">
                @if (config('site.github_url'))
                    <a href="{{ config('site.github_url') }}" rel="noopener" target="_blank">GitHub</a>
                @endif
                <a href="{{ config('site.threads_url') }}" rel="noopener" target="_blank">Threads</a>
            </span>
            <a href="#page-top">Back to top ↑</a>
        </div>
    </footer>

    <x-dock-nav />
</body>
</html>
