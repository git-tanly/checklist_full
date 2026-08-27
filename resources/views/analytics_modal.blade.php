{{-- HEADER MODAL --}}
<div class="modal-header">
    <h5 class="modal-title" id="analyticsModalLabel">
        Analytics Report: <span class="text-primary fw-bold">{{ $restaurant->name }}</span>
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

{{-- BODY MODAL --}}
<div class="modal-body">

    {{-- 1. FILTER SECTION --}}
    <div class="card bg-light border-0 mb-4">
        <div class="card-body py-3">
            <form id="analytics-filter-form" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="small text-muted fw-bold">Start Date</label>
                    <input type="date" id="filter-start-date" class="form-control form-control-sm"
                        value="{{ $startDate }}">
                </div>
                <div class="col-md-4">
                    <label class="small text-muted fw-bold">End Date</label>
                    <input type="date" id="filter-end-date" class="form-control form-control-sm"
                        value="{{ $endDate }}">
                </div>
                <div class="col-md-4">
                    <button type="button" class="btn btn-sm btn-primary w-100"
                        onclick="loadAnalyticsData({{ $restaurant->id }})">
                        <i class="ti ti-filter me-1"></i> Update Data
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- NAV TABS (Agar tidak terlalu panjang ke bawah) --}}
    <style>
        .nav-tabs-scroll {
            flex-wrap: nowrap;
            overflow-x: auto;
            overflow-y: hidden;
            white-space: nowrap;
            padding-bottom: 5px;
        }
        .nav-tabs-scroll::-webkit-scrollbar {
            height: 6px;
        }
        .nav-tabs-scroll::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }
        .nav-tabs-scroll::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }
        .nav-tabs-scroll::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
    </style>
    <ul class="nav nav-tabs mb-3 nav-tabs-scroll" id="analyticsTab" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-cover" type="button">1. Cover
                Report</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-revenue" type="button">2. Revenue
                Report</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-competitor" type="button">3.
                Competitor</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-daytrend" type="button">4. Cover by
                Day</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-occasion" type="button">5. Occasion</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-promo-extras" type="button">6. Promo & Extras</button>
        </li>
    </ul>

    <div class="tab-content" id="analyticsTabContent">

        {{-- TAB 1: COVER REPORT --}}
        <div class="tab-pane fade show active" id="tab-cover">
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-hover text-center align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-start">Cover Item</th>
                            @foreach ($sessions as $sess)
                                <th class="text-capitalize">{{ $sess }}</th>
                            @endforeach
                            <th class="bg-light-primary text-primary">TOTAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $colTotals = array_fill_keys($sessions, 0);
                            $grandTotal = 0;
                        @endphp

                        @forelse($coverMatrix as $item => $data)
                            <tr>
                                <td class="text-start fw-bold text-muted">{{ $item }}</td>
                                @php $rowTotal = 0; @endphp
                                @foreach ($sessions as $sess)
                                    @php
                                        $val = $data[$sess];
                                        $rowTotal += $val;
                                        $colTotals[$sess] += $val;
                                    @endphp
                                    <td>{{ $val > 0 ? number_format($val) : '-' }}</td>
                                @endforeach
                                @php $grandTotal += $rowTotal; @endphp
                                <td class="fw-bold bg-light-primary text-primary">{{ number_format($rowTotal) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($sessions) + 2 }}" class="text-muted fst-italic py-3">No cover
                                    data found for this period.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if (count($coverMatrix) > 0)
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td class="text-start">GRAND TOTAL PAX</td>
                                @foreach ($sessions as $sess)
                                    <td>{{ number_format($colTotals[$sess]) }}</td>
                                @endforeach
                                <td class="bg-primary text-white">{{ number_format($grandTotal) }}</td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
            @if (isset($beoMatrix) && count($beoMatrix) > 0)
                @php
                    $beoGrandTotal = 0;
                    foreach($beoMatrix as $data) {
                        $beoGrandTotal += array_sum($data);
                    }
                @endphp
                    <div class="mt-2 text-muted small">
                        <i class="ti ti-info-circle me-1 text-info"></i> <strong>BEO Total (Event Pax):</strong> {{ number_format($beoGrandTotal) }} Pax (Not included in Grand Total Pax)
                    </div>
            @endif
        </div>

        {{-- TAB 2: REVENUE REPORT --}}
        <div class="tab-pane fade" id="tab-revenue">
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-hover text-center align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-start">Revenue Item</th>
                            @foreach ($sessions as $sess)
                                <th class="text-capitalize">{{ $sess }}</th>
                            @endforeach
                            <th class="bg-light-success text-success">TOTAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $revColTotals = array_fill_keys($sessions, 0);
                            $revGrandTotal = 0;
                        @endphp

                        @foreach ($revenueMatrix as $item => $data)
                            <tr>
                                <td class="text-start fw-bold text-muted">{{ $item }}</td>
                                @php $rowTotal = 0; @endphp
                                @foreach ($sessions as $sess)
                                    @php
                                        $val = $data[$sess];
                                        $rowTotal += $val;
                                        $revColTotals[$sess] += $val;
                                    @endphp
                                    {{-- Tampilkan angka, gunakan class text-muted jika 0 --}}
                                    <td class="{{ $val == 0 ? 'text-muted text-opacity-25' : '' }}">
                                        <small>Rp</small> {{ number_format($val, 0, ',', '.') }}
                                    </td>
                                @endforeach
                                @php $revGrandTotal += $rowTotal; @endphp
                                <td class="fw-bold bg-light-success text-success">
                                    <small>Rp</small> {{ number_format($rowTotal, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td class="text-start">TOTAL REVENUE</td>
                            @foreach ($sessions as $sess)
                                <td><small>Rp</small> {{ number_format($revColTotals[$sess], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="bg-success text-white"><small>Rp</small>
                                {{ number_format($revGrandTotal, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @if (isset($eventRevenueMatrix) && count($eventRevenueMatrix) > 0)
                @php
                    $eventRevGrandTotal = 0;
                    foreach($eventRevenueMatrix as $data) {
                        $eventRevGrandTotal += array_sum($data);
                    }
                @endphp
                    <div class="mt-2 text-muted small">
                        <i class="ti ti-info-circle me-1 text-info"></i> <strong>Event Revenue Total:</strong> Rp {{ number_format($eventRevGrandTotal, 0, ',', '.') }} (Not included in Total Revenue)
                    </div>
            @endif

            @if (isset($restaurant) && $restaurant->code === 'NJR')
                @php
                    $hasNaganoRevenue = false;
                    foreach ($naganoRevenueMatrix ?? [] as $data) {
                        foreach ($sessions as $sess) {
                            if (($data[$sess] ?? 0) > 0) { $hasNaganoRevenue = true; break 2; }
                        }
                    }
                @endphp
                @if ($hasNaganoRevenue)
                    <h6 class="text-muted text-uppercase small fw-bold mb-3 mt-4">Nagano Revenue Breakdown</h6>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered table-sm table-hover text-center align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-start">Revenue Type</th>
                                    @foreach ($sessions as $sess)
                                        <th class="text-capitalize">{{ $sess }}</th>
                                    @endforeach
                                    <th class="bg-light-danger text-danger">TOTAL</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($naganoRevenueMatrix as $item => $data)
                                    @php
                                        $rowTotal = 0;
                                        foreach ($sessions as $sess) { $rowTotal += $data[$sess] ?? 0; }
                                    @endphp
                                    @if ($rowTotal > 0)
                                        <tr>
                                            <td class="text-start fw-bold text-muted">{{ $item }}</td>
                                            @foreach ($sessions as $sess)
                                                @php $val = $data[$sess] ?? 0; @endphp
                                                <td>
                                                    @if ($val > 0)
                                                        <small>Rp</small> {{ number_format($val, 0, ',', '.') }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            @endforeach
                                            <td class="fw-bold bg-light-danger text-danger">
                                                <small>Rp</small> {{ number_format($rowTotal, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                            @php
                                $naganoColTotals = array_fill_keys($sessions, 0);
                                $naganoGrandTotal = 0;
                                foreach ($naganoRevenueMatrix as $data) {
                                    foreach ($sessions as $sess) {
                                        $val = $data[$sess] ?? 0;
                                        $naganoColTotals[$sess] += $val;
                                        $naganoGrandTotal += $val;
                                    }
                                }
                            @endphp
                            @if ($naganoGrandTotal > 0)
                                <tfoot class="table-light fw-bold">
                                    <tr>
                                        <td class="text-start">TOTAL NAGANO REVENUE</td>
                                        @foreach ($sessions as $sess)
                                            <td><small>Rp</small> {{ number_format($naganoColTotals[$sess], 0, ',', '.') }}</td>
                                        @endforeach
                                        <td class="bg-danger text-white"><small>Rp</small> {{ number_format($naganoGrandTotal, 0, ',', '.') }}</td>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                @endif
            @endif
        </div>

        {{-- TAB 3: COMPETITOR --}}
        <div class="tab-pane fade" id="tab-competitor">
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-hover text-center align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-start">Hotel / Venue</th>
                            @foreach ($sessions as $sess)
                                <th class="text-capitalize">{{ $sess }}</th>
                            @endforeach
                            <th class="bg-light-dark text-dark">TOTAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($competitorMatrix as $item => $data)
                            {{-- Highlight baris 'Us' --}}
                            <tr class="{{ Str::startsWith($item, 'Us') ? 'table-info' : '' }}">
                                <td class="text-start fw-bold text-muted">{{ $item }}</td>
                                @php $rowTotal = 0; @endphp
                                @foreach ($sessions as $sess)
                                    @php
                                        $val = $data[$sess];
                                        $rowTotal += $val;
                                    @endphp
                                    <td>{{ $val > 0 ? number_format($val) : '-' }}</td>
                                @endforeach
                                <td class="fw-bold">{{ number_format($rowTotal) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3 small text-muted">
                <i class="ti ti-info-circle me-1"></i> Data based on accumulated daily reports within the selected
                period.
            </div>
        </div>

        {{-- TAB 4: WEEKLY TREND --}}
        <div class="tab-pane fade" id="tab-daytrend">
            {{-- C. TABEL (Sesi di Baris, Hari di Kolom) --}}
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-hover text-center align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-start">Session Type</th>
                            @foreach ($daysOfWeek as $day)
                                <th>{{ $day }}</th>
                            @endforeach
                            <th class="bg-light-primary text-primary">TOTAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $dayColTotals = array_fill_keys($daysOfWeek, 0);
                            $dayGrandTotal = 0;
                        @endphp

                        @foreach ($sessions as $sess)
                            <tr>
                                <td class="text-start fw-bold text-muted text-capitalize">{{ $sess }}</td>
                                @php $rowTotal = 0; @endphp

                                @foreach ($daysOfWeek as $day)
                                    @php
                                        $val = $dayTrendMatrix[$sess][$day];
                                        $rowTotal += $val;
                                        $dayColTotals[$day] += $val;
                                    @endphp
                                    <td>{{ $val > 0 ? number_format($val) : '-' }}</td>
                                @endforeach

                                @php $dayGrandTotal += $rowTotal; @endphp
                                <td class="fw-bold bg-light-primary text-primary">{{ number_format($rowTotal) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td class="text-start">TOTAL PAX</td>
                            @foreach ($daysOfWeek as $day)
                                <td>{{ number_format($dayColTotals[$day]) }}</td>
                            @endforeach
                            <td class="bg-primary text-white">{{ number_format($dayGrandTotal) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @if(!empty($setMenuDayTrendMatrix))
                <h6 class="text-muted text-uppercase small fw-bold mb-3 mt-4">Set Menu by Day</h6>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm table-hover text-center align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-start">Set Menu</th>
                                @foreach ($daysOfWeek as $day)
                                    <th>{{ $day }}</th>
                                @endforeach
                                <th class="bg-light-secondary text-dark">TOTAL</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $setMenuDayColTotals = array_fill_keys($daysOfWeek, 0);
                                $setMenuDayGrandTotal = 0;
                            @endphp

                            @foreach ($setMenuDayTrendMatrix as $item => $data)
                                <tr>
                                    <td class="text-start fw-bold text-muted">{{ $item }}</td>
                                    @php $rowTotal = 0; @endphp

                                    @foreach ($daysOfWeek as $day)
                                        @php
                                            $val = $data[$day];
                                            $rowTotal += $val;
                                            $setMenuDayColTotals[$day] += $val;
                                        @endphp
                                        <td>{{ $val > 0 ? number_format($val) : '-' }}</td>
                                    @endforeach

                                    @php $setMenuDayGrandTotal += $rowTotal; @endphp
                                    <td class="fw-bold bg-light-secondary text-dark">{{ number_format($rowTotal) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td class="text-start">GRAND TOTAL PAX</td>
                                @foreach ($daysOfWeek as $day)
                                    <td>{{ number_format($setMenuDayColTotals[$day]) }}</td>
                                @endforeach
                                <td class="bg-secondary text-white">{{ number_format($setMenuDayGrandTotal) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif

            <div class="mt-3 small text-muted">
                <i class="ti ti-info-circle me-1"></i> Data shows accumulated pax count per day of the week.
            </div>
        </div>

        {{-- TAB 5: OCCASION --}}
        <div class="tab-pane fade" id="tab-occasion">
            @php
                $hasOccasion = !empty($occasionMatrix) || !empty($occOthersAgg);
            @endphp

            @if ($hasOccasion)
                <h6 class="text-muted text-uppercase small fw-bold mb-3 mt-2">Occasion / Event Type</h6>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-sm table-hover text-center align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-start">Occasion</th>
                                @foreach ($sessions as $sess)
                                    <th class="text-capitalize">{{ $sess }}</th>
                                @endforeach
                                <th class="bg-light-primary text-primary">TOTAL</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach (array_merge($occasionMatrix ?? [], $occOthersAgg ?? []) as $item => $data)
                                <tr>
                                    <td class="text-start fw-bold text-muted">{{ $item }}</td>
                                    @php $rowTotal = 0; @endphp
                                    @foreach ($sessions as $sess)
                                        @php
                                            $val = $data[$sess] ?? 0;
                                            $rowTotal += $val;
                                        @endphp
                                        <td>{{ $val > 0 ? number_format($val) : '-' }}</td>
                                    @endforeach
                                    <td class="fw-bold bg-light-primary text-primary">{{ number_format($rowTotal) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        @php
                            $occColTotals = array_fill_keys($sessions, 0);
                            $occGrandTotal = 0;
                            foreach (array_merge($occasionMatrix ?? [], $occOthersAgg ?? []) as $data) {
                                foreach ($sessions as $sess) {
                                    $val = $data[$sess] ?? 0;
                                    $occColTotals[$sess] += $val;
                                    $occGrandTotal += $val;
                                }
                            }
                        @endphp
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td class="text-start">GRAND TOTAL PAX</td>
                                @foreach ($sessions as $sess)
                                    <td>{{ number_format($occColTotals[$sess]) }}</td>
                                @endforeach
                                <td class="bg-primary text-white">{{ number_format($occGrandTotal) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @php
                    $hasOccasionRevenue = !empty($occasionRevenueMatrix);
                @endphp
                @if ($hasOccasionRevenue)
                    <h6 class="text-muted text-uppercase small fw-bold mb-3">Occasion Revenue</h6>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered table-sm table-hover text-center align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-start">Occasion</th>
                                    @foreach ($sessions as $sess)
                                        <th class="text-capitalize">{{ $sess }}</th>
                                    @endforeach
                                    <th class="bg-light-primary text-primary">TOTAL</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($occasionRevenueMatrix as $item => $data)
                                    <tr>
                                        <td class="text-start fw-bold text-muted">{{ $item }}</td>
                                        @php $rowTotal = 0; @endphp
                                        @foreach ($sessions as $sess)
                                            @php
                                                $val = $data[$sess] ?? 0;
                                                $rowTotal += $val;
                                            @endphp
                                            <td>
                                                @if ($val > 0)
                                                    <small>Rp</small> {{ number_format($val, 0, ',', '.') }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        @endforeach
                                        <td class="fw-bold bg-light-primary text-primary">
                                            <small>Rp</small> {{ number_format($rowTotal, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            @php
                                $occRevColTotals = array_fill_keys($sessions, 0);
                                $occRevGrandTotal = 0;
                                foreach ($occasionRevenueMatrix as $data) {
                                    foreach ($sessions as $sess) {
                                        $val = $data[$sess] ?? 0;
                                        $occRevColTotals[$sess] += $val;
                                        $occRevGrandTotal += $val;
                                    }
                                }
                            @endphp
                            <tfoot class="table-light fw-bold">
                                <tr>
                                    <td class="text-start">TOTAL REVENUE</td>
                                    @foreach ($sessions as $sess)
                                        <td><small>Rp</small> {{ number_format($occRevColTotals[$sess], 0, ',', '.') }}</td>
                                    @endforeach
                                    <td class="bg-primary text-white"><small>Rp</small> {{ number_format($occRevGrandTotal, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @endif
            @endif

            @if (!$hasOccasion)
                <div class="text-center py-5 text-muted">
                    <i class="ti ti-info-circle fs-1 mb-3 d-block"></i>
                    <p>No Occasion data found for this period.</p>
                </div>
            @endif
        </div>

        {{-- TAB 6: PROMO & EXTRAS --}}
        <div class="tab-pane fade" id="tab-promo-extras">
            @php
                $hasPromo = !empty($promoMatrix) || !empty($promoOthersAgg);
            @endphp

            @if ($hasPromo)
                <h6 class="text-muted text-uppercase small fw-bold mb-3 mt-4">Promo</h6>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-sm table-hover text-center align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-start">Promo</th>
                                @foreach ($sessions as $sess)
                                    <th class="text-capitalize">{{ $sess }}</th>
                                @endforeach
                                <th class="bg-light-success text-success">TOTAL</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach (array_merge($promoMatrix ?? [], $promoOthersAgg ?? []) as $item => $data)
                                <tr>
                                    <td class="text-start fw-bold text-muted">{{ $item }}</td>
                                    @php $rowTotal = 0; @endphp
                                    @foreach ($sessions as $sess)
                                        @php
                                            $val = $data[$sess] ?? 0;
                                            $rowTotal += $val;
                                        @endphp
                                        <td>{{ $val > 0 ? number_format($val) : '-' }}</td>
                                    @endforeach
                                    <td class="fw-bold bg-light-success text-success">{{ number_format($rowTotal) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        @php
                            $promoColTotals = array_fill_keys($sessions, 0);
                            $promoGrandTotal = 0;
                            foreach (array_merge($promoMatrix ?? [], $promoOthersAgg ?? []) as $data) {
                                foreach ($sessions as $sess) {
                                    $val = $data[$sess] ?? 0;
                                    $promoColTotals[$sess] += $val;
                                    $promoGrandTotal += $val;
                                }
                            }
                        @endphp
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td class="text-start">GRAND TOTAL PAX</td>
                                @foreach ($sessions as $sess)
                                    <td>{{ number_format($promoColTotals[$sess]) }}</td>
                                @endforeach
                                <td class="bg-success text-white">{{ number_format($promoGrandTotal) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @php
                    $hasPromoRevenue = !empty($promoRevenueMatrix);
                @endphp
                @if ($hasPromoRevenue)
                    <h6 class="text-muted text-uppercase small fw-bold mb-3">Promo Revenue</h6>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered table-sm table-hover text-center align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-start">Promo</th>
                                    @foreach ($sessions as $sess)
                                        <th class="text-capitalize">{{ $sess }}</th>
                                    @endforeach
                                    <th class="bg-light-success text-success">TOTAL</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($promoRevenueMatrix as $item => $data)
                                    <tr>
                                        <td class="text-start fw-bold text-muted">{{ $item }}</td>
                                        @php $rowTotal = 0; @endphp
                                        @foreach ($sessions as $sess)
                                            @php
                                                $val = $data[$sess] ?? 0;
                                                $rowTotal += $val;
                                            @endphp
                                            <td>
                                                @if ($val > 0)
                                                    <small>Rp</small> {{ number_format($val, 0, ',', '.') }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        @endforeach
                                        <td class="fw-bold bg-light-success text-success">
                                            <small>Rp</small> {{ number_format($rowTotal, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            @php
                                $promoRevColTotals = array_fill_keys($sessions, 0);
                                $promoRevGrandTotal = 0;
                                foreach ($promoRevenueMatrix as $data) {
                                    foreach ($sessions as $sess) {
                                        $val = $data[$sess] ?? 0;
                                        $promoRevColTotals[$sess] += $val;
                                        $promoRevGrandTotal += $val;
                                    }
                                }
                            @endphp
                            <tfoot class="table-light fw-bold">
                                <tr>
                                    <td class="text-start">TOTAL REVENUE</td>
                                    @foreach ($sessions as $sess)
                                        <td><small>Rp</small> {{ number_format($promoRevColTotals[$sess], 0, ',', '.') }}</td>
                                    @endforeach
                                    <td class="bg-success text-white"><small>Rp</small> {{ number_format($promoRevGrandTotal, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @endif
            @endif

            @php
                $hasSetMenu = !empty($setMenuMatrix);
                $hasSetMenuRevenue = !empty($setMenuRevenueMatrix);
            @endphp
            @if ($hasSetMenu)
                <h6 class="text-muted text-uppercase small fw-bold mb-3 mt-4">Set Menu</h6>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-sm table-hover text-center align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-start">Set Menu</th>
                                @foreach ($sessions as $sess)
                                    <th class="text-capitalize">{{ $sess }}</th>
                                @endforeach
                                <th class="bg-light-secondary text-dark">TOTAL</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($setMenuMatrix as $item => $data)
                                <tr>
                                    <td class="text-start fw-bold text-muted">{{ $item }}</td>
                                    @php $rowTotal = 0; @endphp
                                    @foreach ($sessions as $sess)
                                        @php
                                            $val = $data[$sess] ?? 0;
                                            $rowTotal += $val;
                                        @endphp
                                        <td>{{ $val > 0 ? number_format($val) : '-' }}</td>
                                    @endforeach
                                    <td class="fw-bold bg-light-secondary text-dark">{{ number_format($rowTotal) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        @php
                            $setMenuColTotals = array_fill_keys($sessions, 0);
                            $setMenuGrandTotal = 0;
                            foreach ($setMenuMatrix as $data) {
                                foreach ($sessions as $sess) {
                                    $val = $data[$sess] ?? 0;
                                    $setMenuColTotals[$sess] += $val;
                                    $setMenuGrandTotal += $val;
                                }
                            }
                        @endphp
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td class="text-start">GRAND TOTAL PAX</td>
                                @foreach ($sessions as $sess)
                                    <td>{{ number_format($setMenuColTotals[$sess]) }}</td>
                                @endforeach
                                <td class="bg-secondary text-white">{{ number_format($setMenuGrandTotal) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
            @if ($hasSetMenuRevenue)
                <h6 class="text-muted text-uppercase small fw-bold mb-3">Set Menu Revenue</h6>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-sm table-hover text-center align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-start">Set Menu</th>
                                @foreach ($sessions as $sess)
                                    <th class="text-capitalize">{{ $sess }}</th>
                                @endforeach
                                <th class="bg-light-secondary text-dark">TOTAL</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($setMenuRevenueMatrix as $item => $data)
                                <tr>
                                    <td class="text-start fw-bold text-muted">{{ $item }}</td>
                                    @php $rowTotal = 0; @endphp
                                    @foreach ($sessions as $sess)
                                        @php
                                            $val = $data[$sess] ?? 0;
                                            $rowTotal += $val;
                                        @endphp
                                        <td>
                                            @if ($val > 0)
                                                <small>Rp</small> {{ number_format($val, 0, ',', '.') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                    @endforeach
                                    <td class="fw-bold bg-light-secondary text-dark">
                                        <small>Rp</small> {{ number_format($rowTotal, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        @php
                            $setMenuRevColTotals = array_fill_keys($sessions, 0);
                            $setMenuRevGrandTotal = 0;
                            foreach ($setMenuRevenueMatrix as $data) {
                                foreach ($sessions as $sess) {
                                    $val = $data[$sess] ?? 0;
                                    $setMenuRevColTotals[$sess] += $val;
                                    $setMenuRevGrandTotal += $val;
                                }
                            }
                        @endphp
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td class="text-start">TOTAL REVENUE</td>
                                @foreach ($sessions as $sess)
                                    <td><small>Rp</small> {{ number_format($setMenuRevColTotals[$sess], 0, ',', '.') }}</td>
                                @endforeach
                                <td class="bg-secondary text-white"><small>Rp</small> {{ number_format($setMenuRevGrandTotal, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif

            @php
                $hasUpsellingFood = !empty($upsellingFoodMatrix);
                $hasUpsellingBeverage = !empty($upsellingBeverageMatrix);
            @endphp
            @if ($hasUpsellingFood)
                <h6 class="text-muted text-uppercase small fw-bold mb-3 mt-4">Upselling Food</h6>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-sm table-hover text-center align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-start">Item</th>
                                @foreach ($sessions as $sess)
                                    <th class="text-capitalize">{{ $sess }}</th>
                                @endforeach
                                <th class="bg-light-info text-info">TOTAL</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($upsellingFoodMatrix as $item => $data)
                                <tr>
                                    <td class="text-start fw-bold text-muted">{{ $item }}</td>
                                    @php $rowTotal = 0; @endphp
                                    @foreach ($sessions as $sess)
                                        @php
                                            $val = $data[$sess] ?? 0;
                                            $rowTotal += $val;
                                        @endphp
                                        <td>{{ $val > 0 ? number_format($val) : '-' }}</td>
                                    @endforeach
                                    <td class="fw-bold bg-light-info text-info">{{ number_format($rowTotal) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        @php
                            $upFoodColTotals = array_fill_keys($sessions, 0);
                            $upFoodGrandTotal = 0;
                            foreach ($upsellingFoodMatrix as $data) {
                                foreach ($sessions as $sess) {
                                    $val = $data[$sess] ?? 0;
                                    $upFoodColTotals[$sess] += $val;
                                    $upFoodGrandTotal += $val;
                                }
                            }
                        @endphp
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td class="text-start">GRAND TOTAL PAX</td>
                                @foreach ($sessions as $sess)
                                    <td>{{ number_format($upFoodColTotals[$sess]) }}</td>
                                @endforeach
                                <td class="bg-info text-white">{{ number_format($upFoodGrandTotal) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif

            @if ($hasUpsellingBeverage)
                <h6 class="text-muted text-uppercase small fw-bold mb-3 mt-4">Upselling Beverage</h6>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-sm table-hover text-center align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-start">Item</th>
                                @foreach ($sessions as $sess)
                                    <th class="text-capitalize">{{ $sess }}</th>
                                @endforeach
                                <th class="bg-light-info text-info">TOTAL</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($upsellingBeverageMatrix as $item => $data)
                                <tr>
                                    <td class="text-start fw-bold text-muted">{{ $item }}</td>
                                    @php $rowTotal = 0; @endphp
                                    @foreach ($sessions as $sess)
                                        @php
                                            $val = $data[$sess] ?? 0;
                                            $rowTotal += $val;
                                        @endphp
                                        <td>{{ $val > 0 ? number_format($val) : '-' }}</td>
                                    @endforeach
                                    <td class="fw-bold bg-light-info text-info">{{ number_format($rowTotal) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        @php
                            $upBevColTotals = array_fill_keys($sessions, 0);
                            $upBevGrandTotal = 0;
                            foreach ($upsellingBeverageMatrix as $data) {
                                foreach ($sessions as $sess) {
                                    $val = $data[$sess] ?? 0;
                                    $upBevColTotals[$sess] += $val;
                                    $upBevGrandTotal += $val;
                                }
                            }
                        @endphp
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td class="text-start">GRAND TOTAL PAX</td>
                                @foreach ($sessions as $sess)
                                    <td>{{ number_format($upBevColTotals[$sess]) }}</td>
                                @endforeach
                                <td class="bg-info text-white">{{ number_format($upBevGrandTotal) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif

            @if (!$hasPromo && !$hasSetMenu && !$hasUpsellingFood && !$hasUpsellingBeverage)
                <div class="text-center py-5 text-muted">
                    <i class="ti ti-info-circle fs-1 mb-3 d-block"></i>
                    <p>No Promo, Set Menu, or Upselling data found for this period.</p>
                </div>
            @endif
        </div>

    </div>
</div>

{{-- FOOTER MODAL --}}
<div class="modal-footer bg-light">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
</div>
