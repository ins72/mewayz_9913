<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    
    protected $casts = [
        'extra' => 'array'
    ];

    public function noteUrl(){
        return route('generatedNote', ['_slug' => $this->_slug]);
    }
}
