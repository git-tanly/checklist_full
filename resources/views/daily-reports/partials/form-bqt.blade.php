{{-- FORM KHUSUS BANQUET (BQT) --}}

@php
    // 1. Ambil Data Detail per Sesi
    $bf = $details['breakfast'] ?? null;
    $lc = $details['lunch'] ?? null;
    $dn = $details['dinner'] ?? null;

    // 2. Ambil Data Master (Staff) - BQT tidak butuh menu upselling
    $restoBqt = $restaurants->where('code', 'BQT')->first();
    $myStaffList = $restoBqt ? $restoBqt->users : [];
@endphp

{{-- ============================================================ --}}
{{-- SESSION: BREAKFAST --}}
{{-- ============================================================ --}}
<div class="card mb-4">
    <div class="card-header bg-light-warning">
        <h5 class="mb-0 text-warning"><i class="ti ti-sun"></i> Breakfast Report</h5>
    </div>
    <div class="card-body">

        {{-- 1. COVER REPORT (MICE/WEDDING/BIRTHDAY) --}}
        <h6 class="fw-bold text-muted mt-3">1. Cover Report</h6>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label small fw-bold">MICE (Pax)</label>
                <input type="number" class="form-control" name="session[breakfast][cover_data][mice]"
                    value="{{ old('session.breakfast.cover_data.mice', $bf->cover_data['mice'] ?? '') }}"
                    placeholder="0">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold">Wedding (Pax)</label>
                <input type="number" class="form-control" name="session[breakfast][cover_data][wedding]"
                    value="{{ old('session.breakfast.cover_data.wedding', $bf->cover_data['wedding'] ?? '') }}"
                    placeholder="0">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold">Birthday (Pax)</label>
                <input type="number" class="form-control" name="session[breakfast][cover_data][birthday]"
                    value="{{ old('session.breakfast.cover_data.birthday', $bf->cover_data['birthday'] ?? '') }}"
                    placeholder="0">
            </div>
        </div>

        <hr>

        {{-- 2. REVENUE REPORT (CUSTOM BREAKDOWN) --}}
        <h6 class="fw-bold text-muted mt-3">2. Revenue Report (IDR)</h6>

        {{-- Hidden Input untuk Total Revenue (Masuk ke kolom revenue_event di DB agar terhitung di Dashboard) --}}
        <input type="hidden" id="total-rev-breakfast-BQT" name="session[breakfast][revenue_event]"
            value="{{ old('session.breakfast.revenue_event', isset($bf->revenue_event) ? $bf->revenue_event : 0) }}">

        <div class="row g-3">
            {{-- Revenue MICE --}}
            <div class="col-md-4">
                <label class="form-label small">Revenue MICE</label>
                <input type="text" class="form-control rupiah bqt-rev-breakfast"
                    name="session[breakfast][additional_data][revenue_mice]"
                    value="{{ old('session.breakfast.additional_data.revenue_mice', $bf->additional_data['revenue_mice'] ?? '') }}"
                    oninput="calculateTotalBqt('breakfast')" autocomplete="off" placeholder="0">
            </div>
            {{-- Revenue Wedding --}}
            <div class="col-md-4">
                <label class="form-label small">Revenue Wedding</label>
                <input type="text" class="form-control rupiah bqt-rev-breakfast"
                    name="session[breakfast][additional_data][revenue_wedding]"
                    value="{{ old('session.breakfast.additional_data.revenue_wedding', $bf->additional_data['revenue_wedding'] ?? '') }}"
                    oninput="calculateTotalBqt('breakfast')" autocomplete="off" placeholder="0">
            </div>
            {{-- Revenue Birthday --}}
            <div class="col-md-4">
                <label class="form-label small">Revenue Birthday</label>
                <input type="text" class="form-control rupiah bqt-rev-breakfast"
                    name="session[breakfast][additional_data][revenue_birthday]"
                    value="{{ old('session.breakfast.additional_data.revenue_birthday', $bf->additional_data['revenue_birthday'] ?? '') }}"
                    oninput="calculateTotalBqt('breakfast')" autocomplete="off" placeholder="0">
            </div>
            {{-- Display Total (Readonly) --}}
            <div class="col-md-12 text-end">
                <small class="text-muted me-2">Total Calculated Revenue:</small>
                <span class="fw-bold text-primary fs-5" id="display-total-breakfast-BQT">Rp 0</span>
            </div>
        </div>

        <hr>

        {{-- 3. REMARKS & STAFF --}}
        <h6 class="fw-bold text-muted mt-3">3. Remarks & Staff</h6>
        <div class="row g-3">
            {{-- Remarks --}}
            <div class="col-md-12">
                <label class="form-label small">General Remarks</label>
                <textarea class="form-control" name="session[breakfast][remarks]">{{ old('session.breakfast.remarks', $bf->remarks ?? '') }}</textarea>
            </div>

            {{-- Staff On Duty (Multi Select) --}}
            <div class="col-md-12 mt-3">
                <label class="form-label small fw-bold">Staff on Duty</label>
                @php
                    $bfStaffVal = old('session.breakfast.staff_on_duty', $bf->staff_on_duty ?? []);
                    $bfStaffJson = is_array($bfStaffVal) ? json_encode($bfStaffVal) : $bfStaffVal;
                @endphp
                <input type="hidden" id="input-staff-breakfast-BQT" name="session[breakfast][staff_on_duty]"
                    value="{{ $bfStaffJson }}">

                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="select-staff-breakfast-BQT">
                        <option value="" selected>Select Staff...</option>
                        @foreach ($myStaffList as $staff)
                            <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                        @endforeach
                    </select>
                    <button class="btn btn-sm btn-dark" type="button" onclick="addStaffItem('breakfast', 'BQT')"><i
                            class="ti ti-plus"></i> Add</button>
                </div>
                <div id="list-staff-breakfast-BQT" class="d-flex flex-wrap"></div>
            </div>
        </div>

        <hr>

        {{-- 4. COMPETITOR --}}
        <h6 class="fw-bold text-muted mt-3">4. Competitor Comparison</h6>
        <div class="row g-3">
            <div class="col-md-4"><label class="small">Shangri-La</label><input type="number" class="form-control"
                    name="session[breakfast][competitor_data][shangrila_cover]"
                    value="{{ old('session.breakfast.competitor_data.shangrila_cover', $bf->competitor_data['shangrila_cover'] ?? '') }}">
            </div>
            <div class="col-md-4"><label class="small">JW Marriott</label><input type="number" class="form-control"
                    name="session[breakfast][competitor_data][jw_marriott_cover]"
                    value="{{ old('session.breakfast.competitor_data.jw_marriott_cover', $bf->competitor_data['jw_marriott_cover'] ?? '') }}">
            </div>
            <div class="col-md-4"><label class="small">Sheraton</label><input type="number" class="form-control"
                    name="session[breakfast][competitor_data][sheraton_cover]"
                    value="{{ old('session.breakfast.competitor_data.sheraton_cover', $bf->competitor_data['sheraton_cover'] ?? '') }}">
            </div>
        </div>
    </div>
