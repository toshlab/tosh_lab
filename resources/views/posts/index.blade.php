<x-layouts.app title="ブログ" description="toshLabのブログ。業務システム開発やLaravel、サーバー構築、サッカーとの二刀流について書いています。">
<div class="wrap">
    <section class="block block--first">
        <h1 class="sec-head" data-reveal>
            <span class="sec-title" lang="en">Blog</span>
            <span class="mono muted">ブログ</span>
        </h1>

        @if ($posts->isNotEmpty())
            <div class="rows" data-reveal-group>
                @foreach ($posts as $post)
                    <x-post-row :post="$post" />
                @endforeach
            </div>

            <div class="mt-10">
                {{ $posts->links() }}
            </div>
        @else
            <p class="muted">記事は準備中です。</p>
        @endif
    </section>
</div>
</x-layouts.app>
