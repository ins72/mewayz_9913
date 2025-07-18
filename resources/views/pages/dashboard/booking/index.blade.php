@extends('layouts.dashboard')

@section('title', 'Booking')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Booking Management</h2>
            <div class="header-actions">
                <button class="btn btn-secondary btn-sm">Export Bookings</button>
                <button class="btn btn-primary btn-sm">New Service</button>
            </div>
        </div>
        <div>
            <p style="color: var(--text-secondary); margin-bottom: 1rem;">
                Manage your booking services, appointments, and availability settings.
            </p>
        </div>
    </div>

    <!-- Booking Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value text-accent">34</div>
            <div class="stat-label">This Month</div>
            <div style="font-size: 0.75rem; color: var(--accent-secondary); margin-top: 0.5rem;">
                ↗ +8 from last month
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value" style="color: var(--accent-secondary);">$8,450</div>
            <div class="stat-label">Total Revenue</div>
            <div style="font-size: 0.75rem; color: var(--accent-secondary); margin-top: 0.5rem;">
                ↗ +15% from last month
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value" style="color: var(--accent-warning);">12</div>
            <div class="stat-label">Pending</div>
            <div style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.5rem;">
                Awaiting confirmation
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value" style="color: var(--accent-primary);">98.2%</div>
            <div class="stat-label">Attendance Rate</div>
            <div style="font-size: 0.75rem; color: var(--accent-secondary); margin-top: 0.5rem;">
                ↗ +2% from last month
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Quick Actions</h3>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <button class="p-4 bg-primary rounded-lg text-center hover:opacity-90 transition-opacity" style="background: var(--bg-primary); border-radius: 8px; border: 1px solid var(--border-primary);">
                <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">📅</div>
                <div style="font-size: 0.875rem; font-weight: 500; color: var(--text-primary);">View Calendar</div>
            </button>
            
            <button class="p-4 bg-primary rounded-lg text-center hover:opacity-90 transition-opacity" style="background: var(--bg-primary); border-radius: 8px; border: 1px solid var(--border-primary);">
                <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">⏰</div>
                <div style="font-size: 0.875rem; font-weight: 500; color: var(--text-primary);">Set Availability</div>
            </button>
            
            <button class="p-4 bg-primary rounded-lg text-center hover:opacity-90 transition-opacity" style="background: var(--bg-primary); border-radius: 8px; border: 1px solid var(--border-primary);">
                <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">💰</div>
                <div style="font-size: 0.875rem; font-weight: 500; color: var(--text-primary);">Pricing Settings</div>
            </button>
            
            <button class="p-4 bg-primary rounded-lg text-center hover:opacity-90 transition-opacity" style="background: var(--bg-primary); border-radius: 8px; border: 1px solid var(--border-primary);">
                <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">📊</div>
                <div style="font-size: 0.875rem; font-weight: 500; color: var(--text-primary);">View Reports</div>
            </button>
        </div>
    </div>

    <!-- Today's Appointments -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Today's Appointments</h3>
            <div class="flex gap-2">
                <button class="btn btn-sm btn-primary">Today</button>
                <button class="btn btn-sm btn-secondary">Tomorrow</button>
                <button class="btn btn-sm btn-secondary">This Week</button>
            </div>
        </div>
        <div class="space-y-4">
            <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--bg-primary); border-radius: 8px; border-left: 4px solid var(--accent-primary);">
                <div style="text-align: center; min-width: 4rem;">
                    <div style="font-weight: 600; color: var(--text-primary);">10:00</div>
                    <div style="font-size: 0.75rem; color: var(--text-secondary);">AM</div>
                </div>
                <div style="flex: 1;">
                    <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">Strategy Consultation</div>
                    <div style="color: var(--text-secondary); font-size: 0.75rem;">with John Smith • 60 minutes • $150</div>
                </div>
                <div class="flex gap-2">
                    <button class="btn btn-sm btn-secondary">Reschedule</button>
                    <button class="btn btn-sm btn-primary">Start Call</button>
                </div>
            </div>
            
            <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--bg-primary); border-radius: 8px; border-left: 4px solid var(--accent-secondary);">
                <div style="text-align: center; min-width: 4rem;">
                    <div style="font-weight: 600; color: var(--text-primary);">14:30</div>
                    <div style="font-size: 0.75rem; color: var(--text-secondary);">PM</div>
                </div>
                <div style="flex: 1;">
                    <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">Design Review</div>
                    <div style="color: var(--text-secondary); font-size: 0.75rem;">with Sarah Johnson • 45 minutes • $120</div>
                </div>
                <div class="flex gap-2">
                    <button class="btn btn-sm btn-secondary">Reschedule</button>
                    <button class="btn btn-sm btn-primary">Join Meeting</button>
                </div>
            </div>
            
            <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--bg-primary); border-radius: 8px; border-left: 4px solid var(--accent-warning);">
                <div style="text-align: center; min-width: 4rem;">
                    <div style="font-weight: 600; color: var(--text-primary);">16:00</div>
                    <div style="font-size: 0.75rem; color: var(--text-secondary);">PM</div>
                </div>
                <div style="flex: 1;">
                    <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">Project Planning</div>
                    <div style="color: var(--text-secondary); font-size: 0.75rem;">with Mike Chen • 90 minutes • $200</div>
                </div>
                <div class="flex gap-2">
                    <button class="btn btn-sm btn-secondary">Reschedule</button>
                    <button class="btn btn-sm btn-primary">Prepare</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Services and Pricing -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Services -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Your Services</h3>
                <button class="btn btn-primary btn-sm">Add Service</button>
            </div>
            <div class="space-y-4">
                <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                    <div style="width: 2.5rem; height: 2.5rem; background: var(--accent-primary); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <span style="color: white; font-size: 0.875rem;">💼</span>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">Business Consultation</div>
                        <div style="color: var(--text-secondary); font-size: 0.75rem;">60 minutes • $150</div>
                    </div>
                    <div class="flex gap-2">
                        <button class="btn btn-sm btn-secondary">Edit</button>
                    </div>
                </div>
                
                <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                    <div style="width: 2.5rem; height: 2.5rem; background: var(--accent-secondary); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <span style="color: white; font-size: 0.875rem;">🎨</span>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">Design Review</div>
                        <div style="color: var(--text-secondary); font-size: 0.75rem;">45 minutes • $120</div>
                    </div>
                    <div class="flex gap-2">
                        <button class="btn btn-sm btn-secondary">Edit</button>
                    </div>
                </div>
                
                <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--bg-primary); border-radius: 8px;">
                    <div style="width: 2.5rem; height: 2.5rem; background: var(--accent-warning); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <span style="color: white; font-size: 0.875rem;">📋</span>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-weight: 500; color: var(--text-primary); font-size: 0.875rem;">Project Planning</div>
                        <div style="color: var(--text-secondary); font-size: 0.75rem;">90 minutes • $200</div>
                    </div>
                    <div class="flex gap-2">
                        <button class="btn btn-sm btn-secondary">Edit</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Availability -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Availability</h3>
                <button class="btn btn-primary btn-sm">Update Hours</button>
            </div>
            <div class="space-y-3">
                <div style="display: flex; justify-content: between; align-items: center; padding: 0.75rem; background: var(--bg-primary); border-radius: 6px;">
                    <span style="font-weight: 500; color: var(--text-primary);">Monday</span>
                    <span style="color: var(--text-secondary);">9:00 AM - 5:00 PM</span>
                </div>
                <div style="display: flex; justify-content: between; align-items: center; padding: 0.75rem; background: var(--bg-primary); border-radius: 6px;">
                    <span style="font-weight: 500; color: var(--text-primary);">Tuesday</span>
                    <span style="color: var(--text-secondary);">9:00 AM - 5:00 PM</span>
                </div>
                <div style="display: flex; justify-content: between; align-items: center; padding: 0.75rem; background: var(--bg-primary); border-radius: 6px;">
                    <span style="font-weight: 500; color: var(--text-primary);">Wednesday</span>
                    <span style="color: var(--text-secondary);">9:00 AM - 5:00 PM</span>
                </div>
                <div style="display: flex; justify-content: between; align-items: center; padding: 0.75rem; background: var(--bg-primary); border-radius: 6px;">
                    <span style="font-weight: 500; color: var(--text-primary);">Thursday</span>
                    <span style="color: var(--text-secondary);">9:00 AM - 5:00 PM</span>
                </div>
                <div style="display: flex; justify-content: between; align-items: center; padding: 0.75rem; background: var(--bg-primary); border-radius: 6px;">
                    <span style="font-weight: 500; color: var(--text-primary);">Friday</span>
                    <span style="color: var(--text-secondary);">9:00 AM - 3:00 PM</span>
                </div>
                <div style="display: flex; justify-content: between; align-items: center; padding: 0.75rem; background: var(--bg-primary); border-radius: 6px; opacity: 0.5;">
                    <span style="font-weight: 500; color: var(--text-primary);">Saturday</span>
                    <span style="color: var(--text-secondary);">Closed</span>
                </div>
                <div style="display: flex; justify-content: between; align-items: center; padding: 0.75rem; background: var(--bg-primary); border-radius: 6px; opacity: 0.5;">
                    <span style="font-weight: 500; color: var(--text-primary);">Sunday</span>
                    <span style="color: var(--text-secondary);">Closed</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection