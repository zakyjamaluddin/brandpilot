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
        Schema::table('teams', function (Blueprint $table) {
            $table->string('tiktok_profile_url')->nullable()->after('tiktok_handle');
            $table->boolean('tiktok_ownership_confirmed')->default(false)->after('tiktok_profile_url');
            $table->string('last_tiktok_post_id')->nullable()->after('tiktok_ownership_confirmed');
        });
    }

    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn(['tiktok_profile_url', 'tiktok_ownership_confirmed', 'last_tiktok_post_id']);
        });
    }
};
