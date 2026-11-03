@extends('layouts.portal')

@section('title', 'User Management - Tanly Hospitality')
@section('breadcrumb', 'Users')

@section('content')
<div class="space-y-6">

    {{-- 1. EXECUTIVE HEADER BANNER --}}
    <div class="rounded-2xl border border-slate-200/90 shadow-sm bg-white p-5 md:p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 shadow-sm" style="background-color: #172a40; color: #B89047;">
                    <i class="ti ti-users text-2xl"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold tracking-tight text-slate-800">User Management</h1>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-sky-50 text-sky-800 border border-sky-200">
                            SSO Synchronized
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">Kelola otorisasi role sistem dan penugasan akses restoran untuk pengguna Portal SSO</p>
                </div>
            </div>

            <div class="flex items-center gap-2.5">
                <div class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold bg-slate-50 text-slate-600 border border-slate-200 shadow-2xs">
                    <i class="ti ti-info-circle text-[#B89047] text-sm"></i>
                    <span>User baru otomatis terdaftar saat login SSO</span>
                </div>
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
                    <h5 class="text-xs font-bold uppercase tracking-wider text-rose-900 mb-0.5">Perhatian</h5>
                    <p class="text-xs font-medium text-rose-800 mb-0">{{ session('error') }}</p>
                </div>
            </div>
            <button type="button" class="btn-close text-xs text-rose-700" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- USER DIRECTORY CARD --}}
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden">
        {{-- Card Header --}}
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/40">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-[#172a40]/5 border border-[#172a40]/10 flex items-center justify-center text-[#172a40]">
                    <i class="ti ti-list text-base"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-800 tracking-tight">Active User Directory</h2>
                    <p class="text-[11px] text-slate-400 mb-0">Total <strong>{{ $users->total() }}</strong> pengguna terdaftar di aplikasi</p>
                </div>
            </div>
        </div>

        {{-- Table Responsive --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200/80 bg-slate-50/70 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                        <th class="py-3 px-4">User</th>
                        <th class="py-3 px-4">Email</th>
                        <th class="py-3 px-4">Role Access</th>
                        <th class="py-3 px-4">Assigned Outlets</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse ($users as $user)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            {{-- User Avatar & Name --}}
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-[#172a40] to-slate-700 text-white font-bold text-xs flex items-center justify-center shadow-2xs shrink-0">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-800 text-sm">{{ $user->name }}</div>
                                        <div class="text-[11px] text-slate-400">ID: #{{ $user->id }}</div>
                                    </div>
                                </div>
                            </td>

                            {{-- Email --}}
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-1.5 text-slate-600 font-medium">
                                    <i class="ti ti-mail text-slate-400 text-sm"></i>
                                    <span>{{ $user->email }}</span>
                                </div>
                            </td>

                            {{-- Role --}}
                            <td class="py-3.5 px-4">
                                @if ($user->roles->isNotEmpty())
                                    <div class="flex flex-wrap gap-1">
                                        @foreach ($user->roles as $role)
                                            @php
                                                $roleClasses = match($role->name) {
                                                    'Super Admin' => 'bg-[#172a40] text-white',
                                                    'Restaurant Manager' => 'bg-amber-50 text-amber-800 border border-amber-200',
                                                    'Accounting' => 'bg-emerald-50 text-emerald-800 border border-emerald-200',
                                                    default => 'bg-indigo-50 text-indigo-800 border border-indigo-200'
                                                };
                                            @endphp
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $roleClasses }}">
                                                <i class="ti ti-shield-check text-[11px]"></i>
                                                {{ $role->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-slate-400 italic text-xs">No Role</span>
                                @endif
                            </td>

                            {{-- Restaurant --}}
                            <td class="py-3.5 px-4">
                                @if ($user->hasRole('Super Admin'))
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-900 text-white shadow-2xs">
                                        <i class="ti ti-world text-xs text-[#B89047]"></i> Global Access
                                    </span>
                                @elseif($user->restaurants->isNotEmpty())
                                    <div class="flex flex-wrap gap-1 max-w-md">
                                        @foreach ($user->restaurants as $rest)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[11px] font-semibold bg-slate-100 text-slate-700 border border-slate-200" title="{{ $rest->name }}">
                                                <i class="ti ti-building-store text-[10px] text-slate-400"></i>
                                                {{ $rest->code }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="inline-flex items-center gap-1 text-rose-500 font-medium italic text-xs">
                                        <i class="ti ti-alert-circle"></i> No Access Assigned
                                    </span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="py-3.5 px-4 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('users.edit', $user->id) }}"
                                        class="w-8 h-8 rounded-lg bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200/60 flex items-center justify-center transition shadow-2xs"
                                        title="Edit User Access">
                                        <i class="ti ti-edit text-sm"></i>
                                    </a>

                                    @if(Auth::id() != $user->id)
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="w-8 h-8 rounded-lg bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200/60 flex items-center justify-center transition shadow-2xs cursor-pointer"
                                                title="Revoke User Access"
                                                onclick="return confirm('Apakah Anda yakin ingin mencabut hak akses user {{ $user->name }} dari aplikasi ini?')">
                                                <i class="ti ti-trash text-sm"></i>
                                            </button>
                                        </form>
                                    @endif
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
                                    <h6 class="text-sm font-bold text-slate-700 mb-1">Tidak Ada Pengguna</h6>
                                    <p class="text-xs text-slate-400 max-w-sm">Belum ada pengguna lain yang terdaftar selain Super Admin.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination Footer --}}
        @if ($users->hasPages())
            <div class="px-5 py-3.5 border-t border-slate-100 bg-slate-50/30 flex items-center justify-between">
                <div class="text-xs text-slate-500">
                    Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} users
                </div>
                <div>
                    {{ $users->links() }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
