{{-- FORM KHUSUS XIANG FU HAI (XFH) --}}

@php
    $bf = $details['breakfast'] ?? null;
    $lc = $details['lunch'] ?? null;
    $dn = $details['dinner'] ?? null;

    // 2. Ambil Data Master (Staff & Menu) khusus XFH
    $restoXfh = $restaurants->where('code', 'XFH')->first();

    // A. Staff List
    $myStaffList = $restoXfh ? $restoXfh->users : [];

    // B. Upselling Menu
    $myMenu = $restoXfh && isset($upsellingItems[$restoXfh->id]) ? $upsellingItems[$restoXfh->id] : collect([]);
    $foods = $myMenu->where('type', 'food');
    $beverages = $myMenu->where('type', 'beverage');

    // C. Occasion Items (Dynamic Repeater)
    $bfOccasionData = old('session.breakfast.additional_data.occasion_items', $bf->additional_data['occasion_items'] ?? []);
    $bfOccasionValue = is_array($bfOccasionData) ? json_encode($bfOccasionData) : $bfOccasionData;
    $lcOccasionData = old('session.lunch.additional_data.occasion_items', $lc->additional_data['occasion_items'] ?? []);
    $lcOccasionValue = is_array($lcOccasionData) ? json_encode($lcOccasionData) : $lcOccasionData;
    $dnOccasionData = old('session.dinner.additional_data.occasion_items', $dn->additional_data['occasion_items'] ?? []);
    $dnOccasionValue = is_array($dnOccasionData) ? json_encode($dnOccasionData) : $dnOccasionData;

    // D. Promo Items (Dynamic Repeater)
    $bfPromoData = old('session.breakfast.additional_data.promo_items', $bf->additional_data['promo_items'] ?? []);
    $bfPromoValue = is_array($bfPromoData) ? json_encode($bfPromoData) : $bfPromoData;
    $lcPromoData = old('session.lunch.additional_data.promo_items', $lc->additional_data['promo_items'] ?? []);
    $lcPromoValue = is_array($lcPromoData) ? json_encode($lcPromoData) : $lcPromoData;
    $dnPromoData = old('session.dinner.additional_data.promo_items', $dn->additional_data['promo_items'] ?? []);
    $dnPromoValue = is_array($dnPromoData) ? json_encode($dnPromoData) : $dnPromoData;

    // E. Set Menu Items (Dynamic Repeater)
    $bfSetMenuData = old('session.breakfast.additional_data.setmenu_items', $bf->additional_data['setmenu_items'] ?? []);
    $bfSetMenuValue = is_array($bfSetMenuData) ? json_encode($bfSetMenuData) : $bfSetMenuData;
    $lcSetMenuData = old('session.lunch.additional_data.setmenu_items', $lc->additional_data['setmenu_items'] ?? []);
    $lcSetMenuValue = is_array($lcSetMenuData) ? json_encode($lcSetMenuData) : $lcSetMenuData;
    $dnSetMenuData = old('session.dinner.additional_data.setmenu_items', $dn->additional_data['setmenu_items'] ?? []);
    $dnSetMenuValue = is_array($dnSetMenuData) ? json_encode($dnSetMenuData) : $dnSetMenuData;
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
            {{-- In-House --}}
            <div class="col-md-4">
                <label class="form-label small">In-House (Adult)</label>
                <input type="number" class="form-control" name="session[breakfast][cover_data][in_house_adult]"
                    value="{{ old('session.breakfast.cover_data.in_house_adult', $bf->cover_data['in_house_adult'] ?? '') }}"
                    placeholder="0">
            </div>
            <div class="col-md-4">
                <label class="form-label small">In-House (Child)</label>
                <input type="number" class="form-control" name="session[breakfast][cover_data][in_house_child]"
                    value="{{ old('session.breakfast.cover_data.in_house_child', $bf->cover_data['in_house_child'] ?? '') }}"
                    placeholder="0">
            </div>
            {{-- <div class="col-md-3 border-start"></div> Spacer --}}

            {{-- Walk-In --}}
            <div class="col-md-4">
                <label class="form-label small">Walk-In (Adult)</label>
                <input type="number" class="form-control" name="session[breakfast][cover_data][walk_in_adult]"
                    value="{{ old('session.breakfast.cover_data.walk_in_adult', $bf->cover_data['walk_in_adult'] ?? '') }}"
                    placeholder="0">
            </div>
            <div class="col-md-4">
                <label class="form-label small">Walk-In (Child)</label>
                <input type="number" class="form-control" name="session[breakfast][cover_data][walk_in_child]"
                    value="{{ old('session.breakfast.cover_data.walk_in_child', $bf->cover_data['walk_in_child'] ?? '') }}"
                    placeholder="0">
            </div>
            {{-- <div class="col-md-3 border-start"></div> Spacer --}}

            {{-- Event --}}
            <!-- <div class="col-md-4">
                <label class="form-label small">Event (Adult)</label>
                <input type="number" class="form-control" name="session[breakfast][cover_data][event_adult]"
                    value="{{ old('session.breakfast.cover_data.event_adult', $bf->cover_data['event_adult'] ?? '') }}"
                    placeholder="0">
            </div>
            <div class="col-md-4">
                <label class="form-label small">Event (Child)</label>
                <input type="number" class="form-control" name="session[breakfast][cover_data][event_child]"
                    value="{{ old('session.breakfast.cover_data.event_child', $bf->cover_data['event_child'] ?? '') }}"
                    placeholder="0">
            </div> -->
        </div>

        <hr>

        {{-- 2. OCCASION / EVENT TYPE (Dynamic Repeater) --}}
        <h6 class="fw-bold text-muted mt-3">2. Occasion / Event Type</h6>
        <div class="row g-3">
            <div class="col-md-12">
                <input type="hidden" id="input-occasion-breakfast-XFH" name="session[breakfast][additional_data][occasion_items]" value="{{ $bfOccasionValue ?? '[]' }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="occasion-type-breakfast-XFH" onchange="toggleOccasionOther('breakfast', 'XFH')">
                        <option value="" selected>Select Occasion...</option>
                        <option value="Wedding Party">Wedding Party</option>
                        <option value="Birthday Party">Birthday Party</option>
                        <option value="Social Event">Social Event</option>
                        <option value="Corporate Event">Corporate Event</option>
                        <option value="Other">Other</option>
                    </select>
                    <input type="text" class="form-control form-control-sm d-none" id="occasion-other-breakfast-XFH" placeholder="Occasion Type" style="max-width: 150px;">
                    <input type="text" class="form-control form-control-sm" id="occasion-name-breakfast-XFH"
                        placeholder="Name (e.g. Mr. Budi)">
                    <input type="number" class="form-control form-control-sm" id="occasion-pax-breakfast-XFH"
                        placeholder="Pax" style="max-width: 80px;">
                    <input type="text" class="form-control form-control-sm rupiah" id="occasion-revenue-breakfast-XFH"
                        placeholder="Revenue" style="max-width: 120px;" autocomplete="off">
                    <button class="btn btn-sm btn-dark" type="button" onclick="addOccasionItem('breakfast', 'XFH')">
                        <i class="ti ti-plus"></i> Add
                    </button>
                </div>
                <ul class="list-group small" id="list-occasion-breakfast-XFH"></ul>
            </div>
        </div>

        <hr>

        {{-- 3. PROMO (Dynamic Repeater) --}}
        <h6 class="fw-bold text-muted mt-3">3. Promo</h6>
        <div class="row g-3">
            <div class="col-md-12">
                <input type="hidden" id="input-promo-breakfast-XFH" name="session[breakfast][additional_data][promo_items]" value="{{ $bfPromoValue ?? '[]' }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="promo-type-breakfast-XFH" onchange="togglePromoOther('breakfast', 'XFH')">
                        <option value="" selected>Select Promo...</option>
                        <option value="Mandiri Card">Mandiri Card</option>
                        <option value="BCA Card">BCA Card</option>
                        <option value="Membership">Membership</option>
                        <option value="Other">Other</option>
                    </select>
                    <input type="text" class="form-control form-control-sm d-none" id="promo-other-breakfast-XFH" placeholder="Promo Name" style="max-width: 150px;">
                    <input type="number" class="form-control form-control-sm" id="promo-pax-breakfast-XFH"
                        placeholder="Pax" style="max-width: 80px;">
                    <input type="text" class="form-control form-control-sm rupiah" id="promo-revenue-breakfast-XFH"
                        placeholder="Revenue" style="max-width: 120px;" autocomplete="off">
                    <button class="btn btn-sm btn-dark" type="button" onclick="addPromoItem('breakfast', 'XFH')">
                        <i class="ti ti-plus"></i> Add
                    </button>
                </div>
                <ul class="list-group small" id="list-promo-breakfast-XFH"></ul>
            </div>
        </div>

        <hr>

        <h6 class="fw-bold text-muted mt-3">4. Set Menu &amp; AYCE</h6>
        <div class="row g-3">
            <div class="col-md-12">
                <input type="hidden" id="input-setmenu-breakfast-XFH" name="session[breakfast][additional_data][setmenu_items]" value="{{ $bfSetMenuValue ?? '[]' }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="setmenu-type-breakfast-XFH" >
                        <option value="" selected>Select Set Menu / AYCE...</option>
                        <option value="Set Menu Family 8000">Set Menu Family 8000</option>
                        <option value="Set Menu Family 5000">Set Menu Family 5000</option>
                        <option value="Set Menu Family 6000">Set Menu Family 6000</option>
                        <option value="AYCE Dimsum">AYCE Dimsum</option>
                        <option value="SET MENU 788">SET MENU 788</option>
                        <option value="SET MENU 988">SET MENU 988</option>
                        <option value="SET MENU 1188">SET MENU 1188</option>
                    </select>
                    <input type="number" class="form-control form-control-sm" id="setmenu-pax-breakfast-XFH"
                        placeholder="Pax" style="max-width: 80px;">
                    <input type="text" class="form-control form-control-sm rupiah" id="setmenu-revenue-breakfast-XFH"
                        placeholder="Revenue" style="max-width: 120px;" autocomplete="off">
                    <button class="btn btn-sm btn-dark" type="button" onclick="addSetMenuItem('breakfast', 'XFH')">
                        <i class="ti ti-plus"></i> Add
                    </button>
                </div>
                <ul class="list-group small" id="list-setmenu-breakfast-XFH"></ul>
            </div>
        </div>

        <hr>

        {{-- 5. REVENUE REPORT --}}
        <h6 class="fw-bold text-muted mt-3">5. Revenue Report (IDR)</h6>
        <div class="row g-3">
            <div class="col-md-3"><label class="small">Food</label><input type="text"
                    class="form-control rupiah" name="session[breakfast][revenue_food]"
                    value="{{ old('session.breakfast.revenue_food', isset($bf->revenue_food) ? number_format($bf->revenue_food, 0, ',', '.') : '') }}"
                    autocomplete="off" placeholder="0">
            </div>
            <div class="col-md-3"><label class="small">Beverage</label><input type="text"
                    class="form-control rupiah" name="session[breakfast][revenue_beverage]"
                    value="{{ old('session.breakfast.revenue_beverage', isset($bf->revenue_beverage) ? number_format($bf->revenue_beverage, 0, ',', '.') : '') }}"
                    autocomplete="off" placeholder="0">
            </div>
            <div class="col-md-3"><label class="small">Others</label><input type="text"
                    class="form-control rupiah" name="session[breakfast][revenue_others]"
                    value="{{ old('session.breakfast.revenue_others', isset($bf->revenue_others) ? number_format($bf->revenue_others, 0, ',', '.') : '') }}"
                    autocomplete="off" placeholder="0">
            </div>
            <div class="col-md-3"><label class="small">Event</label><input type="text"
                    class="form-control rupiah" name="session[breakfast][revenue_event]"
                    value="{{ old('session.breakfast.revenue_event', isset($bf->revenue_event) ? number_format($bf->revenue_event, 0, ',', '.') : '') }}"
                    autocomplete="off" placeholder="0">
            </div>
        </div>

        <hr>

        {{-- 6. UPSELLING & REMARKS --}}
        <h6 class="fw-bold text-muted mt-3">6. Upselling & Remarks</h6>
        <div class="row g-3">
            {{-- Food --}}
            <div class="col-md-6">
                <label class="form-label small fw-bold">Upselling Menu (Food)</label>
                @php $bfFoodVal = old('session.breakfast.upselling_data.food', $bf->upselling_data['food'] ?? []); @endphp
                <input type="hidden" id="input-breakfast-food-XFH" name="session[breakfast][upselling_data][food]"
                    value="{{ is_array($bfFoodVal) ? json_encode($bfFoodVal) : $bfFoodVal }}">
                <div class="input-group mb-2">
                    <input type="text" class="form-control form-control-sm" id="select-breakfast-food-XFH" placeholder="Enter food name...">
                    <input type="number" class="form-control form-control-sm" id="pax-breakfast-food-XFH"
                        placeholder="Qty" style="max-width: 70px;">
                    <button class="btn btn-sm btn-dark" type="button"
                        onclick="addUpsellingItem('breakfast', 'food', 'XFH')"><i class="ti ti-plus"></i> Add</button>
                </div>
                <ul class="list-group small" id="list-breakfast-food-XFH"></ul>
            </div>
            {{-- Bev --}}
            <div class="col-md-6">
                <label class="form-label small fw-bold">Beverage Upselling</label>
                @php $bfBevVal = old('session.breakfast.upselling_data.beverage', $bf->upselling_data['beverage'] ?? []); @endphp
                <input type="hidden" id="input-breakfast-beverage-XFH" name="session[breakfast][upselling_data][beverage]"
                    value="{{ is_array($bfBevVal) ? json_encode($bfBevVal) : $bfBevVal }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="select-breakfast-beverage-XFH">
                        <option value="" selected>Select Drink...</option>
                        @foreach ($beverages as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                    <input type="number" class="form-control form-control-sm" id="pax-breakfast-beverage-XFH"
                        placeholder="Qty" style="max-width: 70px;">
                    <button class="btn btn-sm btn-dark" type="button"
                        onclick="addUpsellingItem('breakfast', 'beverage', 'XFH')"><i class="ti ti-plus"></i> Add</button>
                </div>
                <ul class="list-group small" id="list-breakfast-beverage-XFH"></ul>
            </div>
            {{-- Remarks --}}
            <div class="col-md-12">
                <label class="form-label small">General Remarks</label>
                <textarea class="form-control" name="session[breakfast][remarks]">{{ old('session.breakfast.remarks', $bf->remarks ?? '') }}</textarea>
            </div>
            {{-- Staff --}}
            <div class="col-md-12 mt-3">
                <label class="form-label small fw-bold">Staff on Duty</label>
                @php $bfStaffVal = old('session.breakfast.staff_on_duty', $bf->staff_on_duty ?? []); @endphp
                <input type="hidden" id="input-staff-breakfast-XFH" name="session[breakfast][staff_on_duty]"
                    value="{{ is_array($bfStaffVal) ? json_encode($bfStaffVal) : $bfStaffVal }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="select-staff-breakfast-XFH">
                        <option value="" selected>Select Staff...</option>
                        @foreach ($myStaffList as $staff)
                            <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                        @endforeach
                    </select>
                    <button class="btn btn-sm btn-dark" type="button" onclick="addStaffItem('breakfast', 'XFH')"><i
                            class="ti ti-plus"></i> Add</button>
                </div>
                <div id="list-staff-breakfast-XFH" class="d-flex flex-wrap"></div>
            </div>
        </div>

        <hr>

        {{-- 7. COMPETITOR --}}
        <h6 class="fw-bold text-muted mt-3">7. Competitor Comparison</h6>
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

        {{-- 2. OCCASION / EVENT TYPE (Dynamic Repeater) --}}
        <h6 class="fw-bold text-muted mt-3">2. Occasion / Event Type</h6>
        <div class="row g-3">
            <div class="col-md-12">
                <input type="hidden" id="input-occasion-lunch-XFH" name="session[lunch][additional_data][occasion_items]" value="{{ $lcOccasionValue ?? '[]' }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="occasion-type-lunch-XFH" onchange="toggleOccasionOther('lunch', 'XFH')">
                        <option value="" selected>Select Occasion...</option>
                        <option value="Wedding Party">Wedding Party</option>
                        <option value="Birthday Party">Birthday Party</option>
                        <option value="Social Event">Social Event</option>
                        <option value="Corporate Event">Corporate Event</option>
                        <option value="Other">Other</option>
                    </select>
                    <input type="text" class="form-control form-control-sm d-none" id="occasion-other-lunch-XFH" placeholder="Occasion Type" style="max-width: 150px;">
                    <input type="text" class="form-control form-control-sm" id="occasion-name-lunch-XFH"
                        placeholder="Name (e.g. Mr. Budi)">
                    <input type="number" class="form-control form-control-sm" id="occasion-pax-lunch-XFH"
                        placeholder="Pax" style="max-width: 80px;">
                    <input type="text" class="form-control form-control-sm rupiah" id="occasion-revenue-lunch-XFH"
                        placeholder="Revenue" style="max-width: 120px;" autocomplete="off">
                    <button class="btn btn-sm btn-dark" type="button" onclick="addOccasionItem('lunch', 'XFH')">
                        <i class="ti ti-plus"></i> Add
                    </button>
                </div>
                <ul class="list-group small" id="list-occasion-lunch-XFH"></ul>
            </div>
        </div>

        <hr>

        {{-- 3. PROMO (Dynamic Repeater) --}}
        <h6 class="fw-bold text-muted mt-3">3. Promo</h6>
        <div class="row g-3">
            <div class="col-md-12">
                <input type="hidden" id="input-promo-lunch-XFH" name="session[lunch][additional_data][promo_items]" value="{{ $lcPromoValue ?? '[]' }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="promo-type-lunch-XFH" onchange="togglePromoOther('lunch', 'XFH')">
                        <option value="" selected>Select Promo...</option>
                        <option value="Mandiri Card">Mandiri Card</option>
                        <option value="BCA Card">BCA Card</option>
                        <option value="Membership">Membership</option>
                        <option value="Other">Other</option>
                    </select>
                    <input type="text" class="form-control form-control-sm d-none" id="promo-other-lunch-XFH" placeholder="Promo Name" style="max-width: 150px;">
                    <input type="number" class="form-control form-control-sm" id="promo-pax-lunch-XFH"
                        placeholder="Pax" style="max-width: 80px;">
                    <input type="text" class="form-control form-control-sm rupiah" id="promo-revenue-lunch-XFH"
                        placeholder="Revenue" style="max-width: 120px;" autocomplete="off">
                    <button class="btn btn-sm btn-dark" type="button" onclick="addPromoItem('lunch', 'XFH')">
                        <i class="ti ti-plus"></i> Add
                    </button>
                </div>
                <ul class="list-group small" id="list-promo-lunch-XFH"></ul>
            </div>
        </div>

        <hr>

        <h6 class="fw-bold text-muted mt-3">4. Set Menu &amp; AYCE</h6>
        <div class="row g-3">
            <div class="col-md-12">
                <input type="hidden" id="input-setmenu-lunch-XFH" name="session[lunch][additional_data][setmenu_items]" value="{{ $lcSetMenuValue ?? '[]' }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="setmenu-type-lunch-XFH" >
                        <option value="" selected>Select Set Menu / AYCE...</option>
                        <option value="Set Menu Family 8000">Set Menu Family 8000</option>
                        <option value="Set Menu Family 5000">Set Menu Family 5000</option>
                        <option value="Set Menu Family 6000">Set Menu Family 6000</option>
                        <option value="AYCE Dimsum">AYCE Dimsum</option>
                        <option value="SET MENU 788">SET MENU 788</option>
                        <option value="SET MENU 988">SET MENU 988</option>
                        <option value="SET MENU 1188">SET MENU 1188</option>
                    </select>
                    <input type="number" class="form-control form-control-sm" id="setmenu-pax-lunch-XFH"
                        placeholder="Pax" style="max-width: 80px;">
                    <input type="text" class="form-control form-control-sm rupiah" id="setmenu-revenue-lunch-XFH"
                        placeholder="Revenue" style="max-width: 120px;" autocomplete="off">
                    <button class="btn btn-sm btn-dark" type="button" onclick="addSetMenuItem('lunch', 'XFH')">
                        <i class="ti ti-plus"></i> Add
                    </button>
                </div>
                <ul class="list-group small" id="list-setmenu-lunch-XFH"></ul>
            </div>
        </div>

        <hr>

        <h6 class="fw-bold text-muted mt-3">5. Revenue Report (IDR)</h6>
        <div class="row g-3">
            <div class="col-md-3"><label class="small">Food</label><input type="text"
                    class="form-control rupiah" name="session[lunch][revenue_food]"
                    value="{{ old('session.lunch.revenue_food', isset($lc->revenue_food) ? number_format($lc->revenue_food, 0, ',', '.') : '') }}"
                    autocomplete="off" placeholder="0">
            </div>
            <div class="col-md-3"><label class="small">Beverage</label><input type="text"
                    class="form-control rupiah" name="session[lunch][revenue_beverage]"
                    value="{{ old('session.lunch.revenue_beverage', isset($lc->revenue_beverage) ? number_format($lc->revenue_beverage, 0, ',', '.') : '') }}"
                    autocomplete="off" placeholder="0">
            </div>
            <div class="col-md-3"><label class="small">Others</label><input type="text"
                    class="form-control rupiah" name="session[lunch][revenue_others]"
                    value="{{ old('session.lunch.revenue_others', isset($lc->revenue_others) ? number_format($lc->revenue_others, 0, ',', '.') : '') }}"
                    autocomplete="off" placeholder="0">
            </div>
            <div class="col-md-3"><label class="small">Event</label><input type="text"
                    class="form-control rupiah" name="session[lunch][revenue_event]"
                    value="{{ old('session.lunch.revenue_event', isset($lc->revenue_event) ? number_format($lc->revenue_event, 0, ',', '.') : '') }}"
                    autocomplete="off" placeholder="0">
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
                <input type="hidden" id="input-lunch-food-XFH" name="session[lunch][upselling_data][food]"
                    value="{{ is_array($lcFoodVal) ? json_encode($lcFoodVal) : $lcFoodVal }}">
                <div class="input-group mb-2">
                    <input type="text" class="form-control form-control-sm" id="select-lunch-food-XFH" placeholder="Enter food name...">
                    <input type="number" class="form-control form-control-sm" id="pax-lunch-food-XFH"
                        placeholder="Qty" style="max-width: 70px;">
                    <button class="btn btn-sm btn-dark" type="button"
                        onclick="addUpsellingItem('lunch', 'food', 'XFH')"><i class="ti ti-plus"></i> Add</button>
                </div>
                <ul class="list-group small" id="list-lunch-food-XFH"></ul>
            </div>
            {{-- Bev --}}
            <div class="col-md-6">
                <label class="form-label small fw-bold">Beverage Upselling</label>
                @php $lcBevVal = old('session.lunch.upselling_data.beverage', $lc->upselling_data['beverage'] ?? []); @endphp
                <input type="hidden" id="input-lunch-beverage-XFH" name="session[lunch][upselling_data][beverage]"
                    value="{{ is_array($lcBevVal) ? json_encode($lcBevVal) : $lcBevVal }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="select-lunch-beverage-XFH">
                        <option value="" selected>Select Drink...</option>
                        @foreach ($beverages as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                    <input type="number" class="form-control form-control-sm" id="pax-lunch-beverage-XFH"
                        placeholder="Qty" style="max-width: 70px;">
                    <button class="btn btn-sm btn-dark" type="button"
                        onclick="addUpsellingItem('lunch', 'beverage', 'XFH')"><i class="ti ti-plus"></i> Add</button>
                </div>
                <ul class="list-group small" id="list-lunch-beverage-XFH"></ul>
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
                <input type="hidden" id="input-staff-lunch-XFH" name="session[lunch][staff_on_duty]"
                    value="{{ is_array($lcStaffVal) ? json_encode($lcStaffVal) : $lcStaffVal }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="select-staff-lunch-XFH">
                        <option value="" selected>Select Staff...</option>
                        @foreach ($myStaffList as $staff)
                            <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                        @endforeach
                    </select>
                    <button class="btn btn-sm btn-dark" type="button" onclick="addStaffItem('lunch', 'XFH')"><i
                            class="ti ti-plus"></i> Add</button>
                </div>
                <div id="list-staff-lunch-XFH" class="d-flex flex-wrap"></div>
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
                <input type="hidden" id="input-occasion-dinner-XFH" name="session[dinner][additional_data][occasion_items]" value="{{ $dnOccasionValue ?? '[]' }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="occasion-type-dinner-XFH" onchange="toggleOccasionOther('dinner', 'XFH')">
                        <option value="" selected>Select Occasion...</option>
                        <option value="Wedding Party">Wedding Party</option>
                        <option value="Birthday Party">Birthday Party</option>
                        <option value="Social Event">Social Event</option>
                        <option value="Corporate Event">Corporate Event</option>
                        <option value="Other">Other</option>
                    </select>
                    <input type="text" class="form-control form-control-sm d-none" id="occasion-other-dinner-XFH" placeholder="Occasion Type" style="max-width: 150px;">
                    <input type="text" class="form-control form-control-sm" id="occasion-name-dinner-XFH"
                        placeholder="Name (e.g. Mr. Budi)">
                    <input type="number" class="form-control form-control-sm" id="occasion-pax-dinner-XFH"
                        placeholder="Pax" style="max-width: 80px;">
                    <input type="text" class="form-control form-control-sm rupiah" id="occasion-revenue-dinner-XFH"
                        placeholder="Revenue" style="max-width: 120px;" autocomplete="off">
                    <button class="btn btn-sm btn-dark" type="button" onclick="addOccasionItem('dinner', 'XFH')">
                        <i class="ti ti-plus"></i> Add
                    </button>
                </div>
                <ul class="list-group small" id="list-occasion-dinner-XFH"></ul>
            </div>
        </div>

        <hr>

        <h6 class="fw-bold text-muted mt-3">3. Promo</h6>
        <div class="row g-3">
            <div class="col-md-12">
                <input type="hidden" id="input-promo-dinner-XFH" name="session[dinner][additional_data][promo_items]" value="{{ $dnPromoValue ?? '[]' }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="promo-type-dinner-XFH" onchange="togglePromoOther('dinner', 'XFH')">
                        <option value="" selected>Select Promo...</option>
                        <option value="Mandiri Card">Mandiri Card</option>
                        <option value="BCA Card">BCA Card</option>
                        <option value="Membership">Membership</option>
                        <option value="Other">Other</option>
                    </select>
                    <input type="text" class="form-control form-control-sm d-none" id="promo-other-dinner-XFH" placeholder="Promo Name" style="max-width: 150px;">
                    <input type="number" class="form-control form-control-sm" id="promo-pax-dinner-XFH"
                        placeholder="Pax" style="max-width: 80px;">
                    <input type="text" class="form-control form-control-sm rupiah" id="promo-revenue-dinner-XFH"
                        placeholder="Revenue" style="max-width: 120px;" autocomplete="off">
                    <button class="btn btn-sm btn-dark" type="button" onclick="addPromoItem('dinner', 'XFH')">
                        <i class="ti ti-plus"></i> Add
                    </button>
                </div>
                <ul class="list-group small" id="list-promo-dinner-XFH"></ul>
            </div>
        </div>

        <hr>

        <h6 class="fw-bold text-muted mt-3">4. Set Menu &amp; AYCE</h6>
        <div class="row g-3">
            <div class="col-md-12">
                <input type="hidden" id="input-setmenu-dinner-XFH" name="session[dinner][additional_data][setmenu_items]" value="{{ $dnSetMenuValue ?? '[]' }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="setmenu-type-dinner-XFH" >
                        <option value="" selected>Select Set Menu / AYCE...</option>
                        <option value="Set Menu Family 8000">Set Menu Family 8000</option>
                        <option value="Set Menu Family 5000">Set Menu Family 5000</option>
                        <option value="Set Menu Family 6000">Set Menu Family 6000</option>
                        <option value="AYCE Dimsum">AYCE Dimsum</option>
                        <option value="SET MENU 788">SET MENU 788</option>
                        <option value="SET MENU 988">SET MENU 988</option>
                        <option value="SET MENU 1188">SET MENU 1188</option>
                    </select>
                    <input type="number" class="form-control form-control-sm" id="setmenu-pax-dinner-XFH"
                        placeholder="Pax" style="max-width: 80px;">
                    <input type="text" class="form-control form-control-sm rupiah" id="setmenu-revenue-dinner-XFH"
                        placeholder="Revenue" style="max-width: 120px;" autocomplete="off">
                    <button class="btn btn-sm btn-dark" type="button" onclick="addSetMenuItem('dinner', 'XFH')">
                        <i class="ti ti-plus"></i> Add
                    </button>
                </div>
                <ul class="list-group small" id="list-setmenu-dinner-XFH"></ul>
            </div>
        </div>

        <hr>

        <h6 class="fw-bold text-muted mt-3">5. Revenue Report (IDR)</h6>
        <div class="row g-3">
            <div class="col-md-3"><label class="small">Food</label><input type="text"
                    class="form-control rupiah" name="session[dinner][revenue_food]"
                    value="{{ old('session.dinner.revenue_food', isset($dn->revenue_food) ? number_format($dn->revenue_food, 0, ',', '.') : '') }}"
                    autocomplete="off" placeholder="0">
            </div>
            <div class="col-md-3"><label class="small">Beverage</label><input type="text"
                    class="form-control rupiah" name="session[dinner][revenue_beverage]"
                    value="{{ old('session.dinner.revenue_beverage', isset($dn->revenue_beverage) ? number_format($dn->revenue_beverage, 0, ',', '.') : '') }}"
                    autocomplete="off" placeholder="0">
            </div>
            <div class="col-md-3"><label class="small">Others</label><input type="text"
                    class="form-control rupiah" name="session[dinner][revenue_others]"
                    value="{{ old('session.dinner.revenue_others', isset($dn->revenue_others) ? number_format($dn->revenue_others, 0, ',', '.') : '') }}"
                    autocomplete="off" placeholder="0">
            </div>
            <div class="col-md-3"><label class="small">Event</label><input type="text"
                    class="form-control rupiah" name="session[dinner][revenue_event]"
                    value="{{ old('session.dinner.revenue_event', isset($dn->revenue_event) ? number_format($dn->revenue_event, 0, ',', '.') : '') }}"
                    autocomplete="off" placeholder="0">
            </div>
        </div>

        <hr>

        <h6 class="fw-bold text-muted mt-3">6. Upselling & Remarks</h6>
        <div class="row g-3">
            {{-- Food --}}
            <div class="col-md-6">
                <label class="form-label small fw-bold">Upselling Menu (Food)</label>
                @php $dnFoodVal = old('session.dinner.upselling_data.food', $dn->upselling_data['food'] ?? []); @endphp
                <input type="hidden" id="input-dinner-food-XFH" name="session[dinner][upselling_data][food]"
                    value="{{ is_array($dnFoodVal) ? json_encode($dnFoodVal) : $dnFoodVal }}">
                <div class="input-group mb-2">
                    <input type="text" class="form-control form-control-sm" id="select-dinner-food-XFH" placeholder="Enter food name...">
                    <input type="number" class="form-control form-control-sm" id="pax-dinner-food-XFH"
                        placeholder="Qty" style="max-width: 70px;">
                    <button class="btn btn-sm btn-dark" type="button"
                        onclick="addUpsellingItem('dinner', 'food', 'XFH')"><i class="ti ti-plus"></i> Add</button>
                </div>
                <ul class="list-group small" id="list-dinner-food-XFH"></ul>
            </div>
            {{-- Bev --}}
            <div class="col-md-6">
                <label class="form-label small fw-bold">Beverage Upselling</label>
                @php $dnBevVal = old('session.dinner.upselling_data.beverage', $dn->upselling_data['beverage'] ?? []); @endphp
                <input type="hidden" id="input-dinner-beverage-XFH" name="session[dinner][upselling_data][beverage]"
                    value="{{ is_array($dnBevVal) ? json_encode($dnBevVal) : $dnBevVal }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="select-dinner-beverage-XFH">
                        <option value="" selected>Select Drink...</option>
                        @foreach ($beverages as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                    <input type="number" class="form-control form-control-sm" id="pax-dinner-beverage-XFH"
                        placeholder="Qty" style="max-width: 70px;">
                    <button class="btn btn-sm btn-dark" type="button"
                        onclick="addUpsellingItem('dinner', 'beverage', 'XFH')"><i class="ti ti-plus"></i>
                        Add</button>
                </div>
                <ul class="list-group small" id="list-dinner-beverage-XFH"></ul>
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
                <input type="hidden" id="input-staff-dinner-XFH" name="session[dinner][staff_on_duty]"
                    value="{{ is_array($dnStaffVal) ? json_encode($dnStaffVal) : $dnStaffVal }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="select-staff-dinner-XFH">
                        <option value="" selected>Select Staff...</option>
                        @foreach ($myStaffList as $staff)
                            <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                        @endforeach
                    </select>
                    <button class="btn btn-sm btn-dark" type="button" onclick="addStaffItem('dinner', 'XFH')"><i
                            class="ti ti-plus"></i> Add</button>
                </div>
                <div id="list-staff-dinner-XFH" class="d-flex flex-wrap"></div>
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
    var setmenuState = window.setmenuState || {};

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

        if (!type) { alert('Please select a set menu / AYCE type.'); return; }
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

        // --- BREAKFAST INIT ---
        let bfFood = {!! json_encode(old('session.breakfast.upselling_data.food', $bf->upselling_data['food'] ?? [])) !!};
        initUpselling('breakfast', 'food', bfFood, 'XFH');
        let bfBev = {!! json_encode(old('session.breakfast.upselling_data.beverage', $bf->upselling_data['beverage'] ?? [])) !!};
        initUpselling('breakfast', 'beverage', bfBev, 'XFH');
        let bfStaff = {!! json_encode(old('session.breakfast.staff_on_duty', $bf->staff_on_duty ?? [])) !!};
        initStaff('breakfast', bfStaff, 'XFH');
        let bfOccasion = {!! json_encode(old('session.breakfast.additional_data.occasion_items', $bf->additional_data['occasion_items'] ?? [])) !!};
        initOccasion('breakfast', bfOccasion, 'XFH');
        let bfPromo = {!! json_encode(old('session.breakfast.additional_data.promo_items', $bf->additional_data['promo_items'] ?? [])) !!};
        initPromoItems('breakfast', bfPromo, 'XFH');
        let bfSetMenu = {!! json_encode(old('session.breakfast.additional_data.setmenu_items', $bf->additional_data['setmenu_items'] ?? [])) !!};
        initSetMenuItems('breakfast', bfSetMenu, 'XFH');

        // --- LUNCH INIT ---
        let lcFood = {!! json_encode(old('session.lunch.upselling_data.food', $lc->upselling_data['food'] ?? [])) !!};
        initUpselling('lunch', 'food', lcFood, 'XFH');
        let lcBev = {!! json_encode(old('session.lunch.upselling_data.beverage', $lc->upselling_data['beverage'] ?? [])) !!};
        initUpselling('lunch', 'beverage', lcBev, 'XFH');
        let lcStaff = {!! json_encode(old('session.lunch.staff_on_duty', $lc->staff_on_duty ?? [])) !!};
        initStaff('lunch', lcStaff, 'XFH');
        let lcOccasion = {!! json_encode(old('session.lunch.additional_data.occasion_items', $lc->additional_data['occasion_items'] ?? [])) !!};
        initOccasion('lunch', lcOccasion, 'XFH');
        let lcPromo = {!! json_encode(old('session.lunch.additional_data.promo_items', $lc->additional_data['promo_items'] ?? [])) !!};
        initPromoItems('lunch', lcPromo, 'XFH');
        let lcSetMenu = {!! json_encode(old('session.lunch.additional_data.setmenu_items', $lc->additional_data['setmenu_items'] ?? [])) !!};
        initSetMenuItems('lunch', lcSetMenu, 'XFH');

        // --- DINNER INIT ---
        let dnFood = {!! json_encode(old('session.dinner.upselling_data.food', $dn->upselling_data['food'] ?? [])) !!};
        initUpselling('dinner', 'food', dnFood, 'XFH');
        let dnBev = {!! json_encode(old('session.dinner.upselling_data.beverage', $dn->upselling_data['beverage'] ?? [])) !!};
        initUpselling('dinner', 'beverage', dnBev, 'XFH');
        let dnStaff = {!! json_encode(old('session.dinner.staff_on_duty', $dn->staff_on_duty ?? [])) !!};
        initStaff('dinner', dnStaff, 'XFH');
        let dnOccasion = {!! json_encode(old('session.dinner.additional_data.occasion_items', $dn->additional_data['occasion_items'] ?? [])) !!};
        initOccasion('dinner', dnOccasion, 'XFH');
        let dnPromo = {!! json_encode(old('session.dinner.additional_data.promo_items', $dn->additional_data['promo_items'] ?? [])) !!};
        initPromoItems('dinner', dnPromo, 'XFH');
        let dnSetMenu = {!! json_encode(old('session.dinner.additional_data.setmenu_items', $dn->additional_data['setmenu_items'] ?? [])) !!};
        initSetMenuItems('dinner', dnSetMenu, 'XFH');

    });
</script>
