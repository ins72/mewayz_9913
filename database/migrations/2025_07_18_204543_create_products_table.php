<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->longText('content')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('compare_at_price', 10, 2)->nullable();
            $table->string('sku')->nullable();
            $table->integer('inventory_quantity')->default(0);
            $table->boolean('track_inventory')->default(true);
            $table->enum('type', ['physical', 'digital', 'service'])->default('physical');
            $table->string('status')->default('active');
            $table->json('images')->nullable();
            $table->json('variants')->nullable();
            $table->json('options')->nullable();
            $table->json('tags')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->decimal('weight', 8, 2)->nullable();
            $table->json('dimensions')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['status', 'created_at']);
            $table->index(['user_id', 'status']);
            $table->index(['type', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
};
