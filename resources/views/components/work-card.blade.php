@props(['work'])

<article class="card flex min-w-0 flex-[1_1_320px] flex-col overflow-hidden">
    @if ($work->thumbnailUrl())
        <img src="{{ $work->thumbnailUrl() }}" alt="{{ $work->title }}の画面" class="aspect-video w-full object-cover" loading="lazy">
    @else
        <div class="flex aspect-video items-center justify-center bg-placeholder text-[13px] text-muted" aria-hidden="true">{{ $work->title }}</div>
    @endif

    <div class="flex flex-1 flex-col p-6">
        <div class="mb-3 flex flex-wrap gap-2">
            <x-work-label :work="$work" />
            @foreach ($work->tech ?? [] as $tech)
                <span class="tag">{{ $tech }}</span>
            @endforeach
        </div>
        <h3 class="mb-2 text-[18px] font-bold">{{ $work->title }}</h3>
        <p class="mb-5 text-[14px] text-body">{{ $work->summary }}</p>
        <div class="mt-auto flex flex-wrap gap-4 text-[14px] font-medium">
            @if ($work->body)
                <a href="{{ route('works.show', $work) }}">詳しく見る →</a>
            @endif
            @if ($work->demo_url)
                <a href="{{ $work->demo_url }}" target="_blank" rel="noopener">デモを触る →</a>
            @endif
            @if ($work->github_url)
                <a href="{{ $work->github_url }}" target="_blank" rel="noopener">GitHub →</a>
            @endif
        </div>
    </div>
</article>
