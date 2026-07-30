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
        Schema::create('email_extractions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('email_message_id')->constrained()->cascadeOnDelete();
            $table->foreignId('extraction_template_id')->constrained()->cascadeOnDelete();
            $table->json('extracted_data');
            $table->decimal('confidence', 4, 2)->default(0);
            $table->string('model');
            $table->string('prompt_version');
            $table->string('status')->default('pending');
            $table->text('error_message')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->unique(['email_message_id', 'extraction_template_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_extractions');
    }
};
