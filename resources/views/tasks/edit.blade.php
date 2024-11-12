@extends('layouts.app')

@section('content')
    <div class="container">
        <h2 class="mb-4">Edit Task</h2>

        <form action="{{ route('tasks.update', $task->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" name="title" id="title" class="form-control" value="{{ $task->title }}" required>
                @error('title')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" id="description" class="form-control">{{ $task->description }}</textarea>
                @error('description')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-3">
                <label for="due_date" class="form-label">Due Date</label>
                <input type="date" name="due_date" id="due_date" class="form-control" value="{{ $task->due_date }}">
                @error('due_date')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-3">
                <label for="priority" class="form-label">Priority</label>
                <select name="priority" id="priority" class="form-select" required>
                    <option value="low" {{ $task->priority == 'low' ? 'selected' : '' }}>Low</option>
                    <option value="medium" {{ $task->priority == 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="high" {{ $task->priority == 'high' ? 'selected' : '' }}>High</option>
                </select>
                @error('priority')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-3">
                <label for="user_id" class="form-label">Assign To</label>
                <select name="user_id" id="user_id" class="form-select">
                    <option value="{{ $task->user_id }}">{{ $task->user->name }}</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
                @error('user_id')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status" class="form-select" required>
                    <option value="perencanaan" {{ $task->status == 'perencanaan' ? 'selected' : '' }}>Perencanaan</option>
                    <option value="pembuatan" {{ $task->status == 'pembuatan' ? 'selected' : '' }}>Pembuatan</option>
                    <option value="pengeditan" {{ $task->status == 'pengeditan' ? 'selected' : '' }}>Pengeditan</option>
                    <option value="peninjauan" {{ $task->status == 'peninjauan' ? 'selected' : '' }}>Peninjauan</option>
                    <option value="publikasi" {{ $task->status == 'publikasi' ? 'selected' : '' }}>Publikasi</option>
                </select>
                @error('status')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary">Update Task</button>
        </form>
    </div>
@endsection
