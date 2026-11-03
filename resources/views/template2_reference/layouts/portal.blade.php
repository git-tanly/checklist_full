<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Portal SSO') - Tanly Hospitality</title>
    <link rel="icon" href="{{ asset('images/tanly_white.svg') }}" type="image/x-icon">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: {
                            primary: '#172a40',
                            dark: '#112031',
                            subtle: '#245177',
                            light: '#f0f4f8',
                        },
                        gold: {
                            DEFAULT: '#B89047',
                            accent: '#d4af37',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Bootstrap 5 CSS (for DataTables & Modals compatibility) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons & FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: #f4f8fb;
            color: #163047;
        }

        /* Nav links styling */
        .nav-link {
            display: flex;
            align-items: center;
            padding: 0 0.85rem;
            height: 40px;
            font-size: 0.72rem;
            font-weight: 600;
            border-radius: 0.5rem;
            color: rgba(255, 255, 255, 0.75);
            transition: all 0.15s ease;
            white-space: nowrap;
            text-decoration: none;
        }

        .nav-link:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.12);
        }

        .nav-link.active {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.18);
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.15);
        }

        .nav-dropdown-trigger {
            position: relative;
        }

        .nav-dropdown-trigger>button {
            display: flex;
            align-items: center;
            padding: 0 0.85rem;
            height: 40px;
            font-size: 0.72rem;
            font-weight: 600;
            border-radius: 0.5rem;
            color: rgba(255, 255, 255, 0.75);
            transition: all 0.15s ease;
            white-space: nowrap;
            cursor: pointer;
            background: none;
            border: none;
        }

        .nav-dropdown-trigger>button:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.12);
        }

        .nav-dropdown-trigger.active>button {
            color: #fff;
            background: rgba(255, 255, 255, 0.18);
        }

        .nav-dropdown {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            margin-top: 0.5rem;
            min-width: 14rem;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
            padding: 0.5rem;
            z-index: 60;
        }

        .nav-dropdown.right-0 {
            left: auto;
            right: 0;
        }

        .nav-dropdown a,
        .nav-dropdown button {
            display: flex;
            align-items: center;
            padding: 0.5rem 0.75rem;
            font-size: 12px;
            font-weight: 500;
            color: #334155;
            border-radius: 0.5rem;
            transition: all 0.1s ease;
            text-decoration: none;
            white-space: nowrap;
            width: 100%;
        }

        .nav-dropdown a:hover,
        .nav-dropdown button:hover {
            background: #f8fafc;
            color: #172a40;
        }

        .nav-dropdown-trigger.open .nav-dropdown {
            display: block;
            animation: fadeIn 0.15s ease-out;
        }

        .nav-dropdown-trigger.open .dropdown-arrow {
            transform: rotate(180deg);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-5px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .mobile-nav-link {
            display: flex;
            align-items: center;
            padding: 0.65rem 0.85rem;
            font-size: 13px;
            font-weight: 500;
            border-radius: 0.5rem;
            color: rgba(255, 255, 255, 0.75);
            transition: all 0.1s ease;
            text-decoration: none;
        }

        .mobile-nav-link:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.1);
        }

        /* Global DataTables Custom Polish */
        div.dt-container {
            font-size: 13px;
        }

        div.dt-container select.form-select-sm,
        div.dt-container input.form-control-sm {
            border-radius: 8px;
            border-color: #cbd5e1;
            background-color: #f8fafc;
            font-size: 12px;
        }

        div.dt-container select.form-select-sm:focus,
        div.dt-container input.form-control-sm:focus {
            border-color: #172a40;
            box-shadow: 0 0 0 2px rgba(23, 42, 64, 0.15);
        }

        .pagination .page-item.active .page-link {
            background-color: #172a40 !important;
            border-color: #172a40 !important;
            color: #ffffff !important;
        }

        .pagination .page-link {
            color: #172a40;
            border-radius: 6px;
            margin: 0 2px;
            font-size: 12px;
        }
    </style>

    @stack('styles')
</head>

