@extends('layouts.portal')

@section('title', 'Daily Reports')
@section('breadcrumb', 'Daily Reports')

@section('header')
    {{-- JavaScript for Export Modal --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const exportAllCheckbox = document.getElementById('export_all_dates');
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');
            const startRequired = document.getElementById('start_required');
            const endRequired = document.getElementById('end_required');

            if (exportAllCheckbox) {
                exportAllCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        startDateInput.disabled = true;
                        endDateInput.disabled = true;
                        startDateInput.removeAttribute('required');
                        endDateInput.removeAttribute('required');
                        startRequired.style.display = 'none';
                        endRequired.style.display = 'none';
                        startDateInput.value = '';
                        endDateInput.value = '';
                        startDateInput.style.backgroundColor = '#f8fafc';
                        endDateInput.style.backgroundColor = '#f8fafc';
                    } else {
                        startDateInput.disabled = false;
                        endDateInput.disabled = false;
                        startDateInput.setAttribute('required', 'required');
                        endDateInput.setAttribute('required', 'required');
                        startRequired.style.display = 'inline';
                        endRequired.style.display = 'inline';
                        startDateInput.value = '{{ now()->subDays(30)->format('Y-m-d') }}';
                        endDateInput.value = '{{ now()->format('Y-m-d') }}';
                        startDateInput.style.backgroundColor = '';
                        endDateInput.style.backgroundColor = '';
                    }
                });
            }

            // For PDF Modal
            const exportAllCheckboxPdf = document.getElementById('export_all_dates_pdf');
            const startDateInputPdf = document.getElementById('start_date_pdf');
            const endDateInputPdf = document.getElementById('end_date_pdf');
            const startRequiredPdf = document.getElementById('start_required_pdf');
            const endRequiredPdf = document.getElementById('end_required_pdf');

            if (exportAllCheckboxPdf) {
                exportAllCheckboxPdf.addEventListener('change', function() {
                    if (this.checked) {
                        startDateInputPdf.disabled = true;
                        endDateInputPdf.disabled = true;
                        startDateInputPdf.removeAttribute('required');
                        endDateInputPdf.removeAttribute('required');
                        startRequiredPdf.style.display = 'none';
                        endRequiredPdf.style.display = 'none';
                        startDateInputPdf.value = '';
                        endDateInputPdf.value = '';
                        startDateInputPdf.style.backgroundColor = '#f8fafc';
                        endDateInputPdf.style.backgroundColor = '#f8fafc';
                    } else {
                        startDateInputPdf.disabled = false;
                        endDateInputPdf.disabled = false;
                        startDateInputPdf.setAttribute('required', 'required');
                        endDateInputPdf.setAttribute('required', 'required');
                        startRequiredPdf.style.display = 'inline';
                        endRequiredPdf.style.display = 'inline';
                        startDateInputPdf.value = '{{ now()->subDays(30)->format('Y-m-d') }}';
                        endDateInputPdf.value = '{{ now()->format('Y-m-d') }}';
                        startDateInputPdf.style.backgroundColor = '';
                        endDateInputPdf.style.backgroundColor = '';
                    }
                });
            }
        });
    </script>

    <!-- EXECUTIVE PAGE HEADER -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white border border-slate-200/90 rounded-2xl p-5 md:p-6 shadow-sm mb-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-[#172a40] text-white flex items-center justify-center text-2xl shadow-sm shrink-0">
                <i class="ti ti-tools-kitchen-2"></i>
            </div>
            <div>
                <h1 class="text-lg md:text-xl font-bold text-slate-800 tracking-tight mb-0.5">Daily Reports - Restaurants</h1>
                <p class="text-xs text-slate-500 mb-0">Kelola, tinjau, dan ekspor laporan harian seluruh restoran & outlet.</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2 self-start md:self-auto">
            {{-- TOMBOL EXPORT EXCEL --}}
            <button type="button" class="btn btn-sm btn-outline-success rounded-xl px-3.5 py-2 text-xs font-semibold d-inline-flex align-items-center gap-1.5 shadow-sm" data-bs-toggle="modal" data-bs-target="#exportModal">
                <i class="ti ti-file-spreadsheet text-base"></i> Export Excel
            </button>
            {{-- TOMBOL EXPORT PDF --}}
            <button type="button" class="btn btn-sm btn-outline-danger rounded-xl px-3.5 py-2 text-xs font-semibold d-inline-flex align-items-center gap-1.5 shadow-sm" data-bs-toggle="modal" data-bs-target="#exportPdfModal">
                <i class="ti ti-file-type-pdf text-base"></i> Export PDF
            </button>
            {{-- TOMBOL BUAT LAPORAN BARU --}}
            <a href="{{ route('daily-reports.create') }}" class="btn btn-sm btn-primary rounded-xl px-4 py-2 text-xs font-semibold d-inline-flex align-items-center gap-1.5 shadow-sm">
                <i class="ti ti-plus text-base"></i> Create Report
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="row g-4">
        <div class="col-12">

            {{-- Filter Form Card --}}
            <div class="card border border-slate-200/90 rounded-2xl shadow-sm bg-white mb-4">
                <div class="card-body p-4">
                    <form action="{{ route('daily-reports.index') }}" method="GET" class="row g-3 align-items-end">
                        <div class="col-12 col-sm-6 col-md-3">
                            <label for="filter_start_date" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">
                                <i class="ti ti-calendar me-1 text-slate-400"></i> Start Date
                            </label>
                            <input type="date" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2 text-xs text-slate-800 focus:outline-none focus:border-[#172a40] focus:ring-2 focus:ring-[#172a40]/10 transition" id="filter_start_date" name="start_date" 
                                   value="{{ request('start_date') }}">
                        </div>
                        <div class="col-12 col-sm-6 col-md-3">
                            <label for="filter_end_date" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">
                                <i class="ti ti-calendar me-1 text-slate-400"></i> End Date
                            </label>
                            <input type="date" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2 text-xs text-slate-800 focus:outline-none focus:border-[#172a40] focus:ring-2 focus:ring-[#172a40]/10 transition" id="filter_end_date" name="end_date" 
                                   value="{{ request('end_date') }}">
                        </div>
                        <div class="col-12 col-sm-8 col-md-4">
                            <label for="filter_restaurant" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">
                                <i class="ti ti-building-store me-1 text-slate-400"></i> Restaurant
                            </label>
                            <select class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2 text-xs text-slate-800 focus:outline-none focus:border-[#172a40] focus:ring-2 focus:ring-[#172a40]/10 transition" id="filter_restaurant" name="restaurant_id">
                                <option value="">Semua Restoran (All Restaurants)</option>
                                @foreach($restaurants as $restaurant)
                                    <option value="{{ $restaurant->id }}" 
                                        {{ request('restaurant_id') == $restaurant->id ? 'selected' : '' }}>
                                        {{ $restaurant->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-sm-4 col-md-2 d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-100 rounded-xl text-xs font-semibold py-2 d-inline-flex align-items-center justify-center gap-1 shadow-sm">
                                <i class="ti ti-filter"></i> Filter
                            </button>
                            @if(request()->hasAny(['start_date', 'end_date', 'restaurant_id']))
                                <a href="{{ route('daily-reports.index') }}" class="btn btn-outline-secondary rounded-xl text-xs font-semibold py-2 px-3 d-inline-flex align-items-center justify-center" title="Reset Filter">
                                    <i class="ti ti-x"></i>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            {{-- Table Card --}}
            <div class="card border border-slate-200/90 rounded-2xl shadow-sm bg-white overflow-hidden">
                <div class="p-4 md:p-5 border-b border-slate-100 d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-0.5">
                            <i class="ti ti-list me-1.5 text-[#172a40]"></i> Daftar Laporan Harian
                        </h2>
                        <p class="text-xs text-slate-500 mb-0">Menampilkan {{ $reports->total() }} laporan yang tersimpan dalam sistem.</p>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                    <th class="ps-4 py-3">Tanggal Laporan</th>
                                    <th class="py-3">Waktu Input</th>
                                    <th class="py-3">Restoran / Outlet</th>
                                    <th class="py-3">Dibuat Oleh</th>
                                    <th class="py-3">Status</th>
                                    <th class="text-end pe-4 py-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-xs">
                                @forelse($reports as $report)
                                    <tr>
                                        <td class="ps-4 py-3">
                                            <div class="font-bold text-slate-800">{{ $report->date->format('d M Y') }}</div>
                                            <div class="text-[11px] text-slate-400">{{ $report->date->format('H:i') }} WIB</div>
                                        </td>
                                        <td class="py-3">
                                            <div class="font-medium text-slate-700">{{ $report->created_at->format('d M Y') }}</div>
                                            <div class="text-[11px] text-slate-400">{{ $report->created_at->format('H:i') }} WIB</div>
                                        </td>
                                        <td class="py-3">
                                            <span class="font-bold text-slate-800">{{ $report->restaurant->name }}</span>
                                            <span class="ms-1 px-1.5 py-0.2 rounded bg-slate-100 text-slate-500 font-mono text-[10px]">{{ $report->restaurant->code }}</span>
                                        </td>
                                        <td class="py-3">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="w-7 h-7 rounded-full bg-[#172a40] text-white flex items-center justify-center font-bold text-[10px]">
                                                    {{ strtoupper(substr($report->user->name ?? 'U', 0, 2)) }}
                                                </div>
                                                <span class="font-medium text-slate-700">{{ $report->user->name }}</span>
                                            </div>
                                        </td>
                                        <td class="py-3">
                                            @if ($report->status == 'approved')
                                                <span class="badge bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full px-2.5 py-1 font-semibold text-[11px]">
                                                    <i class="ti ti-check me-0.5"></i> Approved
                                                </span>
                                            @elseif($report->status == 'submitted')
                                                <span class="badge bg-blue-50 text-blue-700 border border-blue-200 rounded-full px-2.5 py-1 font-semibold text-[11px]">
                                                    <i class="ti ti-clock me-0.5"></i> Submitted
                                                </span>
                                            @else
                                                <span class="badge bg-amber-50 text-amber-700 border border-amber-200 rounded-full px-2.5 py-1 font-semibold text-[11px]">
                                                    <i class="ti ti-pencil me-0.5"></i> Draft
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-4 py-3">
                                            <div class="d-inline-flex align-items-center gap-1">
                                                {{-- Link ke Detail (Show) --}}
                                                <a href="{{ route('daily-reports.show', $report->id) }}"
                                                    class="btn btn-sm btn-outline-secondary rounded-lg p-1.5 text-slate-600 hover:text-[#172a40] transition"
                                                    title="Lihat Detail">
                                                    <i class="ti ti-eye text-sm"></i>
                                                </a>

                                                {{-- Tombol Edit (Draft, atau Approved khusus Super Admin) --}}
                                                @if ($report->status == 'draft' || ($report->status == 'approved' && auth()->user()->hasRole('Super Admin')))
                                                    <a href="{{ route('daily-reports.edit', $report->id) }}"
                                                        class="btn btn-sm btn-outline-warning rounded-lg p-1.5 text-amber-600 hover:bg-amber-50 transition"
                                                        title="Edit Laporan">
                                                        <i class="ti ti-pencil text-sm"></i>
                                                    </a>
                                                @endif

                                                {{-- TOMBOL HAPUS (Hanya Manager & Super Admin) --}}
                                                @hasanyrole('Super Admin|Restaurant Manager')
                                                    <form action="{{ route('daily-reports.destroy', $report->id) }}" method="POST"
                                                        class="d-inline-block">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-lg p-1.5 text-rose-600 hover:bg-rose-50 transition"
                                                            onclick="return confirm('Apakah Anda yakin ingin menghapus laporan tanggal {{ $report->date->format('d M Y') }} ini? Tindakan ini tidak bisa dibatalkan.')"
                                                            title="Hapus Laporan">
                                                            <i class="ti ti-trash text-sm"></i>
                                                        </button>
                                                    </form>
                                                @endhasanyrole
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-slate-400">
                                            <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center text-2xl mx-auto mb-2">
                                                <i class="ti ti-folder-off"></i>
                                            </div>
                                            <p class="text-xs mb-0">Tidak ada laporan yang ditemukan. Mulai dengan membuat laporan baru!</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if($reports->hasPages())
                        <div class="p-4 border-t border-slate-100">
                            {{ $reports->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Export to Excel --}}
    <div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0 rounded-2xl shadow-2xl overflow-hidden">
                <form action="{{ route('daily-reports.export') }}" method="POST">
                    @csrf
                    <div class="modal-header bg-[#172a40] text-white px-5 py-4 border-b border-[#0f1c2b] relative overflow-hidden">
                        <div class="absolute top-0 left-0 right-0 h-1 bg-[#10b981]"></div>
                        <h5 class="modal-title font-bold text-white text-sm d-flex align-items-center gap-2" id="exportModalLabel">
                            <i class="ti ti-file-spreadsheet text-emerald-400 text-base"></i> Export Daily Reports to Excel
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4 md:p-5">
                        <div class="p-3 bg-emerald-50/70 border border-emerald-200 rounded-xl mb-3.5">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="export_all_dates"
                                    name="export_all_dates" value="1">
                                <label class="form-check-label font-bold text-emerald-800 text-xs" for="export_all_dates">
                                    <i class="ti ti-database me-1"></i> Export Semua Data Historis (Tanpa Filter Tanggal)
                                </label>
                            </div>
                            <small class="text-emerald-700/80 block mt-1 text-[11px]">Centang opsi ini untuk mengunduh seluruh data laporan dari awal tanpa batasan tanggal.</small>
                        </div>

                        <div class="mb-3">
                            <label for="start_date" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">
                                Start Date <span class="text-danger" id="start_required">*</span>
                            </label>
                            <input type="date" class="form-control rounded-xl text-xs" id="start_date" name="start_date"
                                value="{{ now()->subDays(30)->format('Y-m-d') }}" required>
                            <small class="text-slate-400 text-[11px]">Laporan dimulai dari tanggal ini.</small>
                        </div>

                        <div class="mb-3">
                            <label for="end_date" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">
                                End Date <span class="text-danger" id="end_required">*</span>
                            </label>
                            <input type="date" class="form-control rounded-xl text-xs" id="end_date" name="end_date"
                                value="{{ now()->format('Y-m-d') }}" required>
                            <small class="text-slate-400 text-[11px]">Laporan sampai dengan tanggal ini.</small>
                        </div>

                        <div class="mb-3">
                            <label for="restaurant_id" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Restaurant</label>
                            <select class="form-select rounded-xl text-xs" id="restaurant_id" name="restaurant_id">
                                @hasrole('Super Admin')
                                    <option value="">Semua Restoran (All Restaurants)</option>
                                @else
                                    <option value="">Semua Restoran yang Diakses (All My Restaurants)</option>
                                @endhasrole
                                @foreach ($restaurants as $restaurant)
                                    <option value="{{ $restaurant->id }}">{{ $restaurant->name }}</option>
                                @endforeach
                            </select>
                            <small class="text-slate-400 text-[11px]">
                                Kosongkan pilihan jika ingin mengunduh semua restoran sekaligus.
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer bg-slate-50 border-t border-slate-100 px-5 py-3">
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-xl px-4 py-2 text-xs font-semibold" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-sm btn-success rounded-xl px-4 py-2 text-xs font-semibold d-inline-flex align-items-center gap-1.5 shadow-sm">
                            <i class="ti ti-download"></i> Unduh File Excel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Bulk Export PDF --}}
    <div class="modal fade" id="exportPdfModal" tabindex="-1" aria-labelledby="exportPdfModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0 rounded-2xl shadow-2xl overflow-hidden">
                <form action="{{ route('daily-reports.export-pdf') }}" method="POST">
                    @csrf
                    <div class="modal-header bg-[#172a40] text-white px-5 py-4 border-b border-[#0f1c2b] relative overflow-hidden">
                        <div class="absolute top-0 left-0 right-0 h-1 bg-[#ef4444]"></div>
                        <h5 class="modal-title font-bold text-white text-sm d-flex align-items-center gap-2" id="exportPdfModalLabel">
                            <i class="ti ti-file-type-pdf text-rose-400 text-base"></i> Bulk Export Daily Reports to PDF
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4 md:p-5">
                        <div class="mb-3">
                            <label for="start_date_pdf" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">
                                Start Date <span class="text-danger" id="start_required_pdf">*</span>
                            </label>
                            <input type="date" class="form-control rounded-xl text-xs" id="start_date_pdf" name="start_date_pdf"
                                value="{{ now()->subDays(30)->format('Y-m-d') }}" required>
                            <small class="text-slate-400 text-[11px]">Laporan dimulai dari tanggal ini.</small>
                        </div>

                        <div class="mb-3">
                            <label for="end_date_pdf" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">
                                End Date <span class="text-danger" id="end_required_pdf">*</span>
                            </label>
                            <input type="date" class="form-control rounded-xl text-xs" id="end_date_pdf" name="end_date_pdf"
                                value="{{ now()->format('Y-m-d') }}" required>
                            <small class="text-slate-400 text-[11px]">Laporan sampai dengan tanggal ini.</small>
                        </div>

                        <div class="mb-3">
                            <label for="restaurant_id_pdf" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Restaurant</label>
                            <select class="form-select rounded-xl text-xs" id="restaurant_id_pdf" name="restaurant_id">
                                @hasrole('Super Admin')
                                    <option value="">Semua Restoran (All Restaurants)</option>
                                @else
                                    <option value="">Semua Restoran yang Diakses (All My Restaurants)</option>
                                @endhasrole
                                @foreach ($restaurants as $restaurant)
                                    <option value="{{ $restaurant->id }}">{{ $restaurant->name }}</option>
                                @endforeach
                            </select>
                            <small class="text-slate-400 text-[11px]">
                                Kosongkan pilihan jika ingin mengunduh semua restoran sekaligus.
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer bg-slate-50 border-t border-slate-100 px-5 py-3">
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-xl px-4 py-2 text-xs font-semibold" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-sm btn-danger rounded-xl px-4 py-2 text-xs font-semibold d-inline-flex align-items-center gap-1.5 shadow-sm">
                            <i class="ti ti-download"></i> Unduh File PDF
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
