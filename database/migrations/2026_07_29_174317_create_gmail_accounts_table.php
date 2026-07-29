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
        Schema::create('gmail_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('google_email');
            $table->text('access_token');
            $table->text('refresh_token')->nullable();
            $table->timestamp('token_expires_at')->nullable();
            $table->string('history_id')->nullable();
            $table->timestamp('watch_expires_at')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->string('sync_status')->default('connected');
            $table->text('sync_error')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'google_email']);
            $table->index(['user_id', 'sync_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gmail_accounts');
    }
};
