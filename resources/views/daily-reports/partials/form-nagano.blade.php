{{-- FORM KHUSUS NAGANO (NJR) --}}

@php
    $lc = $details['lunch'] ?? null;
    $dn = $details['dinner'] ?? null;

    $restoNgn = $restaurants->where('code', 'NJR')->first();
    $myStaffList = $restoNgn ? $restoNgn->employees : [];

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
<div class="rounded-2xl border border-slate-200/90 shadow-sm bg-white overflow-hidden mb-6">
    <div class="px-6 py-4 bg-sky-50/70 border-b border-sky-100 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-sky-100 text-sky-700 flex items-center justify-center font-bold shadow-xs">
                <i class="ti ti-soup text-xl"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-slate-800">Lunch Report</h3>
                <p class="text-xs text-slate-500">Pencatatan sesi siang Nagano, cover Teppanyaki/Yakiniku, dan revenue</p>
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

        {{-- Teppanyaki --}}
        <div class="rounded-xl border border-slate-200/90 bg-slate-50/50 p-3.5 mb-3">
            <div class="flex items-center gap-2 mb-2.5">
                <span class="px-2.5 py-0.5 rounded-md text-[11px] font-bold tracking-wider uppercase bg-slate-800 text-white">TEPPANYAKI</span>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label text-xs font-semibold text-slate-600 mb-1">In-House (Adult)</label>
                    <input type="number" class="form-control rounded-xl border-slate-300 py-2 text-sm focus:border-[#172a40] focus:ring-[#172a40]"
                        name="session[lunch][cover_data][teppanyaki_inhouse]"
                        value="{{ old('session.lunch.cover_data.teppanyaki_inhouse', $lc->cover_data['teppanyaki_inhouse'] ?? '') }}"
                        placeholder="0">
                </div>
                <div class="col-md-6">
                    <label class="form-label text-xs font-semibold text-slate-600 mb-1">Walk-In (Adult)</label>
                    <input type="number" class="form-control rounded-xl border-slate-300 py-2 text-sm focus:border-[#172a40] focus:ring-[#172a40]"
                        name="session[lunch][cover_data][teppanyaki_walkin]"
                        value="{{ old('session.lunch.cover_data.teppanyaki_walkin', $lc->cover_data['teppanyaki_walkin'] ?? '') }}"
                        placeholder="0">
                </div>
            </div>
        </div>

        {{-- Yakiniku --}}
        <div class="rounded-xl border border-slate-200/90 bg-slate-50/50 p-3.5 mb-4">
            <div class="flex items-center gap-2 mb-2.5">
                <span class="px-2.5 py-0.5 rounded-md text-[11px] font-bold tracking-wider uppercase bg-rose-700 text-white">YAKINIKU</span>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label text-xs font-semibold text-slate-600 mb-1">In-House (Adult)</label>
                    <input type="number" class="form-control rounded-xl border-slate-300 py-2 text-sm focus:border-[#172a40] focus:ring-[#172a40]"
                        name="session[lunch][cover_data][yakiniku_inhouse]"
                        value="{{ old('session.lunch.cover_data.yakiniku_inhouse', $lc->cover_data['yakiniku_inhouse'] ?? '') }}"
                        placeholder="0">
                </div>
                <div class="col-md-6">
                    <label class="form-label text-xs font-semibold text-slate-600 mb-1">Walk-In (Adult)</label>
                    <input type="number" class="form-control rounded-xl border-slate-300 py-2 text-sm focus:border-[#172a40] focus:ring-[#172a40]"
                        name="session[lunch][cover_data][yakiniku_walkin]"
                        value="{{ old('session.lunch.cover_data.yakiniku_walkin', $lc->cover_data['yakiniku_walkin'] ?? '') }}"
                        placeholder="0">
                </div>
            </div>
        </div>

        <div class="my-5 border-t border-slate-100"></div>

        {{-- 2. OCCASION --}}
        <div class="flex items-center gap-2.5 pb-2 mb-3 border-b border-slate-100">
            <span class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold shrink-0" style="background-color: rgba(23, 42, 64, 0.08); color: #172a40;">2</span>
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700">Occasion / Event Type</h4>
        </div>
        <div class="row g-3">
            <div class="col-md-12">
                <input type="hidden" id="input-occasion-lunch-NJR" name="session[lunch][additional_data][occasion_items]" value="{{ $lcOccasionValue ?? '[]' }}">
                <div class="input-group mb-2.5">
                    <select class="form-select form-select-sm rounded-l-xl border-slate-300 text-xs font-medium" id="occasion-type-lunch-NJR" onchange="toggleOccasionOther('lunch', 'NJR')">
                        <option value="" selected>Select Occasion...</option>
                        <option value="Wedding Party">Wedding Party</option>
                        <option value="Birthday Party">Birthday Party</option>
                        <option value="Social Event">Social Event</option>
                        <option value="Corporate Event">Corporate Event</option>
                        <option value="Other">Other</option>
                    </select>
                    <input type="text" class="form-control form-control-sm d-none border-slate-300 text-xs" id="occasion-other-lunch-NJR" placeholder="Occasion Type" style="max-width: 150px;">
                    <input type="text" class="form-control form-control-sm border-slate-300 text-xs" id="occasion-name-lunch-NJR"
                        placeholder="Name (e.g. Mr. Budi)">
                    <input type="number" class="form-control form-control-sm border-slate-300 text-xs" id="occasion-pax-lunch-NJR"
                        placeholder="Pax" style="max-width: 80px;">
                    <input type="text" class="form-control form-control-sm rupiah border-slate-300 text-xs" id="occasion-revenue-lunch-NJR"
                        placeholder="Revenue" style="max-width: 120px;" autocomplete="off">
                    <button class="btn btn-sm rounded-r-xl px-3.5 text-xs font-semibold text-white inline-flex items-center gap-1 shadow-sm transition" style="background-color: #172a40; border-color: #172a40;" type="button" onclick="addOccasionItem('lunch', 'NJR')">
                        <i class="ti ti-plus text-xs" style="color: #B89047;"></i> Add
                    </button>
                </div>
                <ul class="list-group small" id="list-occasion-lunch-NJR"></ul>
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
                <input type="hidden" id="input-promo-lunch-NJR" name="session[lunch][additional_data][promo_items]" value="{{ $lcPromoValue ?? '[]' }}">
                <div class="input-group mb-2.5">
                    <select class="form-select form-select-sm rounded-l-xl border-slate-300 text-xs font-medium" id="promo-type-lunch-NJR" onchange="togglePromoOther('lunch', 'NJR')">
                        <option value="" selected>Select Promo...</option>
                        <option value="Mandiri Card">Mandiri Card</option>
                        <option value="BCA Card">BCA Card</option>
                        <option value="Membership">Membership</option>
                        <option value="Other">Other</option>
                    </select>
                    <input type="text" class="form-control form-control-sm d-none border-slate-300 text-xs" id="promo-other-lunch-NJR" placeholder="Promo Name" style="max-width: 150px;">
                    <input type="number" class="form-control form-control-sm border-slate-300 text-xs" id="promo-pax-lunch-NJR"
                        placeholder="Pax" style="max-width: 80px;">
                    <input type="text" class="form-control form-control-sm rupiah border-slate-300 text-xs" id="promo-revenue-lunch-NJR"
                        placeholder="Revenue" style="max-width: 120px;" autocomplete="off">
                    <button class="btn btn-sm rounded-r-xl px-3.5 text-xs font-semibold text-white inline-flex items-center gap-1 shadow-sm transition" style="background-color: #172a40; border-color: #172a40;" type="button" onclick="addPromoItem('lunch', 'NJR')">
                        <i class="ti ti-plus text-xs" style="color: #B89047;"></i> Add
                    </button>
                </div>
                <ul class="list-group small" id="list-promo-lunch-NJR"></ul>
            </div>
        </div>

        <div class="my-5 border-t border-slate-100"></div>

        {{-- 4. SET MENU --}}
        <div class="flex items-center gap-2.5 pb-2 mb-3 border-b border-slate-100">
            <span class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold shrink-0" style="background-color: rgba(23, 42, 64, 0.08); color: #172a40;">4</span>
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700">Set Menu</h4>
        </div>
        <div class="row g-3">
            <div class="col-md-12">
                <input type="hidden" id="input-setmenu-lunch-NJR" name="session[lunch][additional_data][setmenu_items]" value="{{ $lcSetMenuValue ?? '[]' }}">
                <div class="input-group mb-2.5">
                    <select class="form-select form-select-sm rounded-l-xl border-slate-300 text-xs font-medium" id="setmenu-type-lunch-NJR">
                        <option value="" selected>Select Set Menu...</option>
                        <option value="Matsusaka A5">Matsusaka A5</option>
                        <option value="Kobe A5">Kobe A5</option>
                        <option value="Miyazaki A5">Miyazaki A5</option>
                        <option value="Kumamoto A5">Kumamoto A5</option>
                        <option value="Sendai A5">Sendai A5</option>
                        <option value="Iwate A5">Iwate A5</option>
                        <option value="Hokkaido A5">Hokkaido A5</option>
                        <option value="Saga A5">Saga A5</option>
                        <option value="Oita A5">Oita A5</option>
                    </select>
                    <input type="number" class="form-control form-control-sm border-slate-300 text-xs" id="setmenu-pax-lunch-NJR"
                        placeholder="Pax" style="max-width: 80px;">
                    <input type="text" class="form-control form-control-sm rupiah border-slate-300 text-xs" id="setmenu-revenue-lunch-NJR"
                        placeholder="Revenue" style="max-width: 120px;" autocomplete="off">
                    <button class="btn btn-sm rounded-r-xl px-3.5 text-xs font-semibold text-white inline-flex items-center gap-1 shadow-sm transition" style="background-color: #172a40; border-color: #172a40;" type="button" onclick="addSetMenuItem('lunch', 'NJR')">
                        <i class="ti ti-plus text-xs" style="color: #B89047;"></i> Add
                    </button>
                </div>
                <ul class="list-group small" id="list-setmenu-lunch-NJR"></ul>
            </div>
        </div>

        <div class="my-5 border-t border-slate-100"></div>

        {{-- 5. REVENUE REPORT --}}
        <div class="flex items-center gap-2.5 pb-2 mb-3 border-b border-slate-100">
            <span class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold shrink-0" style="background-color: rgba(23, 42, 64, 0.08); color: #172a40;">5</span>
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

        {{-- 6. REVENUE TEPPAN --}}
        <div class="flex items-center gap-2.5 pb-2 mb-3 border-b border-slate-100">
            <span class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold shrink-0" style="background-color: rgba(23, 42, 64, 0.08); color: #172a40;">6</span>
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700">Revenue Teppan Items</h4>
        </div>
        <div class="row g-3">
            <div class="col-md-12">
                <input type="hidden" id="input-teppan-lunch-NJR" name="session[lunch][additional_data][revenue_teppan_items]" value="{{ $lcTeppanValue ?? '[]' }}">
                <div class="input-group mb-2.5">
                    <select class="form-select form-select-sm rounded-l-xl border-slate-300 text-xs font-medium" id="teppan-type-lunch-NJR">
                        <option value="" selected>Select Teppan Set...</option>
                        <option value="Teppan Set A">Teppan Set A</option>
                        <option value="Teppan Set B">Teppan Set B</option>
                        <option value="Teppan Set C">Teppan Set C</option>
                        <option value="Teppan Set D">Teppan Set D</option>
                        <option value="Teppan Set E">Teppan Set E</option>
                        <option value="Teppan Set F">Teppan Set F</option>
                        <option value="Teppan Set G">Teppan Set G</option>
                    </select>
                    <input type="number" class="form-control form-control-sm border-slate-300 text-xs" id="teppan-pax-lunch-NJR"
                        placeholder="Pax" style="max-width: 80px;">
                    <input type="text" class="form-control form-control-sm rupiah border-slate-300 text-xs" id="teppan-revenue-lunch-NJR"
                        placeholder="Revenue" style="max-width: 120px;" autocomplete="off">
                    <button class="btn btn-sm rounded-r-xl px-3.5 text-xs font-semibold text-white inline-flex items-center gap-1 shadow-sm transition" style="background-color: #172a40; border-color: #172a40;" type="button" onclick="addTeppanItem('lunch', 'NJR')">
                        <i class="ti ti-plus text-xs" style="color: #B89047;"></i> Add
                    </button>
                </div>
                <ul class="list-group small" id="list-teppan-lunch-NJR"></ul>
            </div>
        </div>

        <div class="my-5 border-t border-slate-100"></div>

        {{-- 7. UPSELLING & REMARKS --}}
        <div class="flex items-center gap-2.5 pb-2 mb-3 border-b border-slate-100">
            <span class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold shrink-0" style="background-color: rgba(23, 42, 64, 0.08); color: #172a40;">7</span>
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700">Upselling & Remarks</h4>
        </div>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label text-xs font-semibold text-slate-700 mb-1">Upselling Menu (Food)</label>
                @php $lcFoodVal = old('session.lunch.upselling_data.food', $lc->upselling_data['food'] ?? []); @endphp
                <input type="hidden" id="input-lunch-food-NJR" name="session[lunch][upselling_data][food]"
                    value="{{ is_array($lcFoodVal) ? json_encode($lcFoodVal) : $lcFoodVal }}">
                <div class="input-group mb-2">
                    <input type="text" class="form-control form-control-sm rounded-l-xl border-slate-300 text-xs" id="select-lunch-food-NJR" placeholder="Enter food name...">
                    <input type="number" class="form-control form-control-sm border-slate-300 text-xs" id="pax-lunch-food-NJR"
                        placeholder="Qty" style="max-width: 70px;">
                    <button class="btn btn-sm rounded-r-xl px-3.5 text-xs font-semibold text-white inline-flex items-center gap-1 shadow-sm transition" style="background-color: #172a40; border-color: #172a40;" type="button"
                        onclick="addUpsellingItem('lunch', 'food', 'NJR')"><i class="ti ti-plus text-xs" style="color: #B89047;"></i> Add</button>
                </div>
                <ul class="list-group small" id="list-lunch-food-NJR"></ul>
            </div>
            <div class="col-md-6">
                <label class="form-label text-xs font-semibold text-slate-700 mb-1">Beverage Upselling</label>
                @php $lcBevVal = old('session.lunch.upselling_data.beverage', $lc->upselling_data['beverage'] ?? []); @endphp
                <input type="hidden" id="input-lunch-beverage-NJR" name="session[lunch][upselling_data][beverage]"
                    value="{{ is_array($lcBevVal) ? json_encode($lcBevVal) : $lcBevVal }}">
                <div class="input-group mb-2">
                    <input type="text" class="form-control form-control-sm rounded-l-xl border-slate-300 text-xs" id="select-lunch-beverage-NJR" placeholder="Enter Beverage Name">
                    <input type="number" class="form-control form-control-sm border-slate-300 text-xs" id="pax-lunch-beverage-NJR"
                        placeholder="Qty" style="max-width: 70px;">
                    <button class="btn btn-sm rounded-r-xl px-3.5 text-xs font-semibold text-white inline-flex items-center gap-1 shadow-sm transition" style="background-color: #172a40; border-color: #172a40;" type="button"
                        onclick="addUpsellingItem('lunch', 'beverage', 'NJR')"><i class="ti ti-plus text-xs" style="color: #B89047;"></i> Add</button>
                </div>
                <ul class="list-group small" id="list-lunch-beverage-NJR"></ul>
            </div>

            <div class="col-md-12">
                <label class="form-label text-xs font-semibold text-slate-700 mb-1">General Remarks</label>
                <textarea class="form-control rounded-xl border-slate-300 p-2.5 text-xs focus:border-[#172a40] focus:ring-[#172a40]" rows="2" name="session[lunch][remarks]" placeholder="Catatan operasional sesi lunch...">{{ old('session.lunch.remarks', $lc->remarks ?? '') }}</textarea>
            </div>

            <div class="col-md-12 mt-3">
                <label class="form-label text-xs font-semibold text-slate-700 mb-1">Staff on Duty</label>
                @php $lcStaffVal = old('session.lunch.staff_on_duty', $lc->staff_on_duty ?? []); @endphp
                <input type="hidden" id="input-staff-lunch-NJR" name="session[lunch][staff_on_duty]"
                    value="{{ is_array($lcStaffVal) ? json_encode($lcStaffVal) : $lcStaffVal }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm rounded-l-xl border-slate-300 text-xs" id="select-staff-lunch-NJR">
                        <option value="" selected>Select Staff...</option>
                        @foreach ($myStaffList as $staff)
                            <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                        @endforeach
                    </select>
                    <button class="btn btn-sm rounded-r-xl px-3.5 text-xs font-semibold text-white inline-flex items-center gap-1 shadow-sm transition" style="background-color: #172a40; border-color: #172a40;" type="button" onclick="addStaffItem('lunch', 'NJR')">
                        <i class="ti ti-plus text-xs" style="color: #B89047;"></i> Add
                    </button>
                </div>
                <div id="list-staff-lunch-NJR" class="d-flex flex-wrap gap-1.5"></div>
            </div>
        </div>

        <div class="my-5 border-t border-slate-100"></div>

        {{-- 8. COMPETITOR COMPARISON --}}
        <div class="flex items-center gap-2.5 pb-2 mb-3 border-b border-slate-100">
            <span class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold shrink-0" style="background-color: rgba(23, 42, 64, 0.08); color: #172a40;">8</span>
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
                <p class="text-xs text-slate-500">Pencatatan sesi malam Nagano, cover Teppanyaki/Yakiniku, dan revenue</p>
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

        {{-- Teppanyaki --}}
        <div class="rounded-xl border border-slate-200/90 bg-slate-50/50 p-3.5 mb-3">
            <div class="flex items-center gap-2 mb-2.5">
                <span class="px-2.5 py-0.5 rounded-md text-[11px] font-bold tracking-wider uppercase bg-slate-800 text-white">TEPPANYAKI</span>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label text-xs font-semibold text-slate-600 mb-1">In-House (Adult)</label>
                    <input type="number" class="form-control rounded-xl border-slate-300 py-2 text-sm focus:border-[#172a40] focus:ring-[#172a40]"
                        name="session[dinner][cover_data][teppanyaki_inhouse]"
                        value="{{ old('session.dinner.cover_data.teppanyaki_inhouse', $dn->cover_data['teppanyaki_inhouse'] ?? '') }}"
                        placeholder="0">
                </div>
                <div class="col-md-6">
                    <label class="form-label text-xs font-semibold text-slate-600 mb-1">Walk-In (Adult)</label>
                    <input type="number" class="form-control rounded-xl border-slate-300 py-2 text-sm focus:border-[#172a40] focus:ring-[#172a40]"
                        name="session[dinner][cover_data][teppanyaki_walkin]"
                        value="{{ old('session.dinner.cover_data.teppanyaki_walkin', $dn->cover_data['teppanyaki_walkin'] ?? '') }}"
                        placeholder="0">
                </div>
            </div>
        </div>

        {{-- Yakiniku --}}
        <div class="rounded-xl border border-slate-200/90 bg-slate-50/50 p-3.5 mb-4">
            <div class="flex items-center gap-2 mb-2.5">
                <span class="px-2.5 py-0.5 rounded-md text-[11px] font-bold tracking-wider uppercase bg-rose-700 text-white">YAKINIKU</span>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label text-xs font-semibold text-slate-600 mb-1">In-House (Adult)</label>
                    <input type="number" class="form-control rounded-xl border-slate-300 py-2 text-sm focus:border-[#172a40] focus:ring-[#172a40]"
                        name="session[dinner][cover_data][yakiniku_inhouse]"
                        value="{{ old('session.dinner.cover_data.yakiniku_inhouse', $dn->cover_data['yakiniku_inhouse'] ?? '') }}"
                        placeholder="0">
                </div>
                <div class="col-md-6">
                    <label class="form-label text-xs font-semibold text-slate-600 mb-1">Walk-In (Adult)</label>
                    <input type="number" class="form-control rounded-xl border-slate-300 py-2 text-sm focus:border-[#172a40] focus:ring-[#172a40]"
                        name="session[dinner][cover_data][yakiniku_walkin]"
                        value="{{ old('session.dinner.cover_data.yakiniku_walkin', $dn->cover_data['yakiniku_walkin'] ?? '') }}"
                        placeholder="0">
                </div>
            </div>
        </div>

        <div class="my-5 border-t border-slate-100"></div>

        {{-- 2. OCCASION --}}
        <div class="flex items-center gap-2.5 pb-2 mb-3 border-b border-slate-100">
            <span class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold shrink-0" style="background-color: rgba(23, 42, 64, 0.08); color: #172a40;">2</span>
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700">Occasion / Event Type</h4>
        </div>
        <div class="row g-3">
            <div class="col-md-12">
                <input type="hidden" id="input-occasion-dinner-NJR" name="session[dinner][additional_data][occasion_items]" value="{{ $dnOccasionValue ?? '[]' }}">
                <div class="input-group mb-2.5">
                    <select class="form-select form-select-sm rounded-l-xl border-slate-300 text-xs font-medium" id="occasion-type-dinner-NJR" onchange="toggleOccasionOther('dinner', 'NJR')">
                        <option value="" selected>Select Occasion...</option>
                        <option value="Wedding Party">Wedding Party</option>
                        <option value="Birthday Party">Birthday Party</option>
                        <option value="Social Event">Social Event</option>
                        <option value="Corporate Event">Corporate Event</option>
                        <option value="Other">Other</option>
                    </select>
                    <input type="text" class="form-control form-control-sm d-none border-slate-300 text-xs" id="occasion-other-dinner-NJR" placeholder="Occasion Type" style="max-width: 150px;">
                    <input type="text" class="form-control form-control-sm border-slate-300 text-xs" id="occasion-name-dinner-NJR"
                        placeholder="Name (e.g. Mr. Budi)">
                    <input type="number" class="form-control form-control-sm border-slate-300 text-xs" id="occasion-pax-dinner-NJR"
                        placeholder="Pax" style="max-width: 80px;">
                    <input type="text" class="form-control form-control-sm rupiah border-slate-300 text-xs" id="occasion-revenue-dinner-NJR"
                        placeholder="Revenue" style="max-width: 120px;" autocomplete="off">
                    <button class="btn btn-sm rounded-r-xl px-3.5 text-xs font-semibold text-white inline-flex items-center gap-1 shadow-sm transition" style="background-color: #172a40; border-color: #172a40;" type="button" onclick="addOccasionItem('dinner', 'NJR')">
                        <i class="ti ti-plus text-xs" style="color: #B89047;"></i> Add
                    </button>
                </div>
                <ul class="list-group small" id="list-occasion-dinner-NJR"></ul>
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
                <input type="hidden" id="input-promo-dinner-NJR" name="session[dinner][additional_data][promo_items]" value="{{ $dnPromoValue ?? '[]' }}">
                <div class="input-group mb-2.5">
                    <select class="form-select form-select-sm rounded-l-xl border-slate-300 text-xs font-medium" id="promo-type-dinner-NJR" onchange="togglePromoOther('dinner', 'NJR')">
                        <option value="" selected>Select Promo...</option>
                        <option value="Mandiri Card">Mandiri Card</option>
                        <option value="BCA Card">BCA Card</option>
                        <option value="Membership">Membership</option>
                        <option value="Other">Other</option>
                    </select>
                    <input type="text" class="form-control form-control-sm d-none border-slate-300 text-xs" id="promo-other-dinner-NJR" placeholder="Promo Name" style="max-width: 150px;">
                    <input type="number" class="form-control form-control-sm border-slate-300 text-xs" id="promo-pax-dinner-NJR"
                        placeholder="Pax" style="max-width: 80px;">
                    <input type="text" class="form-control form-control-sm rupiah border-slate-300 text-xs" id="promo-revenue-dinner-NJR"
                        placeholder="Revenue" style="max-width: 120px;" autocomplete="off">
                    <button class="btn btn-sm rounded-r-xl px-3.5 text-xs font-semibold text-white inline-flex items-center gap-1 shadow-sm transition" style="background-color: #172a40; border-color: #172a40;" type="button" onclick="addPromoItem('dinner', 'NJR')">
                        <i class="ti ti-plus text-xs" style="color: #B89047;"></i> Add
                    </button>
                </div>
                <ul class="list-group small" id="list-promo-dinner-NJR"></ul>
            </div>
        </div>

        <div class="my-5 border-t border-slate-100"></div>

        {{-- 4. SET MENU --}}
        <div class="flex items-center gap-2.5 pb-2 mb-3 border-b border-slate-100">
            <span class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold shrink-0" style="background-color: rgba(23, 42, 64, 0.08); color: #172a40;">4</span>
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700">Set Menu</h4>
        </div>
        <div class="row g-3">
            <div class="col-md-12">
                <input type="hidden" id="input-setmenu-dinner-NJR" name="session[dinner][additional_data][setmenu_items]" value="{{ $dnSetMenuValue ?? '[]' }}">
                <div class="input-group mb-2.5">
                    <select class="form-select form-select-sm rounded-l-xl border-slate-300 text-xs font-medium" id="setmenu-type-dinner-NJR">
                        <option value="" selected>Select Set Menu...</option>
                        <option value="Matsusaka A5">Matsusaka A5</option>
                        <option value="Kobe A5">Kobe A5</option>
                        <option value="Miyazaki A5">Miyazaki A5</option>
                        <option value="Kumamoto A5">Kumamoto A5</option>
                        <option value="Sendai A5">Sendai A5</option>
                        <option value="Iwate A5">Iwate A5</option>
                        <option value="Hokkaido A5">Hokkaido A5</option>
                        <option value="Saga A5">Saga A5</option>
                        <option value="Oita A5">Oita A5</option>
                    </select>
                    <input type="number" class="form-control form-control-sm border-slate-300 text-xs" id="setmenu-pax-dinner-NJR"
                        placeholder="Pax" style="max-width: 80px;">
                    <input type="text" class="form-control form-control-sm rupiah border-slate-300 text-xs" id="setmenu-revenue-dinner-NJR"
                        placeholder="Revenue" style="max-width: 120px;" autocomplete="off">
                    <button class="btn btn-sm rounded-r-xl px-3.5 text-xs font-semibold text-white inline-flex items-center gap-1 shadow-sm transition" style="background-color: #172a40; border-color: #172a40;" type="button" onclick="addSetMenuItem('dinner', 'NJR')">
                        <i class="ti ti-plus text-xs" style="color: #B89047;"></i> Add
                    </button>
                </div>
                <ul class="list-group small" id="list-setmenu-dinner-NJR"></ul>
            </div>
        </div>

        <div class="my-5 border-t border-slate-100"></div>

        {{-- 5. REVENUE REPORT --}}
        <div class="flex items-center gap-2.5 pb-2 mb-3 border-b border-slate-100">
            <span class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold shrink-0" style="background-color: rgba(23, 42, 64, 0.08); color: #172a40;">5</span>
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

        {{-- 6. REVENUE TEPPAN --}}
        <div class="flex items-center gap-2.5 pb-2 mb-3 border-b border-slate-100">
            <span class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold shrink-0" style="background-color: rgba(23, 42, 64, 0.08); color: #172a40;">6</span>
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700">Revenue Teppan Items</h4>
        </div>
        <div class="row g-3">
            <div class="col-md-12">
                <input type="hidden" id="input-teppan-dinner-NJR" name="session[dinner][additional_data][revenue_teppan_items]" value="{{ $dnTeppanValue ?? '[]' }}">
                <div class="input-group mb-2.5">
                    <select class="form-select form-select-sm rounded-l-xl border-slate-300 text-xs font-medium" id="teppan-type-dinner-NJR">
                        <option value="" selected>Select Teppan Set...</option>
                        <option value="Teppan Set A">Teppan Set A</option>
                        <option value="Teppan Set B">Teppan Set B</option>
                        <option value="Teppan Set C">Teppan Set C</option>
                        <option value="Teppan Set D">Teppan Set D</option>
                        <option value="Teppan Set E">Teppan Set E</option>
                        <option value="Teppan Set F">Teppan Set F</option>
                        <option value="Teppan Set G">Teppan Set G</option>
                    </select>
                    <input type="number" class="form-control form-control-sm border-slate-300 text-xs" id="teppan-pax-dinner-NJR"
                        placeholder="Pax" style="max-width: 80px;">
                    <input type="text" class="form-control form-control-sm rupiah border-slate-300 text-xs" id="teppan-revenue-dinner-NJR"
                        placeholder="Revenue" style="max-width: 120px;" autocomplete="off">
                    <button class="btn btn-sm rounded-r-xl px-3.5 text-xs font-semibold text-white inline-flex items-center gap-1 shadow-sm transition" style="background-color: #172a40; border-color: #172a40;" type="button" onclick="addTeppanItem('dinner', 'NJR')">
                        <i class="ti ti-plus text-xs" style="color: #B89047;"></i> Add
                    </button>
                </div>
                <ul class="list-group small" id="list-teppan-dinner-NJR"></ul>
            </div>
        </div>

        <div class="my-5 border-t border-slate-100"></div>

        {{-- 7. UPSELLING & REMARKS --}}
        <div class="flex items-center gap-2.5 pb-2 mb-3 border-b border-slate-100">
            <span class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold shrink-0" style="background-color: rgba(23, 42, 64, 0.08); color: #172a40;">7</span>
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700">Upselling & Remarks</h4>
        </div>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label text-xs font-semibold text-slate-700 mb-1">Upselling Menu (Food)</label>
                @php $dnFoodVal = old('session.dinner.upselling_data.food', $dn->upselling_data['food'] ?? []); @endphp
                <input type="hidden" id="input-dinner-food-NJR" name="session[dinner][upselling_data][food]"
                    value="{{ is_array($dnFoodVal) ? json_encode($dnFoodVal) : $dnFoodVal }}">
                <div class="input-group mb-2">
                    <input type="text" class="form-control form-control-sm rounded-l-xl border-slate-300 text-xs" id="select-dinner-food-NJR" placeholder="Enter food name...">
                    <input type="number" class="form-control form-control-sm border-slate-300 text-xs" id="pax-dinner-food-NJR"
                        placeholder="Qty" style="max-width: 70px;">
                    <button class="btn btn-sm rounded-r-xl px-3.5 text-xs font-semibold text-white inline-flex items-center gap-1 shadow-sm transition" style="background-color: #172a40; border-color: #172a40;" type="button"
                        onclick="addUpsellingItem('dinner', 'food', 'NJR')"><i class="ti ti-plus text-xs" style="color: #B89047;"></i> Add</button>
                </div>
                <ul class="list-group small" id="list-dinner-food-NJR"></ul>
            </div>
            <div class="col-md-6">
                <label class="form-label text-xs font-semibold text-slate-700 mb-1">Beverage Upselling</label>
                @php $dnBevVal = old('session.dinner.upselling_data.beverage', $dn->upselling_data['beverage'] ?? []); @endphp
                <input type="hidden" id="input-dinner-beverage-NJR" name="session[dinner][upselling_data][beverage]"
                    value="{{ is_array($dnBevVal) ? json_encode($dnBevVal) : $dnBevVal }}">
                <div class="input-group mb-2">
                    <input type="text" class="form-control form-control-sm rounded-l-xl border-slate-300 text-xs" id="select-dinner-beverage-NJR" placeholder="Enter Beverage Name">
                    <input type="number" class="form-control form-control-sm border-slate-300 text-xs" id="pax-dinner-beverage-NJR"
                        placeholder="Qty" style="max-width: 70px;">
                    <button class="btn btn-sm rounded-r-xl px-3.5 text-xs font-semibold text-white inline-flex items-center gap-1 shadow-sm transition" style="background-color: #172a40; border-color: #172a40;" type="button"
                        onclick="addUpsellingItem('dinner', 'beverage', 'NJR')"><i class="ti ti-plus text-xs" style="color: #B89047;"></i> Add</button>
                </div>
                <ul class="list-group small" id="list-dinner-beverage-NJR"></ul>
            </div>

            <div class="col-md-12">
                <label class="form-label text-xs font-semibold text-slate-700 mb-1">General Remarks</label>
                <textarea class="form-control rounded-xl border-slate-300 p-2.5 text-xs focus:border-[#172a40] focus:ring-[#172a40]" rows="2" name="session[dinner][remarks]" placeholder="Catatan operasional sesi dinner...">{{ old('session.dinner.remarks', $dn->remarks ?? '') }}</textarea>
            </div>

            <div class="col-md-12 mt-3">
                <label class="form-label text-xs font-semibold text-slate-700 mb-1">Staff on Duty</label>
                @php $dnStaffVal = old('session.dinner.staff_on_duty', $dn->staff_on_duty ?? []); @endphp
                <input type="hidden" id="input-staff-dinner-NJR" name="session[dinner][staff_on_duty]"
                    value="{{ is_array($dnStaffVal) ? json_encode($dnStaffVal) : $dnStaffVal }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm rounded-l-xl border-slate-300 text-xs" id="select-staff-dinner-NJR">
                        <option value="" selected>Select Staff...</option>
                        @foreach ($myStaffList as $staff)
                            <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                        @endforeach
                    </select>
                    <button class="btn btn-sm rounded-r-xl px-3.5 text-xs font-semibold text-white inline-flex items-center gap-1 shadow-sm transition" style="background-color: #172a40; border-color: #172a40;" type="button" onclick="addStaffItem('dinner', 'NJR')">
                        <i class="ti ti-plus text-xs" style="color: #B89047;"></i> Add
                    </button>
                </div>
                <div id="list-staff-dinner-NJR" class="d-flex flex-wrap gap-1.5"></div>
            </div>
        </div>

        <div class="my-5 border-t border-slate-100"></div>

        {{-- 8. COMPETITOR COMPARISON --}}
        <div class="flex items-center gap-2.5 pb-2 mb-3 border-b border-slate-100">
            <span class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold shrink-0" style="background-color: rgba(23, 42, 64, 0.08); color: #172a40;">8</span>
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
    var teppanState = window.teppanState || {};

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
        const typeEl = document.getElementById('teppan-type-' + session + '-' + code);
        const paxEl = document.getElementById('teppan-pax-' + session + '-' + code);
        const revEl = document.getElementById('teppan-revenue-' + session + '-' + code);
        const type = typeEl.value;
        const pax = paxEl.value.trim();
        let revenue = revEl.value.replace(/\./g, '').trim();

        if (!type) { alert('Please select a teppan set.'); return; }
        if (!pax || parseInt(pax) <= 0) { alert('Please enter valid pax.'); return; }

        const key = session + '_' + code;
        if (!teppanState[key]) teppanState[key] = [];
        teppanState[key].push({
            type: type,
            pax: parseInt(pax),
            revenue: revenue ? parseInt(revenue) : 0
        });

        typeEl.value = '';
        paxEl.value = '';
        revEl.value = '';
        typeEl.focus();

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
            html += '<li class="list-group-item d-flex justify-content-between align-items-center py-1.5 px-3 rounded-xl border border-slate-200 mb-1.5 bg-slate-50/50">' +
                '<span class="text-xs font-medium text-slate-700"><span class="badge bg-slate-600 me-1">' + escapeHtml(item.type || '') + '</span>' +
                ' <span class="badge bg-blue-600 ms-1">' + (item.pax || 0) + ' pax</span>' +
                (rev ? ' <span class="badge bg-emerald-600 ms-1">' + rev + '</span>' : '') +
                '</span>' +
                '<button class="btn btn-sm btn-link text-rose-500 hover:text-rose-700 p-0" onclick="removeTeppanItem(\'' + session + '\', \'' + code + '\', ' + index + ')" title="Remove"><i class="ti ti-x text-sm"></i></button>' +
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
            html += '<li class="list-group-item d-flex justify-content-between align-items-center py-1.5 px-3 rounded-xl border border-slate-200 mb-1.5 bg-slate-50/50">' +
                '<span class="text-xs font-medium text-slate-700"><span class="badge bg-slate-600 me-1">' + escapeHtml(item.type || '') + '</span>' +
                ' <span class="badge bg-blue-600 ms-1">' + (item.pax || 0) + ' pax</span>' +
                (rev ? ' <span class="badge bg-emerald-600 ms-1">' + rev + '</span>' : '') +
                '</span>' +
                '<button class="btn btn-sm btn-link text-rose-500 hover:text-rose-700 p-0" onclick="removeSetMenuItem(\'' + session + '\', \'' + code + '\', ' + index + ')" title="Remove"><i class="ti ti-x text-sm"></i></button>' +
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
