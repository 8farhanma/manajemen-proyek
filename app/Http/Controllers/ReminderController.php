<?php

namespace App\Http\Controllers;

use App\Models\Reminder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ReminderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Add admin middleware only to admin actions
        $this->middleware('member')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index()
    {
        try {
            // For members, show all reminders
            $reminders = Reminder::all();
            
            // Debug: Explicitly format dates
            $remindersByDate = [];
            foreach ($reminders as $reminder) {
                $formattedDate = $reminder->date instanceof \Carbon\Carbon 
                    ? $reminder->date->format('Y-m-d') 
                    : (is_string($reminder->date) 
                        ? $reminder->date 
                        : date('Y-m-d', strtotime($reminder->date)));
                
                if (!isset($remindersByDate[$formattedDate])) {
                    $remindersByDate[$formattedDate] = [];
                }
                
                $remindersByDate[$formattedDate][] = [
                    'id' => $reminder->id,
                    'title' => $reminder->title,
                    'description' => $reminder->description,
                    'date' => $formattedDate,
                    'time' => substr($reminder->time, 0, 5), // Format as HH:mm
                ];
            }

            return view('reminders.index', compact('remindersByDate'));
        } catch (\Exception $e) {
            report($e);
            return back()->withErrors(['error' => 'Failed to load reminders: ' . $e->getMessage()]);
        }
    }

    public function create()
    {
        // Enhanced logging for role checking
        \Illuminate\Support\Facades\Log::info('Reminder Create Access Attempt', [
            'user_id' => auth()->id(),
            'user_email' => auth()->user()->email,
            'user_role' => auth()->user()->role,
            'is_member' => auth()->user()->isMember(),
        ]);

        // Explicit role check
        if (!auth()->user()->isMember()) {
            return redirect()->route('reminders.index')
                ->withErrors(['error' => 'You do not have permission to create reminders.']);
        }

        // Ensure selected date is always set
        $selectedDate = request('date') ?? now()->format('Y-m-d');
        
        return view('reminders.create', compact('selectedDate'));
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
            // Tambahkan debug logging
            Log::info('Creating reminder with data:', $validated);

            $reminder = Reminder::create([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'date' => Carbon::parse($validated['date'])->format('Y-m-d'),
                'time' => $validated['time'] . ':00', // Pastikan format waktu lengkap
                'user_id' => Auth::id(),
            ]);

            // Tambahkan debug logging
            Log::info('Reminder created:', $reminder->toArray());

            return redirect()->route('reminders.index')
                ->with('success', 'Reminder created successfully. All users will receive a WhatsApp notification at the specified time.');
        } catch (\Exception $e) {
            // Tambahkan error logging
            Log::error('Failed to create reminder: ' . $e->getMessage());
            
            return back()->withInput()
                ->withErrors(['error' => 'Failed to create reminder. Please try again. Error: ' . $e->getMessage()]);
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
