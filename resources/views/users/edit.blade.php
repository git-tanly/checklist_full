@extends('layouts.portal')

@section('title', 'Edit User Access - Tanly Hospitality')
@section('breadcrumb', 'Edit User')

@section('content')
<div class="space-y-6">

    {{-- 1. EXECUTIVE HEADER BANNER --}}
    <div class="rounded-2xl border border-slate-200/90 shadow-sm bg-white p-5 md:p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 shadow-sm" style="background-color: #172a40; color: #B89047;">
                    <i class="ti ti-user-check text-2xl"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold tracking-tight text-slate-800">Edit User Access</h1>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                            {{ $user->name }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">Konfigurasikan peran otorisasi sistem dan hak akses outlet restoran untuk pengguna ini</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('users.index') }}" class="btn btn-light rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-100 transition inline-flex items-center gap-1.5 shadow-sm">
                    <i class="ti ti-arrow-left text-base"></i> Back to User List
                </a>
            </div>
        </div>
    </div>

    {{-- ERROR VALIDATION ALERT --}}
    @if ($errors->any())
        <div class="max-w-3xl mx-auto rounded-2xl border border-rose-200 bg-rose-50/90 p-4 text-rose-800 shadow-sm">
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
    <div class="max-w-3xl mx-auto bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden">
        {{-- Card Header --}}
        <div class="bg-gradient-to-r from-slate-900 via-[#172a40] to-slate-800 text-white px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center text-[#B89047]">
                    <i class="ti ti-shield-lock text-base"></i>
                </span>
                <div>
                    <h2 class="text-sm font-bold text-white tracking-tight">Access Permissions Form</h2>
                    <p class="text-[11px] text-slate-300 mb-0">Role & outlet assignment</p>
                </div>
            </div>
            <span class="w-2 h-2 rounded-full bg-[#B89047]"></span>
        </div>

        <div class="p-6">
            {{-- SSO Notice Info --}}
            <div class="rounded-xl border border-sky-200 bg-sky-50/70 p-3.5 mb-6 flex items-start gap-3 text-sky-900">
                <i class="ti ti-info-circle text-sky-600 text-base mt-0.5 shrink-0"></i>
                <div class="text-xs leading-relaxed">
                    Data identitas profil <strong>(Nama & Email)</strong> dikelola secara terpusat melalui <strong>Portal SSO Tanly Hospitality</strong>. Formulir ini khusus mengatur hak akses dan peran di dalam aplikasi <strong>Checklist Daily Report</strong>.
                </div>
            </div>

            <form action="{{ route('users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Readonly Profile Info --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center gap-1.5">
                            <i class="ti ti-user text-slate-400"></i> Full Name
                        </label>
                        <input type="text" class="w-full px-3.5 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-sm font-semibold text-slate-600 cursor-not-allowed"
                            value="{{ $user->name }}" readonly disabled>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center gap-1.5">
                            <i class="ti ti-mail text-slate-400"></i> Email Address
                        </label>
                        <input type="text" class="w-full px-3.5 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-sm font-semibold text-slate-600 cursor-not-allowed"
                            value="{{ $user->email }}" readonly disabled>
                    </div>
                </div>

                {{-- System Role Select --}}
                <div class="mb-5">
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center justify-between">
                        <span class="flex items-center gap-1.5">
                            <i class="ti ti-shield-check text-slate-400"></i> System Role
                            <span class="text-rose-500">*</span>
                        </span>
                        <span class="text-[11px] text-slate-400 font-normal">Menentukan hak akses menu & fitur</span>
                    </label>
                    @php
                        $currentRole = $user->roles->first()?->name ?? '';
                    @endphp
                    <select name="role" class="w-full px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#172a40]/20 focus:border-[#172a40] transition-all" required>
                        @foreach ($roles as $role)
                            <option value="{{ $role->name }}" {{ (old('role', $currentRole) == $role->name) ? 'selected' : '' }}>
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

                    @php
                        $assignedIds = old('restaurants', $user->restaurants->pluck('id')->toArray());
                    @endphp

                    <div class="p-4 bg-slate-50/70 border border-slate-200/90 rounded-2xl">
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                            @foreach ($restaurants as $rest)
                                <label for="rest_{{ $rest->id }}" class="flex items-center gap-3 p-3 bg-white border border-slate-200/80 rounded-xl cursor-pointer hover:border-slate-300 hover:shadow-2xs transition-all">
                                    <input class="form-check-input mt-0 rounded-md text-[#172a40] focus:ring-[#172a40]"
                                        type="checkbox" name="restaurants[]" value="{{ $rest->id }}" id="rest_{{ $rest->id }}"
                                        {{ in_array($rest->id, $assignedIds) ? 'checked' : '' }}>
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
                        <i class="ti ti-device-floppy text-base"></i> Update Access
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
