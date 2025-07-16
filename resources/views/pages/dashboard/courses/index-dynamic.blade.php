<x-layouts.dashboard title="Courses - Mewayz" page-title="Courses">
    <div class="fade-in">
        <!-- Header Actions -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-primary-text">Course Management</h1>
                <p class="text-secondary-text">Create and manage your online courses</p>
            </div>
            <a href="{{ route('dashboard.courses.create') }}" class="btn btn-primary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Create Course
            </a>
        </div>

        <!-- Course Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="card">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-medium text-secondary-text">Total Courses</h3>
                    <svg class="w-5 h-5 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-primary-text" id="total-courses">-</div>
                <div class="text-sm text-success" id="courses-growth">Loading...</div>
            </div>

            <div class="card">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-medium text-secondary-text">Students</h3>
                    <svg class="w-5 h-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-primary-text" id="total-students">-</div>
                <div class="text-sm text-success" id="students-growth">Loading...</div>
            </div>

            <div class="card">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-medium text-secondary-text">Revenue</h3>
                    <svg class="w-5 h-5 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-primary-text" id="total-revenue">-</div>
                <div class="text-sm text-warning" id="revenue-growth">Loading...</div>
            </div>

            <div class="card">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-medium text-secondary-text">Completion Rate</h3>
                    <svg class="w-5 h-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-primary-text" id="completion-rate">-</div>
                <div class="text-sm text-success" id="completion-growth">Loading...</div>
            </div>
        </div>

        <!-- Courses Grid -->
        <div id="courses-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Loading State -->
            <div class="col-span-full text-center py-8">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-info mx-auto mb-4"></div>
                <p class="text-secondary-text">Loading courses...</p>
            </div>
        </div>

        <!-- Empty State -->
        <div id="empty-state" class="hidden text-center py-12">
            <div class="w-24 h-24 bg-info/10 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-12 h-12 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-primary-text mb-2">No courses yet</h3>
            <p class="text-secondary-text mb-6">Create your first course to get started with online education</p>
            <a href="{{ route('dashboard.courses.create') }}" class="btn btn-primary">Create Your First Course</a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadCoursesData();
            loadCoursesStats();
        });

        async function loadCoursesData() {
            try {
                const response = await fetch('/api/courses', {
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
                        'Content-Type': 'application/json'
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    displayCourses(data.data?.data || data.data || []);
                } else {
                    console.error('Failed to load courses');
                    showEmptyState();
                }
            } catch (error) {
                console.error('Error loading courses:', error);
                showEmptyState();
            }
        }

        async function loadCoursesStats() {
            try {
                // Mock stats for now - you can replace with actual API calls
                document.getElementById('total-courses').textContent = '12';
                document.getElementById('courses-growth').textContent = '2 published this month';
                document.getElementById('total-students').textContent = '1,429';
                document.getElementById('students-growth').textContent = '+23% from last month';
                document.getElementById('total-revenue').textContent = '$24,156';
                document.getElementById('revenue-growth').textContent = '+15.7% from last month';
                document.getElementById('completion-rate').textContent = '68%';
                document.getElementById('completion-growth').textContent = '+4.2% from last month';
            } catch (error) {
                console.error('Error loading stats:', error);
            }
        }

        function displayCourses(courses) {
            const grid = document.getElementById('courses-grid');
            const emptyState = document.getElementById('empty-state');
            
            if (courses.length === 0) {
                showEmptyState();
                return;
            }

            grid.innerHTML = courses.map(course => createCourseCard(course)).join('') + createNewCourseCard();
            emptyState.classList.add('hidden');
        }

        function createCourseCard(course) {
            const statusClass = course.status === 1 ? 'success' : 'warning';
            const statusText = course.status === 1 ? 'Published' : 'Draft';
            const actionText = course.status === 1 ? 'View' : 'Preview';
            
            return `
                <div class="card">
                    <div class="mb-4">
                        <div class="w-full h-32 bg-gradient-to-br from-info/20 to-success/20 rounded-lg flex items-center justify-center mb-4">
                            ${course.thumbnail ? 
                                `<img src="${course.thumbnail}" alt="${course.name}" class="w-full h-full object-cover rounded-lg">` :
                                `<svg class="w-12 h-12 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>`
                            }
                        </div>
                        <h3 class="text-lg font-semibold text-primary-text mb-2">${course.name}</h3>
                        <p class="text-secondary-text text-sm mb-4">${course.description || 'No description available'}</p>
                        <div class="flex items-center justify-between text-sm mb-4">
                            <span class="text-${statusClass}">${statusText}</span>
                            <span class="text-secondary-text">${course.category || 'Uncategorized'}</span>
                        </div>
                        <div class="text-lg font-bold text-primary-text mb-2">$${course.price || '0.00'}</div>
                    </div>
                    <div class="flex gap-2">
                        <button onclick="editCourse(${course.id})" class="btn btn-secondary text-sm flex-1">Edit</button>
                        <button onclick="viewCourse(${course.id})" class="btn btn-primary text-sm flex-1">${actionText}</button>
                    </div>
                </div>
            `;
        }

        function createNewCourseCard() {
            return `
                <div class="card border-dashed border-2 border-border-color hover:border-info/50 transition-colors">
                    <div class="flex flex-col items-center justify-center py-8 text-center">
                        <div class="w-16 h-16 bg-info/10 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-primary-text mb-2">Create New Course</h3>
                        <p class="text-secondary-text text-sm mb-4">Start building your next course</p>
                        <a href="/dashboard/courses/create" class="btn btn-primary">Get Started</a>
                    </div>
                </div>
            `;
        }

        function showEmptyState() {
            document.getElementById('courses-grid').innerHTML = '';
            document.getElementById('empty-state').classList.remove('hidden');
        }

        function editCourse(courseId) {
            window.location.href = `/dashboard/courses/${courseId}/edit`;
        }

        function viewCourse(courseId) {
            window.location.href = `/dashboard/courses/${courseId}`;
        }
    </script>
</x-layouts.dashboard>