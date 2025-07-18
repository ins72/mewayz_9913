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
        // Bio Sites Table
        if (!Schema::hasTable('bio_sites')) {
            Schema::create('bio_sites', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id');
                $table->string('name')->nullable();
                $table->string('title')->nullable(); // Added for API compatibility
                $table->string('slug')->nullable(); // Added for API compatibility
                $table->text('description')->nullable(); // Added for API compatibility
                $table->string('address')->unique();
                $table->longText('bio')->nullable();
                $table->longText('background')->nullable();
                $table->longText('settings')->nullable();
                $table->longText('colors')->nullable();
                $table->json('theme_config')->nullable(); // Added for API compatibility
                $table->string('theme')->nullable(); // Added for API compatibility
                $table->string('logo')->nullable();
                $table->string('_slug')->nullable();
                $table->longText('membership')->nullable();
                $table->longText('qr')->nullable();
                $table->string('seo_image')->nullable();
                $table->string('qr_bg')->nullable();
                $table->string('_domain')->nullable();
                $table->string('qr_logo')->nullable();
                $table->longText('pwa')->nullable();
                $table->longText('contact')->nullable();
                $table->longText('seo')->nullable();
                $table->integer('is_template')->default(0);
                $table->boolean('is_active')->default(true); // Added for API compatibility
                $table->longText('social')->nullable();
                $table->string('banner')->nullable();
                $table->longText('interest')->nullable();
                $table->longText('connect_u')->nullable();
                $table->integer('banned')->default(0);
                $table->integer('status')->default(0);
                $table->string('meta_title')->nullable(); // Added for API compatibility
                $table->text('meta_description')->nullable(); // Added for API compatibility
                $table->softDeletes();
                $table->timestamps();
            });
        }

        // Bio Sites Uploads Table
        if (!Schema::hasTable('bio_sites_uploads')) {
            Schema::create('bio_sites_uploads', function (Blueprint $table) {
                $table->id();
                $table->integer('site_id')->nullable();
                $table->string('size')->default(0);
                $table->integer('trashed')->default(0);
                $table->string('name')->nullable();
                $table->text('path')->nullable();
                $table->integer('is_ai')->default(0);
                $table->integer('saved_ai')->default(0);
                $table->text('temp_ai_url')->nullable();
                $table->integer('ai_uploaded')->default(0);
                $table->softDeletes();
                $table->timestamps();
            });
        }

        // Bio Sites Visitors Table
        if (!Schema::hasTable('bio_sites_visitors')) {
            Schema::create('bio_sites_visitors', function (Blueprint $table) {
                $table->id();
                $table->integer('site_id')->nullable();
                $table->string('slug')->nullable();
                $table->string('session')->nullable();
                $table->string('ip')->nullable();
                $table->longText('tracking')->nullable();
                $table->string('page_slug')->nullable();
                $table->integer('views')->default(0);
                $table->timestamps();
            });
        }

        // Bio Sites Linker Track Table
        if (!Schema::hasTable('bio_sites_linker_track')) {
            Schema::create('bio_sites_linker_track', function (Blueprint $table) {
                $table->id();
                $table->integer('linker')->nullable();
                $table->integer('site_id')->nullable();
                $table->string('session')->nullable();
                $table->text('link')->nullable();
                $table->string('slug')->nullable();
                $table->string('ip')->nullable();
                $table->longText('tracking')->nullable();
                $table->integer('views')->default(1);
                $table->timestamps();
            });
        }

        // Bio Sites Linker Table
        if (!Schema::hasTable('bio_sites_linker')) {
            Schema::create('bio_sites_linker', function (Blueprint $table) {
                $table->id();
                $table->integer('site_id')->nullable();
                $table->text('url')->nullable();
                $table->string('slug')->nullable();
                $table->timestamps();
            });
        }

        // Bio Pages Table
        if (!Schema::hasTable('bio_pages')) {
            Schema::create('bio_pages', function (Blueprint $table) {
                $table->id();
                $table->uuid()->after('id');
                $table->integer('site_id');
                $table->string('name')->nullable();
                $table->string('slug')->nullable();
                $table->integer('published')->default(0);
                $table->integer('default')->default(0);
                $table->longText('settings')->nullable();
                $table->softDeletes();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bio_sites');
    }
};
