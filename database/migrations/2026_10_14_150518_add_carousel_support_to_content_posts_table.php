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
            $table->string('media_type')->default('video')->after('title'); // 'video' atau 'carousel'
            $table->json('media_gallery')->nullable()->after('video_url');  // Menyimpan daftar URL slide foto
        });
    }

    public function down(): void
    {
        Schema::table('content_posts', function (Blueprint $table) {
            $table->dropColumn(['media_type', 'media_gallery']);
        });
    }
};
