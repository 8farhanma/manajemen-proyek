@extends('layouts.app')

@section('content')
<div class="container-fluid">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="content-header">
        <div class="d-flex justify-content-between align-items-start">
            <h1 class="mb-0">Content Management</h1>
            <div class="d-flex flex-column gap-2">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createContentModal">
                    <i class="bi bi-plus"></i> Tambah konten baru
                </button>
                @php
                    $contentCount = count($allContent);
                    $hasEnoughData = $contentCount > 0;
                    $topsisUrl = $hasEnoughData ? route('topsis') : '#';
                @endphp
                <a href="{{ $topsisUrl }}" class="btn btn-primary {{ !$hasEnoughData ? 'disabled' : '' }}" 
                   @if(!$hasEnoughData) 
                   onclick="event.preventDefault(); alert('Tidak ada data yang cukup untuk perhitungan TOPSIS. Minimal harus ada 1 konten.');"
                   @endif>
                    <i class="bi bi-calculator"></i> Perhitungan Topsis
                </a>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white text-center">
                    <h5 class="mb-0">Tabel Nilai Alternatif</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="alternativeTable" class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="width: 5%">No</th>
                                    <th>Alternatif</th>
                                    <th class="text-center" style="width: 10%">Like</th>
                                    <th class="text-center" style="width: 10%">Comments</th>
                                    <th class="text-center" style="width: 10%">Views</th>
                                    <th class="text-center" style="width: 15%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($allContent as $index => $content)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>{{ $content->title }}</td>
                                    <td class="text-center">{{ $content->likes }}</td>
                                    <td class="text-center">{{ $content->comments }}</td>
                                    <td class="text-center">{{ $content->views }}</td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editContentModal{{ $content->id }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="showDeleteModal({{ $content->id }})">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>

                                <!-- Edit Content Modal -->
                                <div class="modal fade" id="editContentModal{{ $content->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('content.update', $content->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit Content</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label for="title{{ $content->id }}" class="form-label">Title</label>
                                                        <input type="text" class="form-control" id="title{{ $content->id }}" name="title" value="{{ $content->title }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="description{{ $content->id }}" class="form-label">Description</label>
                                                        <textarea class="form-control" id="description{{ $content->id }}" name="description" rows="3">{{ $content->description }}</textarea>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label for="likes{{ $content->id }}" class="form-label">
                                                                    <i class="bi bi-hand-thumbs-up"></i> Likes
                                                                </label>
                                                                <input type="number" class="form-control" id="likes{{ $content->id }}" name="likes" min="0" value="{{ $content->likes }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label for="comments{{ $content->id }}" class="form-label">
                                                                    <i class="bi bi-chat-text"></i> Comments
                                                                </label>
                                                                <input type="number" class="form-control" id="comments{{ $content->id }}" name="comments" min="0" value="{{ $content->comments }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label for="views{{ $content->id }}" class="form-label">
                                                                    <i class="bi bi-eye"></i> Views
                                                                </label>
                                                                <input type="number" class="form-control" id="views{{ $content->id }}" name="views" min="0" value="{{ $content->views }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary">Update Content</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Popular Content</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($popularContent as $content)
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $content->title }}</h5>
                                    <p class="card-text">{{ $content->description }}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-outline-primary like-btn" 
                                                    data-id="{{ $content->id }}" 
                                                    onclick="likeContent({{ $content->id }})">
                                                <i class="bi bi-hand-thumbs-up"></i> <span class="likes-count">{{ $content->likes }}</span>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-success comment-btn"
                                                    data-id="{{ $content->id }}"
                                                    onclick="commentContent({{ $content->id }})">
                                                <i class="bi bi-chat-text"></i> <span class="comments-count">{{ $content->comments }}</span>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-info view-btn"
                                                    data-id="{{ $content->id }}"
                                                    onclick="viewContent({{ $content->id }})">
                                                <i class="bi bi-eye"></i> <span class="views-count">{{ $content->views }}</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <small class="text-muted">TOPSIS Score: {{ number_format($content->topsis_score, 4) }}</small>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Content Modal -->
<div class="modal fade" id="createContentModal" tabindex="-1" aria-labelledby="createContentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('content.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="createContentModalLabel">Create New Content</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="likes" class="form-label">
                                    <i class="bi bi-hand-thumbs-up"></i> Likes
                                </label>
                                <input type="number" class="form-control" id="likes" name="likes" min="0" value="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="comments" class="form-label">
                                    <i class="bi bi-chat-text"></i> Comments
                                </label>
                                <input type="number" class="form-control" id="comments" name="comments" min="0" value="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="views" class="form-label">
                                    <i class="bi bi-eye"></i> Views
                                </label>
                                <input type="number" class="form-control" id="views" name="views" min="0" value="0">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Create Content</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteConfirmationModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus konten ini? Tindakan ini tidak dapat dibatalkan.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Hapus</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('#alternativeTable').DataTable({
        pageLength: 10,
        ordering: true,
        searching: true,
        language: {
            search: "Search:",
            lengthMenu: "Show _MENU_ entries per page",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            }
        }
    });

    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').alert('close');
    }, 5000);
});

let contentIdToDelete = null;
const deleteModal = document.getElementById('deleteConfirmationModal');

function showDeleteModal(id) {
    contentIdToDelete = id;
    $('#deleteConfirmationModal').modal('show');
}

// Add event listener to delete confirmation button
document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    if (contentIdToDelete) {
        deleteContent(contentIdToDelete);
        $('#deleteConfirmationModal').modal('hide');
    }
});

function deleteContent(id) {
    fetch(`/content/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        // Show success message
        const alert = document.createElement('div');
        alert.className = 'alert alert-success alert-dismissible fade show mt-3';
        alert.role = 'alert';
        alert.innerHTML = `
            ${data.message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        const container = document.querySelector('.container-fluid');
        container.insertBefore(alert, container.firstChild);

        // Remove the row from the table
        const table = $('#alternativeTable').DataTable();
        const row = table.row($(`button[onclick="showDeleteModal(${id})"]`).closest('tr'));
        row.remove().draw();

        // Refresh numbering
        table.rows().every(function (rowIdx) {
            this.node().querySelector('td:nth-child(1)').textContent = rowIdx + 1;
        });

        // Auto-hide alert after 5 seconds
        setTimeout(() => {
            $(alert).alert('close');
        }, 5000);
    })
    .catch(error => {
        console.error('Error:', error);
        const alert = document.createElement('div');
        alert.className = 'alert alert-danger alert-dismissible fade show mt-3';
        alert.role = 'alert';
        alert.innerHTML = `
            An error occurred while deleting the content
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        const container = document.querySelector('.container-fluid');
        container.insertBefore(alert, container.firstChild);
        
        // Auto-hide alert after 5 seconds
        setTimeout(() => {
            $(alert).alert('close');
        }, 5000);
    });
}

function likeContent(id) {
    fetch(`/content/${id}/like`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        document.querySelectorAll(`.likes-count[data-id="${id}"]`)
            .forEach(el => el.textContent = data.likes);
    });
}

function commentContent(id) {
    fetch(`/content/${id}/comment`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        document.querySelectorAll(`.comments-count[data-id="${id}"]`)
            .forEach(el => el.textContent = data.comments);
    });
}

function viewContent(id) {
    fetch(`/content/${id}/view`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        document.querySelectorAll(`.views-count[data-id="${id}"]`)
            .forEach(el => el.textContent = data.views);
    });
}
</script>
@endpush
@endsection
