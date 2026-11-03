@extends('layouts.portal')

@section('title', 'Master Employees - Tanly Hospitality')
@section('breadcrumb', 'Employees')

@section('content')
<div class="space-y-6">

    {{-- 1. EXECUTIVE HEADER BANNER --}}
    <div class="rounded-2xl border border-slate-200/90 shadow-sm bg-white p-5 md:p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 shadow-sm" style="background-color: #172a40; color: #B89047;">
                    <i class="ti ti-id text-2xl"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold tracking-tight text-slate-800">Master Employees & Staff Roster</h1>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-sky-50 text-sky-800 border border-sky-200">
                            {{ $employees->count() }} Pegawai
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">Kelola staf operasional untuk pencatatan on-duty dan leaderboard upselling F&B</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('employees.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-sm text-white bg-[#172a40] hover:bg-[#112031] transition shadow-sm hover:shadow cursor-pointer">
                    <i class="ti ti-plus text-base"></i> Tambah Pegawai
                </a>
            </div>
        </div>
    </div>

    {{-- ALERTS --}}
    @if (session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50/90 p-4 text-emerald-800 shadow-sm flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-700 shrink-0">
                    <i class="ti ti-check text-lg"></i>
                </div>
                <div>
                    <h5 class="text-xs font-bold uppercase tracking-wider text-emerald-900 mb-0.5">Berhasil</h5>
                    <p class="text-xs font-medium text-emerald-800 mb-0">{{ session('success') }}</p>
                </div>
            </div>
            <button type="button" class="btn-close text-xs text-emerald-700" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- EMPLOYEE DIRECTORY CARD --}}
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden">
        {{-- Card Header --}}
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/40">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-[#172a40]/5 border border-[#172a40]/10 flex items-center justify-center text-[#172a40]">
                    <i class="ti ti-list text-base"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-800 tracking-tight">Active Employee Directory</h2>
                    <p class="text-[11px] text-slate-400 mb-0">Daftar seluruh staf operasional outlet yang terdaftar</p>
                </div>
            </div>
        </div>

        {{-- Table Responsive --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200/80 bg-slate-50/70 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                        <th class="py-3 px-4 w-14 text-center">No</th>
                        <th class="py-3 px-4">Nama Pegawai</th>
                        <th class="py-3 px-4">Posisi / Jabatan</th>
                        <th class="py-3 px-4">Penugasan Outlet</th>
                        <th class="py-3 px-4 text-right w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse ($employees as $employee)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            {{-- No --}}
                            <td class="py-3.5 px-4 text-center font-semibold text-slate-400">
                                {{ $loop->iteration }}
                            </td>

                            {{-- Nama Pegawai --}}
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-[#172a40] to-slate-700 text-white font-bold text-xs flex items-center justify-center shadow-2xs shrink-0">
                                        {{ strtoupper(substr($employee->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-800 text-sm">{{ $employee->name }}</div>
                                        <div class="text-[11px] text-slate-400">ID: #{{ $employee->id }}</div>
                                    </div>
                                </div>
                            </td>

                            {{-- Posisi --}}
                            <td class="py-3.5 px-4">
                                @php
                                    $posClass = match($employee->position) {
                                        'Restaurant Manager', 'Assistant Restaurant Manager' => 'bg-amber-50 text-amber-800 border-amber-200',
                                        'F&B Supervisor' => 'bg-indigo-50 text-indigo-800 border-indigo-200',
                                        'Waiter', 'Cashier', 'Bartender' => 'bg-sky-50 text-sky-800 border-sky-200',
                                        default => 'bg-slate-100 text-slate-700 border-slate-200'
                                    };
                                @endphp
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $posClass }}">
                                    <i class="ti ti-badge text-[11px]"></i>
                                    {{ $employee->position }}
                                </span>
                            </td>

                            {{-- Restoran --}}
                            <td class="py-3.5 px-4">
                                @if($employee->restaurant)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                        <i class="ti ti-building-store text-slate-400 text-xs"></i>
                                        <strong>{{ $employee->restaurant->code }}</strong> — {{ $employee->restaurant->name }}
                                    </span>
                                @else
                                    <span class="text-slate-400 italic text-xs">-</span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="py-3.5 px-4 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('employees.edit', $employee->id) }}"
                                        class="w-8 h-8 rounded-lg bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200/60 flex items-center justify-center transition shadow-2xs"
                                        title="Edit Pegawai">
                                        <i class="ti ti-edit text-sm"></i>
                                    </a>

                                    <form action="{{ route('employees.destroy', $employee->id) }}" method="POST" class="inline-block"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus data pegawai {{ $employee->name }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="w-8 h-8 rounded-lg bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200/60 flex items-center justify-center transition shadow-2xs cursor-pointer"
                                            title="Hapus Pegawai">
                                            <i class="ti ti-trash text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400 text-xl mb-3">
                                        <i class="ti ti-users-minus"></i>
                                    </div>
                                    <h6 class="text-sm font-bold text-slate-700 mb-1">Belum Ada Data Pegawai</h6>
                                    <p class="text-xs text-slate-400 max-w-sm">Klik tombol "Tambah Pegawai" di atas untuk menambahkan data staf operasional.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
