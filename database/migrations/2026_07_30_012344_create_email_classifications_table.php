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
        Schema::create('email_classifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('email_message_id')->constrained()->cascadeOnDelete();
            $table->text('summary');
            $table->foreignId('suggested_state_id')->nullable()->constrained('states')->nullOnDelete();
            $table->decimal('state_confidence', 4, 2)->default(0);
            $table->string('priority')->default('normal');
            $table->string('sentiment')->default('unknown');
            $table->string('language')->nullable();
            $table->boolean('requires_reply')->default(false);
            $table->boolean('requires_human_review')->default(false);
            $table->json('suggested_actions')->nullable();
            $table->json('raw_result');
            $table->string('model');
            $table->string('prompt_version');
            $table->string('status')->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['email_message_id', 'status']);
        });

        Schema::create('email_message_topic', function (Blueprint $table) {
            $table->id();
            $table->foreignId('email_message_id')->constrained()->cascadeOnDelete();
            $table->foreignId('topic_id')->constrained()->cascadeOnDelete();
            $table->decimal('confidence', 4, 2)->default(0);
            $table->string('matched_by')->default('ai');
            $table->text('reasoning')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamps();

            $table->unique(['email_message_id', 'topic_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_message_topic');
        Schema::dropIfExists('email_classifications');
    }
};
