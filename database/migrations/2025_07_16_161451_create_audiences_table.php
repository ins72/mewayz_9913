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
        if (!Schema::hasTable('audiences')) {
            Schema::create('audiences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('workspace_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('company')->nullable();
            $table->string('position')->nullable();
            $table->enum('type', ['contact', 'lead', 'customer', 'prospect'])->default('contact');
            $table->enum('status', ['active', 'inactive', 'hot', 'warm', 'cold', 'archived'])->default('cold');
            $table->string('source')->nullable();
            $table->json('tags')->nullable();
            $table->text('notes')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('contact_type')->nullable();
            $table->decimal('deal_value', 10, 2)->nullable();
            $table->timestamp('last_contact_date')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'type']);
            $table->index(['user_id', 'status']);
            $table->index(['email']);
        });


    /**
     * Reverse the migrations.
     */



    public function down(): void
    {
        Schema::dropIfExists('audiences');

}
};
