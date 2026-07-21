@php
    $lc = $details['lunch'] ?? null;
    $dn = $details['dinner'] ?? null;

    $restoNgn = $restaurants->where('code', 'NJR')->first();
    $myStaffList = $restoNgn ? $restoNgn->users : [];

    $myMenu = $restoNgn && isset($upsellingItems[$restoNgn->id]) ? $upsellingItems[$restoNgn->id] : collect([]);
    $foods = $myMenu->where('type', 'food');
    $beverages = $myMenu->where('type', 'beverage');

    $lcOccasionData = old('session.lunch.additional_data.occasion_items', $lc->additional_data['occasion_items'] ?? []);
    $lcOccasionValue = is_array($lcOccasionData) ? json_encode($lcOccasionData) : $lcOccasionData;
    $dnOccasionData = old('session.dinner.additional_data.occasion_items', $dn->additional_data['occasion_items'] ?? []);
    $dnOccasionValue = is_array($dnOccasionData) ? json_encode($dnOccasionData) : $dnOccasionData;

    $lcPromoData = old('session.lunch.additional_data.promo_items', $lc->additional_data['promo_items'] ?? []);
    $lcPromoValue = is_array($lcPromoData) ? json_encode($lcPromoData) : $lcPromoData;
    $dnPromoData = old('session.dinner.additional_data.promo_items', $dn->additional_data['promo_items'] ?? []);
    $dnPromoValue = is_array($dnPromoData) ? json_encode($dnPromoData) : $dnPromoData;

    $lcTeppanData = old('session.lunch.additional_data.revenue_teppan_items', $lc->additional_data['revenue_teppan_items'] ?? []);
    $lcTeppanValue = is_array($lcTeppanData) ? json_encode($lcTeppanData) : $lcTeppanData;
    $dnTeppanData = old('session.dinner.additional_data.revenue_teppan_items', $dn->additional_data['revenue_teppan_items'] ?? []);
    $dnTeppanValue = is_array($dnTeppanData) ? json_encode($dnTeppanData) : $dnTeppanData;

    $lcSetMenuData = old('session.lunch.additional_data.setmenu_items', $lc->additional_data['setmenu_items'] ?? []);
    $lcSetMenuValue = is_array($lcSetMenuData) ? json_encode($lcSetMenuData) : $lcSetMenuData;
    $dnSetMenuData = old('session.dinner.additional_data.setmenu_items', $dn->additional_data['setmenu_items'] ?? []);
    $dnSetMenuValue = is_array($dnSetMenuData) ? json_encode($dnSetMenuData) : $dnSetMenuData;
@endphp

