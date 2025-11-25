{{-- FORM KHUSUS JOE MILANO (JML) --}}

@php
    // 1. Ambil Data Detail per Sesi
    $bf = $details['breakfast'] ?? null;
    $lc = $details['lunch'] ?? null;
    $dn = $details['dinner'] ?? null;

    // 2. Ambil Data Master (Staff & Menu) khusus Joe Milano
    $restoJml = $restaurants->where('code', 'JM')->first();

    // A. Staff List
    $myStaffList = $restoJml ? $restoJml->users : [];

    // B. Upselling Menu
    $myMenu = $restoJml && isset($upsellingItems[$restoJml->id]) ? $upsellingItems[$restoJml->id] : collect([]);
    $foods = $myMenu->where('type', 'food');
    $beverages = $myMenu->where('type', 'beverage');
@endphp

{{-- ============================================================ --}}
{{-- SESSION: BREAKFAST --}}
{{-- ============================================================ --}}
<div class="card mb-4">
    <div class="card-header bg-light-warning">
        <h5 class="mb-0 text-warning"><i class="ti ti-sun"></i> Breakfast Report</h5>
    </div>
    <div class="card-body">

        {{-- 1. COVER REPORT (SIMPLE) --}}
        <h6 class="fw-bold text-muted mt-3">1. Cover Report</h6>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label small fw-bold">Total Actual Cover</label>
                <input type="number" class="form-control" name="session[breakfast][cover_data][total_actual_cover]"
                    value="{{ old('session.breakfast.cover_data.total_actual_cover', $bf->cover_data['total_actual_cover'] ?? '') }}"
                    placeholder="0">
            </div>
        </div>

        <hr>

        {{-- 2. REVENUE REPORT --}}
        <h6 class="fw-bold text-muted mt-3">2. Revenue Report (IDR)</h6>
        <div class="row g-3">
            <div class="col-md-3"><label class="small">Food Revenue</label><input type="text"
                    class="form-control rupiah" name="session[breakfast][revenue_food]"
                    value="{{ old('session.breakfast.revenue_food', isset($bf->revenue_food) ? number_format($bf->revenue_food, 0, ',', '.') : '') }}"
                    placeholder="0" autocomplete="off">
            </div>
            <div class="col-md-3"><label class="small">Beverage Revenue</label><input type="text"
                    class="form-control rupiah" name="session[breakfast][revenue_beverage]"
                    value="{{ old('session.breakfast.revenue_beverage', isset($bf->revenue_beverage) ? number_format($bf->revenue_beverage, 0, ',', '.') : '') }}"
                    placeholder="0" autocomplete="off">
            </div>
            <div class="col-md-3"><label class="small">Other Revenue</label><input type="text"
                    class="form-control rupiah" name="session[breakfast][revenue_others]"
                    value="{{ old('session.breakfast.revenue_others', isset($bf->revenue_others) ? number_format($bf->revenue_others, 0, ',', '.') : '') }}"
                    placeholder="0" autocomplete="off">
            </div>
            {{-- Tetap sediakan Event revenue untuk konsistensi database, jika tidak dipakai user bisa isi 0 --}}
            <div class="col-md-3"><label class="small">Event Revenue</label><input type="text"
                    class="form-control rupiah" name="session[breakfast][revenue_event]"
                    value="{{ old('session.breakfast.revenue_event', isset($bf->revenue_event) ? number_format($bf->revenue_event, 0, ',', '.') : '') }}"
                    placeholder="0" autocomplete="off">
            </div>
        </div>

        <hr>

        {{-- 3. SMART COMPONENTS --}}
        <h6 class="fw-bold text-muted mt-3">3. Upselling & Remarks</h6>
        <div class="row g-3">
            {{-- Food --}}
            <div class="col-md-6">
                <label class="form-label small fw-bold">Upselling Menu (Food)</label>
                @php $bfFoodVal = old('session.breakfast.upselling_data.food', $bf->upselling_data['food'] ?? []); @endphp
                <input type="hidden" id="input-breakfast-food-JM" name="session[breakfast][upselling_data][food]"
                    value="{{ is_array($bfFoodVal) ? json_encode($bfFoodVal) : $bfFoodVal }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="select-breakfast-food-JM">
                        <option value="" selected>Select Food...</option>
                        @foreach ($foods as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                    <input type="number" class="form-control form-control-sm" id="pax-breakfast-food-JM"
                        placeholder="Qty" style="max-width: 70px;">
                    <button class="btn btn-sm btn-dark" type="button"
                        onclick="addUpsellingItem('breakfast', 'food', 'JM')"><i class="ti ti-plus"></i> Add</button>
                </div>
                <ul class="list-group small" id="list-breakfast-food-JM"></ul>
            </div>
            {{-- Bev --}}
            <div class="col-md-6">
                <label class="form-label small fw-bold">Beverage Upselling</label>
                @php $bfBevVal = old('session.breakfast.upselling_data.beverage', $bf->upselling_data['beverage'] ?? []); @endphp
                <input type="hidden" id="input-breakfast-beverage-JM"
                    name="session[breakfast][upselling_data][beverage]"
                    value="{{ is_array($bfBevVal) ? json_encode($bfBevVal) : $bfBevVal }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="select-breakfast-beverage-JM">
                        <option value="" selected>Select Drink...</option>
                        @foreach ($beverages as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                    <input type="number" class="form-control form-control-sm" id="pax-breakfast-beverage-JM"
                        placeholder="Qty" style="max-width: 70px;">
                    <button class="btn btn-sm btn-dark" type="button"
                        onclick="addUpsellingItem('breakfast', 'beverage', 'JM')"><i class="ti ti-plus"></i>
                        Add</button>
                </div>
                <ul class="list-group small" id="list-breakfast-beverage-JM"></ul>
            </div>
            {{-- VIP --}}
            {{-- <div class="col-md-12 mt-3">
                <label class="form-label small fw-bold">VIP 1 & 2 List</label>
                @php $bfVipVal = old('session.breakfast.vip_remarks', $bf->vip_remarks ?? []); @endphp
                <input type="hidden" id="input-vip-breakfast-JM" name="session[breakfast][vip_remarks]"
                    value="{{ is_array($bfVipVal) ? json_encode($bfVipVal) : $bfVipVal }}">
                <div class="input-group mb-2">
                    <input type="text" class="form-control form-control-sm" id="vip-name-breakfast-JM"
                        placeholder="Guest Name (e.g. Mr. Budi)">
                    <input type="text" class="form-control form-control-sm" id="vip-pos-breakfast-JM"
                        placeholder="Position/Title (e.g. CEO)">
                    <button class="btn btn-sm btn-dark" type="button" onclick="addVipItem('breakfast', 'JM')"><i
                            class="ti ti-plus"></i> Add</button>
                </div>
                <ul class="list-group small" id="list-vip-breakfast-JM"></ul>
            </div> --}}
            {{-- Remarks --}}
            <div class="col-md-12">
                <label class="form-label small">General Remarks</label>
                <textarea class="form-control" name="session[breakfast][remarks]">{{ old('session.breakfast.remarks', $bf->remarks ?? '') }}</textarea>
            </div>
            {{-- Staff --}}
            <div class="col-md-12 mt-3">
                <label class="form-label small fw-bold">Staff on Duty</label>
                @php $bfStaffVal = old('session.breakfast.staff_on_duty', $bf->staff_on_duty ?? []); @endphp
                <input type="hidden" id="input-staff-breakfast-JM" name="session[breakfast][staff_on_duty]"
                    value="{{ is_array($bfStaffVal) ? json_encode($bfStaffVal) : $bfStaffVal }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="select-staff-breakfast-JM">
                        <option value="" selected>Select Staff...</option>
                        @foreach ($myStaffList as $staff)
                            <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                        @endforeach
                    </select>
                    <button class="btn btn-sm btn-dark" type="button" onclick="addStaffItem('breakfast', 'JM')"><i
                            class="ti ti-plus"></i> Add</button>
                </div>
                <div id="list-staff-breakfast-JM" class="d-flex flex-wrap"></div>
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
            <div class="col-md-4"><label class="small">JW Marriott</label><input type="number"
                    class="form-control" name="session[breakfast][competitor_data][jw_marriott_cover]"
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
{{-- SESSION: LUNCH --}}
{{-- ============================================================ --}}
<div class="card mb-4">
    <div class="card-header bg-light-primary">
        <h5 class="mb-0 text-primary"><i class="ti ti-soup"></i> Lunch Report</h5>
    </div>
    <div class="card-body">
        <h6 class="fw-bold text-muted mt-3">1. Cover Report</h6>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label small fw-bold">Total Actual Cover</label>
                <input type="number" class="form-control" name="session[lunch][cover_data][total_actual_cover]"
                    value="{{ old('session.lunch.cover_data.total_actual_cover', $lc->cover_data['total_actual_cover'] ?? '') }}"
                    placeholder="0">
            </div>
        </div>
        <hr>
        <h6 class="fw-bold text-muted mt-3">2. Revenue Report (IDR)</h6>
        <div class="row g-3">
            <div class="col-md-3"><label class="small">Food</label><input type="text"
                    class="form-control rupiah" name="session[lunch][revenue_food]"
                    value="{{ old('session.lunch.revenue_food', isset($lc->revenue_food) ? number_format($lc->revenue_food, 0, ',', '.') : '') }}"
                    placeholder="0" autocomplete="off">
            </div>
            <div class="col-md-3"><label class="small">Beverage</label><input type="text"
                    class="form-control rupiah" name="session[lunch][revenue_beverage]"
                    value="{{ old('session.lunch.revenue_beverage', isset($lc->revenue_beverage) ? number_format($lc->revenue_beverage, 0, ',', '.') : '') }}"
                    placeholder="0" autocomplete="off">
            </div>
            <div class="col-md-3"><label class="small">Others</label><input type="text"
                    class="form-control rupiah" name="session[lunch][revenue_others]"
                    value="{{ old('session.lunch.revenue_others', isset($lc->revenue_others) ? number_format($lc->revenue_others, 0, ',', '.') : '') }}"
                    placeholder="0" autocomplete="off">
            </div>
            <div class="col-md-3"><label class="small">Event</label><input type="text"
                    class="form-control rupiah" name="session[lunch][revenue_event]"
                    value="{{ old('session.lunch.revenue_event', isset($lc->revenue_event) ? number_format($lc->revenue_event, 0, ',', '.') : '') }}"
                    placeholder="0" autocomplete="off">
            </div>
        </div>
        <hr>
        <h6 class="fw-bold text-muted mt-3">3. Upselling & Remarks</h6>
        <div class="row g-3">
            {{-- Food --}}
            <div class="col-md-6">
                <label class="form-label small fw-bold">Upselling Menu (Food)</label>
                @php $lcFoodVal = old('session.lunch.upselling_data.food', $lc->upselling_data['food'] ?? []); @endphp
                <input type="hidden" id="input-lunch-food-JM" name="session[lunch][upselling_data][food]"
                    value="{{ is_array($lcFoodVal) ? json_encode($lcFoodVal) : $lcFoodVal }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="select-lunch-food-JM">
                        <option value="" selected>Select Food...</option>
                        @foreach ($foods as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                    <input type="number" class="form-control form-control-sm" id="pax-lunch-food-JM"
                        placeholder="Qty" style="max-width: 70px;">
                    <button class="btn btn-sm btn-dark" type="button"
                        onclick="addUpsellingItem('lunch', 'food', 'JM')"><i class="ti ti-plus"></i> Add</button>
                </div>
                <ul class="list-group small" id="list-lunch-food-JM"></ul>
            </div>
            {{-- Bev --}}
            <div class="col-md-6">
                <label class="form-label small fw-bold">Beverage Upselling</label>
                @php $lcBevVal = old('session.lunch.upselling_data.beverage', $lc->upselling_data['beverage'] ?? []); @endphp
                <input type="hidden" id="input-lunch-beverage-JM" name="session[lunch][upselling_data][beverage]"
                    value="{{ is_array($lcBevVal) ? json_encode($lcBevVal) : $lcBevVal }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="select-lunch-beverage-JM">
                        <option value="" selected>Select Drink...</option>
                        @foreach ($beverages as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                    <input type="number" class="form-control form-control-sm" id="pax-lunch-beverage-JM"
                        placeholder="Qty" style="max-width: 70px;">
                    <button class="btn btn-sm btn-dark" type="button"
                        onclick="addUpsellingItem('lunch', 'beverage', 'JM')"><i class="ti ti-plus"></i> Add</button>
                </div>
                <ul class="list-group small" id="list-lunch-beverage-JM"></ul>
            </div>
            {{-- VIP --}}
            {{-- <div class="col-md-12 mt-3">
                <label class="form-label small fw-bold">VIP 1 & 2 List</label>
                @php $lcVipVal = old('session.lunch.vip_remarks', $lc->vip_remarks ?? []); @endphp
                <input type="hidden" id="input-vip-lunch-JM" name="session[lunch][vip_remarks]"
                    value="{{ is_array($lcVipVal) ? json_encode($lcVipVal) : $lcVipVal }}">
                <div class="input-group mb-2">
                    <input type="text" class="form-control form-control-sm" id="vip-name-lunch-JM"
                        placeholder="Guest Name (e.g. Mr. Budi)">
                    <input type="text" class="form-control form-control-sm" id="vip-pos-lunch-JM"
                        placeholder="Position/Title (e.g. CEO)">
                    <button class="btn btn-sm btn-dark" type="button" onclick="addVipItem('lunch', 'JM')"><i
                            class="ti ti-plus"></i> Add</button>
                </div>
                <ul class="list-group small" id="list-vip-lunch-JM"></ul>
            </div> --}}
            {{-- Remarks --}}
            <div class="col-md-12">
                <label class="form-label small">General Remarks</label>
                <textarea class="form-control" name="session[lunch][remarks]">{{ old('session.lunch.remarks', $lc->remarks ?? '') }}</textarea>
            </div>
            {{-- Staff --}}
            <div class="col-md-12 mt-3">
                <label class="form-label small fw-bold">Staff on Duty</label>
                @php $lcStaffVal = old('session.lunch.staff_on_duty', $lc->staff_on_duty ?? []); @endphp
                <input type="hidden" id="input-staff-lunch-JM" name="session[lunch][staff_on_duty]"
                    value="{{ is_array($lcStaffVal) ? json_encode($lcStaffVal) : $lcStaffVal }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="select-staff-lunch-JM">
                        <option value="" selected>Select Staff...</option>
                        @foreach ($myStaffList as $staff)
                            <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                        @endforeach
                    </select>
                    <button class="btn btn-sm btn-dark" type="button" onclick="addStaffItem('lunch', 'JM')"><i
                            class="ti ti-plus"></i> Add</button>
                </div>
                <div id="list-staff-lunch-JM" class="d-flex flex-wrap"></div>
            </div>
        </div>
        <hr>
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
{{-- SESSION: DINNER --}}
{{-- ============================================================ --}}
<div class="card mb-4">
    <div class="card-header bg-light-dark text-white">
        <h5 class="mb-0"><i class="ti ti-moon"></i> Dinner Report</h5>
    </div>
    <div class="card-body">
        <h6 class="fw-bold text-muted mt-3">1. Cover Report</h6>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label small fw-bold">Total Actual Cover</label>
                <input type="number" class="form-control" name="session[dinner][cover_data][total_actual_cover]"
                    value="{{ old('session.dinner.cover_data.total_actual_cover', $dn->cover_data['total_actual_cover'] ?? '') }}"
                    placeholder="0">
            </div>
        </div>
        <hr>
        <h6 class="fw-bold text-muted mt-3">2. Revenue Report (IDR)</h6>
        <div class="row g-3">
            <div class="col-md-3"><label class="small">Food</label><input type="text"
                    class="form-control rupiah" name="session[dinner][revenue_food]"
                    value="{{ old('session.dinner.revenue_food', isset($dn->revenue_food) ? number_format($dn->revenue_food, 0, ',', '.') : '') }}"
                    placeholder="0" autocomplete="off">
            </div>
            <div class="col-md-3"><label class="small">Beverage</label><input type="text"
                    class="form-control rupiah" name="session[dinner][revenue_beverage]"
                    value="{{ old('session.dinner.revenue_beverage', isset($dn->revenue_beverage) ? number_format($dn->revenue_beverage, 0, ',', '.') : '') }}"
                    placeholder="0" autocomplete="off">
            </div>
            <div class="col-md-3"><label class="small">Others</label><input type="text"
                    class="form-control rupiah" name="session[dinner][revenue_others]"
                    value="{{ old('session.dinner.revenue_others', isset($dn->revenue_others) ? number_format($dn->revenue_others, 0, ',', '.') : '') }}"
                    placeholder="0" autocomplete="off">
            </div>
            <div class="col-md-3"><label class="small">Event</label><input type="text"
                    class="form-control rupiah" name="session[dinner][revenue_event]"
                    value="{{ old('session.dinner.revenue_event', isset($dn->revenue_event) ? number_format($dn->revenue_event, 0, ',', '.') : '') }}"
                    placeholder="0" autocomplete="off">
            </div>
        </div>
        <hr>
        <h6 class="fw-bold text-muted mt-3">3. Upselling & Remarks</h6>
        <div class="row g-3">
            {{-- Food --}}
            <div class="col-md-6">
                <label class="form-label small fw-bold">Upselling Menu (Food)</label>
                @php $dnFoodVal = old('session.dinner.upselling_data.food', $dn->upselling_data['food'] ?? []); @endphp
                <input type="hidden" id="input-dinner-food-JM" name="session[dinner][upselling_data][food]"
                    value="{{ is_array($dnFoodVal) ? json_encode($dnFoodVal) : $dnFoodVal }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="select-dinner-food-JM">
                        <option value="" selected>Select Food...</option>
                        @foreach ($foods as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                    <input type="number" class="form-control form-control-sm" id="pax-dinner-food-JM"
                        placeholder="Qty" style="max-width: 70px;">
                    <button class="btn btn-sm btn-dark" type="button"
                        onclick="addUpsellingItem('dinner', 'food', 'JM')"><i class="ti ti-plus"></i> Add</button>
                </div>
                <ul class="list-group small" id="list-dinner-food-JM"></ul>
            </div>
            {{-- Bev --}}
            <div class="col-md-6">
                <label class="form-label small fw-bold">Beverage Upselling</label>
                @php $dnBevVal = old('session.dinner.upselling_data.beverage', $dn->upselling_data['beverage'] ?? []); @endphp
                <input type="hidden" id="input-dinner-beverage-JM" name="session[dinner][upselling_data][beverage]"
                    value="{{ is_array($dnBevVal) ? json_encode($dnBevVal) : $dnBevVal }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="select-dinner-beverage-JM">
                        <option value="" selected>Select Drink...</option>
                        @foreach ($beverages as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                    <input type="number" class="form-control form-control-sm" id="pax-dinner-beverage-JM"
                        placeholder="Qty" style="max-width: 70px;">
                    <button class="btn btn-sm btn-dark" type="button"
                        onclick="addUpsellingItem('dinner', 'beverage', 'JM')"><i class="ti ti-plus"></i> Add</button>
                </div>
                <ul class="list-group small" id="list-dinner-beverage-JM"></ul>
            </div>
            {{-- VIP --}}
            {{-- <div class="col-md-12 mt-3">
                <label class="form-label small fw-bold">VIP 1 & 2 List</label>
                @php $dnVipVal = old('session.dinner.vip_remarks', $dn->vip_remarks ?? []); @endphp
                <input type="hidden" id="input-vip-dinner-JM" name="session[dinner][vip_remarks]"
                    value="{{ is_array($dnVipVal) ? json_encode($dnVipVal) : $dnVipVal }}">
                <div class="input-group mb-2">
                    <input type="text" class="form-control form-control-sm" id="vip-name-dinner-JM"
                        placeholder="Guest Name (e.g. Mr. Budi)">
                    <input type="text" class="form-control form-control-sm" id="vip-pos-dinner-JM"
                        placeholder="Position/Title (e.g. CEO)">
                    <button class="btn btn-sm btn-dark" type="button" onclick="addVipItem('dinner', 'JM')"><i
                            class="ti ti-plus"></i> Add</button>
                </div>
                <ul class="list-group small" id="list-vip-dinner-JM"></ul>
            </div> --}}
            {{-- Remarks --}}
            <div class="col-md-12">
                <label class="form-label small">General Remarks</label>
                <textarea class="form-control" name="session[dinner][remarks]">{{ old('session.dinner.remarks', $dn->remarks ?? '') }}</textarea>
            </div>
            {{-- Staff --}}
            <div class="col-md-12 mt-3">
                <label class="form-label small fw-bold">Staff on Duty</label>
                @php $dnStaffVal = old('session.dinner.staff_on_duty', $dn->staff_on_duty ?? []); @endphp
                <input type="hidden" id="input-staff-dinner-JM" name="session[dinner][staff_on_duty]"
                    value="{{ is_array($dnStaffVal) ? json_encode($dnStaffVal) : $dnStaffVal }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="select-staff-dinner-JM">
                        <option value="" selected>Select Staff...</option>
                        @foreach ($myStaffList as $staff)
                            <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                        @endforeach
                    </select>
                    <button class="btn btn-sm btn-dark" type="button" onclick="addStaffItem('dinner', 'JM')"><i
                            class="ti ti-plus"></i> Add</button>
                </div>
                <div id="list-staff-dinner-JM" class="d-flex flex-wrap"></div>
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
{{-- SCRIPT INITIALIZATION --}}
{{-- ============================================================ --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {

        // --- BREAKFAST INIT ---
        let bfFood = {!! json_encode(old('session.breakfast.upselling_data.food', $bf->upselling_data['food'] ?? [])) !!};
        initUpselling('breakfast', 'food', bfFood, 'JM');
        let bfBev = {!! json_encode(old('session.breakfast.upselling_data.beverage', $bf->upselling_data['beverage'] ?? [])) !!};
        initUpselling('breakfast', 'beverage', bfBev, 'JM');
        let bfVip = {!! json_encode(old('session.breakfast.vip_remarks', $bf->vip_remarks ?? [])) !!};
        initVip('breakfast', bfVip, 'JM');
        let bfStaff = {!! json_encode(old('session.breakfast.staff_on_duty', $bf->staff_on_duty ?? [])) !!};
        initStaff('breakfast', bfStaff, 'JM');

        // --- LUNCH INIT ---
        let lcFood = {!! json_encode(old('session.lunch.upselling_data.food', $lc->upselling_data['food'] ?? [])) !!};
        initUpselling('lunch', 'food', lcFood, 'JM');
        let lcBev = {!! json_encode(old('session.lunch.upselling_data.beverage', $lc->upselling_data['beverage'] ?? [])) !!};
        initUpselling('lunch', 'beverage', lcBev, 'JM');
        let lcVip = {!! json_encode(old('session.lunch.vip_remarks', $lc->vip_remarks ?? [])) !!};
        initVip('lunch', lcVip, 'JM');
        let lcStaff = {!! json_encode(old('session.lunch.staff_on_duty', $lc->staff_on_duty ?? [])) !!};
        initStaff('lunch', lcStaff, 'JM');

        // --- DINNER INIT ---
        let dnFood = {!! json_encode(old('session.dinner.upselling_data.food', $dn->upselling_data['food'] ?? [])) !!};
        initUpselling('dinner', 'food', dnFood, 'JM');
        let dnBev = {!! json_encode(old('session.dinner.upselling_data.beverage', $dn->upselling_data['beverage'] ?? [])) !!};
        initUpselling('dinner', 'beverage', dnBev, 'JM');
        let dnVip = {!! json_encode(old('session.dinner.vip_remarks', $dn->vip_remarks ?? [])) !!};
        initVip('dinner', dnVip, 'JM');
        let dnStaff = {!! json_encode(old('session.dinner.staff_on_duty', $dn->staff_on_duty ?? [])) !!};
        initStaff('dinner', dnStaff, 'JM');

    });
</script>
