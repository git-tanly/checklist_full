@extends('layouts.portal')

@section('title', 'Detail Report - ' . $dailyReport->restaurant->name . ' - Tanly Hospitality')

@section('content')
    @php
        // 1. HITUNG TOTAL (Kalkulasi di View agar Controller tetap bersih)
        $totalFood = 0;
        $totalBev = 0;
        $totalOthers = 0;
        $totalEvent = 0;
        $totalRevenue = 0;
        $totalCover = 0;

        foreach ($dailyReport->details as $d) {
            // Sum Revenue
            $totalFood += $d->revenue_food;
            $totalBev += $d->revenue_beverage;
            $totalOthers += $d->revenue_others;
            $totalEvent += $d->revenue_event;

            // Sum Cover (Karena struktur dinamis JSON)
            if (!empty($d->cover_data) && is_array($d->cover_data)) {
                foreach ($d->cover_data as $c) {
                    if (is_numeric($c)) {
                        $totalCover += $c;
                    }
                }
            }
        }
        $totalRevenue = $totalFood + $totalBev + $totalOthers + $totalEvent;
    @endphp

    <div class="space-y-6">
        {{-- Executive Banner Card --}}
        <div class="rounded-2xl border border-slate-200/90 shadow-sm bg-white p-5 md:p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 shadow-sm" style="background-color: #172a40; color: #B89047;">
                        <i class="ti ti-file-analytics text-2xl"></i>
                    </div>
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h1 class="text-xl font-bold tracking-tight text-slate-800">{{ $dailyReport->restaurant->name }}</h1>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-bold tracking-wider uppercase" style="background-color: rgba(23, 42, 64, 0.08); color: #172a40;">
                                {{ $dailyReport->restaurant->code }}
                            </span>
                            @if ($dailyReport->status == 'approved')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <i class="ti ti-check text-xs"></i> Approved
                                </span>
                            @elseif($dailyReport->status == 'submitted')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                    <i class="ti ti-clock text-xs"></i> Submitted
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                    <i class="ti ti-edit text-xs"></i> Draft
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-500 mt-1 flex items-center gap-1.5">
                            <i class="ti ti-calendar text-slate-400"></i>
                            {{ $dailyReport->date->format('l, d F Y - H:i') }}
                        </p>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('daily-reports.index') }}" class="btn btn-light rounded-xl border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 transition inline-flex items-center gap-1.5 shadow-sm">
                        <i class="ti ti-arrow-left text-sm"></i> Back to List
                    </a>
                    @if ($dailyReport->status == 'approved')
                        <a href="{{ route('daily-reports.pdf', $dailyReport->id) }}" class="btn rounded-xl px-4 py-2 text-xs font-semibold text-rose-700 bg-rose-50 border border-rose-200 hover:bg-rose-100 transition inline-flex items-center gap-1.5 shadow-sm" target="_blank">
                            <i class="ti ti-file-type-pdf text-sm text-rose-600"></i> Download PDF
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="row g-4">
            {{-- KOLOM KIRI: REPORT INFO & APPROVAL --}}
            <div class="col-lg-4 col-md-5">
                <div class="rounded-2xl border border-slate-200/90 shadow-sm bg-white overflow-hidden sticky-top" style="top: 80px; z-index: 10;">
                    <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/60 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <i class="ti ti-info-square-rounded text-lg" style="color: #172a40;"></i>
                            <h3 class="text-sm font-bold text-slate-800">Report Information</h3>
                        </div>
                    </div>
                    <div class="p-5 space-y-4">
                        {{-- Outlet --}}
                        <div class="pb-3 border-b border-slate-100">
                            <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block mb-1">Restaurant / Outlet</span>
                            <div class="text-sm font-bold text-slate-800">{{ $dailyReport->restaurant->name }}</div>
                            <div class="text-xs text-slate-500">Code: <span class="font-mono font-medium">{{ $dailyReport->restaurant->code }}</span></div>
                        </div>

                        {{-- Date & Time --}}
                        <div class="pb-3 border-b border-slate-100">
                            <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block mb-1">Date & Time</span>
                            <div class="text-sm font-bold text-slate-800">{{ $dailyReport->date->format('d F Y') }}</div>
                            <div class="text-xs text-slate-500">{{ $dailyReport->date->format('H:i') }} WIB</div>
                        </div>

                        {{-- Created By --}}
                        <div class="pb-3 border-b border-slate-100">
                            <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block mb-1.5">Created By</span>
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs shrink-0" style="background-color: #172a40; color: #B89047;">
                                    {{ strtoupper(substr($dailyReport->user->name ?? 'U', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="text-xs font-bold text-slate-800">{{ $dailyReport->user->name }}</div>
                                    <div class="text-[11px] text-slate-400">{{ $dailyReport->user->email ?? 'Staff' }}</div>
                                </div>
                            </div>
                        </div>

                        {{-- Status --}}
                        <div class="pb-3 border-b border-slate-100">
                            <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block mb-1.5">Status Laporan</span>
                            @if ($dailyReport->status == 'approved')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Approved
                                </span>
                                <div class="text-[11px] text-slate-500 mt-1.5">
                                    Disetujui oleh: <span class="font-semibold text-slate-700">{{ $dailyReport->approver->name ?? '-' }}</span>
                                </div>
                            @elseif($dailyReport->status == 'submitted')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span> Submitted
                                </span>
                                <div class="text-[11px] text-slate-400 mt-1.5">Menunggu review Restaurant Manager</div>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Draft
                                </span>
                                <div class="text-[11px] text-slate-400 mt-1.5">Belum disubmit ke manajemen</div>
                            @endif
                        </div>

                        {{-- Download PDF Button if Approved --}}
                        @if ($dailyReport->status == 'approved')
                            <div class="pt-1">
                                <a href="{{ route('daily-reports.pdf', $dailyReport->id) }}" class="btn rounded-xl w-100 py-2.5 text-xs font-semibold text-rose-700 bg-rose-50 border border-rose-200 hover:bg-rose-100 transition inline-flex items-center justify-center gap-2 shadow-sm" target="_blank">
                                    <i class="ti ti-file-type-pdf text-base text-rose-600"></i> Download Report PDF
                                </a>
                            </div>
                        @endif

                        {{-- APPROVAL ACTIONS --}}
                        @if ($dailyReport->status == 'submitted')
                            @hasanyrole('Super Admin|Restaurant Manager')
                                <div class="pt-2 space-y-2">
                                    {{-- FORM APPROVE --}}
                                    <form action="{{ route('daily-reports.approve', $dailyReport->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn rounded-xl w-100 py-2.5 text-xs font-semibold text-white transition inline-flex items-center justify-center gap-1.5 shadow-sm"
                                            style="background-color: #10b981; border-color: #10b981;"
                                            onclick="return confirm('Setujui laporan ini?')">
                                            <i class="ti ti-check text-sm"></i> Approve Report
                                        </button>
                                    </form>

                                    {{-- FORM REJECT --}}
                                    <form action="{{ route('daily-reports.reject', $dailyReport->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn rounded-xl w-100 py-2 text-xs font-semibold text-rose-600 border border-rose-200 hover:bg-rose-50 transition inline-flex items-center justify-center gap-1.5"
                                            onclick="return confirm('Tolak laporan ini? Status akan kembali menjadi Draft.')">
                                            <i class="ti ti-x text-sm"></i> Reject (Return to Draft)
                                        </button>
                                    </form>
                                </div>
                            @else
                                <div class="rounded-xl border border-blue-200 bg-blue-50/70 p-3 text-xs text-blue-700 flex items-start gap-2">
                                    <i class="ti ti-info-circle text-base text-blue-600 shrink-0 mt-0.5"></i>
                                    <div>Waiting for Restaurant Manager or Super Admin approval.</div>
                                </div>
                            @endhasanyrole
                        @endif
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN: GRAND TOTAL HERO & DETAIL SESI --}}
            <div class="col-lg-8 col-md-7">
                {{-- Hero Revenue & Pax Card --}}
                <div class="rounded-2xl shadow-sm text-white p-5 md:p-6 mb-5 relative overflow-hidden" style="background: linear-gradient(135deg, #172a40 0%, #112031 100%);">
                    <div class="absolute -right-8 -bottom-8 w-48 h-48 rounded-full bg-white/5 pointer-events-none"></div>
                    <div class="row align-items-center g-4 relative z-10">
                        {{-- Kiri: Grand Total Revenue --}}
                        <div class="col-md-7 md:border-r border-white/10 md:pr-6">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-[#B89047] flex items-center gap-1.5 mb-1.5">
                                <i class="ti ti-coin text-sm"></i> Total Daily Revenue
                            </span>
                            <h2 class="text-2xl md:text-3xl font-extrabold text-white tracking-tight">
                                Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                            </h2>

                            {{-- Revenue Breakdown Chips --}}
                            <div class="flex flex-wrap gap-2 mt-4 text-xs">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-white/10 text-white/90 border border-white/10" title="Food Revenue">
                                    <i class="ti ti-tools-kitchen-2 text-[#B89047]"></i> Food: <strong class="text-white">Rp {{ number_format($totalFood, 0, ',', '.') }}</strong>
                                </span>
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-white/10 text-white/90 border border-white/10" title="Beverage Revenue">
                                    <i class="ti ti-glass-full text-[#B89047]"></i> Bev: <strong class="text-white">Rp {{ number_format($totalBev, 0, ',', '.') }}</strong>
                                </span>
                                @if ($totalEvent > 0)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-white/10 text-white/90 border border-white/10" title="Event Revenue">
                                        <i class="ti ti-calendar-event text-[#B89047]"></i> Event: <strong class="text-white">Rp {{ number_format($totalEvent, 0, ',', '.') }}</strong>
                                    </span>
                                @endif
                                @if ($totalOthers > 0)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-white/10 text-white/90 border border-white/10" title="Others Revenue">
                                        <i class="ti ti-dots-circle-horizontal text-[#B89047]"></i> Others: <strong class="text-white">Rp {{ number_format($totalOthers, 0, ',', '.') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Kanan: Total Cover --}}
                        <div class="col-md-5 text-center md:text-left md:pl-4">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-300 flex items-center justify-center md:justify-start gap-1.5 mb-1.5">
                                <i class="ti ti-users text-sm text-[#B89047]"></i> Total Pax / Cover
                            </span>
                            <h2 class="text-3xl md:text-4xl font-extrabold text-white tracking-tight">
                                {{ number_format($totalCover) }}
                            </h2>
                            <div class="mt-2.5">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-white/15 text-white border border-white/20">
                                    <i class="ti ti-sum text-xs"></i> All Sessions Accumulated
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Session Details Loop --}}
                @foreach ($dailyReport->details as $detail)
                    <div class="rounded-2xl border border-slate-200/90 shadow-sm bg-white overflow-hidden mb-5">
                        <div class="px-5 py-3.5 flex flex-wrap items-center justify-between gap-2
                            {{ $detail->session_type == 'breakfast'
                                ? 'bg-amber-50/70 border-b border-amber-100'
                                : ($detail->session_type == 'lunch'
                                    ? 'bg-sky-50/70 border-b border-sky-100'
                                    : 'bg-indigo-50/70 border-b border-indigo-100') }}">
                            <div class="flex items-center gap-2">
                                @if ($detail->session_type == 'breakfast')
                                    <div class="w-7 h-7 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center">
                                        <i class="ti ti-sun text-base"></i>
                                    </div>
                                @elseif($detail->session_type == 'lunch')
                                    <div class="w-7 h-7 rounded-lg bg-sky-100 text-sky-700 flex items-center justify-center">
                                        <i class="ti ti-soup text-base"></i>
                                    </div>
                                @else
                                    <div class="w-7 h-7 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center">
                                        <i class="ti ti-moon-stars text-base"></i>
                                    </div>
                                @endif
                                <h3 class="text-sm font-bold text-slate-800 capitalize">
                                    {{ $detail->session_type }} Report
                                </h3>
                            </div>
                            @if ($detail->thematic)
                                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-slate-800 text-white shadow-sm">
                                    <i class="ti ti-sparkles text-[#B89047]"></i> Theme: {{ $detail->thematic }}
                                    @if (!empty($detail->additional_data['thematic_pax']))
                                        <span class="text-slate-400">|</span> Pax: {{ $detail->additional_data['thematic_pax'] }}
                                    @endif
                                    @if (!empty($detail->additional_data['thematic_revenue']))
                                        <span class="text-slate-400">|</span> Rp {{ number_format($detail->additional_data['thematic_revenue'], 0, ',', '.') }}
                                    @endif
                                </div>
                            @endif
                        </div>
                        <div class="p-5">
                            @if(isset($detail->additional_data['nagano_floors']))
                                <ul class="nav nav-pills mb-4 gap-2 bg-slate-50 p-1.5 rounded-xl border border-slate-200" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active rounded-lg text-xs font-bold px-3 py-1.5" data-bs-toggle="pill" data-bs-target="#gabungan-{{$detail->id}}" type="button" role="tab">Total (Gabungan)</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link rounded-lg text-xs font-bold px-3 py-1.5" data-bs-toggle="pill" data-bs-target="#lt5-{{$detail->id}}" type="button" role="tab">Lantai 5</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link rounded-lg text-xs font-bold px-3 py-1.5" data-bs-toggle="pill" data-bs-target="#lt6-{{$detail->id}}" type="button" role="tab">Lantai 6</button>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane fade show active" id="gabungan-{{$detail->id}}" role="tabpanel">
                                        @include('daily-reports.partials.show-detail-body', ['data' => $detail])
                                    </div>
                                    <div class="tab-pane fade" id="lt5-{{$detail->id}}" role="tabpanel">
                                        @include('daily-reports.partials.show-detail-body', ['data' => (object) $detail->additional_data['nagano_floors']['lt5']])
                                    </div>
                                    <div class="tab-pane fade" id="lt6-{{$detail->id}}" role="tabpanel">
                                        @include('daily-reports.partials.show-detail-body', ['data' => (object) $detail->additional_data['nagano_floors']['lt6']])
                                    </div>
                                </div>
                            @else
                                @include('daily-reports.partials.show-detail-body', ['data' => $detail])
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
