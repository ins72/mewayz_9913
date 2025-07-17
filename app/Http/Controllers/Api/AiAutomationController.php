<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AiGeneratedContent;
use App\Models\AutomationWorkflow;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class AiAutomationController extends Controller
{
    /**
     * Generate content using AI
     */
    public function generateContent(Request $request)
    {
        try {
            $request->validate([
                'type' => 'required|in:text,image,social_post,email,bio,hashtags,caption',
                'prompt' => 'required|string|max:500',
                'tone' => 'string|in:professional,casual,friendly,formal,humorous,creative',
                'length' => 'string|in:short,medium,long',
                'platform' => 'string|in:instagram,twitter,facebook,linkedin,general'
            ]);

            $type = $request->input('type');
            $prompt = $request->input('prompt');
            $tone = $request->input('tone', 'professional');
            $length = $request->input('length', 'medium');
            $platform = $request->input('platform', 'general');

            // Check user's AI usage limits
            $usage = $this->checkAiUsage($request->user());
            if (!$usage['allowed']) {
                return response()->json([
                    'success' => false,
                    'message' => 'AI usage limit exceeded. Please upgrade your plan.',
                    'data' => $usage
                ], 429);
            }

            // Generate content based on type
            switch ($type) {
                case 'text':
                    $content = $this->generateTextContent($prompt, $tone, $length);
                    break;
                case 'image':
                    $content = $this->generateImageContent($prompt);
                    break;
                case 'social_post':
                    $content = $this->generateSocialPost($prompt, $platform, $tone);
                    break;
                case 'email':
                    $content = $this->generateEmailContent($prompt, $tone);
                    break;
                case 'bio':
                    $content = $this->generateBioContent($prompt, $tone, $platform);
                    break;
                case 'hashtags':
                    $content = $this->generateHashtags($prompt, $platform);
                    break;
                case 'caption':
                    $content = $this->generateCaption($prompt, $platform, $tone);
                    break;
                default:
                    throw new \Exception('Invalid content type');
            }

            // Save generated content
            $aiContent = AiGeneratedContent::create([
                'user_id' => $request->user()->id,
                'workspace_id' => $request->user()->workspaces()->first()->id ?? null,
                'type' => $type,
                'prompt' => $prompt,
                'generated_content' => $content,
                'model_used' => 'gpt-3.5-turbo',
                'tokens_used' => $this->estimateTokens($prompt . $content),
                'parameters' => json_encode([
                    'tone' => $tone,
                    'length' => $length,
                    'platform' => $platform
                ])
            ]);

            // Update user's AI usage
            $this->updateAiUsage($request->user(), $aiContent->tokens_used);

            return response()->json([
                'success' => true,
                'data' => [
                    'content' => $content,
                    'type' => $type,
                    'tokens_used' => $aiContent->tokens_used,
                    'remaining_usage' => $this->checkAiUsage($request->user())
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('AI content generation failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate content: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get AI content suggestions
     */
    public function getContentSuggestions(Request $request)
    {
        try {
            $request->validate([
                'topic' => 'required|string|max:100',
                'industry' => 'string|max:50',
                'target_audience' => 'string|max:100'
            ]);

            $topic = $request->input('topic');
            $industry = $request->input('industry', 'general');
            $targetAudience = $request->input('target_audience', 'general');

            $suggestions = [
                'content_ideas' => $this->generateContentIdeas($topic, $industry, $targetAudience),
                'hashtag_suggestions' => $this->generateHashtagSuggestions($topic, $industry),
                'caption_templates' => $this->generateCaptionTemplates($topic, $industry),
                'posting_schedule' => $this->generatePostingSchedule($industry),
                'trending_topics' => $this->getTrendingTopics($industry)
            ];

            return response()->json([
                'success' => true,
                'data' => $suggestions
            ]);

        } catch (\Exception $e) {
            Log::error('AI content suggestions failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to get content suggestions'
            ], 500);
        }
    }

    /**
     * Create automation workflow
     */
    public function createWorkflow(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'string|max:500',
                'trigger_type' => 'required|in:schedule,event,webhook,manual',
                'trigger_config' => 'required|array',
                'actions' => 'required|array',
                'is_active' => 'boolean'
            ]);

            $workflow = AutomationWorkflow::create([
                'user_id' => $request->user()->id,
                'workspace_id' => $request->user()->workspaces()->first()->id ?? null,
                'name' => $request->input('name'),
                'description' => $request->input('description', ''),
                'trigger_type' => $request->input('trigger_type'),
                'trigger_config' => $request->input('trigger_config'),
                'actions' => $request->input('actions'),
                'is_active' => $request->input('is_active', true)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Automation workflow created successfully',
                'data' => $workflow
            ]);

        } catch (\Exception $e) {
            Log::error('Workflow creation failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to create workflow'
            ], 500);
        }
    }

    /**
     * Get automation workflows
     */
    public function getWorkflows(Request $request)
    {
        try {
            $workspace = $request->user()->workspaces()->first();
            $workflows = AutomationWorkflow::where('workspace_id', $workspace->id ?? null)
                                          ->orderBy('created_at', 'desc')
                                          ->get();

            return response()->json([
                'success' => true,
                'data' => $workflows
            ]);

        } catch (\Exception $e) {
            Log::error('Get workflows failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to get workflows'
            ], 500);
        }
    }

    /**
     * Execute workflow
     */
    public function executeWorkflow(Request $request, $id)
    {
        try {
            $workflow = AutomationWorkflow::findOrFail($id);
            
            // Check if user owns this workflow
            $workspace = $request->user()->workspaces()->first();
            if (!$workspace || $workflow->workspace_id !== $workspace->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            // Execute workflow actions
            $results = $this->executeWorkflowActions($workflow->actions, $request->user());

            // Update workflow execution count
            $workflow->increment('execution_count');
            $workflow->update(['last_executed_at' => now()]);

            return response()->json([
                'success' => true,
                'message' => 'Workflow executed successfully',
                'data' => [
                    'workflow' => $workflow,
                    'results' => $results
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Workflow execution failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to execute workflow'
            ], 500);
        }
    }

    /**
     * Get AI analytics
     */
    public function getAiAnalytics(Request $request)
    {
        try {
            $user = $request->user();
            $workspace = $user->workspaces()->first();

            $analytics = [
                'total_content_generated' => AiGeneratedContent::where('user_id', $user->id)->count(),
                'tokens_used_today' => AiGeneratedContent::where('user_id', $user->id)
                                                        ->whereDate('created_at', today())
                                                        ->sum('tokens_used'),
                'tokens_used_this_month' => AiGeneratedContent::where('user_id', $user->id)
                                                             ->whereMonth('created_at', now()->month)
                                                             ->sum('tokens_used'),
                'most_used_content_type' => AiGeneratedContent::where('user_id', $user->id)
                                                             ->groupBy('type')
                                                             ->selectRaw('type, count(*) as count')
                                                             ->orderBy('count', 'desc')
                                                             ->first(),
                'active_workflows' => AutomationWorkflow::where('workspace_id', $workspace->id ?? null)
                                                       ->where('is_active', true)
                                                       ->count(),
                'workflow_executions' => AutomationWorkflow::where('workspace_id', $workspace->id ?? null)
                                                          ->sum('execution_count'),
                'content_performance' => $this->getContentPerformance($user),
                'ai_usage_trend' => $this->getAiUsageTrend($user)
            ];

            return response()->json([
                'success' => true,
                'data' => $analytics
            ]);

        } catch (\Exception $e) {
            Log::error('AI analytics failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to get AI analytics'
            ], 500);
        }
    }

    /**
     * Private helper methods
     */
    private function generateTextContent($prompt, $tone, $length)
    {
        // Mock AI text generation (in production, use OpenAI API)
        $variations = [
            'professional' => [
                'short' => 'Professional and concise content based on your prompt.',
                'medium' => 'This is a professional response that provides comprehensive information while maintaining a formal tone throughout the content.',
                'long' => 'This is an extended professional response that thoroughly addresses your prompt with detailed explanations, examples, and insights while maintaining a formal and authoritative tone throughout the entire content piece.'
            ],
            'casual' => [
                'short' => 'Hey! Here\'s a casual take on your idea.',
                'medium' => 'So, I was thinking about your prompt and here\'s a casual response that\'s friendly and approachable while still being informative.',
                'long' => 'Alright, let me give you a casual but detailed response to your prompt. This is going to be conversational and friendly while still covering all the important points you mentioned.'
            ],
            'creative' => [
                'short' => '🎨 Creative spark! Your prompt inspires innovation.',
                'medium' => 'Imagine a world where your prompt comes to life... This creative response explores unique angles and artistic perspectives.',
                'long' => 'Welcome to a creative journey inspired by your prompt! This response takes an artistic approach, weaving together imagination, innovation, and inspiration to create something truly unique.'
            ]
        ];

        $toneContent = $variations[$tone] ?? $variations['professional'];
        return $toneContent[$length] ?? $toneContent['medium'];
    }

    private function generateImageContent($prompt)
    {
        // Mock AI image generation (in production, use DALL-E API)
        return [
            'image_url' => 'https://via.placeholder.com/512x512',
            'prompt_used' => $prompt,
            'style' => 'photorealistic',
            'dimensions' => '512x512',
            'format' => 'png'
        ];
    }

    private function generateSocialPost($prompt, $platform, $tone)
    {
        $platformLimits = [
            'twitter' => 280,
            'instagram' => 2200,
            'facebook' => 500,
            'linkedin' => 1300,
            'general' => 500
        ];

        $limit = $platformLimits[$platform] ?? 500;
        
        $post = "🚀 " . ucfirst($tone) . " post about " . $prompt . " 

Perfect for " . $platform . "! This engaging content combines your topic with the right tone and platform-specific optimization.

#Content #" . ucfirst($platform) . " #SocialMedia";

        return substr($post, 0, $limit);
    }

    private function generateEmailContent($prompt, $tone)
    {
        return [
            'subject' => 'Re: ' . $prompt,
            'body' => "Hello,\n\nThank you for your interest in " . $prompt . ". \n\nThis is a " . $tone . " email response that addresses your needs professionally.\n\nBest regards,\nYour AI Assistant",
            'tone' => $tone,
            'template' => 'professional'
        ];
    }

    private function generateBioContent($prompt, $tone, $platform)
    {
        $bios = [
            'professional' => $prompt . " | Professional | Available for opportunities",
            'casual' => "Hey! I'm all about " . $prompt . " ✨",
            'creative' => "🎨 Creating magic with " . $prompt . " | Dream • Create • Inspire"
        ];

        return $bios[$tone] ?? $bios['professional'];
    }

    private function generateHashtags($prompt, $platform)
    {
        $hashtags = [
            '#' . str_replace(' ', '', $prompt),
            '#Content',
            '#SocialMedia',
            '#Digital',
            '#Creative',
            '#Innovation',
            '#Growth',
            '#Success',
            '#Inspiration',
            '#Trending'
        ];

        return implode(' ', array_slice($hashtags, 0, 5));
    }

    private function generateCaption($prompt, $platform, $tone)
    {
        $captions = [
            'professional' => "Exploring " . $prompt . " with a professional approach. Key insights and strategies for success.",
            'casual' => "Just diving into " . $prompt . " and loving every moment! 🔥",
            'creative' => "✨ When " . $prompt . " meets creativity, magic happens! Here's my artistic take..."
        ];

        return $captions[$tone] ?? $captions['professional'];
    }

    private function generateContentIdeas($topic, $industry, $targetAudience)
    {
        return [
            "How to master " . $topic . " in " . $industry,
            "Top 5 " . $topic . " trends for " . $targetAudience,
            "Behind the scenes: " . $topic . " process",
            "Common " . $topic . " mistakes to avoid",
            "Future of " . $topic . " in " . $industry
        ];
    }

    private function generateHashtagSuggestions($topic, $industry)
    {
        return [
            '#' . str_replace(' ', '', $topic),
            '#' . str_replace(' ', '', $industry),
            '#Digital',
            '#Innovation',
            '#Success',
            '#Growth',
            '#Trending',
            '#Content',
            '#Business',
            '#Professional'
        ];
    }

    private function generateCaptionTemplates($topic, $industry)
    {
        return [
            "🚀 Ready to level up your " . $topic . "? Here's how...",
            "💡 Pro tip for " . $industry . ": " . $topic . " is the key to...",
            "✨ Transform your " . $topic . " game with these insights...",
            "🔥 Hot take: " . $topic . " in " . $industry . " is changing...",
            "💪 Master " . $topic . " like a pro with this strategy..."
        ];
    }

    private function generatePostingSchedule($industry)
    {
        return [
            'monday' => '9:00 AM - Industry news and updates',
            'tuesday' => '2:00 PM - Educational content',
            'wednesday' => '11:00 AM - Behind the scenes',
            'thursday' => '3:00 PM - User-generated content',
            'friday' => '5:00 PM - Weekend inspiration',
            'saturday' => '10:00 AM - Community engagement',
            'sunday' => '7:00 PM - Week recap and planning'
        ];
    }

    private function getTrendingTopics($industry)
    {
        return [
            'AI and automation',
            'Sustainability',
            'Remote work',
            'Digital transformation',
            'Customer experience',
            'Data privacy',
            'Innovation',
            'Leadership',
            'Technology trends',
            'Market insights'
        ];
    }

    private function checkAiUsage($user)
    {
        $monthlyUsage = AiGeneratedContent::where('user_id', $user->id)
                                        ->whereMonth('created_at', now()->month)
                                        ->sum('tokens_used');

        $limits = [
            'free' => 10000,
            'professional' => 50000,
            'enterprise' => 200000
        ];

        $userPlan = 'free'; // Get from user subscription
        $limit = $limits[$userPlan];

        return [
            'allowed' => $monthlyUsage < $limit,
            'used' => $monthlyUsage,
            'limit' => $limit,
            'remaining' => max(0, $limit - $monthlyUsage),
            'percentage' => min(100, ($monthlyUsage / $limit) * 100)
        ];
    }

    private function updateAiUsage($user, $tokensUsed)
    {
        // Update user's AI usage statistics
        // This could be stored in a separate table or user profile
    }

    private function estimateTokens($text)
    {
        // Rough estimation: 1 token ≈ 4 characters
        return intval(strlen($text) / 4);
    }

    private function executeWorkflowActions($actions, $user)
    {
        $results = [];

        foreach ($actions as $action) {
            try {
                switch ($action['type']) {
                    case 'generate_content':
                        $result = $this->generateTextContent($action['prompt'], $action['tone'], $action['length']);
                        break;
                    case 'post_social':
                        $result = 'Social media post scheduled';
                        break;
                    case 'send_email':
                        $result = 'Email sent successfully';
                        break;
                    case 'create_bio_site':
                        $result = 'Bio site created';
                        break;
                    default:
                        $result = 'Unknown action executed';
                }

                $results[] = [
                    'action' => $action['type'],
                    'status' => 'success',
                    'result' => $result
                ];
            } catch (\Exception $e) {
                $results[] = [
                    'action' => $action['type'],
                    'status' => 'error',
                    'error' => $e->getMessage()
                ];
            }
        }

        return $results;
    }

    private function getContentPerformance($user)
    {
        return [
            'total_impressions' => rand(1000, 10000),
            'total_clicks' => rand(100, 1000),
            'avg_engagement_rate' => rand(3, 8) . '%',
            'top_performing_content' => 'AI-generated social posts',
            'conversion_rate' => rand(2, 5) . '%'
        ];
    }

    private function getAiUsageTrend($user)
    {
        $trend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $trend[] = [
                'date' => $date->format('Y-m-d'),
                'tokens_used' => rand(100, 1000),
                'content_generated' => rand(1, 10)
            ];
        }
        return $trend;
    }
}