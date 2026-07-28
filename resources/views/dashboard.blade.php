@extends('layouts.mantis')

@push('styles')
    <link rel="stylesheet" href="{{ asset('template/dist/assets/css/plugins/flatpickr.min.css') }}">
@endpush

@section('header')
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h5 class="m-b-10">Dashboard Overview</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item" aria-current="page">Dashboard</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        {{-- FILTER SECTION --}}
        <div class="col-md-12">
            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('dashboard') }}" id="filterForm">
                        <div class="row align-items-end">
                            <div class="col-md-4">
                                <label class="form-label">Date Range</label>
                                <input type="text" class="form-control" id="dateRangePicker"
                                    placeholder="Select date range" readonly>
                                <input type="hidden" name="start_date" id="start_date" value="{{ $startDate }}">
                                <input type="hidden" name="end_date" id="end_date" value="{{ $endDate }}">
                            </div>
                            @if(Auth::user()->hasRole('Super Admin') || Auth::user()->restaurants->count() > 1)
                            <div class="col-md-4">
                                <label class="form-label">Restaurant</label>
                                <select class="form-select" name="restaurant_id" id="restaurant_id">
                                    <option value="">All Restaurants</option>
                                    @foreach($allRestaurants as $resto)
                                        <option value="{{ $resto->id }}"
                                            {{ $restaurantFilter == $resto->id ? 'selected' : '' }}>
                                            {{ $resto->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="ti ti-filter me-1"></i> Apply Filter
                                </button>
                                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                                    <i class="ti ti-refresh me-1"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- PERFORMANCE SECTION (TABS VIEW) --}}
        <div class="col-md-12">
            <div class="card">
                <div class="card-header p-0 mx-3 mt-3 border-0">
                    <ul class="nav nav-tabs profile-tabs" id="myTab" role="tablist">
                        {{-- Tab 1: Overview --}}
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active pb-2" id="overview-tab" data-bs-toggle="tab" href="#overview"
                                role="tab" aria-selected="true">
                                <i class="ti ti-chart-pie me-2"></i> Overview
                            </a>
                        </li>

                        {{-- Tab 2: Outlet Details (Hanya muncul jika Multi-Resto) --}}
                        @if (Auth::user()->hasRole('Super Admin') || Auth::user()->restaurants->count() > 1)
                            <li class="nav-item" role="presentation">
                                <a class="nav-link pb-2" id="breakdown-tab" data-bs-toggle="tab" href="#breakdown"
                                    role="tab" aria-selected="false">
                                    <i class="ti ti-list-details me-2"></i> Outlet Details
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="myTabContent">

                        {{-- KONTEN TAB 1: OVERVIEW --}}
                        <div class="tab-pane fade show active" id="overview" role="tabpanel"
                            aria-labelledby="overview-tab">
                            @php
                                $overallBudgetPercentage = $monthlyBudget > 0 ? ($mtdRevenue / $monthlyBudget) * 100 : 0;
                            @endphp
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="bg-light-primary rounded p-4 border border-primary-subtle h-100">
                                        <h5 class="text-primary mb-3">Performance vs Budget ({{ $periodLabel }})</h5>
                                        <div class="d-flex justify-content-between align-items-end mb-2">
                                            <div>
                                                <h3 class="mb-0 fw-bold text-dark">Rp {{ number_format($mtdRevenue, 0, ',', '.') }}</h3>
                                                <span class="text-muted small">/ Budget: Rp {{ number_format($monthlyBudget, 0, ',', '.') }}</span>
                                            </div>
                                            <div class="text-end">
                                                <h4 class="mb-0 {{ $overallBudgetPercentage >= 100 ? 'text-success' : ($overallBudgetPercentage >= 80 ? 'text-warning' : 'text-danger') }}">
                                                    {{ number_format($overallBudgetPercentage, 1) }}%
                                                </h4>
                                            </div>
                                        </div>
                                        <div class="progress" style="height: 12px;">
                                            <div class="progress-bar {{ $overallBudgetPercentage >= 100 ? 'bg-success' : ($overallBudgetPercentage >= 80 ? 'bg-warning' : 'bg-danger') }}"
                                                role="progressbar" style="width: {{ min($overallBudgetPercentage, 100) }}%">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="bg-light-info rounded p-4 border border-info-subtle h-100">
                                        <h5 class="text-info mb-3">Performance vs Forecast ({{ $periodLabel }})</h5>
                                        <div class="d-flex justify-content-between align-items-end mb-2">
                                            <div>
                                                <h3 class="mb-0 fw-bold text-dark">Rp {{ number_format($mtdRevenue, 0, ',', '.') }}</h3>
                                                <span class="text-muted small">/ Forecast: Rp {{ number_format($monthlyTarget, 0, ',', '.') }}</span>
                                            </div>
                                            <div class="text-end">
                                                <h4 class="mb-0 {{ $achievementPercent >= 100 ? 'text-success' : ($achievementPercent >= 80 ? 'text-warning' : 'text-danger') }}">
                                                    {{ number_format($achievementPercent, 1) }}%
                                                </h4>
                                            </div>
                                        </div>
                                        <div class="progress" style="height: 12px;">
                                            <div class="progress-bar {{ $achievementPercent >= 100 ? 'bg-success' : ($achievementPercent >= 80 ? 'bg-warning' : 'bg-danger') }}"
                                                role="progressbar" style="width: {{ min($achievementPercent, 100) }}%">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- MTD STATS SECTION --}}
                            <div class="mt-4 pt-3 border-top">
                                <h6 class="fw-bold mb-3 text-muted"><i class="ti ti-calendar-stats text-primary me-1"></i> Month-to-Date (MTD) Analytics</h6>
                                
                                {{-- COVER, FOOD, BEVERAGE --}}
                                <div class="row g-3 mb-3">
                                    <div class="col-12 col-md-4">
                                        <div class="p-3 bg-light rounded border h-100 d-flex flex-column justify-content-center align-items-center text-center">
                                            <i class="ti ti-users fs-2 text-muted mb-2"></i>
                                            <span class="small text-muted d-block mb-1 fw-bold text-uppercase">Total Cover</span>
                                            <h5 class="fw-bold mb-0">{{ number_format($mtdCoverReport, 0, ',', '.') }} <small class="text-muted fs-6">Pax</small></h5>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-4">
                                        <div class="p-3 bg-light rounded border h-100 d-flex flex-column justify-content-between text-center">
                                            <div>
                                                <i class="ti ti-soup fs-2 text-muted mb-2"></i>
                                                <span class="small text-muted d-block mb-1 fw-bold text-uppercase">Food Revenue</span>
                                                <h6 class="fw-bold mb-3">Rp {{ number_format($mtdFoodRevenue, 0, ',', '.') }}</h6>
                                            </div>
                                            <div class="pt-2 border-top">
                                                <span class="small text-muted d-block">Average / Pax</span>
                                                <span class="fw-bold text-dark">Rp {{ number_format($mtdAverageFood, 0, ',', '.') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-4">
                                        <div class="p-3 bg-light rounded border h-100 d-flex flex-column justify-content-between text-center">
                                            <div>
                                                <i class="ti ti-glass-full fs-2 text-muted mb-2"></i>
                                                <span class="small text-muted d-block mb-1 fw-bold text-uppercase">Beverage Revenue</span>
                                                <h6 class="fw-bold mb-3">Rp {{ number_format($mtdBeverageRevenue, 0, ',', '.') }}</h6>
                                            </div>
                                            <div class="pt-2 border-top">
                                                <span class="small text-muted d-block">Average / Pax</span>
                                                <span class="fw-bold text-dark">Rp {{ number_format($mtdAverageBeverage, 0, ',', '.') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- REVENUE VS TARGETS --}}
                                <div class="row g-3">
                                    <div class="col-12 col-md-4">
                                        <div class="p-3 bg-light-primary border border-primary-subtle rounded h-100 d-flex flex-column justify-content-center align-items-center text-center">
                                            <span class="small text-primary fw-bold text-uppercase mb-1">Total MTD Revenue</span>
                                            <h4 class="fw-bolder text-primary mb-0">Rp {{ number_format($totalMtdRevenue, 0, ',', '.') }}</h4>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-4">
                                        <div class="p-3 bg-light rounded border h-100 d-flex flex-column justify-content-between">
                                            <div>
                                                <span class="small text-muted d-block text-center text-uppercase fw-bold mb-1">MTD Budget</span>
                                                <h5 class="fw-bold text-center mb-3">Rp {{ number_format($totalMtdBudget, 0, ',', '.') }}</h5>
                                            </div>
                                            <div class="p-2 rounded text-center {{ $mtdBudgetBalance >= 0 ? 'bg-light-success border border-success-subtle' : 'bg-light-danger border border-danger-subtle' }}">
                                                <span class="small {{ $mtdBudgetBalance >= 0 ? 'text-success' : 'text-danger' }} d-block fw-bold">Budget Variance</span>
                                                <span class="fw-bold {{ $mtdBudgetBalance >= 0 ? 'text-success' : 'text-danger' }}">
                                                    {{ $mtdBudgetBalance >= 0 ? '+' : '' }}Rp {{ number_format($mtdBudgetBalance, 0, ',', '.') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-4">
                                        <div class="p-3 bg-light rounded border h-100 d-flex flex-column justify-content-between">
                                            <div>
                                                <span class="small text-muted d-block text-center text-uppercase fw-bold mb-1">MTD Forecast</span>
                                                <h5 class="fw-bold text-center mb-3">Rp {{ number_format($totalMtdTarget, 0, ',', '.') }}</h5>
                                            </div>
                                            <div class="p-2 rounded text-center {{ $mtdBalance >= 0 ? 'bg-light-success border border-success-subtle' : 'bg-light-danger border border-danger-subtle' }}">
                                                <span class="small {{ $mtdBalance >= 0 ? 'text-success' : 'text-danger' }} d-block fw-bold">Forecast Variance</span>
                                                <span class="fw-bold {{ $mtdBalance >= 0 ? 'text-success' : 'text-danger' }}">
                                                    {{ $mtdBalance >= 0 ? '+' : '' }}Rp {{ number_format($mtdBalance, 0, ',', '.') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- KONTEN TAB 2: BREAKDOWN LIST --}}
                        @if (Auth::user()->hasRole('Super Admin') || Auth::user()->restaurants->count() > 1)
                            <div class="tab-pane fade" id="breakdown" role="tabpanel" aria-labelledby="breakdown-tab">
                                <div style="max-height: 400px; overflow-y: auto;">
                                    <ul class="list-group list-group-flush">
                                        @foreach ($breakdownPerformance as $data)
                                            <li class="list-group-item px-0 py-3 border-bottom">
                                                <div class="d-flex align-items-center justify-content-between mb-2">
                                                    <div>
                                                        <h6 class="mb-0 fw-bold">
                                                            {{-- Panggil fungsi JS openAnalyticsModal dengan ID Resto --}}
                                                            <a href="javascript:void(0)"
                                                                onclick="openAnalyticsModal({{ $data['id'] }})"
                                                                class="text-decoration-none text-primary">
                                                                {{ $data['name'] }} <i
                                                                    class="ti ti-external-link ms-1 small"></i>
                                                            </a>
                                                        </h6>
                                                        <small class="text-muted">
                                                            <span class="text-dark fw-bold">Rp {{ number_format($data['actual'], 0, ',', '.') }}</span><br>
                                                            <span class="me-2">/ Budget: Rp {{ number_format($data['budget'], 0, ',', '.') }}</span>
                                                            <span>/ Forecast: Rp {{ number_format($data['target'], 0, ',', '.') }}</span>
                                                        </small>
                                                    </div>
                                                    <div class="text-end d-flex flex-column align-items-end gap-1">
                                                        <span title="Budget Achievement" class="badge {{ $data['budget_percentage'] >= 100 ? 'bg-light-success text-success' : ($data['budget_percentage'] >= 80 ? 'bg-light-warning text-warning' : 'bg-light-danger text-danger') }} f-12">
                                                            B: {{ number_format($data['budget_percentage'], 1) }}%
                                                        </span>
                                                        <span title="Forecast Achievement" class="badge {{ $data['percentage'] >= 100 ? 'bg-light-info text-info' : ($data['percentage'] >= 80 ? 'bg-light-warning text-warning' : 'bg-light-danger text-danger') }} f-12">
                                                            F: {{ number_format($data['percentage'], 1) }}%
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="progress mt-1" style="height: 4px;" title="Budget Progress">
                                                    <div class="progress-bar {{ $data['budget_percentage'] >= 100 ? 'bg-success' : ($data['budget_percentage'] >= 80 ? 'bg-warning' : 'bg-danger') }}"
                                                        role="progressbar" style="width: {{ min($data['budget_percentage'], 100) }}%">
                                                    </div>
                                                </div>
                                                <div class="progress mt-1" style="height: 4px;" title="Forecast Progress">
                                                    <div class="progress-bar {{ $data['percentage'] >= 100 ? 'bg-info' : ($data['percentage'] >= 80 ? 'bg-warning' : 'bg-danger') }}"
                                                        role="progressbar" style="width: {{ min($data['percentage'], 100) }}%">
                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>

        {{-- WIDGET 1: WAITING APPROVAL --}}
        {{-- <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avtar avtar-s bg-light-warning text-warning">
                                <i class="ti ti-clock f-24"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">Waiting Approval</h6>
                            <div class="d-flex align-items-end mt-1">
                                <h4 class="mb-0">{{ $waitingApproval }}</h4>
                                <span
                                    class="badge bg-light-warning text-warning border border-warning ms-2">Submitted</span>
                            </div>
                            <p class="text-muted mt-2 mb-0 small">Reports waiting for manager action</p>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}

        {{-- WIDGET 2: TODAY'S REVENUE --}}
        {{-- <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avtar avtar-s bg-light-primary text-primary">
                                <i class="ti ti-wallet f-24"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">Today's Revenue</h6>
                            <div class="d-flex align-items-end mt-1">
                                <h4 class="mb-0">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</h4>
                            </div>
                            <p class="text-muted mt-2 mb-0 small">Total accumulated revenue today</p>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}

        {{-- WIDGET 3: MY DRAFTS --}}
        {{-- <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avtar avtar-s bg-light-secondary text-secondary">
                                <i class="ti ti-edit f-24"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">Drafts in Progress</h6>
                            <div class="d-flex align-items-end mt-1">
                                <h4 class="mb-0">{{ $myDrafts }}</h4>
                                <span
                                    class="badge bg-light-secondary text-secondary border border-secondary ms-2">Draft</span>
                            </div>
                            <p class="text-muted mt-2 mb-0 small">Unfinished reports (You/Team)</p>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}

        {{-- CHART SECTION --}}
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>Revenue Analytics (Last 7 Days)</h5>
                </div>
                <div class="card-body">
                    <div id="revenue-chart"></div>
                </div>
            </div>
        </div>

        {{-- COMPETITOR CHART (BARU) --}}
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>Competitor Cover Comparison (Last 7 Days)</h5>
                </div>
                <div class="card-body">
                    <div id="competitor-chart"></div>
                </div>
            </div>
        </div>

        {{-- MAIN CONTENT: RECENT REPORTS TABLE --}}
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>Recent Daily Reports</h5>
                    <a href="{{ route('daily-reports.index') }}" class="link-primary small fw-bold">View All Reports</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4">Date</th>
                                    <th>Restaurant</th>
                                    <th>Created By</th>
                                    <th>Status</th>
                                    <th class="text-end pe-4">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentReports as $report)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-bold">{{ $report->date->format('d M Y') }}</div>
                                            <div class="small text-muted">{{ $report->date->format('H:i') }}</div>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-dark">{{ $report->restaurant->name }}</span>
                                            <div class="small text-muted">{{ $report->restaurant->code }}</div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ asset('template/dist') }}/assets/images/user/avatar-2.jpg"
                                                    alt="user" class="wid-30 rounded-circle me-2">
                                                <span>{{ $report->user->name }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            @if ($report->status == 'approved')
                                                <span class="badge bg-light-success text-success">Approved</span>
                                            @elseif($report->status == 'submitted')
                                                <span class="badge bg-light-primary text-primary">Submitted</span>
                                            @else
                                                <span class="badge bg-light-warning text-warning">Draft</span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-4">
                                            <a href="{{ route('daily-reports.show', $report->id) }}"
                                                class="btn btn-sm btn-icon btn-link-secondary">
                                                <i class="ti ti-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="ti ti-folder-off fs-1 d-block mb-2"></i>
                                            No reports available yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="analyticsModal" tabindex="-1" aria-labelledby="analyticsModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                {{-- Konten ini akan diganti oleh AJAX (Loading Spinner Default) --}}
                <div class="modal-content" id="analyticsModalContent">
                    <div class="modal-body text-center p-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-3 text-muted">Loading analytics data...</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script src="{{ asset('template/dist/assets/js/plugins/flatpickr.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Flatpickr
            const dateRangePicker = flatpickr("#dateRangePicker", {
                mode: "range",
                dateFormat: "Y-m-d",
                defaultDate: ["{{ $startDate }}", "{{ $endDate }}"],
                onChange: function(selectedDates, dateStr, instance) {
                    if (selectedDates.length === 2) {
                        document.getElementById('start_date').value = instance.formatDate(selectedDates[0], 'Y-m-d');
                        document.getElementById('end_date').value = instance.formatDate(selectedDates[1], 'Y-m-d');
                    }
                }
            });

            var options = {
                series: [
                    {
                        name: 'Revenue',
                        type: 'column',
                        data: @json($chartValues)
                    },
                    {
                        name: 'Budget',
                        type: 'line',
                        data: @json($chartBudgetValues)
                    },
                    {
                        name: 'Forecast',
                        type: 'line',
                        data: @json($chartForecastValues)
                    }
                ],
                chart: {
                    height: 350,
                    type: 'line',
                    toolbar: {
                        show: false
                    }
                },
                stroke: {
                    width: [0, 3, 3],
                    curve: 'smooth',
                    dashArray: [0, 0, 5]
                },
                plotOptions: {
                    bar: {
                        borderRadius: 4,
                        columnWidth: '45%',
                    }
                },
                colors: ['#4680ff', '#ffba57', '#2ca87f'],
                dataLabels: {
                    enabled: false
                },
                xaxis: {
                    categories: @json($chartLabels),
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    }
                },
                yaxis: {
                    labels: {
                        formatter: function(value) {
                            return "Rp " + (value / 1000).toLocaleString() + "k";
                        }
                    }
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    y: {
                        formatter: function(val) {
                            return "Rp " + val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                        }
                    }
                }
            };

            var chart = new ApexCharts(document.querySelector("#revenue-chart"), options);
            chart.render();

            // --- 2. CHART COMPETITOR (KODE BARU) ---
            var compOptions = {
                series: @json($compSeries), // Data dari Controller
                chart: {
                    height: 350,
                    type: 'line', // Line chart cocok untuk perbandingan tren
                    zoom: {
                        enabled: false
                    },
                    toolbar: {
                        show: false
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    width: [3, 2, 2, 2], // Garis kita tebal, kompetitor tipis
                    curve: 'smooth',
                    dashArray: [0, 5, 5, 5] // Garis kompetitor putus-putus (opsional, biar kita menonjol)
                },
                colors: ['#4680ff', '#ff5252', '#ffba57', '#2ca87f'], // Biru(Kita), Merah, Kuning, Hijau
                xaxis: {
                    categories: @json($chartLabels), // Label tanggal sama dengan revenue
                },
                yaxis: {
                    title: {
                        text: 'Total Covers (Pax)'
                    }
                },
                legend: {
                    position: 'top'
                },
                markers: {
                    size: 4,
                    hover: {
                        size: 6
                    }
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return val + " Pax"
                        }
                    }
                }
            };

            var chartComp = new ApexCharts(document.querySelector("#competitor-chart"), compOptions);
            chartComp.render();
        });
        // 1. Fungsi Buka Modal Pertama Kali
        function openAnalyticsModal(restaurantId) {
            // Tampilkan Modal Bootstrap
            var myModal = new bootstrap.Modal(document.getElementById('analyticsModal'));
            myModal.show();

            // Panggil Data
            loadAnalyticsData(restaurantId);
        }

        // 2. Fungsi Load Data (AJAX) - Dipakai saat buka modal ATAU saat filter tanggal
        function loadAnalyticsData(restaurantId) {
            // Ambil elemen konten modal
            const contentDiv = document.getElementById('analyticsModalContent');

            // Cek apakah user sedang filter tanggal (elemen input ada di dalam modal)
            const startDateInput = document.getElementById('filter-start-date');
            const endDateInput = document.getElementById('filter-end-date');

            let url = `{{ url('/dashboard/analytics') }}/${restaurantId}`;

            // Jika input tanggal ada (artinya ini reload filter), tambahkan parameter
            if (startDateInput && endDateInput) {
                url += `?start_date=${startDateInput.value}&end_date=${endDateInput.value}`;

                // Tampilkan loading overlay tipis biar UX bagus
                contentDiv.style.opacity = '0.5';
                contentDiv.style.pointerEvents = 'none';
            } else {
                // Tampilkan Full Spinner (Reset tampilan awal)
                contentDiv.innerHTML = `
                <div class="modal-body text-center p-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-3 text-muted">Retrieving data...</p>
                </div>
            `;
                contentDiv.style.opacity = '1';
            }

            // Fetch Data dari Server
            fetch(url)
                .then(response => response.text())
                .then(html => {
                    contentDiv.innerHTML = html;
                    contentDiv.style.opacity = '1';
                    contentDiv.style.pointerEvents = 'auto';
                })
                .catch(error => {
                    console.error(error);
                    contentDiv.innerHTML = `<p class="text-center text-danger p-5">Failed to load data.</p>`;
                });
        }

        // Variabel Global untuk menyimpan object Chart
    </script>
@endpush
