<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseEnrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'payment_amount',
        'payment_method',
        'payment_status',
        'enrollment_date',
        'completion_date',
        'progress_percentage',
        'completion_status',
        'last_activity_at',
        'total_time_spent',
        'lessons_completed',
        'quiz_scores',
        'certificates_earned',
        'is_active'
    ];

    protected $casts = [
        'enrollment_date' => 'datetime',
        'completion_date' => 'datetime',
        'last_activity_at' => 'datetime',
        'quiz_scores' => 'array',
        'progress_percentage' => 'decimal:2',
        'payment_amount' => 'decimal:2',
        'total_time_spent' => 'integer',
        'lessons_completed' => 'integer',
        'certificates_earned' => 'integer',
        'is_active' => 'boolean'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function lessons()
    {
        return $this->hasMany(LessonCompletion::class, 'enrollment_id');
    }

    public function quizzes()
    {
        return $this->hasMany(QuizCompletion::class, 'enrollment_id');
    }

    public function certificates()
    {
        return $this->hasMany(CourseCertificate::class, 'enrollment_id');
    }
}