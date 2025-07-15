<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workspace extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'is_primary',
        'settings'
    ];
    
    protected $casts = [
        'settings' => 'array',
        'is_primary' => 'boolean'
    ];
    
    /**
     * Get the user that owns the workspace
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
