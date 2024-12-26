@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">TOPSIS Score Analysis</h3>
                </div>
                <div class="card-body">
                    <canvas id="topsisChart" style="height: 400px;"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('topsisChart').getContext('2d');
    const chartData = @json($chartData);
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: chartData.map(item => item.name),
            datasets: [{
                label: 'TOPSIS Score',
                data: chartData.map(item => item.score),
                backgroundColor: chartData.map((_, index) => {
                    const value = index / chartData.length;
                    return `rgba(54, 162, 235, ${1 - value * 0.6})`; // Gradient blue
                }),
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                title: {
                    display: true,
                    text: 'Alternative Rankings by TOPSIS Score'
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'TOPSIS Score'
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Alternatives'
                    }
                }
            }
        }
    });
});
</script>
@endpush
@endsection
