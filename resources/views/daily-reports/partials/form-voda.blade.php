{{-- FORM KHUSUS VODA BISTRO (VDA) --}}

@php
    $bf = $details['breakfast'] ?? null;
    $lc = $details['lunch'] ?? null;
    $dn = $details['dinner'] ?? null;

    $restoVda = $restaurants->where('code', 'VODA')->first();
    $myStaffList = $restoVda ? $restoVda->users : [];

    $myMenu = $restoVda && isset($upsellingItems[$restoVda->id]) ? $upsellingItems[$restoVda->id] : collect([]);
    $foods = $myMenu->where('type', 'food');
    $beverages = $myMenu->where('type', 'beverage');

    $bfOccasionData = old('session.breakfast.additional_data.occasion_items', $bf->additional_data['occasion_items'] ?? []);
    $bfOccasionValue = is_array($bfOccasionData) ? json_encode($bfOccasionData) : $bfOccasionData;
    $lcOccasionData = old('session.lunch.additional_data.occasion_items', $lc->additional_data['occasion_items'] ?? []);
    $lcOccasionValue = is_array($lcOccasionData) ? json_encode($lcOccasionData) : $lcOccasionData;
    $dnOccasionData = old('session.dinner.additional_data.occasion_items', $dn->additional_data['occasion_items'] ?? []);
    $dnOccasionValue = is_array($dnOccasionData) ? json_encode($dnOccasionData) : $dnOccasionData;

    $bfPromoData = old('session.breakfast.additional_data.promo_items', $bf->additional_data['promo_items'] ?? []);
    $bfPromoValue = is_array($bfPromoData) ? json_encode($bfPromoData) : $bfPromoData;
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
            <div class="col-md-4"><label class="form-label small">Event (Adult)</label><input type="number"
                    class="form-control" name="session[lunch][cover_data][event_adult]"
                    value="{{ old('session.lunch.cover_data.event_adult', $lc->cover_data['event_adult'] ?? '') }}"
                    placeholder="0">
            </div>
            <div class="col-md-4"><label class="form-label small">Event (Child)</label><input type="number"
                    class="form-control" name="session[lunch][cover_data][event_child]"
                    value="{{ old('session.lunch.cover_data.event_child', $lc->cover_data['event_child'] ?? '') }}"
                    placeholder="0">
            </div>
        </div>

        <hr>

        <h6 class="fw-bold text-muted mt-3">2. Occasion / Event Type</h6>
        <div class="row g-3">
            <div class="col-md-12">
                <input type="hidden" id="input-occasion-lunch-VODA" name="session[lunch][additional_data][occasion_items]" value="{{ $lcOccasionValue ?? '[]' }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="occasion-type-lunch-VODA">
                        <option value="" selected>Select Occasion...</option>
                        <option value="Wedding Party">Wedding Party</option>
                        <option value="Birthday Party">Birthday Party</option>
                        <option value="Social Event">Social Event</option>
                    </select>
                    <input type="text" class="form-control form-control-sm" id="occasion-name-lunch-VODA"
                        placeholder="Name (e.g. Mr. Budi)">
                    <input type="number" class="form-control form-control-sm" id="occasion-pax-lunch-VODA"
                        placeholder="Pax" style="max-width: 80px;">
                    <input type="text" class="form-control form-control-sm rupiah" id="occasion-revenue-lunch-VODA"
                        placeholder="Revenue" style="max-width: 120px;" autocomplete="off">
                    <button class="btn btn-sm btn-dark" type="button" onclick="addOccasionItem('lunch', 'VODA')">
                        <i class="ti ti-plus"></i> Add
                    </button>
                </div>
                <ul class="list-group small" id="list-occasion-lunch-VODA"></ul>
            </div>
        </div>

        <hr>

        <h6 class="fw-bold text-muted mt-3">3. Promo</h6>
        <div class="row g-3">
            <div class="col-md-12">
                <input type="hidden" id="input-promo-lunch-VODA" name="session[lunch][additional_data][promo_items]" value="{{ $lcPromoValue ?? '[]' }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="promo-type-lunch-VODA">
                        <option value="" selected>Select Promo...</option>
                        <option value="Mandiri Card">Mandiri Card</option>
                        <option value="BCA Card">BCA Card</option>
                        <option value="Membership">Membership</option>
                    </select>
                    <input type="number" class="form-control form-control-sm" id="promo-pax-lunch-VODA"
                        placeholder="Pax" style="max-width: 80px;">
                    <input type="text" class="form-control form-control-sm rupiah" id="promo-revenue-lunch-VODA"
                        placeholder="Revenue" style="max-width: 120px;" autocomplete="off">
                    <button class="btn btn-sm btn-dark" type="button" onclick="addPromoItem('lunch', 'VODA')">
                        <i class="ti ti-plus"></i> Add
                    </button>
                </div>
                <ul class="list-group small" id="list-promo-lunch-VODA"></ul>
            </div>
        </div>

        <hr>

        <h6 class="fw-bold text-muted mt-3">4. Revenue Report (IDR)</h6>
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
            <div class="col-md-3"><label class="small">Others</label><input type="text" class="form-control rupiah"
                    name="session[lunch][revenue_others]"
                    value="{{ old('session.lunch.revenue_others', isset($lc->revenue_others) ? number_format($lc->revenue_others, 0, ',', '.') : '') }}"
                    placeholder="0" autocomplete="off">
            </div>
            <div class="col-md-3"><label class="small">Event</label><input type="text" class="form-control rupiah"
                    name="session[lunch][revenue_event]"
                    value="{{ old('session.lunch.revenue_event', isset($lc->revenue_event) ? number_format($lc->revenue_event, 0, ',', '.') : '') }}"
                    placeholder="0" autocomplete="off">
            </div>
        </div>

        <hr>

        <h6 class="fw-bold text-muted mt-3">5. Upselling & Remarks</h6>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label small fw-bold">Upselling Menu (Food)</label>
                @php $lcFoodVal = old('session.lunch.upselling_data.food', $lc->upselling_data['food'] ?? []); @endphp
                <input type="hidden" id="input-lunch-food-VODA" name="session[lunch][upselling_data][food]"
                    value="{{ is_array($lcFoodVal) ? json_encode($lcFoodVal) : $lcFoodVal }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="select-lunch-food-VODA">
                        <option value="" selected>Select Food...</option>
                        @foreach ($foods as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                    <input type="number" class="form-control form-control-sm" id="pax-lunch-food-VODA"
                        placeholder="Qty" style="max-width: 70px;">
                    <button class="btn btn-sm btn-dark" type="button"
                        onclick="addUpsellingItem('lunch', 'food', 'VODA')"><i class="ti ti-plus"></i> Add</button>
                </div>
                <ul class="list-group small" id="list-lunch-food-VODA"></ul>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-bold">Beverage Upselling</label>
                @php $lcBevVal = old('session.lunch.upselling_data.beverage', $lc->upselling_data['beverage'] ?? []); @endphp
                <input type="hidden" id="input-lunch-beverage-VODA" name="session[lunch][upselling_data][beverage]"
                    value="{{ is_array($lcBevVal) ? json_encode($lcBevVal) : $lcBevVal }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="select-lunch-beverage-VODA">
                        <option value="" selected>Select Drink...</option>
                        @foreach ($beverages as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                    <input type="number" class="form-control form-control-sm" id="pax-lunch-beverage-VODA"
                        placeholder="Qty" style="max-width: 70px;">
                    <button class="btn btn-sm btn-dark" type="button"
                        onclick="addUpsellingItem('lunch', 'beverage', 'VODA')"><i class="ti ti-plus"></i>
                        Add</button>
                </div>
                <ul class="list-group small" id="list-lunch-beverage-VODA"></ul>
            </div>
            <div class="col-md-12">
                <label class="form-label small">General Remarks</label>
                <textarea class="form-control" name="session[lunch][remarks]">{{ old('session.lunch.remarks', $lc->remarks ?? '') }}</textarea>
            </div>
            <div class="col-md-12 mt-3">
                <label class="form-label small fw-bold">Staff on Duty</label>
                @php $lcStaffVal = old('session.lunch.staff_on_duty', $lc->staff_on_duty ?? []); @endphp
                <input type="hidden" id="input-staff-lunch-VODA" name="session[lunch][staff_on_duty]"
                    value="{{ is_array($lcStaffVal) ? json_encode($lcStaffVal) : $lcStaffVal }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="select-staff-lunch-VODA">
                        <option value="" selected>Select Staff...</option>
                        @foreach ($myStaffList as $staff)
                            <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                        @endforeach
                    </select>
                    <button class="btn btn-sm btn-dark" type="button" onclick="addStaffItem('lunch', 'VODA')"><i
                            class="ti ti-plus"></i> Add</button>
                </div>
                <div id="list-staff-lunch-VODA" class="d-flex flex-wrap"></div>
            </div>
        </div>

        <hr>

        <h6 class="fw-bold text-muted mt-3">6. Competitor Comparison</h6>
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
            <div class="col-md-4"><label class="form-label small">Event (Adult)</label><input type="number"
                    class="form-control" name="session[dinner][cover_data][event_adult]"
                    value="{{ old('session.dinner.cover_data.event_adult', $dn->cover_data['event_adult'] ?? '') }}"
                    placeholder="0">
            </div>
            <div class="col-md-4"><label class="form-label small">Event (Child)</label><input type="number"
                    class="form-control" name="session[dinner][cover_data][event_child]"
                    value="{{ old('session.dinner.cover_data.event_child', $dn->cover_data['event_child'] ?? '') }}"
                    placeholder="0">
            </div>
        </div>

        <hr>

        <h6 class="fw-bold text-muted mt-3">2. Occasion / Event Type</h6>
        <div class="row g-3">
            <div class="col-md-12">
                <input type="hidden" id="input-occasion-dinner-VODA" name="session[dinner][additional_data][occasion_items]" value="{{ $dnOccasionValue ?? '[]' }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="occasion-type-dinner-VODA">
                        <option value="" selected>Select Occasion...</option>
                        <option value="Wedding Party">Wedding Party</option>
                        <option value="Birthday Party">Birthday Party</option>
                        <option value="Social Event">Social Event</option>
                    </select>
                    <input type="text" class="form-control form-control-sm" id="occasion-name-dinner-VODA"
                        placeholder="Name (e.g. Mr. Budi)">
                    <input type="number" class="form-control form-control-sm" id="occasion-pax-dinner-VODA"
                        placeholder="Pax" style="max-width: 80px;">
                    <input type="text" class="form-control form-control-sm rupiah" id="occasion-revenue-dinner-VODA"
                        placeholder="Revenue" style="max-width: 120px;" autocomplete="off">
                    <button class="btn btn-sm btn-dark" type="button" onclick="addOccasionItem('dinner', 'VODA')">
                        <i class="ti ti-plus"></i> Add
                    </button>
                </div>
                <ul class="list-group small" id="list-occasion-dinner-VODA"></ul>
            </div>
        </div>

        <hr>

        <h6 class="fw-bold text-muted mt-3">3. Promo</h6>
        <div class="row g-3">
            <div class="col-md-12">
                <input type="hidden" id="input-promo-dinner-VODA" name="session[dinner][additional_data][promo_items]" value="{{ $dnPromoValue ?? '[]' }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="promo-type-dinner-VODA">
                        <option value="" selected>Select Promo...</option>
                        <option value="Mandiri Card">Mandiri Card</option>
                        <option value="BCA Card">BCA Card</option>
                        <option value="Membership">Membership</option>
                    </select>
                    <input type="number" class="form-control form-control-sm" id="promo-pax-dinner-VODA"
                        placeholder="Pax" style="max-width: 80px;">
                    <input type="text" class="form-control form-control-sm rupiah" id="promo-revenue-dinner-VODA"
                        placeholder="Revenue" style="max-width: 120px;" autocomplete="off">
                    <button class="btn btn-sm btn-dark" type="button" onclick="addPromoItem('dinner', 'VODA')">
                        <i class="ti ti-plus"></i> Add
                    </button>
                </div>
                <ul class="list-group small" id="list-promo-dinner-VODA"></ul>
            </div>
        </div>

        <hr>

        <h6 class="fw-bold text-muted mt-3">4. Revenue Report (IDR)</h6>
        <div class="row g-3">
            {{-- Food --}}
            <div class="col-md-6">
                <label class="form-label small fw-bold">Upselling Menu (Food)</label>
                @php $dnFoodVal = old('session.dinner.upselling_data.food', $dn->upselling_data['food'] ?? []); @endphp
                <input type="hidden" id="input-dinner-food-VODA" name="session[dinner][upselling_data][food]"
                    value="{{ is_array($dnFoodVal) ? json_encode($dnFoodVal) : $dnFoodVal }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="select-dinner-food-VODA">
                        <option value="" selected>Select Food...</option>
                        @foreach ($foods as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                    <input type="number" class="form-control form-control-sm" id="pax-dinner-food-VODA"
                        placeholder="Qty" style="max-width: 70px;">
                    <button class="btn btn-sm btn-dark" type="button"
                        onclick="addUpsellingItem('dinner', 'food', 'VODA')"><i class="ti ti-plus"></i> Add</button>
                </div>
                <ul class="list-group small" id="list-dinner-food-VODA"></ul>
            </div>
            {{-- Bev --}}
            <div class="col-md-6">
                <label class="form-label small fw-bold">Beverage Upselling</label>
                @php $dnBevVal = old('session.dinner.upselling_data.beverage', $dn->upselling_data['beverage'] ?? []); @endphp
                <input type="hidden" id="input-dinner-beverage-VODA"
                    name="session[dinner][upselling_data][beverage]"
                    value="{{ is_array($dnBevVal) ? json_encode($dnBevVal) : $dnBevVal }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="select-dinner-beverage-VODA">
                        <option value="" selected>Select Drink...</option>
                        @foreach ($beverages as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                    <input type="number" class="form-control form-control-sm" id="pax-dinner-beverage-VODA"
                        placeholder="Qty" style="max-width: 70px;">
                    <button class="btn btn-sm btn-dark" type="button"
                        onclick="addUpsellingItem('dinner', 'beverage', 'VODA')"><i class="ti ti-plus"></i>
                        Add</button>
                </div>
                <ul class="list-group small" id="list-dinner-beverage-VODA"></ul>
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
                <input type="hidden" id="input-staff-dinner-VODA" name="session[dinner][staff_on_duty]"
                    value="{{ is_array($dnStaffVal) ? json_encode($dnStaffVal) : $dnStaffVal }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="select-staff-dinner-VODA">
                        <option value="" selected>Select Staff...</option>
                        @foreach ($myStaffList as $staff)
                            <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                        @endforeach
                    </select>
                    <button class="btn btn-sm btn-dark" type="button" onclick="addStaffItem('dinner', 'VODA')"><i
                            class="ti ti-plus"></i> Add</button>
                </div>
                <div id="list-staff-dinner-VODA" class="d-flex flex-wrap"></div>
            </div>
        </div>

        <hr>

        <h6 class="fw-bold text-muted mt-3">6. Competitor Comparison</h6>
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
        initUpselling('breakfast', 'food', bfFood, 'VODA');
        let bfBev = {!! json_encode(old('session.breakfast.upselling_data.beverage', $bf->upselling_data['beverage'] ?? [])) !!};
        initUpselling('breakfast', 'beverage', bfBev, 'VODA');
        let bfStaff = {!! json_encode(old('session.breakfast.staff_on_duty', $bf->staff_on_duty ?? [])) !!};
        initStaff('breakfast', bfStaff, 'VODA');
        let bfOccasion = {!! json_encode(old('session.breakfast.additional_data.occasion_items', $bf->additional_data['occasion_items'] ?? [])) !!};
        initOccasion('breakfast', bfOccasion, 'VODA');
        let bfPromo = {!! json_encode(old('session.breakfast.additional_data.promo_items', $bf->additional_data['promo_items'] ?? [])) !!};
        initPromoItems('breakfast', bfPromo, 'VODA');

        // --- LUNCH INIT ---
        let lcFood = {!! json_encode(old('session.lunch.upselling_data.food', $lc->upselling_data['food'] ?? [])) !!};
        initUpselling('lunch', 'food', lcFood, 'VODA');
        let lcBev = {!! json_encode(old('session.lunch.upselling_data.beverage', $lc->upselling_data['beverage'] ?? [])) !!};
        initUpselling('lunch', 'beverage', lcBev, 'VODA');
        let lcStaff = {!! json_encode(old('session.lunch.staff_on_duty', $lc->staff_on_duty ?? [])) !!};
        initStaff('lunch', lcStaff, 'VODA');
        let lcOccasion = {!! json_encode(old('session.lunch.additional_data.occasion_items', $lc->additional_data['occasion_items'] ?? [])) !!};
        initOccasion('lunch', lcOccasion, 'VODA');
        let lcPromo = {!! json_encode(old('session.lunch.additional_data.promo_items', $lc->additional_data['promo_items'] ?? [])) !!};
        initPromoItems('lunch', lcPromo, 'VODA');

        // --- DINNER INIT ---
        let dnFood = {!! json_encode(old('session.dinner.upselling_data.food', $dn->upselling_data['food'] ?? [])) !!};
        initUpselling('dinner', 'food', dnFood, 'VODA');
        let dnBev = {!! json_encode(old('session.dinner.upselling_data.beverage', $dn->upselling_data['beverage'] ?? [])) !!};
        initUpselling('dinner', 'beverage', dnBev, 'VODA');
        let dnStaff = {!! json_encode(old('session.dinner.staff_on_duty', $dn->staff_on_duty ?? [])) !!};
        initStaff('dinner', dnStaff, 'VODA');
        let dnOccasion = {!! json_encode(old('session.dinner.additional_data.occasion_items', $dn->additional_data['occasion_items'] ?? [])) !!};
        initOccasion('dinner', dnOccasion, 'VODA');
        let dnPromo = {!! json_encode(old('session.dinner.additional_data.promo_items', $dn->additional_data['promo_items'] ?? [])) !!};
        initPromoItems('dinner', dnPromo, 'VODA');

    });
</script>
