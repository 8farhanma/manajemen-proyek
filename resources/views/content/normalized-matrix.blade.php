@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white text-center">
                    <h5 class="mb-0">Tabel Keputusan Ternormalisasi</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="normalizedTable" class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="width: 5%">No</th>
                                    <th>Alternatif</th>
                                    <th class="text-center">Likes</th>
                                    <th class="text-center">Comments</th>
                                    <th class="text-center">Views</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($normalizedMatrix as $alternative => $values)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>{{ $alternative }}</td>
                                    @foreach($values as $value)
                                    <td class="text-center">{{ number_format($value, 10) }}</td>
                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
$(document).ready(function() {
    $('#normalizedTable').DataTable({
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
</script>
@endpush
@endsection
