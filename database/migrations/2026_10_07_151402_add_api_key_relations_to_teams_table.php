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
        $table->foreignId('composio_api_key_id')->nullable()->unique()->constrained('api_keys')->nullOnDelete();
        $table->foreignId('gemini_api_key_id')->nullable()->unique()->constrained('api_keys')->nullOnDelete();
        $table->foreignId('apify_api_key_id')->nullable()->unique()->constrained('api_keys')->nullOnDelete();
    });
}

public function down(): void
{
    Schema::table('teams', function (Blueprint $table) {
        $table->dropForeign(['composio_api_key_id']);
        $table->dropForeign(['gemini_api_key_id']);
        $table->dropForeign(['apify_api_key_id']);
        $table->dropColumn(['composio_api_key_id', 'gemini_api_key_id', 'apify_api_key_id']);
    });
}

  
};
