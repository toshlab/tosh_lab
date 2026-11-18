<x-layouts.app :title="$post->title" :description="$post->excerptText()" type="article">
@push('head')
    <meta property="article:published_time" content="{{ $post->published_at->toIso8601String() }}">
    <meta property="article:modified_time" content="{{ $post->updated_at->toIso8601String() }}">
    @if ($post->category)
        <meta property="article:section" content="{{ $post->category }}">
    @endif
    <script type="application/ld+json">{!! json_encode($post->structuredData(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>
@endpush
<div class="wrap">
    <article class="article mx-auto">
        <header class="page-head" data-reveal>
            <p class="mono muted">
                <a href="{{ route('posts.index') }}">Blog</a>
                <span aria-hidden="true"> — </span>
                <time datetime="{{ $post->published_at->toDateString() }}">{{ $post->published_at->format('Y.m.d') }}</time>
                @if ($post->category)
                    ・{{ $post->category }}
                @endif
            </p>
            <h1 class="page-title">{{ $post->title }}</h1>
            @if ($post->tags)
                <ul class="mt-6 flex flex-wrap gap-2" aria-label="タグ">
                    @foreach ($post->tags as $tag)
                        <li class="badge mb-0!">#{{ $tag }}</li>
                    @endforeach
                </ul>
            @endif
        </header>

        <div class="prose-post">
            {!! $post->bodyHtml() !!}
        </div>

        <div class="cta-box">
            <p class="text-[20px] font-semibold">業務システムやサイト制作のご相談はこちら</p>
            <p class="mt-2 mb-6 text-[14px] muted">「この作業、もっと楽にならないかな」の段階から大丈夫です。</p>
            <a href="{{ route('home') }}#contact" class="mail-btn">無料で相談する →</a>
        </div>

        <p class="mt-12 mb-24"><a href="{{ route('posts.index') }}" class="pill-btn">← 記事一覧へ</a></p>
    </article>
</div>
</x-layouts.app>
