<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Reminder extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'date',
        'time',
        'user_id'
    ];

    protected $casts = [
        'date' => 'date',
        'time' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function setTimeAttribute($value)
    {
        if (is_string($value)) {
            // If it's just HH:mm format
            if (strlen($value) <= 5) {
                $this->attributes['time'] = $value . ':00';
            } else {
                $this->attributes['time'] = Carbon::parse($value)->format('H:i:s');
            }
        } else {
            $this->attributes['time'] = $value;
        }
    }

    public function getTimeAttribute($value)
    {
        return Carbon::createFromFormat('H:i:s', $value);
    }

    public function scopeForDateAndTime($query, $date, $time)
    {
        $connection = \DB::connection();
        $driver = $connection->getDriverName();

        // Build the query based on the database driver
        $timeFormat = $driver === 'sqlite' 
            ? "strftime('%H:%M', time)"
            : "DATE_FORMAT(time, '%H:%i')";

        return $query
            ->whereDate('date', $date)
            ->whereRaw("{$timeFormat} = ?", [$time]);
    }
}
