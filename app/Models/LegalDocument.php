<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LegalDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'title',
        'content',
        'version',
        'is_active',
        'effective_date',
        'created_by',
        'approved_by',
        'approval_date',
        'metadata'
    ];

    protected $casts = [
        'effective_date' => 'datetime',
        'approval_date' => 'datetime',
        'is_active' => 'boolean',
        'metadata' => 'array'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }
}