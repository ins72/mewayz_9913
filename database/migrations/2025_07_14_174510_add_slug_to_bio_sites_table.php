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
        Schema::table('bio_sites', function (Blueprint $table) {
            if (!Schema::hasColumn('bio_sites', 'slug')) {
                $table->string('slug')->unique()->after('name');

            if (!Schema::hasColumn('bio_sites', 'title')) {
                $table->string('title')->nullable()->after('name');

            if (!Schema::hasColumn('bio_sites', 'description')) {
                $table->text('description')->nullable()->after('bio');

            if (!Schema::hasColumn('bio_sites', 'theme_config')) {
                $table->json('theme_config')->nullable()->after('description');

            if (!Schema::hasColumn('bio_sites', 'view_count')) {
                $table->integer('view_count')->default(0)->after('theme_config');

            if (!Schema::hasColumn('bio_sites', 'click_count')) {
                $table->integer('click_count')->default(0)->after('view_count');

            if (!Schema::hasColumn('bio_sites', 'template_id')) {
                $table->integer('template_id')->default(1)->after('click_count');

        });


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bio_sites', function (Blueprint $table) {
            $table->dropColumn(['slug', 'title', 'description', 'theme_config', 'view_count', 'click_count', 'template_id']);
        });

};