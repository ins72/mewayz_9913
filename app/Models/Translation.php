<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Translation extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;
    
    protected $fillable = [
        'language_id',
        'key',
        'value',
        'namespace',
        'metadata'
    ];
    
    protected $casts = [
        'metadata' => 'array'
    ];
    
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            $model->id = (string) Str::uuid();
        });
    }
    
    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
    
    public function scopeByNamespace($query, $namespace)
    {
        return $query->where('namespace', $namespace);
    }
    
    public function scopeByKey($query, $key)
    {
        return $query->where('key', $key);
    }
    
    public static function getTranslation(string $key, string $languageCode, string $namespace = 'default', $default = null): ?string
    {
        $translation = self::join('languages', 'translations.language_id', '=', 'languages.id')
            ->where('languages.code', $languageCode)
            ->where('translations.key', $key)
            ->where('translations.namespace', $namespace)
            ->first();
            
        return $translation ? $translation->value : $default;
    }
    
    public static function setTranslation(string $key, string $value, string $languageCode, string $namespace = 'default'): void
    {
        $language = Language::where('code', $languageCode)->first();
        if (!$language) {
            throw new \Exception("Language not found: {$languageCode}");
        }
        
        self::updateOrCreate(
            [
                'language_id' => $language->id,
                'key' => $key,
                'namespace' => $namespace
            ],
            ['value' => $value]
        );
    }
    
    public static function getTranslations(string $languageCode, string $namespace = 'default'): array
    {
        return self::join('languages', 'translations.language_id', '=', 'languages.id')
            ->where('languages.code', $languageCode)
            ->where('translations.namespace', $namespace)
            ->pluck('translations.value', 'translations.key')
            ->toArray();
    }
    
    public static function exportTranslations(string $languageCode, string $namespace = 'default'): array
    {
        $translations = self::join('languages', 'translations.language_id', '=', 'languages.id')
            ->where('languages.code', $languageCode)
            ->where('translations.namespace', $namespace)
            ->select('translations.key', 'translations.value', 'translations.metadata')
            ->get();
            
        return $translations->map(function ($translation) {
            return [
                'key' => $translation->key,
                'value' => $translation->value,
                'metadata' => $translation->metadata
            ];
        })->toArray();
    }
    
    public static function importTranslations(array $translations, string $languageCode, string $namespace = 'default'): void
    {
        $language = Language::where('code', $languageCode)->first();
        if (!$language) {
            throw new \Exception("Language not found: {$languageCode}");
        }
        
        foreach ($translations as $translation) {
            self::updateOrCreate(
                [
                    'language_id' => $language->id,
                    'key' => $translation['key'],
                    'namespace' => $namespace
                ],
                [
                    'value' => $translation['value'],
                    'metadata' => $translation['metadata'] ?? []
                ]
            );
        }
    }
}