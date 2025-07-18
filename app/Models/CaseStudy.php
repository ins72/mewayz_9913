<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CaseStudy extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'client_name',
        'client_logo',
        'industry',
        'challenges',
        'solutions',
        'results',
        'metrics',
        'featured_image',
        'gallery',
        'is_published',
        'published_at'
    ];

    protected $casts = [
        'challenges' => 'array',
        'solutions' => 'array',
        'results' => 'array',
        'metrics' => 'array',
        'gallery' => 'array',
        'is_published' => 'boolean',
        'published_at' => 'datetime'
    ];

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeByIndustry($query, $industry)
    {
        return $query->where('industry', $industry);
    }
}