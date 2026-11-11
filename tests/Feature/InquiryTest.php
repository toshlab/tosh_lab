<?php

namespace Tests\Feature;

use App\Mail\InquiryReceived;
use App\Models\Inquiry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use RuntimeException;
use Tests\TestCase;

class InquiryTest extends TestCase
{
    use RefreshDatabase;

    private const VALID = [
        'name' => '山田 太郎',
        'email' => 'taro@example.com',
        'message' => '在庫管理をExcelからWebに移したいです。',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        config(['site.admin_email' => 'admin@example.com']);
    }

    public function test_お問い合わせを保存して管理者に通知メールを送る(): void
    {
        Mail::fake();

        $this->post('/inquiries', self::VALID)
            ->assertRedirect(route('home').'#contact')
            ->assertSessionHas('inquiry_sent', true);

        $this->assertDatabaseHas('inquiries', self::VALID + ['read_at' => null]);
        Mail::assertSent(InquiryReceived::class, function (InquiryReceived $mail) {
            return $mail->hasTo('admin@example.com')
                && $mail->hasReplyTo('taro@example.com')
                && $mail->hasSubject(__('inquiry.mail.subject'));
        });
    }

    public function test_送信後のトップページに完了メッセージが出る(): void
    {
        Mail::fake();

        $this->followingRedirects()
            ->post('/inquiries', self::VALID)
            ->assertSee(__('inquiry.sent.title'));
    }

    public function test_必須項目が空ならエラーになり保存しない(): void
    {
        Mail::fake();

        $this->post('/inquiries', [])
            ->assertSessionHasErrors([
                'name' => __('validation.required', ['attribute' => __('inquiry.attributes.name')]),
                'email' => __('validation.required', ['attribute' => __('inquiry.attributes.email')]),
                'message' => __('validation.required', ['attribute' => __('inquiry.attributes.message')]),
            ]);

        $this->assertDatabaseCount('inquiries', 0);
        Mail::assertNothingSent();
    }

    public function test_メールアドレスの形式が正しくなければエラーになる(): void
    {
        $this->post('/inquiries', ['email' => 'not-an-email'] + self::VALID)
            ->assertSessionHasErrors([
                'email' => __('validation.email', ['attribute' => __('inquiry.attributes.email')]),
            ]);
    }

    public function test_おとり欄に入力があればbotとして保存しない(): void
    {
        Mail::fake();

        $this->post('/inquiries', self::VALID + ['website' => 'https://spam.example'])
            ->assertSessionHasErrors(['website' => __('inquiry.rejected')]);

        $this->assertDatabaseCount('inquiries', 0);
        Mail::assertNothingSent();
    }

    public function test_通知先が未設定ならメールは送らず保存だけする(): void
    {
        Mail::fake();
        config(['site.admin_email' => null]);

        $this->post('/inquiries', self::VALID)->assertRedirect();

        $this->assertDatabaseCount('inquiries', 1);
        Mail::assertNothingSent();
    }

    public function test_通知メールの送信に失敗しても保存は成功する(): void
    {
        Mail::shouldReceive('to')->andThrow(new RuntimeException('SMTP down'));
        Log::spy();

        $this->post('/inquiries', self::VALID)
            ->assertRedirect()
            ->assertSessionHas('inquiry_sent', true);

        $this->assertDatabaseCount('inquiries', 1);
        Log::shouldHaveReceived('error')->once();
    }

    public function test_短時間の連続送信は制限される(): void
    {
        Mail::fake();

        for ($i = 0; $i < 5; $i++) {
            $this->post('/inquiries', self::VALID)->assertRedirect();
        }

        $this->post('/inquiries', self::VALID)->assertTooManyRequests();
        $this->assertSame(5, Inquiry::count());
    }
}
