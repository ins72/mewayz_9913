<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ai_models', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('type'); // text, image, video, audio, etc.
            $table->string('provider'); // openai, anthropic, google, etc.
            $table->string('model_id'); // gpt-4, claude-3, etc.
            $table->json('config');
            $table->json('capabilities');
            $table->integer('usage_count')->default(0);
            $table->decimal('cost_per_request', 10, 6)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['type', 'is_active']);
            $table->index(['provider']);
        });


    public function down()
    {
        Schema::dropIfExists('ai_models');

};