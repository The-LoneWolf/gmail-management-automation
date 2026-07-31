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
        Schema::create('ai_providers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('vendor')->default('customendpoint');
            $table->string('api_type')->default('chat-completions');
            $table->string('endpoint_url', 2048);
            $table->text('api_key')->nullable();
            $table->text('secret_headers')->nullable();
            $table->json('default_body')->nullable();
            $table->unsignedSmallInteger('timeout_seconds')->default(60);
            $table->unsignedTinyInteger('retry_attempts')->default(2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['vendor', 'api_type']);
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_providers');
    }
};
