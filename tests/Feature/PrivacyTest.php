<?php

namespace Tests\Feature;

use Tests\TestCase;

class PrivacyTest extends TestCase
{
    public function test_プライバシーポリシーが表示される(): void
    {
        $this->get('/privacy')->assertOk();
    }

    public function test_ルート名privacyは_privacyを指す(): void
    {
        $this->assertSame('/privacy', route('privacy', absolute: false));
    }

    public function test_タイトルとh1がプライバシーポリシーになっている(): void
    {
        $html = $this->get('/privacy')->getContent();

        $this->assertStringContainsString('<title>プライバシーポリシー｜toshLab</title>', $html);
        $this->assertSame(1, substr_count($html, '<h1'));
        $this->assertMatchesRegularExpression('#<h1[^>]*>プライバシーポリシー</h1>#u', $html);
    }

    public function test_h2が仕様の順に並んでいる(): void
    {
        preg_match_all('#<h2[^>]*>(.*?)</h2>#u', $this->get('/privacy')->getContent(), $m);

        $this->assertSame([
            '1. はじめに',
            '2. お預かりする情報（フォームの入力内容）',
            '3. 利用目的',
            '4. IP アドレスなどの扱い',
            '5. お預かりした情報の提供と、メール送信の委託',
            '6. サイトの表示のための外部送信',
            '7. Cookie について',
            '8. 保存期間と削除',
            '9. 安全管理について',
            '10. 開示・訂正・削除のご依頼と、苦情・ご相談の窓口',
            '11. 改定について・制定日',
        ], $m[1]);
    }

    public function test_運営者名と制定日が表示される(): void
    {
        $this->get('/privacy')
            ->assertSee(config('site.name').'（運営者: '.config('site.owner.name').'）', false)
            ->assertSee('制定日: 2026年11月20日');
    }

    public function test_誇大な表現を含まない(): void
    {
        $text = strip_tags($this->get('/privacy')->getContent());

        foreach (['絶対', '必ず', '完全', '万全', '100%', '最高水準'] as $word) {
            $this->assertStringNotContainsString($word, $text);
        }
    }

    public function test_トップのフォームの送信ボタンの直前に注記とリンクがある(): void
    {
        $html = $this->get('/')->getContent();
        $link = '<a href="'.route('privacy').'" target="_blank" rel="noopener">'.__('inquiry.privacy_notice.link').'</a>'.__('inquiry.privacy_notice.after');

        $this->assertStringContainsString($link, $html);
        $this->assertLessThan(
            strpos($html, __('inquiry.submit').' →</button>'),
            strpos($html, $link),
        );
    }

    public function test_6節にgithubの記述があり同意のうえの文言がない(): void
    {
        $text = strip_tags($this->get('/privacy')->getContent());
        $main = substr($text, strpos($text, 'このページでは'), strpos($text, '制定日') - strpos($text, 'このページでは'));

        $this->assertStringContainsString('GitHub（このサイトのソースコードを公開しているページ）へのリンク', $main);
        $this->assertStringContainsString('Threads や GitHub へ個人情報を送ることはありません', $main);
        $this->assertStringNotContainsString('同意のうえ', $main);
        $this->assertStringNotContainsString('同意のうえ', $this->get('/')->getContent());
    }

    public function test_github_urlを設定するとフッターにgithubリンクが出る(): void
    {
        config(['site.github_url' => 'https://github.com/toshlab/tosh_lab']);

        $this->get('/privacy')->assertSee('<a href="https://github.com/toshlab/tosh_lab" rel="noopener" target="_blank">GitHub</a>', false);
    }

    public function test_github_url未設定ならフッターにgithubリンクが出ない(): void
    {
        config(['site.github_url' => null]);

        $this->get('/privacy')->assertDontSee('target="_blank">GitHub</a>', false);
    }

    public function test_google_fontsの外部送信の3項目と外部リンク属性(): void
    {
        $html = $this->get('/privacy')->getContent();

        foreach (['送信先', '送信される情報', '利用目的', 'fonts.googleapis.com', 'fonts.gstatic.com'] as $word) {
            $this->assertStringContainsString($word, $html);
        }
        $this->assertSame(2, substr_count($html, '<a href="https://policies.google.com/privacy" target="_blank" rel="noopener">'));
    }

    public function test_バックアップ14日と手元の写しとip保存しない旨が書かれている(): void
    {
        $this->get('/privacy')
            ->assertSee('最大14日ほど残り')
            ->assertSee('IP アドレスもブラウザの種類も保存しません')
            ->assertSee('ご請求があれば遅滞なくお知らせします');
    }

    public function test_手元の写しは最新の1件だけで月1回の整理の対象は記録とメールだけ(): void
    {
        $html = $this->get('/privacy')->getContent();
        $this->assertStringContainsString('取るたびに古い写しは自動で消え、手元には最新の1件だけが残ります。', $html);
        $this->assertStringContainsString('次に写しを取り直すまで、手元の写しに残ることがあります。', $html);
        $this->assertStringContainsString('管理画面の記録と通知メールの削除は、運営者が月に1回ほど、まとめて整理して行います。', $html);
        $this->assertStringNotContainsString('手元の写しをまとめて整理', $html);
        $this->assertStringNotContainsString('この手元の写しも、上の1年の目安', $html);
    }

    public function test_注記の文言は確認のうえになっている(): void
    {
        $this->assertSame('をご確認のうえ、送信してください。', __('inquiry.privacy_notice.after'));
        $this->get('/')->assertSee('をご確認のうえ、送信してください。');
    }

    public function test_送信完了後も完了メッセージが表示される(): void
    {
        $this->withSession(['inquiry_sent' => true])->get('/')
            ->assertSee(__('inquiry.sent.title'));
    }

    public function test_フッターにリンクがありトップとブログとポリシーで表示される(): void
    {
        foreach (['/', '/blog', '/privacy'] as $path) {
            $this->get($path)->assertSee('<a href="'.route('privacy').'">プライバシーポリシー</a>', false);
        }
    }

    public function test_サイトマップにprivacyが含まれる(): void
    {
        $this->get('/sitemap.xml')->assertSee('<loc>'.route('privacy').'</loc>', false);
    }
}
