<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Audience;
use Illuminate\Http\Request;

class CrmController extends Controller
{
    public function getLeads(Request $request)
    {
        $leads = Audience::where('user_id', $request->user()->id)
            ->where('type', 'lead')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $leads,
        ]);
    }

    public function createLead(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:audiences,email',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:hot,warm,cold',
            'source' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $lead = Audience::create([
            'user_id' => $request->user()->id,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'type' => 'lead',
            'status' => $request->status,
            'source' => $request->source,
            'notes' => $request->notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Lead created successfully',
            'data' => $lead,
        ], 201);
    }

    public function showLead(Audience $lead)
    {
        // Check if user owns the lead
        if ($lead->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to lead',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $lead,
        ]);
    }

    public function updateLead(Request $request, Audience $lead)
    {
        // Check if user owns the lead
        if ($lead->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to lead',
            ], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:audiences,email,' . $lead->id,
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:hot,warm,cold',
            'source' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $lead->update($request->only(['name', 'email', 'phone', 'status', 'source', 'notes']));

        return response()->json([
            'success' => true,
            'message' => 'Lead updated successfully',
            'data' => $lead,
        ]);
    }

    public function deleteLead(Audience $lead)
    {
        // Check if user owns the lead
        if ($lead->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to lead',
            ], 403);
        }

        $lead->delete();

        return response()->json([
            'success' => true,
            'message' => 'Lead deleted successfully',
        ]);
    }

    /**
     * Get all contacts with advanced filtering and pagination
     */
    public function getContacts(Request $request)
    {
        $request->validate([
            'search' => 'nullable|string|max:255',
            'status' => 'nullable|string|in:active,inactive,lead,prospect,customer,archived',
            'type' => 'nullable|string|in:individual,company,organization',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'sort_by' => 'nullable|string|in:name,email,created_at,updated_at,last_contact_date,deal_value',
            'sort_order' => 'nullable|string|in:asc,desc',
            'per_page' => 'nullable|integer|min:1|max:100',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'country' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'source' => 'nullable|string|in:website,social_media,referral,cold_outreach,event,advertisement,organic'
        ]);

        try {
            $query = Audience::where('user_id', $request->user()->id)
                ->where('type', 'contact')
                ->with(['notes', 'activities', 'deals']);

            // Apply search filter
            if ($request->search) {
                $query->where(function($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%')
                      ->orWhere('email', 'like', '%' . $request->search . '%')
                      ->orWhere('phone', 'like', '%' . $request->search . '%')
                      ->orWhere('company', 'like', '%' . $request->search . '%');
                });
            }

            // Apply status filter
            if ($request->status) {
                $query->where('status', $request->status);
            }

            // Apply type filter
            if ($request->type) {
                $query->where('contact_type', $request->type);
            }

            // Apply date range filter
            if ($request->date_from) {
                $query->where('created_at', '>=', $request->date_from);
            }
            if ($request->date_to) {
                $query->where('created_at', '<=', $request->date_to);
            }

            // Apply location filters
            if ($request->country) {
                $query->where('country', $request->country);
            }
            if ($request->city) {
                $query->where('city', $request->city);
            }

            // Apply source filter
            if ($request->source) {
                $query->where('source', $request->source);
            }

            // Apply tags filter
            if ($request->tags) {
                $query->whereJsonContains('tags', $request->tags);
            }

            // Apply sorting
            $sortBy = $request->sort_by ?? 'created_at';
            $sortOrder = $request->sort_order ?? 'desc';
            $query->orderBy($sortBy, $sortOrder);

            // Paginate results
            $perPage = $request->per_page ?? 20;
            $contacts = $query->paginate($perPage);

            // Calculate summary statistics
            $totalContacts = Audience::where('user_id', $request->user()->id)
                ->where('type', 'contact')
                ->count();

            $activeContacts = Audience::where('user_id', $request->user()->id)
                ->where('type', 'contact')
                ->where('status', 'active')
                ->count();

            $contactsThisMonth = Audience::where('user_id', $request->user()->id)
                ->where('type', 'contact')
                ->where('created_at', '>=', now()->startOfMonth())
                ->count();

            $totalDealValue = Audience::where('user_id', $request->user()->id)
                ->where('type', 'contact')
                ->sum('deal_value');

            return response()->json([
                'success' => true,
                'data' => [
                    'contacts' => $contacts->items(),
                    'pagination' => [
                        'current_page' => $contacts->currentPage(),
                        'per_page' => $contacts->perPage(),
                        'total' => $contacts->total(),
                        'last_page' => $contacts->lastPage(),
                        'from' => $contacts->firstItem(),
                        'to' => $contacts->lastItem()
                    ],
                    'summary' => [
                        'total_contacts' => $totalContacts,
                        'active_contacts' => $activeContacts,
                        'contacts_this_month' => $contactsThisMonth,
                        'total_deal_value' => $totalDealValue,
                        'conversion_rate' => $totalContacts > 0 ? round(($activeContacts / $totalContacts) * 100, 2) : 0
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to retrieve contacts', ['error' => $e->getMessage(), 'user_id' => $request->user()->id]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve contacts: ' . $e->getMessage()
            ], 500);
        }
    }

    public function importContacts(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
        ]);

        try {
            $file = $request->file('file');
            $path = $file->store('imports', 'local');
            $fullPath = storage_path('app/' . $path);

            $imported = 0;
            $failed = 0;
            $errors = [];

            if (($handle = fopen($fullPath, 'r')) !== FALSE) {
                $header = fgetcsv($handle, 1000, ',');
                
                // Expected headers: name, email, phone, status, source, notes
                while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                    try {
                        $contactData = [
                            'user_id' => $request->user()->id,
                            'name' => $data[0] ?? '',
                            'email' => $data[1] ?? '',
                            'phone' => $data[2] ?? null,
                            'type' => 'contact',
                            'status' => in_array($data[3] ?? '', ['hot', 'warm', 'cold']) ? $data[3] : 'cold',
                            'source' => $data[4] ?? 'import',
                            'notes' => $data[5] ?? null,
                        ];

                        // Validate email
                        if (!filter_var($contactData['email'], FILTER_VALIDATE_EMAIL)) {
                            $failed++;
                            $errors[] = "Invalid email: {$contactData['email']}";
                            continue;
                        }

                        // Check if contact already exists
                        $existing = Audience::where('user_id', $request->user()->id)
                            ->where('email', $contactData['email'])
                            ->first();

                        if ($existing) {
                            $existing->update($contactData);
                        } else {
                            Audience::create($contactData);
                        }

                        $imported++;
                    } catch (\Exception $e) {
                        $failed++;
                        $errors[] = "Row error: " . $e->getMessage();
                    }
                }
                fclose($handle);
            }

            // Clean up uploaded file
            unlink($fullPath);

            return response()->json([
                'success' => true,
                'message' => "Import completed. {$imported} contacts imported, {$failed} failed.",
                'data' => [
                    'imported' => $imported,
                    'failed' => $failed,
                    'errors' => array_slice($errors, 0, 10), // Show first 10 errors
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getPipeline(Request $request)
    {
        $pipeline = [
            'stages' => [
                ['name' => 'New', 'count' => 0, 'value' => 0],
                ['name' => 'Qualified', 'count' => 0, 'value' => 0],
                ['name' => 'Proposal', 'count' => 0, 'value' => 0],
                ['name' => 'Negotiation', 'count' => 0, 'value' => 0],
                ['name' => 'Closed Won', 'count' => 0, 'value' => 0],
                ['name' => 'Closed Lost', 'count' => 0, 'value' => 0],
            ],
            'total_value' => 0,
            'conversion_rate' => '0%',
        ];

        return response()->json([
            'success' => true,
            'data' => $pipeline,
        ]);
    }

    /**
     * Create a new contact with advanced fields
     */
    public function createContact(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:audiences,email',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'job_title' => 'nullable|string|max:255',
            'contact_type' => 'required|string|in:individual,company,organization',
            'status' => 'required|string|in:active,inactive,lead,prospect,customer,archived',
            'source' => 'nullable|string|in:website,social_media,referral,cold_outreach,event,advertisement,organic',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'linkedin' => 'nullable|url|max:255',
            'twitter' => 'nullable|string|max:100',
            'facebook' => 'nullable|url|max:255',
            'instagram' => 'nullable|string|max:100',
            'deal_value' => 'nullable|numeric|min:0',
            'deal_stage' => 'nullable|string|in:prospecting,qualification,proposal,negotiation,closed_won,closed_lost',
            'priority' => 'nullable|string|in:low,medium,high,urgent',
            'notes' => 'nullable|string|max:2000',
            'birthday' => 'nullable|date',
            'anniversary' => 'nullable|date',
            'time_zone' => 'nullable|string|max:50',
            'preferred_contact_method' => 'nullable|string|in:email,phone,sms,whatsapp,linkedin',
            'marketing_consent' => 'boolean',
            'custom_fields' => 'nullable|array',
            'custom_fields.*.key' => 'required|string|max:100',
            'custom_fields.*.value' => 'required|string|max:500',
            'custom_fields.*.type' => 'required|string|in:text,number,date,boolean,select'
        ]);

        try {
            $contact = Audience::create([
                'user_id' => $request->user()->id,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'company' => $request->company,
                'job_title' => $request->job_title,
                'type' => 'contact',
                'contact_type' => $request->contact_type,
                'status' => $request->status,
                'source' => $request->source,
                'tags' => $request->tags ?? [],
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'country' => $request->country,
                'postal_code' => $request->postal_code,
                'website' => $request->website,
                'linkedin' => $request->linkedin,
                'twitter' => $request->twitter,
                'facebook' => $request->facebook,
                'instagram' => $request->instagram,
                'deal_value' => $request->deal_value ?? 0,
                'deal_stage' => $request->deal_stage,
                'priority' => $request->priority ?? 'medium',
                'birthday' => $request->birthday,
                'anniversary' => $request->anniversary,
                'time_zone' => $request->time_zone,
                'preferred_contact_method' => $request->preferred_contact_method ?? 'email',
                'marketing_consent' => $request->marketing_consent ?? false,
                'custom_fields' => $request->custom_fields ?? [],
                'last_contact_date' => now()
            ]);

            // Create initial note if provided
            if ($request->notes) {
                // Assuming there's a notes relationship/model
                $contact->notes()->create([
                    'content' => $request->notes,
                    'type' => 'initial_note'
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Contact created successfully',
                'data' => [
                    'id' => $contact->id,
                    'name' => $contact->name,
                    'email' => $contact->email,
                    'phone' => $contact->phone,
                    'company' => $contact->company,
                    'job_title' => $contact->job_title,
                    'contact_type' => $contact->contact_type,
                    'status' => $contact->status,
                    'source' => $contact->source,
                    'deal_value' => $contact->deal_value,
                    'priority' => $contact->priority,
                    'created_at' => $contact->created_at,
                    'tags' => $contact->tags,
                    'location' => [
                        'city' => $contact->city,
                        'country' => $contact->country
                    ]
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('Failed to create contact', ['error' => $e->getMessage(), 'user_id' => $request->user()->id]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to create contact: ' . $e->getMessage()
            ], 500);
        }
    }

    public function createBulkAccounts(Request $request)
    {
        $request->validate([
            'accounts' => 'required|array|min:1|max:100',
            'accounts.*.name' => 'required|string|max:255',
            'accounts.*.email' => 'required|email',
            'generate_bio_links' => 'boolean',
            'send_welcome_emails' => 'boolean',
        ]);

        try {
            $created = 0;
            $failed = 0;
            $errors = [];

            foreach ($request->accounts as $accountData) {
                try {
                    // Check if contact already exists
                    $existing = Audience::where('user_id', $request->user()->id)
                        ->where('email', $accountData['email'])
                        ->first();

                    if ($existing) {
                        $failed++;
                        $errors[] = "Contact already exists: {$accountData['email']}";
                        continue;
                    }

                    // Create new contact
                    $contact = Audience::create([
                        'user_id' => $request->user()->id,
                        'name' => $accountData['name'],
                        'email' => $accountData['email'],
                        'type' => 'contact',
                        'status' => 'cold',
                        'source' => 'bulk_creation',
                        'notes' => 'Created via bulk account creation',
                    ]);

                    // Generate bio link if requested
                    if ($request->generate_bio_links) {
                        // This would create a bio site for each contact
                        // Implementation depends on bio site creation logic
                    }

                    // Send welcome email if requested
                    if ($request->send_welcome_emails) {
                        // This would send a welcome email to each contact
                        // Implementation depends on email system
                    }

                    $created++;
                } catch (\Exception $e) {
                    $failed++;
                    $errors[] = "Failed to create {$accountData['email']}: " . $e->getMessage();
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Bulk creation completed. {$created} accounts created, {$failed} failed.",
                'data' => [
                    'created' => $created,
                    'failed' => $failed,
                    'errors' => array_slice($errors, 0, 10), // Show first 10 errors
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Bulk creation failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}