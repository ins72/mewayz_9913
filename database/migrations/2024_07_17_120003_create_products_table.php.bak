<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('workspace_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('sku')->unique();
            $table->text('description')->nullable();
            $table->text('short_description')->nullable();
            $table->decimal('price', 15, 2);
            $table->decimal('cost_price', 15, 2)->nullable();
            $table->string('currency', 3)->default('USD');
            $table->enum('type', ['physical', 'digital', 'service'])->default('physical');
            $table->enum('status', ['active', 'inactive', 'draft'])->default('active');
            $table->integer('stock_quantity')->default(0);
            $table->boolean('track_inventory')->default(true);
            $table->integer('low_stock_threshold')->default(10);
            $table->decimal('weight', 8, 2)->nullable();
            $table->json('dimensions')->nullable(); // length, width, height
            $table->json('images')->nullable();
            $table->json('variants')->nullable(); // size, color, etc.
            $table->json('seo_data')->nullable(); // meta title, description, keywords
            $table->json('tags')->nullable();
            $table->json('custom_fields')->nullable();
            $table->timestamps();
            
            $table->index(['workspace_id', 'status']);
            $table->index(['workspace_id', 'type']);
            $table->index('sku');
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
};