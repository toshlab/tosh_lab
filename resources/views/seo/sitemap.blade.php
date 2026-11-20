{!! '<'.'?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ route('home') }}</loc>
        @if ($latest)
            <lastmod>{{ $latest->toAtomString() }}</lastmod>
        @endif
    </url>
    <url>
        <loc>{{ route('posts.index') }}</loc>
        @if ($posts->isNotEmpty())
            <lastmod>{{ $posts->max('updated_at')->toAtomString() }}</lastmod>
        @endif
    </url>
    <url>
        <loc>{{ route('privacy') }}</loc>
    </url>
    @foreach ($posts as $post)
        <url>
            <loc>{{ route('posts.show', $post) }}</loc>
            <lastmod>{{ $post->updated_at->toAtomString() }}</lastmod>
        </url>
    @endforeach
    @foreach ($works as $work)
        <url>
            <loc>{{ route('works.show', $work) }}</loc>
            <lastmod>{{ $work->updated_at->toAtomString() }}</lastmod>
        </url>
    @endforeach
</urlset>
