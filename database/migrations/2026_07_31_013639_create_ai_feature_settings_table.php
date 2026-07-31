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
        Schema::create('ai_feature_settings', function (Blueprint $table) {
            $table->id();
            $table->string('feature')->unique();
            $table->string('name');
            $table->foreignId('ai_provider_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ai_model_id')->constrained()->cascadeOnDelete();
            $table->decimal('temperature', 3, 2)->default(0.20);
            $table->unsignedInteger('max_output_tokens')->nullable();
            $table->text('system_prompt')->nullable();
            $table->json('request_overrides')->nullable();
            $table->boolean('requires_json')->default(false);
            $table->boolean('requires_tools')->default(false);
            $table->boolean('requires_vision')->default(false);
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();

            $table->index(['is_enabled', 'feature']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_feature_settings');
    }
};
