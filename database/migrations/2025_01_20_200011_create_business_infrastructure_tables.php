<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Contact Messages
        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('subject');
            $table->text('message');
            $table->string('phone')->nullable();
            $table->string('company')->nullable();
            $table->enum('inquiry_type', ['sales', 'support', 'partnership', 'general']);
            $table->ipAddress('ip_address');
            $table->text('user_agent')->nullable();
            $table->enum('status', ['new', 'in_progress', 'resolved', 'closed'])->default('new');
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['inquiry_type', 'created_at']);
        });

        // Testimonials
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('title')->nullable();
            $table->string('company')->nullable();
            $table->text('content');
            $table->integer('rating')->default(5);
            $table->string('image')->nullable();
            $table->string('company_logo')->nullable();
            $table->boolean('is_published')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();

            $table->index(['is_published', 'rating']);
            $table->index(['is_featured', 'created_at']);
        });

        // Case Studies
        Schema::create('case_studies', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt');
            $table->longText('content');
            $table->string('client_name');
            $table->string('client_logo')->nullable();
            $table->string('industry');
            $table->json('challenges')->nullable();
            $table->json('solutions')->nullable();
            $table->json('results')->nullable();
            $table->json('metrics')->nullable();
            $table->string('featured_image')->nullable();
            $table->json('gallery')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['is_published', 'published_at']);
            $table->index(['industry', 'is_published']);
        });

        // Blog Posts
        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt');
            $table->longText('content');
            $table->string('featured_image')->nullable();
            $table->string('category');
            $table->json('tags')->nullable();
            $table->unsignedBigInteger('author_id');
            $table->boolean('is_published')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->integer('views')->default(0);
            $table->timestamps();

            $table->foreign('author_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['is_published', 'published_at']);
            $table->index(['category', 'is_published']);
            $table->index(['is_featured', 'published_at']);
        });

        // Press Releases
        Schema::create('press_releases', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt');
            $table->longText('content');
            $table->string('featured_image')->nullable();
            $table->json('attachments')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['is_published', 'published_at']);
        });

        // Jobs
        Schema::create('jobs_listings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('department');
            $table->string('location');
            $table->enum('type', ['full_time', 'part_time', 'contract', 'freelance', 'internship']);
            $table->enum('remote_type', ['on_site', 'remote', 'hybrid']);
            $table->decimal('salary_min', 10, 2)->nullable();
            $table->decimal('salary_max', 10, 2)->nullable();
            $table->string('salary_currency', 3)->default('USD');
            $table->json('requirements')->nullable();
            $table->json('benefits')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('posted_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['is_active', 'posted_at']);
            $table->index(['department', 'is_active']);
        });

        // Partners
        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('logo')->nullable();
            $table->string('website')->nullable();
            $table->enum('type', ['technology', 'integration', 'consulting', 'reseller']);
            $table->enum('tier', ['platinum', 'gold', 'silver', 'bronze']);
            $table->json('services')->nullable();
            $table->json('certifications')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['type', 'is_active']);
            $table->index(['tier', 'is_active']);
        });

        // Features
        Schema::create('features_showcase', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('icon')->nullable();
            $table->string('category');
            $table->json('benefits')->nullable();
            $table->json('screenshots')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->index(['category', 'is_active', 'order']);
        });

        // Pricing Plans
        Schema::create('pricing_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->decimal('price', 10, 2);
            $table->enum('billing_period', ['monthly', 'annually', 'lifetime']);
            $table->json('features')->nullable();
            $table->json('limitations')->nullable();
            $table->boolean('is_popular')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'order']);
        });

        // Status Updates
        Schema::create('status_updates', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->enum('type', ['incident', 'maintenance', 'notice']);
            $table->enum('status', ['investigating', 'identified', 'monitoring', 'resolved', 'scheduled', 'in_progress', 'completed']);
            $table->enum('severity', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->json('affected_services')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->boolean('is_public')->default(true);
            $table->timestamps();

            $table->index(['type', 'is_public', 'created_at']);
            $table->index(['status', 'severity']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('status_updates');
        Schema::dropIfExists('pricing_plans');
        Schema::dropIfExists('features_showcase');
        Schema::dropIfExists('partners');
        Schema::dropIfExists('jobs_listings');
        Schema::dropIfExists('press_releases');
        Schema::dropIfExists('blog_posts');
        Schema::dropIfExists('case_studies');
        Schema::dropIfExists('testimonials');
        Schema::dropIfExists('contact_messages');
    }
};