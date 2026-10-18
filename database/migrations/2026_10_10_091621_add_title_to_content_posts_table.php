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
            $table->string('title')->nullable()->after('tiktok_post_id');
        });
    }

    public function down(): void
    {
        Schema::table('content_posts', function (Blueprint $table) {
            $table->dropColumn('title');
        });
    }
};
