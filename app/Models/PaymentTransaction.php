<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'session_id',
        'payment_id',
        'user_id',
        'email',
        'amount',
        'currency',
        'metadata',
        'payment_status',
        'stripe_price_id',
        'quantity'
    ];
    
    protected $casts = [
        'metadata' => 'array',
        'amount' => 'decimal:2'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
