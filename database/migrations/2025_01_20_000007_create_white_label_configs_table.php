<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('white_label_configs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('workspace_id');
            $table->string('company_name');
            $table->string('logo_url')->nullable();
            $table->string('favicon_url')->nullable();
            $table->string('primary_color')->default('#000000');
            $table->string('secondary_color')->default('#ffffff');
            $table->string('accent_color')->default('#007bff');
            $table->string('custom_domain')->nullable();
            $table->json('email_templates')->nullable();
            $table->json('custom_css')->nullable();
            $table->json('custom_js')->nullable();
            $table->boolean('hide_platform_branding')->default(false);
            $table->boolean('custom_login_page')->default(false);
            $table->json('login_page_config')->nullable();
            $table->timestamps();

            $table->foreign('workspace_id')->references('id')->on('workspaces')->onDelete('cascade');
            $table->unique(['workspace_id']);
        });


    public function down()
    {
        Schema::dropIfExists('white_label_configs');

};