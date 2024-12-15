<?php

namespace App\Http\Controllers;

use App\Models\Reminder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReminderController extends Controller
{
    public function index()
    {
        $reminders = Reminder::all();
        $remindersByDate = $reminders->groupBy('date');
        return view('reminders.index', compact('remindersByDate'));
    }

    public function create()
    {
        $selectedDate = request('date') ?? now()->format('Y-m-d');
        return view('reminders.create', compact('selectedDate'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'nullable|date',
            'time' => 'nullable|date_format:H:i',
        ]);

        Auth::user()->reminders()->create($request->all());

        return redirect()->route('reminders.index')->with('success', 'Reminder created successfully.');
    }

    public function edit(Reminder $reminder)
    {
        return view('reminders.edit', compact('reminder'));
    }

    public function update(Request $request, Reminder $reminder)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'nullable|date',
            'time' => 'nullable|date_format:H:i',
        ]);

        $reminder->update($request->all());

        return redirect()->route('reminders.index')->with('success', 'Reminder updated successfully.');
    }

    public function destroy(Reminder $reminder)
    {
        $reminder->delete();
        return redirect()->route('reminders.index')->with('success', 'Reminder deleted successfully.');
    }
}
