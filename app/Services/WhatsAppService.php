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
            
            Log::info('Sending WhatsApp reminder', [
                'recipient' => $normalizedNumber,
                'message' => $message
            ]);

            // Send request to WhatsApp service
            $response = Http::timeout(10)
                ->post("{$this->whatsappServiceUrl}/send-reminder", [
                    'phone' => $normalizedNumber,
                    'message' => $message
                ]);

            if ($response->successful()) {
                Log::info('WhatsApp reminder sent successfully', [
                    'recipient' => $normalizedNumber
                ]);
                return true;
            }

            Log::error('Failed to send WhatsApp reminder', [
                'recipient' => $normalizedNumber,
                'error' => $response->body()
            ]);
            return false;

        } catch (\Exception $e) {
            Log::error('WhatsApp service error', [
                'recipient' => $recipient,
                'error' => $e->getMessage()
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
    protected function normalizePhoneNumber($number)
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
}
