<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class AdminApiKey extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_name',
        'api_key_name',
        'api_key_value',
        'is_active',
        'last_used',
        'description'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_used' => 'datetime'
    ];

    protected $hidden = [
        'api_key_value'
    ];

    public function getDecryptedApiKeyAttribute()
    {
        return $this->api_key_value ? decrypt($this->api_key_value) : null;
    }

    public function markAsUsed()
    {
        $this->update(['last_used' => now()]);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForService($query, $serviceName)
    {
        return $query->where('service_name', $serviceName);
    }

    public static function getApiKey($serviceName, $keyName = 'default')
    {
        $key = self::where('service_name', $serviceName)
                   ->where('api_key_name', $keyName)
                   ->where('is_active', true)
                   ->first();

        if ($key) {
            $key->markAsUsed();
            return $key->getDecryptedApiKeyAttribute();
        }

        return null;
    }
}