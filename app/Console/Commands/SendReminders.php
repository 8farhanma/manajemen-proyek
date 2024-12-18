<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Reminder;
use App\Services\WhatsAppNotificationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SendReminders extends Command
{
    protected $signature = 'reminders:send 
                            {--test : Run in test mode} 
                            {--force : Force send reminders even if service is not ready}';
    
    protected $description = 'Send WhatsApp notifications for due reminders';

    protected $whatsappService;

    public function __construct(WhatsAppNotificationService $whatsappService)
    {
        parent::__construct();
        $this->whatsappService = $whatsappService;
    }

    public function handle()
    {
        try {
            $now = Carbon::now();
            
            // Debug logging
            Log::channel('daily')->info('SendReminders Debug: Starting execution', [
                'current_time' => $now->toDateTimeString(),
                'timezone' => config('app.timezone'),
                'force_flag' => $this->option('force')
            ]);

            // Check WhatsApp service
            if (!$this->option('force')) {
                $serviceStatus = $this->whatsappService->checkWhatsAppServiceStatus();
                Log::channel('daily')->info('SendReminders Debug: WhatsApp service status', [
                    'status' => $serviceStatus
                ]);
                
                if (!$serviceStatus['success']) {
                    Log::channel('daily')->warning('SendReminders Debug: WhatsApp service unavailable');
                    $this->error('WhatsApp service is not available. Use --force to override.');
                    return 1;
                }
            }

            // Find due reminders with detailed logging
            $query = Reminder::where('notification_sent', false)
                ->whereDate('date', '<=', $now->format('Y-m-d'))
                ->whereTime('time', '<=', $now->format('H:i:s'))
                ->with('user');

            // Log the SQL query for debugging
            Log::channel('daily')->info('SendReminders Debug: Query', [
                'sql' => $query->toSql(),
                'bindings' => $query->getBindings()
            ]);

            $reminders = $query->get();

            Log::channel('daily')->info('SendReminders Debug: Found reminders', [
                'count' => $reminders->count(),
                'reminders' => $reminders->map(function($r) {
                    return [
                        'id' => $r->id,
                        'title' => $r->title,
                        'date' => $r->date,
                        'time' => $r->time,
                        'user_id' => $r->user_id,
                        'has_user' => $r->user ? true : false,
                        'user_phone' => optional($r->user)->phone_number
                    ];
                })
            ]);

            $successCount = 0;
            $failureCount = 0;

            foreach ($reminders as $reminder) {
                Log::channel('daily')->info('SendReminders Debug: Processing reminder', [
                    'id' => $reminder->id,
                    'title' => $reminder->title,
                    'scheduled_time' => "{$reminder->date} {$reminder->time}",
                    'current_time' => $now->toDateTimeString()
                ]);

                if (!$reminder->user || !$reminder->user->phone_number) {
                    Log::channel('daily')->warning('SendReminders Debug: Invalid user data', [
                        'reminder_id' => $reminder->id,
                        'has_user' => $reminder->user ? true : false,
                        'has_phone' => optional($reminder->user)->phone_number ? true : false
                    ]);
                    continue;
                }

                try {
                    $sent = $this->whatsappService->sendReminderNotification(
                        $reminder->user, 
                        $reminder
                    );

                    if ($sent) {
                        $reminder->notification_sent = true;
                        $reminder->notification_sent_at = $now;
                        $reminder->save();

                        Log::channel('daily')->info('SendReminders Debug: Notification sent', [
                            'reminder_id' => $reminder->id,
                            'user_id' => $reminder->user_id,
                            'sent_at' => $now->toDateTimeString()
                        ]);

                        $successCount++;
                    } else {
                        Log::channel('daily')->error('SendReminders Debug: Send failed', [
                            'reminder_id' => $reminder->id,
                            'user_id' => $reminder->user_id
                        ]);
                        $failureCount++;
                    }
                } catch (\Exception $e) {
                    Log::channel('daily')->error('SendReminders Debug: Exception', [
                        'reminder_id' => $reminder->id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    $failureCount++;
                }
            }

            Log::channel('daily')->info('SendReminders Debug: Execution completed', [
                'total_processed' => $reminders->count(),
                'success_count' => $successCount,
                'failure_count' => $failureCount
            ]);

            $this->info("Notification Summary:");
            $this->info("Total Reminders: {$reminders->count()}");
            $this->info("Successful Notifications: {$successCount}");
            $this->info("Failed Notifications: {$failureCount}");

            return 0;
        } catch (\Exception $e) {
            Log::channel('daily')->critical('SendReminders Debug: Critical error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->error("Critical error: " . $e->getMessage());
            return 1;
        }
    }
}
