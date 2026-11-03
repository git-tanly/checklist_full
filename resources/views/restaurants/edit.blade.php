@extends('layouts.portal')

@section('title', 'Edit Restaurant - Tanly Hospitality')
@section('breadcrumb', 'Edit Restaurant')

@section('content')
<div class="space-y-6">

    {{-- 1. EXECUTIVE HEADER BANNER --}}
    <div class="rounded-2xl border border-slate-200/90 shadow-sm bg-white p-5 md:p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 shadow-sm" style="background-color: #172a40; color: #B89047;">
                    <i class="ti ti-edit text-2xl"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold tracking-tight text-slate-800">Edit Restaurant Details</h1>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                            {{ $restaurant->name }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">Perbarui informasi nama dan kode unit outlet {{ $restaurant->code }}</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('restaurants.index') }}" class="btn btn-light rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-100 transition inline-flex items-center gap-1.5 shadow-sm">
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
                    <i class="ti ti-building-store text-base"></i>
                </span>
                <div>
                    <h2 class="text-sm font-bold text-white tracking-tight">Edit Outlet Details</h2>
                    <p class="text-[11px] text-slate-300 mb-0">ID: #{{ $restaurant->id }}</p>
                </div>
            </div>
            <span class="w-2 h-2 rounded-full bg-[#B89047]"></span>
        </div>

        <div class="p-6">
            <form action="{{ route('restaurants.update', $restaurant->id) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Restaurant Code --}}
                <div class="mb-5">
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center justify-between">
                        <span class="flex items-center gap-1.5">
                            <i class="ti ti-tag text-slate-400"></i> Restaurant Code (Unique)
                            <span class="text-rose-500">*</span>
                        </span>
                        <span class="text-[11px] text-slate-400 font-normal">Maks. 10 karakter</span>
                    </label>
                    <input type="text" name="code" class="w-full px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-mono font-bold text-slate-800 uppercase focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all"
                        value="{{ old('code', $restaurant->code) }}" required>
                    <div class="rounded-xl border border-amber-200 bg-amber-50/70 p-3 mt-2 flex items-start gap-2.5 text-amber-900">
                        <i class="ti ti-alert-circle text-amber-600 text-sm mt-0.5 shrink-0"></i>
                        <span class="text-[11px] leading-relaxed">
                            Perhatian: Perubahan kode outlet dapat memengaruhi pemetaan template formulir laporan harian (Daily Report) yang menggunakan kode unik ini.
                        </span>
                    </div>
                </div>

                {{-- Restaurant Name --}}
                <div class="mb-6">
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center gap-1.5">
                        <i class="ti ti-tools-kitchen-2 text-slate-400"></i> Restaurant / Outlet Name
                        <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="name" class="w-full px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all"
                        value="{{ old('name', $restaurant->name) }}" required>
                </div>

                {{-- Action Buttons --}}
                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('restaurants.index') }}" class="btn btn-light rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-100 transition shadow-2xs">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-sm text-white bg-[#172a40] hover:bg-[#112031] transition shadow-sm hover:shadow cursor-pointer">
                        <i class="ti ti-device-floppy text-base"></i> Update Restaurant
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
