<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('translations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('language_id');
            $table->string('key');
            $table->text('value');
            $table->string('namespace')->default('default');
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->foreign('language_id')->references('id')->on('languages')->onDelete('cascade');
            $table->unique(['language_id', 'key', 'namespace']);
            $table->index(['language_id', 'namespace']);
        });


    public function down()
    {
        Schema::dropIfExists('translations');

}
};