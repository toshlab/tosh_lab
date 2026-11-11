<x-layouts.app :title="$post->title" :description="$post->excerptText()">
    <article class="mx-auto max-w-[760px] px-6 pt-20 pb-24">
        <p class="mb-3 text-[13px] text-muted">
            <time datetime="{{ $post->published_at->toDateString() }}">{{ $post->published_at->format('Y.m.d') }}</time>
            @if ($post->category)
                ・{{ $post->category }}
            @endif
        </p>
        <h1 class="mb-10 text-[28px] leading-[1.5] font-bold md:text-[32px]">{{ $post->title }}</h1>

        <div class="prose-post">
            {!! $post->bodyHtml() !!}
        </div>

        <div class="mt-16 rounded-lg bg-band p-8">
            <p class="mb-2 font-bold">業務システムやサイト制作のご相談はこちら</p>
            <p class="mb-5 text-[14px] text-body">「この作業、もっと楽にならないかな」の段階から大丈夫です。</p>
            <a href="{{ route('home') }}#contact" class="btn-primary">無料で相談する</a>
        </div>

        <p class="mt-10"><a href="{{ route('posts.index') }}" class="text-[14px] font-medium">← 記事一覧へ</a></p>
    </article>
</x-layouts.app>
