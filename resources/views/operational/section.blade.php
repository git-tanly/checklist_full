@extends('layouts.checklist')

@section('title', 'Checklist '.$section->name.' - '.$restaurant->name)
@section('header_title', $section->name)

@section('header_action')
    <a href="{{ route('operational.entry', $restaurant->checklist_token) }}"
        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/10 text-white transition hover:bg-white/20"
        aria-label="Kembali">
        <i class="bi bi-arrow-left"></i>
    </a>
@endsection

@section('content')
    {{-- Info sesi --}}
    <div class="mb-4 flex items-center justify-between rounded-xl bg-[#172a40] px-4 py-3 text-white">
        <div>
            <p class="mb-0 text-[10px] font-semibold uppercase tracking-wider text-white/50">Sesi</p>
            <p class="mb-0 text-base font-bold capitalize">{{ $session['session_type'] }}</p>
        </div>
        <div class="text-right">
            <p class="mb-0 text-[10px] font-semibold uppercase tracking-wider text-white/50">Tanggal</p>
            <p class="mb-0 text-sm font-semibold">
                {{ \Carbon\Carbon::parse($session['business_date'])->translatedFormat('d M Y') }}
            </p>
        </div>
    </div>

    @if ($submission?->status === 'rejected' && $submission->verification_note)
        <div class="mb-4 rounded-xl border border-red-300 bg-red-50 px-4 py-3">
            <p class="mb-1 text-xs font-bold uppercase tracking-wide text-red-700">Catatan dari manajer</p>
            <p class="mb-0 text-sm text-red-800">{{ $submission->verification_note }}</p>
        </div>
    @endif

    <form id="checklist-form" method="POST"
        action="{{ route('operational.submit', [$restaurant->checklist_token, $section->id]) }}" novalidate>
        @csrf

        {{-- Pilih staff --}}
        <div class="card mb-4 p-4">
            <label for="employee_id" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">
                Nama Staff <span class="text-red-500">*</span>
            </label>
            <select name="employee_id" id="employee_id" required
                class="form-select rounded-xl border-slate-300 py-3 text-sm font-medium focus:border-[#172a40] focus:ring-[#172a40]">
                <option value="" disabled {{ old('employee_id') ? '' : 'selected' }}>-- Pilih nama Anda --</option>
                @foreach ($employees as $employee)
                    <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                        {{ $employee->name }}@if ($employee->position) &mdash; {{ $employee->position }} @endif
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Daftar pertanyaan --}}
        <div class="grid grid-cols-1 gap-3">
            @foreach ($questions as $index => $question)
                @php
                    $old = $oldAnswers->get($question->id);
                    $oldValue = $old?->answer_type === 'number' ? $old->answer_number : ($old?->answer_boolean ? 'yes' : 'no');
                    $value = old('answers.'.$index.'.value', $submission && $submission->status === 'rejected' ? $oldValue : null);
                    $remarks = old('answers.'.$index.'.remarks', $submission && $submission->status === 'rejected' ? $old?->remarks : null);
                @endphp
                <div class="card p-4">
                    <div class="mb-3 flex gap-3">
                        <span
                            class="flex h-6 w-6 shrink-0 items-center justify-center rounded-md bg-[#172a40] text-xs font-bold text-white">
                            {{ $index + 1 }}
                        </span>
                        <div class="min-w-0">
                            <p class="mb-1 text-sm font-semibold leading-snug text-[#172a40]">{{ $question->check_point }}</p>
                            @if ($question->key_check_point)
                                <p class="mb-0 text-xs leading-relaxed text-slate-500">
                                    <span class="font-semibold text-slate-600">Panduan: </span>{{ $question->key_check_point }}
                                </p>
                            @endif
                        </div>
                    </div>

                    @if ($question->answer_type === 'number')
                        <div class="flex items-stretch gap-2">
                            <input type="number" step="any" inputmode="decimal"
                                name="answers[{{ $index }}][value]" value="{{ $value }}"
                                placeholder="Isi nilai..." required
                                class="form-control rounded-xl border-slate-300 py-3 text-sm font-semibold focus:border-[#172a40] focus:ring-[#172a40]">
                            @if ($question->unit)
                                <span
                                    class="flex min-w-[52px] items-center justify-center rounded-xl bg-slate-100 px-3 text-xs font-bold uppercase text-slate-500">
                                    {{ $question->unit }}
                                </span>
                            @endif
                        </div>
                    @else
                        <div class="grid grid-cols-2 gap-2">
                            <label class="yes-no-option">
                                <input class="yes-input" type="radio" name="answers[{{ $index }}][value]" value="yes"
                                    {{ $value === 'yes' ? 'checked' : '' }} required>
                                <span><i class="bi bi-check-lg me-1"></i>Yes</span>
                            </label>
                            <label class="yes-no-option">
                                <input class="no-input" type="radio" name="answers[{{ $index }}][value]" value="no"
                                    {{ $value === 'no' ? 'checked' : '' }} required>
                                <span><i class="bi bi-x-lg me-1"></i>No</span>
                            </label>
                        </div>
                    @endif

                    <input type="hidden" name="answers[{{ $index }}][question_id]" value="{{ $question->id }}">

                    {{-- Remarks opsional (buka/tutup) --}}
                    <div class="mt-3">
                        <button type="button"
                            class="flex items-center gap-1 border-0 bg-transparent p-0 text-xs font-semibold text-[#245177]"
                            onclick="const box=this.parentElement.querySelector('.remarks-box'); box.classList.toggle('d-none');">
                            <i class="bi bi-chat-left-text"></i> Remarks (opsional)
                        </button>
                        <div class="remarks-box mt-2 {{ empty($remarks) ? 'd-none' : '' }}">
                            <textarea name="answers[{{ $index }}][remarks]" rows="2" maxlength="500"
                                placeholder="Tulis catatan tambahan..."
                                class="form-control rounded-xl border-slate-300 text-sm focus:border-[#172a40] focus:ring-[#172a40]">{{ $remarks }}</textarea>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Submit --}}
        <div class="sticky bottom-0 -mx-4 mt-5 border-t border-slate-200 bg-[#f4f8fb]/95 px-4 py-3 backdrop-blur sm:-mx-6 sm:px-6">
            <button type="submit" id="submit-btn"
                class="btn btn-navy w-full py-3 text-sm uppercase tracking-wider">
                <i class="bi bi-send me-1.5"></i> Kirim Checklist
            </button>
            <p class="mb-0 mt-2 text-center text-[11px] text-slate-400">
                Pastikan semua poin terisi sebelum mengirim.
            </p>
        </div>
    </form>
@endsection

@section('scripts')
    <script>
        // Cegah submit ganda + konfirmasi ringan.
        document.getElementById('checklist-form').addEventListener('submit', function (e) {
            const form = e.target;

            if (!form.checkValidity()) {
                e.preventDefault();
                form.reportValidity();
                return;
            }

            const btn = document.getElementById('submit-btn');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mengirim...';
        });
    </script>
@endsection
