<x-layouts.app>
<div class="wrap">

    {{-- ファーストビュー（モックの .hero） --}}
    <section id="top" class="hero">
        <div class="hero-meta mono muted" data-hero-item>
            <span>(00) — Portfolio {{ now()->year }}</span>
            <span class="status">初回相談 無料・24時間以内に返信</span>
        </div>

        {{-- 大きな名前。SEOのため h1 は下のキャッチコピーにして、ここはロゴ画像として表示する --}}
        <p class="brand" lang="en">
            <span data-hero-line class="brand-logo">
                <img src="{{ asset('images/logo/toshlab-logo-3-twotone.svg') }}" alt="{{ config('site.name') }}" width="1120" height="174" class="logo-light">
                <img src="{{ asset('images/logo/toshlab-logo-3-twotone-white.svg') }}" alt="{{ config('site.name') }}" width="1120" height="174" class="logo-dark">
            </span>
        </p>

        <div class="hero-foot">
            <div data-hero-item>
                <h1 class="hero-lead">Excelや手作業の業務を、<br>使いやすいシステムに。</h1>
                <p class="hero-text muted">実務4年目のLaravelエンジニアです。在庫・受発注などの業務システムの開発から、サーバーの構築・運用、ホームページ制作まで対応します。「この作業、もっと楽にならないかな」という段階からご相談ください。</p>
                <div class="hero-actions">
                    <a href="{{ config('site.threads_url') }}" class="mail-btn" target="_blank" rel="noopener">ThreadsのDMで相談する →</a>
                    <a href="#contact" class="pill-btn">フォームで相談する</a>
                </div>
            </div>
            <div class="hero-info mono muted" data-hero-item>
                <span>現役サッカー選手のWebエンジニア</span>
                <span>Laravel / PHP / AWS</span>
                <span>対応エリア：全国（オンライン）</span>
            </div>
            <a href="#for-you" class="scroll mono" data-hero-item>Scroll
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M12 4v16M6 14l6 6 6-6"/></svg>
            </a>
        </div>
    </section>

    {{-- (01) こんな方へ --}}
    <section id="for-you" class="block block--first">
        <h2 class="sec-head" data-reveal>
            <span class="sec-title" lang="en">For you</span>
            <span class="mono muted"><span aria-hidden="true">(01) — </span>こんな方のお手伝いをしています</span>
        </h2>
        @php
            $targets = [
                ['業務を効率化したい会社', 'Excelや紙、手作業の管理を楽にしたい', ['在庫・受発注・顧客の管理システム', 'Excelの自動集計・見える化', '申請・承認などの社内ワークフロー']],
                ['制作会社・エンジニアの方', 'Laravel案件やインフラを任せたい', ['Laravelの開発・改修・保守', 'CakePHPなどからLaravelへの移行', 'AWS・サーバーの構築と運用']],
                ['中小企業・個人事業主', 'ホームページを作りたい、古いサイトを直したい', ['会社・お店のホームページ', '集客用のLP', 'スマホ対応・表示速度の改善']],
                ['スポーツスクール・クラブ', '体験申込や会員管理をWebでしたい', ['スクール・クラブのサイト', '体験申込・会員管理の仕組み', 'コース・料金ページの整理']],
            ];
        @endphp
        <div class="rows" data-reveal-group>
            @foreach ($targets as $i => [$title, $lead, $items])
                <div class="wrow wrow--static" data-reveal-item>
                    <span class="mono muted">{{ sprintf('%02d', $i + 1) }}</span>
                    <div>
                        <h3 class="title">{{ $title }}</h3>
                        <span class="desc muted">{{ $lead }}</span>
                    </div>
                    <div class="stack mono muted">
                        <ul>
                            @foreach ($items as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- (02) 実績 --}}
    <section id="works" class="block">
        <h2 class="sec-head" data-reveal>
            <span class="sec-title" lang="en">Works</span>
            <span class="mono muted"><span aria-hidden="true">(02) — </span>制作実績</span>
        </h2>
        <p class="sec-lead muted" data-reveal>実際の案件と自主制作のサンプルは、ラベルで区別して掲載しています。</p>

        @if ($works->isNotEmpty())
            <div class="rows" data-reveal-group>
                @foreach ($works as $work)
                    <x-work-row :work="$work" :number="$loop->iteration" />
                @endforeach
            </div>
        @else
            <p class="muted">実績は準備中です。</p>
        @endif
    </section>

    {{-- (03) サービス・料金 --}}
    <section id="services" class="block">
        <h2 class="sec-head" data-reveal>
            <span class="sec-title" lang="en">Services</span>
            <span class="mono muted"><span aria-hidden="true">(03) — </span>サービスと料金の目安</span>
        </h2>
        @php
            $services = [
                ['業務システム開発', '¥150,000〜', null, 'Excel・紙の管理をWeb化／要件の整理から設計・実装・テスト／管理画面付き'],
                ['既存システムの改修・移行', 'お見積り', null, 'Laravelの機能追加・不具合対応／CakePHPなどからの移行／引継ぎ資料つき'],
                ['保守・サーバー運用', '¥10,000〜', '/月', 'AWS・VPSの構築と運用／バックアップ／不具合対応・軽微な修正'],
                ['ホームページ・LP制作', '¥50,000〜', null, 'LP・会社やお店のサイト／お問い合わせフォーム／スマホ対応'],
            ];
        @endphp
        <div class="rows" data-reveal-group>
            @foreach ($services as $i => [$name, $price, $unit, $detail])
                <div class="wrow wrow--static" data-reveal-item>
                    <span class="mono muted">{{ sprintf('%02d', $i + 1) }}</span>
                    <div>
                        <h3 class="title">{{ $name }}</h3>
                        <span class="desc muted">{{ $detail }}</span>
                    </div>
                    <div class="stack price">{{ $price }}@if ($unit)<small>{{ $unit }}</small>@endif</div>
                </div>
            @endforeach
        </div>
        <p class="sec-lead muted mt-8!" data-reveal>内容をうかがってからお見積りします。お見積りまで費用はかかりません。</p>

        <h3 class="sub-title" data-reveal>ご相談から公開までの流れ</h3>
        @php
            $steps = [
                ['DMかフォームで相談', '「こんな感じのものが欲しい」の一言で大丈夫です。'],
                ['30分の無料ヒアリング', '目的・予算・時期をうかがい、必要なものを一緒に整理します。'],
                ['お見積り・ご提案', '作るもの・金額・スケジュールを書面でお出しします。'],
                ['制作・公開・サポート', '途中経過を共有しながら進め、公開後の更新もご相談ください。'],
            ];
        @endphp
        <ol class="flow" data-reveal-group>
            @foreach ($steps as $i => [$title, $text])
                <li data-reveal-item>
                    <span class="mono muted">STEP {{ sprintf('%02d', $i + 1) }}</span>
                    <p class="flow-title">{{ $title }}</p>
                    <p class="flow-text muted">{{ $text }}</p>
                </li>
            @endforeach
        </ol>
    </section>

    {{-- (04) プロフィール --}}
    <section id="about" class="block">
        <h2 class="sec-head" data-reveal>
            <span class="sec-title" lang="en">Profile</span>
            <span class="mono muted"><span aria-hidden="true">(04) — </span>プロフィール</span>
        </h2>
        <div class="profile-grid">
            <div class="portrait mono muted" data-reveal>
                @if (file_exists(public_path('images/icon.png')))
                    <img src="{{ asset('images/icon.png') }}" alt="toshLabのアイコン" width="420" height="420">
                @else
                    <span>{{ config('site.owner.name') }} — {{ config('site.owner.role') }}</span>
                @endif
            </div>
            <div class="profile-body" data-reveal>
                <p class="bio">午前はサッカー選手としてトレーニング、午後は正社員のWebエンジニアとして開発をしています。実務は4年目。Laravelを中心に、在庫・受発注管理やイベント受付・決済などの業務システムを、設計から実装・テスト・AWSでのインフラ運用・保守まで担当してきました。</p>
                <p class="bio-sub muted">製造業の家族の仕事を手伝う中で、Excelや手作業に追われる現場の大変さを見てきました。見た目のきれいさだけでなく、「毎日使う人が楽になるか」を大切にしています。</p>

                <dl class="facts">
                    <dt class="mono muted">Name</dt>
                    <dd>{{ config('site.owner.name') }}（{{ config('site.owner.kana') }}）</dd>
                    <dt class="mono muted">Role</dt>
                    <dd>{{ config('site.owner.role') }}</dd>
                    <dt class="mono muted">Experience</dt>
                    <dd>実務4年目</dd>
                    <dt class="mono muted">Area</dt>
                    <dd>全国（オンライン）</dd>
                </dl>

                @php
                    $strengths = [
                        ['開発からインフラ・保守まで一人で', 'アプリの実装だけでなく、サーバー構築やアクセス増に備えた増強まで対応できます。専任のインフラ担当がいなくても大丈夫です。'],
                        ['引き継ぎやすい資料を必ず残す', '手順書・構成の解説・引継ぎ資料を案件ごとに作成し、納品後も状況が分かる形でお渡しします。'],
                        ['自動化で、早く・ミスなく', 'サーバー構築の自動化やテストの標準化、AIツールの活用で、品質を保ちながら効率よく進めます。'],
                    ];
                @endphp
                <div>
                    <span class="mono muted">Strengths</span>
                    <ul class="strengths">
                        @foreach ($strengths as [$title, $text])
                            <li>
                                <p class="font-semibold">{{ $title }}</p>
                                <p class="mt-1! text-[14px] leading-[1.8] muted">{{ $text }}</p>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- (05) 実務での経験（社名・クライアント名は出さない） --}}
    <section id="experience" class="block">
        <h2 class="sec-head" data-reveal>
            <span class="sec-title" lang="en">Experience</span>
            <span class="mono muted"><span aria-hidden="true">(05) — </span>実務での主な経験</span>
        </h2>
        <p class="sec-lead muted" data-reveal>本業で担当した案件です。守秘義務のため、社名・クライアント名は伏せています。</p>
        @php
            $experiences = [
                ['在庫・受発注管理システムの新規開発・保守', '実装を単独で担当し、納品後の保守も継続。同時に更新されてもデータが壊れない仕組みや、売上の一括登録機能を開発。', 'Laravel / PHP / MySQL / Apache'],
                ['イベント参加登録・決済・動画配信システムの運用', '大人数の同時アクセスに備えて、サーバーとデータベースを増強。ロードバランサーの構成管理や手順書の作成も担当。', 'AWS / Laravel / nginx'],
                ['写真のオンライン販売システムの開発・保守', 'オンライン決済付きの販売システムの実装と保守。', 'Laravel / PHP / AWS / nginx'],
                ['既存システムのLaravelへの移行', 'CakePHPで作られた既存システムをLaravelへ移行。設計から実装まで担当。', 'CakePHP → Laravel / MySQL'],
                ['サーバー構築の自動化ツール（社内）', 'コマンド一つでWebサーバー・PHP・Laravelの環境を構築できるツールを開発。パスワードなどの秘密情報は暗号化して管理。', 'Ansible / Shell / nginx・Apache'],
                ['有給休暇の申請・承認システム（社内）', '申請 → 承認 → Googleカレンダーへの反映までを自動化。チームごとの承認者や、営業日ベースの申請期限に対応。', 'Google Apps Script / スプレッドシート'],
            ];
        @endphp
        <div class="rows" data-reveal-group>
            @foreach ($experiences as $i => [$title, $text, $stack])
                <div class="wrow wrow--static" data-reveal-item>
                    <span class="mono muted">{{ sprintf('%02d', $i + 1) }}</span>
                    <div>
                        <h3 class="title">{{ $title }}</h3>
                        <span class="desc muted">{{ $text }}</span>
                    </div>
                    <div class="stack stack--tech mono muted">{{ $stack }}</div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- (06) スキル --}}
    <section id="skills" class="block">
        <h2 class="sec-head" data-reveal>
            <span class="sec-title" lang="en">Skills</span>
            <span class="mono muted"><span aria-hidden="true">(06) — </span>スキル</span>
        </h2>
        <p class="sec-lead muted mono" data-reveal>
            <span class="skill-level skill-level--work">実務</span> 仕事で使っている　／　<span class="skill-level">個人</span> 自分の制作物で使った
        </p>
        @php
            $skills = [
                '言語・フレームワーク' => [['PHP', 'work'], ['Laravel 8〜12', 'work'], ['CakePHP', 'work'], ['JavaScript', 'work'], ['Livewire / Alpine.js', 'work'], ['Tailwind CSS', 'work'], ['Google Apps Script', 'work'], ['WordPress', 'personal']],
                'データベース' => [['MySQL（AWS RDS含む）', 'work']],
                'インフラ' => [['AWS（EC2 / RDS / ALB）', 'work'], ['nginx / Apache / php-fpm', 'work'], ['Linux（Amazon Linux / Oracle Linux）', 'work']],
                '自動化・テスト' => [['Ansible', 'work'], ['Shell', 'work'], ['PHPUnit', 'work'], ['Docker', 'work']],
                'その他' => [['Git / GitHub', 'work'], ['Claude Code', 'work'], ['設計書・手順書・引継ぎ資料', 'work'], ['Excel', 'personal']],
            ];
        @endphp
        <dl class="skill-groups" data-reveal-group>
            @foreach ($skills as $category => $items)
                <dt class="mono muted" data-reveal-item>{{ $category }}</dt>
                <dd data-reveal-item>
                    <div class="skills">
                        @foreach ($items as [$name, $level])
                            <x-skill :level="$level">{{ $name }}</x-skill>
                        @endforeach
                    </div>
                </dd>
            @endforeach
        </dl>
    </section>

    {{-- (07) ブログ --}}
    <section id="blog" class="block">
        <h2 class="sec-head" data-reveal>
            <span class="sec-title" lang="en">Blog</span>
            <span class="mono muted"><span aria-hidden="true">(07) — </span>ブログ</span>
        </h2>
        @if ($posts->isNotEmpty())
            <div class="rows" data-reveal-group>
                @foreach ($posts as $post)
                    <x-post-row :post="$post" />
                @endforeach
            </div>
            <div class="more"><a href="{{ route('posts.index') }}" class="pill-btn">記事一覧へ →</a></div>
        @else
            <p class="muted">記事は準備中です。</p>
        @endif
    </section>

    {{-- (08) よくあるご質問 --}}
    <section id="faq" class="block">
        <h2 class="sec-head" data-reveal>
            <span class="sec-title" lang="en">FAQ</span>
            <span class="mono muted"><span aria-hidden="true">(08) — </span>よくあるご質問</span>
        </h2>
        @php
            $faqs = [
                ['何を作りたいか、まだはっきりしていません。', '大丈夫です。今困っていることをうかがって、作るべきものを一緒に整理します。'],
                ['遠方でも頼めますか？', 'はい。打ち合わせはすべてオンラインで行うので、全国どこからでもご依頼いただけます。'],
            ];
        @endphp
        <div class="rows faq" data-reveal-group>
            @foreach ($faqs as $i => [$q, $a])
                <details data-reveal-item>
                    <summary>
                        <span class="mono muted">{{ sprintf('%02d', $i + 1) }}</span>
                        <span>Q. {{ $q }}</span>
                        <span class="faq-icon" aria-hidden="true">＋</span>
                    </summary>
                    <p class="faq-answer muted">{{ $a }}</p>
                </details>
            @endforeach
        </div>
    </section>

    {{-- (09) お問い合わせ（モックの #contact） --}}
    <section id="contact" class="block contact-block">
        <span class="mono muted">(09) — Contact</span>
        <h2 data-reveal>
            <span class="contact-title" lang="en">Let's work<br>together<span class="dot">.</span></span>
            <span class="contact-heading">お気軽にご相談ください</span>
        </h2>
        <div class="contact-foot" data-reveal>
            <p class="contact-lead muted">いちばん早いのはThreadsのDMです。内容がまとまっていなくても大丈夫です。</p>
            <a href="{{ config('site.threads_url') }}" class="mail-btn" target="_blank" rel="noopener">ThreadsのDMで相談する →</a>
        </div>

        @if (session('inquiry_sent'))
            <div role="status" class="notice">
                <p class="font-semibold">{{ __('inquiry.sent.title') }}</p>
                <p class="mt-1 text-[14px]">{{ __('inquiry.sent.body') }}</p>
            </div>
        @else
            <form method="POST" action="{{ route('inquiries.store') }}" class="contact-form" novalidate>
                @csrf

                @if ($errors->has('website') || $errors->has('form_started_at'))
                    <p role="alert" class="form-error">{{ $errors->first('website') ?: $errors->first('form_started_at') }}</p>
                @endif

                <label class="form-field">
                    <span class="mono muted">{{ __('inquiry.attributes.name') }}</span>
                    <input type="text" name="name" value="{{ old('name') }}" required maxlength="100" autocomplete="name" class="form-input" @error('name') aria-invalid="true" @enderror>
                    @error('name')<span class="form-error">{{ $message }}</span>@enderror
                </label>

                <label class="form-field">
                    <span class="mono muted">{{ __('inquiry.attributes.email') }}</span>
                    <input type="email" name="email" value="{{ old('email') }}" required maxlength="255" autocomplete="email" class="form-input" @error('email') aria-invalid="true" @enderror>
                    @error('email')<span class="form-error">{{ $message }}</span>@enderror
                </label>

                <label class="form-field">
                    <span class="mono muted">{{ __('inquiry.attributes.message') }}</span>
                    <textarea name="message" rows="5" required maxlength="5000" class="form-input resize-y" @error('message') aria-invalid="true" @enderror>{{ old('message') }}</textarea>
                    @error('message')<span class="form-error">{{ $message }}</span>@enderror
                </label>

                {{-- スパム対策（人には見えない欄）。表示時刻を暗号化して持たせ、送信が速すぎるbotを弾く。old() では引き継がず、表示のたびに新しくする --}}
                <input type="hidden" name="form_started_at" value="{{ \Illuminate\Support\Facades\Crypt::encryptString((string) now()->getTimestamp()) }}">
                <div class="hidden" aria-hidden="true">
                    <label>空欄のままにしてください<input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                </div>

                <p class="mono muted privacy-notice">
                    {{ __('inquiry.privacy_notice.before') }}<a href="{{ route('privacy') }}" target="_blank" rel="noopener">{{ __('inquiry.privacy_notice.link') }}</a>{{ __('inquiry.privacy_notice.after') }}
                </p>

                <div>
                    <button type="submit" class="mail-btn">{{ __('inquiry.submit') }} →</button>
                </div>
            </form>
        @endif
    </section>

</div>
</x-layouts.app>
