<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sso_providers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('workspace_id');
            $table->string('name');
            $table->string('provider_type'); // saml, oauth, ldap
            $table->json('config');
            $table->string('entity_id')->nullable();
            $table->text('metadata_url')->nullable();
            $table->text('certificate')->nullable();
            $table->boolean('is_active')->default(false);
            $table->json('attribute_mapping')->nullable();
            $table->timestamps();
            
            $table->foreign('workspace_id')->references('id')->on('workspaces')->onDelete('cascade');
            $table->index(['workspace_id', 'is_active']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('sso_providers');
    }
};