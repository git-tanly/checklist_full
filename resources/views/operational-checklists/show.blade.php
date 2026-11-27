@extends('layouts.portal')

@section('title', 'Detail Checklist Operasional - Tanly Hospitality')
@section('breadcrumb', 'Operational Checklist')

@section('content')
<div class="space-y-6">

    {{-- EXECUTIVE HEADER BANNER --}}
    <div class="rounded-2xl border border-slate-200/90 shadow-sm bg-white p-5 md:p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <a href="{{ route('operational-checklists.index') }}"
                    class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200 flex items-center justify-center shrink-0 transition"
                    title="Kembali">
                    <i class="ti ti-arrow-left text-lg"></i>
                </a>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-xl font-bold tracking-tight text-slate-800">{{ $submission->section->name }}</h1>
                        @if ($submission->status === 'pending')
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                                <i class="ti ti-clock text-[11px]"></i> Menunggu Verifikasi
                            </span>
                        @elseif ($submission->status === 'verified')
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                <i class="ti ti-circle-check text-[11px]"></i> Terverifikasi
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-800 border border-rose-200">
                                <i class="ti ti-circle-x text-[11px]"></i> Ditolak
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        {{ $submission->restaurant->name }} &middot;
                        {{ $submission->date->format('d M Y') }} &middot;
                        <span class="capitalize">{{ $submission->session_type }}</span>
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                    <i class="ti ti-list-check text-slate-400"></i>
                    {{ $answers->count() }} Poin Checklist
                </span>
            </div>
        </div>
    </div>

    {{-- SUBMISSION INFO CARD --}}
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/40">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-[#172a40]/5 border border-[#172a40]/10 flex items-center justify-center text-[#172a40]">
                    <i class="ti ti-info-circle text-base"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-800 tracking-tight">Informasi Checklist</h2>
                    <p class="text-[11px] text-slate-400 mb-0">Detail pengisian dan status verifikasi</p>
                </div>
            </div>
        </div>

        <div class="p-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-xs">
            <div>
                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Outlet</p>
                <p class="mb-0 font-semibold text-slate-800">{{ $submission->restaurant->code }} — {{ $submission->restaurant->name }}</p>
            </div>
            <div>
                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Staff</p>
                <p class="mb-0 font-semibold text-slate-800">{{ $submission->employee?->name ?? '-' }}</p>
                <p class="mb-0 text-[11px] text-slate-400">{{ $submission->employee?->position ?? '' }}</p>
            </div>
            <div>
                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Dikirim</p>
                <p class="mb-0 font-semibold text-slate-800">{{ optional($submission->submitted_at)?->format('d M Y, H:i') ?? '-' }} WIB</p>
            </div>
            <div>
                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Sesi</p>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200 capitalize">
                    {{ $submission->session_type }}
                </span>
            </div>
        </div>

        @if ($submission->status === 'verified' && $submission->verifier)
            <div class="px-5 pb-4">
                <div class="rounded-xl border border-emerald-200 bg-emerald-50/70 px-4 py-3 text-xs text-emerald-800">
                    <i class="ti ti-circle-check me-1"></i>
                    Diverifikasi oleh <span class="font-bold">{{ $submission->verifier->name }}</span>
                    pada {{ optional($submission->verified_at)?->format('d M Y, H:i') }} WIB.
                </div>
            </div>
        @elseif ($submission->status === 'rejected' && $submission->verification_note)
            <div class="px-5 pb-4">
                <div class="rounded-xl border border-rose-200 bg-rose-50/70 px-4 py-3 text-xs text-rose-800">
                    <i class="ti ti-alert-triangle me-1"></i>
                    <span class="font-bold">Catatan penolakan:</span> {{ $submission->verification_note }}
                </div>
            </div>
        @endif
    </div>

    {{-- ANSWERS CARD --}}
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/40">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-[#172a40]/5 border border-[#172a40]/10 flex items-center justify-center text-[#172a40]">
                    <i class="ti ti-checklist text-base"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-800 tracking-tight">Hasil Checklist</h2>
                    <p class="text-[11px] text-slate-400 mb-0">Snapshot jawaban staff sesuai pertanyaan saat pengisian</p>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200/80 bg-slate-50/70 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4">Check Point</th>
                        <th class="py-3 px-4">Key Check Point</th>
                        <th class="py-3 px-4 text-center">Jawaban</th>
                        <th class="py-3 px-4">Remarks</th>
                        <th class="py-3 px-4 text-center">Hasil</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @foreach ($answers as $index => $answer)
                        <tr class="{{ $answer->result === 'not_ok' ? 'bg-rose-50/50' : '' }} hover:bg-slate-50/60 transition-colors">
                            <td class="py-3.5 px-4 text-center font-semibold text-slate-400">{{ $index + 1 }}</td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-800">{{ $answer->check_point }}</div>
                            </td>
                            <td class="py-3.5 px-4 text-slate-500">{{ $answer->key_check_point ?? '-' }}</td>
                            <td class="py-3.5 px-4 text-center">
                                @if ($answer->answer_type === 'number')
                                    <span class="font-bold text-slate-800">{{ $answer->answer_number }}</span>
                                    @if ($answer->unit)
                                        <span class="text-[11px] text-slate-400">{{ $answer->unit }}</span>
                                    @endif
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold {{ $answer->answer_boolean ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                        {{ $answer->answer_boolean ? 'Yes' : 'No' }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-slate-500">{{ $answer->remarks ?? '-' }}</td>
                            <td class="py-3.5 px-4 text-center">
                                @if ($answer->result === 'ok')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <i class="ti ti-check text-[11px]"></i> OK
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                        <i class="ti ti-x text-[11px]"></i> NOT OK
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- VERIFICATION ACTION CARD --}}
    @if ($submission->status === 'pending')
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/40">
                <h2 class="text-sm font-bold text-slate-800 tracking-tight mb-0.5">Aksi Verifikasi</h2>
                <p class="text-[11px] text-slate-400 mb-0">Tolak akan mengembalikan checklist ke staff untuk diperbaiki lalu dikirim ulang.</p>
            </div>
            <div class="p-5 flex flex-col sm:flex-row gap-3">
                <form method="POST" action="{{ route('operational-checklists.verify', $submission->id) }}" class="flex-1">
                    @csrf
                    @method('PATCH')
                    <button type="submit"
                        class="inline-flex items-center justify-center gap-2 w-full px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 transition shadow-sm">
                        <i class="ti ti-circle-check text-base"></i> Verifikasi (Setujui)
                    </button>
                </form>

                <form method="POST" action="{{ route('operational-checklists.reject', $submission->id) }}"
                    class="flex-1" id="reject-form">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="verification_note" id="reject-note">
                    <button type="submit"
                        class="inline-flex items-center justify-center gap-2 w-full px-4 py-2.5 rounded-xl text-xs font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition shadow-sm">
                        <i class="ti ti-circle-x text-base"></i> Tolak (Minta Perbaikan)
                    </button>
                </form>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
    <script>
        // Catatan penolakan diisi lewat SweetAlert2 (konsisten dengan halaman admin lain).
        const rejectForm = document.getElementById('reject-form');

        if (rejectForm) {
            rejectForm.addEventListener('submit', function (event) {
                event.preventDefault();

                Swal.fire({
                    title: 'Tolak checklist?',
                    input: 'textarea',
                    inputLabel: 'Catatan untuk staff (minimal 5 karakter)',
                    inputPlaceholder: 'Contoh: Suhu kulkas melebihi standar, mohon dicek ulang.',
                    inputAttributes: { maxlength: 1000 },
                    showCancelButton: true,
                    confirmButtonText: 'Ya, tolak',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#b91c1c',
                    inputValidator: (value) => {
                        if (!value || value.trim().length < 5) {
                            return 'Catatan penolakan minimal 5 karakter.';
                        }
                    },
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('reject-note').value = result.value.trim();
                        rejectForm.submit();
                    }
                });
            });
        }
    </script>
@endpush
