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
        Schema::create('email_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('email_thread_id')->constrained()->cascadeOnDelete();
            $table->foreignId('gmail_account_id')->constrained()->cascadeOnDelete();
            $table->string('gmail_message_id');
            $table->string('gmail_thread_id');
            $table->string('history_id')->nullable();
            $table->string('message_id_header')->nullable();
            $table->string('in_reply_to_header')->nullable();
            $table->text('references_header')->nullable();
            $table->string('sender_name')->nullable();
            $table->string('sender_email');
            $table->string('reply_to_email')->nullable();
            $table->json('to_addresses');
            $table->json('cc_addresses')->nullable();
            $table->json('bcc_addresses')->nullable();
            $table->string('subject')->nullable();
            $table->text('snippet')->nullable();
            $table->longText('text_body')->nullable();
            $table->longText('html_body')->nullable();
            $table->longText('sanitized_html_body')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->timestamp('internal_date')->nullable();
            $table->json('labels')->nullable();
            $table->boolean('is_read')->default(false);
            $table->boolean('is_starred')->default(false);
            $table->boolean('is_archived')->default(false);
            $table->boolean('has_attachments')->default(false);
            $table->string('direction')->default('unknown');
            $table->string('processing_status')->default('pending');
            $table->timestamp('ai_processed_at')->nullable();
            $table->timestamps();

            $table->unique(['gmail_account_id', 'gmail_message_id']);
            $table->index(['gmail_account_id', 'received_at']);
            $table->index(['email_thread_id', 'received_at']);
            $table->index('processing_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_messages');
    }
};
