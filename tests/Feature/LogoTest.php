<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoTest extends TestCase
{
    use RefreshDatabase;

    public function test_トップのヘッダーとフッターにロゴ画像が出る(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee(asset('images/logo/toshlab-logo-1-black.svg'), false)
            ->assertSee(asset('images/logo/toshlab-logo-1-white.svg'), false)
            ->assertSee(asset('images/logo/toshlab-logo-3-twotone.svg'), false)
            ->assertSee(asset('images/logo/toshlab-logo-3-twotone-white.svg'), false)
            ->assertSee('alt="'.e(config('site.name')).'"', false);
    }

    public function test_トップのヒーローにロゴ画像が1行で出て文字ロゴは出ない(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        $this->assertMatchesRegularExpression('/<p class="brand".*?<\/p>/s', $html);
        preg_match('/<p class="brand".*?<\/p>/s', $html, $m);
        $brand = $m[0];

        $this->assertStringContainsString(asset('images/logo/toshlab-logo-3-twotone.svg'), $brand);
        $this->assertStringContainsString(asset('images/logo/toshlab-logo-3-twotone-white.svg'), $brand);
        $this->assertStringContainsString('data-hero-line', $brand);
        $this->assertStringNotContainsString('<span data-hero-line>tosh</span>', $html);
    }

    public function test_headにファビコンとapple_touch_iconがある(): void
    {
        $this->get('/')
            ->assertSee('rel="icon"', false)
            ->assertSee('rel="apple-touch-icon"', false);
    }

    public function test_参照しているロゴとファビコンとogp画像のファイルがある(): void
    {
        $paths = [
            'images/logo/toshlab-logo-1-black.svg',
            'images/logo/toshlab-logo-1-white.svg',
            'images/logo/toshlab-logo-3-twotone.svg',
            'images/logo/toshlab-logo-3-twotone-white.svg',
            'favicon-32.png',
            'apple-touch-icon.png',
            config('site.og_image'),
        ];

        foreach ($paths as $path) {
            $this->assertFileExists(public_path($path));
        }
    }
}
