@extends('admin.layouts.layout')
@section('content')
<div class="content  d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="subheader py-2 py-lg-4  subheader-solid " id="kt_subheader">
        <div class=" container-fluid  d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <div class="d-flex align-items-center flex-wrap mr-1">
                <div class="d-flex align-items-baseline flex-wrap mr-5">
                    <h5 class="text-dark font-weight-bold my-1 mr-5">Dashboard</h5>
                </div>
            </div>
            @include("admin.elements.quick_links")
        </div>
    </div>
    <div class="d-flex flex-column-fluid">
        <div class="container">
            <!-- Statistics Cards -->
            <div class="row mb-6">
                <!-- Total Users Card -->
                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 mb-4">
    <a href="{{ $routes['total_users'] }}" class="card-link">
        <div class="card card-custom card-stretch bg-primary">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="card-title font-weight-bolder text-white font-size-h2 mb-0 d-block">
                            {{ number_format($totalUsers) }}
                        </span>
                        <span class="font-weight-bold text-white font-size-lg">
                            Total Users
                        </span>
                    </div>
                    <div class="symbol symbol-circle symbol-50" style="background: rgba(255,255,255,0.2)">
                        <span class="symbol-label">
                            <i class="fas fa-users fa-2x text-dark"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </a>
</div>

<!-- Active Users Card -->
<div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 mb-4">
    <a href="{{ $routes['active_users'] }}" class="card-link">
        <div class="card card-custom card-stretch bg-info">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="card-title font-weight-bolder text-white font-size-h2 mb-0 d-block">
                            {{ number_format($activeUsers) }}
                        </span>
                        <span class="font-weight-bold text-white font-size-lg">
                            Active Users
                        </span>
                    </div>
                    <div class="symbol symbol-circle symbol-50" style="background: rgba(255,255,255,0.2)">
                        <span class="symbol-label">
                            <i class="fas fa-user-clock fa-2x text-dark"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </a>
</div>

<!-- Deleted Users Card -->
<div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 mb-4">
    <a href="{{ $routes['deleted_users'] }}" class="card-link">
        <div class="card card-custom card-stretch bg-danger">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="card-title font-weight-bolder text-white font-size-h2 mb-0 d-block">
                            {{ number_format($deletedUsers) }}
                        </span>
                        <span class="font-weight-bold text-white font-size-lg">
                            Deleted Users
                        </span>
                    </div>
                    <div class="symbol symbol-circle symbol-50" style="background: rgba(255,255,255,0.2)">
                        <span class="symbol-label">
                            <i class="fas fa-user-xmark fa-2x text-dark"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </a>
</div>

<!-- Total Festivals Card -->
<div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 mb-4">
    <a href="{{ $routes['festivals'] }}" class="card-link">
        <div class="card card-custom card-stretch bg-success">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="card-title font-weight-bolder text-white font-size-h2 mb-0 d-block">
                            {{ number_format($totalFestivals) }}
                        </span>
                        <span class="font-weight-bold text-white font-size-lg">
                            Total Festivals
                        </span>
                    </div>
                    <div class="symbol symbol-circle symbol-50" style="background: rgba(255,255,255,0.2)">
                        <span class="symbol-label">
                            <i class="fas fa-calendar fa-2x text-dark"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </a>
</div>

<!-- Total Temples Card -->
<div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 mb-4">
    <a href="{{ $routes['temples'] }}" class="card-link">
        <div class="card card-custom card-stretch bg-warning">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="card-title font-weight-bolder text-white font-size-h2 mb-0 d-block">
                            {{ number_format($totalTemples) }}
                        </span>
                        <span class="font-weight-bold text-white font-size-lg">
                            Total Temples
                        </span>
                    </div>
                    <div class="symbol symbol-circle symbol-50" style="background: rgba(255,255,255,0.2)">
                        <span class="symbol-label">
                            <i class="fas fa-place-of-worship fa-2x text-dark"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </a>
