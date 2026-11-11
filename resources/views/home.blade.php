<x-layouts.app>

    {{-- ファーストビュー --}}
    <section id="top" class="bg-band">
        <div class="mx-auto flex max-w-[1120px] flex-wrap items-center gap-12 px-6 pt-20 pb-20 md:pt-24 md:pb-22">
            <div class="min-w-0 flex-[1_1_520px]">
                <p class="mb-4 text-[14px] font-medium text-accent">現役サッカー選手のWebエンジニア</p>
                <h1 class="mb-6 text-[32px] leading-[1.4] font-bold md:text-[44px]">Excelや手作業の業務を、<br>使いやすいシステムに。</h1>
                <p class="mb-9 max-w-[560px] text-[16px] text-body">実務4年目のLaravelエンジニアです。在庫・受発注などの業務システムの開発から、サーバーの構築・運用、ホームページ制作まで対応します。「この作業、もっと楽にならないかな」という段階からご相談ください。</p>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ config('site.threads_url') }}" class="btn-primary" target="_blank" rel="noopener">ThreadsのDMで相談する</a>
                    <a href="#contact" class="btn-secondary">フォームで相談する</a>
                </div>
            </div>
            <dl class="flex min-w-0 flex-[1_1_300px] flex-col gap-3">
                @foreach ([['初回相談', '無料'], ['お問い合わせへの返信', '24時間以内'], ['対応エリア', '全国（オンライン）']] as [$label, $value])
                    <div class="card px-6 py-5">
                        <dt class="text-[13px] text-muted">{{ $label }}</dt>
                        <dd class="m-0 text-[22px] font-bold">{{ $value }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>
    </section>

    {{-- こんな方へ --}}
    <section class="section">
        <p class="eyebrow">FOR YOU</p>
        <h2 class="section-title">こんな方のお手伝いをしています</h2>
        @php
            $targets = [
                ['業務を効率化したい会社', 'Excelや紙、手作業の管理を楽にしたい', ['在庫・受発注・顧客の管理システム', 'Excelの自動集計・見える化', '申請・承認などの社内ワークフロー']],
                ['制作会社・エンジニアの方', 'Laravel案件やインフラを任せたい', ['Laravelの開発・改修・保守', 'CakePHPなどからLaravelへの移行', 'AWS・サーバーの構築と運用']],
                ['中小企業・個人事業主', 'ホームページを作りたい、古いサイトを直したい', ['会社・お店のホームページ', '集客用のLP', 'スマホ対応・表示速度の改善']],
                ['スポーツスクール・クラブ', '体験申込や会員管理をWebでしたい', ['スクール・クラブのサイト', '体験申込・会員管理の仕組み', 'コース・料金ページの整理']],
            ];
        @endphp
        <div class="flex flex-wrap gap-5">
            @foreach ($targets as $i => [$title, $lead, $items])
                <div class="card min-w-0 flex-[1_1_240px] px-6 py-7">
                    <p class="mb-2 text-[13px] text-muted">{{ sprintf('%02d', $i + 1) }}</p>
                    <h3 class="mb-2 text-[18px] font-bold">{{ $title }}</h3>
                    <p class="mb-4 text-[14px] text-body">{{ $lead }}</p>
                    <ul class="list-disc pl-[18px] text-[14px] text-body">
                        @foreach ($items as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
    </section>

    {{-- 実績 --}}
    <section id="works" class="section">
        <p class="eyebrow">WORKS</p>
        <h2 class="section-title mb-3">制作実績</h2>
        <p class="mb-10 text-[14px] text-body">実際の案件と自主制作のサンプルは、ラベルで区別して掲載しています。</p>
        @if ($works->isNotEmpty())
            <div class="flex flex-wrap gap-6">
                @foreach ($works as $work)
                    <x-work-card :work="$work" />
                @endforeach
            </div>
        @else
            <p class="card p-6 text-[14px] text-muted">実績は準備中です。</p>
        @endif
    </section>

    {{-- サービス・料金 --}}
    <section id="services" class="section">
        <p class="eyebrow">SERVICES</p>
        <h2 class="section-title">サービスと料金の目安</h2>
        @php
            $services = [
                ['業務システム開発', '¥150,000〜', null, 'Excel・紙の管理をWeb化／要件の整理から設計・実装・テスト／管理画面付き'],
                ['既存システムの改修・移行', 'お見積り', null, 'Laravelの機能追加・不具合対応／CakePHPなどからの移行／引継ぎ資料つき'],
                ['保守・サーバー運用', '¥10,000〜', '/月', 'AWS・VPSの構築と運用／バックアップ／不具合対応・軽微な修正'],
                ['ホームページ・LP制作', '¥50,000〜', null, 'LP・会社やお店のサイト／お問い合わせフォーム／スマホ対応'],
            ];
        @endphp
        <div class="flex flex-wrap gap-5">
            @foreach ($services as [$name, $price, $unit, $detail])
                <div class="card min-w-0 flex-[1_1_240px] px-6 py-7">
                    <h3 class="mb-1 text-[17px] font-bold">{{ $name }}</h3>
                    <p class="mb-4 text-[26px] font-bold text-accent">{{ $price }}@if ($unit)<span class="text-[14px] font-medium">{{ $unit }}</span>@endif</p>
                    <p class="text-[14px] text-body">{{ $detail }}</p>
                </div>
            @endforeach
        </div>
        <p class="mt-5 text-[14px] text-body">内容をうかがってからお見積りします。お見積りまで費用はかかりません。</p>

        <h3 class="mt-16 mb-6 text-[20px] font-bold">ご相談から公開までの流れ</h3>
        @php
            $steps = [
                ['DMかフォームで相談', '「こんな感じのものが欲しい」の一言で大丈夫です。'],
                ['30分の無料ヒアリング', '目的・予算・時期をうかがい、必要なものを一緒に整理します。'],
                ['お見積り・ご提案', '作るもの・金額・スケジュールを書面でお出しします。'],
                ['制作・公開・サポート', '途中経過を共有しながら進め、公開後の更新もご相談ください。'],
            ];
        @endphp
        <ol class="flex flex-wrap gap-5">
            @foreach ($steps as $i => [$title, $text])
                <li class="min-w-0 flex-[1_1_240px] rounded-lg bg-band p-6">
                    <p class="mb-1.5 text-[13px] font-bold text-accent">STEP {{ $i + 1 }}</p>
                    <p class="mb-1.5 font-bold">{{ $title }}</p>
                    <p class="text-[14px] text-body">{{ $text }}</p>
                </li>
            @endforeach
        </ol>
    </section>

    {{-- プロフィール --}}
    <section id="about" class="section">
        <p class="eyebrow">PROFILE</p>
        <h2 class="section-title">プロフィール</h2>
        <div class="flex flex-wrap items-start gap-12">
            @if (file_exists(public_path('images/icon.png')))
                <img src="{{ asset('images/icon.png') }}" alt="toshのアイコン" width="220" height="220"
                     class="aspect-square w-[220px] max-w-full rounded-full bg-placeholder object-cover">
            @else
                <div class="aspect-square w-[220px] max-w-full rounded-full bg-placeholder" aria-hidden="true"></div>
            @endif
            <div class="min-w-0 flex-[1_1_480px]">
                <p class="mb-1 text-[22px] font-bold">{{ config('site.owner.name') }}<span class="ml-2 text-[14px] font-medium text-muted">{{ config('site.owner.kana') }}</span></p>
                <p class="mb-5 text-[14px] text-muted">{{ config('site.owner.role') }}</p>
                <p class="mb-4 text-[15px] text-body">午前はサッカー選手としてトレーニング、午後は正社員のWebエンジニアとして開発をしています。実務は4年目。Laravelを中心に、在庫・受発注管理やイベント受付・決済などの業務システムを、設計から実装・テスト・AWSでのインフラ運用・保守まで担当してきました。</p>
                <p class="mb-6 text-[15px] text-body">製造業の家族の仕事を手伝う中で、Excelや手作業に追われる現場の大変さを見てきました。見た目のきれいさだけでなく、「毎日使う人が楽になるか」を大切にしています。</p>
                @php
                    $strengths = [
                        ['開発からインフラ・保守まで一人で', 'アプリの実装だけでなく、サーバー構築やアクセス増に備えた増強まで対応できます。専任のインフラ担当がいなくても大丈夫です。'],
                        ['引き継ぎやすい資料を必ず残す', '手順書・構成の解説・引継ぎ資料を案件ごとに作成し、納品後も状況が分かる形でお渡しします。'],
                        ['自動化で、早く・ミスなく', 'サーバー構築の自動化やテストの標準化、AIツールの活用で、品質を保ちながら効率よく進めます。'],
                    ];
                @endphp
                <ul class="flex flex-col gap-3">
                    @foreach ($strengths as [$title, $text])
                        <li class="card px-5 py-4">
                            <p class="mb-0.5 font-bold">{{ $title }}</p>
                            <p class="text-[14px] text-body">{{ $text }}</p>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </section>

    {{-- 実務での経験（社名・クライアント名は出さない） --}}
    <section id="experience" class="section">
        <p class="eyebrow">EXPERIENCE</p>
        <h2 class="section-title mb-3">実務での主な経験</h2>
        <p class="mb-8 text-[14px] text-body">本業で担当した案件です。守秘義務のため、社名・クライアント名は伏せています。</p>
        @php
            $experiences = [
                ['在庫・受発注管理システムの新規開発・保守', '実装を単独で担当し、納品後の保守も継続。同時に更新されてもデータが壊れない仕組みや、売上の一括登録機能を開発。', 'Laravel 11 ／ PHP 8.2 ／ MySQL ／ Apache'],
                ['イベント参加登録・決済・動画配信システムの運用', '同時接続 約3,500人を想定してサーバーとデータベースを増強。ロードバランサーの構成管理や手順書の作成も担当。', 'AWS（EC2 / RDS / ALB）／ Laravel 8 ／ nginx'],
                ['写真のオンライン販売システムの開発・保守', 'オンライン決済付きの販売システムの実装と保守。', 'Laravel 12 ／ PHP 8.4 ／ AWS ／ nginx'],
                ['既存システムのLaravelへの移行', 'CakePHPで作られた既存システムをLaravelへ移行。設計から実装まで担当。', 'CakePHP → Laravel ／ MySQL'],
                ['サーバー構築の自動化ツール（社内）', 'コマンド一つでWebサーバー・PHP・Laravelの環境を構築できるツールを開発。パスワードなどの秘密情報は暗号化して管理。', 'Ansible ／ Shell ／ nginx・Apache'],
                ['有給休暇の申請・承認システム（社内）', '申請 → 承認 → Googleカレンダーへの反映までを自動化。チームごとの承認者や、営業日ベースの申請期限に対応。', 'Google Apps Script ／ スプレッドシート'],
            ];
        @endphp
        <div class="flex flex-wrap gap-5">
            @foreach ($experiences as [$title, $text, $stack])
                <div class="card min-w-0 flex-[1_1_480px] p-6">
                    <h3 class="mb-1.5 text-[17px] font-bold">{{ $title }}</h3>
                    <p class="mb-3 text-[14px] text-body">{{ $text }}</p>
                    <p class="text-[12px] text-muted">{{ $stack }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- スキル --}}
    <section id="skills" class="section">
        <p class="eyebrow">SKILLS</p>
        <h2 class="section-title mb-3">スキル</h2>
        <div class="mb-7 flex flex-wrap items-center gap-x-5 gap-y-2 text-[13px] text-body">
            <span class="inline-flex items-center gap-1.5"><span class="rounded-[3px] bg-accent px-1.5 text-[11px] font-bold text-white">実務</span>仕事で使っている</span>
            <span class="inline-flex items-center gap-1.5"><span class="rounded-[3px] border border-accent px-[5px] text-[11px] font-bold text-accent">個人</span>自分の制作物で使った</span>
        </div>
        @php
            $skills = [
                '言語・フレームワーク' => [['PHP', 'work'], ['Laravel 8〜12', 'work'], ['CakePHP', 'work'], ['JavaScript', 'work'], ['Livewire / Alpine.js', 'work'], ['Tailwind CSS', 'work'], ['Google Apps Script', 'work'], ['WordPress', 'personal']],
                'データベース' => [['MySQL（AWS RDS含む）', 'work']],
                'インフラ' => [['AWS（EC2 / RDS / ALB）', 'work'], ['nginx / Apache / php-fpm', 'work'], ['Linux（Amazon Linux / Oracle Linux）', 'work']],
                '自動化・テスト' => [['Ansible', 'work'], ['Shell', 'work'], ['PHPUnit', 'work'], ['Docker', 'work']],
                'その他' => [['Git / GitHub', 'work'], ['Claude Code', 'work'], ['設計書・手順書・引継ぎ資料', 'work'], ['Excel', 'personal']],
            ];
        @endphp
        <div class="card">
            @foreach ($skills as $category => $items)
                <div @class(['flex flex-wrap gap-x-6 gap-y-2 px-6 py-5', 'border-b border-line' => ! $loop->last])>
                    <p class="flex-[0_0_140px] text-[14px] font-bold">{{ $category }}</p>
                    <div class="flex min-w-0 flex-[1_1_480px] flex-wrap gap-2">
                        @foreach ($items as [$name, $level])
                            <x-skill :level="$level">{{ $name }}</x-skill>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ブログ --}}
    <section id="blog" class="section">
        <div class="mb-8 flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="eyebrow">BLOG</p>
                <h2 class="mt-1 text-[26px] font-bold md:text-[30px]">ブログ</h2>
            </div>
            @if ($posts->isNotEmpty())
                <a href="{{ route('posts.index') }}" class="text-[14px] font-medium">記事一覧へ →</a>
            @endif
        </div>
        @if ($posts->isNotEmpty())
            <div class="flex flex-wrap gap-5">
                @foreach ($posts as $post)
                    <x-post-card :post="$post" />
                @endforeach
            </div>
        @else
            <p class="card p-6 text-[14px] text-muted">記事は準備中です。</p>
        @endif
    </section>

    {{-- よくあるご質問 --}}
    <section class="section">
        <p class="eyebrow">FAQ</p>
        <h2 class="section-title mb-8">よくあるご質問</h2>
        @php
            $faqs = [
                ['サッカーと両立していて、連絡はつきますか？', '午前中は練習のため、返信は主に午後〜夜になります。いただいたご連絡には24時間以内にお返事します。'],
                ['1週間にどれくらい作業してもらえますか？', '週8〜10時間ほど（平日19時以降・土日）です。この時間に合わせて、無理のないスケジュールをご提案します。'],
                ['何を作りたいか、まだはっきりしていません。', '大丈夫です。今困っていることをうかがって、作るべきものを一緒に整理します。'],
                ['遠方でも頼めますか？', 'はい。打ち合わせはすべてオンラインで行うので、全国どこからでもご依頼いただけます。'],
            ];
        @endphp
        <div class="border-t border-line">
            @foreach ($faqs as [$q, $a])
                <details class="group border-b border-line py-5">
                    <summary class="flex cursor-pointer list-none items-start justify-between gap-4 font-bold">
                        <span>Q. {{ $q }}</span>
                        <span class="text-accent transition-transform group-open:rotate-45" aria-hidden="true">＋</span>
                    </summary>
                    <p class="mt-2 text-[14px] text-body">{{ $a }}</p>
                </details>
            @endforeach
        </div>
    </section>

    {{-- お問い合わせ --}}
    <section id="contact" class="mt-24 bg-band">
        <div class="mx-auto flex max-w-[1120px] flex-wrap gap-12 px-6 py-22">
            <div class="min-w-0 flex-[1_1_360px]">
                <p class="eyebrow">CONTACT</p>
                <h2 class="mt-1 mb-4 text-[26px] font-bold md:text-[30px]">お気軽にご相談ください</h2>
                <p class="mb-7 text-[15px] text-body">いちばん早いのはThreadsのDMです。内容がまとまっていなくても大丈夫です。</p>
                <a href="{{ config('site.threads_url') }}" class="btn-primary" target="_blank" rel="noopener">ThreadsのDMで相談する</a>
            </div>

            <div class="card min-w-0 flex-[1_1_420px] p-8">
                @if (session('inquiry_sent'))
                    <div role="status" class="rounded-md bg-accent-soft p-6 text-accent-dark">
                        <p class="mb-1 font-bold">{{ __('inquiry.sent.title') }}</p>
                        <p class="text-[14px]">{{ __('inquiry.sent.body') }}</p>
                    </div>
                @else
                    <form method="POST" action="{{ route('inquiries.store') }}" class="flex flex-col gap-[18px]" novalidate>
                        @csrf

                        @if ($errors->has('website'))
                            <p role="alert" class="text-[14px] text-red-700">{{ $errors->first('website') }}</p>
                        @endif

                        <label class="flex flex-col gap-1.5 text-[14px] font-medium">
                            {{ __('inquiry.attributes.name') }}
                            <input type="text" name="name" value="{{ old('name') }}" required maxlength="100" autocomplete="name" class="field" @error('name') aria-invalid="true" @enderror>
                            @error('name')<span class="text-[13px] font-normal text-red-700">{{ $message }}</span>@enderror
                        </label>

                        <label class="flex flex-col gap-1.5 text-[14px] font-medium">
                            {{ __('inquiry.attributes.email') }}
                            <input type="email" name="email" value="{{ old('email') }}" required maxlength="255" autocomplete="email" class="field" @error('email') aria-invalid="true" @enderror>
                            @error('email')<span class="text-[13px] font-normal text-red-700">{{ $message }}</span>@enderror
                        </label>

                        <label class="flex flex-col gap-1.5 text-[14px] font-medium">
                            {{ __('inquiry.attributes.message') }}
                            <textarea name="message" rows="5" required maxlength="5000" class="field resize-y" @error('message') aria-invalid="true" @enderror>{{ old('message') }}</textarea>
                            @error('message')<span class="text-[13px] font-normal text-red-700">{{ $message }}</span>@enderror
                        </label>

                        {{-- スパム対策（人には見えない欄） --}}
                        <div class="hidden" aria-hidden="true">
                            <label>空欄のままにしてください<input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                        </div>

                        <button type="submit" class="cursor-pointer rounded-md bg-accent p-3.5 font-medium text-white hover:bg-accent-dark">{{ __('inquiry.submit') }}</button>
                    </form>
                @endif
            </div>
        </div>
    </section>

</x-layouts.app>
