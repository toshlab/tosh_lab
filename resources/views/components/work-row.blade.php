@props(['work', 'number'])

{{--
    実績の1行（モックの .wrow）。
    行の行き先：詳細ページがある（Work::hasDetailPage）→ 詳細ページ／無ければデモ → GitHub／どれも無ければリンクにしない。
--}}
@php
    $href = match (true) {
        $work->hasDetailPage() => route('works.show', $work),
        filled($work->demo_url) => $work->demo_url,
        filled($work->github_url) => $work->github_url,
        default => null,
    };
    $external = $href !== null && ! $work->hasDetailPage();
    $tag = $href ? 'a' : 'div';
@endphp

<{{ $tag }} @if ($href) href="{{ $href }}" @if ($external) target="_blank" rel="noopener" @endif @endif class="wrow" data-reveal-item>
    <span class="mono muted">{{ sprintf('%02d', $number) }}</span>
    <div>
        <x-work-label :work="$work" />
        <h3 class="title">{{ $work->title }}</h3>
        <span class="desc muted">{{ $work->summary }}</span>
    </div>
    <span class="stack stack--tech mono muted">{{ implode(' / ', $work->tech ?? []) }}</span>
    @if ($href)
        <svg class="arrow" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" aria-hidden="true"><path d="M4 12h16M14 6l6 6-6 6"/></svg>
    @else
        <span></span>
    @endif
</{{ $tag }}>
