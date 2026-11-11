<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Work;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view('home', [
            'works' => Work::published()->get(),
            'posts' => Post::published()->take(config('const.home_posts_count'))->get(),
        ]);
    }
}
