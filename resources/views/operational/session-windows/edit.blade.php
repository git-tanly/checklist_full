@extends('layouts.portal')

@section('title', 'Jam Sesi Checklist')
@section('breadcrumb', 'Checklist Sections')

@section('content')
<div class="space-y-6">

    {{-- 1. EXECUTIVE HEADER BANNER --}}
    <div class="rounded-2xl border border-slate-200/90 shadow-sm bg-white p-5 md:p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 shadow-sm" style="background-color: #172a40; color: #B89047;">
                    <i class="ti ti-clock text-2xl"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-xl font-bold tracking-tight text-slate-800">Jam Sesi Checklist</h1>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-sky-50 text-sky-800 border border-sky-200">
                            {{ $restaurant ? $restaurant->code : 'Global' }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Atur jam sesi global (semua outlet) atau override untuk satu outlet.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('operational.sections.index') }}"
                    class="btn btn-light rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-100 transition inline-flex items-center gap-1.5 shadow-sm">
                    <i class="ti ti-arrow-left text-base"></i> Kembali ke Section
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

    @if (isset($errors) && $errors->any())
        <div class="rounded-2xl border border-rose-200 bg-rose-50/90 p-4 text-rose-800 shadow-sm">
            <div class="flex items-start gap-3">
                <div class="w-9 h-9 rounded-xl bg-rose-100 flex items-center justify-center text-rose-700 shrink-0">
                    <i class="ti ti-alert-triangle text-lg"></i>
                </div>
                <div class="flex-1">
                    <h5 class="text-xs font-bold uppercase tracking-wider text-rose-900 mb-1">Terjadi Kesalahan</h5>
                    <ul class="text-xs space-y-1 list-disc list-inside text-rose-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {{-- PEMILIH SCOPE --}}
    <div class="card border border-slate-200/90 rounded-2xl shadow-sm bg-white">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('operational.session-windows.edit') }}" class="row g-3 align-items-end">
                <div class="col-12 col-sm-8 col-md-5">
                    <label for="scope_restaurant" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">
                        <i class="ti ti-building-store me-1 text-slate-400"></i> Scope Pengaturan
                    </label>
                    <select id="scope_restaurant" name="restaurant_id" onchange="this.form.submit()"
                        class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2 text-xs text-slate-800 focus:outline-none focus:border-[#172a40] focus:ring-2 focus:ring-[#172a40]/10 transition">
                        <option value="">Global (semua outlet)</option>
                        @foreach ($restaurants as $item)
                            <option value="{{ $item->id }}" {{ $restaurantId === $item->id ? 'selected' : '' }}>
                                {{ $item->code }} — {{ $item->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-sm-4 col-md-3">
                    <button type="submit"
                        class="inline-flex items-center justify-center gap-1.5 w-full px-4 py-2 rounded-xl text-xs font-semibold text-white bg-[#172a40] hover:bg-[#112031] transition shadow-sm">
                        <i class="ti ti-filter"></i> Tampilkan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- FORM JAM SESI --}}
    <form method="POST" action="{{ route('operational.session-windows.update') }}">
        @csrf
        @method('PUT')
        <input type="hidden" name="restaurant_id" value="{{ $restaurantId }}">

        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden">
            <div class="bg-gradient-to-r from-slate-900 via-[#172a40] to-slate-800 text-white px-6 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center text-[#B89047]">
                        <i class="ti ti-clock-hour-4 text-base"></i>
                    </span>
                    <div>
                        <h2 class="text-sm font-bold text-white tracking-tight">
                            Jam Sesi {{ $restaurant ? '— Override '.$restaurant->name : '— Global' }}
                        </h2>
                        <p class="text-[11px] text-slate-300 mb-0">
                            Window berlaku mulai jam mulai sampai sebelum jam selesai. Jam selesai lebih kecil dari jam mulai berarti sesi melewati tengah malam.
                        </p>
                    </div>
                </div>

                @if ($restaurant)
                    @if ($hasOverride)
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-amber-400/15 text-amber-200 border border-amber-300/30">
                            <i class="ti ti-pin text-[11px]"></i> Override aktif
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-white/10 text-slate-200 border border-white/20">
                            <i class="ti ti-world text-[11px]"></i> Memakai global
                        </span>
                    @endif
                @else
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-white/10 text-slate-200 border border-white/20">
                        <i class="ti ti-world text-[11px]"></i> Default global
                    </span>
                @endif
            </div>

            <div class="p-6">
                @foreach ($rows as $index => $row)
                    @php
                        $startTime = substr($row->start_time, 0, 5);
                        $endTime = substr($row->end_time, 0, 5);
                        $crossesMidnight = $row->is_active && $endTime < $startTime;
                        $sessionIcon = ['breakfast' => 'ti-coffee', 'lunch' => 'ti-sun', 'dinner' => 'ti-moon'][$row->session_type] ?? 'ti-clock';
                    @endphp

                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-start py-4 {{ $loop->last ? '' : 'border-b border-slate-100' }}">
                        {{-- Sesi + switch --}}
                        <div class="md:col-span-4">
                            <div class="flex items-center gap-2.5 mb-2">
                                <span class="w-8 h-8 rounded-lg bg-[#172a40]/5 border border-[#172a40]/10 flex items-center justify-center text-[#172a40]">
                                    <i class="ti {{ $sessionIcon }} text-base"></i>
                                </span>
                                <div>
                                    <p class="text-sm font-bold text-slate-800 mb-0">{{ ucfirst($row->session_type) }}</p>
                                    <p class="text-[11px] text-slate-400 mb-0">Sesi {{ $row->session_type }}</p>
                                </div>
                            </div>

                            <input type="hidden" name="windows[{{ $index }}][session_type]" value="{{ $row->session_type }}">

                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input cursor-pointer" type="checkbox" role="switch"
                                    id="active_{{ $row->session_type }}"
                                    name="windows[{{ $index }}][is_active]" value="1"
                                    {{ $row->is_active ? 'checked' : '' }}>
                                <label class="form-check-label text-xs font-bold text-slate-700" for="active_{{ $row->session_type }}">
                                    Aktif
                                </label>
                            </div>
                        </div>

                        {{-- Jam mulai --}}
                        <div class="md:col-span-3">
                            <label for="start_{{ $row->session_type }}" class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center gap-1.5">
                                <i class="ti ti-player-play text-slate-400"></i> Jam Mulai
                            </label>
                            <input type="time" id="start_{{ $row->session_type }}" name="windows[{{ $index }}][start_time]"
                                value="{{ $startTime }}"
                                class="w-full px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all">
                        </div>

                        {{-- Jam selesai --}}
                        <div class="md:col-span-3">
                            <label for="end_{{ $row->session_type }}" class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center gap-1.5">
                                <i class="ti ti-player-stop text-slate-400"></i> Jam Selesai
                            </label>
                            <input type="time" id="end_{{ $row->session_type }}" name="windows[{{ $index }}][end_time]"
                                value="{{ $endTime }}"
                                class="w-full px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all">
                        </div>

                        {{-- Keterangan --}}
                        <div class="md:col-span-2">
                            <span class="block text-xs font-bold text-slate-700 mb-1.5">Keterangan</span>
                            @if (! $row->is_active)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                    <i class="ti ti-circle-minus text-[11px]"></i> Nonaktif
                                </span>
                            @elseif ($crossesMidnight)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                    <i class="ti ti-moon-stars text-[11px]"></i> Lintas malam
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <i class="ti ti-sun text-[11px]"></i> Normal
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach

                {{-- Action Buttons --}}
                <div class="mt-4 pt-4 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-end gap-2">
                    @if ($restaurant && $hasOverride)
                        <button type="submit" name="remove_override" value="1"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-rose-700 bg-rose-50 border border-rose-200 hover:bg-rose-100 transition shadow-2xs cursor-pointer"
                            onclick="return confirm('Hapus override dan kembalikan outlet ini ke jam global?');">
                            <i class="ti ti-restore text-base"></i> Hapus Override
                        </button>
                    @endif

                    <button type="submit"
                        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl font-bold text-sm text-white bg-[#172a40] hover:bg-[#112031] transition shadow-sm hover:shadow cursor-pointer">
                        <i class="ti ti-device-floppy text-base"></i> Simpan Jam Sesi
                    </button>
                </div>
            </div>
        </div>
    </form>

    {{-- RINGKASAN WINDOW EFEKTIF PER OUTLET --}}
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/40">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-[#172a40]/5 border border-[#172a40]/10 flex items-center justify-center text-[#172a40]">
                    <i class="ti ti-list-details text-base"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-800 tracking-tight">Jam Efektif per Outlet</h2>
                    <p class="text-[11px] text-slate-400 mb-0">Hasil resolusi override outlet terhadap default global</p>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200/80 bg-slate-50/70 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                        <th class="py-3 px-4">Outlet</th>
                        <th class="py-3 px-4 text-center">Breakfast</th>
                        <th class="py-3 px-4 text-center">Lunch</th>
                        <th class="py-3 px-4 text-center">Dinner</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @foreach ($restaurants as $item)
                        @php
                            $effective = $effectivePerOutlet[$item->id] ?? [];
                        @endphp
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-3.5 px-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                    <i class="ti ti-building-store text-slate-400 text-xs"></i>
                                    <strong>{{ $item->code }}</strong> — {{ $item->name }}
                                </span>
                            </td>
                            @foreach (['breakfast', 'lunch', 'dinner'] as $session)
                                <td class="py-3.5 px-4 text-center">
                                    @if (($effective[$session] ?? '-') === '-')
                                        <span class="text-slate-300">—</span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[11px] font-mono font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            {{ $effective[$session] }}
                                        </span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/40 text-[11px] text-slate-400">
            <i class="ti ti-info-circle me-1"></i> Tanda "—" berarti sesi tidak aktif untuk outlet tersebut.
        </div>
    </div>
</div>
@endsection
