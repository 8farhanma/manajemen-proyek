@extends('layouts.app')

@section('title', 'Reminders')

@push('styles')
<link href="{{ asset('css/calendar.css') }}" rel="stylesheet">
<style>
    .calendar-wrapper {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.12);
        margin-bottom: 20px;
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center bg-white shadow-sm p-3 rounded mb-4">
        <h2>Reminders</h2>
        @if(Auth::user()->isAdmin())
            <a href="{{ route('reminders.create') }}" class="btn btn-primary">Add Reminder</a>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="calendar-wrapper">
        <div class="calendar-container" data-reminders="{{ json_encode($remindersByDate) }}"></div>
    </div>
</div>

<!-- Reminder Modal -->
<div class="modal fade reminder-modal" id="reminderModal" tabindex="-1" aria-labelledby="reminderModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reminderModalLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="reminder-detail">
                    <label>Description</label>
                    <div class="modal-description"></div>
                </div>
                <div class="reminder-detail">
                    <label>Date</label>
                    <div class="modal-date"></div>
                </div>
                <div class="reminder-detail">
                    <label>Time</label>
                    <div class="modal-time"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                @if(Auth::user()->isAdmin())
                    <div class="action-buttons">
                        <form id="deleteReminderForm" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger delete-reminder">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </form>
                        <a href="#" class="btn btn-warning edit-reminder">
                            <i class="bi bi-pencil-square"></i> Edit
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
// Debug: Log reminders data
console.log('Reminders Data:', {!! json_encode($remindersByDate) !!});

// Verify data on page load
document.addEventListener('DOMContentLoaded', () => {
    const calendarContainer = document.querySelector('.calendar-container');
    if (calendarContainer) {
        const remindersData = JSON.parse(calendarContainer.dataset.reminders);
        console.log('Parsed Reminders Data:', remindersData);
    }
});
</script>

@endsection

@push('scripts')
<script>
// Add admin class to body if user is admin
document.body.classList.toggle('is-admin', {{ Auth::user()->isAdmin() ? 'true' : 'false' }});
</script>
<script src="{{ asset('js/calendar.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('reminderModal');
    if (modal) {
        modal.addEventListener('show.bs.modal', function (event) {
            const reminder = JSON.parse(event.relatedTarget.dataset.reminder);
            
            // Update modal content
            modal.querySelector('.modal-title').textContent = reminder.title;
            modal.querySelector('.modal-description').textContent = reminder.description || 'No description';
            modal.querySelector('.modal-date').textContent = reminder.date;
            modal.querySelector('.modal-time').textContent = reminder.time;

            @if(Auth::user()->isAdmin())
            // Update action buttons
            const editButton = modal.querySelector('.edit-reminder');
            const deleteForm = modal.querySelector('#deleteReminderForm');
            
            editButton.href = `/reminders/${reminder.id}/edit`;
            deleteForm.action = `/reminders/${reminder.id}`;

            // Handle delete confirmation
            const deleteButton = modal.querySelector('.delete-reminder');
            if (deleteButton) {
                deleteButton.addEventListener('click', function(e) {
                    if (!confirm('Are you sure you want to delete this reminder?')) {
                        e.preventDefault();
                    }
                });
            }
            @endif
        });
    }
});
</script>
@endpush
