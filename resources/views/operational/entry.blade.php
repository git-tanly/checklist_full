@extends('layouts.checklist')

@section('title', 'Checklist Operasional - '.$restaurant->name)
@section('header_title', $restaurant->name)

@section('content')
    @php
        $activeWindow = $session !== null ? ($windows[$session['session_type']] ?? null) : null;
        $sessionIcon = ['breakfast' => 'ti-coffee', 'lunch' => 'ti-sun', 'dinner' => 'ti-moon'][$session['session_type'] ?? ''] ?? 'ti-clock';
        $completedSections = $sections->filter(fn ($section) => $submissions->get($section->id) !== null)->count();
        $totalSections = $sections->count();
    @endphp

    {{-- 1. HEADER BANNER OUTLET --}}
    <div class="mb-4 rounded-2xl border border-slate-200/90 bg-white p-4 shadow-sm">
        <div class="flex items-start justify-between gap-3">
            <div class="flex min-w-0 items-center gap-3">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl shadow-sm" style="background-color: #172a40; color: #B89047;">
                    <i class="ti ti-clipboard-check text-xl"></i>
                </div>
                <div class="min-w-0">
                    <p class="mb-0 text-[10px] font-bold uppercase tracking-wider" style="color: #B89047;">
                        Checklist Operasional Harian
                    </p>
                    <h2 class="mb-0 truncate text-base font-bold leading-tight text-slate-800">{{ $restaurant->name }}</h2>
                    <p class="mb-0 text-[11px] text-slate-500">
                        <i class="ti ti-building-store text-slate-400"></i> Outlet {{ $restaurant->code }}
                    </p>
                </div>
            </div>

            @if ($session === null)
                <span class="inline-flex shrink-0 items-center gap-1 rounded-full border border-amber-200 bg-amber-50 px-2.5 py-0.5 text-[11px] font-semibold text-amber-800">
                    <i class="ti ti-clock-pause text-[11px]"></i> Tutup
                </span>
            @else
                <span class="inline-flex shrink-0 items-center gap-1 rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-0.5 text-[11px] font-semibold text-emerald-700">
                    <i class="ti ti-circle-check text-[11px]"></i> Buka
                </span>
            @endif
        </div>
    </div>

    @if ($session === null)
        {{-- DI LUAR JAM SESI --}}
        <div class="overflow-hidden rounded-2xl border border-amber-200 bg-white shadow-sm">
            <div class="flex items-start gap-3 border-b border-amber-100 bg-amber-50/70 px-4 py-4">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-700">
                    <i class="ti ti-clock-pause text-lg"></i>
                </div>
                <div>
                    <h3 class="mb-0.5 text-sm font-bold text-amber-900">Di luar jam checklist</h3>
                    <p class="mb-0 text-[11px] leading-relaxed text-amber-800">
                        Saat ini tidak ada sesi checklist yang berjalan. Silakan kembali pada salah satu jam sesi di bawah.
                    </p>
                </div>
            </div>
            <div class="px-4 py-3">
                <p class="mb-0 text-[11px] text-slate-500">
                    <i class="ti ti-info-circle text-slate-400"></i>
                    Tombol checklist akan muncul otomatis saat jam sesi dimulai.
                </p>
            </div>
        </div>
    @else
        {{-- 2. SESI AKTIF --}}
        <div class="mb-4 overflow-hidden rounded-2xl shadow-sm" style="background-color: #172a40;">
            <div class="h-1 w-full" style="background: linear-gradient(90deg, #B89047 0%, #B89047 35%, rgba(255,255,255,.12) 35%, rgba(255,255,255,.12) 100%);"></div>

            <div class="flex items-center justify-between gap-3 px-4 py-4 text-white">
                <div class="flex min-w-0 items-center gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/10 text-[#B89047]">
                        <i class="ti {{ $sessionIcon }} text-lg"></i>
                    </span>
                    <div class="min-w-0">
                        <p class="mb-0 text-[10px] font-semibold uppercase tracking-wider text-white/50">Sesi berjalan</p>
                        <p class="mb-0 truncate text-base font-bold capitalize leading-tight">{{ $session['session_type'] }}</p>
                        @if ($activeWindow)
                            <p class="mb-0 text-[11px] font-mono text-white/60">
                                {{ substr($activeWindow->start_time, 0, 5) }} &ndash; {{ substr($activeWindow->end_time, 0, 5) }}
                            </p>
                        @endif
                    </div>
                </div>

                <div class="shrink-0 text-right">
                    <p class="mb-0 text-[10px] font-semibold uppercase tracking-wider text-white/50">Tanggal checklist</p>
                    <p class="mb-0 text-sm font-semibold">
                        {{ \Carbon\Carbon::parse($session['business_date'])->translatedFormat('d M Y') }}
                    </p>
                </div>
            </div>

            @if ($totalSections > 0)
                <div class="border-t border-white/10 px-4 py-2.5">
                    <div class="flex items-center justify-between gap-2">
                        <p class="mb-0 text-[11px] font-medium text-white/60">Section terisi</p>
                        <p class="mb-0 text-[11px] font-bold text-white">{{ $completedSections }}/{{ $totalSections }}</p>
                    </div>
                    <div class="mt-1.5 h-1 w-full overflow-hidden rounded-full bg-white/15">
                        <div class="h-full rounded-full transition-all"
                            style="width: {{ (int) round(($completedSections / max($totalSections, 1)) * 100) }}%; background-color: #B89047;"></div>
                    </div>
                </div>
            @endif
        </div>

        {{-- 3. DAFTAR SECTION --}}
        @if ($sections->isEmpty())
            <div class="rounded-2xl border border-slate-200/90 bg-white px-4 py-10 text-center shadow-sm">
                <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-xl text-slate-400">
                    <i class="ti ti-layout-grid"></i>
                </div>
                <h3 class="mb-1 text-sm font-bold text-slate-700">Belum ada checklist untuk sesi ini</h3>
                <p class="mb-0 text-xs text-slate-400">
                    Belum ada section aktif pada sesi <span class="capitalize">{{ $session['session_type'] }}</span> untuk outlet ini.
                </p>
            </div>
        @else
            <div class="mb-3 flex items-center justify-between gap-2">
                <h3 class="mb-0 text-sm font-bold tracking-tight text-slate-800">Pilih Section Checklist</h3>
                <span class="inline-flex items-center gap-1 rounded-full border border-slate-200 bg-slate-100 px-2.5 py-0.5 text-[11px] font-semibold text-slate-600">
                    {{ $totalSections }} section
                </span>
            </div>

            <div class="grid grid-cols-1 gap-3">
                @foreach ($sections as $section)
                    @php
                        $submission = $submissions->get($section->id);
                        $isLocked = $submission !== null && $submission->status !== 'rejected';
                    @endphp

                    <a href="{{ route('operational.section', [$restaurant->checklist_token, $section->id]) }}"
                        class="flex items-center justify-between gap-3 rounded-2xl border border-slate-200/90 bg-white px-4 py-3.5 no-underline shadow-sm transition hover:border-slate-300 hover:shadow-md active:scale-[.99]">
                        <div class="flex min-w-0 items-center gap-3">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border
                                {{ $submission?->status === 'verified'
                                    ? 'border-emerald-200 bg-emerald-50 text-emerald-600'
                                    : ($submission?->status === 'pending'
                                        ? 'border-amber-200 bg-amber-50 text-amber-600'
                                        : ($submission?->status === 'rejected'
                                            ? 'border-rose-200 bg-rose-50 text-rose-600'
                                            : 'border-[#172a40]/10 bg-[#172a40]/5 text-[#172a40]')) }}">
                                <i class="ti {{ $isLocked ? 'ti-lock' : 'ti-clipboard-list' }} text-base"></i>
                            </span>

                            <div class="min-w-0">
                                <p class="mb-0.5 truncate text-sm font-bold leading-snug text-slate-800">{{ $section->name }}</p>
                                <p class="mb-0 text-[11px] text-slate-500">
                                    <i class="ti ti-list-check text-slate-400"></i>
                                    {{ $section->questions_count }} poin checklist
                                </p>
                            </div>
                        </div>

                        <div class="flex shrink-0 items-center gap-1.5">
                            @if ($submission?->status === 'verified')
                                <span class="inline-flex items-center gap-1 rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wide text-emerald-700">
                                    <i class="ti ti-circle-check text-[11px]"></i> Verified
                                </span>
                            @elseif ($submission?->status === 'pending')
                                <span class="inline-flex items-center gap-1 rounded-full border border-amber-200 bg-amber-50 px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wide text-amber-800">
                                    <i class="ti ti-hourglass text-[11px]"></i> Pending
                                </span>
                            @elseif ($submission?->status === 'rejected')
                                <span class="inline-flex items-center gap-1 rounded-full border border-rose-200 bg-rose-50 px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wide text-rose-700">
                                    <i class="ti ti-alert-triangle text-[11px]"></i> Perbaiki
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 rounded-full border border-slate-200 bg-white px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wide text-slate-500">
                                    <i class="ti ti-pencil text-[11px]"></i> Isi
                                </span>
                            @endif

                            <i class="ti ti-chevron-right text-base text-slate-300"></i>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    @endif

    {{-- 4. JADWAL SESI --}}
    <div class="mt-4 overflow-hidden rounded-2xl border border-slate-200/90 bg-white shadow-sm">
        <div class="flex items-center gap-2.5 border-b border-slate-100 bg-slate-50/50 px-4 py-3">
            <div class="flex h-8 w-8 items-center justify-center rounded-lg border border-[#172a40]/10 bg-[#172a40]/5 text-[#172a40]">
                <i class="ti ti-calendar-time text-base"></i>
            </div>
            <div>
                <h3 class="mb-0 text-xs font-bold tracking-tight text-slate-800">Jadwal Sesi Checklist</h3>
                <p class="mb-0 text-[10px] text-slate-400">Jam operasional checklist untuk outlet ini</p>
            </div>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse ($windows as $sessionType => $window)
                @php
                    $isActive = ($session['session_type'] ?? null) === $sessionType;
                    $rowIcon = ['breakfast' => 'ti-coffee', 'lunch' => 'ti-sun', 'dinner' => 'ti-moon'][$sessionType] ?? 'ti-clock';
                @endphp

                <div class="flex items-center justify-between gap-3 px-4 py-2.5 {{ $isActive ? 'bg-amber-50/50' : '' }}">
                    <div class="flex min-w-0 items-center gap-2.5">
                        <i class="ti {{ $rowIcon }} text-sm {{ $isActive ? 'text-[#B89047]' : 'text-slate-400' }}"></i>
                        <span class="text-xs font-semibold capitalize {{ $isActive ? 'text-[#172a40]' : 'text-slate-600' }}">
                            {{ $sessionType }}
                        </span>
                        @if ($isActive)
                            <span class="inline-flex items-center gap-1 rounded-full border border-emerald-200 bg-emerald-50 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-emerald-700">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Berjalan
                            </span>
                        @endif
                    </div>

                    <span class="shrink-0 font-mono text-[11px] {{ $isActive ? 'font-bold text-[#172a40]' : 'text-slate-500' }}">
                        {{ substr($window->start_time, 0, 5) }} &ndash; {{ substr($window->end_time, 0, 5) }}
                    </span>
                </div>
            @empty
                <div class="px-4 py-4 text-center">
                    <p class="mb-0 text-xs text-slate-400">Jadwal sesi belum diatur. Hubungi Restaurant Manager.</p>
                </div>
            @endforelse
        </div>
    </div>

    <p class="mb-0 mt-4 text-center text-[10px] leading-relaxed text-slate-400">
        <i class="ti ti-info-circle"></i>
        Checklist hanya dapat diisi pada jam sesi yang berlaku.
    </p>
@endsection
