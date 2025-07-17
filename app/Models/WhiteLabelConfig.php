<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class WhiteLabelConfig extends Model
{
    protected $table = 'white_label_configs';
    
    protected $keyType = 'string';
    public $incrementing = false;
    
    protected $fillable = [
        'workspace_id',
        'company_name',
        'logo_url',
        'favicon_url',
        'primary_color',
        'secondary_color',
        'accent_color',
        'custom_domain',
        'email_templates',
        'custom_css',
        'custom_js',
        'hide_platform_branding',
        'custom_login_page',
        'login_page_config'
    ];
    
    protected $casts = [
        'email_templates' => 'array',
        'custom_css' => 'array',
        'custom_js' => 'array',
        'hide_platform_branding' => 'boolean',
        'custom_login_page' => 'boolean',
        'login_page_config' => 'array'
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
    
    public function getColorsArray(): array
    {
        return [
            'primary' => $this->primary_color,
            'secondary' => $this->secondary_color,
            'accent' => $this->accent_color
        ];
    }
    
    public function generateCSS(): string
    {
        $css = ":root {\n";
        $css .= "  --primary-color: {$this->primary_color};\n";
        $css .= "  --secondary-color: {$this->secondary_color};\n";
        $css .= "  --accent-color: {$this->accent_color};\n";
        $css .= "}\n";
        
        if (!empty($this->custom_css)) {
            foreach ($this->custom_css as $selector => $rules) {
                $css .= "{$selector} {\n";
                foreach ($rules as $property => $value) {
                    $css .= "  {$property}: {$value};\n";
                }
                $css .= "}\n";
            }
        }
        
        return $css;
    }
    
    public function generateJS(): string
    {
        $js = '';
        
        if (!empty($this->custom_js)) {
            foreach ($this->custom_js as $script) {
                $js .= $script . "\n";
            }
        }
        
        return $js;
    }
    
    public function getEmailTemplate(string $type): ?array
    {
        return $this->email_templates[$type] ?? null;
    }
    
    public function setEmailTemplate(string $type, array $template): void
    {
        $templates = $this->email_templates ?? [];
        $templates[$type] = $template;
        $this->update(['email_templates' => $templates]);
    }
    
    public function getLoginPageConfig(string $key = null, $default = null)
    {
        if ($key === null) {
            return $this->login_page_config ?? [];
        }
        
        return data_get($this->login_page_config, $key, $default);
    }
    
    public function setLoginPageConfig(string $key, $value): void
    {
        $config = $this->login_page_config ?? [];
        data_set($config, $key, $value);
        $this->update(['login_page_config' => $config]);
    }
    
    public function isFullyCustomized(): bool
    {
        return $this->hide_platform_branding && 
               !empty($this->logo_url) && 
               !empty($this->company_name) &&
               !empty($this->custom_domain);
    }
    
    public function generateManifest(): array
    {
        return [
            'name' => $this->company_name,
            'short_name' => $this->company_name,
            'theme_color' => $this->primary_color,
            'background_color' => $this->secondary_color,
            'start_url' => '/',
            'display' => 'standalone',
            'icons' => [
                [
                    'src' => $this->logo_url ?? '/default-logo.png',
                    'sizes' => '192x192',
                    'type' => 'image/png'
                ]
            ]
        ];
    }
}