<?php

namespace App\Models;

use App\Models\Base\Course as BaseCourse;

class Course extends BaseCourse
{
	protected $fillable = [
		'user_id',
		'name',
		'status',
		'price_type',
		'price',
		'price_pwyw',
		'compare_price',
		'course_level',
		'settings',
		'course_includes',
		'course_duration',
		'course_expiry_type',
		'course_expiry',
		'tags',
		'banner',
		'description',
		'position'
	];


	protected $casts = [
		'status' => 'integer',
		'price_type' => 'integer',
		'course_expiry_type' => 'integer',
		'position' => 'integer',
		'settings' => 'array',
		'course_includes' => 'array'
	];

	public function user(){
		return $this->belongsTo(User::class, 'user_id', 'id');
	}
	
	public function lessons(){
		return $this->hasMany(CoursesLesson::class, 'course_id', 'id');
	}

	public function enrollments(){
		return $this->hasMany(CourseEnrollment::class, 'course_id', 'id');
	}

	public function students(){
		return $this->belongsToMany(User::class, 'course_enrollments', 'course_id', 'user_id');
	}

	public function reviews(){
		return $this->hasMany(CourseReview::class, 'course_id', 'id');
	}

	public function quizzes(){
		return $this->hasMany(Quiz::class, 'course_id', 'id');
	}

	public function certificates(){
		return $this->hasMany(CourseCertificate::class, 'course_id', 'id');
	}

	public function communityGroups(){
		return $this->hasMany(CommunityGroup::class, 'course_id', 'id');
	}

    public function has_enroll($payee){
        // // Check IF its in membership
        // $levels = ao($course->settings, 'membership.levels');
        // if(is_array($levels) && array_intersect(\App\Sandy\SandyMembership::ids($bio_id), $levels)){
        //     //return true;
        // }


        // Check if its enrolled
        if (!$enrollment = CoursesEnrollment::where('user_id', $this->user_id)->where('payee_user_id', $payee)->where('course_id', $this->id)->first()) {
            return false;
        }

        // Check if it's expired

        
        return $enrollment;
    }
	public function getPrice(){
		$price = $this->user()->first()->price($this->price);

		return $price;
	}

	public function getFeaturedImage(){
		
		$banner = $this->banner;
		if(is_array($banner)) $banner = '';

		return $banner;
	}

	public function _get_featured_image(){
		
		$banner = $this->banner;
		if(is_array($banner)) $banner = '';

		return gs('media/courses/image', $banner);
	}

    protected static function boot(){
        parent::boot();

        static::creating(function ($model) {
            // Auto-assign user_id if not set
            if (empty($model->user_id) && auth()->check()) {
                $model->user_id = auth()->id();
            }
            $model->slug = $model->slug ? $model->slug : (string) str()->random(17);
        });
		
        static::updated(function ($model) {
            $model->slug = $model->slug ? $model->slug : (string) str()->random(17);
        });
    }
}
