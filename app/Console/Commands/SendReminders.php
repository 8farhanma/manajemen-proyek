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
        
        // Set test mode if needed
        if ($this->option('test-mode')) {
            $this->whatsappService->setTestMode(true);
        }

        $now = Carbon::now();
        $currentDate = $now->format('Y-m-d');
        $currentTime = $now->format('H:i:s');

        // In test mode, use fixed test date and time
        if ($this->option('test-mode')) {
            $currentDate = '2024-12-17';
            $currentTime = '14:30:00';
        }

        $this->info("Checking reminders for date: {$currentDate} time: {$currentTime} (Asia/Jakarta)");

        // Get reminders that match the current time
        $reminders = Reminder::with('user')
            ->whereDate('date', $currentDate)
            ->where('time', $currentTime)
            ->get();

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
            
            if ($this->whatsappService->sendReminder($reminder)) {
                $this->info("✅ Reminder sent to {$reminder->user->name}");
            } else {
                $this->error("❌ Failed to send reminder to {$reminder->user->name}");
            }
        }
    }
}
