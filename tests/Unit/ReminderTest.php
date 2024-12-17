<?php

namespace Tests\Unit;

use App\Models\Reminder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class ReminderTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_belongs_to_a_user()
    {
        $user = User::factory()->create();
        $reminder = Reminder::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $reminder->user);
        $this->assertEquals($user->id, $reminder->user->id);
    }

    /** @test */
    public function it_can_format_time_correctly()
    {
        $reminder = Reminder::factory()->create([
            'time' => '14:30:00'
        ]);

        $this->assertEquals('14:30', $reminder->time->format('H:i'));
    }

    /** @test */
    public function it_can_get_reminders_for_specific_date_and_time()
    {
        $user = User::factory()->create();
        $currentDate = now()->format('Y-m-d');
        $currentTime = '14:30';

        // Create a reminder for current date and time
        $dueReminder = Reminder::factory()->create([
            'user_id' => $user->id,
            'date' => $currentDate,
            'time' => $currentTime . ':00'
        ]);

        // Create a reminder for different time
        $futureReminder = Reminder::factory()->create([
            'user_id' => $user->id,
            'date' => $currentDate,
            'time' => '15:30:00'
        ]);

        $dueReminders = Reminder::forDateAndTime($currentDate, $currentTime)->get();

        $this->assertCount(1, $dueReminders);
        $this->assertTrue($dueReminders->contains($dueReminder));
        $this->assertFalse($dueReminders->contains($futureReminder));
    }
}
