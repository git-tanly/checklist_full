@extends('layouts.portal')

@section('title', 'Master Restaurants - Tanly Hospitality')
@section('breadcrumb', 'Restaurants')

@section('content')
<div class="space-y-6">

    {{-- 1. EXECUTIVE HEADER BANNER --}}
    <div class="rounded-2xl border border-slate-200/90 shadow-sm bg-white p-5 md:p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 shadow-sm" style="background-color: #172a40; color: #B89047;">
                    <i class="ti ti-building-store text-2xl"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold tracking-tight text-slate-800">Master Restaurants & Outlets</h1>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-sky-50 text-sky-800 border border-sky-200">
                            {{ $restaurants->count() }} Outlets
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">Kelola daftar unit restoran dan outlet F&B di bawah naungan Tanly Hospitality</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('restaurants.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-sm text-white bg-[#172a40] hover:bg-[#112031] transition shadow-sm hover:shadow cursor-pointer">
                    <i class="ti ti-plus text-base"></i> Add New Restaurant
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

    {{-- RESTAURANT DIRECTORY CARD --}}
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden">
        {{-- Card Header --}}
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/40">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-[#172a40]/5 border border-[#172a40]/10 flex items-center justify-center text-[#172a40]">
                    <i class="ti ti-list text-base"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-800 tracking-tight">Active Outlets Directory</h2>
                    <p class="text-[11px] text-slate-400 mb-0">Daftar seluruh outlet yang terdaftar di sistem pelaporan</p>
                </div>
            </div>
        </div>

        {{-- Table Responsive --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200/80 bg-slate-50/70 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                        <th class="py-3 px-5 w-24">Code</th>
                        <th class="py-3 px-5">Restaurant Name</th>
                        <th class="py-3 px-5 text-right w-32">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse ($restaurants as $rest)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            {{-- Code --}}
                            <td class="py-4 px-5">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-mono font-bold bg-[#172a40]/5 text-[#172a40] border border-[#172a40]/15 tracking-wider">
                                    {{ $rest->code }}
                                </span>
                            </td>

                            {{-- Name --}}
                            <td class="py-4 px-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-slate-100 to-slate-200 border border-slate-300/80 flex items-center justify-center text-slate-700 font-bold text-xs shrink-0">
                                        <i class="ti ti-tools-kitchen-2 text-sm text-[#172a40]"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-800 text-sm">{{ $rest->name }}</div>
                                        <div class="text-[11px] text-slate-400">ID: #{{ $rest->id }}</div>
                                    </div>
                                </div>
                            </td>

                            {{-- Actions --}}
                            <td class="py-4 px-5 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('restaurants.edit', $rest->id) }}"
                                        class="w-8 h-8 rounded-lg bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200/60 flex items-center justify-center transition shadow-2xs"
                                        title="Edit Restaurant">
                                        <i class="ti ti-edit text-sm"></i>
                                    </a>

                                    <form action="{{ route('restaurants.destroy', $rest->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="w-8 h-8 rounded-lg bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200/60 flex items-center justify-center transition shadow-2xs cursor-pointer"
                                            title="Delete Restaurant"
                                            onclick="return confirm('Hapus restoran {{ $rest->name }}? PERINGATAN: Semua laporan harian terkait restoran ini akan ikut terhapus!')">
                                            <i class="ti ti-trash text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400 text-xl mb-3">
                                        <i class="ti ti-building-store"></i>
                                    </div>
                                    <h6 class="text-sm font-bold text-slate-700 mb-1">Belum Ada Outlet Terdaftar</h6>
                                    <p class="text-xs text-slate-400 max-w-sm">Klik tombol "Add New Restaurant" di atas untuk menambahkan outlet baru.</p>
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
