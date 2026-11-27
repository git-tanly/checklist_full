<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Operational Checklist') - Checklist Operasional</title>
    <link rel="icon" href="{{ asset('images/tanly_white.svg') }}" type="image/x-icon">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN (konfigurasi sama dengan layouts/portal) -->
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

    <!-- Bootstrap 5.3.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Tabler Icons (sama dengan layouts/portal) -->
    <link rel="stylesheet" href="{{ asset('template/dist/assets/fonts/tabler-icons.min.css') }}">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        :root {
            --navy: #172a40;
            --navy-dark: #112031;
            --navy-subtle: #245177;
            --gold: #B89047;
            --surface: #f4f8fb;
        }

        * {
            -webkit-tap-highlight-color: transparent;
        }

        html,
        body {
            overscroll-behavior-y: none;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: var(--surface);
            color: #163047;
        }

        .btn-navy {
            background-color: var(--navy);
            color: #fff;
            border: none;
            font-weight: 600;
            border-radius: 0.75rem;
            transition: background-color .15s ease, transform .1s ease;
        }

        .btn-navy:hover {
            background-color: var(--navy-dark);
            color: #fff;
        }

        .btn-navy:active {
            transform: scale(.98);
        }

        .btn-navy:disabled {
            opacity: .6;
        }

        .card {
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            box-shadow: 0 1px 2px rgba(16, 42, 67, .06);
        }

        .yes-no-option input[type="radio"] {
            display: none;
        }

        .yes-no-option span {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 48px;
            border: 1.5px solid #cbd5e1;
            border-radius: .75rem;
            font-weight: 600;
            font-size: .9rem;
            background: #fff;
            transition: all .15s ease;
        }

        .yes-no-option input[type="radio"]:checked+span {
            border-color: var(--navy);
            background: var(--navy);
            color: #fff;
        }

        .yes-no-option input.yes-input[type="radio"]:checked+span {
            background: #047857;
            border-color: #047857;
        }

        .yes-no-option input.no-input[type="radio"]:checked+span {
            background: #b91c1c;
            border-color: #b91c1c;
        }
    </style>

    @yield('styles')
</head>

<body class="min-h-screen d-flex flex-column">
    <!-- HEADER -->
    <header class="bg-[#172a40] text-white">
        <div class="flex items-center justify-between gap-3 px-4 py-4 sm:px-6">
            <div class="flex items-center gap-3 min-w-0">
                <img src="{{ asset('images/tanly_white.svg') }}" alt="Tanly" class="h-8 w-auto shrink-0">
                <div class="min-w-0">
                    <p class="mb-0 text-[10px] font-semibold uppercase tracking-wider text-white/50">
                        Checklist Operasional
                    </p>
                    <h1 class="mb-0 truncate text-base font-bold leading-tight">
                        @yield('header_title', 'Checklist Harian')
                    </h1>
                </div>
            </div>
            @yield('header_action')
        </div>
    </header>

    <!-- MAIN -->
    <main class="flex-1 w-full px-4 py-5 pb-16 sm:px-6">
        <div class="mx-auto w-full max-w-xl">
            @if (session('success'))
                <div class="mb-4 rounded-xl border border-emerald-300 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 rounded-xl border border-red-300 bg-red-50 px-4 py-3 text-sm font-semibold text-red-800">
                    {{ session('error') }}
                </div>
            @endif
            @if (isset($errors) && $errors->any())
                <div class="mb-4 rounded-xl border border-red-300 bg-red-50 px-4 py-3 text-sm font-semibold text-red-800">
                    {{ $errors->first() }}
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- FOOTER -->
    <footer class="border-t border-white/10 bg-[#112031] py-4 text-center">
        <p class="mb-0 text-[11px] text-white/40">
            Checklist Operasional &middot; Tanly Hospitality &middot; {{ date('Y') }}
        </p>
    </footer>

    <!-- SCRIPTS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @yield('scripts')
</body>

</html>
