@extends('layouts.app')
@section('title')
    {{ $project->name }} - Tasks
@endsection
@section('content')
    <style>
        .kanban-container {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 1rem;
            padding: 1rem;
            min-height: calc(100vh - 200px);
        }
        
        .kanban-column {
            background: #f8f9fa;
            border-radius: 0.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .kanban-list {
            padding: 1rem;
            min-height: 100px;
            max-height: calc(100vh - 300px);
            overflow-y: auto;
        }

        .kanban-item {
            cursor: move;
        }

        /* Custom scrollbar untuk tampilan yang lebih baik */
        .kanban-list::-webkit-scrollbar {
            width: 6px;
        }

        .kanban-list::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        .kanban-list::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 3px;
        }

        .kanban-list::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Responsive design */
        @media (max-width: 1400px) {
            .kanban-container {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 992px) {
            .kanban-container {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .kanban-container {
                grid-template-columns: 1fr;
            }
        }

        .card-title {
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .card-text {
            font-size: 0.8rem;
            margin-bottom: 0.5rem;
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
        }
    </style>

    <div class="container-fluid px-4">
        <div class="bg-white align-items-center mb-4 shadow-sm p-3 rounded">
            <h2 class="text-center">{{ $project->name }} - Tasks</h2>
        </div>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="kanban-container">
            <div class="kanban-column">
                <div class="d-flex justify-content-between bg-success text-white shadow-sm align-items-center px-3 py-2 rounded-top">
                    <h4 class="text-white fw-bolder m-0">Perencanaan</h4>
                    @if(Auth::user()->isMember())
                    <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#createTaskModal"
                        data-status="perencanaan" style="padding-top: 0.5rem; padding-bottom: 0.5rem;">+</button>
                    @endif
                </div>

                <div class="kanban-list" id="perencanaan">
                    @foreach ($tasks['perencanaan'] ?? [] as $task)
                        <div class="card mb-3 kanban-item" data-id="{{ $task->id }}" draggable="true">
                            <div class="card-body">
                                <h5 class="card-title">{{ $task->title }}</h5>
                                <p class="card-text">{{ $task->description }}</p>
                                <a href="{{ route('tasks.show', $task->id) }}" class="btn btn-success btn-sm">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="kanban-column">
                <div class="d-flex justify-content-between bg-primary text-white shadow-sm align-items-center px-3 py-2 rounded-top">
                    <h4 class="text-white fw-bolder m-0">Pembuatan</h4>
                    @if(Auth::user()->isMember())
                    <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#createTaskModal"
                        data-status="pembuatan" style="padding-top: 0.5rem; padding-bottom: 0.5rem;">+</button>
                    @endif
                </div>

                <div class="kanban-list" id="pembuatan">
                    @foreach ($tasks['pembuatan'] ?? [] as $task)
                        <div class="card mb-3 kanban-item" data-id="{{ $task->id }}" draggable="true">
                            <div class="card-body">
                                <h5 class="card-title">{{ $task->title }}</h5>
                                <p class="card-text">{{ $task->description }}</p>
                                <a href="{{ route('tasks.show', $task->id) }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="kanban-column">
                <div class="d-flex justify-content-between bg-info text-white shadow-sm align-items-center px-3 py-2 rounded-top">
                    <h4 class="text-white fw-bolder m-0">Pengeditan</h4>
                    @if(Auth::user()->isMember())
                    <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#createTaskModal"
                        data-status="pengeditan" style="padding-top: 0.5rem; padding-bottom: 0.5rem;">+</button>
                    @endif
                </div>

                <div class="kanban-list" id="pengeditan">
                    @foreach ($tasks['pengeditan'] ?? [] as $task)
                        <div class="card mb-3 kanban-item" data-id="{{ $task->id }}" draggable="true">
                            <div class="card-body">
                                <h5 class="card-title">{{ $task->title }}</h5>
                                <p class="card-text">{{ $task->description }}</p>
                                <a href="{{ route('tasks.show', $task->id) }}" class="btn btn-info btn-sm">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="kanban-column">
                <div class="d-flex justify-content-between bg-warning text-white shadow-sm align-items-center px-3 py-2 rounded-top">
                    <h4 class="text-white fw-bolder m-0">Peninjauan</h4>
                    @if(Auth::user()->isMember())
                    <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#createTaskModal"
                        data-status="peninjauan" style="padding-top: 0.5rem; padding-bottom: 0.5rem;">+</button>
                    @endif
                </div>

                <div class="kanban-list" id="peninjauan">
                    @foreach ($tasks['peninjauan'] ?? [] as $task)
                        <div class="card mb-3 kanban-item" data-id="{{ $task->id }}" draggable="true">
                            <div class="card-body">
                                <h5 class="card-title">{{ $task->title }}</h5>
                                <p class="card-text">{{ $task->description }}</p>
                                <a href="{{ route('tasks.show', $task->id) }}" class="btn btn-warning btn-sm">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="kanban-column">
                <div class="d-flex justify-content-between bg-danger text-white shadow-sm align-items-center px-3 py-2 rounded-top">
                    <h4 class="text-white fw-bolder m-0">Publikasi</h4>
                    @if(Auth::user()->isMember())
                    <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#createTaskModal"
                        data-status="publikasi" style="padding-top: 0.5rem; padding-bottom: 0.5rem;">+</button>
                    @endif
                </div>

                <div class="kanban-list" id="publikasi">
                    @foreach ($tasks['publikasi'] ?? [] as $task)
                        <div class="card mb-3 kanban-item" data-id="{{ $task->id }}" draggable="true">
                            <div class="card-body">
                                <h5 class="card-title">{{ $task->title }}</h5>
                                <p class="card-text">{{ $task->description }}</p>
                                <a href="{{ route('tasks.show', $task->id) }}" class="btn btn-danger btn-sm">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Create Task Modal -->
        <div class="modal fade" id="createTaskModal" tabindex="-1" aria-labelledby="createTaskModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('projects.tasks.store', $project->id) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="createTaskModalLabel">Create Task</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="title" class="form-label">Title</label>
                                <input type="text" name="title" id="title" class="form-control" required>
                                @error('title')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea name="description" id="description" class="form-control"></textarea>
                                @error('description')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="due_date" class="form-label">Due Date</label>
                                <input type="date" name="due_date" id="due_date" class="form-control">
                                @error('due_date')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <!-- <div class="mb-3">
                                <label for="user_id" class="form-label">Assign To</label>
                                <select name="user_id" id="user_id" class="form-select">
                                    <option value="{{ auth()->user()->id }}">Self</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div> -->
                            <input type="hidden" name="status" id="task_status">

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Create Task</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
            const kanbanItems = document.querySelectorAll('.kanban-item');
            const kanbanLists = document.querySelectorAll('.kanban-list');
            const createTaskModal = document.getElementById('createTaskModal');
            const taskStatusInput = document.getElementById('task_status');

            createTaskModal.addEventListener('show.bs.modal', function(event) {
                var button = event.relatedTarget;
                var status = button.getAttribute('data-status');
                taskStatusInput.value = status;
            });

            kanbanItems.forEach(item => {
                item.addEventListener('dragstart', handleDragStart);
                item.addEventListener('dragend', handleDragEnd);
            });

            kanbanLists.forEach(list => {
                list.addEventListener('dragover', handleDragOver);
                list.addEventListener('drop', handleDrop);
            });

            function handleDragStart(e) {
                e.dataTransfer.setData('text/plain', e.target.dataset.id);
                setTimeout(() => {
                    e.target.classList.add('invisible');
                }, 0);
            }

            function handleDragEnd(e) {
                e.target.classList.remove('invisible');
            }

            function handleDragOver(e) {
                e.preventDefault();
            }

            function handleDrop(e) {
                e.preventDefault();
                const id = e.dataTransfer.getData('text');
                const draggableElement = document.querySelector(`.kanban-item[data-id='${id}']`);
                const dropzone = e.target.closest('.kanban-list');
                dropzone.appendChild(draggableElement);

                const status = dropzone.id;

                updateTaskStatus(id, status);
            }

            function updateTaskStatus(id, status) {
                fetch(`/tasks/${id}/update-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        status
                    })
                }).then(response => {
                    if (!response.ok) {
                        throw new Error('Failed to update task status');
                    }
                    return response.json();
                }).then(data => {
                    console.log('Task status updated:', data);
                }).catch(error => {
                    console.error('Error:', error);
                });
            }
        });
    </script>
@endsection
