@extends('layouts.dashboard')

@section('title', 'Courses')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Course Management</h2>
            <div class="header-actions">
                <button class="btn btn-secondary btn-sm">Import Courses</button>
                <button class="btn btn-primary btn-sm">Create Course</button>
            </div>
        </div>
        <div>
            <p style="color: var(--text-secondary); margin-bottom: 1rem;">
                Create and manage your online courses, track student progress, and boost engagement.
            </p>
        </div>
    </div>

    <!-- Course Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value text-accent">12</div>
            <div class="stat-label">Total Courses</div>
            <div style="font-size: 0.75rem; color: var(--accent-secondary); margin-top: 0.5rem;">
                ↗ +3 new this month
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value" style="color: var(--accent-secondary);">847</div>
            <div class="stat-label">Active Students</div>
            <div style="font-size: 0.75rem; color: var(--accent-secondary); margin-top: 0.5rem;">
                ↗ +12% from last month
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value" style="color: var(--accent-warning);">$24,580</div>
            <div class="stat-label">Total Revenue</div>
            <div style="font-size: 0.75rem; color: var(--accent-secondary); margin-top: 0.5rem;">
                ↗ +18% from last month
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value" style="color: var(--accent-primary);">87.3%</div>
            <div class="stat-label">Completion Rate</div>
            <div style="font-size: 0.75rem; color: var(--accent-secondary); margin-top: 0.5rem;">
                ↗ +5% from last month
            </div>
        </div>
    </div>

    <!-- Course Filters -->
    <div class="card">
        <div class="flex gap-2 flex-wrap">
            <button class="btn btn-sm btn-primary">All Courses</button>
            <button class="btn btn-sm btn-secondary">Published</button>
            <button class="btn btn-sm btn-secondary">Draft</button>
            <button class="btn btn-sm btn-secondary">Archived</button>
        </div>
    </div>

    <!-- Course List -->
    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
        <!-- Course 1 -->
        <div class="card">
            <div style="height: 200px; background: linear-gradient(135deg, var(--accent-primary) 0%, var(--accent-secondary) 100%); border-radius: 8px; margin-bottom: 1rem; display: flex; align-items: center; justify-content: center;">
                <div style="color: white; font-size: 3rem;">📚</div>
            </div>
            <div class="space-y-3">
                <div>
                    <h3 style="font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Complete Marketing Masterclass</h3>
                    <p style="color: var(--text-secondary); font-size: 0.875rem;">Learn advanced marketing strategies and grow your business with proven techniques.</p>
                </div>
                
                <div class="flex justify-between text-sm">
                    <span style="color: var(--text-secondary);">24 Lessons</span>
                    <span style="color: var(--text-secondary);">8.5 hours</span>
                </div>
                
                <div class="flex justify-between items-center">
                    <div>
                        <div style="font-weight: 600; color: var(--text-primary);">$197</div>
                        <div style="color: var(--text-secondary); font-size: 0.75rem;">142 students</div>
                    </div>
                    <div class="flex gap-2">
                        <button class="btn btn-sm btn-secondary">Edit</button>
                        <button class="btn btn-sm btn-primary">View</button>
                    </div>
                </div>
                
                <div style="background: var(--bg-primary); padding: 0.75rem; border-radius: 6px;">
                    <div style="display: flex; justify-content: between; align-items: center; margin-bottom: 0.5rem;">
                        <span style="font-size: 0.75rem; color: var(--text-secondary);">Completion Rate</span>
                        <span style="font-size: 0.75rem; color: var(--text-primary);">89%</span>
                    </div>
                    <div style="width: 100%; height: 4px; background: var(--border-primary); border-radius: 2px; overflow: hidden;">
                        <div style="width: 89%; height: 100%; background: var(--accent-secondary); transition: width 0.3s ease;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Course 2 -->
        <div class="card">
            <div style="height: 200px; background: linear-gradient(135deg, var(--accent-warning) 0%, var(--accent-error) 100%); border-radius: 8px; margin-bottom: 1rem; display: flex; align-items: center; justify-content: center;">
                <div style="color: white; font-size: 3rem;">🎨</div>
            </div>
            <div class="space-y-3">
                <div>
                    <h3 style="font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Creative Design Fundamentals</h3>
                    <p style="color: var(--text-secondary); font-size: 0.875rem;">Master the principles of design and create stunning visuals for your brand.</p>
                </div>
                
                <div class="flex justify-between text-sm">
                    <span style="color: var(--text-secondary);">18 Lessons</span>
                    <span style="color: var(--text-secondary);">6.2 hours</span>
                </div>
                
                <div class="flex justify-between items-center">
                    <div>
                        <div style="font-weight: 600; color: var(--text-primary);">$149</div>
                        <div style="color: var(--text-secondary); font-size: 0.75rem;">89 students</div>
                    </div>
                    <div class="flex gap-2">
                        <button class="btn btn-sm btn-secondary">Edit</button>
                        <button class="btn btn-sm btn-primary">View</button>
                    </div>
                </div>
                
                <div style="background: var(--bg-primary); padding: 0.75rem; border-radius: 6px;">
                    <div style="display: flex; justify-content: between; align-items: center; margin-bottom: 0.5rem;">
                        <span style="font-size: 0.75rem; color: var(--text-secondary);">Completion Rate</span>
                        <span style="font-size: 0.75rem; color: var(--text-primary);">76%</span>
                    </div>
                    <div style="width: 100%; height: 4px; background: var(--border-primary); border-radius: 2px; overflow: hidden;">
                        <div style="width: 76%; height: 100%; background: var(--accent-warning); transition: width 0.3s ease;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Course 3 -->
        <div class="card">
            <div style="height: 200px; background: linear-gradient(135deg, var(--accent-secondary) 0%, var(--accent-primary) 100%); border-radius: 8px; margin-bottom: 1rem; display: flex; align-items: center; justify-content: center;">
                <div style="color: white; font-size: 3rem;">💻</div>
            </div>
            <div class="space-y-3">
                <div>
                    <h3 style="font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Web Development Basics</h3>
                    <p style="color: var(--text-secondary); font-size: 0.875rem;">Learn HTML, CSS, and JavaScript to build your first website from scratch.</p>
                </div>
                
                <div class="flex justify-between text-sm">
                    <span style="color: var(--text-secondary);">32 Lessons</span>
                    <span style="color: var(--text-secondary);">12.3 hours</span>
                </div>
                
                <div class="flex justify-between items-center">
                    <div>
                        <div style="font-weight: 600; color: var(--text-primary);">$299</div>
                        <div style="color: var(--text-secondary); font-size: 0.75rem;">267 students</div>
                    </div>
                    <div class="flex gap-2">
                        <button class="btn btn-sm btn-secondary">Edit</button>
                        <button class="btn btn-sm btn-primary">View</button>
                    </div>
                </div>
                
                <div style="background: var(--bg-primary); padding: 0.75rem; border-radius: 6px;">
                    <div style="display: flex; justify-content: between; align-items: center; margin-bottom: 0.5rem;">
                        <span style="font-size: 0.75rem; color: var(--text-secondary);">Completion Rate</span>
                        <span style="font-size: 0.75rem; color: var(--text-primary);">92%</span>
                    </div>
                    <div style="width: 100%; height: 4px; background: var(--border-primary); border-radius: 2px; overflow: hidden;">
                        <div style="width: 92%; height: 100%; background: var(--accent-secondary); transition: width 0.3s ease;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Course Card -->
        <div class="card" style="border: 2px dashed var(--border-primary); background: var(--bg-primary);">
            <div style="height: 400px; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center;">
                <div style="font-size: 3rem; margin-bottom: 1rem; color: var(--text-secondary);">➕</div>
                <h3 style="font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Create New Course</h3>
                <p style="color: var(--text-secondary); font-size: 0.875rem; margin-bottom: 1.5rem;">Start building your next educational masterpiece</p>
                <button class="btn btn-primary">Get Started</button>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Recent Course Activity</h3>
            <a href="#" class="btn btn-secondary btn-sm">View All</a>
        </div>
        <div class="space-y-4">
            <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                <div style="width: 2.5rem; height: 2.5rem; background: var(--accent-primary); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <span style="color: white; font-size: 0.875rem;">👤</span>
                </div>
                <div style="flex: 1;">
                    <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">New student enrolled</div>
                    <div style="color: var(--text-secondary); font-size: 0.75rem;">Sarah Johnson enrolled in "Complete Marketing Masterclass" • 2 hours ago</div>
                </div>
            </div>
            
            <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                <div style="width: 2.5rem; height: 2.5rem; background: var(--accent-secondary); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <span style="color: white; font-size: 0.875rem;">✅</span>
                </div>
                <div style="flex: 1;">
                    <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">Course completed</div>
                    <div style="color: var(--text-secondary); font-size: 0.75rem;">Mike Chen completed "Web Development Basics" • 4 hours ago</div>
                </div>
            </div>
            
            <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                <div style="width: 2.5rem; height: 2.5rem; background: var(--accent-warning); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <span style="color: white; font-size: 0.875rem;">⭐</span>
                </div>
                <div style="flex: 1;">
                    <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">New review received</div>
                    <div style="color: var(--text-secondary); font-size: 0.75rem;">Emma Davis left a 5-star review for "Creative Design Fundamentals" • 6 hours ago</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection