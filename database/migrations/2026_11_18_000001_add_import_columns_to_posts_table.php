<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * GitHub の Markdown（content/posts/）から記事を取り込むための列（docs/blog-import-plan.md）。
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->json('tags')->nullable()->after('category');
            // admin = 管理画面で作った記事 / file = content/posts/ のファイルから取り込んだ記事
            $table->string('source', 20)->default('admin')->after('published_at')->index();
            // 取り込んだファイルのパス（content/posts/ からの相対パス）
            $table->string('source_path')->nullable()->unique()->after('source');
            // ファイルの中身の SHA-256。変わっていなければ取り込みを飛ばす
            $table->char('source_hash', 64)->nullable()->after('source_path');
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropUnique(['source_path']);
            $table->dropIndex(['source']);
            $table->dropColumn(['tags', 'source', 'source_path', 'source_hash']);
        });
    }
};
