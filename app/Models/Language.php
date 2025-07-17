<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Language extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;
    
    protected $fillable = [
        'code',
        'name',
        'native_name',
        'flag_icon',
        'is_rtl',
        'is_active',
        'sort_order'
    ];
    
    protected $casts = [
        'is_rtl' => 'boolean',
        'is_active' => 'boolean'
    ];
    
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            $model->id = (string) Str::uuid();
        });
    }
    
    public function translations(): HasMany
    {
        return $this->hasMany(Translation::class);
    }
    
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
    
    public function scopeRtl($query)
    {
        return $query->where('is_rtl', true);
    }
    
    public function scopeLtr($query)
    {
        return $query->where('is_rtl', false);
    }
    
    public static function getDefault(): ?self
    {
        return self::where('code', 'en')->first();
    }
    
    public static function getAvailableLanguages(): array
    {
        return self::active()->ordered()->get()->map(function ($language) {
            return [
                'code' => $language->code,
                'name' => $language->name,
                'native_name' => $language->native_name,
                'flag_icon' => $language->flag_icon,
                'is_rtl' => $language->is_rtl
            ];
        })->toArray();
    }
    
    public function getTranslation(string $key, string $namespace = 'default'): ?string
    {
        $translation = $this->translations()
            ->where('key', $key)
            ->where('namespace', $namespace)
            ->first();
            
        return $translation ? $translation->value : null;
    }
    
    public function setTranslation(string $key, string $value, string $namespace = 'default'): void
    {
        $this->translations()->updateOrCreate(
            ['key' => $key, 'namespace' => $namespace],
            ['value' => $value]
        );
    }
    
    public function getTranslations(string $namespace = 'default'): array
    {
        return $this->translations()
            ->where('namespace', $namespace)
            ->pluck('value', 'key')
            ->toArray();
    }
    
    public function getCompletionPercentage(): float
    {
        $englishTranslations = Language::where('code', 'en')->first();
        if (!$englishTranslations) return 0;
        
        $totalKeys = $englishTranslations->translations()->count();
        if ($totalKeys === 0) return 0;
        
        $translatedKeys = $this->translations()->count();
        
        return round(($translatedKeys / $totalKeys) * 100, 2);
    }
    
    public function getMissingTranslations(): array
    {
        $englishTranslations = Language::where('code', 'en')->first();
        if (!$englishTranslations) return [];
        
        $englishKeys = $englishTranslations->translations()->pluck('key')->toArray();
        $translatedKeys = $this->translations()->pluck('key')->toArray();
        
        return array_diff($englishKeys, $translatedKeys);
    }
    
    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }
    
    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }
}