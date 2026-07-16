@extends('layouts.mantis')

@section('header')
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h5 class="m-b-10">Daily Reports - Restaurants</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item" aria-current="page">Restaurants</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

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
                        // Disable date inputs
                        startDateInput.disabled = true;
                        endDateInput.disabled = true;

                        // Remove required attribute
                        startDateInput.removeAttribute('required');
                        endDateInput.removeAttribute('required');

                        // Hide required asterisk
                        startRequired.style.display = 'none';
                        endRequired.style.display = 'none';

                        // Clear values (optional)
                        startDateInput.value = '';
                        endDateInput.value = '';

                        // Visual feedback
                        startDateInput.style.backgroundColor = '#f8f9fa';
                        endDateInput.style.backgroundColor = '#f8f9fa';
                    } else {
                        // Enable date inputs
                        startDateInput.disabled = false;
                        endDateInput.disabled = false;

                        // Add back required attribute
                        startDateInput.setAttribute('required', 'required');
                        endDateInput.setAttribute('required', 'required');

                        // Show required asterisk
                        startRequired.style.display = 'inline';
                        endRequired.style.display = 'inline';

                        // Restore default values
                        startDateInput.value = '{{ now()->subDays(30)->format('Y-m-d') }}';
                        endDateInput.value = '{{ now()->format('Y-m-d') }}';

                        // Remove visual feedback
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
                        startDateInputPdf.style.backgroundColor = '#f8f9fa';
                        endDateInputPdf.style.backgroundColor = '#f8f9fa';
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
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">

            {{-- Alert Messages --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Validation Error:</strong>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>Recent Reports</h5>
                    <div class="d-flex gap-2">
                        {{-- TOMBOL EXPORT TO EXCEL --}}
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#exportModal">
                            <i class="ti ti-file-spreadsheet"></i> Export to Excel
                        </button>
                        {{-- TOMBOL EXPORT TO PDF (BULK) --}}
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#exportPdfModal">
                            <i class="ti ti-file-type-pdf"></i> Bulk Export PDF
                        </button>
                        {{-- TOMBOL MENUJU HALAMAN CREATE --}}
                        <a href="{{ route('daily-reports.create') }}" class="btn btn-primary">
                            <i class="ti ti-plus"></i> Create New Report
                        </a>
                    </div>
                </div>
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Report Date</th>
                                    <th>Created At</th>
                                    <th>Restaurant</th>
                                    <th>Created By</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reports as $report)
                                    <tr>
                                        <td>
                                            <div class="fw-bold">{{ $report->date->format('d M Y') }}</div>
                                            <div class="small text-muted">{{ $report->date->format('H:i') }}</div>
                                        </td>
                                        <td>
                                            <div class="fw-bold">{{ $report->created_at->format('d M Y') }}</div>
                                            <div class="small text-muted">{{ $report->created_at->format('H:i') }}</div>
                                        </td>
                                        <td>{{ $report->restaurant->name }}</td>
                                        <td>{{ $report->user->name }}</td>
                                        <td>
                                            @if ($report->status == 'approved')
                                                <span class="badge bg-success">Approved</span>
                                            @elseif($report->status == 'submitted')
                                                <span class="badge bg-primary">Submitted</span>
                                            @else
                                                <span class="badge bg-warning text-dark">Draft</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{-- Link ke Detail (Show) --}}
                                            <a href="{{ route('daily-reports.show', $report->id) }}"
                                                class="btn btn-icon btn-link-secondary">
                                                <i class="ti ti-eye"></i>
                                            </a>

                                            {{-- Tombol Edit (Hanya Draft) --}}
                                            @if ($report->status == 'draft')
                                                <a href="{{ route('daily-reports.edit', $report->id) }}"
                                                    class="btn btn-icon btn-link-warning">
                                                    <i class="ti ti-pencil"></i>
                                                </a>
                                            @endif

                                            {{-- TOMBOL HAPUS (Hanya Manager & Super Admin) --}}
                                            @hasanyrole('Super Admin|Restaurant Manager')
                                                <form action="{{ route('daily-reports.destroy', $report->id) }}" method="POST"
                                                    class="d-inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-icon btn-link-danger"
                                                        onclick="return confirm('Apakah Anda yakin ingin menghapus laporan tanggal {{ $report->date->format('d M Y') }} ini? Tindakan ini tidak bisa dibatalkan.')">
                                                        <i class="ti ti-trash"></i>
                                                    </button>
                                                </form>
                                            @endhasanyrole
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">
                                            No reports found. Start by creating one!
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        {{-- Pagination --}}
                        <div class="mt-3">
                            {{ $reports->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Export to Excel --}}
    <div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('daily-reports.export') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="exportModalLabel">
                            <i class="ti ti-file-spreadsheet"></i> Export Daily Reports to Excel
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="export_all_dates" name="export_all_dates" value="1">
                                <label class="form-check-label fw-bold text-primary" for="export_all_dates">
                                    <i class="ti ti-database"></i> Export All Historical Data (No Date Filter)
                                </label>
                            </div>
                            <small class="text-muted">Check this to export all data from the beginning without date limitation</small>
                        </div>

                        <div class="mb-3">
                            <label for="start_date" class="form-label">Start Date <span class="text-danger" id="start_required">*</span></label>
                            <input type="date" class="form-control" id="start_date" name="start_date"
                                   value="{{ now()->subDays(30)->format('Y-m-d') }}" required>
                            <small class="text-muted">Reports from this date onwards</small>
                        </div>

                        <div class="mb-3">
                            <label for="end_date" class="form-label">End Date <span class="text-danger" id="end_required">*</span></label>
                            <input type="date" class="form-control" id="end_date" name="end_date"
                                   value="{{ now()->format('Y-m-d') }}" required>
                            <small class="text-muted">Reports up to this date</small>
                        </div>

                        <div class="mb-3">
                            <label for="restaurant_id" class="form-label">Restaurant</label>
                            <select class="form-select" id="restaurant_id" name="restaurant_id">
                                @hasrole('Super Admin')
                                    <option value="">All Restaurants</option>
                                @else
                                    <option value="">All My Restaurants</option>
                                @endhasrole
                                @foreach($restaurants as $restaurant)
                                    <option value="{{ $restaurant->id }}">{{ $restaurant->name }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">
                                @hasrole('Super Admin')
                                    Leave blank to export all restaurants
                                @else
                                    Leave blank to export all your accessible restaurants
                                @endhasrole
                            </small>
                        </div>

                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">
                            <i class="ti ti-download"></i> Export to Excel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Bulk Export PDF --}}
    <div class="modal fade" id="exportPdfModal" tabindex="-1" aria-labelledby="exportPdfModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('daily-reports.export-pdf') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="exportPdfModalLabel">
                            <i class="ti ti-file-type-pdf"></i> Bulk Export Daily Reports to PDF
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="export_all_dates_pdf" name="export_all_dates_pdf" value="1">
                                <label class="form-check-label fw-bold text-danger" for="export_all_dates_pdf">
                                    <i class="ti ti-database"></i> Export All Historical Data (No Date Filter)
                                </label>
                            </div>
                            <small class="text-muted">Check this to export all data from the beginning without date limitation</small>
                        </div>

                        <div class="mb-3">
                            <label for="start_date_pdf" class="form-label">Start Date <span class="text-danger" id="start_required_pdf">*</span></label>
                            <input type="date" class="form-control" id="start_date_pdf" name="start_date_pdf"
                                   value="{{ now()->subDays(30)->format('Y-m-d') }}" required>
                            <small class="text-muted">Reports from this date onwards</small>
                        </div>

                        <div class="mb-3">
                            <label for="end_date_pdf" class="form-label">End Date <span class="text-danger" id="end_required_pdf">*</span></label>
                            <input type="date" class="form-control" id="end_date_pdf" name="end_date_pdf"
                                   value="{{ now()->format('Y-m-d') }}" required>
                            <small class="text-muted">Reports up to this date</small>
                        </div>

                        <div class="mb-3">
                            <label for="restaurant_id_pdf" class="form-label">Restaurant</label>
                            <select class="form-select" id="restaurant_id_pdf" name="restaurant_id">
                                @hasrole('Super Admin')
                                    <option value="">All Restaurants</option>
                                @else
                                    <option value="">All My Restaurants</option>
                                @endhasrole
                                @foreach($restaurants as $restaurant)
                                    <option value="{{ $restaurant->id }}">{{ $restaurant->name }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">
                                @hasrole('Super Admin')
                                    Leave blank to export all restaurants
                                @else
                                    Leave blank to export all your accessible restaurants
                                @endhasrole
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="ti ti-download"></i> Export to PDF
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
