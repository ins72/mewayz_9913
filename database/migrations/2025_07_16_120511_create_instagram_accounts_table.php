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
        Schema::create('instagram_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('workspace_id')->constrained()->onDelete('cascade');
            $table->string('instagram_id')->unique();
            $table->string('username');
            $table->string('display_name');
            $table->text('bio')->nullable();
            $table->string('profile_picture_url')->nullable();
            $table->string('website')->nullable();
            $table->integer('followers_count')->default(0);
            $table->integer('following_count')->default(0);
            $table->integer('media_count')->default(0);
            $table->string('account_type')->default('personal'); // personal, business, creator
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_private')->default(false);
            $table->string('access_token')->nullable();
            $table->string('refresh_token')->nullable();
            $table->timestamp('token_expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('permissions')->nullable(); // Instagram permissions granted
            $table->json('metadata')->nullable(); // Additional account data
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instagram_accounts');
    }
};