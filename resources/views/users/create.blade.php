@extends('layouts.portal')

@section('title', 'Grant User Access - Tanly Hospitality')
@section('breadcrumb', 'Grant Access')

@section('content')
<div class="space-y-6">

    {{-- 1. EXECUTIVE HEADER BANNER --}}
    <div class="rounded-2xl border border-slate-200/90 shadow-sm bg-white p-5 md:p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 shadow-sm" style="background-color: #172a40; color: #B89047;">
                    <i class="ti ti-user-plus text-2xl"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold tracking-tight text-slate-800">Grant User Access</h1>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                            New Authorization
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">Tambahkan otorisasi pengguna yang sudah terdaftar di SSO Portal ke aplikasi Checklist Daily Report</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('users.index') }}" class="btn btn-light rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-100 transition inline-flex items-center gap-1.5 shadow-sm">
                    <i class="ti ti-arrow-left text-base"></i> Back to User List
                </a>
            </div>
        </div>
    </div>

    {{-- ALERTS --}}
    @if (session('error'))
        <div class="max-w-3xl mx-auto rounded-2xl border border-rose-200 bg-rose-50/90 p-4 text-rose-800 shadow-sm">
            <div class="flex items-start gap-3">
                <div class="w-9 h-9 rounded-xl bg-rose-100 flex items-center justify-center text-rose-700 shrink-0">
                    <i class="ti ti-alert-triangle text-lg"></i>
                </div>
                <div>
                    <h5 class="text-xs font-bold uppercase tracking-wider text-rose-900 mb-0.5">Perhatian</h5>
                    <p class="text-xs font-medium text-rose-800 mb-0">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    {{-- FORM CARD --}}
    <div class="max-w-3xl mx-auto bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden">
        {{-- Card Header --}}
        <div class="bg-gradient-to-r from-slate-900 via-[#172a40] to-slate-800 text-white px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center text-[#B89047]">
                    <i class="ti ti-shield-plus text-base"></i>
                </span>
                <div>
                    <h2 class="text-sm font-bold text-white tracking-tight">Grant Access Form</h2>
                    <p class="text-[11px] text-slate-300 mb-0">Authorize an existing SSO account</p>
                </div>
            </div>
            <span class="w-2 h-2 rounded-full bg-[#B89047]"></span>
        </div>

        <div class="p-6">
            <form action="{{ route('users.store') }}" method="POST">
                @csrf

                {{-- User Email --}}
                <div class="mb-5">
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center justify-between">
                        <span class="flex items-center gap-1.5">
                            <i class="ti ti-mail text-slate-400"></i> User Email (Registered in Portal SSO)
                            <span class="text-rose-500">*</span>
                        </span>
                        <span class="text-[11px] text-slate-400 font-normal">Wajib sudah terdaftar di SSO</span>
                    </label>
                    <input type="email" name="email" class="w-full px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all"
                        value="{{ old('email') }}" placeholder="user@tanly.id" required>
                    <p class="text-[11px] text-slate-400 mt-1 mb-0">Sistem akan mencocokkan email ini dengan akun yang ada di Tanly Portal.</p>
                </div>

                {{-- Role Access --}}
                <div class="mb-5">
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center justify-between">
                        <span class="flex items-center gap-1.5">
                            <i class="ti ti-shield-check text-slate-400"></i> Role Access
                            <span class="text-rose-500">*</span>
                        </span>
                        <span class="text-[11px] text-slate-400 font-normal">Pilih tingkat otorisasi</span>
                    </label>
                    <select name="role" class="w-full px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all" required>
                        <option value="" selected disabled>-- Select Role --</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Assigned Restaurants Multi-Checkbox --}}
                <div class="mb-6">
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center justify-between">
                        <span class="flex items-center gap-1.5">
                            <i class="ti ti-building-store text-slate-400"></i> Assigned Restaurants / Outlets
                        </span>
                        <span class="text-[11px] text-slate-400 font-normal">Pilih outlet yang dapat diakses</span>
                    </label>

                    <div class="p-4 bg-slate-50/70 border border-slate-200/90 rounded-2xl">
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                            @foreach ($restaurants as $rest)
                                <label for="rest_{{ $rest->id }}" class="flex items-center gap-3 p-3 bg-white border border-slate-200/80 rounded-xl cursor-pointer hover:border-slate-300 hover:shadow-2xs transition-all">
                                    <input class="form-check-input mt-0 rounded-md text-[#172a40] focus:ring-[#172a40]"
                                        type="checkbox" name="restaurants[]" value="{{ $rest->id }}" id="rest_{{ $rest->id }}"
                                        {{ is_array(old('restaurants')) && in_array($rest->id, old('restaurants')) ? 'checked' : '' }}>
                                    <div class="leading-tight">
                                        <div class="text-xs font-bold text-slate-800">{{ $rest->name }}</div>
                                        <div class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">{{ $rest->code }}</div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('users.index') }}" class="btn btn-light rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-100 transition shadow-2xs">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-sm text-white bg-[#172a40] hover:bg-[#112031] transition shadow-sm hover:shadow cursor-pointer">
                        <i class="ti ti-shield-check text-base"></i> Grant Access
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
