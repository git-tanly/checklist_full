@extends('layouts.checklist')

@section('title', 'Checklist Terkirim - '.$restaurant->name)
@section('header_title', 'Checklist Terkirim')

@section('content')
    <div class="card overflow-hidden">
        <div class="bg-emerald-600 px-5 py-6 text-center text-white">
            <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-full bg-white/15">
                <i class="bi bi-check-circle-fill text-2xl"></i>
            </div>
            <h2 class="mb-1 text-lg font-bold">Checklist berhasil dikirim</h2>
            <p class="mb-0 text-sm text-white/80">
                {{ $summary['section'] ?? '-' }} &middot; Sesi <span class="capitalize">{{ $summary['session'] ?? '-' }}</span>
                &middot; {{ now()->translatedFormat('d M Y, H:i') }}
            </p>
        </div>

        <div class="grid grid-cols-2 divide-x divide-slate-100">
            <div class="px-4 py-5 text-center">
                <p class="mb-1 text-3xl font-extrabold text-emerald-600">{{ $summary['ok'] ?? 0 }}</p>
                <p class="mb-0 text-[11px] font-bold uppercase tracking-wider text-slate-500">OK</p>
            </div>
            <div class="px-4 py-5 text-center">
                <p class="mb-1 text-3xl font-extrabold {{ ($summary['not_ok'] ?? 0) > 0 ? 'text-red-600' : 'text-slate-300' }}">
                    {{ $summary['not_ok'] ?? 0 }}
                </p>
                <p class="mb-0 text-[11px] font-bold uppercase tracking-wider text-slate-500">Not OK</p>
            </div>
        </div>

        <div class="border-t border-slate-100 bg-amber-50 px-4 py-3 text-center">
            <p class="mb-0 text-xs font-semibold text-amber-800">
                <i class="bi bi-hourglass-split me-1"></i> Status: menunggu verifikasi Restaurant Manager
            </p>
        </div>
    </div>

    <a href="{{ route('operational.entry', $restaurant->checklist_token) }}"
        class="btn btn-navy mt-4 w-full py-3 text-sm uppercase tracking-wider no-underline">
        Kembali ke Daftar Checklist
    </a>
@endsection
