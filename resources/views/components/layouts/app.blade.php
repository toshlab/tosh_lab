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

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&display=swap">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <a href="#main" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded focus:bg-white focus:px-4 focus:py-2">本文へ移動</a>

    <header class="sticky top-0 z-40 border-b border-line bg-paper/95 backdrop-blur">
        <div class="mx-auto flex max-w-[1120px] flex-wrap items-center justify-between gap-x-6 gap-y-3 px-6 py-4">
            <a href="{{ route('home') }}" class="text-[20px] font-bold tracking-[0.02em] text-ink no-underline hover:text-ink">
                tosh<span class="text-accent">Lab</span>
            </a>
            <nav aria-label="メインメニュー" class="flex flex-wrap items-center gap-x-6 gap-y-2 text-[14px]">
                <a href="{{ route('home') }}#works" class="text-body no-underline hover:text-accent">実績</a>
                <a href="{{ route('home') }}#services" class="text-body no-underline hover:text-accent">サービス・料金</a>
                <a href="{{ route('home') }}#about" class="text-body no-underline hover:text-accent">プロフィール</a>
                <a href="{{ route('posts.index') }}" class="text-body no-underline hover:text-accent">ブログ</a>
                <a href="{{ route('home') }}#contact" class="rounded-md bg-accent px-[18px] py-2.5 font-medium text-white no-underline hover:bg-accent-dark hover:text-white">無料で相談する</a>
            </nav>
        </div>
    </header>

    <main id="main">
        {{ $slot }}
    </main>

    <footer class="border-t border-line">
        <div class="mx-auto flex max-w-[1120px] flex-wrap justify-between gap-3 px-6 py-7 text-[13px] text-muted">
            <span>&copy; {{ config('site.name') }}</span>
            <div class="flex flex-wrap gap-5">
                @if (config('site.github_url'))
                    <a href="{{ config('site.github_url') }}" class="text-muted" rel="noopener" target="_blank">GitHub</a>
                @endif
                <a href="{{ config('site.threads_url') }}" class="text-muted" rel="noopener" target="_blank">Threads {{ config('site.threads_handle') }}</a>
            </div>
        </div>
    </footer>
</body>
</html>
