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
        Schema::create('email_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('email_message_id')->constrained()->cascadeOnDelete();
            $table->text('gmail_attachment_id');
            $table->string('gmail_attachment_key', 64);
            $table->string('filename');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size_bytes')->default(0);
            $table->string('storage_disk')->nullable();
            $table->string('storage_path')->nullable();
            $table->string('content_hash')->nullable();
            $table->longText('extracted_text')->nullable();
            $table->boolean('is_downloaded')->default(false);
            $table->timestamps();

            $table->unique(['email_message_id', 'gmail_attachment_key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_attachments');
    }
};
