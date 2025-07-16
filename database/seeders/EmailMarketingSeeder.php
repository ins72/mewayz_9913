<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmailTemplate;
use App\Models\EmailList;
use App\Models\EmailSubscriber;
use App\Models\EmailCampaign;
use App\Models\User;
use App\Models\Workspace;

class EmailMarketingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get first workspace and user
        $workspace = Workspace::first();
        $user = User::first();
        
        if (!$workspace || !$user) {
            $this->command->info('Please ensure you have at least one workspace and user created first.');
            return;
        }

        // Create email templates
        $templates = [
            [
                'name' => 'Welcome Email',
                'description' => 'Welcome new subscribers to your community',
                'category' => 'transactional',
                'subject' => 'Welcome to {{company_name}}!',
                'html_content' => $this->getWelcomeEmailTemplate(),
                'is_default' => true,
            ],
            [
                'name' => 'Newsletter Template',
                'description' => 'Monthly newsletter template',
                'category' => 'newsletter',
                'subject' => '{{company_name}} Newsletter - {{month}} {{year}}',
                'html_content' => $this->getNewsletterTemplate(),
                'is_default' => true,
            ],
            [
                'name' => 'Promotional Email',
                'description' => 'Promotional email for sales and offers',
                'category' => 'promotional',
                'subject' => 'Special Offer: {{discount_percent}}% Off!',
                'html_content' => $this->getPromotionalTemplate(),
                'is_default' => true,
            ],
            [
                'name' => 'Product Launch',
                'description' => 'Announce new product launches',
                'category' => 'promotional',
                'subject' => 'Introducing {{product_name}} - Now Available!',
                'html_content' => $this->getProductLaunchTemplate(),
                'is_default' => true,
            ],
            [
                'name' => 'Custom Template',
                'description' => 'Custom email template for your business',
                'category' => 'custom',
                'subject' => 'Custom Email from {{company_name}}',
                'html_content' => $this->getCustomTemplate(),
                'is_default' => false,
            ],
        ];

        foreach ($templates as $template) {
            EmailTemplate::create([
                'workspace_id' => $workspace->id,
                'user_id' => $user->id,
                'name' => $template['name'],
                'description' => $template['description'],
                'category' => $template['category'],
                'subject' => $template['subject'],
                'html_content' => $template['html_content'],
                'text_content' => strip_tags($template['html_content']),
                'is_default' => $template['is_default'],
                'is_active' => true,
            ]);
        }

        // Create email lists
        $lists = [
            [
                'name' => 'Newsletter Subscribers',
                'description' => 'Monthly newsletter subscribers',
                'tags' => ['newsletter', 'monthly'],
            ],
            [
                'name' => 'VIP Customers',
                'description' => 'High-value customers and VIP members',
                'tags' => ['vip', 'customers'],
            ],
            [
                'name' => 'Product Updates',
                'description' => 'Subscribers interested in product updates',
                'tags' => ['product', 'updates'],
            ],
            [
                'name' => 'Marketing Promotions',
                'description' => 'Subscribers interested in promotions and offers',
                'tags' => ['promotions', 'offers'],
            ],
        ];

        foreach ($lists as $list) {
            EmailList::create([
                'workspace_id' => $workspace->id,
                'user_id' => $user->id,
                'name' => $list['name'],
                'description' => $list['description'],
                'tags' => $list['tags'],
                'is_active' => true,
            ]);
        }

        // Create sample subscribers
        $subscribers = [
            [
                'email' => 'john.doe@example.com',
                'first_name' => 'John',
                'last_name' => 'Doe',
                'location' => 'New York, USA',
                'tags' => ['newsletter', 'customer'],
                'source' => 'website_form',
            ],
            [
                'email' => 'jane.smith@example.com',
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'location' => 'London, UK',
                'tags' => ['vip', 'newsletter'],
                'source' => 'manual_import',
            ],
            [
                'email' => 'mike.johnson@example.com',
                'first_name' => 'Mike',
                'last_name' => 'Johnson',
                'location' => 'Toronto, Canada',
                'tags' => ['product_updates'],
                'source' => 'api',
            ],
            [
                'email' => 'sarah.wilson@example.com',
                'first_name' => 'Sarah',
                'last_name' => 'Wilson',
                'location' => 'Sydney, Australia',
                'tags' => ['promotions', 'newsletter'],
                'source' => 'social_media',
            ],
            [
                'email' => 'david.brown@example.com',
                'first_name' => 'David',
                'last_name' => 'Brown',
                'location' => 'Berlin, Germany',
                'tags' => ['vip', 'customer'],
                'source' => 'referral',
            ],
        ];

        foreach ($subscribers as $subscriber) {
            EmailSubscriber::create([
                'workspace_id' => $workspace->id,
                'email' => $subscriber['email'],
                'first_name' => $subscriber['first_name'],
                'last_name' => $subscriber['last_name'],
                'location' => $subscriber['location'],
                'tags' => $subscriber['tags'],
                'source' => $subscriber['source'],
                'status' => 'subscribed',
                'subscribed_at' => now(),
            ]);
        }

        // Add subscribers to lists
        $emailLists = EmailList::where('workspace_id', $workspace->id)->get();
        $emailSubscribers = EmailSubscriber::where('workspace_id', $workspace->id)->get();

        foreach ($emailLists as $list) {
            foreach ($emailSubscribers as $subscriber) {
                // Add subscribers to relevant lists based on tags
                $listTags = $list->tags ?? [];
                $subscriberTags = $subscriber->tags ?? [];
                
                if (array_intersect($listTags, $subscriberTags)) {
                    $list->addSubscriber($subscriber);
                }
            }
        }

        // Create sample campaigns
        $campaigns = [
            [
                'name' => 'Welcome Campaign',
                'subject' => 'Welcome to Mewayz!',
                'content' => 'Welcome to our platform! We\'re excited to have you join our community.',
                'status' => 'sent',
                'sent_at' => now()->subDays(7),
            ],
            [
                'name' => 'Monthly Newsletter - June 2025',
                'subject' => 'Mewayz Newsletter - June 2025',
                'content' => 'Check out our latest updates and features in this month\'s newsletter.',
                'status' => 'sent',
                'sent_at' => now()->subDays(3),
            ],
            [
                'name' => 'Summer Sale Campaign',
                'subject' => 'Summer Sale - 50% Off All Plans!',
                'content' => 'Don\'t miss our biggest sale of the year! Get 50% off all subscription plans.',
                'status' => 'draft',
            ],
        ];

        foreach ($campaigns as $campaign) {
            EmailCampaign::create([
                'workspace_id' => $workspace->id,
                'user_id' => $user->id,
                'name' => $campaign['name'],
                'subject' => $campaign['subject'],
                'content' => $campaign['content'],
                'recipient_lists' => [$emailLists->first()->id],
                'status' => $campaign['status'],
                'sent_at' => $campaign['sent_at'] ?? null,
                'total_recipients' => $emailSubscribers->count(),
                'delivered_count' => $campaign['status'] === 'sent' ? $emailSubscribers->count() : 0,
                'opened_count' => $campaign['status'] === 'sent' ? rand(20, 40) : 0,
                'clicked_count' => $campaign['status'] === 'sent' ? rand(5, 15) : 0,
                'unsubscribed_count' => $campaign['status'] === 'sent' ? rand(0, 2) : 0,
                'bounced_count' => $campaign['status'] === 'sent' ? rand(0, 3) : 0,
                'open_rate' => $campaign['status'] === 'sent' ? rand(45, 75) : 0,
                'click_rate' => $campaign['status'] === 'sent' ? rand(10, 25) : 0,
            ]);
        }

        $this->command->info('Email marketing sample data created successfully!');
    }

    private function getWelcomeEmailTemplate(): string
    {
        return '
        <html>
        <body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
            <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
                <h1 style="color: #2563eb;">Welcome to {{company_name}}!</h1>
                <p>Hi {{first_name}},</p>
                <p>Welcome to our platform! We\'re thrilled to have you join our community.</p>
                <p>Here\'s what you can do next:</p>
                <ul>
                    <li>Complete your profile</li>
                    <li>Explore our features</li>
                    <li>Connect with other users</li>
                </ul>
                <p>If you have any questions, don\'t hesitate to reach out to our support team.</p>
                <p>Best regards,<br>The {{company_name}} Team</p>
            </div>
        </body>
        </html>';
    }

    private function getNewsletterTemplate(): string
    {
        return '
        <html>
        <body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
            <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
                <h1 style="color: #2563eb;">{{company_name}} Newsletter</h1>
                <h2 style="color: #1f2937;">{{month}} {{year}}</h2>
                <p>Hi {{first_name}},</p>
                <p>Here are the latest updates from {{company_name}}:</p>
                
                <div style="background: #f3f4f6; padding: 20px; margin: 20px 0; border-radius: 8px;">
                    <h3 style="color: #1f2937;">Feature Updates</h3>
                    <p>{{feature_updates}}</p>
                </div>
                
                <div style="background: #f3f4f6; padding: 20px; margin: 20px 0; border-radius: 8px;">
                    <h3 style="color: #1f2937;">News & Announcements</h3>
                    <p>{{news_content}}</p>
                </div>
                
                <p>Thank you for being part of our community!</p>
                <p>Best regards,<br>The {{company_name}} Team</p>
            </div>
        </body>
        </html>';
    }

    private function getPromotionalTemplate(): string
    {
        return '
        <html>
        <body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
            <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
                <h1 style="color: #dc2626;">Special Offer: {{discount_percent}}% Off!</h1>
                <p>Hi {{first_name}},</p>
                <p>We have an exclusive offer just for you!</p>
                
                <div style="background: #fef2f2; border: 2px solid #dc2626; padding: 20px; margin: 20px 0; border-radius: 8px; text-align: center;">
                    <h2 style="color: #dc2626; margin: 0;">{{discount_percent}}% OFF</h2>
                    <p style="font-size: 18px; margin: 10px 0;">Use code: <strong>{{promo_code}}</strong></p>
                    <p style="color: #6b7280;">Valid until {{expiry_date}}</p>
                </div>
                
                <p>Don\'t miss out on this limited-time offer!</p>
                
                <div style="text-align: center; margin: 30px 0;">
                    <a href="{{shop_url}}" style="background: #dc2626; color: white; padding: 12px 30px; text-decoration: none; border-radius: 6px; font-weight: bold;">Shop Now</a>
                </div>
                
                <p>Happy shopping!</p>
                <p>The {{company_name}} Team</p>
            </div>
        </body>
        </html>';
    }

    private function getProductLaunchTemplate(): string
    {
        return '
        <html>
        <body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
            <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
                <h1 style="color: #059669;">Introducing {{product_name}}!</h1>
                <p>Hi {{first_name}},</p>
                <p>We\'re excited to announce the launch of our latest product: <strong>{{product_name}}</strong>!</p>
                
                <div style="background: #f0fdf4; padding: 20px; margin: 20px 0; border-radius: 8px;">
                    <h3 style="color: #059669;">What\'s New?</h3>
                    <p>{{product_description}}</p>
                    
                    <h4 style="color: #065f46;">Key Features:</h4>
                    <ul>
                        <li>{{feature_1}}</li>
                        <li>{{feature_2}}</li>
                        <li>{{feature_3}}</li>
                    </ul>
                </div>
                
                <div style="text-align: center; margin: 30px 0;">
                    <a href="{{product_url}}" style="background: #059669; color: white; padding: 12px 30px; text-decoration: none; border-radius: 6px; font-weight: bold;">Learn More</a>
                </div>
                
                <p>We can\'t wait for you to try it!</p>
                <p>Best regards,<br>The {{company_name}} Team</p>
            </div>
        </body>
        </html>';
    }

    private function getCustomTemplate(): string
    {
        return '
        <html>
        <body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
            <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
                <h1 style="color: #6366f1;">{{email_title}}</h1>
                <p>Hi {{first_name}},</p>
                <p>{{email_content}}</p>
                
                <div style="background: #f8fafc; padding: 20px; margin: 20px 0; border-radius: 8px;">
                    <p>{{additional_content}}</p>
                </div>
                
                <p>{{closing_message}}</p>
                <p>Best regards,<br>The {{company_name}} Team</p>
            </div>
        </body>
        </html>';
    }
}
