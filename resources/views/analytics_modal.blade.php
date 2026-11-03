{{-- HEADER MODAL (Tanly Corporate Navy & Gold) --}}
<div class="modal-header bg-[#172a40] text-white px-5 py-4 border-b border-[#0f1c2b] relative overflow-hidden">
    <!-- Gold Top Accent Line -->
    <div class="absolute top-0 left-0 right-0 h-1 bg-[#B89047]"></div>
    <div class="d-flex align-items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-white/10 text-white flex items-center justify-center text-xl shadow-sm shrink-0">
            <i class="ti ti-chart-dots"></i>
        </div>
        <div>
            <div class="d-flex align-items-center gap-2">
                <h5 class="modal-title font-bold text-white mb-0" id="analyticsModalLabel">
                    Analytics Report: <span class="text-[#B89047]">{{ $restaurant->name }}</span>
                </h5>
                <span class="badge bg-white/15 text-white border border-white/25 rounded-full px-2 py-0.5 text-[10px] font-mono">
                    {{ $restaurant->code }}
                </span>
            </div>
            <p class="text-xs text-white/60 mb-0 mt-0.5">Rincian performa cover, revenue, tren hari, dan kompetitor outlet.</p>
        </div>
    </div>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

{{-- BODY MODAL --}}
<div class="modal-body p-4 md:p-5">

    {{-- 1. FILTER SECTION --}}
    <div class="card border border-slate-200/90 rounded-2xl bg-slate-50/70 p-3.5 mb-4 shadow-sm">
        <form id="analytics-filter-form" class="row g-2 align-items-end">
            <div class="col-12 col-sm-4">
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">
                    <i class="ti ti-calendar me-1 text-slate-400"></i> Start Date
                </label>
                <input type="date" id="filter-start-date" class="form-control form-control-sm rounded-xl border-slate-300 text-xs px-3 py-1.5 focus:border-[#172a40]"
                    value="{{ $startDate }}">
            </div>
            <div class="col-12 col-sm-4">
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">
                    <i class="ti ti-calendar me-1 text-slate-400"></i> End Date
                </label>
                <input type="date" id="filter-end-date" class="form-control form-control-sm rounded-xl border-slate-300 text-xs px-3 py-1.5 focus:border-[#172a40]"
                    value="{{ $endDate }}">
            </div>
            <div class="col-12 col-sm-4">
                <button type="button" class="btn btn-sm btn-primary w-100 rounded-xl text-xs font-semibold py-2 d-inline-flex align-items-center justify-center gap-1.5 shadow-sm transition"
                    onclick="loadAnalyticsData({{ $restaurant->id }})">
                    <i class="ti ti-filter"></i> Update Data
                </button>
            </div>
        </form>
    </div>

    {{-- NAV TABS (Horizontal Scrollable Pills) --}}
    <style>
        .nav-tabs-scroll {
            flex-wrap: nowrap;
            overflow-x: auto;
            overflow-y: hidden;
            white-space: nowrap;
            padding-bottom: 6px;
        }
        .nav-tabs-scroll::-webkit-scrollbar {
            height: 5px;
        }
        .nav-tabs-scroll::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 4px;
        }
        .nav-tabs-scroll::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        .nav-tabs-scroll::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .analytics-nav-pill {
            white-space: nowrap;
            font-size: 12px;
            font-weight: 600;
            padding: 0.5rem 0.85rem;
            border-radius: 0.75rem;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            color: #475569;
            transition: all 0.15s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
        }
        .analytics-nav-pill:hover {
            background: #f8fafc;
            color: #172a40;
        }
        .analytics-nav-pill.active {
            background: #172a40 !important;
            border-color: #172a40 !important;
            color: #ffffff !important;
            box-shadow: 0 1px 3px rgba(23, 42, 64, 0.2);
        }
    </style>

    <div class="nav-tabs-scroll d-flex gap-2 mb-4" id="analyticsTab" role="tablist">
        <button class="analytics-nav-pill active" data-bs-toggle="tab" data-bs-target="#tab-cover" type="button">
            <i class="ti ti-users text-sm"></i> 1. Cover Report
        </button>
        <button class="analytics-nav-pill" data-bs-toggle="tab" data-bs-target="#tab-revenue" type="button">
            <i class="ti ti-coin text-sm"></i> 2. Revenue Report
        </button>
        <button class="analytics-nav-pill" data-bs-toggle="tab" data-bs-target="#tab-competitor" type="button">
            <i class="ti ti-building-skyscraper text-sm"></i> 3. Competitor
        </button>
        <button class="analytics-nav-pill" data-bs-toggle="tab" data-bs-target="#tab-daytrend" type="button">
            <i class="ti ti-calendar-event text-sm"></i> 4. Cover by Day
        </button>
        <button class="analytics-nav-pill" data-bs-toggle="tab" data-bs-target="#tab-occasion" type="button">
            <i class="ti ti-confetti text-sm"></i> 5. Occasion
        </button>
        <button class="analytics-nav-pill" data-bs-toggle="tab" data-bs-target="#tab-promo-extras" type="button">
            <i class="ti ti-tag text-sm"></i> 6. Promo & Extras
        </button>
    </div>

    <div class="tab-content" id="analyticsTabContent">

        {{-- TAB 1: COVER REPORT --}}
        <div class="tab-pane fade show active" id="tab-cover">
            <div class="table-responsive rounded-xl border border-slate-200 overflow-hidden shadow-sm">
                <table class="table table-hover table-sm text-center align-middle mb-0">
                    <thead class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <tr>
                            <th class="text-start ps-3 py-2.5">Cover Item</th>
                            @foreach ($sessions as $sess)
                                <th class="text-capitalize py-2.5">{{ $sess }}</th>
                            @endforeach
                            <th class="bg-slate-100 text-[#172a40] py-2.5 pe-3">TOTAL</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @php
                            $colTotals = array_fill_keys($sessions, 0);
                            $grandTotal = 0;
                        @endphp

                        @forelse($coverMatrix as $item => $data)
                            <tr>
                                <td class="text-start ps-3 py-2 font-semibold text-slate-700">{{ $item }}</td>
                                @php $rowTotal = 0; @endphp
                                @foreach ($sessions as $sess)
                                    @php
                                        $val = $data[$sess];
                                        $rowTotal += $val;
                                        $colTotals[$sess] += $val;
                                    @endphp
                                    <td class="py-2 text-slate-600">{{ $val > 0 ? number_format($val) : '-' }}</td>
                                @endforeach
                                @php $grandTotal += $rowTotal; @endphp
                                <td class="fw-bold bg-slate-50 text-[#172a40] pe-3 py-2">{{ number_format($rowTotal) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($sessions) + 2 }}" class="text-slate-400 fst-italic py-4">
                                    Belum ada data cover pada periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if (count($coverMatrix) > 0)
                        <tfoot class="bg-[#172a40] text-white font-bold text-xs">
                            <tr>
                                <td class="text-start ps-3 py-2.5">GRAND TOTAL PAX</td>
                                @foreach ($sessions as $sess)
                                    <td class="py-2.5">{{ number_format($colTotals[$sess]) }}</td>
                                @endforeach
                                <td class="bg-[#B89047] text-white pe-3 py-2.5">{{ number_format($grandTotal) }}</td>
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
                <div class="mt-3 p-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-600 flex items-center gap-2">
                    <i class="ti ti-info-circle text-sm text-[#172a40]"></i>
                    <span><strong>BEO Total (Event Pax):</strong> {{ number_format($beoGrandTotal) }} Pax <span class="text-slate-400">(Tidak termasuk dalam Grand Total Pax)</span></span>
                </div>
            @endif
        </div>

        {{-- TAB 2: REVENUE REPORT --}}
        <div class="tab-pane fade" id="tab-revenue">
            <div class="table-responsive rounded-xl border border-slate-200 overflow-hidden shadow-sm">
                <table class="table table-hover table-sm text-center align-middle mb-0">
                    <thead class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <tr>
                            <th class="text-start ps-3 py-2.5">Revenue Item</th>
                            @foreach ($sessions as $sess)
                                <th class="text-capitalize py-2.5">{{ $sess }}</th>
                            @endforeach
                            <th class="bg-slate-100 text-[#172a40] py-2.5 pe-3">TOTAL</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @php
                            $revColTotals = array_fill_keys($sessions, 0);
                            $revGrandTotal = 0;
                        @endphp

                        @foreach ($revenueMatrix as $item => $data)
                            <tr>
                                <td class="text-start ps-3 py-2 font-semibold text-slate-700">{{ $item }}</td>
                                @php $rowTotal = 0; @endphp
                                @foreach ($sessions as $sess)
                                    @php
                                        $val = $data[$sess];
                                        $rowTotal += $val;
                                        $revColTotals[$sess] += $val;
                                    @endphp
                                    <td class="py-2 {{ $val == 0 ? 'text-slate-300' : 'text-slate-700' }}">
                                        @if($val > 0)
                                            <span class="text-[10px] text-slate-400">Rp</span> {{ number_format($val, 0, ',', '.') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                @endforeach
                                @php $revGrandTotal += $rowTotal; @endphp
                                <td class="fw-bold bg-slate-50 text-[#172a40] pe-3 py-2">
                                    <span class="text-[10px] text-slate-400">Rp</span> {{ number_format($rowTotal, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-[#172a40] text-white font-bold text-xs">
                        <tr>
                            <td class="text-start ps-3 py-2.5">TOTAL REVENUE</td>
                            @foreach ($sessions as $sess)
                                <td class="py-2.5"><span class="text-[10px] text-white/70">Rp</span> {{ number_format($revColTotals[$sess], 0, ',', '.') }}</td>
                            @endforeach
                            <td class="bg-[#B89047] text-white pe-3 py-2.5">
                                <span class="text-[10px] text-white/70">Rp</span> {{ number_format($revGrandTotal, 0, ',', '.') }}
                            </td>
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
                <div class="mt-3 p-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-600 flex items-center gap-2">
                    <i class="ti ti-info-circle text-sm text-[#172a40]"></i>
                    <span><strong>Event Revenue Total:</strong> Rp {{ number_format($eventRevGrandTotal, 0, ',', '.') }} <span class="text-slate-400">(Tidak termasuk dalam Total Revenue)</span></span>
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
                    <div class="mt-4 pt-3 border-t border-slate-100">
                        <h6 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2.5">
                            <i class="ti ti-building-store me-1 text-slate-400"></i> Nagano Revenue Breakdown
                        </h6>
                        <div class="table-responsive rounded-xl border border-slate-200 overflow-hidden shadow-sm">
                            <table class="table table-hover table-sm text-center align-middle mb-0">
                                <thead class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                    <tr>
                                        <th class="text-start ps-3 py-2">Revenue Type</th>
                                        @foreach ($sessions as $sess)
                                            <th class="text-capitalize py-2">{{ $sess }}</th>
                                        @endforeach
                                        <th class="bg-slate-100 text-rose-700 py-2 pe-3">TOTAL</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-xs">
                                    @foreach ($naganoRevenueMatrix as $item => $data)
                                        @php
                                            $rowTotal = 0;
                                            foreach ($sessions as $sess) { $rowTotal += $data[$sess] ?? 0; }
                                        @endphp
                                        @if ($rowTotal > 0)
                                            <tr>
                                                <td class="text-start ps-3 py-2 font-semibold text-slate-700">{{ $item }}</td>
                                                @foreach ($sessions as $sess)
                                                    @php $val = $data[$sess] ?? 0; @endphp
                                                    <td class="py-2 text-slate-600">
                                                        @if ($val > 0)
                                                            <small>Rp</small> {{ number_format($val, 0, ',', '.') }}
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                @endforeach
                                                <td class="fw-bold bg-slate-50 text-rose-700 pe-3 py-2">
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
                                    <tfoot class="bg-rose-900 text-white font-bold text-xs">
                                        <tr>
                                            <td class="text-start ps-3 py-2">TOTAL NAGANO REVENUE</td>
                                            @foreach ($sessions as $sess)
                                                <td><small>Rp</small> {{ number_format($naganoColTotals[$sess], 0, ',', '.') }}</td>
                                            @endforeach
                                            <td class="bg-rose-800 text-white pe-3"><small>Rp</small> {{ number_format($naganoGrandTotal, 0, ',', '.') }}</td>
                                        </tr>
                                    </tfoot>
                                @endif
                            </table>
                        </div>
                    </div>
                @endif
            @endif
        </div>

        {{-- TAB 3: COMPETITOR --}}
        <div class="tab-pane fade" id="tab-competitor">
            <div class="table-responsive rounded-xl border border-slate-200 overflow-hidden shadow-sm">
                <table class="table table-hover table-sm text-center align-middle mb-0">
                    <thead class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <tr>
                            <th class="text-start ps-3 py-2.5">Hotel / Venue</th>
                            @foreach ($sessions as $sess)
                                <th class="text-capitalize py-2.5">{{ $sess }}</th>
                            @endforeach
                            <th class="bg-slate-100 text-[#172a40] py-2.5 pe-3">TOTAL</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @foreach ($competitorMatrix as $item => $data)
                            @php
                                $isOurVenue = Str::startsWith($item, 'Us') || Str::contains(strtolower($item), ['vasa', 'our', 'kita']);
                            @endphp
                            <tr class="{{ $isOurVenue ? 'bg-[#172a40]/5 font-semibold' : '' }}">
                                <td class="text-start ps-3 py-2 font-bold {{ $isOurVenue ? 'text-[#172a40]' : 'text-slate-700' }}">
                                    @if($isOurVenue)
                                        <i class="ti ti-star-filled text-[#B89047] me-1 text-xs"></i>
                                    @endif
                                    {{ $item }}
                                </td>
                                @php $rowTotal = 0; @endphp
                                @foreach ($sessions as $sess)
                                    @php
                                        $val = $data[$sess];
                                        $rowTotal += $val;
                                    @endphp
                                    <td class="py-2 text-slate-600">{{ $val > 0 ? number_format($val) : '-' }}</td>
                                @endforeach
                                <td class="fw-bold {{ $isOurVenue ? 'bg-[#172a40] text-white' : 'bg-slate-50 text-slate-800' }} pe-3 py-2">
                                    {{ number_format($rowTotal) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3 text-xs text-slate-500 flex items-center gap-1.5">
                <i class="ti ti-info-circle text-[#172a40]"></i>
                <span>Data dihitung berdasarkan akumulasi laporan harian pada rentang tanggal terpilih.</span>
            </div>
        </div>

        {{-- TAB 4: WEEKLY TREND --}}
        <div class="tab-pane fade" id="tab-daytrend">
            <div class="table-responsive rounded-xl border border-slate-200 overflow-hidden shadow-sm">
                <table class="table table-hover table-sm text-center align-middle mb-0">
                    <thead class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <tr>
                            <th class="text-start ps-3 py-2.5">Session Type</th>
                            @foreach ($daysOfWeek as $day)
                                <th class="py-2.5">{{ $day }}</th>
                            @endforeach
                            <th class="bg-slate-100 text-[#172a40] py-2.5 pe-3">TOTAL</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @php
                            $dayColTotals = array_fill_keys($daysOfWeek, 0);
                            $dayGrandTotal = 0;
                        @endphp

                        @foreach ($sessions as $sess)
                            <tr>
                                <td class="text-start ps-3 py-2 font-semibold text-slate-700 text-capitalize">{{ $sess }}</td>
                                @php $rowTotal = 0; @endphp

                                @foreach ($daysOfWeek as $day)
                                    @php
                                        $val = $dayTrendMatrix[$sess][$day];
                                        $rowTotal += $val;
                                        $dayColTotals[$day] += $val;
                                    @endphp
                                    <td class="py-2 text-slate-600">{{ $val > 0 ? number_format($val) : '-' }}</td>
                                @endforeach

                                @php $dayGrandTotal += $rowTotal; @endphp
                                <td class="fw-bold bg-slate-50 text-[#172a40] pe-3 py-2">{{ number_format($rowTotal) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-[#172a40] text-white font-bold text-xs">
                        <tr>
                            <td class="text-start ps-3 py-2.5">TOTAL PAX</td>
                            @foreach ($daysOfWeek as $day)
                                <td class="py-2.5">{{ number_format($dayColTotals[$day]) }}</td>
                            @endforeach
                            <td class="bg-[#B89047] text-white pe-3 py-2.5">{{ number_format($dayGrandTotal) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            @if(!empty($setMenuDayTrendMatrix))
                <div class="mt-4 pt-3 border-t border-slate-100">
                    <h6 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2.5">
                        <i class="ti ti-calendar me-1 text-slate-400"></i> Set Menu by Day
                    </h6>
                    <div class="table-responsive rounded-xl border border-slate-200 overflow-hidden shadow-sm">
                        <table class="table table-hover table-sm text-center align-middle mb-0">
                            <thead class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                <tr>
                                    <th class="text-start ps-3 py-2">Set Menu</th>
                                    @foreach ($daysOfWeek as $day)
                                        <th class="py-2">{{ $day }}</th>
                                    @endforeach
                                    <th class="bg-slate-100 text-slate-800 py-2 pe-3">TOTAL</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-xs">
                                @php
                                    $setMenuDayColTotals = array_fill_keys($daysOfWeek, 0);
                                    $setMenuDayGrandTotal = 0;
                                @endphp

                                @foreach ($setMenuDayTrendMatrix as $item => $data)
                                    <tr>
                                        <td class="text-start ps-3 py-2 font-semibold text-slate-700">{{ $item }}</td>
                                        @php $rowTotal = 0; @endphp

                                        @foreach ($daysOfWeek as $day)
                                            @php
                                                $val = $data[$day];
                                                $rowTotal += $val;
                                                $setMenuDayColTotals[$day] += $val;
                                            @endphp
                                            <td class="py-2 text-slate-600">{{ $val > 0 ? number_format($val) : '-' }}</td>
                                        @endforeach

                                        @php $setMenuDayGrandTotal += $rowTotal; @endphp
                                        <td class="fw-bold bg-slate-50 text-slate-800 pe-3 py-2">{{ number_format($rowTotal) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-slate-800 text-white font-bold text-xs">
                                <tr>
                                    <td class="text-start ps-3 py-2">GRAND TOTAL PAX</td>
                                    @foreach ($daysOfWeek as $day)
                                        <td>{{ number_format($setMenuDayColTotals[$day]) }}</td>
                                    @endforeach
                                    <td class="bg-slate-900 text-white pe-3">{{ number_format($setMenuDayGrandTotal) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            @endif

            <div class="mt-3 text-xs text-slate-500 flex items-center gap-1.5">
                <i class="ti ti-info-circle text-[#172a40]"></i>
                <span>Data menampilkan akumulasi cover per hari dalam seminggu.</span>
            </div>
        </div>

        {{-- TAB 5: OCCASION --}}
        <div class="tab-pane fade" id="tab-occasion">
            @php
                $hasOccasion = !empty($occasionMatrix) || !empty($occOthersAgg);
            @endphp

            @if ($hasOccasion)
                <div class="mb-4">
                    <h6 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2.5">
                        <i class="ti ti-confetti me-1 text-slate-400"></i> Occasion / Event Type (Pax)
                    </h6>
                    <div class="table-responsive rounded-xl border border-slate-200 overflow-hidden shadow-sm">
                        <table class="table table-hover table-sm text-center align-middle mb-0">
                            <thead class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                <tr>
                                    <th class="text-start ps-3 py-2.5">Occasion</th>
                                    @foreach ($sessions as $sess)
                                        <th class="text-capitalize py-2.5">{{ $sess }}</th>
                                    @endforeach
                                    <th class="bg-slate-100 text-[#172a40] py-2.5 pe-3">TOTAL</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-xs">
                                @foreach (array_merge($occasionMatrix ?? [], $occOthersAgg ?? []) as $item => $data)
                                    <tr>
                                        <td class="text-start ps-3 py-2 font-semibold text-slate-700">{{ $item }}</td>
                                        @php $rowTotal = 0; @endphp
                                        @foreach ($sessions as $sess)
                                            @php
                                                $val = $data[$sess] ?? 0;
                                                $rowTotal += $val;
                                            @endphp
                                            <td class="py-2 text-slate-600">{{ $val > 0 ? number_format($val) : '-' }}</td>
                                        @endforeach
                                        <td class="fw-bold bg-slate-50 text-[#172a40] pe-3 py-2">{{ number_format($rowTotal) }}</td>
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
                            <tfoot class="bg-[#172a40] text-white font-bold text-xs">
                                <tr>
                                    <td class="text-start ps-3 py-2.5">GRAND TOTAL PAX</td>
                                    @foreach ($sessions as $sess)
                                        <td class="py-2.5">{{ number_format($occColTotals[$sess]) }}</td>
                                    @endforeach
                                    <td class="bg-[#B89047] text-white pe-3 py-2.5">{{ number_format($occGrandTotal) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                @php
                    $hasOccasionRevenue = !empty($occasionRevenueMatrix);
                @endphp
                @if ($hasOccasionRevenue)
                    <div class="mb-4">
                        <h6 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2.5">
                            <i class="ti ti-coin me-1 text-slate-400"></i> Occasion Revenue
                        </h6>
                        <div class="table-responsive rounded-xl border border-slate-200 overflow-hidden shadow-sm">
                            <table class="table table-hover table-sm text-center align-middle mb-0">
                                <thead class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                    <tr>
                                        <th class="text-start ps-3 py-2.5">Occasion</th>
                                        @foreach ($sessions as $sess)
                                            <th class="text-capitalize py-2.5">{{ $sess }}</th>
                                        @endforeach
                                        <th class="bg-slate-100 text-[#172a40] py-2.5 pe-3">TOTAL</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-xs">
                                    @foreach ($occasionRevenueMatrix as $item => $data)
                                        <tr>
                                            <td class="text-start ps-3 py-2 font-semibold text-slate-700">{{ $item }}</td>
                                            @php $rowTotal = 0; @endphp
                                            @foreach ($sessions as $sess)
                                                @php
                                                    $val = $data[$sess] ?? 0;
                                                    $rowTotal += $val;
                                                @endphp
                                                <td class="py-2 text-slate-600">
                                                    @if ($val > 0)
                                                        <small>Rp</small> {{ number_format($val, 0, ',', '.') }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            @endforeach
                                            <td class="fw-bold bg-slate-50 text-[#172a40] pe-3 py-2">
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
                                <tfoot class="bg-[#172a40] text-white font-bold text-xs">
                                    <tr>
                                        <td class="text-start ps-3 py-2.5">TOTAL REVENUE</td>
                                        @foreach ($sessions as $sess)
                                            <td class="py-2.5"><small>Rp</small> {{ number_format($occRevColTotals[$sess], 0, ',', '.') }}</td>
                                        @endforeach
                                        <td class="bg-[#B89047] text-white pe-3 py-2.5"><small>Rp</small> {{ number_format($occRevGrandTotal, 0, ',', '.') }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                @endif
            @endif

            @if (!$hasOccasion)
                <div class="text-center py-5 text-slate-400">
                    <i class="ti ti-confetti fs-1 mb-2 d-block"></i>
                    <p class="text-xs mb-0">Belum ada data Occasion pada periode ini.</p>
                </div>
            @endif
        </div>

        {{-- TAB 6: PROMO & EXTRAS --}}
        <div class="tab-pane fade" id="tab-promo-extras">
            @php
                $hasPromo = !empty($promoMatrix) || !empty($promoOthersAgg);
                $hasPromoRevenue = !empty($promoRevenueMatrix);
                $hasSetMenu = !empty($setMenuMatrix);
                $hasSetMenuRevenue = !empty($setMenuRevenueMatrix);
                $hasUpsellingFood = !empty($upsellingFoodMatrix);
                $hasUpsellingBeverage = !empty($upsellingBeverageMatrix);
            @endphp

            @if ($hasPromo)
                <div class="mb-4">
                    <h6 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2.5">
                        <i class="ti ti-tag me-1 text-slate-400"></i> Promo (Pax)
                    </h6>
                    <div class="table-responsive rounded-xl border border-slate-200 overflow-hidden shadow-sm">
                        <table class="table table-hover table-sm text-center align-middle mb-0">
                            <thead class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                <tr>
                                    <th class="text-start ps-3 py-2.5">Promo Item</th>
                                    @foreach ($sessions as $sess)
                                        <th class="text-capitalize py-2.5">{{ $sess }}</th>
                                    @endforeach
                                    <th class="bg-slate-100 text-emerald-800 py-2.5 pe-3">TOTAL</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-xs">
                                @foreach (array_merge($promoMatrix ?? [], $promoOthersAgg ?? []) as $item => $data)
                                    <tr>
                                        <td class="text-start ps-3 py-2 font-semibold text-slate-700">{{ $item }}</td>
                                        @php $rowTotal = 0; @endphp
                                        @foreach ($sessions as $sess)
                                            @php
                                                $val = $data[$sess] ?? 0;
                                                $rowTotal += $val;
                                            @endphp
                                            <td class="py-2 text-slate-600">{{ $val > 0 ? number_format($val) : '-' }}</td>
                                        @endforeach
                                        <td class="fw-bold bg-slate-50 text-emerald-800 pe-3 py-2">{{ number_format($rowTotal) }}</td>
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
                            <tfoot class="bg-emerald-900 text-white font-bold text-xs">
                                <tr>
                                    <td class="text-start ps-3 py-2.5">GRAND TOTAL PAX</td>
                                    @foreach ($sessions as $sess)
                                        <td class="py-2.5">{{ number_format($promoColTotals[$sess]) }}</td>
                                    @endforeach
                                    <td class="bg-emerald-800 text-white pe-3 py-2.5">{{ number_format($promoGrandTotal) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                @if ($hasPromoRevenue)
                    <div class="mb-4">
                        <h6 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2.5">
                            <i class="ti ti-coin me-1 text-slate-400"></i> Promo Revenue
                        </h6>
                        <div class="table-responsive rounded-xl border border-slate-200 overflow-hidden shadow-sm">
                            <table class="table table-hover table-sm text-center align-middle mb-0">
                                <thead class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                    <tr>
                                        <th class="text-start ps-3 py-2.5">Promo</th>
                                        @foreach ($sessions as $sess)
                                            <th class="text-capitalize py-2.5">{{ $sess }}</th>
                                        @endforeach
                                        <th class="bg-slate-100 text-emerald-800 py-2.5 pe-3">TOTAL</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-xs">
                                    @foreach ($promoRevenueMatrix as $item => $data)
                                        <tr>
                                            <td class="text-start ps-3 py-2 font-semibold text-slate-700">{{ $item }}</td>
                                            @php $rowTotal = 0; @endphp
                                            @foreach ($sessions as $sess)
                                                @php
                                                    $val = $data[$sess] ?? 0;
                                                    $rowTotal += $val;
                                                @endphp
                                                <td class="py-2 text-slate-600">
                                                    @if ($val > 0)
                                                        <small>Rp</small> {{ number_format($val, 0, ',', '.') }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            @endforeach
                                            <td class="fw-bold bg-slate-50 text-emerald-800 pe-3 py-2">
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
                                <tfoot class="bg-emerald-900 text-white font-bold text-xs">
                                    <tr>
                                        <td class="text-start ps-3 py-2.5">TOTAL REVENUE</td>
                                        @foreach ($sessions as $sess)
                                            <td class="py-2.5"><small>Rp</small> {{ number_format($promoRevColTotals[$sess], 0, ',', '.') }}</td>
                                        @endforeach
                                        <td class="bg-emerald-800 text-white pe-3 py-2.5"><small>Rp</small> {{ number_format($promoRevGrandTotal, 0, ',', '.') }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                @endif
            @endif

            @if ($hasSetMenu)
                <div class="mb-4">
                    <h6 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2.5">
                        <i class="ti ti-menu-2 me-1 text-slate-400"></i> Set Menu (Pax)
                    </h6>
                    <div class="table-responsive rounded-xl border border-slate-200 overflow-hidden shadow-sm">
                        <table class="table table-hover table-sm text-center align-middle mb-0">
                            <thead class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                <tr>
                                    <th class="text-start ps-3 py-2.5">Set Menu</th>
                                    @foreach ($sessions as $sess)
                                        <th class="text-capitalize py-2.5">{{ $sess }}</th>
                                    @endforeach
                                    <th class="bg-slate-100 text-slate-800 py-2.5 pe-3">TOTAL</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-xs">
                                @foreach ($setMenuMatrix as $item => $data)
                                    <tr>
                                        <td class="text-start ps-3 py-2 font-semibold text-slate-700">{{ $item }}</td>
                                        @php $rowTotal = 0; @endphp
                                        @foreach ($sessions as $sess)
                                            @php
                                                $val = $data[$sess] ?? 0;
                                                $rowTotal += $val;
                                            @endphp
                                            <td class="py-2 text-slate-600">{{ $val > 0 ? number_format($val) : '-' }}</td>
                                        @endforeach
                                        <td class="fw-bold bg-slate-50 text-slate-800 pe-3 py-2">{{ number_format($rowTotal) }}</td>
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
                            <tfoot class="bg-slate-800 text-white font-bold text-xs">
                                <tr>
                                    <td class="text-start ps-3 py-2.5">GRAND TOTAL PAX</td>
                                    @foreach ($sessions as $sess)
                                        <td class="py-2.5">{{ number_format($setMenuColTotals[$sess]) }}</td>
                                    @endforeach
                                    <td class="bg-slate-900 text-white pe-3 py-2.5">{{ number_format($setMenuGrandTotal) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            @endif

            @if ($hasSetMenuRevenue)
                <div class="mb-4">
                    <h6 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2.5">
                        <i class="ti ti-coin me-1 text-slate-400"></i> Set Menu Revenue
                    </h6>
                    <div class="table-responsive rounded-xl border border-slate-200 overflow-hidden shadow-sm">
                        <table class="table table-hover table-sm text-center align-middle mb-0">
                            <thead class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                <tr>
                                    <th class="text-start ps-3 py-2.5">Set Menu</th>
                                    @foreach ($sessions as $sess)
                                        <th class="text-capitalize py-2.5">{{ $sess }}</th>
                                    @endforeach
                                    <th class="bg-slate-100 text-slate-800 py-2.5 pe-3">TOTAL</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-xs">
                                @foreach ($setMenuRevenueMatrix as $item => $data)
                                    <tr>
                                        <td class="text-start ps-3 py-2 font-semibold text-slate-700">{{ $item }}</td>
                                        @php $rowTotal = 0; @endphp
                                        @foreach ($sessions as $sess)
                                            @php
                                                $val = $data[$sess] ?? 0;
                                                $rowTotal += $val;
                                            @endphp
                                            <td class="py-2 text-slate-600">
                                                @if ($val > 0)
                                                    <small>Rp</small> {{ number_format($val, 0, ',', '.') }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        @endforeach
                                        <td class="fw-bold bg-slate-50 text-slate-800 pe-3 py-2">
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
                            <tfoot class="bg-slate-800 text-white font-bold text-xs">
                                <tr>
                                    <td class="text-start ps-3 py-2.5">TOTAL REVENUE</td>
                                    @foreach ($sessions as $sess)
                                        <td class="py-2.5"><small>Rp</small> {{ number_format($setMenuRevColTotals[$sess], 0, ',', '.') }}</td>
                                    @endforeach
                                    <td class="bg-slate-900 text-white pe-3 py-2.5"><small>Rp</small> {{ number_format($setMenuRevGrandTotal, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            @endif

            @if ($hasUpsellingFood)
                <div class="mb-4">
                    <h6 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2.5">
                        <i class="ti ti-soup me-1 text-slate-400"></i> Upselling Food (Pax)
                    </h6>
                    <div class="table-responsive rounded-xl border border-slate-200 overflow-hidden shadow-sm">
                        <table class="table table-hover table-sm text-center align-middle mb-0">
                            <thead class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                <tr>
                                    <th class="text-start ps-3 py-2.5">Item</th>
                                    @foreach ($sessions as $sess)
                                        <th class="text-capitalize py-2.5">{{ $sess }}</th>
                                    @endforeach
                                    <th class="bg-slate-100 text-sky-800 py-2.5 pe-3">TOTAL</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-xs">
                                @foreach ($upsellingFoodMatrix as $item => $data)
                                    <tr>
                                        <td class="text-start ps-3 py-2 font-semibold text-slate-700">{{ $item }}</td>
                                        @php $rowTotal = 0; @endphp
                                        @foreach ($sessions as $sess)
                                            @php
                                                $val = $data[$sess] ?? 0;
                                                $rowTotal += $val;
                                            @endphp
                                            <td class="py-2 text-slate-600">{{ $val > 0 ? number_format($val) : '-' }}</td>
                                        @endforeach
                                        <td class="fw-bold bg-slate-50 text-sky-800 pe-3 py-2">{{ number_format($rowTotal) }}</td>
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
                            <tfoot class="bg-sky-900 text-white font-bold text-xs">
                                <tr>
                                    <td class="text-start ps-3 py-2.5">GRAND TOTAL PAX</td>
                                    @foreach ($sessions as $sess)
                                        <td class="py-2.5">{{ number_format($upFoodColTotals[$sess]) }}</td>
                                    @endforeach
                                    <td class="bg-sky-800 text-white pe-3 py-2.5">{{ number_format($upFoodGrandTotal) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            @endif

            @if ($hasUpsellingBeverage)
                <div class="mb-4">
                    <h6 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2.5">
                        <i class="ti ti-glass-full me-1 text-slate-400"></i> Upselling Beverage (Pax)
                    </h6>
                    <div class="table-responsive rounded-xl border border-slate-200 overflow-hidden shadow-sm">
                        <table class="table table-hover table-sm text-center align-middle mb-0">
                            <thead class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                <tr>
                                    <th class="text-start ps-3 py-2.5">Item</th>
                                    @foreach ($sessions as $sess)
                                        <th class="text-capitalize py-2.5">{{ $sess }}</th>
                                    @endforeach
                                    <th class="bg-slate-100 text-cyan-800 py-2.5 pe-3">TOTAL</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-xs">
                                @foreach ($upsellingBeverageMatrix as $item => $data)
                                    <tr>
                                        <td class="text-start ps-3 py-2 font-semibold text-slate-700">{{ $item }}</td>
                                        @php $rowTotal = 0; @endphp
                                        @foreach ($sessions as $sess)
                                            @php
                                                $val = $data[$sess] ?? 0;
                                                $rowTotal += $val;
                                            @endphp
                                            <td class="py-2 text-slate-600">{{ $val > 0 ? number_format($val) : '-' }}</td>
                                        @endforeach
                                        <td class="fw-bold bg-slate-50 text-cyan-800 pe-3 py-2">{{ number_format($rowTotal) }}</td>
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
                            <tfoot class="bg-cyan-900 text-white font-bold text-xs">
                                <tr>
                                    <td class="text-start ps-3 py-2.5">GRAND TOTAL PAX</td>
                                    @foreach ($sessions as $sess)
                                        <td class="py-2.5">{{ number_format($upBevColTotals[$sess]) }}</td>
                                    @endforeach
                                    <td class="bg-cyan-800 text-white pe-3 py-2.5">{{ number_format($upBevGrandTotal) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            @endif

            @if (!$hasPromo && !$hasSetMenu && !$hasUpsellingFood && !$hasUpsellingBeverage)
                <div class="text-center py-5 text-slate-400">
                    <i class="ti ti-tag fs-1 mb-2 d-block"></i>
                    <p class="text-xs mb-0">Belum ada data Promo, Set Menu, atau Upselling pada periode ini.</p>
                </div>
            @endif
        </div>

    </div>
</div>

{{-- FOOTER MODAL --}}
<div class="modal-footer bg-slate-50 border-t border-slate-100 px-5 py-3">
    <button type="button" class="btn btn-sm btn-outline-secondary rounded-xl px-4 py-2 text-xs font-semibold" data-bs-dismiss="modal">
        <i class="ti ti-x me-1"></i> Tutup
    </button>
</div>
