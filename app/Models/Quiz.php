<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'lesson_id',
        'course_id',
        'title',
        'description',
        'questions',
        'max_score',
        'passing_score',
        'time_limit',
        'attempts_allowed',
        'is_required',
        'is_active'
    ];

    protected $casts = [
        'questions' => 'array',
        'max_score' => 'decimal:2',
        'passing_score' => 'decimal:2',
        'time_limit' => 'integer',
        'attempts_allowed' => 'integer',
        'is_required' => 'boolean',
        'is_active' => 'boolean'
    ];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(CoursesLesson::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function completions(): HasMany
    {
        return $this->hasMany(QuizCompletion::class);
    }
}