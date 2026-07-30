<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql' && Schema::hasColumn('email_attachments', 'gmail_attachment_key')) {
            return;
        }

        if (DB::getDriverName() === 'mysql'
            && Schema::hasColumn('email_attachments', 'gmail_attachment_key')
            && ! $this->indexExists('email_attachments_email_message_id_gmail_attachment_id_unique')) {
            return;
        }

        if (! Schema::hasColumn('email_attachments', 'gmail_attachment_key')) {
            Schema::table('email_attachments', function (Blueprint $table) {
                $table->string('gmail_attachment_key', 64)->nullable()->after('gmail_attachment_id');
            });
        }

        DB::table('email_attachments')
            ->select(['id', 'gmail_attachment_id'])
            ->whereNull('gmail_attachment_key')
            ->orderBy('id')
            ->chunkById(500, function ($attachments): void {
                foreach ($attachments as $attachment) {
                    DB::table('email_attachments')
                        ->where('id', $attachment->id)
                        ->update(['gmail_attachment_key' => hash('sha256', $attachment->gmail_attachment_id)]);
                }
            });

        if (! $this->indexExists('email_attachments_email_message_id_index')) {
            Schema::table('email_attachments', function (Blueprint $table) {
                $table->index('email_message_id');
            });
        }

        if ($this->indexExists('email_attachments_email_message_id_gmail_attachment_id_unique')) {
            Schema::table('email_attachments', function (Blueprint $table) {
                $table->dropUnique(['email_message_id', 'gmail_attachment_id']);
            });
        }

        Schema::table('email_attachments', function (Blueprint $table) {
            $table->text('gmail_attachment_id')->change();
        });

        if (! $this->indexExists('email_attachments_email_message_id_gmail_attachment_key_unique')) {
            Schema::table('email_attachments', function (Blueprint $table) {
                $table->unique(['email_message_id', 'gmail_attachment_key']);
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('email_attachments', 'gmail_attachment_key')) {
            return;
        }

        if (DB::getDriverName() === 'mysql' && $this->indexExists('email_attachments_email_message_id_gmail_attachment_key_unique')) {
            Schema::table('email_attachments', function (Blueprint $table) {
                $table->dropUnique(['email_message_id', 'gmail_attachment_key']);
            });
        }

        Schema::table('email_attachments', function (Blueprint $table) {
            $table->string('gmail_attachment_id')->change();
        });

        if (DB::getDriverName() === 'mysql' && ! $this->indexExists('email_attachments_email_message_id_gmail_attachment_id_unique')) {
            Schema::table('email_attachments', function (Blueprint $table) {
                $table->unique(['email_message_id', 'gmail_attachment_id']);
            });
        }

        Schema::table('email_attachments', function (Blueprint $table) {
            $table->dropColumn('gmail_attachment_key');
        });
    }

    private function indexExists(string $name): bool
    {
        if (DB::getDriverName() !== 'mysql') {
            return false;
        }

        return DB::select('SHOW INDEXES FROM email_attachments WHERE Key_name = ?', [$name]) !== [];
    }
};
