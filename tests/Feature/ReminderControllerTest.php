<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Reminder;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReminderControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $testDate;
    protected $testTime;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['phone_number' => '1234567890']);
        $this->actingAs($this->user);

        // Set fixed test date and time
        $this->testDate = '2024-12-17';
        $this->testTime = '14:30:00';
    }

    /** @test */
    public function user_can_create_a_reminder()
    {
        $response = $this->post(route('reminders.store'), [
            'title' => 'Test Reminder',
            'description' => 'This is a test reminder',
            'date' => $this->testDate,
            'time' => '14:30',
        ]);

        $response->assertRedirect(route('reminders.index'));
        $this->assertDatabaseHas('reminders', [
            'title' => 'Test Reminder',
            'description' => 'This is a test reminder',
            'date' => $this->testDate,
            'time' => $this->testTime,
            'user_id' => $this->user->id,
        ]);
    }

    /** @test */
    public function user_can_view_their_reminders()
    {
        $reminder = Reminder::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Test Reminder',
            'date' => $this->testDate,
            'time' => $this->testTime,
        ]);

        $response = $this->get(route('reminders.index'));

        $response->assertStatus(200);
        $response->assertSee('Test Reminder');
    }

    /** @test */
    public function user_can_update_their_reminder()
    {
        $reminder = Reminder::factory()->create([
            'user_id' => $this->user->id,
            'date' => $this->testDate,
            'time' => $this->testTime,
        ]);

        $newTime = '15:30:00';
        $response = $this->put(route('reminders.update', $reminder), [
            'title' => 'Updated Reminder',
            'description' => 'This is an updated reminder',
            'date' => $this->testDate,
            'time' => '15:30',
        ]);

        $response->assertRedirect(route('reminders.index'));
        $this->assertDatabaseHas('reminders', [
            'id' => $reminder->id,
            'title' => 'Updated Reminder',
            'description' => 'This is an updated reminder',
            'date' => $this->testDate,
            'time' => $newTime,
        ]);
    }

    /** @test */
    public function user_can_delete_their_reminder()
    {
        $reminder = Reminder::factory()->create([
            'user_id' => $this->user->id,
            'date' => $this->testDate,
            'time' => $this->testTime,
        ]);

        $response = $this->delete(route('reminders.destroy', $reminder));

        $response->assertRedirect(route('reminders.index'));
        $this->assertDatabaseMissing('reminders', [
            'id' => $reminder->id
        ]);
    }

    /** @test */
    public function user_cannot_access_others_reminders()
    {
        $otherUser = User::factory()->create();
        $otherReminder = Reminder::factory()->create([
            'user_id' => $otherUser->id,
            'date' => $this->testDate,
            'time' => $this->testTime,
        ]);

        $response = $this->get(route('reminders.edit', $otherReminder));
        $response->assertStatus(403);

        $response = $this->put(route('reminders.update', $otherReminder), [
            'title' => 'Hacked Reminder',
            'date' => $this->testDate,
            'time' => $this->testTime,
        ]);
        $response->assertStatus(403);

        $response = $this->delete(route('reminders.destroy', $otherReminder));
        $response->assertStatus(403);
    }
}
