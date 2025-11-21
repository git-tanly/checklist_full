{{-- FORM KHUSUS 209 DINING --}}
@php
    $bf = $details['breakfast'] ?? null;
    $lc = $details['lunch'] ?? null;
    $dn = $details['dinner'] ?? null;

    $myMenu = $upsellingItems[1] ?? collect([]);
    $foods = $myMenu->where('type', 'food');
    $beverages = $myMenu->where('type', 'beverage');

    $resto209 = $restaurants->where('code', '209')->first();
    $myStaffList = $resto209 ? $resto209->users : [];

    // Ambil data (bisa dari old input array, atau database array)
    $bfVipData = old('session.breakfast.vip_remarks', $bf->vip_remarks ?? []);
    $bfVipValue = is_array($bfVipData) ? json_encode($bfVipData) : $bfVipData; // Jika Array (karena validasi controller atau database), ubah jadi JSON String, Jika sudah String (jarang terjadi di logic baru kita), biarkan
    $lcVipData = old('session.lunch.vip_remarks', $lc->vip_remarks ?? []);
    $lcVipValue = is_array($lcVipData) ? json_encode($lcVipData) : $lcVipData;
    $dnVipData = old('session.dinner.vip_remarks', $dn->vip_remarks ?? []);
    $dnVipValue = is_array($dnVipData) ? json_encode($dnVipData) : $dnVipData;

    $bfStaffData = old('session.breakfast.staff_on_duty', $bf->staff_on_duty ?? []);
    $bfStaffValue = is_array($bfStaffData) ? json_encode($bfStaffData) : $bfStaffData;
    $lcStaffData = old('session.lunch.staff_on_duty', $lc->staff_on_duty ?? []);
    $lcStaffValue = is_array($lcStaffData) ? json_encode($lcStaffData) : $lcStaffData;
    $dnStaffData = old('session.dinner.staff_on_duty', $dn->staff_on_duty ?? []);
    $dnStaffValue = is_array($dnStaffData) ? json_encode($dnStaffData) : $dnStaffData;

    $bfFoodData = old('session.breakfast.upselling_data.food', $bf->upselling_data['food'] ?? []);
    $bfFoodValue = is_array($bfFoodData) ? json_encode($bfFoodData) : $bfFoodData;
    $lcFoodData = old('session.lunch.upselling_data.food', $lc->upselling_data['food'] ?? []);
    $lcFoodValue = is_array($lcFoodData) ? json_encode($lcFoodData) : $lcFoodData;
    $dnFoodData = old('session.dinner.upselling_data.food', $dn->upselling_data['food'] ?? []);
    $dnFoodValue = is_array($dnFoodData) ? json_encode($dnFoodData) : $dnFoodData;

    $bfBevData = old('session.breakfast.upselling_data.beverage', $bf->upselling_data['beverage'] ?? []);
    $bfBevValue = is_array($bfBevData) ? json_encode($bfBevData) : $bfBevData;
    $lcBevData = old('session.lunch.upselling_data.beverage', $lc->upselling_data['beverage'] ?? []);
    $lcBevValue = is_array($lcBevData) ? json_encode($lcBevData) : $lcBevData;
    $dnBevData = old('session.dinner.upselling_data.beverage', $dn->upselling_data['beverage'] ?? []);
    $dnBevValue = is_array($dnBevData) ? json_encode($dnBevData) : $dnBevData;
@endphp

