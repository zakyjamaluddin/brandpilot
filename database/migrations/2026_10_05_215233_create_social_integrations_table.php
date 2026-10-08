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
        Schema::create('social_integrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->string('platform'); // facebook, instagram, youtube
            $table->string('composio_connection_id')->nullable(); // ID koneksi dari Composio
            $table->string('account_name')->nullable();
            $table->boolean('is_connected')->default(false);
            $table->json('metadata')->nullable(); // Simpan page_id, channel_id, dll.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_integrations');
    }
};
