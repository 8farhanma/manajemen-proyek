<?php

namespace App\Services;

use App\Models\User;
use App\Models\Reminder;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class WhatsAppNotificationService
{
    protected $whatsappService;
    protected $whatsappServiceUrl;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
        $this->whatsappServiceUrl = env('WHATSAPP_SERVICE_URL', 'http://localhost:3000');
    }

    /**
     * Send a reminder notification via WhatsApp
     *
     * @param User $user
     * @param Reminder $reminder
     * @return bool
     */
    public function sendReminderNotification(User $user, Reminder $reminder): bool
    {
        // Validate inputs
        if (!$user->phone_number) {
            Log::warning("Cannot send reminder: No phone number for user", [
                'user_id' => $user->id,
                'reminder_id' => $reminder->id
            ]);
            return false;
        }

        try {
            // Format the reminder message
            $message = $this->formatReminderMessage($reminder);

            // Normalize phone number
            $normalizedNumber = $this->whatsappService->normalizePhoneNumber($user->phone_number);

            // Log detailed information before sending
            Log::info("Preparing to send WhatsApp reminder", [
                'user_id' => $user->id,
                'reminder_id' => $reminder->id,
                'original_phone' => $user->phone_number,
                'normalized_phone' => $normalizedNumber,
                'message_length' => strlen($message)
            ]);

            // Send via HTTP request to WhatsApp service
            $response = Http::timeout(10)
                ->post("{$this->whatsappServiceUrl}/send-message", [
                    'phone' => $normalizedNumber,
                    'message' => $message
                ]);

            // Check response
            if ($response->successful()) {
                $responseData = $response->json();

                if ($responseData['success'] ?? false) {
                    Log::info("WhatsApp reminder sent successfully", [
                        'user_id' => $user->id,
                        'reminder_id' => $reminder->id,
                        'phone_number' => $normalizedNumber
                    ]);
                    return true;
                }
            }

            // Log failed response
            Log::error("Failed to send WhatsApp reminder", [
                'user_id' => $user->id,
                'reminder_id' => $reminder->id,
                'phone_number' => $normalizedNumber,
                'response_status' => $response->status(),
                'response_body' => $response->body()
            ]);

            return false;

        } catch (\Exception $e) {
            Log::error("Exception sending WhatsApp reminder", [
                'user_id' => $user->id,
                'reminder_id' => $reminder->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Format reminder message for WhatsApp
     *
     * @param Reminder $reminder
     * @return string
     */
    protected function formatReminderMessage(Reminder $reminder): string
    {
        // Use Carbon with the application's configured timezone
        $formattedDate = Carbon::parse($reminder->date)->translatedFormat('l, d F Y');
        $formattedTime = Carbon::parse($reminder->time)->format('H:i');

        return sprintf(
            "🔔 *Pengingat: %s*\n\n" .
            "📝 Deskripsi: %s\n\n" .
            "📅 Tanggal: %s\n" .
            "⏰ Waktu: %s",
            $reminder->title,
            $reminder->description ?? '-',
            $formattedDate,
            $formattedTime
        );
    }

    /**
     * Check WhatsApp service status
     *
     * @return array
     */
    public function checkWhatsAppServiceStatus(): array
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

            Log::error('Failed to check WhatsApp service status', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return [
                'success' => false,
                'status' => null
            ];

        } catch (\Exception $e) {
            Log::error('Exception checking WhatsApp service status', [
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
