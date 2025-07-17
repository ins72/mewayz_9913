<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class EscrowDocument extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'escrow_transaction_id',
        'uploaded_by',
        'file_name',
        'file_path',
        'file_size',
        'file_type',
        'document_type',
        'description',
        'is_public',
    ];

    protected $casts = [
        'id' => 'string',
        'file_size' => 'integer',
        'is_public' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    // Relationships
    public function escrowTransaction(): BelongsTo
    {
        return $this->belongsTo(EscrowTransaction::class);
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // Accessors
    public function getFileSizeFormattedAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getIsImageAttribute(): bool
    {
        return in_array($this->file_type, ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
    }

    public function getIsPdfAttribute(): bool
    {
        return $this->file_type === 'application/pdf';
    }

    // Business Logic
    public function canBeViewedBy(User $user): bool
    {
        if ($this->is_public) {
            return true;
        }

        $transaction = $this->escrowTransaction;
        return $transaction->buyer_id === $user->id || 
               $transaction->seller_id === $user->id ||
               $this->uploaded_by === $user->id;
    }

    public function canBeDeletedBy(User $user): bool
    {
        return $this->uploaded_by === $user->id;
    }
}