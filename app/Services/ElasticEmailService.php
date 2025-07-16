<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ElasticEmailService
{
    private $apiKey;
    private $baseUrl;

    public function __construct()
    {
        $this->apiKey = env('ELASTICEMAIL_API_KEY');
        $this->baseUrl = 'https://api.elasticemail.com/v2';
    }

    /**
     * Send a single email
     */
    public function sendEmail($to, $subject, $htmlContent, $from = null, $fromName = null)
    {
        try {
            $response = Http::post($this->baseUrl . '/email/send', [
                'apikey' => $this->apiKey,
                'from' => $from ?? env('MAIL_FROM_ADDRESS', 'noreply@mewayz.com'),
                'fromName' => $fromName ?? env('MAIL_FROM_NAME', 'Mewayz'),
                'to' => $to,
                'subject' => $subject,
                'bodyHtml' => $htmlContent,
                'isTransactional' => false,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'message_id' => $data['data']['messageid'] ?? null,
                    'data' => $data,
                ];
            } else {
                return [
                    'success' => false,
                    'error' => $response->json()['error'] ?? 'Failed to send email',
                ];
            }
        } catch (Exception $e) {
            Log::error('ElasticEmail send error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send bulk emails
     */
    public function sendBulkEmails($emails, $subject, $htmlContent, $from = null, $fromName = null)
    {
        try {
            $response = Http::post($this->baseUrl . '/email/send', [
                'apikey' => $this->apiKey,
                'from' => $from ?? env('MAIL_FROM_ADDRESS', 'noreply@mewayz.com'),
                'fromName' => $fromName ?? env('MAIL_FROM_NAME', 'Mewayz'),
                'to' => implode(',', $emails),
                'subject' => $subject,
                'bodyHtml' => $htmlContent,
                'isTransactional' => false,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'message_id' => $data['data']['messageid'] ?? null,
                    'data' => $data,
                ];
            } else {
                return [
                    'success' => false,
                    'error' => $response->json()['error'] ?? 'Failed to send bulk emails',
                ];
            }
        } catch (Exception $e) {
            Log::error('ElasticEmail bulk send error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Add contact to a list
     */
    public function addContact($email, $listName = 'Main List', $firstName = null, $lastName = null)
    {
        try {
            $response = Http::post($this->baseUrl . '/contact/add', [
                'apikey' => $this->apiKey,
                'email' => $email,
                'firstName' => $firstName,
                'lastName' => $lastName,
                'listName' => $listName,
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            } else {
                return [
                    'success' => false,
                    'error' => $response->json()['error'] ?? 'Failed to add contact',
                ];
            }
        } catch (Exception $e) {
            Log::error('ElasticEmail add contact error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get account information
     */
    public function getAccountInfo()
    {
        try {
            $response = Http::get($this->baseUrl . '/account', [
                'apikey' => $this->apiKey,
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            } else {
                return [
                    'success' => false,
                    'error' => $response->json()['error'] ?? 'Failed to get account info',
                ];
            }
        } catch (Exception $e) {
            Log::error('ElasticEmail account info error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get email statistics
     */
    public function getEmailStats($from = null, $to = null)
    {
        try {
            $params = [
                'apikey' => $this->apiKey,
            ];

            if ($from) {
                $params['from'] = $from;
            }

            if ($to) {
                $params['to'] = $to;
            }

            $response = Http::get($this->baseUrl . '/email/getstats', $params);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            } else {
                return [
                    'success' => false,
                    'error' => $response->json()['error'] ?? 'Failed to get email stats',
                ];
            }
        } catch (Exception $e) {
            Log::error('ElasticEmail stats error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Test API connection
     */
    public function testConnection()
    {
        try {
            $response = Http::get($this->baseUrl . '/account', [
                'apikey' => $this->apiKey,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'message' => 'ElasticEmail connection successful',
                    'account' => $data['data'] ?? null,
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'Invalid API key or connection failed',
                ];
            }
        } catch (Exception $e) {
            Log::error('ElasticEmail test connection error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}