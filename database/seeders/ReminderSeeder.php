<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Reminder;
use Carbon\Carbon;

class ReminderSeeder extends Seeder
{
    public function run()
    {
        // Get current time
        $now = Carbon::now('Asia/Jakarta');
        
        // Create a reminder for 2 minutes from now
        Reminder::create([
            'user_id' => 1, // Admin user
            'title' => 'Test Reminder 1',
            'description' => 'This is a test reminder that should trigger soon.',
            'date' => $now->format('Y-m-d'),
            'time' => $now->addMinutes(2)->format('H:i:s'),
            'notification_sent' => false
        ]);

        // Create another reminder for 5 minutes from now
        Reminder::create([
            'user_id' => 1,
            'title' => 'Test Reminder 2',
            'description' => 'This is another test reminder.',
            'date' => $now->format('Y-m-d'),
            'time' => $now->addMinutes(3)->format('H:i:s'),
            'notification_sent' => false
        ]);
    }
}
