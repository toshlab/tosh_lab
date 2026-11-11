<?php

namespace App\Http\Controllers;

use App\Models\Work;
use Illuminate\View\View;

class WorkController extends Controller
{
    public function show(Work $work): View
    {
        abort_unless($work->is_published, 404);

        return view('works.show', ['work' => $work]);
    }
}
