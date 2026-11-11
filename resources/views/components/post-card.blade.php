@props(['post'])

<a href="{{ route('posts.show', $post) }}" class="card block min-w-0 flex-[1_1_300px] p-6 text-ink no-underline hover:border-accent hover:text-ink">
    <p class="mb-2 text-[13px] text-muted">
        <time datetime="{{ $post->published_at->toDateString() }}">{{ $post->published_at->format('Y.m.d') }}</time>
        @if ($post->category)
            ・{{ $post->category }}
        @endif
    </p>
    <p class="font-bold">{{ $post->title }}</p>
</a>
