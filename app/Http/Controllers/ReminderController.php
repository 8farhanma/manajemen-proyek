<?php

namespace App\Http\Controllers;

use App\Models\Reminder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReminderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->authorizeResource(Reminder::class, 'reminder');
    }

    public function index()
    {
        try {
            $reminders = Auth::user()->reminders;
            
            // Format the reminders by date
            $remindersByDate = $reminders->groupBy(function($reminder) {
                return $reminder->date;
            })->map(function($dateReminders) {
                return $dateReminders->map(function($reminder) {
                    return [
                        'id' => $reminder->id,
                        'title' => $reminder->title,
                        'description' => $reminder->description,
                        'date' => $reminder->date,
                        'time' => substr($reminder->time, 0, 5), // Format as HH:mm
                    ];
                });
            });

            return view('reminders.index', compact('remindersByDate'));
        } catch (\Exception $e) {
            report($e);
            return back()->withErrors(['error' => 'Failed to load reminders. Please try again.']);
        }
    }

    public function create()
    {
        try {
            $selectedDate = request('date') ?? now()->format('Y-m-d');
            return view('reminders.create', compact('selectedDate'));
        } catch (\Exception $e) {
            report($e);
            return back()->withErrors(['error' => 'Failed to load create form. Please try again.']);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
        ]);

        try {
            Auth::user()->reminders()->create([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'date' => Carbon::parse($validated['date'])->format('Y-m-d'),
                'time' => $validated['time'] . ':00',
            ]);

            return redirect()->route('reminders.index')
                ->with('success', 'Reminder created successfully. You will receive a WhatsApp notification at the specified time.');
        } catch (\Exception $e) {
            report($e);
            return back()->withInput()
                ->withErrors(['error' => 'Failed to create reminder. Please try again.']);
        }
    }

    public function edit(Reminder $reminder)
    {
        try {
            return view('reminders.edit', compact('reminder'));
        } catch (\Exception $e) {
            report($e);
            return back()->withErrors(['error' => 'Failed to load edit form. Please try again.']);
        }
    }

    public function update(Request $request, Reminder $reminder)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
        ]);

        try {
            $reminder->update([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'date' => Carbon::parse($validated['date'])->format('Y-m-d'),
                'time' => $validated['time'] . ':00',
            ]);

            return redirect()->route('reminders.index')
                ->with('success', 'Reminder updated successfully.');
        } catch (\Exception $e) {
            report($e);
            return back()->withInput()
                ->withErrors(['error' => 'Failed to update reminder. Please try again.']);
        }
    }

    public function destroy(Reminder $reminder)
    {
        try {
            $reminder->delete();
            return redirect()->route('reminders.index')
                ->with('success', 'Reminder deleted successfully.');
        } catch (\Exception $e) {
            report($e);
            return back()->withErrors(['error' => 'Failed to delete reminder. Please try again.']);
        }
    }
}
