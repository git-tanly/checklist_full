@extends('layouts.portal')

@section('title', 'QR Code Checklist - '.$restaurant->name)
@section('breadcrumb', 'Restaurants')

@section('content')
<div class="space-y-6">

    {{-- 1. EXECUTIVE HEADER BANNER --}}
    <div class="rounded-2xl border border-slate-200/90 shadow-sm bg-white p-5 md:p-6 print-hide">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 shadow-sm" style="background-color: #172a40; color: #B89047;">
                    <i class="ti ti-qrcode text-2xl"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-xl font-bold tracking-tight text-slate-800">QR Code Checklist</h1>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-sky-50 text-sky-800 border border-sky-200">
                            {{ $restaurant->code }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Cetak dan tempel QR ini di outlet agar staff bisa mengisi checklist tanpa login.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('restaurants.index') }}"
                    class="btn btn-light rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-100 transition inline-flex items-center gap-1.5 shadow-sm">
                    <i class="ti ti-arrow-left text-base"></i> Kembali ke Restaurants
                </a>
            </div>
        </div>
    </div>

    {{-- ALERTS --}}
    @if (session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50/90 p-4 text-emerald-800 shadow-sm flex items-center justify-between gap-3 print-hide">
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

    @if (! $checklistUrl)
        <div class="rounded-2xl border border-amber-200 bg-amber-50/90 p-4 text-amber-800 shadow-sm flex items-center gap-3 print-hide">
            <div class="w-9 h-9 rounded-xl bg-amber-100 flex items-center justify-center text-amber-700 shrink-0">
                <i class="ti ti-alert-triangle text-lg"></i>
            </div>
            <p class="text-xs font-medium mb-0">
                Outlet ini belum memiliki token QR. Tekan tombol "Buat Token Baru" di panel kanan untuk membuatnya.
            </p>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">

        {{-- KARTU QR SIAP CETAK --}}
        <div class="qr-card bg-white rounded-2xl border border-slate-200/90 shadow-sm mx-auto w-full max-w-md overflow-hidden">
            <div class="h-1.5 w-full" style="background: linear-gradient(90deg, #172a40 0%, #172a40 65%, #B89047 65%, #B89047 100%);"></div>

            <div class="p-8 text-center">
                <p class="text-[10px] font-bold uppercase tracking-[0.2em] mb-1" style="color: #B89047;">
                    Checklist Operasional Harian
                </p>
                <h2 class="text-xl font-bold text-slate-800 mb-1">{{ $restaurant->name }}</h2>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-[11px] font-mono font-bold bg-[#172a40]/5 text-[#172a40] border border-[#172a40]/15 tracking-wider mb-5">
                    {{ $restaurant->code }}
                </span>

                <div class="qr-box mx-auto mb-4" id="qr-box" data-url="{{ $checklistUrl }}"></div>

                @if ($checklistUrl)
                    <p class="text-sm font-bold text-slate-800 mb-1">Scan untuk mengisi checklist</p>
                    <p class="text-[11px] text-slate-400 break-all mb-0">{{ $checklistUrl }}</p>
                @else
                    <p class="text-sm text-slate-400 mb-0">QR belum tersedia.</p>
                @endif
            </div>

            <div class="px-8 py-3 border-t border-dashed border-slate-200 bg-slate-50/50">
                <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 text-center mb-0">
                    Tanly Hospitality &bull; Staff Checklist
                </p>
            </div>
        </div>

        {{-- PANEL AKSI --}}
        <div class="space-y-4 print-hide">
            {{-- Cetak & pasang --}}
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden">
                <div class="bg-gradient-to-r from-slate-900 via-[#172a40] to-slate-800 text-white px-6 py-4 flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center text-[#B89047]">
                        <i class="ti ti-printer text-base"></i>
                    </span>
                    <div>
                        <h2 class="text-sm font-bold text-white tracking-tight">Cetak &amp; Pasang</h2>
                        <p class="text-[11px] text-slate-300 mb-0">Cetak QR, laminasi, lalu tempel di area yang mudah dilihat staff</p>
                    </div>
                </div>

                <div class="p-5">
                    <p class="text-xs text-slate-500 mb-4 leading-relaxed">
                        Pasang di area yang sering dilewati staff, misalnya pintu masuk dapur atau dekat station
                        persiapan. Pastikan QR tidak tertutup benda lain dan tetap bersih.
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <button type="button" onclick="window.print()"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl font-bold text-sm text-white bg-[#172a40] hover:bg-[#112031] transition shadow-sm cursor-pointer {{ $checklistUrl ? '' : 'opacity-50 pointer-events-none' }}"
                            {{ $checklistUrl ? '' : 'disabled' }}>
                            <i class="ti ti-printer text-base"></i> Cetak QR
                        </button>

                        <a href="{{ $checklistUrl ? route('operational.entry', $restaurant->checklist_token) : '#' }}" target="_blank"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl font-semibold text-sm text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 transition shadow-2xs {{ $checklistUrl ? '' : 'opacity-50 pointer-events-none' }}">
                            <i class="ti ti-external-link text-base"></i> Uji Buka Link
                        </a>
                    </div>
                </div>
            </div>

            {{-- Token keamanan --}}
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/40">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-rose-50 border border-rose-200/70 flex items-center justify-center text-rose-600">
                            <i class="ti ti-shield-lock text-base"></i>
                        </div>
                        <div>
                            <h2 class="text-sm font-bold text-slate-800 tracking-tight">Token Keamanan</h2>
                            <p class="text-[11px] text-slate-400 mb-0">Identitas unik outlet pada QR</p>
                        </div>
                    </div>
                </div>

                <div class="p-5">
                    <p class="text-xs text-slate-500 mb-3 leading-relaxed">
                        QR memuat token unik outlet. Jika QR tersebar ke pihak yang salah, buat token baru agar
                        QR lama otomatis tidak berlaku.
                    </p>

                    <div class="rounded-xl bg-slate-50 border border-slate-200 px-3.5 py-2.5 font-mono text-[11px] text-slate-600 break-all mb-4">
                        <i class="ti ti-key text-slate-400 me-1"></i>{{ $restaurant->checklist_token ?? '—' }}
                    </div>

                    <form action="{{ route('restaurants.regenerate-token', $restaurant->id) }}" method="POST"
                        onsubmit="return confirm('Buat token QR baru? QR lama yang sudah terpasang akan tidak berlaku.');">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center justify-center gap-2 w-full px-4 py-2.5 rounded-xl text-sm font-semibold text-rose-700 bg-rose-50 border border-rose-200 hover:bg-rose-100 transition shadow-2xs cursor-pointer">
                            <i class="ti ti-refresh text-base"></i> Buat Token Baru
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
    <style>
        .qr-box {
            width: 220px;
            height: 220px;
            padding: 10px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            background: #fff;
        }

        .qr-box img,
        .qr-box canvas {
            width: 100% !important;
            height: 100% !important;
            display: block;
        }

        @media print {
            @page {
                margin: 12mm;
            }

            .print-hide,
            header,
            footer,
            #toast-container {
                display: none !important;
            }

            main {
                padding: 0 !important;
            }

            body {
                background: #fff !important;
            }

            .qr-card {
                border: 1px solid #cbd5e1 !important;
                border-radius: 12px !important;
                box-shadow: none !important;
                max-width: 380px;
                margin: 0 auto;
            }
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <script>
        // Render QR dari URL outlet (client-side, tanpa dependency Composer).
        const qrBox = document.getElementById('qr-box');

        if (qrBox && qrBox.dataset.url && typeof QRCode !== 'undefined') {
            new QRCode(qrBox, {
                text: qrBox.dataset.url,
                width: 220,
                height: 220,
                colorDark: '#172a40',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.M,
            });
        }
    </script>
@endpush
