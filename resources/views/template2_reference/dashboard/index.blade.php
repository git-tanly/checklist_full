@extends('layouts.portal')

@section('title', 'Dashboard')
@section('breadcrumb', 'Operational Systems')

@section('content')
<div class="flex flex-col gap-6">

    <!-- PAGE HEADER -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white border border-slate-200/90 rounded-2xl p-5 md:p-6 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-[#172a40] text-white flex items-center justify-center text-xl shadow-sm shrink-0">
                <i class="bi bi-grid-1x2-fill"></i>
            </div>
            <div>
                <h1 class="text-lg md:text-xl font-bold text-slate-800 tracking-tight mb-0.5">Tanly Hospitality Portal</h1>
                <p class="text-xs text-slate-500 mb-0">Pusat akses terintegrasi seluruh layanan dan sistem operasional digital.</p>
            </div>
        </div>

        <div class="flex items-center gap-2 self-start md:self-auto">
            <div class="bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs text-slate-600 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <span class="font-medium">Single Sign-On Active</span>
            </div>
        </div>
    </div>

    <!-- OPERATIONAL SYSTEMS TOOLBAR & FILTERS -->
    <div class="flex flex-col gap-4">

        <!-- Top Row: Section Title, Search, and View Switcher -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 px-1">
            <div>
                <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-0.5">Operational Systems</h2>
                <p class="text-xs text-slate-500 mb-0">Pilih modul operasional untuk membuka sesi kerja aplikasi.</p>
            </div>

            <div class="flex items-center gap-2.5 self-stretch sm:self-auto">
                <!-- Live Search Apps -->
                <div class="relative flex-1 sm:w-64">
                    <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" id="appFilterInput" placeholder="Cari aplikasi atau fitur..." 
                           class="w-full bg-white border border-slate-300 rounded-xl pl-9 pr-3.5 py-2 text-xs text-slate-700 focus:outline-none focus:border-[#172a40] focus:ring-2 focus:ring-[#172a40]/10 transition">
                </div>

                <!-- Dual View Switcher (Grid vs List) -->
                <div class="inline-flex bg-slate-100 p-1 rounded-xl border border-slate-200 shrink-0">
                    <button type="button" id="btnViewGrid" class="p-1.5 rounded-lg text-xs font-semibold text-white bg-[#172a40] shadow-sm transition" title="Tampilan Grid Kompak">
                        <i class="bi bi-grid-fill px-1"></i>
                    </button>
                    <button type="button" id="btnViewList" class="p-1.5 rounded-lg text-xs font-semibold text-slate-500 hover:text-slate-800 transition" title="Tampilan List Padat">
                        <i class="bi bi-list-ul px-1"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Department Category Filter Tabs (Pills) -->
        <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none">
            <!-- All Systems Tab -->
            <button type="button" class="category-pill active inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold border transition whitespace-nowrap bg-[#172a40] text-white border-[#172a40]" data-category="all">
                <span>All Systems</span>
                <span class="bg-white/20 text-white text-[10px] px-1.5 py-0.2 rounded-full font-mono">{{ $categoryCounts['all'] ?? count($allPortalApps) }}</span>
            </button>

            <!-- Department Pills -->
            @foreach ($categories as $catKey => $cat)
                <button type="button" class="category-pill inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold border transition whitespace-nowrap bg-white text-slate-600 border-slate-200 hover:bg-slate-50" data-category="{{ $catKey }}">
                    <i class="{{ $cat['icon'] }} text-[11px]"></i>
                    <span>{{ $cat['label'] }}</span>
                    <span class="bg-slate-100 text-slate-500 text-[10px] px-1.5 py-0.2 rounded-full font-mono border border-slate-200">{{ $categoryCounts[$catKey] ?? 0 }}</span>
                </button>
            @endforeach
        </div>

        <!-- 1. COMPACT GRID VIEW -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3.5" id="appsGridView">
            @forelse ($allPortalApps as $app)
                @php
                    $catDetails = $app['category_details'];
                    $featuresJson = json_encode($app['features']);
                    $appJson = json_encode([
                        'id'          => $app['id'],
                        'name'        => $app['name'],
                        'icon'        => $app['icon'],
                        'category'    => $catDetails['label'],
                        'category_id' => $app['category'],
                        'version'     => $app['version'],
                        'tagline'     => $app['tagline'],
                        'description' => $app['description'],
                        'features'    => $app['features'],
                        'status'      => $app['status'],
                        'has_access'  => $app['has_access'],
                        'url'         => $app['url'],
                        'redirect'    => $app['raw_redirect'],
                    ]);
                @endphp
                <div class="app-card group bg-white border border-slate-200/90 hover:border-slate-300 rounded-2xl p-4 shadow-sm hover:shadow-md transition-all duration-200 flex flex-col justify-between"
                     data-name="{{ strtolower($app['name']) }}"
                     data-category="{{ $app['category'] }}"
                     data-tagline="{{ strtolower($app['tagline'] ?? '') }}"
                     data-desc="{{ strtolower($app['description'] ?? '') }}">
                    <div>
                        <!-- Top Header: Category Badge + Version Tag -->
                        <div class="flex items-center justify-between gap-2 mb-3">
                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-lg text-[10px] font-semibold border {{ $catDetails['badge'] }}">
                                <i class="{{ $catDetails['icon'] }} text-[9px]"></i> {{ $catDetails['label'] }}
                            </span>
                            <span class="text-[10px] font-mono text-slate-400 bg-slate-100 px-1.5 py-0.5 rounded-md border border-slate-200">
                                v{{ $app['version'] }}
                            </span>
                        </div>

                        <!-- App Identity: Icon + Name -->
                        <div class="flex items-start gap-3 mb-2">
                            <div class="w-10 h-10 rounded-xl bg-[#172a40]/5 text-[#172a40] flex items-center justify-center text-lg group-hover:scale-105 transition-transform shrink-0 mt-0.5">
                                <i class="{{ $app['icon'] }}"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <h3 class="text-sm font-bold text-slate-800 tracking-tight group-hover:text-[#172a40] transition-colors truncate mb-0.5">
                                    {{ $app['name'] }}
                                </h3>
                                @if (!empty($app['tagline']))
                                    <p class="text-[11px] font-medium text-slate-500 line-clamp-1 mb-0">
                                        {{ $app['tagline'] }}
                                    </p>
                                @else
                                    <p class="text-[11px] text-slate-400 line-clamp-1 mb-0">
                                        Modul Operasional Satelit
                                    </p>
                                @endif
                            </div>
                        </div>

                        <!-- Short Description (Compact clamped 2 lines) -->
                        <p class="text-[11px] text-slate-500 leading-relaxed line-clamp-2 mb-3">
                            {{ $app['description'] }}
                        </p>
                    </div>

                    <!-- Card Footer: Status Indicator + Info Modal Trigger + Action Button -->
                    <div class="mt-auto pt-2.5 border-t border-slate-100 flex items-center justify-between gap-2">
                        <!-- Status Badge -->
                        <div class="flex items-center gap-1.5">
                            @if ($app['status'] == 'maintenance')
                                <span class="w-2 h-2 rounded-full bg-amber-500 shrink-0"></span>
                                <span class="text-[10px] font-semibold text-amber-700">Maint</span>
                            @elseif ($app['has_access'])
                                <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                                <span class="text-[10px] font-semibold text-emerald-700">Active</span>
                            @else
                                <i class="bi bi-lock-fill text-[10px] text-slate-400"></i>
                                <span class="text-[10px] font-medium text-slate-400">Locked</span>
                            @endif
                        </div>

                        <!-- Action Buttons: Quick Info + Launch -->
                        <div class="flex items-center gap-1.5">
                            <!-- Quick Info Trigger Button -->
                            <button type="button" class="btn-open-app-modal w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center text-xs transition" 
                                    data-app="{{ htmlspecialchars($appJson, ENT_QUOTES, 'UTF-8') }}" 
                                    title="Lihat Detail & Fitur Aplikasi">
                                <i class="bi bi-info-circle"></i>
                            </button>

                            <!-- Direct Launch Button -->
                            @if ($app['status'] == 'maintenance')
                                <button disabled class="bg-amber-50 text-amber-600 text-[11px] font-semibold px-2.5 py-1 rounded-lg border border-amber-200 cursor-not-allowed">
                                    Maint
                                </button>
                            @elseif ($app['has_access'])
                                <a href="{{ $app['url'] }}" target="_blank" rel="noopener noreferrer"
                                   class="inline-flex items-center gap-1 bg-[#172a40] hover:bg-[#112031] text-white text-[11px] font-semibold px-2.5 py-1 rounded-lg shadow-sm transition text-decoration-none">
                                    <span>Buka</span>
                                    <i class="bi bi-box-arrow-up-right text-[9px]"></i>
                                </a>
                            @else
                                <button disabled class="bg-slate-100 text-slate-400 text-[11px] font-medium px-2.5 py-1 rounded-lg border border-slate-200 cursor-not-allowed">
                                    <i class="bi bi-lock-fill"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 text-center bg-white rounded-2xl border border-slate-200">
                    <i class="bi bi-shield-lock text-3xl text-slate-300 mb-2 d-block"></i>
                    <p class="text-xs font-semibold text-slate-700 mb-1">Belum Ada Akses Aplikasi</p>
                    <p class="text-xs text-slate-400 mb-0">Akun Anda belum memiliki hak akses ke sistem aplikasi operasional. Hubungi Administrator untuk penugasan aplikasi.</p>
                </div>
            @endforelse
        </div>

        <!-- 2. COMPACT LIST / TABLE VIEW (Alternative dense view for scalability) -->
        <div class="hidden bg-white border border-slate-200/90 rounded-2xl shadow-sm overflow-hidden" id="appsListView">
            <div class="table-responsive mb-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-slate-50 border-b border-slate-200 text-[#172a40]">
                        <tr>
                            <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-slate-600">Application</th>
                            <th class="py-3 text-xs font-bold uppercase tracking-wider text-slate-600">Category</th>
                            <th class="py-3 text-xs font-bold uppercase tracking-wider text-slate-600">Version</th>
                            <th class="py-3 text-xs font-bold uppercase tracking-wider text-slate-600">Access Status</th>
                            <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-slate-600 text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($allPortalApps as $app)
                            @php
                                $catDetails = $app['category_details'];
                                $appJson = json_encode([
                                    'id'          => $app['id'],
                                    'name'        => $app['name'],
                                    'icon'        => $app['icon'],
                                    'category'    => $catDetails['label'],
                                    'category_id' => $app['category'],
                                    'version'     => $app['version'],
                                    'tagline'     => $app['tagline'],
                                    'description' => $app['description'],
                                    'features'    => $app['features'],
                                    'status'      => $app['status'],
                                    'has_access'  => $app['has_access'],
                                    'url'         => $app['url'],
                                    'redirect'    => $app['raw_redirect'],
                                ]);
                            @endphp
                            <tr class="app-row" 
                                data-name="{{ strtolower($app['name']) }}"
                                data-category="{{ $app['category'] }}"
                                data-tagline="{{ strtolower($app['tagline'] ?? '') }}"
                                data-desc="{{ strtolower($app['description'] ?? '') }}">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-[#172a40]/5 text-[#172a40] flex items-center justify-center text-base shrink-0">
                                            <i class="{{ $app['icon'] }}"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-bold text-slate-800 text-[13px]">{{ $app['name'] }}</div>
                                            <div class="text-[11px] text-slate-400 truncate max-w-xs">{{ $app['tagline'] ?: $app['description'] }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg text-[10px] font-semibold border {{ $catDetails['badge'] }}">
                                        <i class="{{ $catDetails['icon'] }} text-[9px]"></i> {{ $catDetails['label'] }}
                                    </span>
                                </td>
                                <td class="py-3 font-mono text-[11px] text-slate-500">
                                    v{{ $app['version'] }}
                                </td>
                                <td class="py-3">
                                    @if ($app['status'] == 'maintenance')
                                        <span class="bg-amber-50 text-amber-700 border border-amber-200 text-[10px] font-semibold px-2.5 py-0.5 rounded-full inline-flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Maintenance
                                        </span>
                                    @elseif ($app['has_access'])
                                        <span class="bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-semibold px-2.5 py-0.5 rounded-full inline-flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active Access
                                        </span>
                                    @else
                                        <span class="bg-slate-100 text-slate-500 border border-slate-200 text-[10px] font-medium px-2 py-0.5 rounded-full inline-flex items-center gap-1">
                                            <i class="bi bi-lock-fill text-[9px]"></i> Locked
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-end">
                                    <div class="flex items-center justify-end gap-2">
                                        <button type="button" class="btn-open-app-modal btn btn-light btn-sm text-slate-600 border" 
                                                data-app="{{ htmlspecialchars($appJson, ENT_QUOTES, 'UTF-8') }}"
                                                title="Lihat Detail">
                                            <i class="bi bi-info-circle"></i>
                                        </button>
                                        @if ($app['status'] == 'maintenance')
                                            <button disabled class="btn btn-sm btn-light text-amber-600 border border-amber-200 cursor-not-allowed text-xs">
                                                Maint
                                            </button>
                                        @elseif ($app['has_access'])
                                            <a href="{{ $app['url'] }}" target="_blank" rel="noopener noreferrer" 
                                               class="btn btn-sm bg-[#172a40] hover:bg-[#112031] text-white text-xs font-semibold px-3 text-decoration-none">
                                                Buka <i class="bi bi-box-arrow-up-right ms-1 text-[10px]"></i>
                                            </a>
                                        @else
                                            <button disabled class="btn btn-sm btn-light text-slate-400 border cursor-not-allowed text-xs">
                                                Locked
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-xs text-slate-400">
                                    <i class="bi bi-shield-lock text-base text-slate-300 me-1"></i>
                                    Belum ada akses aplikasi yang ditugaskan ke akun Anda. Hubungi Administrator.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Empty Search Notice -->
        <div id="noAppsFound" class="hidden py-12 text-center bg-white rounded-2xl border border-slate-200">
            <i class="bi bi-search text-3xl text-slate-300 mb-2 d-block"></i>
            <p class="text-xs font-semibold text-slate-700 mb-1">Aplikasi Tidak Ditemukan</p>
            <p class="text-xs text-slate-400 mb-0">Tidak ada aplikasi yang cocok dengan filter atau kata kunci pencarian Anda.</p>
        </div>

    </div>

</div>

<!-- APP DETAIL & FEATURES SHOWCASE MODAL -->
<div class="modal fade" id="appDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-2xl border-0 shadow-2xl overflow-hidden">
            
            <!-- Modal Header -->
            <div class="bg-[#172a40] text-white p-5 md:p-6 relative">
                <button type="button" class="btn-close btn-close-white absolute top-5 right-5" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="flex items-start gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-white/10 text-white flex items-center justify-center text-2xl border border-white/20 shrink-0" id="modalAppIcon">
                        <i class="fa-solid fa-server"></i>
                    </div>
                    <div class="min-w-0 flex-1 pr-6">
                        <div class="flex items-center gap-2 mb-1.5 flex-wrap">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg text-[10px] font-bold bg-[#B89047] text-white uppercase tracking-wider" id="modalAppCategory">
                                Engineering
                            </span>
                            <span class="text-[11px] font-mono text-white/70 bg-white/10 px-2 py-0.5 rounded-md" id="modalAppVersion">
                                v1.0.0
                            </span>
                            <span class="text-[11px] font-medium" id="modalAppStatus"></span>
                        </div>
                        <h2 class="text-lg md:text-xl font-bold text-white tracking-tight mb-0.5" id="modalAppName">
                            Nama Aplikasi
                        </h2>
                        <p class="text-xs text-slate-300 mb-0 leading-snug" id="modalAppTagline">
                            Tagline Slogan Aplikasi
                        </p>
                    </div>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="p-5 md:p-6 bg-slate-50 space-y-5 max-h-[70vh] overflow-y-auto">
                
                <!-- Description Section -->
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 mb-2 flex items-center gap-1.5">
                        <i class="bi bi-file-text text-[#B89047]"></i> Deskripsi Operasional
                    </h4>
                    <p class="text-xs text-slate-600 leading-relaxed mb-0" id="modalAppDescription">
                        Deskripsi lengkap dari aplikasi operasional ini.
                    </p>
                </div>

                <!-- Key Features Highlights (Rendered from metadata features[]) -->
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm" id="modalFeaturesContainer">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 mb-3 flex items-center gap-1.5">
                        <i class="fa-solid fa-star text-[#B89047]"></i> Fitur Utama & Keunggulan
                    </h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3" id="modalFeaturesList">
                        <!-- Dynamic items rendered via JS -->
                    </div>
                </div>

                <!-- Technical & OAuth Info -->
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 mb-3 flex items-center gap-1.5">
                        <i class="bi bi-shield-check text-emerald-600"></i> Informasi Integrasi SSO
                    </h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        <div class="bg-slate-50 p-2.5 rounded-lg border border-slate-200">
                            <span class="text-[11px] text-slate-400 block mb-0.5">OAuth Client ID</span>
                            <code class="text-[11px] text-slate-700 font-mono break-all" id="modalAppId">-</code>
                        </div>
                        <div class="bg-slate-50 p-2.5 rounded-lg border border-slate-200">
                            <span class="text-[11px] text-slate-400 block mb-0.5">Callback Redirect URL</span>
                            <span class="text-[11px] text-slate-700 font-mono truncate block" id="modalAppRedirect" title="">-</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Modal Footer -->
            <div class="p-4 bg-white border-t border-slate-200 flex items-center justify-between gap-3">
                <button type="button" class="btn btn-light btn-sm text-xs font-semibold px-4 py-2 rounded-xl text-slate-600 border" data-bs-dismiss="modal">
                    Tutup
                </button>
                <div id="modalActionContainer">
                    <!-- Dynamic launch button -->
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Elements
        const filterInput = document.getElementById('appFilterInput');
        const categoryPills = document.querySelectorAll('.category-pill');
        const gridView = document.getElementById('appsGridView');
        const listView = document.getElementById('appsListView');
        const btnViewGrid = document.getElementById('btnViewGrid');
        const btnViewList = document.getElementById('btnViewList');
        const noApps = document.getElementById('noAppsFound');

        let currentCategory = 'all';
        let currentKeyword = '';

        // 1. Dual View Switcher Logic (Persistent via localStorage)
        const savedView = localStorage.getItem('tanly_portal_view') || 'grid';
        setViewMode(savedView);

        btnViewGrid.addEventListener('click', () => {
            setViewMode('grid');
            localStorage.setItem('tanly_portal_view', 'grid');
        });

        btnViewList.addEventListener('click', () => {
            setViewMode('list');
            localStorage.setItem('tanly_portal_view', 'list');
        });

        function setViewMode(mode) {
            if (mode === 'list') {
                gridView.classList.add('hidden');
                listView.classList.remove('hidden');

                btnViewList.classList.remove('text-slate-500', 'hover:text-slate-800');
                btnViewList.classList.add('text-white', 'bg-[#172a40]', 'shadow-sm');

                btnViewGrid.classList.remove('text-white', 'bg-[#172a40]', 'shadow-sm');
                btnViewGrid.classList.add('text-slate-500', 'hover:text-slate-800');
            } else {
                listView.classList.add('hidden');
                gridView.classList.remove('hidden');

                btnViewGrid.classList.remove('text-slate-500', 'hover:text-slate-800');
                btnViewGrid.classList.add('text-white', 'bg-[#172a40]', 'shadow-sm');

                btnViewList.classList.remove('text-white', 'bg-[#172a40]', 'shadow-sm');
                btnViewList.classList.add('text-slate-500', 'hover:text-slate-800');
            }
        }

        // 2. Category Tab Filtering
        categoryPills.forEach(pill => {
            pill.addEventListener('click', () => {
                categoryPills.forEach(p => {
                    p.classList.remove('bg-[#172a40]', 'text-white', 'border-[#172a40]', 'active');
                    p.classList.add('bg-white', 'text-slate-600', 'border-slate-200');
                });

                pill.classList.remove('bg-white', 'text-slate-600', 'border-slate-200');
                pill.classList.add('bg-[#172a40]', 'text-white', 'border-[#172a40]', 'active');

                currentCategory = pill.getAttribute('data-category') || 'all';
                applyFilters();
            });
        });

        // 3. Live Search Input
        if (filterInput) {
            filterInput.addEventListener('input', (e) => {
                currentKeyword = e.target.value.toLowerCase().trim();
                applyFilters();
            });
        }

        // Master Filter Function
        function applyFilters() {
            const cards = document.querySelectorAll('.app-card');
            const rows = document.querySelectorAll('.app-row');
            let visibleCount = 0;

            cards.forEach(card => {
                const name = card.getAttribute('data-name') || '';
                const cat = card.getAttribute('data-category') || '';
                const tagline = card.getAttribute('data-tagline') || '';
                const desc = card.getAttribute('data-desc') || '';

                const matchesCat = (currentCategory === 'all' || cat === currentCategory);
                const matchesKeyword = !currentKeyword || 
                    name.includes(currentKeyword) || 
                    tagline.includes(currentKeyword) || 
                    desc.includes(currentKeyword);

                if (matchesCat && matchesKeyword) {
                    card.classList.remove('hidden');
                    visibleCount++;
                } else {
                    card.classList.add('hidden');
                }
            });

            rows.forEach(row => {
                const name = row.getAttribute('data-name') || '';
                const cat = row.getAttribute('data-category') || '';
                const tagline = row.getAttribute('data-tagline') || '';
                const desc = row.getAttribute('data-desc') || '';

                const matchesCat = (currentCategory === 'all' || cat === currentCategory);
                const matchesKeyword = !currentKeyword || 
                    name.includes(currentKeyword) || 
                    tagline.includes(currentKeyword) || 
                    desc.includes(currentKeyword);

                if (matchesCat && matchesKeyword) {
                    row.classList.remove('hidden');
                } else {
                    row.classList.add('hidden');
                }
            });

            if (noApps) {
                if (visibleCount === 0 && cards.length > 0) {
                    noApps.classList.remove('hidden');
                } else {
                    noApps.classList.add('hidden');
                }
            }
        }

        // 4. Modal Detail Trigger Logic
        const detailModalEl = document.getElementById('appDetailModal');
        const detailModal = detailModalEl ? new bootstrap.Modal(detailModalEl) : null;

        document.querySelectorAll('.btn-open-app-modal').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                const appDataStr = btn.getAttribute('data-app');
                if (!appDataStr) return;

                try {
                    const app = JSON.parse(appDataStr);

                    // Populate Header
                    document.getElementById('modalAppName').textContent = app.name || '-';
                    document.getElementById('modalAppTagline').textContent = app.tagline || 'Modul Operasional Terintegrasi SSO';
                    document.getElementById('modalAppCategory').textContent = app.category || 'General';
                    document.getElementById('modalAppVersion').textContent = 'v' + (app.version || '1.0.0');

                    const iconEl = document.getElementById('modalAppIcon');
                    if (iconEl) {
                        iconEl.innerHTML = `<i class="${app.icon || 'fa-solid fa-server'}"></i>`;
                    }

                    // Status Badge in Header
                    const statusEl = document.getElementById('modalAppStatus');
                    if (statusEl) {
                        if (app.status === 'maintenance') {
                            statusEl.innerHTML = '<span class="bg-amber-500/20 text-amber-300 border border-amber-400/30 text-[10px] font-semibold px-2 py-0.5 rounded-md">Maintenance</span>';
                        } else if (app.has_access) {
                            statusEl.innerHTML = '<span class="bg-emerald-500/20 text-emerald-300 border border-emerald-400/30 text-[10px] font-semibold px-2 py-0.5 rounded-md">Active Access</span>';
                        } else {
                            statusEl.innerHTML = '<span class="bg-red-500/20 text-red-300 border border-red-400/30 text-[10px] font-semibold px-2 py-0.5 rounded-md">No Access</span>';
                        }
                    }

                    // Description
                    document.getElementById('modalAppDescription').textContent = app.description || 'Tidak ada deskripsi rinci untuk aplikasi ini.';

                    // Key Features List
                    const featuresContainer = document.getElementById('modalFeaturesContainer');
                    const featuresList = document.getElementById('modalFeaturesList');
                    featuresList.innerHTML = '';

                    if (app.features && Array.isArray(app.features) && app.features.length > 0) {
                        featuresContainer.classList.remove('hidden');
                        app.features.forEach(feat => {
                            const featCard = document.createElement('div');
                            featCard.className = 'flex items-start gap-2.5 p-2.5 rounded-lg bg-slate-50 border border-slate-200';
                            featCard.innerHTML = `
                                <div class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-[10px] shrink-0 mt-0.5">
                                    <i class="fa-solid fa-check"></i>
                                </div>
                                <div class="min-w-0">
                                    <div class="font-bold text-slate-800 text-xs leading-snug">${escapeHtml(feat.title || 'Fitur Unggulan')}</div>
                                    <div class="text-[11px] text-slate-500 leading-tight">${escapeHtml(feat.desc || '')}</div>
                                </div>
                            `;
                            featuresList.appendChild(featCard);
                        });
                    } else {
                        featuresList.innerHTML = '<p class="text-xs text-slate-400 italic mb-0 col-span-full">Belum ada daftar fitur spesifik yang didaftarkan pada metadata aplikasi ini.</p>';
                    }

                    // Technical Info
                    document.getElementById('modalAppId').textContent = app.id || '-';
                    const redirectEl = document.getElementById('modalAppRedirect');
                    if (redirectEl) {
                        redirectEl.textContent = app.redirect || '-';
                        redirectEl.title = app.redirect || '';
                    }

                    // Action Button in Footer
                    const actionContainer = document.getElementById('modalActionContainer');
                    if (actionContainer) {
                        if (app.status === 'maintenance') {
                            actionContainer.innerHTML = `
                                <button disabled class="btn btn-warning btn-sm text-xs font-semibold px-4 py-2 rounded-xl cursor-not-allowed">
                                    <i class="bi bi-wrench-adjustable me-1"></i> Sedang Pemeliharaan
                                </button>
                            `;
                        } else if (app.has_access) {
                            actionContainer.innerHTML = `
                                <a href="${app.url}" target="_blank" rel="noopener noreferrer" 
                                   class="btn bg-[#172a40] hover:bg-[#112031] text-white btn-sm text-xs font-semibold px-4 py-2 rounded-xl shadow-sm text-decoration-none">
                                    <span>Buka Aplikasi Sekarang</span>
                                    <i class="bi bi-box-arrow-up-right ms-1 text-[10px]"></i>
                                </a>
                            `;
                        } else {
                            actionContainer.innerHTML = `
                                <button disabled class="btn btn-secondary btn-sm text-xs font-medium px-4 py-2 rounded-xl cursor-not-allowed">
                                    <i class="bi bi-lock-fill me-1"></i> Akses Terkunci
                                </button>
                            `;
                        }
                    }

                    if (detailModal) {
                        detailModal.show();
                    }
                } catch (err) {
                    console.error('Error parsing app modal data:', err);
                }
            });
        });

        function escapeHtml(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }
    });
</script>
@endpush
