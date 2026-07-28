@extends('layouts.mantis')

@section('header')
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h5 class="m-b-10">Report Detail</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('daily-reports.index') }}">Reports</a></li>
                        <li class="breadcrumb-item" aria-current="page">Detail</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        {{-- KOLOM KIRI: HEADER LAPORAN --}}
        <div class="col-md-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>Report Info</h5>
                    {{-- TOMBOL BACK --}}
                    <a href="{{ route('daily-reports.index') }}" class="btn btn-sm btn-light-secondary">
                        <i class="ti ti-arrow-left"></i> Back to List
                    </a>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item px-0">
                            <span class="text-muted mb-1 d-block">Restaurant</span>
                            <h6 class="mb-0">{{ $dailyReport->restaurant->name }} ({{ $dailyReport->restaurant->code }})
                            </h6>
                        </li>
                        <li class="list-group-item px-0">
                            <span class="text-muted mb-1 d-block">Date & Time</span>
                            {{-- TAMBAHKAN JAM DI SINI --}}
                            <h6 class="mb-0">{{ $dailyReport->date->format('d F Y - H:i') }}</h6>
                        </li>
                        <li class="list-group-item px-0">
                            <span class="text-muted mb-1 d-block">Created By</span>
                            <div class="d-flex align-items-center">
                                <img src="{{ asset('template/dist') }}/assets/images/user/avatar-2.jpg" alt="user"
                                    class="wid-30 rounded-circle me-2">
                                <h6 class="mb-0">{{ $dailyReport->user->name }}</h6>
                            </div>
                        </li>
                        @if ($dailyReport->status == 'approved')
                            <li class="list-group-item px-0 pb-3 pt-3">
                                <div class="d-grid">
                                    <a href="{{ route('daily-reports.pdf', $dailyReport->id) }}" class="btn btn-danger"
                                        target="_blank">
                                        <i class="ti ti-file-type-pdf me-1"></i> Download Report PDF
                                    </a>
                                </div>
                            </li>
                        @endif
                        <li class="list-group-item px-0">
                            <span class="text-muted mb-1 d-block">Status</span>
                            @if ($dailyReport->status == 'approved')
                                <span class="badge bg-success">Approved</span>
                                <div class="small mt-1 text-muted">by {{ $dailyReport->approver->name ?? '-' }}</div>
                            @elseif($dailyReport->status == 'submitted')
                                <span class="badge bg-primary">Submitted</span>
                            @else
                                <span class="badge bg-warning text-dark">Draft</span>
                            @endif
                        </li>
                    </ul>

                    @if ($dailyReport->status == 'submitted')
                        @hasanyrole('Super Admin|Restaurant Manager')
                            <div class="d-grid mt-3">
                                {{-- FORM APPROVE --}}
                                <form action="{{ route('daily-reports.approve', $dailyReport->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-success w-100"
                                        onclick="return confirm('Setujui laporan ini?')">
                                        <i class="ti ti-check"></i> Approve Report
                                    </button>
                                </form>

                                {{-- TAMBAHKAN FORM REJECT DI BAWAH SINI --}}
                                <form action="{{ route('daily-reports.reject', $dailyReport->id) }}" method="POST"
                                    class="mt-2">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-outline-danger w-100"
                                        onclick="return confirm('Tolak laporan ini? Status akan kembali menjadi Draft.')">
                                        <i class="ti ti-x"></i> Reject (Return to Draft)
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="alert alert-info mt-3 small">
                                <i class="ti ti-info-circle"></i> Waiting for Restaurant Manager Approval.
                            </div>
                        @endhasanyrole
                    @endif
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: DETAIL SESI (Looping) --}}
        <div class="col-md-8">
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

            <div class="card mb-3 bg-primary text-white">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        {{-- Kiri: Grand Total Revenue --}}
                        <div class="col-md-7 border-end border-white border-opacity-25">
                            <h6 class="text-white text-opacity-75 text-uppercase small fw-bold mb-1">Total Daily Revenue
                            </h6>
                            <h2 class="mb-0 text-white fw-bold">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h2>

                            {{-- Breakdown Kecil di bawah Total --}}
                            <div class="d-flex flex-wrap gap-3 mt-3 text-white text-opacity-75 small">
                                <div title="Food Revenue"><i class="ti ti-tools-kitchen-2 me-1"></i> F:
                                    {{ number_format($totalFood) }}</div>
                                <div title="Beverage Revenue"><i class="ti ti-glass-full me-1"></i> B:
                                    {{ number_format($totalBev) }}</div>
                                <div title="Event Revenue"><i class="ti ti-calendar-event me-1"></i> E:
                                    {{ number_format($totalEvent) }}</div>
                                <div title="Others Revenue"><i class="ti ti-dots-circle-horizontal me-1"></i> O:
                                    {{ number_format($totalOthers) }}</div>
                            </div>
                        </div>

                        {{-- Kanan: Total Cover --}}
                        <div class="col-md-5 text-center ps-md-4 mt-3 mt-md-0">
                            <h6 class="text-white text-opacity-75 text-uppercase small fw-bold mb-1">Total Pax/Cover</h6>
                            <h2 class="mb-0 text-white fw-bold">{{ number_format($totalCover) }}</h2>
                            <span class="badge bg-white text-primary mt-2">Accumulated</span>
                        </div>
                    </div>
                </div>
            </div>
            @foreach ($dailyReport->details as $detail)
                <div class="card mb-3">
                    <div
                        class="card-header d-flex justify-content-between align-items-center
                    {{ $detail->session_type == 'breakfast'
                        ? 'bg-light-warning'
                        : ($detail->session_type == 'lunch'
                            ? 'bg-light-primary'
                            : 'bg-light-danger') }}">
                        <h5 class="mb-0 text-capitalize">
                            @if ($detail->session_type == 'breakfast')
                                <i class="ti ti-sun me-2"></i>
                            @elseif($detail->session_type == 'lunch')
                                <i class="ti ti-soup me-2"></i>
                            @else
                                <i class="ti ti-moon-stars me-2"></i>
                            @endif
                            {{ $detail->session_type }} Report
                        </h5>
                        @if ($detail->thematic)
                            <span class="badge bg-dark">{{ $detail->thematic }}</span>
                        @endif
                    </div>
                    <div class="card-body">

                        @if(isset($detail->additional_data['nagano_floors']))
                            <ul class="nav nav-tabs mb-3" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active fw-bold" data-bs-toggle="tab" href="#gabungan-{{$detail->id}}" role="tab">Total (Gabungan)</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link fw-bold" data-bs-toggle="tab" href="#lt5-{{$detail->id}}" role="tab">Lantai 5</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link fw-bold" data-bs-toggle="tab" href="#lt6-{{$detail->id}}" role="tab">Lantai 6</a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="gabungan-{{$detail->id}}" role="tabpanel">
                                    @include('daily-reports.partials.show-detail-body', ['data' => $detail])
                                </div>
                                <div class="tab-pane" id="lt5-{{$detail->id}}" role="tabpanel">
                                    @include('daily-reports.partials.show-detail-body', ['data' => (object) $detail->additional_data['nagano_floors']['lt5']])
                                </div>
                                <div class="tab-pane" id="lt6-{{$detail->id}}" role="tabpanel">
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
@endsection
