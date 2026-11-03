@extends('layouts.portal')

@section('title', 'Budget & Forecast')
@section('breadcrumb', 'Budget & Forecast')

@section('content')
<div class="space-y-6">

    {{-- 1. EXECUTIVE HEADER BANNER --}}
    <div class="rounded-2xl border border-slate-200/90 shadow-sm bg-white p-5 md:p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 shadow-sm" style="background-color: #172a40; color: #B89047;">
                    <i class="ti ti-target text-2xl"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold tracking-tight text-slate-800">Budget & Forecast</h1>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            Fiscal Year {{ $year }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">Kelola target anggaran (budget) dan estimasi pendapatan (forecast) bulanan seluruh outlet</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('dashboard') }}" class="btn btn-light rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-100 transition inline-flex items-center gap-1.5 shadow-sm">
                    <i class="ti ti-arrow-left text-base"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    {{-- ALERTS --}}
    @if (session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50/90 p-4 text-emerald-800 shadow-sm flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-700 shrink-0">
                    <i class="ti ti-check text-lg"></i>
                </div>
                <div>
                    <h5 class="text-xs font-bold uppercase tracking-wider text-emerald-900 mb-0.5">Berhasil</h5>
                    <p class="text-xs font-medium text-emerald-800 mb-0">{{ session('success') }}</p>
                </div>
            </div>
            <button type="button" class="btn-close text-xs text-emerald-700" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-2xl border border-rose-200 bg-rose-50/90 p-4 text-rose-800 shadow-sm">
            <div class="flex items-start gap-3">
                <div class="w-9 h-9 rounded-xl bg-rose-100 flex items-center justify-center text-rose-700 shrink-0">
                    <i class="ti ti-alert-triangle text-lg"></i>
                </div>
                <div class="flex-1">
                    <h5 class="text-xs font-bold uppercase tracking-wider text-rose-900 mb-1">Terjadi Kesalahan Validasi</h5>
                    <ul class="text-xs space-y-1 list-disc list-inside text-rose-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {{-- TWO COLUMN MAIN GRID --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        {{-- LEFT COLUMN: FORM SET BUDGET & FORECAST --}}
        <div class="lg:col-span-5 xl:col-span-4">
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden sticky top-24">
                {{-- Header --}}
                <div class="bg-gradient-to-r from-slate-900 via-[#172a40] to-slate-800 text-white px-5 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center text-[#B89047]">
                            <i class="ti ti-adjustments text-base"></i>
                        </span>
                        <div>
                            <h2 class="text-sm font-bold text-white tracking-tight">Set Budget & Forecast</h2>
                            <p class="text-[11px] text-slate-300 mb-0">Input target baru atau perbarui eksisting</p>
                        </div>
                    </div>
                    <span class="w-2 h-2 rounded-full bg-[#B89047]"></span>
                </div>

                {{-- Form Body --}}
                <div class="p-5">
                    <form action="{{ route('revenue-targets.store') }}" method="POST" id="form-target">
                        @csrf

                        {{-- Outlet / Restaurant Select --}}
                        <div class="mb-4">
                            <label class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center gap-1.5">
                                <i class="ti ti-building-store text-slate-400"></i> Restaurant / Outlet
                                <span class="text-rose-500">*</span>
                            </label>
                            <select name="restaurant_id" class="w-full px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all" required>
                                @foreach ($restaurants as $rest)
                                    <option value="{{ $rest->id }}" {{ old('restaurant_id') == $rest->id ? 'selected' : '' }}>
                                        {{ $rest->name }} ({{ $rest->code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Full Year Switch Option --}}
                        <div class="p-3 bg-slate-50 border border-slate-200/90 rounded-xl mb-4 transition-colors hover:border-slate-300">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <label for="is_full_year" class="text-xs font-bold text-slate-700 cursor-pointer flex items-center gap-1.5 mb-0.5">
                                        <i class="ti ti-calendar-repeat text-[#B89047]"></i> Set for Full Year
                                    </label>
                                    <p class="text-[11px] text-slate-500 mb-0">Terapkan nominal ke seluruh bulan (Jan - Des)</p>
                                </div>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input cursor-pointer" type="checkbox" name="is_full_year" id="is_full_year" value="1" {{ old('is_full_year') ? 'checked' : '' }}>
                                </div>
                            </div>
                        </div>

                        {{-- Month & Year Grid --}}
                        <div class="grid grid-cols-2 gap-3 mb-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center gap-1.5">
                                    <i class="ti ti-calendar text-slate-400"></i> Month
                                </label>
                                <select name="month" id="month_select" class="w-full px-3 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all">
                                    @foreach (range(1, 12) as $m)
                                        <option value="{{ $m }}" {{ (old('month') ?? date('n')) == $m ? 'selected' : '' }}>
                                            {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center gap-1.5">
                                    <i class="ti ti-calendar-event text-slate-400"></i> Year
                                    <span class="text-rose-500">*</span>
                                </label>
                                <select name="year" class="w-full px-3 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all" required>
                                    @foreach (range(date('Y') - 1, date('Y') + 1) as $y)
                                        <option value="{{ $y }}" {{ (old('year') ?? $year) == $y ? 'selected' : '' }}>
                                            {{ $y }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Budget Amount --}}
                        <div class="mb-4">
                            <label class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center justify-between">
                                <span class="flex items-center gap-1.5">
                                    <i class="ti ti-coin text-slate-400"></i> Budget Amount (IDR)
                                    <span class="text-rose-500">*</span>
                                </span>
                                <span class="text-[10px] font-medium text-slate-400 uppercase">Anggaran Resmi</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                                <input type="text" name="budget_amount" class="w-full pl-10 pr-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all rupiah"
                                    value="{{ old('budget_amount') }}" placeholder="0" required autocomplete="off">
                            </div>
                        </div>

                        {{-- Forecast Amount --}}
                        <div class="mb-5">
                            <label class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center justify-between">
                                <span class="flex items-center gap-1.5">
                                    <i class="ti ti-trending-up text-slate-400"></i> Forecast Amount (IDR)
                                    <span class="text-rose-500">*</span>
                                </span>
                                <span class="text-[10px] font-medium text-slate-400 uppercase">Estimasi Aktual</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                                <input type="text" name="amount" class="w-full pl-10 pr-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all rupiah"
                                    value="{{ old('amount') }}" placeholder="0" required autocomplete="off">
                            </div>
                        </div>

                        {{-- Action Button --}}
                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl font-bold text-sm text-white bg-[#172a40] hover:bg-[#112031] transition-all shadow-sm hover:shadow cursor-pointer">
                            <i class="ti ti-device-floppy text-base"></i> Save Budget & Forecast
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN: LIST DATA --}}
        <div class="lg:col-span-7 xl:col-span-8">
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden">
                {{-- Header with Year Filter --}}
                <div class="px-5 py-4 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3 bg-slate-50/40">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-[#172a40]/5 border border-[#172a40]/10 flex items-center justify-center text-[#172a40]">
                            <i class="ti ti-list text-base"></i>
                        </div>
                        <div>
                            <h2 class="text-sm font-bold text-slate-800 tracking-tight">Budget & Forecast Directory</h2>
                            <p class="text-[11px] text-slate-400 mb-0">Daftar target pendapatan tahun buku <strong>{{ $year }}</strong></p>
                        </div>
                    </div>

                    {{-- Filter Tahun --}}
                    <form action="{{ route('revenue-targets.index') }}" method="GET" class="flex items-center gap-2">
                        <span class="text-xs font-medium text-slate-500">Tahun:</span>
                        <div class="relative">
                            <select name="year" class="px-3 py-1.5 pr-8 bg-white border border-slate-300 rounded-xl text-xs font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all cursor-pointer"
                                onchange="this.form.submit()">
                                @foreach (range(date('Y') - 1, date('Y') + 1) as $y)
                                    <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>
                                        {{ $y }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>

                {{-- Table Responsive --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200/80 bg-slate-50/70 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                                <th class="py-3 px-4">Restaurant</th>
                                <th class="py-3 px-4">Month</th>
                                <th class="py-3 px-4 text-right">Budget (IDR)</th>
                                <th class="py-3 px-4 text-right">Forecast (IDR)</th>
                                <th class="py-3 px-4 text-right">Last Updated</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs">
                            @forelse($targets as $target)
                                <tr class="hover:bg-slate-50/60 transition-colors">
                                    <td class="py-3.5 px-4">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-7 h-7 rounded-lg bg-[#172a40]/5 border border-[#172a40]/10 flex items-center justify-center text-[#172a40] font-bold text-[10px] shrink-0">
                                                {{ $target->restaurant->code ?? 'RES' }}
                                            </div>
                                            <span class="font-bold text-slate-800">{{ $target->restaurant->name }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-sky-50 text-sky-800 border border-sky-200">
                                            <i class="ti ti-calendar text-xs text-sky-600"></i>
                                            {{ DateTime::createFromFormat('!m', $target->month)->format('F') }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-right font-bold text-slate-800">
                                        Rp {{ number_format($target->budget_amount, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-4 text-right font-bold text-[#172a40]">
                                        Rp {{ number_format($target->amount, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-4 text-right text-slate-400">
                                        <span class="inline-flex items-center gap-1 text-[11px]">
                                            <i class="ti ti-clock text-xs"></i>
                                            {{ $target->updated_at->diffForHumans() }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400 text-xl mb-3">
                                                <i class="ti ti-database-off"></i>
                                            </div>
                                            <h6 class="text-sm font-bold text-slate-700 mb-1">Belum Ada Target Revenue</h6>
                                            <p class="text-xs text-slate-400 max-w-sm">Tidak ditemukan data budget & forecast untuk tahun <strong>{{ $year }}</strong>. Silakan masukkan data melalui formulir di samping.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Footer --}}
                @if ($targets->hasPages())
                    <div class="px-5 py-3.5 border-t border-slate-100 bg-slate-50/30 flex items-center justify-between">
                        <div class="text-xs text-slate-500">
                            Showing {{ $targets->firstItem() }} to {{ $targets->lastItem() }} of {{ $targets->total() }} entries
                        </div>
                        <div>
                            {{ $targets->withQueryString()->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- SCRIPT: RUPIAH FORMATTER & FULL YEAR TOGGLE --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Rupiah input masking
        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('rupiah')) {
                let value = e.target.value;
                let numberString = value.replace(/[^,\d]/g, '').toString();
                let split = numberString.split(',');
                let sisa = split[0].length % 3;
                let rupiah = split[0].substr(0, sisa);
                let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

                if (ribuan) {
                    let separator = sisa ? '.' : '';
                    rupiah += separator + ribuan.join('.');
                }
                rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
                e.target.value = rupiah;
            }
        });

        // 2. Full Year checkbox handling
        const checkFullYear = document.getElementById('is_full_year');
        const selectMonth = document.getElementById('month_select');

        function updateMonthDisabledState() {
            if (!checkFullYear || !selectMonth) return;
            if (checkFullYear.checked) {
                selectMonth.disabled = true;
                selectMonth.classList.add('bg-slate-100', 'text-slate-400', 'cursor-not-allowed');
            } else {
                selectMonth.disabled = false;
                selectMonth.classList.remove('bg-slate-100', 'text-slate-400', 'cursor-not-allowed');
            }
        }

        if (checkFullYear && selectMonth) {
            checkFullYear.addEventListener('change', updateMonthDisabledState);
            // Run on initial load in case old input was checked
            updateMonthDisabledState();
        }
    });
</script>
@endsection
