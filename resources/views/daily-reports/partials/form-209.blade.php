{{-- FORM KHUSUS 209 DINING --}}
@php
    $bf = $details['breakfast'] ?? null;
    $lc = $details['lunch'] ?? null;
    $dn = $details['dinner'] ?? null;

    $myMenu = $upsellingItems[1] ?? collect([]);
    $foods = $myMenu->where('type', 'food');
    $beverages = $myMenu->where('type', 'beverage');

    $resto209 = $restaurants->where('code', '209')->first();
    $myStaffList = $resto209 ? $resto209->employees : [];

    // Ambil data (bisa dari old input array, atau database array)
    $bfOccasionData = old('session.breakfast.additional_data.occasion_items', $bf->additional_data['occasion_items'] ?? []);
    $bfOccasionValue = is_array($bfOccasionData) ? json_encode($bfOccasionData) : $bfOccasionData;
    $lcOccasionData = old('session.lunch.additional_data.occasion_items', $lc->additional_data['occasion_items'] ?? []);
    $lcOccasionValue = is_array($lcOccasionData) ? json_encode($lcOccasionData) : $lcOccasionData;
    $dnOccasionData = old('session.dinner.additional_data.occasion_items', $dn->additional_data['occasion_items'] ?? []);
    $dnOccasionValue = is_array($dnOccasionData) ? json_encode($dnOccasionData) : $dnOccasionData;

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

    // F. Promo Items (dynamic repeater)
    $bfPromoData = old('session.breakfast.additional_data.promo_items', $bf->additional_data['promo_items'] ?? []);
    $bfPromoValue = is_array($bfPromoData) ? json_encode($bfPromoData) : $bfPromoData;
    $lcPromoData = old('session.lunch.additional_data.promo_items', $lc->additional_data['promo_items'] ?? []);
    $lcPromoValue = is_array($lcPromoData) ? json_encode($lcPromoData) : $lcPromoData;
    $dnPromoData = old('session.dinner.additional_data.promo_items', $dn->additional_data['promo_items'] ?? []);
    $dnPromoValue = is_array($dnPromoData) ? json_encode($dnPromoData) : $dnPromoData;
@endphp

