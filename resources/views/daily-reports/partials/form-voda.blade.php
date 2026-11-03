{{-- FORM KHUSUS VODA BISTRO (VODA) --}}

@php
    $bf = $details['breakfast'] ?? null;
    $lc = $details['lunch'] ?? null;
    $dn = $details['dinner'] ?? null;

    $restoVda = $restaurants->where('code', 'VODA')->first();
    $myStaffList = $restoVda ? $restoVda->employees : [];

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
<div class="rounded-2xl border border-slate-200/90 shadow-sm bg-white overflow-hidden mb-6">
    <div class="px-6 py-4 bg-sky-50/70 border-b border-sky-100 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-sky-100 text-sky-700 flex items-center justify-center font-bold shadow-xs">
                <i class="ti ti-soup text-xl"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-slate-800">Lunch Report</h3>
                <p class="text-xs text-slate-500">Pencatatan sesi siang Voda Bistro, cover in-house / walk-in, dan revenue</p>
            </div>
        </div>
        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-sky-100 text-sky-800 border border-sky-200/60">
            Afternoon Session
        </span>
    </div>
    <div class="p-6">
        {{-- 1. COVER REPORT --}}
        <div class="flex items-center gap-2.5 pb-2 mb-3 border-b border-slate-100">
            <span class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold shrink-0" style="background-color: rgba(23, 42, 64, 0.08); color: #172a40;">1</span>
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700">Cover Report</h4>
        </div>
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label text-xs font-semibold text-slate-600 mb-1">In-House (Adult)</label>
                <input type="number" class="form-control rounded-xl border-slate-300 py-2 text-sm focus:border-[#172a40] focus:ring-[#172a40]" name="session[lunch][cover_data][in_house_adult]"
                    value="{{ old('session.lunch.cover_data.in_house_adult', $lc->cover_data['in_house_adult'] ?? '') }}"
                    placeholder="0">
            </div>
            <div class="col-md-3">
                <label class="form-label text-xs font-semibold text-slate-600 mb-1">In-House (Child)</label>
                <input type="number" class="form-control rounded-xl border-slate-300 py-2 text-sm focus:border-[#172a40] focus:ring-[#172a40]" name="session[lunch][cover_data][in_house_child]"
                    value="{{ old('session.lunch.cover_data.in_house_child', $lc->cover_data['in_house_child'] ?? '') }}"
                    placeholder="0">
            </div>
            <div class="col-md-3">
                <label class="form-label text-xs font-semibold text-slate-600 mb-1">Walk-In (Adult)</label>
                <input type="number" class="form-control rounded-xl border-slate-300 py-2 text-sm focus:border-[#172a40] focus:ring-[#172a40]" name="session[lunch][cover_data][walk_in_adult]"
                    value="{{ old('session.lunch.cover_data.walk_in_adult', $lc->cover_data['walk_in_adult'] ?? '') }}"
                    placeholder="0">
            </div>
            <div class="col-md-3">
                <label class="form-label text-xs font-semibold text-slate-600 mb-1">Walk-In (Child)</label>
                <input type="number" class="form-control rounded-xl border-slate-300 py-2 text-sm focus:border-[#172a40] focus:ring-[#172a40]" name="session[lunch][cover_data][walk_in_child]"
                    value="{{ old('session.lunch.cover_data.walk_in_child', $lc->cover_data['walk_in_child'] ?? '') }}"
                    placeholder="0">
            </div>
        </div>

        <div class="my-5 border-t border-slate-100"></div>

        {{-- 2. OCCASION / EVENT TYPE --}}
        <div class="flex items-center gap-2.5 pb-2 mb-3 border-b border-slate-100">
            <span class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold shrink-0" style="background-color: rgba(23, 42, 64, 0.08); color: #172a40;">2</span>
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700">Occasion / Event Type</h4>
        </div>
        <div class="row g-3">
            <div class="col-md-12">
                <input type="hidden" id="input-occasion-lunch-VODA" name="session[lunch][additional_data][occasion_items]" value="{{ $lcOccasionValue ?? '[]' }}">
                <div class="input-group mb-2.5">
                    <select class="form-select form-select-sm rounded-l-xl border-slate-300 text-xs font-medium" id="occasion-type-lunch-VODA" onchange="toggleOccasionOther('lunch', 'VODA')">
                        <option value="" selected>Select Occasion...</option>
                        <option value="Wedding Party">Wedding Party</option>
                        <option value="Birthday Party">Birthday Party</option>
                        <option value="Social Event">Social Event</option>
                        <option value="Corporate Event">Corporate Event</option>
                        <option value="Other">Other</option>
                    </select>
                    <input type="text" class="form-control form-control-sm d-none border-slate-300 text-xs" id="occasion-other-lunch-VODA" placeholder="Occasion Type" style="max-width: 150px;">
                    <input type="text" class="form-control form-control-sm border-slate-300 text-xs" id="occasion-name-lunch-VODA"
                        placeholder="Name (e.g. Mr. Budi)">
                    <input type="number" class="form-control form-control-sm border-slate-300 text-xs" id="occasion-pax-lunch-VODA"
                        placeholder="Pax" style="max-width: 80px;">
                    <input type="text" class="form-control form-control-sm rupiah border-slate-300 text-xs" id="occasion-revenue-lunch-VODA"
                        placeholder="Revenue" style="max-width: 120px;" autocomplete="off">
                    <button class="btn btn-sm rounded-r-xl px-3.5 text-xs font-semibold text-white inline-flex items-center gap-1 shadow-sm transition" style="background-color: #172a40; border-color: #172a40;" type="button" onclick="addOccasionItem('lunch', 'VODA')">
                        <i class="ti ti-plus text-xs" style="color: #B89047;"></i> Add
                    </button>
                </div>
                <ul class="list-group small" id="list-occasion-lunch-VODA"></ul>
            </div>
        </div>

        <div class="my-5 border-t border-slate-100"></div>

        {{-- 3. PROMO --}}
        <div class="flex items-center gap-2.5 pb-2 mb-3 border-b border-slate-100">
            <span class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold shrink-0" style="background-color: rgba(23, 42, 64, 0.08); color: #172a40;">3</span>
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700">Promo</h4>
        </div>
        <div class="row g-3">
            <div class="col-md-12">
                <input type="hidden" id="input-promo-lunch-VODA" name="session[lunch][additional_data][promo_items]" value="{{ $lcPromoValue ?? '[]' }}">
                <div class="input-group mb-2.5">
                    <select class="form-select form-select-sm rounded-l-xl border-slate-300 text-xs font-medium" id="promo-type-lunch-VODA" onchange="togglePromoOther('lunch', 'VODA')">
                        <option value="" selected>Select Promo...</option>
                        <option value="Mandiri Card">Mandiri Card</option>
                        <option value="BCA Card">BCA Card</option>
                        <option value="Membership">Membership</option>
                        <option value="Other">Other</option>
                    </select>
                    <input type="text" class="form-control form-control-sm d-none border-slate-300 text-xs" id="promo-other-lunch-VODA" placeholder="Promo Name" style="max-width: 150px;">
                    <input type="number" class="form-control form-control-sm border-slate-300 text-xs" id="promo-pax-lunch-VODA"
                        placeholder="Pax" style="max-width: 80px;">
                    <input type="text" class="form-control form-control-sm rupiah border-slate-300 text-xs" id="promo-revenue-lunch-VODA"
                        placeholder="Revenue" style="max-width: 120px;" autocomplete="off">
                    <button class="btn btn-sm rounded-r-xl px-3.5 text-xs font-semibold text-white inline-flex items-center gap-1 shadow-sm transition" style="background-color: #172a40; border-color: #172a40;" type="button" onclick="addPromoItem('lunch', 'VODA')">
                        <i class="ti ti-plus text-xs" style="color: #B89047;"></i> Add
                    </button>
                </div>
                <ul class="list-group small" id="list-promo-lunch-VODA"></ul>
            </div>
        </div>

        <div class="my-5 border-t border-slate-100"></div>

        {{-- 4. REVENUE REPORT --}}
        <div class="flex items-center gap-2.5 pb-2 mb-3 border-b border-slate-100">
            <span class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold shrink-0" style="background-color: rgba(23, 42, 64, 0.08); color: #172a40;">4</span>
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700">Revenue Report (IDR)</h4>
        </div>
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label text-xs font-semibold text-slate-600 mb-1">Food</label>
                <div class="input-group">
                    <span class="input-group-text rounded-l-xl bg-slate-50 text-slate-500 text-xs font-bold border-slate-300">Rp</span>
                    <input type="text" class="form-control rounded-r-xl border-slate-300 py-2 text-sm rupiah focus:border-[#172a40] focus:ring-[#172a40]" name="session[lunch][revenue_food]"
                        value="{{ old('session.lunch.revenue_food', isset($lc->revenue_food) ? number_format($lc->revenue_food, 0, ',', '.') : '') }}"
                        autocomplete="off" placeholder="0">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label text-xs font-semibold text-slate-600 mb-1">Beverage</label>
                <div class="input-group">
                    <span class="input-group-text rounded-l-xl bg-slate-50 text-slate-500 text-xs font-bold border-slate-300">Rp</span>
                    <input type="text" class="form-control rounded-r-xl border-slate-300 py-2 text-sm rupiah focus:border-[#172a40] focus:ring-[#172a40]" name="session[lunch][revenue_beverage]"
                        value="{{ old('session.lunch.revenue_beverage', isset($lc->revenue_beverage) ? number_format($lc->revenue_beverage, 0, ',', '.') : '') }}"
                        autocomplete="off" placeholder="0">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label text-xs font-semibold text-slate-600 mb-1">Others</label>
                <div class="input-group">
                    <span class="input-group-text rounded-l-xl bg-slate-50 text-slate-500 text-xs font-bold border-slate-300">Rp</span>
                    <input type="text" class="form-control rounded-r-xl border-slate-300 py-2 text-sm rupiah focus:border-[#172a40] focus:ring-[#172a40]" name="session[lunch][revenue_others]"
                        value="{{ old('session.lunch.revenue_others', isset($lc->revenue_others) ? number_format($lc->revenue_others, 0, ',', '.') : '') }}"
                        autocomplete="off" placeholder="0">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label text-xs font-semibold text-slate-600 mb-1">Event</label>
                <div class="input-group">
                    <span class="input-group-text rounded-l-xl bg-slate-50 text-slate-500 text-xs font-bold border-slate-300">Rp</span>
                    <input type="text" class="form-control rounded-r-xl border-slate-300 py-2 text-sm rupiah focus:border-[#172a40] focus:ring-[#172a40]" name="session[lunch][revenue_event]"
                        value="{{ old('session.lunch.revenue_event', isset($lc->revenue_event) ? number_format($lc->revenue_event, 0, ',', '.') : '') }}"
                        autocomplete="off" placeholder="0">
                </div>
            </div>
        </div>

        <div class="my-5 border-t border-slate-100"></div>

        {{-- 5. UPSELLING & REMARKS --}}
        <div class="flex items-center gap-2.5 pb-2 mb-3 border-b border-slate-100">
            <span class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold shrink-0" style="background-color: rgba(23, 42, 64, 0.08); color: #172a40;">5</span>
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700">Upselling & Remarks</h4>
        </div>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label text-xs font-semibold text-slate-700 mb-1">Upselling Menu (Food)</label>
                @php $lcFoodVal = old('session.lunch.upselling_data.food', $lc->upselling_data['food'] ?? []); @endphp
                <input type="hidden" id="input-lunch-food-VODA" name="session[lunch][upselling_data][food]"
                    value="{{ is_array($lcFoodVal) ? json_encode($lcFoodVal) : $lcFoodVal }}">
                <div class="input-group mb-2">
                    <input type="text" class="form-control form-control-sm rounded-l-xl border-slate-300 text-xs" id="select-lunch-food-VODA" placeholder="Enter Food Name">
                    <input type="number" class="form-control form-control-sm border-slate-300 text-xs" id="pax-lunch-food-VODA"
                        placeholder="Qty" style="max-width: 70px;">
                    <button class="btn btn-sm rounded-r-xl px-3.5 text-xs font-semibold text-white inline-flex items-center gap-1 shadow-sm transition" style="background-color: #172a40; border-color: #172a40;" type="button"
                        onclick="addUpsellingItem('lunch', 'food', 'VODA')"><i class="ti ti-plus text-xs" style="color: #B89047;"></i> Add</button>
                </div>
                <ul class="list-group small" id="list-lunch-food-VODA"></ul>
            </div>
            <div class="col-md-6">
                <label class="form-label text-xs font-semibold text-slate-700 mb-1">Beverage Upselling</label>
                @php $lcBevVal = old('session.lunch.upselling_data.beverage', $lc->upselling_data['beverage'] ?? []); @endphp
                <input type="hidden" id="input-lunch-beverage-VODA" name="session[lunch][upselling_data][beverage]"
                    value="{{ is_array($lcBevVal) ? json_encode($lcBevVal) : $lcBevVal }}">
                <div class="input-group mb-2">
                    <input type="text" class="form-control form-control-sm rounded-l-xl border-slate-300 text-xs" id="select-lunch-beverage-VODA" placeholder="Enter Beverage Name">
                    <input type="number" class="form-control form-control-sm border-slate-300 text-xs" id="pax-lunch-beverage-VODA"
                        placeholder="Qty" style="max-width: 70px;">
                    <button class="btn btn-sm rounded-r-xl px-3.5 text-xs font-semibold text-white inline-flex items-center gap-1 shadow-sm transition" style="background-color: #172a40; border-color: #172a40;" type="button"
                        onclick="addUpsellingItem('lunch', 'beverage', 'VODA')"><i class="ti ti-plus text-xs" style="color: #B89047;"></i> Add</button>
                </div>
                <ul class="list-group small" id="list-lunch-beverage-VODA"></ul>
            </div>

            <div class="col-md-12">
                <label class="form-label text-xs font-semibold text-slate-700 mb-1">General Remarks</label>
                <textarea class="form-control rounded-xl border-slate-300 p-2.5 text-xs focus:border-[#172a40] focus:ring-[#172a40]" rows="2" name="session[lunch][remarks]" placeholder="Catatan operasional sesi lunch...">{{ old('session.lunch.remarks', $lc->remarks ?? '') }}</textarea>
            </div>

            <div class="col-md-12 mt-3">
                <label class="form-label text-xs font-semibold text-slate-700 mb-1">Staff on Duty</label>
                @php $lcStaffVal = old('session.lunch.staff_on_duty', $lc->staff_on_duty ?? []); @endphp
                <input type="hidden" id="input-staff-lunch-VODA" name="session[lunch][staff_on_duty]"
                    value="{{ is_array($lcStaffVal) ? json_encode($lcStaffVal) : $lcStaffVal }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm rounded-l-xl border-slate-300 text-xs" id="select-staff-lunch-VODA">
                        <option value="" selected>Select Staff...</option>
                        @foreach ($myStaffList as $staff)
                            <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                        @endforeach
                    </select>
                    <button class="btn btn-sm rounded-r-xl px-3.5 text-xs font-semibold text-white inline-flex items-center gap-1 shadow-sm transition" style="background-color: #172a40; border-color: #172a40;" type="button" onclick="addStaffItem('lunch', 'VODA')">
                        <i class="ti ti-plus text-xs" style="color: #B89047;"></i> Add
                    </button>
                </div>
                <div id="list-staff-lunch-VODA" class="d-flex flex-wrap gap-1.5"></div>
            </div>
        </div>

        <div class="my-5 border-t border-slate-100"></div>

        {{-- 6. COMPETITOR COMPARISON --}}
        <div class="flex items-center gap-2.5 pb-2 mb-3 border-b border-slate-100">
            <span class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold shrink-0" style="background-color: rgba(23, 42, 64, 0.08); color: #172a40;">6</span>
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700">Competitor Comparison</h4>
        </div>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label text-xs font-semibold text-slate-600 mb-1">Shangri-La</label>
                <input type="number" class="form-control rounded-xl border-slate-300 py-2 text-sm focus:border-[#172a40] focus:ring-[#172a40]" name="session[lunch][competitor_data][shangrila_cover]"
                    value="{{ old('session.lunch.competitor_data.shangrila_cover', $lc->competitor_data['shangrila_cover'] ?? '') }}"
                    placeholder="0">
            </div>
            <div class="col-md-4">
                <label class="form-label text-xs font-semibold text-slate-600 mb-1">JW Marriott</label>
                <input type="number" class="form-control rounded-xl border-slate-300 py-2 text-sm focus:border-[#172a40] focus:ring-[#172a40]" name="session[lunch][competitor_data][jw_marriott_cover]"
                    value="{{ old('session.lunch.competitor_data.jw_marriott_cover', $lc->competitor_data['jw_marriott_cover'] ?? '') }}"
                    placeholder="0">
            </div>
            <div class="col-md-4">
                <label class="form-label text-xs font-semibold text-slate-600 mb-1">Sheraton</label>
                <input type="number" class="form-control rounded-xl border-slate-300 py-2 text-sm focus:border-[#172a40] focus:ring-[#172a40]" name="session[lunch][competitor_data][sheraton_cover]"
                    value="{{ old('session.lunch.competitor_data.sheraton_cover', $lc->competitor_data['sheraton_cover'] ?? '') }}"
                    placeholder="0">
            </div>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- SESSION: DINNER --}}
{{-- ============================================================ --}}
<div class="rounded-2xl border border-slate-200/90 shadow-sm bg-white overflow-hidden mb-6">
    <div class="px-6 py-4 bg-indigo-50/70 border-b border-indigo-100 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold shadow-xs">
                <i class="ti ti-moon-stars text-xl"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-slate-800">Dinner Report</h3>
                <p class="text-xs text-slate-500">Pencatatan sesi malam Voda Bistro, cover in-house / walk-in, dan revenue</p>
            </div>
        </div>
        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800 border border-indigo-200/60">
            Evening Session
        </span>
    </div>
    <div class="p-6">
        {{-- 1. COVER REPORT --}}
        <div class="flex items-center gap-2.5 pb-2 mb-3 border-b border-slate-100">
            <span class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold shrink-0" style="background-color: rgba(23, 42, 64, 0.08); color: #172a40;">1</span>
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700">Cover Report</h4>
        </div>
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label text-xs font-semibold text-slate-600 mb-1">In-House (Adult)</label>
                <input type="number" class="form-control rounded-xl border-slate-300 py-2 text-sm focus:border-[#172a40] focus:ring-[#172a40]" name="session[dinner][cover_data][in_house_adult]"
                    value="{{ old('session.dinner.cover_data.in_house_adult', $dn->cover_data['in_house_adult'] ?? '') }}"
                    placeholder="0">
            </div>
            <div class="col-md-3">
                <label class="form-label text-xs font-semibold text-slate-600 mb-1">In-House (Child)</label>
                <input type="number" class="form-control rounded-xl border-slate-300 py-2 text-sm focus:border-[#172a40] focus:ring-[#172a40]" name="session[dinner][cover_data][in_house_child]"
                    value="{{ old('session.dinner.cover_data.in_house_child', $dn->cover_data['in_house_child'] ?? '') }}"
                    placeholder="0">
            </div>
            <div class="col-md-3">
                <label class="form-label text-xs font-semibold text-slate-600 mb-1">Walk-In (Adult)</label>
                <input type="number" class="form-control rounded-xl border-slate-300 py-2 text-sm focus:border-[#172a40] focus:ring-[#172a40]" name="session[dinner][cover_data][walk_in_adult]"
                    value="{{ old('session.dinner.cover_data.walk_in_adult', $dn->cover_data['walk_in_adult'] ?? '') }}"
                    placeholder="0">
            </div>
            <div class="col-md-3">
                <label class="form-label text-xs font-semibold text-slate-600 mb-1">Walk-In (Child)</label>
                <input type="number" class="form-control rounded-xl border-slate-300 py-2 text-sm focus:border-[#172a40] focus:ring-[#172a40]" name="session[dinner][cover_data][walk_in_child]"
                    value="{{ old('session.dinner.cover_data.walk_in_child', $dn->cover_data['walk_in_child'] ?? '') }}"
                    placeholder="0">
            </div>
        </div>

        <div class="my-5 border-t border-slate-100"></div>

        {{-- 2. OCCASION / EVENT TYPE --}}
        <div class="flex items-center gap-2.5 pb-2 mb-3 border-b border-slate-100">
            <span class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold shrink-0" style="background-color: rgba(23, 42, 64, 0.08); color: #172a40;">2</span>
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700">Occasion / Event Type</h4>
        </div>
        <div class="row g-3">
            <div class="col-md-12">
                <input type="hidden" id="input-occasion-dinner-VODA" name="session[dinner][additional_data][occasion_items]" value="{{ $dnOccasionValue ?? '[]' }}">
                <div class="input-group mb-2.5">
                    <select class="form-select form-select-sm rounded-l-xl border-slate-300 text-xs font-medium" id="occasion-type-dinner-VODA" onchange="toggleOccasionOther('dinner', 'VODA')">
                        <option value="" selected>Select Occasion...</option>
                        <option value="Wedding Party">Wedding Party</option>
                        <option value="Birthday Party">Birthday Party</option>
                        <option value="Social Event">Social Event</option>
                        <option value="Corporate Event">Corporate Event</option>
                        <option value="Other">Other</option>
                    </select>
                    <input type="text" class="form-control form-control-sm d-none border-slate-300 text-xs" id="occasion-other-dinner-VODA" placeholder="Occasion Type" style="max-width: 150px;">
                    <input type="text" class="form-control form-control-sm border-slate-300 text-xs" id="occasion-name-dinner-VODA"
                        placeholder="Name (e.g. Mr. Budi)">
                    <input type="number" class="form-control form-control-sm border-slate-300 text-xs" id="occasion-pax-dinner-VODA"
                        placeholder="Pax" style="max-width: 80px;">
                    <input type="text" class="form-control form-control-sm rupiah border-slate-300 text-xs" id="occasion-revenue-dinner-VODA"
                        placeholder="Revenue" style="max-width: 120px;" autocomplete="off">
                    <button class="btn btn-sm rounded-r-xl px-3.5 text-xs font-semibold text-white inline-flex items-center gap-1 shadow-sm transition" style="background-color: #172a40; border-color: #172a40;" type="button" onclick="addOccasionItem('dinner', 'VODA')">
                        <i class="ti ti-plus text-xs" style="color: #B89047;"></i> Add
                    </button>
                </div>
                <ul class="list-group small" id="list-occasion-dinner-VODA"></ul>
            </div>
        </div>

        <div class="my-5 border-t border-slate-100"></div>

        {{-- 3. PROMO --}}
        <div class="flex items-center gap-2.5 pb-2 mb-3 border-b border-slate-100">
            <span class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold shrink-0" style="background-color: rgba(23, 42, 64, 0.08); color: #172a40;">3</span>
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700">Promo</h4>
        </div>
        <div class="row g-3">
            <div class="col-md-12">
                <input type="hidden" id="input-promo-dinner-VODA" name="session[dinner][additional_data][promo_items]" value="{{ $dnPromoValue ?? '[]' }}">
                <div class="input-group mb-2.5">
                    <select class="form-select form-select-sm rounded-l-xl border-slate-300 text-xs font-medium" id="promo-type-dinner-VODA" onchange="togglePromoOther('dinner', 'VODA')">
                        <option value="" selected>Select Promo...</option>
                        <option value="Mandiri Card">Mandiri Card</option>
                        <option value="BCA Card">BCA Card</option>
                        <option value="Membership">Membership</option>
                        <option value="Other">Other</option>
                    </select>
                    <input type="text" class="form-control form-control-sm d-none border-slate-300 text-xs" id="promo-other-dinner-VODA" placeholder="Promo Name" style="max-width: 150px;">
                    <input type="number" class="form-control form-control-sm border-slate-300 text-xs" id="promo-pax-dinner-VODA"
                        placeholder="Pax" style="max-width: 80px;">
                    <input type="text" class="form-control form-control-sm rupiah border-slate-300 text-xs" id="promo-revenue-dinner-VODA"
                        placeholder="Revenue" style="max-width: 120px;" autocomplete="off">
                    <button class="btn btn-sm rounded-r-xl px-3.5 text-xs font-semibold text-white inline-flex items-center gap-1 shadow-sm transition" style="background-color: #172a40; border-color: #172a40;" type="button" onclick="addPromoItem('dinner', 'VODA')">
                        <i class="ti ti-plus text-xs" style="color: #B89047;"></i> Add
                    </button>
                </div>
                <ul class="list-group small" id="list-promo-dinner-VODA"></ul>
            </div>
        </div>

        <div class="my-5 border-t border-slate-100"></div>

        {{-- 4. REVENUE REPORT --}}
        <div class="flex items-center gap-2.5 pb-2 mb-3 border-b border-slate-100">
            <span class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold shrink-0" style="background-color: rgba(23, 42, 64, 0.08); color: #172a40;">4</span>
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700">Revenue Report (IDR)</h4>
        </div>
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label text-xs font-semibold text-slate-600 mb-1">Food</label>
                <div class="input-group">
                    <span class="input-group-text rounded-l-xl bg-slate-50 text-slate-500 text-xs font-bold border-slate-300">Rp</span>
                    <input type="text" class="form-control rounded-r-xl border-slate-300 py-2 text-sm rupiah focus:border-[#172a40] focus:ring-[#172a40]" name="session[dinner][revenue_food]"
                        value="{{ old('session.dinner.revenue_food', isset($dn->revenue_food) ? number_format($dn->revenue_food, 0, ',', '.') : '') }}"
                        autocomplete="off" placeholder="0">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label text-xs font-semibold text-slate-600 mb-1">Beverage</label>
                <div class="input-group">
                    <span class="input-group-text rounded-l-xl bg-slate-50 text-slate-500 text-xs font-bold border-slate-300">Rp</span>
                    <input type="text" class="form-control rounded-r-xl border-slate-300 py-2 text-sm rupiah focus:border-[#172a40] focus:ring-[#172a40]" name="session[dinner][revenue_beverage]"
                        value="{{ old('session.dinner.revenue_beverage', isset($dn->revenue_beverage) ? number_format($dn->revenue_beverage, 0, ',', '.') : '') }}"
                        autocomplete="off" placeholder="0">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label text-xs font-semibold text-slate-600 mb-1">Others</label>
                <div class="input-group">
                    <span class="input-group-text rounded-l-xl bg-slate-50 text-slate-500 text-xs font-bold border-slate-300">Rp</span>
                    <input type="text" class="form-control rounded-r-xl border-slate-300 py-2 text-sm rupiah focus:border-[#172a40] focus:ring-[#172a40]" name="session[dinner][revenue_others]"
                        value="{{ old('session.dinner.revenue_others', isset($dn->revenue_others) ? number_format($dn->revenue_others, 0, ',', '.') : '') }}"
                        autocomplete="off" placeholder="0">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label text-xs font-semibold text-slate-600 mb-1">Event</label>
                <div class="input-group">
                    <span class="input-group-text rounded-l-xl bg-slate-50 text-slate-500 text-xs font-bold border-slate-300">Rp</span>
                    <input type="text" class="form-control rounded-r-xl border-slate-300 py-2 text-sm rupiah focus:border-[#172a40] focus:ring-[#172a40]" name="session[dinner][revenue_event]"
                        value="{{ old('session.dinner.revenue_event', isset($dn->revenue_event) ? number_format($dn->revenue_event, 0, ',', '.') : '') }}"
                        autocomplete="off" placeholder="0">
                </div>
            </div>
        </div>

        <div class="my-5 border-t border-slate-100"></div>

        {{-- 5. UPSELLING & REMARKS --}}
        <div class="flex items-center gap-2.5 pb-2 mb-3 border-b border-slate-100">
            <span class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold shrink-0" style="background-color: rgba(23, 42, 64, 0.08); color: #172a40;">5</span>
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700">Upselling & Remarks</h4>
        </div>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label text-xs font-semibold text-slate-700 mb-1">Upselling Menu (Food)</label>
                @php $dnFoodVal = old('session.dinner.upselling_data.food', $dn->upselling_data['food'] ?? []); @endphp
                <input type="hidden" id="input-dinner-food-VODA" name="session[dinner][upselling_data][food]"
                    value="{{ is_array($dnFoodVal) ? json_encode($dnFoodVal) : $dnFoodVal }}">
                <div class="input-group mb-2">
                    <input type="text" class="form-control form-control-sm rounded-l-xl border-slate-300 text-xs" id="select-dinner-food-VODA" placeholder="Enter Food Name">
                    <input type="number" class="form-control form-control-sm border-slate-300 text-xs" id="pax-dinner-food-VODA"
                        placeholder="Qty" style="max-width: 70px;">
                    <button class="btn btn-sm rounded-r-xl px-3.5 text-xs font-semibold text-white inline-flex items-center gap-1 shadow-sm transition" style="background-color: #172a40; border-color: #172a40;" type="button"
                        onclick="addUpsellingItem('dinner', 'food', 'VODA')"><i class="ti ti-plus text-xs" style="color: #B89047;"></i> Add</button>
                </div>
                <ul class="list-group small" id="list-dinner-food-VODA"></ul>
            </div>
            <div class="col-md-6">
                <label class="form-label text-xs font-semibold text-slate-700 mb-1">Beverage Upselling</label>
                @php $dnBevVal = old('session.dinner.upselling_data.beverage', $dn->upselling_data['beverage'] ?? []); @endphp
                <input type="hidden" id="input-dinner-beverage-VODA" name="session[dinner][upselling_data][beverage]"
                    value="{{ is_array($dnBevVal) ? json_encode($dnBevVal) : $dnBevVal }}">
                <div class="input-group mb-2">
                    <input type="text" class="form-control form-control-sm rounded-l-xl border-slate-300 text-xs" id="select-dinner-beverage-VODA" placeholder="Enter Beverage Name">
                    <input type="number" class="form-control form-control-sm border-slate-300 text-xs" id="pax-dinner-beverage-VODA"
                        placeholder="Qty" style="max-width: 70px;">
                    <button class="btn btn-sm rounded-r-xl px-3.5 text-xs font-semibold text-white inline-flex items-center gap-1 shadow-sm transition" style="background-color: #172a40; border-color: #172a40;" type="button"
                        onclick="addUpsellingItem('dinner', 'beverage', 'VODA')"><i class="ti ti-plus text-xs" style="color: #B89047;"></i> Add</button>
                </div>
                <ul class="list-group small" id="list-dinner-beverage-VODA"></ul>
            </div>

            <div class="col-md-12">
                <label class="form-label text-xs font-semibold text-slate-700 mb-1">General Remarks</label>
                <textarea class="form-control rounded-xl border-slate-300 p-2.5 text-xs focus:border-[#172a40] focus:ring-[#172a40]" rows="2" name="session[dinner][remarks]" placeholder="Catatan operasional sesi dinner...">{{ old('session.dinner.remarks', $dn->remarks ?? '') }}</textarea>
            </div>

            <div class="col-md-12 mt-3">
                <label class="form-label text-xs font-semibold text-slate-700 mb-1">Staff on Duty</label>
                @php $dnStaffVal = old('session.dinner.staff_on_duty', $dn->staff_on_duty ?? []); @endphp
                <input type="hidden" id="input-staff-dinner-VODA" name="session[dinner][staff_on_duty]"
                    value="{{ is_array($dnStaffVal) ? json_encode($dnStaffVal) : $dnStaffVal }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm rounded-l-xl border-slate-300 text-xs" id="select-staff-dinner-VODA">
                        <option value="" selected>Select Staff...</option>
                        @foreach ($myStaffList as $staff)
                            <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                        @endforeach
                    </select>
                    <button class="btn btn-sm rounded-r-xl px-3.5 text-xs font-semibold text-white inline-flex items-center gap-1 shadow-sm transition" style="background-color: #172a40; border-color: #172a40;" type="button" onclick="addStaffItem('dinner', 'VODA')">
                        <i class="ti ti-plus text-xs" style="color: #B89047;"></i> Add
                    </button>
                </div>
                <div id="list-staff-dinner-VODA" class="d-flex flex-wrap gap-1.5"></div>
            </div>
        </div>

        <div class="my-5 border-t border-slate-100"></div>

        {{-- 6. COMPETITOR COMPARISON --}}
        <div class="flex items-center gap-2.5 pb-2 mb-3 border-b border-slate-100">
            <span class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold shrink-0" style="background-color: rgba(23, 42, 64, 0.08); color: #172a40;">6</span>
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700">Competitor Comparison</h4>
        </div>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label text-xs font-semibold text-slate-600 mb-1">Shangri-La</label>
                <input type="number" class="form-control rounded-xl border-slate-300 py-2 text-sm focus:border-[#172a40] focus:ring-[#172a40]" name="session[dinner][competitor_data][shangrila_cover]"
                    value="{{ old('session.dinner.competitor_data.shangrila_cover', $dn->competitor_data['shangrila_cover'] ?? '') }}"
                    placeholder="0">
            </div>
            <div class="col-md-4">
                <label class="form-label text-xs font-semibold text-slate-600 mb-1">JW Marriott</label>
                <input type="number" class="form-control rounded-xl border-slate-300 py-2 text-sm focus:border-[#172a40] focus:ring-[#172a40]" name="session[dinner][competitor_data][jw_marriott_cover]"
                    value="{{ old('session.dinner.competitor_data.jw_marriott_cover', $dn->competitor_data['jw_marriott_cover'] ?? '') }}"
                    placeholder="0">
            </div>
            <div class="col-md-4">
                <label class="form-label text-xs font-semibold text-slate-600 mb-1">Sheraton</label>
                <input type="number" class="form-control rounded-xl border-slate-300 py-2 text-sm focus:border-[#172a40] focus:ring-[#172a40]" name="session[dinner][competitor_data][sheraton_cover]"
                    value="{{ old('session.dinner.competitor_data.sheraton_cover', $dn->competitor_data['sheraton_cover'] ?? '') }}"
                    placeholder="0">
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
