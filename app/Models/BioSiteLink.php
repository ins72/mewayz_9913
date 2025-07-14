<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BioSiteLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'bio_site_id',
        'title',
        'url',
        'description',
        'type',
        'icon',
        'sort_order',
        'is_active',
        'click_count',
        'metadata',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
        'click_count' => 'integer',
        'metadata' => 'array',
    ];

    /**
     * Get the bio site that owns the link
     */
    public function bioSite(): BelongsTo
    {
        return $this->belongsTo(BioSite::class);
    }

    /**
     * Scope for active links
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordering links
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}