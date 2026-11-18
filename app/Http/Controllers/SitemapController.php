<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Work;
use Illuminate\Http\Response;

/**
 * 検索エンジン向けのサイトマップ（/sitemap.xml）。
 * 公開中のページだけを載せる（下書き・予約投稿は載せない。実績は詳細ページがあるもの＝Work::withDetailPage）。
 */
class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $posts = Post::published()->get(['slug', 'published_at', 'updated_at']);
        $works = Work::withDetailPage()->get(['slug', 'updated_at']);

        $latest = collect([$posts->max('updated_at'), $works->max('updated_at')])->filter()->max();

        return response()
            ->view('seo.sitemap', compact('posts', 'works', 'latest'))
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