{{-- === SESSION: BREAKFAST === --}}
<div class="rounded-2xl border border-slate-200/90 shadow-sm bg-white overflow-hidden mb-6">
    <div class="px-6 py-4 bg-amber-50/70 border-b border-amber-100 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center font-bold shadow-xs">
                <i class="ti ti-sun text-xl"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-slate-800">Breakfast Report</h3>
                <p class="text-xs text-slate-500">Pencatatan sesi pagi, cover in-house / walk-in, dan revenue</p>
            </div>
        </div>
        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-amber-100/80 text-amber-800 border border-amber-200/60">
            Morning Session
        </span>
    </div>
    <div class="p-6">
        {{-- 1. Cover Report --}}
        <div class="flex items-center gap-2.5 pb-2 mb-3 border-b border-slate-100">
            <span class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold shrink-0" style="background-color: rgba(23, 42, 64, 0.08); color: #172a40;">1</span>
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700">Cover Report</h4>
        </div>
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label text-xs font-semibold text-slate-600 mb-1">In-House (Adult)</label>
                <input type="number" class="form-control rounded-xl border-slate-300 py-2 text-sm focus:border-[#172a40] focus:ring-[#172a40]" name="session[breakfast][cover_data][in_house_adult]"
                    value="{{ old('session.breakfast.cover_data.in_house_adult', $bf->cover_data['in_house_adult'] ?? '') }}"
                    placeholder="0">
            </div>
            <div class="col-md-3">
                <label class="form-label text-xs font-semibold text-slate-600 mb-1">In-House (Child)</label>
                <input type="number" class="form-control rounded-xl border-slate-300 py-2 text-sm focus:border-[#172a40] focus:ring-[#172a40]" name="session[breakfast][cover_data][in_house_child]"
                    value="{{ old('session.breakfast.cover_data.in_house_child', $bf->cover_data['in_house_child'] ?? '') }}"
                    placeholder="0">
            </div>
            <div class="col-md-3">
                <label class="form-label text-xs font-semibold text-slate-600 mb-1">Walk-In (Adult)</label>
                <input type="number" class="form-control rounded-xl border-slate-300 py-2 text-sm focus:border-[#172a40] focus:ring-[#172a40]" name="session[breakfast][cover_data][walk_in_adult]"
                    value="{{ old('session.breakfast.cover_data.walk_in_adult', $bf->cover_data['walk_in_adult'] ?? '') }}"
                    placeholder="0">
            </div>
            <div class="col-md-3">
                <label class="form-label text-xs font-semibold text-slate-600 mb-1">Walk-In (Child)</label>
                <input type="number" class="form-control rounded-xl border-slate-300 py-2 text-sm focus:border-[#172a40] focus:ring-[#172a40]" name="session[breakfast][cover_data][walk_in_child]"
                    value="{{ old('session.breakfast.cover_data.walk_in_child', $bf->cover_data['walk_in_child'] ?? '') }}"
                    placeholder="0">
            </div>
            <div class="col-md-3">
                <label class="form-label text-xs font-semibold text-rose-600 mb-1">BEO (Total)</label>
                <input type="number" class="form-control rounded-xl border-rose-200 bg-rose-50/30 py-2 text-sm focus:border-rose-400 focus:ring-rose-400" name="session[breakfast][cover_data][beo_total]"
                    value="{{ old('session.breakfast.cover_data.beo_total', $bf->cover_data['beo_total'] ?? '') }}"
                    placeholder="0">
            </div>
        </div>

        <div class="my-5 border-t border-slate-100"></div>

        {{-- 2. Occasion --}}
        <div class="flex items-center gap-2.5 pb-2 mb-3 border-b border-slate-100">
            <span class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold shrink-0" style="background-color: rgba(23, 42, 64, 0.08); color: #172a40;">2</span>
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700">Occasion</h4>
        </div>
        <div class="row g-3">
            <div class="col-md-12">
                <input type="hidden" id="input-occasion-breakfast-209" name="session[breakfast][additional_data][occasion_items]" value="{{ $bfOccasionValue ?? '[]' }}">
                <div class="input-group mb-2.5">
                    <select class="form-select form-select-sm rounded-l-xl border-slate-300 text-xs font-medium" id="occasion-type-breakfast-209" onchange="toggleOccasionOther('breakfast', '209')">
                        <option value="" selected>Select Occasion...</option>
                        <option value="Wedding Party">Wedding Party</option>
                        <option value="Birthday Party">Birthday Party</option>
                        <option value="Social Event">Social Event</option>
                        <option value="Corporate Event">Corporate Event</option>
                        <option value="Other">Other</option>
                    </select>
                    <input type="text" class="form-control form-control-sm d-none border-slate-300 text-xs" id="occasion-other-breakfast-209" placeholder="Occasion Type" style="max-width: 150px;">
                    <input type="text" class="form-control form-control-sm border-slate-300 text-xs" id="occasion-name-breakfast-209"
                        placeholder="Name (e.g. Mr. Budi)">
                    <input type="number" class="form-control form-control-sm border-slate-300 text-xs" id="occasion-pax-breakfast-209"
                        placeholder="Pax" style="max-width: 80px;">
                    <input type="text" class="form-control form-control-sm rupiah border-slate-300 text-xs" id="occasion-revenue-breakfast-209"
                        placeholder="Revenue" style="max-width: 120px;" autocomplete="off">
                    <button class="btn btn-sm rounded-r-xl px-3.5 text-xs font-semibold text-white inline-flex items-center gap-1 shadow-sm transition" style="background-color: #172a40; border-color: #172a40;" type="button" onclick="addOccasionItem('breakfast', '209')">
                        <i class="ti ti-plus text-xs" style="color: #B89047;"></i> Add
                    </button>
                </div>
                <ul class="list-group small" id="list-occasion-breakfast-209"></ul>
            </div>
        </div>

        <div class="my-5 border-t border-slate-100"></div>

        {{-- 3. Promo --}}
        <div class="flex items-center gap-2.5 pb-2 mb-3 border-b border-slate-100">
            <span class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold shrink-0" style="background-color: rgba(23, 42, 64, 0.08); color: #172a40;">3</span>
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700">Promo</h4>
        </div>
        <div class="row g-3">
            <div class="col-md-12">
                <input type="hidden" id="input-promo-breakfast-209" name="session[breakfast][additional_data][promo_items]" value="{{ $bfPromoValue ?? '[]' }}">
                <div class="input-group mb-2.5">
                    <select class="form-select form-select-sm rounded-l-xl border-slate-300 text-xs font-medium" id="promo-type-breakfast-209" onchange="togglePromoOther('breakfast', '209')">
                        <option value="" selected>Select Promo...</option>
                        <option value="Mandiri Card">Mandiri Card</option>
                        <option value="BCA Card">BCA Card</option>
                        <option value="Membership">Membership</option>
                        <option value="Other">Other</option>
                    </select>
                    <input type="text" class="form-control form-control-sm d-none border-slate-300 text-xs" id="promo-other-breakfast-209" placeholder="Promo Name" style="max-width: 150px;">
                    <input type="number" class="form-control form-control-sm border-slate-300 text-xs" id="promo-pax-breakfast-209"
                        placeholder="Pax" style="max-width: 80px;">
                    <input type="text" class="form-control form-control-sm rupiah border-slate-300 text-xs" id="promo-revenue-breakfast-209"
                        placeholder="Revenue" style="max-width: 120px;" autocomplete="off">
                    <button class="btn btn-sm rounded-r-xl px-3.5 text-xs font-semibold text-white inline-flex items-center gap-1 shadow-sm transition" style="background-color: #172a40; border-color: #172a40;" type="button" onclick="addPromoItem('breakfast', '209')">
                        <i class="ti ti-plus text-xs" style="color: #B89047;"></i> Add
                    </button>
                </div>
                <ul class="list-group small" id="list-promo-breakfast-209"></ul>
            </div>
        </div>

        <div class="my-5 border-t border-slate-100"></div>

        {{-- 4. Revenue Report (IDR) --}}
        <div class="flex items-center gap-2.5 pb-2 mb-3 border-b border-slate-100">
            <span class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold shrink-0" style="background-color: rgba(23, 42, 64, 0.08); color: #172a40;">4</span>
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700">Revenue Report (IDR)</h4>
        </div>
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label text-xs font-semibold text-slate-600 mb-1">Food Revenue</label>
                <div class="input-group">
                    <span class="input-group-text rounded-l-xl bg-slate-50 text-slate-500 text-xs font-bold border-slate-300">Rp</span>
                    <input type="text" class="form-control rounded-r-xl border-slate-300 py-2 text-sm rupiah focus:border-[#172a40] focus:ring-[#172a40]" name="session[breakfast][revenue_food]"
                        value="{{ old('session.breakfast.revenue_food', isset($bf->revenue_food) ? number_format($bf->revenue_food, 0, ',', '.') : '') }}"
                        placeholder="0" autocomplete="off">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label text-xs font-semibold text-slate-600 mb-1">Beverage Revenue</label>
                <div class="input-group">
                    <span class="input-group-text rounded-l-xl bg-slate-50 text-slate-500 text-xs font-bold border-slate-300">Rp</span>
                    <input type="text" class="form-control rounded-r-xl border-slate-300 py-2 text-sm rupiah focus:border-[#172a40] focus:ring-[#172a40]" name="session[breakfast][revenue_beverage]"
                        value="{{ old('session.breakfast.revenue_beverage', isset($bf->revenue_beverage) ? number_format($bf->revenue_beverage, 0, ',', '.') : '') }}"
                        placeholder="0" autocomplete="off">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label text-xs font-semibold text-slate-600 mb-1">Others Revenue</label>
                <div class="input-group">
                    <span class="input-group-text rounded-l-xl bg-slate-50 text-slate-500 text-xs font-bold border-slate-300">Rp</span>
                    <input type="text" class="form-control rounded-r-xl border-slate-300 py-2 text-sm rupiah focus:border-[#172a40] focus:ring-[#172a40]" name="session[breakfast][revenue_others]"
                        value="{{ old('session.breakfast.revenue_others', isset($bf->revenue_others) ? number_format($bf->revenue_others, 0, ',', '.') : '') }}"
                        placeholder="0" autocomplete="off">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label text-xs font-semibold text-slate-600 mb-1">Total Event Revenue</label>
                <div class="input-group">
                    <span class="input-group-text rounded-l-xl bg-slate-50 text-slate-500 text-xs font-bold border-slate-300">Rp</span>
                    <input type="text" class="form-control rounded-r-xl border-slate-300 py-2 text-sm rupiah focus:border-[#172a40] focus:ring-[#172a40]" name="session[breakfast][revenue_event]"
                        value="{{ old('session.breakfast.revenue_event', isset($bf->revenue_event) ? number_format($bf->revenue_event, 0, ',', '.') : '') }}"
                        placeholder="0" autocomplete="off">
                </div>
            </div>
        </div>

        <div class="my-5 border-t border-slate-100"></div>

        {{-- 5. Upselling & Remarks --}}
        <div class="flex items-center gap-2.5 pb-2 mb-3 border-b border-slate-100">
            <span class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold shrink-0" style="background-color: rgba(23, 42, 64, 0.08); color: #172a40;">5</span>
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700">Upselling & Remarks</h4>
        </div>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label text-xs font-semibold text-slate-700 mb-1">Upselling Menu (Food)</label>
                <input type="hidden" id="input-breakfast-food-209" name="session[breakfast][upselling_data][food]"
                    value="{{ $bfFoodValue }}">
                <div class="input-group mb-2">
                    <input type="text" class="form-control form-control-sm rounded-l-xl border-slate-300 text-xs" id="select-breakfast-food-209" placeholder="Enter Food Name">
                    <input type="number" class="form-control form-control-sm border-slate-300 text-xs" id="pax-breakfast-food-209"
                        placeholder="Qty" style="max-width: 70px;">
                    <button class="btn btn-sm rounded-r-xl px-3.5 text-xs font-semibold text-white inline-flex items-center gap-1 shadow-sm transition" style="background-color: #172a40; border-color: #172a40;" type="button"
                        onclick="addUpsellingItem('breakfast', 'food', '209')">
                        <i class="ti ti-plus text-xs" style="color: #B89047;"></i> Add
                    </button>
                </div>
                <ul class="list-group small" id="list-breakfast-food-209"></ul>
            </div>

            <div class="col-md-6">
                <label class="form-label text-xs font-semibold text-slate-700 mb-1">Beverage Upselling</label>
                <input type="hidden" id="input-breakfast-beverage-209"
                    name="session[breakfast][upselling_data][beverage]" value="{{ $bfBevValue }}">
                <div class="input-group mb-2">
                    <input type="text" class="form-control form-control-sm rounded-l-xl border-slate-300 text-xs" id="select-breakfast-beverage-209" placeholder="Enter Beverage Name">
                    <input type="number" class="form-control form-control-sm border-slate-300 text-xs" id="pax-breakfast-beverage-209"
                        placeholder="Qty" style="max-width: 70px;">
                    <button class="btn btn-sm rounded-r-xl px-3.5 text-xs font-semibold text-white inline-flex items-center gap-1 shadow-sm transition" style="background-color: #172a40; border-color: #172a40;" type="button"
                        onclick="addUpsellingItem('breakfast', 'beverage', '209')">
                        <i class="ti ti-plus text-xs" style="color: #B89047;"></i> Add
                    </button>
                </div>
                <ul class="list-group small" id="list-breakfast-beverage-209"></ul>
            </div>

            <div class="col-md-12">
                <label class="form-label text-xs font-semibold text-slate-700 mb-1">General Remarks</label>
                <textarea class="form-control rounded-xl border-slate-300 p-2.5 text-xs focus:border-[#172a40] focus:ring-[#172a40]" rows="2" name="session[breakfast][remarks]" placeholder="Catatan operasional sesi breakfast...">{{ old('session.breakfast.remarks', $bf->remarks ?? '') }}</textarea>
            </div>
            <div class="col-md-12 mt-3">
                <label class="form-label text-xs font-semibold text-slate-700 mb-1">Staff on Duty</label>
                <input type="hidden" id="input-staff-breakfast-209" name="session[breakfast][staff_on_duty]"
                    value="{{ $bfStaffValue }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm rounded-l-xl border-slate-300 text-xs" id="select-staff-breakfast-209">
                        <option value="" selected>Select Staff...</option>
                        @foreach ($myStaffList ?? [] as $staff)
                            <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                        @endforeach
                    </select>
                    <button class="btn btn-sm rounded-r-xl px-3.5 text-xs font-semibold text-white inline-flex items-center gap-1 shadow-sm transition" style="background-color: #172a40; border-color: #172a40;" type="button" onclick="addStaffItem('breakfast', '209')">
                        <i class="ti ti-plus text-xs" style="color: #B89047;"></i> Add
                    </button>
                </div>
                <div id="list-staff-breakfast-209" class="d-flex flex-wrap gap-1.5"></div>
            </div>
        </div>

        <div class="my-5 border-t border-slate-100"></div>

        {{-- 6. Competitor Comparison (Cover) --}}
        <div class="flex items-center gap-2.5 pb-2 mb-3 border-b border-slate-100">
            <span class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold shrink-0" style="background-color: rgba(23, 42, 64, 0.08); color: #172a40;">6</span>
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700">Competitor Comparison (Cover)</h4>
        </div>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label text-xs font-semibold text-slate-600 mb-1">Shangri-La</label>
                <input type="number" class="form-control rounded-xl border-slate-300 py-2 text-sm focus:border-[#172a40] focus:ring-[#172a40]"
                    name="session[breakfast][competitor_data][shangrila_cover]"
                    value="{{ old('session.breakfast.competitor_data.shangrila_cover', $bf->competitor_data['shangrila_cover'] ?? '') }}"
                    placeholder="0">
            </div>
            <div class="col-md-4">
                <label class="form-label text-xs font-semibold text-slate-600 mb-1">JW Marriott</label>
                <input type="number" class="form-control rounded-xl border-slate-300 py-2 text-sm focus:border-[#172a40] focus:ring-[#172a40]"
                    name="session[breakfast][competitor_data][jw_marriott_cover]"
                    value="{{ old('session.breakfast.competitor_data.jw_marriott_cover', $bf->competitor_data['jw_marriott_cover'] ?? '') }}"
                    placeholder="0">
            </div>
            <div class="col-md-4">
                <label class="form-label text-xs font-semibold text-slate-600 mb-1">Sheraton</label>
                <input type="number" class="form-control rounded-xl border-slate-300 py-2 text-sm focus:border-[#172a40] focus:ring-[#172a40]" name="session[breakfast][competitor_data][sheraton_cover]"
                    value="{{ old('session.breakfast.competitor_data.sheraton_cover', $bf->competitor_data['sheraton_cover'] ?? '') }}"
                    placeholder="0">
            </div>
        </div>

    </div>
