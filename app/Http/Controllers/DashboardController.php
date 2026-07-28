<?php

namespace App\Http\Controllers;

use App\Models\DailyReport;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    //
    public function index(Request $request)
    {
        $user = Auth::user();

        $startDate = $request->input('start_date', now()->subDays(6)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        $restaurantFilter = $request->input('restaurant_id');

        $today = now()->format('Y-m-d');
        $queryDraft = DailyReport::where('status', 'draft');

        // ---------------------------------------------------------
        // 1. QUERY DASAR (Otomatis terfilter oleh Global Scope)
        // ---------------------------------------------------------

        // Widget 1: Waiting Approval (Status Submitted)
        $waitingApprovalQuery = DailyReport::where('status', 'submitted');
        if ($restaurantFilter) {
            $waitingApprovalQuery->where('restaurant_id', $restaurantFilter);
        }
        $waitingApproval = $waitingApprovalQuery->count();

        // Widget 2: Drafts (Status Draft)
        if (!$user->hasRole('Super Admin')) {
            $queryDraft->where('user_id', $user->id);
        }
        if ($restaurantFilter) {
            $queryDraft->where('restaurant_id', $restaurantFilter);
        }
        $myDrafts = $queryDraft->count();

        // Widget 3: Today's Revenue
        $todaysReportsQuery = DailyReport::whereDate('date', $today);
        if ($restaurantFilter) {
            $todaysReportsQuery->where('restaurant_id', $restaurantFilter);
        }
        $todaysReports = $todaysReportsQuery->with('details')->get();

        $todayRevenue = 0;
        foreach ($todaysReports as $report) {
            foreach ($report->details as $detail) {
                // Jumlahkan semua komponen revenue (Food + Bev + Others + Event)
                $totalSesi = $detail->revenue_food
                    + $detail->revenue_beverage
                    + $detail->revenue_others
                    + $detail->revenue_event;
                $todayRevenue += $totalSesi;
            }
        }

        $chartData = collect();
        $chartBudgets = collect();
        $chartForecasts = collect();
        $start = \Carbon\Carbon::parse($startDate);
        $end = \Carbon\Carbon::parse($endDate);
        $daysDiff = $start->diffInDays($end);

        $restaurantIdsForTarget = $user->hasRole('Super Admin')
            ? null
            : $user->restaurants->pluck('id')->toArray();

        for ($i = 0; $i <= $daysDiff; $i++) {
            $date = $start->copy()->addDays($i)->format('Y-m-d');
            $chartData->put($date, 0);

            $dailyForecast = $this->calculateProratedTarget(
                $date,
                $date,
                $restaurantIdsForTarget,
                $restaurantFilter,
                'amount'
            );
            $dailyBudget = $this->calculateProratedTarget(
                $date,
                $date,
                $restaurantIdsForTarget,
                $restaurantFilter,
                'budget_amount'
            );

            $chartForecasts->put($date, $dailyForecast);
            $chartBudgets->put($date, $dailyBudget);
        }

        // 2. Ambil Data dari Database (Otomatis terfilter Scope User/Resto)
        $weeklyReportsQuery = DailyReport::whereBetween('date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        if ($restaurantFilter) {
            $weeklyReportsQuery->where('restaurant_id', $restaurantFilter);
        }
        $weeklyReports = $weeklyReportsQuery->with('details')->get();

        // 3. Isi Kerangka Array dengan Data Asli
        foreach ($weeklyReports as $report) {
            $dateKey = $report->date->format('Y-m-d');

            // Hitung total per laporan
            $reportTotal = 0;
            foreach ($report->details as $detail) {
                $reportTotal += $detail->revenue_food
                    + $detail->revenue_beverage
                    + $detail->revenue_others
                    + $detail->revenue_event;
            }

            // Tambahkan ke tanggal yang sesuai (Accumulate jika ada banyak resto)
            if ($chartData->has($dateKey)) {
                $chartData[$dateKey] += $reportTotal;
            }
        }

        // 4. Pisahkan Keys (Tanggal) dan Values (Uang) untuk ApexCharts
        // Format tanggal dipercantik jadi "21 Nov"
        $chartLabels = $chartData->keys()->map(fn($d) => \Carbon\Carbon::parse($d)->format('d M'))->values();
        $chartValues = $chartData->values();
        $chartForecastValues = $chartForecasts->values();
        $chartBudgetValues = $chartBudgets->values();

        // 1. Siapkan Kerangka Array untuk 4 Series
        $compData = [
            'us' => clone $chartData,        // Pakai clone agar index tanggalnya sama
            'shangrila' => clone $chartData,
            'jw' => clone $chartData,
            'sheraton' => clone $chartData,
        ];

        // Reset values jadi 0 semua
        foreach ($compData as $key => $collection) {
            $compData[$key] = $collection->map(fn() => 0);
        }

        // 2. Loop Data Laporan (Pakai variabel $weeklyReports yg sudah ada)
        foreach ($weeklyReports as $report) {
            $dateKey = $report->date->format('Y-m-d');

            foreach ($report->details as $detail) {

                // A. HITUNG TOTAL COVER KITA SENDIRI (SUM JSON)
                $myTotalCover = 0;
                if (!empty($detail->cover_data) && is_array($detail->cover_data)) {
                    foreach ($detail->cover_data as $val) {
                        // Jumlahkan hanya jika nilainya angka
                        if (is_numeric($val)) {
                            $myTotalCover += $val;
                        }
                    }
                }

                // B. AMBIL DATA KOMPETITOR
                $shangrila = $detail->competitor_data['shangrila_cover'] ?? 0;
                $jw = $detail->competitor_data['jw_marriott_cover'] ?? 0;
                $sheraton = $detail->competitor_data['sheraton_cover'] ?? 0;

                // C. MASUKKAN KE ARRAY (Accumulate)
                if ($compData['us']->has($dateKey)) {
                    $compData['us'][$dateKey] += $myTotalCover;
                    $compData['shangrila'][$dateKey] += (int)$shangrila;
                    $compData['jw'][$dateKey] += (int)$jw;
                    $compData['sheraton'][$dateKey] += (int)$sheraton;
                }
            }
        }

        // 3. Format Data untuk Grafik
        $compSeries = [
            ['name' => 'Our Restaurant', 'data' => $compData['us']->values()],
            ['name' => 'Shangri-La', 'data' => $compData['shangrila']->values()],
            ['name' => 'JW Marriott', 'data' => $compData['jw']->values()],
            ['name' => 'Sheraton', 'data' => $compData['sheraton']->values()],
        ];

        // ---------------------------------------------------------
        // PERFORMANCE (mengikuti filter date range + restaurant)
        // ---------------------------------------------------------

        // 1. Actual Revenue Periode = jumlah revenue
        //    di rentang tanggal yang dipilih (reuse $weeklyReports).
        $mtdRevenue = 0;
        foreach ($weeklyReports as $report) {
            foreach ($report->details as $detail) {
                $mtdRevenue += $detail->revenue_food
                    + $detail->revenue_beverage
                    + $detail->revenue_others
                    + $detail->revenue_event;
            }
        }

        // 2. Target Periode = prorata harian dari revenue_targets per
        //    bulan yang bersinggungan dengan rentang tanggal.
        $monthlyTarget = $this->calculateProratedTarget(
            $startDate,
            $endDate,
            $restaurantIdsForTarget,
            $restaurantFilter
        );

        $monthlyBudget = $this->calculateProratedTarget(
            $startDate,
            $endDate,
            $restaurantIdsForTarget,
            $restaurantFilter,
            'budget_amount'
        );

        // 3. Hitung Persentase (Cegah division by zero)
        $achievementPercent = $monthlyTarget > 0 ? ($mtdRevenue / $monthlyTarget) * 100 : 0;

        // 4. Label periode untuk header card "Performance (...)"
        $periodLabel = $this->formatPeriodLabel($startDate, $endDate);

        $breakdownPerformance = [];
        $relevantRestaurants = collect();

        // 1. Tentukan Restoran mana yang mau dihitung
        if ($restaurantFilter) {
            $relevantRestaurants = Restaurant::where('id', $restaurantFilter)->get();
        } elseif ($user->hasRole('Super Admin')) {
            $relevantRestaurants = Restaurant::all();
        } else {
            // Untuk Cluster & Single Unit, ambil dari relasi
            $relevantRestaurants = $user->restaurants;
        }

        // 2. Loop setiap restoran untuk hitung target vs actual
        // Hitung dalam rentang tanggal terpilih (bukan hardcoded bulan ini)
        foreach ($relevantRestaurants as $resto) {

            // A. Hitung Actual Revenue untuk Resto ini di rentang tanggal
            $restoReports = DailyReport::where('restaurant_id', $resto->id)
                ->whereBetween('date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->with('details')
                ->get();

            $actual = 0;
            foreach ($restoReports as $rpt) {
                foreach ($rpt->details as $dtl) {
                    $actual += $dtl->revenue_food + $dtl->revenue_beverage + $dtl->revenue_others + $dtl->revenue_event;
                }
            }

            // B. Hitung Target Periode (prorata harian) untuk Resto ini
            $target = $this->calculateProratedTarget($startDate, $endDate, [$resto->id]);
            $budget = $this->calculateProratedTarget($startDate, $endDate, [$resto->id], null, 'budget_amount');

            // C. Hitung Persentase
            $percentage = $target > 0 ? ($actual / $target) * 100 : 0;
            $budgetPercentage = $budget > 0 ? ($actual / $budget) * 100 : 0;

            // D. Masukkan ke Array
            $breakdownPerformance[] = [
                'id' => $resto->id,
                'name' => $resto->name,
                'code' => $resto->code,
                'target' => $target,
                'budget' => $budget,
                'actual' => $actual,
                'percentage' => $percentage,
                'budget_percentage' => $budgetPercentage
            ];
        }

        // ---------------------------------------------------------
        // TRUE MTD (Month to Date) - STATIC 1st of month to today
        // ---------------------------------------------------------
        $mtdStart = now()->startOfMonth()->format('Y-m-d');
        $mtdEnd = now()->format('Y-m-d');

        $mtdQuery = DailyReport::whereBetween('date', [$mtdStart, $mtdEnd]);

        if ($restaurantFilter) {
            $mtdQuery->where('restaurant_id', $restaurantFilter);
        } elseif (!$user->hasRole('Super Admin')) {
            $mtdQuery->whereIn('restaurant_id', $user->restaurants->pluck('id'));
        }

        $mtdReports = $mtdQuery->with('details')->get();

        $mtdFoodRevenue = 0;
        $mtdBeverageRevenue = 0;
        $mtdCoverReport = 0;
        $totalMtdRevenue = 0;

        foreach ($mtdReports as $report) {
            foreach ($report->details as $detail) {
                $mtdFoodRevenue += (float)$detail->revenue_food;
                $mtdBeverageRevenue += (float)$detail->revenue_beverage;
                $totalMtdRevenue += $detail->revenue_food + $detail->revenue_beverage + $detail->revenue_others + $detail->revenue_event;

                if (!empty($detail->cover_data) && is_array($detail->cover_data)) {
                    foreach ($detail->cover_data as $val) {
                        if (is_numeric($val)) {
                            $mtdCoverReport += $val;
                        }
                    }
                }
            }
        }


        $mtdAverageFood = $mtdCoverReport > 0 ? $mtdFoodRevenue / $mtdCoverReport : 0;
        $mtdAverageBeverage = $mtdCoverReport > 0 ? $mtdBeverageRevenue / $mtdCoverReport : 0;

        $totalMtdTarget = $this->calculateProratedTarget(
            $mtdStart,
            $mtdEnd,
            $user->hasRole('Super Admin') ? null : $user->restaurants->pluck('id')->toArray(),
            $restaurantFilter
        );

        $totalMtdBudget = $this->calculateProratedTarget(
            $mtdStart,
            $mtdEnd,
            $user->hasRole('Super Admin') ? null : $user->restaurants->pluck('id')->toArray(),
            $restaurantFilter,
            'budget_amount'
        );

        $mtdBalance = $totalMtdRevenue - $totalMtdTarget;
        $mtdBudgetBalance = $totalMtdRevenue - $totalMtdBudget;

        // ---------------------------------------------------------
        // 2. TABEL RINGKASAN (5 Laporan Terakhir)
        // ---------------------------------------------------------
        $recentReportsQuery = DailyReport::with(['restaurant', 'user']);
        if ($restaurantFilter) {
            $recentReportsQuery->where('restaurant_id', $restaurantFilter);
        }
        $recentReports = $recentReportsQuery
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $allRestaurants = collect();
        if ($user->hasRole('Super Admin')) {
            $allRestaurants = Restaurant::orderBy('name')->get();
        } else {
            $allRestaurants = $user->restaurants;
        }

        // Kirim semua data ke View
        return view('dashboard', compact(
            'waitingApproval',
            'myDrafts',
            'todayRevenue',
            'recentReports',
            'chartLabels',
            'chartValues',
            'chartForecastValues',
            'chartBudgetValues',
            'compSeries',
            'mtdRevenue',
            'monthlyTarget',
            'monthlyBudget',
            'achievementPercent',
            'periodLabel',
            'breakdownPerformance',
            'allRestaurants',
            'startDate',
            'endDate',
            'restaurantFilter',
            'mtdCoverReport',
            'mtdFoodRevenue',
            'mtdBeverageRevenue',
            'mtdAverageFood',
            'mtdAverageBeverage',
            'totalMtdRevenue',
            'totalMtdTarget',
            'mtdBalance',
            'totalMtdBudget',
            'mtdBudgetBalance'
        ));
    }

    /**
     * Hitung total target revenue secara prorata harian untuk rentang tanggal.
     *
     * Setiap baris revenue_targets disimpan per (restaurant_id, year, month) sebagai
     * target satu bulan penuh. Saat user memilih rentang yang tidak persis sama dengan
     * bulan kalender (mis. 1-15 Nov, atau 25 Okt - 5 Nov), kita prorata berdasarkan
     * jumlah hari di rentang yang jatuh di bulan tersebut.
     *
     * @param string     $startDate          Y-m-d
     * @param string     $endDate            Y-m-d
     * @param array|null $allowedRestaurants Daftar restaurant_id yang user boleh akses
     *                                       (null = semua, untuk Super Admin)
     * @param int|null   $restaurantFilter   Filter restaurant tertentu dari UI
     */
    private function calculateProratedTarget(
        string $startDate,
        string $endDate,
        ?array $allowedRestaurants = null,
        $restaurantFilter = null,
        string $column = 'amount'
    ): float {
        $start = \Carbon\Carbon::parse($startDate)->startOfDay();
        $end = \Carbon\Carbon::parse($endDate)->endOfDay();

        if ($end->lt($start)) {
            return 0.0;
        }

        $total = 0.0;

        // Loop setiap bulan kalender yang bersinggungan dengan rentang
        $cursor = $start->copy()->startOfMonth();
        while ($cursor->lte($end)) {
            $monthStart = $cursor->copy()->startOfMonth();
            $monthEnd = $cursor->copy()->endOfMonth();
            $daysInMonth = $cursor->daysInMonth;

            // Irisan rentang dengan bulan ini
            $sliceStart = $start->greaterThan($monthStart) ? $start : $monthStart;
            $sliceEnd = $end->lessThan($monthEnd) ? $end : $monthEnd;
            $daysInSlice = (int) $sliceStart->copy()->startOfDay()->diffInDays($sliceEnd->copy()->startOfDay()) + 1;

            // Ambil semua target untuk bulan ini
            $targetQuery = \App\Models\RevenueTarget::where('year', $cursor->year)
                ->where('month', $cursor->month);

            if (is_array($allowedRestaurants)) {
                $targetQuery->whereIn('restaurant_id', $allowedRestaurants);
            }

            if ($restaurantFilter) {
                $targetQuery->where('restaurant_id', $restaurantFilter);
            }

            $monthAmount = (float) $targetQuery->sum($column);

            if ($monthAmount > 0 && $daysInMonth > 0) {
                $total += ($monthAmount / $daysInMonth) * $daysInSlice;
            }

            $cursor->addMonthNoOverflow();
        }

        return $total;
    }

    /**
     * Format label rentang tanggal yang ramah dibaca untuk header card.
     * Contoh:
     *  - "1 Nov 2025"                        → tanggal sama
     *  - "1 - 15 Nov 2025"                   → bulan & tahun sama
     *  - "25 Oct - 5 Nov 2025"               → tahun sama, bulan beda
     *  - "25 Dec 2024 - 5 Jan 2025"          → tahun beda
     */
    private function formatPeriodLabel(string $startDate, string $endDate): string
    {
        $start = \Carbon\Carbon::parse($startDate);
        $end = \Carbon\Carbon::parse($endDate);

        if ($start->isSameDay($end)) {
            return $start->format('d M Y');
        }

        if ($start->isSameYear($end)) {
            if ($start->isSameMonth($end)) {
                return $start->format('d') . ' - ' . $end->format('d M Y');
            }
            return $start->format('d M') . ' - ' . $end->format('d M Y');
        }

        return $start->format('d M Y') . ' - ' . $end->format('d M Y');
    }

    public function getOutletAnalytics(Request $request, Restaurant $restaurant)
    {
        // 1. Validasi Akses (Security)
        $user = Auth::user();
        if (!$user->hasRole('Super Admin') && !$user->restaurants->contains($restaurant->id)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // 2. Ambil Filter Tanggal (Default: Bulan Ini)
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        // 3. Query Data (Semua Report)
        $reports = DailyReport::where('restaurant_id', $restaurant->id)
            ->whereBetween('date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->with('details')
            ->get();

        // 4. Inisialisasi Struktur Data Matriks
        // Kita butuh array kosong untuk menampung penjumlahan
        $sessions = ['breakfast', 'lunch', 'dinner', 'supper'];

        // Structure: ['Item Name' => ['breakfast' => 0, 'lunch' => 0, ...]]
        $revenueMatrix = [
            'Food Revenue' => array_fill_keys($sessions, 0),
            'Beverage Revenue' => array_fill_keys($sessions, 0),
            'Others Revenue' => array_fill_keys($sessions, 0),
            'Event Revenue' => array_fill_keys($sessions, 0),
        ];

        $coverMatrix = []; // Dinamis (tergantung key yang ditemukan)
        $competitorMatrix = []; // Dinamis
        $usCoverTotal = array_fill_keys($sessions, 0); // Untuk baris "Us" di tabel kompetitor

        // Inisialisasi untuk Occasion, Promo, Group
        $occasionMatrix = []; // occasion_items aggregated (pax)
        $occasionRevenueMatrix = []; // occasion_items aggregated (revenue)
        $promoMatrix = [];    // promo_items aggregated (pax)
        $promoRevenueMatrix = []; // promo_items aggregated (revenue)
        $occOthersAgg = [];   // others_occasion aggregated (legacy)
        $promoOthersAgg = []; // others_promo aggregated
        $groupAgg = [];       // group_data aggregated
        $setMenuMatrix = []; // setmenu_items aggregated (pax)
        $setMenuRevenueMatrix = []; // setmenu_items aggregated (revenue)
        $upsellingFoodMatrix = []; // upselling_data['food'] aggregated (pax)
        $upsellingBeverageMatrix = []; // upselling_data['beverage'] aggregated (pax)
        // Inisialisasi untuk Nagano Revenue
        $naganoRevenueMatrix = [
            'Teppan (Lt 5)' => array_fill_keys($sessions, 0),
            'Teppan (Lt 6)' => array_fill_keys($sessions, 0),
            'Yakiniku' => array_fill_keys($sessions, 0),
            'Ala Carte' => array_fill_keys($sessions, 0),
        ];

        // 5. Loop & Agregasi Data (The Heavy Lifting)
        foreach ($reports as $report) {
            foreach ($report->details as $detail) {
                $sess = $detail->session_type;

                // A. Agregasi Revenue
                $revenueMatrix['Food Revenue'][$sess] += $detail->revenue_food;
                $revenueMatrix['Beverage Revenue'][$sess] += $detail->revenue_beverage;
                $revenueMatrix['Others Revenue'][$sess] += $detail->revenue_others;
                $revenueMatrix['Event Revenue'][$sess] += $detail->revenue_event;

                // B. Agregasi Cover (Dinamis)
                if (!empty($detail->cover_data) && is_array($detail->cover_data)) {
                    foreach ($detail->cover_data as $key => $val) {
                        if (is_numeric($val)) {
                            // Bersihkan nama key (misal: "in_house_adult" -> "In House Adult")
                            $cleanKey = ucwords(str_replace('_', ' ', $key));

                            // Init jika belum ada di matriks
                            if (!isset($coverMatrix[$cleanKey])) {
                                $coverMatrix[$cleanKey] = array_fill_keys($sessions, 0);
                            }

                            $coverMatrix[$cleanKey][$sess] += $val;
                            $usCoverTotal[$sess] += $val; // Tambah ke total kita
                        }
                    }
                }

                // C. Agregasi Competitor
                if (!empty($detail->competitor_data) && is_array($detail->competitor_data)) {
                    foreach ($detail->competitor_data as $key => $val) {
                        if (is_numeric($val)) {
                            $cleanKey = ucwords(str_replace(['_cover', 'cover', '_'], ['', '', ' '], $key)); // Hapus kata "cover" agar pendek

                            if (!isset($competitorMatrix[$cleanKey])) {
                                $competitorMatrix[$cleanKey] = array_fill_keys($sessions, 0);
                            }

                            $competitorMatrix[$cleanKey][$sess] += $val;
                        }
                    }
                }

                // D. Agregasi Occasion Items
                $addData = $detail->additional_data ?? [];
                if (is_string($addData)) $addData = json_decode($addData, true) ?? [];
                if (is_array($addData)) {
                    $occItems = $addData['occasion_items'] ?? [];
                    if (is_string($occItems)) $occItems = json_decode($occItems, true) ?? [];
                    if (is_array($occItems)) {
                        foreach ($occItems as $item) {
                            $type = $item['type'] ?? 'Unknown';
                            $pax = $item['pax'] ?? 0;
                            $rev = $item['revenue'] ?? 0;
                            if ($pax > 0) {
                                if (!isset($occasionMatrix[$type])) {
                                    $occasionMatrix[$type] = array_fill_keys($sessions, 0);
                                }
                                $occasionMatrix[$type][$sess] += $pax;
                            }
                            if ($rev > 0) {
                                if (!isset($occasionRevenueMatrix[$type])) {
                                    $occasionRevenueMatrix[$type] = array_fill_keys($sessions, 0);
                                }
                                $occasionRevenueMatrix[$type][$sess] += $rev;
                            }
                        }
                    }

                    // Legacy occasion fields (for backward compatibility)
                    $legacyFields = ['wedding_party', 'birthday_party', 'social_event'];
                    foreach ($legacyFields as $field) {
                        $val = $addData[$field] ?? 0;
                        if ($val > 0) {
                            $label = ucwords(str_replace('_', ' ', $field));
                            if (!isset($occOthersAgg[$label])) {
                                $occOthersAgg[$label] = array_fill_keys($sessions, 0);
                            }
                            $occOthersAgg[$label][$sess] += $val;
                        }
                    }

                    // Others Occasion (legacy)
                    $othersOcc = $addData['others_occasion'] ?? [];
                    if (is_string($othersOcc)) $othersOcc = json_decode($othersOcc, true) ?? [];
                    if (is_array($othersOcc)) {
                        foreach ($othersOcc as $item) {
                            $name = $item['name'] ?? 'Other Occasion';
                            $pax = $item['pax'] ?? 0;
                            if ($pax > 0) {
                                if (!isset($occOthersAgg[$name])) {
                                    $occOthersAgg[$name] = array_fill_keys($sessions, 0);
                                }
                                $occOthersAgg[$name][$sess] += $pax;
                            }
                        }
                    }

                    // E. Agregasi Promo Items (new format)
                    $promoItemsData = $addData['promo_items'] ?? [];
                    if (is_string($promoItemsData)) $promoItemsData = json_decode($promoItemsData, true) ?? [];
                    if (is_array($promoItemsData)) {
                        foreach ($promoItemsData as $item) {
                            $type = $item['type'] ?? 'Unknown Promo';
                            $pax = $item['pax'] ?? 0;
                            $rev = $item['revenue'] ?? 0;
                            if ($pax > 0) {
                                if (!isset($promoMatrix[$type])) {
                                    $promoMatrix[$type] = array_fill_keys($sessions, 0);
                                }
                                $promoMatrix[$type][$sess] += $pax;
                            }
                            if ($rev > 0) {
                                if (!isset($promoRevenueMatrix[$type])) {
                                    $promoRevenueMatrix[$type] = array_fill_keys($sessions, 0);
                                }
                                $promoRevenueMatrix[$type][$sess] += $rev;
                            }
                        }
                    }

                    // H. Agregasi Set Menu
                    $setMenuItems = $addData['setmenu_items'] ?? [];
                    if (is_string($setMenuItems)) $setMenuItems = json_decode($setMenuItems, true) ?? [];
                    if (is_array($setMenuItems)) {
                        foreach ($setMenuItems as $item) {
                            $type = $item['type'] ?? 'Unknown Set Menu';
                            $pax = $item['pax'] ?? 0;
                            $rev = $item['revenue'] ?? 0;
                            if ($pax > 0) {
                                if (!isset($setMenuMatrix[$type])) {
                                    $setMenuMatrix[$type] = array_fill_keys($sessions, 0);
                                }
                                $setMenuMatrix[$type][$sess] += $pax;
                            }
                            if ($rev > 0) {
                                if (!isset($setMenuRevenueMatrix[$type])) {
                                    $setMenuRevenueMatrix[$type] = array_fill_keys($sessions, 0);
                                }
                                $setMenuRevenueMatrix[$type][$sess] += $rev;
                            }
                        }
                    }

                    // Legacy set menu fields
                    $legacySetMenuFields = [
                        'set_menu_family_8000' => 'Family 8000',
                        'set_menu_family_5000' => 'Family 5000',
                        'set_menu_family_6000' => 'Family 6000',
                        'set_menu_ayce_dimsum' => 'AYCE Dimsum',
                        'set_menu_788' => 'Set Menu 788',
                        'set_menu_988' => 'Set Menu 988',
                        'set_menu_1188' => 'Set Menu 1188',
                    ];
                    foreach ($legacySetMenuFields as $field => $label) {
                        $val = $addData[$field] ?? 0;
                        if ($val > 0) {
                            if (!isset($setMenuMatrix[$label])) {
                                $setMenuMatrix[$label] = array_fill_keys($sessions, 0);
                            }
                            $setMenuMatrix[$label][$sess] += $val;
                        }
                    }

                    // I. Agregasi Upselling Food & Beverage
                    $upsellingData = $detail->upselling_data ?? [];
                    if (is_string($upsellingData)) $upsellingData = json_decode($upsellingData, true) ?? [];
                    if (is_array($upsellingData)) {
                        // Food
                        $foodData = $upsellingData['food'] ?? [];
                        if (is_string($foodData)) $foodData = json_decode($foodData, true) ?? [];
                        if (is_array($foodData)) {
                            foreach ($foodData as $item) {
                                $name = $item['name'] ?? 'Unknown Food';
                                $pax = $item['pax'] ?? 0;
                                if ($pax > 0) {
                                    if (!isset($upsellingFoodMatrix[$name])) {
                                        $upsellingFoodMatrix[$name] = array_fill_keys($sessions, 0);
                                    }
                                    $upsellingFoodMatrix[$name][$sess] += $pax;
                                }
                            }
                        }

                        // Beverage
                        $bevData = $upsellingData['beverage'] ?? [];
                        if (is_string($bevData)) $bevData = json_decode($bevData, true) ?? [];
                        if (is_array($bevData)) {
                            foreach ($bevData as $item) {
                                $name = $item['name'] ?? 'Unknown Beverage';
                                $pax = $item['pax'] ?? 0;
                                if ($pax > 0) {
                                    if (!isset($upsellingBeverageMatrix[$name])) {
                                        $upsellingBeverageMatrix[$name] = array_fill_keys($sessions, 0);
                                    }
                                    $upsellingBeverageMatrix[$name][$sess] += $pax;
                                }
                            }
                        }
                    }

                    // Old promo fields (legacy)
                    $promoFields = ['mandiri_card', 'bca_card', 'membership'];
                    foreach ($promoFields as $field) {
                        $val = $addData[$field] ?? 0;
                        if ($val > 0) {
                            $label = ucwords(str_replace('_', ' ', $field));
                            if (!isset($promoMatrix[$label])) {
                                $promoMatrix[$label] = array_fill_keys($sessions, 0);
                            }
                            $promoMatrix[$label][$sess] += $val;
                        }
                    }

                    // Others Promo (legacy)
                    $othersPromo = $addData['others_promo'] ?? [];
                    if (is_string($othersPromo)) $othersPromo = json_decode($othersPromo, true) ?? [];
                    if (is_array($othersPromo)) {
                        foreach ($othersPromo as $item) {
                            $name = $item['name'] ?? 'Other Promo';
                            $qty = $item['qty'] ?? 0;
                            if ($qty > 0) {
                                if (!isset($promoOthersAgg[$name])) {
                                    $promoOthersAgg[$name] = array_fill_keys($sessions, 0);
                                }
                                $promoOthersAgg[$name][$sess] += $qty;
                            }
                        }
                    }

                    // F. Agregasi Group
                    $groupData = $addData['group_data'] ?? [];
                    if (is_string($groupData)) $groupData = json_decode($groupData, true) ?? [];
                    if (is_array($groupData)) {
                        foreach ($groupData as $item) {
                            $name = $item['name'] ?? 'Group';
                            $qty = $item['qty'] ?? 0;
                            if ($qty > 0) {
                                if (!isset($groupAgg[$name])) {
                                    $groupAgg[$name] = array_fill_keys($sessions, 0);
                                }
                                $groupAgg[$name][$sess] += $qty;
                            }
                        }
                    }

                    // G. Agregasi Nagano Revenue
                    $teppanItems = $addData['revenue_teppan_items'] ?? [];
                    if (is_string($teppanItems)) $teppanItems = json_decode($teppanItems, true) ?? [];
                    if (is_array($teppanItems)) {
                        foreach ($teppanItems as $item) {
                            $floor = $item['floor'] ?? '';
                            $rev = $item['revenue'] ?? 0;
                            $key = 'Teppan (' . $floor . ')';
                            if (isset($naganoRevenueMatrix[$key])) {
                                $naganoRevenueMatrix[$key][$sess] += $rev;
                            }
                        }
                    }
                    $yakinikuRev = $addData['revenue_yakiniku'] ?? 0;
                    if ($yakinikuRev > 0) {
                        $naganoRevenueMatrix['Yakiniku'][$sess] += $yakinikuRev;
                    }
                    $alaCarteRev = $addData['revenue_ala_carte'] ?? 0;
                    if ($alaCarteRev > 0) {
                        $naganoRevenueMatrix['Ala Carte'][$sess] += $alaCarteRev;
                    }
                }
            }
        }

        // 6. Masukkan "Us (Our Resto)" ke baris pertama Competitor Matrix
        // Kita merge array agar "Us" ada di paling atas
        $competitorMatrix = array_merge(
            ['Us (' . $restaurant->name . ')' => $usCoverTotal],
            $competitorMatrix
        );

        // 8. Siapkan Data Day Trend (untuk Tab Cover by Day)
        $daysOfWeek = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        $dayTrendMatrix = [];
        $setMenuDayTrendMatrix = [];
        foreach ($sessions as $sess) {
            $dayTrendMatrix[$sess] = array_fill_keys($daysOfWeek, 0);
        }
        foreach ($reports as $report) {
            $dayName = $report->date->format('D');
            if (!in_array($dayName, $daysOfWeek)) continue;

            foreach ($report->details as $detail) {
                $sess = $detail->session_type;
                $pax = 0;
                if (!empty($detail->cover_data) && is_array($detail->cover_data)) {
                    foreach ($detail->cover_data as $val) {
                        if (is_numeric($val)) $pax += $val;
                    }
                }
                $dayTrendMatrix[$sess][$dayName] += $pax;

                // Set Menu Trend
                $addData = $detail->additional_data ?? [];
                if (is_string($addData)) $addData = json_decode($addData, true) ?? [];
                if (is_array($addData)) {
                    $setMenuItems = $addData['setmenu_items'] ?? [];
                    if (is_string($setMenuItems)) $setMenuItems = json_decode($setMenuItems, true) ?? [];
                    if (is_array($setMenuItems)) {
                        foreach ($setMenuItems as $item) {
                            $type = $item['type'] ?? 'Unknown Set Menu';
                            $spax = $item['pax'] ?? 0;
                            if ($spax > 0) {
                                if (!isset($setMenuDayTrendMatrix[$type])) {
                                    $setMenuDayTrendMatrix[$type] = array_fill_keys($daysOfWeek, 0);
                                }
                                $setMenuDayTrendMatrix[$type][$dayName] += $spax;
                            }
                        }
                    }

                    $legacySetMenuFields = [
                        'set_menu_family_8000' => 'Family 8000',
                        'set_menu_family_5000' => 'Family 5000',
                        'set_menu_family_6000' => 'Family 6000',
                        'set_menu_ayce_dimsum' => 'AYCE Dimsum',
                        'set_menu_788' => 'Set Menu 788',
                        'set_menu_988' => 'Set Menu 988',
                        'set_menu_1188' => 'Set Menu 1188',
                    ];
                    foreach ($legacySetMenuFields as $field => $label) {
                        $sval = $addData[$field] ?? 0;
                        if ($sval > 0) {
                            if (!isset($setMenuDayTrendMatrix[$label])) {
                                $setMenuDayTrendMatrix[$label] = array_fill_keys($daysOfWeek, 0);
                            }
                            $setMenuDayTrendMatrix[$label][$dayName] += $sval;
                        }
                    }
                }
            }
        }


        // 9. Return Partial View
        return view('analytics_modal', compact(
            'restaurant',
            'startDate',
            'endDate',
            'sessions',
            'revenueMatrix',
            'coverMatrix',
            'competitorMatrix',
            'daysOfWeek',
            'dayTrendMatrix',
            'occasionMatrix',
            'occOthersAgg',
            'promoMatrix',
            'promoRevenueMatrix',
            'promoOthersAgg',
            'groupAgg',
            'occasionRevenueMatrix',
            'naganoRevenueMatrix',
            'setMenuMatrix',
            'setMenuRevenueMatrix',
            'upsellingFoodMatrix',
            'upsellingBeverageMatrix',
            'setMenuDayTrendMatrix'
        ));
    }
}
