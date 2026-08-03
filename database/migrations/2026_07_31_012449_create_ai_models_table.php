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
        Schema::create('ai_models', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ai_provider_id')->constrained()->cascadeOnDelete();
            $table->string('provider_model_id');
            $table->string('name');
            $table->string('endpoint_url', 2048)->nullable();
            $table->boolean('supports_tool_calling')->default(false);
            $table->boolean('supports_vision')->default(false);
            $table->boolean('supports_streaming')->default(true);
            $table->unsignedInteger('max_input_tokens')->nullable();
            $table->unsignedInteger('max_output_tokens')->nullable();
            $table->json('metadata')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['ai_provider_id', 'provider_model_id']);
            $table->index(['is_active', 'supports_tool_calling', 'supports_vision']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_models');
    }
};
