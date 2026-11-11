<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\WorkController;
use Illuminate\Support\Facades\Route;

/*
| 訪問者向けの公開ページ
*/

Route::get('/', HomeController::class)->name('home');

Route::get('/works/{work}', [WorkController::class, 'show'])->name('works.show');

Route::get('/blog', [PostController::class, 'index'])->name('posts.index');
Route::get('/blog/{post}', [PostController::class, 'show'])->name('posts.show');

// 10分に5回まで。フォームの連投・bot対策
Route::post('/inquiries', [InquiryController::class, 'store'])
    ->middleware('throttle:5,10')
    ->name('inquiries.store');
