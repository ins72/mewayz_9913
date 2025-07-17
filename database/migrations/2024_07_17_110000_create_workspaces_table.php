<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('workspaces', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('domain')->nullable();
            $table->string('logo')->nullable();
            $table->json('settings')->nullable();
            $table->json('features')->nullable(); // enabled features
            $table->enum('plan', ['free', 'professional', 'enterprise'])->default('free');
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamps();
            
            $table->index('slug');
            $table->index('plan');
        });
    }

    public function down()
    {
        Schema::dropIfExists('workspaces');
    }
};