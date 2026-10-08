<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('content_posts', function (Blueprint $table) {
            $table->string('caption_type')->default('manual')->after('original_caption'); // 'manual' atau 'gemini_ai'
            $table->string('video_url')->nullable()->change(); // bisa berupa path lokal storage
        });
    }

    public function down(): void
    {
        Schema::table('content_posts', function (Blueprint $table) {
            $table->dropColumn('caption_type');
        });
    }
};
