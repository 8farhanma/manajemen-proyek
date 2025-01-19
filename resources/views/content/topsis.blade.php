@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Perhitungan TOPSIS</h3>
                </div>
                <div class="card-body">
                    <!-- Pembagi Normalisasi -->
                    <h4>1. Pembagi Normalisasi</h4>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th class="bg-primary text-white text-center" style="width: 5%">No</th>
                                    <th class="bg-primary text-white">Keterangan</th>
                                    <th class="text-center bg-primary text-white">Likes</th>
                                    <th class="text-center bg-primary text-white">Comments</th>
                                    <th class="text-center bg-primary text-white">Views</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center">1</td>
                                    <td>Nilai Pembagi</td>
                                    <td class="text-center">{{ number_format($divisors['likes'], 10) }}</td>
                                    <td class="text-center">{{ number_format($divisors['comments'], 10) }}</td>
                                    <td class="text-center">{{ number_format($divisors['views'], 10) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Tabel Ternormalisasi -->
                    <h4>2. Tabel Ternormalisasi</h4>
                    <div class="table-responsive mb-4">
                        <table id="normalizedTable" class="table table-bordered table-hover">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th class="text-center bg-primary text-white" style="width: 5%">No</th>
                                    <th class="bg-primary text-white">Alternatif</th>
                                    <th class="text-center bg-primary text-white">Likes</th>
                                    <th class="text-center bg-primary text-white">Comments</th>
                                    <th class="text-center bg-primary text-white">Views</th>
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

                    <!-- Tabel Ternormalisasi Terbobot -->
                    <h4>3. Tabel Ternormalisasi Terbobot</h4>
                    <div class="table-responsive mb-4">
                        <table id="weightedTable" class="table table-bordered table-hover">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th class="text-center bg-primary text-white" style="width: 5%">No</th>
                                    <th class="bg-primary text-white">Alternatif</th>
                                    <th class="text-center bg-primary text-white">Like (V1)</th>
                                    <th class="text-center bg-primary text-white">Comments (V2)</th>
                                    <th class="text-center bg-primary text-white">Views (V3)</th>
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

                    <!-- Solusi Ideal -->
                    <h4>4. Solusi Ideal Positif dan Negatif</h4>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered table-hover">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th class="text-center bg-primary text-white" style="width: 5%">No</th>
                                    <th class="bg-primary text-white">Solusi Ideal</th>
                                    @foreach($criteria as $criterion)
                                        <th class="text-center bg-primary text-white">{{ $criterion }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center">1</td>
                                    <td>V+</td>
                                    @foreach($idealPositive as $value)
                                        <td class="text-center">{{ number_format($value, 10) }}</td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td class="text-center">2</td>
                                    <td>V-</td>
                                    @foreach($idealNegative as $value)
                                        <td class="text-center">{{ number_format($value, 10) }}</td>
                                    @endforeach
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Jarak Ideal -->
                    <h4>5. Jarak Ideal Positif dan Negatif</h4>
                    <div class="table-responsive mb-4">
                        <table id="measuresTable" class="table table-bordered table-hover">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th class="text-center bg-primary text-white" style="width: 5%">No</th>
                                    <th class="bg-primary text-white">Alternatif</th>
                                    <th class="text-center bg-primary text-white">Nilai Di+</th>
                                    <th class="text-center bg-primary text-white">Nilai Di-</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($alternatives as $index => $alternative)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>{{ $alternative }}</td>
                                    <td class="text-center">{{ number_format($distancePositive[$index], 14) }}</td>
                                    <td class="text-center">{{ number_format($distanceNegative[$index], 14) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Kedekatan Relatif -->
                    <h4>6. Kedekatan Relatif</h4>
                    <div class="table-responsive mb-4">
                        <table id="closenessTable" class="table table-bordered table-hover">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th class="text-center bg-primary text-white" style="width: 5%">No</th>
                                    <th class="bg-primary text-white">Alternatif</th>
                                    <th class="text-center bg-primary text-white">Nilai Ci</th>
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
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#normalizedTable, #weightedTable, #measuresTable, #closenessTable').DataTable({
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
