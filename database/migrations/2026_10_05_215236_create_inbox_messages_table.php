<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */

// create_inbox_messages_table.php
public function up(): void
{
    Schema::create('inbox_messages', function (Blueprint $table) {
        $table->id();
        $table->foreignId('inbox_thread_id')->constrained()->cascadeOnDelete();
        $table->enum('sender_type', ['customer', 'ai', 'user']); // Siapa yang mengirim
        $table->text('message');
        $table->timestamp('sent_at');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inbox_messages');
    }
};
