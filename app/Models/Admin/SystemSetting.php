<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SystemSetting extends Model
{
    protected $fillable = [
        'group', 'key', 'value', 'type', 'description', 'is_encrypted', 'is_public'
    ];

    protected $casts = [
        'is_encrypted' => 'boolean',
        'is_public' => 'boolean'
    ];

    public function scopeByGroup($query, string $group)
    {
        return $query->where('group', $group);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopePrivate($query)
    {
        return $query->where('is_public', false);
    }

    public function getValue()
    {
        $value = $this->value;

        if ($this->is_encrypted && $value) {
            $value = decrypt($value);
        }

        return $this->castValue($value);
    }

    public function setValue($value)
    {
        if ($this->is_encrypted && $value) {
            $value = encrypt($value);
        }

        $this->value = $value;
        $this->save();

        // Clear cache
        Cache::forget("system_setting_{$this->key}");
    }

    private function castValue($value)
    {
        switch ($this->type) {
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            case 'integer':
                return (int) $value;
            case 'float':
                return (float) $value;
            case 'json':
                return json_decode($value, true);
            case 'array':
                return is_array($value) ? $value : json_decode($value, true);
            default:
                return $value;
        }
    }

    public static function getSetting(string $key, $default = null)
    {
        return Cache::remember("system_setting_{$key}", 3600, function () use ($key, $default) {
            $setting = self::where('key', $key)->first();
            return $setting ? $setting->getValue() : $default;
        });
    }

    public static function setSetting(string $key, $value, string $group = 'general', string $type = 'string', string $description = null)
    {
        $setting = self::updateOrCreate(
            ['key' => $key],
            [
                'group' => $group,
                'type' => $type,
                'description' => $description
            ]
        );

        $setting->setValue($value);
        return $setting;
    }

    public static function getSettingsByGroup(string $group): array
    {
        return self::where('group', $group)->get()->mapWithKeys(function ($setting) {
            return [$setting->key => $setting->getValue()];
        })->toArray();
    }
}