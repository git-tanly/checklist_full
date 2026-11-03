@extends('layouts.portal')

@section('title', 'Dashboard Overview')
@section('breadcrumb', 'Dashboard')

@push('styles')
    <link rel="stylesheet" href="{{ asset('template/dist/assets/css/plugins/flatpickr.min.css') }}">
    <style>
        .metric-card {
            transition: all 0.2s ease;
        }
        .metric-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(23, 42, 64, 0.08);
        }
        .nav-tabs-portal {
            border-bottom: 2px solid #e2e8f0;
        }
        .nav-tabs-portal .nav-link {
            color: #64748b;
            font-weight: 600;
            font-size: 0.85rem;
            border: none;
            border-bottom: 2px solid transparent;
            padding: 0.75rem 1.25rem;
            margin-bottom: -2px;
            background: transparent;
            transition: all 0.15s ease;
        }
        .nav-tabs-portal .nav-link:hover {
            color: #172a40;
            border-bottom-color: #cbd5e1;
        }
        .nav-tabs-portal .nav-link.active {
            color: #172a40;
            border-bottom-color: #172a40;
            font-weight: 700;
            background: transparent;
        }
    </style>
@endpush

@section('header')
    <!-- EXECUTIVE PAGE HEADER -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white border border-slate-200/90 rounded-2xl p-5 md:p-6 shadow-sm mb-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-[#172a40] text-white flex items-center justify-center text-2xl shadow-sm shrink-0">
                <i class="ti ti-dashboard"></i>
            </div>
            <div>
                <h1 class="text-lg md:text-xl font-bold text-slate-800 tracking-tight mb-0.5">Dashboard Overview</h1>
                <p class="text-xs text-slate-500 mb-0">Pemantauan performa harian restoran, perbandingan budget & forecast, serta analitik kompetitor.</p>
            </div>
        </div>

        <div class="flex items-center gap-2 self-start md:self-auto">
            <div class="bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs text-slate-600 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <span class="font-medium text-slate-700">Periode: <strong>{{ $periodLabel }}</strong></span>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="row g-4">
        {{-- FILTER SECTION --}}
        <div class="col-12">
            <div class="card border border-slate-200/90 rounded-2xl shadow-sm bg-white">
                <div class="card-body p-4">
                    <form method="GET" action="{{ route('dashboard') }}" id="filterForm">
                        <div class="row align-items-end g-3">
                            <div class="col-12 col-md-4">
                                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">
                                    <i class="ti ti-calendar me-1 text-slate-400"></i> Date Range
                                </label>
                                <div class="relative">
                                    <input type="text" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2 text-xs text-slate-800 focus:outline-none focus:border-[#172a40] focus:ring-2 focus:ring-[#172a40]/10 transition" id="dateRangePicker"
                                        placeholder="Pilih rentang tanggal" readonly>
                                    <input type="hidden" name="start_date" id="start_date" value="{{ $startDate }}">
                                    <input type="hidden" name="end_date" id="end_date" value="{{ $endDate }}">
                                </div>
                            </div>

                            @if(Auth::user()->hasRole('Super Admin') || Auth::user()->restaurants->count() > 1)
                            <div class="col-12 col-md-4">
                                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">
                                    <i class="ti ti-building-store me-1 text-slate-400"></i> Restaurant
                                </label>
                                <select class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2 text-xs text-slate-800 focus:outline-none focus:border-[#172a40] focus:ring-2 focus:ring-[#172a40]/10 transition" name="restaurant_id" id="restaurant_id">
                                    <option value="">Semua Restoran (All Restaurants)</option>
                                    @foreach($allRestaurants as $resto)
                                        <option value="{{ $resto->id }}"
                                            {{ $restaurantFilter == $resto->id ? 'selected' : '' }}>
                                            {{ $resto->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @endif

                            <div class="col-12 col-md-4 d-flex gap-2">
                                <button type="submit" class="btn btn-primary d-inline-flex align-items-center justify-content-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold shadow-sm transition">
                                    <i class="ti ti-filter"></i> Apply Filter
                                </button>
                                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold transition">
                                    <i class="ti ti-refresh"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- PERFORMANCE SECTION (TABS VIEW) --}}
        <div class="col-12">
            <div class="card border border-slate-200/90 rounded-2xl shadow-sm bg-white overflow-hidden">
                <div class="px-4 pt-3 border-b border-slate-100 bg-slate-50/50">
                    <ul class="nav nav-tabs-portal" id="performanceTab" role="tablist">
                        {{-- Tab 1: Overview --}}
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview"
                                type="button" role="tab" aria-selected="true">
                                <i class="ti ti-chart-pie me-1.5"></i> Overview Summary
                            </button>
                        </li>

                        {{-- Tab 2: Outlet Details --}}
                        @if (Auth::user()->hasRole('Super Admin') || Auth::user()->hasRole('Restaurant Manager'))
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="breakdown-tab" data-bs-toggle="tab" data-bs-target="#breakdown"
                                    type="button" role="tab" aria-selected="false">
                                    <i class="ti ti-list-details me-1.5"></i> Outlet Breakdown
                                </button>
                            </li>
                        @endif
                    </ul>
                </div>

                <div class="card-body p-4 md:p-6">
                    <div class="tab-content" id="performanceTabContent">

                        {{-- KONTEN TAB 1: OVERVIEW --}}
                        <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview-tab">
                            @php
                                $overallBudgetPercentage = $monthlyBudget > 0 ? ($mtdRevenue / $monthlyBudget) * 100 : 0;
                            @endphp

                            <!-- DUAL HERO PERFORMANCE CARDS -->
                            <div class="row g-4">
                                <!-- Card 1: Performance vs Budget -->
                                <div class="col-12 col-lg-6">
                                    <div class="metric-card bg-gradient-to-br from-white to-[#f0f4f8] border border-slate-200/90 rounded-2xl p-5 shadow-sm h-100 relative overflow-hidden">
                                        <div class="absolute top-0 left-0 right-0 h-1.5 bg-[#172a40]"></div>
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <span class="text-xs font-bold text-[#172a40] uppercase tracking-wider">
                                                <i class="ti ti-coin me-1"></i> Performance vs Budget ({{ $periodLabel }})
                                            </span>
                                            <span class="badge {{ $overallBudgetPercentage >= 100 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($overallBudgetPercentage >= 80 ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-rose-50 text-rose-700 border border-rose-200') }} rounded-full px-2.5 py-1 font-bold text-xs">
                                                {{ number_format($overallBudgetPercentage, 1) }}%
                                            </span>
                                        </div>

                                        <div class="mb-3">
                                            <div class="text-2xl font-bold text-slate-800 tracking-tight">
                                                Rp {{ number_format($mtdRevenue, 0, ',', '.') }}
                                            </div>
                                            <div class="text-xs text-slate-500 mt-0.5">
                                                Target Budget: <strong class="text-slate-700">Rp {{ number_format($monthlyBudget, 0, ',', '.') }}</strong>
                                            </div>
                                        </div>

                                        <div class="progress rounded-full bg-slate-200/70 mb-3" style="height: 10px;">
                                            <div class="progress-bar rounded-full {{ $overallBudgetPercentage >= 100 ? 'bg-emerald-500' : ($overallBudgetPercentage >= 80 ? 'bg-amber-500' : 'bg-rose-500') }}"
                                                role="progressbar" style="width: {{ min($overallBudgetPercentage, 100) }}%">
                                            </div>
                                        </div>

                                        @php
                                            $budgetBalance = $mtdRevenue - $monthlyBudget;
                                        @endphp
                                        <div class="d-flex justify-content-between align-items-center pt-2 border-t border-slate-200/70 text-xs">
                                            <span class="text-slate-500 font-medium">Variance (Balance)</span>
                                            <span class="font-bold {{ $budgetBalance >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                                {{ $budgetBalance >= 0 ? '+' : '' }}Rp {{ number_format($budgetBalance, 0, ',', '.') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Card 2: Performance vs Forecast -->
                                <div class="col-12 col-lg-6">
                                    <div class="metric-card bg-gradient-to-br from-white to-[#fffbf2] border border-amber-200/70 rounded-2xl p-5 shadow-sm h-100 relative overflow-hidden">
                                        <div class="absolute top-0 left-0 right-0 h-1.5 bg-[#B89047]"></div>
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <span class="text-xs font-bold text-[#B89047] uppercase tracking-wider">
                                                <i class="ti ti-target me-1"></i> Performance vs Forecast ({{ $periodLabel }})
                                            </span>
                                            <span class="badge {{ $achievementPercent >= 100 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($achievementPercent >= 80 ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-rose-50 text-rose-700 border border-rose-200') }} rounded-full px-2.5 py-1 font-bold text-xs">
                                                {{ number_format($achievementPercent, 1) }}%
                                            </span>
                                        </div>

                                        <div class="mb-3">
                                            <div class="text-2xl font-bold text-slate-800 tracking-tight">
                                                Rp {{ number_format($mtdRevenue, 0, ',', '.') }}
                                            </div>
                                            <div class="text-xs text-slate-500 mt-0.5">
                                                Target Forecast: <strong class="text-slate-700">Rp {{ number_format($monthlyTarget, 0, ',', '.') }}</strong>
                                            </div>
                                        </div>

                                        <div class="progress rounded-full bg-amber-100/70 mb-3" style="height: 10px;">
                                            <div class="progress-bar rounded-full {{ $achievementPercent >= 100 ? 'bg-emerald-500' : ($achievementPercent >= 80 ? 'bg-amber-500' : 'bg-rose-500') }}"
                                                role="progressbar" style="width: {{ min($achievementPercent, 100) }}%">
                                            </div>
                                        </div>

                                        @php
                                            $forecastBalance = $mtdRevenue - $monthlyTarget;
                                        @endphp
                                        <div class="d-flex justify-content-between align-items-center pt-2 border-t border-amber-200/50 text-xs">
                                            <span class="text-slate-500 font-medium">Variance (Balance)</span>
                                            <span class="font-bold {{ $forecastBalance >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                                {{ $forecastBalance >= 0 ? '+' : '' }}Rp {{ number_format($forecastBalance, 0, ',', '.') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- MTD STATS SECTION --}}
                            <div class="mt-5 pt-4 border-t border-slate-100">
                                <div class="flex items-center gap-2 mb-3.5">
                                    <div class="w-7 h-7 rounded-lg bg-[#172a40]/10 text-[#172a40] flex items-center justify-center text-sm font-bold">
                                        <i class="ti ti-calendar-stats"></i>
                                    </div>
                                    <h2 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-0">Month-to-Date (MTD) Analytics</h2>
                                </div>

                                {{-- COVER, FOOD, BEVERAGE ROW --}}
                                <div class="row g-3 mb-3">
                                    <!-- Total Cover -->
                                    <div class="col-12 col-md-4">
                                        <div class="metric-card p-4 bg-slate-50/70 rounded-2xl border border-slate-200/90 h-100 d-flex flex-column justify-content-center text-center">
                                            <div class="w-10 h-10 mx-auto rounded-xl bg-white text-slate-600 flex items-center justify-center text-xl shadow-sm mb-2.5">
                                                <i class="ti ti-users"></i>
                                            </div>
                                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Total Cover</span>
                                            <div class="text-xl font-bold text-slate-800">
                                                {{ number_format($mtdCoverReport, 0, ',', '.') }} <span class="text-xs font-normal text-slate-500">Pax</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Food Revenue -->
                                    <div class="col-12 col-md-4">
                                        <div class="metric-card p-4 bg-slate-50/70 rounded-2xl border border-slate-200/90 h-100 d-flex flex-column justify-content-between text-center">
                                            <div>
                                                <div class="w-10 h-10 mx-auto rounded-xl bg-white text-amber-600 flex items-center justify-center text-xl shadow-sm mb-2.5">
                                                    <i class="ti ti-soup"></i>
                                                </div>
                                                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Food Revenue</span>
                                                <div class="text-lg font-bold text-slate-800 mb-2">
                                                    Rp {{ number_format($mtdFoodRevenue, 0, ',', '.') }}
                                                </div>
                                            </div>
                                            <div class="pt-2 border-t border-slate-200/70 text-xs">
                                                <span class="text-slate-400">Average / Pax:</span>
                                                <strong class="text-slate-700 ms-1">Rp {{ number_format($mtdAverageFood, 0, ',', '.') }}</strong>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Beverage Revenue -->
                                    <div class="col-12 col-md-4">
                                        <div class="metric-card p-4 bg-slate-50/70 rounded-2xl border border-slate-200/90 h-100 d-flex flex-column justify-content-between text-center">
                                            <div>
                                                <div class="w-10 h-10 mx-auto rounded-xl bg-white text-cyan-600 flex items-center justify-center text-xl shadow-sm mb-2.5">
                                                    <i class="ti ti-glass-full"></i>
                                                </div>
                                                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Beverage Revenue</span>
                                                <div class="text-lg font-bold text-slate-800 mb-2">
                                                    Rp {{ number_format($mtdBeverageRevenue, 0, ',', '.') }}
                                                </div>
                                            </div>
                                            <div class="pt-2 border-t border-slate-200/70 text-xs">
                                                <span class="text-slate-400">Average / Pax:</span>
                                                <strong class="text-slate-700 ms-1">Rp {{ number_format($mtdAverageBeverage, 0, ',', '.') }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- REVENUE VS TARGETS TOTALS ROW --}}
                                <div class="row g-3">
                                    <!-- Total MTD Revenue -->
                                    <div class="col-12 col-md-4">
                                        <div class="metric-card p-4 bg-[#172a40] text-white rounded-2xl border border-[#0f1c2b] shadow-sm h-100 d-flex flex-column justify-content-center align-items-center text-center">
                                            <span class="text-[11px] font-bold text-white/70 uppercase tracking-wider mb-1">Total MTD Revenue</span>
                                            <div class="text-xl md:text-2xl font-black text-white">
                                                Rp {{ number_format($totalMtdRevenue, 0, ',', '.') }}
                                            </div>
                                        </div>
                                    </div>

                                    <!-- MTD Budget & Variance -->
                                    <div class="col-12 col-md-4">
                                        <div class="metric-card p-4 bg-slate-50/70 rounded-2xl border border-slate-200/90 h-100 d-flex flex-column justify-content-between">
                                            <div class="text-center mb-2">
                                                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">MTD Budget Target</span>
                                                <div class="text-lg font-bold text-slate-800">
                                                    Rp {{ number_format($totalMtdBudget, 0, ',', '.') }}
                                                </div>
                                            </div>
                                            <div class="p-2.5 rounded-xl text-center {{ $mtdBudgetBalance >= 0 ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-rose-50 text-rose-800 border border-rose-200' }} text-xs">
                                                <span class="block text-[10px] font-bold uppercase tracking-wider {{ $mtdBudgetBalance >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">Budget Variance</span>
                                                <span class="font-bold">
                                                    {{ $mtdBudgetBalance >= 0 ? '+' : '' }}Rp {{ number_format($mtdBudgetBalance, 0, ',', '.') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- MTD Forecast & Variance -->
                                    <div class="col-12 col-md-4">
                                        <div class="metric-card p-4 bg-slate-50/70 rounded-2xl border border-slate-200/90 h-100 d-flex flex-column justify-content-between">
                                            <div class="text-center mb-2">
                                                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">MTD Forecast Target</span>
                                                <div class="text-lg font-bold text-slate-800">
                                                    Rp {{ number_format($totalMtdTarget, 0, ',', '.') }}
                                                </div>
                                            </div>
                                            <div class="p-2.5 rounded-xl text-center {{ $mtdBalance >= 0 ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-rose-50 text-rose-800 border border-rose-200' }} text-xs">
                                                <span class="block text-[10px] font-bold uppercase tracking-wider {{ $mtdBalance >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">Forecast Variance</span>
                                                <span class="font-bold">
                                                    {{ $mtdBalance >= 0 ? '+' : '' }}Rp {{ number_format($mtdBalance, 0, ',', '.') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- KONTEN TAB 2: BREAKDOWN LIST --}}
                        @if (Auth::user()->hasRole('Super Admin') || Auth::user()->hasRole('Restaurant Manager'))
                            <div class="tab-pane fade" id="breakdown" role="tabpanel" aria-labelledby="breakdown-tab">
                                <div class="space-y-3" style="max-height: 480px; overflow-y: auto;">
                                    @foreach ($breakdownPerformance as $data)
                                        <div class="p-4 rounded-xl border border-slate-200 bg-white hover:border-slate-300 transition-all shadow-sm">
                                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-2.5">
                                                <div>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <a href="javascript:void(0)"
                                                            onclick="openAnalyticsModal({{ $data['id'] }})"
                                                            class="text-sm font-bold text-[#172a40] hover:text-[#B89047] text-decoration-none transition">
                                                            {{ $data['name'] }}
                                                            <i class="ti ti-external-link ms-1 text-xs text-slate-400"></i>
                                                        </a>
                                                    </div>
                                                    <div class="text-xs text-slate-500 mt-0.5">
                                                        Actual: <strong class="text-slate-800">Rp {{ number_format($data['actual'], 0, ',', '.') }}</strong>
                                                        <span class="text-slate-300 mx-1.5">|</span>
                                                        Budget: Rp {{ number_format($data['budget'], 0, ',', '.') }}
                                                        <span class="text-slate-300 mx-1.5">|</span>
                                                        Forecast: Rp {{ number_format($data['target'], 0, ',', '.') }}
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center gap-2">
                                                    <span title="Budget Achievement" class="badge {{ $data['budget_percentage'] >= 100 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($data['budget_percentage'] >= 80 ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-rose-50 text-rose-700 border border-rose-200') }} rounded-full px-2.5 py-1 text-xs font-bold">
                                                        Budget: {{ number_format($data['budget_percentage'], 1) }}%
                                                    </span>
                                                    <span title="Forecast Achievement" class="badge {{ $data['percentage'] >= 100 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($data['percentage'] >= 80 ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-rose-50 text-rose-700 border border-rose-200') }} rounded-full px-2.5 py-1 text-xs font-bold">
                                                        Forecast: {{ number_format($data['percentage'], 1) }}%
                                                    </span>
                                                </div>
                                            </div>

                                            <!-- Progress Bars -->
                                            <div class="space-y-1.5 pt-1">
                                                <div class="flex items-center gap-2 text-[10px] text-slate-400 font-medium">
                                                    <span class="w-14">Budget</span>
                                                    <div class="progress flex-1 rounded-full bg-slate-100" style="height: 6px;" title="Budget Progress">
                                                        <div class="progress-bar rounded-full {{ $data['budget_percentage'] >= 100 ? 'bg-emerald-500' : ($data['budget_percentage'] >= 80 ? 'bg-amber-500' : 'bg-rose-500') }}"
                                                            role="progressbar" style="width: {{ min($data['budget_percentage'], 100) }}%">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="flex items-center gap-2 text-[10px] text-slate-400 font-medium">
                                                    <span class="w-14">Forecast</span>
                                                    <div class="progress flex-1 rounded-full bg-slate-100" style="height: 6px;" title="Forecast Progress">
                                                        <div class="progress-bar rounded-full {{ $data['percentage'] >= 100 ? 'bg-emerald-500' : ($data['percentage'] >= 80 ? 'bg-amber-500' : 'bg-rose-500') }}"
                                                            role="progressbar" style="width: {{ min($data['percentage'], 100) }}%">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>

        {{-- CHART 1: REVENUE ANALYTICS SECTION --}}
        <div class="col-12 col-lg-6">
            <div class="card border border-slate-200/90 rounded-2xl shadow-sm bg-white overflow-hidden h-100">
                <div class="p-4 md:p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-0.5">
                            <i class="ti ti-chart-bar me-1.5 text-[#172a40]"></i> Revenue Analytics
                        </h2>
                        <p class="text-xs text-slate-500 mb-0">Tren perolehan revenue harian vs target budget dan forecast.</p>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div id="revenue-chart"></div>
                </div>
            </div>
        </div>

        {{-- CHART 2: COMPETITOR CHART SECTION --}}
        <div class="col-12 col-lg-6">
            <div class="card border border-slate-200/90 rounded-2xl shadow-sm bg-white overflow-hidden h-100">
                <div class="p-4 md:p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-0.5">
                            <i class="ti ti-chart-line me-1.5 text-[#B89047]"></i> Competitor Cover Comparison
                        </h2>
                        <p class="text-xs text-slate-500 mb-0">Perbandingan jumlah cover tamu harian hotel dengan properti kompetitor.</p>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div id="competitor-chart"></div>
                </div>
            </div>
        </div>

        {{-- MAIN CONTENT: RECENT REPORTS TABLE --}}
        <div class="col-12">
            <div class="card border border-slate-200/90 rounded-2xl shadow-sm bg-white overflow-hidden">
                <div class="p-4 md:p-5 border-b border-slate-100 d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-0.5">
                            <i class="ti ti-file-text me-1.5 text-[#172a40]"></i> Recent Daily Reports
                        </h2>
                        <p class="text-xs text-slate-500 mb-0">Daftar laporan harian yang baru diperbarui atau diinput.</p>
                    </div>
                    <a href="{{ route('daily-reports.index') }}" class="btn btn-outline-primary btn-sm rounded-xl px-3 py-1.5 text-xs font-semibold d-inline-flex align-items-center gap-1">
                        View All Reports <i class="ti ti-arrow-right"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                    <th class="ps-4 py-3">Tanggal Laporan</th>
                                    <th class="py-3">Restoran / Outlet</th>
                                    <th class="py-3">Dibuat Oleh</th>
                                    <th class="py-3">Status</th>
                                    <th class="text-end pe-4 py-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-xs">
                                @forelse($recentReports as $report)
                                    <tr>
                                        <td class="ps-4 py-3">
                                            <div class="font-bold text-slate-800">{{ $report->date->format('d M Y') }}</div>
                                            <div class="text-[11px] text-slate-400">{{ $report->date->format('H:i') }} WIB</div>
                                        </td>
                                        <td class="py-3">
                                            <span class="font-bold text-slate-800">{{ $report->restaurant->name }}</span>
                                            <span class="ms-1 px-1.5 py-0.2 rounded bg-slate-100 text-slate-500 font-mono text-[10px]">{{ $report->restaurant->code }}</span>
                                        </td>
                                        <td class="py-3">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="w-7 h-7 rounded-full bg-[#172a40] text-white flex items-center justify-center font-bold text-[10px]">
                                                    {{ strtoupper(substr($report->user->name ?? 'U', 0, 2)) }}
                                                </div>
                                                <span class="font-medium text-slate-700">{{ $report->user->name }}</span>
                                            </div>
                                        </td>
                                        <td class="py-3">
                                            @if ($report->status == 'approved')
                                                <span class="badge bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full px-2.5 py-1 font-semibold text-[11px]">
                                                    <i class="ti ti-check me-0.5"></i> Approved
                                                </span>
                                            @elseif($report->status == 'submitted')
                                                <span class="badge bg-blue-50 text-blue-700 border border-blue-200 rounded-full px-2.5 py-1 font-semibold text-[11px]">
                                                    <i class="ti ti-clock me-0.5"></i> Submitted
                                                </span>
                                            @else
                                                <span class="badge bg-amber-50 text-amber-700 border border-amber-200 rounded-full px-2.5 py-1 font-semibold text-[11px]">
                                                    <i class="ti ti-pencil me-0.5"></i> Draft
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-4 py-3">
                                            <a href="{{ route('daily-reports.show', $report->id) }}"
                                                class="btn btn-sm btn-outline-secondary rounded-lg p-1.5 text-slate-600 hover:text-[#172a40] transition"
                                                title="Lihat Detail Laporan">
                                                <i class="ti ti-eye text-sm"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-slate-400">
                                            <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center text-2xl mx-auto mb-2">
                                                <i class="ti ti-folder-off"></i>
                                            </div>
                                            <p class="text-xs mb-0">Belum ada laporan harian pada sistem.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- ANALYTICS MODAL CONTAINER --}}
        <div class="modal fade" id="analyticsModal" tabindex="-1" aria-labelledby="analyticsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content border-0 rounded-2xl shadow-2xl overflow-hidden" id="analyticsModalContent">
                    <div class="modal-body text-center p-5">
                        <div class="spinner-border text-[#172a40]" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-3 text-xs text-slate-500 font-medium">Memuat data analitik outlet...</p>
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

            // --- 1. REVENUE CHART ---
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
                    },
                    fontFamily: 'Inter, Public Sans, sans-serif'
                },
                stroke: {
                    width: [0, 3, 3],
                    curve: 'smooth',
                    dashArray: [0, 0, 4]
                },
                plotOptions: {
                    bar: {
                        borderRadius: 6,
                        columnWidth: '42%',
                    }
                },
                colors: ['#172a40', '#B89047', '#10b981'], // Tanly Palette: Corporate Navy, Gold, Emerald
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
                    },
                    labels: {
                        style: {
                            colors: '#64748b',
                            fontSize: '11px'
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: '#64748b',
                            fontSize: '11px'
                        },
                        formatter: function(value) {
                            return "Rp " + (value / 1000).toLocaleString() + "k";
                        }
                    }
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'right',
                    fontSize: '12px',
                    markers: {
                        radius: 12
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

            // --- 2. COMPETITOR CHART ---
            var compOptions = {
                series: @json($compSeries),
                chart: {
                    height: 350,
                    type: 'line',
                    zoom: {
                        enabled: false
                    },
                    toolbar: {
                        show: false
                    },
                    fontFamily: 'Inter, Public Sans, sans-serif'
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    width: [3.5, 2, 2, 2], // Properti kita tebal, kompetitor lebih tipis
                    curve: 'smooth',
                    dashArray: [0, 4, 4, 4] // Kompetitor putus-putus
                },
                colors: ['#172a40', '#ef4444', '#B89047', '#06b6d4', '#8b5cf6'], // Navy (Kita), Merah, Gold, Cyan, Ungu
                xaxis: {
                    categories: @json($chartLabels),
                    labels: {
                        style: {
                            colors: '#64748b',
                            fontSize: '11px'
                        }
                    }
                },
                yaxis: {
                    title: {
                        text: 'Total Covers (Pax)',
                        style: {
                            color: '#64748b',
                            fontSize: '11px'
                        }
                    },
                    labels: {
                        style: {
                            colors: '#64748b',
                            fontSize: '11px'
                        }
                    }
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'right',
                    fontSize: '12px'
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
                            return val + " Pax";
                        }
                    }
                }
            };

            var chartComp = new ApexCharts(document.querySelector("#competitor-chart"), compOptions);
            chartComp.render();
        });

        // 1. Fungsi Buka Modal Pertama Kali
        function openAnalyticsModal(restaurantId) {
            var myModal = new bootstrap.Modal(document.getElementById('analyticsModal'));
            myModal.show();
            loadAnalyticsData(restaurantId);
        }

        // 2. Fungsi Load Data (AJAX)
        function loadAnalyticsData(restaurantId) {
            const contentDiv = document.getElementById('analyticsModalContent');
            const startDateInput = document.getElementById('filter-start-date');
            const endDateInput = document.getElementById('filter-end-date');

            let url = `{{ url('/dashboard/analytics') }}/${restaurantId}`;

            if (startDateInput && endDateInput) {
                url += `?start_date=${startDateInput.value}&end_date=${endDateInput.value}`;
                contentDiv.style.opacity = '0.5';
                contentDiv.style.pointerEvents = 'none';
            } else {
                contentDiv.innerHTML = `
                    <div class="modal-body text-center p-5">
                        <div class="spinner-border text-[#172a40]" role="status"></div>
                        <p class="mt-3 text-xs text-slate-500 font-medium">Mengambil data analitik outlet...</p>
                    </div>
                `;
                contentDiv.style.opacity = '1';
            }

            fetch(url)
                .then(response => response.text())
                .then(html => {
                    contentDiv.innerHTML = html;
                    contentDiv.style.opacity = '1';
                    contentDiv.style.pointerEvents = 'auto';
                })
                .catch(error => {
                    console.error(error);
                    contentDiv.innerHTML = `<p class="text-center text-rose-600 p-5 font-bold text-xs">Gagal memuat data analitik.</p>`;
                });
        }
    </script>
@endpush
