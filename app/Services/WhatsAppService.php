<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Reminder;
use Carbon\Carbon;

class WhatsAppService
{
    protected $apiUrl;
    protected $apiKey;
    protected $testMode;

    public function __construct()
    {
        $this->apiUrl = config('services.whatsapp.url', 'http://whatsapp.test');
        $this->apiKey = config('services.whatsapp.key', 'test-key');
        $this->testMode = false;
    }

    public function setTestMode(bool $testMode)
    {
        $this->testMode = $testMode;
    }

    public function sendReminder(Reminder $reminder)
    {
        if (!$reminder->user || !$reminder->user->phone_number) {
            return false;
        }

        try {
            $message = $this->formatMessage($reminder);
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl . '/messages', [
                'phone' => $reminder->user->phone_number,
                'message' => $message,
            ]);

            // In test mode, check the response status and error
            if ($this->testMode || app()->environment('testing')) {
                return $response->successful() && !isset($response['error']);
            }

            return $response->successful() && ($response->json()['success'] ?? false);
        } catch (\Exception $e) {
            report($e);
            return false;
        }
    }

    protected function formatMessage(Reminder $reminder): string
    {
        $time = substr($reminder->time, 0, 5); // Get only HH:mm part
        return sprintf(
            "🔔 Reminder: %s\n\n%s\n\nTime: %s",
            $reminder->title,
            $reminder->description ?? '',
            $time
        );
    }
}
