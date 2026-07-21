                        {{-- 1. REVENUE TABLE --}}
                        <h6 class="text-muted text-uppercase small fw-bold">Revenue Summary</h6>
                        <div class="table-responsive mb-3">
                            <table class="table table-sm table-bordered mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Food</th>
                                        <th>Beverage</th>
                                        <th>Others</th>
                                        <th>Event</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Rp {{ number_format($data->revenue_food) }}</td>
                                        <td>Rp {{ number_format($data->revenue_beverage) }}</td>
                                        <td>Rp {{ number_format($data->revenue_others) }}</td>
                                        <td>Rp {{ number_format($data->revenue_event) }}</td>
                                        <td class="text-end fw-bold table-active">
                                            Rp
                                            {{ number_format($data->revenue_food + $data->revenue_beverage + $data->revenue_others + $data->revenue_event) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        {{-- 2. COVER DATA (JSON DUMP YANG RAPI) --}}
                        <h6 class="text-muted text-uppercase small fw-bold mt-4">Cover Report Details</h6>
                        @if (!empty($data->cover_data))
                            <div class="row g-2">
                                @foreach ($data->cover_data as $key => $value)
                                    <div class="col-md-4 col-6">
                                        <div class="p-2 border rounded bg-light">
                                            <small class="d-block text-muted text-uppercase" style="font-size: 10px;">
                                                {{ str_replace('_', ' ', $key) }}
                                            </small>
                                            <span class="fw-bold">{{ $value }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted small">No cover data recorded.</p>
                        @endif

                        {{-- OCCASION SECTION --}}
                        @php
                            $occasionItems = $data->additional_data['occasion_items'] ?? [];
                            if (is_string($occasionItems)) {
                                $occasionItems = json_decode($occasionItems, true) ?? [];
                            }
                        @endphp
                        @if (!empty($occasionItems) && is_array($occasionItems) && count($occasionItems) > 0)
                            <h6 class="text-muted text-uppercase small fw-bold mt-4">Occasion</h6>
                            <div class="table-responsive mb-3">
                                <table class="table table-sm table-bordered mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Type</th>
                                            <th>Name</th>
                                            <th class="text-end">Pax</th>
                                            <th class="text-end">Revenue</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($occasionItems as $item)
                                            <tr>
                                                <td>
                                                    @php
                                                        $badgeClass = match($item['type'] ?? '') {
                                                            'Wedding Party' => 'bg-warning text-dark',
                                                            'Birthday Party' => 'bg-info text-dark',
                                                            'Social Event' => 'bg-primary',
                                                            default => 'bg-secondary'
                                                        };
                                                    @endphp
                                                    <span class="badge {{ $badgeClass }}">{{ $item['type'] ?? '-' }}</span>
                                                </td>
                                                <td class="fw-bold">{{ $item['name'] ?? '-' }}</td>
                                                <td class="text-end">{{ number_format($item['pax'] ?? 0) }}</td>
                                                <td class="text-end">Rp {{ number_format($item['revenue'] ?? 0, 0, ',', '.') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif

                        {{-- PROMO SECTION --}}
                        @php
                            $promoItems = $data->additional_data['promo_items'] ?? [];
                            if (is_string($promoItems)) $promoItems = json_decode($promoItems, true) ?? [];
                            $hasNewPromo = !empty($promoItems) && is_array($promoItems);
                            $hasOldPromo = !empty($data->additional_data) && (
                                isset($data->additional_data['mandiri_card']) ||
                                isset($data->additional_data['bca_card']) ||
                                isset($data->additional_data['membership']) ||
                                isset($data->additional_data['others_promo'])
                            );
                        @endphp
                        @if ($hasNewPromo || $hasOldPromo)
                            <h6 class="text-muted text-uppercase small fw-bold mt-4">Promo</h6>
                            @if ($hasNewPromo)
                                <div class="table-responsive mb-3">
                                    <table class="table table-sm table-bordered mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Type</th>
                                                <th class="text-end">Pax</th>
                                                <th class="text-end">Revenue</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($promoItems as $item)
                                                <tr>
                                                    <td><span class="badge bg-success">{{ $item['type'] ?? '-' }}</span></td>
                                                    <td class="text-end">{{ number_format($item['pax'] ?? 0) }}</td>
                                                    <td class="text-end">Rp {{ number_format($item['revenue'] ?? 0, 0, ',', '.') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                            @if ($hasOldPromo)
                                <div class="row g-2">
                                    @if (isset($data->additional_data['mandiri_card']))
                                        <div class="col-md-4 col-6">
                                            <div class="p-2 border rounded bg-light">
                                                <small class="d-block text-muted text-uppercase" style="font-size: 10px;">
                                                    <i class="ti ti-credit-card me-1"></i> Mandiri Card
                                                </small>
                                                <span class="fw-bold">{{ $data->additional_data['mandiri_card'] }} pax</span>
                                            </div>
                                        </div>
                                    @endif
                                    @if (isset($data->additional_data['bca_card']))
                                        <div class="col-md-4 col-6">
                                            <div class="p-2 border rounded bg-light">
                                                <small class="d-block text-muted text-uppercase" style="font-size: 10px;">
                                                    <i class="ti ti-credit-card me-1"></i> BCA Card
                                                </small>
                                                <span class="fw-bold">{{ $data->additional_data['bca_card'] }} pax</span>
                                            </div>
                                        </div>
                                    @endif
                                    @if (isset($data->additional_data['membership']))
                                        <div class="col-md-4 col-6">
                                            <div class="p-2 border rounded bg-light">
                                                <small class="d-block text-muted text-uppercase" style="font-size: 10px;">
                                                    <i class="ti ti-id-badge me-1"></i> Membership
                                                </small>
                                                <span class="fw-bold">{{ $data->additional_data['membership'] }} pax</span>
                                            </div>
                                        </div>
                                    @endif
                                    @php
                                        $promoOthers = $data->additional_data['others_promo'] ?? [];
                                        if (is_string($promoOthers)) $promoOthers = json_decode($promoOthers, true) ?? [];
                                    @endphp
                                    @if (!empty($promoOthers) && is_array($promoOthers))
                                        @foreach ($promoOthers as $item)
                                            <div class="col-md-4 col-6">
                                                <div class="p-2 border rounded bg-light border-success">
                                                    <small class="d-block text-muted text-uppercase" style="font-size: 10px;">
                                                        <i class="ti ti-plus me-1"></i> {{ $item['name'] ?? 'Other' }}
                                                    </small>
                                                    <span class="fw-bold">{{ $item['qty'] ?? 0 }} pax</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            @endif
                        @endif

                        {{-- SET MENU SECTION --}}
                        @php
                            $setMenuItems = $data->additional_data['setmenu_items'] ?? [];
                            if (is_string($setMenuItems)) {
                                $setMenuItems = json_decode($setMenuItems, true) ?? [];
                            }

                            $setMenuFields = [
                                'set_menu_family_8000' => 'Family 8000',
                                'set_menu_family_5000' => 'Family 5000',
                                'set_menu_family_6000' => 'Family 6000',
                                'set_menu_ayce_dimsum' => 'AYCE Dimsum',
                                'set_menu_788' => 'Set Menu 788',
                                'set_menu_988' => 'Set Menu 988',
                                'set_menu_1188' => 'Set Menu 1188',
                            ];
                            $hasOldSetMenu = false;
                            foreach (array_keys($setMenuFields) as $key) {
                                if (!empty($data->additional_data[$key])) {
                                    $hasOldSetMenu = true;
                                    break;
                                }
                            }
                            $hasNewSetMenu = !empty($setMenuItems) && is_array($setMenuItems) && count($setMenuItems) > 0;
                        @endphp
                        @if ($hasNewSetMenu || $hasOldSetMenu)
                            <h6 class="text-muted text-uppercase small fw-bold mt-4">Set Menu</h6>
                            @if ($hasNewSetMenu)
                                <div class="table-responsive mb-3">
                                    <table class="table table-sm table-bordered mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Type</th>
                                                <th class="text-end">Pax</th>
                                                <th class="text-end">Revenue</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($setMenuItems as $item)
                                                <tr>
                                                    <td><span class="badge bg-secondary">{{ $item['type'] ?? '-' }}</span></td>
                                                    <td class="text-end">{{ number_format($item['pax'] ?? 0) }}</td>
                                                    <td class="text-end">Rp {{ number_format($item['revenue'] ?? 0, 0, ',', '.') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                            @if ($hasOldSetMenu)
                                <div class="row g-2">
                                    @foreach ($setMenuFields as $key => $label)
                                        @if (!empty($data->additional_data[$key]))
                                            <div class="col-md-4 col-6">
                                                <div class="p-2 border rounded bg-light">
                                                    <small class="d-block text-muted text-uppercase" style="font-size: 10px;">
                                                        {{ $label }}
                                                    </small>
                                                    <span class="fw-bold">{{ $data->additional_data[$key] }} pax</span>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        @endif

                        {{-- ALA CARTE SECTION --}}
                        @php
                            $alaCarteFields = [
                                'ala_carte_steamed_pork' => 'Steamed PORK Soy Mushroom',
                                'ala_carte_king_prawn' => 'KG PRWN HNY SOYA SC',
                                'ala_carte_chicken_feet' => 'STMD CHCKN FEET EGGPLNT',
                                'ala_carte_aspa_soup' => 'ASPA SOUP FRESH CRB',
                                'ala_carte_pork_ribs' => 'STMD PORK RIBS BLCK BEAN',
                                'ala_carte_fish_lip_soup' => 'FISH LIP SOUP CMB',
                                'ala_carte_gurami' => 'GURAMI ALBINO',
                                'ala_carte_fried_glut_rice' => 'FRD GLUT RICE BALL BLCK',
                                'ala_carte_ikan_dewa' => 'IKAN DEWA / EMPUROU FISH',
                                'ala_carte_beef_tender_m' => 'BEEF TENDER MIX CAPS (M)',
                                'ala_carte_beef_tender_s' => 'BEEF TENDER MIX CAPS (S)',
                                'ala_carte_soup_ayam' => 'Soup Ayam Ginseng',
                                'ala_carte_grmi_deep_fried' => 'Grmi Deep Fried',
                                'ala_carte_lobster' => 'Lobster Bkd Cheese',
                            ];
                            $hasAlaCarte = false;
                            foreach (array_keys($alaCarteFields) as $key) {
                                if (!empty($data->additional_data[$key])) {
                                    $hasAlaCarte = true;
                                    break;
                                }
                            }
                        @endphp
                        @if ($hasAlaCarte)
                            <h6 class="text-muted text-uppercase small fw-bold mt-4">Ala Carte</h6>
                            <div class="row g-2">
                                @foreach ($alaCarteFields as $key => $label)
                                    @if (!empty($data->additional_data[$key]))
                                        <div class="col-md-4 col-6">
                                            <div class="p-2 border rounded bg-light">
                                                <small class="d-block text-muted text-uppercase" style="font-size: 10px;">
                                                    {{ $label }}
                                                </small>
                                                <span class="fw-bold">{{ $data->additional_data[$key] }} pax</span>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif

                        <hr class="my-4">

                        {{-- A. UPSELLING SECTION --}}
                        <h6 class="text-muted text-uppercase small fw-bold mb-3">
                            <i class="ti ti-trending-up me-1"></i> Upselling Performance
                        </h6>

                        <div class="row">
                            {{-- Food Upselling --}}
                            <div class="col-md-6">
                                <div class="card bg-light-primary border-0">
                                    <div class="card-body p-3">
                                        <h6 class="card-title text-primary mb-2"><i class="ti ti-tools-kitchen-2"></i>
                                            Food
                                            Items</h6>
                                        {{-- LOGIKA FIX: Decode dulu jika datanya String --}}
                                        @php
                                            $foodItems = $data->upselling_data['food'] ?? [];
                                            if (is_string($foodItems)) {
                                                $foodItems = json_decode($foodItems, true) ?? [];
                                            }
                                        @endphp

                                        @if (!empty($foodItems) && is_array($foodItems) && count($foodItems) > 0)
                                            <div class="table-responsive">
                                                <table class="table table-sm table-borderless mb-0 small">
                                                    <thead class="text-muted">
                                                        <tr>
                                                            <th>Menu Name</th>
                                                            <th class="text-end">Qty</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($foodItems as $item)
                                                            <tr class="border-bottom border-light">
                                                                <td class="fw-bold text-dark">{{ $item['name'] ?? '-' }}
                                                                </td>
                                                                <td class="text-end">
                                                                    <span
                                                                        class="badge bg-white text-primary border border-primary">
                                                                        {{ $item['pax'] ?? 0 }}
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <span class="small text-muted fst-italic">- No food upselling -</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Beverage Upselling --}}
                            <div class="col-md-6">
                                <div class="card bg-light-success border-0">
                                    <div class="card-body p-3">
                                        <h6 class="card-title text-success mb-2"><i class="ti ti-glass-full"></i> Beverage
                                            Items</h6>
                                        @php
                                            $bevItems = $data->upselling_data['beverage'] ?? [];
                                            if (is_string($bevItems)) {
                                                $bevItems = json_decode($bevItems, true) ?? [];
                                            }
                                        @endphp

                                        @if (!empty($bevItems) && is_array($bevItems) && count($bevItems) > 0)
                                            <div class="table-responsive">
                                                <table class="table table-sm table-borderless mb-0 small">
                                                    <thead class="text-muted">
                                                        <tr>
                                                            <th>Menu Name</th>
                                                            <th class="text-end">Qty</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($bevItems as $item)
                                                            <tr class="border-bottom border-light">
                                                                <td class="fw-bold text-dark">{{ $item['name'] ?? '-' }}
                                                                </td>
                                                                <td class="text-end">
                                                                    <span
                                                                        class="badge bg-white text-success border border-success">
                                                                        {{ $item['pax'] ?? 0 }}
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <span class="small text-muted fst-italic">- No beverage upselling -</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- B. STAFF --}}
                        <div class="row mt-4">

                            {{-- Staff On Duty (Badges) --}}
                            <div class="col-md-12">
                                <h6 class="text-muted text-uppercase small fw-bold mb-2">
                                    <i class="ti ti-users me-1"></i> Staff On Duty
                                </h6>
                                <div class="d-flex flex-wrap gap-2">
                                    @php
                                        $staffList = $data->staff_on_duty;
                                        if (is_string($staffList)) {
                                            $staffList = json_decode($staffList, true) ?? [];
                                        }
                                    @endphp
                                    @if (!empty($staffList) && is_array($staffList))
                                        @foreach ($staffList as $staff)
                                            <span class="badge bg-light-secondary text-dark border">
                                                <i class="ti ti-user me-1"></i> {{ $staff }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="small text-muted">- No staff recorded -</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- NAGANO REVENUE (Teppan, Yakiniku, Ala Carte) --}}
                        @php
                            $teppanItems = $data->additional_data['revenue_teppan_items'] ?? [];
                            if (is_string($teppanItems)) $teppanItems = json_decode($teppanItems, true) ?? [];
                            $revYakiniku = $data->additional_data['revenue_yakiniku'] ?? 0;
                            $revAlaCarte = $data->additional_data['revenue_ala_carte'] ?? 0;
                            $hasNaganoRevenue = (!empty($teppanItems) && is_array($teppanItems)) || $revYakiniku > 0 || $revAlaCarte > 0;
                        @endphp
                        @if ($hasNaganoRevenue)
                            <h6 class="text-muted text-uppercase small fw-bold mt-4">Revenue Breakdown (Nagano)</h6>
                            <div class="table-responsive mb-3">
                                <table class="table table-sm table-bordered mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Type</th>
                                            <th class="text-end">Revenue</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (!empty($teppanItems) && is_array($teppanItems))
                                            @foreach ($teppanItems as $item)
                                                <tr>
                                                    <td>Teppan ({{ $item['floor'] ?? '-' }})</td>
                                                    <td class="text-end">Rp {{ number_format($item['revenue'] ?? 0, 0, ',', '.') }}</td>
                                                </tr>
                                            @endforeach
                                        @endif
                                        @if ($revYakiniku > 0)
                                            <tr>
                                                <td>Yakiniku</td>
                                                <td class="text-end">Rp {{ number_format($revYakiniku, 0, ',', '.') }}</td>
                                            </tr>
                                        @endif
                                        @if ($revAlaCarte > 0)
                                            <tr>
                                                <td>Ala Carte</td>
                                                <td class="text-end">Rp {{ number_format($revAlaCarte, 0, ',', '.') }}</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        @endif

                        {{-- C. COMPETITOR & REMARKS --}}
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card border">
                                    <div class="card-header bg-light py-2">
                                        <h6 class="mb-0 small fw-bold">Competitor & General Remarks</h6>
                                    </div>
                                    <div class="card-body p-3">
                                        <div class="row align-items-start">
                                            {{-- Competitor --}}
                                            <div class="mb-4">
                                                <small class="text-muted d-block mb-2">Competitor Cover Comparison</small>

                                                <div
                                                    class="d-flex gap-4 text-center align-items-center overflow-auto pb-2">

                                                    {{-- A. LOGIKA: Hitung Total Cover Kita --}}
                                                    @php
                                                        $myTotalCover = 0;
                                                        if (
                                                            !empty($data->cover_data) &&
                                                            is_array($data->cover_data)
                                                        ) {
                                                            foreach ($data->cover_data as $val) {
                                                                if (is_numeric($val)) {
                                                                    $myTotalCover += $val;
                                                                }
                                                            }
                                                        }
                                                    @endphp

                                                    {{-- B. TAMPILAN: Restoran Kita (Warna Biru/Primary) --}}
                                                    <div class="flex-shrink-0">
                                                        <h4 class="mb-0 fw-bold text-primary">{{ $myTotalCover }}</h4>
                                                        <span class="text-primary fw-bold"
                                                            style="font-size: 10px; text-transform: uppercase;">
                                                            {{ $dailyReport->restaurant->name }}
                                                        </span>
                                                    </div>

                                                    {{-- Garis Pemisah Vertikal --}}
                                                    <div class="border-end h-100 mx-2" style="min-height: 30px;"></div>

                                                    {{-- C. TAMPILAN: Kompetitor (Looping) --}}
                                                    @if (!empty($data->competitor_data))
                                                        @foreach ($data->competitor_data as $key => $val)
                                                            <div class="flex-shrink-0">
                                                                <h4 class="mb-0 fw-bold">{{ $val }}</h4>
                                                                <span class="text-muted"
                                                                    style="font-size: 10px; text-transform: uppercase;">
                                                                    {{ str_replace(['_cover', 'cover'], '', $key) }}
                                                                </span>
                                                            </div>
                                                        @endforeach
                                                    @else
                                                        <span class="small text-muted fst-italic">- No data -</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <hr class="border-light my-0 mb-3">

                                            {{-- General Remarks --}}
                                            <div>
                                                <small class="text-muted d-block mb-1">General Notes</small>
                                                <p class="mb-0 small text-dark">
                                                    {{ $data->remarks ?? '-' }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

