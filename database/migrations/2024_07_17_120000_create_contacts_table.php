<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('workspace_id')->constrained()->onDelete('cascade');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('company')->nullable();
            $table->string('job_title')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('website')->nullable();
            $table->text('notes')->nullable();
            $table->json('tags')->nullable();
            $table->string('source')->nullable(); // organic, referral, campaign, etc.
            $table->integer('lead_score')->default(0);
            $table->enum('status', ['lead', 'prospect', 'customer', 'inactive'])->default('lead');
            $table->timestamp('last_contacted_at')->nullable();
            $table->json('social_profiles')->nullable(); // LinkedIn, Twitter, etc.
            $table->json('custom_fields')->nullable();
            $table->timestamps();

            $table->index(['workspace_id', 'status']);
            $table->index(['workspace_id', 'email']);
            $table->index('lead_score');
        });


    public function down()
    {
        Schema::dropIfExists('contacts');

};