<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\Task;
use App\Models\Routine;
use App\Models\Note;
use App\Models\File;
use App\Models\Reminder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get counts
        $tasksCount = $user->tasks()->count();
        $routinesCount = $user->routines()->whereDate('start_time', now())->count();
        $notesCount = $user->notes()->count();
        $filesCount = $user->files()->count();

        // Get recent items
        $recentTasks = $user->tasks()->latest()->take(5)->get();
        $todayRoutines = $user->routines()->whereDate('start_time', now())->take(5)->get();
        $recentNotes = $user->notes()->latest()->take(5)->get();
        $upcomingReminders = $user->reminders()
            ->where('date', '>=', now())
            ->orderBy('date')
            ->orderBy('time')
            ->take(5)
            ->get();

        // Get TOPSIS analysis data
        $contents = Content::getPopularContent();
        $chartData = $contents->map(function ($content) {
            return [
                'name' => $content->title,
                'score' => $content->topsis_score,
            ];
        })->sortByDesc('score')->values();

        return view('dashboard', compact(
            'tasksCount',
            'routinesCount',
            'notesCount',
            'filesCount',
            'recentTasks',
            'todayRoutines',
            'recentNotes',
            'upcomingReminders',
            'chartData'
        ));
    }
}
