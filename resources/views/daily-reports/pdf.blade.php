<!DOCTYPE html>
<html>

<head>
    <title>Daily Report PDF</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #444;
            padding-bottom: 10px;
        }

        .header h1 {
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
        }

        .header p {
            margin: 2px 0;
            font-size: 10px;
            color: #666;
        }

        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }

        .info-table td {
            padding: 3px;
        }

        .font-bold {
            font-weight: bold;
        }

        .section-title {
            background-color: #eee;
            padding: 5px;
            font-weight: bold;
            font-size: 11px;
            margin-top: 15px;
            border-top: 1px solid #ccc;
            border-bottom: 1px solid #ccc;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #ddd;
            padding: 5px;
            text-align: left;
        }

        .data-table th {
            background-color: #f9f9f9;
        }

        .text-end {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .badges {
            margin-top: 2px;
        }

        .badge {
            display: inline-block;
            padding: 2px 5px;
            background: #f0f0f0;
            border: 1px solid #ccc;
            border-radius: 3px;
            font-size: 9px;
            margin-right: 2px;
        }

        .footer {
            margin-top: 40px;
            text-align: right;
            font-size: 9px;
            color: #888;
        }

        .signature-box {
            margin-top: 30px;
            width: 100%;
        }

        .sig-col {
            width: 33%;
            float: left;
            text-align: center;
        }

        .sig-line {
            margin-top: 50px;
            border-top: 1px solid #333;
            width: 80%;
            margin-left: auto;
            margin-right: auto;
        }
    </style>
</head>

<body>

    {{-- KOP SURAT --}}
    <div class="header">
        {{-- Ganti dengan path logo Anda yang benar (harus path absolut public_path) --}}
        <img src="{{ public_path('images/VasaHotel.png') }}" height="40">
        <h1>{{ $dailyReport->restaurant->name }}</h1>
        <p>Daily Operations Report</p>
    </div>

    {{-- INFO UMUM --}}
    <table class="info-table">
        <tr>
            <td width="15%" class="font-bold">Date</td>
            <td width="35%">: {{ $dailyReport->date->format('d F Y') }}</td>
            <td width="15%" class="font-bold">Status</td>
            <td width="35%">: <span style="color: green; font-weight:bold">APPROVED</span></td>
        </tr>
        <tr>
            <td class="font-bold">Created By</td>
            <td>: {{ $dailyReport->user->name }} ({{ $dailyReport->user->nik }})</td>
            <td class="font-bold">Approved By</td>
            <td>: {{ $dailyReport->approver->name ?? '-' }}</td>
        </tr>
    </table>

    {{-- LOOPING SESI (Breakfast, Lunch, Dinner) --}}
    @foreach ($dailyReport->details as $detail)
        <div class="section-title">
            {{ strtoupper($detail->session_type) }} SESSION
            @if ($detail->thematic)
                <span style="font-weight: normal">| Theme: {{ $detail->thematic }}</span>
            @endif
        </div>

        {{-- 1. REVENUE SUMMARY --}}
        <table class="data-table">
            <thead>
                <tr>
                    <th>Revenue Category</th>
                    <th class="text-end">Amount (IDR)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Food Revenue</td>
                    <td class="text-end">{{ number_format($detail->revenue_food, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Beverage Revenue</td>
                    <td class="text-end">{{ number_format($detail->revenue_beverage, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Others Revenue</td>
                    <td class="text-end">{{ number_format($detail->revenue_others, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Event Revenue</td>
                    <td class="text-end">{{ number_format($detail->revenue_event, 0, ',', '.') }}</td>
                </tr>
                <tr style="background-color: #f0f0f0; font-weight:bold;">
                    <td>TOTAL REVENUE</td>
                    <td class="text-end">
                        {{ number_format($detail->revenue_food + $detail->revenue_beverage + $detail->revenue_others + $detail->revenue_event, 0, ',', '.') }}
                    </td>
                </tr>
            </tbody>
        </table>

        {{-- 2. COVER & COMPETITOR (Grid Layout sederhana dgn Table) --}}
        <table style="width: 100%; margin-top: 10px;">
            <tr>
                {{-- Kolom Kiri: Cover Data --}}
                <td width="50%" valign="top">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th colspan="2">Cover Statistics</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($detail->cover_data)
                                @foreach ($detail->cover_data as $k => $v)
                                    <tr>
                                        <td>{{ ucwords(str_replace('_', ' ', $k)) }}</td>
                                        <td class="text-end font-bold">{{ $v }}</td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </td>
                {{-- Kolom Kanan: Competitor --}}
                <td width="50%" valign="top">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th colspan="2">Competitor Comparison</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($detail->competitor_data)
                                @foreach ($detail->competitor_data as $k => $v)
                                    <tr>
                                        <td>{{ ucwords(str_replace(['_cover', 'cover'], '', $k)) }}</td>
                                        <td class="text-end font-bold">{{ $v }}</td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>

        {{-- 3. ADDITIONAL DATA (Occasion, Promo, Set Menu) --}}
        @php
            // OCCASION
            $occasionItems = $detail->additional_data['occasion_items'] ?? [];
            if (is_string($occasionItems)) $occasionItems = json_decode($occasionItems, true) ?? [];
        @endphp
        @if (!empty($occasionItems) && is_array($occasionItems) && count($occasionItems) > 0)
            <div style="font-weight: bold; font-size: 11px; margin-top: 15px;">Occasion</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Name</th>
                        <th class="text-end" width="15%">Pax</th>
                        <th class="text-end" width="25%">Revenue (IDR)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($occasionItems as $item)
                        <tr>
                            <td>{{ $item['type'] ?? '-' }}</td>
                            <td>{{ $item['name'] ?? '-' }}</td>
                            <td class="text-end">{{ number_format($item['pax'] ?? 0) }}</td>
                            <td class="text-end">{{ number_format($item['revenue'] ?? 0, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        @php
            // PROMO
            $promoItems = $detail->additional_data['promo_items'] ?? [];
            if (is_string($promoItems)) $promoItems = json_decode($promoItems, true) ?? [];
            
            $allPromos = [];
            foreach ($promoItems as $item) {
                $allPromos[] = [
                    'type' => $item['type'] ?? '-',
                    'name' => '-',
                    'pax' => $item['pax'] ?? 0,
                    'revenue' => $item['revenue'] ?? 0
                ];
            }
            if (isset($detail->additional_data['mandiri_card'])) $allPromos[] = ['type' => 'Mandiri Card', 'name' => '-', 'pax' => $detail->additional_data['mandiri_card'], 'revenue' => 0];
            if (isset($detail->additional_data['bca_card'])) $allPromos[] = ['type' => 'BCA Card', 'name' => '-', 'pax' => $detail->additional_data['bca_card'], 'revenue' => 0];
            if (isset($detail->additional_data['membership'])) $allPromos[] = ['type' => 'Membership', 'name' => '-', 'pax' => $detail->additional_data['membership'], 'revenue' => 0];
            
            $promoOthers = $detail->additional_data['others_promo'] ?? [];
            if (is_string($promoOthers)) $promoOthers = json_decode($promoOthers, true) ?? [];
            if (!empty($promoOthers) && is_array($promoOthers)) {
                foreach ($promoOthers as $item) {
                    $allPromos[] = ['type' => 'Other', 'name' => $item['name'] ?? 'Other', 'pax' => $item['qty'] ?? 0, 'revenue' => 0];
                }
            }
        @endphp
        @if (count($allPromos) > 0)
            <div style="font-weight: bold; font-size: 11px; margin-top: 15px;">Promo</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Name</th>
                        <th class="text-end" width="15%">Pax</th>
                        <th class="text-end" width="25%">Revenue (IDR)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($allPromos as $item)
                        <tr>
                            <td>{{ $item['type'] }}</td>
                            <td>{{ $item['name'] }}</td>
                            <td class="text-end">{{ number_format($item['pax']) }}</td>
                            <td class="text-end">{{ number_format($item['revenue'], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        @php
            // SET MENU
            $setMenuItems = $detail->additional_data['setmenu_items'] ?? [];
            if (is_string($setMenuItems)) $setMenuItems = json_decode($setMenuItems, true) ?? [];
            
            $allSetMenus = [];
            foreach ($setMenuItems as $item) {
                $allSetMenus[] = [
                    'type' => $item['type'] ?? '-',
                    'pax' => $item['pax'] ?? 0,
                    'revenue' => $item['revenue'] ?? 0
                ];
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
            foreach ($setMenuFields as $key => $label) {
                if (!empty($detail->additional_data[$key])) {
                    $allSetMenus[] = ['type' => $label, 'pax' => $detail->additional_data[$key], 'revenue' => 0];
                }
            }
        @endphp
        @if (count($allSetMenus) > 0)
            <div style="font-weight: bold; font-size: 11px; margin-top: 15px;">Set Menu</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th class="text-end" width="15%">Pax</th>
                        <th class="text-end" width="25%">Revenue (IDR)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($allSetMenus as $item)
                        <tr>
                            <td>{{ $item['type'] }}</td>
                            <td class="text-end">{{ number_format($item['pax']) }}</td>
                            <td class="text-end">{{ number_format($item['revenue'], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        @php
            // NAGANO REVENUE BREAKDOWN
            $teppanItems = $detail->additional_data['revenue_teppan_items'] ?? [];
            if (is_string($teppanItems)) $teppanItems = json_decode($teppanItems, true) ?? [];
            $revYakiniku = floatval(str_replace('.', '', $detail->additional_data['revenue_yakiniku'] ?? '0'));
            $revAlaCarte = floatval(str_replace('.', '', $detail->additional_data['revenue_ala_carte'] ?? '0'));
            $hasNaganoRevenue = (!empty($teppanItems) && is_array($teppanItems)) || $revYakiniku > 0 || $revAlaCarte > 0;
        @endphp
        @if ($hasNaganoRevenue)
            <div style="font-weight: bold; font-size: 11px; margin-top: 15px;">Revenue Breakdown (Nagano)</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th class="text-end">Revenue (IDR)</th>
                    </tr>
                </thead>
                <tbody>
                    @if (!empty($teppanItems) && is_array($teppanItems))
                        @foreach ($teppanItems as $item)
                            <tr>
                                <td>Teppan ({{ $item['floor'] ?? '-' }})</td>
                                <td class="text-end">{{ number_format(floatval(str_replace('.', '', $item['revenue'] ?? '0')), 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    @endif
                    @if ($revYakiniku > 0)
                        <tr>
                            <td>Yakiniku</td>
                            <td class="text-end">{{ number_format($revYakiniku, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                    @if ($revAlaCarte > 0)
                        <tr>
                            <td>Ala Carte</td>
                            <td class="text-end">{{ number_format($revAlaCarte, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        @endif

        {{-- 4. UPSELLING PERFORMANCE --}}
        @php
            $foodUpselling = $detail->upselling_data['food'] ?? [];
            if (is_string($foodUpselling)) $foodUpselling = json_decode($foodUpselling, true) ?? [];
            
            $bevUpselling = $detail->upselling_data['beverage'] ?? [];
            if (is_string($bevUpselling)) $bevUpselling = json_decode($bevUpselling, true) ?? [];
        @endphp
        @if (count($foodUpselling) > 0 || count($bevUpselling) > 0)
            <div style="font-weight: bold; font-size: 11px; margin-top: 15px;">Upselling Performance</div>
            <table style="width: 100%; margin-top: 5px;">
                <tr>
                    <td width="50%" valign="top">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th colspan="2">Food Items</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (count($foodUpselling) > 0)
                                    @foreach ($foodUpselling as $item)
                                        <tr>
                                            <td>{{ $item['name'] ?? '-' }}</td>
                                            <td class="text-end" width="15%">{{ $item['pax'] ?? 0 }}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr><td colspan="2" class="text-center text-muted">No food upselling</td></tr>
                                @endif
                            </tbody>
                        </table>
                    </td>
                    <td width="5%" valign="top"></td> {{-- Spacer --}}
                    <td width="45%" valign="top">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th colspan="2">Beverage Items</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (count($bevUpselling) > 0)
                                    @foreach ($bevUpselling as $item)
                                        <tr>
                                            <td>{{ $item['name'] ?? '-' }}</td>
                                            <td class="text-end" width="15%">{{ $item['pax'] ?? 0 }}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr><td colspan="2" class="text-center text-muted">No beverage upselling</td></tr>
                                @endif
                            </tbody>
                        </table>
                    </td>
                </tr>
            </table>
        @endif

        {{-- 5. REMARKS & STAFF --}}
        <div style="margin-top: 10px; font-size: 11px;">
            <strong>General Remarks:</strong> {{ $detail->remarks ?? '-' }} <br>
            <strong>Staff On Duty:</strong>
            @php
                $staff = $detail->staff_on_duty;
                if (is_string($staff)) {
                    $staff = json_decode($staff, true);
                }
            @endphp
            @if (is_array($staff))
                {{ implode(', ', $staff) }}
            @else
                -
            @endif
        </div>
    @endforeach

    {{-- FOOTER / TANDA TANGAN --}}
    <div class="signature-box">
        <div class="sig-col">
            <br><br>
            <div class="sig-line">{{ $dailyReport->user->name }}</div>
            Created By
        </div>
        <div class="sig-col">
            <br><br>
            <div class="sig-line">Manager On Duty</div>
            Checked By
        </div>
        <div class="sig-col">
            <br><br>
            <div class="sig-line">{{ $dailyReport->approver->name ?? 'Manager' }}</div>
            Approved By
        </div>
        <div style="clear: both;"></div>
    </div>

    <div class="footer">
        Generated by System on {{ now()->format('d M Y H:i') }}
    </div>

</body>

</html>
