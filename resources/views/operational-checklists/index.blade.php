@extends('layouts.portal')

@section('title', 'Verifikasi Checklist Operasional - Tanly Hospitality')
@section('breadcrumb', 'Operational Checklist')

@section('content')
<div class="space-y-6">

    {{-- 1. EXECUTIVE HEADER BANNER --}}
    <div class="rounded-2xl border border-slate-200/90 shadow-sm bg-white p-5 md:p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 shadow-sm" style="background-color: #172a40; color: #B89047;">
                    <i class="ti ti-clipboard-check text-2xl"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-xl font-bold tracking-tight text-slate-800">Verifikasi Checklist Operasional</h1>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-sky-50 text-sky-800 border border-sky-200">
                            {{ $submissions->total() }} Checklist
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">Periksa hasil checklist harian staff dan lakukan verifikasi per outlet.</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                    <i class="ti ti-building-store text-slate-400"></i>
                    {{ $isSuperAdmin ? 'Semua Outlet' : 'Outlet Anda' }}
                </span>
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

    @if (session('error'))
        <div class="rounded-2xl border border-rose-200 bg-rose-50/90 p-4 text-rose-800 shadow-sm flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-rose-100 flex items-center justify-center text-rose-700 shrink-0">
                    <i class="ti ti-alert-triangle text-lg"></i>
                </div>
                <div>
                    <h5 class="text-xs font-bold uppercase tracking-wider text-rose-900 mb-0.5">Gagal</h5>
                    <p class="text-xs font-medium text-rose-800 mb-0">{{ session('error') }}</p>
                </div>
            </div>
            <button type="button" class="btn-close text-xs text-rose-700" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- FILTER CARD --}}
    <div class="card border border-slate-200/90 rounded-2xl shadow-sm bg-white">
        <div class="card-body p-4">
            <form action="{{ route('operational-checklists.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-12 col-sm-6 col-md-3">
                    <label for="filter_date" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">
                        <i class="ti ti-calendar me-1 text-slate-400"></i> Tanggal
                    </label>
                    <input type="date" id="filter_date" name="date" value="{{ $date }}"
                        class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2 text-xs text-slate-800 focus:outline-none focus:border-[#172a40] focus:ring-2 focus:ring-[#172a40]/10 transition">
                </div>

                <div class="col-12 col-sm-6 col-md-3">
                    <label for="filter_restaurant" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">
                        <i class="ti ti-building-store me-1 text-slate-400"></i> Outlet
                    </label>
                    <select id="filter_restaurant" name="restaurant_id"
                        class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2 text-xs text-slate-800 focus:outline-none focus:border-[#172a40] focus:ring-2 focus:ring-[#172a40]/10 transition">
                        <option value="">Semua Outlet</option>
                        @foreach ($restaurants as $restaurant)
                            <option value="{{ $restaurant->id }}" {{ $restaurantId === $restaurant->id ? 'selected' : '' }}>
                                {{ $restaurant->code }} — {{ $restaurant->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-sm-6 col-md-3">
                    <label for="filter_status" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">
                        <i class="ti ti-progress-check me-1 text-slate-400"></i> Status
                    </label>
                    <select id="filter_status" name="status"
                        class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2 text-xs text-slate-800 focus:outline-none focus:border-[#172a40] focus:ring-2 focus:ring-[#172a40]/10 transition">
                        <option value="">Semua Status</option>
                        @foreach (['pending' => 'Menunggu Verifikasi', 'verified' => 'Terverifikasi', 'rejected' => 'Ditolak'] as $value => $label)
                            <option value="{{ $value }}" {{ $status === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-sm-6 col-md-3 d-flex gap-2">
                    <button type="submit"
                        class="inline-flex items-center justify-center gap-1.5 w-full px-4 py-2 rounded-xl text-xs font-semibold text-white bg-[#172a40] hover:bg-[#112031] transition shadow-sm">
                        <i class="ti ti-filter"></i> Filter
                    </button>
                    @if (request()->hasAny(['date', 'restaurant_id', 'status']))
                        <a href="{{ route('operational-checklists.index') }}"
                            class="inline-flex items-center justify-center px-3 py-2 rounded-xl text-xs font-semibold text-slate-600 bg-white border border-slate-300 hover:bg-slate-50 transition"
                            title="Reset Filter">
                            <i class="ti ti-x"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- CHECKLIST DIRECTORY CARD --}}
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/40">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-[#172a40]/5 border border-[#172a40]/10 flex items-center justify-center text-[#172a40]">
                    <i class="ti ti-list-check text-base"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-800 tracking-tight">Daftar Checklist Operasional</h2>
                    <p class="text-[11px] text-slate-400 mb-0">Menampilkan {{ $submissions->total() }} checklist sesuai filter yang dipilih</p>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200/80 bg-slate-50/70 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                        <th class="py-3 px-4">Tanggal</th>
                        <th class="py-3 px-4">Outlet</th>
                        <th class="py-3 px-4">Section</th>
                        <th class="py-3 px-4">Staff</th>
                        <th class="py-3 px-4 text-center">Sesi</th>
                        <th class="py-3 px-4 text-center">NOT OK</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse ($submissions as $submission)
                        <tr class="{{ $submission->not_ok_count > 0 ? 'bg-rose-50/50' : '' }} hover:bg-slate-50/60 transition-colors">
                            {{-- Tanggal --}}
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-800">{{ $submission->date->format('d M Y') }}</div>
                                <div class="text-[11px] text-slate-400">
                                    {{ optional($submission->submitted_at)?->format('H:i') ?? '-' }} WIB
                                </div>
                            </td>

                            {{-- Outlet --}}
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-2">
                                    <span class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-500 font-mono text-[10px] font-bold">{{ $submission->restaurant->code }}</span>
                                    <span class="font-semibold text-slate-700">{{ $submission->restaurant->name }}</span>
                                </div>
                            </td>

                            {{-- Section --}}
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-[#172a40]">{{ $submission->section->name }}</span>
                                <div class="text-[11px] text-slate-400">{{ $submission->answers_count }} poin checklist</div>
                            </td>

                            {{-- Staff --}}
                            <td class="py-3.5 px-4">
                                @if ($submission->employee)
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full bg-[#172a40] text-white flex items-center justify-center font-bold text-[10px] shrink-0">
                                            {{ strtoupper(substr($submission->employee->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="font-medium text-slate-700">{{ $submission->employee->name }}</div>
                                            <div class="text-[11px] text-slate-400">{{ $submission->employee->position }}</div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-slate-400 italic text-xs">-</span>
                                @endif
                            </td>

                            {{-- Sesi --}}
                            <td class="py-3.5 px-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200 capitalize">
                                    {{ $submission->session_type }}
                                </span>
                            </td>

                            {{-- NOT OK --}}
                            <td class="py-3.5 px-4 text-center">
                                @if ($submission->not_ok_count > 0)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                        <i class="ti ti-alert-circle text-[11px]"></i> {{ $submission->not_ok_count }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <i class="ti ti-check text-[11px]"></i> 0
                                    </span>
                                @endif
                            </td>

                            {{-- Status --}}
                            <td class="py-3.5 px-4 text-center">
                                @if ($submission->status === 'pending')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                        <i class="ti ti-clock text-[11px]"></i> Menunggu
                                    </span>
                                @elseif ($submission->status === 'verified')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <i class="ti ti-circle-check text-[11px]"></i> Terverifikasi
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                        <i class="ti ti-circle-x text-[11px]"></i> Ditolak
                                    </span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="py-3.5 px-4 text-right">
                                <a href="{{ route('operational-checklists.show', $submission->id) }}"
                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-[#172a40]/5 text-[#172a40] hover:bg-[#172a40]/10 border border-[#172a40]/10 text-[11px] font-semibold transition"
                                    title="Lihat Detail">
                                    <i class="ti ti-eye text-sm"></i> Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400 text-xl mb-3">
                                        <i class="ti ti-clipboard-off"></i>
                                    </div>
                                    <h6 class="text-sm font-bold text-slate-700 mb-1">Belum Ada Checklist</h6>
                                    <p class="text-xs text-slate-400 max-w-sm">Tidak ada checklist untuk filter ini. Coba ubah tanggal, outlet, atau status.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($submissions->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $submissions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
