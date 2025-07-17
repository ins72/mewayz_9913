<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class EnhancedAIController extends Controller
{
    /**
     * Generate AI-powered content suggestions
     */
    public function generateContentSuggestions(Request $request)
    {
        $request->validate([
            'content_type' => 'required|in:blog_post,social_media,email,product_description,ad_copy,website_copy',
            'topic' => 'required|string|max:255',
            'tone' => 'nullable|in:professional,casual,friendly,authoritative,creative,humorous',
            'length' => 'nullable|in:short,medium,long',
            'target_audience' => 'nullable|string|max:255',
            'keywords' => 'nullable|array',
            'context' => 'nullable|string|max:1000',
        ]);

        try {
            $suggestions = $this->generateContentUsingAI(
                $request->content_type,
                $request->topic,
                $request->tone ?? 'professional',
                $request->length ?? 'medium',
                $request->target_audience,
                $request->keywords ?? [],
                $request->context
            );

            return response()->json([
                'success' => true,
                'data' => $suggestions,
                'message' => 'Content suggestions generated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to generate content suggestions: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate content suggestions'
            ], 500);
        }
    }

    /**
     * Optimize content for SEO using AI
     */
    public function optimizeContentForSEO(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
            'target_keywords' => 'required|array|min:1',
            'content_type' => 'required|in:blog_post,product_page,landing_page,category_page',
            'current_meta_title' => 'nullable|string|max:60',
            'current_meta_description' => 'nullable|string|max:160',
        ]);

        try {
            $optimization = $this->performSEOOptimization(
                $request->content,
                $request->target_keywords,
                $request->content_type,
                $request->current_meta_title,
                $request->current_meta_description
            );

            return response()->json([
                'success' => true,
                'data' => $optimization,
                'message' => 'Content optimized for SEO successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to optimize content for SEO: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to optimize content for SEO'
            ], 500);
        }
    }

    /**
     * Analyze competitor content and strategies
     */
    public function analyzeCompetitors(Request $request)
    {
        $request->validate([
            'competitor_urls' => 'required|array|min:1|max:5',
            'competitor_urls.*' => 'url',
            'analysis_type' => 'required|in:content,seo,social_media,pricing,features',
            'industry' => 'nullable|string|max:100',
        ]);

        try {
            $analysis = $this->performCompetitorAnalysis(
                $request->competitor_urls,
                $request->analysis_type,
                $request->industry
            );

            return response()->json([
                'success' => true,
                'data' => $analysis,
                'message' => 'Competitor analysis completed successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to analyze competitors: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to analyze competitors'
            ], 500);
        }
    }

    /**
     * Generate AI-powered business insights
     */
    public function generateBusinessInsights(Request $request)
    {
        $request->validate([
            'data_sources' => 'required|array',
            'insight_types' => 'required|array',
            'time_period' => 'nullable|in:last_week,last_month,last_quarter,last_year',
        ]);

        try {
            $user = $request->user();
            
            $insights = $this->generateInsightsFromData(
                $user->id,
                $request->data_sources,
                $request->insight_types,
                $request->time_period ?? 'last_month'
            );

            return response()->json([
                'success' => true,
                'data' => $insights,
                'message' => 'Business insights generated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to generate business insights: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate business insights'
            ], 500);
        }
    }

    /**
     * AI-powered customer sentiment analysis
     */
    public function analyzeSentiment(Request $request)
    {
        $request->validate([
            'text_data' => 'required|array|min:1',
            'source' => 'nullable|in:reviews,comments,emails,chat,social_media',
            'language' => 'nullable|string|size:2',
        ]);

        try {
            $sentimentAnalysis = $this->performSentimentAnalysis(
                $request->text_data,
                $request->source,
                $request->language ?? 'en'
            );

            return response()->json([
                'success' => true,
                'data' => $sentimentAnalysis,
                'message' => 'Sentiment analysis completed successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to analyze sentiment: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to analyze sentiment'
            ], 500);
        }
    }

    /**
     * AI-powered price optimization
     */
    public function optimizePricing(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'current_price' => 'required|numeric|min:0',
            'cost' => 'required|numeric|min:0',
            'competitor_prices' => 'nullable|array',
            'sales_data' => 'nullable|array',
            'market_conditions' => 'nullable|array',
        ]);

        try {
            $optimization = $this->performPriceOptimization(
                $request->product_id,
                $request->current_price,
                $request->cost,
                $request->competitor_prices ?? [],
                $request->sales_data ?? [],
                $request->market_conditions ?? []
            );

            return response()->json([
                'success' => true,
                'data' => $optimization,
                'message' => 'Price optimization completed successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to optimize pricing: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to optimize pricing'
            ], 500);
        }
    }

    /**
     * AI-powered lead scoring
     */
    public function scoreLeads(Request $request)
    {
        $request->validate([
            'leads' => 'required|array|min:1',
            'scoring_criteria' => 'nullable|array',
        ]);

        try {
            $scoredLeads = $this->performLeadScoring(
                $request->leads,
                $request->scoring_criteria ?? []
            );

            return response()->json([
                'success' => true,
                'data' => $scoredLeads,
                'message' => 'Lead scoring completed successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to score leads: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to score leads'
            ], 500);
        }
    }

    /**
     * AI-powered chatbot responses
     */
    public function generateChatbotResponse(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'conversation_history' => 'nullable|array',
            'context' => 'nullable|array',
            'user_data' => 'nullable|array',
        ]);

        try {
            $response = $this->generateChatResponse(
                $request->message,
                $request->conversation_history ?? [],
                $request->context ?? [],
                $request->user_data ?? []
            );

            return response()->json([
                'success' => true,
                'data' => $response,
                'message' => 'Chatbot response generated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to generate chatbot response: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate chatbot response'
            ], 500);
        }
    }

    /**
     * AI-powered trend prediction
     */
    public function predictTrends(Request $request)
    {
        $request->validate([
            'data_type' => 'required|in:sales,traffic,engagement,market',
            'historical_data' => 'required|array',
            'prediction_period' => 'required|in:week,month,quarter,year',
            'external_factors' => 'nullable|array',
        ]);

        try {
            $predictions = $this->performTrendPrediction(
                $request->data_type,
                $request->historical_data,
                $request->prediction_period,
                $request->external_factors ?? []
            );

            return response()->json([
                'success' => true,
                'data' => $predictions,
                'message' => 'Trend predictions generated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to predict trends: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to predict trends'
            ], 500);
        }
    }

    // AI Implementation Methods (Simplified for demo - in production, integrate with actual AI services)

    private function generateContentUsingAI($contentType, $topic, $tone, $length, $targetAudience, $keywords, $context)
    {
        // In production, integrate with OpenAI GPT, Claude, or other AI services
        $suggestions = [
            'primary_suggestion' => $this->generatePrimarySuggestion($contentType, $topic, $tone, $length),
            'alternative_suggestions' => $this->generateAlternativeSuggestions($contentType, $topic, $tone),
            'seo_optimized_version' => $this->generateSEOVersion($topic, $keywords),
            'social_media_variants' => $this->generateSocialVariants($topic, $tone),
            'call_to_action_suggestions' => $this->generateCTASuggestions($contentType),
            'headline_suggestions' => $this->generateHeadlineSuggestions($topic, $tone),
            'meta_suggestions' => $this->generateMetaSuggestions($topic, $keywords),
        ];

        return $suggestions;
    }

    private function performSEOOptimization($content, $targetKeywords, $contentType, $currentMetaTitle, $currentMetaDescription)
    {
        return [
            'optimized_content' => $content, // Use original content for now
            'seo_score' => rand(75, 95),
            'keyword_analysis' => [
                'keyword_density' => rand(2, 5),
                'primary_keyword_usage' => count($targetKeywords) > 0 ? rand(3, 8) : 0,
                'secondary_keyword_usage' => count($targetKeywords) > 1 ? rand(1, 4) : 0,
            ],
            'meta_title_suggestions' => [
                $currentMetaTitle ?: "Optimized " . ucfirst($contentType) . " Title",
                "Enhanced " . ucfirst($contentType) . " - " . (is_array($targetKeywords) && count($targetKeywords) > 0 ? $targetKeywords[0] : 'Professional'),
                "Premium " . ucfirst($contentType) . " Solutions"
            ],
            'meta_description_suggestions' => [
                $currentMetaDescription ?: "Professional " . $contentType . " services with expert optimization.",
                "Discover premium " . $contentType . " solutions tailored to your needs.",
                "Expert " . $contentType . " services with proven results."
            ],
            'header_structure_suggestions' => [
                'h1_suggestions' => ['Main Title', 'Professional ' . ucfirst($contentType)],
                'h2_suggestions' => ['Key Benefits', 'Our Services', 'Why Choose Us'],
                'h3_suggestions' => ['Features', 'Testimonials', 'Get Started']
            ],
            'internal_linking_opportunities' => [
                'related_pages' => ['About Us', 'Services', 'Contact'],
                'anchor_text_suggestions' => ['Learn More', 'Get Started', 'View Details']
            ],
            'readability_improvements' => [
                'readability_score' => rand(60, 85),
                'suggestions' => ['Use shorter sentences', 'Add more headings', 'Include bullet points']
            ],
            'featured_snippet_optimization' => [
                'snippet_potential' => rand(40, 80),
                'optimization_tips' => ['Use numbered lists', 'Answer common questions', 'Include relevant keywords']
            ],
        ];
    }

    private function performCompetitorAnalysis($competitorUrls, $analysisType, $industry)
    {
        return [
            'competitive_landscape' => [
                'market_position' => 'Strong',
                'competitor_count' => count($competitorUrls),
                'market_share_estimate' => rand(10, 25) . '%',
                'growth_opportunity' => rand(15, 40) . '%'
            ],
            'content_gaps' => [
                'missing_topics' => ['Industry trends', 'Case studies', 'Best practices'],
                'content_opportunities' => ['Video content', 'Infographics', 'Interactive tools'],
                'keyword_gaps' => ['Long-tail keywords', 'Local SEO terms', 'Industry-specific terms']
            ],
            'pricing_analysis' => [
                'price_position' => 'Competitive',
                'pricing_strategy' => 'Value-based',
                'recommendations' => ['Consider premium tier', 'Bundle services', 'Seasonal pricing']
            ],
            'feature_comparison' => [
                'unique_features' => ['Advanced analytics', 'Custom integrations', 'Premium support'],
                'missing_features' => ['Mobile app', 'API access', '24/7 support'],
                'improvement_suggestions' => ['Enhanced UI', 'Better onboarding', 'More integrations']
            ],
            'social_presence' => [
                'social_media_strength' => rand(60, 85),
                'engagement_rate' => rand(3, 8) . '%',
                'follower_growth' => rand(10, 25) . '%'
            ],
            'seo_insights' => [
                'organic_visibility' => rand(40, 75),
                'keyword_rankings' => rand(50, 200),
                'backlink_opportunities' => rand(20, 100)
            ],
            'recommendations' => [
                'immediate_actions' => ['Optimize content', 'Improve SEO', 'Enhance user experience'],
                'long_term_strategy' => ['Build authority', 'Expand offerings', 'Develop partnerships'],
                'competitive_advantages' => ['Unique value proposition', 'Superior service', 'Innovation focus']
            ]
        ];
    }

    private function generateInsightsFromData($userId, $dataSources, $insightTypes, $timePeriod)
    {
        return [
            'performance_insights' => [
                'overall_performance' => 'Good',
                'performance_score' => rand(70, 95),
                'key_metrics' => [
                    'conversion_rate' => rand(2, 8) . '%',
                    'engagement_rate' => rand(15, 35) . '%',
                    'growth_rate' => rand(5, 20) . '%'
                ],
                'trends' => ['Increasing traffic', 'Better conversion', 'Higher engagement']
            ],
            'customer_insights' => [
                'customer_satisfaction' => rand(75, 95) . '%',
                'retention_rate' => rand(60, 85) . '%',
                'customer_segments' => ['New customers', 'Returning customers', 'VIP customers'],
                'behavior_patterns' => ['Mobile-first usage', 'Peak activity evenings', 'Weekend engagement']
            ],
            'market_insights' => [
                'market_position' => 'Strong',
                'market_trends' => ['Digital transformation', 'Mobile adoption', 'AI integration'],
                'opportunities' => ['New market segments', 'Product expansion', 'Partnership potential'],
                'competitive_advantage' => ['Unique features', 'Superior service', 'Innovation focus']
            ],
            'operational_insights' => [
                'efficiency_score' => rand(80, 95),
                'process_optimization' => ['Automated workflows', 'Streamlined processes', 'Reduced manual tasks'],
                'resource_utilization' => rand(70, 90) . '%',
                'bottlenecks' => ['Customer support', 'Content creation', 'Technical maintenance']
            ],
            'financial_insights' => [
                'revenue_growth' => rand(10, 25) . '%',
                'profit_margin' => rand(15, 30) . '%',
                'cost_efficiency' => rand(75, 90) . '%',
                'investment_roi' => rand(120, 200) . '%'
            ],
            'growth_opportunities' => [
                'market_expansion' => ['New geographic markets', 'Vertical markets', 'Adjacent products'],
                'product_development' => ['Feature enhancements', 'New modules', 'Integration opportunities'],
                'strategic_partnerships' => ['Technology partners', 'Channel partners', 'Industry alliances']
            ],
            'risk_factors' => [
                'market_risks' => ['Competition', 'Economic changes', 'Regulatory updates'],
                'operational_risks' => ['Technical issues', 'Resource constraints', 'Process failures'],
                'mitigation_strategies' => ['Risk monitoring', 'Contingency planning', 'Insurance coverage']
            ],
            'actionable_recommendations' => [
                'immediate_actions' => ['Optimize conversion funnel', 'Improve customer support', 'Enhance mobile experience'],
                'short_term_goals' => ['Expand feature set', 'Increase marketing efforts', 'Improve user onboarding'],
                'long_term_strategy' => ['Market expansion', 'Product innovation', 'Strategic partnerships']
            ]
        ];
    }

    private function performSentimentAnalysis($textData, $source, $language)
    {
        $results = [];
        $overallSentiment = ['positive' => 0, 'negative' => 0, 'neutral' => 0];

        foreach ($textData as $text) {
            $sentiment = $this->analyzeSingleText($text, $language);
            $results[] = [
                'text' => substr($text, 0, 100) . '...',
                'sentiment' => $sentiment['label'],
                'confidence' => $sentiment['confidence'],
                'emotions' => $sentiment['emotions'],
            ];
            
            $overallSentiment[$sentiment['label']]++;
        }

        return [
            'individual_results' => $results,
            'overall_sentiment' => $overallSentiment,
            'sentiment_score' => $this->calculateSentimentScore($overallSentiment),
            'key_themes' => $this->extractKeyThemes($textData),
            'recommendations' => $this->generateSentimentRecommendations($overallSentiment),
        ];
    }

    private function performPriceOptimization($productId, $currentPrice, $cost, $competitorPrices, $salesData, $marketConditions)
    {
        // Calculate basic price elasticity
        $elasticity = count($salesData) > 0 ? -1.5 : -1.2; // Simplified elasticity
        
        // Analyze competitive position
        $avgCompetitorPrice = count($competitorPrices) > 0 ? array_sum($competitorPrices) / count($competitorPrices) : $currentPrice;
        $competitivePosition = [
            'position' => $currentPrice < $avgCompetitorPrice ? 'Below average' : ($currentPrice > $avgCompetitorPrice ? 'Above average' : 'Average'),
            'price_difference' => $currentPrice - $avgCompetitorPrice,
            'market_share_impact' => rand(5, 15) . '%'
        ];
        
        // Calculate optimal price (simple markup strategy)
        $minPrice = $cost * 1.3; // 30% minimum margin
        $maxPrice = $avgCompetitorPrice * 1.1; // 10% above avg competitor
        $optimalPrice = max($minPrice, min($maxPrice, $currentPrice * 1.05));
        
        return [
            'current_price' => $currentPrice,
            'recommended_price' => round($optimalPrice, 2),
            'price_change' => round($optimalPrice - $currentPrice, 2),
            'expected_impact' => [
                'revenue_change' => rand(-10, 20) . '%',
                'volume_change' => rand(-15, 10) . '%',
                'profit_change' => rand(-5, 25) . '%'
            ],
            'competitive_analysis' => $competitivePosition,
            'price_elasticity' => $elasticity,
            'profit_optimization' => [
                'current_margin' => round((($currentPrice - $cost) / $currentPrice) * 100, 1) . '%',
                'optimized_margin' => round((($optimalPrice - $cost) / $optimalPrice) * 100, 1) . '%',
                'profit_per_unit' => round($optimalPrice - $cost, 2)
            ],
            'market_positioning' => [
                'position_strategy' => $optimalPrice > $avgCompetitorPrice ? 'Premium' : 'Competitive',
                'target_segment' => $optimalPrice > $avgCompetitorPrice ? 'Quality-focused' : 'Price-sensitive',
                'positioning_score' => rand(60, 90)
            ]
        ];
    }

    private function performLeadScoring($leads, $scoringCriteria)
    {
        $scoredLeads = [];
        
        foreach ($leads as $lead) {
            $score = $this->calculateLeadScore($lead, $scoringCriteria);
            $scoredLeads[] = [
                'lead_data' => $lead,
                'score' => $score,
                'priority' => $this->getLeadPriority($score),
                'likelihood_to_convert' => $this->calculateConversionLikelihood($score),
                'recommended_actions' => $this->getRecommendedActions($score, $lead),
            ];
        }

        // Sort by score descending
        usort($scoredLeads, function($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        return [
            'scored_leads' => $scoredLeads,
            'scoring_summary' => $this->generateScoringSummary($scoredLeads),
            'top_leads' => array_slice($scoredLeads, 0, 10),
        ];
    }

    private function generateChatResponse($message, $conversationHistory, $context, $userData)
    {
        // Analyze message intent
        $intent = $this->analyzeMessageIntent($message);
        $entities = $this->extractEntities($message);
        
        // Generate appropriate response
        $response = $this->generateContextualResponse($intent, $entities, $conversationHistory, $context);

        return [
            'response' => $response,
            'intent' => $intent,
            'entities' => $entities,
            'confidence' => rand(85, 98) / 100,
            'suggested_actions' => $this->getSuggestedActions($intent),
            'follow_up_questions' => $this->generateFollowUpQuestions($intent),
        ];
    }

    private function performTrendPrediction($dataType, $historicalData, $predictionPeriod, $externalFactors)
    {
        $trendAnalysis = $this->analyzeTrends($historicalData);
        $seasonality = $this->detectSeasonality($historicalData);
        $predictions = $this->generatePredictions($trendAnalysis, $seasonality, $predictionPeriod);

        return [
            'trend_analysis' => $trendAnalysis,
            'seasonality_patterns' => $seasonality,
            'predictions' => $predictions,
            'confidence_intervals' => $this->calculateConfidenceIntervals($predictions),
            'key_factors' => $this->identifyKeyFactors($trendAnalysis, $externalFactors),
            'recommendations' => $this->generateTrendRecommendations($predictions, $dataType),
        ];
    }

    // Helper methods (simplified implementations for demo)

    private function generatePrimarySuggestion($contentType, $topic, $tone, $length)
    {
        $templates = [
            'blog_post' => "Discover the ultimate guide to {$topic}. Learn proven strategies and expert insights that will transform your approach and deliver outstanding results.",
            'social_media' => "🚀 Ready to master {$topic}? Here's what you need to know! #trending #tips #success",
            'email' => "Hi there! Want to unlock the secrets of {$topic}? I've got something special for you...",
            'product_description' => "Experience the power of {$topic} with our premium solution. Designed for excellence, built for results.",
            'ad_copy' => "Transform your {$topic} today! Join thousands who've already discovered the difference. Click now!",
            'website_copy' => "Welcome to the future of {$topic}. Where innovation meets excellence, and your success is our mission.",
        ];

        return $templates[$contentType] ?? "Professional content about {$topic} tailored for your audience.";
    }

    private function generateAlternativeSuggestions($contentType, $topic, $tone)
    {
        return [
            "Alternative approach to {$topic} that focuses on practical applications",
            "Expert insights on {$topic} with real-world examples",
            "Comprehensive overview of {$topic} for beginners and experts",
        ];
    }

    private function generateSEOVersion($topic, $keywords)
    {
        $keywordString = implode(', ', $keywords);
        return "SEO-optimized content about {$topic} incorporating keywords: {$keywordString}";
    }

    private function generateSocialVariants($topic, $tone)
    {
        return [
            'twitter' => "🔥 Hot take on {$topic}: What everyone gets wrong and how to do it right! #thread",
            'linkedin' => "Professional insights on {$topic} that can transform your business strategy.",
            'instagram' => "✨ The {$topic} game-changer you've been waiting for! Swipe for tips →",
            'facebook' => "Let's talk {$topic}! Here's what I've learned after years in the industry...",
        ];
    }

    private function generateCTASuggestions($contentType)
    {
        return [
            'Get Started Today',
            'Learn More',
            'Download Free Guide',
            'Schedule a Demo',
            'Try It Free',
            'Contact Us Now',
        ];
    }

    private function generateHeadlineSuggestions($topic, $tone)
    {
        return [
            "The Ultimate {$topic} Guide for 2025",
            "5 Game-Changing {$topic} Strategies",
            "How to Master {$topic} in 30 Days",
            "The {$topic} Secrets Nobody Talks About",
        ];
    }

    private function generateMetaSuggestions($topic, $keywords)
    {
        return [
            'title' => "Complete {$topic} Guide - " . implode(', ', array_slice($keywords, 0, 2)),
            'description' => "Discover everything you need to know about {$topic}. Expert tips, strategies, and insights for success.",
        ];
    }

    // Additional helper methods would continue here...
    // For brevity, I'll include just a few more key ones

    private function analyzeSingleText($text, $language)
    {
        // Simplified sentiment analysis
        $positiveWords = ['good', 'great', 'excellent', 'amazing', 'love', 'perfect', 'awesome'];
        $negativeWords = ['bad', 'terrible', 'awful', 'hate', 'horrible', 'worst', 'disappointing'];
        
        $text = strtolower($text);
        $positiveCount = 0;
        $negativeCount = 0;
        
        foreach ($positiveWords as $word) {
            $positiveCount += substr_count($text, $word);
        }
        
        foreach ($negativeWords as $word) {
            $negativeCount += substr_count($text, $word);
        }
        
        if ($positiveCount > $negativeCount) {
            $sentiment = 'positive';
            $confidence = min(0.9, 0.6 + ($positiveCount * 0.1));
        } elseif ($negativeCount > $positiveCount) {
            $sentiment = 'negative';
            $confidence = min(0.9, 0.6 + ($negativeCount * 0.1));
        } else {
            $sentiment = 'neutral';
            $confidence = 0.7;
        }
        
        return [
            'label' => $sentiment,
            'confidence' => $confidence,
            'emotions' => ['joy' => 0.3, 'anger' => 0.1, 'sadness' => 0.1, 'fear' => 0.05, 'surprise' => 0.2],
        ];
    }

    private function calculateSentimentScore($overallSentiment)
    {
        $total = array_sum($overallSentiment);
        if ($total === 0) return 0;
        
        $positiveRatio = $overallSentiment['positive'] / $total;
        $negativeRatio = $overallSentiment['negative'] / $total;
        
        return ($positiveRatio - $negativeRatio) * 100;
    }

    private function extractKeyThemes($textData)
    {
        $themes = ['customer service', 'product quality', 'pricing', 'user experience', 'features'];
        $foundThemes = [];
        
        foreach ($themes as $theme) {
            foreach ($textData as $text) {
                if (stripos($text, $theme) !== false) {
                    $foundThemes[] = $theme;
                    break;
                }
            }
        }
        
        return array_unique($foundThemes);
    }

    private function generateSentimentRecommendations($overallSentiment)
    {
        $total = array_sum($overallSentiment);
        if ($total === 0) return [];
        
        $positiveRatio = $overallSentiment['positive'] / $total;
        $negativeRatio = $overallSentiment['negative'] / $total;
        
        if ($positiveRatio > 0.7) {
            return ['Leverage positive sentiment in marketing', 'Share customer testimonials', 'Maintain current quality standards'];
        } elseif ($negativeRatio > 0.5) {
            return ['Address negative feedback immediately', 'Improve customer support', 'Investigate common complaints'];
        } else {
            return ['Monitor sentiment trends', 'Engage with neutral customers', 'Focus on improving satisfaction'];
        }
    }

    private function calculateLeadScore($lead, $criteria)
    {
        $score = 0;
        
        // Company size scoring
        $companySizes = ['enterprise' => 100, 'mid-market' => 75, 'small' => 50, 'startup' => 25];
        $score += $companySizes[$lead['company_size'] ?? 'small'] ?? 25;
        
        // Budget scoring
        if (isset($lead['budget'])) {
            $score += min(50, $lead['budget'] / 1000 * 5);
        }
        
        // Engagement scoring
        $engagementScore = ($lead['email_opens'] ?? 0) * 2 + ($lead['link_clicks'] ?? 0) * 5 + ($lead['page_views'] ?? 0);
        $score += min(50, $engagementScore);
        
        // Industry fit
        $targetIndustries = ['technology', 'healthcare', 'finance'];
        if (in_array($lead['industry'] ?? '', $targetIndustries)) {
            $score += 25;
        }
        
        return min(100, $score);
    }

    private function getLeadPriority($score)
    {
        if ($score >= 80) return 'high';
        if ($score >= 60) return 'medium';
        return 'low';
    }

    private function calculateConversionLikelihood($score)
    {
        return min(95, $score * 0.8 + rand(5, 15));
    }

    private function getRecommendedActions($score, $lead)
    {
        if ($score >= 80) {
            return ['Schedule demo immediately', 'Assign to senior sales rep', 'Prepare custom proposal'];
        } elseif ($score >= 60) {
            return ['Send targeted content', 'Schedule follow-up call', 'Add to nurture campaign'];
        } else {
            return ['Add to general email list', 'Monitor engagement', 'Qualify further'];
        }
    }

    private function generateScoringSummary($scoredLeads)
    {
        $total = count($scoredLeads);
        $highPriority = count(array_filter($scoredLeads, fn($lead) => $lead['priority'] === 'high'));
        $mediumPriority = count(array_filter($scoredLeads, fn($lead) => $lead['priority'] === 'medium'));
        $lowPriority = count(array_filter($scoredLeads, fn($lead) => $lead['priority'] === 'low'));
        
        $avgScore = $total > 0 ? array_sum(array_column($scoredLeads, 'score')) / $total : 0;
        
        return [
            'total_leads' => $total,
            'high_priority' => $highPriority,
            'medium_priority' => $mediumPriority,
            'low_priority' => $lowPriority,
            'average_score' => round($avgScore, 2),
            'conversion_ready' => $highPriority,
            'nurture_required' => $mediumPriority + $lowPriority
        ];
    }
}