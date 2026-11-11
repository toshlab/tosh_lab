<?php

namespace Tests\Feature\Admin;

use App\Filament\Resources\Inquiries\Pages\ViewInquiry;
use App\Models\Inquiry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_ログインしていなければログイン画面に移動する(): void
    {
        $this->get('/admin/works')->assertRedirect('/admin/login');
    }

    public function test_ログインすれば各管理ページを開ける(): void
    {
        $this->actingAs(User::factory()->create());

        foreach (['/admin', '/admin/works', '/admin/works/create', '/admin/posts', '/admin/posts/create', '/admin/inquiries'] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_お問い合わせは管理画面から新規作成できない(): void
    {
        $this->actingAs(User::factory()->create());

        $this->get('/admin/inquiries/create')->assertNotFound();
    }

    public function test_お問い合わせを開くと既読になる(): void
    {
        $this->actingAs(User::factory()->create());
        $inquiry = Inquiry::factory()->create();

        Livewire::test(ViewInquiry::class, ['record' => $inquiry->getRouteKey()])
            ->assertOk();

        $this->assertNotNull($inquiry->fresh()->read_at);
    }
}
