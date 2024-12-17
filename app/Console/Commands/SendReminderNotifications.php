<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Reminder;
use App\Services\WhatsAppNotificationService;
use Carbon\Carbon;

class SendReminderNotifications extends Command
{
    protected $signature = 'reminders:send';
    protected $description = 'Send WhatsApp notifications for due reminders';

    protected $whatsappService;

    public function __construct(WhatsAppNotificationService $whatsappService)
    {
        parent::__construct();
        $this->whatsappService = $whatsappService;
    }

    public function handle()
    {
        // Ambil pengingat yang belum dikirim dan sudah waktunya
        $now = Carbon::now('Asia/Jakarta');
        $reminders = Reminder::where('date', $now->format('Y-m-d'))
            ->where('time', '<=', $now->format('H:i:s'))
            ->where('notification_sent', false)
            ->with('user')
            ->get();

        $this->info("Found {$reminders->count()} reminders to send");

        foreach ($reminders as $reminder) {
            if (!$reminder->user) {
                $this->warn("Skipping reminder {$reminder->id}: No user found");
                continue;
            }

            $sent = $this->whatsappService->sendReminderNotification($reminder->user, $reminder);

            if ($sent) {
                $this->info("Sent reminder {$reminder->id} to {$reminder->user->name}");
            } else {
                $this->error("Failed to send reminder {$reminder->id}");
            }
        }

        return 0;
    }
}
