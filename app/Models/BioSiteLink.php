<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BioSiteLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'bio_site_id',
        'title',
        'url',
        'description',
        'icon',
        'clicks_count',
        'is_active',
        'order'
    ];

    protected $casts = [
        'clicks_count' => 'integer',
        'is_active' => 'boolean',
        'order' => 'integer'
    ];

    public function bioSite()
    {
        return $this->belongsTo(BioSite::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}