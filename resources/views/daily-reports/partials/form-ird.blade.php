{{-- FORM KHUSUS IN ROOM DINING (IRD) --}}

@php
    // 1. Ambil Data Detail per Sesi (Ada 4 Sesi)
    $bf = $details['breakfast'] ?? null;
    $lc = $details['lunch'] ?? null;
    $dn = $details['dinner'] ?? null;
    $sp = $details['supper'] ?? null;

    // 2. Ambil Data Master
    $restoIrd = $restaurants->where('code', 'IRD')->first();
    $myStaffList = $restoIrd ? $restoIrd->users : [];

    // 3. Upselling Menu
    $myMenu = $restoIrd && isset($upsellingItems[$restoIrd->id]) ? $upsellingItems[$restoIrd->id] : collect([]);
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

        {{-- 1. COVER REPORT --}}
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
        <input type="hidden" name="session[breakfast][revenue_event]"
            value="{{ old('session.breakfast.revenue_event', isset($bf->revenue_event) ? $bf->revenue_event : 0) }}">
        <div class="row g-3">
            <div class="col-md-4"><label class="small">Food Revenue</label><input type="text"
                    class="form-control rupiah" name="session[breakfast][revenue_food]"
                    value="{{ old('session.breakfast.revenue_food', isset($bf->revenue_food) ? number_format($bf->revenue_food, 0, ',', '.') : '') }}"
                    placeholder="0" autocomplete="off">
            </div>
            <div class="col-md-4"><label class="small">Beverage Revenue</label><input type="text"
                    class="form-control rupiah" name="session[breakfast][revenue_beverage]"
                    value="{{ old('session.breakfast.revenue_beverage', isset($bf->revenue_beverage) ? number_format($bf->revenue_beverage, 0, ',', '.') : '') }}"
                    placeholder="0" autocomplete="off">
            </div>
            <div class="col-md-4"><label class="small">Others Revenue</label><input type="text"
                    class="form-control rupiah" name="session[breakfast][revenue_others]"
                    value="{{ old('session.breakfast.revenue_others', isset($bf->revenue_others) ? number_format($bf->revenue_others, 0, ',', '.') : '') }}"
                    placeholder="0" autocomplete="off">
            </div>
        </div>

        <hr>

        {{-- 3. UPSELLING & STAFF --}}
        <h6 class="fw-bold text-muted mt-3">3. Upselling & Staff</h6>
        <div class="row g-3">
            {{-- Food --}}
            <div class="col-md-6">
                <label class="small fw-bold">Upselling Menu (Food)</label>
                @php $bfFoodVal = old('session.breakfast.upselling_data.food', $bf->upselling_data['food'] ?? []); @endphp
                <input type="hidden" id="input-breakfast-food-IRD" name="session[breakfast][upselling_data][food]"
                    value="{{ is_array($bfFoodVal) ? json_encode($bfFoodVal) : $bfFoodVal }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="select-breakfast-food-IRD">
                        <option value="" selected>Select Food...</option>
                        @foreach ($foods as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                    <input type="number" class="form-control form-control-sm" id="pax-breakfast-food-IRD"
                        placeholder="Qty" style="max-width: 70px;">
                    <button class="btn btn-sm btn-dark" type="button"
                        onclick="addUpsellingItem('breakfast', 'food', 'IRD')"><i class="ti ti-plus"></i> Add</button>
                </div>
                <ul class="list-group small" id="list-breakfast-food-IRD"></ul>
            </div>
            {{-- Bev --}}
            <div class="col-md-6">
                <label class="small fw-bold">Beverage Upselling</label>
                @php $bfBevVal = old('session.breakfast.upselling_data.beverage', $bf->upselling_data['beverage'] ?? []); @endphp
                <input type="hidden" id="input-breakfast-beverage-IRD"
                    name="session[breakfast][upselling_data][beverage]"
                    value="{{ is_array($bfBevVal) ? json_encode($bfBevVal) : $bfBevVal }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="select-breakfast-beverage-IRD">
                        <option value="" selected>Select Drink...</option>
                        @foreach ($beverages as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                    <input type="number" class="form-control form-control-sm" id="pax-breakfast-beverage-IRD"
                        placeholder="Qty" style="max-width: 70px;">
                    <button class="btn btn-sm btn-dark" type="button"
                        onclick="addUpsellingItem('breakfast', 'beverage', 'IRD')"><i class="ti ti-plus"></i>
                        Add</button>
                </div>
                <ul class="list-group small" id="list-breakfast-beverage-IRD"></ul>
            </div>
            {{-- VIP Note --}}
            <div class="col-md-12 mt-3">
                <label class="small fw-bold">VIP Note / List</label>
                @php $bfVipVal = old('session.breakfast.vip_remarks', $bf->vip_remarks ?? []); @endphp
                <input type="hidden" id="input-vip-breakfast-IRD" name="session[breakfast][vip_remarks]"
                    value="{{ is_array($bfVipVal) ? json_encode($bfVipVal) : $bfVipVal }}">
                <div class="input-group mb-2">
                    <input type="text" class="form-control form-control-sm" id="vip-name-breakfast-IRD"
                        placeholder="Guest Name">
                    <input type="text" class="form-control form-control-sm" id="vip-pos-breakfast-IRD"
                        placeholder="Note/Position">
                    <button class="btn btn-sm btn-dark" type="button" onclick="addVipItem('breakfast', 'IRD')"><i
                            class="ti ti-plus"></i> Add</button>
                </div>
                <ul class="list-group small" id="list-vip-breakfast-IRD"></ul>
            </div>
            {{-- Remarks --}}
            <div class="col-md-12"><label class="small">Remarks</label><input type="text" class="form-control"
                    name="session[breakfast][remarks]"
                    value="{{ old('session.breakfast.remarks', $bf->remarks ?? '') }}"></div>
            {{-- Staff --}}
            <div class="col-md-12 mt-3">
                <label class="small fw-bold">Staff on Duty</label>
                @php $bfStaffVal = old('session.breakfast.staff_on_duty', $bf->staff_on_duty ?? []); @endphp
                <input type="hidden" id="input-staff-breakfast-IRD" name="session[breakfast][staff_on_duty]"
                    value="{{ is_array($bfStaffVal) ? json_encode($bfStaffVal) : $bfStaffVal }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="select-staff-breakfast-IRD">
                        <option value="" selected>Select Staff...</option>
                        @foreach ($myStaffList as $staff)
                            <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                        @endforeach
                    </select>
                    <button class="btn btn-sm btn-dark" type="button" onclick="addStaffItem('breakfast', 'IRD')"><i
                            class="ti ti-plus"></i> Add</button>
                </div>
                <div id="list-staff-breakfast-IRD" class="d-flex flex-wrap"></div>
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
        <input type="hidden" name="session[lunch][revenue_event]"
            value="{{ old('session.lunch.revenue_event', isset($lc->revenue_event) ? $lc->revenue_event : 0) }}">
        <div class="row g-3">
            <div class="col-md-4"><label class="small">Food Revenue</label><input type="text"
                    class="form-control rupiah" name="session[lunch][revenue_food]"
                    value="{{ old('session.lunch.revenue_food', isset($lc->revenue_food) ? number_format($lc->revenue_food, 0, ',', '.') : '') }}"
                    placeholder="0" autocomplete="off">
            </div>
            <div class="col-md-4"><label class="small">Beverage Revenue</label><input type="text"
                    class="form-control rupiah" name="session[lunch][revenue_beverage]"
                    value="{{ old('session.lunch.revenue_beverage', isset($lc->revenue_beverage) ? number_format($lc->revenue_beverage, 0, ',', '.') : '') }}"
                    placeholder="0" autocomplete="off">
            </div>
            <div class="col-md-4"><label class="small">Others Revenue</label><input type="text"
                    class="form-control rupiah" name="session[lunch][revenue_others]"
                    value="{{ old('session.lunch.revenue_others', isset($lc->revenue_others) ? number_format($lc->revenue_others, 0, ',', '.') : '') }}"
                    placeholder="0" autocomplete="off">
            </div>
        </div>

        <hr>

        <h6 class="fw-bold text-muted mt-3">3. Upselling & Staff</h6>
        <div class="row g-3">
            {{-- Food --}}
            <div class="col-md-6">
                <label class="small fw-bold">Upselling Menu (Food)</label>
                @php $lcFoodVal = old('session.lunch.upselling_data.food', $lc->upselling_data['food'] ?? []); @endphp
                <input type="hidden" id="input-lunch-food-IRD" name="session[lunch][upselling_data][food]"
                    value="{{ is_array($lcFoodVal) ? json_encode($lcFoodVal) : $lcFoodVal }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="select-lunch-food-IRD">
                        <option value="" selected>Select Food...</option>
                        @foreach ($foods as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                    <input type="number" class="form-control form-control-sm" id="pax-lunch-food-IRD"
                        placeholder="Qty" style="max-width: 70px;">
                    <button class="btn btn-sm btn-dark" type="button"
                        onclick="addUpsellingItem('lunch', 'food', 'IRD')"><i class="ti ti-plus"></i> Add</button>
                </div>
                <ul class="list-group small" id="list-lunch-food-IRD"></ul>
            </div>
            {{-- Bev --}}
            <div class="col-md-6">
                <label class="small fw-bold">Beverage Upselling</label>
                @php $lcBevVal = old('session.lunch.upselling_data.beverage', $lc->upselling_data['beverage'] ?? []); @endphp
                <input type="hidden" id="input-lunch-beverage-IRD" name="session[lunch][upselling_data][beverage]"
                    value="{{ is_array($lcBevVal) ? json_encode($lcBevVal) : $lcBevVal }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="select-lunch-beverage-IRD">
                        <option value="" selected>Select Drink...</option>
                        @foreach ($beverages as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                    <input type="number" class="form-control form-control-sm" id="pax-lunch-beverage-IRD"
                        placeholder="Qty" style="max-width: 70px;">
                    <button class="btn btn-sm btn-dark" type="button"
                        onclick="addUpsellingItem('lunch', 'beverage', 'IRD')"><i class="ti ti-plus"></i> Add</button>
                </div>
                <ul class="list-group small" id="list-lunch-beverage-IRD"></ul>
            </div>
            {{-- VIP Note --}}
            <div class="col-md-12 mt-3">
                <label class="small fw-bold">VIP Note / List</label>
                @php $lcVipVal = old('session.lunch.vip_remarks', $lc->vip_remarks ?? []); @endphp
                <input type="hidden" id="input-vip-lunch-IRD" name="session[lunch][vip_remarks]"
                    value="{{ is_array($lcVipVal) ? json_encode($lcVipVal) : $lcVipVal }}">
                <div class="input-group mb-2">
                    <input type="text" class="form-control form-control-sm" id="vip-name-lunch-IRD"
                        placeholder="Guest Name">
                    <input type="text" class="form-control form-control-sm" id="vip-pos-lunch-IRD"
                        placeholder="Note/Position">
                    <button class="btn btn-sm btn-dark" type="button" onclick="addVipItem('lunch', 'IRD')"><i
                            class="ti ti-plus"></i> Add</button>
                </div>
                <ul class="list-group small" id="list-vip-lunch-IRD"></ul>
            </div>
            {{-- Remarks --}}
            <div class="col-md-12"><label class="small">Remarks</label><input type="text" class="form-control"
                    name="session[lunch][remarks]" value="{{ old('session.lunch.remarks', $lc->remarks ?? '') }}">
            </div>
            {{-- Staff --}}
            <div class="col-md-12 mt-3">
                <label class="small fw-bold">Staff on Duty</label>
                @php $lcStaffVal = old('session.lunch.staff_on_duty', $lc->staff_on_duty ?? []); @endphp
                <input type="hidden" id="input-staff-lunch-IRD" name="session[lunch][staff_on_duty]"
                    value="{{ is_array($lcStaffVal) ? json_encode($lcStaffVal) : $lcStaffVal }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="select-staff-lunch-IRD">
                        <option value="" selected>Select Staff...</option>
                        @foreach ($myStaffList as $staff)
                            <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                        @endforeach
                    </select>
                    <button class="btn btn-sm btn-dark" type="button" onclick="addStaffItem('lunch', 'IRD')"><i
                            class="ti ti-plus"></i> Add</button>
                </div>
                <div id="list-staff-lunch-IRD" class="d-flex flex-wrap"></div>
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
        <input type="hidden" name="session[dinner][revenue_event]"
            value="{{ old('session.dinner.revenue_event', isset($dn->revenue_event) ? $dn->revenue_event : 0) }}">
        <div class="row g-3">
            <div class="col-md-4"><label class="small">Food Revenue</label><input type="text"
                    class="form-control rupiah" name="session[dinner][revenue_food]"
                    value="{{ old('session.dinner.revenue_food', isset($dn->revenue_food) ? number_format($dn->revenue_food, 0, ',', '.') : '') }}"
                    placeholder="0" autocomplete="off">
            </div>
            <div class="col-md-4"><label class="small">Beverage Revenue</label><input type="text"
                    class="form-control rupiah" name="session[dinner][revenue_beverage]"
                    value="{{ old('session.dinner.revenue_beverage', isset($dn->revenue_beverage) ? number_format($dn->revenue_beverage, 0, ',', '.') : '') }}"
                    placeholder="0" autocomplete="off">
            </div>
            <div class="col-md-4"><label class="small">Others Revenue</label><input type="text"
                    class="form-control rupiah" name="session[dinner][revenue_others]"
                    value="{{ old('session.dinner.revenue_others', isset($dn->revenue_others) ? number_format($dn->revenue_others, 0, ',', '.') : '') }}"
                    placeholder="0" autocomplete="off">
            </div>
        </div>

        <hr>

        <h6 class="fw-bold text-muted mt-3">3. Upselling & Staff</h6>
        <div class="row g-3">
            {{-- Food --}}
            <div class="col-md-6">
                <label class="small fw-bold">Upselling Menu (Food)</label>
                @php $dnFoodVal = old('session.dinner.upselling_data.food', $dn->upselling_data['food'] ?? []); @endphp
                <input type="hidden" id="input-dinner-food-IRD" name="session[dinner][upselling_data][food]"
                    value="{{ is_array($dnFoodVal) ? json_encode($dnFoodVal) : $dnFoodVal }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="select-dinner-food-IRD">
                        <option value="" selected>Select Food...</option>
                        @foreach ($foods as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                    <input type="number" class="form-control form-control-sm" id="pax-dinner-food-IRD"
                        placeholder="Qty" style="max-width: 70px;">
                    <button class="btn btn-sm btn-dark" type="button"
                        onclick="addUpsellingItem('dinner', 'food', 'IRD')"><i class="ti ti-plus"></i> Add</button>
                </div>
                <ul class="list-group small" id="list-dinner-food-IRD"></ul>
            </div>
            {{-- Bev --}}
            <div class="col-md-6">
                <label class="small fw-bold">Beverage Upselling</label>
                @php $dnBevVal = old('session.dinner.upselling_data.beverage', $dn->upselling_data['beverage'] ?? []); @endphp
                <input type="hidden" id="input-dinner-beverage-IRD" name="session[dinner][upselling_data][beverage]"
                    value="{{ is_array($dnBevVal) ? json_encode($dnBevVal) : $dnBevVal }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="select-dinner-beverage-IRD">
                        <option value="" selected>Select Drink...</option>
                        @foreach ($beverages as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                    <input type="number" class="form-control form-control-sm" id="pax-dinner-beverage-IRD"
                        placeholder="Qty" style="max-width: 70px;">
                    <button class="btn btn-sm btn-dark" type="button"
                        onclick="addUpsellingItem('dinner', 'beverage', 'IRD')"><i class="ti ti-plus"></i>
                        Add</button>
                </div>
                <ul class="list-group small" id="list-dinner-beverage-IRD"></ul>
            </div>
            {{-- VIP Note --}}
            <div class="col-md-12 mt-3">
                <label class="small fw-bold">VIP Note / List</label>
                @php $dnVipVal = old('session.dinner.vip_remarks', $dn->vip_remarks ?? []); @endphp
                <input type="hidden" id="input-vip-dinner-IRD" name="session[dinner][vip_remarks]"
                    value="{{ is_array($dnVipVal) ? json_encode($dnVipVal) : $dnVipVal }}">
                <div class="input-group mb-2">
                    <input type="text" class="form-control form-control-sm" id="vip-name-dinner-IRD"
                        placeholder="Guest Name">
                    <input type="text" class="form-control form-control-sm" id="vip-pos-dinner-IRD"
                        placeholder="Note/Position">
                    <button class="btn btn-sm btn-dark" type="button" onclick="addVipItem('dinner', 'IRD')"><i
                            class="ti ti-plus"></i> Add</button>
                </div>
                <ul class="list-group small" id="list-vip-dinner-IRD"></ul>
            </div>
            {{-- Remarks --}}
            <div class="col-md-12"><label class="small">Remarks</label><input type="text" class="form-control"
                    name="session[dinner][remarks]" value="{{ old('session.dinner.remarks', $dn->remarks ?? '') }}">
            </div>
            {{-- Staff --}}
            <div class="col-md-12 mt-3">
                <label class="small fw-bold">Staff on Duty</label>
                @php $dnStaffVal = old('session.dinner.staff_on_duty', $dn->staff_on_duty ?? []); @endphp
                <input type="hidden" id="input-staff-dinner-IRD" name="session[dinner][staff_on_duty]"
                    value="{{ is_array($dnStaffVal) ? json_encode($dnStaffVal) : $dnStaffVal }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="select-staff-dinner-IRD">
                        <option value="" selected>Select Staff...</option>
                        @foreach ($myStaffList as $staff)
                            <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                        @endforeach
                    </select>
                    <button class="btn btn-sm btn-dark" type="button" onclick="addStaffItem('dinner', 'IRD')"><i
                            class="ti ti-plus"></i> Add</button>
                </div>
                <div id="list-staff-dinner-IRD" class="d-flex flex-wrap"></div>
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
{{-- SESSION: SUPPER --}}
{{-- ============================================================ --}}
<div class="card mb-4">
    <div class="card-header bg-secondary text-white">
        <h5 class="mb-0"><i class="ti ti-moon-stars"></i> Supper Report</h5>
    </div>
    <div class="card-body">

        <h6 class="fw-bold text-muted mt-3">1. Cover Report</h6>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label small fw-bold">Total Actual Cover</label>
                <input type="number" class="form-control" name="session[supper][cover_data][total_actual_cover]"
                    value="{{ old('session.supper.cover_data.total_actual_cover', $sp->cover_data['total_actual_cover'] ?? '') }}"
                    placeholder="0">
            </div>
        </div>

        <hr>

        <h6 class="fw-bold text-muted mt-3">2. Revenue Report (IDR)</h6>
        <input type="hidden" name="session[supper][revenue_event]"
            value="{{ old('session.supper.revenue_event', isset($sp->revenue_event) ? $sp->revenue_event : 0) }}">
        <div class="row g-3">
            <div class="col-md-4"><label class="small">Food Revenue</label><input type="text"
                    class="form-control rupiah" name="session[supper][revenue_food]"
                    value="{{ old('session.supper.revenue_food', isset($sp->revenue_food) ? number_format($sp->revenue_food, 0, ',', '.') : '') }}"
                    placeholder="0" autocomplete="off">
            </div>
            <div class="col-md-4"><label class="small">Beverage Revenue</label><input type="text"
                    class="form-control rupiah" name="session[supper][revenue_beverage]"
                    value="{{ old('session.supper.revenue_beverage', isset($sp->revenue_beverage) ? number_format($sp->revenue_beverage, 0, ',', '.') : '') }}"
                    placeholder="0" autocomplete="off">
            </div>
            <div class="col-md-4"><label class="small">Others Revenue</label><input type="text"
                    class="form-control rupiah" name="session[supper][revenue_others]"
                    value="{{ old('session.supper.revenue_others', isset($sp->revenue_others) ? number_format($sp->revenue_others, 0, ',', '.') : '') }}"
                    placeholder="0" autocomplete="off">
            </div>
        </div>

        <hr>

        <h6 class="fw-bold text-muted mt-3">3. Upselling & Staff</h6>
        <div class="row g-3">
            {{-- Food --}}
            <div class="col-md-6">
                <label class="small fw-bold">Upselling Menu (Food)</label>
                @php $spFoodVal = old('session.supper.upselling_data.food', $sp->upselling_data['food'] ?? []); @endphp
                <input type="hidden" id="input-supper-food-IRD" name="session[supper][upselling_data][food]"
                    value="{{ is_array($spFoodVal) ? json_encode($spFoodVal) : $spFoodVal }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="select-supper-food-IRD">
                        <option value="" selected>Select Food...</option>
                        @foreach ($foods as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                    <input type="number" class="form-control form-control-sm" id="pax-supper-food-IRD"
                        placeholder="Qty" style="max-width: 70px;">
                    <button class="btn btn-sm btn-dark" type="button"
                        onclick="addUpsellingItem('supper', 'food', 'IRD')"><i class="ti ti-plus"></i> Add</button>
                </div>
                <ul class="list-group small" id="list-supper-food-IRD"></ul>
            </div>
            {{-- Bev --}}
            <div class="col-md-6">
                <label class="small fw-bold">Beverage Upselling</label>
                @php $spBevVal = old('session.supper.upselling_data.beverage', $sp->upselling_data['beverage'] ?? []); @endphp
                <input type="hidden" id="input-supper-beverage-IRD" name="session[supper][upselling_data][beverage]"
                    value="{{ is_array($spBevVal) ? json_encode($spBevVal) : $spBevVal }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="select-supper-beverage-IRD">
                        <option value="" selected>Select Drink...</option>
                        @foreach ($beverages as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                    <input type="number" class="form-control form-control-sm" id="pax-supper-beverage-IRD"
                        placeholder="Qty" style="max-width: 70px;">
                    <button class="btn btn-sm btn-dark" type="button"
                        onclick="addUpsellingItem('supper', 'beverage', 'IRD')"><i class="ti ti-plus"></i>
                        Add</button>
                </div>
                <ul class="list-group small" id="list-supper-beverage-IRD"></ul>
            </div>
            {{-- VIP Note --}}
            <div class="col-md-12 mt-3">
                <label class="small fw-bold">VIP Note / List</label>
                @php $spVipVal = old('session.supper.vip_remarks', $sp->vip_remarks ?? []); @endphp
                <input type="hidden" id="input-vip-supper-IRD" name="session[supper][vip_remarks]"
                    value="{{ is_array($spVipVal) ? json_encode($spVipVal) : $spVipVal }}">
                <div class="input-group mb-2">
                    <input type="text" class="form-control form-control-sm" id="vip-name-supper-IRD"
                        placeholder="Guest Name">
                    <input type="text" class="form-control form-control-sm" id="vip-pos-supper-IRD"
                        placeholder="Note/Position">
                    <button class="btn btn-sm btn-dark" type="button" onclick="addVipItem('supper', 'IRD')"><i
                            class="ti ti-plus"></i> Add</button>
                </div>
                <ul class="list-group small" id="list-vip-supper-IRD"></ul>
            </div>
            {{-- Remarks --}}
            <div class="col-md-12"><label class="small">Remarks</label><input type="text" class="form-control"
                    name="session[supper][remarks]" value="{{ old('session.supper.remarks', $sp->remarks ?? '') }}">
            </div>
            {{-- Staff --}}
            <div class="col-md-12 mt-3">
                <label class="small fw-bold">Staff on Duty</label>
                @php $spStaffVal = old('session.supper.staff_on_duty', $sp->staff_on_duty ?? []); @endphp
                <input type="hidden" id="input-staff-supper-IRD" name="session[supper][staff_on_duty]"
                    value="{{ is_array($spStaffVal) ? json_encode($spStaffVal) : $spStaffVal }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="select-staff-supper-IRD">
                        <option value="" selected>Select Staff...</option>
                        @foreach ($myStaffList as $staff)
                            <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                        @endforeach
                    </select>
                    <button class="btn btn-sm btn-dark" type="button" onclick="addStaffItem('supper', 'IRD')"><i
                            class="ti ti-plus"></i> Add</button>
                </div>
                <div id="list-staff-supper-IRD" class="d-flex flex-wrap"></div>
            </div>
        </div>
        <hr>
        <h6 class="fw-bold text-muted mt-3">4. Competitor Comparison</h6>
        <div class="row g-3">
            <div class="col-md-4"><label class="small">Shangri-La</label><input type="number" class="form-control"
                    name="session[supper][competitor_data][shangrila_cover]"
                    value="{{ old('session.supper.competitor_data.shangrila_cover', $sp->competitor_data['shangrila_cover'] ?? '') }}">
            </div>
            <div class="col-md-4"><label class="small">JW Marriott</label><input type="number"
                    class="form-control" name="session[supper][competitor_data][jw_marriott_cover]"
                    value="{{ old('session.supper.competitor_data.jw_marriott_cover', $sp->competitor_data['jw_marriott_cover'] ?? '') }}">
            </div>
            <div class="col-md-4"><label class="small">Sheraton</label><input type="number" class="form-control"
                    name="session[supper][competitor_data][sheraton_cover]"
                    value="{{ old('session.supper.competitor_data.sheraton_cover', $sp->competitor_data['sheraton_cover'] ?? '') }}">
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
        initUpselling('breakfast', 'food', bfFood, 'IRD');
        let bfBev = {!! json_encode(old('session.breakfast.upselling_data.beverage', $bf->upselling_data['beverage'] ?? [])) !!};
        initUpselling('breakfast', 'beverage', bfBev, 'IRD');
        let bfVip = {!! json_encode(old('session.breakfast.vip_remarks', $bf->vip_remarks ?? [])) !!};
        initVip('breakfast', bfVip, 'IRD');
        let bfStaff = {!! json_encode(old('session.breakfast.staff_on_duty', $bf->staff_on_duty ?? [])) !!};
        initStaff('breakfast', bfStaff, 'IRD');

        // --- LUNCH INIT ---
        let lcFood = {!! json_encode(old('session.lunch.upselling_data.food', $lc->upselling_data['food'] ?? [])) !!};
        initUpselling('lunch', 'food', lcFood, 'IRD');
        let lcBev = {!! json_encode(old('session.lunch.upselling_data.beverage', $lc->upselling_data['beverage'] ?? [])) !!};
        initUpselling('lunch', 'beverage', lcBev, 'IRD');
        let lcVip = {!! json_encode(old('session.lunch.vip_remarks', $lc->vip_remarks ?? [])) !!};
        initVip('lunch', lcVip, 'IRD');
        let lcStaff = {!! json_encode(old('session.lunch.staff_on_duty', $lc->staff_on_duty ?? [])) !!};
        initStaff('lunch', lcStaff, 'IRD');

        // --- DINNER INIT ---
        let dnFood = {!! json_encode(old('session.dinner.upselling_data.food', $dn->upselling_data['food'] ?? [])) !!};
        initUpselling('dinner', 'food', dnFood, 'IRD');
        let dnBev = {!! json_encode(old('session.dinner.upselling_data.beverage', $dn->upselling_data['beverage'] ?? [])) !!};
        initUpselling('dinner', 'beverage', dnBev, 'IRD');
        let dnVip = {!! json_encode(old('session.dinner.vip_remarks', $dn->vip_remarks ?? [])) !!};
        initVip('dinner', dnVip, 'IRD');
        let dnStaff = {!! json_encode(old('session.dinner.staff_on_duty', $dn->staff_on_duty ?? [])) !!};
        initStaff('dinner', dnStaff, 'IRD');

        // --- SUPPER INIT ---
        let spFood = {!! json_encode(old('session.supper.upselling_data.food', $sp->upselling_data['food'] ?? [])) !!};
        initUpselling('supper', 'food', spFood, 'IRD');
        let spBev = {!! json_encode(old('session.supper.upselling_data.beverage', $sp->upselling_data['beverage'] ?? [])) !!};
        initUpselling('supper', 'beverage', spBev, 'IRD');
        let spVip = {!! json_encode(old('session.supper.vip_remarks', $sp->vip_remarks ?? [])) !!};
        initVip('supper', spVip, 'IRD');
        let spStaff = {!! json_encode(old('session.supper.staff_on_duty', $sp->staff_on_duty ?? [])) !!};
        initStaff('supper', spStaff, 'IRD');

    });
</script>
