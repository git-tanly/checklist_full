@extends('layouts.portal')

@section('title', 'App Management')
@section('breadcrumb', 'App Management')

@section('content')
<div class="flex flex-col gap-6">

    @if (session('new_client_credentials'))
        @php
            $newCreds = session('new_client_credentials');
        @endphp
        <!-- NEW CLIENT CREDENTIALS POP-UP MODAL -->
        <div id="credentialsModal" class="fixed inset-0 z-[999] flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm transition-all duration-300">
            <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 max-w-lg w-full p-6 relative overflow-hidden transform transition-all">

                <!-- Gold/Navy Top Accent Line -->
                <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-[#172a40] via-[#B89047] to-[#172a40]"></div>

                <!-- Header -->
                <div class="flex items-start justify-between mb-4 pt-1">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-xl bg-amber-50 border border-amber-200 text-amber-600 flex items-center justify-center text-xl shrink-0 shadow-sm">
                            <i class="bi bi-shield-lock-fill"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-800 mb-0.5">Application Credentials</h3>
                            <p class="text-xs text-slate-500 mb-0">Kredensial OAuth2 untuk <strong>{{ $newCreds['name'] }}</strong></p>
                        </div>
                    </div>
                    <button type="button" onclick="closeCredentialsModal()" class="text-slate-400 hover:text-slate-700 p-1.5 rounded-lg hover:bg-slate-100 transition" title="Tutup Modal">
                        <i class="bi bi-x-lg text-sm"></i>
                    </button>
                </div>

                <!-- Strict Warning Alert Box -->
                <div class="bg-rose-50 border border-rose-200 rounded-xl p-3.5 mb-5 flex items-start gap-3 text-xs text-rose-800">
                    <i class="bi bi-exclamation-triangle-fill text-rose-600 text-base shrink-0 mt-0.5"></i>
                    <div class="leading-relaxed">
                        <strong class="font-bold block text-rose-900 mb-0.5">Simpan Client Secret Sekarang!</strong>
                        <span>Client Secret hanya akan <strong>ditampilkan satu kali ini saja</strong> demi keamanan enkripsi. Jika modal ini ditutup, secret tidak dapat dilihat kembali.</span>
                    </div>
                </div>

                <!-- Credential Fields -->
                <div class="space-y-3.5 mb-6">
                    <!-- Client ID -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Client ID (UUID)</label>
                            <button type="button" onclick="copyCredText('{{ $newCreds['id'] }}', 'Client ID')" class="text-xs font-semibold text-[#172a40] hover:text-[#B89047] flex items-center gap-1 transition">
                                <i class="bi bi-clipboard"></i> Salin ID
                            </button>
                        </div>
                        <div class="flex items-center bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 font-mono text-xs text-slate-700 select-all">
                            <span class="truncate flex-1">{{ $newCreds['id'] }}</span>
                        </div>
                    </div>

                    <!-- Client Secret -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-[11px] font-bold text-amber-700 uppercase tracking-wider flex items-center gap-1">
                                <i class="bi bi-key-fill"></i> Client Secret
                            </label>
                            <button type="button" onclick="copyCredText('{{ $newCreds['secret'] }}', 'Client Secret')" class="text-xs font-bold text-amber-800 bg-amber-100 hover:bg-amber-200 px-2.5 py-1 rounded-lg flex items-center gap-1.5 transition">
                                <i class="bi bi-clipboard-check"></i> Salin Secret
                            </button>
                        </div>
                        <div class="relative">
                            <input type="text" readonly value="{{ $newCreds['secret'] }}"
                                   class="w-full bg-amber-50/50 border-2 border-amber-300 rounded-xl px-3 py-2.5 font-mono text-xs font-bold text-slate-800 focus:outline-none select-all">
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-4 border-t border-slate-100">
                    <button type="button" onclick="copyEnvFormat('{{ $newCreds['id'] }}', '{{ $newCreds['secret'] }}')" class="w-full sm:w-auto bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold py-2.5 px-3.5 rounded-xl transition flex items-center justify-center gap-2">
                        <i class="bi bi-file-earmark-code"></i> Salin Format .env
                    </button>
                    <button type="button" onclick="closeCredentialsModal()" class="w-full sm:w-auto bg-[#172a40] hover:bg-[#112031] text-white text-xs font-bold py-2.5 px-5 rounded-xl shadow-sm transition flex items-center justify-center gap-1.5">
                        <i class="bi bi-check2-circle"></i> Saya Sudah Menyimpan
                    </button>
                </div>

            </div>
        </div>
    @endif

    <!-- PAGE HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white border border-slate-200/90 rounded-2xl p-5 md:p-6 shadow-sm">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('dashboard') }}" class="text-slate-400 hover:text-slate-600 text-xs font-medium flex items-center gap-1 text-decoration-none">
                    <i class="bi bi-arrow-left"></i> Dashboard
                </a>
                <span class="text-slate-300">/</span>
                <span class="text-xs font-semibold text-slate-600">Applications</span>
            </div>
            <h1 class="text-lg md:text-xl font-bold text-slate-800 tracking-tight mb-0.5">App Management</h1>
            <p class="text-xs text-slate-500 mb-0">Daftarkan dan kelola aplikasi satelit yang terintegrasi dengan Portal SSO.</p>
        </div>

        <a href="{{ route('applications.create') }}" class="inline-flex items-center justify-center gap-2 bg-[#172a40] hover:bg-[#112031] text-white text-xs font-semibold px-4 py-2.5 rounded-xl shadow-sm transition-all text-decoration-none self-start sm:self-auto">
            <i class="bi bi-plus-lg"></i>
            <span>Add New App</span>
        </a>
    </div>

    <!-- TABLE CARD -->
    <div class="bg-white border border-slate-200/90 rounded-2xl shadow-sm overflow-hidden p-4 md:p-6">
        <div class="table-responsive">
            <table id="manageAppsTable" class="table table-hover align-middle w-100 mb-0">
                <thead class="bg-slate-50 border-b border-slate-200 text-[#172a40]">
                    <tr>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-slate-600">Application</th>
                        <th class="py-3 text-xs font-bold uppercase tracking-wider text-slate-600">Category</th>
                        <th class="py-3 text-xs font-bold uppercase tracking-wider text-slate-600">Client ID</th>
                        <th class="py-3 text-xs font-bold uppercase tracking-wider text-slate-600">Redirect URI</th>
                        <th class="py-3 text-xs font-bold uppercase tracking-wider text-slate-600 text-center">Status</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-slate-600 text-end">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    <!-- Data loaded via Ajax DataTables -->
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
    <!-- DataTables 2.0 Bootstrap 5 integration -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.2/css/dataTables.bootstrap5.css">
    <script src="https://cdn.datatables.net/2.0.2/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.2/js/dataTables.bootstrap5.js"></script>

    <script>
        $(document).ready(function() {
            $('#manageAppsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('applications.index') }}",
                columns: [
                    {
                        data: 'name',
                        name: 'name',
                        className: 'px-4',
                        render: function(data, type, row) {
                            const icon = row.icon ? row.icon : 'fa-solid fa-server';
                            return `
                                <div class="flex items-center gap-3 py-1">
                                    <div class="w-10 h-10 rounded-xl bg-[#172a40]/5 text-[#172a40] flex items-center justify-center text-lg shrink-0">
                                        <i class="${icon}"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-800 text-[13px]">${data}</div>
                                        <div class="text-[11px] text-slate-400">OAuth Satellite App</div>
                                    </div>
                                </div>
                            `;
                        }
                    },
                    {
                        data: 'category_badge',
                        name: 'category',
                        className: 'py-2',
                    },
                    {
                        data: 'id',
                        name: 'id',
                        render: function(data) {
                            return `
                                <div class="flex items-center gap-1.5">
                                    <code class="text-[11px] bg-slate-100 text-slate-700 px-2 py-1 rounded font-mono border border-slate-200">${data}</code>
                                    <button type="button" class="text-slate-400 hover:text-slate-700 p-1 rounded" onclick="navigator.clipboard.writeText('${data}'); showToast('Client ID disalin ke clipboard', 'info');" title="Copy Client ID">
                                        <i class="bi bi-clipboard text-xs"></i>
                                    </button>
                                </div>
                            `;
                        }
                    },
                    {
                        data: 'redirect_uri',
                        name: 'redirect_uri',
                        className: 'text-slate-500 text-xs font-mono',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return `<span class="truncate block max-w-[280px]" title="${data}">${data}</span>`;
                        }
                    },
                    {
                        data: 'revoked',
                        name: 'revoked',
                        className: 'text-center',
                        render: function(data) {
                            if (data == 0 || data === false) {
                                return '<span class="bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-semibold px-2.5 py-0.5 rounded-full inline-flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active</span>';
                            } else {
                                return '<span class="bg-rose-50 text-rose-700 border border-rose-200 text-[10px] font-semibold px-2.5 py-0.5 rounded-full inline-flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Revoked</span>';
                            }
                        }
                    },
                    {
                        data: 'action',
                        name: 'action',
                        className: 'px-4 text-end',
                        orderable: false,
                        searchable: false,
                    }
                ],
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Cari aplikasi...",
                    lengthMenu: "Tampilkan _MENU_ baris",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ aplikasi",
                    infoEmpty: "Menampilkan 0 aplikasi",
                    infoFiltered: "(disaring dari _MAX_ total)",
                    zeroRecords: "Aplikasi tidak ditemukan",
                    processing: '<div class="spinner-border spinner-border-sm text-[#172a40]" role="status"></div> Meload data...'
                }
            });

            $('.dataTables_filter input').addClass('form-control form-control-sm d-inline-block w-auto ms-2 text-xs');
            $('.dataTables_length select').addClass('form-select form-select-sm d-inline-block w-auto me-2 text-xs');
        });

        function confirmToggleStatus(appId, appName, action) {
            const isActivate = action === 'activate';
            const title = isActivate ? 'Aktifkan Kembali Aplikasi?' : 'Nonaktifkan Aplikasi?';
            const text = isActivate
                ? `Aplikasi "${appName}" akan diizinkan kembali untuk menerima login pengguna melalui SSO.`
                : `Aplikasi "${appName}" akan dinonaktifkan. Pengguna tidak akan dapat login ke aplikasi ini sampai diaktifkan kembali!`;
            const confirmBtnText = isActivate ? 'Ya, Aktifkan' : 'Ya, Nonaktifkan';
            const confirmBtnColor = isActivate ? '#172a40' : '#f59e0b';

            Swal.fire({
                title: title,
                text: text,
                icon: isActivate ? 'question' : 'warning',
                showCancelButton: true,
                confirmButtonColor: confirmBtnColor,
                cancelButtonColor: '#64748b',
                confirmButtonText: confirmBtnText,
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-2xl shadow-xl'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('toggle-form-' + appId);
                    if (form) form.submit();
                }
            });
        }

        function confirmDeleteApp(appId, appName) {
            Swal.fire({
                title: 'Hapus Aplikasi Permanen?',
                text: `Aplikasi "${appName}" beserta seluruh hak akses pengguna dan sesi token OAuth yang terkait akan dihapus secara permanen. Tindakan ini TIDAK dapat dibatalkan!`,
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
                    const form = document.getElementById('delete-form-' + appId);
                    if (form) form.submit();
                }
            });
        }

        function closeCredentialsModal() {
            const modal = document.getElementById('credentialsModal');
            if (modal) {
                modal.classList.add('opacity-0', 'pointer-events-none');
                setTimeout(() => modal.remove(), 250);
            }
        }

        function copyCredText(text, label) {
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(() => {
                    if (typeof showToast === 'function') {
                        showToast(label + ' berhasil disalin ke clipboard!', 'success');
                    }
                }).catch(() => fallbackCopy(text, label));
            } else {
                fallbackCopy(text, label);
            }
        }

        function fallbackCopy(text, label) {
            const temp = document.createElement('textarea');
            temp.value = text;
            document.body.appendChild(temp);
            temp.select();
            document.execCommand('copy');
            document.body.removeChild(temp);
            if (typeof showToast === 'function') {
                showToast(label + ' berhasil disalin ke clipboard!', 'success');
            }
        }

        function copyEnvFormat(id, secret) {
            const envText = `SSO_CLIENT_ID="${id}"\nSSO_CLIENT_SECRET="${secret}"`;
            copyCredText(envText, 'Format konfigurasi .env');
        }
    </script>
@endpush
