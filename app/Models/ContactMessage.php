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
        'company',
        'phone',
        'status',
        'ip_address',
        'user_agent'
    ];

    public function scopeNew($query)
    {
        return $query->where('status', 'new');
    }

    public function scopeProcessed($query)
    {
        return $query->where('status', 'processed');
    }

    public function markAsProcessed()
    {
        $this->update(['status' => 'processed']);
    }
}