</div>

{{-- === SESSION: LUNCH === --}}
<div class="rounded-2xl border border-slate-200/90 shadow-sm bg-white overflow-hidden mb-6">
    <div class="px-6 py-4 bg-sky-50/70 border-b border-sky-100 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-sky-100 text-sky-700 flex items-center justify-center font-bold shadow-xs">
                <i class="ti ti-soup text-xl"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-slate-800">Lunch Report</h3>
                <p class="text-xs text-slate-500">Pencatatan sesi siang, thematic event, dan upselling</p>
            </div>
        </div>
        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-sky-100/80 text-sky-800 border border-sky-200/60">
            Afternoon Session
        </span>
    </div>
    <div class="p-6">
        {{-- Thematic Event Details --}}
        <div class="rounded-xl border border-slate-200/90 bg-slate-50/70 p-4 mb-5">
            <div class="flex items-center gap-2 mb-3">
                <i class="ti ti-sparkles text-sm text-[#B89047]"></i>
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700">Thematic Event Details</h4>
            </div>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label text-xs font-semibold text-slate-600 mb-1">Thematic Name</label>
                    <input type="text" class="form-control rounded-xl border-slate-300 py-2 text-sm focus:border-[#172a40] focus:ring-[#172a40]" name="session[lunch][thematic]"
                        value="{{ old('session.lunch.thematic', $lc->thematic ?? '') }}" placeholder="Contoh: Buffet Nusantara">
                </div>
                <div class="col-md-3">
                    <label class="form-label text-xs font-semibold text-slate-600 mb-1">Thematic Pax</label>
                    <input type="number" class="form-control rounded-xl border-slate-300 py-2 text-sm focus:border-[#172a40] focus:ring-[#172a40]" name="session[lunch][additional_data][thematic_pax]"
                        value="{{ old('session.lunch.additional_data.thematic_pax', $lc->additional_data['thematic_pax'] ?? '') }}" placeholder="0">
                </div>
                <div class="col-md-5">
                    <label class="form-label text-xs font-semibold text-slate-600 mb-1">Thematic Revenue (IDR)</label>
                    <input type="text" class="form-control rounded-xl border-slate-300 py-2 text-sm rupiah focus:border-[#172a40] focus:ring-[#172a40]" name="session[lunch][additional_data][thematic_revenue]"
                        value="{{ old('session.lunch.additional_data.thematic_revenue', isset($lc->additional_data['thematic_revenue']) ? number_format($lc->additional_data['thematic_revenue'], 0, ',', '.') : '') }}" placeholder="0" autocomplete="off">
                </div>
            </div>
        </div>

        {{-- 1. Cover Report --}}
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
            <div class="col-md-3">
                <label class="form-label text-xs font-semibold text-rose-600 mb-1">BEO (Total)</label>
                <input type="number" class="form-control rounded-xl border-rose-200 bg-rose-50/30 py-2 text-sm focus:border-rose-400 focus:ring-rose-400" name="session[lunch][cover_data][beo_total]"
                    value="{{ old('session.lunch.cover_data.beo_total', $lc->cover_data['beo_total'] ?? '') }}"
                    placeholder="0">
            </div>
        </div>

        <div class="my-5 border-t border-slate-100"></div>

        {{-- 2. Occasion --}}
        <div class="flex items-center gap-2.5 pb-2 mb-3 border-b border-slate-100">
            <span class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold shrink-0" style="background-color: rgba(23, 42, 64, 0.08); color: #172a40;">2</span>
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700">Occasion</h4>
        </div>
        <div class="row g-3">
            <div class="col-md-12">
                <input type="hidden" id="input-occasion-lunch-209" name="session[lunch][additional_data][occasion_items]" value="{{ $lcOccasionValue }}">
                <div class="input-group mb-2.5">
                    <select class="form-select form-select-sm rounded-l-xl border-slate-300 text-xs font-medium" id="occasion-type-lunch-209" onchange="toggleOccasionOther('lunch', '209')">
                        <option value="" selected>Select Occasion...</option>
                        <option value="Wedding Party">Wedding Party</option>
                        <option value="Birthday Party">Birthday Party</option>
                        <option value="Social Event">Social Event</option>
                        <option value="Corporate Event">Corporate Event</option>
                        <option value="Other">Other</option>
                    </select>
                    <input type="text" class="form-control form-control-sm d-none border-slate-300 text-xs" id="occasion-other-lunch-209" placeholder="Occasion Type" style="max-width: 150px;">
                    <input type="text" class="form-control form-control-sm border-slate-300 text-xs" id="occasion-name-lunch-209"
                        placeholder="Name (e.g. Mr. Budi)">
                    <input type="number" class="form-control form-control-sm border-slate-300 text-xs" id="occasion-pax-lunch-209"
                        placeholder="Pax" style="max-width: 80px;">
                    <input type="text" class="form-control form-control-sm rupiah border-slate-300 text-xs" id="occasion-revenue-lunch-209"
                        placeholder="Revenue" style="max-width: 120px;" autocomplete="off">
                    <button class="btn btn-sm rounded-r-xl px-3.5 text-xs font-semibold text-white inline-flex items-center gap-1 shadow-sm transition" style="background-color: #172a40; border-color: #172a40;" type="button" onclick="addOccasionItem('lunch', '209')">
                        <i class="ti ti-plus text-xs" style="color: #B89047;"></i> Add
                    </button>
                </div>
                <ul class="list-group small" id="list-occasion-lunch-209"></ul>
            </div>
        </div>

        <div class="my-5 border-t border-slate-100"></div>

        {{-- 3. Promo --}}
        <div class="flex items-center gap-2.5 pb-2 mb-3 border-b border-slate-100">
            <span class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold shrink-0" style="background-color: rgba(23, 42, 64, 0.08); color: #172a40;">3</span>
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700">Promo</h4>
        </div>
        <div class="row g-3">
            <div class="col-md-12">
                <input type="hidden" id="input-promo-lunch-209" name="session[lunch][additional_data][promo_items]" value="{{ $lcPromoValue ?? '[]' }}">
                <div class="input-group mb-2.5">
                    <select class="form-select form-select-sm rounded-l-xl border-slate-300 text-xs font-medium" id="promo-type-lunch-209" onchange="togglePromoOther('lunch', '209')">
                        <option value="" selected>Select Promo...</option>
                        <option value="Mandiri Card">Mandiri Card</option>
                        <option value="BCA Card">BCA Card</option>
                        <option value="Membership">Membership</option>
                        <option value="Other">Other</option>
                    </select>
                    <input type="text" class="form-control form-control-sm d-none border-slate-300 text-xs" id="promo-other-lunch-209" placeholder="Promo Name" style="max-width: 150px;">
                    <input type="number" class="form-control form-control-sm border-slate-300 text-xs" id="promo-pax-lunch-209"
                        placeholder="Pax" style="max-width: 80px;">
                    <input type="text" class="form-control form-control-sm rupiah border-slate-300 text-xs" id="promo-revenue-lunch-209"
                        placeholder="Revenue" style="max-width: 120px;" autocomplete="off">
                    <button class="btn btn-sm rounded-r-xl px-3.5 text-xs font-semibold text-white inline-flex items-center gap-1 shadow-sm transition" style="background-color: #172a40; border-color: #172a40;" type="button" onclick="addPromoItem('lunch', '209')">
                        <i class="ti ti-plus text-xs" style="color: #B89047;"></i> Add
                    </button>
                </div>
                <ul class="list-group small" id="list-promo-lunch-209"></ul>
            </div>
        </div>

        <div class="my-5 border-t border-slate-100"></div>

        {{-- 4. Revenue Report (IDR) --}}
        <div class="flex items-center gap-2.5 pb-2 mb-3 border-b border-slate-100">
            <span class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold shrink-0" style="background-color: rgba(23, 42, 64, 0.08); color: #172a40;">4</span>
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700">Revenue Report (IDR)</h4>
        </div>
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label text-xs font-semibold text-slate-600 mb-1">Food Revenue</label>
                <div class="input-group">
                    <span class="input-group-text rounded-l-xl bg-slate-50 text-slate-500 text-xs font-bold border-slate-300">Rp</span>
                    <input type="text" class="form-control rounded-r-xl border-slate-300 py-2 text-sm rupiah focus:border-[#172a40] focus:ring-[#172a40]" name="session[lunch][revenue_food]"
                        value="{{ old('session.lunch.revenue_food', isset($lc->revenue_food) ? number_format($lc->revenue_food, 0, ',', '.') : '') }}"
                        placeholder="0" autocomplete="off">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label text-xs font-semibold text-slate-600 mb-1">Beverage Revenue</label>
                <div class="input-group">
                    <span class="input-group-text rounded-l-xl bg-slate-50 text-slate-500 text-xs font-bold border-slate-300">Rp</span>
                    <input type="text" class="form-control rounded-r-xl border-slate-300 py-2 text-sm rupiah focus:border-[#172a40] focus:ring-[#172a40]" name="session[lunch][revenue_beverage]"
                        value="{{ old('session.lunch.revenue_beverage', isset($lc->revenue_beverage) ? number_format($lc->revenue_beverage, 0, ',', '.') : '') }}"
                        placeholder="0" autocomplete="off">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label text-xs font-semibold text-slate-600 mb-1">Others Revenue</label>
                <div class="input-group">
                    <span class="input-group-text rounded-l-xl bg-slate-50 text-slate-500 text-xs font-bold border-slate-300">Rp</span>
                    <input type="text" class="form-control rounded-r-xl border-slate-300 py-2 text-sm rupiah focus:border-[#172a40] focus:ring-[#172a40]" name="session[lunch][revenue_others]"
                        value="{{ old('session.lunch.revenue_others', isset($lc->revenue_others) ? number_format($lc->revenue_others, 0, ',', '.') : '') }}"
                        placeholder="0" autocomplete="off">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label text-xs font-semibold text-slate-600 mb-1">Total Event Revenue</label>
                <div class="input-group">
                    <span class="input-group-text rounded-l-xl bg-slate-50 text-slate-500 text-xs font-bold border-slate-300">Rp</span>
                    <input type="text" class="form-control rounded-r-xl border-slate-300 py-2 text-sm rupiah focus:border-[#172a40] focus:ring-[#172a40]" name="session[lunch][revenue_event]"
                        value="{{ old('session.lunch.revenue_event', isset($lc->revenue_event) ? number_format($lc->revenue_event, 0, ',', '.') : '') }}"
                        placeholder="0" autocomplete="off">
                </div>
            </div>
        </div>

        <div class="my-5 border-t border-slate-100"></div>

        {{-- 5. Upselling & Remarks --}}
        <div class="flex items-center gap-2.5 pb-2 mb-3 border-b border-slate-100">
            <span class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold shrink-0" style="background-color: rgba(23, 42, 64, 0.08); color: #172a40;">5</span>
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700">Upselling & Remarks</h4>
        </div>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label text-xs font-semibold text-slate-700 mb-1">Upselling Menu (Food)</label>
                <input type="hidden" id="input-lunch-food-209" name="session[lunch][upselling_data][food]"
                    value="{{ $lcFoodValue }}">
                <div class="input-group mb-2">
                    <input type="text" class="form-control form-control-sm rounded-l-xl border-slate-300 text-xs" id="select-lunch-food-209" placeholder="Enter Food Name">
                    <input type="number" class="form-control form-control-sm border-slate-300 text-xs" id="pax-lunch-food-209"
                        placeholder="Qty" style="max-width: 70px;">
                    <button class="btn btn-sm rounded-r-xl px-3.5 text-xs font-semibold text-white inline-flex items-center gap-1 shadow-sm transition" style="background-color: #172a40; border-color: #172a40;" type="button"
                        onclick="addUpsellingItem('lunch', 'food', '209')">
                        <i class="ti ti-plus text-xs" style="color: #B89047;"></i> Add
                    </button>
                </div>
                <ul class="list-group small" id="list-lunch-food-209"></ul>
            </div>

            <div class="col-md-6">
                <label class="form-label text-xs font-semibold text-slate-700 mb-1">Beverage Upselling</label>
                <input type="hidden" id="input-lunch-beverage-209" name="session[lunch][upselling_data][beverage]"
                    value="{{ $lcBevValue }}">
                <div class="input-group mb-2">
                    <input type="text" class="form-control form-control-sm rounded-l-xl border-slate-300 text-xs" id="select-lunch-beverage-209" placeholder="Enter Beverage Name">
                    <input type="number" class="form-control form-control-sm border-slate-300 text-xs" id="pax-lunch-beverage-209"
                        placeholder="Qty" style="max-width: 70px;">
                    <button class="btn btn-sm rounded-r-xl px-3.5 text-xs font-semibold text-white inline-flex items-center gap-1 shadow-sm transition" style="background-color: #172a40; border-color: #172a40;" type="button"
                        onclick="addUpsellingItem('lunch', 'beverage', '209')">
                        <i class="ti ti-plus text-xs" style="color: #B89047;"></i> Add
                    </button>
                </div>
                <ul class="list-group small" id="list-lunch-beverage-209"></ul>
            </div>

            <div class="col-md-12">
                <label class="form-label text-xs font-semibold text-slate-700 mb-1">General Remarks</label>
                <textarea class="form-control rounded-xl border-slate-300 p-2.5 text-xs focus:border-[#172a40] focus:ring-[#172a40]" rows="2" name="session[lunch][remarks]" placeholder="Catatan operasional sesi lunch...">{{ old('session.lunch.remarks', $lc->remarks ?? '') }}</textarea>
            </div>
            <div class="col-md-12 mt-3">
                <label class="form-label text-xs font-semibold text-slate-700 mb-1">Staff on Duty</label>
                <input type="hidden" id="input-staff-lunch-209" name="session[lunch][staff_on_duty]"
                    value="{{ $lcStaffValue }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm rounded-l-xl border-slate-300 text-xs" id="select-staff-lunch-209">
                        <option value="" selected>Select Staff...</option>
                        @foreach ($myStaffList ?? [] as $staff)
                            <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                        @endforeach
                    </select>
                    <button class="btn btn-sm rounded-r-xl px-3.5 text-xs font-semibold text-white inline-flex items-center gap-1 shadow-sm transition" style="background-color: #172a40; border-color: #172a40;" type="button" onclick="addStaffItem('lunch', '209')">
                        <i class="ti ti-plus text-xs" style="color: #B89047;"></i> Add
                    </button>
                </div>
                <div id="list-staff-lunch-209" class="d-flex flex-wrap gap-1.5"></div>
            </div>
        </div>

        <div class="my-5 border-t border-slate-100"></div>

        {{-- 6. Competitor Comparison (Cover) --}}
        <div class="flex items-center gap-2.5 pb-2 mb-3 border-b border-slate-100">
            <span class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold shrink-0" style="background-color: rgba(23, 42, 64, 0.08); color: #172a40;">6</span>
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700">Competitor Comparison (Cover)</h4>
        </div>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label text-xs font-semibold text-slate-600 mb-1">Shangri-La</label>
                <input type="number" class="form-control rounded-xl border-slate-300 py-2 text-sm focus:border-[#172a40] focus:ring-[#172a40]"
                    name="session[lunch][competitor_data][shangrila_cover]"
                    value="{{ old('session.lunch.competitor_data.shangrila_cover', $lc->competitor_data['shangrila_cover'] ?? '') }}"
                    placeholder="0">
            </div>
            <div class="col-md-4">
                <label class="form-label text-xs font-semibold text-slate-600 mb-1">JW Marriott</label>
                <input type="number" class="form-control rounded-xl border-slate-300 py-2 text-sm focus:border-[#172a40] focus:ring-[#172a40]"
                    name="session[lunch][competitor_data][jw_marriott_cover]"
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

