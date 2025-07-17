<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AIModel extends Model
{
    protected $table = 'ai_models';
    
    protected $keyType = 'string';
    public $incrementing = false;
    
    protected $fillable = [
        'name',
        'type',
        'provider',
        'model_id',
        'config',
        'capabilities',
        'usage_count',
        'cost_per_request',
        'is_active'
    ];
    
    protected $casts = [
        'config' => 'array',
        'capabilities' => 'array',
        'cost_per_request' => 'decimal:6',
        'is_active' => 'boolean'
    ];
    
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            $model->id = (string) Str::uuid();
        });
    }
    
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }
    
    public function scopeByProvider($query, $provider)
    {
        return $query->where('provider', $provider);
    }
    
    public function scopeByCapability($query, $capability)
    {
        return $query->whereJsonContains('capabilities', $capability);
    }
    
    public static function getAvailableModels(string $type = null): array
    {
        $query = self::active();
        
        if ($type) {
            $query->byType($type);
        }
        
        return $query->get()->map(function ($model) {
            return [
                'id' => $model->id,
                'name' => $model->name,
                'type' => $model->type,
                'provider' => $model->provider,
                'capabilities' => $model->capabilities,
                'cost_per_request' => $model->cost_per_request
            ];
        })->toArray();
    }
    
    public function hasCapability(string $capability): bool
    {
        return in_array($capability, $this->capabilities);
    }
    
    public function getConfigValue(string $key, $default = null)
    {
        return data_get($this->config, $key, $default);
    }
    
    public function setConfigValue(string $key, $value): void
    {
        $config = $this->config;
        data_set($config, $key, $value);
        $this->update(['config' => $config]);
    }
    
    public function incrementUsage(): void
    {
        $this->increment('usage_count');
    }
    
    public function getTotalCost(): float
    {
        return $this->usage_count * $this->cost_per_request;
    }
    
    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }
    
    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }
    
    public static function seedDefaultModels(): void
    {
        $models = [
            // OpenAI Models
            [
                'name' => 'GPT-4',
                'type' => 'text',
                'provider' => 'openai',
                'model_id' => 'gpt-4',
                'config' => [
                    'max_tokens' => 4096,
                    'temperature' => 0.7,
                    'top_p' => 1.0
                ],
                'capabilities' => ['text_generation', 'conversation', 'code_generation', 'analysis'],
                'cost_per_request' => 0.03
            ],
            [
                'name' => 'GPT-3.5 Turbo',
                'type' => 'text',
                'provider' => 'openai',
                'model_id' => 'gpt-3.5-turbo',
                'config' => [
                    'max_tokens' => 4096,
                    'temperature' => 0.7,
                    'top_p' => 1.0
                ],
                'capabilities' => ['text_generation', 'conversation', 'code_generation'],
                'cost_per_request' => 0.002
            ],
            [
                'name' => 'DALL-E 3',
                'type' => 'image',
                'provider' => 'openai',
                'model_id' => 'dall-e-3',
                'config' => [
                    'size' => '1024x1024',
                    'quality' => 'standard',
                    'style' => 'vivid'
                ],
                'capabilities' => ['image_generation'],
                'cost_per_request' => 0.04
            ],
            
            // Anthropic Models
            [
                'name' => 'Claude 3 Opus',
                'type' => 'text',
                'provider' => 'anthropic',
                'model_id' => 'claude-3-opus-20240229',
                'config' => [
                    'max_tokens' => 4096,
                    'temperature' => 0.7
                ],
                'capabilities' => ['text_generation', 'conversation', 'analysis', 'reasoning'],
                'cost_per_request' => 0.015
            ],
            [
                'name' => 'Claude 3 Sonnet',
                'type' => 'text',
                'provider' => 'anthropic',
                'model_id' => 'claude-3-sonnet-20240229',
                'config' => [
                    'max_tokens' => 4096,
                    'temperature' => 0.7
                ],
                'capabilities' => ['text_generation', 'conversation', 'analysis'],
                'cost_per_request' => 0.003
            ],
            
            // Google Models
            [
                'name' => 'Gemini Pro',
                'type' => 'text',
                'provider' => 'google',
                'model_id' => 'gemini-pro',
                'config' => [
                    'max_tokens' => 2048,
                    'temperature' => 0.7
                ],
                'capabilities' => ['text_generation', 'conversation', 'multimodal'],
                'cost_per_request' => 0.0005
            ],
            
            // Stability AI Models
            [
                'name' => 'Stable Diffusion XL',
                'type' => 'image',
                'provider' => 'stability',
                'model_id' => 'stable-diffusion-xl-1024-v1-0',
                'config' => [
                    'width' => 1024,
                    'height' => 1024,
                    'steps' => 30,
                    'cfg_scale' => 7
                ],
                'capabilities' => ['image_generation', 'image_editing'],
                'cost_per_request' => 0.02
            ]
        ];
        
        foreach ($models as $model) {
            self::updateOrCreate(
                [
                    'provider' => $model['provider'],
                    'model_id' => $model['model_id']
                ],
                $model
            );
        }
    }
    
    public static function getModelByProvider(string $provider, string $type = null): ?self
    {
        $query = self::active()->byProvider($provider);
        
        if ($type) {
            $query->byType($type);
        }
        
        return $query->first();
    }
    
    public static function getBestModelForCapability(string $capability): ?self
    {
        return self::active()
            ->byCapability($capability)
            ->orderBy('cost_per_request')
            ->first();
    }
    
    public static function getUsageStats(): array
    {
        return [
            'total_requests' => self::sum('usage_count'),
            'total_cost' => self::selectRaw('SUM(usage_count * cost_per_request) as total_cost')->first()->total_cost,
            'by_provider' => self::selectRaw('provider, SUM(usage_count) as requests, SUM(usage_count * cost_per_request) as cost')
                ->groupBy('provider')
                ->get()
                ->toArray(),
            'by_type' => self::selectRaw('type, SUM(usage_count) as requests, SUM(usage_count * cost_per_request) as cost')
                ->groupBy('type')
                ->get()
                ->toArray()
        ];
    }
}