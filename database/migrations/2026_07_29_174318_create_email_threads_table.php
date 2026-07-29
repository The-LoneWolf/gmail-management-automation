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
        Schema::create('email_threads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gmail_account_id')->constrained()->cascadeOnDelete();
            $table->string('gmail_thread_id');
            $table->string('subject')->nullable();
            $table->json('participants')->nullable();
            $table->timestamp('last_message_at')->nullable();
            $table->unsignedInteger('message_count')->default(0);
            $table->foreignId('current_state_id')->nullable();
            $table->foreignId('assigned_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('ai_summary')->nullable();
            $table->string('priority')->nullable();
            $table->boolean('requires_reply')->default(false);
            $table->boolean('requires_human_review')->default(false);
            $table->timestamps();

            $table->unique(['gmail_account_id', 'gmail_thread_id']);
            $table->index(['gmail_account_id', 'last_message_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_threads');
    }
};
