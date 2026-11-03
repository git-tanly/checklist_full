{{-- FORM KHUSUS JOE MILANO (JM) --}}

@php
    $bf = $details['breakfast'] ?? null;
    $lc = $details['lunch'] ?? null;
    $dn = $details['dinner'] ?? null;

    $restoJml = $restaurants->where('code', 'JM')->first();
    $myStaffList = $restoJml ? $restoJml->employees : [];

    $myMenu = $restoJml && isset($upsellingItems[$restoJml->id]) ? $upsellingItems[$restoJml->id] : collect([]);
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
{{-- SESSION: BREAKFAST --}}
{{-- ============================================================ --}}
<div class="rounded-2xl border border-slate-200/90 shadow-sm bg-white overflow-hidden mb-6">
    <div class="px-6 py-4 bg-amber-50/70 border-b border-amber-100 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center font-bold shadow-xs">
                <i class="ti ti-sun text-xl"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-slate-800">Breakfast Report</h3>
                <p class="text-xs text-slate-500">Pencatatan sesi pagi Joe Milano, cover in-house / walk-in, dan revenue</p>
            </div>
        </div>
        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-amber-100/80 text-amber-800 border border-amber-200/60">
            Morning Session
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
                <label class="form-label text-xs font-semibold text-slate-600 mb-1">In House Adult</label>
                <input type="number" class="form-control rounded-xl border-slate-300 py-2 text-sm focus:border-[#172a40] focus:ring-[#172a40]" name="session[breakfast][cover_data][in_house_adult]"
                    value="{{ old('session.breakfast.cover_data.in_house_adult', $bf->cover_data['in_house_adult'] ?? '') }}"
                    placeholder="0">
            </div>
            <div class="col-md-3">
                <label class="form-label text-xs font-semibold text-slate-600 mb-1">In House Child</label>
                <input type="number" class="form-control rounded-xl border-slate-300 py-2 text-sm focus:border-[#172a40] focus:ring-[#172a40]" name="session[breakfast][cover_data][in_house_child]"
                    value="{{ old('session.breakfast.cover_data.in_house_child', $bf->cover_data['in_house_child'] ?? '') }}"
                    placeholder="0">
            </div>
            <div class="col-md-3">
                <label class="form-label text-xs font-semibold text-slate-600 mb-1">Walk In Adult</label>
                <input type="number" class="form-control rounded-xl border-slate-300 py-2 text-sm focus:border-[#172a40] focus:ring-[#172a40]" name="session[breakfast][cover_data][walk_in_adult]"
                    value="{{ old('session.breakfast.cover_data.walk_in_adult', $bf->cover_data['walk_in_adult'] ?? '') }}"
                    placeholder="0">
            </div>
            <div class="col-md-3">
                <label class="form-label text-xs font-semibold text-slate-600 mb-1">Walk In Child</label>
                <input type="number" class="form-control rounded-xl border-slate-300 py-2 text-sm focus:border-[#172a40] focus:ring-[#172a40]" name="session[breakfast][cover_data][walk_in_child]"
                    value="{{ old('session.breakfast.cover_data.walk_in_child', $bf->cover_data['walk_in_child'] ?? '') }}"
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
                <input type="hidden" id="input-occasion-breakfast-JM" name="session[breakfast][additional_data][occasion_items]" value="{{ $bfOccasionValue ?? '[]' }}">
                <div class="input-group mb-2.5">
                    <select class="form-select form-select-sm rounded-l-xl border-slate-300 text-xs font-medium" id="occasion-type-breakfast-JM" onchange="toggleOccasionOther('breakfast', 'JM')">
                        <option value="" selected>Select Occasion...</option>
                        <option value="Wedding Party">Wedding Party</option>
                        <option value="Birthday Party">Birthday Party</option>
                        <option value="Social Event">Social Event</option>
                        <option value="Corporate Event">Corporate Event</option>
                        <option value="Other">Other</option>
                    </select>
                    <input type="text" class="form-control form-control-sm d-none border-slate-300 text-xs" id="occasion-other-breakfast-JM" placeholder="Occasion Type" style="max-width: 150px;">
                    <input type="text" class="form-control form-control-sm border-slate-300 text-xs" id="occasion-name-breakfast-JM"
                        placeholder="Name (e.g. Mr. Budi)">
                    <input type="number" class="form-control form-control-sm border-slate-300 text-xs" id="occasion-pax-breakfast-JM"
                        placeholder="Pax" style="max-width: 80px;">
                    <input type="text" class="form-control form-control-sm rupiah border-slate-300 text-xs" id="occasion-revenue-breakfast-JM"
                        placeholder="Revenue" style="max-width: 120px;" autocomplete="off">
                    <button class="btn btn-sm rounded-r-xl px-3.5 text-xs font-semibold text-white inline-flex items-center gap-1 shadow-sm transition" style="background-color: #172a40; border-color: #172a40;" type="button" onclick="addOccasionItem('breakfast', 'JM')">
                        <i class="ti ti-plus text-xs" style="color: #B89047;"></i> Add
                    </button>
                </div>
                <ul class="list-group small" id="list-occasion-breakfast-JM"></ul>
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
                <input type="hidden" id="input-promo-breakfast-JM" name="session[breakfast][additional_data][promo_items]" value="{{ $bfPromoValue ?? '[]' }}">
                <div class="input-group mb-2.5">
                    <select class="form-select form-select-sm rounded-l-xl border-slate-300 text-xs font-medium" id="promo-type-breakfast-JM" onchange="togglePromoOther('breakfast', 'JM')">
                        <option value="" selected>Select Promo...</option>
                        <option value="Mandiri Card">Mandiri Card</option>
                        <option value="BCA Card">BCA Card</option>
                        <option value="Membership">Membership</option>
                        <option value="Other">Other</option>
                    </select>
                    <input type="text" class="form-control form-control-sm d-none border-slate-300 text-xs" id="promo-other-breakfast-JM" placeholder="Promo Name" style="max-width: 150px;">
                    <input type="number" class="form-control form-control-sm border-slate-300 text-xs" id="promo-pax-breakfast-JM"
                        placeholder="Pax" style="max-width: 80px;">
                    <input type="text" class="form-control form-control-sm rupiah border-slate-300 text-xs" id="promo-revenue-breakfast-JM"
                        placeholder="Revenue" style="max-width: 120px;" autocomplete="off">
                    <button class="btn btn-sm rounded-r-xl px-3.5 text-xs font-semibold text-white inline-flex items-center gap-1 shadow-sm transition" style="background-color: #172a40; border-color: #172a40;" type="button" onclick="addPromoItem('breakfast', 'JM')">
                        <i class="ti ti-plus text-xs" style="color: #B89047;"></i> Add
                    </button>
                </div>
                <ul class="list-group small" id="list-promo-breakfast-JM"></ul>
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
                    <input type="text" class="form-control rounded-r-xl border-slate-300 py-2 text-sm rupiah focus:border-[#172a40] focus:ring-[#172a40]" name="session[breakfast][revenue_food]"
                        value="{{ old('session.breakfast.revenue_food', isset($bf->revenue_food) ? number_format($bf->revenue_food, 0, ',', '.') : '') }}"
                        autocomplete="off" placeholder="0">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label text-xs font-semibold text-slate-600 mb-1">Beverage</label>
                <div class="input-group">
                    <span class="input-group-text rounded-l-xl bg-slate-50 text-slate-500 text-xs font-bold border-slate-300">Rp</span>
                    <input type="text" class="form-control rounded-r-xl border-slate-300 py-2 text-sm rupiah focus:border-[#172a40] focus:ring-[#172a40]" name="session[breakfast][revenue_beverage]"
                        value="{{ old('session.breakfast.revenue_beverage', isset($bf->revenue_beverage) ? number_format($bf->revenue_beverage, 0, ',', '.') : '') }}"
                        autocomplete="off" placeholder="0">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label text-xs font-semibold text-slate-600 mb-1">Others</label>
                <div class="input-group">
                    <span class="input-group-text rounded-l-xl bg-slate-50 text-slate-500 text-xs font-bold border-slate-300">Rp</span>
                    <input type="text" class="form-control rounded-r-xl border-slate-300 py-2 text-sm rupiah focus:border-[#172a40] focus:ring-[#172a40]" name="session[breakfast][revenue_others]"
                        value="{{ old('session.breakfast.revenue_others', isset($bf->revenue_others) ? number_format($bf->revenue_others, 0, ',', '.') : '') }}"
                        autocomplete="off" placeholder="0">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label text-xs font-semibold text-slate-600 mb-1">Event</label>
                <div class="input-group">
                    <span class="input-group-text rounded-l-xl bg-slate-50 text-slate-500 text-xs font-bold border-slate-300">Rp</span>
                    <input type="text" class="form-control rounded-r-xl border-slate-300 py-2 text-sm rupiah focus:border-[#172a40] focus:ring-[#172a40]" name="session[breakfast][revenue_event]"
                        value="{{ old('session.breakfast.revenue_event', isset($bf->revenue_event) ? number_format($bf->revenue_event, 0, ',', '.') : '') }}"
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
                @php $bfFoodVal = old('session.breakfast.upselling_data.food', $bf->upselling_data['food'] ?? []); @endphp
                <input type="hidden" id="input-breakfast-food-JM" name="session[breakfast][upselling_data][food]"
                    value="{{ is_array($bfFoodVal) ? json_encode($bfFoodVal) : $bfFoodVal }}">
                <div class="input-group mb-2">
                    <input type="text" class="form-control form-control-sm rounded-l-xl border-slate-300 text-xs" id="select-breakfast-food-JM" placeholder="Enter Food Name">
                    <input type="number" class="form-control form-control-sm border-slate-300 text-xs" id="pax-breakfast-food-JM"
                        placeholder="Qty" style="max-width: 70px;">
                    <button class="btn btn-sm rounded-r-xl px-3.5 text-xs font-semibold text-white inline-flex items-center gap-1 shadow-sm transition" style="background-color: #172a40; border-color: #172a40;" type="button"
                        onclick="addUpsellingItem('breakfast', 'food', 'JM')"><i class="ti ti-plus text-xs" style="color: #B89047;"></i> Add</button>
                </div>
                <ul class="list-group small" id="list-breakfast-food-JM"></ul>
            </div>
            <div class="col-md-6">
                <label class="form-label text-xs font-semibold text-slate-700 mb-1">Beverage Upselling</label>
                @php $bfBevVal = old('session.breakfast.upselling_data.beverage', $bf->upselling_data['beverage'] ?? []); @endphp
                <input type="hidden" id="input-breakfast-beverage-JM" name="session[breakfast][upselling_data][beverage]"
                    value="{{ is_array($bfBevVal) ? json_encode($bfBevVal) : $bfBevVal }}">
                <div class="input-group mb-2">
                    <input type="text" class="form-control form-control-sm rounded-l-xl border-slate-300 text-xs" id="select-breakfast-beverage-JM" placeholder="Enter Beverage Name">
                    <input type="number" class="form-control form-control-sm border-slate-300 text-xs" id="pax-breakfast-beverage-JM"
                        placeholder="Qty" style="max-width: 70px;">
                    <button class="btn btn-sm rounded-r-xl px-3.5 text-xs font-semibold text-white inline-flex items-center gap-1 shadow-sm transition" style="background-color: #172a40; border-color: #172a40;" type="button"
                        onclick="addUpsellingItem('breakfast', 'beverage', 'JM')"><i class="ti ti-plus text-xs" style="color: #B89047;"></i> Add</button>
                </div>
                <ul class="list-group small" id="list-breakfast-beverage-JM"></ul>
            </div>

            <div class="col-md-12">
                <label class="form-label text-xs font-semibold text-slate-700 mb-1">General Remarks</label>
                <textarea class="form-control rounded-xl border-slate-300 p-2.5 text-xs focus:border-[#172a40] focus:ring-[#172a40]" rows="2" name="session[breakfast][remarks]" placeholder="Catatan operasional sesi breakfast...">{{ old('session.breakfast.remarks', $bf->remarks ?? '') }}</textarea>
            </div>

            <div class="col-md-12 mt-3">
                <label class="form-label text-xs font-semibold text-slate-700 mb-1">Staff on Duty</label>
                @php $bfStaffVal = old('session.breakfast.staff_on_duty', $bf->staff_on_duty ?? []); @endphp
                <input type="hidden" id="input-staff-breakfast-JM" name="session[breakfast][staff_on_duty]"
                    value="{{ is_array($bfStaffVal) ? json_encode($bfStaffVal) : $bfStaffVal }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm rounded-l-xl border-slate-300 text-xs" id="select-staff-breakfast-JM">
                        <option value="" selected>Select Staff...</option>
                        @foreach ($myStaffList as $staff)
                            <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                        @endforeach
                    </select>
                    <button class="btn btn-sm rounded-r-xl px-3.5 text-xs font-semibold text-white inline-flex items-center gap-1 shadow-sm transition" style="background-color: #172a40; border-color: #172a40;" type="button" onclick="addStaffItem('breakfast', 'JM')">
                        <i class="ti ti-plus text-xs" style="color: #B89047;"></i> Add
                    </button>
                </div>
                <div id="list-staff-breakfast-JM" class="d-flex flex-wrap gap-1.5"></div>
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
                <input type="number" class="form-control rounded-xl border-slate-300 py-2 text-sm focus:border-[#172a40] focus:ring-[#172a40]" name="session[breakfast][competitor_data][shangrila_cover]"
                    value="{{ old('session.breakfast.competitor_data.shangrila_cover', $bf->competitor_data['shangrila_cover'] ?? '') }}"
                    placeholder="0">
            </div>
            <div class="col-md-4">
                <label class="form-label text-xs font-semibold text-slate-600 mb-1">JW Marriott</label>
                <input type="number" class="form-control rounded-xl border-slate-300 py-2 text-sm focus:border-[#172a40] focus:ring-[#172a40]" name="session[breakfast][competitor_data][jw_marriott_cover]"
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
                <p class="text-xs text-slate-500">Pencatatan sesi siang Joe Milano, cover in-house / walk-in, dan revenue</p>
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
                <label class="form-label text-xs font-semibold text-slate-600 mb-1">In House Adult</label>
                <input type="number" class="form-control rounded-xl border-slate-300 py-2 text-sm focus:border-[#172a40] focus:ring-[#172a40]" name="session[lunch][cover_data][in_house_adult]"
                    value="{{ old('session.lunch.cover_data.in_house_adult', $lc->cover_data['in_house_adult'] ?? '') }}"
                    placeholder="0">
            </div>
            <div class="col-md-3">
                <label class="form-label text-xs font-semibold text-slate-600 mb-1">In House Child</label>
                <input type="number" class="form-control rounded-xl border-slate-300 py-2 text-sm focus:border-[#172a40] focus:ring-[#172a40]" name="session[lunch][cover_data][in_house_child]"
                    value="{{ old('session.lunch.cover_data.in_house_child', $lc->cover_data['in_house_child'] ?? '') }}"
                    placeholder="0">
            </div>
            <div class="col-md-3">
                <label class="form-label text-xs font-semibold text-slate-600 mb-1">Walk In Adult</label>
                <input type="number" class="form-control rounded-xl border-slate-300 py-2 text-sm focus:border-[#172a40] focus:ring-[#172a40]" name="session[lunch][cover_data][walk_in_adult]"
                    value="{{ old('session.lunch.cover_data.walk_in_adult', $lc->cover_data['walk_in_adult'] ?? '') }}"
                    placeholder="0">
            </div>
            <div class="col-md-3">
                <label class="form-label text-xs font-semibold text-slate-600 mb-1">Walk In Child</label>
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
                <input type="hidden" id="input-occasion-lunch-JM" name="session[lunch][additional_data][occasion_items]" value="{{ $lcOccasionValue ?? '[]' }}">
                <div class="input-group mb-2.5">
                    <select class="form-select form-select-sm rounded-l-xl border-slate-300 text-xs font-medium" id="occasion-type-lunch-JM" onchange="toggleOccasionOther('lunch', 'JM')">
                        <option value="" selected>Select Occasion...</option>
                        <option value="Wedding Party">Wedding Party</option>
                        <option value="Birthday Party">Birthday Party</option>
                        <option value="Social Event">Social Event</option>
                        <option value="Corporate Event">Corporate Event</option>
                        <option value="Other">Other</option>
                    </select>
                    <input type="text" class="form-control form-control-sm d-none border-slate-300 text-xs" id="occasion-other-lunch-JM" placeholder="Occasion Type" style="max-width: 150px;">
                    <input type="text" class="form-control form-control-sm border-slate-300 text-xs" id="occasion-name-lunch-JM"
                        placeholder="Name (e.g. Mr. Budi)">
                    <input type="number" class="form-control form-control-sm border-slate-300 text-xs" id="occasion-pax-lunch-JM"
                        placeholder="Pax" style="max-width: 80px;">
                    <input type="text" class="form-control form-control-sm rupiah border-slate-300 text-xs" id="occasion-revenue-lunch-JM"
                        placeholder="Revenue" style="max-width: 120px;" autocomplete="off">
                    <button class="btn btn-sm rounded-r-xl px-3.5 text-xs font-semibold text-white inline-flex items-center gap-1 shadow-sm transition" style="background-color: #172a40; border-color: #172a40;" type="button" onclick="addOccasionItem('lunch', 'JM')">
                        <i class="ti ti-plus text-xs" style="color: #B89047;"></i> Add
                    </button>
                </div>
                <ul class="list-group small" id="list-occasion-lunch-JM"></ul>
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
                <input type="hidden" id="input-promo-lunch-JM" name="session[lunch][additional_data][promo_items]" value="{{ $lcPromoValue ?? '[]' }}">
                <div class="input-group mb-2.5">
                    <select class="form-select form-select-sm rounded-l-xl border-slate-300 text-xs font-medium" id="promo-type-lunch-JM" onchange="togglePromoOther('lunch', 'JM')">
                        <option value="" selected>Select Promo...</option>
                        <option value="Mandiri Card">Mandiri Card</option>
                        <option value="BCA Card">BCA Card</option>
                        <option value="Membership">Membership</option>
                        <option value="Other">Other</option>
                    </select>
                    <input type="text" class="form-control form-control-sm d-none border-slate-300 text-xs" id="promo-other-lunch-JM" placeholder="Promo Name" style="max-width: 150px;">
                    <input type="number" class="form-control form-control-sm border-slate-300 text-xs" id="promo-pax-lunch-JM"
                        placeholder="Pax" style="max-width: 80px;">
                    <input type="text" class="form-control form-control-sm rupiah border-slate-300 text-xs" id="promo-revenue-lunch-JM"
                        placeholder="Revenue" style="max-width: 120px;" autocomplete="off">
                    <button class="btn btn-sm rounded-r-xl px-3.5 text-xs font-semibold text-white inline-flex items-center gap-1 shadow-sm transition" style="background-color: #172a40; border-color: #172a40;" type="button" onclick="addPromoItem('lunch', 'JM')">
                        <i class="ti ti-plus text-xs" style="color: #B89047;"></i> Add
                    </button>
                </div>
                <ul class="list-group small" id="list-promo-lunch-JM"></ul>
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
                <input type="hidden" id="input-lunch-food-JM" name="session[lunch][upselling_data][food]"
                    value="{{ is_array($lcFoodVal) ? json_encode($lcFoodVal) : $lcFoodVal }}">
                <div class="input-group mb-2">
                    <input type="text" class="form-control form-control-sm rounded-l-xl border-slate-300 text-xs" id="select-lunch-food-JM" placeholder="Enter Food Name">
                    <input type="number" class="form-control form-control-sm border-slate-300 text-xs" id="pax-lunch-food-JM"
                        placeholder="Qty" style="max-width: 70px;">
                    <button class="btn btn-sm rounded-r-xl px-3.5 text-xs font-semibold text-white inline-flex items-center gap-1 shadow-sm transition" style="background-color: #172a40; border-color: #172a40;" type="button"
                        onclick="addUpsellingItem('lunch', 'food', 'JM')"><i class="ti ti-plus text-xs" style="color: #B89047;"></i> Add</button>
                </div>
                <ul class="list-group small" id="list-lunch-food-JM"></ul>
            </div>
            <div class="col-md-6">
                <label class="form-label text-xs font-semibold text-slate-700 mb-1">Beverage Upselling</label>
                @php $lcBevVal = old('session.lunch.upselling_data.beverage', $lc->upselling_data['beverage'] ?? []); @endphp
                <input type="hidden" id="input-lunch-beverage-JM" name="session[lunch][upselling_data][beverage]"
                    value="{{ is_array($lcBevVal) ? json_encode($lcBevVal) : $lcBevVal }}">
                <div class="input-group mb-2">
                    <input type="text" class="form-control form-control-sm rounded-l-xl border-slate-300 text-xs" id="select-lunch-beverage-JM" placeholder="Enter Beverage Name">
                    <input type="number" class="form-control form-control-sm border-slate-300 text-xs" id="pax-lunch-beverage-JM"
                        placeholder="Qty" style="max-width: 70px;">
                    <button class="btn btn-sm rounded-r-xl px-3.5 text-xs font-semibold text-white inline-flex items-center gap-1 shadow-sm transition" style="background-color: #172a40; border-color: #172a40;" type="button"
                        onclick="addUpsellingItem('lunch', 'beverage', 'JM')"><i class="ti ti-plus text-xs" style="color: #B89047;"></i> Add</button>
                </div>
                <ul class="list-group small" id="list-lunch-beverage-JM"></ul>
            </div>

            <div class="col-md-12">
                <label class="form-label text-xs font-semibold text-slate-700 mb-1">General Remarks</label>
                <textarea class="form-control rounded-xl border-slate-300 p-2.5 text-xs focus:border-[#172a40] focus:ring-[#172a40]" rows="2" name="session[lunch][remarks]" placeholder="Catatan operasional sesi lunch...">{{ old('session.lunch.remarks', $lc->remarks ?? '') }}</textarea>
            </div>

            <div class="col-md-12 mt-3">
                <label class="form-label text-xs font-semibold text-slate-700 mb-1">Staff on Duty</label>
                @php $lcStaffVal = old('session.lunch.staff_on_duty', $lc->staff_on_duty ?? []); @endphp
                <input type="hidden" id="input-staff-lunch-JM" name="session[lunch][staff_on_duty]"
                    value="{{ is_array($lcStaffVal) ? json_encode($lcStaffVal) : $lcStaffVal }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm rounded-l-xl border-slate-300 text-xs" id="select-staff-lunch-JM">
                        <option value="" selected>Select Staff...</option>
                        @foreach ($myStaffList as $staff)
                            <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                        @endforeach
                    </select>
                    <button class="btn btn-sm rounded-r-xl px-3.5 text-xs font-semibold text-white inline-flex items-center gap-1 shadow-sm transition" style="background-color: #172a40; border-color: #172a40;" type="button" onclick="addStaffItem('lunch', 'JM')">
                        <i class="ti ti-plus text-xs" style="color: #B89047;"></i> Add
                    </button>
                </div>
                <div id="list-staff-lunch-JM" class="d-flex flex-wrap gap-1.5"></div>
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
                <p class="text-xs text-slate-500">Pencatatan sesi malam Joe Milano, cover in-house / walk-in, dan revenue</p>
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
                <label class="form-label text-xs font-semibold text-slate-600 mb-1">In House Adult</label>
                <input type="number" class="form-control rounded-xl border-slate-300 py-2 text-sm focus:border-[#172a40] focus:ring-[#172a40]" name="session[dinner][cover_data][in_house_adult]"
                    value="{{ old('session.dinner.cover_data.in_house_adult', $dn->cover_data['in_house_adult'] ?? '') }}"
                    placeholder="0">
            </div>
            <div class="col-md-3">
                <label class="form-label text-xs font-semibold text-slate-600 mb-1">In House Child</label>
                <input type="number" class="form-control rounded-xl border-slate-300 py-2 text-sm focus:border-[#172a40] focus:ring-[#172a40]" name="session[dinner][cover_data][in_house_child]"
                    value="{{ old('session.dinner.cover_data.in_house_child', $dn->cover_data['in_house_child'] ?? '') }}"
                    placeholder="0">
            </div>
            <div class="col-md-3">
                <label class="form-label text-xs font-semibold text-slate-600 mb-1">Walk In Adult</label>
                <input type="number" class="form-control rounded-xl border-slate-300 py-2 text-sm focus:border-[#172a40] focus:ring-[#172a40]" name="session[dinner][cover_data][walk_in_adult]"
                    value="{{ old('session.dinner.cover_data.walk_in_adult', $dn->cover_data['walk_in_adult'] ?? '') }}"
                    placeholder="0">
            </div>
            <div class="col-md-3">
                <label class="form-label text-xs font-semibold text-slate-600 mb-1">Walk In Child</label>
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
                <input type="hidden" id="input-occasion-dinner-JM" name="session[dinner][additional_data][occasion_items]" value="{{ $dnOccasionValue ?? '[]' }}">
                <div class="input-group mb-2.5">
                    <select class="form-select form-select-sm rounded-l-xl border-slate-300 text-xs font-medium" id="occasion-type-dinner-JM" onchange="toggleOccasionOther('dinner', 'JM')">
                        <option value="" selected>Select Occasion...</option>
                        <option value="Wedding Party">Wedding Party</option>
                        <option value="Birthday Party">Birthday Party</option>
                        <option value="Social Event">Social Event</option>
                        <option value="Corporate Event">Corporate Event</option>
                        <option value="Other">Other</option>
                    </select>
                    <input type="text" class="form-control form-control-sm d-none border-slate-300 text-xs" id="occasion-other-dinner-JM" placeholder="Occasion Type" style="max-width: 150px;">
                    <input type="text" class="form-control form-control-sm border-slate-300 text-xs" id="occasion-name-dinner-JM"
                        placeholder="Name (e.g. Mr. Budi)">
                    <input type="number" class="form-control form-control-sm border-slate-300 text-xs" id="occasion-pax-dinner-JM"
                        placeholder="Pax" style="max-width: 80px;">
                    <input type="text" class="form-control form-control-sm rupiah border-slate-300 text-xs" id="occasion-revenue-dinner-JM"
                        placeholder="Revenue" style="max-width: 120px;" autocomplete="off">
                    <button class="btn btn-sm rounded-r-xl px-3.5 text-xs font-semibold text-white inline-flex items-center gap-1 shadow-sm transition" style="background-color: #172a40; border-color: #172a40;" type="button" onclick="addOccasionItem('dinner', 'JM')">
                        <i class="ti ti-plus text-xs" style="color: #B89047;"></i> Add
                    </button>
                </div>
                <ul class="list-group small" id="list-occasion-dinner-JM"></ul>
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
                <input type="hidden" id="input-promo-dinner-JM" name="session[dinner][additional_data][promo_items]" value="{{ $dnPromoValue ?? '[]' }}">
                <div class="input-group mb-2.5">
                    <select class="form-select form-select-sm rounded-l-xl border-slate-300 text-xs font-medium" id="promo-type-dinner-JM" onchange="togglePromoOther('dinner', 'JM')">
                        <option value="" selected>Select Promo...</option>
                        <option value="Mandiri Card">Mandiri Card</option>
                        <option value="BCA Card">BCA Card</option>
                        <option value="Membership">Membership</option>
                        <option value="Other">Other</option>
                    </select>
                    <input type="text" class="form-control form-control-sm d-none border-slate-300 text-xs" id="promo-other-dinner-JM" placeholder="Promo Name" style="max-width: 150px;">
                    <input type="number" class="form-control form-control-sm border-slate-300 text-xs" id="promo-pax-dinner-JM"
                        placeholder="Pax" style="max-width: 80px;">
                    <input type="text" class="form-control form-control-sm rupiah border-slate-300 text-xs" id="promo-revenue-dinner-JM"
                        placeholder="Revenue" style="max-width: 120px;" autocomplete="off">
                    <button class="btn btn-sm rounded-r-xl px-3.5 text-xs font-semibold text-white inline-flex items-center gap-1 shadow-sm transition" style="background-color: #172a40; border-color: #172a40;" type="button" onclick="addPromoItem('dinner', 'JM')">
                        <i class="ti ti-plus text-xs" style="color: #B89047;"></i> Add
                    </button>
                </div>
                <ul class="list-group small" id="list-promo-dinner-JM"></ul>
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
                <input type="hidden" id="input-dinner-food-JM" name="session[dinner][upselling_data][food]"
                    value="{{ is_array($dnFoodVal) ? json_encode($dnFoodVal) : $dnFoodVal }}">
                <div class="input-group mb-2">
                    <input type="text" class="form-control form-control-sm rounded-l-xl border-slate-300 text-xs" id="select-dinner-food-JM" placeholder="Enter Food Name">
                    <input type="number" class="form-control form-control-sm border-slate-300 text-xs" id="pax-dinner-food-JM"
                        placeholder="Qty" style="max-width: 70px;">
                    <button class="btn btn-sm rounded-r-xl px-3.5 text-xs font-semibold text-white inline-flex items-center gap-1 shadow-sm transition" style="background-color: #172a40; border-color: #172a40;" type="button"
                        onclick="addUpsellingItem('dinner', 'food', 'JM')"><i class="ti ti-plus text-xs" style="color: #B89047;"></i> Add</button>
                </div>
                <ul class="list-group small" id="list-dinner-food-JM"></ul>
            </div>
            <div class="col-md-6">
                <label class="form-label text-xs font-semibold text-slate-700 mb-1">Beverage Upselling</label>
                @php $dnBevVal = old('session.dinner.upselling_data.beverage', $dn->upselling_data['beverage'] ?? []); @endphp
                <input type="hidden" id="input-dinner-beverage-JM" name="session[dinner][upselling_data][beverage]"
                    value="{{ is_array($dnBevVal) ? json_encode($dnBevVal) : $dnBevVal }}">
                <div class="input-group mb-2">
                    <input type="text" class="form-control form-control-sm rounded-l-xl border-slate-300 text-xs" id="select-dinner-beverage-JM" placeholder="Enter Beverage Name">
                    <input type="number" class="form-control form-control-sm border-slate-300 text-xs" id="pax-dinner-beverage-JM"
                        placeholder="Qty" style="max-width: 70px;">
                    <button class="btn btn-sm rounded-r-xl px-3.5 text-xs font-semibold text-white inline-flex items-center gap-1 shadow-sm transition" style="background-color: #172a40; border-color: #172a40;" type="button"
                        onclick="addUpsellingItem('dinner', 'beverage', 'JM')"><i class="ti ti-plus text-xs" style="color: #B89047;"></i> Add</button>
                </div>
                <ul class="list-group small" id="list-dinner-beverage-JM"></ul>
            </div>

            <div class="col-md-12">
                <label class="form-label text-xs font-semibold text-slate-700 mb-1">General Remarks</label>
                <textarea class="form-control rounded-xl border-slate-300 p-2.5 text-xs focus:border-[#172a40] focus:ring-[#172a40]" rows="2" name="session[dinner][remarks]" placeholder="Catatan operasional sesi dinner...">{{ old('session.dinner.remarks', $dn->remarks ?? '') }}</textarea>
            </div>

            <div class="col-md-12 mt-3">
                <label class="form-label text-xs font-semibold text-slate-700 mb-1">Staff on Duty</label>
                @php $dnStaffVal = old('session.dinner.staff_on_duty', $dn->staff_on_duty ?? []); @endphp
                <input type="hidden" id="input-staff-dinner-JM" name="session[dinner][staff_on_duty]"
                    value="{{ is_array($dnStaffVal) ? json_encode($dnStaffVal) : $dnStaffVal }}">
                <div class="input-group mb-2">
                    <select class="form-select form-select-sm rounded-l-xl border-slate-300 text-xs" id="select-staff-dinner-JM">
                        <option value="" selected>Select Staff...</option>
                        @foreach ($myStaffList as $staff)
                            <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                        @endforeach
                    </select>
                    <button class="btn btn-sm rounded-r-xl px-3.5 text-xs font-semibold text-white inline-flex items-center gap-1 shadow-sm transition" style="background-color: #172a40; border-color: #172a40;" type="button" onclick="addStaffItem('dinner', 'JM')">
                        <i class="ti ti-plus text-xs" style="color: #B89047;"></i> Add
                    </button>
                </div>
                <div id="list-staff-dinner-JM" class="d-flex flex-wrap gap-1.5"></div>
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
        initUpselling('breakfast', 'food', bfFood, 'JM');
        let bfBev = {!! json_encode(old('session.breakfast.upselling_data.beverage', $bf->upselling_data['beverage'] ?? [])) !!};
        initUpselling('breakfast', 'beverage', bfBev, 'JM');
        let bfStaff = {!! json_encode(old('session.breakfast.staff_on_duty', $bf->staff_on_duty ?? [])) !!};
        initStaff('breakfast', bfStaff, 'JM');
        let bfOccasion = {!! json_encode(old('session.breakfast.additional_data.occasion_items', $bf->additional_data['occasion_items'] ?? [])) !!};
        initOccasion('breakfast', bfOccasion, 'JM');
        let bfPromo = {!! json_encode(old('session.breakfast.additional_data.promo_items', $bf->additional_data['promo_items'] ?? [])) !!};
        initPromoItems('breakfast', bfPromo, 'JM');

        // --- LUNCH INIT ---
        let lcFood = {!! json_encode(old('session.lunch.upselling_data.food', $lc->upselling_data['food'] ?? [])) !!};
        initUpselling('lunch', 'food', lcFood, 'JM');
        let lcBev = {!! json_encode(old('session.lunch.upselling_data.beverage', $lc->upselling_data['beverage'] ?? [])) !!};
        initUpselling('lunch', 'beverage', lcBev, 'JM');
        let lcStaff = {!! json_encode(old('session.lunch.staff_on_duty', $lc->staff_on_duty ?? [])) !!};
        initStaff('lunch', lcStaff, 'JM');
        let lcOccasion = {!! json_encode(old('session.lunch.additional_data.occasion_items', $lc->additional_data['occasion_items'] ?? [])) !!};
        initOccasion('lunch', lcOccasion, 'JM');
        let lcPromo = {!! json_encode(old('session.lunch.additional_data.promo_items', $lc->additional_data['promo_items'] ?? [])) !!};
        initPromoItems('lunch', lcPromo, 'JM');

        // --- DINNER INIT ---
        let dnFood = {!! json_encode(old('session.dinner.upselling_data.food', $dn->upselling_data['food'] ?? [])) !!};
        initUpselling('dinner', 'food', dnFood, 'JM');
        let dnBev = {!! json_encode(old('session.dinner.upselling_data.beverage', $dn->upselling_data['beverage'] ?? [])) !!};
        initUpselling('dinner', 'beverage', dnBev, 'JM');
        let dnStaff = {!! json_encode(old('session.dinner.staff_on_duty', $dn->staff_on_duty ?? [])) !!};
        initStaff('dinner', dnStaff, 'JM');
        let dnOccasion = {!! json_encode(old('session.dinner.additional_data.occasion_items', $dn->additional_data['occasion_items'] ?? [])) !!};
        initOccasion('dinner', dnOccasion, 'JM');
        let dnPromo = {!! json_encode(old('session.dinner.additional_data.promo_items', $dn->additional_data['promo_items'] ?? [])) !!};
        initPromoItems('dinner', dnPromo, 'JM');

    });
</script>
