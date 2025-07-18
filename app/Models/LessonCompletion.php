<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonCompletion extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'lesson_id',
        'enrollment_id',
        'completed_at',
        'completion_percentage',
        'time_spent',
        'attempts',
        'is_completed',
        'notes'
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'completion_percentage' => 'decimal:2',
        'time_spent' => 'integer',
        'attempts' => 'integer',
        'is_completed' => 'boolean'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(CoursesLesson::class);
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(CourseEnrollment::class);
    }
}