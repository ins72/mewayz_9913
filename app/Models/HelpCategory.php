<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HelpCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'order',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function articles()
    {
        return $this->hasMany(HelpArticle::class, 'category_id');
    }

    public function publishedArticles()
    {
        return $this->articles()->where('is_published', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}