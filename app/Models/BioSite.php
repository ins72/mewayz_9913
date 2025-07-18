<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BioSite extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'title',
        'slug',
        'description',
        'address',
        'bio',
        'background',
        'settings',
        'colors',
        'theme_config',
        'theme',
        'logo',
        '_slug',
        'membership',
        'qr',
        'seo_image',
        'qr_bg',
        '_domain',
        'qr_logo',
        'pwa',
        'contact',
        'seo',
        'is_template',
        'is_active',
        'social',
        'banner',
        'interest',
        'connect_u',
        'banned',
        'status',
        'meta_title',
        'meta_description',
        'avatar',
        'cover_image',
        'custom_css',
        'is_public',
        'views_count',
        'links_count'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_public' => 'boolean',
        'views_count' => 'integer',
        'links_count' => 'integer',
        'settings' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function links()
    {
        return $this->hasMany(BioSiteLink::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }
}