<body class="flex min-h-screen flex-col bg-[#f4f8fb] text-[14px] text-slate-800 antialiased">

    <!-- APP HEADER -->
    <header class="sticky top-0 z-50 flex flex-col shadow-sm">

        <!-- HEADER PRIMARY (Deep Corporate Navy) -->
        <div class="border-b border-[#0f1c2b] bg-[#172a40] text-white">
            <div class="flex h-[72px] w-full items-center justify-between px-4 lg:px-8">
                <div class="flex h-full items-center gap-2 md:gap-6">

                    <!-- Mobile Nav Toggle -->
                    <button onclick="toggleMobileNav()"
                        class="rounded-lg p-1.5 text-white/70 transition hover:bg-white/10 hover:text-white lg:hidden"
                        aria-label="Toggle Menu">
                        <svg viewBox="0 0 24 24" class="h-6 w-6 fill-none stroke-current stroke-2">
                            <path d="M4 7h16M4 12h16M4 17h16" stroke-linecap="round"></path>
                        </svg>
                    </button>

                    <!-- Brand Logo & Name -->
                    <a href="{{ route('dashboard') }}"
                        class="text-decoration-none mr-6 flex shrink-0 items-center gap-[0.8rem] transition-opacity hover:opacity-95 md:mr-8">
                        <div
                            class="flex h-[46px] w-[104px] shrink-0 items-center justify-center rounded-lg p-1.5 shadow-sm">
                            <img src="{{ asset('images/tanly_white.svg') }}" alt="Tanly Hospitality"
                                class="max-h-full max-w-full object-contain">
                        </div>
                        <div class="hidden flex-col border-l border-white/20 pl-3 leading-tight sm:flex">
                            <span class="text-[13px] font-bold tracking-wide text-white">Portal SSO</span>
                            <span class="mt-0.5 text-[9px] font-semibold uppercase tracking-widest text-white/60">Tanly
                                Hospitality</span>
                        </div>
                    </a>

                    <!-- Desktop Navigation Links -->
                    <nav class="hidden h-full items-center gap-[0.25rem] lg:flex">
                        <a href="{{ route('dashboard') }}"
                            class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i class="bi bi-grid-fill me-1.5 text-xs"></i> Dashboard
                        </a>

                        @auth
                            @if (Auth::user()->role === 'super_admin')
                                <a href="{{ route('users.index') }}"
                                    class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                                    <i class="bi bi-people-fill me-1.5 text-xs"></i> Users
                                </a>

                                <a href="{{ route('applications.index') }}"
                                    class="nav-link {{ request()->routeIs('applications.*') ? 'active' : '' }}">
                                    <i class="bi bi-app-indicator me-1.5 text-xs"></i> Applications
                                </a>

                                <a href="{{ route('logs.index') }}"
                                    class="nav-link {{ request()->routeIs('logs.*') ? 'active' : '' }}">
                                    <i class="bi bi-journal-text me-1.5 text-xs"></i> System Logs
                                </a>

                                <a href="{{ route('health.index') }}"
                                    class="nav-link {{ request()->routeIs('health.*') ? 'active' : '' }}">
                                    <i class="bi bi-activity me-1.5 text-xs"></i> System Health
                                </a>
                            @endif
                        @endauth
                    </nav>
                </div>

                <!-- Right Side: User Profile & Actions -->
                <div class="flex items-center gap-3 md:gap-5">
                    @auth
                        <!-- User Profile Menu -->
                        <div class="nav-dropdown-trigger">
                            <button onclick="toggleDropdown(this)"
                                class="group flex items-center gap-2.5 rounded-full p-1 transition hover:bg-white/10 md:rounded-lg"
                                aria-expanded="false">
                                <!-- Avatar Bulat Gold -->
                                <div
                                    class="flex h-9 w-9 items-center justify-center rounded-full border-2 border-white bg-[#B89047] text-xs font-bold text-white shadow-sm ring-2 ring-[#B89047]/30 group-hover:ring-[#B89047]">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                                </div>
                                <div class="hidden flex-col items-start text-left leading-tight sm:flex">
                                    <span class="text-[12px] font-bold text-white">{{ Auth::user()->name }}</span>
                                    <span
                                        class="text-[10px] capitalize text-white/60">{{ str_replace('_', ' ', Auth::user()->role) }}</span>
                                </div>
                                <svg viewBox="0 0 16 16"
                                    class="dropdown-arrow ml-1 hidden h-3 w-3 fill-current text-white/50 transition-transform sm:block">
                                    <path d="m4 6 4 4 4-4"></path>
                                </svg>
                            </button>

                            <div class="nav-dropdown right-0 mt-2 w-60">
                                <div class="rounded-t-lg border-b border-slate-100 bg-slate-50/70 px-4 py-3">
                                    <p class="mb-0.5 text-[10px] font-semibold uppercase tracking-wider text-slate-400">
                                        Signed in as</p>
                                    <p class="mb-0.5 truncate text-xs font-bold text-slate-800">{{ Auth::user()->name }}
                                    </p>
                                    <p class="truncate text-[11px] text-slate-500">{{ Auth::user()->email }}</p>
                                </div>
                                <div class="p-1">
                                    <a href="{{ route('profile.edit') }}"
                                        class="{{ request()->routeIs('profile.*') ? 'bg-slate-50 font-bold text-[#172a40]' : '' }}">
                                        <i class="bi bi-person-gear mr-2.5 text-sm text-slate-400"></i> My Profile
                                    </a>
                                    <a href="{{ route('devices.index') }}"
                                        class="{{ request()->routeIs('devices.*') ? 'bg-slate-50 font-bold text-[#172a40]' : '' }}">
                                        <i class="bi bi-laptop mr-2.5 text-sm text-slate-400"></i> Active Devices
                                    </a>
                                    <div class="my-1 h-px bg-slate-100"></div>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="font-medium text-red-600 hover:bg-red-50 hover:text-red-700">
                                            <i class="bi bi-box-arrow-right mr-2.5 text-sm"></i> Sign out
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}"
                            class="text-decoration-none rounded-lg bg-white/10 px-3.5 py-2 text-xs font-semibold text-white transition hover:bg-white/20">Sign
                            In</a>
                    @endauth
                </div>
            </div>
        </div>

        <!-- HEADER CONTEXT (Solid Shade Darker Navy) -->
        <div class="relative z-10 border-b border-black/15 bg-[#112031] text-white shadow-sm">
            <div class="flex h-[42px] w-full items-center justify-between px-4 lg:px-8">
                <!-- Breadcrumb -->
                <nav class="flex items-center text-[11px] font-medium text-white/60" aria-label="Breadcrumb">
                    <a href="{{ route('dashboard') }}"
                        class="text-decoration-none flex items-center text-white/70 transition hover:text-white">
                        <svg viewBox="0 0 20 20" class="mr-1.5 h-3.5 w-3.5 fill-current">
                            <path d="M3 9.2 10 3l7 6.2V17h-5v-4H8v4H3z"></path>
                        </svg>
                        <span>Portal</span>
                    </a>
                    @hasSection('breadcrumb')
                        <svg class="mx-1.5 h-3.5 w-3.5 text-white/30" viewBox="0 0 16 16" fill="none"
                            stroke="currentColor" stroke-width="2">
                            <path d="m6 3 5 5-5 5" stroke-linecap="round"></path>
                        </svg>
                        <span class="font-semibold text-white">@yield('breadcrumb')</span>
                    @else
                        <svg class="mx-1.5 h-3.5 w-3.5 text-white/30" viewBox="0 0 16 16" fill="none"
                            stroke="currentColor" stroke-width="2">
                            <path d="m6 3 5 5-5 5" stroke-linecap="round"></path>
                        </svg>
                        <span class="font-semibold text-white">@yield('title', 'Workspace')</span>
                    @endif
                </nav>

                <!-- Status Badge -->
                <div class="flex items-center gap-2">
                    <span class="hidden text-[10px] font-medium uppercase tracking-wider text-white/50 sm:block">SECURE
                        WORKSPACE</span>
                    <span class="h-1.5 w-1.5 rounded-full bg-[#B89047] shadow-[0_0_4px_#B89047]"></span>
                    <span class="text-[11px] font-bold text-white">v1.0</span>
                </div>
            </div>
        </div>

        <!-- MOBILE NAVIGATION DRAWER -->
        <div id="mobile-nav"
            class="absolute left-0 top-[72px] z-40 hidden w-full border-t border-white/10 bg-[#0b1520] shadow-xl lg:hidden">
            <div class="space-y-1 px-4 py-3">
                <a href="{{ route('dashboard') }}"
                    class="mobile-nav-link {{ request()->routeIs('dashboard') ? 'bg-white/15 text-white' : '' }}">
                    <i class="bi bi-grid-fill mr-3 text-lg text-white/50"></i> Dashboard
                </a>

                @auth
                    @if (Auth::user()->role === 'super_admin')
                        <div class="px-3 pb-1 pt-3 text-[10px] font-bold uppercase tracking-wider text-[#B89047]">
                            Administration</div>
                        <a href="{{ route('users.index') }}"
                            class="mobile-nav-link {{ request()->routeIs('users.*') ? 'bg-white/15 text-white' : '' }}">
                            <i class="bi bi-people-fill mr-3 text-lg text-white/50"></i> Users Management
                        </a>
                        <a href="{{ route('applications.index') }}"
                            class="mobile-nav-link {{ request()->routeIs('applications.*') ? 'bg-white/15 text-white' : '' }}">
                            <i class="bi bi-app-indicator mr-3 text-lg text-white/50"></i> App Management
                        </a>
                        <a href="{{ route('logs.index') }}"
                            class="mobile-nav-link {{ request()->routeIs('logs.*') ? 'bg-white/15 text-white' : '' }}">
                            <i class="bi bi-journal-text mr-3 text-lg text-white/50"></i> System Logs
                        </a>
                        <a href="{{ route('health.index') }}"
                            class="mobile-nav-link {{ request()->routeIs('health.*') ? 'bg-white/15 text-white' : '' }}">
                            <i class="bi bi-activity mr-3 text-lg text-white/50"></i> System Health
                        </a>
                    @endif

                    <div class="px-3 pb-1 pt-3 text-[10px] font-bold uppercase tracking-wider text-[#B89047]">Account</div>
                    <a href="{{ route('profile.edit') }}"
                        class="mobile-nav-link {{ request()->routeIs('profile.*') ? 'bg-white/15 text-white' : '' }}">
                        <i class="bi bi-person-gear mr-3 text-lg text-white/50"></i> My Profile
                    </a>
                    <a href="{{ route('devices.index') }}"
                        class="mobile-nav-link {{ request()->routeIs('devices.*') ? 'bg-white/15 text-white' : '' }}">
                        <i class="bi bi-laptop mr-3 text-lg text-white/50"></i> Active Devices
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- MAIN CONTENT -->
    <main class="w-full flex-1 px-4 py-6 pb-10 lg:px-8">
        @yield('content')
    </main>

    <!-- APP FOOTER (Soft Tint with Clean Border) -->
    <footer class="mt-auto border-t border-[#dce3ec] bg-[#f0f4f8]">
        <div
            class="flex min-h-[72px] w-full flex-col items-center justify-between gap-4 px-4 py-3 md:flex-row lg:px-8">

            <!-- Footer Ownership -->
            <div class="flex items-center gap-4">
                <a href="{{ route('dashboard') }}"
                    class="rounded-lg border border-slate-200 bg-[#17344c] p-1.5 shadow-sm transition hover:shadow"
                    aria-label="Tanly Hospitality">
                    <img src="{{ asset('images/tanly_white.svg') }}" alt="Tanly Hospitality"
                        class="h-6 w-auto object-contain">
                </a>
                <div class="hidden h-8 w-px bg-slate-300 sm:block" aria-hidden="true"></div>
                <div class="text-center text-[13px] leading-relaxed text-slate-600 sm:text-left">
                    <p class="mb-0">Copyright &copy; {{ date('Y') }} <strong
                            class="font-bold text-[#172a40]">Tanly Hospitality</strong></p>
                    <p class="mb-0 mt-0.5 text-[10px] font-medium uppercase tracking-wider text-slate-500">ICT &
                        Central Operational Portal</p>
                </div>
            </div>

            <!-- Footer Product Info Capsule -->
            <div
                class="flex items-center gap-3 rounded-full border border-slate-200 bg-white px-4 py-1.5 text-xs font-semibold text-[#172a40] shadow-sm">
                <span>Portal SSO</span>
                <span class="h-1.5 w-1.5 rounded-full bg-[#B89047]" aria-hidden="true"></span>
                <span class="font-medium text-slate-500">Ver 1.0</span>
            </div>
        </div>
    </footer>

    <!-- TOAST NOTIFICATION CONTAINER -->
    <div id="toast-container"
        class="fixed right-4 top-[130px] z-[100] flex w-[calc(100%-2rem)] max-w-[380px] flex-col gap-2 sm:w-auto">
    </div>

    <!-- SCRIPTS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // System Toast Configuration
        const TOAST_ICONS = {
            success: 'bi-check-circle-fill',
            warning: 'bi-exclamation-triangle-fill',
            danger: 'bi-x-circle-fill',
            error: 'bi-x-circle-fill',
            info: 'bi-info-circle-fill'
        };
        const TOAST_COLORS = {
            success: 'bg-emerald-50 border-emerald-300 text-emerald-800',
            warning: 'bg-amber-50 border-amber-300 text-amber-800',
            danger: 'bg-red-50 border-red-300 text-red-800',
            error: 'bg-red-50 border-red-300 text-red-800',
            info: 'bg-blue-50 border-blue-300 text-blue-800'
        };

        function showToast(message, type = 'info') {
            const container = document.getElementById('toast-container');
            if (!container) return;
            const toast = document.createElement('div');
            const colors = TOAST_COLORS[type] || TOAST_COLORS['info'];
            const icon = TOAST_ICONS[type] || TOAST_ICONS['info'];

            toast.className = 'flex items-start gap-2.5 px-4 py-3 rounded-xl border shadow-lg text-xs font-bold ' + colors +
                ' transform translate-x-full transition-transform duration-300 ease-out';
            toast.innerHTML = '<i class="bi ' + icon + ' text-sm shrink-0 mt-px"></i><span class="flex-1 leading-snug">' +
                message +
                '</span><button onclick="this.parentElement.remove()" class="text-current opacity-40 hover:opacity-100 shrink-0 text-sm ml-1">&times;</button>';
            container.appendChild(toast);

            requestAnimationFrame(() => {
                toast.classList.remove('translate-x-full');
            });

            setTimeout(() => {
                toast.classList.add('opacity-0', 'transition-opacity', 'duration-300');
                setTimeout(() => {
                    if (toast.parentElement) toast.remove();
                }, 300);
            }, 4500);
        }

        // Navigation Toggles
        function toggleMobileNav() {
            const nav = document.getElementById('mobile-nav');
            if (nav) nav.classList.toggle('hidden');
        }

        function toggleDropdown(btn) {
            const trigger = btn.closest('.nav-dropdown-trigger');
            const wasOpen = trigger.classList.contains('open');
            closeAllDropdowns();
            if (!wasOpen) {
                trigger.classList.add('open');
            }
        }

        function closeAllDropdowns() {
            document.querySelectorAll('.nav-dropdown-trigger.open').forEach(el => el.classList.remove('open'));
        }

        document.addEventListener('click', (e) => {
            if (!e.target.closest('.nav-dropdown-trigger')) {
                closeAllDropdowns();
            }
        });
    </script>

    <!-- Session Flash Message Render -->
    @if (session('success'))
        <script>
            document.addEventListener("DOMContentLoaded", () => showToast({!! json_encode(session('success')) !!}, "success"));
        </script>
    @endif
    @if (session('error'))
        <script>
            document.addEventListener("DOMContentLoaded", () => showToast({!! json_encode(session('error')) !!}, "error"));
        </script>
    @endif
    @if (session('warning'))
        <script>
            document.addEventListener("DOMContentLoaded", () => showToast({!! json_encode(session('warning')) !!}, "warning"));
        </script>
    @endif
    @if (session('info'))
        <script>
            document.addEventListener("DOMContentLoaded", () => showToast({!! json_encode(session('info')) !!}, "info"));
        </script>
    @endif
    @if (isset($errors) && $errors->any())
        <script>
            document.addEventListener("DOMContentLoaded", () => {
                const errs = {!! json_encode($errors->all()) !!};
                showToast("<strong>Validasi Gagal:</strong><br>• " + errs.join("<br>• "), "error");
            });
        </script>
    @endif

    @stack('scripts')

</body>

</html>