</div>
            </div>

            <!-- User Growth Chart -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card card-custom">
                        <div class="card-header">
                            <div class="card-title">
                                <h3 class="card-label">User Registration - {{ $currentYear }}</h3>
                                <small class="text-muted">Monthly user registration statistics</small>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="chart-container" style="position: relative; height:400px; width:100%">
                                <canvas id="userGrowthChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Stats -->
            <div class="row mt-4">
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card card-custom bg-light-info">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="text-muted font-weight-bold">Average Monthly Users</span>
                                    <h3 class="font-weight-bolder mt-2">
                                        {{ number_format(array_sum($monthsData) / count(array_filter($monthsData)) ?: 1, 1) }}
                                    </h3>
                                </div>
                                <div class="symbol symbol-40" style="background: rgba(23, 162, 184, 0.1)">
                                    <span class="symbol-label">
                                        <i class="fas fa-chart-line fa-2x text-info"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card card-custom bg-light-primary">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="text-muted font-weight-bold">Peak Month</span>
                                    <h3 class="font-weight-bolder mt-2">
                                        {{ $monthNames[array_search(max($monthsData), $monthsData)] ?? 'N/A' }}
                                    </h3>
                                    <small class="text-muted">{{ max($monthsData) }} users</small>
                                </div>
                                <div class="symbol symbol-40" style="background: rgba(0, 123, 255, 0.1)">
                                    <span class="symbol-label">
                                        <i class="fas fa-chart-bar fa-2x text-primary"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card card-custom bg-light-success">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="text-muted font-weight-bold">User Retention Rate</span>
                                    <h3 class="font-weight-bolder mt-2">
                                        @php
                                            $retentionRate = $totalUsers > 0 ? (($totalUsers - $deletedUsers) / $totalUsers) * 100 : 0;
                                        @endphp
                                        {{ number_format($retentionRate, 1) }}%
                                    </h3>
                                    <small class="text-muted">{{ $activeUsers }} active / {{ $totalUsers }} total</small>
                                </div>
                                <div class="symbol symbol-40" style="background: rgba(40, 167, 69, 0.1)">
                                    <span class="symbol-label">
                                        <i class="fas fa-percentage fa-2x text-success"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- User Status Chart -->
          
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .card-stretch {
        border-radius: 10px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: none;
    }
    
    .card-stretch:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
    
    .symbol.symbol-circle {
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .symbol-40 {
        width: 40px;
        height: 40px;
    }
    
    .symbol-50 {
        width: 50px;
        height: 50px;
    }
    
    .symbol-label {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
    }
    
    /* Background colors */
    .bg-primary { 
        background: linear-gradient(135deg, #6993FF 0%, #0062FF 100%) !important; 
    }
    
    .bg-info { 
        background: linear-gradient(135deg, #17a2b8 0%, #138496 100%) !important; 
    }
    
    .bg-danger { 
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%) !important; 
    }
    
    .bg-success { 
        background: linear-gradient(135deg, #28a745 0%, #218838 100%) !important; 
    }
    
    .bg-warning { 
        background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%) !important; 
    }
    
    .bg-light-primary { background-color: #F1FAFF !important; }
    .bg-light-info { background-color: #F0F9FF !important; }
    .bg-light-success { background-color: #F0FFF4 !important; }
    
    .chart-container {
        border-radius: 8px;
        padding: 20px;
        background: #fff;
        border: 1px solid #eaeaea;
    }
    
    .card-header {
        border-bottom: 1px solid rgba(0,0,0,.125);
        background: #fff;
    }
    
    /* Icon styling */
    .fa-users, .fa-user-check, .fa-user-slash, .fa-holiday, .fa-torii-gate {
        filter: drop-shadow(0 2px 2px rgba(0,0,0,0.1));
    }
</style>
@endsection

@section('js')
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // User Growth Chart
    const growthCtx = document.getElementById('userGrowthChart').getContext('2d');
    const monthNames = @json($monthNames);
    const monthsData = @json($monthsData);
    const currentYear = '{{ $currentYear }}';
    
    // Create gradient for growth chart
    const growthGradient = growthCtx.createLinearGradient(0, 0, 0, 400);
    growthGradient.addColorStop(0, 'rgba(105, 147, 255, 0.8)');
    growthGradient.addColorStop(1, 'rgba(105, 147, 255, 0.1)');
    
    const growthChart = new Chart(growthCtx, {
        type: 'bar',
        data: {
            labels: monthNames,
            datasets: [{
                label: 'New Users',
                data: monthsData,
                backgroundColor: growthGradient,
                borderColor: '#6993FF',
                borderWidth: 2,
                borderRadius: 6,
                hoverBackgroundColor: '#0062FF',
                hoverBorderWidth: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.parsed.y + ' users';
                        },
                        title: function(context) {
                            return context[0].label + ' ' + currentYear;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Number of Users'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Months'
                    }
                }
            }
        }
    });
    
    // User Status Chart (Pie/Doughnut)
    const statusCtx = document.getElementById('userStatusChart').getContext('2d');
    const activeUsers = {{ $activeUsers }};
    const deletedUsers = {{ $deletedUsers }};
    const totalUsers = {{ $totalUsers }};
    
    const statusChart = new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Active Users', 'Deleted Users'],
            datasets: [{
                data: [activeUsers, deletedUsers],
                backgroundColor: [
                    'rgba(40, 167, 69, 0.8)',
                    'rgba(220, 53, 69, 0.8)'
                ],
                borderColor: [
                    'rgba(40, 167, 69, 1)',
                    'rgba(220, 53, 69, 1)'
                ],
                borderWidth: 2,
                hoverOffset: 15
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.raw || 0;
                            const percentage = Math.round((value / totalUsers) * 100);
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            },
            cutout: '70%'
        }
    });
    
    // Animation for cards
    const cards = document.querySelectorAll('.card-stretch');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, { threshold: 0.1 });
    
    cards.forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        observer.observe(card);
    });
});
</script>

<!-- FontAwesome for icons (if not already included) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endsection