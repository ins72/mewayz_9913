<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'subject',
        'message',
        'phone',
        'company',
        'inquiry_type',
        'ip_address',
        'user_agent',
        'status'
    ];

    public function scopeNew($query)
    {
        return $query->where('status', 'new');
    }

    public function scopeByInquiryType($query, $type)
    {
        return $query->where('inquiry_type', $type);
    }
}