{{-- ============================================================ --}}
{{-- SESSION: LUNCH --}}
{{-- ============================================================ --}}
<div class="card mb-4">
    <div class="card-header bg-light-danger">
        <h5 class="mb-0 text-danger"><i class="ti ti-sun"></i> Lunch Report</h5>
    </div>
    <div class="card-body">

        {{-- 1. COVER REPORT --}}
        <h6 class="fw-bold text-muted mt-3">1. Cover Report</h6>
        <div class="p-3 border rounded mb-3 bg-white">
            <span class="badge bg-dark mb-2">TEPPANYAKI</span>
            <div class="row g-2">
                <div class="col-md-4">
                    <label class="small text-muted">In-House (Adult)</label>
                    <input type="number" class="form-control form-control-sm"
                        name="session[lunch][cover_data][teppanyaki_inhouse]"
                        value="{{ old('session.lunch.cover_data.teppanyaki_inhouse', $lc->cover_data['teppanyaki_inhouse'] ?? '') }}"
                        placeholder="0">
                </div>
                <div class="col-md-4">
                    <label class="small text-muted">Walk-In (Adult)</label>
                    <input type="number" class="form-control form-control-sm"
                        name="session[lunch][cover_data][teppanyaki_walkin]"
                        value="{{ old('session.lunch.cover_data.teppanyaki_walkin', $lc->cover_data['teppanyaki_walkin'] ?? '') }}"
                        placeholder="0">
                </div>
                <div class="col-md-4">
                    <label class="small text-muted">Event (Adult)</label>
                    <input type="number" class="form-control form-control-sm"
                        name="session[lunch][cover_data][teppanyaki_event]"
                        value="{{ old('session.lunch.cover_data.teppanyaki_event', $lc->cover_data['teppanyaki_event'] ?? '') }}"
                        placeholder="0">
                </div>
            </div>
        </div>
        <div class="p-3 border rounded mb-3 bg-white">
            <span class="badge bg-danger mb-2">YAKINIKU</span>
            <div class="row g-2">
                <div class="col-md-4">
                    <label class="small text-muted">In-House (Adult)</label>
                    <input type="number" class="form-control form-control-sm"
                        name="session[lunch][cover_data][yakiniku_inhouse]"
                        value="{{ old('session.lunch.cover_data.yakiniku_inhouse', $lc->cover_data['yakiniku_inhouse'] ?? '') }}"
                        placeholder="0">
                </div>
                <div class="col-md-4">
                    <label class="small text-muted">Walk-In (Adult)</label>
                    <input type="number" class="form-control form-control-sm"
                        name="session[lunch][cover_data][yakiniku_walkin]"
                        value="{{ old('session.lunch.cover_data.yakiniku_walkin', $lc->cover_data['yakiniku_walkin'] ?? '') }}"
                        placeholder="0">
                </div>
                <div class="col-md-4">
                    <label class="small text-muted">Event (Adult)</label>
                    <input type="number" class="form-control form-control-sm"
                        name="session[lunch][cover_data][yakiniku_event]"
                        value="{{ old('session.lunch.cover_data.yakiniku_event', $lc->cover_data['yakiniku_event'] ?? '') }}"
                        placeholder="0">
                </div>
            </div>
        </div>

        <hr>

        {{-- 2. OCCASION --}}
        <h6 class="fw-bold text-muted mt-3">2. Occasion / Event Type</h6>
        <div class="row g-3">
            <div class="col-md-12">
                <input type="hidden" id="input-occasion-lunch-NJR" name="session[lunch][additional_data][occasion_items]" value="{{ $lcOccasionValue ?? '[]' }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="occasion-type-lunch-NJR">
                        <option value="" selected>Select Occasion...</option>
                        <option value="Wedding Party">Wedding Party</option>
                        <option value="Birthday Party">Birthday Party</option>
                        <option value="Social Event">Social Event</option>
                        <option value="Corporate Event">Corporate Event</option>
                    </select>
                    <input type="text" class="form-control form-control-sm" id="occasion-name-lunch-NJR"
                        placeholder="Name (e.g. Mr. Budi)">
                    <input type="number" class="form-control form-control-sm" id="occasion-pax-lunch-NJR"
                        placeholder="Pax" style="max-width: 80px;">
                    <input type="text" class="form-control form-control-sm rupiah" id="occasion-revenue-lunch-NJR"
                        placeholder="Revenue" style="max-width: 120px;" autocomplete="off">
                    <button class="btn btn-sm btn-dark" type="button" onclick="addOccasionItem('lunch', 'NJR')">
                        <i class="ti ti-plus"></i> Add
                    </button>
                </div>
                <ul class="list-group small" id="list-occasion-lunch-NJR"></ul>
            </div>
        </div>

        <hr>

        {{-- 3. PROMO --}}
        <h6 class="fw-bold text-muted mt-3">3. Promo</h6>
        <div class="row g-3">
            <div class="col-md-12">
                <input type="hidden" id="input-promo-lunch-NJR" name="session[lunch][additional_data][promo_items]" value="{{ $lcPromoValue ?? '[]' }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="promo-type-lunch-NJR">
                        <option value="" selected>Select Promo...</option>
                        <option value="Mandiri Card">Mandiri Card</option>
                        <option value="BCA Card">BCA Card</option>
                        <option value="Membership">Membership</option>
                    </select>
                    <input type="number" class="form-control form-control-sm" id="promo-pax-lunch-NJR"
                        placeholder="Pax" style="max-width: 80px;">
                    <input type="text" class="form-control form-control-sm rupiah" id="promo-revenue-lunch-NJR"
                        placeholder="Revenue" style="max-width: 120px;" autocomplete="off">
                    <button class="btn btn-sm btn-dark" type="button" onclick="addPromoItem('lunch', 'NJR')">
                        <i class="ti ti-plus"></i> Add
                    </button>
                </div>
                <ul class="list-group small" id="list-promo-lunch-NJR"></ul>
            </div>
        </div>

        <hr>

        {{-- 4. SET MENU --}}
        <h6 class="fw-bold text-muted mt-3">4. Set Menu</h6>
        <div class="row g-3">
            <div class="col-md-12">
                <input type="hidden" id="input-setmenu-lunch-NJR" name="session[lunch][additional_data][setmenu_items]" value="{{ $lcSetMenuValue ?? '[]' }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="setmenu-type-lunch-NJR">
                        <option value="" selected>Select Set Menu...</option>
                        <option value="NOZAWA SET">NOZAWA SET</option>
                        <option value="NAGANO PREMIUM">NAGANO PREMIUM</option>
                        <option value="Add On Japanese Set">Add On Japanese Set</option>
                        <option value="MATSUMOTO SET">MATSUMOTO SET</option>
                        <option value="NAGANO SALAD">NAGANO SALAD</option>
                        <option value="NIGATA SET">NIGATA SET</option>
                        <option value="SHINANO SET">SHINANO SET</option>
                        <option value="AYCE GOCHISO YAKINIKU">AYCE GOCHISO YAKINIKU</option>
                        <option value="Add On Wagyu Superior Set">Add On Wagyu Superior Set</option>
                        <option value="Nagano Superior Course Ebi Set">Nagano Superior Course Ebi Set</option>
                        <option value="Japanese Superior Set Course">Japanese Superior Set Course</option>
                        <option value="Nagano Ocean Course Prawn Set">Nagano Ocean Course Prawn Set</option>
                        <option value="Nagano Superior Course Lobster Set">Nagano Superior Course Lobster Set</option>
                        <option value="Nagano Ocean Course Lobster Set">Nagano Ocean Course Lobster Set</option>
                        <option value="Nagano Superior Course Hotate Set">Nagano Superior Course Hotate Set</option>
                    </select>
                    <input type="number" class="form-control form-control-sm" id="setmenu-pax-lunch-NJR"
                        placeholder="Pax" style="max-width: 80px;">
                    <input type="text" class="form-control form-control-sm rupiah" id="setmenu-revenue-lunch-NJR"
                        placeholder="Revenue" style="max-width: 120px;" autocomplete="off">
                    <button class="btn btn-sm btn-dark" type="button" onclick="addSetMenuItem('lunch', 'NJR')">
                        <i class="ti ti-plus"></i> Add
                    </button>
                </div>
                <ul class="list-group small" id="list-setmenu-lunch-NJR"></ul>
            </div>
        </div>

        <hr>

        {{-- 5. REVENUE REPORT --}}
        <h6 class="fw-bold text-muted mt-3">5. Revenue Report (IDR)</h6>
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label small">Food Revenue</label>
                <input type="text" class="form-control rupiah" name="session[lunch][revenue_food]"
                    value="{{ old('session.lunch.revenue_food', isset($lc->revenue_food) ? number_format($lc->revenue_food, 0, ',', '.') : '') }}"
                    placeholder="0" autocomplete="off">
            </div>
            <div class="col-md-3">
                <label class="form-label small">Beverage Revenue</label>
                <input type="text" class="form-control rupiah" name="session[lunch][revenue_beverage]"
                    value="{{ old('session.lunch.revenue_beverage', isset($lc->revenue_beverage) ? number_format($lc->revenue_beverage, 0, ',', '.') : '') }}"
                    placeholder="0" autocomplete="off">
            </div>
            <div class="col-md-3">
                <label class="form-label small">Others Revenue</label>
                <input type="text" class="form-control rupiah" name="session[lunch][revenue_others]"
                    value="{{ old('session.lunch.revenue_others', isset($lc->revenue_others) ? number_format($lc->revenue_others, 0, ',', '.') : '') }}"
                    placeholder="0" autocomplete="off">
            </div>
            <div class="col-md-3">
                <label class="form-label small">Event Revenue</label>
                <input type="text" class="form-control rupiah" name="session[lunch][revenue_event]"
                    value="{{ old('session.lunch.revenue_event', isset($lc->revenue_event) ? number_format($lc->revenue_event, 0, ',', '.') : '') }}"
                    placeholder="0" autocomplete="off">
            </div>
        </div>
        <div class="row g-3 mt-2">
            <div class="col-md-4">
                <label class="form-label small fw-bold">Revenue Teppan</label>
                <input type="hidden" id="input-teppan-lunch-NJR" name="session[lunch][additional_data][revenue_teppan_items]" value="{{ $lcTeppanValue ?? '[]' }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="teppan-floor-lunch-NJR" style="max-width: 100px;">
                        <option value="">Floor</option>
                        <option value="Lt 5">Lt 5</option>
                        <option value="Lt 6">Lt 6</option>
                    </select>
                    <input type="text" class="form-control form-control-sm rupiah" id="teppan-revenue-lunch-NJR"
                        placeholder="Revenue" style="max-width: 120px;" autocomplete="off">
                    <button class="btn btn-sm btn-dark" type="button" onclick="addTeppanItem('lunch', 'NJR')">
                        <i class="ti ti-plus"></i> Add
                    </button>
                </div>
                <ul class="list-group small" id="list-teppan-lunch-NJR"></ul>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold">Revenue Yakiniku</label>
                <input type="text" class="form-control rupiah" name="session[lunch][additional_data][revenue_yakiniku]"
                    value="{{ old('session.lunch.additional_data.revenue_yakiniku', isset($lc->additional_data['revenue_yakiniku']) ? number_format($lc->additional_data['revenue_yakiniku'], 0, ',', '.') : '') }}"
                    placeholder="0" autocomplete="off">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold">Revenue Ala Carte</label>
                <input type="text" class="form-control rupiah" name="session[lunch][additional_data][revenue_ala_carte]"
                    value="{{ old('session.lunch.additional_data.revenue_ala_carte', isset($lc->additional_data['revenue_ala_carte']) ? number_format($lc->additional_data['revenue_ala_carte'], 0, ',', '.') : '') }}"
                    placeholder="0" autocomplete="off">
            </div>
        </div>

        <hr>

        {{-- 5. UPSELLING & REMARKS --}}
        <h6 class="fw-bold text-muted mt-3">6. Upselling & Remarks</h6>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label small fw-bold">Upselling Menu (Food)</label>
                @php $lcFoodVal = old('session.lunch.upselling_data.food', $lc->upselling_data['food'] ?? []); @endphp
                <input type="hidden" id="input-lunch-food-NJR" name="session[lunch][upselling_data][food]"
                    value="{{ is_array($lcFoodVal) ? json_encode($lcFoodVal) : $lcFoodVal }}">
                <div class="input-group mb-2">
                    <input type="text" class="form-control form-control-sm" id="select-lunch-food-NJR" placeholder="Enter food name...">
                    <input type="number" class="form-control form-control-sm" id="pax-lunch-food-NJR"
                        placeholder="Qty" style="max-width: 70px;">
                    <button class="btn btn-sm btn-dark" type="button"
                        onclick="addUpsellingItem('lunch', 'food', 'NJR')"><i class="ti ti-plus"></i> Add</button>
                </div>
                <ul class="list-group small" id="list-lunch-food-NJR"></ul>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-bold">Beverage Upselling</label>
                @php $lcBevVal = old('session.lunch.upselling_data.beverage', $lc->upselling_data['beverage'] ?? []); @endphp
                <input type="hidden" id="input-lunch-beverage-NJR" name="session[lunch][upselling_data][beverage]"
                    value="{{ is_array($lcBevVal) ? json_encode($lcBevVal) : $lcBevVal }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="select-lunch-beverage-NJR">
                        <option value="" selected>Select Drink...</option>
                        @foreach ($beverages as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                    <input type="number" class="form-control form-control-sm" id="pax-lunch-beverage-NJR"
                        placeholder="Qty" style="max-width: 70px;">
                    <button class="btn btn-sm btn-dark" type="button"
                        onclick="addUpsellingItem('lunch', 'beverage', 'NJR')"><i class="ti ti-plus"></i> Add</button>
                </div>
                <ul class="list-group small" id="list-lunch-beverage-NJR"></ul>
            </div>
            <div class="col-md-12 mt-3">
                <label class="form-label small">General Remarks</label>
                <textarea class="form-control" name="session[lunch][remarks]">{{ old('session.lunch.remarks', $lc->remarks ?? '') }}</textarea>
            </div>
            <div class="col-md-12 mt-3">
                <label class="form-label small fw-bold">Staff on Duty</label>
                @php $lcStaffVal = old('session.lunch.staff_on_duty', $lc->staff_on_duty ?? []); @endphp
                <input type="hidden" id="input-staff-lunch-NJR" name="session[lunch][staff_on_duty]"
                    value="{{ is_array($lcStaffVal) ? json_encode($lcStaffVal) : $lcStaffVal }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="select-staff-lunch-NJR">
                        <option value="" selected>Select Staff...</option>
                        @foreach ($myStaffList as $staff)
                            <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                        @endforeach
                    </select>
                    <button class="btn btn-sm btn-dark" type="button" onclick="addStaffItem('lunch', 'NJR')"><i
                            class="ti ti-plus"></i> Add</button>
                </div>
                <div id="list-staff-lunch-NJR" class="d-flex flex-wrap mt-2"></div>
            </div>
        </div>

        <hr>

        {{-- 6. COMPETITOR --}}
        <h6 class="fw-bold text-muted mt-3">7. Competitor Comparison</h6>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label small">Shangri-La</label>
                <input type="number" class="form-control" name="session[lunch][competitor_data][shangrila_cover]"
                    value="{{ old('session.lunch.competitor_data.shangrila_cover', $lc->competitor_data['shangrila_cover'] ?? '') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label small">JW Marriott</label>
                <input type="number" class="form-control" name="session[lunch][competitor_data][jw_marriott_cover]"
                    value="{{ old('session.lunch.competitor_data.jw_marriott_cover', $lc->competitor_data['jw_marriott_cover'] ?? '') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label small">Sheraton</label>
                <input type="number" class="form-control" name="session[lunch][competitor_data][sheraton_cover]"
                    value="{{ old('session.lunch.competitor_data.sheraton_cover', $lc->competitor_data['sheraton_cover'] ?? '') }}">
            </div>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- SESSION: DINNER --}}
{{-- ============================================================ --}}
<div class="card mb-4">
    <div class="card-header bg-light-primary">
        <h5 class="mb-0 text-primary"><i class="ti ti-moon"></i> Dinner Report</h5>
    </div>
    <div class="card-body">

        {{-- 1. COVER REPORT --}}
        <h6 class="fw-bold text-muted mt-3">1. Cover Report</h6>
        <div class="p-3 border rounded mb-3 bg-white">
            <span class="badge bg-dark mb-2">TEPPANYAKI</span>
            <div class="row g-2">
                <div class="col-md-4">
                    <label class="small text-muted">In-House (Adult)</label>
                    <input type="number" class="form-control form-control-sm"
                        name="session[dinner][cover_data][teppanyaki_inhouse]"
                        value="{{ old('session.dinner.cover_data.teppanyaki_inhouse', $dn->cover_data['teppanyaki_inhouse'] ?? '') }}"
                        placeholder="0">
                </div>
                <div class="col-md-4">
                    <label class="small text-muted">Walk-In (Adult)</label>
                    <input type="number" class="form-control form-control-sm"
                        name="session[dinner][cover_data][teppanyaki_walkin]"
                        value="{{ old('session.dinner.cover_data.teppanyaki_walkin', $dn->cover_data['teppanyaki_walkin'] ?? '') }}"
                        placeholder="0">
                </div>
                <div class="col-md-4">
                    <label class="small text-muted">Event (Adult)</label>
                    <input type="number" class="form-control form-control-sm"
                        name="session[dinner][cover_data][teppanyaki_event]"
                        value="{{ old('session.dinner.cover_data.teppanyaki_event', $dn->cover_data['teppanyaki_event'] ?? '') }}"
                        placeholder="0">
                </div>
            </div>
        </div>
        <div class="p-3 border rounded mb-3 bg-white">
            <span class="badge bg-danger mb-2">YAKINIKU</span>
            <div class="row g-2">
                <div class="col-md-4">
                    <label class="small text-muted">In-House (Adult)</label>
                    <input type="number" class="form-control form-control-sm"
                        name="session[dinner][cover_data][yakiniku_inhouse]"
                        value="{{ old('session.dinner.cover_data.yakiniku_inhouse', $dn->cover_data['yakiniku_inhouse'] ?? '') }}"
                        placeholder="0">
                </div>
                <div class="col-md-4">
                    <label class="small text-muted">Walk-In (Adult)</label>
                    <input type="number" class="form-control form-control-sm"
                        name="session[dinner][cover_data][yakiniku_walkin]"
                        value="{{ old('session.dinner.cover_data.yakiniku_walkin', $dn->cover_data['yakiniku_walkin'] ?? '') }}"
                        placeholder="0">
                </div>
                <div class="col-md-4">
                    <label class="small text-muted">Event (Adult)</label>
                    <input type="number" class="form-control form-control-sm"
                        name="session[dinner][cover_data][yakiniku_event]"
                        value="{{ old('session.dinner.cover_data.yakiniku_event', $dn->cover_data['yakiniku_event'] ?? '') }}"
                        placeholder="0">
                </div>
            </div>
        </div>

        <hr>

        {{-- 2. OCCASION --}}
        <h6 class="fw-bold text-muted mt-3">2. Occasion / Event Type</h6>
        <div class="row g-3">
            <div class="col-md-12">
                <input type="hidden" id="input-occasion-dinner-NJR" name="session[dinner][additional_data][occasion_items]" value="{{ $dnOccasionValue ?? '[]' }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="occasion-type-dinner-NJR">
                        <option value="" selected>Select Occasion...</option>
                        <option value="Wedding Party">Wedding Party</option>
                        <option value="Birthday Party">Birthday Party</option>
                        <option value="Social Event">Social Event</option>
                        <option value="Corporate Event">Corporate Event</option>
                    </select>
                    <input type="text" class="form-control form-control-sm" id="occasion-name-dinner-NJR"
                        placeholder="Name (e.g. Mr. Budi)">
                    <input type="number" class="form-control form-control-sm" id="occasion-pax-dinner-NJR"
                        placeholder="Pax" style="max-width: 80px;">
                    <input type="text" class="form-control form-control-sm rupiah" id="occasion-revenue-dinner-NJR"
                        placeholder="Revenue" style="max-width: 120px;" autocomplete="off">
                    <button class="btn btn-sm btn-dark" type="button" onclick="addOccasionItem('dinner', 'NJR')">
                        <i class="ti ti-plus"></i> Add
                    </button>
                </div>
                <ul class="list-group small" id="list-occasion-dinner-NJR"></ul>
            </div>
        </div>

        <hr>

        {{-- 3. PROMO --}}
        <h6 class="fw-bold text-muted mt-3">3. Promo</h6>
        <div class="row g-3">
            <div class="col-md-12">
                <input type="hidden" id="input-promo-dinner-NJR" name="session[dinner][additional_data][promo_items]" value="{{ $dnPromoValue ?? '[]' }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="promo-type-dinner-NJR">
                        <option value="" selected>Select Promo...</option>
                        <option value="Mandiri Card">Mandiri Card</option>
                        <option value="BCA Card">BCA Card</option>
                        <option value="Membership">Membership</option>
                    </select>
                    <input type="number" class="form-control form-control-sm" id="promo-pax-dinner-NJR"
                        placeholder="Pax" style="max-width: 80px;">
                    <input type="text" class="form-control form-control-sm rupiah" id="promo-revenue-dinner-NJR"
                        placeholder="Revenue" style="max-width: 120px;" autocomplete="off">
                    <button class="btn btn-sm btn-dark" type="button" onclick="addPromoItem('dinner', 'NJR')">
                        <i class="ti ti-plus"></i> Add
                    </button>
                </div>
                <ul class="list-group small" id="list-promo-dinner-NJR"></ul>
            </div>
        </div>

        <hr>

        {{-- 4. SET MENU --}}
        <h6 class="fw-bold text-muted mt-3">4. Set Menu</h6>
        <div class="row g-3">
            <div class="col-md-12">
                <input type="hidden" id="input-setmenu-dinner-NJR" name="session[dinner][additional_data][setmenu_items]" value="{{ $dnSetMenuValue ?? '[]' }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="setmenu-type-dinner-NJR">
                        <option value="" selected>Select Set Menu...</option>
                        <option value="NOZAWA SET">NOZAWA SET</option>
                        <option value="NAGANO PREMIUM">NAGANO PREMIUM</option>
                        <option value="Add On Japanese Set">Add On Japanese Set</option>
                        <option value="MATSUMOTO SET">MATSUMOTO SET</option>
                        <option value="NAGANO SALAD">NAGANO SALAD</option>
                        <option value="NIGATA SET">NIGATA SET</option>
                        <option value="SHINANO SET">SHINANO SET</option>
                        <option value="AYCE GOCHISO YAKINIKU">AYCE GOCHISO YAKINIKU</option>
                        <option value="Add On Wagyu Superior Set">Add On Wagyu Superior Set</option>
                        <option value="Nagano Superior Course Ebi Set">Nagano Superior Course Ebi Set</option>
                        <option value="Japanese Superior Set Course">Japanese Superior Set Course</option>
                        <option value="Nagano Ocean Course Prawn Set">Nagano Ocean Course Prawn Set</option>
                        <option value="Nagano Superior Course Lobster Set">Nagano Superior Course Lobster Set</option>
                        <option value="Nagano Ocean Course Lobster Set">Nagano Ocean Course Lobster Set</option>
                        <option value="Nagano Superior Course Hotate Set">Nagano Superior Course Hotate Set</option>
                    </select>
                    <input type="number" class="form-control form-control-sm" id="setmenu-pax-dinner-NJR"
                        placeholder="Pax" style="max-width: 80px;">
                    <input type="text" class="form-control form-control-sm rupiah" id="setmenu-revenue-dinner-NJR"
                        placeholder="Revenue" style="max-width: 120px;" autocomplete="off">
                    <button class="btn btn-sm btn-dark" type="button" onclick="addSetMenuItem('dinner', 'NJR')">
                        <i class="ti ti-plus"></i> Add
                    </button>
                </div>
                <ul class="list-group small" id="list-setmenu-dinner-NJR"></ul>
            </div>
        </div>

        <hr>

        {{-- 5. REVENUE REPORT --}}
        <h6 class="fw-bold text-muted mt-3">5. Revenue Report (IDR)</h6>
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label small">Food Revenue</label>
                <input type="text" class="form-control rupiah" name="session[dinner][revenue_food]"
                    value="{{ old('session.dinner.revenue_food', isset($dn->revenue_food) ? number_format($dn->revenue_food, 0, ',', '.') : '') }}"
                    placeholder="0" autocomplete="off">
            </div>
            <div class="col-md-3">
                <label class="form-label small">Beverage Revenue</label>
                <input type="text" class="form-control rupiah" name="session[dinner][revenue_beverage]"
                    value="{{ old('session.dinner.revenue_beverage', isset($dn->revenue_beverage) ? number_format($dn->revenue_beverage, 0, ',', '.') : '') }}"
                    placeholder="0" autocomplete="off">
            </div>
            <div class="col-md-3">
                <label class="form-label small">Others Revenue</label>
                <input type="text" class="form-control rupiah" name="session[dinner][revenue_others]"
                    value="{{ old('session.dinner.revenue_others', isset($dn->revenue_others) ? number_format($dn->revenue_others, 0, ',', '.') : '') }}"
                    placeholder="0" autocomplete="off">
            </div>
            <div class="col-md-3">
                <label class="form-label small">Event Revenue</label>
                <input type="text" class="form-control rupiah" name="session[dinner][revenue_event]"
                    value="{{ old('session.dinner.revenue_event', isset($dn->revenue_event) ? number_format($dn->revenue_event, 0, ',', '.') : '') }}"
                    placeholder="0" autocomplete="off">
            </div>
        </div>
        <div class="row g-3 mt-2">
            <div class="col-md-4">
                <label class="form-label small fw-bold">Revenue Teppan</label>
                <input type="hidden" id="input-teppan-dinner-NJR" name="session[dinner][additional_data][revenue_teppan_items]" value="{{ $dnTeppanValue ?? '[]' }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="teppan-floor-dinner-NJR" style="max-width: 100px;">
                        <option value="">Floor</option>
                        <option value="Lt 5">Lt 5</option>
                        <option value="Lt 6">Lt 6</option>
                    </select>
                    <input type="text" class="form-control form-control-sm rupiah" id="teppan-revenue-dinner-NJR"
                        placeholder="Revenue" style="max-width: 120px;" autocomplete="off">
                    <button class="btn btn-sm btn-dark" type="button" onclick="addTeppanItem('dinner', 'NJR')">
                        <i class="ti ti-plus"></i> Add
                    </button>
                </div>
                <ul class="list-group small" id="list-teppan-dinner-NJR"></ul>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold">Revenue Yakiniku</label>
                <input type="text" class="form-control rupiah" name="session[dinner][additional_data][revenue_yakiniku]"
                    value="{{ old('session.dinner.additional_data.revenue_yakiniku', isset($dn->additional_data['revenue_yakiniku']) ? number_format($dn->additional_data['revenue_yakiniku'], 0, ',', '.') : '') }}"
                    placeholder="0" autocomplete="off">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold">Revenue Ala Carte</label>
                <input type="text" class="form-control rupiah" name="session[dinner][additional_data][revenue_ala_carte]"
                    value="{{ old('session.dinner.additional_data.revenue_ala_carte', isset($dn->additional_data['revenue_ala_carte']) ? number_format($dn->additional_data['revenue_ala_carte'], 0, ',', '.') : '') }}"
                    placeholder="0" autocomplete="off">
            </div>
        </div>

        <hr>

        {{-- 5. UPSELLING & REMARKS --}}
        <h6 class="fw-bold text-muted mt-3">6. Upselling & Remarks</h6>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label small fw-bold">Upselling Menu (Food)</label>
                @php $dnFoodVal = old('session.dinner.upselling_data.food', $dn->upselling_data['food'] ?? []); @endphp
                <input type="hidden" id="input-dinner-food-NJR" name="session[dinner][upselling_data][food]"
                    value="{{ is_array($dnFoodVal) ? json_encode($dnFoodVal) : $dnFoodVal }}">
                <div class="input-group mb-2">
                    <input type="text" class="form-control form-control-sm" id="select-dinner-food-NJR" placeholder="Enter food name...">
                    <input type="number" class="form-control form-control-sm" id="pax-dinner-food-NJR"
                        placeholder="Qty" style="max-width: 70px;">
                    <button class="btn btn-sm btn-dark" type="button"
                        onclick="addUpsellingItem('dinner', 'food', 'NJR')"><i class="ti ti-plus"></i> Add</button>
                </div>
                <ul class="list-group small" id="list-dinner-food-NJR"></ul>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-bold">Beverage Upselling</label>
                @php $dnBevVal = old('session.dinner.upselling_data.beverage', $dn->upselling_data['beverage'] ?? []); @endphp
                <input type="hidden" id="input-dinner-beverage-NJR" name="session[dinner][upselling_data][beverage]"
                    value="{{ is_array($dnBevVal) ? json_encode($dnBevVal) : $dnBevVal }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="select-dinner-beverage-NJR">
                        <option value="" selected>Select Drink...</option>
                        @foreach ($beverages as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                    <input type="number" class="form-control form-control-sm" id="pax-dinner-beverage-NJR"
                        placeholder="Qty" style="max-width: 70px;">
                    <button class="btn btn-sm btn-dark" type="button"
                        onclick="addUpsellingItem('dinner', 'beverage', 'NJR')"><i class="ti ti-plus"></i> Add</button>
                </div>
                <ul class="list-group small" id="list-dinner-beverage-NJR"></ul>
            </div>
            <div class="col-md-12 mt-3">
                <label class="form-label small">General Remarks</label>
                <textarea class="form-control" name="session[dinner][remarks]">{{ old('session.dinner.remarks', $dn->remarks ?? '') }}</textarea>
            </div>
            <div class="col-md-12 mt-3">
                <label class="form-label small fw-bold">Staff on Duty</label>
                @php $dnStaffVal = old('session.dinner.staff_on_duty', $dn->staff_on_duty ?? []); @endphp
                <input type="hidden" id="input-staff-dinner-NJR" name="session[dinner][staff_on_duty]"
                    value="{{ is_array($dnStaffVal) ? json_encode($dnStaffVal) : $dnStaffVal }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="select-staff-dinner-NJR">
                        <option value="" selected>Select Staff...</option>
                        @foreach ($myStaffList as $staff)
                            <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                        @endforeach
                    </select>
                    <button class="btn btn-sm btn-dark" type="button" onclick="addStaffItem('dinner', 'NJR')"><i
                            class="ti ti-plus"></i> Add</button>
                </div>
                <div id="list-staff-dinner-NJR" class="d-flex flex-wrap mt-2"></div>
            </div>
        </div>

        <hr>

        {{-- 6. COMPETITOR --}}
        <h6 class="fw-bold text-muted mt-3">7. Competitor Comparison</h6>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label small">Shangri-La</label>
                <input type="number" class="form-control" name="session[dinner][competitor_data][shangrila_cover]"
                    value="{{ old('session.dinner.competitor_data.shangrila_cover', $dn->competitor_data['shangrila_cover'] ?? '') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label small">JW Marriott</label>
                <input type="number" class="form-control" name="session[dinner][competitor_data][jw_marriott_cover]"
                    value="{{ old('session.dinner.competitor_data.jw_marriott_cover', $dn->competitor_data['jw_marriott_cover'] ?? '') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label small">Sheraton</label>
                <input type="number" class="form-control" name="session[dinner][competitor_data][sheraton_cover]"
                    value="{{ old('session.dinner.competitor_data.sheraton_cover', $dn->competitor_data['sheraton_cover'] ?? '') }}">
            </div>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- SCRIPT INITIALIZATION --}}
{{-- ============================================================ --}}
<script>
    var teppanState = window.teppanState || {};
    var setmenuState = window.setmenuState || {};

    window.initTeppanItems = function(session, initialData, code) {
        const key = session + '_' + code;
        let safeData = [];
        if (initialData) {
            if (Array.isArray(initialData)) safeData = initialData;
            else if (typeof initialData === 'object') safeData = Object.values(initialData);
            else if (typeof initialData === 'string') try { safeData = JSON.parse(initialData); } catch (e) {}
        }
        teppanState[key] = safeData;
        const hiddenInput = document.getElementById('input-teppan-' + session + '-' + code);
        if (hiddenInput) hiddenInput.value = JSON.stringify(safeData);
        renderTeppanList(document.getElementById('list-teppan-' + session + '-' + code), safeData, session, code);
    };

    window.addTeppanItem = function(session, code) {
        const floorEl = document.getElementById('teppan-floor-' + session + '-' + code);
        const revEl = document.getElementById('teppan-revenue-' + session + '-' + code);
        const floor = floorEl.value;
        let revenue = revEl.value.replace(/\./g, '').trim();

        if (!floor) { alert('Please select a floor.'); return; }

        const key = session + '_' + code;
        if (!teppanState[key]) teppanState[key] = [];
        teppanState[key].push({
            floor: floor,
            revenue: revenue ? parseInt(revenue) : 0
        });

        floorEl.value = '';
        revEl.value = '';
        floorEl.focus();

        const hiddenInput = document.getElementById('input-teppan-' + session + '-' + code);
        if (hiddenInput) hiddenInput.value = JSON.stringify(teppanState[key]);
        renderTeppanList(document.getElementById('list-teppan-' + session + '-' + code), teppanState[key], session, code);
    };

    window.removeTeppanItem = function(session, code, index) {
        const key = session + '_' + code;
        teppanState[key].splice(index, 1);
        const hiddenInput = document.getElementById('input-teppan-' + session + '-' + code);
        if (hiddenInput) hiddenInput.value = JSON.stringify(teppanState[key]);
        renderTeppanList(document.getElementById('list-teppan-' + session + '-' + code), teppanState[key], session, code);
    };

    function renderTeppanList(listEl, items, session, code) {
        if (!listEl) return;
        if (!items || items.length === 0) { listEl.innerHTML = ''; return; }
        let html = '';
        items.forEach(function(item, index) {
            const rev = item.revenue ? 'Rp ' + Number(item.revenue).toLocaleString('id-ID') : '';
            html += '<li class="list-group-item d-flex justify-content-between align-items-center py-1 px-2">' +
                '<span class="small"><span class="badge bg-dark me-1">' + escapeHtml(item.floor || '') + '</span>' +
                (rev ? ' <span class="badge bg-success ms-1">' + rev + '</span>' : '') +
                '</span>' +
                '<button class="btn btn-sm btn-link text-danger p-0" onclick="removeTeppanItem(\'' + session + '\', \'' + code + '\', ' + index + ')" title="Remove"><i class="ti ti-x"></i></button>' +
                '</li>';
        });
        listEl.innerHTML = html;
    }

    window.initSetMenuItems = function(session, initialData, code) {
        const key = session + '_' + code;
        let safeData = [];
        if (initialData) {
            if (Array.isArray(initialData)) safeData = initialData;
            else if (typeof initialData === 'object') safeData = Object.values(initialData);
            else if (typeof initialData === 'string') try { safeData = JSON.parse(initialData); } catch (e) {}
        }
        setmenuState[key] = safeData;
        const hiddenInput = document.getElementById('input-setmenu-' + session + '-' + code);
        if (hiddenInput) hiddenInput.value = JSON.stringify(safeData);
        renderSetMenuList(document.getElementById('list-setmenu-' + session + '-' + code), safeData, session, code);
    };

    window.addSetMenuItem = function(session, code) {
        const typeEl = document.getElementById('setmenu-type-' + session + '-' + code);
        const paxEl = document.getElementById('setmenu-pax-' + session + '-' + code);
        const revEl = document.getElementById('setmenu-revenue-' + session + '-' + code);
        const type = typeEl.value;
        const pax = paxEl.value.trim();
        let revenue = revEl.value.replace(/\./g, '').trim();

        if (!type) { alert('Please select a set menu type.'); return; }
        if (!pax || parseInt(pax) <= 0) { alert('Please enter valid pax.'); return; }

        const key = session + '_' + code;
        if (!setmenuState[key]) setmenuState[key] = [];
        setmenuState[key].push({
            type: type,
            pax: parseInt(pax),
            revenue: revenue ? parseInt(revenue) : 0
        });

        typeEl.value = '';
        paxEl.value = '';
        revEl.value = '';
        typeEl.focus();

        const hiddenInput = document.getElementById('input-setmenu-' + session + '-' + code);
        if (hiddenInput) hiddenInput.value = JSON.stringify(setmenuState[key]);
        renderSetMenuList(document.getElementById('list-setmenu-' + session + '-' + code), setmenuState[key], session, code);
    };

    window.removeSetMenuItem = function(session, code, index) {
        const key = session + '_' + code;
        setmenuState[key].splice(index, 1);
        const hiddenInput = document.getElementById('input-setmenu-' + session + '-' + code);
        if (hiddenInput) hiddenInput.value = JSON.stringify(setmenuState[key]);
        renderSetMenuList(document.getElementById('list-setmenu-' + session + '-' + code), setmenuState[key], session, code);
    };

    function renderSetMenuList(listEl, items, session, code) {
        if (!listEl) return;
        if (!items || items.length === 0) { listEl.innerHTML = ''; return; }
        let html = '';
        items.forEach(function(item, index) {
            const rev = item.revenue ? 'Rp ' + Number(item.revenue).toLocaleString('id-ID') : '';
            html += '<li class="list-group-item d-flex justify-content-between align-items-center py-1 px-2">' +
                '<span class="small"><span class="badge bg-secondary me-1">' + escapeHtml(item.type || '') + '</span>' +
                ' <span class="badge bg-primary ms-1">' + (item.pax || 0) + ' pax</span>' +
                (rev ? ' <span class="badge bg-success ms-1">' + rev + '</span>' : '') +
                '</span>' +
                '<button class="btn btn-sm btn-link text-danger p-0" onclick="removeSetMenuItem(\'' + session + '\', \'' + code + '\', ' + index + ')" title="Remove"><i class="ti ti-x"></i></button>' +
                '</li>';
        });
        listEl.innerHTML = html;
    }

    document.addEventListener('DOMContentLoaded', function() {
        // --- LUNCH INIT ---
        let lcFood = {!! json_encode(old('session.lunch.upselling_data.food', $lc->upselling_data['food'] ?? [])) !!};
        initUpselling('lunch', 'food', lcFood, 'NJR');
        let lcBev = {!! json_encode(old('session.lunch.upselling_data.beverage', $lc->upselling_data['beverage'] ?? [])) !!};
        initUpselling('lunch', 'beverage', lcBev, 'NJR');
        let lcStaff = {!! json_encode(old('session.lunch.staff_on_duty', $lc->staff_on_duty ?? [])) !!};
        initStaff('lunch', lcStaff, 'NJR');
        let lcOccasion = {!! json_encode(old('session.lunch.additional_data.occasion_items', $lc->additional_data['occasion_items'] ?? [])) !!};
        initOccasion('lunch', lcOccasion, 'NJR');
        let lcPromo = {!! json_encode(old('session.lunch.additional_data.promo_items', $lc->additional_data['promo_items'] ?? [])) !!};
        initPromoItems('lunch', lcPromo, 'NJR');
        let lcTeppan = {!! json_encode(old('session.lunch.additional_data.revenue_teppan_items', $lc->additional_data['revenue_teppan_items'] ?? [])) !!};
        initTeppanItems('lunch', lcTeppan, 'NJR');
        let lcSetMenu = {!! json_encode(old('session.lunch.additional_data.setmenu_items', $lc->additional_data['setmenu_items'] ?? [])) !!};
        initSetMenuItems('lunch', lcSetMenu, 'NJR');

        // --- DINNER INIT ---
        let dnFood = {!! json_encode(old('session.dinner.upselling_data.food', $dn->upselling_data['food'] ?? [])) !!};
        initUpselling('dinner', 'food', dnFood, 'NJR');
        let dnBev = {!! json_encode(old('session.dinner.upselling_data.beverage', $dn->upselling_data['beverage'] ?? [])) !!};
        initUpselling('dinner', 'beverage', dnBev, 'NJR');
        let dnStaff = {!! json_encode(old('session.dinner.staff_on_duty', $dn->staff_on_duty ?? [])) !!};
        initStaff('dinner', dnStaff, 'NJR');
        let dnOccasion = {!! json_encode(old('session.dinner.additional_data.occasion_items', $dn->additional_data['occasion_items'] ?? [])) !!};
        initOccasion('dinner', dnOccasion, 'NJR');
        let dnPromo = {!! json_encode(old('session.dinner.additional_data.promo_items', $dn->additional_data['promo_items'] ?? [])) !!};
        initPromoItems('dinner', dnPromo, 'NJR');
        let dnTeppan = {!! json_encode(old('session.dinner.additional_data.revenue_teppan_items', $dn->additional_data['revenue_teppan_items'] ?? [])) !!};
        initTeppanItems('dinner', dnTeppan, 'NJR');
        let dnSetMenu = {!! json_encode(old('session.dinner.additional_data.setmenu_items', $dn->additional_data['setmenu_items'] ?? [])) !!};
        initSetMenuItems('dinner', dnSetMenu, 'NJR');
    });
</script>
