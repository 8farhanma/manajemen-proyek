<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Reminder;
use App\Models\User;

class WhatsAppNotificationService
{
    protected $whatsappServiceUrl;

    public function __construct()
    {
        $this->whatsappServiceUrl = env('WHATSAPP_SERVICE_URL', 'http://localhost:3000');
    }

    /**
     * Send WhatsApp reminder notification
     *
     * @param User $user
     * @param Reminder $reminder
     * @return bool
     */
    public function sendReminderNotification(User $user, Reminder $reminder)
    {
        // Pastikan pengguna memiliki nomor telepon
        if (!$user->phone_number) {
            Log::warning("Cannot send WhatsApp notification: No phone number for user {$user->id}");
            return false;
        }

        try {
            $message = $this->formatReminderMessage($reminder);

            $response = Http::timeout(10)
                ->post("{$this->whatsappServiceUrl}/send-reminder", [
                    'phone' => $user->phone_number,
                    'message' => $message
                ]);

            if ($response->successful()) {
                Log::info("WhatsApp reminder sent successfully to {$user->phone_number}", [
                    'reminder_id' => $reminder->id,
                    'user_id' => $user->id
                ]);

                // Update reminder status
                $reminder->update([
                    'notification_sent' => true,
                    'notification_sent_at' => now()
                ]);

                return true;
            }

            Log::error("Failed to send WhatsApp notification", [
                'response' => $response->body(),
                'reminder_id' => $reminder->id,
                'user_id' => $user->id
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error("WhatsApp notification error: " . $e->getMessage(), [
                'reminder_id' => $reminder->id,
                'user_id' => $user->id
            ]);

            return false;
        }
    }

    /**
     * Format reminder message
     *
     * @param Reminder $reminder
     * @return string
     */
    protected function formatReminderMessage(Reminder $reminder)
    {
        return sprintf(
            "🔔 Pengingat: %s\n\n" .
            "📝 Deskripsi: %s\n\n" .
            "📅 Tanggal: %s\n" .
            "⏰ Waktu: %s",
            $reminder->title,
            $reminder->description,
            \Carbon\Carbon::parse($reminder->date)->translatedFormat('l, d F Y'),
            \Carbon\Carbon::parse($reminder->time)->format('H:i')
        );
    }
}
