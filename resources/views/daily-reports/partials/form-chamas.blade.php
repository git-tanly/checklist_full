{{-- FORM KHUSUS CHAMAS (CMS) --}}

@php
    // 1. Ambil Data Detail per Sesi (Hanya Lunch & Dinner)
    $lc = $details['lunch'] ?? null;
    $dn = $details['dinner'] ?? null;

    // 2. Ambil Data Master (Staff & Menu) khusus Chamas
    $restoCms = $restaurants->where('code', 'CHA')->first();

    // A. Staff List
    $myStaffList = $restoCms ? $restoCms->users : [];

    // B. Upselling Menu
    $myMenu = $restoCms && isset($upsellingItems[$restoCms->id]) ? $upsellingItems[$restoCms->id] : collect([]);
    $foods = $myMenu->where('type', 'food');
    $beverages = $myMenu->where('type', 'beverage');

    // C. Occasion Items (format baru)
    $lcOccasionData = old('session.lunch.additional_data.occasion_items', $lc->additional_data['occasion_items'] ?? []);
    $lcOccasionValue = is_array($lcOccasionData) ? json_encode($lcOccasionData) : $lcOccasionData;
    $dnOccasionData = old('session.dinner.additional_data.occasion_items', $dn->additional_data['occasion_items'] ?? []);
    $dnOccasionValue = is_array($dnOccasionData) ? json_encode($dnOccasionData) : $dnOccasionData;

    // D. Promo Items (format baru)
    $lcPromoData = old('session.lunch.additional_data.promo_items', $lc->additional_data['promo_items'] ?? []);
    $lcPromoValue = is_array($lcPromoData) ? json_encode($lcPromoData) : $lcPromoData;
    $dnPromoData = old('session.dinner.additional_data.promo_items', $dn->additional_data['promo_items'] ?? []);
    $dnPromoValue = is_array($dnPromoData) ? json_encode($dnPromoData) : $dnPromoData;
@endphp

