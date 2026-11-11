<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * 管理画面のユーザーは seed で作らない（本番で弱いパスワードが残らないように）。
     * `sail artisan make:filament-user` で作成する。
     */
    public function run(): void
    {
        $this->call(WorkSeeder::class);
    }
}
