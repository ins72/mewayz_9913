<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;

class AdminSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'setting_key',
        'setting_value',
        'setting_type',
        'description',
        'is_public'
    ];

    protected $casts = [
        'is_public' => 'boolean'
    ];

    public function getValueAttribute()
    {
        switch ($this->setting_type) {
            case 'boolean':
                return (bool) $this->setting_value;
            case 'number':
                return (float) $this->setting_value;
            case 'json':
                return json_decode($this->setting_value, true);
            default:
                return $this->setting_value;
        }
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopePrivate($query)
    {
        return $query->where('is_public', false);
    }

    public static function get($key, $default = null)
    {
        $cacheKey = "admin_setting_{$key}";
        
        return Cache::remember($cacheKey, 3600, function () use ($key, $default) {
            $setting = self::where('setting_key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    public static function set($key, $value, $type = null, $description = null)
    {
        if ($type === null) {
            $type = self::detectType($value);
        }

        $setting = self::updateOrCreate(
            ['setting_key' => $key],
            [
                'setting_value' => is_array($value) ? json_encode($value) : $value,
                'setting_type' => $type,
                'description' => $description
            ]
        );

        // Clear cache
        Cache::forget("admin_setting_{$key}");

        return $setting;
    }

    public static function detectType($value)
    {
        if (is_bool($value)) {
            return 'boolean';
        } elseif (is_numeric($value)) {
            return 'number';
        } elseif (is_array($value)) {
            return 'json';
        } else {
            return 'string';
        }
    }

    public static function getPublicSettings()
    {
        return Cache::remember('public_admin_settings', 3600, function () {
            return self::public()->get()->keyBy('setting_key');
        });
    }

    public static function clearCache()
    {
        $settings = self::all();
        foreach ($settings as $setting) {
            Cache::forget("admin_setting_{$setting->setting_key}");
        }
        Cache::forget('public_admin_settings');
    }
}