{{-- ============================================================ --}}
{{-- SESSION: LUNCH --}}
{{-- ============================================================ --}}
<div class="card mb-4">
    <div class="card-header bg-light-primary">
        <h5 class="mb-0 text-primary"><i class="ti ti-soup"></i> Lunch Report</h5>
    </div>
    <div class="card-body">

        {{-- 1. COVER REPORT --}}
        <h6 class="fw-bold text-muted mt-3">1. Cover Report</h6>
        <div class="row g-3">
            <div class="col-md-4"><label class="form-label small">In-House (Adult)</label><input type="number"
                    class="form-control" name="session[lunch][cover_data][in_house_adult]"
                    value="{{ old('session.lunch.cover_data.in_house_adult', $lc->cover_data['in_house_adult'] ?? '') }}"
                    placeholder="0">
            </div>
            <div class="col-md-4"><label class="form-label small">In-House (Child)</label><input type="number"
                    class="form-control" name="session[lunch][cover_data][in_house_child]"
                    value="{{ old('session.lunch.cover_data.in_house_child', $lc->cover_data['in_house_child'] ?? '') }}"
                    placeholder="0">
            </div>

            <div class="col-md-4"><label class="form-label small">Walk-In (Adult)</label><input type="number"
                    class="form-control" name="session[lunch][cover_data][walk_in_adult]"
                    value="{{ old('session.lunch.cover_data.walk_in_adult', $lc->cover_data['walk_in_adult'] ?? '') }}"
                    placeholder="0">
            </div>
            <div class="col-md-4"><label class="form-label small">Walk-In (Child)</label><input type="number"
                    class="form-control" name="session[lunch][cover_data][walk_in_child]"
                    value="{{ old('session.lunch.cover_data.walk_in_child', $lc->cover_data['walk_in_child'] ?? '') }}"
                    placeholder="0">
            </div>

            <!-- <div class="col-md-4"><label class="form-label small">Event (Adult)</label><input type="number"
                    class="form-control" name="session[lunch][cover_data][event_adult]"
                    value="{{ old('session.lunch.cover_data.event_adult', $lc->cover_data['event_adult'] ?? '') }}"
                    placeholder="0">
            </div>
            <div class="col-md-4"><label class="form-label small">Event (Child)</label><input type="number"
                    class="form-control" name="session[lunch][cover_data][event_child]"
                    value="{{ old('session.lunch.cover_data.event_child', $lc->cover_data['event_child'] ?? '') }}"
                    placeholder="0">
            </div> -->
        </div>

        <hr>

        {{-- 2. OCCASION / EVENT TYPE --}}
        <h6 class="fw-bold text-muted mt-3">2. Occasion / Event Type</h6>
        <div class="row g-3">
            <div class="col-md-12">
                <input type="hidden" id="input-occasion-lunch-CHA" name="session[lunch][additional_data][occasion_items]" value="{{ $lcOccasionValue ?? '[]' }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="occasion-type-lunch-CHA" onchange="toggleOccasionOther('lunch', 'CHA')">
                        <option value="" selected>Select Occasion...</option>
                        <option value="Wedding Party">Wedding Party</option>
                        <option value="Birthday Party">Birthday Party</option>
                        <option value="Social Event">Social Event</option>
                        <option value="Corporate Event">Corporate Event</option>
                        <option value="Other">Other</option>
                    </select>
                    <input type="text" class="form-control form-control-sm d-none" id="occasion-other-lunch-CHA" placeholder="Occasion Type" style="max-width: 150px;">
                    <input type="text" class="form-control form-control-sm" id="occasion-name-lunch-CHA"
                        placeholder="Name (e.g. Mr. Budi)">
                    <input type="number" class="form-control form-control-sm" id="occasion-pax-lunch-CHA"
                        placeholder="Pax" style="max-width: 80px;">
                    <input type="text" class="form-control form-control-sm rupiah" id="occasion-revenue-lunch-CHA"
                        placeholder="Revenue" style="max-width: 120px;" autocomplete="off">
                    <button class="btn btn-sm btn-dark" type="button" onclick="addOccasionItem('lunch', 'CHA')">
                        <i class="ti ti-plus"></i> Add
                    </button>
                </div>
                <ul class="list-group small" id="list-occasion-lunch-CHA"></ul>
            </div>
        </div>

        <hr>

        {{-- 3. PROMO --}}
        <h6 class="fw-bold text-muted mt-3">3. Promo</h6>
        <div class="row g-3">
            <div class="col-md-12">
                <input type="hidden" id="input-promo-lunch-CHA" name="session[lunch][additional_data][promo_items]" value="{{ $lcPromoValue ?? '[]' }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="promo-type-lunch-CHA" onchange="togglePromoOther('lunch', 'CHA')">
                        <option value="" selected>Select Promo...</option>
                        <option value="Mandiri Card">Mandiri Card</option>
                        <option value="BCA Card">BCA Card</option>
                        <option value="Membership">Membership</option>
                        <option value="Other">Other</option>
                    </select>
                    <input type="text" class="form-control form-control-sm d-none" id="promo-other-lunch-CHA" placeholder="Promo Name" style="max-width: 150px;">
                    <input type="number" class="form-control form-control-sm" id="promo-pax-lunch-CHA"
                        placeholder="Pax" style="max-width: 80px;">
                    <input type="text" class="form-control form-control-sm rupiah" id="promo-revenue-lunch-CHA"
                        placeholder="Revenue" style="max-width: 120px;" autocomplete="off">
                    <button class="btn btn-sm btn-dark" type="button" onclick="addPromoItem('lunch', 'CHA')">
                        <i class="ti ti-plus"></i> Add
                    </button>
                </div>
                <ul class="list-group small" id="list-promo-lunch-CHA"></ul>
            </div>
        </div>

        <hr>

        {{-- 4. PACKAGE REPORT (KHUSUS CHAMAS) --}}
        {{-- Kita simpan di kolom 'additional_data' --}}
        <h6 class="fw-bold text-muted mt-3">4. Package Report (Total Qty)</h6>
        <div class="p-3 border rounded mb-3 bg-light">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-danger">Supreme Package</label>
                    <input type="number" class="form-control" name="session[lunch][additional_data][package_supreme]"
                        value="{{ old('session.lunch.additional_data.package_supreme', $lc->additional_data['package_supreme'] ?? '') }}"
                        placeholder="0">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-warning">Premium Package</label>
                    <input type="number" class="form-control" name="session[lunch][additional_data][package_premium]"
                        value="{{ old('session.lunch.additional_data.package_premium', $lc->additional_data['package_premium'] ?? '') }}"
                        placeholder="0">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-primary">Reguler Package</label>
                    <input type="number" class="form-control" name="session[lunch][additional_data][package_reguler]"
                        value="{{ old('session.lunch.additional_data.package_reguler', $lc->additional_data['package_reguler'] ?? '') }}"
                        placeholder="0">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-success">Promo Package</label>
                    <input type="number" class="form-control" name="session[lunch][additional_data][package_promo]"
                        value="{{ old('session.lunch.additional_data.package_promo', $lc->additional_data['package_promo'] ?? '') }}"
                        placeholder="0">
                </div>
            </div>
        </div>

        <hr>

        {{-- 3. REVENUE REPORT --}}
        <h6 class="fw-bold text-muted mt-3">5. Revenue Report (IDR)</h6>
        <div class="row g-3">
            <div class="col-md-3"><label class="small">Food</label><input type="text" class="form-control rupiah"
                    name="session[lunch][revenue_food]"
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

        {{-- 6. UPSELLING & REMARKS --}}
        <h6 class="fw-bold text-muted mt-3">6. Upselling & Remarks</h6>
        <div class="row g-3">
            {{-- Food --}}
            <div class="col-md-6">
                <label class="form-label small fw-bold">Upselling Menu (Food)</label>
                @php $lcFoodVal = old('session.lunch.upselling_data.food', $lc->upselling_data['food'] ?? []); @endphp
                <input type="hidden" id="input-lunch-food-CHA" name="session[lunch][upselling_data][food]"
                    value="{{ is_array($lcFoodVal) ? json_encode($lcFoodVal) : $lcFoodVal }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="select-lunch-food-CHA">
                        <option value="" selected>Select Food...</option>
                        @foreach ($foods as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                    <input type="number" class="form-control form-control-sm" id="pax-lunch-food-CHA"
                        placeholder="Qty" style="max-width: 70px;">
                    <button class="btn btn-sm btn-dark" type="button"
                        onclick="addUpsellingItem('lunch', 'food', 'CHA')"><i class="ti ti-plus"></i> Add</button>
                </div>
                <ul class="list-group small" id="list-lunch-food-CHA"></ul>
            </div>
            {{-- Bev --}}
            <div class="col-md-6">
                <label class="form-label small fw-bold">Beverage Upselling</label>
                @php $lcBevVal = old('session.lunch.upselling_data.beverage', $lc->upselling_data['beverage'] ?? []); @endphp
                <input type="hidden" id="input-lunch-beverage-CHA" name="session[lunch][upselling_data][beverage]"
                    value="{{ is_array($lcBevVal) ? json_encode($lcBevVal) : $lcBevVal }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="select-lunch-beverage-CHA">
                        <option value="" selected>Select Drink...</option>
                        @foreach ($beverages as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                    <input type="number" class="form-control form-control-sm" id="pax-lunch-beverage-CHA"
                        placeholder="Qty" style="max-width: 70px;">
                    <button class="btn btn-sm btn-dark" type="button"
                        onclick="addUpsellingItem('lunch', 'beverage', 'CHA')"><i class="ti ti-plus"></i> Add</button>
                </div>
                <ul class="list-group small" id="list-lunch-beverage-CHA"></ul>
            </div>
            {{-- Remarks --}}
            <div class="col-md-12">
                <label class="form-label small">General Remarks</label>
                <textarea class="form-control" name="session[lunch][remarks]">{{ old('session.lunch.remarks', $lc->remarks ?? '') }}</textarea>
            </div>
            {{-- Staff --}}
            <div class="col-md-12 mt-3">
                <label class="form-label small fw-bold">Staff on Duty</label>
                @php $lcStaffVal = old('session.lunch.staff_on_duty', $lc->staff_on_duty ?? []); @endphp
                <input type="hidden" id="input-staff-lunch-CHA" name="session[lunch][staff_on_duty]"
                    value="{{ is_array($lcStaffVal) ? json_encode($lcStaffVal) : $lcStaffVal }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="select-staff-lunch-CHA">
                        <option value="" selected>Select Staff...</option>
                        @foreach ($myStaffList as $staff)
                            <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                        @endforeach
                    </select>
                    <button class="btn btn-sm btn-dark" type="button" onclick="addStaffItem('lunch', 'CHA')"><i
                            class="ti ti-plus"></i> Add</button>
                </div>
                <div id="list-staff-lunch-CHA" class="d-flex flex-wrap"></div>
            </div>
        </div>

        <hr>

        {{-- 7. COMPETITOR --}}
        <h6 class="fw-bold text-muted mt-3">7. Competitor Comparison</h6>
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
            <div class="col-md-4"><label class="form-label small">In-House (Adult)</label><input type="number"
                    class="form-control" name="session[dinner][cover_data][in_house_adult]"
                    value="{{ old('session.dinner.cover_data.in_house_adult', $dn->cover_data['in_house_adult'] ?? '') }}"
                    placeholder="0">
            </div>
            <div class="col-md-4"><label class="form-label small">In-House (Child)</label><input type="number"
                    class="form-control" name="session[dinner][cover_data][in_house_child]"
                    value="{{ old('session.dinner.cover_data.in_house_child', $dn->cover_data['in_house_child'] ?? '') }}"
                    placeholder="0">
            </div>
            <div class="col-md-4"><label class="form-label small">Walk-In (Adult)</label><input type="number"
                    class="form-control" name="session[dinner][cover_data][walk_in_adult]"
                    value="{{ old('session.dinner.cover_data.walk_in_adult', $dn->cover_data['walk_in_adult'] ?? '') }}"
                    placeholder="0">
            </div>
            <div class="col-md-4"><label class="form-label small">Walk-In (Child)</label><input type="number"
                    class="form-control" name="session[dinner][cover_data][walk_in_child]"
                    value="{{ old('session.dinner.cover_data.walk_in_child', $dn->cover_data['walk_in_child'] ?? '') }}"
                    placeholder="0">
            </div>
            <!-- <div class="col-md-4"><label class="form-label small">Event (Adult)</label><input type="number"
                    class="form-control" name="session[dinner][cover_data][event_adult]"
                    value="{{ old('session.dinner.cover_data.event_adult', $dn->cover_data['event_adult'] ?? '') }}"
                    placeholder="0">
            </div>
            <div class="col-md-4"><label class="form-label small">Event (Child)</label><input type="number"
                    class="form-control" name="session[dinner][cover_data][event_child]"
                    value="{{ old('session.dinner.cover_data.event_child', $dn->cover_data['event_child'] ?? '') }}"
                    placeholder="0">
            </div> -->
        </div>

        <hr>

        <h6 class="fw-bold text-muted mt-3">2. Occasion / Event Type</h6>
        <div class="row g-3">
            <div class="col-md-12">
                <input type="hidden" id="input-occasion-dinner-CHA" name="session[dinner][additional_data][occasion_items]" value="{{ $dnOccasionValue ?? '[]' }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="occasion-type-dinner-CHA" onchange="toggleOccasionOther('dinner', 'CHA')">
                        <option value="" selected>Select Occasion...</option>
                        <option value="Wedding Party">Wedding Party</option>
                        <option value="Birthday Party">Birthday Party</option>
                        <option value="Social Event">Social Event</option>
                        <option value="Corporate Event">Corporate Event</option>
                        <option value="Other">Other</option>
                    </select>
                    <input type="text" class="form-control form-control-sm d-none" id="occasion-other-dinner-CHA" placeholder="Occasion Type" style="max-width: 150px;">
                    <input type="text" class="form-control form-control-sm" id="occasion-name-dinner-CHA"
                        placeholder="Name (e.g. Mr. Budi)">
                    <input type="number" class="form-control form-control-sm" id="occasion-pax-dinner-CHA"
                        placeholder="Pax" style="max-width: 80px;">
                    <input type="text" class="form-control form-control-sm rupiah" id="occasion-revenue-dinner-CHA"
                        placeholder="Revenue" style="max-width: 120px;" autocomplete="off">
                    <button class="btn btn-sm btn-dark" type="button" onclick="addOccasionItem('dinner', 'CHA')">
                        <i class="ti ti-plus"></i> Add
                    </button>
                </div>
                <ul class="list-group small" id="list-occasion-dinner-CHA"></ul>
            </div>
        </div>

        <hr>

        <h6 class="fw-bold text-muted mt-3">3. Promo</h6>
        <div class="row g-3">
            <div class="col-md-12">
                <input type="hidden" id="input-promo-dinner-CHA" name="session[dinner][additional_data][promo_items]" value="{{ $dnPromoValue ?? '[]' }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="promo-type-dinner-CHA" onchange="togglePromoOther('dinner', 'CHA')">
                        <option value="" selected>Select Promo...</option>
                        <option value="Mandiri Card">Mandiri Card</option>
                        <option value="BCA Card">BCA Card</option>
                        <option value="Membership">Membership</option>
                        <option value="Other">Other</option>
                    </select>
                    <input type="text" class="form-control form-control-sm d-none" id="promo-other-dinner-CHA" placeholder="Promo Name" style="max-width: 150px;">
                    <input type="number" class="form-control form-control-sm" id="promo-pax-dinner-CHA"
                        placeholder="Pax" style="max-width: 80px;">
                    <input type="text" class="form-control form-control-sm rupiah" id="promo-revenue-dinner-CHA"
                        placeholder="Revenue" style="max-width: 120px;" autocomplete="off">
                    <button class="btn btn-sm btn-dark" type="button" onclick="addPromoItem('dinner', 'CHA')">
                        <i class="ti ti-plus"></i> Add
                    </button>
                </div>
                <ul class="list-group small" id="list-promo-dinner-CHA"></ul>
            </div>
        </div>

        <hr>

        <h6 class="fw-bold text-muted mt-3">4. Package Report (Total Qty)</h6>
        <div class="p-3 border rounded mb-3 bg-light">
            <div class="row g-3">
                <div class="col-md-3"><label class="small text-danger fw-bold">Supreme</label><input type="number"
                        class="form-control" name="session[dinner][additional_data][package_supreme]"
                        value="{{ old('session.dinner.additional_data.package_supreme', $dn->additional_data['package_supreme'] ?? '') }}"
                        placeholder="0">
                </div>
                <div class="col-md-3"><label class="small text-warning fw-bold">Premium</label><input type="number"
                        class="form-control" name="session[dinner][additional_data][package_premium]"
                        value="{{ old('session.dinner.additional_data.package_premium', $dn->additional_data['package_premium'] ?? '') }}"
                        placeholder="0">
                </div>
                <div class="col-md-3"><label class="small text-primary fw-bold">Reguler</label><input type="number"
                        class="form-control" name="session[dinner][additional_data][package_reguler]"
                        value="{{ old('session.dinner.additional_data.package_reguler', $dn->additional_data['package_reguler'] ?? '') }}"
                        placeholder="0">
                </div>
                <div class="col-md-3"><label class="small text-success fw-bold">Promo</label><input type="number"
                        class="form-control" name="session[dinner][additional_data][package_promo]"
                        value="{{ old('session.dinner.additional_data.package_promo', $dn->additional_data['package_promo'] ?? '') }}"
                        placeholder="0">
                </div>
            </div>
        </div>

        <hr>

        <h6 class="fw-bold text-muted mt-3">5. Revenue Report (IDR)</h6>
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

        <h6 class="fw-bold text-muted mt-3">6. Upselling & Remarks</h6>
        <div class="row g-3">
            {{-- Food --}}
            <div class="col-md-6">
                <label class="form-label small fw-bold">Upselling Menu (Food)</label>
                @php $dnFoodVal = old('session.dinner.upselling_data.food', $dn->upselling_data['food'] ?? []); @endphp
                <input type="hidden" id="input-dinner-food-CHA" name="session[dinner][upselling_data][food]"
                    value="{{ is_array($dnFoodVal) ? json_encode($dnFoodVal) : $dnFoodVal }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="select-dinner-food-CHA">
                        <option value="" selected>Select Food...</option>
                        @foreach ($foods as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                    <input type="number" class="form-control form-control-sm" id="pax-dinner-food-CHA"
                        placeholder="Qty" style="max-width: 70px;">
                    <button class="btn btn-sm btn-dark" type="button"
                        onclick="addUpsellingItem('dinner', 'food', 'CHA')"><i class="ti ti-plus"></i> Add</button>
                </div>
                <ul class="list-group small" id="list-dinner-food-CHA"></ul>
            </div>
            {{-- Bev --}}
            <div class="col-md-6">
                <label class="form-label small fw-bold">Beverage Upselling</label>
                @php $dnBevVal = old('session.dinner.upselling_data.beverage', $dn->upselling_data['beverage'] ?? []); @endphp
                <input type="hidden" id="input-dinner-beverage-CHA" name="session[dinner][upselling_data][beverage]"
                    value="{{ is_array($dnBevVal) ? json_encode($dnBevVal) : $dnBevVal }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="select-dinner-beverage-CHA">
                        <option value="" selected>Select Drink...</option>
                        @foreach ($beverages as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                    <input type="number" class="form-control form-control-sm" id="pax-dinner-beverage-CHA"
                        placeholder="Qty" style="max-width: 70px;">
                    <button class="btn btn-sm btn-dark" type="button"
                        onclick="addUpsellingItem('dinner', 'beverage', 'CHA')"><i class="ti ti-plus"></i>
                        Add</button>
                </div>
                <ul class="list-group small" id="list-dinner-beverage-CHA"></ul>
            </div>
            {{-- Remarks --}}
            <div class="col-md-12">
                <label class="form-label small">General Remarks</label>
                <textarea class="form-control" name="session[dinner][remarks]">{{ old('session.dinner.remarks', $dn->remarks ?? '') }}</textarea>
            </div>
            {{-- Staff --}}
            <div class="col-md-12 mt-3">
                <label class="form-label small fw-bold">Staff on Duty</label>
                @php $dnStaffVal = old('session.dinner.staff_on_duty', $dn->staff_on_duty ?? []); @endphp
                <input type="hidden" id="input-staff-dinner-CHA" name="session[dinner][staff_on_duty]"
                    value="{{ is_array($dnStaffVal) ? json_encode($dnStaffVal) : $dnStaffVal }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="select-staff-dinner-CHA">
                        <option value="" selected>Select Staff...</option>
                        @foreach ($myStaffList as $staff)
                            <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                        @endforeach
                    </select>
                    <button class="btn btn-sm btn-dark" type="button" onclick="addStaffItem('dinner', 'CHA')"><i
                            class="ti ti-plus"></i> Add</button>
                </div>
                <div id="list-staff-dinner-CHA" class="d-flex flex-wrap"></div>
            </div>
        </div>

        <hr>

        <h6 class="fw-bold text-muted mt-3">7. Competitor Comparison</h6>
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

        // --- LUNCH INIT ---
        let lcFood = {!! json_encode(old('session.lunch.upselling_data.food', $lc->upselling_data['food'] ?? [])) !!};
        initUpselling('lunch', 'food', lcFood, 'CHA');
        let lcBev = {!! json_encode(old('session.lunch.upselling_data.beverage', $lc->upselling_data['beverage'] ?? [])) !!};
        initUpselling('lunch', 'beverage', lcBev, 'CHA');
        let lcStaff = {!! json_encode(old('session.lunch.staff_on_duty', $lc->staff_on_duty ?? [])) !!};
        initStaff('lunch', lcStaff, 'CHA');

        let lcOccasion = {!! json_encode(old('session.lunch.additional_data.occasion_items', $lc->additional_data['occasion_items'] ?? [])) !!};
        initOccasion('lunch', lcOccasion, 'CHA');
        let lcPromo = {!! json_encode(old('session.lunch.additional_data.promo_items', $lc->additional_data['promo_items'] ?? [])) !!};
        initPromoItems('lunch', lcPromo, 'CHA');

        // --- DINNER INIT ---
        let dnFood = {!! json_encode(old('session.dinner.upselling_data.food', $dn->upselling_data['food'] ?? [])) !!};
        initUpselling('dinner', 'food', dnFood, 'CHA');
        let dnBev = {!! json_encode(old('session.dinner.upselling_data.beverage', $dn->upselling_data['beverage'] ?? [])) !!};
        initUpselling('dinner', 'beverage', dnBev, 'CHA');
        let dnStaff = {!! json_encode(old('session.dinner.staff_on_duty', $dn->staff_on_duty ?? [])) !!};
        initStaff('dinner', dnStaff, 'CHA');

        let dnOccasion = {!! json_encode(old('session.dinner.additional_data.occasion_items', $dn->additional_data['occasion_items'] ?? [])) !!};
        initOccasion('dinner', dnOccasion, 'CHA');
        let dnPromo = {!! json_encode(old('session.dinner.additional_data.promo_items', $dn->additional_data['promo_items'] ?? [])) !!};
        initPromoItems('dinner', dnPromo, 'CHA');

    });
</script>
