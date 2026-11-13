@props(['post', 'heading' => 'h3'])

{{--
    ブログ記事の1行（モックの .wrow）。番号の位置に日付を出す。
    見出しの階層を飛ばさないよう、置く場所に合わせて heading を変える（トップ：h2の下なのでh3／一覧：h1の下なのでh2）。
--}}
<a href="{{ route('posts.show', $post) }}" class="wrow wrow--post" data-reveal-item>
    <time class="mono muted" datetime="{{ $post->published_at->toDateString() }}">{{ $post->published_at->format('Y.m.d') }}</time>
    <div>
        <{{ $heading }} class="title">{{ $post->title }}</{{ $heading }}>
        <span class="desc muted">{{ $post->excerptText() }}</span>
    </div>
    <span class="stack stack--tech mono muted">{{ $post->category }}</span>
    <svg class="arrow" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" aria-hidden="true"><path d="M4 12h16M14 6l6 6-6 6"/></svg>
</a>
