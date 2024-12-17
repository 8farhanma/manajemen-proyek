<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use App\Models\Reminder;
use App\Models\User;
use Carbon\Carbon;

class ReminderNotificationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test complete reminder notification workflow
     *
     * @return void
     */
    public function testReminderNotificationWorkflow()
    {
        // 1. Prepare Test Data
        $user = User::factory()->create([
            'phone_number' => '081234567890', // Contoh nomor telepon
            'name' => 'Test User'
        ]);

        // 2. Create Reminder
        $reminder = Reminder::create([
            'title' => 'Test Reminder',
            'description' => 'Pengujian sistem notifikasi',
            'date' => now()->format('Y-m-d'),
            'time' => now()->format('H:i:s'),
            'user_id' => $user->id
        ]);

        // 3. Verify Reminder Creation
        $this->assertDatabaseHas('reminders', [
            'id' => $reminder->id,
            'title' => 'Test Reminder',
            'user_id' => $user->id
        ]);

        // 4. Simulate WhatsApp Notification
        $whatsappResponse = $this->sendWhatsAppNotification($user, $reminder);

        // 5. Assert Notification Sent
        $this->assertTrue($whatsappResponse, 'WhatsApp notification should be sent successfully');

        // 6. Log Test Results
        \Log::info('Reminder Notification Test Completed', [
            'user' => $user->name,
            'phone' => $user->phone_number,
            'reminder_id' => $reminder->id
        ]);
    }

    /**
     * Send WhatsApp notification via external service
     *
     * @param User $user
     * @param Reminder $reminder
     * @return bool
     */
    private function sendWhatsAppNotification(User $user, Reminder $reminder)
    {
        try {
            // Increase timeout and add more robust error handling
            $response = Http::timeout(10)->post('http://localhost:3000/send-reminder', [
                'phone' => $user->phone_number,
                'message' => sprintf(
                    "Pengingat: %s\n%s\nWaktu: %s %s", 
                    $reminder->title, 
                    $reminder->description, 
                    $reminder->date, 
                    $reminder->time
                )
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            \Log::error('WhatsApp Notification Failed', [
                'error' => $e->getMessage(),
                'user' => $user->name
            ]);
            return false;
        }
    }

    /**
     * Test WhatsApp Service Connection
     *
     * @return void
     */
    public function testWhatsAppServiceConnection()
    {
        try {
            // Increase timeout for connection test
            $response = Http::timeout(10)->get('http://localhost:3000/status');
            
            $this->assertTrue(
                $response->successful(), 
                'WhatsApp service should be running and accessible'
            );

            $status = $response->json();
            $this->assertEquals('connected', $status['status'], 'WhatsApp should be connected');
        } catch (\Exception $e) {
            $this->fail('WhatsApp service connection test failed: ' . $e->getMessage());
        }
    }
}
