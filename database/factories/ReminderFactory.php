<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Reminder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class ReminderFactory extends Factory
{
    protected $model = Reminder::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->sentence(10),
            'date' => '2024-12-17',
            'time' => '14:30:00',
        ];
    }

    /**
     * Configure the reminder to be due at the current time
     */
    public function dueNow()
    {
        return $this->state(function (array $attributes) {
            $now = Carbon::now();
            return [
                'date' => $now->format('Y-m-d'),
                'time' => $now->format('H:i:00'),
            ];
        });
    }

    /**
     * Configure the reminder to be due in the future
     */
    public function dueLater($minutes = 30)
    {
        return $this->state(function (array $attributes) use ($minutes) {
            $later = Carbon::now()->addMinutes($minutes);
            return [
                'date' => $later->format('Y-m-d'),
                'time' => $later->format('H:i:00'),
            ];
        });
    }

    public function withCustomDateTime($date, $time)
    {
        return $this->state(function (array $attributes) use ($date, $time) {
            return [
                'date' => $date,
                'time' => $time,
            ];
        });
    }

    public function forTestMode()
    {
        return $this->state(function (array $attributes) {
            return [
                'date' => '2024-12-17',
                'time' => '14:30:00',
            ];
        });
    }
}
