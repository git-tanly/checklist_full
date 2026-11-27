@extends('layouts.portal')

@section('title', 'Pertanyaan Checklist - '.$section->name)
@section('breadcrumb', 'Checklist Sections')

@section('content')
<div class="space-y-6">

    {{-- 1. EXECUTIVE HEADER BANNER --}}
    <div class="rounded-2xl border border-slate-200/90 shadow-sm bg-white p-5 md:p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 shadow-sm" style="background-color: #172a40; color: #B89047;">
                    <i class="ti ti-list-check text-2xl"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-xl font-bold tracking-tight text-slate-800">Pertanyaan Checklist</h1>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                            {{ $section->name }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        <i class="ti ti-building-store text-slate-400"></i>
                        {{ $section->restaurant->code }} — {{ $section->restaurant->name }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('operational.sections.index', ['restaurant_id' => $section->restaurant_id]) }}"
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

    {{-- FORM TAMBAH PERTANYAAN --}}
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-slate-900 via-[#172a40] to-slate-800 text-white px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center text-[#B89047]">
                    <i class="ti ti-plus text-base"></i>
                </span>
                <div>
                    <h2 class="text-sm font-bold text-white tracking-tight">Tambah Pertanyaan</h2>
                    <p class="text-[11px] text-slate-300 mb-0">Pertanyaan baru otomatis dipakai staff untuk sesi yang dipilih</p>
                </div>
            </div>
            <span class="w-2 h-2 rounded-full bg-[#B89047]"></span>
        </div>

        <div class="p-6">
            <form method="POST" action="{{ route('operational.questions.store', $section->id) }}" id="question-form">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    {{-- Sesi --}}
                    <div>
                        <label for="session_type" class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center gap-1.5">
                            <i class="ti ti-clock text-slate-400"></i> Sesi <span class="text-rose-500">*</span>
                        </label>
                        <select id="session_type" name="session_type" required
                            class="w-full px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all">
                            @foreach (['breakfast', 'lunch', 'dinner'] as $session)
                                <option value="{{ $session }}" {{ old('session_type') === $session ? 'selected' : '' }}>
                                    {{ ucfirst($session) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Tipe jawaban --}}
                    <div>
                        <label for="answer_type" class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center gap-1.5">
                            <i class="ti ti-forms text-slate-400"></i> Tipe Jawaban <span class="text-rose-500">*</span>
                        </label>
                        <select id="answer_type" name="answer_type" required
                            class="w-full px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all">
                            <option value="number" {{ old('answer_type', 'number') === 'number' ? 'selected' : '' }}>Angka (jumlah / suhu)</option>
                            <option value="yes_no" {{ old('answer_type') === 'yes_no' ? 'selected' : '' }}>Yes / No</option>
                        </select>
                    </div>

                    {{-- Urutan --}}
                    <div>
                        <label for="sort_order" class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center gap-1.5">
                            <i class="ti ti-sort-ascending text-slate-400"></i> Urutan Tampil
                        </label>
                        <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" min="0" max="65535"
                            class="w-full px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all">
                        <p class="text-[11px] text-slate-400 mt-1.5 mb-0">Angka kecil tampil lebih dulu.</p>
                    </div>

                    {{-- Check point --}}
                    <div class="sm:col-span-2 lg:col-span-3">
                        <label for="check_point" class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center gap-1.5">
                            <i class="ti ti-checklist text-slate-400"></i> Check Point / Standard <span class="text-rose-500">*</span>
                        </label>
                        <textarea id="check_point" name="check_point" rows="2" required maxlength="2000"
                            placeholder="Contoh: Salad bar ingredients: 8-10 items"
                            class="w-full px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all">{{ old('check_point') }}</textarea>
                    </div>

                    {{-- Key check point --}}
                    <div class="sm:col-span-2 lg:col-span-3">
                        <label for="key_check_point" class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center gap-1.5">
                            <i class="ti ti-bulb text-slate-400"></i> Key Check Point <span class="text-[11px] font-medium text-slate-400">(guide)</span>
                        </label>
                        <textarea id="key_check_point" name="key_check_point" rows="2" maxlength="2000"
                            placeholder="Contoh: Count every salad ingredient on display..."
                            class="w-full px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all">{{ old('key_check_point') }}</textarea>
                    </div>

                    {{-- Threshold --}}
                    <div class="threshold-field">
                        <label for="std_min" class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center gap-1.5">
                            <i class="ti ti-arrow-bar-down text-slate-400"></i> Std Min
                        </label>
                        <input type="number" step="any" id="std_min" name="std_min" value="{{ old('std_min') }}" min="0"
                            class="w-full px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                    </div>

                    <div class="threshold-field">
                        <label for="std_max" class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center gap-1.5">
                            <i class="ti ti-arrow-bar-up text-slate-400"></i> Std Max
                        </label>
                        <input type="number" step="any" id="std_max" name="std_max" value="{{ old('std_max') }}" min="0"
                            class="w-full px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                    </div>

                    <div class="threshold-field">
                        <label for="temp_max" class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center gap-1.5">
                            <i class="ti ti-temperature text-slate-400"></i> Suhu Max
                        </label>
                        <input type="number" step="any" id="temp_max" name="temp_max" value="{{ old('temp_max') }}" min="0"
                            class="w-full px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                    </div>

                    <div class="threshold-field">
                        <label for="unit" class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center gap-1.5">
                            <i class="ti ti-ruler-measure text-slate-400"></i> Unit
                        </label>
                        <input type="text" id="unit" name="unit" value="{{ old('unit') }}" maxlength="16"
                            placeholder="°C / pcs"
                            class="w-full px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                    </div>

                    <div class="threshold-note sm:col-span-2 lg:col-span-3 hidden">
                        <div class="flex items-start gap-2.5 rounded-xl border border-amber-200 bg-amber-50/80 px-4 py-3">
                            <i class="ti ti-info-circle text-amber-600 mt-0.5"></i>
                            <p class="text-[11px] text-amber-800 mb-0 leading-relaxed">
                                Jawaban Yes/No tidak memakai batas angka. Field Std Min, Std Max, Suhu Max, dan Unit dinonaktifkan.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Status & Submit --}}
                <div class="mt-6 pt-4 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input cursor-pointer" type="checkbox" role="switch" id="is_active"
                            name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                        <label class="form-check-label text-xs font-bold text-slate-700" for="is_active">
                            Aktif
                        </label>
                        <p class="text-[11px] text-slate-400 mt-1 mb-0">Pertanyaan nonaktif tidak muncul di checklist staff.</p>
                    </div>

                    <button type="submit"
                        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl font-bold text-sm text-white bg-[#172a40] hover:bg-[#112031] transition shadow-sm hover:shadow cursor-pointer">
                        <i class="ti ti-plus text-base"></i> Tambah Pertanyaan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- DAFTAR PERTANYAAN PER SESI --}}
    <div class="flex items-center gap-2 flex-wrap">
        <h2 class="text-sm font-bold text-slate-800 tracking-tight mb-0">Daftar Pertanyaan</h2>
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
            {{ $questions->sum(fn ($items) => $items->count()) }} pertanyaan
        </span>
    </div>

    @foreach (['breakfast', 'lunch', 'dinner'] as $sessionType)
        @php
            $items = $questions->get($sessionType, collect());
        @endphp

        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/40">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-[#172a40]/5 border border-[#172a40]/10 flex items-center justify-center text-[#172a40]">
                        <i class="ti ti-clock text-base"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-800 tracking-tight">{{ ucfirst($sessionType) }}</h3>
                        <p class="text-[11px] text-slate-400 mb-0">{{ $items->count() }} pertanyaan pada sesi ini</p>
                    </div>
                </div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                    {{ ucfirst($sessionType) }}
                </span>
            </div>

            @if ($items->isEmpty())
                <div class="py-10 text-center">
                    <div class="w-11 h-11 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400 text-lg mx-auto mb-3">
                        <i class="ti ti-list-search"></i>
                    </div>
                    <h6 class="text-sm font-bold text-slate-700 mb-1">Belum Ada Pertanyaan</h6>
                    <p class="text-xs text-slate-400 mb-0">Tambahkan pertanyaan untuk sesi {{ ucfirst($sessionType) }} lewat form di atas.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200/80 bg-slate-50/70 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                                <th class="py-3 px-4 w-14 text-center">No</th>
                                <th class="py-3 px-4 min-w-[220px]">Check Point</th>
                                <th class="py-3 px-4 min-w-[200px]">Key Check Point</th>
                                <th class="py-3 px-4 text-center">Tipe</th>
                                <th class="py-3 px-4 text-center min-w-[150px]">Batas Standar</th>
                                <th class="py-3 px-4 text-center">Status</th>
                                <th class="py-3 px-4 text-right w-24">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs">
                            @foreach ($items as $item)
                                <tr class="hover:bg-slate-50/60 transition-colors {{ $item->is_active ? '' : 'opacity-60' }}">
                                    <td class="py-3.5 px-4 text-center font-semibold text-slate-400">
                                        {{ $item->sort_order }}
                                    </td>

                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-slate-800 text-sm leading-snug">{{ $item->check_point }}</div>
                                        <div class="text-[11px] text-slate-400">ID: #{{ $item->id }}</div>
                                    </td>

                                    <td class="py-3.5 px-4 text-slate-500 leading-snug">
                                        {{ $item->key_check_point ?? '—' }}
                                    </td>

                                    <td class="py-3.5 px-4 text-center">
                                        @if ($item->answer_type === 'number')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-sky-50 text-sky-700 border border-sky-200">
                                                <i class="ti ti-hash text-[11px]"></i> Angka
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                                <i class="ti ti-toggle-left text-[11px]"></i> Yes/No
                                            </span>
                                        @endif
                                    </td>

                                    <td class="py-3.5 px-4 text-center">
                                        @if ($item->answer_type === 'number')
                                            <div class="inline-flex flex-wrap justify-center gap-1">
                                                @if ($item->std_min !== null || $item->std_max !== null)
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                        {{ $item->std_min ?? '—' }} – {{ $item->std_max ?? '—' }}
                                                        {{ $item->unit }}
                                                    </span>
                                                @endif
                                                @if ($item->temp_max !== null)
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                                        ≤ {{ $item->temp_max }} {{ $item->unit ?: '°C' }}
                                                    </span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-slate-400">—</span>
                                        @endif
                                    </td>

                                    <td class="py-3.5 px-4 text-center">
                                        @if ($item->is_active)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <i class="ti ti-circle-check text-[11px]"></i> Aktif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                                <i class="ti ti-circle-minus text-[11px]"></i> Nonaktif
                                            </span>
                                        @endif
                                    </td>

                                    <td class="py-3.5 px-4 text-right">
                                        <div class="inline-flex items-center gap-1.5">
                                            <a href="{{ route('operational.questions.edit', $item->id) }}"
                                                class="w-8 h-8 rounded-lg bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200/60 flex items-center justify-center transition shadow-2xs"
                                                title="Edit Pertanyaan">
                                                <i class="ti ti-edit text-sm"></i>
                                            </a>

                                            <form action="{{ route('operational.questions.destroy', $item->id) }}" method="POST"
                                                class="inline-block"
                                                onsubmit="return confirm('Hapus pertanyaan ini? Jawaban yang sudah tersimpan tetap aman (snapshot).')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="w-8 h-8 rounded-lg bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200/60 flex items-center justify-center transition shadow-2xs cursor-pointer"
                                                    title="Hapus Pertanyaan">
                                                    <i class="ti ti-trash text-sm"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endforeach
</div>
@endsection

@push('scripts')
    <script>
        // Threshold tidak dipakai untuk pertanyaan Yes/No.
        const answerTypeSelect = document.getElementById('answer_type');
        const questionForm = document.getElementById('question-form');

        function toggleThreshold() {
            if (!answerTypeSelect || !questionForm) return;

            const isYesNo = answerTypeSelect.value === 'yes_no';

            questionForm.querySelectorAll('.threshold-field input').forEach(function (input) {
                input.disabled = isYesNo;
                if (isYesNo) input.value = '';
            });

            const note = questionForm.querySelector('.threshold-note');
            if (note) note.classList.toggle('hidden', !isYesNo);
        }

        if (answerTypeSelect) {
            answerTypeSelect.addEventListener('change', toggleThreshold);
            toggleThreshold();
        }
    </script>
@endpush
