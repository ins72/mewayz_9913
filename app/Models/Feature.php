<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    use HasFactory;

    protected $table = 'features_showcase';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'category',
        'benefits',
        'screenshots',
        'is_active',
        'order'
    ];

    protected $casts = [
        'benefits' => 'array',
        'screenshots' => 'array',
        'is_active' => 'boolean',
        'order' => 'integer'
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}