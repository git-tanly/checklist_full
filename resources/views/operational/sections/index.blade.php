@extends('layouts.portal')

@section('title', 'Checklist Sections - Tanly Hospitality')
@section('breadcrumb', 'Checklist Sections')

@section('content')
<div class="space-y-6">

    {{-- 1. EXECUTIVE HEADER BANNER --}}
    <div class="rounded-2xl border border-slate-200/90 shadow-sm bg-white p-5 md:p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 shadow-sm" style="background-color: #172a40; color: #B89047;">
                    <i class="ti ti-layout-grid text-2xl"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-xl font-bold tracking-tight text-slate-800">Checklist Sections</h1>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-sky-50 text-sky-800 border border-sky-200">
                            {{ $sections->total() }} Section
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">Kelola section checklist operasional untuk setiap outlet.</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('operational.sections.create', $restaurantId ? ['restaurant_id' => $restaurantId] : []) }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-sm text-white bg-[#172a40] hover:bg-[#112031] transition shadow-sm hover:shadow cursor-pointer">
                    <i class="ti ti-plus text-base"></i> Tambah Section
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

    @if (session('error'))
        <div class="rounded-2xl border border-rose-200 bg-rose-50/90 p-4 text-rose-800 shadow-sm flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-rose-100 flex items-center justify-center text-rose-700 shrink-0">
                    <i class="ti ti-alert-triangle text-lg"></i>
                </div>
                <div>
                    <h5 class="text-xs font-bold uppercase tracking-wider text-rose-900 mb-0.5">Gagal</h5>
                    <p class="text-xs font-medium text-rose-800 mb-0">{{ session('error') }}</p>
                </div>
            </div>
            <button type="button" class="btn-close text-xs text-rose-700" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- FILTER CARD --}}
    <div class="card border border-slate-200/90 rounded-2xl shadow-sm bg-white">
        <div class="card-body p-4">
            <form action="{{ route('operational.sections.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-12 col-sm-8 col-md-4">
                    <label for="filter_restaurant" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">
                        <i class="ti ti-building-store me-1 text-slate-400"></i> Outlet
                    </label>
                    <select id="filter_restaurant" name="restaurant_id"
                        class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2 text-xs text-slate-800 focus:outline-none focus:border-[#172a40] focus:ring-2 focus:ring-[#172a40]/10 transition">
                        <option value="">Semua Outlet</option>
                        @foreach ($restaurants as $restaurant)
                            <option value="{{ $restaurant->id }}" {{ $restaurantId === $restaurant->id ? 'selected' : '' }}>
                                {{ $restaurant->code }} — {{ $restaurant->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-sm-4 col-md-3 d-flex gap-2">
                    <button type="submit"
                        class="inline-flex items-center justify-center gap-1.5 w-full px-4 py-2 rounded-xl text-xs font-semibold text-white bg-[#172a40] hover:bg-[#112031] transition shadow-sm">
                        <i class="ti ti-filter"></i> Filter
                    </button>
                    @if ($restaurantId)
                        <a href="{{ route('operational.sections.index') }}"
                            class="inline-flex items-center justify-center px-3 py-2 rounded-xl text-xs font-semibold text-slate-600 bg-white border border-slate-300 hover:bg-slate-50 transition"
                            title="Reset Filter">
                            <i class="ti ti-x"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- SECTION DIRECTORY CARD --}}
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/40">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-[#172a40]/5 border border-[#172a40]/10 flex items-center justify-center text-[#172a40]">
                    <i class="ti ti-list text-base"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-800 tracking-tight">Daftar Section Checklist</h2>
                    <p class="text-[11px] text-slate-400 mb-0">Menampilkan {{ $sections->total() }} section sesuai filter outlet</p>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200/80 bg-slate-50/70 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                        <th class="py-3 px-4 w-14 text-center">No</th>
                        <th class="py-3 px-4">Section</th>
                        <th class="py-3 px-4">Outlet</th>
                        <th class="py-3 px-4 text-center">Pertanyaan</th>
                        <th class="py-3 px-4 text-center">Urutan</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right w-36">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse ($sections as $section)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            {{-- No --}}
                            <td class="py-3.5 px-4 text-center font-semibold text-slate-400">
                                {{ ($sections->currentPage() - 1) * $sections->perPage() + $loop->iteration }}
                            </td>

                            {{-- Section --}}
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-800 text-sm">{{ $section->name }}</div>
                                <div class="text-[11px] text-slate-400">ID: #{{ $section->id }}</div>
                            </td>

                            {{-- Outlet --}}
                            <td class="py-3.5 px-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                    <i class="ti ti-building-store text-slate-400 text-xs"></i>
                                    <strong>{{ $section->restaurant->code }}</strong> — {{ $section->restaurant->name }}
                                </span>
                            </td>

                            {{-- Pertanyaan --}}
                            <td class="py-3.5 px-4 text-center">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                    <i class="ti ti-list-check text-[11px]"></i> {{ $section->questions_count }}
                                </span>
                            </td>

                            {{-- Urutan --}}
                            <td class="py-3.5 px-4 text-center font-semibold text-slate-600">
                                {{ $section->sort_order }}
                            </td>

                            {{-- Status --}}
                            <td class="py-3.5 px-4 text-center">
                                @if ($section->is_active)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <i class="ti ti-circle-check text-[11px]"></i> Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                        <i class="ti ti-circle-minus text-[11px]"></i> Nonaktif
                                    </span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="py-3.5 px-4 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('operational.questions.create', ['section' => $section->id]) }}"
                                        class="w-8 h-8 rounded-lg bg-sky-50 text-sky-700 hover:bg-sky-100 border border-sky-200/60 flex items-center justify-center transition shadow-2xs"
                                        title="Kelola Pertanyaan">
                                        <i class="ti ti-list-check text-sm"></i>
                                    </a>

                                    <a href="{{ route('operational.sections.edit', $section->id) }}"
                                        class="w-8 h-8 rounded-lg bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200/60 flex items-center justify-center transition shadow-2xs"
                                        title="Edit Section">
                                        <i class="ti ti-edit text-sm"></i>
                                    </a>

                                    <form action="{{ route('operational.sections.destroy', $section->id) }}" method="POST"
                                        class="inline-block"
                                        onsubmit="return confirm('Hapus section {{ $section->name }}? Seluruh pertanyaan di dalamnya juga akan terhapus.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="w-8 h-8 rounded-lg bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200/60 flex items-center justify-center transition shadow-2xs cursor-pointer"
                                            title="Hapus Section">
                                            <i class="ti ti-trash text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400 text-xl mb-3">
                                        <i class="ti ti-layout-grid"></i>
                                    </div>
                                    <h6 class="text-sm font-bold text-slate-700 mb-1">Belum Ada Section</h6>
                                    <p class="text-xs text-slate-400 max-w-sm">
                                        {{ $restaurantId ? 'Outlet ini belum memiliki section checklist.' : 'Klik tombol "Tambah Section" untuk membuat section checklist pertama.' }}
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($sections->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $sections->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
