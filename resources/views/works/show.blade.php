<x-layouts.app :title="$work->title" :description="$work->summary" type="article" :image="$work->thumbnailUrl() ? url($work->thumbnailUrl()) : null">
<div class="wrap">
    {{-- 本文が空でも、概要・技術・画像・リンクだけでページとして成り立つようにする。本文があれば、その下に出す --}}
    <article class="article mx-auto">
        <header class="page-head" data-reveal>
            <p class="mono muted">
                <a href="{{ route('home') }}#works">Works</a>
                @if ($work->tech)
                    <span aria-hidden="true"> — </span>{{ implode(' / ', $work->tech) }}
                @endif
            </p>
            <div class="mt-6"><x-work-label :work="$work" /></div>
            <h1 class="page-title mt-0!">{{ $work->title }}</h1>
            @if (filled($work->summary))
                <p class="mt-6 text-[17px] leading-[1.7] muted">{{ $work->summary }}</p>
            @endif
        </header>

        @if ($work->thumbnailUrl())
            <img src="{{ $work->thumbnailUrl() }}" alt="{{ $work->title }}の画面" class="mb-12 w-full rounded-[4px] border border-line">
        @endif

        @if ($work->tech)
            <dl class="facts mb-12">
                <dt class="mono muted">Tech</dt>
                <dd>{{ implode(' / ', $work->tech) }}</dd>
            </dl>
        @endif

        @if ($work->demo_url || $work->github_url)
            <div class="mb-12 flex flex-wrap gap-3">
                @if ($work->demo_url)
                    <a href="{{ $work->demo_url }}" class="mail-btn" target="_blank" rel="noopener">デモを触る →</a>
                @endif
                @if ($work->github_url)
                    <a href="{{ $work->github_url }}" class="pill-btn" target="_blank" rel="noopener">GitHubでコードを見る</a>
                @endif
            </div>
        @endif

        @if (filled(trim((string) $work->body)))
            <div class="prose-post">
                {!! \Illuminate\Support\Str::markdown($work->body, ['html_input' => 'strip', 'allow_unsafe_links' => false]) !!}
            </div>
        @endif

        <div class="cta-box">
            <p class="text-[20px] font-semibold">同じような仕組みのご相談はこちら</p>
            <p class="mt-2 mb-6 text-[14px] muted">「この作業、もっと楽にならないかな」の段階から大丈夫です。</p>
            <a href="{{ route('home') }}#contact" class="mail-btn">無料で相談する →</a>
        </div>

        <p class="mt-12 mb-24"><a href="{{ route('home') }}#works" class="pill-btn">← 実績一覧へ</a></p>
    </article>
</div>
</x-layouts.app>
