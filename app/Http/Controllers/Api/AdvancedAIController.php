<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AIModel;
use App\Models\AiGeneratedContent;
use App\Models\AutomationWorkflow;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AdvancedAIController extends Controller
{
    /**
     * Get available AI models
     */
    public function getModels(Request $request): JsonResponse
    {
        $type = $request->get('type');
        $models = AIModel::getAvailableModels($type);
        
        return response()->json([
            'success' => true,
            'data' => $models
        ]);
    }
    
    /**
     * Generate advanced content with AI
     */
    public function generateAdvancedContent(Request $request): JsonResponse
    {
        $request->validate([
            'type' => 'required|in:text,image,video,audio,code',
            'prompt' => 'required|string',
            'model_id' => 'required|exists:ai_models,id',
            'parameters' => 'nullable|array',
            'context' => 'nullable|array'
        ]);
        
        $workspace = $request->user()->workspaces()->first();
        $model = AIModel::findOrFail($request->model_id);
        
        try {
            $content = $this->generateContent($model, $request->prompt, $request->parameters ?? []);
            
            // Save generated content
            $generatedContent = AiGeneratedContent::create([
                'user_id' => $request->user()->id,
                'workspace_id' => $workspace->id,
                'model_id' => $model->id,
                'type' => $request->type,
                'prompt' => $request->prompt,
                'generated_content' => $content,
                'parameters' => $request->parameters,
                'context' => $request->context,
                'cost' => $model->cost_per_request
            ]);
            
            // Update model usage
            $model->incrementUsage();
            
            // Log the action
            AuditLog::logAction([
                'workspace_id' => $workspace->id,
                'action' => 'generate',
                'resource_type' => 'ai_content',
                'resource_id' => $generatedContent->id,
                'metadata' => [
                    'type' => $request->type,
                    'model' => $model->name,
                    'cost' => $model->cost_per_request
                ]
            ]);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $generatedContent->id,
                    'content' => $content,
                    'model' => $model->name,
                    'type' => $request->type,
                    'cost' => $model->cost_per_request
                ],
                'message' => 'Content generated successfully'
            ]);
            
        } catch (\Exception $e) {
            Log::error('AI Content Generation Error', [
                'error' => $e->getMessage(),
                'model' => $model->name,
                'prompt' => $request->prompt
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate content: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Analyze sentiment of content
     */
    public function analyzeSentiment(Request $request): JsonResponse
    {
        $request->validate([
            'text' => 'required|string|max:10000'
        ]);
        
        $workspace = $request->user()->workspaces()->first();
        $model = AIModel::getModelByProvider('openai', 'text');
        
        if (!$model) {
            return response()->json([
                'success' => false,
                'message' => 'No AI model available for sentiment analysis'
            ], 400);
        }
        
        try {
            $prompt = "Analyze the sentiment of the following text and provide a detailed analysis including:\n"
                    . "1. Overall sentiment (positive, negative, neutral)\n"
                    . "2. Confidence score (0-100)\n"
                    . "3. Key emotional indicators\n"
                    . "4. Tone analysis\n"
                    . "5. Suggestions for improvement\n\n"
                    . "Text: " . $request->text;
            
            $analysis = $this->generateContent($model, $prompt);
            
            // Log the action
            AuditLog::logAction([
                'workspace_id' => $workspace->id,
                'action' => 'analyze',
                'resource_type' => 'sentiment_analysis',
                'metadata' => [
                    'text_length' => strlen($request->text),
                    'model' => $model->name
                ]
            ]);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'analysis' => $analysis,
                    'text_length' => strlen($request->text),
                    'model' => $model->name
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to analyze sentiment: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Generate business insights
     */
    public function generateBusinessInsights(Request $request): JsonResponse
    {
        $request->validate([
            'data' => 'required|array',
            'insight_type' => 'required|in:performance,trends,opportunities,risks,recommendations'
        ]);
        
        $workspace = $request->user()->workspaces()->first();
        $model = AIModel::getModelByProvider('openai', 'text');
        
        if (!$model) {
            return response()->json([
                'success' => false,
                'message' => 'No AI model available for business insights'
            ], 400);
        }
        
        try {
            $dataString = json_encode($request->data, JSON_PRETTY_PRINT);
            
            $prompt = "Analyze the following business data and provide {$request->insight_type} insights:\n\n"
                    . "Data:\n{$dataString}\n\n"
                    . "Please provide:\n"
                    . "1. Key findings\n"
                    . "2. Trends and patterns\n"
                    . "3. Actionable recommendations\n"
                    . "4. Risk assessment\n"
                    . "5. Opportunities for growth\n";
            
            $insights = $this->generateContent($model, $prompt);
            
            // Log the action
            AuditLog::logAction([
                'workspace_id' => $workspace->id,
                'action' => 'generate',
                'resource_type' => 'business_insights',
                'metadata' => [
                    'insight_type' => $request->insight_type,
                    'data_points' => count($request->data),
                    'model' => $model->name
                ]
            ]);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'insights' => $insights,
                    'insight_type' => $request->insight_type,
                    'data_points_analyzed' => count($request->data),
                    'model' => $model->name
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate business insights: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Predict trends using AI
     */
    public function predictTrends(Request $request): JsonResponse
    {
        $request->validate([
            'historical_data' => 'required|array',
            'prediction_period' => 'required|integer|min:1|max:12',
            'data_type' => 'required|in:sales,traffic,engagement,revenue,growth'
        ]);
        
        $workspace = $request->user()->workspaces()->first();
        $model = AIModel::getModelByProvider('openai', 'text');
        
        if (!$model) {
            return response()->json([
                'success' => false,
                'message' => 'No AI model available for trend prediction'
            ], 400);
        }
        
        try {
            $dataString = json_encode($request->historical_data, JSON_PRETTY_PRINT);
            
            $prompt = "Analyze the following historical {$request->data_type} data and predict trends for the next {$request->prediction_period} months:\n\n"
                    . "Historical Data:\n{$dataString}\n\n"
                    . "Please provide:\n"
                    . "1. Trend analysis of historical data\n"
                    . "2. Predictions for the next {$request->prediction_period} months\n"
                    . "3. Confidence levels for predictions\n"
                    . "4. Key factors influencing trends\n"
                    . "5. Recommended actions based on predictions\n";
            
            $predictions = $this->generateContent($model, $prompt);
            
            // Log the action
            AuditLog::logAction([
                'workspace_id' => $workspace->id,
                'action' => 'predict',
                'resource_type' => 'trend_prediction',
                'metadata' => [
                    'data_type' => $request->data_type,
                    'prediction_period' => $request->prediction_period,
                    'data_points' => count($request->historical_data),
                    'model' => $model->name
                ]
            ]);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'predictions' => $predictions,
                    'data_type' => $request->data_type,
                    'prediction_period' => $request->prediction_period,
                    'data_points_analyzed' => count($request->historical_data),
                    'model' => $model->name
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to predict trends: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Generate chatbot response
     */
    public function generateChatbotResponse(Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'required|string',
            'conversation_history' => 'nullable|array',
            'context' => 'nullable|array',
            'personality' => 'nullable|string'
        ]);
        
        $workspace = $request->user()->workspaces()->first();
        $model = AIModel::getModelByProvider('openai', 'text');
        
        if (!$model) {
            return response()->json([
                'success' => false,
                'message' => 'No AI model available for chatbot'
            ], 400);
        }
        
        try {
            $conversationHistory = $request->conversation_history ?? [];
            $context = $request->context ?? [];
            $personality = $request->personality ?? 'professional and helpful';
            
            $prompt = "You are a {$personality} chatbot assistant. ";
            
            if (!empty($context)) {
                $prompt .= "Context: " . json_encode($context) . "\n\n";
            }
            
            if (!empty($conversationHistory)) {
                $prompt .= "Conversation History:\n";
                foreach ($conversationHistory as $message) {
                    $prompt .= "{$message['role']}: {$message['content']}\n";
                }
                $prompt .= "\n";
            }
            
            $prompt .= "User: {$request->message}\nAssistant:";
            
            $response = $this->generateContent($model, $prompt);
            
            // Log the action
            AuditLog::logAction([
                'workspace_id' => $workspace->id,
                'action' => 'generate',
                'resource_type' => 'chatbot_response',
                'metadata' => [
                    'message_length' => strlen($request->message),
                    'conversation_length' => count($conversationHistory),
                    'model' => $model->name
                ]
            ]);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'response' => $response,
                    'model' => $model->name,
                    'personality' => $personality
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate chatbot response: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get AI usage statistics
     */
    public function getUsageStatistics(Request $request): JsonResponse
    {
        $workspace = $request->user()->workspaces()->first();
        
        $stats = AIModel::getUsageStats();
        
        $workspaceStats = AiGeneratedContent::where('workspace_id', $workspace->id)
            ->selectRaw('COUNT(*) as total_requests, SUM(cost) as total_cost')
            ->first();
        
        $recentContent = AiGeneratedContent::where('workspace_id', $workspace->id)
            ->with('model')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => [
                'workspace_stats' => [
                    'total_requests' => $workspaceStats->total_requests ?? 0,
                    'total_cost' => $workspaceStats->total_cost ?? 0
                ],
                'platform_stats' => $stats,
                'recent_content' => $recentContent
            ]
        ]);
    }
    
    /**
     * Generate content using AI model
     */
    private function generateContent(AIModel $model, string $prompt, array $parameters = []): string
    {
        switch ($model->provider) {
            case 'openai':
                return $this->generateOpenAIContent($model, $prompt, $parameters);
            case 'anthropic':
                return $this->generateAnthropicContent($model, $prompt, $parameters);
            case 'google':
                return $this->generateGoogleContent($model, $prompt, $parameters);
            case 'stability':
                return $this->generateStabilityContent($model, $prompt, $parameters);
            default:
                throw new \Exception('Unsupported AI provider: ' . $model->provider);
        }
    }
    
    /**
     * Generate content using OpenAI
     */
    private function generateOpenAIContent(AIModel $model, string $prompt, array $parameters = []): string
    {
        $apiKey = config('app.openai_key');
        if (!$apiKey) {
            throw new \Exception('OpenAI API key not configured');
        }
        
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json'
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => $model->model_id,
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ],
            'max_tokens' => $parameters['max_tokens'] ?? $model->getConfigValue('max_tokens', 1000),
            'temperature' => $parameters['temperature'] ?? $model->getConfigValue('temperature', 0.7),
            'top_p' => $parameters['top_p'] ?? $model->getConfigValue('top_p', 1.0)
        ]);
        
        if (!$response->successful()) {
            throw new \Exception('OpenAI API request failed: ' . $response->body());
        }
        
        $data = $response->json();
        return $data['choices'][0]['message']['content'] ?? '';
    }
    
    /**
     * Generate content using Anthropic
     */
    private function generateAnthropicContent(AIModel $model, string $prompt, array $parameters = []): string
    {
        // Placeholder for Anthropic API integration
        throw new \Exception('Anthropic integration not implemented yet');
    }
    
    /**
     * Generate content using Google
     */
    private function generateGoogleContent(AIModel $model, string $prompt, array $parameters = []): string
    {
        // Placeholder for Google AI API integration
        throw new \Exception('Google AI integration not implemented yet');
    }
    
    /**
     * Generate content using Stability AI
     */
    private function generateStabilityContent(AIModel $model, string $prompt, array $parameters = []): string
    {
        // Placeholder for Stability AI API integration
        throw new \Exception('Stability AI integration not implemented yet');
    }
}