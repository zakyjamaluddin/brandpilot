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
    Schema::create('tenant_settings', function (Blueprint $table) {
        $table->id();
        $table->foreignId('team_id')->constrained()->cascadeOnDelete();

        // Mode Penjadwalan
        $table->enum('schedule_mode', ['dynamic_delay', 'static_fixed'])->default('dynamic_delay');
        $table->integer('dynamic_delay_minutes')->default(30); // Default delay 30 menit dari tiktok
        $table->json('static_posting_hours')->nullable(); // Contoh: ["07:00", "16:00"]

        // Fitur Auto Caption (Gemini AI)
        $table->boolean('auto_caption_enabled')->default(true);
        $table->text('custom_caption_prompt')->nullable(); // Prompt opsional dari user

        // Fitur Auto Reply (Gemini AI)
        $table->boolean('auto_reply_enabled')->default(true);
        $table->text('brand_knowledge_base')->nullable(); // Konteks bisnis agar AI menjawab akurat

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_settings');
    }
};
