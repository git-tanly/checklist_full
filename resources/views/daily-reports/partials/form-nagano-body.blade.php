@php
    $dataGroupData = old('session.' . $sessionName . '.' . $floor . '.additional_data.group_data', $data['additional_data']['group_data'] ?? []);
    $dataGroupValue = is_array($dataGroupData) ? json_encode($dataGroupData) : $dataGroupData;

    $dataOccOthersData = old('session.' . $sessionName . '.' . $floor . '.additional_data.others_occasion', $data['additional_data']['others_occasion'] ?? []);
    $dataOccOthersValue = is_array($dataOccOthersData) ? json_encode($dataOccOthersData) : $dataOccOthersData;

    $dataPromoOthersData = old('session.' . $sessionName . '.' . $floor . '.additional_data.others_promo', $data['additional_data']['others_promo'] ?? []);
    $dataPromoOthersValue = is_array($dataPromoOthersData) ? json_encode($dataPromoOthersData) : $dataPromoOthersData;
@endphp

{{-- 1. COVER REPORT (KOMPLEKS) --}}
<h6 class="fw-bold text-muted mt-3">1. Cover Report Details</h6>

{{-- Teppanyaki --}}
<div class="p-3 border rounded mb-3 bg-white">
    <span class="badge bg-dark mb-2">TEPPANYAKI</span>
    <div class="row g-2">
        <div class="col-md-4">
            <label class="small text-muted">In-House (Adult)</label>
            <input type="number" class="form-control form-control-sm"
                name="session[{{ $sessionName }}][{{ $floor }}][cover_data][teppanyaki_inhouse]"
                value="{{ old('session.'.$sessionName.'.'.$floor.'.cover_data.teppanyaki_inhouse', $data['cover_data']['teppanyaki_inhouse'] ?? '') }}"
                placeholder="0">
        </div>
        <div class="col-md-4">
            <label class="small text-muted">Walk-In (Adult)</label>
            <input type="number" class="form-control form-control-sm"
                name="session[{{ $sessionName }}][{{ $floor }}][cover_data][teppanyaki_walkin]"
                value="{{ old('session.'.$sessionName.'.'.$floor.'.cover_data.teppanyaki_walkin', $data['cover_data']['teppanyaki_walkin'] ?? '') }}"
                placeholder="0">
        </div>
        <div class="col-md-4">
            <label class="small text-muted">Event (Adult)</label>
            <input type="number" class="form-control form-control-sm"
                name="session[{{ $sessionName }}][{{ $floor }}][cover_data][teppanyaki_event]"
                value="{{ old('session.'.$sessionName.'.'.$floor.'.cover_data.teppanyaki_event', $data['cover_data']['teppanyaki_event'] ?? '') }}"
                placeholder="0">
        </div>
    </div>
</div>

{{-- Yakiniku --}}
<div class="p-3 border rounded mb-3 bg-white">
    <span class="badge bg-danger mb-2">YAKINIKU</span>
    <div class="row g-2">
        <div class="col-md-4">
            <label class="small text-muted">In-House (Adult)</label>
            <input type="number" class="form-control form-control-sm"
                name="session[{{ $sessionName }}][{{ $floor }}][cover_data][yakiniku_inhouse]"
                value="{{ old('session.'.$sessionName.'.'.$floor.'.cover_data.yakiniku_inhouse', $data['cover_data']['yakiniku_inhouse'] ?? '') }}"
                placeholder="0">
        </div>
        <div class="col-md-4">
            <label class="small text-muted">Walk-In (Adult)</label>
            <input type="number" class="form-control form-control-sm"
                name="session[{{ $sessionName }}][{{ $floor }}][cover_data][yakiniku_walkin]"
                value="{{ old('session.'.$sessionName.'.'.$floor.'.cover_data.yakiniku_walkin', $data['cover_data']['yakiniku_walkin'] ?? '') }}"
                placeholder="0">
        </div>
        <div class="col-md-4">
            <label class="small text-muted">Event (Adult)</label>
            <input type="number" class="form-control form-control-sm"
                name="session[{{ $sessionName }}][{{ $floor }}][cover_data][yakiniku_event]"
                value="{{ old('session.'.$sessionName.'.'.$floor.'.cover_data.yakiniku_event', $data['cover_data']['yakiniku_event'] ?? '') }}"
                placeholder="0">
        </div>
    </div>
