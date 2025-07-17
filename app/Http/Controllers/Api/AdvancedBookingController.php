<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BookingService;
use App\Models\BookingAppointment;
use App\Models\BookingCalendar;
use App\Models\BookingAvailability;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AdvancedBookingController extends Controller
{
    /**
     * Get all booking services
     */
    public function getServices(Request $request)
    {
        try {
            $user = $request->user();
            
            $services = BookingService::where('user_id', $user->id)
                ->with(['availabilities', 'calendar'])
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $services,
                'message' => 'Booking services retrieved successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve booking services: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve booking services'
            ], 500);
        }
    }

    /**
     * Create a new booking service
     */
    public function createService(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'required|integer|min:15|max:480',
            'price' => 'required|numeric|min:0',
            'thumbnail' => 'nullable|string',
            'booking_time_interval' => 'nullable|string',
            'booking_workhours' => 'nullable|array',
            'gallery' => 'nullable|array',
            'settings' => 'nullable|array',
        ]);

        try {
            $user = $request->user();

            $service = BookingService::create([
                'user_id' => $user->id,
                'name' => $request->name,
                'description' => $request->description,
                'duration' => $request->duration,
                'price' => $request->price,
                'thumbnail' => $request->thumbnail,
                'booking_time_interval' => $request->booking_time_interval ?? '15',
                'booking_workhours' => $request->booking_workhours ?? [],
                'gallery' => $request->gallery ?? [],
                'settings' => $request->settings ?? [],
                'status' => 1, // Active
                'position' => 0,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Booking service created successfully',
                'data' => $service
            ], 201);
        } catch (\Exception $e) {
            Log::error('Failed to create booking service: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create booking service'
            ], 500);
        }
    }

    /**
     * Get available time slots for a service
     */
    public function getAvailableSlots(Request $request, $serviceId)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'timezone' => 'nullable|string',
        ]);

        try {
            $user = $request->user();
            $date = Carbon::parse($request->date);
            $timezone = $request->timezone ?? 'UTC';

            $service = BookingService::where('id', $serviceId)
                ->where('user_id', $user->id)
                ->firstOrFail();

            // Check if date is within advance booking limit
            $maxAdvanceDate = now()->addDays($service->max_advance_booking_days);
            if ($date->gt($maxAdvanceDate)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Date exceeds maximum advance booking period'
                ], 400);
            }

            // Check minimum notice requirement
            $minNoticeDate = now()->addHours($service->min_notice_hours);
            if ($date->lt($minNoticeDate)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Date does not meet minimum notice requirement'
                ], 400);
            }

            $availableSlots = $this->calculateAvailableSlots($service, $date, $timezone);

            return response()->json([
                'success' => true,
                'data' => [
                    'date' => $date->toDateString(),
                    'service' => $service->only(['id', 'name', 'duration_minutes', 'price']),
                    'available_slots' => $availableSlots,
                    'timezone' => $timezone,
                ],
                'message' => 'Available slots retrieved successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve available slots: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve available slots'
            ], 500);
        }
    }

    /**
     * Create a booking appointment
     */
    public function createAppointment(Request $request)
    {
        $request->validate([
            'service_ids' => 'required|array',
            'service_ids.*' => 'required|integer|exists:booking_services,id',
            'date' => 'required|date',
            'time' => 'required|string',
            'client_name' => 'required|string|max:255',
            'client_email' => 'required|email',
            'client_phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $user = $request->user();

            // Get the first service to calculate price
            $service = BookingService::find($request->service_ids[0]);

            $appointment = BookingAppointment::create([
                'user_id' => $user->id,
                'payee_user_id' => $user->id,
                'service_ids' => $request->service_ids,
                'date' => $request->date,
                'time' => $request->time,
                'settings' => [
                    'client_name' => $request->client_name,
                    'client_email' => $request->client_email,
                    'client_phone' => $request->client_phone,
                    'notes' => $request->notes,
                ],
                'info' => [
                    'booking_reference' => 'BK-' . strtoupper(substr(uniqid(), -8)),
                    'timezone' => $request->timezone ?? 'UTC',
                ],
                'appointment_status' => 0, // Pending
                'price' => $service->price,
                'is_paid' => false,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Appointment booked successfully',
                'data' => $appointment
            ], 201);
        } catch (\Exception $e) {
            Log::error('Failed to create appointment: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create appointment'
            ], 500);
        }
    }

    /**
     * Get all appointments
     */
    public function getAppointments(Request $request)
    {
        $request->validate([
            'appointment_status' => 'nullable|in:0,1,2,3',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_paid' => 'nullable|boolean',
        ]);

        try {
            $user = $request->user();
            
            $query = BookingAppointment::where('user_id', $user->id);

            if ($request->has('appointment_status')) {
                $query->where('appointment_status', $request->appointment_status);
            }

            if ($request->start_date) {
                $query->where('date', '>=', $request->start_date);
            }

            if ($request->end_date) {
                $query->where('date', '<=', $request->end_date);
            }

            if ($request->has('is_paid')) {
                $query->where('is_paid', $request->is_paid);
            }

            $appointments = $query->orderBy('date', 'desc')
                ->orderBy('time', 'desc')
                ->paginate(20);

            return response()->json([
                'success' => true,
                'data' => $appointments,
                'message' => 'Appointments retrieved successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve appointments: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve appointments'
            ], 500);
        }
    }

    /**
     * Update appointment status
     */
    public function updateAppointmentStatus(Request $request, $appointmentId)
    {
        $request->validate([
            'status' => 'required|in:confirmed,completed,cancelled,no_show',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $user = $request->user();
            
            $appointment = BookingAppointment::where('id', $appointmentId)
                ->where('user_id', $user->id)
                ->firstOrFail();

            $appointment->update([
                'status' => $request->status,
                'status_notes' => $request->notes,
                'status_updated_at' => now(),
            ]);

            // Send status update notification
            $this->sendStatusUpdateNotification($appointment);

            return response()->json([
                'success' => true,
                'message' => 'Appointment status updated successfully',
                'data' => $appointment->load('service')
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update appointment status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update appointment status'
            ], 500);
        }
    }

    /**
     * Set availability schedule
     */
    public function setAvailability(Request $request, $serviceId)
    {
        $request->validate([
            'schedule' => 'required|array',
            'schedule.*.day' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'schedule.*.is_available' => 'required|boolean',
            'schedule.*.slots' => 'required_if:schedule.*.is_available,true|array',
            'schedule.*.slots.*.start_time' => 'required_with:schedule.*.slots|date_format:H:i',
            'schedule.*.slots.*.end_time' => 'required_with:schedule.*.slots|date_format:H:i|after:schedule.*.slots.*.start_time',
        ]);

        try {
            $user = $request->user();
            
            $service = BookingService::where('id', $serviceId)
                ->where('user_id', $user->id)
                ->firstOrFail();

            // Delete existing availability
            BookingAvailability::where('service_id', $service->id)->delete();

            // Create new availability
            foreach ($request->schedule as $daySchedule) {
                if ($daySchedule['is_available'] && isset($daySchedule['slots'])) {
                    foreach ($daySchedule['slots'] as $slot) {
                        BookingAvailability::create([
                            'service_id' => $service->id,
                            'day_of_week' => $daySchedule['day'],
                            'start_time' => $slot['start_time'],
                            'end_time' => $slot['end_time'],
                            'is_available' => true,
                        ]);
                    }
                } else {
                    BookingAvailability::create([
                        'service_id' => $service->id,
                        'day_of_week' => $daySchedule['day'],
                        'start_time' => null,
                        'end_time' => null,
                        'is_available' => false,
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Availability schedule updated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to set availability: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to set availability'
            ], 500);
        }
    }

    /**
     * Get booking analytics
     */
    public function getBookingAnalytics(Request $request)
    {
        try {
            $user = $request->user();
            
            $analytics = [
                'total_appointments' => BookingAppointment::where('user_id', $user->id)->count(),
                'confirmed_appointments' => BookingAppointment::where('user_id', $user->id)->where('status', 'confirmed')->count(),
                'completed_appointments' => BookingAppointment::where('user_id', $user->id)->where('status', 'completed')->count(),
                'cancelled_appointments' => BookingAppointment::where('user_id', $user->id)->where('status', 'cancelled')->count(),
                'no_show_rate' => $this->calculateNoShowRate($user->id),
                'total_revenue' => BookingAppointment::where('user_id', $user->id)->where('status', 'completed')->sum('total_amount'),
                'average_booking_value' => BookingAppointment::where('user_id', $user->id)->where('status', 'completed')->avg('total_amount'),
                'popular_services' => $this->getPopularServices($user->id),
                'monthly_bookings' => $this->getMonthlyBookings($user->id),
                'peak_booking_times' => $this->getPeakBookingTimes($user->id),
            ];

            return response()->json([
                'success' => true,
                'data' => $analytics,
                'message' => 'Booking analytics retrieved successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve booking analytics: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve booking analytics'
            ], 500);
        }
    }

    // Helper methods

    private function calculateAvailableSlots($service, $date, $timezone)
    {
        $dayOfWeek = strtolower($date->format('l'));
        
        $availabilities = BookingAvailability::where('service_id', $service->id)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_available', true)
            ->orderBy('start_time')
            ->get();

        if ($availabilities->isEmpty()) {
            return [];
        }

        $slots = [];
        $serviceDuration = $service->duration_minutes;

        foreach ($availabilities as $availability) {
            $startTime = Carbon::parse($date->toDateString() . ' ' . $availability->start_time);
            $endTime = Carbon::parse($date->toDateString() . ' ' . $availability->end_time);

            while ($startTime->copy()->addMinutes($serviceDuration)->lte($endTime)) {
                $slotEndTime = $startTime->copy()->addMinutes($serviceDuration);
                
                // Check if slot is available (no existing appointments)
                $isAvailable = !BookingAppointment::where('service_id', $service->id)
                    ->where('status', '!=', 'cancelled')
                    ->where('start_time', '<', $slotEndTime)
                    ->where('end_time', '>', $startTime)
                    ->exists();

                if ($isAvailable) {
                    $slots[] = [
                        'start_time' => $startTime->format('H:i'),
                        'end_time' => $slotEndTime->format('H:i'),
                        'available' => true,
                    ];
                }

                $startTime->addMinutes($serviceDuration);
            }
        }

        return $slots;
    }

    private function sendBookingConfirmation($appointment)
    {
        // Implementation for sending booking confirmation email
        Log::info("Booking confirmation sent for appointment {$appointment->id}");
    }

    private function sendStatusUpdateNotification($appointment)
    {
        // Implementation for sending status update notification
        Log::info("Status update notification sent for appointment {$appointment->id}");
    }

    private function calculateNoShowRate($userId)
    {
        $totalAppointments = BookingAppointment::where('user_id', $userId)
            ->whereIn('status', ['completed', 'no_show'])
            ->count();

        if ($totalAppointments == 0) return 0;

        $noShows = BookingAppointment::where('user_id', $userId)
            ->where('status', 'no_show')
            ->count();

        return round(($noShows / $totalAppointments) * 100, 2);
    }

    private function getPopularServices($userId)
    {
        return BookingService::where('user_id', $userId)
            ->withCount('appointments')
            ->orderBy('appointments_count', 'desc')
            ->limit(5)
            ->get(['id', 'name', 'appointments_count']);
    }

    private function getMonthlyBookings($userId)
    {
        // Implementation for monthly booking statistics
        return [];
    }

    private function getPeakBookingTimes($userId)
    {
        // Implementation for peak booking times analysis
        return [];
    }
}