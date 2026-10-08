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
    Schema::create('content_posts', function (Blueprint $table) {
        $table->id();
        $table->foreignId('team_id')->constrained()->cascadeOnDelete();
        $table->string('tiktok_post_id')->nullable()->index();
        $table->text('original_caption')->nullable();
        $table->text('ai_generated_caption')->nullable();
        $table->text('video_url');

        // Status postingan: pending, processing, published, failed
        $table->string('status')->default('pending');
        $table->timestamp('scheduled_at')->nullable();
        $table->timestamp('published_at')->nullable();

        // Agregasi statistik views dan komentar dari platform tujuan
        $table->unsignedBigInteger('total_views')->default(0);
        $table->unsignedBigInteger('total_comments')->default(0);
        $table->json('platform_post_ids')->nullable(); // ID postingan FB, IG, YT

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_posts');
    }
};