</div>

<hr>

{{-- 2. OCCASION / EVENT TYPE --}}
<h6 class="fw-bold text-muted mt-3">2. Occasion / Event Type</h6>
<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label small fw-bold">Wedding Party</label>
        <input type="number" class="form-control" name="session[{{ $sessionName }}][{{ $floor }}][additional_data][wedding_party]"
            value="{{ old('session.'.$sessionName.'.'.$floor.'.additional_data.wedding_party', $data['additional_data']['wedding_party'] ?? '') }}" placeholder="0">
    </div>
    <div class="col-md-4">
        <label class="form-label small fw-bold">Birthday Party</label>
        <input type="number" class="form-control" name="session[{{ $sessionName }}][{{ $floor }}][additional_data][birthday_party]"
            value="{{ old('session.'.$sessionName.'.'.$floor.'.additional_data.birthday_party', $data['additional_data']['birthday_party'] ?? '') }}" placeholder="0">
    </div>
    <div class="col-md-4">
        <label class="form-label small fw-bold">Social Event</label>
        <input type="number" class="form-control" name="session[{{ $sessionName }}][{{ $floor }}][additional_data][social_event]"
            value="{{ old('session.'.$sessionName.'.'.$floor.'.additional_data.social_event', $data['additional_data']['social_event'] ?? '') }}" placeholder="0">
    </div>
</div>
<div class="row mt-3">
    <div class="col-md-12">
        <label class="form-label small fw-bold text-primary"><i class="ti ti-plus"></i> Other Occasion</label>
        <input type="hidden" id="input-occothers-{{ $sessionName }}-NJR-{{ $floor }}" name="session[{{ $sessionName }}][{{ $floor }}][additional_data][others_occasion]" value="{{ $dataOccOthersValue }}">
        <div class="input-group mb-2">
            <input type="text" class="form-control form-control-sm" id="occothers-name-{{ $sessionName }}-NJR-{{ $floor }}" placeholder="Name of Occasion">
            <input type="number" class="form-control form-control-sm" id="occothers-pax-{{ $sessionName }}-NJR-{{ $floor }}" placeholder="Pax" style="max-width: 80px;">
            <button class="btn btn-sm btn-outline-primary" type="button" onclick="addOccOthers('{{ $sessionName }}', 'NJR-{{ $floor }}')"><i class="ti ti-plus"></i> Add</button>
        </div>
        <ul class="list-group small" id="list-occothers-{{ $sessionName }}-NJR-{{ $floor }}"></ul>
    </div>
</div>

<hr>

{{-- 3. PROMO --}}
<h6 class="fw-bold text-muted mt-3">3. Promo</h6>
<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label small fw-bold">Mandiri Card</label>
        <input type="number" class="form-control" name="session[{{ $sessionName }}][{{ $floor }}][additional_data][mandiri_card]"
            value="{{ old('session.'.$sessionName.'.'.$floor.'.additional_data.mandiri_card', $data['additional_data']['mandiri_card'] ?? '') }}" placeholder="0">
    </div>
    <div class="col-md-4">
        <label class="form-label small fw-bold">BCA Card</label>
        <input type="number" class="form-control" name="session[{{ $sessionName }}][{{ $floor }}][additional_data][bca_card]"
            value="{{ old('session.'.$sessionName.'.'.$floor.'.additional_data.bca_card', $data['additional_data']['bca_card'] ?? '') }}" placeholder="0">
    </div>
    <div class="col-md-4">
        <label class="form-label small fw-bold">Membership</label>
        <input type="number" class="form-control" name="session[{{ $sessionName }}][{{ $floor }}][additional_data][membership]"
            value="{{ old('session.'.$sessionName.'.'.$floor.'.additional_data.membership', $data['additional_data']['membership'] ?? '') }}" placeholder="0">
    </div>
