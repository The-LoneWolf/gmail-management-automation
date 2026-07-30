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
        Schema::create('google_o_auth_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('Default Google OAuth');
            $table->string('client_id');
            $table->text('client_secret');
            $table->string('redirect_uri');
            $table->json('scopes');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('google_o_auth_configurations');
    }
};