{{-- === SESSION: DINNER === --}}
<div class="rounded-2xl border border-slate-200/90 shadow-sm bg-white overflow-hidden mb-6">
    <div class="px-6 py-4 bg-indigo-50/70 border-b border-indigo-100 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold shadow-xs">
                <i class="ti ti-moon-stars text-xl"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-slate-800">Dinner Report</h3>
                <p class="text-xs text-slate-500">Pencatatan sesi malam, occasion, revenue, dan kompetitor</p>
            </div>
        </div>
        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-indigo-100/80 text-indigo-800 border border-indigo-200/60">
            Evening Session
        </span>
    </div>
    <div class="p-6">
        {{-- Thematic Event Details --}}
        <div class="rounded-xl border border-slate-200/90 bg-slate-50/70 p-4 mb-5">
            <div class="flex items-center gap-2 mb-3">
                <i class="ti ti-sparkles text-sm text-[#B89047]"></i>
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700">Thematic Event Details</h4>
            </div>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label text-xs font-semibold text-slate-600 mb-1">Thematic Name</label>
                    <input type="text" class="form-control rounded-xl border-slate-300 py-2 text-sm focus:border-[#172a40] focus:ring-[#172a40]" name="session[dinner][thematic]"
                        value="{{ old('session.dinner.thematic', $dn->thematic ?? '') }}" placeholder="Contoh: Romantic Dinner Special">
                </div>
                <div class="col-md-3">
                    <label class="form-label text-xs font-semibold text-slate-600 mb-1">Thematic Pax</label>
                    <input type="number" class="form-control rounded-xl border-slate-300 py-2 text-sm focus:border-[#172a40] focus:ring-[#172a40]" name="session[dinner][additional_data][thematic_pax]"
                        value="{{ old('session.dinner.additional_data.thematic_pax', $dn->additional_data['thematic_pax'] ?? '') }}" placeholder="0">
                </div>
                <div class="col-md-5">
                    <label class="form-label text-xs font-semibold text-slate-600 mb-1">Thematic Revenue (IDR)</label>
                    <input type="text" class="form-control rounded-xl border-slate-300 py-2 text-sm rupiah focus:border-[#172a40] focus:ring-[#172a40]" name="session[dinner][additional_data][thematic_revenue]"
                        value="{{ old('session.dinner.additional_data.thematic_revenue', isset($dn->additional_data['thematic_revenue']) ? number_format($dn->additional_data['thematic_revenue'], 0, ',', '.') : '') }}" placeholder="0" autocomplete="off">
                </div>
            </div>
        </div>

        {{-- 1. Cover Report --}}
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
            <div class="col-md-3">
                <label class="form-label text-xs font-semibold text-rose-600 mb-1">BEO (Total)</label>
                <input type="number" class="form-control rounded-xl border-rose-200 bg-rose-50/30 py-2 text-sm focus:border-rose-400 focus:ring-rose-400" name="session[dinner][cover_data][beo_total]"
                    value="{{ old('session.dinner.cover_data.beo_total', $dn->cover_data['beo_total'] ?? '') }}"
                    placeholder="0">
            </div>
        </div>

        <div class="my-5 border-t border-slate-100"></div>

        {{-- 2. Occasion --}}
        <div class="flex items-center gap-2.5 pb-2 mb-3 border-b border-slate-100">
            <span class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold shrink-0" style="background-color: rgba(23, 42, 64, 0.08); color: #172a40;">2</span>
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700">Occasion</h4>
        </div>
        <div class="row g-3">
            <div class="col-md-12">
                <input type="hidden" id="input-occasion-dinner-209" name="session[dinner][additional_data][occasion_items]" value="{{ $dnOccasionValue }}">
                <div class="input-group mb-2.5">
                    <select class="form-select form-select-sm rounded-l-xl border-slate-300 text-xs font-medium" id="occasion-type-dinner-209" onchange="toggleOccasionOther('dinner', '209')">
                        <option value="" selected>Select Occasion...</option>
                        <option value="Wedding Party">Wedding Party</option>
                        <option value="Birthday Party">Birthday Party</option>
                        <option value="Social Event">Social Event</option>
                        <option value="Corporate Event">Corporate Event</option>
                        <option value="Other">Other</option>
                    </select>
                    <input type="text" class="form-control form-control-sm d-none border-slate-300 text-xs" id="occasion-other-dinner-209" placeholder="Occasion Type" style="max-width: 150px;">
                    <input type="text" class="form-control form-control-sm border-slate-300 text-xs" id="occasion-name-dinner-209"
                        placeholder="Name (e.g. Mr. Budi)">
                    <input type="number" class="form-control form-control-sm border-slate-300 text-xs" id="occasion-pax-dinner-209"
                        placeholder="Pax" style="max-width: 80px;">
                    <input type="text" class="form-control form-control-sm rupiah border-slate-300 text-xs" id="occasion-revenue-dinner-209"
                        placeholder="Revenue" style="max-width: 120px;" autocomplete="off">
                    <button class="btn btn-sm rounded-r-xl px-3.5 text-xs font-semibold text-white inline-flex items-center gap-1 shadow-sm transition" style="background-color: #172a40; border-color: #172a40;" type="button" onclick="addOccasionItem('dinner', '209')">
                        <i class="ti ti-plus text-xs" style="color: #B89047;"></i> Add
                    </button>
                </div>
                <ul class="list-group small" id="list-occasion-dinner-209"></ul>
            </div>
        </div>

        <div class="my-5 border-t border-slate-100"></div>

        {{-- 3. Promo --}}
        <div class="flex items-center gap-2.5 pb-2 mb-3 border-b border-slate-100">
            <span class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold shrink-0" style="background-color: rgba(23, 42, 64, 0.08); color: #172a40;">3</span>
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700">Promo</h4>
        </div>
        <div class="row g-3">
            <div class="col-md-12">
                <input type="hidden" id="input-promo-dinner-209" name="session[dinner][additional_data][promo_items]" value="{{ $dnPromoValue ?? '[]' }}">
                <div class="input-group mb-2.5">
                    <select class="form-select form-select-sm rounded-l-xl border-slate-300 text-xs font-medium" id="promo-type-dinner-209" onchange="togglePromoOther('dinner', '209')">
                        <option value="" selected>Select Promo...</option>
                        <option value="Mandiri Card">Mandiri Card</option>
                        <option value="BCA Card">BCA Card</option>
                        <option value="Membership">Membership</option>
                        <option value="Other">Other</option>
                    </select>
                    <input type="text" class="form-control form-control-sm d-none border-slate-300 text-xs" id="promo-other-dinner-209" placeholder="Promo Name" style="max-width: 150px;">
                    <input type="number" class="form-control form-control-sm border-slate-300 text-xs" id="promo-pax-dinner-209"
                        placeholder="Pax" style="max-width: 80px;">
                    <input type="text" class="form-control form-control-sm rupiah border-slate-300 text-xs" id="promo-revenue-dinner-209"
                        placeholder="Revenue" style="max-width: 120px;" autocomplete="off">
                    <button class="btn btn-sm rounded-r-xl px-3.5 text-xs font-semibold text-white inline-flex items-center gap-1 shadow-sm transition" style="background-color: #172a40; border-color: #172a40;" type="button" onclick="addPromoItem('dinner', '209')">
                        <i class="ti ti-plus text-xs" style="color: #B89047;"></i> Add
                    </button>
                </div>
                <ul class="list-group small" id="list-promo-dinner-209"></ul>
            </div>
        </div>

        <div class="my-5 border-t border-slate-100"></div>

        {{-- 4. Revenue Report (IDR) --}}
        <div class="flex items-center gap-2.5 pb-2 mb-3 border-b border-slate-100">
            <span class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold shrink-0" style="background-color: rgba(23, 42, 64, 0.08); color: #172a40;">4</span>
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700">Revenue Report (IDR)</h4>
        </div>
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label text-xs font-semibold text-slate-600 mb-1">Food Revenue</label>
                <div class="input-group">
                    <span class="input-group-text rounded-l-xl bg-slate-50 text-slate-500 text-xs font-bold border-slate-300">Rp</span>
                    <input type="text" class="form-control rounded-r-xl border-slate-300 py-2 text-sm rupiah focus:border-[#172a40] focus:ring-[#172a40]" name="session[dinner][revenue_food]"
                        value="{{ old('session.dinner.revenue_food', isset($dn->revenue_food) ? number_format($dn->revenue_food, 0, ',', '.') : '') }}"
                        placeholder="0" autocomplete="off">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label text-xs font-semibold text-slate-600 mb-1">Beverage Revenue</label>
                <div class="input-group">
                    <span class="input-group-text rounded-l-xl bg-slate-50 text-slate-500 text-xs font-bold border-slate-300">Rp</span>
                    <input type="text" class="form-control rounded-r-xl border-slate-300 py-2 text-sm rupiah focus:border-[#172a40] focus:ring-[#172a40]" name="session[dinner][revenue_beverage]"
                        value="{{ old('session.dinner.revenue_beverage', isset($dn->revenue_beverage) ? number_format($dn->revenue_beverage, 0, ',', '.') : '') }}"
                        placeholder="0" autocomplete="off">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label text-xs font-semibold text-slate-600 mb-1">Others Revenue</label>
                <div class="input-group">
                    <span class="input-group-text rounded-l-xl bg-slate-50 text-slate-500 text-xs font-bold border-slate-300">Rp</span>
                    <input type="text" class="form-control rounded-r-xl border-slate-300 py-2 text-sm rupiah focus:border-[#172a40] focus:ring-[#172a40]" name="session[dinner][revenue_others]"
                        value="{{ old('session.dinner.revenue_others', isset($dn->revenue_others) ? number_format($dn->revenue_others, 0, ',', '.') : '') }}"
                        placeholder="0" autocomplete="off">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label text-xs font-semibold text-slate-600 mb-1">Total Event Revenue</label>
                <div class="input-group">
                    <span class="input-group-text rounded-l-xl bg-slate-50 text-slate-500 text-xs font-bold border-slate-300">Rp</span>
                    <input type="text" class="form-control rounded-r-xl border-slate-300 py-2 text-sm rupiah focus:border-[#172a40] focus:ring-[#172a40]" name="session[dinner][revenue_event]"
                        value="{{ old('session.dinner.revenue_event', isset($dn->revenue_event) ? number_format($dn->revenue_event, 0, ',', '.') : '') }}"
                        placeholder="0" autocomplete="off">
                </div>
            </div>
        </div>

        <div class="my-5 border-t border-slate-100"></div>

        {{-- 5. Upselling & Remarks --}}
        <div class="flex items-center gap-2.5 pb-2 mb-3 border-b border-slate-100">
            <span class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold shrink-0" style="background-color: rgba(23, 42, 64, 0.08); color: #172a40;">5</span>
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700">Upselling & Remarks</h4>
        </div>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label text-xs font-semibold text-slate-700 mb-1">Upselling Menu (Food)</label>
                <input type="hidden" id="input-dinner-food-209" name="session[dinner][upselling_data][food]"
                    value="{{ $dnFoodValue }}">
                <div class="input-group mb-2">
                    <input type="text" class="form-control form-control-sm rounded-l-xl border-slate-300 text-xs" id="select-dinner-food-209" placeholder="Enter Food Name">
                    <input type="number" class="form-control form-control-sm border-slate-300 text-xs" id="pax-dinner-food-209"
                        placeholder="Qty" style="max-width: 70px;">
                    <button class="btn btn-sm rounded-r-xl px-3.5 text-xs font-semibold text-white inline-flex items-center gap-1 shadow-sm transition" style="background-color: #172a40; border-color: #172a40;" type="button"
                        onclick="addUpsellingItem('dinner', 'food', '209')">
                        <i class="ti ti-plus text-xs" style="color: #B89047;"></i> Add
                    </button>
                </div>
                <ul class="list-group small" id="list-dinner-food-209"></ul>
            </div>

            <div class="col-md-6">
                <label class="form-label text-xs font-semibold text-slate-700 mb-1">Beverage Upselling</label>
                <input type="hidden" id="input-dinner-beverage-209" name="session[dinner][upselling_data][beverage]"
                    value="{{ $dnBevValue }}">
                <div class="input-group mb-2">
                    <input type="text" class="form-control form-control-sm rounded-l-xl border-slate-300 text-xs" id="select-dinner-beverage-209" placeholder="Enter Beverage Name">
                    <input type="number" class="form-control form-control-sm border-slate-300 text-xs" id="pax-dinner-beverage-209"
                        placeholder="Qty" style="max-width: 70px;">
                    <button class="btn btn-sm rounded-r-xl px-3.5 text-xs font-semibold text-white inline-flex items-center gap-1 shadow-sm transition" style="background-color: #172a40; border-color: #172a40;" type="button"
                        onclick="addUpsellingItem('dinner', 'beverage', '209')">
                        <i class="ti ti-plus text-xs" style="color: #B89047;"></i> Add
                    </button>
                </div>
                <ul class="list-group small" id="list-dinner-beverage-209"></ul>
            </div>

            <div class="col-md-12">
                <label class="form-label text-xs font-semibold text-slate-700 mb-1">General Remarks</label>
                <textarea class="form-control rounded-xl border-slate-300 p-2.5 text-xs focus:border-[#172a40] focus:ring-[#172a40]" rows="2" name="session[dinner][remarks]" placeholder="Catatan operasional sesi dinner...">{{ old('session.dinner.remarks', $dn->remarks ?? '') }}</textarea>
            </div>
            <div class="col-md-12 mt-3">
                <label class="form-label text-xs font-semibold text-slate-700 mb-1">Staff on Duty</label>
                <input type="hidden" id="input-staff-dinner-209" name="session[dinner][staff_on_duty]"
                    value="{{ $dnStaffValue }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm rounded-l-xl border-slate-300 text-xs" id="select-staff-dinner-209">
                        <option value="" selected>Select Staff...</option>
                        @foreach ($myStaffList ?? [] as $staff)
                            <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                        @endforeach
                    </select>
                    <button class="btn btn-sm rounded-r-xl px-3.5 text-xs font-semibold text-white inline-flex items-center gap-1 shadow-sm transition" style="background-color: #172a40; border-color: #172a40;" type="button" onclick="addStaffItem('dinner', '209')">
                        <i class="ti ti-plus text-xs" style="color: #B89047;"></i> Add
                    </button>
                </div>
                <div id="list-staff-dinner-209" class="d-flex flex-wrap gap-1.5"></div>
            </div>
        </div>

        <div class="my-5 border-t border-slate-100"></div>

        {{-- 6. Competitor Comparison (Cover) --}}
        <div class="flex items-center gap-2.5 pb-2 mb-3 border-b border-slate-100">
            <span class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold shrink-0" style="background-color: rgba(23, 42, 64, 0.08); color: #172a40;">6</span>
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700">Competitor Comparison (Cover)</h4>
        </div>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label text-xs font-semibold text-slate-600 mb-1">Shangri-La</label>
                <input type="number" class="form-control rounded-xl border-slate-300 py-2 text-sm focus:border-[#172a40] focus:ring-[#172a40]"
                    name="session[dinner][competitor_data][shangrila_cover]"
                    value="{{ old('session.dinner.competitor_data.shangrila_cover', $dn->competitor_data['shangrila_cover'] ?? '') }}"
                    placeholder="0">
            </div>
            <div class="col-md-4">
                <label class="form-label text-xs font-semibold text-slate-600 mb-1">JW Marriott</label>
                <input type="number" class="form-control rounded-xl border-slate-300 py-2 text-sm focus:border-[#172a40] focus:ring-[#172a40]"
                    name="session[dinner][competitor_data][jw_marriott_cover]"
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ============================================================
        // 1. UPSELLING INITIALIZATION
        // ============================================================

        // --- BREAKFAST ---
        let bfFood = {!! json_encode(old('session.breakfast.upselling_data.food', $bf->upselling_data['food'] ?? [])) !!};
        initUpselling('breakfast', 'food', bfFood, '209');

        let bfBev = {!! json_encode(old('session.breakfast.upselling_data.beverage', $bf->upselling_data['beverage'] ?? [])) !!};
        initUpselling('breakfast', 'beverage', bfBev, '209');

        // --- LUNCH ---
        let lcFood = {!! json_encode(old('session.lunch.upselling_data.food', $lc->upselling_data['food'] ?? [])) !!};
        initUpselling('lunch', 'food', lcFood, '209');

        let lcBev = {!! json_encode(old('session.lunch.upselling_data.beverage', $lc->upselling_data['beverage'] ?? [])) !!};
        initUpselling('lunch', 'beverage', lcBev, '209');

        // --- DINNER ---
        let dnFood = {!! json_encode(old('session.dinner.upselling_data.food', $dn->upselling_data['food'] ?? [])) !!};
        initUpselling('dinner', 'food', dnFood, '209');

        let dnBev = {!! json_encode(old('session.dinner.upselling_data.beverage', $dn->upselling_data['beverage'] ?? [])) !!};
        initUpselling('dinner', 'beverage', dnBev, '209');


        // ============================================================
        // 2. STAFF ON DUTY INITIALIZATION
        // ============================================================

        let bfStaff = {!! json_encode(old('session.breakfast.staff_on_duty', $bf->staff_on_duty ?? [])) !!};
        initStaff('breakfast', bfStaff, '209');

        let lcStaff = {!! json_encode(old('session.lunch.staff_on_duty', $lc->staff_on_duty ?? [])) !!};
        initStaff('lunch', lcStaff, '209');

        let dnStaff = {!! json_encode(old('session.dinner.staff_on_duty', $dn->staff_on_duty ?? [])) !!};
        initStaff('dinner', dnStaff, '209');


        // ============================================================
        // 3. OCCASION INITIALIZATION
        // ============================================================

        let bfOccasion = {!! json_encode(old('session.breakfast.additional_data.occasion_items', $bf->additional_data['occasion_items'] ?? [])) !!};
        initOccasion('breakfast', bfOccasion, '209');

        let lcOccasion = {!! json_encode(old('session.lunch.additional_data.occasion_items', $lc->additional_data['occasion_items'] ?? [])) !!};
        initOccasion('lunch', lcOccasion, '209');

        let dnOccasion = {!! json_encode(old('session.dinner.additional_data.occasion_items', $dn->additional_data['occasion_items'] ?? [])) !!};
        initOccasion('dinner', dnOccasion, '209');


        // ============================================================
        // 6. PROMO OTHERS INITIALIZATION
        // ============================================================

        let bfPromo = {!! json_encode(old('session.breakfast.additional_data.promo_items', $bf->additional_data['promo_items'] ?? [])) !!};
        initPromoItems('breakfast', bfPromo, '209');

        let lcPromo = {!! json_encode(old('session.lunch.additional_data.promo_items', $lc->additional_data['promo_items'] ?? [])) !!};
        initPromoItems('lunch', lcPromo, '209');

        let dnPromo = {!! json_encode(old('session.dinner.additional_data.promo_items', $dn->additional_data['promo_items'] ?? [])) !!};
        initPromoItems('dinner', dnPromo, '209');
    });


    // ============================================================
    // GROUP FUNCTIONS
    // ============================================================

    function addGroupItem(session, kode) {
        let nameInput = document.getElementById('group-name-' + session + '-' + kode);
        let qtyInput = document.getElementById('group-qty-' + session + '-' + kode);
        let hiddenInput = document.getElementById('input-group-' + session + '-' + kode);
        let listEl = document.getElementById('list-group-' + session + '-' + kode);

        let name = nameInput.value.trim();
        let qty = qtyInput.value.trim();

        if (!name) {
            alert('Please enter group name.');
            return;
        }

        if (!qty || parseInt(qty) <= 0) {
            alert('Please enter a valid quantity.');
            return;
        }

        let items = JSON.parse(hiddenInput.value || '[]');
        items.push({ name: name, qty: parseInt(qty) });
        hiddenInput.value = JSON.stringify(items);

        renderGroupList(listEl, items, session, kode);

        nameInput.value = '';
        qtyInput.value = '';
        nameInput.focus();
    }

    function initGroup(session, data, kode) {
        let items = Array.isArray(data) ? data : [];
        let hiddenInput = document.getElementById('input-group-' + session + '-' + kode);
        let listEl = document.getElementById('list-group-' + session + '-' + kode);
        hiddenInput.value = JSON.stringify(items);
        renderGroupList(listEl, items, session, kode);
    }

    function removeGroupItem(session, kode, index) {
        let hiddenInput = document.getElementById('input-group-' + session + '-' + kode);
        let listEl = document.getElementById('list-group-' + session + '-' + kode);
        let items = JSON.parse(hiddenInput.value || '[]');
        items.splice(index, 1);
        hiddenInput.value = JSON.stringify(items);
        renderGroupList(listEl, items, session, kode);
    }

    function renderGroupList(listEl, items, session, kode) {
        if (items.length === 0) {
            listEl.innerHTML = '';
            return;
        }

        let html = '';
        items.forEach(function(item, index) {
            html += '<li class="list-group-item d-flex justify-content-between align-items-center py-1.5 px-3 rounded-xl border border-slate-200 mb-1.5 bg-slate-50/50">' +
                '<span class="text-xs font-medium text-slate-700"><strong>' + escapeHtml(item.name || '') + '</strong> <span class="badge bg-slate-600 ms-1">' + (item.qty || 0) + ' pax</span></span>' +
                '<button class="btn btn-sm btn-link text-rose-500 hover:text-rose-700 p-0" onclick="removeGroupItem(\'' + session + '\', \'' + kode + '\', ' + index + ')" title="Remove">' +
                '<i class="ti ti-x text-sm"></i></button>' +
                '</li>';
        });
        listEl.innerHTML = html;
    }

    function escapeHtml(text) {
        if (!text) return '';
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(text));
        return div.innerHTML;
    }


    // ============================================================
    // OCCASION OTHERS FUNCTIONS
    // ============================================================

    function addOccOthers(session, kode) {
        let nameInput = document.getElementById('occothers-name-' + session + '-' + kode);
        let paxInput = document.getElementById('occothers-pax-' + session + '-' + kode);
        let hiddenInput = document.getElementById('input-occothers-' + session + '-' + kode);
        let listEl = document.getElementById('list-occothers-' + session + '-' + kode);

        let name = nameInput.value.trim();
        let pax = paxInput.value.trim();

        if (!name) {
            alert('Please enter occasion name.');
            return;
        }

        if (!pax || parseInt(pax) <= 0) {
            alert('Please enter a valid pax number.');
            return;
        }

        let items = JSON.parse(hiddenInput.value || '[]');
        items.push({ name: name, pax: parseInt(pax) });
        hiddenInput.value = JSON.stringify(items);

        renderOccOthers(listEl, items, session, kode);

        nameInput.value = '';
        paxInput.value = '';
        nameInput.focus();
    }

    function initOccOthers(session, data, kode) {
        let items = Array.isArray(data) ? data : [];
        let hiddenInput = document.getElementById('input-occothers-' + session + '-' + kode);
        let listEl = document.getElementById('list-occothers-' + session + '-' + kode);
        hiddenInput.value = JSON.stringify(items);
        renderOccOthers(listEl, items, session, kode);
    }

    function removeOccOthers(session, kode, index) {
        let hiddenInput = document.getElementById('input-occothers-' + session + '-' + kode);
        let listEl = document.getElementById('list-occothers-' + session + '-' + kode);
        let items = JSON.parse(hiddenInput.value || '[]');
        items.splice(index, 1);
        hiddenInput.value = JSON.stringify(items);
        renderOccOthers(listEl, items, session, kode);
    }

    function renderOccOthers(listEl, items, session, kode) {
        if (items.length === 0) {
            listEl.innerHTML = '';
            return;
        }

        let html = '';
        items.forEach(function(item, index) {
            html += '<li class="list-group-item d-flex justify-content-between align-items-center py-1.5 px-3 rounded-xl border border-slate-200 mb-1.5 bg-slate-50/50">' +
                '<span class="text-xs font-medium text-slate-700"><strong>' + escapeHtml(item.name || '') + '</strong> <span class="badge bg-blue-600 ms-1">' + (item.pax || 0) + ' pax</span></span>' +
                '<button class="btn btn-sm btn-link text-rose-500 hover:text-rose-700 p-0" onclick="removeOccOthers(\'' + session + '\', \'' + kode + '\', ' + index + ')" title="Remove">' +
                '<i class="ti ti-x text-sm"></i></button>' +
                '</li>';
        });
        listEl.innerHTML = html;
    }


    // ============================================================
    // PROMO OTHERS FUNCTIONS
    // ============================================================

    function addPromoOthers(session, kode) {
        let nameInput = document.getElementById('promoothers-name-' + session + '-' + kode);
        let qtyInput = document.getElementById('promoothers-qty-' + session + '-' + kode);
        let hiddenInput = document.getElementById('input-promoothers-' + session + '-' + kode);
        let listEl = document.getElementById('list-promoothers-' + session + '-' + kode);

        let name = nameInput.value.trim();
        let qty = qtyInput.value.trim();

        if (!name) {
            alert('Please enter promo name.');
            return;
        }

        if (!qty || parseInt(qty) <= 0) {
            alert('Please enter a valid quantity.');
            return;
        }

        let items = JSON.parse(hiddenInput.value || '[]');
        items.push({ name: name, qty: parseInt(qty) });
        hiddenInput.value = JSON.stringify(items);

        renderPromoOthers(listEl, items, session, kode);

        nameInput.value = '';
        qtyInput.value = '';
        nameInput.focus();
    }

    function initPromoOthers(session, data, kode) {
        let items = Array.isArray(data) ? data : [];
        let hiddenInput = document.getElementById('input-promoothers-' + session + '-' + kode);
        let listEl = document.getElementById('list-promoothers-' + session + '-' + kode);
        hiddenInput.value = JSON.stringify(items);
        renderPromoOthers(listEl, items, session, kode);
    }

    function removePromoOthers(session, kode, index) {
        let hiddenInput = document.getElementById('input-promoothers-' + session + '-' + kode);
        let listEl = document.getElementById('list-promoothers-' + session + '-' + kode);
        let items = JSON.parse(hiddenInput.value || '[]');
        items.splice(index, 1);
        hiddenInput.value = JSON.stringify(items);
        renderPromoOthers(listEl, items, session, kode);
    }

    function renderPromoOthers(listEl, items, session, kode) {
        if (items.length === 0) {
            listEl.innerHTML = '';
            return;
        }

        let html = '';
        items.forEach(function(item, index) {
            html += '<li class="list-group-item d-flex justify-content-between align-items-center py-1.5 px-3 rounded-xl border border-slate-200 mb-1.5 bg-slate-50/50">' +
                '<span class="text-xs font-medium text-slate-700"><strong>' + escapeHtml(item.name || '') + '</strong> <span class="badge bg-emerald-600 ms-1">' + (item.qty || 0) + ' pax</span></span>' +
                '<button class="btn btn-sm btn-link text-rose-500 hover:text-rose-700 p-0" onclick="removePromoOthers(\'' + session + '\', \'' + kode + '\', ' + index + ')" title="Remove">' +
                '<i class="ti ti-x text-sm"></i></button>' +
                '</li>';
        });
        listEl.innerHTML = html;
    }
</script>
