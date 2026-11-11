<x-layouts.app title="ブログ" description="toshLabのブログ。業務システム開発やLaravel、サーバー構築、サッカーとの二刀流について書いています。">
    <div class="mx-auto max-w-[1120px] px-6 pt-20 pb-24">
        <p class="eyebrow">BLOG</p>
        <h1 class="mt-1 mb-10 text-[30px] font-bold">ブログ</h1>

        @if ($posts->isNotEmpty())
            <div class="flex flex-wrap gap-5">
                @foreach ($posts as $post)
                    <x-post-card :post="$post" />
                @endforeach
            </div>

            <div class="mt-10">
                {{ $posts->links() }}
            </div>
        @else
            <p class="card p-6 text-[14px] text-muted">記事は準備中です。</p>
        @endif
    </div>
</x-layouts.app>
