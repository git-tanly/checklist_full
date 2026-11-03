{{-- FORM KHUSUS BANQUET (BQT) --}}

@php
    // 1. Ambil Data Detail per Sesi
    $bf = $details['breakfast'] ?? null;
    $lc = $details['lunch'] ?? null;
    $dn = $details['dinner'] ?? null;

    // 2. Ambil Data Master (Staff) - BQT tidak butuh menu upselling
    $restoBqt = $restaurants->where('code', 'BQT')->first();
    $myStaffList = $restoBqt ? $restoBqt->employees : [];
@endphp

{{-- ============================================================ --}}
{{-- SESSION: BREAKFAST                                          --}}
{{-- ============================================================ --}}
<div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden mb-6 transition-all duration-200 hover:shadow-md">
    {{-- Header Sesi --}}
    <div class="bg-gradient-to-r from-amber-500/10 via-amber-500/5 to-transparent border-b border-amber-200/60 px-6 py-4 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-200 shadow-xs">
                <i class="ti ti-sun text-amber-600"></i> Breakfast Session
            </span>
            <span class="text-xs text-slate-500">Morning Banquet & MICE Operation</span>
        </div>
        <span class="text-xs font-medium text-slate-400">06:00 - 10:00</span>
    </div>

    <div class="p-6 space-y-6">
        {{-- 1. COVER REPORT --}}
        <div>
            <div class="flex items-center justify-between border-b border-slate-100 pb-2 mb-4">
                <h6 class="text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#B89047]"></span>
                    1. Cover Report (Pax)
                </h6>
                <span class="text-[11px] text-slate-400">Total guest attendance breakdown</span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center gap-1.5">
                        <i class="ti ti-briefcase text-slate-400"></i> MICE (Pax)
                    </label>
                    <div class="relative">
                        <input type="number" min="0" class="w-full pl-3.5 pr-12 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all"
                            name="session[breakfast][cover_data][mice]"
                            value="{{ old('session.breakfast.cover_data.mice', $bf->cover_data['mice'] ?? '') }}"
                            placeholder="0">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-medium text-slate-400 pointer-events-none">Pax</span>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center gap-1.5">
                        <i class="ti ti-rings text-slate-400"></i> Wedding (Pax)
                    </label>
                    <div class="relative">
                        <input type="number" min="0" class="w-full pl-3.5 pr-12 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all"
                            name="session[breakfast][cover_data][wedding]"
                            value="{{ old('session.breakfast.cover_data.wedding', $bf->cover_data['wedding'] ?? '') }}"
                            placeholder="0">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-medium text-slate-400 pointer-events-none">Pax</span>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center gap-1.5">
                        <i class="ti ti-cake text-slate-400"></i> Birthday (Pax)
                    </label>
                    <div class="relative">
                        <input type="number" min="0" class="w-full pl-3.5 pr-12 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all"
                            name="session[breakfast][cover_data][birthday]"
                            value="{{ old('session.breakfast.cover_data.birthday', $bf->cover_data['birthday'] ?? '') }}"
                            placeholder="0">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-medium text-slate-400 pointer-events-none">Pax</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. REVENUE REPORT --}}
        <div>
            <div class="flex items-center justify-between border-b border-slate-100 pb-2 mb-4">
                <h6 class="text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#B89047]"></span>
                    2. Revenue Report (IDR)
                </h6>
                <span class="text-[11px] text-slate-400">Custom Banquet event breakdown</span>
            </div>

            {{-- Hidden Input untuk Total Revenue Event --}}
            <input type="hidden" id="total-rev-breakfast-BQT" name="session[breakfast][revenue_event]"
                value="{{ old('session.breakfast.revenue_event', isset($bf->revenue_event) ? $bf->revenue_event : 0) }}">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Revenue MICE</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-slate-400">Rp</span>
                        <input type="text" class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all rupiah bqt-rev-breakfast"
                            name="session[breakfast][additional_data][revenue_mice]"
                            value="{{ old('session.breakfast.additional_data.revenue_mice', $bf->additional_data['revenue_mice'] ?? '') }}"
                            oninput="calculateTotalBqt('breakfast')" autocomplete="off" placeholder="0">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Revenue Wedding</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-slate-400">Rp</span>
                        <input type="text" class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all rupiah bqt-rev-breakfast"
                            name="session[breakfast][additional_data][revenue_wedding]"
                            value="{{ old('session.breakfast.additional_data.revenue_wedding', $bf->additional_data['revenue_wedding'] ?? '') }}"
                            oninput="calculateTotalBqt('breakfast')" autocomplete="off" placeholder="0">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Revenue Birthday</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-slate-400">Rp</span>
                        <input type="text" class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all rupiah bqt-rev-breakfast"
                            name="session[breakfast][additional_data][revenue_birthday]"
                            value="{{ old('session.breakfast.additional_data.revenue_birthday', $bf->additional_data['revenue_birthday'] ?? '') }}"
                            oninput="calculateTotalBqt('breakfast')" autocomplete="off" placeholder="0">
                    </div>
                </div>
            </div>

            {{-- Summary Total Display --}}
            <div class="bg-gradient-to-r from-slate-50 via-amber-50/20 to-slate-50 rounded-xl p-4 border border-slate-200/90 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-[#172a40]/5 border border-[#172a40]/10 flex items-center justify-center text-[#172a40]">
                        <i class="ti ti-calculator text-base"></i>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-slate-700 uppercase tracking-wider">Total Calculated Revenue</div>
                        <div class="text-[11px] text-slate-400">Sum of MICE + Wedding + Birthday for Breakfast</div>
                    </div>
                </div>
                <div class="sm:text-right">
                    <span class="text-xs text-slate-400 mr-2 font-medium">Session Total:</span>
                    <span class="text-lg md:text-xl font-black text-[#172a40]" id="display-total-breakfast-BQT">Rp 0</span>
                </div>
            </div>
        </div>

        {{-- 3. REMARKS & STAFF --}}
        <div>
            <div class="flex items-center justify-between border-b border-slate-100 pb-2 mb-4">
                <h6 class="text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#B89047]"></span>
                    3. Remarks & Staff
                </h6>
                <span class="text-[11px] text-slate-400">Operational notes & team assignment</span>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">General Remarks</label>
                    <textarea class="w-full px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all resize-y min-h-[70px]"
                        rows="2" name="session[breakfast][remarks]" placeholder="Enter banquet operational remarks or notes...">{{ old('session.breakfast.remarks', $bf->remarks ?? '') }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Staff on Duty</label>
                    @php
                        $bfStaffVal = old('session.breakfast.staff_on_duty', $bf->staff_on_duty ?? []);
                        $bfStaffJson = is_array($bfStaffVal) ? json_encode($bfStaffVal) : $bfStaffVal;
                    @endphp
                    <input type="hidden" id="input-staff-breakfast-BQT" name="session[breakfast][staff_on_duty]"
                        value="{{ $bfStaffJson }}">

                    <div class="flex items-center gap-2 max-w-md mb-2">
                        <select class="w-full px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all"
                            id="select-staff-breakfast-BQT">
                            <option value="" selected>Select Staff...</option>
                            @foreach ($myStaffList as $staff)
                                <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                            @endforeach
                        </select>
                        <button class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold bg-[#172a40] text-white hover:bg-[#112031] transition-all shadow-xs shrink-0 cursor-pointer"
                            type="button" onclick="addStaffItem('breakfast', 'BQT')">
                            <i class="ti ti-plus"></i> Add
                        </button>
                    </div>
                    <div id="list-staff-breakfast-BQT" class="flex flex-wrap gap-2 min-h-[38px] p-2.5 bg-slate-50/80 border border-slate-200 rounded-xl"></div>
                </div>
            </div>
        </div>

        {{-- 4. COMPETITOR --}}
        <div>
            <div class="flex items-center justify-between border-b border-slate-100 pb-2 mb-4">
                <h6 class="text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#B89047]"></span>
                    4. Competitor Comparison
                </h6>
                <span class="text-[11px] text-slate-400">Market benchmark estimation (Cover)</span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Shangri-La</label>
                    <input type="number" min="0" class="w-full px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all"
                        name="session[breakfast][competitor_data][shangrila_cover]"
                        value="{{ old('session.breakfast.competitor_data.shangrila_cover', $bf->competitor_data['shangrila_cover'] ?? '') }}"
                        placeholder="0">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">JW Marriott</label>
                    <input type="number" min="0" class="w-full px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all"
                        name="session[breakfast][competitor_data][jw_marriott_cover]"
                        value="{{ old('session.breakfast.competitor_data.jw_marriott_cover', $bf->competitor_data['jw_marriott_cover'] ?? '') }}"
                        placeholder="0">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Sheraton</label>
                    <input type="number" min="0" class="w-full px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all"
                        name="session[breakfast][competitor_data][sheraton_cover]"
                        value="{{ old('session.breakfast.competitor_data.sheraton_cover', $bf->competitor_data['sheraton_cover'] ?? '') }}"
                        placeholder="0">
                </div>
            </div>
        </div>
    </div>
</div>


{{-- ============================================================ --}}
{{-- SESSION: LUNCH                                              --}}
{{-- ============================================================ --}}
<div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden mb-6 transition-all duration-200 hover:shadow-md">
    {{-- Header Sesi --}}
    <div class="bg-gradient-to-r from-sky-500/10 via-sky-500/5 to-transparent border-b border-sky-200/60 px-6 py-4 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-sky-100 text-sky-800 border border-sky-200 shadow-xs">
                <i class="ti ti-soup text-sky-600"></i> Lunch Session
            </span>
            <span class="text-xs text-slate-500">Midday Banquet & Luncheon Events</span>
        </div>
        <span class="text-xs font-medium text-slate-400">11:30 - 15:00</span>
    </div>

    <div class="p-6 space-y-6">
        {{-- 1. COVER REPORT --}}
        <div>
            <div class="flex items-center justify-between border-b border-slate-100 pb-2 mb-4">
                <h6 class="text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#B89047]"></span>
                    1. Cover Report (Pax)
                </h6>
                <span class="text-[11px] text-slate-400">Total guest attendance breakdown</span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center gap-1.5">
                        <i class="ti ti-briefcase text-slate-400"></i> MICE (Pax)
                    </label>
                    <div class="relative">
                        <input type="number" min="0" class="w-full pl-3.5 pr-12 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all"
                            name="session[lunch][cover_data][mice]"
                            value="{{ old('session.lunch.cover_data.mice', $lc->cover_data['mice'] ?? '') }}"
                            placeholder="0">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-medium text-slate-400 pointer-events-none">Pax</span>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center gap-1.5">
                        <i class="ti ti-rings text-slate-400"></i> Wedding (Pax)
                    </label>
                    <div class="relative">
                        <input type="number" min="0" class="w-full pl-3.5 pr-12 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all"
                            name="session[lunch][cover_data][wedding]"
                            value="{{ old('session.lunch.cover_data.wedding', $lc->cover_data['wedding'] ?? '') }}"
                            placeholder="0">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-medium text-slate-400 pointer-events-none">Pax</span>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center gap-1.5">
                        <i class="ti ti-cake text-slate-400"></i> Birthday (Pax)
                    </label>
                    <div class="relative">
                        <input type="number" min="0" class="w-full pl-3.5 pr-12 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all"
                            name="session[lunch][cover_data][birthday]"
                            value="{{ old('session.lunch.cover_data.birthday', $lc->cover_data['birthday'] ?? '') }}"
                            placeholder="0">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-medium text-slate-400 pointer-events-none">Pax</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. REVENUE REPORT --}}
        <div>
            <div class="flex items-center justify-between border-b border-slate-100 pb-2 mb-4">
                <h6 class="text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#B89047]"></span>
                    2. Revenue Report (IDR)
                </h6>
                <span class="text-[11px] text-slate-400">Custom Banquet event breakdown</span>
            </div>

            <input type="hidden" id="total-rev-lunch-BQT" name="session[lunch][revenue_event]"
                value="{{ old('session.lunch.revenue_event', isset($lc->revenue_event) ? $lc->revenue_event : 0) }}">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Revenue MICE</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-slate-400">Rp</span>
                        <input type="text" class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all rupiah bqt-rev-lunch"
                            name="session[lunch][additional_data][revenue_mice]"
                            value="{{ old('session.lunch.additional_data.revenue_mice', $lc->additional_data['revenue_mice'] ?? '') }}"
                            oninput="calculateTotalBqt('lunch')" placeholder="0" autocomplete="off">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Revenue Wedding</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-slate-400">Rp</span>
                        <input type="text" class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all rupiah bqt-rev-lunch"
                            name="session[lunch][additional_data][revenue_wedding]"
                            value="{{ old('session.lunch.additional_data.revenue_wedding', $lc->additional_data['revenue_wedding'] ?? '') }}"
                            oninput="calculateTotalBqt('lunch')" placeholder="0" autocomplete="off">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Revenue Birthday</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-slate-400">Rp</span>
                        <input type="text" class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all rupiah bqt-rev-lunch"
                            name="session[lunch][additional_data][revenue_birthday]"
                            value="{{ old('session.lunch.additional_data.revenue_birthday', $lc->additional_data['revenue_birthday'] ?? '') }}"
                            oninput="calculateTotalBqt('lunch')" placeholder="0" autocomplete="off">
                    </div>
                </div>
            </div>

            {{-- Summary Total Display --}}
            <div class="bg-gradient-to-r from-slate-50 via-sky-50/20 to-slate-50 rounded-xl p-4 border border-slate-200/90 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-[#172a40]/5 border border-[#172a40]/10 flex items-center justify-center text-[#172a40]">
                        <i class="ti ti-calculator text-base"></i>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-slate-700 uppercase tracking-wider">Total Calculated Revenue</div>
                        <div class="text-[11px] text-slate-400">Sum of MICE + Wedding + Birthday for Lunch</div>
                    </div>
                </div>
                <div class="sm:text-right">
                    <span class="text-xs text-slate-400 mr-2 font-medium">Session Total:</span>
                    <span class="text-lg md:text-xl font-black text-[#172a40]" id="display-total-lunch-BQT">Rp 0</span>
                </div>
            </div>
        </div>

        {{-- 3. REMARKS & STAFF --}}
        <div>
            <div class="flex items-center justify-between border-b border-slate-100 pb-2 mb-4">
                <h6 class="text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#B89047]"></span>
                    3. Remarks & Staff
                </h6>
                <span class="text-[11px] text-slate-400">Operational notes & team assignment</span>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">General Remarks</label>
                    <textarea class="w-full px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all resize-y min-h-[70px]"
                        rows="2" name="session[lunch][remarks]" placeholder="Enter banquet operational remarks or notes...">{{ old('session.lunch.remarks', $lc->remarks ?? '') }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Staff on Duty</label>
                    @php
                        $lcStaffVal = old('session.lunch.staff_on_duty', $lc->staff_on_duty ?? []);
                        $lcStaffJson = is_array($lcStaffVal) ? json_encode($lcStaffVal) : $lcStaffVal;
                    @endphp
                    <input type="hidden" id="input-staff-lunch-BQT" name="session[lunch][staff_on_duty]"
                        value="{{ $lcStaffJson }}">

                    <div class="flex items-center gap-2 max-w-md mb-2">
                        <select class="w-full px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all"
                            id="select-staff-lunch-BQT">
                            <option value="" selected>Select Staff...</option>
                            @foreach ($myStaffList as $staff)
                                <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                            @endforeach
                        </select>
                        <button class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold bg-[#172a40] text-white hover:bg-[#112031] transition-all shadow-xs shrink-0 cursor-pointer"
                            type="button" onclick="addStaffItem('lunch', 'BQT')">
                            <i class="ti ti-plus"></i> Add
                        </button>
                    </div>
                    <div id="list-staff-lunch-BQT" class="flex flex-wrap gap-2 min-h-[38px] p-2.5 bg-slate-50/80 border border-slate-200 rounded-xl"></div>
                </div>
            </div>
        </div>

        {{-- 4. COMPETITOR --}}
        <div>
            <div class="flex items-center justify-between border-b border-slate-100 pb-2 mb-4">
                <h6 class="text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#B89047]"></span>
                    4. Competitor Comparison
                </h6>
                <span class="text-[11px] text-slate-400">Market benchmark estimation (Cover)</span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Shangri-La</label>
                    <input type="number" min="0" class="w-full px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all"
                        name="session[lunch][competitor_data][shangrila_cover]"
                        value="{{ old('session.lunch.competitor_data.shangrila_cover', $lc->competitor_data['shangrila_cover'] ?? '') }}"
                        placeholder="0">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">JW Marriott</label>
                    <input type="number" min="0" class="w-full px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all"
                        name="session[lunch][competitor_data][jw_marriott_cover]"
                        value="{{ old('session.lunch.competitor_data.jw_marriott_cover', $lc->competitor_data['jw_marriott_cover'] ?? '') }}"
                        placeholder="0">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Sheraton</label>
                    <input type="number" min="0" class="w-full px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all"
                        name="session[lunch][competitor_data][sheraton_cover]"
                        value="{{ old('session.lunch.competitor_data.sheraton_cover', $lc->competitor_data['sheraton_cover'] ?? '') }}"
                        placeholder="0">
                </div>
            </div>
        </div>
    </div>
</div>


{{-- ============================================================ --}}
{{-- SESSION: DINNER                                             --}}
{{-- ============================================================ --}}
<div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden mb-6 transition-all duration-200 hover:shadow-md">
    {{-- Header Sesi --}}
    <div class="bg-gradient-to-r from-indigo-500/10 via-indigo-500/5 to-transparent border-b border-indigo-200/60 px-6 py-4 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800 border border-indigo-200 shadow-xs">
                <i class="ti ti-moon text-indigo-600"></i> Dinner Session
            </span>
            <span class="text-xs text-slate-500">Evening Gala, Wedding & Corporate Banquets</span>
        </div>
        <span class="text-xs font-medium text-slate-400">18:00 - 22:00</span>
    </div>

    <div class="p-6 space-y-6">
        {{-- 1. COVER REPORT --}}
        <div>
            <div class="flex items-center justify-between border-b border-slate-100 pb-2 mb-4">
                <h6 class="text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#B89047]"></span>
                    1. Cover Report (Pax)
                </h6>
                <span class="text-[11px] text-slate-400">Total guest attendance breakdown</span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center gap-1.5">
                        <i class="ti ti-briefcase text-slate-400"></i> MICE (Pax)
                    </label>
                    <div class="relative">
                        <input type="number" min="0" class="w-full pl-3.5 pr-12 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all"
                            name="session[dinner][cover_data][mice]"
                            value="{{ old('session.dinner.cover_data.mice', $dn->cover_data['mice'] ?? '') }}"
                            placeholder="0">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-medium text-slate-400 pointer-events-none">Pax</span>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center gap-1.5">
                        <i class="ti ti-rings text-slate-400"></i> Wedding (Pax)
                    </label>
                    <div class="relative">
                        <input type="number" min="0" class="w-full pl-3.5 pr-12 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all"
                            name="session[dinner][cover_data][wedding]"
                            value="{{ old('session.dinner.cover_data.wedding', $dn->cover_data['wedding'] ?? '') }}"
                            placeholder="0">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-medium text-slate-400 pointer-events-none">Pax</span>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center gap-1.5">
                        <i class="ti ti-cake text-slate-400"></i> Birthday (Pax)
                    </label>
                    <div class="relative">
                        <input type="number" min="0" class="w-full pl-3.5 pr-12 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all"
                            name="session[dinner][cover_data][birthday]"
                            value="{{ old('session.dinner.cover_data.birthday', $dn->cover_data['birthday'] ?? '') }}"
                            placeholder="0">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-medium text-slate-400 pointer-events-none">Pax</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. REVENUE REPORT --}}
        <div>
            <div class="flex items-center justify-between border-b border-slate-100 pb-2 mb-4">
                <h6 class="text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#B89047]"></span>
                    2. Revenue Report (IDR)
                </h6>
                <span class="text-[11px] text-slate-400">Custom Banquet event breakdown</span>
            </div>

            <input type="hidden" id="total-rev-dinner-BQT" name="session[dinner][revenue_event]"
                value="{{ old('session.dinner.revenue_event', isset($dn->revenue_event) ? $dn->revenue_event : 0) }}">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Revenue MICE</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-slate-400">Rp</span>
                        <input type="text" class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all rupiah bqt-rev-dinner"
                            name="session[dinner][additional_data][revenue_mice]"
                            value="{{ old('session.dinner.additional_data.revenue_mice', $dn->additional_data['revenue_mice'] ?? '') }}"
                            oninput="calculateTotalBqt('dinner')" placeholder="0" autocomplete="off">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Revenue Wedding</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-slate-400">Rp</span>
                        <input type="text" class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all rupiah bqt-rev-dinner"
                            name="session[dinner][additional_data][revenue_wedding]"
                            value="{{ old('session.dinner.additional_data.revenue_wedding', $dn->additional_data['revenue_wedding'] ?? '') }}"
                            oninput="calculateTotalBqt('dinner')" placeholder="0" autocomplete="off">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Revenue Birthday</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-slate-400">Rp</span>
                        <input type="text" class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all rupiah bqt-rev-dinner"
                            name="session[dinner][additional_data][revenue_birthday]"
                            value="{{ old('session.dinner.additional_data.revenue_birthday', $dn->additional_data['revenue_birthday'] ?? '') }}"
                            oninput="calculateTotalBqt('dinner')" placeholder="0" autocomplete="off">
                    </div>
                </div>
            </div>

            {{-- Summary Total Display --}}
            <div class="bg-gradient-to-r from-slate-50 via-indigo-50/20 to-slate-50 rounded-xl p-4 border border-slate-200/90 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-[#172a40]/5 border border-[#172a40]/10 flex items-center justify-center text-[#172a40]">
                        <i class="ti ti-calculator text-base"></i>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-slate-700 uppercase tracking-wider">Total Calculated Revenue</div>
                        <div class="text-[11px] text-slate-400">Sum of MICE + Wedding + Birthday for Dinner</div>
                    </div>
                </div>
                <div class="sm:text-right">
                    <span class="text-xs text-slate-400 mr-2 font-medium">Session Total:</span>
                    <span class="text-lg md:text-xl font-black text-[#172a40]" id="display-total-dinner-BQT">Rp 0</span>
                </div>
            </div>
        </div>

        {{-- 3. REMARKS & STAFF --}}
        <div>
            <div class="flex items-center justify-between border-b border-slate-100 pb-2 mb-4">
                <h6 class="text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#B89047]"></span>
                    3. Remarks & Staff
                </h6>
                <span class="text-[11px] text-slate-400">Operational notes & team assignment</span>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">General Remarks</label>
                    <textarea class="w-full px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all resize-y min-h-[70px]"
                        rows="2" name="session[dinner][remarks]" placeholder="Enter banquet operational remarks or notes...">{{ old('session.dinner.remarks', $dn->remarks ?? '') }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Staff on Duty</label>
                    @php
                        $dnStaffVal = old('session.dinner.staff_on_duty', $dn->staff_on_duty ?? []);
                        $dnStaffJson = is_array($dnStaffVal) ? json_encode($dnStaffVal) : $dnStaffVal;
                    @endphp
                    <input type="hidden" id="input-staff-dinner-BQT" name="session[dinner][staff_on_duty]"
                        value="{{ $dnStaffJson }}">

                    <div class="flex items-center gap-2 max-w-md mb-2">
                        <select class="w-full px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all"
                            id="select-staff-dinner-BQT">
                            <option value="" selected>Select Staff...</option>
                            @foreach ($myStaffList as $staff)
                                <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                            @endforeach
                        </select>
                        <button class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold bg-[#172a40] text-white hover:bg-[#112031] transition-all shadow-xs shrink-0 cursor-pointer"
                            type="button" onclick="addStaffItem('dinner', 'BQT')">
                            <i class="ti ti-plus"></i> Add
                        </button>
                    </div>
                    <div id="list-staff-dinner-BQT" class="flex flex-wrap gap-2 min-h-[38px] p-2.5 bg-slate-50/80 border border-slate-200 rounded-xl"></div>
                </div>
            </div>
        </div>

        {{-- 4. COMPETITOR --}}
        <div>
            <div class="flex items-center justify-between border-b border-slate-100 pb-2 mb-4">
                <h6 class="text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#B89047]"></span>
                    4. Competitor Comparison
                </h6>
                <span class="text-[11px] text-slate-400">Market benchmark estimation (Cover)</span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Shangri-La</label>
                    <input type="number" min="0" class="w-full px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all"
                        name="session[dinner][competitor_data][shangrila_cover]"
                        value="{{ old('session.dinner.competitor_data.shangrila_cover', $dn->competitor_data['shangrila_cover'] ?? '') }}"
                        placeholder="0">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">JW Marriott</label>
                    <input type="number" min="0" class="w-full px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all"
                        name="session[dinner][competitor_data][jw_marriott_cover]"
                        value="{{ old('session.dinner.competitor_data.jw_marriott_cover', $dn->competitor_data['jw_marriott_cover'] ?? '') }}"
                        placeholder="0">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Sheraton</label>
                    <input type="number" min="0" class="w-full px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all"
                        name="session[dinner][competitor_data][sheraton_cover]"
                        value="{{ old('session.dinner.competitor_data.sheraton_cover', $dn->competitor_data['sheraton_cover'] ?? '') }}"
                        placeholder="0">
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- SCRIPT INITIALIZATION & CALCULATOR                          --}}
{{-- ============================================================ --}}
<script>
    // Fungsi hitung total otomatis
    function calculateTotalBqt(session) {
        let total = 0;
        // Ambil semua input dengan class 'bqt-rev-session'
        const inputs = document.querySelectorAll(`.bqt-rev-${session}`);

        inputs.forEach(input => {
            // Hapus titik ribuan, ubah jadi angka
            let cleanVal = input.value.replace(/\./g, '');
            cleanVal = parseInt(cleanVal) || 0;
            total += cleanVal;
        });

        // Update Hidden Input (Untuk Database)
        const hiddenEl = document.getElementById(`total-rev-${session}-BQT`);
        if (hiddenEl) hiddenEl.value = total;

        // Update Tampilan Total
        const displayEl = document.getElementById(`display-total-${session}-BQT`);
        if (displayEl) displayEl.innerText = 'Rp ' + total.toLocaleString('id-ID');
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Init Staff
        let bfStaff = {!! json_encode(old('session.breakfast.staff_on_duty', $bf->staff_on_duty ?? [])) !!};
        if (typeof initStaff === 'function') initStaff('breakfast', bfStaff, 'BQT');

        let lcStaff = {!! json_encode(old('session.lunch.staff_on_duty', $lc->staff_on_duty ?? [])) !!};
        if (typeof initStaff === 'function') initStaff('lunch', lcStaff, 'BQT');

        let dnStaff = {!! json_encode(old('session.dinner.staff_on_duty', $dn->staff_on_duty ?? [])) !!};
        if (typeof initStaff === 'function') initStaff('dinner', dnStaff, 'BQT');

        // Trigger Kalkulasi Total saat Load (untuk Edit/Error State)
        calculateTotalBqt('breakfast');
        calculateTotalBqt('lunch');
        calculateTotalBqt('dinner');
    });
</script>