</div>
<div class="row mt-3">
    <div class="col-md-12">
        <label class="form-label small fw-bold text-success"><i class="ti ti-plus"></i> Other Promo</label>
        <input type="hidden" id="input-promoothers-{{ $sessionName }}-NJR-{{ $floor }}" name="session[{{ $sessionName }}][{{ $floor }}][additional_data][others_promo]" value="{{ $dataPromoOthersValue }}">
        <div class="input-group mb-2">
            <input type="text" class="form-control form-control-sm" id="promoothers-name-{{ $sessionName }}-NJR-{{ $floor }}" placeholder="Name of Promo">
            <input type="number" class="form-control form-control-sm" id="promoothers-qty-{{ $sessionName }}-NJR-{{ $floor }}" placeholder="Qty" style="max-width: 80px;">
            <button class="btn btn-sm btn-outline-success" type="button" onclick="addPromoOthers('{{ $sessionName }}', 'NJR-{{ $floor }}')"><i class="ti ti-plus"></i> Add</button>
        </div>
        <ul class="list-group small" id="list-promoothers-{{ $sessionName }}-NJR-{{ $floor }}"></ul>
    </div>
</div>

<hr>

{{-- 4. REVENUE REPORT (Format Rupiah) --}}
<h6 class="fw-bold text-muted mt-3">4. Revenue Report (IDR)</h6>
<div class="row g-3">
    <div class="col-md-3">
        <label class="form-label small">Food Revenue</label>
        <input type="text" class="form-control rupiah" name="session[{{ $sessionName }}][{{ $floor }}][revenue_food]"
            value="{{ old('session.'.$sessionName.'.'.$floor.'.revenue_food', isset($data['revenue_food']) ? number_format($data['revenue_food'], 0, ',', '.') : '') }}"
            placeholder="0" autocomplete="off">
    </div>
    <div class="col-md-3">
        <label class="form-label small">Beverage Revenue</label>
        <input type="text" class="form-control rupiah" name="session[{{ $sessionName }}][{{ $floor }}][revenue_beverage]"
            value="{{ old('session.'.$sessionName.'.'.$floor.'.revenue_beverage', isset($data['revenue_beverage']) ? number_format($data['revenue_beverage'], 0, ',', '.') : '') }}"
            placeholder="0" autocomplete="off">
    </div>
    <div class="col-md-3">
        <label class="form-label small">Others Revenue</label>
        <input type="text" class="form-control rupiah" name="session[{{ $sessionName }}][{{ $floor }}][revenue_others]"
            value="{{ old('session.'.$sessionName.'.'.$floor.'.revenue_others', isset($data['revenue_others']) ? number_format($data['revenue_others'], 0, ',', '.') : '') }}"
            placeholder="0" autocomplete="off">
    </div>
    <div class="col-md-3">
        <label class="form-label small">Event Revenue</label>
        <input type="text" class="form-control rupiah" name="session[{{ $sessionName }}][{{ $floor }}][revenue_event]"
            value="{{ old('session.'.$sessionName.'.'.$floor.'.revenue_event', isset($data['revenue_event']) ? number_format($data['revenue_event'], 0, ',', '.') : '') }}"
            placeholder="0" autocomplete="off">
    </div>
</div>

<hr>