</div>


{{-- ============================================================ --}}
{{-- SESSION: LUNCH (COPY DARI BREAKFAST) --}}
{{-- ============================================================ --}}
<div class="card mb-4">
    <div class="card-header bg-light-primary">
        <h5 class="mb-0 text-primary"><i class="ti ti-soup"></i> Lunch Report</h5>
    </div>
    <div class="card-body">
        {{-- 1. COVER --}}
        <h6 class="fw-bold text-muted mt-3">1. Cover Report</h6>
        <div class="row g-3">
            <div class="col-md-4"><label class="form-label small fw-bold">MICE</label><input type="number"
                    class="form-control" name="session[lunch][cover_data][mice]"
                    value="{{ old('session.lunch.cover_data.mice', $lc->cover_data['mice'] ?? '') }}"
                    placeholder="0"></div>
            <div class="col-md-4"><label class="form-label small fw-bold">Wedding</label><input type="number"
                    class="form-control" name="session[lunch][cover_data][wedding]"
                    value="{{ old('session.lunch.cover_data.wedding', $lc->cover_data['wedding'] ?? '') }}"
                    placeholder="0"></div>
            <div class="col-md-4"><label class="form-label small fw-bold">Birthday</label><input type="number"
                    class="form-control" name="session[lunch][cover_data][birthday]"
                    value="{{ old('session.lunch.cover_data.birthday', $lc->cover_data['birthday'] ?? '') }}"
                    placeholder="0"></div>
        </div>
        <hr>
        {{-- 2. REVENUE --}}
        <h6 class="fw-bold text-muted mt-3">2. Revenue Report (IDR)</h6>
        <input type="hidden" id="total-rev-lunch-BQT" name="session[lunch][revenue_event]"
            value="{{ old('session.lunch.revenue_event', isset($lc->revenue_event) ? $lc->revenue_event : 0) }}">
        <div class="row g-3">
            <div class="col-md-4"><label class="small">Revenue MICE</label><input type="text"
                    class="form-control rupiah bqt-rev-lunch" name="session[lunch][additional_data][revenue_mice]"
                    value="{{ old('session.lunch.additional_data.revenue_mice', $lc->additional_data['revenue_mice'] ?? '') }}"
                    oninput="calculateTotalBqt('lunch')" placeholder="0" autocomplete="off"></div>
            <div class="col-md-4"><label class="small">Revenue Wedding</label><input type="text"
                    class="form-control rupiah bqt-rev-lunch" name="session[lunch][additional_data][revenue_wedding]"
                    value="{{ old('session.lunch.additional_data.revenue_wedding', $lc->additional_data['revenue_wedding'] ?? '') }}"
                    oninput="calculateTotalBqt('lunch')" placeholder="0" autocomplete="off"></div>
            <div class="col-md-4"><label class="small">Revenue Birthday</label><input type="text"
                    class="form-control rupiah bqt-rev-lunch" name="session[lunch][additional_data][revenue_birthday]"
                    value="{{ old('session.lunch.additional_data.revenue_birthday', $lc->additional_data['revenue_birthday'] ?? '') }}"
                    oninput="calculateTotalBqt('lunch')" placeholder="0" autocomplete="off"></div>
            <div class="col-md-12 text-end"><small class="text-muted me-2">Total Calculated Revenue:</small><span
                    class="fw-bold text-primary fs-5" id="display-total-lunch-BQT">Rp 0</span></div>
        </div>
        <hr>
        {{-- 3. REMARKS & STAFF --}}
        <h6 class="fw-bold text-muted mt-3">3. Remarks & Staff</h6>
        <div class="row g-3">
            <div class="col-md-12">
                <label class="small">General Remarks</label>
                <textarea class="form-control" name="session[lunch][remarks]">{{ old('session.lunch.remarks', $lc->remarks ?? '') }}</textarea>
            </div>
            <div class="col-md-12 mt-3">
                <label class="small fw-bold">Staff on Duty</label>
                @php
                    $lcStaffVal = old('session.lunch.staff_on_duty', $lc->staff_on_duty ?? []);
                    $lcStaffJson = is_array($lcStaffVal) ? json_encode($lcStaffVal) : $lcStaffVal;
                @endphp
                <input type="hidden" id="input-staff-lunch-BQT" name="session[lunch][staff_on_duty]"
                    value="{{ $lcStaffJson }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="select-staff-lunch-BQT">
                        <option value="" selected>Select Staff...</option>
                        @foreach ($myStaffList as $staff)
                            <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                        @endforeach
                    </select>
                    <button class="btn btn-sm btn-dark" type="button" onclick="addStaffItem('lunch', 'BQT')"><i
                            class="ti ti-plus"></i> Add</button>
                </div>
                <div id="list-staff-lunch-BQT" class="d-flex flex-wrap"></div>
            </div>
        </div>
        <hr>
        {{-- 4. COMPETITOR --}}
        <h6 class="fw-bold text-muted mt-3">4. Competitor Comparison</h6>
        <div class="row g-3">
            <div class="col-md-4"><label class="small">Shangri-La</label><input type="number" class="form-control"
                    name="session[lunch][competitor_data][shangrila_cover]"
                    value="{{ old('session.lunch.competitor_data.shangrila_cover', $lc->competitor_data['shangrila_cover'] ?? '') }}">
            </div>
            <div class="col-md-4"><label class="small">JW Marriott</label><input type="number"
                    class="form-control" name="session[lunch][competitor_data][jw_marriott_cover]"
                    value="{{ old('session.lunch.competitor_data.jw_marriott_cover', $lc->competitor_data['jw_marriott_cover'] ?? '') }}">
            </div>
            <div class="col-md-4"><label class="small">Sheraton</label><input type="number" class="form-control"
                    name="session[lunch][competitor_data][sheraton_cover]"
                    value="{{ old('session.lunch.competitor_data.sheraton_cover', $lc->competitor_data['sheraton_cover'] ?? '') }}">
            </div>
        </div>
    </div>
