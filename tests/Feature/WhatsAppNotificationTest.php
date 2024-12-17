<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Reminder;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WhatsAppNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected $testDate;
    protected $testTime;

    protected function setUp(): void
    {
        parent::setUp();
        Http::fake();

        // Set fixed test date and time
        $this->testDate = '2024-12-17';
        $this->testTime = '14:30:00';
    }

    /** @test */
    public function it_sends_whatsapp_notification_for_due_reminders()
    {
        $user = User::factory()->create([
            'phone_number' => '+1234567890'
        ]);

        $reminder = Reminder::factory()->create([
            'user_id' => $user->id,
            'title' => 'Test Reminder',
            'description' => 'Test Description',
            'date' => $this->testDate,
            'time' => $this->testTime,
        ]);

        Http::fake([
            '*' => Http::response(['success' => true], 200)
        ]);

        $service = new WhatsAppService();
        $result = $service->sendReminder($reminder);

        $this->assertTrue($result);

        Http::assertSent(function ($request) use ($user) {
            return $request['phone'] === $user->phone_number &&
                   str_contains($request['message'], 'Test Reminder');
        });
    }

    /** @test */
    public function it_handles_whatsapp_service_failure()
    {
        $user = User::factory()->create([
            'phone_number' => '+1234567890'
        ]);

        $reminder = Reminder::factory()->create([
            'user_id' => $user->id,
            'title' => 'Test Reminder',
            'date' => $this->testDate,
            'time' => $this->testTime,
        ]);

        Http::fake([
            '*' => Http::response(['error' => 'Service unavailable'], 500)
        ]);

        $service = new WhatsAppService();
        $result = $service->sendReminder($reminder);

        $this->assertFalse($result);
    }

    /** @test */
    public function it_skips_reminders_for_users_without_phone_numbers()
    {
        $user = User::factory()->create([
            'phone_number' => null
        ]);

        $reminder = Reminder::factory()->create([
            'user_id' => $user->id,
            'title' => 'Test Reminder',
            'date' => $this->testDate,
            'time' => $this->testTime,
        ]);

        $service = new WhatsAppService();
        $result = $service->sendReminder($reminder);

        $this->assertFalse($result);
        Http::assertNothingSent();
    }
}