{{-- 5. UPSELLING & REMARKS --}}
<h6 class="fw-bold text-muted mt-3">5. Upselling & Remarks</h6>
<div class="row g-3">
    {{-- Food Upselling --}}
    <div class="col-md-6">
        <label class="form-label small fw-bold">Upselling Menu (Food)</label>
        @php
            $dataFoodVal = old('session.'.$sessionName.'.'.$floor.'.upselling_data.food', $data['upselling_data']['food'] ?? []);
            $dataFoodJson = is_array($dataFoodVal) ? json_encode($dataFoodVal) : $dataFoodVal;
        @endphp
        <input type="hidden" id="input-{{ $sessionName }}-food-NJR-{{ $floor }}" name="session[{{ $sessionName }}][{{ $floor }}][upselling_data][food]"
            value="{{ $dataFoodJson }}">

        <div class="input-group mb-2">
            <input type="text" class="form-control form-control-sm" id="select-{{ $sessionName }}-food-NJR-{{ $floor }}" placeholder="Enter Food Name">
            <input type="number" class="form-control form-control-sm" id="pax-{{ $sessionName }}-food-NJR-{{ $floor }}"
                placeholder="Qty" style="max-width: 70px;">
            <button class="btn btn-sm btn-dark" type="button"
                onclick="addUpsellingItem('{{ $sessionName }}', 'food', 'NJR-{{ $floor }}')"><i class="ti ti-plus"></i> Add</button>
        </div>
        <ul class="list-group small" id="list-{{ $sessionName }}-food-NJR-{{ $floor }}"></ul>
    </div>

    {{-- Beverage Upselling --}}
    <div class="col-md-6">
        <label class="form-label small fw-bold">Beverage Upselling</label>
        @php
            $dataBevVal = old('session.'.$sessionName.'.'.$floor.'.upselling_data.beverage', $data['upselling_data']['beverage'] ?? []);
            $dataBevJson = is_array($dataBevVal) ? json_encode($dataBevVal) : $dataBevVal;
        @endphp
        <input type="hidden" id="input-{{ $sessionName }}-beverage-NJR-{{ $floor }}" name="session[{{ $sessionName }}][{{ $floor }}][upselling_data][beverage]"
            value="{{ $dataBevJson }}">

        <div class="input-group mb-2">
            <input type="text" class="form-control form-control-sm" id="select-{{ $sessionName }}-beverage-NJR-{{ $floor }}" placeholder="Enter Beverage Name">
            <input type="number" class="form-control form-control-sm" id="pax-{{ $sessionName }}-beverage-NJR-{{ $floor }}"
                placeholder="Qty" style="max-width: 70px;">
            <button class="btn btn-sm btn-dark" type="button"
                onclick="addUpsellingItem('{{ $sessionName }}', 'beverage', 'NJR-{{ $floor }}')"><i class="ti ti-plus"></i> Add</button>
        </div>
        <ul class="list-group small" id="list-{{ $sessionName }}-beverage-NJR-{{ $floor }}"></ul>
    </div>

    {{-- Group --}}
    <div class="col-md-12 mt-3">
        <label class="form-label small fw-bold"><i class="ti ti-users-group"></i> Group</label>
        <input type="hidden" id="input-group-{{ $sessionName }}-NJR-{{ $floor }}" name="session[{{ $sessionName }}][{{ $floor }}][additional_data][group_data]" value="{{ $dataGroupValue }}">
        <div class="input-group mb-2">
            <input type="text" class="form-control form-control-sm" id="group-name-{{ $sessionName }}-NJR-{{ $floor }}" placeholder="Name of Group">
            <input type="number" class="form-control form-control-sm" id="group-qty-{{ $sessionName }}-NJR-{{ $floor }}" placeholder="Qty" style="max-width: 80px;">
            <button class="btn btn-sm btn-dark" type="button" onclick="addGroupItem('{{ $sessionName }}', 'NJR-{{ $floor }}')"><i class="ti ti-plus"></i> Add</button>
        </div>
        <ul class="list-group small" id="list-group-{{ $sessionName }}-NJR-{{ $floor }}"></ul>
    </div>

    {{-- VIP List --}}
    <div class="col-md-12 mt-3">
        <label class="form-label small fw-bold">VIP 1 & 2 List</label>
        @php
            $dataVipVal = old('session.'.$sessionName.'.'.$floor.'.vip_remarks', $data['vip_remarks'] ?? []);
            $dataVipJson = is_array($dataVipVal) ? json_encode($dataVipVal) : $dataVipVal;
        @endphp
        <input type="hidden" id="input-vip-{{ $sessionName }}-NJR-{{ $floor }}" name="session[{{ $sessionName }}][{{ $floor }}][vip_remarks]"
            value="{{ $dataVipJson }}">

        <div class="input-group mb-2">
            <input type="text" class="form-control form-control-sm" id="vip-name-{{ $sessionName }}-NJR-{{ $floor }}"
                placeholder="Guest Name (e.g. Mr. Budi)">
            <input type="text" class="form-control form-control-sm" id="vip-pos-{{ $sessionName }}-NJR-{{ $floor }}"
                placeholder="Position/Title (e.g. CEO)">
            <button class="btn btn-sm btn-dark" type="button" onclick="addVipItem('{{ $sessionName }}', 'NJR-{{ $floor }}')"><i
                    class="ti ti-plus"></i> Add</button>
        </div>
        <ul class="list-group small" id="list-vip-{{ $sessionName }}-NJR-{{ $floor }}"></ul>
    </div>

    {{-- Remarks --}}
    <div class="col-md-12">
        <label class="form-label small">General Remarks</label>
        <textarea class="form-control" name="session[{{ $sessionName }}][{{ $floor }}][remarks]">{{ old('session.'.$sessionName.'.'.$floor.'.remarks', $data['remarks'] ?? '') }}</textarea>
    </div>

    {{-- Staff On Duty --}}
    <div class="col-md-12 mt-3">
        <label class="form-label small fw-bold">Staff on Duty</label>
        @php
            $dataStaffVal = old('session.'.$sessionName.'.'.$floor.'.staff_on_duty', $data['staff_on_duty'] ?? []);
            $dataStaffJson = is_array($dataStaffVal) ? json_encode($dataStaffVal) : $dataStaffVal;
        @endphp
        <input type="hidden" id="input-staff-{{ $sessionName }}-NJR-{{ $floor }}" name="session[{{ $sessionName }}][{{ $floor }}][staff_on_duty]"
            value="{{ $dataStaffJson }}">

        <div class="input-group mb-2">
            <select class="form-select form-select-sm" id="select-staff-{{ $sessionName }}-NJR-{{ $floor }}">
                <option value="" selected>Select Staff...</option>
                @foreach ($myStaffList as $staff)
                    <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                @endforeach
            </select>
            <button class="btn btn-sm btn-dark" type="button" onclick="addStaffItem('{{ $sessionName }}', 'NJR-{{ $floor }}')"><i
                    class="ti ti-plus"></i> Add</button>
        </div>
        <div id="list-staff-{{ $sessionName }}-NJR-{{ $floor }}" class="d-flex flex-wrap mt-2"></div>
    </div>
</div>

<hr>

{{-- 6. COMPETITOR COMPARISON --}}
<h6 class="fw-bold text-muted mt-3">6. Competitor Comparison</h6>
<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label small">Shangri-La</label>
        <input type="number" class="form-control" name="session[{{ $sessionName }}][{{ $floor }}][competitor_data][shangrila_cover]"
            value="{{ old('session.'.$sessionName.'.'.$floor.'.competitor_data.shangrila_cover', $data['competitor_data']['shangrila_cover'] ?? '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label small">JW Marriott</label>
        <input type="number" class="form-control" name="session[{{ $sessionName }}][{{ $floor }}][competitor_data][jw_marriott_cover]"
            value="{{ old('session.'.$sessionName.'.'.$floor.'.competitor_data.jw_marriott_cover', $data['competitor_data']['jw_marriott_cover'] ?? '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label small">Sheraton</label>
        <input type="number" class="form-control" name="session[{{ $sessionName }}][{{ $floor }}][competitor_data][sheraton_cover]"
            value="{{ old('session.'.$sessionName.'.'.$floor.'.competitor_data.sheraton_cover', $data['competitor_data']['sheraton_cover'] ?? '') }}">
    </div>
</div>
