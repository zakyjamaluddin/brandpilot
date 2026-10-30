<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            // Token Apify milik tenant sendiri (disimpan terenkripsi lewat cast di model).
            $table->text('apify_token')->nullable()->after('apify_api_key_id');
        });
    }

    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn('apify_token');
        });
    }
};
