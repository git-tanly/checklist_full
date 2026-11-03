@extends('layouts.portal')

@section('title', 'Edit Application: ' . $application->name)
@section('breadcrumb', 'Edit Application')

@section('content')
@php
    $metadata = $application->metadata ?? [];
    $version = $metadata['version'] ?? '1.0.0';
    $tagline = $metadata['tagline'] ?? '';
    $description = $metadata['description'] ?? '';
    $features = $metadata['features'] ?? [];
    $uris = $application->redirect_uris;
    $currentRedirect = is_array($uris) && count($uris) > 0 ? $uris[0] : '';
@endphp

<div class="flex flex-col gap-6">

    <!-- PAGE HEADER -->
    <div class="flex items-center justify-between bg-white border border-slate-200/90 rounded-2xl p-5 md:p-6 shadow-sm">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('applications.index') }}" class="text-slate-400 hover:text-slate-600 text-xs font-medium flex items-center gap-1 text-decoration-none">
                    <i class="bi bi-arrow-left"></i> App Management
                </a>
                <span class="text-slate-300">/</span>
                <span class="text-xs font-semibold text-slate-600">Edit Application</span>
            </div>
            <h1 class="text-lg md:text-xl font-bold text-slate-800 tracking-tight mb-0.5">Edit Application: {{ $application->name }}</h1>
            <p class="text-xs text-slate-500 mb-0">Perbarui konfigurasi OAuth dan sesuaikan tampilan login SSO khusus aplikasi ini.</p>
        </div>
    </div>

    <!-- MAIN FORM GRID (LEFT: FORM, RIGHT: LIVE PREVIEW) -->
    <form action="{{ route('applications.update', $application->id) }}" method="POST" id="appForm">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <!-- LEFT COLUMN: INPUT FIELDS (7 COLS) -->
            <div class="lg:col-span-7 flex flex-col gap-5">

                <!-- SECTION 1: CORE APPLICATION INFO -->
                <div class="bg-white border border-slate-200/90 rounded-2xl p-5 md:p-6 shadow-sm flex flex-col gap-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-[#172a40]/10 text-[#172a40] flex items-center justify-center text-sm font-bold">1</div>
                            <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-0">Data Pokok Aplikasi</h2>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-slate-400 font-mono">ID:</span>
                            <code class="text-[11px] bg-slate-100 text-slate-700 px-2 py-0.5 rounded font-mono border border-slate-200">{{ $application->id }}</code>
                        </div>
                    </div>

                    <!-- App Name -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Application Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="inputAppName"
                               class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-xs text-slate-800 focus:outline-none focus:border-[#172a40] focus:ring-2 focus:ring-[#172a40]/10 transition"
                               placeholder="Contoh: Sistem Reservasi Outlet" value="{{ old('name', $application->name) }}" required>
                    </div>

                    <!-- Department Category -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Department Category <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                            @foreach ($categories as $key => $cat)
                                <label class="cursor-pointer">
                                    <input type="radio" name="category" value="{{ $key }}" class="peer sr-only" {{ old('category', $application->category ?? 'eng') === $key ? 'checked' : '' }} required>
                                    <div class="border border-slate-200 peer-checked:border-[#172a40] peer-checked:bg-[#172a40]/5 peer-checked:text-[#172a40] rounded-xl p-3 flex flex-col items-center text-center transition hover:bg-slate-50">
                                        <div class="w-8 h-8 rounded-lg bg-white border border-slate-200 peer-checked:border-[#172a40]/30 flex items-center justify-center text-sm mb-1.5 shadow-sm text-slate-600">
                                            <i class="{{ $cat['icon'] }}"></i>
                                        </div>
                                        <span class="text-xs font-bold text-slate-700 block leading-tight">{{ $cat['label'] }}</span>
                                        <span class="text-[10px] text-slate-400 font-mono mt-0.5">{{ strtoupper($key) }}</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        <span class="text-[11px] text-slate-400 mt-1.5 block">Kategori departemen untuk pengelompokan pada dashboard operasional.</span>
                    </div>

                    <!-- App Icon Picker -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Application Icon
                        </label>
                        <div class="flex items-center gap-2">
                            <div class="w-11 h-11 rounded-xl bg-slate-100 border border-slate-300 flex items-center justify-center text-lg text-[#172a40] shrink-0">
                                <i id="iconPreviewEl" class="{{ old('icon', $application->icon ?? 'fa-solid fa-server') }}"></i>
                            </div>
                            <input type="text" name="icon" id="inputIcon"
                                   class="flex-1 bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-xs text-slate-800 font-mono focus:outline-none focus:border-[#172a40] focus:ring-2 focus:ring-[#172a40]/10 transition"
                                   placeholder="fa-solid fa-server" value="{{ old('icon', $application->icon ?? 'fa-solid fa-server') }}">
                            <button type="button" class="bg-[#172a40] hover:bg-[#112031] text-white text-xs font-semibold px-4 py-2.5 rounded-xl shadow-sm transition shrink-0" data-bs-toggle="modal" data-bs-target="#iconPickerModal">
                                <i class="bi bi-search me-1"></i> Browse
                            </button>
                        </div>
                    </div>

                    <!-- Redirect URI -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Redirect URI (Callback OAuth) <span class="text-red-500">*</span>
                        </label>
                        <input type="url" name="redirect"
                               class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-xs text-slate-800 font-mono focus:outline-none focus:border-[#172a40] focus:ring-2 focus:ring-[#172a40]/10 transition"
                               placeholder="https://app-satelit.tanly.id/auth/callback" value="{{ old('redirect', $currentRedirect) }}" required>
                    </div>

                    <!-- System Status -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            System Status
                        </label>
                        <select name="status" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-xs text-slate-800 focus:outline-none focus:border-[#172a40] focus:ring-2 focus:ring-[#172a40]/10 transition">
                            <option value="active" {{ !$application->revoked ? 'selected' : '' }}>Active (Izinkan Akses Login)</option>
                            <option value="revoked" {{ $application->revoked ? 'selected' : '' }}>Revoked (Blokir Akses Login)</option>
                        </select>
                    </div>
                </div>

                <!-- SECTION 2: VISUAL LOGIN PAGE CUSTOMIZER (METADATA) -->
                <div class="bg-white border border-slate-200/90 rounded-2xl p-5 md:p-6 shadow-sm flex flex-col gap-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-[#B89047]/15 text-[#B89047] flex items-center justify-center text-sm font-bold">2</div>
                            <div>
                                <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-0">Login Page Customizer</h2>
                                <span class="text-[11px] text-slate-400">Atur teks spesifik saat pengguna login ke aplikasi ini</span>
                            </div>
                        </div>
                        <span class="bg-[#B89047]/10 text-[#B89047] text-[10px] font-bold px-2 py-0.5 rounded-md uppercase">Metadata Builder</span>
                    </div>

                    <!-- Version & Tagline -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Version</label>
                            <input type="text" name="version" id="inputVersion"
                                   class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-xs text-slate-800 focus:outline-none focus:border-[#172a40] focus:ring-2 focus:ring-[#172a40]/10 transition"
                                   placeholder="1.0.0" value="{{ old('version', $version) }}">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Application Tagline</label>
                            <input type="text" name="tagline" id="inputTagline"
                                   class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-xs text-slate-800 focus:outline-none focus:border-[#172a40] focus:ring-2 focus:ring-[#172a40]/10 transition"
                                   placeholder="Contoh: Integrated Management Platform." value="{{ old('tagline', $tagline) }}">
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Short Description</label>
                        <textarea name="description" id="inputDesc" rows="3"
                                  class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-xs text-slate-800 focus:outline-none focus:border-[#172a40] focus:ring-2 focus:ring-[#172a40]/10 transition leading-relaxed"
                                  placeholder="Deskripsi singkat modul sistem untuk ditampilkan pada panel login...">{{ old('description', $description) }}</textarea>
                    </div>

                    <!-- Role Configuration Card (Optional Toggle) -->
                    <div class="flex flex-col gap-2 pt-3 border-t border-slate-100">
                        <div class="flex items-center justify-between p-3.5 bg-slate-50 border border-slate-200 rounded-xl">
                            <div>
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-user-shield text-xs text-[#B89047]"></i>
                                    <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Konfigurasi Atribut Role</span>
                                </div>
                                <span class="text-[11px] text-slate-400 mt-0.5 block">Aktifkan jika aplikasi klien ini memiliki tingkatan role/hak akses berjenjang.</span>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer mb-0">
                                @php $hasRolesActive = old('has_roles', $application->has_roles); @endphp
                                <input type="checkbox" name="has_roles" id="toggleHasRoles" value="1" class="sr-only peer" onchange="toggleSection('rolesContainerWrapper', this.checked)" {{ $hasRolesActive ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#172a40]"></div>
                            </label>
                        </div>

                        <div id="rolesContainerWrapper" class="{{ $hasRolesActive ? '' : 'hidden' }} flex flex-col gap-2 p-3 bg-slate-50/60 border border-slate-200 rounded-xl">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="text-[11px] font-bold text-slate-600 uppercase tracking-wider">Daftar Role Aplikasi:</span>
                                    <span class="text-[10px] text-slate-400 block">Pasangan <strong>Value</strong> (dikirim ke backend klien) dan <strong>Label</strong> (tampil di dropdown portal).</span>
                                </div>
                                <button type="button" onclick="addRoleRow()" class="text-xs font-bold text-[#172a40] hover:text-[#112031] bg-white border border-slate-200 hover:bg-slate-100 px-2.5 py-1 rounded-lg transition flex items-center gap-1 shadow-sm">
                                    <i class="bi bi-plus-circle-fill text-[#B89047]"></i> Tambah Role Enum
                                </button>
                            </div>

                            <!-- Hidden Default Role input synced dynamically -->
                            <input type="hidden" name="default_role" id="hiddenDefaultRole" value="{{ old('default_role', $application->default_role) }}">

                            <div id="rolesContainer" class="flex flex-col gap-2 pt-1">
                                <!-- Populated dynamically by JS -->
                            </div>
                            <span class="text-[11px] text-slate-400 italic">Pilih radio button di samping role untuk menandainya sebagai <strong>Default Role</strong>.</span>
                        </div>
                    </div>

                    <!-- Outlet Configuration Card (Optional Toggle) -->
                    <div class="flex flex-col gap-2 pt-3 border-t border-slate-100">
                        <div class="flex items-center justify-between p-3.5 bg-slate-50 border border-slate-200 rounded-xl">
                            <div>
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-store text-xs text-[#B89047]"></i>
                                    <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Konfigurasi Atribut Outlet</span>
                                </div>
                                <span class="text-[11px] text-slate-400 mt-0.5 block">Aktifkan jika aplikasi klien ini mengelola hak akses per unit outlet/cabang.</span>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer mb-0">
                                @php $hasOutletsActive = old('has_outlets', $application->has_outlets); @endphp
                                <input type="checkbox" name="has_outlets" id="toggleHasOutlets" value="1" class="sr-only peer" onchange="toggleSection('outletsContainerWrapper', this.checked)" {{ $hasOutletsActive ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#172a40]"></div>
                            </label>
                        </div>

                        <div id="outletsContainerWrapper" class="{{ $hasOutletsActive ? '' : 'hidden' }} flex flex-col gap-2 p-3 bg-slate-50/60 border border-slate-200 rounded-xl">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-bold text-slate-600 uppercase tracking-wider">Daftar Opsi Outlet:</span>
                                <button type="button" onclick="addOutletRow()" class="text-xs font-bold text-[#172a40] hover:text-[#112031] bg-white border border-slate-200 hover:bg-slate-100 px-2.5 py-1 rounded-lg transition flex items-center gap-1 shadow-sm">
                                    <i class="bi bi-plus-circle-fill text-emerald-600"></i> Tambah Outlet
                                </button>
                            </div>
                            <div id="outletsContainer" class="flex flex-col gap-2">
                                <!-- Populated dynamically by JS -->
                            </div>
                        </div>
                    </div>

                    <!-- Hotel Configuration Card (Optional Toggle) -->
                    <div class="flex flex-col gap-2 pt-3 border-t border-slate-100">
                        <div class="flex items-center justify-between p-3.5 bg-slate-50 border border-slate-200 rounded-xl">
                            <div>
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-hotel text-xs text-[#B89047]"></i>
                                    <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Konfigurasi Atribut Hotel</span>
                                </div>
                                <span class="text-[11px] text-slate-400 mt-0.5 block">Aktifkan jika aplikasi klien ini beroperasi multi-hotel/properti penginapan.</span>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer mb-0">
                                @php $hasHotelsActive = old('has_hotels', $application->has_hotels); @endphp
                                <input type="checkbox" name="has_hotels" id="toggleHasHotels" value="1" class="sr-only peer" onchange="toggleSection('hotelsContainerWrapper', this.checked)" {{ $hasHotelsActive ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#172a40]"></div>
                            </label>
                        </div>

                        <div id="hotelsContainerWrapper" class="{{ $hasHotelsActive ? '' : 'hidden' }} flex flex-col gap-2 p-3 bg-slate-50/60 border border-slate-200 rounded-xl">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-bold text-slate-600 uppercase tracking-wider">Daftar Opsi Hotel:</span>
                                <button type="button" onclick="addHotelRow()" class="text-xs font-bold text-[#172a40] hover:text-[#112031] bg-white border border-slate-200 hover:bg-slate-100 px-2.5 py-1 rounded-lg transition flex items-center gap-1 shadow-sm">
                                    <i class="bi bi-plus-circle-fill text-blue-600"></i> Tambah Hotel
                                </button>
                            </div>
                            <div id="hotelsContainer" class="flex flex-col gap-2">
                                <!-- Populated dynamically by JS -->
                            </div>
                        </div>
                    </div>

                    <!-- Key Features Repeater -->
                    <div class="flex flex-col gap-2 pt-2">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Key Features (01, 02, 03...)
                            </label>
                            <button type="button" onclick="addFeatureItem()" class="text-xs font-bold text-[#172a40] hover:text-[#112031] bg-slate-100 hover:bg-slate-200 px-3 py-1.5 rounded-lg transition flex items-center gap-1">
                                <i class="bi bi-plus-circle-fill"></i> Tambah Fitur
                            </button>
                        </div>

                        <div id="featuresContainer" class="flex flex-col gap-3">
                            @forelse ($features as $idx => $feat)
                                <div class="feature-row bg-slate-50 border border-slate-200 rounded-xl p-3 relative group">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider feature-index-badge">Fitur {{ str_pad($idx + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                        <button type="button" onclick="removeFeatureItem(this)" class="text-slate-400 hover:text-rose-500 text-xs transition">
                                            <i class="bi bi-trash"></i> Hapus
                                        </button>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                        <input type="text" name="features[{{ $idx }}][title]" placeholder="Judul Fitur" class="feature-title-input bg-white border border-slate-200 rounded-lg px-3 py-1.5 text-xs text-slate-800 focus:outline-none focus:border-[#172a40]" value="{{ $feat['title'] ?? '' }}">
                                        <input type="text" name="features[{{ $idx }}][desc]" placeholder="Deskripsi Singkat Fitur" class="feature-desc-input sm:col-span-2 bg-white border border-slate-200 rounded-lg px-3 py-1.5 text-xs text-slate-800 focus:outline-none focus:border-[#172a40]" value="{{ $feat['desc'] ?? '' }}">
                                    </div>
                                </div>
                            @empty
                                <div class="feature-row bg-slate-50 border border-slate-200 rounded-xl p-3 relative group">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider feature-index-badge">Fitur 01</span>
                                        <button type="button" onclick="removeFeatureItem(this)" class="text-slate-400 hover:text-rose-500 text-xs transition">
                                            <i class="bi bi-trash"></i> Hapus
                                        </button>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                        <input type="text" name="features[0][title]" placeholder="Judul Fitur" class="feature-title-input bg-white border border-slate-200 rounded-lg px-3 py-1.5 text-xs text-slate-800 focus:outline-none focus:border-[#172a40]" value="Single Sign-On (SSO)">
                                        <input type="text" name="features[0][desc]" placeholder="Deskripsi Singkat Fitur" class="feature-desc-input sm:col-span-2 bg-white border border-slate-200 rounded-lg px-3 py-1.5 text-xs text-slate-800 focus:outline-none focus:border-[#172a40]" value="Satu akun terpusat untuk akses instan tanpa login berulang.">
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- SUBMIT BUTTON -->
                <div class="flex items-center gap-3">
                    <button type="submit" class="flex-1 bg-[#172a40] hover:bg-[#112031] text-white text-xs font-bold py-3.5 px-6 rounded-xl shadow-md transition flex items-center justify-center gap-2">
                        <i class="bi bi-check2-circle text-base"></i>
                        <span>Save Changes</span>
                    </button>
                    <a href="{{ route('applications.index') }}" class="bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-semibold py-3.5 px-6 rounded-xl transition text-decoration-none text-center">
                        Batal
                    </a>
                </div>

                <!-- SECTION 3: DANGER ZONE -->
                <div class="bg-white border border-rose-200 rounded-2xl p-5 md:p-6 shadow-sm flex flex-col gap-4 mt-2">
                    <div class="flex items-center justify-between pb-3 border-b border-rose-100">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center text-sm font-bold">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                            </div>
                            <div>
                                <h2 class="text-sm font-bold text-rose-800 uppercase tracking-wider mb-0">Danger Zone</h2>
                                <span class="text-[11px] text-slate-400">Tindakan destruktif terhadap aplikasi ini</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 p-4 bg-rose-50/50 border border-rose-100 rounded-xl">
                        <div>
                            <strong class="text-xs font-bold text-slate-800 block">Hapus Aplikasi Secara Permanen</strong>
                            <p class="text-[11px] text-slate-500 mb-0">Menghapus data aplikasi, konfigurasi metadata, serta mencabut seluruh sesi token OAuth dan hak akses pengguna terkait.</p>
                        </div>
                        <button type="button" onclick="confirmDeleteFromEdit()" class="bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold py-2.5 px-4 rounded-xl shadow-sm transition flex items-center gap-2 shrink-0">
                            <i class="bi bi-trash"></i> Hapus Aplikasi
                        </button>
                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN: LIVE LOGIN PREVIEW WIDGET (5 COLS) -->
            <div class="lg:col-span-5 flex flex-col gap-4">
                <div class="sticky top-28 bg-white border border-slate-200/90 rounded-2xl p-5 md:p-6 shadow-sm">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                        <div class="flex items-center gap-2">
                            <i class="bi bi-eye-fill text-[#B89047]"></i>
                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800 mb-0">Live Login Preview</h3>
                        </div>
                        <span class="text-[10px] text-slate-400 font-medium">Real-time Simulation</span>
                    </div>

                    <!-- MINI LOGIN LEFT PANEL PREVIEW CONTAINER -->
                    <div class="bg-[#17344c] text-white rounded-2xl p-5 md:p-6 flex flex-col justify-between shadow-lg relative overflow-hidden" style="min-height: 480px;">
                        <!-- Glow Background Shape -->
                        <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-[#326da0]/20 rounded-full blur-2xl pointer-events-none"></div>

                        <!-- Top Brand -->
                        <div>
                            <div class="flex items-center gap-2 mb-6">
                                <div class="bg-white p-1 rounded-md w-16 h-7 flex items-center justify-center">
                                    <img src="{{ asset('images/tanly.png') }}" alt="Tanly" class="max-h-full max-w-full object-contain">
                                </div>
                                <span class="text-[9px] uppercase tracking-widest text-slate-400 font-bold border-l border-white/20 pl-2">Internal System</span>
                            </div>

                            <!-- Preview Copy -->
                            <div class="mb-5">
                                <div id="previewVersion" class="text-[10px] font-bold uppercase tracking-widest text-[#B89047] mb-1">Version {{ $version }}</div>
                                <h4 id="previewAppName" class="text-base font-bold text-white leading-tight mb-0.5">{{ $application->name }}</h4>
                                <div id="previewTagline" class="text-xs text-slate-300 font-medium leading-tight mb-2.5">{{ $tagline ?: 'Application Tagline.' }}</div>
                                <p id="previewDesc" class="text-[11px] text-slate-400 leading-relaxed line-clamp-3">
                                    {{ $description ?: 'Deskripsi singkat modul sistem untuk ditampilkan pada panel login pengguna.' }}
                                </p>
                            </div>

                            <!-- Capabilities Preview -->
                            <div id="previewCapabilities" class="flex flex-col gap-2.5 border-t border-white/10 pt-3.5">
                                <!-- Populated dynamically by JS -->
                            </div>
                        </div>

                        <!-- Footer Preview -->
                        <div class="pt-4 mt-4 border-t border-white/10 flex items-center justify-between text-[10px] text-slate-400">
                            <span id="previewFooterApp">{{ $application->name }}</span>
                            <span>Tanly Hospitality</span>
                        </div>
                    </div>

                    <div class="text-[11px] text-slate-400 text-center mt-3">
                        <i class="bi bi-info-circle me-1"></i> Preview di atas mencerminkan panel kiri layar saat user mengakses login SSO aplikasi ini.
                    </div>
                </div>
            </div>

        </div>
    </form>

    <!-- HIDDEN DELETE FORM -->
    <form id="deleteAppForm" action="{{ route('applications.destroy', $application->id) }}" method="POST" class="d-none">
        @csrf
        @method('DELETE')
    </form>

</div>

<!-- MODAL ICON PICKER -->
<div class="modal fade" id="iconPickerModal" tabindex="-1" aria-labelledby="iconPickerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-2xl rounded-2xl">
            <div class="modal-header border-b border-slate-100 p-4">
                <h5 class="modal-title font-bold text-sm text-slate-800" id="iconPickerModalLabel">
                    <i class="bi bi-icons me-2 text-[#172a40]"></i> Pilih Ikon Aplikasi
                </h5>
                <button type="button" class="btn-close text-xs" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="relative mb-3">
                    <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" id="iconSearchInput" placeholder="Cari ikon (misal: chart, box, server, user)..."
                           class="w-full bg-slate-50 border border-slate-300 rounded-xl pl-8 pr-3 py-2 text-xs text-slate-700 focus:outline-none focus:border-[#172a40]">
                </div>

                <div class="grid grid-cols-4 sm:grid-cols-6 gap-2 max-h-[320px] overflow-y-auto p-1" id="iconGridContainer">
                    <!-- Populated by JS -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const commonIcons = [
        'fa-server', 'fa-database', 'fa-cloud', 'fa-network-wired', 'fa-microchip',
        'fa-laptop-code', 'fa-desktop', 'fa-chart-pie', 'fa-chart-line', 'fa-chart-column',
        'fa-wallet', 'fa-coins', 'fa-users', 'fa-user-tie', 'fa-user-gear', 'fa-building',
        'fa-briefcase', 'fa-clipboard-list', 'fa-boxes-stacked', 'fa-box', 'fa-cart-shopping',
        'fa-utensils', 'fa-mug-hot', 'fa-bed', 'fa-shield-halved', 'fa-key', 'fa-bell',
        'fa-gear', 'fa-wrench', 'fa-screwdriver-wrench', 'fa-qrcode', 'fa-barcode',
        'fa-file-invoice', 'fa-envelope', 'fa-headset', 'fa-sliders', 'fa-robot'
    ];

    function renderIconGrid(filter = '') {
        const grid = document.getElementById('iconGridContainer');
        if (!grid) return;
        grid.innerHTML = '';
        const filtered = commonIcons.filter(ic => ic.includes(filter.toLowerCase()));

        if (filtered.length === 0) {
            grid.innerHTML = '<div class="col-span-full py-8 text-center text-xs text-slate-400">Tidak ada ikon yang cocok.</div>';
            return;
        }

        filtered.forEach(ic => {
            const fullClass = `fa-solid ${ic}`;
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'flex flex-col items-center justify-center p-2.5 rounded-xl border border-slate-100 hover:border-[#172a40] hover:bg-slate-50 text-slate-600 hover:text-[#172a40] transition';
            btn.innerHTML = `<i class="${fullClass} text-base mb-1"></i><span class="text-[9px] text-slate-400 truncate w-full text-center">${ic.replace('fa-', '')}</span>`;
            btn.onclick = () => {
                document.getElementById('inputIcon').value = fullClass;
                document.getElementById('iconPreviewEl').className = fullClass;
                const modal = bootstrap.Modal.getInstance(document.getElementById('iconPickerModal'));
                if (modal) modal.hide();
            };
            grid.appendChild(btn);
        });
    }

    function updateLivePreview() {
        const appName = document.getElementById('inputAppName')?.value.trim() || 'Nama Aplikasi';
        const version = document.getElementById('inputVersion')?.value.trim() || '1.0.0';
        const tagline = document.getElementById('inputTagline')?.value.trim() || 'Application Tagline.';
        const desc = document.getElementById('inputDesc')?.value.trim() || 'Deskripsi singkat modul sistem untuk ditampilkan pada panel login pengguna.';

        document.getElementById('previewAppName').textContent = appName;
        document.getElementById('previewFooterApp').textContent = appName;
        document.getElementById('previewVersion').textContent = `Version ${version}`;
        document.getElementById('previewTagline').textContent = tagline;
        document.getElementById('previewDesc').textContent = desc;

        // Render features in preview
        const capContainer = document.getElementById('previewCapabilities');
        if (capContainer) {
            capContainer.innerHTML = '';
            const rows = document.querySelectorAll('.feature-row');
            rows.forEach((row, idx) => {
                const title = row.querySelector('.feature-title-input')?.value.trim() || `Fitur ${idx + 1}`;
                const fDesc = row.querySelector('.feature-desc-input')?.value.trim() || 'Deskripsi kapabilitas fitur.';
                const num = String(idx + 1).padStart(2, '0');

                const item = document.createElement('div');
                item.className = 'flex items-start gap-2.5 text-[11px]';
                item.innerHTML = `
                    <span class="text-[#B89047] font-bold text-xs shrink-0">${num}</span>
                    <div class="leading-tight">
                        <strong class="text-white block font-semibold text-[11px]">${title}</strong>
                        <span class="text-slate-400 text-[10px] line-clamp-1">${fDesc}</span>
                    </div>
                `;
                capContainer.appendChild(item);
            });
        }
    }

    function addFeatureItem() {
        const container = document.getElementById('featuresContainer');
        const count = document.querySelectorAll('.feature-row').length;
        const nextIndex = count;
        const nextNum = String(count + 1).padStart(2, '0');

        const div = document.createElement('div');
        div.className = 'feature-row bg-slate-50 border border-slate-200 rounded-xl p-3 relative group';
        div.innerHTML = `
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider feature-index-badge">Fitur ${nextNum}</span>
                <button type="button" onclick="removeFeatureItem(this)" class="text-slate-400 hover:text-rose-500 text-xs transition">
                    <i class="bi bi-trash"></i> Hapus
                </button>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                <input type="text" name="features[${nextIndex}][title]" placeholder="Judul Fitur" class="feature-title-input bg-white border border-slate-200 rounded-lg px-3 py-1.5 text-xs text-slate-800 focus:outline-none focus:border-[#172a40]">
                <input type="text" name="features[${nextIndex}][desc]" placeholder="Deskripsi Singkat Fitur" class="feature-desc-input sm:col-span-2 bg-white border border-slate-200 rounded-lg px-3 py-1.5 text-xs text-slate-800 focus:outline-none focus:border-[#172a40]">
            </div>
        `;
        container.appendChild(div);
        attachInputListeners();
        updateLivePreview();
    }

    function removeFeatureItem(btn) {
        const row = btn.closest('.feature-row');
        if (row) {
            row.remove();
            document.querySelectorAll('.feature-row').forEach((r, idx) => {
                const num = String(idx + 1).padStart(2, '0');
                const badge = r.querySelector('.feature-index-badge');
                if (badge) badge.textContent = `Fitur ${num}`;
            });
            updateLivePreview();
        }
    }

    function attachInputListeners() {
        document.querySelectorAll('#inputAppName, #inputVersion, #inputTagline, #inputDesc, .feature-title-input, .feature-desc-input').forEach(input => {
            input.removeEventListener('input', updateLivePreview);
            input.addEventListener('input', updateLivePreview);
        });
    }

    function toggleSection(wrapperId, isChecked) {
        const el = document.getElementById(wrapperId);
        if (el) {
            el.classList.toggle('hidden', !isChecked);
            el.querySelectorAll('input, select, textarea').forEach(input => {
                input.disabled = !isChecked;
            });
            if (isChecked) {
                if (wrapperId === 'rolesContainerWrapper' && el.querySelectorAll('.role-row').length === 0) {
                    addRoleRow('admin', 'Admin', true);
                    addRoleRow('user', 'User', false);
                } else if (wrapperId === 'outletsContainerWrapper' && el.querySelectorAll('.outlet-row').length === 0) {
                    addOutletRow('', '');
                } else if (wrapperId === 'hotelsContainerWrapper' && el.querySelectorAll('.hotel-row').length === 0) {
                    addHotelRow('', '');
                }
            }
        }
    }

    let roleIndex = 0;

    function addRoleRow(value = '', label = '', isDefault = false) {
        const container = document.getElementById('rolesContainer');
        if (!container) return;
        const rowId = `role_row_${roleIndex}`;
        const escapedValue = String(value).replace(/"/g, '&quot;');
        const escapedLabel = String(label).replace(/"/g, '&quot;');

        const div = document.createElement('div');
        div.id = rowId;
        div.className = 'role-row flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-xl p-2.5 transition hover:border-slate-300';
        div.innerHTML = `
            <label class="flex items-center gap-1.5 cursor-pointer px-2.5 py-1.5 rounded-lg bg-white border border-slate-200 hover:border-slate-300 transition mb-0 shrink-0 select-none" title="Tetapkan sebagai role default">
                <input type="radio" name="default_role_radio" class="default-role-radio w-3.5 h-3.5 text-[#172a40] focus:ring-[#172a40]" onchange="onDefaultRoleChanged(this)" ${isDefault ? 'checked' : ''}>
                <span class="text-[11px] font-bold text-slate-600">Default</span>
            </label>
            <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-2">
                <div>
                    <input type="text" name="roles[${roleIndex}][value]" class="role-value-input w-full bg-white border border-slate-300 rounded-lg px-3 py-1.5 text-xs font-mono text-slate-800 focus:outline-none focus:border-[#172a40] transition" 
                           placeholder="Value / Enum (misal: 0, admin, staff)" value="${escapedValue}" oninput="syncDefaultRoleRadio()" required>
                </div>
                <div>
                    <input type="text" name="roles[${roleIndex}][label]" class="role-label-input w-full bg-white border border-slate-300 rounded-lg px-3 py-1.5 text-xs text-slate-800 focus:outline-none focus:border-[#172a40] transition" 
                           placeholder="Display Label (misal: Staff, Admin)" value="${escapedLabel}" required>
                </div>
            </div>
            <button type="button" onclick="removeRoleRow('${rowId}')" class="text-slate-400 hover:text-rose-500 p-1.5 rounded-lg hover:bg-rose-50 transition shrink-0" title="Hapus Role">
                <i class="bi bi-trash text-sm"></i>
            </button>
        `;
        container.appendChild(div);
        roleIndex++;
        syncDefaultRoleRadio();
    }

    function removeRoleRow(rowId) {
        const container = document.getElementById('rolesContainer');
        const rows = container ? container.querySelectorAll('.role-row') : [];
        if (rows.length <= 1) {
            const valInput = rows[0].querySelector('.role-value-input');
            const lblInput = rows[0].querySelector('.role-label-input');
            if (valInput) valInput.value = '';
            if (lblInput) lblInput.value = '';
            syncDefaultRoleRadio();
            return;
        }
        const row = document.getElementById(rowId);
        if (row) {
            row.remove();
            syncDefaultRoleRadio();
        }
    }

    function onDefaultRoleChanged(radio) {
        const row = radio.closest('.role-row');
        const input = row ? row.querySelector('.role-value-input') : null;
        const hidden = document.getElementById('hiddenDefaultRole');
        if (input && hidden) {
            hidden.value = input.value.trim();
        }
    }

    function syncDefaultRoleRadio() {
        const radios = document.querySelectorAll('.default-role-radio');
        const hidden = document.getElementById('hiddenDefaultRole');
        
        let checkedRadio = null;
        radios.forEach((r) => {
            if (r.checked) checkedRadio = r;
        });

        if (!checkedRadio && radios.length > 0) {
            radios[0].checked = true;
            checkedRadio = radios[0];
        }

        if (checkedRadio) {
            const row = checkedRadio.closest('.role-row');
            const input = row ? row.querySelector('.role-value-input') : null;
            if (input && hidden) {
                hidden.value = input.value.trim();
            }
        } else if (hidden) {
            hidden.value = '';
        }
    }

    let outletIndex = 0;

    function addOutletRow(value = '', label = '') {
        const container = document.getElementById('outletsContainer');
        if (!container) return;
        const rowId = `outlet_row_${outletIndex}`;
        const escapedValue = String(value).replace(/"/g, '&quot;');
        const escapedLabel = String(label).replace(/"/g, '&quot;');

        const div = document.createElement('div');
        div.id = rowId;
        div.className = 'outlet-row flex items-center gap-2 bg-white border border-slate-200 rounded-xl p-2.5 transition hover:border-slate-300';
        div.innerHTML = `
            <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-2">
                <div>
                    <input type="text" name="outlets[${outletIndex}][value]" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-1.5 text-xs font-mono text-slate-800 focus:outline-none focus:border-[#172a40] transition" 
                           placeholder="Value (misal: 1, kuta_outlet)" value="${escapedValue}" required>
                </div>
                <div>
                    <input type="text" name="outlets[${outletIndex}][label]" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-1.5 text-xs text-slate-800 focus:outline-none focus:border-[#172a40] transition" 
                           placeholder="Display Label (misal: Grand Ballroom, Outlet Kuta)" value="${escapedLabel}" required>
                </div>
            </div>
            <button type="button" onclick="removeOutletRow('${rowId}')" class="text-slate-400 hover:text-rose-500 p-1.5 rounded-lg hover:bg-rose-50 transition shrink-0" title="Hapus Outlet">
                <i class="bi bi-trash text-sm"></i>
            </button>
        `;
        container.appendChild(div);
        outletIndex++;
    }

    function removeOutletRow(rowId) {
        const row = document.getElementById(rowId);
        if (row) {
            row.remove();
        }
    }

    let hotelIndex = 0;

    function addHotelRow(value = '', label = '') {
        const container = document.getElementById('hotelsContainer');
        if (!container) return;
        const rowId = `hotel_row_${hotelIndex}`;
        const escapedValue = String(value).replace(/"/g, '&quot;');
        const escapedLabel = String(label).replace(/"/g, '&quot;');

        const div = document.createElement('div');
        div.id = rowId;
        div.className = 'hotel-row flex items-center gap-2 bg-white border border-slate-200 rounded-xl p-2.5 transition hover:border-slate-300';
        div.innerHTML = `
            <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-2">
                <div>
                    <input type="text" name="hotels[${hotelIndex}][value]" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-1.5 text-xs font-mono text-slate-800 focus:outline-none focus:border-[#172a40] transition" 
                           placeholder="Value (misal: 101, HTL-SBY)" value="${escapedValue}" required>
                </div>
                <div>
                    <input type="text" name="hotels[${hotelIndex}][label]" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-1.5 text-xs text-slate-800 focus:outline-none focus:border-[#172a40] transition" 
                           placeholder="Display Label (misal: Hotel Aston Denpasar, Vasa Hotel)" value="${escapedLabel}" required>
                </div>
            </div>
            <button type="button" onclick="removeHotelRow('${rowId}')" class="text-slate-400 hover:text-rose-500 p-1.5 rounded-lg hover:bg-rose-50 transition shrink-0" title="Hapus Hotel">
                <i class="bi bi-trash text-sm"></i>
            </button>
        `;
        container.appendChild(div);
        hotelIndex++;
    }

    function removeHotelRow(rowId) {
        const row = document.getElementById(rowId);
        if (row) {
            row.remove();
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        renderIconGrid();
        attachInputListeners();
        updateLivePreview();

        // Initialize role enums from existing client metadata or old input
        @php
            $existingRoles = old('roles', $application->available_roles);
            $normalizedExistingRoles = [];
            if (is_array($existingRoles)) {
                foreach ($existingRoles as $r) {
                    if (is_array($r)) {
                        $normalizedExistingRoles[] = [
                            'value' => $r['value'] ?? '',
                            'label' => $r['label'] ?? ($r['value'] ?? ''),
                        ];
                    } elseif (is_string($r) || is_numeric($r)) {
                        $normalizedExistingRoles[] = [
                            'value' => (string) $r,
                            'label' => ucfirst((string) $r),
                        ];
                    }
                }
            }
            $existingDefault = (string) old('default_role', $application->default_role);
            $existingOutlets = old('outlets', $application->available_outlets);
            $existingHotels = old('hotels', $application->available_hotels);
        @endphp
        const initialRoles = @json($normalizedExistingRoles);
        const initialDefault = @json($existingDefault);

        if (Array.isArray(initialRoles) && initialRoles.length > 0) {
            initialRoles.forEach((roleObj) => {
                addRoleRow(roleObj.value, roleObj.label, String(roleObj.value) === String(initialDefault));
            });
        } else if (document.getElementById('toggleHasRoles')?.checked) {
            addRoleRow('admin', 'Admin', true);
            addRoleRow('user', 'User', false);
        }

        // Initialize outlets
        const initialOutlets = @json($existingOutlets);
        if (Array.isArray(initialOutlets) && initialOutlets.length > 0) {
            initialOutlets.forEach((o) => {
                if (o && typeof o === 'object') {
                    addOutletRow(o.value || '', o.label || '');
                }
            });
        } else if (document.getElementById('toggleHasOutlets')?.checked) {
            addOutletRow('', '');
        }

        // Initialize hotels
        const initialHotels = @json($existingHotels);
        if (Array.isArray(initialHotels) && initialHotels.length > 0) {
            initialHotels.forEach((h) => {
                if (h && typeof h === 'object') {
                    addHotelRow(h.value || '', h.label || '');
                }
            });
        } else if (document.getElementById('toggleHasHotels')?.checked) {
            addHotelRow('', '');
        }

        if (!document.getElementById('toggleHasRoles')?.checked) {
            toggleSection('rolesContainerWrapper', false);
        }
        if (!document.getElementById('toggleHasOutlets')?.checked) {
            toggleSection('outletsContainerWrapper', false);
        }
        if (!document.getElementById('toggleHasHotels')?.checked) {
            toggleSection('hotelsContainerWrapper', false);
        }

        document.getElementById('iconSearchInput')?.addEventListener('input', (e) => {
            renderIconGrid(e.target.value);
        });

        document.getElementById('inputIcon')?.addEventListener('input', (e) => {
            document.getElementById('iconPreviewEl').className = e.target.value;
        });
    });

    function confirmDeleteFromEdit() {
        Swal.fire({
            title: 'Hapus Aplikasi Permanen?',
            text: 'Aplikasi "{{ addslashes($application->name) }}" beserta seluruh hak akses pengguna dan sesi token OAuth yang terkait akan dihapus secara permanen. Tindakan ini TIDAK dapat dibatalkan!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus Permanen',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'rounded-2xl shadow-xl'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('deleteAppForm').submit();
            }
        });
    }
</script>
@endpush
