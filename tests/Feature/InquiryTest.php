<?php

namespace Tests\Feature;

use App\Mail\InquiryReceived;
use App\Models\Inquiry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
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

        $this->travelTo(now()->startOfSecond());

        config(['site.admin_email' => 'admin@example.com']);
    }

    /** 表示から指定秒数たった時点の、正しい form_started_at を作る（時刻は travelTo で固定済み） */
    private function startedAt(int $secondsAgo = 10): string
    {
        return Crypt::encryptString((string) (now()->getTimestamp() - $secondsAgo));
    }

    /** @return array<string, string> */
    private function validInput(int $secondsAgo = 10): array
    {
        return self::VALID + ['form_started_at' => $this->startedAt($secondsAgo)];
    }

    public function test_お問い合わせを保存して管理者に通知メールを送る(): void
    {
        Mail::fake();

        $this->post('/inquiries', $this->validInput())
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
            ->post('/inquiries', $this->validInput())
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
        $this->post('/inquiries', ['email' => 'not-an-email'] + $this->validInput())
            ->assertSessionHasErrors([
                'email' => __('validation.email', ['attribute' => __('inquiry.attributes.email')]),
            ]);
    }

    public function test_おとり欄に入力があればbotとして保存しない(): void
    {
        Mail::fake();

        $this->post('/inquiries', $this->validInput() + ['website' => 'https://spam.example'])
            ->assertSessionHasErrors(['website' => __('inquiry.rejected')]);

        $this->assertDatabaseCount('inquiries', 0);
        Mail::assertNothingSent();
    }

    public function test_通知先が未設定ならメールは送らず保存だけする(): void
    {
        Mail::fake();
        config(['site.admin_email' => null]);

        $this->post('/inquiries', $this->validInput())->assertRedirect();

        $this->assertDatabaseCount('inquiries', 1);
        Mail::assertNothingSent();
    }

    public function test_通知メールの送信に失敗しても保存は成功する(): void
    {
        Mail::shouldReceive('to')->andThrow(new RuntimeException('SMTP down'));
        Log::spy();

        $this->post('/inquiries', $this->validInput())
            ->assertRedirect()
            ->assertSessionHas('inquiry_sent', true);

        $this->assertDatabaseCount('inquiries', 1);
        Log::shouldHaveReceived('error')->once();
    }

    public function test_短時間の連続送信は制限される(): void
    {
        Mail::fake();

        for ($i = 0; $i < 5; $i++) {
            $this->post('/inquiries', $this->validInput())->assertRedirect();
        }

        $this->post('/inquiries', $this->validInput())->assertTooManyRequests();
        $this->assertSame(5, Inquiry::count());
    }

    public function test_表示からちょうど3秒たっていれば受け付ける(): void
    {
        Mail::fake();

        $this->post('/inquiries', $this->validInput(Inquiry::MIN_SUBMIT_SECONDS))
            ->assertSessionHas('inquiry_sent', true);

        $this->assertDatabaseCount('inquiries', 1);
        Mail::assertSent(InquiryReceived::class);
    }

    public function test_表示から3秒未満の送信はbotとして保存しない(): void
    {
        Mail::fake();

        $this->post('/inquiries', $this->validInput(Inquiry::MIN_SUBMIT_SECONDS - 1))
            ->assertSessionHasErrors(['form_started_at' => __('inquiry.rejected')]);

        $this->assertDatabaseCount('inquiries', 0);
        Mail::assertNothingSent();
    }

    public function test_表示時刻の項目がなければ保存しない(): void
    {
        Mail::fake();

        $this->post('/inquiries', self::VALID)
            ->assertSessionHasErrors(['form_started_at' => __('inquiry.rejected')]);

        $this->assertDatabaseCount('inquiries', 0);
        Mail::assertNothingSent();
    }

    public function test_表示時刻が改ざんされていれば保存しない(): void
    {
        Mail::fake();

        $this->post('/inquiries', self::VALID + ['form_started_at' => 'tampered'])
            ->assertSessionHasErrors(['form_started_at' => __('inquiry.rejected')]);

        $this->assertDatabaseCount('inquiries', 0);
        Mail::assertNothingSent();
    }

    public function test_表示時刻が未来なら保存しない(): void
    {
        Mail::fake();

        $this->post('/inquiries', $this->validInput(-60))
            ->assertSessionHasErrors(['form_started_at' => __('inquiry.rejected')]);

        $this->assertDatabaseCount('inquiries', 0);
        Mail::assertNothingSent();
    }

    public function test_拒否されたときフォームに拒否の文言が出る(): void
    {
        $this->followingRedirects()
            ->from('/')
            ->post('/inquiries', self::VALID)
            ->assertSee(__('inquiry.rejected'));
    }

    public function test_トップページのフォームに表示時刻の隠し項目がある(): void
    {
        $this->get('/')->assertSee('name="form_started_at"', false);
    }
}
