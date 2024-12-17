<?php

namespace App\Console\Commands;

use App\Models\Reminder;
use App\Services\WhatsAppService;
use Illuminate\Console\Command;
use Carbon\Carbon;

class SendReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminders:send {--test-mode : Run in test mode}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send WhatsApp reminders to users';

    protected $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        parent::__construct();
        $this->whatsappService = $whatsappService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Set timezone to Asia/Jakarta
        date_default_timezone_set('Asia/Jakarta');
        
        // Tambahkan logging tambahan
        $this->info("SendReminders command started at: " . now());

        // Set test mode if needed
        if ($this->option('test-mode')) {
            $this->whatsappService->setTestMode(true);
        }

        $now = Carbon::now('Asia/Jakarta');
        $currentDate = $now->format('Y-m-d');
        $currentTime = $now->format('H:i:s');

        // Tambahkan logging detail waktu
        $this->info("Current Date: {$currentDate}, Current Time: {$currentTime}");

        // Debug: Tampilkan semua reminder untuk membantu diagnosis
        $allReminders = Reminder::all();
        $this->info("Total reminders in database: " . $allReminders->count());
        foreach ($allReminders as $reminder) {
            // Pastikan kita menggunakan format yang benar
            $reminderDate = $reminder->date instanceof Carbon 
                ? $reminder->date->format('Y-m-d') 
                : $reminder->date;
            $reminderTime = $reminder->time instanceof Carbon 
                ? $reminder->time->format('H:i:s') 
                : $reminder->time;

            $this->info("Reminder: {$reminder->title}, Date: {$reminderDate}, Time: {$reminderTime}");
            
            // Tambahkan debug untuk membandingkan waktu
            try {
                $reminderCarbon = Carbon::parse($reminderDate . ' ' . $reminderTime, 'Asia/Jakarta');
                $timeDiff = $now->diffInMinutes($reminderCarbon);
                $this->info("Time difference: {$timeDiff} minutes");
            } catch (\Exception $e) {
                $this->error("Error parsing reminder time: " . $e->getMessage());
            }
        }

        // Modifikasi query untuk mencari reminder dalam rentang waktu 5 menit
        $reminders = Reminder::with('user')
            ->whereDate('date', $currentDate)
            ->whereRaw('TIME(time) BETWEEN ? AND ?', [
                $now->copy()->subMinutes(5)->format('H:i:s'), 
                $now->copy()->addMinutes(5)->format('H:i:s')
            ])
            ->get();

        // Tambahkan logging detail reminder
        $this->info("Total reminders found: " . $reminders->count());
        foreach ($reminders as $reminder) {
            $this->info("Reminder details: " . json_encode([
                'id' => $reminder->id,
                'title' => $reminder->title,
                'date' => $reminder->date,
                'time' => $reminder->time,
                'user' => optional($reminder->user)->name
            ]));
        }

        if ($reminders->isEmpty()) {
            $this->info("No reminders found for the current time.");
            return;
        }

        $this->info("Found " . $reminders->count() . " reminders to send");

        foreach ($reminders as $reminder) {
            if (!$reminder->user || !$reminder->user->phone_number) {
                $this->warn("Skipping reminder for {$reminder->title} - No user or phone number found");
                continue;
            }

            $this->info("Sending reminder to {$reminder->user->name}");
            
            if ($this->sendReminderNotification($reminder)) {
                $this->info("✅ Reminder sent to {$reminder->user->name}");
            } else {
                $this->error("❌ Failed to send reminder to {$reminder->user->name}");
            }
        }
    }

    protected function sendReminderNotification(Reminder $reminder)
    {
        try {
            $user = $reminder->user;
            if (!$user || !$user->phone_number) {
                $this->error("No valid phone number for user of reminder {$reminder->id}");
                return false;
            }

            $message = $this->formatReminderMessage($reminder);
            
            return $this->whatsappService->sendReminder($user->phone_number, $message);
        } catch (\Exception $e) {
            $this->error("Error sending reminder {$reminder->id}: " . $e->getMessage());
            return false;
        }
    }

    protected function formatReminderMessage(Reminder $reminder): string
    {
        return sprintf(
            "🔔 Pengingat: %s\n\n" .
            "📝 Deskripsi: %s\n\n" .
            "📅 Tanggal: %s\n" .
            "⏰ Waktu: %s",
            $reminder->title,
            $reminder->description ?? '-',
            Carbon::parse($reminder->date)->translatedFormat('l, d F Y'),
            Carbon::parse($reminder->time)->format('H:i')
        );
    }
}
