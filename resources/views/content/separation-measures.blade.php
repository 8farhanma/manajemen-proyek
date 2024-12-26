@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white text-center">
                    <h5 class="mb-0">Jarak Ideal Positif (Di+) dan Negatif (Di-)</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="measuresTable" class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="width: 5%">No</th>
                                    <th>Alternatif</th>
                                    <th class="text-center">Nilai Di+</th>
                                    <th class="text-center">Nilai Di-</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($alternatives as $index => $alternative)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>{{ $alternative['name'] }}</td>
                                    <td class="text-center">{{ number_format($distancePositive[$index], 14) }}</td>
                                    <td class="text-center">{{ number_format($distanceNegative[$index], 14) }}</td>
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
    $('#measuresTable').DataTable({
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
