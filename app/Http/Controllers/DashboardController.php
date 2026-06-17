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
        $todaysReportsQuery = DailyReport::whereDate('date', $today)
            ->where('status', 'approved');
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
        $start = \Carbon\Carbon::parse($startDate);
        $end = \Carbon\Carbon::parse($endDate);
        $daysDiff = $start->diffInDays($end);
        
        for ($i = 0; $i <= $daysDiff; $i++) {
            $date = $start->copy()->addDays($i)->format('Y-m-d');
            $chartData->put($date, 0);
        }

        // 2. Ambil Data dari Database (Otomatis terfilter Scope User/Resto)
        $weeklyReportsQuery = DailyReport::whereBetween('date', [$startDate, $endDate])
            ->where('status', 'approved');
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

        // 1. Actual Revenue Periode = jumlah revenue laporan approved
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
        $restaurantIdsForTarget = $user->hasRole('Super Admin')
            ? null
            : $user->restaurants->pluck('id')->toArray();

        $monthlyTarget = $this->calculateProratedTarget(
            $startDate,
            $endDate,
            $restaurantIdsForTarget,
            $restaurantFilter
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

            // A. Hitung Actual Revenue (Approved Only) untuk Resto ini di rentang tanggal
            $restoReports = DailyReport::where('restaurant_id', $resto->id)
                ->whereBetween('date', [$startDate, $endDate])
                ->where('status', 'approved')
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

            // C. Hitung Persentase
            $percentage = $target > 0 ? ($actual / $target) * 100 : 0;

            // D. Masukkan ke Array
            $breakdownPerformance[] = [
                'id' => $resto->id,
                'name' => $resto->name,
                'code' => $resto->code,
                'target' => $target,
                'actual' => $actual,
                'percentage' => $percentage
            ];
        }

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
            'compSeries',
            'mtdRevenue',
            'monthlyTarget',
            'achievementPercent',
            'periodLabel',
            'breakdownPerformance',
            'allRestaurants',
            'startDate',
            'endDate',
            'restaurantFilter'
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
        $restaurantFilter = null
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
            $daysInSlice = $sliceStart->copy()->startOfDay()->diffInDays($sliceEnd->copy()->endOfDay()) + 1;

            // Ambil semua target untuk bulan ini
            $targetQuery = \App\Models\RevenueTarget::where('year', $cursor->year)
                ->where('month', $cursor->month);

            if (is_array($allowedRestaurants)) {
                $targetQuery->whereIn('restaurant_id', $allowedRestaurants);
            }

            if ($restaurantFilter) {
                $targetQuery->where('restaurant_id', $restaurantFilter);
            }

            $monthAmount = (float) $targetQuery->sum('amount');

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

        // 3. Query Data (Hanya Approved)
        $reports = DailyReport::where('restaurant_id', $restaurant->id)
            ->where('status', 'approved') // Wajib Approved
            ->whereBetween('date', [$startDate, $endDate])
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
            }
        }

        // 6. Masukkan "Us (Our Resto)" ke baris pertama Competitor Matrix
        // Kita merge array agar "Us" ada di paling atas
        $competitorMatrix = array_merge(
            ['Us (' . $restaurant->name . ')' => $usCoverTotal],
            $competitorMatrix
        );

        // 7A. Siapkan Data untuk Grafik Cover Report
        $chartCategories = array_keys($coverMatrix); // Label Sumbu X
        $chartSeries = [];

        foreach ($sessions as $sess) {
            $dataPerSession = [];
            foreach ($chartCategories as $category) {
                // Ambil data dari matrix, default 0 jika error
                $dataPerSession[] = $coverMatrix[$category][$sess] ?? 0;
            }

            $chartSeries[] = [
                'name' => ucfirst($sess), // Breakfast, Lunch, etc
                'data' => $dataPerSession
            ];
        }

        // 7B. SIAPKAN DATA REVENUE CHART
        // Categories: ['Food Revenue', 'Beverage Revenue', 'Others Revenue', 'Event Revenue']
        $revChartCategories = array_keys($revenueMatrix);
        $revChartSeries = [];

        foreach ($sessions as $sess) {
            $dataPerSession = [];
            foreach ($revChartCategories as $category) {
                // Ambil data dari matrix
                $dataPerSession[] = $revenueMatrix[$category][$sess] ?? 0;
            }

            $revChartSeries[] = [
                'name' => ucfirst($sess), // Breakfast, Lunch, etc
                'data' => $dataPerSession
            ];
        }

        // 7C. SIAPKAN DATA COMPETITOR CHART
        // Categories: ['Us (Restaurant Name)', 'Shangri-La', 'JW Marriott', ...]
        $compChartCategories = array_keys($competitorMatrix);
        $compChartSeries = [];

        foreach ($sessions as $sess) {
            $dataPerSession = [];
            foreach ($compChartCategories as $hotel) {
                $dataPerSession[] = $competitorMatrix[$hotel][$sess] ?? 0;
            }

            $compChartSeries[] = [
                'name' => ucfirst($sess),
                'data' => $dataPerSession
            ];
        }

        // 7D. SIAPKAN DATA DAILY TREND (BY DAY OF WEEK)
        // Columns: Mon, Tue, Wed, Thu, Fri, Sat, Sun
        $daysOfWeek = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

        // Rows: Breakfast, Lunch, Dinner, Supper
        // Structure: ['breakfast' => ['Mon' => 0, 'Tue' => 0...], ...]
        $dayTrendMatrix = [];
        foreach ($sessions as $sess) {
            $dayTrendMatrix[$sess] = array_fill_keys($daysOfWeek, 0);
        }

        // AGREGASI DATA
        foreach ($reports as $report) {
            // Ambil nama hari (Mon, Tue, etc) dari tanggal laporan
            $dayName = $report->date->format('D');

            foreach ($report->details as $detail) {
                $sess = $detail->session_type;

                // Hitung Total Pax (Sum semua field cover)
                $pax = 0;
                if (!empty($detail->cover_data) && is_array($detail->cover_data)) {
                    foreach ($detail->cover_data as $val) {
                        if (is_numeric($val)) $pax += $val;
                    }
                }

                // Masukkan ke Matrix
                if (in_array($dayName, $daysOfWeek)) {
                    $dayTrendMatrix[$sess][$dayName] += $pax;
                }
            }
        }

        // SIAPKAN DATA CHART (Line Chart)
        $dayChartSeries = [];
        foreach ($sessions as $sess) {
            $dataPerDay = [];
            foreach ($daysOfWeek as $day) {
                $dataPerDay[] = $dayTrendMatrix[$sess][$day];
            }
            $dayChartSeries[] = [
                'name' => ucfirst($sess),
                'data' => $dataPerDay
            ];
        }

        // 8. Return Partial View
        // Kita kirim data yang sudah matang ke view khusus (belum kita buat)
        return view('analytics_modal', compact(
            'restaurant',
            'startDate',
            'endDate',
            'sessions',
            'revenueMatrix',
            'coverMatrix',
            'competitorMatrix',
            'chartCategories',
            'chartSeries',
            'revChartCategories',
            'revChartSeries',
            'compChartCategories',
            'compChartSeries',
            'daysOfWeek',
            'dayTrendMatrix',
            'dayChartSeries',
        ));
    }
}
