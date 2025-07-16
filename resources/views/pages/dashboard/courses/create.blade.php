<x-layouts.dashboard title="Create Course - Mewayz" page-title="Create Course">
    <div class="fade-in">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-primary-text">Create New Course</h1>
                <p class="text-secondary-text">Build and launch your online course</p>
            </div>
            <a href="{{ route('dashboard.courses.index') }}" class="btn btn-secondary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Courses
            </a>
        </div>

        <!-- Course Creation Form -->
        <div class="max-w-4xl mx-auto">
            <form id="courseForm" class="space-y-8">
                <!-- Basic Information -->
                <div class="card">
                    <div class="mb-6">
                        <h2 class="text-xl font-semibold text-primary-text mb-2">Basic Information</h2>
                        <p class="text-secondary-text">Enter the basic details of your course</p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-primary-text mb-2">Course Title *</label>
                            <input type="text" name="name" required class="form-input w-full" placeholder="e.g., Web Development Fundamentals">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-primary-text mb-2">Course Level *</label>
                            <select name="level" required class="form-input w-full">
                                <option value="">Select Level</option>
                                <option value="beginner">Beginner</option>
                                <option value="intermediate">Intermediate</option>
                                <option value="advanced">Advanced</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-primary-text mb-2">Course Description</label>
                        <textarea name="description" rows="4" class="form-input w-full" placeholder="Describe what students will learn in this course..."></textarea>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        <div>
                            <label class="block text-sm font-medium text-primary-text mb-2">Category</label>
                            <input type="text" name="category" class="form-input w-full" placeholder="e.g., Web Development, Design, Marketing">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-primary-text mb-2">Course Price (USD) *</label>
                            <input type="number" name="price" step="0.01" min="0" required class="form-input w-full" placeholder="0.00">
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-primary-text mb-2">Course Thumbnail URL</label>
                        <input type="url" name="thumbnail" class="form-input w-full" placeholder="https://example.com/thumbnail.jpg">
                    </div>
                </div>

                <!-- Course Content Preview -->
                <div class="card">
                    <div class="mb-6">
                        <h2 class="text-xl font-semibold text-primary-text mb-2">Course Preview</h2>
                        <p class="text-secondary-text">This is how your course will appear to students</p>
                    </div>
                    
                    <div class="border border-border-color rounded-lg p-6 bg-card-bg">
                        <div class="flex items-start gap-4">
                            <div class="w-24 h-24 bg-gradient-to-br from-info/20 to-success/20 rounded-lg flex items-center justify-center">
                                <svg class="w-12 h-12 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-primary-text mb-2" id="preview-title">Course Title</h3>
                                <p class="text-secondary-text mb-3" id="preview-description">Course description will appear here...</p>
                                <div class="flex items-center gap-4 text-sm">
                                    <span class="text-warning" id="preview-level">Level</span>
                                    <span class="text-secondary-text" id="preview-category">Category</span>
                                    <span class="text-success font-medium" id="preview-price">$0.00</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end gap-4">
                    <button type="button" class="btn btn-secondary" onclick="window.history.back()">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Create Course
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Real-time preview updates
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('courseForm');
            const inputs = form.querySelectorAll('input, textarea, select');
            
            // Update preview in real-time
            inputs.forEach(input => {
                input.addEventListener('input', updatePreview);
            });
            
            function updatePreview() {
                const title = form.querySelector('[name="name"]').value || 'Course Title';
                const description = form.querySelector('[name="description"]').value || 'Course description will appear here...';
                const level = form.querySelector('[name="level"]').value || 'Level';
                const category = form.querySelector('[name="category"]').value || 'Category';
                const price = form.querySelector('[name="price"]').value || '0.00';
                
                document.getElementById('preview-title').textContent = title;
                document.getElementById('preview-description').textContent = description;
                document.getElementById('preview-level').textContent = level.charAt(0).toUpperCase() + level.slice(1);
                document.getElementById('preview-category').textContent = category;
                document.getElementById('preview-price').textContent = `$${price}`;
            }
            
            // Form submission
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const formData = new FormData(form);
                const data = Object.fromEntries(formData);
                
                try {
                    const response = await fetch('/api/courses', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify(data)
                    });
                    
                    if (response.ok) {
                        const result = await response.json();
                        alert('Course created successfully!');
                        window.location.href = '/dashboard/courses';
                    } else {
                        const error = await response.json();
                        alert('Error: ' + (error.message || 'Failed to create course'));
                    }
                } catch (error) {
                    alert('Error: ' + error.message);
                }
            });
        });
    </script>
</x-layouts.dashboard>