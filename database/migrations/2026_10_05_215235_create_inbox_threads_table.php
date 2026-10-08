<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // create_inbox_threads_table.php
public function up(): void
{
    Schema::create('inbox_threads', function (Blueprint $table) {
        $table->id();
        $table->foreignId('team_id')->constrained()->cascadeOnDelete();
        $table->string('platform'); // facebook, instagram, youtube
        $table->enum('type', ['comment', 'direct_message']);
        $table->string('external_id')->index(); // ID komentar atau thread DM dari sosmed
        $table->string('sender_name');
        $table->string('sender_avatar')->nullable();
        $table->text('last_snippet')->nullable();
        $table->timestamp('last_interaction_at')->nullable();
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inbox_threads');
    }
};
