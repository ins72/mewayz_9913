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

    public function getContacts(Request $request)
    {
        $contacts = Audience::where('user_id', $request->user()->id)
            ->where('type', 'contact')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $contacts,
        ]);
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

    public function createBulkAccounts(Request $request)
    {
        $request->validate([
            'accounts' => 'required|array|min:1',
            'accounts.*.name' => 'required|string|max:255',
            'accounts.*.email' => 'required|email',
            'generate_bio_links' => 'boolean',
            'send_welcome_emails' => 'boolean',
        ]);

        // TODO: Implement bulk account creation logic
        
        return response()->json([
            'success' => true,
            'message' => 'Bulk account creation started.',
        ]);
    }
}