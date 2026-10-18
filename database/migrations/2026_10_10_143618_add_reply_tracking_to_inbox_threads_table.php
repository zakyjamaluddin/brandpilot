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
        Schema::table('inbox_threads', function (Blueprint $table) {
            $table->boolean('is_replied')->default(false)->after('last_interaction_at');
            $table->timestamp('replied_at')->nullable()->after('is_replied');
        });
    }

    public function down(): void
    {
        Schema::table('inbox_threads', function (Blueprint $table) {
            $table->dropColumn(['is_replied', 'replied_at']);
        });
    }
};
