@extends('layouts.portal')

@section('title', 'Master Menu Items - Tanly Hospitality')
@section('breadcrumb', 'Master Menu')

@section('content')
<div class="space-y-6">

    {{-- 1. EXECUTIVE HEADER BANNER --}}
    <div class="rounded-2xl border border-slate-200/90 shadow-sm bg-white p-5 md:p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 shadow-sm" style="background-color: #172a40; color: #B89047;">
                    <i class="ti ti-tools-kitchen-2 text-2xl"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold tracking-tight text-slate-800">Master Menu & Upselling Items</h1>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-sky-50 text-sky-800 border border-sky-200">
                            {{ $items->total() }} Total Menu
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">Katalog item menu makanan & minuman upselling untuk pelaporan operasional harian outlet</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('upselling-items.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-sm text-white bg-[#172a40] hover:bg-[#112031] transition shadow-sm hover:shadow cursor-pointer">
                    <i class="ti ti-plus text-base"></i> Add New Menu
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

    {{-- MENU DIRECTORY CARD --}}
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden">
        {{-- Card Header with Filter --}}
        <div class="px-5 py-4 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3 bg-slate-50/40">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-[#172a40]/5 border border-[#172a40]/10 flex items-center justify-center text-[#172a40]">
                    <i class="ti ti-list text-base"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-800 tracking-tight">Menu Items Directory</h2>
                    <p class="text-[11px] text-slate-400 mb-0">Daftar item menu aktif per outlet</p>
                </div>
            </div>

            {{-- Filter Restoran --}}
            <form action="{{ route('upselling-items.index') }}" method="GET" class="flex items-center gap-2">
                <span class="text-xs font-medium text-slate-500">Filter Outlet:</span>
                <select name="restaurant_id" class="px-3 py-1.5 pr-8 bg-white border border-slate-300 rounded-xl text-xs font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all cursor-pointer"
                    onchange="this.form.submit()">
                    <option value="">-- All Restaurants --</option>
                    @foreach ($restaurants as $rest)
                        <option value="{{ $rest->id }}" {{ request('restaurant_id') == $rest->id ? 'selected' : '' }}>
                            {{ $rest->name }} ({{ $rest->code }})
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        {{-- Table Responsive --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200/80 bg-slate-50/70 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                        <th class="py-3 px-4 w-12 text-center">#</th>
                        <th class="py-3 px-4">Restaurant</th>
                        <th class="py-3 px-4 w-32">Type</th>
                        <th class="py-3 px-4">Menu Name</th>
                        <th class="py-3 px-4 text-right w-28">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($items as $index => $item)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            {{-- Index --}}
                            <td class="py-3.5 px-4 text-center font-semibold text-slate-400">
                                {{ $items->firstItem() + $index }}
                            </td>

                            {{-- Restaurant --}}
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-2.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-mono font-bold bg-[#172a40]/5 text-[#172a40] border border-[#172a40]/15 tracking-wider">
                                        {{ $item->restaurant->code ?? 'RES' }}
                                    </span>
                                    <span class="font-bold text-slate-800">{{ $item->restaurant->name }}</span>
                                </div>
                            </td>

                            {{-- Type --}}
                            <td class="py-3.5 px-4">
                                @if ($item->type == 'food')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                                        <i class="ti ti-meat text-[11px]"></i> Food
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-sky-50 text-sky-800 border border-sky-200">
                                        <i class="ti ti-glass-full text-[11px]"></i> Beverage
                                    </span>
                                @endif
                            </td>

                            {{-- Menu Name --}}
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-slate-800 text-sm">{{ $item->name }}</span>
                            </td>

                            {{-- Actions --}}
                            <td class="py-3.5 px-4 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('upselling-items.edit', $item->id) }}"
                                        class="w-8 h-8 rounded-lg bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200/60 flex items-center justify-center transition shadow-2xs"
                                        title="Edit Menu Item">
                                        <i class="ti ti-edit text-sm"></i>
                                    </a>

                                    <form action="{{ route('upselling-items.destroy', $item->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="w-8 h-8 rounded-lg bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200/60 flex items-center justify-center transition shadow-2xs cursor-pointer"
                                            title="Delete Menu Item"
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus menu {{ $item->name }}?')">
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
                                        <i class="ti ti-clipboard-off"></i>
                                    </div>
                                    <h6 class="text-sm font-bold text-slate-700 mb-1">Tidak Ada Menu Ditemukan</h6>
                                    <p class="text-xs text-slate-400 max-w-sm">Belum ada item menu upselling yang terdaftar untuk filter restoran yang dipilih.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination Footer --}}
        @if ($items->hasPages())
            <div class="px-5 py-3.5 border-t border-slate-100 bg-slate-50/30 flex items-center justify-between">
                <div class="text-xs text-slate-500">
                    Showing {{ $items->firstItem() }} to {{ $items->lastItem() }} of {{ $items->total() }} entries
                </div>
                <div>
                    {{ $items->withQueryString()->links() }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
