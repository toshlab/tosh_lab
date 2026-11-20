<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FontTest extends TestCase
{
    use RefreshDatabase;

    private const BUNGEE_PATTERN = '/<link rel="stylesheet" href="https:\/\/fonts\.googleapis\.com\/css2\?[^"]*family=Bungee[&"]/';

    public function test_トップのフォント読み込みにbungeeが含まれる(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        $this->assertMatchesRegularExpression(self::BUNGEE_PATTERN, $html);
    }

    public function test_ブログ一覧のフォント読み込みにbungeeが含まれる(): void
    {
        $html = $this->get('/blog')->assertOk()->getContent();

        $this->assertMatchesRegularExpression(self::BUNGEE_PATTERN, $html);
    }

    public function test_cssに装飾用フォントのトークンとbungeeがある(): void
    {
        $css = file_get_contents(resource_path('css/app.css'));

        $this->assertStringContainsString('--font-display', $css);
        $this->assertStringContainsString('Bungee', $css);
    }
}
