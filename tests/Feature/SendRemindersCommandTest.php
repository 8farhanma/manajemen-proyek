<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Reminder;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class SendRemindersCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        date_default_timezone_set('Asia/Jakarta');
    }

    /** @test */
    public function it_sends_reminders_due_at_current_time()
    {
        $user = User::factory()->create([
            'phone_number' => '+1234567890'
        ]);

        $reminder = Reminder::factory()
            ->forTestMode()
            ->create([
                'user_id' => $user->id,
                'title' => 'Test Reminder',
            ]);

        Http::fake([
            '*' => Http::response(['success' => true], 200)
        ]);

        $this->artisan('reminders:send', ['--test-mode' => true])
            ->assertSuccessful()
            ->expectsOutput('✅ Reminder sent to ' . $user->name);

        Http::assertSent(function ($request) use ($user) {
            return $request['phone'] === $user->phone_number &&
                   str_contains($request['message'], 'Test Reminder');
        });
    }

    /** @test */
    public function it_handles_multiple_reminders_at_same_time()
    {
        $users = User::factory()->count(3)->create([
            'phone_number' => '+1234567890'
        ]);

        foreach ($users as $user) {
            Reminder::factory()
                ->forTestMode()
                ->create([
                    'user_id' => $user->id,
                ]);
        }

        Http::fake([
            '*' => Http::response(['success' => true], 200)
        ]);

        $this->artisan('reminders:send', ['--test-mode' => true])
            ->assertSuccessful();

        Http::assertSentCount(3);
    }

    /** @test */
    public function it_logs_failed_reminder_attempts()
    {
        $user = User::factory()->create([
            'name' => 'Olen Kassulke',
            'phone_number' => '+1234567890'
        ]);

        $reminder = Reminder::factory()
            ->forTestMode()
            ->create([
                'user_id' => $user->id,
            ]);

        Http::fake([
            '*' => Http::response(['error' => 'Service unavailable'], 500)
        ]);

        $this->artisan('reminders:send', ['--test-mode' => true])
            ->assertSuccessful()
            ->expectsOutput('❌ Failed to send reminder to ' . $user->name);
    }

    /** @test */
    public function it_respects_timezone_settings()
    {
        $user = User::factory()->create([
            'phone_number' => '+1234567890'
        ]);

        $reminder = Reminder::factory()
            ->forTestMode()
            ->create([
                'user_id' => $user->id,
                'title' => 'Test Reminder',
            ]);

        Http::fake([
            '*' => Http::response(['success' => true], 200)
        ]);

        $this->artisan('reminders:send', ['--test-mode' => true])
            ->assertSuccessful()
            ->expectsOutput("Checking reminders for date: 2024-12-17 time: 14:30:00 (Asia/Jakarta)");

        // Also verify that the timezone is set correctly
        $this->assertEquals('Asia/Jakarta', date_default_timezone_get());
    }
}
