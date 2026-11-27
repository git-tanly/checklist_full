@extends('layouts.portal')

@section('title', 'Edit Checklist Section - Tanly Hospitality')
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
                        <h1 class="text-xl font-bold tracking-tight text-slate-800">Edit Section</h1>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                            {{ $section->name }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        {{ $section->restaurant->code }} — {{ $section->restaurant->name }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('operational.sections.index', ['restaurant_id' => $section->restaurant_id]) }}"
                    class="btn btn-light rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-100 transition inline-flex items-center gap-1.5 shadow-sm">
                    <i class="ti ti-arrow-left text-base"></i> Back to List
                </a>
            </div>
        </div>
    </div>

    {{-- ERROR VALIDATION ALERT --}}
    @if ($errors->any())
        <div class="max-w-2xl mx-auto rounded-2xl border border-rose-200 bg-rose-50/90 p-4 text-rose-800 shadow-sm">
            <div class="flex items-start gap-3">
                <div class="w-9 h-9 rounded-xl bg-rose-100 flex items-center justify-center text-rose-700 shrink-0">
                    <i class="ti ti-alert-triangle text-lg"></i>
                </div>
                <div class="flex-1">
                    <h5 class="text-xs font-bold uppercase tracking-wider text-rose-900 mb-1">Terjadi Kesalahan</h5>
                    <ul class="text-xs space-y-1 list-disc list-inside text-rose-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {{-- FORM CARD --}}
    <div class="max-w-2xl mx-auto bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden">
        {{-- Card Header --}}
        <div class="bg-gradient-to-r from-slate-900 via-[#172a40] to-slate-800 text-white px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center text-[#B89047]">
                    <i class="ti ti-layout-grid text-base"></i>
                </span>
                <div>
                    <h2 class="text-sm font-bold text-white tracking-tight">Detail Section Checklist</h2>
                    <p class="text-[11px] text-slate-300 mb-0">Perbarui nama section, outlet, urutan tampil, dan status aktif</p>
                </div>
            </div>
            <span class="w-2 h-2 rounded-full bg-[#B89047]"></span>
        </div>

        <div class="p-6">
            <form action="{{ route('operational.sections.update', $section->id) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Outlet --}}
                <div class="mb-5">
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center gap-1.5">
                        <i class="ti ti-building-store text-slate-400"></i> Restoran / Outlet
                        <span class="text-rose-500">*</span>
                    </label>
                    <select name="restaurant_id" class="w-full px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all" required autofocus>
                        @foreach ($restaurants as $restaurant)
                            <option value="{{ $restaurant->id }}"
                                {{ (string) old('restaurant_id', $section->restaurant_id) === (string) $restaurant->id ? 'selected' : '' }}>
                                {{ $restaurant->name }} ({{ $restaurant->code }})
                            </option>
                        @endforeach
                    </select>
                    <p class="text-[11px] text-slate-400 mt-1.5 mb-0">Memindahkan section ke outlet lain akan memindahkan seluruh pertanyaannya.</p>
                </div>

                {{-- Nama Section --}}
                <div class="mb-5">
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center gap-1.5">
                        <i class="ti ti-tag text-slate-400"></i> Nama Section
                        <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name', $section->name) }}" maxlength="120" required
                        class="w-full px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all">
                </div>

                <div class="row g-4">
                    {{-- Urutan --}}
                    <div class="col-12 col-sm-6 mb-5 mb-sm-0">
                        <label class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center gap-1.5">
                            <i class="ti ti-sort-ascending text-slate-400"></i> Urutan Tampil
                        </label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', $section->sort_order) }}" min="0" max="65535"
                            class="w-full px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all">
                        <p class="text-[11px] text-slate-400 mt-1.5 mb-0">Angka kecil tampil lebih dulu.</p>
                    </div>

                    {{-- Status Aktif --}}
                    <div class="col-12 col-sm-6 d-flex align-items-end">
                        <div class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-300 rounded-xl">
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input cursor-pointer" type="checkbox" role="switch" id="is_active"
                                    name="is_active" value="1" {{ old('is_active', $section->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label text-xs font-bold text-slate-700" for="is_active">
                                    Aktif
                                </label>
                            </div>
                            <p class="text-[11px] text-slate-400 mt-1 mb-0">Section nonaktif tidak tampil di halaman checklist staff.</p>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('operational.sections.index', ['restaurant_id' => $section->restaurant_id]) }}"
                        class="btn btn-light rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-100 transition shadow-2xs">
                        Batal
                    </a>
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-sm text-white bg-[#172a40] hover:bg-[#112031] transition shadow-sm hover:shadow cursor-pointer">
                        <i class="ti ti-device-floppy text-base"></i> Perbarui Section
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
