<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

    protected $table = 'jobs_listings';

    protected $fillable = [
        'title',
        'slug',
        'description',
        'department',
        'location',
        'type',
        'remote_type',
        'salary_min',
        'salary_max',
        'salary_currency',
        'requirements',
        'benefits',
        'is_active',
        'posted_at',
        'expires_at'
    ];

    protected $casts = [
        'salary_min' => 'decimal:2',
        'salary_max' => 'decimal:2',
        'requirements' => 'array',
        'benefits' => 'array',
        'is_active' => 'boolean',
        'posted_at' => 'datetime',
        'expires_at' => 'datetime'
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByDepartment($query, $department)
    {
        return $query->where('department', $department);
    }

    public function scopeNotExpired($query)
    {
        return $query->where(function ($query) {
            $query->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
        });
    }
}