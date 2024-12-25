@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white text-center">
                    <h5 class="mb-0">Solusi Ideal Positif (V+) dan Negatif (V-)</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="width: 5%">No</th>
                                    <th>Solusi Ideal</th>
                                    @foreach($criteria as $criterion)
                                        <th class="text-center">{{ $criterion }}</th>
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
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
