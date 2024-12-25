@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white text-center">
                    <h5 class="mb-0">Pembagi Normalisasi</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="width: 5%">No</th>
                                    <th>Keterangan</th>
                                    <th class="text-center">Likes</th>
                                    <th class="text-center">Comments</th>
                                    <th class="text-center">Views</th>
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
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
