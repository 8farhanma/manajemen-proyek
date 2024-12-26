@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="content-header">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="mb-0">Content Management</h1>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createContentModal">
                <i class="fas fa-plus"></i> Add New Content
            </button>
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
                                </tr>
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
                                                <i class="fas fa-thumbs-up"></i> <span class="likes-count">{{ $content->likes }}</span>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-success comment-btn"
                                                    data-id="{{ $content->id }}"
                                                    onclick="commentContent({{ $content->id }})">
                                                <i class="fas fa-comment"></i> <span class="comments-count">{{ $content->comments }}</span>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-info view-btn"
                                                    data-id="{{ $content->id }}"
                                                    onclick="viewContent({{ $content->id }})">
                                                <i class="fas fa-eye"></i> <span class="views-count">{{ $content->views }}</span>
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
                                    <i class="fas fa-thumbs-up"></i> Likes
                                </label>
                                <input type="number" class="form-control" id="likes" name="likes" min="0" value="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="comments" class="form-label">
                                    <i class="fas fa-comment"></i> Comments
                                </label>
                                <input type="number" class="form-control" id="comments" name="comments" min="0" value="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="views" class="form-label">
                                    <i class="fas fa-eye"></i> Views
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
});

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
