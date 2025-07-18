<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cookie_consents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('session_id');
            $table->enum('consent_type', ['accept_all', 'reject_all', 'customize']);
            $table->json('cookies_accepted')->nullable();
            $table->ipAddress('ip_address');
            $table->text('user_agent')->nullable();
            $table->timestamp('consented_at');
            $table->timestamp('expires_at');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['session_id', 'expires_at']);
            $table->index(['ip_address', 'consented_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cookie_consents');
    }
};