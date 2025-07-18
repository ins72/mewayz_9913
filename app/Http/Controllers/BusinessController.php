<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Testimonial;
use App\Models\CaseStudy;
use App\Models\BlogPost;
use App\Models\PressRelease;
use App\Models\Job;
use App\Models\Partner;
use App\Models\Feature;
use App\Models\PricingPlan;
use App\Models\StatusUpdate;
use App\Models\ContactMessage;
use App\Models\SupportTicket;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class BusinessController extends Controller
{
    /**
     * About Us page
     */
    public function about()
    {
        $teamMembers = [
            [
                'name' => 'John Doe',
                'position' => 'CEO & Founder',
                'bio' => 'Visionary leader with 15+ years in digital transformation',
                'image' => '/images/team/john-doe.jpg',
                'social' => [
                    'linkedin' => 'https://linkedin.com/in/johndoe',
                    'twitter' => 'https://twitter.com/johndoe'
                ]
            ],
            [
                'name' => 'Jane Smith',
                'position' => 'CTO',
                'bio' => 'Technical innovator focused on scalable solutions',
                'image' => '/images/team/jane-smith.jpg',
                'social' => [
                    'linkedin' => 'https://linkedin.com/in/janesmith',
                    'github' => 'https://github.com/janesmith'
                ]
            ],
            [
                'name' => 'Mike Johnson',
                'position' => 'Head of Product',
                'bio' => 'Product strategist with passion for user experience',
                'image' => '/images/team/mike-johnson.jpg',
                'social' => [
                    'linkedin' => 'https://linkedin.com/in/mikejohnson'
                ]
            ]
        ];

        $stats = [
            'users' => '50,000+',
            'countries' => '120+',
            'courses' => '10,000+',
            'satisfaction' => '98%'
        ];

        $values = [
            [
                'title' => 'Innovation',
                'description' => 'We constantly push the boundaries of what\'s possible',
                'icon' => 'lightbulb'
            ],
            [
                'title' => 'Customer Success',
                'description' => 'Your success is our primary measure of achievement',
                'icon' => 'trophy'
            ],
            [
                'title' => 'Integrity',
                'description' => 'We operate with transparency and ethical standards',
                'icon' => 'shield'
            ],
            [
                'title' => 'Excellence',
                'description' => 'We strive for perfection in everything we do',
                'icon' => 'star'
            ]
        ];

        return view('business.about', compact('teamMembers', 'stats', 'values'));
    }

    /**
     * Features page
     */
    public function features()
    {
        $features = Feature::where('is_active', true)
            ->orderBy('order')
            ->get();

        $categories = [
            'social-media' => [
                'name' => 'Social Media Management',
                'description' => 'Comprehensive tools for managing your social presence',
                'icon' => 'share'
            ],
            'course-creation' => [
                'name' => 'Course Creation',
                'description' => 'Build and sell online courses with ease',
                'icon' => 'book'
            ],
            'ecommerce' => [
                'name' => 'E-Commerce',
                'description' => 'Complete online store functionality',
                'icon' => 'shopping-cart'
            ],
            'marketing' => [
                'name' => 'Marketing Tools',
                'description' => 'Advanced marketing automation and analytics',
                'icon' => 'megaphone'
            ],
            'analytics' => [
                'name' => 'Analytics & Reporting',
                'description' => 'Deep insights into your business performance',
                'icon' => 'chart-bar'
            ],
            'collaboration' => [
                'name' => 'Team Collaboration',
                'description' => 'Real-time collaboration tools for teams',
                'icon' => 'users'
            ]
        ];

        return view('business.features', compact('features', 'categories'));
    }

    /**
     * Pricing page
     */
    public function pricing()
    {
        $plans = PricingPlan::where('is_active', true)
            ->orderBy('price')
            ->get();

        $faqs = [
            [
                'question' => 'Can I change my plan at any time?',
                'answer' => 'Yes, you can upgrade or downgrade your plan at any time. Changes take effect immediately and billing is prorated.'
            ],
            [
                'question' => 'Is there a free trial?',
                'answer' => 'Yes, we offer a 14-day free trial with full access to all features. No credit card required.'
            ],
            [
                'question' => 'What payment methods do you accept?',
                'answer' => 'We accept all major credit cards, PayPal, and bank transfers for annual plans.'
            ],
            [
                'question' => 'Do you offer refunds?',
                'answer' => 'Yes, we offer a 30-day money-back guarantee for all new subscriptions.'
            ]
        ];

        return view('business.pricing', compact('plans', 'faqs'));
    }

    /**
     * Contact Us page
     */
    public function contact()
    {
        $contactMethods = [
            [
                'type' => 'email',
                'title' => 'Email Support',
                'value' => 'support@mewayz.com',
                'description' => 'Get help with any questions or issues',
                'icon' => 'mail'
            ],
            [
                'type' => 'phone',
                'title' => 'Phone Support',
                'value' => '+1 (555) 123-4567',
                'description' => 'Speak with our support team',
                'icon' => 'phone'
            ],
            [
                'type' => 'chat',
                'title' => 'Live Chat',
                'value' => 'Available 24/7',
                'description' => 'Get instant help from our team',
                'icon' => 'chat'
            ],
            [
                'type' => 'address',
                'title' => 'Office Address',
                'value' => '123 Business St, Suite 100, City, State 12345',
                'description' => 'Visit us in person',
                'icon' => 'location'
            ]
        ];

        return view('business.contact', compact('contactMethods'));
    }

    /**
     * Handle contact form submission
     */
    public function submitContact(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'inquiry_type' => 'required|in:sales,support,partnership,general'
        ]);

        try {
            $contactMessage = ContactMessage::create([
                'name' => $request->name,
                'email' => $request->email,
                'subject' => $request->subject,
                'message' => $request->message,
                'phone' => $request->phone,
                'company' => $request->company,
                'inquiry_type' => $request->inquiry_type,
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
                'status' => 'new'
            ]);

            // Send notification to admin
            Mail::to('admin@mewayz.com')->send(new \App\Mail\ContactFormSubmitted($contactMessage));

            // Send confirmation to user
            Mail::to($request->email)->send(new \App\Mail\ContactFormConfirmation($contactMessage));

            return response()->json([
                'success' => true,
                'message' => 'Thank you for your message. We will get back to you within 24 hours.',
                'ticket_id' => $contactMessage->id
            ]);

        } catch (\Exception $e) {
            Log::error('Contact form submission failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'There was an error sending your message. Please try again later.'
            ], 500);
        }
    }

    /**
     * Blog page
     */
    public function blog()
    {
        $posts = BlogPost::where('is_published', true)
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        $categories = BlogPost::where('is_published', true)
            ->distinct()
            ->pluck('category')
            ->filter()
            ->sort();

        $featuredPosts = BlogPost::where('is_published', true)
            ->where('is_featured', true)
            ->orderBy('published_at', 'desc')
            ->take(3)
            ->get();

        return view('business.blog.index', compact('posts', 'categories', 'featuredPosts'));
    }

    /**
     * Blog post details
     */
    public function blogPost($slug)
    {
        $post = BlogPost::where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        $relatedPosts = BlogPost::where('is_published', true)
            ->where('category', $post->category)
            ->where('id', '!=', $post->id)
            ->orderBy('published_at', 'desc')
            ->take(3)
            ->get();

        return view('business.blog.show', compact('post', 'relatedPosts'));
    }

    /**
     * Case Studies page
     */
    public function caseStudies()
    {
        $caseStudies = CaseStudy::where('is_published', true)
            ->orderBy('published_at', 'desc')
            ->get();

        $industries = CaseStudy::where('is_published', true)
            ->distinct()
            ->pluck('industry')
            ->filter()
            ->sort();

        return view('business.case-studies.index', compact('caseStudies', 'industries'));
    }

    /**
     * Case study details
     */
    public function caseStudy($slug)
    {
        $caseStudy = CaseStudy::where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        $relatedCaseStudies = CaseStudy::where('is_published', true)
            ->where('industry', $caseStudy->industry)
            ->where('id', '!=', $caseStudy->id)
            ->take(3)
            ->get();

        return view('business.case-studies.show', compact('caseStudy', 'relatedCaseStudies'));
    }

    /**
     * Testimonials page
     */
    public function testimonials()
    {
        $testimonials = Testimonial::where('is_published', true)
            ->orderBy('created_at', 'desc')
            ->get();

        $stats = [
            'total_reviews' => $testimonials->count(),
            'average_rating' => $testimonials->avg('rating'),
            'five_star_percentage' => $testimonials->where('rating', 5)->count() / max($testimonials->count(), 1) * 100
        ];

        return view('business.testimonials', compact('testimonials', 'stats'));
    }

    /**
     * Careers page
     */
    public function careers()
    {
        $jobs = Job::where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get();

        $departments = $jobs->pluck('department')->unique()->sort();

        $benefits = [
            'Health Insurance',
            'Remote Work Options',
            'Professional Development',
            'Flexible Hours',
            'Unlimited PTO',
            'Stock Options',
            'Gym Membership',
            'Free Lunch'
        ];

        return view('business.careers', compact('jobs', 'departments', 'benefits'));
    }

    /**
     * Partners page
     */
    public function partners()
    {
        $partners = Partner::where('is_active', true)
            ->orderBy('tier')
            ->get();

        $partnerTypes = [
            'technology' => 'Technology Partners',
            'integration' => 'Integration Partners',
            'consulting' => 'Consulting Partners',
            'reseller' => 'Reseller Partners'
        ];

        return view('business.partners', compact('partners', 'partnerTypes'));
    }

    /**
     * Press Kit page
     */
    public function pressKit()
    {
        $pressReleases = PressRelease::where('is_published', true)
            ->orderBy('published_at', 'desc')
            ->take(5)
            ->get();

        $assets = [
            'logos' => [
                'Logo (PNG)' => '/press-kit/logo.png',
                'Logo (SVG)' => '/press-kit/logo.svg',
                'Logo (EPS)' => '/press-kit/logo.eps'
            ],
            'screenshots' => [
                'Dashboard' => '/press-kit/dashboard.png',
                'Course Builder' => '/press-kit/course-builder.png',
                'Analytics' => '/press-kit/analytics.png'
            ],
            'brand_assets' => [
                'Brand Guidelines' => '/press-kit/brand-guidelines.pdf',
                'Color Palette' => '/press-kit/colors.pdf',
                'Typography' => '/press-kit/typography.pdf'
            ]
        ];

        return view('business.press-kit', compact('pressReleases', 'assets'));
    }

    /**
     * Security page
     */
    public function security()
    {
        $certifications = [
            'SOC 2 Type II',
            'ISO 27001',
            'GDPR Compliant',
            'CCPA Compliant',
            'HIPAA Ready'
        ];

        $securityFeatures = [
            'End-to-end encryption',
            'Multi-factor authentication',
            'Regular security audits',
            'Automated backups',
            'Incident response plan',
            'Employee security training'
        ];

        return view('business.security', compact('certifications', 'securityFeatures'));
    }

    /**
     * Status page
     */
    public function status()
    {
        $services = [
            'Web Application' => 'operational',
            'API' => 'operational',
            'Database' => 'operational',
            'File Storage' => 'operational',
            'Email Service' => 'operational',
            'CDN' => 'operational'
        ];

        $incidents = StatusUpdate::where('type', 'incident')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $maintenances = StatusUpdate::where('type', 'maintenance')
            ->where('scheduled_at', '>', now())
            ->orderBy('scheduled_at')
            ->take(5)
            ->get();

        return view('business.status', compact('services', 'incidents', 'maintenances'));
    }

    /**
     * Help Center
     */
    public function helpCenter()
    {
        $categories = [
            'getting-started' => [
                'name' => 'Getting Started',
                'icon' => 'play',
                'articles' => 12
            ],
            'account-management' => [
                'name' => 'Account Management',
                'icon' => 'user',
                'articles' => 8
            ],
            'course-creation' => [
                'name' => 'Course Creation',
                'icon' => 'book',
                'articles' => 15
            ],
            'billing' => [
                'name' => 'Billing & Payments',
                'icon' => 'credit-card',
                'articles' => 6
            ],
            'integrations' => [
                'name' => 'Integrations',
                'icon' => 'link',
                'articles' => 10
            ],
            'troubleshooting' => [
                'name' => 'Troubleshooting',
                'icon' => 'wrench',
                'articles' => 20
            ]
        ];

        return view('business.help-center', compact('categories'));
    }

    /**
     * Sitemap page
     */
    public function sitemap()
    {
        $sections = [
            'Main Pages' => [
                'Home' => route('home'),
                'About' => route('business.about'),
                'Features' => route('business.features'),
                'Pricing' => route('business.pricing'),
                'Contact' => route('business.contact')
            ],
            'Resources' => [
                'Blog' => route('business.blog'),
                'Case Studies' => route('business.case-studies'),
                'Help Center' => route('business.help-center'),
                'API Documentation' => route('api.documentation')
            ],
            'Company' => [
                'Careers' => route('business.careers'),
                'Partners' => route('business.partners'),
                'Press Kit' => route('business.press-kit'),
                'Security' => route('business.security')
            ],
            'Legal' => [
                'Terms of Service' => route('legal.terms-of-service'),
                'Privacy Policy' => route('legal.privacy-policy'),
                'Cookie Policy' => route('legal.cookie-policy'),
                'Refund Policy' => route('legal.refund-policy')
            ]
        ];

        return view('business.sitemap', compact('sections'));
    }
}