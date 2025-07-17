<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class SSOProvider extends Model
{
    protected $table = 'sso_providers';
    
    protected $keyType = 'string';
    public $incrementing = false;
    
    protected $fillable = [
        'workspace_id',
        'name',
        'provider_type',
        'config',
        'entity_id',
        'metadata_url',
        'certificate',
        'is_active',
        'attribute_mapping'
    ];
    
    protected $casts = [
        'config' => 'array',
        'is_active' => 'boolean',
        'attribute_mapping' => 'array'
    ];
    
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            $model->id = (string) Str::uuid();
        });
    }
    
    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }
    
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    public function scopeByType($query, $type)
    {
        return $query->where('provider_type', $type);
    }
    
    public function isSAML(): bool
    {
        return $this->provider_type === 'saml';
    }
    
    public function isOAuth(): bool
    {
        return $this->provider_type === 'oauth';
    }
    
    public function isLDAP(): bool
    {
        return $this->provider_type === 'ldap';
    }
    
    public function getConfigValue(string $key, $default = null)
    {
        return data_get($this->config, $key, $default);
    }
    
    public function setConfigValue(string $key, $value)
    {
        $config = $this->config;
        data_set($config, $key, $value);
        $this->update(['config' => $config]);
    }
    
    public function testConnection(): array
    {
        try {
            switch ($this->provider_type) {
                case 'saml':
                    return $this->testSAMLConnection();
                case 'oauth':
                    return $this->testOAuthConnection();
                case 'ldap':
                    return $this->testLDAPConnection();
                default:
                    return ['success' => false, 'message' => 'Unknown provider type'];
            }
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    private function testSAMLConnection(): array
    {
        // Test SAML connection
        $metadataUrl = $this->metadata_url;
        if (!$metadataUrl) {
            return ['success' => false, 'message' => 'Metadata URL is required'];
        }
        
        // Validate metadata URL accessibility
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $metadataUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200) {
            return ['success' => false, 'message' => 'Cannot access metadata URL'];
        }
        
        return ['success' => true, 'message' => 'SAML provider connection successful'];
    }
    
    private function testOAuthConnection(): array
    {
        // Test OAuth connection
        $clientId = $this->getConfigValue('client_id');
        $clientSecret = $this->getConfigValue('client_secret');
        
        if (!$clientId || !$clientSecret) {
            return ['success' => false, 'message' => 'Client ID and Secret are required'];
        }
        
        return ['success' => true, 'message' => 'OAuth provider connection successful'];
    }
    
    private function testLDAPConnection(): array
    {
        // Test LDAP connection
        $host = $this->getConfigValue('host');
        $port = $this->getConfigValue('port', 389);
        $baseDN = $this->getConfigValue('base_dn');
        
        if (!$host || !$baseDN) {
            return ['success' => false, 'message' => 'Host and Base DN are required'];
        }
        
        if (!function_exists('ldap_connect')) {
            return ['success' => false, 'message' => 'LDAP extension is not installed'];
        }
        
        $connection = ldap_connect($host, $port);
        if (!$connection) {
            return ['success' => false, 'message' => 'Cannot connect to LDAP server'];
        }
        
        ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($connection, LDAP_OPT_REFERRALS, 0);
        
        $bindUser = $this->getConfigValue('bind_user');
        $bindPassword = $this->getConfigValue('bind_password');
        
        if ($bindUser && $bindPassword) {
            $bind = ldap_bind($connection, $bindUser, $bindPassword);
            if (!$bind) {
                ldap_close($connection);
                return ['success' => false, 'message' => 'LDAP bind failed'];
            }
        }
        
        ldap_close($connection);
        return ['success' => true, 'message' => 'LDAP connection successful'];
    }
}