</div>


{{-- ============================================================ --}}
{{-- SESSION: DINNER (COPY DARI LUNCH) --}}
{{-- ============================================================ --}}
<div class="card mb-4">
    <div class="card-header bg-light-dark text-white">
        <h5 class="mb-0"><i class="ti ti-moon"></i> Dinner Report</h5>
    </div>
    <div class="card-body">
        <h6 class="fw-bold text-muted mt-3">1. Cover Report</h6>
        <div class="row g-3">
            <div class="col-md-4"><label class="form-label small fw-bold">MICE</label><input type="number"
                    class="form-control" name="session[dinner][cover_data][mice]"
                    value="{{ old('session.dinner.cover_data.mice', $dn->cover_data['mice'] ?? '') }}"
                    placeholder="0"></div>
            <div class="col-md-4"><label class="form-label small fw-bold">Wedding</label><input type="number"
                    class="form-control" name="session[dinner][cover_data][wedding]"
                    value="{{ old('session.dinner.cover_data.wedding', $dn->cover_data['wedding'] ?? '') }}"
                    placeholder="0"></div>
            <div class="col-md-4"><label class="form-label small fw-bold">Birthday</label><input type="number"
                    class="form-control" name="session[dinner][cover_data][birthday]"
                    value="{{ old('session.dinner.cover_data.birthday', $dn->cover_data['birthday'] ?? '') }}"
                    placeholder="0"></div>
        </div>
        <hr>
        <h6 class="fw-bold text-muted mt-3">2. Revenue Report (IDR)</h6>
        <input type="hidden" id="total-rev-dinner-BQT" name="session[dinner][revenue_event]"
            value="{{ old('session.dinner.revenue_event', isset($dn->revenue_event) ? $dn->revenue_event : 0) }}">
        <div class="row g-3">
            <div class="col-md-4"><label class="small">Revenue MICE</label><input type="text"
                    class="form-control rupiah bqt-rev-dinner" name="session[dinner][additional_data][revenue_mice]"
                    value="{{ old('session.dinner.additional_data.revenue_mice', $dn->additional_data['revenue_mice'] ?? '') }}"
                    oninput="calculateTotalBqt('dinner')" placeholder="0" autocomplete="off"></div>
            <div class="col-md-4"><label class="small">Revenue Wedding</label><input type="text"
                    class="form-control rupiah bqt-rev-dinner"
                    name="session[dinner][additional_data][revenue_wedding]"
                    value="{{ old('session.dinner.additional_data.revenue_wedding', $dn->additional_data['revenue_wedding'] ?? '') }}"
                    oninput="calculateTotalBqt('dinner')" placeholder="0" autocomplete="off"></div>
            <div class="col-md-4"><label class="small">Revenue Birthday</label><input type="text"
                    class="form-control rupiah bqt-rev-dinner"
                    name="session[dinner][additional_data][revenue_birthday]"
                    value="{{ old('session.dinner.additional_data.revenue_birthday', $dn->additional_data['revenue_birthday'] ?? '') }}"
                    oninput="calculateTotalBqt('dinner')" placeholder="0" autocomplete="off"></div>
            <div class="col-md-12 text-end"><small class="text-muted me-2">Total Calculated Revenue:</small><span
                    class="fw-bold text-primary fs-5" id="display-total-dinner-BQT">Rp 0</span></div>
        </div>
        <hr>
        <h6 class="fw-bold text-muted mt-3">3. Remarks & Staff</h6>
        <div class="row g-3">
            <div class="col-md-12">
                <label class="small">General Remarks</label>
                <textarea class="form-control" name="session[dinner][remarks]">{{ old('session.dinner.remarks', $dn->remarks ?? '') }}</textarea>
            </div>
            <div class="col-md-12 mt-3">
                <label class="small fw-bold">Staff on Duty</label>
                @php
                    $dnStaffVal = old('session.dinner.staff_on_duty', $dn->staff_on_duty ?? []);
                    $dnStaffJson = is_array($dnStaffVal) ? json_encode($dnStaffVal) : $dnStaffVal;
                @endphp
                <input type="hidden" id="input-staff-dinner-BQT" name="session[dinner][staff_on_duty]"
                    value="{{ $dnStaffJson }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="select-staff-dinner-BQT">
                        <option value="" selected>Select Staff...</option>
                        @foreach ($myStaffList as $staff)
                            <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                        @endforeach
                    </select>
                    <button class="btn btn-sm btn-dark" type="button" onclick="addStaffItem('dinner', 'BQT')"><i
                            class="ti ti-plus"></i> Add</button>
                </div>
                <div id="list-staff-dinner-BQT" class="d-flex flex-wrap"></div>
            </div>
        </div>
        <hr>
        <h6 class="fw-bold text-muted mt-3">4. Competitor Comparison</h6>
        <div class="row g-3">
            <div class="col-md-4"><label class="small">Shangri-La</label><input type="number" class="form-control"
                    name="session[dinner][competitor_data][shangrila_cover]"
                    value="{{ old('session.dinner.competitor_data.shangrila_cover', $dn->competitor_data['shangrila_cover'] ?? '') }}">
            </div>
            <div class="col-md-4"><label class="small">JW Marriott</label><input type="number"
                    class="form-control" name="session[dinner][competitor_data][jw_marriott_cover]"
                    value="{{ old('session.dinner.competitor_data.jw_marriott_cover', $dn->competitor_data['jw_marriott_cover'] ?? '') }}">
            </div>
            <div class="col-md-4"><label class="small">Sheraton</label><input type="number" class="form-control"
                    name="session[dinner][competitor_data][sheraton_cover]"
                    value="{{ old('session.dinner.competitor_data.sheraton_cover', $dn->competitor_data['sheraton_cover'] ?? '') }}">
            </div>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- SCRIPT INITIALIZATION & CALCULATOR --}}
{{-- ============================================================ --}}
<script>
    // Fungsi hitung total otomatis
    function calculateTotalBqt(session) {
        let total = 0;
        // Ambil semua input dengan class 'bqt-rev-session'
        const inputs = document.querySelectorAll(`.bqt-rev-${session}`);

        inputs.forEach(input => {
            // Hapus titik ribuan, ubah jadi angka
            let cleanVal = input.value.replace(/\./g, '');
            cleanVal = parseInt(cleanVal) || 0;
            total += cleanVal;
        });

        // Update Hidden Input (Untuk Database)
        document.getElementById(`total-rev-${session}-BQT`).value = total;

        // Update Tampilan Total
        document.getElementById(`display-total-${session}-BQT`).innerText = 'Rp ' + total.toLocaleString('id-ID');
    }

    document.addEventListener('DOMContentLoaded', function() {

        // Init Staff
        let bfStaff = {!! json_encode(old('session.breakfast.staff_on_duty', $bf->staff_on_duty ?? [])) !!};
        initStaff('breakfast', bfStaff, 'BQT');

        let lcStaff = {!! json_encode(old('session.lunch.staff_on_duty', $lc->staff_on_duty ?? [])) !!};
        initStaff('lunch', lcStaff, 'BQT');

        let dnStaff = {!! json_encode(old('session.dinner.staff_on_duty', $dn->staff_on_duty ?? [])) !!};
        initStaff('dinner', dnStaff, 'BQT');

        // Trigger Kalkulasi Total saat Load (untuk Edit/Error State)
        calculateTotalBqt('breakfast');
        calculateTotalBqt('lunch');
        calculateTotalBqt('dinner');
    });
</script>
