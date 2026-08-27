<?php

namespace App\Http\Controllers;

use App\Models\DailyReport;
use App\Models\DailyReportDetail;
use App\Models\Restaurant;
use App\Models\UpsellingItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\DailyReportsExport;

class DailyReportController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = DailyReport::with(['restaurant', 'user']);

        // Apply date range filter
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        }

        // Apply restaurant filter
        if ($request->filled('restaurant_id')) {
            $query->where('restaurant_id', $request->restaurant_id);
        }

        $reports = $query->orderBy('date', 'desc')->paginate(10);

        // Append query string to pagination links
        $reports->appends($request->only(['start_date', 'end_date', 'restaurant_id']));

        // Get restaurants for filter and export
        if ($user->hasRole('Super Admin')) {
            $restaurants = Restaurant::orderBy('name')->get();
        } else {
            $restaurants = $user->restaurants()->orderBy('name')->get();
        }

        return view('daily-reports.index', compact('reports', 'restaurants'));
    }

    public function create()
    {
        $user = Auth::user();

        if ($user->hasRole('Super Admin')) {
            $restaurants = Restaurant::with(['users', 'employees'])->get();
        } else {
            $restaurants = $user->restaurants()->with(['users', 'employees'])->get();
        }

        $details = [];

        $upsellingQuery = UpsellingItem::query();
        if (!$user->hasRole('Super Admin')) {
            $upsellingQuery->whereIn('restaurant_id', $user->restaurants->pluck('id'));
        }
        $upsellingItems = $upsellingQuery->get()->groupBy('restaurant_id');

        return view('daily-reports.create', compact('restaurants', 'details', 'upsellingItems'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        // PROTEKSI DATA
        if (!$user->hasRole('Super Admin')) {
            $myRestoIds = $user->restaurants->pluck('id')->toArray();

            if (!in_array($request->restaurant_id, $myRestoIds)) {
                return back()->with('error', 'Anda tidak memiliki akses ke restoran ini.');
            }
        }

        // 1. Validasi Dasar (Header)
        $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
            'date' => 'required|date',
        ]);

        $rawSessions = $request->input('session', []);
        $inputSessions = $this->sanitizeSessionData($rawSessions);
        $request->merge(['session' => $inputSessions]);

        $this->validateSubmission($request, $request->restaurant_id);

        try {
            DB::beginTransaction();

            $status = 'draft';
            $approvedBy = null;
            $approvedAt = null;

            if ($request->input('action') === 'submit') {
                if ($this->isUserApprover($user)) {
                    $status = 'approved';
                    $approvedBy = $user->id;
                    $approvedAt = now();
                } else {
                    $status = 'submitted';
                }
            }

            // 3. Simpan Header Laporan
            $report = DailyReport::create([
                'restaurant_id' => $request->restaurant_id,
                'user_id' => Auth::id(),
                'date' => $request->date,
                'status' => $status,
                'approved_by' => $approvedBy,
                'approved_at' => $approvedAt,
            ]);

            // 4. Simpan Detail Sesi
            $sessions = ['breakfast', 'lunch', 'dinner', 'supper'];

            foreach ($sessions as $sessionType) {
                if (isset($inputSessions[$sessionType])) {
                    $data = $inputSessions[$sessionType];

                    DailyReportDetail::create([
                        'daily_report_id' => $report->id,
                        'session_type' => $sessionType,
                        'revenue_food' => $data['revenue_food'] ?? 0,
                        'revenue_beverage' => $data['revenue_beverage'] ?? 0,
                        'revenue_others' => $data['revenue_others'] ?? 0,
                        'revenue_event' => $data['revenue_event'] ?? 0,
                        'cover_data' => $data['cover_data'] ?? [],
                        'upselling_data' => $data['upselling_data'] ?? [],
                        'competitor_data' => $data['competitor_data'] ?? [],
                        'additional_data' => $data['additional_data'] ?? [],
                        'thematic' => $data['thematic'] ?? null,
                        'staff_on_duty' => $data['staff_on_duty'] ?? null,
                        'remarks' => $data['remarks'] ?? null,
                        'vip_remarks' => $data['vip_remarks'] ?? null,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('daily-reports.index')
                ->with('success', 'Laporan berhasil disimpan sebagai ' . ucfirst($status));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(DailyReport $dailyReport)
    {
        $canEdit = $dailyReport->status === 'draft' || ($dailyReport->status === 'approved' && Auth::user()->hasRole('Super Admin'));

        if (!$canEdit) {
            return redirect()->route('daily-reports.index')
                ->with('error', 'Laporan yang sudah disubmit atau diapprove tidak dapat diedit (kecuali oleh Super Admin).');
        }

        $dailyReport->load(['details', 'restaurant']);
        $user = Auth::user();

        if ($user->hasRole('Super Admin')) {
            $restaurants = Restaurant::with(['users', 'employees'])->get();
        } else {
            $restaurants = $user->restaurants()->with(['users', 'employees'])->get();
        }

        $details = $dailyReport->details->keyBy('session_type');
        $upsellingItems = UpsellingItem::all()->groupBy('restaurant_id');

        return view('daily-reports.edit', compact('dailyReport', 'restaurants', 'details', 'upsellingItems'));
    }

    public function update(Request $request, DailyReport $dailyReport)
    {
        $canEdit = $dailyReport->status === 'draft' || ($dailyReport->status === 'approved' && Auth::user()->hasRole('Super Admin'));

        if (!$canEdit) {
            return back()->with('error', 'Hanya laporan Draft yang bisa diupdate (kecuali oleh Super Admin).');
        }

        $request->validate([
            'date' => 'required|date',
        ]);

        $rawSessions = $request->input('session', []);
        $inputSessions = $this->sanitizeSessionData($rawSessions);
        $request->merge(['session' => $inputSessions]);

        $this->validateSubmission($request, $dailyReport->restaurant_id);

        try {
            DB::beginTransaction();

            $status = 'draft';
            $approvedBy = null;
            $approvedAt = null;

            if ($request->input('action') === 'submit') {
                $user = Auth::user();
                if ($this->isUserApprover($user)) {
                    $status = 'approved';
                    $approvedBy = $user->id;
                    $approvedAt = now();
                } else {
                    $status = 'submitted';
                }
            }

            $dailyReport->update([
                'date' => $request->date,
                'status' => $status,
                'approved_by' => $approvedBy,
                'approved_at' => $approvedAt,
            ]);

            $dailyReport->details()->delete();

            $sessions = ['breakfast', 'lunch', 'dinner', 'supper'];

            foreach ($sessions as $sessionType) {
                if (isset($inputSessions[$sessionType])) {
                    $data = $inputSessions[$sessionType];

                    DailyReportDetail::create([
                        'daily_report_id' => $dailyReport->id,
                        'session_type' => $sessionType,
                        'revenue_food' => $data['revenue_food'] ?? 0,
                        'revenue_beverage' => $data['revenue_beverage'] ?? 0,
                        'revenue_others' => $data['revenue_others'] ?? 0,
                        'revenue_event' => $data['revenue_event'] ?? 0,
                        'cover_data' => $data['cover_data'] ?? [],
                        'upselling_data' => $data['upselling_data'] ?? [],
                        'competitor_data' => $data['competitor_data'] ?? [],
                        'additional_data' => $data['additional_data'] ?? [],
                        'thematic' => $data['thematic'] ?? null,
                        'staff_on_duty' => $data['staff_on_duty'] ?? null,
                        'remarks' => $data['remarks'] ?? null,
                        'vip_remarks' => $data['vip_remarks'] ?? null,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('daily-reports.index')
                ->with('success', 'Laporan berhasil diperbarui status menjadi: ' . ucfirst($status));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Update gagal: ' . $e->getMessage())->withInput();
        }
    }

    public function show(DailyReport $dailyReport)
    {
        $dailyReport->load(['details', 'restaurant', 'user']);
        return view('daily-reports.show', compact('dailyReport'));
    }

    public function destroy(DailyReport $dailyReport)
    {
        $user = Auth::user();

        if (!$user->hasRole(['Super Admin', 'Restaurant Manager'])) {
            abort(403, 'Unauthorized. Only Managers can delete reports.');
        }

        $dailyReport->delete();
        return back()->with('success', 'Laporan berhasil dihapus.');
    }

    public function approve(DailyReport $dailyReport)
    {
        if (!$this->isUserApprover(Auth::user())) {
            abort(403, 'Unauthorized action. Only Managers can approve reports.');
        }

        if ($dailyReport->status !== 'submitted') {
            return back()->with('error', 'Hanya laporan dengan status Submitted yang bisa disetujui.');
        }

        $dailyReport->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Laporan berhasil disetujui (Approved).');
    }

    public function reject(DailyReport $dailyReport)
    {
        if (!$this->isUserApprover(Auth::user())) {
            abort(403, 'Unauthorized action. Only Managers can reject reports.');
        }

        if ($dailyReport->status === 'draft') {
            return back()->with('error', 'Laporan status Draft tidak perlu di-reject.');
        }

        $dailyReport->update([
            'status' => 'draft',
            'approved_by' => null,
            'approved_at' => null,
        ]);

        return back()->with('success', 'Laporan ditolak dan status kembali menjadi Draft. User dapat mengeditnya sekarang.');
    }

    public function downloadPdf(DailyReport $dailyReport)
    {
        if ($dailyReport->status !== 'approved') {
            return back()->with('error', 'Hanya laporan yang sudah disetujui (Approved) yang dapat diunduh.');
        }

        $dailyReport->load(['details', 'restaurant', 'user', 'approver']);
        $pdf = Pdf::loadView('daily-reports.pdf', compact('dailyReport'));
        $pdf->setPaper('a4', 'portrait');

        $filename = 'Report_' . str_replace(' ', '', $dailyReport->restaurant->code) . '_' . $dailyReport->date->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }

    private function validateSubmission(Request $request, $restaurantId = null)
    {
        if ($request->input('action') === 'draft') {
            return;
        }

        $isBqt = false;
        if ($restaurantId) {
            $restaurant = \App\Models\Restaurant::find($restaurantId);
            if ($restaurant && $restaurant->code === 'BQT') {
                $isBqt = true;
            }
        }

        $rules = [
            'session' => 'required|array',
            'session.*.cover_data' => 'required|array',
            'session.*.revenue_event' => 'required|numeric|min:0',
            'session.*.remarks' => 'required|string',
            'session.*.staff_on_duty' => 'required|array|min:1',
            'session.*.competitor_data' => 'required|array',
        ];

        if (!$isBqt) {
            $rules['session.*.revenue_food'] = 'required|numeric|min:0';
            $rules['session.*.revenue_beverage'] = 'required|numeric|min:0';
            $rules['session.*.revenue_others'] = 'required|numeric|min:0';
        } else {
            $rules['session.*.revenue_food'] = 'nullable|numeric|min:0';
            $rules['session.*.revenue_beverage'] = 'nullable|numeric|min:0';
            $rules['session.*.revenue_others'] = 'nullable|numeric|min:0';
        }

        $messages = [
            'session.*.cover_data.required' => 'Cover Report details wajib diisi.',
            'session.*.revenue_food.required' => 'Food Revenue wajib diisi (isi 0 jika tidak ada).',
            'session.*.revenue_beverage.required' => 'Beverage Revenue wajib diisi (isi 0 jika tidak ada).',
            'session.*.remarks.required' => 'General Remarks wajib diisi.',
            'session.*.staff_on_duty.min' => 'Staff on Duty wajib dipilih minimal 1 orang.',
            'session.*.competitor_data.required' => 'Competitor Comparison wajib diisi.',
        ];

        $request->validate($rules, $messages);
    }

    private function sanitizeSessionData($inputSessions)
    {
        if (!$inputSessions) return [];

        $cleanedSessions = [];
        foreach ($inputSessions as $session => $data) {
            $cleanedData = $data;

            $moneyFields = ['revenue_food', 'revenue_beverage', 'revenue_others', 'revenue_event'];
            foreach ($moneyFields as $field) {
                if (isset($cleanedData[$field])) {
                    $cleanedData[$field] = str_replace('.', '', $cleanedData[$field]);
                }
            }

            $jsonFields = ['staff_on_duty', 'upselling_data', 'vip_remarks'];
            foreach ($jsonFields as $field) {
                if (isset($cleanedData[$field]) && is_string($cleanedData[$field])) {
                    $decoded = json_decode($cleanedData[$field], true);
                    $cleanedData[$field] = $decoded ?: [];
                }
            }

            if (isset($cleanedData['additional_data']['thematic_revenue'])) {
                $cleanedData['additional_data']['thematic_revenue'] = str_replace('.', '', $cleanedData['additional_data']['thematic_revenue']);
            }

            $cleanedSessions[$session] = $cleanedData;
        }

        return $cleanedSessions;
    }

    private function isUserApprover($user)
    {
        // Pengecekan role menggunakan array pada hasAnyRole/hasRole (Spatie Native)
        return $user->hasAnyRole([
            'Super Admin',
            'Restaurant Manager',
        ]);
    }

    public function exportExcel(Request $request)
    {
        $exportAllDates = $request->has('export_all_dates');

        // Conditional validation based on checkbox
        $rules = [
            'restaurant_id' => 'nullable|exists:restaurants,id',
        ];

        if (!$exportAllDates) {
            $rules['start_date'] = 'required|date';
            $rules['end_date'] = 'required|date|after_or_equal:start_date';
        }

        $request->validate($rules);

        $user = Auth::user();
        $restaurantId = $request->restaurant_id;
        $restaurantIds = null;

        if ($user->hasRole('Super Admin')) {
            // Super Admin: export selected restaurant or all restaurants
            $restaurantIds = $restaurantId ? $restaurantId : null;
        } else {
            // Non-Super Admin: export selected restaurant or all their restaurants
            $userRestaurantIds = $user->restaurants->pluck('id')->toArray();

            if ($restaurantId) {
                // Validate user has access to selected restaurant
                if (!in_array($restaurantId, $userRestaurantIds)) {
                    abort(403, 'Unauthorized: You do not have access to this restaurant.');
                }
                $restaurantIds = $restaurantId;
            } else {
                // Export all restaurants user has access to
                $restaurantIds = $userRestaurantIds;
            }
        }

        try {
            $exporter = new DailyReportsExport(
                $exportAllDates ? null : $request->start_date,
                $exportAllDates ? null : $request->end_date,
                $restaurantIds,
                $exportAllDates
            );

            $result = $exporter->export();

            return response()->download($result['file'], $result['filename'])->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return back()->with('error', 'Export failed: ' . $e->getMessage());
        }
    }

    public function exportPdfBulk(Request $request)
    {
        $exportAllDates = $request->has('export_all_dates_pdf');

        $rules = [
            'restaurant_id' => 'nullable|exists:restaurants,id',
        ];

        if (!$exportAllDates) {
            $rules['start_date_pdf'] = 'required|date';
            $rules['end_date_pdf'] = 'required|date|after_or_equal:start_date_pdf';
        }

        $request->validate($rules);

        $user = Auth::user();
        $restaurantId = $request->restaurant_id;

        $query = DailyReport::with(['restaurant', 'user', 'details', 'approver'])
            ->where('status', 'approved')
            ->orderBy('date', 'desc')
            ->orderBy('restaurant_id', 'asc');

        if (!$exportAllDates && $request->start_date_pdf && $request->end_date_pdf) {
            $query->whereBetween('date', [$request->start_date_pdf . ' 00:00:00', $request->end_date_pdf . ' 23:59:59']);
        }

        if ($user->hasRole('Super Admin')) {
            if ($restaurantId) {
                $query->where('restaurant_id', $restaurantId);
            }
        } else {
            $userRestaurantIds = $user->restaurants->pluck('id')->toArray();
            if ($restaurantId) {
                if (!in_array($restaurantId, $userRestaurantIds)) {
                    abort(403, 'Unauthorized: You do not have access to this restaurant.');
                }
                $query->where('restaurant_id', $restaurantId);
            } else {
                $query->whereIn('restaurant_id', $userRestaurantIds);
            }
        }

        $reports = $query->get();

        if ($reports->isEmpty()) {
            return back()->with('error', 'Tidak ada data laporan (Approved) pada rentang tanggal dan outlet tersebut.');
        }

        $pdf = Pdf::loadView('daily-reports.pdf-bulk', compact('reports'));
        $pdf->setPaper('a4', 'portrait');

        if ($exportAllDates) {
            $filename = 'Bulk_Report_All_Dates_' . date('Y-m-d_His') . '.pdf';
        } else {
            $filename = 'Bulk_Report_' . $request->start_date_pdf . '_to_' . $request->end_date_pdf . '.pdf';
        }

        return $pdf->download($filename);
    }
}