{{-- === SESSION: BREAKFAST === --}}
<div class="card">
    <div class="card-header bg-light-warning">
        <h5 class="mb-0 text-capitalize"><i class="ti ti-sun me-2"></i> Breakfast Report</h5>
    </div>
    <div class="card-body">

        <h6 class="fw-bold text-muted mt-3">1. Cover Report</h6>
        <div class="row g-3">
            {{-- In-House --}}
            <div class="col-md-3">
                <label class="form-label small">In-House (Adult)</label>
                <input type="number" class="form-control" name="session[breakfast][cover_data][in_house_adult]"
                    value="{{ old('session.breakfast.cover_data.in_house_adult', $bf->cover_data['in_house_adult'] ?? '') }}"
                    placeholder="0">
            </div>
            <div class="col-md-3">
                <label class="form-label small">In-House (Child)</label>
                <input type="number" class="form-control" name="session[breakfast][cover_data][in_house_child]"
                    value="{{ old('session.breakfast.cover_data.in_house_child', $bf->cover_data['in_house_child'] ?? '') }}"
                    placeholder="0">
            </div>

            {{-- Walk-In --}}
            <div class="col-md-3">
                <label class="form-label small">Walk-In (Adult)</label>
                <input type="number" class="form-control" name="session[breakfast][cover_data][walk_in_adult]"
                    value="{{ old('session.breakfast.cover_data.walk_in_adult', $bf->cover_data['walk_in_adult'] ?? '') }}"
                    placeholder="0">
            </div>
            <div class="col-md-3">
                <label class="form-label small">Walk-In (Child)</label>
                <input type="number" class="form-control" name="session[breakfast][cover_data][walk_in_child]"
                    value="{{ old('session.breakfast.cover_data.walk_in_child', $bf->cover_data['walk_in_child'] ?? '') }}"
                    placeholder="0">
            </div>

            {{-- Event & BEO --}}
            <div class="col-md-3">
                <label class="form-label small">Event (Adult)</label>
                <input type="number" class="form-control" name="session[breakfast][cover_data][event_adult]"
                    value="{{ old('session.breakfast.cover_data.event_adult', $bf->cover_data['event_adult'] ?? '') }}"
                    placeholder="0">
            </div>
            <div class="col-md-3">
                <label class="form-label small">Event (Child)</label>
                <input type="number" class="form-control" name="session[breakfast][cover_data][event_child]"
                    value="{{ old('session.breakfast.cover_data.event_child', $bf->cover_data['event_child'] ?? '') }}"
                    placeholder="0">
            </div>
            <div class="col-md-3">
                <label class="form-label small text-danger">BEO (Total)</label>
                <input type="number" class="form-control" name="session[breakfast][cover_data][beo_total]"
                    value="{{ old('session.breakfast.cover_data.beo_total', $bf->cover_data['beo_total'] ?? '') }}"
                    placeholder="0">
            </div>
        </div>

        <hr>

        <h6 class="fw-bold text-muted mt-3">2. Revenue Report (IDR)</h6>
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label small">Food Revenue</label>
                <input type="text" class="form-control rupiah" name="session[breakfast][revenue_food]"
                    value="{{ old('session.breakfast.revenue_food', isset($bf->revenue_food) ? number_format($bf->revenue_food, 0, ',', '.') : '') }}"
                    placeholder="0" autocomplete="off">
            </div>
            <div class="col-md-3">
                <label class="form-label small">Beverage Revenue</label>
                <input type="text" class="form-control rupiah" name="session[breakfast][revenue_beverage]"
                    value="{{ old('session.breakfast.revenue_beverage', isset($bf->revenue_beverage) ? number_format($bf->revenue_beverage, 0, ',', '.') : '') }}"
                    placeholder="0" autocomplete="off">
            </div>
            <div class="col-md-3">
                <label class="form-label small">Others Revenue</label>
                <input type="text" class="form-control rupiah" name="session[breakfast][revenue_others]"
                    value="{{ old('session.breakfast.revenue_others', isset($bf->revenue_others) ? number_format($bf->revenue_others, 0, ',', '.') : '') }}"
                    placeholder="0" autocomplete="off">
            </div>
            <div class="col-md-3">
                <label class="form-label small">Total Event Revenue</label>
                <input type="text" class="form-control rupiah" name="session[breakfast][revenue_event]"
                    value="{{ old('session.breakfast.revenue_event', isset($bf->revenue_event) ? number_format($bf->revenue_event, 0, ',', '.') : '') }}"
                    placeholder="0" autocomplete="off">
            </div>
        </div>

        <hr>

        <h6 class="fw-bold text-muted mt-3">3. Upselling & Remarks</h6>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label small fw-bold">Upselling Menu (Food)</label>

                {{-- 1. Hidden Input (Penyimpan Data JSON) --}}
                <input type="hidden" id="input-breakfast-food" name="session[breakfast][upselling_data][food]"
                    value="{{ $bfFoodValue }}">

                {{-- 2. Area Input (Dropdown & Pax) --}}
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="select-breakfast-food">
                        <option value="" selected>Select Food...</option>
                        @foreach ($foods as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                    <input type="number" class="form-control form-control-sm" id="pax-breakfast-food"
                        placeholder="Qty" style="max-width: 70px;">
                    <button class="btn btn-sm btn-dark" type="button"
                        onclick="addUpsellingItem('breakfast', 'food')">
                        <i class="ti ti-plus"></i> Add
                    </button>
                </div>

                {{-- 3. List Tampilan --}}
                <ul class="list-group small" id="list-breakfast-food">
                    {{-- Item akan muncul di sini lewat JS --}}
                </ul>
            </div>

            {{-- === UPSELLING BEVERAGE === --}}
            <div class="col-md-6">
                <label class="form-label small fw-bold">Beverage Upselling</label>

                {{-- 1. Hidden Input --}}
                <input type="hidden" id="input-breakfast-beverage"
                    name="session[breakfast][upselling_data][beverage]" value="{{ $bfBevValue }}">

                {{-- 2. Area Input --}}
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="select-breakfast-beverage">
                        <option value="" selected>Select Drink...</option>
                        @foreach ($beverages as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                    <input type="number" class="form-control form-control-sm" id="pax-breakfast-beverage"
                        placeholder="Qty" style="max-width: 70px;">
                    <button class="btn btn-sm btn-dark" type="button"
                        onclick="addUpsellingItem('breakfast', 'beverage')">
                        <i class="ti ti-plus"></i> Add
                    </button>
                </div>

                {{-- 3. List Tampilan --}}
                <ul class="list-group small" id="list-breakfast-beverage">
                    {{-- Item akan muncul di sini lewat JS --}}
                </ul>
            </div>
            {{-- <div class="col-md-6">
                <label class="form-label small">Upselling Menu (Food)</label>
                <textarea class="form-control" rows="2" name="session[breakfast][upselling_data][food_items]">{{ old('session.breakfast.upselling_data.food_items', $bf->upselling_data['food_items'] ?? '') }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label small">Beverage Upselling</label>
                <textarea class="form-control" rows="2" name="session[breakfast][upselling_data][beverage_items]">{{ old('session.breakfast.upselling_data.beverage_items', $bf->upselling_data['beverage_items'] ?? '') }}</textarea>
            </div> --}}
            <div class="col-md-12 mt-3">
                <label class="form-label small fw-bold">VIP 1 & 2 List</label>

                {{-- 1. Hidden Input (Penyimpan Data JSON ke Database) --}}
                {{-- Value logic: Prioritaskan old input, lalu data DB, lalu array kosong default --}}
                <input type="hidden" id="input-vip-breakfast" name="session[breakfast][vip_remarks]"
                    value="{{ $bfVipValue }}">

                {{-- 2. Area Input (Nama & Jabatan) --}}
                <div class="input-group mb-2">
                    <input type="text" class="form-control form-control-sm" id="vip-name-breakfast"
                        placeholder="Guest Name (e.g. Mr. Budi)">
                    <input type="text" class="form-control form-control-sm" id="vip-pos-breakfast"
                        placeholder="Position/Title (e.g. CEO)">
                    <button class="btn btn-sm btn-dark" type="button" onclick="addVipItem('breakfast')">
                        <i class="ti ti-plus"></i> Add
                    </button>
                </div>

                {{-- 3. List Tampilan --}}
                <ul class="list-group small" id="list-vip-breakfast">
                    {{-- Item akan muncul di sini lewat JS --}}
                </ul>
            </div>
            <div class="col-md-12">
                <label class="form-label small">General Remarks</label>
                <input type="text" class="form-control" name="session[breakfast][remarks]"
                    value="{{ old('session.breakfast.remarks', $bf->remarks ?? '') }}">
            </div>
            <div class="col-md-12 mt-3">
                <label class="form-label small fw-bold">Staff on Duty</label>

                {{-- 1. Hidden Input (Simpan JSON Array) --}}
                <input type="hidden" id="input-staff-breakfast" name="session[breakfast][staff_on_duty]"
                    value="{{ $bfStaffValue }}">

                {{-- 2. Area Dropdown & Add --}}
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="select-staff-breakfast">
                        <option value="" selected>Select Staff...</option>
                        {{-- Ambil Staff List khusus Resto 209 (ID 1) --}}
                        @foreach ($myStaffList ?? [] as $staff)
                            <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                        @endforeach
                    </select>
                    <button class="btn btn-sm btn-dark" type="button" onclick="addStaffItem('breakfast')">
                        <i class="ti ti-plus"></i> Add
                    </button>
                </div>

                {{-- 3. List Tampilan (Badge) --}}
                <div id="list-staff-breakfast" class="d-flex flex-wrap">
                    {{-- Item badge akan muncul di sini --}}
                </div>
            </div>
        </div>

        <hr>

        <h6 class="fw-bold text-muted mt-3">4. Competitor Comparison (Cover)</h6>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label small">Shangri-La</label>
                <input type="number" class="form-control"
                    name="session[breakfast][competitor_data][shangrila_cover]"
                    value="{{ old('session.breakfast.competitor_data.shangrila_cover', $bf->competitor_data['shangrila_cover'] ?? '') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label small">JW Marriott</label>
                <input type="number" class="form-control"
                    name="session[breakfast][competitor_data][jw_marriott_cover]"
                    value="{{ old('session.breakfast.competitor_data.jw_marriott_cover', $bf->competitor_data['jw_marriott_cover'] ?? '') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label small">Sheraton</label>
                <input type="number" class="form-control" name="session[breakfast][competitor_data][sheraton_cover]"
                    value="{{ old('session.breakfast.competitor_data.sheraton_cover', $bf->competitor_data['sheraton_cover'] ?? '') }}">
            </div>
        </div>

    </div>
</div>

{{-- === SESSION: LUNCH === --}}
<div class="card">
    <div class="card-header bg-light-primary">
        <h5 class="mb-0 text-capitalize"><i class="ti ti-soup me-2"></i> Lunch Report</h5>
    </div>
    <div class="card-body">
        <div class="col-md-3">
            <label class="form-label small fw-bold text-muted mt-3">Thematic</label>

            <select class="form-select" name="session[lunch][thematic]">
                <option value="" selected disabled>-- Select Thematic --</option>

                @foreach (['Sulawesi', 'Seafood', 'Western', 'Japanese', 'Texas'] as $theme)
                    <option value="{{ $theme }}" {{-- Logika Cek: Jika old input ATAU data database sama dengan opsi ini, maka pilih (selected) --}}
                        {{ old('session.lunch.thematic', $lc->thematic ?? '') == $theme ? 'selected' : '' }}>
                        {{ $theme }}
                    </option>
                @endforeach
            </select>
        </div>
        <h6 class="fw-bold text-muted mt-3">1. Cover Report</h6>
        <div class="row g-3">
            {{-- In-House --}}
            <div class="col-md-3">
                <label class="form-label small">In-House (Adult)</label>
                <input type="number" class="form-control" name="session[lunch][cover_data][in_house_adult]"
                    value="{{ old('session.lunch.cover_data.in_house_adult', $lc->cover_data['in_house_adult'] ?? '') }}"
                    placeholder="0">
            </div>
            <div class="col-md-3">
                <label class="form-label small">In-House (Child)</label>
                <input type="number" class="form-control" name="session[lunch][cover_data][in_house_child]"
                    value="{{ old('session.lunch.cover_data.in_house_child', $lc->cover_data['in_house_child'] ?? '') }}"
                    placeholder="0">
            </div>

            {{-- Walk-In --}}
            <div class="col-md-3">
                <label class="form-label small">Walk-In (Adult)</label>
                <input type="number" class="form-control" name="session[lunch][cover_data][walk_in_adult]"
                    value="{{ old('session.lunch.cover_data.walk_in_adult', $lc->cover_data['walk_in_adult'] ?? '') }}"
                    placeholder="0">
            </div>
            <div class="col-md-3">
                <label class="form-label small">Walk-In (Child)</label>
                <input type="number" class="form-control" name="session[lunch][cover_data][walk_in_child]"
                    value="{{ old('session.lunch.cover_data.walk_in_child', $lc->cover_data['walk_in_child'] ?? '') }}"
                    placeholder="0">
            </div>

            {{-- Event & BEO --}}
            <div class="col-md-3">
                <label class="form-label small">Event (Adult)</label>
                <input type="number" class="form-control" name="session[lunch][cover_data][event_adult]"
                    value="{{ old('session.lunch.cover_data.event_adult', $lc->cover_data['event_adult'] ?? '') }}"
                    placeholder="0">
            </div>
            <div class="col-md-3">
                <label class="form-label small">Event (Child)</label>
                <input type="number" class="form-control" name="session[lunch][cover_data][event_child]"
                    value="{{ old('session.lunch.cover_data.event_child', $lc->cover_data['event_child'] ?? '') }}"
                    placeholder="0">
            </div>
            <div class="col-md-3">
                <label class="form-label small text-danger">BEO (Total)</label>
                <input type="number" class="form-control" name="session[lunch][cover_data][beo_total]"
                    value="{{ old('session.lunch.cover_data.beo_total', $lc->cover_data['beo_total'] ?? '') }}"
                    placeholder="0">
            </div>
        </div>

        <hr>

        <h6 class="fw-bold text-muted mt-3">2. Revenue Report (IDR)</h6>
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
                <label class="form-label small">Total Event Revenue</label>
                <input type="text" class="form-control rupiah" name="session[lunch][revenue_event]"
                    value="{{ old('session.lunch.revenue_event', isset($lc->revenue_event) ? number_format($lc->revenue_event, 0, ',', '.') : '') }}"
                    placeholder="0" autocomplete="off">
            </div>
        </div>

        <hr>

        <h6 class="fw-bold text-muted mt-3">3. Upselling & Remarks</h6>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label small fw-bold">Upselling Menu (Food)</label>

                {{-- 1. Hidden Input (Penyimpan Data JSON) --}}
                <input type="hidden" id="input-lunch-food" name="session[lunch][upselling_data][food]"
                    value="{{ $lcFoodValue }}">

                {{-- 2. Area Input (Dropdown & Pax) --}}
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="select-lunch-food">
                        <option value="" selected>Select Food...</option>
                        @foreach ($foods as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                    <input type="number" class="form-control form-control-sm" id="pax-lunch-food" placeholder="Qty"
                        style="max-width: 70px;">
                    <button class="btn btn-sm btn-dark" type="button" onclick="addUpsellingItem('lunch', 'food')">
                        <i class="ti ti-plus"></i> Add
                    </button>
                </div>

                {{-- 3. List Tampilan --}}
                <ul class="list-group small" id="list-lunch-food">
                    {{-- Item akan muncul di sini lewat JS --}}
                </ul>
            </div>

            {{-- === UPSELLING BEVERAGE === --}}
            <div class="col-md-6">
                <label class="form-label small fw-bold">Beverage Upselling</label>

                {{-- 1. Hidden Input --}}
                <input type="hidden" id="input-lunch-beverage" name="session[lunch][upselling_data][beverage]"
                    value="{{ $lcBevValue }}">

                {{-- 2. Area Input --}}
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="select-lunch-beverage">
                        <option value="" selected>Select Drink...</option>
                        @foreach ($beverages as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                    <input type="number" class="form-control form-control-sm" id="pax-lunch-beverage"
                        placeholder="Qty" style="max-width: 70px;">
                    <button class="btn btn-sm btn-dark" type="button"
                        onclick="addUpsellingItem('lunch', 'beverage')">
                        <i class="ti ti-plus"></i> Add
                    </button>
                </div>

                {{-- 3. List Tampilan --}}
                <ul class="list-group small" id="list-lunch-beverage">
                    {{-- Item akan muncul di sini lewat JS --}}
                </ul>
            </div>
            {{-- <div class="col-md-6">
                <label class="form-label small">Upselling Menu (Food)</label>
                <textarea class="form-control" rows="2" name="session[lunch][upselling_data][food_items]">{{ old('session.lunch.upselling_data.food_items', $lc->upselling_data['food_items'] ?? '') }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label small">Beverage Upselling</label>
                <textarea class="form-control" rows="2" name="session[lunch][upselling_data][beverage_items]">{{ old('session.lunch.upselling_data.beverage_items', $lc->upselling_data['beverage_items'] ?? '') }}</textarea>
            </div> --}}
            <div class="col-md-12 mt-3">
                <label class="form-label small fw-bold">VIP 1 & 2 List</label>

                {{-- 1. Hidden Input (Penyimpan Data JSON ke Database) --}}
                {{-- Value logic: Prioritaskan old input, lalu data DB, lalu array kosong default --}}
                <input type="hidden" id="input-vip-lunch" name="session[lunch][vip_remarks]"
                    value="{{ $lcVipValue }}">

                {{-- 2. Area Input (Nama & Jabatan) --}}
                <div class="input-group mb-2">
                    <input type="text" class="form-control form-control-sm" id="vip-name-lunch"
                        placeholder="Guest Name (e.g. Mr. Budi)">
                    <input type="text" class="form-control form-control-sm" id="vip-pos-lunch"
                        placeholder="Position/Title (e.g. CEO)">
                    <button class="btn btn-sm btn-dark" type="button" onclick="addVipItem('lunch')">
                        <i class="ti ti-plus"></i> Add
                    </button>
                </div>

                {{-- 3. List Tampilan --}}
                <ul class="list-group small" id="list-vip-lunch">
                    {{-- Item akan muncul di sini lewat JS --}}
                </ul>
            </div>
            <div class="col-md-12">
                <label class="form-label small">General Remarks</label>
                <input type="text" class="form-control" name="session[lunch][remarks]"
                    value="{{ old('session.lunch.remarks', $lc->remarks ?? '') }}">
            </div>
            <div class="col-md-12 mt-3">
                <label class="form-label small fw-bold">Staff on Duty</label>

                {{-- 1. Hidden Input (Simpan JSON Array) --}}
                <input type="hidden" id="input-staff-lunch" name="session[lunch][staff_on_duty]"
                    value="{{ $lcStaffValue }}">

                {{-- 2. Area Dropdown & Add --}}
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="select-staff-lunch">
                        <option value="" selected>Select Staff...</option>
                        {{-- Ambil Staff List khusus Resto 209 (ID 1) --}}
                        @foreach ($myStaffList ?? [] as $staff)
                            <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                        @endforeach
                    </select>
                    <button class="btn btn-sm btn-dark" type="button" onclick="addStaffItem('lunch')">
                        <i class="ti ti-plus"></i> Add
                    </button>
                </div>

                {{-- 3. List Tampilan (Badge) --}}
                <div id="list-staff-lunch" class="d-flex flex-wrap">
                    {{-- Item badge akan muncul di sini --}}
                </div>
            </div>
        </div>

        <hr>

        <h6 class="fw-bold text-muted mt-3">4. Competitor Comparison (Cover)</h6>
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

{{-- === SESSION: DINNER === --}}
<div class="card">
    <div class="card-header bg-light-danger">
        <h5 class="mb-0 text-capitalize"><i class="ti ti-moon-stars me-2"></i> Dinner Report</h5>
    </div>
    <div class="card-body">
        <div class="col-md-3">
            <label class="form-label small fw-bold text-muted mt-3">Thematic</label>

            <select class="form-select" name="session[dinner][thematic]">
                <option value="" selected disabled>-- Select Thematic --</option>

                @foreach (['Sulawesi', 'Seafood', 'Western', 'Japanese', 'Texas'] as $theme)
                    <option value="{{ $theme }}" {{-- Logika Cek: Jika old input ATAU data database sama dengan opsi ini, maka pilih (selected) --}}
                        {{ old('session.dinner.thematic', $dn->thematic ?? '') == $theme ? 'selected' : '' }}>
                        {{ $theme }}
                    </option>
                @endforeach
            </select>
        </div>
        <h6 class="fw-bold text-muted mt-3">1. Cover Report</h6>
        <div class="row g-3">
            {{-- In-House --}}
            <div class="col-md-3">
                <label class="form-label small">In-House (Adult)</label>
                <input type="number" class="form-control" name="session[dinner][cover_data][in_house_adult]"
                    value="{{ old('session.dinner.cover_data.in_house_adult', $dn->cover_data['in_house_adult'] ?? '') }}"
                    placeholder="0">
            </div>
            <div class="col-md-3">
                <label class="form-label small">In-House (Child)</label>
                <input type="number" class="form-control" name="session[dinner][cover_data][in_house_child]"
                    value="{{ old('session.dinner.cover_data.in_house_child', $dn->cover_data['in_house_child'] ?? '') }}"
                    placeholder="0">
            </div>

            {{-- Walk-In --}}
            <div class="col-md-3">
                <label class="form-label small">Walk-In (Adult)</label>
                <input type="number" class="form-control" name="session[dinner][cover_data][walk_in_adult]"
                    value="{{ old('session.dinner.cover_data.walk_in_adult', $dn->cover_data['walk_in_adult'] ?? '') }}"
                    placeholder="0">
            </div>
            <div class="col-md-3">
                <label class="form-label small">Walk-In (Child)</label>
                <input type="number" class="form-control" name="session[dinner][cover_data][walk_in_child]"
                    value="{{ old('session.dinner.cover_data.walk_in_child', $dn->cover_data['walk_in_child'] ?? '') }}"
                    placeholder="0">
            </div>

            {{-- Event & BEO --}}
            <div class="col-md-3">
                <label class="form-label small">Event (Adult)</label>
                <input type="number" class="form-control" name="session[dinner][cover_data][event_adult]"
                    value="{{ old('session.dinner.cover_data.event_adult', $dn->cover_data['event_adult'] ?? '') }}"
                    placeholder="0">
            </div>
            <div class="col-md-3">
                <label class="form-label small">Event (Child)</label>
                <input type="number" class="form-control" name="session[dinner][cover_data][event_child]"
                    value="{{ old('session.dinner.cover_data.event_child', $dn->cover_data['event_child'] ?? '') }}"
                    placeholder="0">
            </div>
            <div class="col-md-3">
                <label class="form-label small text-danger">BEO (Total)</label>
                <input type="number" class="form-control" name="session[dinner][cover_data][beo_total]"
                    value="{{ old('session.dinner.cover_data.beo_total', $dn->cover_data['beo_total'] ?? '') }}"
                    placeholder="0">
            </div>
        </div>

        <hr>

        <h6 class="fw-bold text-muted mt-3">2. Revenue Report (IDR)</h6>
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
                <label class="form-label small">Total Event Revenue</label>
                <input type="text" class="form-control rupiah" name="session[dinner][revenue_event]"
                    value="{{ old('session.dinner.revenue_event', isset($dn->revenue_event) ? number_format($dn->revenue_event, 0, ',', '.') : '') }}"
                    placeholder="0" autocomplete="off">
            </div>
        </div>

        <hr>

        <h6 class="fw-bold text-muted mt-3">3. Upselling & Remarks</h6>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label small fw-bold">Upselling Menu (Food)</label>

                {{-- 1. Hidden Input (Penyimpan Data JSON) --}}
                <input type="hidden" id="input-dinner-food" name="session[dinner][upselling_data][food]"
                    value="{{ $dnFoodValue }}">

                {{-- 2. Area Input (Dropdown & Pax) --}}
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="select-dinner-food">
                        <option value="" selected>Select Food...</option>
                        @foreach ($foods as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                    <input type="number" class="form-control form-control-sm" id="pax-dinner-food"
                        placeholder="Qty" style="max-width: 70px;">
                    <button class="btn btn-sm btn-dark" type="button" onclick="addUpsellingItem('dinner', 'food')">
                        <i class="ti ti-plus"></i> Add
                    </button>
                </div>

                {{-- 3. List Tampilan --}}
                <ul class="list-group small" id="list-dinner-food">
                    {{-- Item akan muncul di sini lewat JS --}}
                </ul>
            </div>

            {{-- === UPSELLING BEVERAGE === --}}
            <div class="col-md-6">
                <label class="form-label small fw-bold">Beverage Upselling</label>

                {{-- 1. Hidden Input --}}
                <input type="hidden" id="input-dinner-beverage" name="session[dinner][upselling_data][beverage]"
                    value="{{ $dnBevValue }}">

                {{-- 2. Area Input --}}
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="select-dinner-beverage">
                        <option value="" selected>Select Drink...</option>
                        @foreach ($beverages as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                    <input type="number" class="form-control form-control-sm" id="pax-dinner-beverage"
                        placeholder="Qty" style="max-width: 70px;">
                    <button class="btn btn-sm btn-dark" type="button"
                        onclick="addUpsellingItem('dinner', 'beverage')">
                        <i class="ti ti-plus"></i> Add
                    </button>
                </div>

                {{-- 3. List Tampilan --}}
                <ul class="list-group small" id="list-dinner-beverage">
                    {{-- Item akan muncul di sini lewat JS --}}
                </ul>
            </div>
            {{-- <div class="col-md-6">
                <label class="form-label small">Upselling Menu (Food)</label>
                <textarea class="form-control" rows="2" name="session[dinner][upselling_data][food_items]">{{ old('session.dinner.upselling_data.food_items', $dn->upselling_data['food_items'] ?? '') }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label small">Beverage Upselling</label>
                <textarea class="form-control" rows="2" name="session[dinner][upselling_data][beverage_items]">{{ old('session.dinner.upselling_data.beverage_items', $dn->upselling_data['beverage_items'] ?? '') }}</textarea>
            </div> --}}
            <div class="col-md-12 mt-3">
                <label class="form-label small fw-bold">VIP 1 & 2 List</label>

                {{-- 1. Hidden Input (Penyimpan Data JSON ke Database) --}}
                {{-- Value logic: Prioritaskan old input, lalu data DB, lalu array kosong default --}}
                <input type="hidden" id="input-vip-dinner" name="session[dinner][vip_remarks]"
                    value="{{ $dnVipValue }}">

                {{-- 2. Area Input (Nama & Jabatan) --}}
                <div class="input-group mb-2">
                    <input type="text" class="form-control form-control-sm" id="vip-name-dinner"
                        placeholder="Guest Name (e.g. Mr. Budi)">
                    <input type="text" class="form-control form-control-sm" id="vip-pos-dinner"
                        placeholder="Position/Title (e.g. CEO)">
                    <button class="btn btn-sm btn-dark" type="button" onclick="addVipItem('dinner')">
                        <i class="ti ti-plus"></i> Add
                    </button>
                </div>

                {{-- 3. List Tampilan --}}
                <ul class="list-group small" id="list-vip-dinner">
                    {{-- Item akan muncul di sini lewat JS --}}
                </ul>
            </div>
            <div class="col-md-12">
                <label class="form-label small">General Remarks</label>
                <input type="text" class="form-control" name="session[dinner][remarks]"
                    value="{{ old('session.dinner.remarks', $dn->remarks ?? '') }}">
            </div>
            <div class="col-md-12 mt-3">
                <label class="form-label small fw-bold">Staff on Duty</label>

                {{-- 1. Hidden Input (Simpan JSON Array) --}}
                <input type="hidden" id="input-staff-dinner" name="session[dinner][staff_on_duty]"
                    value="{{ $dnStaffValue }}">

                {{-- 2. Area Dropdown & Add --}}
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm" id="select-staff-dinner">
                        <option value="" selected>Select Staff...</option>
                        {{-- Ambil Staff List khusus Resto 209 (ID 1) --}}
                        @foreach ($myStaffList ?? [] as $staff)
                            <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                        @endforeach
                    </select>
                    <button class="btn btn-sm btn-dark" type="button" onclick="addStaffItem('dinner')">
                        <i class="ti ti-plus"></i> Add
                    </button>
                </div>

                {{-- 3. List Tampilan (Badge) --}}
                <div id="list-staff-dinner" class="d-flex flex-wrap">
                    {{-- Item badge akan muncul di sini --}}
                </div>
            </div>
        </div>

        <hr>

        <h6 class="fw-bold text-muted mt-3">4. Competitor Comparison (Cover)</h6>
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ============================================================
        // 1. UPSELLING INITIALIZATION
        // ============================================================

        // --- BREAKFAST ---
        // Perhatikan: json_encode membungkus SELURUH old()
        let bfFood = {!! json_encode(old('session.breakfast.upselling_data.food', $bf->upselling_data['food'] ?? [])) !!};
        initUpselling('breakfast', 'food', bfFood);

        let bfBev = {!! json_encode(old('session.breakfast.upselling_data.beverage', $bf->upselling_data['beverage'] ?? [])) !!};
        initUpselling('breakfast', 'beverage', bfBev);

        // --- LUNCH ---
        let lcFood = {!! json_encode(old('session.lunch.upselling_data.food', $lc->upselling_data['food'] ?? [])) !!};
        initUpselling('lunch', 'food', lcFood);

        let lcBev = {!! json_encode(old('session.lunch.upselling_data.beverage', $lc->upselling_data['beverage'] ?? [])) !!};
        initUpselling('lunch', 'beverage', lcBev);

        // --- DINNER ---
        let dnFood = {!! json_encode(old('session.dinner.upselling_data.food', $dn->upselling_data['food'] ?? [])) !!};
        initUpselling('dinner', 'food', dnFood);

        let dnBev = {!! json_encode(old('session.dinner.upselling_data.beverage', $dn->upselling_data['beverage'] ?? [])) !!};
        initUpselling('dinner', 'beverage', dnBev);


        // ============================================================
        // 2. VIP LIST INITIALIZATION
        // ============================================================

        let bfVip = {!! json_encode(old('session.breakfast.vip_remarks', $bf->vip_remarks ?? [])) !!};
        initVip('breakfast', bfVip);

        let lcVip = {!! json_encode(old('session.lunch.vip_remarks', $lc->vip_remarks ?? [])) !!};
        initVip('lunch', lcVip);

        let dnVip = {!! json_encode(old('session.dinner.vip_remarks', $dn->vip_remarks ?? [])) !!};
        initVip('dinner', dnVip);


        // ============================================================
        // 3. STAFF ON DUTY INITIALIZATION
        // ============================================================

        let bfStaff = {!! json_encode(old('session.breakfast.staff_on_duty', $bf->staff_on_duty ?? [])) !!};
        initStaff('breakfast', bfStaff);

        let lcStaff = {!! json_encode(old('session.lunch.staff_on_duty', $lc->staff_on_duty ?? [])) !!};
        initStaff('lunch', lcStaff);

        let dnStaff = {!! json_encode(old('session.dinner.staff_on_duty', $dn->staff_on_duty ?? [])) !!};
        initStaff('dinner', dnStaff);
    });
</script>
