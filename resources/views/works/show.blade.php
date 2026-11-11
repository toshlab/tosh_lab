<x-layouts.app :title="$work->title" :description="$work->summary">
    <article class="mx-auto max-w-[860px] px-6 pt-20 pb-24">
        <div class="mb-3 flex flex-wrap gap-2">
            <x-work-label :work="$work" />
            @foreach ($work->tech ?? [] as $tech)
                <span class="tag">{{ $tech }}</span>
            @endforeach
        </div>
        <h1 class="mb-4 text-[28px] leading-[1.5] font-bold md:text-[32px]">{{ $work->title }}</h1>
        <p class="mb-8 text-[16px] text-body">{{ $work->summary }}</p>

        @if ($work->thumbnailUrl())
            <img src="{{ $work->thumbnailUrl() }}" alt="{{ $work->title }}の画面" class="mb-10 w-full rounded-lg border border-line">
        @endif

        @if ($work->demo_url || $work->github_url)
            <div class="mb-10 flex flex-wrap gap-3">
                @if ($work->demo_url)
                    <a href="{{ $work->demo_url }}" class="btn-primary" target="_blank" rel="noopener">デモを触る</a>
                @endif
                @if ($work->github_url)
                    <a href="{{ $work->github_url }}" class="btn-secondary" target="_blank" rel="noopener">GitHubでコードを見る</a>
                @endif
            </div>
        @endif

        @if ($work->body)
            <div class="prose-post">
                {!! \Illuminate\Support\Str::markdown($work->body, ['html_input' => 'strip', 'allow_unsafe_links' => false]) !!}
            </div>
        @endif

        <p class="mt-12"><a href="{{ route('home') }}#works" class="text-[14px] font-medium">← 実績一覧へ</a></p>
    </article>
</x-layouts.app>
