<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $whatsappServiceUrl;

    public function __construct()
    {
        $this->whatsappServiceUrl = env('WHATSAPP_SERVICE_URL', 'http://localhost:3000');
    }

    /**
     * Send reminder via WhatsApp
     *
     * @param string $recipient Phone number of recipient
     * @param string $message Message to send
     * @return bool Whether sending was successful
     */
    public function sendReminder($recipient, $message)
    {
        try {
            // Normalize phone number
            $normalizedNumber = $this->normalizePhoneNumber($recipient);
            
            Log::info('Attempting to send WhatsApp reminder', [
                'recipient' => $recipient,
                'normalizedNumber' => $normalizedNumber,
                'messageLength' => strlen($message)
            ]);

            // Send request to WhatsApp service
            $response = Http::timeout(10)
                ->post("{$this->whatsappServiceUrl}/send-message", [
                    'phone' => $normalizedNumber,
                    'message' => $message
                ]);

            // Log the full response for debugging
            Log::info('WhatsApp service response', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            // Check for successful response
            if ($response->successful()) {
                $responseData = $response->json();
                
                if ($responseData['success'] ?? false) {
                    Log::info('WhatsApp reminder sent successfully', [
                        'recipient' => $normalizedNumber
                    ]);
                    return true;
                }
            }

            // Log error details if sending failed
            Log::error('Failed to send WhatsApp reminder', [
                'recipient' => $normalizedNumber,
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return false;

        } catch (\Exception $e) {
            Log::error('WhatsApp service error', [
                'recipient' => $recipient,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Normalize phone number to WhatsApp format
     *
     * @param string $number
     * @return string
     */
    public function normalizePhoneNumber($number)
    {
        // Remove any non-digit characters
        $number = preg_replace('/\D/', '', $number);
        
        // Add country code if not present
        if (substr($number, 0, 2) !== '62') {
            if (substr($number, 0, 1) === '0') {
                $number = '62' . substr($number, 1);
            } else {
                $number = '62' . $number;
            }
        }
        
        return $number;
    }

    /**
     * Check WhatsApp service status
     *
     * @return array
     */
    public function checkStatus()
    {
        try {
            $response = Http::timeout(5)
                ->get("{$this->whatsappServiceUrl}/status");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'status' => $response->json()
                ];
            }

            Log::error('Failed to check WhatsApp status', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return [
                'success' => false,
                'status' => null
            ];

        } catch (\Exception $e) {
            Log::error('Exception checking WhatsApp status', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'status' => null
            ];
        }
    }
}
