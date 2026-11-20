<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

/**
 * プライバシーポリシー（/privacy）。本文は Blade に直接書いている。
 */
class PrivacyController extends Controller
{
    public function __invoke(): View
    {
        return view('privacy.show');
    }
}
