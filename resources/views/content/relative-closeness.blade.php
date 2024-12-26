@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white text-center">
                    <h5 class="mb-0">Kedekatan Relatif (Ci)</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="closenessTable" class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="width: 5%">No</th>
                                    <th>Alternatif</th>
                                    <th class="text-center">Nilai Ci</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($results as $index => $result)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>{{ $result['alternative'] }}</td>
                                    <td class="text-center">{{ number_format($result['score'], 12) }}</td>
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
    $('#closenessTable').DataTable({
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
