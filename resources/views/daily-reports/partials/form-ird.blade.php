{{-- FORM KHUSUS IN ROOM DINING (IRD) --}}

@php
    // 1. Ambil Data Detail per Sesi (Ada 4 Sesi: Breakfast, Lunch, Dinner, Supper)
    $bf = $details['breakfast'] ?? null;
    $lc = $details['lunch'] ?? null;
    $dn = $details['dinner'] ?? null;
    $sp = $details['supper'] ?? null;

    // 2. Ambil Data Master
    $restoIrd = $restaurants->where('code', 'IRD')->first();
    $myStaffList = $restoIrd ? $restoIrd->employees : [];

    // 3. Upselling Menu
    $myMenu = $restoIrd && isset($upsellingItems[$restoIrd->id]) ? $upsellingItems[$restoIrd->id] : collect([]);
    $foods = $myMenu->where('type', 'food');
    $beverages = $myMenu->where('type', 'beverage');
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
            <span class="text-xs text-slate-500">Morning In-Room Dining Service</span>
        </div>
        <span class="text-xs font-medium text-slate-400">06:00 - 11:00</span>
    </div>

    <div class="p-6 space-y-6">
        {{-- 1. COVER REPORT --}}
        <div>
            <div class="flex items-center justify-between border-b border-slate-100 pb-2 mb-4">
                <h6 class="text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#B89047]"></span>
                    1. Cover Report (Pax)
                </h6>
                <span class="text-[11px] text-slate-400">Actual room service orders</span>
            </div>
            <div class="max-w-xs">
                <label class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center gap-1.5">
                    <i class="ti ti-users text-slate-400"></i> Total Actual Cover
                </label>
                <div class="relative">
                    <input type="number" min="0" class="w-full pl-3.5 pr-12 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all"
                        name="session[breakfast][cover_data][total_actual_cover]"
                        value="{{ old('session.breakfast.cover_data.total_actual_cover', $bf->cover_data['total_actual_cover'] ?? '') }}"
                        placeholder="0">
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-medium text-slate-400 pointer-events-none">Pax</span>
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
                <span class="text-[11px] text-slate-400">Net departmental sales</span>
            </div>

            <input type="hidden" name="session[breakfast][revenue_event]"
                value="{{ old('session.breakfast.revenue_event', isset($bf->revenue_event) ? $bf->revenue_event : 0) }}">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Food Revenue</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-slate-400">Rp</span>
                        <input type="text" class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all rupiah"
                            name="session[breakfast][revenue_food]"
                            value="{{ old('session.breakfast.revenue_food', isset($bf->revenue_food) ? number_format($bf->revenue_food, 0, ',', '.') : '') }}"
                            placeholder="0" autocomplete="off">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Beverage Revenue</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-slate-400">Rp</span>
                        <input type="text" class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all rupiah"
                            name="session[breakfast][revenue_beverage]"
                            value="{{ old('session.breakfast.revenue_beverage', isset($bf->revenue_beverage) ? number_format($bf->revenue_beverage, 0, ',', '.') : '') }}"
                            placeholder="0" autocomplete="off">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Others Revenue</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-slate-400">Rp</span>
                        <input type="text" class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all rupiah"
                            name="session[breakfast][revenue_others]"
                            value="{{ old('session.breakfast.revenue_others', isset($bf->revenue_others) ? number_format($bf->revenue_others, 0, ',', '.') : '') }}"
                            placeholder="0" autocomplete="off">
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. UPSELLING & STAFF --}}
        <div>
            <div class="flex items-center justify-between border-b border-slate-100 pb-2 mb-4">
                <h6 class="text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#B89047]"></span>
                    3. Upselling & Staff
                </h6>
                <span class="text-[11px] text-slate-400">Items sold, VIP notes, & on-duty roster</span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-5">
                {{-- Food Upselling --}}
                <div class="bg-slate-50/70 p-4 rounded-xl border border-slate-200/80">
                    <label class="block text-xs font-bold text-slate-700 mb-2 flex items-center gap-1.5">
                        <i class="ti ti-meat text-amber-600"></i> Upselling Menu (Food)
                    </label>
                    @php $bfFoodVal = old('session.breakfast.upselling_data.food', $bf->upselling_data['food'] ?? []); @endphp
                    <input type="hidden" id="input-breakfast-food-IRD" name="session[breakfast][upselling_data][food]"
                        value="{{ is_array($bfFoodVal) ? json_encode($bfFoodVal) : $bfFoodVal }}">

                    <div class="flex items-center gap-2 mb-2">
                        <input type="text" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all"
                            id="select-breakfast-food-IRD" placeholder="Enter Food Name">
                        <input type="number" min="1" class="w-20 px-3 py-2 bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 text-center focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all shrink-0"
                            id="pax-breakfast-food-IRD" placeholder="Qty">
                        <button class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold bg-[#172a40] text-white hover:bg-[#112031] transition-all shadow-xs shrink-0 cursor-pointer"
                            type="button" onclick="addUpsellingItem('breakfast', 'food', 'IRD')">
                            <i class="ti ti-plus"></i> Add
                        </button>
                    </div>
                    <ul class="list-none space-y-1.5 min-h-[42px] max-h-48 overflow-y-auto" id="list-breakfast-food-IRD"></ul>
                </div>

                {{-- Beverage Upselling --}}
                <div class="bg-slate-50/70 p-4 rounded-xl border border-slate-200/80">
                    <label class="block text-xs font-bold text-slate-700 mb-2 flex items-center gap-1.5">
                        <i class="ti ti-cup text-sky-600"></i> Beverage Upselling
                    </label>
                    @php $bfBevVal = old('session.breakfast.upselling_data.beverage', $bf->upselling_data['beverage'] ?? []); @endphp
                    <input type="hidden" id="input-breakfast-beverage-IRD"
                        name="session[breakfast][upselling_data][beverage]"
                        value="{{ is_array($bfBevVal) ? json_encode($bfBevVal) : $bfBevVal }}">

                    <div class="flex items-center gap-2 mb-2">
                        <input type="text" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all"
                            id="select-breakfast-beverage-IRD" placeholder="Enter Beverage Name">
                        <input type="number" min="1" class="w-20 px-3 py-2 bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 text-center focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all shrink-0"
                            id="pax-breakfast-beverage-IRD" placeholder="Qty">
                        <button class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold bg-[#172a40] text-white hover:bg-[#112031] transition-all shadow-xs shrink-0 cursor-pointer"
                            type="button" onclick="addUpsellingItem('breakfast', 'beverage', 'IRD')">
                            <i class="ti ti-plus"></i> Add
                        </button>
                    </div>
                    <ul class="list-none space-y-1.5 min-h-[42px] max-h-48 overflow-y-auto" id="list-breakfast-beverage-IRD"></ul>
                </div>
            </div>

            {{-- VIP Note / List --}}
            <div class="bg-slate-50/70 p-4 rounded-xl border border-slate-200/80 mb-5">
                <label class="block text-xs font-bold text-slate-700 mb-2 flex items-center gap-1.5">
                    <i class="ti ti-crown text-amber-500"></i> VIP Note / Guest Special Attention
                </label>
                @php $bfVipVal = old('session.breakfast.vip_remarks', $bf->vip_remarks ?? []); @endphp
                <input type="hidden" id="input-vip-breakfast-IRD" name="session[breakfast][vip_remarks]"
                    value="{{ is_array($bfVipVal) ? json_encode($bfVipVal) : $bfVipVal }}">

                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 mb-2">
                    <input type="text" class="w-full sm:w-1/2 px-3 py-2 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all"
                        id="vip-name-breakfast-IRD" placeholder="Guest Name / Room No">
                    <input type="text" class="w-full sm:w-1/2 px-3 py-2 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all"
                        id="vip-pos-breakfast-IRD" placeholder="Note / Position / Preference">
                    <button class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold bg-[#172a40] text-white hover:bg-[#112031] transition-all shadow-xs shrink-0 cursor-pointer"
                        type="button" onclick="addVipItem('breakfast', 'IRD')">
                        <i class="ti ti-plus"></i> Add VIP
                    </button>
                </div>
                <ul class="list-none space-y-1.5 min-h-[38px]" id="list-vip-breakfast-IRD"></ul>
            </div>

            {{-- Remarks & Staff --}}
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">General Remarks</label>
                    <textarea class="w-full px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all resize-y min-h-[60px]"
                        rows="2" name="session[breakfast][remarks]" placeholder="Enter breakfast operational remarks...">{{ old('session.breakfast.remarks', $bf->remarks ?? '') }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Staff on Duty</label>
                    @php $bfStaffVal = old('session.breakfast.staff_on_duty', $bf->staff_on_duty ?? []); @endphp
                    <input type="hidden" id="input-staff-breakfast-IRD" name="session[breakfast][staff_on_duty]"
                        value="{{ is_array($bfStaffVal) ? json_encode($bfStaffVal) : $bfStaffVal }}">

                    <div class="flex items-center gap-2 max-w-md mb-2">
                        <select class="w-full px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all"
                            id="select-staff-breakfast-IRD">
                            <option value="" selected>Select Staff...</option>
                            @foreach ($myStaffList as $staff)
                                <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                            @endforeach
                        </select>
                        <button class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold bg-[#172a40] text-white hover:bg-[#112031] transition-all shadow-xs shrink-0 cursor-pointer"
                            type="button" onclick="addStaffItem('breakfast', 'IRD')">
                            <i class="ti ti-plus"></i> Add
                        </button>
                    </div>
                    <div id="list-staff-breakfast-IRD" class="flex flex-wrap gap-2 min-h-[38px] p-2.5 bg-slate-50/80 border border-slate-200 rounded-xl"></div>
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
            <span class="text-xs text-slate-500">Midday In-Room Dining Service</span>
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
                <span class="text-[11px] text-slate-400">Actual room service orders</span>
            </div>
            <div class="max-w-xs">
                <label class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center gap-1.5">
                    <i class="ti ti-users text-slate-400"></i> Total Actual Cover
                </label>
                <div class="relative">
                    <input type="number" min="0" class="w-full pl-3.5 pr-12 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all"
                        name="session[lunch][cover_data][total_actual_cover]"
                        value="{{ old('session.lunch.cover_data.total_actual_cover', $lc->cover_data['total_actual_cover'] ?? '') }}"
                        placeholder="0">
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-medium text-slate-400 pointer-events-none">Pax</span>
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
                <span class="text-[11px] text-slate-400">Net departmental sales</span>
            </div>

            <input type="hidden" name="session[lunch][revenue_event]"
                value="{{ old('session.lunch.revenue_event', isset($lc->revenue_event) ? $lc->revenue_event : 0) }}">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Food Revenue</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-slate-400">Rp</span>
                        <input type="text" class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all rupiah"
                            name="session[lunch][revenue_food]"
                            value="{{ old('session.lunch.revenue_food', isset($lc->revenue_food) ? number_format($lc->revenue_food, 0, ',', '.') : '') }}"
                            placeholder="0" autocomplete="off">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Beverage Revenue</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-slate-400">Rp</span>
                        <input type="text" class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all rupiah"
                            name="session[lunch][revenue_beverage]"
                            value="{{ old('session.lunch.revenue_beverage', isset($lc->revenue_beverage) ? number_format($lc->revenue_beverage, 0, ',', '.') : '') }}"
                            placeholder="0" autocomplete="off">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Others Revenue</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-slate-400">Rp</span>
                        <input type="text" class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all rupiah"
                            name="session[lunch][revenue_others]"
                            value="{{ old('session.lunch.revenue_others', isset($lc->revenue_others) ? number_format($lc->revenue_others, 0, ',', '.') : '') }}"
                            placeholder="0" autocomplete="off">
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. UPSELLING & STAFF --}}
        <div>
            <div class="flex items-center justify-between border-b border-slate-100 pb-2 mb-4">
                <h6 class="text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#B89047]"></span>
                    3. Upselling & Staff
                </h6>
                <span class="text-[11px] text-slate-400">Items sold, VIP notes, & on-duty roster</span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-5">
                {{-- Food Upselling --}}
                <div class="bg-slate-50/70 p-4 rounded-xl border border-slate-200/80">
                    <label class="block text-xs font-bold text-slate-700 mb-2 flex items-center gap-1.5">
                        <i class="ti ti-meat text-amber-600"></i> Upselling Menu (Food)
                    </label>
                    @php $lcFoodVal = old('session.lunch.upselling_data.food', $lc->upselling_data['food'] ?? []); @endphp
                    <input type="hidden" id="input-lunch-food-IRD" name="session[lunch][upselling_data][food]"
                        value="{{ is_array($lcFoodVal) ? json_encode($lcFoodVal) : $lcFoodVal }}">

                    <div class="flex items-center gap-2 mb-2">
                        <input type="text" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all"
                            id="select-lunch-food-IRD" placeholder="Enter Food Name">
                        <input type="number" min="1" class="w-20 px-3 py-2 bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 text-center focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all shrink-0"
                            id="pax-lunch-food-IRD" placeholder="Qty">
                        <button class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold bg-[#172a40] text-white hover:bg-[#112031] transition-all shadow-xs shrink-0 cursor-pointer"
                            type="button" onclick="addUpsellingItem('lunch', 'food', 'IRD')">
                            <i class="ti ti-plus"></i> Add
                        </button>
                    </div>
                    <ul class="list-none space-y-1.5 min-h-[42px] max-h-48 overflow-y-auto" id="list-lunch-food-IRD"></ul>
                </div>

                {{-- Beverage Upselling --}}
                <div class="bg-slate-50/70 p-4 rounded-xl border border-slate-200/80">
                    <label class="block text-xs font-bold text-slate-700 mb-2 flex items-center gap-1.5">
                        <i class="ti ti-cup text-sky-600"></i> Beverage Upselling
                    </label>
                    @php $lcBevVal = old('session.lunch.upselling_data.beverage', $lc->upselling_data['beverage'] ?? []); @endphp
                    <input type="hidden" id="input-lunch-beverage-IRD" name="session[lunch][upselling_data][beverage]"
                        value="{{ is_array($lcBevVal) ? json_encode($lcBevVal) : $lcBevVal }}">

                    <div class="flex items-center gap-2 mb-2">
                        <input type="text" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all"
                            id="select-lunch-beverage-IRD" placeholder="Enter Beverage Name">
                        <input type="number" min="1" class="w-20 px-3 py-2 bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 text-center focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all shrink-0"
                            id="pax-lunch-beverage-IRD" placeholder="Qty">
                        <button class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold bg-[#172a40] text-white hover:bg-[#112031] transition-all shadow-xs shrink-0 cursor-pointer"
                            type="button" onclick="addUpsellingItem('lunch', 'beverage', 'IRD')">
                            <i class="ti ti-plus"></i> Add
                        </button>
                    </div>
                    <ul class="list-none space-y-1.5 min-h-[42px] max-h-48 overflow-y-auto" id="list-lunch-beverage-IRD"></ul>
                </div>
            </div>

            {{-- VIP Note / List --}}
            <div class="bg-slate-50/70 p-4 rounded-xl border border-slate-200/80 mb-5">
                <label class="block text-xs font-bold text-slate-700 mb-2 flex items-center gap-1.5">
                    <i class="ti ti-crown text-amber-500"></i> VIP Note / Guest Special Attention
                </label>
                @php $lcVipVal = old('session.lunch.vip_remarks', $lc->vip_remarks ?? []); @endphp
                <input type="hidden" id="input-vip-lunch-IRD" name="session[lunch][vip_remarks]"
                    value="{{ is_array($lcVipVal) ? json_encode($lcVipVal) : $lcVipVal }}">

                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 mb-2">
                    <input type="text" class="w-full sm:w-1/2 px-3 py-2 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all"
                        id="vip-name-lunch-IRD" placeholder="Guest Name / Room No">
                    <input type="text" class="w-full sm:w-1/2 px-3 py-2 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all"
                        id="vip-pos-lunch-IRD" placeholder="Note / Position / Preference">
                    <button class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold bg-[#172a40] text-white hover:bg-[#112031] transition-all shadow-xs shrink-0 cursor-pointer"
                        type="button" onclick="addVipItem('lunch', 'IRD')">
                        <i class="ti ti-plus"></i> Add VIP
                    </button>
                </div>
                <ul class="list-none space-y-1.5 min-h-[38px]" id="list-vip-lunch-IRD"></ul>
            </div>

            {{-- Remarks & Staff --}}
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">General Remarks</label>
                    <textarea class="w-full px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all resize-y min-h-[60px]"
                        rows="2" name="session[lunch][remarks]" placeholder="Enter lunch operational remarks...">{{ old('session.lunch.remarks', $lc->remarks ?? '') }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Staff on Duty</label>
                    @php $lcStaffVal = old('session.lunch.staff_on_duty', $lc->staff_on_duty ?? []); @endphp
                    <input type="hidden" id="input-staff-lunch-IRD" name="session[lunch][staff_on_duty]"
                        value="{{ is_array($lcStaffVal) ? json_encode($lcStaffVal) : $lcStaffVal }}">

                    <div class="flex items-center gap-2 max-w-md mb-2">
                        <select class="w-full px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all"
                            id="select-staff-lunch-IRD">
                            <option value="" selected>Select Staff...</option>
                            @foreach ($myStaffList as $staff)
                                <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                            @endforeach
                        </select>
                        <button class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold bg-[#172a40] text-white hover:bg-[#112031] transition-all shadow-xs shrink-0 cursor-pointer"
                            type="button" onclick="addStaffItem('lunch', 'IRD')">
                            <i class="ti ti-plus"></i> Add
                        </button>
                    </div>
                    <div id="list-staff-lunch-IRD" class="flex flex-wrap gap-2 min-h-[38px] p-2.5 bg-slate-50/80 border border-slate-200 rounded-xl"></div>
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
            <span class="text-xs text-slate-500">Evening In-Room Dining Service</span>
        </div>
        <span class="text-xs font-medium text-slate-400">18:00 - 22:30</span>
    </div>

    <div class="p-6 space-y-6">
        {{-- 1. COVER REPORT --}}
        <div>
            <div class="flex items-center justify-between border-b border-slate-100 pb-2 mb-4">
                <h6 class="text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#B89047]"></span>
                    1. Cover Report (Pax)
                </h6>
                <span class="text-[11px] text-slate-400">Actual room service orders</span>
            </div>
            <div class="max-w-xs">
                <label class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center gap-1.5">
                    <i class="ti ti-users text-slate-400"></i> Total Actual Cover
                </label>
                <div class="relative">
                    <input type="number" min="0" class="w-full pl-3.5 pr-12 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all"
                        name="session[dinner][cover_data][total_actual_cover]"
                        value="{{ old('session.dinner.cover_data.total_actual_cover', $dn->cover_data['total_actual_cover'] ?? '') }}"
                        placeholder="0">
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-medium text-slate-400 pointer-events-none">Pax</span>
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
                <span class="text-[11px] text-slate-400">Net departmental sales</span>
            </div>

            <input type="hidden" name="session[dinner][revenue_event]"
                value="{{ old('session.dinner.revenue_event', isset($dn->revenue_event) ? $dn->revenue_event : 0) }}">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Food Revenue</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-slate-400">Rp</span>
                        <input type="text" class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all rupiah"
                            name="session[dinner][revenue_food]"
                            value="{{ old('session.dinner.revenue_food', isset($dn->revenue_food) ? number_format($dn->revenue_food, 0, ',', '.') : '') }}"
                            placeholder="0" autocomplete="off">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Beverage Revenue</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-slate-400">Rp</span>
                        <input type="text" class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all rupiah"
                            name="session[dinner][revenue_beverage]"
                            value="{{ old('session.dinner.revenue_beverage', isset($dn->revenue_beverage) ? number_format($dn->revenue_beverage, 0, ',', '.') : '') }}"
                            placeholder="0" autocomplete="off">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Others Revenue</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-slate-400">Rp</span>
                        <input type="text" class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all rupiah"
                            name="session[dinner][revenue_others]"
                            value="{{ old('session.dinner.revenue_others', isset($dn->revenue_others) ? number_format($dn->revenue_others, 0, ',', '.') : '') }}"
                            placeholder="0" autocomplete="off">
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. UPSELLING & STAFF --}}
        <div>
            <div class="flex items-center justify-between border-b border-slate-100 pb-2 mb-4">
                <h6 class="text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#B89047]"></span>
                    3. Upselling & Staff
                </h6>
                <span class="text-[11px] text-slate-400">Items sold, VIP notes, & on-duty roster</span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-5">
                {{-- Food Upselling --}}
                <div class="bg-slate-50/70 p-4 rounded-xl border border-slate-200/80">
                    <label class="block text-xs font-bold text-slate-700 mb-2 flex items-center gap-1.5">
                        <i class="ti ti-meat text-amber-600"></i> Upselling Menu (Food)
                    </label>
                    @php $dnFoodVal = old('session.dinner.upselling_data.food', $dn->upselling_data['food'] ?? []); @endphp
                    <input type="hidden" id="input-dinner-food-IRD" name="session[dinner][upselling_data][food]"
                        value="{{ is_array($dnFoodVal) ? json_encode($dnFoodVal) : $dnFoodVal }}">

                    <div class="flex items-center gap-2 mb-2">
                        <input type="text" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all"
                            id="select-dinner-food-IRD" placeholder="Enter Food Name">
                        <input type="number" min="1" class="w-20 px-3 py-2 bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 text-center focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all shrink-0"
                            id="pax-dinner-food-IRD" placeholder="Qty">
                        <button class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold bg-[#172a40] text-white hover:bg-[#112031] transition-all shadow-xs shrink-0 cursor-pointer"
                            type="button" onclick="addUpsellingItem('dinner', 'food', 'IRD')">
                            <i class="ti ti-plus"></i> Add
                        </button>
                    </div>
                    <ul class="list-none space-y-1.5 min-h-[42px] max-h-48 overflow-y-auto" id="list-dinner-food-IRD"></ul>
                </div>

                {{-- Beverage Upselling --}}
                <div class="bg-slate-50/70 p-4 rounded-xl border border-slate-200/80">
                    <label class="block text-xs font-bold text-slate-700 mb-2 flex items-center gap-1.5">
                        <i class="ti ti-cup text-sky-600"></i> Beverage Upselling
                    </label>
                    @php $dnBevVal = old('session.dinner.upselling_data.beverage', $dn->upselling_data['beverage'] ?? []); @endphp
                    <input type="hidden" id="input-dinner-beverage-IRD" name="session[dinner][upselling_data][beverage]"
                        value="{{ is_array($dnBevVal) ? json_encode($dnBevVal) : $dnBevVal }}">

                    <div class="flex items-center gap-2 mb-2">
                        <input type="text" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all"
                            id="select-dinner-beverage-IRD" placeholder="Enter Beverage Name">
                        <input type="number" min="1" class="w-20 px-3 py-2 bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 text-center focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all shrink-0"
                            id="pax-dinner-beverage-IRD" placeholder="Qty">
                        <button class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold bg-[#172a40] text-white hover:bg-[#112031] transition-all shadow-xs shrink-0 cursor-pointer"
                            type="button" onclick="addUpsellingItem('dinner', 'beverage', 'IRD')">
                            <i class="ti ti-plus"></i> Add
                        </button>
                    </div>
                    <ul class="list-none space-y-1.5 min-h-[42px] max-h-48 overflow-y-auto" id="list-dinner-beverage-IRD"></ul>
                </div>
            </div>

            {{-- VIP Note / List --}}
            <div class="bg-slate-50/70 p-4 rounded-xl border border-slate-200/80 mb-5">
                <label class="block text-xs font-bold text-slate-700 mb-2 flex items-center gap-1.5">
                    <i class="ti ti-crown text-amber-500"></i> VIP Note / Guest Special Attention
                </label>
                @php $dnVipVal = old('session.dinner.vip_remarks', $dn->vip_remarks ?? []); @endphp
                <input type="hidden" id="input-vip-dinner-IRD" name="session[dinner][vip_remarks]"
                    value="{{ is_array($dnVipVal) ? json_encode($dnVipVal) : $dnVipVal }}">

                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 mb-2">
                    <input type="text" class="w-full sm:w-1/2 px-3 py-2 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all"
                        id="vip-name-dinner-IRD" placeholder="Guest Name / Room No">
                    <input type="text" class="w-full sm:w-1/2 px-3 py-2 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all"
                        id="vip-pos-dinner-IRD" placeholder="Note / Position / Preference">
                    <button class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold bg-[#172a40] text-white hover:bg-[#112031] transition-all shadow-xs shrink-0 cursor-pointer"
                        type="button" onclick="addVipItem('dinner', 'IRD')">
                        <i class="ti ti-plus"></i> Add VIP
                    </button>
                </div>
                <ul class="list-none space-y-1.5 min-h-[38px]" id="list-vip-dinner-IRD"></ul>
            </div>

            {{-- Remarks & Staff --}}
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">General Remarks</label>
                    <textarea class="w-full px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all resize-y min-h-[60px]"
                        rows="2" name="session[dinner][remarks]" placeholder="Enter dinner operational remarks...">{{ old('session.dinner.remarks', $dn->remarks ?? '') }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Staff on Duty</label>
                    @php $dnStaffVal = old('session.dinner.staff_on_duty', $dn->staff_on_duty ?? []); @endphp
                    <input type="hidden" id="input-staff-dinner-IRD" name="session[dinner][staff_on_duty]"
                        value="{{ is_array($dnStaffVal) ? json_encode($dnStaffVal) : $dnStaffVal }}">

                    <div class="flex items-center gap-2 max-w-md mb-2">
                        <select class="w-full px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all"
                            id="select-staff-dinner-IRD">
                            <option value="" selected>Select Staff...</option>
                            @foreach ($myStaffList as $staff)
                                <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                            @endforeach
                        </select>
                        <button class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold bg-[#172a40] text-white hover:bg-[#112031] transition-all shadow-xs shrink-0 cursor-pointer"
                            type="button" onclick="addStaffItem('dinner', 'IRD')">
                            <i class="ti ti-plus"></i> Add
                        </button>
                    </div>
                    <div id="list-staff-dinner-IRD" class="flex flex-wrap gap-2 min-h-[38px] p-2.5 bg-slate-50/80 border border-slate-200 rounded-xl"></div>
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
{{-- SESSION: SUPPER                                             --}}
{{-- ============================================================ --}}
<div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden mb-6 transition-all duration-200 hover:shadow-md">
    {{-- Header Sesi --}}
    <div class="bg-gradient-to-r from-purple-500/10 via-purple-500/5 to-transparent border-b border-purple-200/60 px-6 py-4 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-800 border border-purple-200 shadow-xs">
                <i class="ti ti-moon-stars text-purple-600"></i> Supper Session
            </span>
            <span class="text-xs text-slate-500">Late Night / Overnight Room Service</span>
        </div>
        <span class="text-xs font-medium text-slate-400">23:00 - 05:30</span>
    </div>

    <div class="p-6 space-y-6">
        {{-- 1. COVER REPORT --}}
        <div>
            <div class="flex items-center justify-between border-b border-slate-100 pb-2 mb-4">
                <h6 class="text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#B89047]"></span>
                    1. Cover Report (Pax)
                </h6>
                <span class="text-[11px] text-slate-400">Actual late night room orders</span>
            </div>
            <div class="max-w-xs">
                <label class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center gap-1.5">
                    <i class="ti ti-users text-slate-400"></i> Total Actual Cover
                </label>
                <div class="relative">
                    <input type="number" min="0" class="w-full pl-3.5 pr-12 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all"
                        name="session[supper][cover_data][total_actual_cover]"
                        value="{{ old('session.supper.cover_data.total_actual_cover', $sp->cover_data['total_actual_cover'] ?? '') }}"
                        placeholder="0">
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-medium text-slate-400 pointer-events-none">Pax</span>
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
                <span class="text-[11px] text-slate-400">Net departmental sales</span>
            </div>

            <input type="hidden" name="session[supper][revenue_event]"
                value="{{ old('session.supper.revenue_event', isset($sp->revenue_event) ? $sp->revenue_event : 0) }}">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Food Revenue</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-slate-400">Rp</span>
                        <input type="text" class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all rupiah"
                            name="session[supper][revenue_food]"
                            value="{{ old('session.supper.revenue_food', isset($sp->revenue_food) ? number_format($sp->revenue_food, 0, ',', '.') : '') }}"
                            placeholder="0" autocomplete="off">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Beverage Revenue</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-slate-400">Rp</span>
                        <input type="text" class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all rupiah"
                            name="session[supper][revenue_beverage]"
                            value="{{ old('session.supper.revenue_beverage', isset($sp->revenue_beverage) ? number_format($sp->revenue_beverage, 0, ',', '.') : '') }}"
                            placeholder="0" autocomplete="off">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Others Revenue</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-slate-400">Rp</span>
                        <input type="text" class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all rupiah"
                            name="session[supper][revenue_others]"
                            value="{{ old('session.supper.revenue_others', isset($sp->revenue_others) ? number_format($sp->revenue_others, 0, ',', '.') : '') }}"
                            placeholder="0" autocomplete="off">
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. UPSELLING & STAFF --}}
        <div>
            <div class="flex items-center justify-between border-b border-slate-100 pb-2 mb-4">
                <h6 class="text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#B89047]"></span>
                    3. Upselling & Staff
                </h6>
                <span class="text-[11px] text-slate-400">Items sold, VIP notes, & on-duty roster</span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-5">
                {{-- Food Upselling --}}
                <div class="bg-slate-50/70 p-4 rounded-xl border border-slate-200/80">
                    <label class="block text-xs font-bold text-slate-700 mb-2 flex items-center gap-1.5">
                        <i class="ti ti-meat text-amber-600"></i> Upselling Menu (Food)
                    </label>
                    @php $spFoodVal = old('session.supper.upselling_data.food', $sp->upselling_data['food'] ?? []); @endphp
                    <input type="hidden" id="input-supper-food-IRD" name="session[supper][upselling_data][food]"
                        value="{{ is_array($spFoodVal) ? json_encode($spFoodVal) : $spFoodVal }}">

                    <div class="flex items-center gap-2 mb-2">
                        <input type="text" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all"
                            id="select-supper-food-IRD" placeholder="Enter Food Name">
                        <input type="number" min="1" class="w-20 px-3 py-2 bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 text-center focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all shrink-0"
                            id="pax-supper-food-IRD" placeholder="Qty">
                        <button class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold bg-[#172a40] text-white hover:bg-[#112031] transition-all shadow-xs shrink-0 cursor-pointer"
                            type="button" onclick="addUpsellingItem('supper', 'food', 'IRD')">
                            <i class="ti ti-plus"></i> Add
                        </button>
                    </div>
                    <ul class="list-none space-y-1.5 min-h-[42px] max-h-48 overflow-y-auto" id="list-supper-food-IRD"></ul>
                </div>

                {{-- Beverage Upselling --}}
                <div class="bg-slate-50/70 p-4 rounded-xl border border-slate-200/80">
                    <label class="block text-xs font-bold text-slate-700 mb-2 flex items-center gap-1.5">
                        <i class="ti ti-cup text-sky-600"></i> Beverage Upselling
                    </label>
                    @php $spBevVal = old('session.supper.upselling_data.beverage', $sp->upselling_data['beverage'] ?? []); @endphp
                    <input type="hidden" id="input-supper-beverage-IRD" name="session[supper][upselling_data][beverage]"
                        value="{{ is_array($spBevVal) ? json_encode($spBevVal) : $spBevVal }}">

                    <div class="flex items-center gap-2 mb-2">
                        <input type="text" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all"
                            id="select-supper-beverage-IRD" placeholder="Enter Beverage Name">
                        <input type="number" min="1" class="w-20 px-3 py-2 bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 text-center focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all shrink-0"
                            id="pax-supper-beverage-IRD" placeholder="Qty">
                        <button class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold bg-[#172a40] text-white hover:bg-[#112031] transition-all shadow-xs shrink-0 cursor-pointer"
                            type="button" onclick="addUpsellingItem('supper', 'beverage', 'IRD')">
                            <i class="ti ti-plus"></i> Add
                        </button>
                    </div>
                    <ul class="list-none space-y-1.5 min-h-[42px] max-h-48 overflow-y-auto" id="list-supper-beverage-IRD"></ul>
                </div>
            </div>

            {{-- VIP Note / List --}}
            <div class="bg-slate-50/70 p-4 rounded-xl border border-slate-200/80 mb-5">
                <label class="block text-xs font-bold text-slate-700 mb-2 flex items-center gap-1.5">
                    <i class="ti ti-crown text-amber-500"></i> VIP Note / Guest Special Attention
                </label>
                @php $spVipVal = old('session.supper.vip_remarks', $sp->vip_remarks ?? []); @endphp
                <input type="hidden" id="input-vip-supper-IRD" name="session[supper][vip_remarks]"
                    value="{{ is_array($spVipVal) ? json_encode($spVipVal) : $spVipVal }}">

                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 mb-2">
                    <input type="text" class="w-full sm:w-1/2 px-3 py-2 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all"
                        id="vip-name-supper-IRD" placeholder="Guest Name / Room No">
                    <input type="text" class="w-full sm:w-1/2 px-3 py-2 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all"
                        id="vip-pos-supper-IRD" placeholder="Note / Position / Preference">
                    <button class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold bg-[#172a40] text-white hover:bg-[#112031] transition-all shadow-xs shrink-0 cursor-pointer"
                        type="button" onclick="addVipItem('supper', 'IRD')">
                        <i class="ti ti-plus"></i> Add VIP
                    </button>
                </div>
                <ul class="list-none space-y-1.5 min-h-[38px]" id="list-vip-supper-IRD"></ul>
            </div>

            {{-- Remarks & Staff --}}
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">General Remarks</label>
                    <textarea class="w-full px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all resize-y min-h-[60px]"
                        rows="2" name="session[supper][remarks]" placeholder="Enter supper operational remarks...">{{ old('session.supper.remarks', $sp->remarks ?? '') }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Staff on Duty</label>
                    @php $spStaffVal = old('session.supper.staff_on_duty', $sp->staff_on_duty ?? []); @endphp
                    <input type="hidden" id="input-staff-supper-IRD" name="session[supper][staff_on_duty]"
                        value="{{ is_array($spStaffVal) ? json_encode($spStaffVal) : $spStaffVal }}">

                    <div class="flex items-center gap-2 max-w-md mb-2">
                        <select class="w-full px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all"
                            id="select-staff-supper-IRD">
                            <option value="" selected>Select Staff...</option>
                            @foreach ($myStaffList as $staff)
                                <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                            @endforeach
                        </select>
                        <button class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold bg-[#172a40] text-white hover:bg-[#112031] transition-all shadow-xs shrink-0 cursor-pointer"
                            type="button" onclick="addStaffItem('supper', 'IRD')">
                            <i class="ti ti-plus"></i> Add
                        </button>
                    </div>
                    <div id="list-staff-supper-IRD" class="flex flex-wrap gap-2 min-h-[38px] p-2.5 bg-slate-50/80 border border-slate-200 rounded-xl"></div>
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
                        name="session[supper][competitor_data][shangrila_cover]"
                        value="{{ old('session.supper.competitor_data.shangrila_cover', $sp->competitor_data['shangrila_cover'] ?? '') }}"
                        placeholder="0">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">JW Marriott</label>
                    <input type="number" min="0" class="w-full px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all"
                        name="session[supper][competitor_data][jw_marriott_cover]"
                        value="{{ old('session.supper.competitor_data.jw_marriott_cover', $sp->competitor_data['jw_marriott_cover'] ?? '') }}"
                        placeholder="0">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Sheraton</label>
                    <input type="number" min="0" class="w-full px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all"
                        name="session[supper][competitor_data][sheraton_cover]"
                        value="{{ old('session.supper.competitor_data.sheraton_cover', $sp->competitor_data['sheraton_cover'] ?? '') }}"
                        placeholder="0">
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- SCRIPT INITIALIZATION                                       --}}
{{-- ============================================================ --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {

        // --- BREAKFAST INIT ---
        let bfFood = {!! json_encode(old('session.breakfast.upselling_data.food', $bf->upselling_data['food'] ?? [])) !!};
        if (typeof initUpselling === 'function') initUpselling('breakfast', 'food', bfFood, 'IRD');

        let bfBev = {!! json_encode(old('session.breakfast.upselling_data.beverage', $bf->upselling_data['beverage'] ?? [])) !!};
        if (typeof initUpselling === 'function') initUpselling('breakfast', 'beverage', bfBev, 'IRD');

        let bfVip = {!! json_encode(old('session.breakfast.vip_remarks', $bf->vip_remarks ?? [])) !!};
        if (typeof initVip === 'function') initVip('breakfast', bfVip, 'IRD');

        let bfStaff = {!! json_encode(old('session.breakfast.staff_on_duty', $bf->staff_on_duty ?? [])) !!};
        if (typeof initStaff === 'function') initStaff('breakfast', bfStaff, 'IRD');


        // --- LUNCH INIT ---
        let lcFood = {!! json_encode(old('session.lunch.upselling_data.food', $lc->upselling_data['food'] ?? [])) !!};
        if (typeof initUpselling === 'function') initUpselling('lunch', 'food', lcFood, 'IRD');

        let lcBev = {!! json_encode(old('session.lunch.upselling_data.beverage', $lc->upselling_data['beverage'] ?? [])) !!};
        if (typeof initUpselling === 'function') initUpselling('lunch', 'beverage', lcBev, 'IRD');

        let lcVip = {!! json_encode(old('session.lunch.vip_remarks', $lc->vip_remarks ?? [])) !!};
        if (typeof initVip === 'function') initVip('lunch', lcVip, 'IRD');

        let lcStaff = {!! json_encode(old('session.lunch.staff_on_duty', $lc->staff_on_duty ?? [])) !!};
        if (typeof initStaff === 'function') initStaff('lunch', lcStaff, 'IRD');


        // --- DINNER INIT ---
        let dnFood = {!! json_encode(old('session.dinner.upselling_data.food', $dn->upselling_data['food'] ?? [])) !!};
        if (typeof initUpselling === 'function') initUpselling('dinner', 'food', dnFood, 'IRD');

        let dnBev = {!! json_encode(old('session.dinner.upselling_data.beverage', $dn->upselling_data['beverage'] ?? [])) !!};
        if (typeof initUpselling === 'function') initUpselling('dinner', 'beverage', dnBev, 'IRD');

        let dnVip = {!! json_encode(old('session.dinner.vip_remarks', $dn->vip_remarks ?? [])) !!};
        if (typeof initVip === 'function') initVip('dinner', dnVip, 'IRD');

        let dnStaff = {!! json_encode(old('session.dinner.staff_on_duty', $dn->staff_on_duty ?? [])) !!};
        if (typeof initStaff === 'function') initStaff('dinner', dnStaff, 'IRD');


        // --- SUPPER INIT ---
        let spFood = {!! json_encode(old('session.supper.upselling_data.food', $sp->upselling_data['food'] ?? [])) !!};
        if (typeof initUpselling === 'function') initUpselling('supper', 'food', spFood, 'IRD');

        let spBev = {!! json_encode(old('session.supper.upselling_data.beverage', $sp->upselling_data['beverage'] ?? [])) !!};
        if (typeof initUpselling === 'function') initUpselling('supper', 'beverage', spBev, 'IRD');

        let spVip = {!! json_encode(old('session.supper.vip_remarks', $sp->vip_remarks ?? [])) !!};
        if (typeof initVip === 'function') initVip('supper', spVip, 'IRD');

        let spStaff = {!! json_encode(old('session.supper.staff_on_duty', $sp->staff_on_duty ?? [])) !!};
        if (typeof initStaff === 'function') initStaff('supper', spStaff, 'IRD');

    });
</script>
