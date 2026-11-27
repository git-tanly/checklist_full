@extends('layouts.portal')

@section('title', 'Edit Pertanyaan Checklist')
@section('breadcrumb', 'Checklist Sections')

@section('content')
<div class="space-y-6">

    {{-- 1. EXECUTIVE HEADER BANNER --}}
    <div class="rounded-2xl border border-slate-200/90 shadow-sm bg-white p-5 md:p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 shadow-sm" style="background-color: #172a40; color: #B89047;">
                    <i class="ti ti-edit text-2xl"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-xl font-bold tracking-tight text-slate-800">Edit Pertanyaan</h1>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                            {{ ucfirst($question->session_type) }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        <i class="ti ti-layout-grid text-slate-400"></i>
                        {{ $section->name }} &mdash; {{ $section->restaurant->code }} {{ $section->restaurant->name }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('operational.questions.create', ['section' => $section->id]) }}"
                    class="btn btn-light rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-100 transition inline-flex items-center gap-1.5 shadow-sm">
                    <i class="ti ti-arrow-left text-base"></i> Kembali ke Daftar
                </a>
            </div>
        </div>
    </div>

    {{-- ALERTS --}}
    @if (isset($errors) && $errors->any())
        <div class="max-w-3xl mx-auto rounded-2xl border border-rose-200 bg-rose-50/90 p-4 text-rose-800 shadow-sm">
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

    {{-- FORM CARD --}}
    <div class="max-w-3xl mx-auto bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-slate-900 via-[#172a40] to-slate-800 text-white px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center text-[#B89047]">
                    <i class="ti ti-checklist text-base"></i>
                </span>
                <div>
                    <h2 class="text-sm font-bold text-white tracking-tight">Detail Pertanyaan</h2>
                    <p class="text-[11px] text-slate-300 mb-0">Perbarui teks pertanyaan, tipe jawaban, dan batas standar penilaian</p>
                </div>
            </div>
            <span class="w-2 h-2 rounded-full bg-[#B89047]"></span>
        </div>

        <div class="p-6">
            <form method="POST" action="{{ route('operational.questions.update', $question->id) }}" id="question-form">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    {{-- Sesi --}}
                    <div>
                        <label for="session_type" class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center gap-1.5">
                            <i class="ti ti-clock text-slate-400"></i> Sesi <span class="text-rose-500">*</span>
                        </label>
                        <select id="session_type" name="session_type" required
                            class="w-full px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all">
                            @foreach (['breakfast', 'lunch', 'dinner'] as $session)
                                <option value="{{ $session }}"
                                    {{ (string) old('session_type', $question->session_type) === $session ? 'selected' : '' }}>
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
                            <option value="number" {{ (string) old('answer_type', $question->answer_type) === 'number' ? 'selected' : '' }}>Angka (jumlah / suhu)</option>
                            <option value="yes_no" {{ (string) old('answer_type', $question->answer_type) === 'yes_no' ? 'selected' : '' }}>Yes / No</option>
                        </select>
                    </div>

                    {{-- Urutan --}}
                    <div>
                        <label for="sort_order" class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center gap-1.5">
                            <i class="ti ti-sort-ascending text-slate-400"></i> Urutan Tampil
                        </label>
                        <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', $question->sort_order) }}" min="0" max="65535"
                            class="w-full px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all">
                        <p class="text-[11px] text-slate-400 mt-1.5 mb-0">Angka kecil tampil lebih dulu.</p>
                    </div>

                    {{-- Check point --}}
                    <div class="sm:col-span-2 lg:col-span-3">
                        <label for="check_point" class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center gap-1.5">
                            <i class="ti ti-checklist text-slate-400"></i> Check Point / Standard <span class="text-rose-500">*</span>
                        </label>
                        <textarea id="check_point" name="check_point" rows="2" required maxlength="2000"
                            class="w-full px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all">{{ old('check_point', $question->check_point) }}</textarea>
                    </div>

                    {{-- Key check point --}}
                    <div class="sm:col-span-2 lg:col-span-3">
                        <label for="key_check_point" class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center gap-1.5">
                            <i class="ti ti-bulb text-slate-400"></i> Key Check Point <span class="text-[11px] font-medium text-slate-400">(guide)</span>
                        </label>
                        <textarea id="key_check_point" name="key_check_point" rows="2" maxlength="2000"
                            class="w-full px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all">{{ old('key_check_point', $question->key_check_point) }}</textarea>
                    </div>

                    {{-- Threshold --}}
                    <div class="threshold-field">
                        <label for="std_min" class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center gap-1.5">
                            <i class="ti ti-arrow-bar-down text-slate-400"></i> Std Min
                        </label>
                        <input type="number" step="any" id="std_min" name="std_min" value="{{ old('std_min', $question->std_min) }}" min="0"
                            class="w-full px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                    </div>

                    <div class="threshold-field">
                        <label for="std_max" class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center gap-1.5">
                            <i class="ti ti-arrow-bar-up text-slate-400"></i> Std Max
                        </label>
                        <input type="number" step="any" id="std_max" name="std_max" value="{{ old('std_max', $question->std_max) }}" min="0"
                            class="w-full px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                    </div>

                    <div class="threshold-field">
                        <label for="temp_max" class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center gap-1.5">
                            <i class="ti ti-temperature text-slate-400"></i> Suhu Max
                        </label>
                        <input type="number" step="any" id="temp_max" name="temp_max" value="{{ old('temp_max', $question->temp_max) }}" min="0"
                            class="w-full px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                    </div>

                    <div class="threshold-field">
                        <label for="unit" class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center gap-1.5">
                            <i class="ti ti-ruler-measure text-slate-400"></i> Unit
                        </label>
                        <input type="text" id="unit" name="unit" value="{{ old('unit', $question->unit) }}" maxlength="16"
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

                {{-- Status & Actions --}}
                <div class="mt-6 pt-4 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input cursor-pointer" type="checkbox" role="switch" id="is_active"
                            name="is_active" value="1" {{ (bool) old('is_active', $question->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label text-xs font-bold text-slate-700" for="is_active">
                            Aktif
                        </label>
                        <p class="text-[11px] text-slate-400 mt-1 mb-0">Pertanyaan nonaktif tidak muncul di checklist staff.</p>
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('operational.questions.create', ['section' => $section->id]) }}"
                            class="btn btn-light rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-100 transition shadow-2xs">
                            Batal
                        </a>
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-sm text-white bg-[#172a40] hover:bg-[#112031] transition shadow-sm hover:shadow cursor-pointer">
                            <i class="ti ti-device-floppy text-base"></i> Perbarui
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
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
