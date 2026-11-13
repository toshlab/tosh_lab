{{--
    画面下に常に表示するメニューバー（docs/design/design-system.md §2-2）。
    トップでは、今いるセクションの光を ③ の animations/dock.js が動かす。
    他のページでは、どのメニューにいるかを Blade で aria-current にして表す。
--}}
@php
    $current = match (true) {
        request()->routeIs('posts.*') => 'blog',
        request()->routeIs('works.*') => 'works',
        default => null,
    };

    $items = [
        ['key' => 'works', 'href' => route('home').'#works', 'en' => 'Works', 'ja' => '実績'],
        ['key' => 'services', 'href' => route('home').'#services', 'en' => 'Services', 'ja' => 'サービス'],
        ['key' => 'about', 'href' => route('home').'#about', 'en' => 'Profile', 'ja' => '経歴'],
        ['key' => 'blog', 'href' => route('posts.index'), 'en' => 'Blog', 'ja' => 'ブログ'],
    ];
@endphp

<nav class="dock" aria-label="ページ内メニュー" data-dock>
    <ul class="dock-list">
        <li class="dock-indicator" aria-hidden="true" data-dock-indicator></li>

        @foreach ($items as $item)
            <li>
                <a href="{{ $item['href'] }}" class="dock-link" data-dock-link="{{ $item['key'] }}"
                   @if ($current === $item['key']) aria-current="page" @endif>
                    <span lang="en">{{ $item['en'] }}</span>
                    <span class="dock-ja">{{ $item['ja'] }}</span>
                </a>
            </li>
        @endforeach

        <li>
            <a href="{{ route('home') }}#contact" class="dock-link dock-cta" data-dock-link="contact">
                <span lang="en">Contact</span>
            </a>
        </li>
    </ul>
</nav>
