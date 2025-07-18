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
        if (!Schema::hasTable('settings')) {
            Schema::create('settings', function (Blueprint $table) {
                $table->id();
                $table->string('key');
                $table->longText('value');
            });
    
        
        if (!Schema::hasTable('community')) {
            Schema::create('community', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id')->nullable();

                $table->string('name')->nullable();
                $table->string('logo')->nullable();
                $table->longText('description')->nullable();
                
                $table->longText('seo')->nullable();
                $table->integer('banned')->default(0);
                
                $table->string('address')->unique();
                $table->string('slug')->nullable();

                $table->longText('settings')->nullable();
                $table->timestamps();
            });
    
        
        if (!Schema::hasTable('community_space_group')) {
            Schema::create('community_space_group', function (Blueprint $table) {
                $table->id();
                $table->integer('community_id')->nullable();

                $table->string('name')->nullable();
                $table->longText('description')->nullable();
                
                $table->integer('status')->default(0);
                $table->string('slug')->nullable();

                $table->longText('settings')->nullable();
                $table->timestamps();
            });
    
        
        if (!Schema::hasTable('community_space')) {
            Schema::create('community_space', function (Blueprint $table) {
                $table->id();
                $table->integer('community_id')->nullable();
                $table->integer('space_group_id')->nullable();

                $table->string('name')->nullable();
                $table->string('type')->defalt('posts');
                $table->longText('description')->nullable();
                
                $table->integer('status')->default(0);
                $table->string('slug')->nullable();

                $table->longText('settings')->nullable();
                $table->timestamps();
            });
    

        // if (!Schema::hasTable('project_pixel')) {
        //     Schema::create('project_pixel', function (Blueprint $table) {
        //         $table->id();
        //         $table->integer('user_id');
        //         $table->string('name')->nullable();
        //         $table->string('domain')->nullable();
        //         $table->string('pixel')->nullable();
        //         $table->string('logo')->nullable();
        //         $table->longText('_cta')->nullable();
        //         $table->longText('_colors')->nullable();
        //         $table->longText('settings')->nullable();
        //         $table->integer('status')->default(0);
        //         $table->timestamps();
        //     });
        // }

        // if (!Schema::hasTable('project_pixel_data')) {
        //     Schema::create('project_pixel_data', function (Blueprint $table) {
        //         $table->id();
        //         $table->integer('project_id');
        //         $table->string('email')->nullable();
        //         $table->longText('feedback')->nullable();
        //         $table->string('reaction')->nullable();
        //         $table->longText('_tracking')->nullable();
        //         $table->longText('_tags')->nullable();
        //         $table->longText('settings')->nullable();
        //         $table->integer('status')->default(0);
        //         $table->timestamps();
        //     });
        // }

        // if (!Schema::hasTable('project_pixel_keywords')) {
        //     Schema::create('project_pixel_keywords', function (Blueprint $table) {
        //         $table->id();
        //         $table->integer('project_id');
        //         $table->integer('feedback_id')->nullable();
        //         $table->string('keyword')->nullable();
        //         $table->longText('settings')->nullable();
        //         $table->integer('status')->default(0);
        //         $table->timestamps();
        //     });
        // }
        
        if (!Schema::hasTable('plans')) {
            Schema::create('plans', function (Blueprint $table) {
                $table->increments('id');

                $table->string('name')->nullable();
                $table->text('description')->nullable();

                $table->float('price', 8, 2)->nullable();
                $table->float('annual_price', 8, 2)->nullable();
                $table->integer('is_free')->default(0)->nullable();

                $table->integer('has_trial')->default(0)->nullable();
                $table->string('trial_days')->nullable();

                $table->string('currency')->nullable();

                $table->integer('duration')->default(30);
                $table->mediumText('metadata')->nullable();

                $table->string('status')->default(1);
                $table->string('slug')->nullable();

                $table->integer('position')->default(0);
                $table->integer('defaults')->default(0);

                $table->timestamps();
            });
    

        if (!Schema::hasTable('plans_features')) {
            Schema::create('plans_features', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('plan_id')->nullable();
                $table->integer('enable')->default(0)->nullable();

                $table->string('name')->nullable();
                $table->string('code')->nullable();
                $table->text('description')->nullable();

                $table->enum('type', ['feature', 'limit'])->default('feature');
                $table->integer('limit')->default(0);
                $table->mediumText('metadata')->nullable();

                $table->timestamps();
            });
    

        if (!Schema::hasTable('plans_subscriptions')) {
            Schema::create('plans_subscriptions', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('plan_id')->nullable();

                $table->integer('model_id')->nullable();
                $table->string('model_type')->nullable();

                $table->enum('payment_method', ['stripe'])->nullable()->default(null);
                $table->boolean('is_paid')->default(false);

                $table->float('charging_price', 8, 2)->nullable();
                $table->string('charging_currency')->nullable();

                $table->boolean('is_recurring')->default(true);
                $table->integer('recurring_each_days')->default(30);

                $table->timestamp('starts_on')->nullable();
                $table->timestamp('expires_on')->nullable();
                $table->timestamp('cancelled_on')->nullable();

                $table->timestamps();
            });
    

        if (!Schema::hasTable('plans_history')) {
            Schema::create('plans_history', function (Blueprint $table) {
                $table->id();
                $table->integer('plan_id');
                $table->integer('user_id');
                $table->integer('trial')->default(0)->nullable();
                $table->timestamps();
            });
    

        if (!Schema::hasTable('plan_payments')) {
            Schema::create('plan_payments', function (Blueprint $table) {
                $table->id();
                $table->integer('user');
                $table->string('name')->nullable();
                $table->string('plan')->nullable();
                $table->string('plan_name')->nullable();
                $table->string('duration')->nullable();
                $table->string('email')->nullable();
                $table->string('ref')->nullable();
                $table->string('currency')->nullable();
                $table->float('price', 16, 2)->nullable();
                $table->string('gateway')->nullable();
                $table->timestamps();
            });
    

        if (!Schema::hasTable('plans_usages')) {
            Schema::create('plans_usages', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('subscription_id')->nullable();

                $table->string('code')->nullable();
                $table->float('used', 9, 2)->default(0);

                $table->timestamps();
            });
    
        
        if (!Schema::hasTable('site_domains')) {
            Schema::create('site_domains', function (Blueprint $table) {
                $table->id();
                $table->integer('site_id')->nullable();
                $table->integer('is_active')->default(0);
                $table->integer('is_connected')->default(0);
                $table->string('scheme')->nullable();
                $table->string('host')->nullable();
                $table->longText('settings')->nullable();
                $table->timestamps();
            });
    

        if (!Schema::hasTable('site_pixels')) {
            Schema::create('site_pixels', function (Blueprint $table) {
                $table->id();
                $table->uuid();
                $table->integer('site_id');
                $table->string('name')->nullable();
                $table->integer('status')->default(0);
                $table->string('pixel_id')->nullable();
                $table->string('pixel_type')->nullable();
                $table->timestamps();
            });
    

        if (!Schema::hasTable('sites_static_thumbnail')) {
            Schema::create('sites_static_thumbnail', function (Blueprint $table) {
                $table->id();
                $table->uuid();
                $table->integer('site_id');
                $table->longText('thumbnail')->nullable();
                $table->timestamps();
            });
    
        
        if (!Schema::hasTable('sites')) {
            Schema::create('sites', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id');
                $table->string('name')->nullable();
                $table->string('address')->unique();
                $table->longText('settings')->nullable();
                $table->string('logo')->nullable();
                $table->string('_slug')->nullable();
                $table->longText('qr')->nullable();
                $table->string('qr_bg')->nullable();
                $table->string('qr_logo')->nullable();
                $table->longText('seo')->nullable();
                $table->integer('banned')->default(0);
                $table->integer('status')->default(0);
                $table->softDeletes();
                $table->timestamps();
                
            });
    

        Schema::table('sites', function (Blueprint $table) {
            if (!Schema::hasColumn('sites', 'ai_generate')) {
                $table->integer('ai_generate')->after('settings')->default(0);
        
            if (!Schema::hasColumn('sites', 'ai_generate_prompt')) {
                $table->longText('ai_generate_prompt')->after('settings')->nullable();
        
            if (!Schema::hasColumn('sites', 'workspace_permission')) {
                $table->string('workspace_permission')->after('settings')->nullable();
        
            if (!Schema::hasColumn('sites', 'ai_completed')) {
                $table->integer('ai_completed')->after('settings')->default(0);
        
            if (!Schema::hasColumn('sites', 'published')) {
                $table->integer('published')->after('settings')->default(0);
        
            if (!Schema::hasColumn('sites', 'is_template')) {
                $table->integer('is_template')->after('settings')->default(0);
        
            if (!Schema::hasColumn('sites', 'is_admin')) {
                $table->integer('is_admin')->after('settings')->default(0);
        
            if (!Schema::hasColumn('sites', 'is_admin_selected')) {
                $table->integer('is_admin_selected')->after('settings')->default(0);
        
            if (!Schema::hasColumn('sites', 'created_by')) {
                $table->integer('created_by')->after('settings')->nullable();
        
            if (!Schema::hasColumn('sites', 'email')) {
                $table->string('email')->after('settings')->nullable();
        
            if (!Schema::hasColumn('sites', 'favicon')) {
                $table->string('favicon')->after('logo')->nullable();
        
            if (!Schema::hasColumn('sites', 'current_edit_page')) {
                $table->string('current_edit_page')->after('settings')->nullable();
        
            if (!Schema::hasColumn('sites', 'header')) {
                $table->longText('header')->after('settings')->nullable();
        
            if (!Schema::hasColumn('sites', 'footer')) {
                $table->longText('footer')->after('settings')->nullable();
        
        });

        if (!Schema::hasTable('site_access')) {
            Schema::create('site_access', function (Blueprint $table) {
                $table->id();
                $table->uuid();
                $table->integer('team_id')->nullable();
                $table->integer('user_id')->nullable();
                $table->integer('site_id')->nullable();
                $table->string('permission')->default('no_access');
                $table->timestamps();
            });
    
        

        if (!Schema::hasTable('yena_embed_store')) {
            Schema::create('yena_embed_store', function (Blueprint $table) {
                $table->id();
                $table->string('link')->nullable();
                $table->longText('data')->nullable();
                $table->timestamps();
            });
    

        if (!Schema::hasTable('yena_bio_templates')) {
            Schema::create('yena_bio_templates', function (Blueprint $table) {
                $table->id();
                $table->uuid();
                $table->integer('site_id')->nullable();
                $table->integer('created_by')->nullable();
                $table->string('name')->nullable();
                $table->float('price', 16, 2)->nullable();
                $table->longText('extra')->nullable();
                $table->timestamps();
            });
    

        if (!Schema::hasTable('yena_bio_template_access')) {
            Schema::create('yena_bio_template_access', function (Blueprint $table) {
                $table->id();
                $table->uuid();
                $table->integer('template_id')->nullable();
                $table->integer('user_id')->nullable();
                $table->integer('site_id')->nullable();
                $table->longText('extra')->nullable();
                $table->timestamps();
            });
    

        if (!Schema::hasTable('yena_templates')) {
            Schema::create('yena_templates', function (Blueprint $table) {
                $table->id();
                $table->uuid();
                $table->integer('site_id')->nullable();
                $table->integer('created_by')->nullable();
                $table->string('name')->nullable();
                $table->float('price', 16, 2)->nullable();
                $table->longText('extra')->nullable();
                $table->timestamps();
            });
    

        if (!Schema::hasTable('yena_template_access')) {
            Schema::create('yena_template_access', function (Blueprint $table) {
                $table->id();
                $table->uuid();
                $table->integer('template_id')->nullable();
                $table->integer('user_id')->nullable();
                $table->integer('site_id')->nullable();
                $table->longText('extra')->nullable();
                $table->timestamps();
            });
    

        if (!Schema::hasTable('site_ai_chat_session')) {
            Schema::create('site_ai_chat_session', function (Blueprint $table) {
                $table->id();
                $table->uuid();
                $table->integer('site_id')->nullable();
                $table->integer('started_by')->nullable();
                $table->string('session')->nullable();
                $table->longText('extra')->nullable();
                $table->timestamps();
            });
    

        if (!Schema::hasTable('site_ai_chat_history')) {
            Schema::create('site_ai_chat_history', function (Blueprint $table) {
                $table->id();
                $table->uuid();
                $table->integer('site_id')->nullable();
                $table->string('session_id')->nullable();
                $table->string('role')->nullable();
                $table->longText('human')->nullable();
                $table->longText('ai')->nullable();
                $table->longText('response')->nullable();
                $table->longText('extra')->nullable();
                $table->timestamps();
            });
    

        
        if (!Schema::hasTable('yena_favorites')) {
            Schema::create('yena_favorites', function (Blueprint $table) {
                $table->id();
                $table->uuid();
                $table->integer('owner_id')->nullable();
                $table->integer('user_id')->nullable();
                $table->integer('site_id')->nullable();
                $table->timestamps();
            });
    
        if (!Schema::hasTable('yena_teams')) {
            Schema::create('yena_teams', function (Blueprint $table) {
                $table->id();
                $table->uuid();
                $table->string('slug')->nullable();
                $table->integer('owner_id')->unsigned()->nullable();
                $table->string('name')->nullable();
                $table->timestamps();
            });
    
        
        Schema::table('yena_teams', function (Blueprint $table) {
            if (!Schema::hasColumn('yena_teams', 'logo')) {
                $table->string('logo')->after('owner_id')->nullable();
        
        });
        
        if (!Schema::hasTable('yena_teams_invite')) {
            Schema::create('yena_teams_invite', function (Blueprint $table) {
                $table->id();
                $table->uuid();
                $table->bigInteger('user_id')->unsigned();
                $table->integer('team_id')->unsigned();
                $table->string('email')->nullable();
                $table->string('accept_token')->nullable();
                $table->string('deny_token')->nullable();
                $table->longText('settings')->nullable();
                $table->timestamps();
            });
    

        if (!Schema::hasTable('yena_teams_user_table')) {
            Schema::create('yena_teams_user_table', function (Blueprint $table) {
                $table->id();
                $table->uuid();
                $table->bigInteger('user_id')->unsigned();
                $table->integer('team_id')->unsigned();
                $table->integer('can_update')->default(0);
                $table->integer('can_delete')->default(0);
                $table->integer('can_create')->default(0);
                $table->timestamps();

                
                $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            });
    

        Schema::table('yena_teams_user_table', function (Blueprint $table) {
            if (!Schema::hasColumn('yena_teams_user_table', 'is_accepted')) {
                $table->integer('is_accepted')->after('can_create')->default(0);
        

            if (!Schema::hasColumn('yena_teams_user_table', 'role')) {
                $table->string('role')->after('can_create')->default('member');
        

            if (!Schema::hasColumn('yena_teams_user_table', 'settings')) {
                $table->longText('settings')->after('can_create')->nullable();
        
        });
        
        if (!Schema::hasTable('audience_broadcast')) {
            Schema::create('audience_broadcast', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id')->nullable();
                $table->text('subject')->nullable();
                $table->string('name')->nullable();
                $table->string('email')->nullable();
                $table->longText('content')->nullable();
                $table->integer('folder_id')->nullable();
                $table->integer('schedule')->default(0);
                $table->timestamp('schedule_on')->nullable();
                $table->text('thumbnail')->nullable();
                $table->longText('settings')->nullable();
                $table->timestamps();
            });
    

        if (!Schema::hasTable('audience_broadcast_users')) {
            Schema::create('audience_broadcast_users', function (Blueprint $table) {
                $table->id();
                $table->integer('broadcast_id')->nullable();
                $table->integer('audience_id')->nullable();
                $table->longText('settings')->nullable();
                $table->timestamps();
            });
    

        if (!Schema::hasTable('audience_broadcast_sent')) {
            Schema::create('audience_broadcast_sent', function (Blueprint $table) {
                $table->id();
                $table->integer('broadcast_id')->nullable();
                $table->integer('broadcast_user_id')->nullable();
                $table->integer('status')->default(0);
                $table->longText('settings')->nullable();
                $table->timestamps();
            });
    
        
        if (!Schema::hasTable('audience_folders')) {
            Schema::create('audience_folders', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id')->nullable();
                $table->text('name')->nullable();
                $table->text('thumbnail')->nullable();
                $table->longText('settings')->nullable();
                $table->timestamps();
            });
    
        
        if (!Schema::hasTable('audience_folders_users')) {
            Schema::create('audience_folders_users', function (Blueprint $table) {
                $table->id();
                $table->integer('folder_id')->nullable();
                $table->integer('audience_id')->nullable();
                $table->longText('settings')->nullable();
                $table->timestamps();
            });
    

        if (!Schema::hasTable('audience')) {
            Schema::create('audience', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id')->nullable();
                $table->integer('owner_id');
                $table->integer('is_collab')->nullable();
                $table->string('name')->nullable();
                $table->longText('tags')->nullable();
                $table->longText('contact')->nullable();
                $table->longText('extra')->nullable();
                $table->timestamps();
            });
    

        Schema::table('audience', function (Blueprint $table) {
            if (!Schema::hasColumn('audience', 'avatar')) {
                $table->string('avatar')->after('name')->nullable();
        
        });

        if (!Schema::hasTable('audience_notes')) {
            Schema::create('audience_notes', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id');
                $table->integer('audience_id');
                $table->longText('note')->nullable();
                $table->longText('extra')->nullable();
                $table->timestamps();
            });
    

        if (!Schema::hasTable('audience_activity')) {
            Schema::create('audience_activity', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id');
                $table->integer('audience_id');
                $table->string('type')->nullable();
                $table->text('message')->nullable();
                $table->string('ip')->nullable();
                $table->string('os')->nullable();
                $table->string('browser')->nullable();
                $table->timestamps();
            });
    

        if (!Schema::hasTable('wallet_settlements')) {
            Schema::create('wallet_settlements', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id');
                $table->integer('settlement_id')->nullable();
                $table->longText('settlement')->nullable();
                $table->timestamps();
            });
    

        if (!Schema::hasTable('wallet_withdrawals')) {
            Schema::create('wallet_withdrawals', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id')->nullable();
                $table->float('amount', 16, 2)->nullable();
                $table->longText('note')->nullable();
                $table->integer('is_paid')->nullable();
                $table->longText('transaction')->nullable();
                $table->longText('extra')->nullable();
                $table->timestamps();
            });
    

        if (!Schema::hasTable('wallet_transactions')) {
            Schema::create('wallet_transactions', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id');
                $table->integer('spv_id')->nullable();
                $table->string('type')->nullable();
                $table->float('amount', 16, 2)->nullable();
                $table->string('currency')->nullable();
                $table->longText('transaction')->nullable();
                $table->longText('payload')->nullable();
                $table->timestamps();
            });
    
        
        Schema::table('wallet_transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('wallet_transactions', 'method')) {
                $table->string('method')->after('user_id')->nullable();
        
            if (!Schema::hasColumn('wallet_transactions', 'amount_settled')) {
                $table->float('amount_settled', 16, 2)->after('amount')->nullable();
        
        });
        if (!Schema::hasTable('products')) {
            Schema::create('products', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id');
                $table->string('name')->nullable();
                $table->string('slug')->nullable();
                $table->integer('status')->default(1);
                $table->integer('price_type')->default(1);
                $table->float('price', 16, 2)->nullable();
                $table->longText('price_pwyw')->nullable();
                $table->string('comparePrice')->nullable();
                $table->integer('enableOptions')->default(0);
                $table->integer('isDeal')->default(0);
                $table->string('dealPrice')->nullable();
                $table->dateTime('dealEnds')->nullable();
                $table->integer('enableBid')->default(0);
                $table->integer('stock')->nullable();
                $table->longText('stock_settings')->nullable();
                $table->integer('productType')->default(0);
                $table->longText('banner')->nullable();
                $table->longText('media')->nullable();
                $table->longText('description')->nullable();
                $table->longText('ribbon')->nullable();
                $table->longText('seo')->nullable();
                $table->longText('api')->nullable();
                $table->longText('files')->nullable();
                $table->longText('extra')->nullable();
                $table->integer('position')->default(0);
                $table->timestamps();
            });
    

        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                if (!Schema::hasColumn('products', 'featured_img')) {
                    $table->string('featured_img')->after('banner')->nullable();
            
                if (!Schema::hasColumn('products', 'sku')) {
                    $table->text('sku')->after('stock_settings')->nullable();
            
                if (!Schema::hasColumn('products', 'min_quantity')) {
                    $table->text('min_quantity')->after('description')->nullable();
            
                if (!Schema::hasColumn('products', 'external_product_link')) {
                    $table->text('external_product_link')->after('description')->nullable();
            
                if (!Schema::hasColumn('products', 'slug')) {
                    $table->string('slug')->after('name')->nullable();
            
            });
    

        if (!Schema::hasTable('product_options')) {
            Schema::create('product_options', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id');
                $table->integer('product_id');
                $table->string('name')->nullable();
                $table->float('price', 16, 2)->nullable();
                $table->integer('stock')->nullable();
                $table->longText('description')->nullable();
                $table->longText('files')->nullable();
                $table->integer('position')->default(0);
                $table->timestamps();
            });
    

        if (Schema::hasTable('product_options')) {
            Schema::table('product_options', function (Blueprint $table) {
                if (!Schema::hasColumn('product_options', 'type')) {
                    $table->string('type')->after('position')->nullable();
            
                if (!Schema::hasColumn('product_options', 'image')) {
                    $table->string('image')->after('position')->nullable();
            
                if (!Schema::hasColumn('product_options', 'extra')) {
                    $table->longText('extra')->after('position')->nullable();
            
                if (!Schema::hasColumn('product_options', 'variation_value')) {
                    $table->string('variation_value')->after('position')->nullable();
            
            });
    

        if (!Schema::hasTable('product_shipping')) {
            Schema::create('product_shipping', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id');
                $table->float('price', 16, 2)->nullable();
                $table->string('name')->nullable();
                $table->longText('description')->nullable();
                $table->string('country_iso')->nullable();
                $table->string('country')->nullable();
                $table->longText('locations')->nullable();
                $table->longText('extra')->nullable();
                $table->timestamps();
            });
    

        if (!Schema::hasTable('product_order_timeline')) {
            Schema::create('product_order_timeline', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id');
                $table->integer('tid');
                $table->string('type')->nullable();
                $table->longText('data')->nullable();
                $table->timestamps();
            });
    

        if (!Schema::hasTable('product_order_note')) {
            Schema::create('product_order_note', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id');
                $table->integer('tid');
                $table->longText('note')->nullable();
                $table->timestamps();
            });
    

        if (!Schema::hasTable('product_order')) {
            Schema::create('product_order', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id');
                $table->integer('payee_user_id')->nullable();
                $table->longText('details')->nullable();
                $table->string('currency')->nullable();
                $table->string('email')->nullable();
                $table->string('ref')->nullable();
                $table->float('price', 16, 2)->nullable();
                $table->integer('is_deal')->default(0);
                $table->integer('deal_discount')->nullable();
                $table->longText('products')->nullable();
                $table->longText('extra')->nullable();
                $table->integer('status')->default(0);
                $table->timestamps();
            });
    

        if (!Schema::hasTable('product_reviews')) {
            Schema::create('product_reviews', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id');
                $table->integer('reviewer_id')->nullable();
                $table->integer('product_id')->nullable();
                $table->string('rating')->nullable();
                $table->longText('review')->nullable();
                $table->timestamps();
            });
    
        if (!Schema::hasTable('product_coupon_codes')) {
            Schema::create('product_coupon_codes', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id')->nullable();
                $table->integer('product_id')->nullable();
                $table->string('code')->nullable();
                $table->string('type')->nullable();
                $table->dateTime('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->integer('discount')->default(0);
                $table->longText('settings')->nullable();
                $table->timestamps();
            });
    
        
        if (!Schema::hasTable('product_coupon_history')) {
            Schema::create('product_coupon_history', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id')->nullable();
                $table->integer('coupon_id')->nullable();
                $table->longText('settings')->nullable();
                $table->timestamps();
            });
    
        
        if (!Schema::hasTable('courses_exam')) {
            Schema::create('courses_exam', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id');
                $table->integer('course_id')->nullable();
                $table->string('name')->nullable();
                $table->longText('description')->nullable();
                $table->longText('settings')->nullable();
                $table->timestamps();
            });
    

        if (!Schema::hasTable('courses_exam_el')) {
            Schema::create('courses_exam_el', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id');
                $table->integer('exam_id')->nullable();
                $table->string('name')->nullable();
                $table->integer('is_correct')->default(0);
                $table->longText('settings')->nullable();
                $table->timestamps();
            });
    

        if (!Schema::hasTable('courses_exam_db')) {
            Schema::create('courses_exam_db', function (Blueprint $table) {
                $table->id();
                $table->integer('page_id');
                $table->integer('user_id');
                $table->integer('exam_id')->nullable();
                $table->integer('is_passed')->default(0);
                $table->longText('settings')->nullable();
                $table->timestamps();
            });
    

        
        if (!Schema::hasTable('courses_performance_exam')) {
            Schema::create('courses_performance_exam', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id');
                $table->string('name')->nullable();
                $table->string('level')->nullable();
                $table->longText('description')->nullable();
                $table->longText('settings')->nullable();
                $table->timestamps();
            });
    

        if (!Schema::hasTable('courses_performance_exam_questions')) {
            Schema::create('courses_performance_exam_questions', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id');
                $table->integer('exam_id')->nullable();
                $table->string('name')->nullable();
                $table->longText('settings')->nullable();
                $table->timestamps();
            });
    

        if (!Schema::hasTable('courses_performance_exam_answers')) {
            Schema::create('courses_performance_exam_answers', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id');
                $table->integer('exam_id')->nullable();
                $table->integer('question_id')->nullable();
                $table->string('name')->nullable();
                $table->integer('is_correct')->default(0);
                $table->longText('settings')->nullable();
                $table->timestamps();
            });
    

        if (!Schema::hasTable('courses_performance_taken_completed')) {
            Schema::create('courses_performance_taken_completed', function (Blueprint $table) {
                $table->id();
                $table->integer('page_id');
                $table->integer('user_id');
                $table->integer('exam_id')->nullable();
                $table->integer('course_id')->nullable();
                $table->integer('is_passed')->default(0);
                $table->longText('settings')->nullable();
                $table->timestamps();
            });
    

        if (!Schema::hasTable('courses_performance_take_db')) {
            Schema::create('courses_performance_take_db', function (Blueprint $table) {
                $table->id();
                $table->integer('page_id');
                $table->integer('user_id');
                $table->integer('course_id')->nullable();
                $table->integer('exam_id')->nullable();
                $table->integer('question_id')->nullable();
                $table->integer('selected_answer')->nullable();
                $table->string('selected_answer_name')->nullable();
                $table->integer('is_passed')->default(0);
                $table->longText('settings')->nullable();
                $table->timestamps();
            });
    

        if (!Schema::hasTable('courses_intro')) {
            Schema::create('courses_intro', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id');
                $table->integer('course_id')->nullable();
                $table->string('name')->nullable();
                $table->string('file')->nullable();
                $table->longText('settings')->nullable();
                $table->timestamps();
            });
    
        if (!Schema::hasTable('courses')) {
            Schema::create('courses', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id');
                $table->string('name')->nullable();
                $table->integer('status')->default(1);
                $table->integer('price_type')->default(1);
                $table->float('price', 16, 2)->nullable();
                $table->longText('price_pwyw')->nullable();
                $table->string('compare_price')->nullable();
                $table->string('course_level')->nullable();
                $table->longText('settings')->nullable();
                $table->longText('course_includes')->nullable();
                $table->string('course_duration')->nullable();
                $table->integer('course_expiry_type')->default(1);
                $table->string('course_expiry')->nullable();
                $table->longText('tags')->nullable();
                $table->longText('banner')->nullable();
                $table->longText('description')->nullable();
                $table->integer('position')->default(0);
                $table->timestamps();
            });
    

        if (Schema::hasTable('courses')) {
            Schema::table('courses', function (Blueprint $table) {
                if (!Schema::hasColumn('courses', 'slug')) {
                    $table->string('slug')->after('name')->nullable();
            
            });
    

        if (!Schema::hasTable('courses_lesson')) {
            Schema::create('courses_lesson', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id');
                $table->integer('course_id')->nullable();
                $table->string('name')->nullable();
                $table->longText('description')->nullable();
                $table->string('lesson_type')->default(0);
                $table->integer('status')->default(1);
                $table->string('lesson_duration')->nullable();
                $table->longText('lesson_data')->nullable();
                $table->longText('settings')->nullable();
                $table->integer('position')->default(0);
                $table->timestamps();
            });
    

        if (!Schema::hasTable('courses_reviews')) {
            Schema::create('courses_reviews', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id');
                $table->integer('reviewer_id')->nullable();
                $table->integer('course_id')->nullable();
                $table->string('rating')->nullable();
                $table->longText('review')->nullable();
                $table->timestamps();
            });
    

        if (!Schema::hasTable('courses_enrollments')) {
            Schema::create('courses_enrollments', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id');
                $table->integer('payee_user_id')->nullable();
                $table->integer('course_id')->nullable();
                $table->longText('lesson_taken')->nullable();
                $table->timestamps();
            });
    

        if (!Schema::hasTable('courses_order')) {
            Schema::create('courses_order', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id');
                $table->integer('payee_user_id')->nullable();
                $table->integer('course_id')->nullable();
                $table->longText('details')->nullable();
                $table->string('currency')->nullable();
                $table->string('email')->nullable();
                $table->string('ref')->nullable();
                $table->float('price', 16, 2)->nullable();
                $table->longText('extra')->nullable();
                $table->integer('status')->default(0);
                $table->timestamps();
            });
    

        if (!Schema::hasTable('site_post')) {
            Schema::create('site_post', function (Blueprint $table) {
                $table->id();
                $table->uuid();
                $table->integer('site_id')->nullable();
                $table->string('slug')->nullable();
                $table->text('name')->nullable();
                $table->integer('published')->default(0);
                $table->longText('seo')->nullable();
                $table->longText('content')->nullable();
                $table->longText('description')->nullable();
                $table->longText('settings')->nullable();
                $table->longText('section_settings')->nullable();
                $table->timestamps();
            });
    

        if (!Schema::hasTable('site_socials')) {
            Schema::create('site_socials', function (Blueprint $table) {
                $table->id();
                $table->uuid();
                $table->integer('site_id')->nullable();
                $table->string('social')->nullable();
                $table->string('link')->nullable();
                $table->integer('position')->default(0);
                $table->longText('settings')->nullable();
                $table->timestamps();
            });
    

        if (!Schema::hasTable('sites_uploads')) {
            Schema::create('sites_uploads', function (Blueprint $table) {
                $table->id();
                $table->integer('site_id')->nullable();
                $table->string('size')->default(0);
                $table->integer('trashed')->default(0);
                $table->string('name')->nullable();
                $table->text('path')->nullable();
                $table->softDeletes();
                $table->timestamps();
            });
    

        if (!Schema::hasTable('checkout_go')) {
            Schema::create('checkout_go', function (Blueprint $table) {
                $table->id();
                $table->string('uref')->nullable();
                $table->string('email')->nullable();
                $table->string('currency')->nullable();
                $table->string('payment_subscription_id')->nullable();
                $table->string('payment_type')->default('onetime');
                $table->string('frequency')->default('monthly');
                $table->float('price', 16, 2)->nullable();
                $table->integer('paid')->default(0);
                $table->string('method')->nullable();
                $table->string('callback')->nullable();
                $table->longText('call_function')->nullable();
                $table->longText('keys')->nullable();
                $table->longText('meta')->nullable();
                $table->timestamps();
            });
    

        Schema::table('sites_uploads', function (Blueprint $table) {
            if (!Schema::hasColumn('sites_uploads', 'is_ai')) {
                $table->integer('is_ai')->after('path')->default(0);
        
            if (!Schema::hasColumn('sites_uploads', 'saved_ai')) {
                $table->integer('saved_ai')->after('path')->default(0);
        
            if (!Schema::hasColumn('sites_uploads', 'temp_ai_url')) {
                $table->text('temp_ai_url')->after('path')->nullable();
        
            if (!Schema::hasColumn('sites_uploads', 'ai_uploaded')) {
                $table->integer('ai_uploaded')->after('path')->default(0);
        
        });
        
        if (!Schema::hasTable('pages')) {
            Schema::create('pages', function (Blueprint $table) {
                $table->id();
                $table->integer('site_id');
                $table->string('name')->nullable();
                $table->string('slug')->nullable();
                $table->integer('published')->default(0);
                $table->longText('settings')->nullable();
                $table->softDeletes();
                $table->timestamps();
            });
    

        Schema::table('pages', function (Blueprint $table) {
            if (!Schema::hasColumn('pages', 'default')) {
                $table->integer('default')->after('settings')->default(0);
        
            if (!Schema::hasColumn('pages', 'hide_header')) {
                $table->integer('hide_header')->after('settings')->default(0);
        
            if (!Schema::hasColumn('pages', 'header')) {
                $table->longText('header')->after('settings')->nullable();
        
            if (!Schema::hasColumn('pages', 'seo')) {
                $table->longText('seo')->after('settings')->nullable();
        
            if (!Schema::hasColumn('pages', 'footer')) {
                $table->longText('footer')->after('settings')->nullable();
        
            if (!Schema::hasColumn('pages', 'uuid')) {
                $table->uuid()->after('id');
        
        });
        
        if (!Schema::hasTable('sections')) {
            Schema::create('sections', function (Blueprint $table) {
                $table->id();
                $table->integer('site_id')->nullable();
                $table->string('page_id')->nullable();
                $table->string('section')->nullable();
                $table->longText('image')->nullable();
                $table->longText('background')->nullable();
                $table->longText('content')->nullable();
                $table->integer('published')->default(0);
                $table->longText('settings')->nullable();
                $table->timestamps();
            });
    

        Schema::table('sections', function (Blueprint $table) {
            if (!Schema::hasColumn('sections', 'form')) {
                $table->longText('form')->after('section')->nullable();
        
            if (!Schema::hasColumn('sections', 'position')) {
                $table->integer('position')->after('section')->default(0);
        
            if (!Schema::hasColumn('sections', 'section_settings')) {
                $table->longText('section_settings')->after('section')->nullable();
        
            if (!Schema::hasColumn('sections', 'uuid')) {
                $table->uuid()->after('id');
        
            if (!Schema::hasColumn('sections', 'generated_ai')) {
                $table->integer('generated_ai')->after('section')->default(0);
        
            if (!Schema::hasColumn('sections', 'calling_ai')) {
                $table->integer('calling_ai')->after('section')->default(0);
        
            if (!Schema::hasColumn('sections', 'generated_ai_image')) {
                $table->integer('generated_ai_image')->after('section')->default(0);
        
            if (!Schema::hasColumn('sections', 'use_generated_ai')) {
                $table->integer('use_generated_ai')->after('section')->default(0);
        
        });

        if (!Schema::hasTable('section_items')) {
            Schema::create('section_items', function (Blueprint $table) {
                $table->id();
                $table->string('section_id')->nullable();
                $table->longText('image')->nullable();
                $table->longText('content')->nullable();
                $table->longText('settings')->nullable();
                $table->timestamps();
            });
    

        Schema::table('section_items', function (Blueprint $table) {
            if (!Schema::hasColumn('section_items', 'position')) {
                $table->integer('position')->after('settings')->default(0);
        
            if (!Schema::hasColumn('section_items', 'generated_ai')) {
                $table->integer('generated_ai')->after('settings')->default(0);
        
            if (!Schema::hasColumn('section_items', 'generated_ai_image')) {
                $table->integer('generated_ai_image')->after('settings')->default(0);
        
            if (!Schema::hasColumn('section_items', 'uuid')) {
                $table->uuid()->after('id');
        
        });

        if (!Schema::hasTable('site_forms')) {
            Schema::create('site_forms', function (Blueprint $table) {
                $table->id();
                $table->uuid();
                $table->integer('site_id')->nullable();
                $table->string('email')->nullable();
                $table->longText('content')->nullable();
                $table->longText('settings')->nullable();
                $table->timestamps();
            });
    

        if (!Schema::hasTable('site_footer_groups')) {
            Schema::create('site_footer_groups', function (Blueprint $table) {
                $table->id();
                $table->uuid();
                $table->integer('site_id')->nullable();
                $table->string('title')->nullable();
                $table->longText('links')->nullable();
                $table->integer('position')->default(0);
                $table->longText('settings')->nullable();
                $table->timestamps();
            });
    
        
        if (!Schema::hasTable('site_header_links')) {
            Schema::create('site_header_links', function (Blueprint $table) {
                $table->id();
                $table->uuid();
                $table->integer('site_id')->nullable();
                $table->string('parent_id')->nullable();
                $table->string('title')->nullable();
                $table->string('link')->nullable();
                $table->integer('position')->default(0);
                $table->longText('settings')->nullable();
                $table->timestamps();
            });
    

        if (!Schema::hasTable('sites_visitors')) {
            Schema::create('sites_visitors', function (Blueprint $table) {
                $table->id();
                $table->integer('site_id')->nullable();
                $table->string('slug')->nullable();
                $table->string('session')->nullable();
                $table->string('ip')->nullable();
                $table->longText('tracking')->nullable();
                $table->integer('views')->default(0);
                $table->timestamps();
            });
    
        Schema::table('sites_visitors', function (Blueprint $table) {
            if (!Schema::hasColumn('sites_visitors', 'page_slug')) {
                $table->string('page_slug')->after('tracking')->nullable();
        
        });

        if (!Schema::hasTable('sites_linker_track')) {
            Schema::create('sites_linker_track', function (Blueprint $table) {
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
    

        if (!Schema::hasTable('sites_linker')) {
            Schema::create('sites_linker', function (Blueprint $table) {
                $table->id();
                $table->integer('site_id')->nullable();
                $table->text('url')->nullable();
                $table->string('slug')->nullable();
                $table->timestamps();
            });
    
        
        if (!Schema::hasTable('folders')) {
            Schema::create('folders', function (Blueprint $table) {
                $table->id();
                $table->integer('owner_id');
                $table->string('name')->nullable();
                $table->string('slug')->nullable();
                $table->integer('published')->default(0);
                $table->longText('settings')->nullable();
                $table->timestamps();
            });
    
        
        if (!Schema::hasTable('folder_members')) {
            Schema::create('folder_members', function (Blueprint $table) {
                $table->id();
                $table->integer('folder_id');
                $table->integer('user_id');
                $table->longText('settings')->nullable();
                $table->timestamps();
            });
    
        
        if (!Schema::hasTable('folder_sites')) {
            Schema::create('folder_sites', function (Blueprint $table) {
                $table->id();
                $table->integer('folder_id');
                $table->integer('site_id')->nullable();
                $table->longText('settings')->nullable();
                $table->timestamps();
            });
    

        if (!Schema::hasTable('mediakit_site_domains')) {
            Schema::create('mediakit_site_domains', function (Blueprint $table) {
                $table->id();
                $table->integer('site_id')->nullable();
                $table->integer('is_active')->default(0);
                $table->integer('is_connected')->default(0);
                $table->string('scheme')->nullable();
                $table->string('host')->nullable();
                $table->longText('settings')->nullable();
                $table->timestamps();
            });
    
        if (!Schema::hasTable('mediakit_sites')) {
            Schema::create('mediakit_sites', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id');
                $table->string('name')->nullable();
                $table->string('address')->unique();
                $table->longText('bio')->nullable();
                $table->longText('background')->nullable();
                $table->longText('settings')->nullable();
                $table->longText('colors')->nullable();
                $table->string('logo')->nullable();
                $table->string('_slug')->nullable();
                $table->longText('membership')->nullable();
                $table->string('seo_image')->nullable();
                $table->string('_domain')->nullable();
                $table->longText('contact')->nullable();
                $table->longText('seo')->nullable();
                $table->integer('is_template')->default(0);
                $table->longText('social')->nullable();
                $table->string('banner')->nullable();
                $table->longText('interest')->nullable();
                $table->longText('connect_u')->nullable();
                $table->integer('banned')->default(0);
                $table->integer('status')->default(0);
                $table->softDeletes();
                $table->timestamps();
                
            });
    

        Schema::table('mediakit_sites', function (Blueprint $table) {
            if (!Schema::hasColumn('mediakit_sites', 'created_by')) {
                $table->integer('created_by')->after('settings')->nullable();
        
            if (!Schema::hasColumn('mediakit_sites', 'current_edit_page')) {
                $table->string('current_edit_page')->after('settings')->nullable();
        
            if (!Schema::hasColumn('mediakit_sites', 'location')) {
                $table->string('location')->after('settings')->nullable();
        
        });
        
        if (!Schema::hasTable('mediakit_site_socials')) {
            Schema::create('mediakit_site_socials', function (Blueprint $table) {
                $table->id();
                $table->uuid();
                $table->integer('site_id')->nullable();
                $table->string('social')->nullable();
                $table->string('link')->nullable();
                $table->integer('position')->default(0);
                $table->longText('settings')->nullable();
                $table->timestamps();
            });
    

        if (!Schema::hasTable('mediakit_sites_uploads')) {
            Schema::create('mediakit_sites_uploads', function (Blueprint $table) {
                $table->id();
                $table->integer('site_id')->nullable();
                $table->string('size')->default(0);
                $table->integer('trashed')->default(0);
                $table->string('name')->nullable();
                $table->text('path')->nullable();
                $table->softDeletes();
                $table->timestamps();
            });
    

        Schema::table('mediakit_sites_uploads', function (Blueprint $table) {
            if (!Schema::hasColumn('mediakit_sites_uploads', 'is_ai')) {
                $table->integer('is_ai')->after('path')->default(0);
        
            if (!Schema::hasColumn('mediakit_sites_uploads', 'saved_ai')) {
                $table->integer('saved_ai')->after('path')->default(0);
        
            if (!Schema::hasColumn('mediakit_sites_uploads', 'temp_ai_url')) {
                $table->text('temp_ai_url')->after('path')->nullable();
        
            if (!Schema::hasColumn('mediakit_sites_uploads', 'ai_uploaded')) {
                $table->integer('ai_uploaded')->after('path')->default(0);
        
        });
        if (!Schema::hasTable('mediakit_sites_visitors')) {
            Schema::create('mediakit_sites_visitors', function (Blueprint $table) {
                $table->id();
                $table->integer('site_id')->nullable();
                $table->string('slug')->nullable();
                $table->string('session')->nullable();
                $table->string('ip')->nullable();
                $table->longText('tracking')->nullable();
                $table->integer('views')->default(0);
                $table->timestamps();
            });
    
        Schema::table('mediakit_sites_visitors', function (Blueprint $table) {
            if (!Schema::hasColumn('mediakit_sites_visitors', 'page_slug')) {
                $table->string('page_slug')->after('tracking')->nullable();
        
        });

        if (!Schema::hasTable('mediakit_sites_linker_track')) {
            Schema::create('mediakit_sites_linker_track', function (Blueprint $table) {
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
    

        if (!Schema::hasTable('mediakit_sites_linker')) {
            Schema::create('mediakit_sites_linker', function (Blueprint $table) {
                $table->id();
                $table->integer('site_id')->nullable();
                $table->text('url')->nullable();
                $table->string('slug')->nullable();
                $table->timestamps();
            });
    
        
        if (!Schema::hasTable('mediakit_sections')) {
            Schema::create('mediakit_sections', function (Blueprint $table) {
                $table->id();
                $table->integer('site_id')->nullable();
                $table->string('page_id')->nullable();
                $table->string('section')->nullable();
                $table->longText('image')->nullable();
                $table->longText('background')->nullable();
                $table->longText('content')->nullable();
                $table->integer('published')->default(0);
                $table->longText('settings')->nullable();
                $table->timestamps();
            });
    

        Schema::table('mediakit_sections', function (Blueprint $table) {
            if (!Schema::hasColumn('mediakit_sections', 'form')) {
                $table->longText('form')->after('section')->nullable();
        
            if (!Schema::hasColumn('mediakit_sections', 'position')) {
                $table->integer('position')->after('section')->default(0);
        
            if (!Schema::hasColumn('mediakit_sections', 'section_settings')) {
                $table->longText('section_settings')->after('section')->nullable();
        
            if (!Schema::hasColumn('mediakit_sections', 'uuid')) {
                $table->uuid()->after('id');
        
        });

        if (!Schema::hasTable('mediakit_section_items')) {
            Schema::create('mediakit_section_items', function (Blueprint $table) {
                $table->id();
                $table->string('section_id')->nullable();
                $table->longText('image')->nullable();
                $table->longText('content')->nullable();
                $table->longText('settings')->nullable();
                $table->timestamps();
            });
    

        Schema::table('mediakit_section_items', function (Blueprint $table) {
            if (!Schema::hasColumn('mediakit_section_items', 'position')) {
                $table->integer('position')->after('settings')->default(0);
        
            if (!Schema::hasColumn('mediakit_section_items', 'uuid')) {
                $table->uuid()->after('id');
        
        });

        
        if (!Schema::hasTable('link_shortener')) {
            Schema::create('link_shortener', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id')->nullable();
                $table->text('slug')->nullable();
                $table->text('link')->nullable();
                $table->longText('settings')->nullable();
                $table->timestamps();
            });
    

        if (!Schema::hasTable('link_shortener_visitors')) {
            Schema::create('link_shortener_visitors', function (Blueprint $table) {
                $table->id();
                $table->integer('link_id')->nullable();
                $table->string('slug')->nullable();
                $table->string('session')->nullable();
                $table->string('ip')->nullable();
                $table->longText('tracking')->nullable();
                $table->text('link')->nullable();
                $table->integer('views')->default(0);
                $table->timestamps();
            });
    
        
        if (!Schema::hasTable('invoices')) {
            Schema::create('invoices', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id')->nullable();
                $table->string('slug')->nullable();
                $table->float('price', 16, 2)->nullable();
                $table->integer('draft')->default(0);
                $table->integer('paid')->default(0);
                $table->string('due')->nullable();
                $table->longText('data')->nullable();
                $table->longText('payer')->nullable();
                $table->longText('settings')->nullable();
                $table->timestamps();
            });
    

        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'enable_reminder')) {
                $table->integer('enable_reminder')->after('paid')->default(1);
        
            if (!Schema::hasColumn('invoices', 'viewed')) {
                $table->integer('viewed')->after('paid')->default(0);
        
            if (!Schema::hasColumn('invoices', 'last_viewed')) {
                $table->dateTime('last_viewed')->after('paid')->nullable();
        
        });
        
        if (!Schema::hasTable('invoices_timeline')) {
            Schema::create('invoices_timeline', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id');
                $table->integer('invoice_id');
                $table->string('type')->nullable();
                $table->longText('data')->nullable();
                $table->timestamps();
            });
    



        ///

        
        if (!Schema::hasTable('bio_site_domains')) {
            Schema::create('bio_site_domains', function (Blueprint $table) {
                $table->id();
                $table->integer('site_id')->nullable();
                $table->integer('is_active')->default(0);
                $table->integer('is_connected')->default(0);
                $table->string('scheme')->nullable();
                $table->string('host')->nullable();
                $table->longText('settings')->nullable();
                $table->timestamps();
            });
    

        if (!Schema::hasTable('bio_site_pixels')) {
            Schema::create('bio_site_pixels', function (Blueprint $table) {
                $table->id();
                $table->uuid();
                $table->integer('site_id');
                $table->string('name')->nullable();
                $table->integer('status')->default(0);
                $table->string('pixel_id')->nullable();
                $table->string('pixel_type')->nullable();
                $table->timestamps();
            });
    
        
        if (!Schema::hasTable('bio_sites')) {
            Schema::create('bio_sites', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id');
                $table->string('name')->nullable();
                $table->string('address')->unique();
                $table->longText('bio')->nullable();
                $table->longText('background')->nullable();
                $table->longText('settings')->nullable();
                $table->longText('colors')->nullable();
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
                $table->longText('social')->nullable();
                $table->string('banner')->nullable();
                $table->longText('interest')->nullable();
                $table->longText('connect_u')->nullable();
                $table->integer('banned')->default(0);
                $table->integer('status')->default(0);
                $table->softDeletes();
                $table->timestamps();
                
            });
    

        Schema::table('bio_sites', function (Blueprint $table) {
            if (!Schema::hasColumn('bio_sites', 'created_by')) {
                $table->integer('created_by')->after('settings')->nullable();
        
            if (!Schema::hasColumn('bio_sites', 'current_edit_page')) {
                $table->string('current_edit_page')->after('settings')->nullable();
        
            if (!Schema::hasColumn('bio_sites', 'location')) {
                $table->string('location')->after('settings')->nullable();
        
        });
        
        if (!Schema::hasTable('bio_site_socials')) {
            Schema::create('bio_site_socials', function (Blueprint $table) {
                $table->id();
                $table->uuid();
                $table->integer('site_id')->nullable();
                $table->string('social')->nullable();
                $table->string('link')->nullable();
                $table->integer('position')->default(0);
                $table->longText('settings')->nullable();
                $table->timestamps();
            });
    

        if (!Schema::hasTable('bio_sites_uploads')) {
            Schema::create('bio_sites_uploads', function (Blueprint $table) {
                $table->id();
                $table->integer('site_id')->nullable();
                $table->string('size')->default(0);
                $table->integer('trashed')->default(0);
                $table->string('name')->nullable();
                $table->text('path')->nullable();
                $table->softDeletes();
                $table->timestamps();
            });
    

        Schema::table('bio_sites_uploads', function (Blueprint $table) {
            if (!Schema::hasColumn('bio_sites_uploads', 'is_ai')) {
                $table->integer('is_ai')->after('path')->default(0);
        
            if (!Schema::hasColumn('bio_sites_uploads', 'saved_ai')) {
                $table->integer('saved_ai')->after('path')->default(0);
        
            if (!Schema::hasColumn('bio_sites_uploads', 'temp_ai_url')) {
                $table->text('temp_ai_url')->after('path')->nullable();
        
            if (!Schema::hasColumn('bio_sites_uploads', 'ai_uploaded')) {
                $table->integer('ai_uploaded')->after('path')->default(0);
        
        });
        if (!Schema::hasTable('bio_sites_visitors')) {
            Schema::create('bio_sites_visitors', function (Blueprint $table) {
                $table->id();
                $table->integer('site_id')->nullable();
                $table->string('slug')->nullable();
                $table->string('session')->nullable();
                $table->string('ip')->nullable();
                $table->longText('tracking')->nullable();
                $table->integer('views')->default(0);
                $table->timestamps();
            });
    
        Schema::table('bio_sites_visitors', function (Blueprint $table) {
            if (!Schema::hasColumn('bio_sites_visitors', 'page_slug')) {
                $table->string('page_slug')->after('tracking')->nullable();
        
        });

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
    

        if (!Schema::hasTable('bio_sites_linker')) {
            Schema::create('bio_sites_linker', function (Blueprint $table) {
                $table->id();
                $table->integer('site_id')->nullable();
                $table->text('url')->nullable();
                $table->string('slug')->nullable();
                $table->timestamps();
            });
    
        
        if (!Schema::hasTable('bio_pages')) {
            Schema::create('bio_pages', function (Blueprint $table) {
                $table->id();
                $table->integer('site_id');
                $table->string('name')->nullable();
                $table->string('slug')->nullable();
                $table->integer('published')->default(0);
                $table->longText('settings')->nullable();
                $table->softDeletes();
                $table->timestamps();
            });
    

        Schema::table('bio_pages', function (Blueprint $table) {
            if (!Schema::hasColumn('bio_pages', 'default')) {
                $table->integer('default')->after('settings')->default(0);
        
            if (!Schema::hasColumn('bio_pages', 'uuid')) {
                $table->uuid()->after('id');
        
        });

        // Elements
        if (!Schema::hasTable('bio_addons')) {
            Schema::create('bio_addons', function (Blueprint $table) {
                $table->id();
                $table->uuid();
                $table->integer('site_id');
                $table->string('slug')->unique();
                $table->string('name')->nullable();
                $table->string('thumbnail')->nullable();
                $table->string('addon')->nullable();
                $table->longText('content')->nullable();
                $table->longText('settings')->nullable();
                $table->integer('position')->default(0);
                $table->timestamps();
            });
    

        if (!Schema::hasTable('bio_addons_db')) {
            Schema::create('bio_addons_db', function (Blueprint $table) {
                $table->id();
                $table->uuid();
                $table->integer('site_id');
                $table->string('addon')->nullable();
                $table->string('email')->nullable();
                $table->longText('database')->nullable();
                $table->timestamps();
            });
    

        if (!Schema::hasTable('bio_site_story')) {
            Schema::create('bio_site_story', function (Blueprint $table) {
                $table->id();
                $table->uuid();
                $table->integer('site_id');
                $table->string('thumbnail')->nullable();
                $table->string('name')->nullable();
                $table->string('link')->nullable();
                $table->longText('settings')->nullable();
                $table->integer('position')->default(0);
                $table->timestamps();
            });
    
        
        if (!Schema::hasTable('bio_sections')) {
            Schema::create('bio_sections', function (Blueprint $table) {
                $table->id();
                $table->integer('site_id')->nullable();
                $table->string('page_id')->nullable();
                $table->string('section')->nullable();
                $table->longText('image')->nullable();
                $table->longText('background')->nullable();
                $table->longText('content')->nullable();
                $table->integer('published')->default(0);
                $table->longText('settings')->nullable();
                $table->timestamps();
            });
    

        Schema::table('bio_sections', function (Blueprint $table) {
            if (!Schema::hasColumn('bio_sections', 'form')) {
                $table->longText('form')->after('section')->nullable();
        
            if (!Schema::hasColumn('bio_sections', 'position')) {
                $table->integer('position')->after('section')->default(0);
        
            if (!Schema::hasColumn('bio_sections', 'section_settings')) {
                $table->longText('section_settings')->after('section')->nullable();
        
            if (!Schema::hasColumn('bio_sections', 'uuid')) {
                $table->uuid()->after('id');
        
        });

        if (!Schema::hasTable('bio_section_items')) {
            Schema::create('bio_section_items', function (Blueprint $table) {
                $table->id();
                $table->string('section_id')->nullable();
                $table->longText('image')->nullable();
                $table->longText('content')->nullable();
                $table->longText('settings')->nullable();
                $table->timestamps();
            });
    

        Schema::table('bio_section_items', function (Blueprint $table) {
            if (!Schema::hasColumn('bio_section_items', 'position')) {
                $table->integer('position')->after('settings')->default(0);
        
            if (!Schema::hasColumn('bio_section_items', 'uuid')) {
                $table->uuid()->after('id');
        
        });

        if (!Schema::hasTable('user_donations')) {
            Schema::create('user_donations', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id');
                $table->integer('bio_id')->nullable();
                $table->integer('payee_user_id')->nullable();
                $table->integer('is_private')->default(0);
                $table->float('amount', 16, 2)->nullable();
                $table->string('currency')->nullable();
                $table->string('email')->nullable();
                $table->string('source')->nullable();
                $table->longText('info')->nullable();
                $table->integer('is_recurring')->default(0);
                $table->integer('recurring_id')->nullable();
                $table->timestamps();
            });
    

        if (!Schema::hasTable('user_donations_recurring')) {
            Schema::create('user_donations_recurring', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id');
                $table->integer('is_active')->default(0);
                $table->string('last_subscription_uref')->nullable();
                $table->timestamps();
            });
    
        if (!Schema::hasTable('booking_appointments')) {
            Schema::create('booking_appointments', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id');
                $table->integer('payee_user_id')->nullable();
                $table->longText('service_ids')->nullable();
                $table->date('date')->nullable();
                $table->string('time')->nullable();
                $table->longText('settings')->nullable();
                $table->longText('info')->nullable();
                $table->integer('appointment_status')->default(0);
                $table->float('price', 16, 2)->nullable();
                $table->integer('is_paid')->default(0);
                $table->timestamps();
            });
    
        
        if (!Schema::hasTable('booking_working_breaks')) {
            Schema::create('booking_working_breaks', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id');
                $table->date('date')->nullable();
                $table->string('time')->nullable();
                $table->longText('settings')->nullable();
                $table->timestamps();
            });
    

        if (!Schema::hasTable('booking_services')) {
            Schema::create('booking_services', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id');
                $table->string('name')->nullable();
                $table->longText('thumbnail')->nullable();
                $table->float('price', 16, 2)->default(0);
                $table->integer('duration')->nullable();
                $table->longText('settings')->nullable();
                $table->integer('status')->default(1);
                $table->integer('position')->default(0);
                $table->timestamps();
            });
    

        Schema::table('booking_services', function (Blueprint $table) {
            if (!Schema::hasColumn('booking_services', 'description')) {
                $table->longText('description')->after('settings')->nullable();
        
            if (!Schema::hasColumn('booking_services', 'gallery')) {
                $table->longText('gallery')->after('settings')->nullable();
        
            if (!Schema::hasColumn('booking_services', 'booking_time_interval')) {
                $table->string('booking_time_interval')->after('settings')->default('15');
        
            if (!Schema::hasColumn('booking_services', 'booking_workhours')) {
                $table->longText('booking_workhours')->after('settings')->nullable();
        
        });

        if (!Schema::hasTable('booking_orders')) {
            Schema::create('booking_orders', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id');
                $table->integer('payee_user_id')->nullable();
                $table->integer('appointment_id')->nullable();
                $table->string('method')->nullable();
                $table->longText('details')->nullable();
                $table->string('currency')->nullable();
                $table->string('email')->nullable();
                $table->string('ref')->nullable();
                $table->float('price', 16, 2)->nullable();
                $table->longText('extra')->nullable();
                $table->integer('status')->default(0);
                $table->timestamps();
            });
    

        if (!Schema::hasTable('booking_reviews')) {
            Schema::create('booking_reviews', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id');
                $table->integer('reviewer_id')->nullable();
                $table->string('rating')->nullable();
                $table->longText('review')->nullable();
                $table->timestamps();
            });
    

        if (!Schema::hasTable('qrcodes')) {
            Schema::create('qrcodes', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id')->nullable();
                $table->longText('text')->nullable();
                $table->string('logo')->nullable();
                $table->string('background')->nullable();
                $table->longText('extra')->nullable();
                $table->timestamps();
            });
    

        if (!Schema::hasTable('user_conversations')) {
            Schema::create('user_conversations', function (Blueprint $table) {
                $table->id();
                $table->integer('user_1');
                $table->integer('user_2');
                $table->integer('status')->default(1);
                $table->longText('extra')->nullable();
                $table->timestamps();
            });
    

        if (!Schema::hasTable('user_messages')) {
            Schema::create('user_messages', function (Blueprint $table) {
                $table->id();
                $table->integer('conversation_id');
                $table->integer('user_id');
                $table->integer('from_user_id')->nullable();
                $table->integer('to_user_id')->nullable();
                $table->longText('message')->nullable();
                $table->string('status')->default('new');
                $table->string('link')->nullable();
                $table->string('image')->nullable();
                $table->string('seen')->default(0);
                $table->longText('extra')->nullable();
                $table->timestamps();
            });
    

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', '_last_edited_bio')) {
                $table->integer('_last_edited_bio')->after('settings')->nullable();
        
            if (!Schema::hasColumn('users', '_last_project_id')) {
                $table->integer('_last_project_id')->after('settings')->nullable();
        
            if (!Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->after('settings')->nullable();
        
            if (!Schema::hasColumn('users', 'title')) {
                $table->text('title')->after('settings')->nullable();
        
            if (!Schema::hasColumn('users', 'payment_subscription_ids')) {
                $table->longText('payment_subscription_ids')->after('settings')->nullable();
        
            if (!Schema::hasColumn('users', 'last_subscription_uref')) {
                $table->string('last_subscription_uref')->after('settings')->nullable();
        
            if (!Schema::hasColumn('users', 'store')) {
                $table->longText('store')->after('settings')->nullable();
        
            if (!Schema::hasColumn('users', 'wallet_settings')) {
                $table->longText('wallet_settings')->after('settings')->nullable();
        
            if (!Schema::hasColumn('users', 'booking_hour_type')) {
                $table->string('booking_hour_type')->after('settings')->default('12');
        
            if (!Schema::hasColumn('users', 'booking_title')) {
                $table->string('booking_title')->after('settings')->nullable();
        
            if (!Schema::hasColumn('users', 'booking_description')) {
                $table->text('booking_description')->after('settings')->nullable();
        
            if (!Schema::hasColumn('users', 'booking_time_interval')) {
                $table->string('booking_time_interval')->after('settings')->default('15');
        
            if (!Schema::hasColumn('users', 'booking_workhours')) {
                $table->longText('booking_workhours')->after('settings')->nullable();
        
            if (!Schema::hasColumn('users', 'booking_gallery')) {
                $table->longText('booking_gallery')->after('settings')->nullable();
        
        });


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('draftables');

};
