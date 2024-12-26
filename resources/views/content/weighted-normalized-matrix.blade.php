@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white text-center">
                    <h5 class="mb-0">Tabel Keputusan Ternormalisasi dan Terbobot (V)</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="weightedTable" class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="width: 5%">No</th>
                                    <th>Alternatif</th>
                                    <th class="text-center">Like (V1)</th>
                                    <th class="text-center">Comments (V2)</th>
                                    <th class="text-center">Views (V3)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($weightedMatrix as $i => $row)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>{{ $alternatives[$i] }}</td>
                                    @foreach($row as $value)
                                    <td class="text-center">{{ number_format($value, 4) }}</td>
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
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#weightedTable').DataTable({
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
