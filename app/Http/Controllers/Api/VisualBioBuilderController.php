<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BioSite;
use App\Models\BioSiteComponent;
use App\Models\BioSiteTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VisualBioBuilderController extends Controller
{
    /**
     * Get available components for drag-and-drop builder
     */
    public function getComponents(Request $request)
    {
        try {
            $components = [
                'text' => [
                    'id' => 'text',
                    'name' => 'Text Block',
                    'icon' => 'text-icon',
                    'description' => 'Add text content, headings, and descriptions',
                    'category' => 'content',
                    'default_props' => [
                        'content' => 'Your text here',
                        'font_size' => 16,
                        'font_weight' => 'normal',
                        'color' => '#000000',
                        'align' => 'left',
                        'margin' => '10px 0'
                    ]
                ],
                'link' => [
                    'id' => 'link',
                    'name' => 'Link Button',
                    'icon' => 'link-icon',
                    'description' => 'Add clickable link buttons',
                    'category' => 'navigation',
                    'default_props' => [
                        'title' => 'Link Title',
                        'url' => 'https://example.com',
                        'background_color' => '#007bff',
                        'text_color' => '#ffffff',
                        'border_radius' => '8px',
                        'padding' => '12px 24px',
                        'margin' => '8px 0',
                        'target' => '_blank'
                    ]
                ],
                'image' => [
                    'id' => 'image',
                    'name' => 'Image',
                    'icon' => 'image-icon',
                    'description' => 'Add images, logos, or photos',
                    'category' => 'media',
                    'default_props' => [
                        'src' => 'https://via.placeholder.com/300x200',
                        'alt' => 'Image description',
                        'width' => '100%',
                        'height' => 'auto',
                        'border_radius' => '8px',
                        'margin' => '10px 0'
                    ]
                ],
                'video' => [
                    'id' => 'video',
                    'name' => 'Video',
                    'icon' => 'video-icon',
                    'description' => 'Embed videos from YouTube, Vimeo, or upload',
                    'category' => 'media',
                    'default_props' => [
                        'src' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                        'type' => 'youtube',
                        'width' => '100%',
                        'height' => '200px',
                        'autoplay' => false,
                        'controls' => true,
                        'margin' => '10px 0'
                    ]
                ],
                'social_links' => [
                    'id' => 'social_links',
                    'name' => 'Social Links',
                    'icon' => 'social-icon',
                    'description' => 'Add social media links with icons',
                    'category' => 'social',
                    'default_props' => [
                        'links' => [
                            ['platform' => 'instagram', 'url' => 'https://instagram.com/username', 'icon' => 'instagram'],
                            ['platform' => 'twitter', 'url' => 'https://twitter.com/username', 'icon' => 'twitter'],
                            ['platform' => 'facebook', 'url' => 'https://facebook.com/username', 'icon' => 'facebook']
                        ],
                        'size' => 'medium',
                        'style' => 'rounded',
                        'align' => 'center',
                        'margin' => '15px 0'
                    ]
                ],
                'contact_form' => [
                    'id' => 'contact_form',
                    'name' => 'Contact Form',
                    'icon' => 'form-icon',
                    'description' => 'Add contact form for lead generation',
                    'category' => 'forms',
                    'default_props' => [
                        'title' => 'Contact Me',
                        'fields' => [
                            ['type' => 'text', 'name' => 'name', 'placeholder' => 'Your Name', 'required' => true],
                            ['type' => 'email', 'name' => 'email', 'placeholder' => 'Your Email', 'required' => true],
                            ['type' => 'textarea', 'name' => 'message', 'placeholder' => 'Your Message', 'required' => true]
                        ],
                        'button_text' => 'Send Message',
                        'button_color' => '#28a745',
                        'margin' => '20px 0'
                    ]
                ],
                'spacer' => [
                    'id' => 'spacer',
                    'name' => 'Spacer',
                    'icon' => 'spacer-icon',
                    'description' => 'Add empty space between components',
                    'category' => 'layout',
                    'default_props' => [
                        'height' => '20px',
                        'background_color' => 'transparent'
                    ]
                ],
                'divider' => [
                    'id' => 'divider',
                    'name' => 'Divider',
                    'icon' => 'divider-icon',
                    'description' => 'Add horizontal divider lines',
                    'category' => 'layout',
                    'default_props' => [
                        'style' => 'solid',
                        'color' => '#e0e0e0',
                        'thickness' => '1px',
                        'margin' => '15px 0'
                    ]
                ],
                'countdown' => [
                    'id' => 'countdown',
                    'name' => 'Countdown Timer',
                    'icon' => 'countdown-icon',
                    'description' => 'Add countdown timer for events or launches',
                    'category' => 'interactive',
                    'default_props' => [
                        'target_date' => '2025-12-31T23:59:59',
                        'title' => 'Event Countdown',
                        'background_color' => '#f8f9fa',
                        'text_color' => '#333333',
                        'border_radius' => '8px',
                        'padding' => '20px',
                        'margin' => '15px 0'
                    ]
                ],
                'testimonial' => [
                    'id' => 'testimonial',
                    'name' => 'Testimonial',
                    'icon' => 'testimonial-icon',
                    'description' => 'Add customer testimonials and reviews',
                    'category' => 'content',
                    'default_props' => [
                        'content' => 'Amazing service! Highly recommend.',
                        'author' => 'John Doe',
                        'role' => 'Customer',
                        'avatar' => 'https://via.placeholder.com/50',
                        'rating' => 5,
                        'background_color' => '#f8f9fa',
                        'border_radius' => '8px',
                        'margin' => '15px 0'
                    ]
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => array_values($components)
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get components', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to get components'
            ], 500);
        }
    }

    /**
     * Get bio site builder data
     */
    public function getBioSiteBuilder(Request $request, $id)
    {
        try {
            $bioSite = BioSite::with('components')->findOrFail($id);
            
            // Check if user owns this bio site
            $workspace = $request->user()->workspaces()->first();
            if (!$workspace || $bioSite->workspace_id !== $workspace->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            // Get components sorted by position
            $components = $bioSite->components()->orderBy('position')->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'bio_site' => $bioSite,
                    'components' => $components,
                    'settings' => [
                        'theme' => $bioSite->theme ?? 'default',
                        'background_color' => $bioSite->background_color ?? '#ffffff',
                        'text_color' => $bioSite->text_color ?? '#000000',
                        'font_family' => $bioSite->font_family ?? 'Inter',
                        'custom_css' => $bioSite->custom_css ?? '',
                        'favicon' => $bioSite->favicon ?? null,
                        'meta_title' => $bioSite->meta_title ?? $bioSite->name,
                        'meta_description' => $bioSite->meta_description ?? '',
                        'analytics_code' => $bioSite->analytics_code ?? ''
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get bio site builder data', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to load bio site'
            ], 500);
        }
    }

    /**
     * Save bio site with components
     */
    public function saveBioSite(Request $request, $id)
    {
        try {
            $request->validate([
                'components' => 'required|array',
                'settings' => 'required|array',
                'publish' => 'boolean'
            ]);

            $bioSite = BioSite::findOrFail($id);
            
            // Check if user owns this bio site
            $workspace = $request->user()->workspaces()->first();
            if (!$workspace || $bioSite->workspace_id !== $workspace->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            // Update bio site settings
            $settings = $request->input('settings');
            $bioSite->update([
                'theme' => $settings['theme'] ?? 'default',
                'background_color' => $settings['background_color'] ?? '#ffffff',
                'text_color' => $settings['text_color'] ?? '#000000',
                'font_family' => $settings['font_family'] ?? 'Inter',
                'custom_css' => $settings['custom_css'] ?? '',
                'favicon' => $settings['favicon'] ?? null,
                'meta_title' => $settings['meta_title'] ?? $bioSite->name,
                'meta_description' => $settings['meta_description'] ?? '',
                'analytics_code' => $settings['analytics_code'] ?? '',
                'is_published' => $request->input('publish', false)
            ]);

            // Delete existing components
            $bioSite->components()->delete();

            // Save new components
            foreach ($request->input('components') as $index => $componentData) {
                BioSiteComponent::create([
                    'bio_site_id' => $bioSite->id,
                    'component_type' => $componentData['type'],
                    'component_props' => $componentData['props'] ?? [],
                    'position' => $index,
                    'is_visible' => $componentData['visible'] ?? true
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Bio site saved successfully',
                'data' => [
                    'bio_site' => $bioSite->fresh(),
                    'preview_url' => route('bio-site.preview', ['slug' => $bioSite->slug]),
                    'public_url' => $bioSite->is_published ? route('bio-site.public', ['slug' => $bioSite->slug]) : null
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to save bio site', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to save bio site'
            ], 500);
        }
    }

    /**
     * Get available templates
     */
    public function getTemplates(Request $request)
    {
        try {
            $templates = [
                [
                    'id' => 'minimal',
                    'name' => 'Minimal',
                    'description' => 'Clean and simple design',
                    'thumbnail' => 'https://via.placeholder.com/300x500',
                    'category' => 'Professional',
                    'premium' => false,
                    'components' => [
                        ['type' => 'text', 'props' => ['content' => 'Welcome to my page', 'font_size' => 24, 'font_weight' => 'bold', 'align' => 'center']],
                        ['type' => 'image', 'props' => ['src' => 'https://via.placeholder.com/150', 'width' => '150px', 'height' => '150px', 'border_radius' => '50%']],
                        ['type' => 'text', 'props' => ['content' => 'Your bio text goes here', 'align' => 'center']],
                        ['type' => 'link', 'props' => ['title' => 'Visit My Website', 'url' => 'https://example.com']],
                        ['type' => 'social_links', 'props' => ['align' => 'center']]
                    ]
                ],
                [
                    'id' => 'creative',
                    'name' => 'Creative',
                    'description' => 'Colorful and engaging design',
                    'thumbnail' => 'https://via.placeholder.com/300x500',
                    'category' => 'Creative',
                    'premium' => false,
                    'components' => [
                        ['type' => 'text', 'props' => ['content' => 'Creative Professional', 'font_size' => 28, 'font_weight' => 'bold', 'align' => 'center', 'color' => '#6c5ce7']],
                        ['type' => 'image', 'props' => ['src' => 'https://via.placeholder.com/200x150', 'border_radius' => '12px']],
                        ['type' => 'text', 'props' => ['content' => 'Exploring creativity through design and innovation', 'align' => 'center']],
                        ['type' => 'link', 'props' => ['title' => 'View Portfolio', 'url' => 'https://example.com', 'background_color' => '#6c5ce7']],
                        ['type' => 'link', 'props' => ['title' => 'Contact Me', 'url' => 'mailto:hello@example.com', 'background_color' => '#00cec9']],
                        ['type' => 'social_links', 'props' => ['align' => 'center']]
                    ]
                ],
                [
                    'id' => 'business',
                    'name' => 'Business',
                    'description' => 'Professional business layout',
                    'thumbnail' => 'https://via.placeholder.com/300x500',
                    'category' => 'Business',
                    'premium' => true,
                    'components' => [
                        ['type' => 'text', 'props' => ['content' => 'John Doe', 'font_size' => 26, 'font_weight' => 'bold', 'align' => 'center']],
                        ['type' => 'text', 'props' => ['content' => 'CEO & Founder', 'font_size' => 18, 'align' => 'center', 'color' => '#666666']],
                        ['type' => 'image', 'props' => ['src' => 'https://via.placeholder.com/150', 'width' => '150px', 'height' => '150px', 'border_radius' => '50%']],
                        ['type' => 'text', 'props' => ['content' => 'Leading innovation in technology and business solutions', 'align' => 'center']],
                        ['type' => 'link', 'props' => ['title' => 'Book a Meeting', 'url' => 'https://calendly.com/username', 'background_color' => '#007bff']],
                        ['type' => 'link', 'props' => ['title' => 'Download Resume', 'url' => 'https://example.com/resume.pdf', 'background_color' => '#28a745']],
                        ['type' => 'testimonial', 'props' => ['content' => 'Outstanding leadership and vision', 'author' => 'Jane Smith', 'role' => 'Business Partner']],
                        ['type' => 'social_links', 'props' => ['align' => 'center']]
                    ]
                ],
                [
                    'id' => 'influencer',
                    'name' => 'Influencer',
                    'description' => 'Perfect for content creators',
                    'thumbnail' => 'https://via.placeholder.com/300x500',
                    'category' => 'Influencer',
                    'premium' => true,
                    'components' => [
                        ['type' => 'text', 'props' => ['content' => '@username', 'font_size' => 24, 'font_weight' => 'bold', 'align' => 'center']],
                        ['type' => 'image', 'props' => ['src' => 'https://via.placeholder.com/200x200', 'width' => '200px', 'height' => '200px', 'border_radius' => '50%']],
                        ['type' => 'text', 'props' => ['content' => 'Content Creator | Lifestyle | Travel', 'align' => 'center']],
                        ['type' => 'link', 'props' => ['title' => 'Latest YouTube Video', 'url' => 'https://youtube.com/watch?v=example', 'background_color' => '#ff0000']],
                        ['type' => 'link', 'props' => ['title' => 'Shop My Favorites', 'url' => 'https://example.com/shop', 'background_color' => '#ff6b6b']],
                        ['type' => 'link', 'props' => ['title' => 'Collaboration Inquiries', 'url' => 'mailto:business@example.com', 'background_color' => '#4ecdc4']],
                        ['type' => 'social_links', 'props' => ['align' => 'center']],
                        ['type' => 'countdown', 'props' => ['title' => 'Next Live Stream', 'target_date' => '2025-12-31T20:00:00']]
                    ]
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $templates
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get templates', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to get templates'
            ], 500);
        }
    }

    /**
     * Apply template to bio site
     */
    public function applyTemplate(Request $request, $id)
    {
        try {
            $request->validate([
                'template_id' => 'required|string'
            ]);

            $bioSite = BioSite::findOrFail($id);
            
            // Check if user owns this bio site
            $workspace = $request->user()->workspaces()->first();
            if (!$workspace || $bioSite->workspace_id !== $workspace->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            // Get template data
            $templatesResponse = $this->getTemplates($request);
            $templates = $templatesResponse->getData(true)['data'];
            
            $template = collect($templates)->firstWhere('id', $request->input('template_id'));
            
            if (!$template) {
                return response()->json([
                    'success' => false,
                    'message' => 'Template not found'
                ], 404);
            }

            // Delete existing components
            $bioSite->components()->delete();

            // Apply template components
            foreach ($template['components'] as $index => $componentData) {
                BioSiteComponent::create([
                    'bio_site_id' => $bioSite->id,
                    'component_type' => $componentData['type'],
                    'component_props' => $componentData['props'] ?? [],
                    'position' => $index,
                    'is_visible' => true
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Template applied successfully',
                'data' => [
                    'bio_site' => $bioSite->fresh(),
                    'components' => $bioSite->components()->orderBy('position')->get()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to apply template', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to apply template'
            ], 500);
        }
    }

    /**
     * Preview bio site
     */
    public function previewBioSite(Request $request, $slug)
    {
        try {
            $bioSite = BioSite::with('components')->where('slug', $slug)->firstOrFail();
            
            $components = $bioSite->components()->where('is_visible', true)->orderBy('position')->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'bio_site' => $bioSite,
                    'components' => $components,
                    'preview_mode' => true
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to preview bio site', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Bio site not found'
            ], 404);
        }
    }

    /**
     * Upload media for bio site
     */
    public function uploadMedia(Request $request, $id)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:jpeg,png,jpg,gif,svg,mp4,mov,avi|max:10240',
                'type' => 'required|in:image,video'
            ]);

            $bioSite = BioSite::findOrFail($id);
            
            // Check if user owns this bio site
            $workspace = $request->user()->workspaces()->first();
            if (!$workspace || $bioSite->workspace_id !== $workspace->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            $file = $request->file('file');
            $type = $request->input('type');
            
            // Generate unique filename
            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            
            // Store file
            $path = $file->storeAs("bio-sites/{$bioSite->id}/{$type}s", $filename, 'public');
            
            // Generate URL
            $url = Storage::url($path);

            return response()->json([
                'success' => true,
                'message' => 'Media uploaded successfully',
                'data' => [
                    'url' => $url,
                    'filename' => $filename,
                    'type' => $type,
                    'size' => $file->getSize()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to upload media', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload media'
            ], 500);
        }
    }
}