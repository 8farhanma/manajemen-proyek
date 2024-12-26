@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">TOPSIS Score Trend Over Time</h3>
                </div>
                <div class="card-body">
                    <canvas id="topsisLineChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Criteria Performance Comparison</h3>
                </div>
                <div class="card-body">
                    <canvas id="radarChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">TOPSIS Score Rankings</h3>
                </div>
                <div class="card-body">
                    <canvas id="topsisChart" style="height: 400px;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Content Quality Distribution</h3>
                </div>
                <div class="card-body">
                    <canvas id="qualityPieChart" style="height: 400px;"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Radar Chart
    const radarCtx = document.getElementById('radarChart').getContext('2d');
    const radarData = @json($radarChartData);
    
    new Chart(radarCtx, {
        type: 'radar',
        data: radarData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            elements: {
                line: {
                    borderWidth: 2
                }
            },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12,
                        font: {
                            size: 11
                        }
                    }
                },
                title: {
                    display: true,
                    text: 'Top 5 Content Performance by Criteria'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.dataset.label || '';
                            const value = context.raw.toFixed(2);
                            const criteriaName = radarData.labels[context.dataIndex];
                            return `${label}: ${value} (${criteriaName})`;
                        }
                    }
                }
            },
            scales: {
                r: {
                    beginAtZero: true,
                    max: 1,
                    ticks: {
                        stepSize: 0.2
                    }
                }
            }
        }
    });

    // Line Chart
    const lineCtx = document.getElementById('topsisLineChart').getContext('2d');
    const lineData = @json($lineChartData);
    
    new Chart(lineCtx, {
        type: 'line',
        data: {
            labels: lineData.labels,
            datasets: lineData.datasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index'
            },
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'TOPSIS Score Trend'
                },
                tooltip: {
                    callbacks: {
                        afterBody: function(context) {
                            const date = lineData.labels[context[0].dataIndex];
                            const contents = lineData.contentsByDate[date];
                            if (!contents) return '';
                            
                            return '\nContent on this date:\n' + contents.map(content => 
                                `${content.title}: ${content.score.toFixed(3)}`
                            ).join('\n');
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 1,
                    title: {
                        display: true,
                        text: 'TOPSIS Score'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Date'
                    }
                }
            }
        }
    });

    // Bar Chart
    const barCtx = document.getElementById('topsisChart').getContext('2d');
    const chartData = @json($chartData);
    
    new Chart(barCtx, {
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
                    max: 1
                }
            }
        }
    });

    // Pie Chart
    const pieCtx = document.getElementById('qualityPieChart').getContext('2d');
    const pieData = @json($pieChartData);
    
    new Chart(pieCtx, {
        type: 'pie',
        data: {
            labels: pieData.labels,
            datasets: [{
                data: pieData.data,
                backgroundColor: [
                    'rgba(75, 192, 192, 0.8)',  // Excellent - Teal
                    'rgba(54, 162, 235, 0.8)',  // Good - Blue
                    'rgba(255, 206, 86, 0.8)',  // Average - Yellow
                    'rgba(255, 99, 132, 0.8)',  // Poor - Red
                ],
                borderColor: 'white',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                title: {
                    display: true,
                    text: 'Content Quality Distribution'
                }
            }
        }
    });
});
</script>
@endpush
@endsection
