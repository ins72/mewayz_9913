<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CoursesLesson;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $courses = Course::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $courses,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category' => 'nullable|string|max:255',
            'thumbnail' => 'nullable|string',
            'level' => 'required|in:beginner,intermediate,advanced',
        ]);

        $course = Course::create([
            'user_id' => $request->user()->id,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'category' => $request->category,
            'thumbnail' => $request->thumbnail,
            'level' => $request->level,
            'status' => 0, // 0 = draft, 1 = published/active
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Course created successfully',
            'data' => $course,
        ], 201);
    }

    public function show(Course $course)
    {
        // Check if user owns the course
        if ($course->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to course',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $course,
        ]);
    }

    public function update(Request $request, Course $course)
    {
        // Check if user owns the course
        if ($course->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to course',
            ], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category' => 'nullable|string|max:255',
            'thumbnail' => 'nullable|string',
            'level' => 'required|in:beginner,intermediate,advanced',
            'status' => 'in:draft,published,archived',
        ]);

        $course->update($request->only([
            'name', 'description', 'price', 'category', 
            'thumbnail', 'level', 'status'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Course updated successfully',
            'data' => $course,
        ]);
    }

    public function destroy(Course $course)
    {
        // Check if user owns the course
        if ($course->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to course',
            ], 403);
        }

        $course->delete();

        return response()->json([
            'success' => true,
            'message' => 'Course deleted successfully',
        ]);
    }

    public function getStudents(Course $course)
    {
        // Check if user owns the course
        if ($course->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to course',
            ], 403);
        }

        // TODO: Get course students
        $students = [];

        return response()->json([
            'success' => true,
            'data' => $students,
        ]);
    }

    public function getLessons(Course $course)
    {
        // Check if user owns the course
        if ($course->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to course',
            ], 403);
        }

        $lessons = CoursesLesson::where('course_id', $course->id)
            ->orderBy('order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $lessons,
        ]);
    }

    public function createLesson(Request $request, Course $course)
    {
        // Check if user owns the course
        if ($course->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to course',
            ], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'video_url' => 'nullable|url',
            'duration' => 'nullable|integer|min:0',
            'order' => 'required|integer|min:1',
        ]);

        $lesson = CoursesLesson::create([
            'course_id' => $course->id,
            'title' => $request->title,
            'content' => $request->content,
            'video_url' => $request->video_url,
            'duration' => $request->duration,
            'order' => $request->order,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Lesson created successfully',
            'data' => $lesson,
        ], 201);
    }

    public function getAnalytics(Request $request)
    {
        $analytics = [
            'total_courses' => 0,
            'published_courses' => 0,
            'total_students' => 0,
            'total_revenue' => 0,
            'avg_completion_rate' => '0%',
            'popular_courses' => [],
            'revenue_chart' => [],
        ];

        return response()->json([
            'success' => true,
            'data' => $analytics,
        ]);
    }

    public function getCommunityGroups(Request $request)
    {
        // TODO: Get community groups
        $groups = [];

        return response()->json([
            'success' => true,
            'data' => $groups,
        ]);
    }
}