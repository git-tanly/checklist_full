@extends('layouts.mantis')

@section('header')
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h5 class="m-b-10">Create Daily Report</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('daily-reports.index') }}">Dashboard</a></li>
                        <li class="breadcrumb-item" aria-current="page">Create Report</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">

            {{-- Tampilkan Error Validasi jika ada --}}
            @if ($errors->any())
                <div class="alert alert-danger border-0 shadow-sm mb-4">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-alert-circle fs-2 me-3"></i>
                        <div>
                            <h5 class="mb-1">Gagal Menyimpan Laporan!</h5>
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <form action="{{ route('daily-reports.store') }}" method="POST" id="reportForm">
                @csrf

                {{-- CARD 1: General Info --}}
                <div class="card">
                    <div class="card-header">
                        <h5>General Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Select Restaurant</label>
                                    <select name="restaurant_id" id="restaurant_selector" class="form-select" required>
                                        <option value="" selected disabled>-- Choose Restaurant --</option>
                                        @foreach ($restaurants as $rest)
                                            {{-- Kita simpan 'code' di atribut data-code untuk dipanggil JS --}}
                                            <option value="{{ $rest->id }}" data-code="{{ $rest->code }}"
                                                {{ old('restaurant_id') == $rest->id ? 'selected' : '' }}>
                                                {{ $rest->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Date & Time</label>

                                    {{-- GANTI MENJADI INPUT DATETIME-LOCAL --}}
                                    {{-- Format value harus 'Y-m-d\TH:i' agar terbaca oleh input HTML5 --}}
                                    <input type="datetime-local" name="date" class="form-control"
                                        value="{{ old('date', now()->format('Y-m-d\TH:i')) }}" required>

                                    <small class="text-muted">You can change this date if reporting for past events.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CARD 2: Dynamic Forms Container --}}
                <div id="form-container">
                    {{-- Pesan awal sebelum memilih resto --}}
                    <div id="empty-state" class="text-center py-5 text-muted">
                        <i class="ti ti-hand-click fs-1 mb-2"></i>
                        <p>Please select a restaurant to fill the report.</p>
                    </div>

                    {{-- INCLUDE PARTIAL FORM DI SINI --}}
                    {{-- Defaultnya disembunyikan (d-none) --}}

                    <div id="form-209" class="resto-form d-none">
                        @include('daily-reports.partials.form-209')
                    </div>

                    <div id="form-XFH" class="resto-form d-none">
                        @include('daily-reports.partials.form-xfh')
                    </div>

                    <div id="form-CHA" class="resto-form d-none">
                        @include('daily-reports.partials.form-chamas')
                        {{-- <div class="alert alert-warning">Form Chamas belum dibuat</div> --}}
                    </div>

                    <div id="form-NJR" class="resto-form d-none">
                        @include('daily-reports.partials.form-nagano')
                        {{-- <div class="alert alert-warning">Form Nagano belum dibuat</div> --}}
                    </div>

                    <div id="form-VODA" class="resto-form d-none">
                        @include('daily-reports.partials.form-voda')
                        {{-- <div class="alert alert-warning">Form Nagano belum dibuat</div> --}}
                    </div>

                    <div id="form-JM" class="resto-form d-none">
                        @include('daily-reports.partials.form-jm')
                    </div>

                    <div id="form-BQT" class="resto-form d-none">
                        @include('daily-reports.partials.form-bqt')
                        {{-- <div class="alert alert-warning">Form Nagano belum dibuat</div> --}}
                    </div>

                    <div id="form-IRD" class="resto-form d-none">
                        @include('daily-reports.partials.form-ird')
                        {{-- <div class="alert alert-warning">Form Nagano belum dibuat</div> --}}
                    </div>
                </div>

                {{-- ACTION BUTTONS --}}
                <div class="card mt-3 d-none" id="submit-actions">
                    <div class="card-body text-end">
                        <a href="{{ route('daily-reports.index') }}" class="btn btn-light me-2">
                            Cancel
                        </a>
                        <button type="submit" name="action" value="draft" class="btn btn-secondary me-2">
                            <i class="ti ti-device-floppy"></i> Save as Draft
                        </button>
                        <button type="submit" name="action" value="submit" class="btn btn-primary">
                            <i class="ti ti-send"></i> Submit Report
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selector = document.getElementById('restaurant_selector');
            const forms = document.querySelectorAll('.resto-form');
            const emptyState = document.getElementById('empty-state');
            const submitActions = document.getElementById('submit-actions');

            // Fungsi untuk mematikan input pada form yang sembunyi
            // Agar data dari form lain tidak ikut terkirim ke server
            function toggleInputs(containerId, enable) {
                const container = document.getElementById(containerId);
                if (!container) return;

                const inputs = container.querySelectorAll('input, select, textarea');
                inputs.forEach(input => {
                    input.disabled = !enable;
                });
            }

            selector.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const code = selectedOption.getAttribute('data-code'); // Ambil kode: 209, XFH, NGN

                // 1. Sembunyikan Empty State
                emptyState.classList.add('d-none');

                submitActions.classList.add('d-none');

                // 2. Sembunyikan SEMUA form dulu & disable inputnya
                forms.forEach(form => {
                    form.classList.add('d-none');
                    toggleInputs(form.id, false); // Disable inputs
                });

                // 3. Tampilkan form yang dipilih & enable inputnya
                const targetId = 'form-' + code;
                const targetForm = document.getElementById(targetId);

                if (targetForm) {
                    targetForm.classList.remove('d-none');
                    toggleInputs(targetId, true); // Enable inputs
                    submitActions.classList.remove('d-none');
                } else {
                    // Fallback jika file partial belum ada
                    emptyState.classList.remove('d-none');
                    emptyState.innerHTML = '<p class="text-danger">Form for ' + code +
                        ' not found/created yet.</p>';
                }
            });

            // Trigger saat load (jika browser menyimpan cache pilihan, misal saat back button)
            if (selector.value) {
                selector.dispatchEvent(new Event('change'));
            }
        });
        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('rupiah')) {
                let value = e.target.value;

                // 1. Hapus karakter selain angka
                let numberString = value.replace(/[^,\d]/g, '').toString();

                // 2. Format menjadi ribuan dengan titik
                let split = numberString.split(',');
                let sisa = split[0].length % 3;
                let rupiah = split[0].substr(0, sisa);
                let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

                if (ribuan) {
                    let separator = sisa ? '.' : '';
                    rupiah += separator + ribuan.join('.');
                }

                rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;

                // 3. Kembalikan nilai yang sudah diformat ke input
                e.target.value = rupiah;
            }
        });

        // --- UPSELLING MANAGER SCRIPT ---
        // Variabel global untuk menyimpan state data
        let upsellingState = {};

        // 1. Init dengan parameter CODE
        window.initUpselling = function(session, type, initialData, code) {
            const key = `${session}_${type}_${code}`; // Key unik per resto

            let safeData = [];
            if (initialData) {
                if (Array.isArray(initialData)) safeData = initialData;
                else if (typeof initialData === 'object') safeData = Object.values(initialData);
                else if (typeof initialData === 'string') try {
                    safeData = JSON.parse(initialData);
                } catch (e) {}
            }

            upsellingState[key] = safeData;
            renderUpsellingTable(session, type, code);
        };

        // 2. Add Item dengan parameter CODE
        window.addUpsellingItem = function(session, type, code) {
            // ID Unik: select-lunch-food-NGN
            const selectId = `select-${session}-${type}-${code}`;
            const paxId = `pax-${session}-${type}-${code}`;

            const selectEl = document.getElementById(selectId);
            const paxEl = document.getElementById(paxId);

            if (!selectEl || !paxEl) {
                console.error("Element not found:", selectId, paxId);
                return;
            }

            const itemId = selectEl.value;
            const itemName = selectEl.options[selectEl.selectedIndex].text;
            const pax = parseInt(paxEl.value);

            if (!itemId) {
                alert("Please select a menu item.");
                return;
            }
            if (isNaN(pax) || pax < 0) {
                alert("Please enter a valid quantity (0 or higher).");
                return;
            }

            const key = `${session}_${type}_${code}`;
            if (!upsellingState[key]) upsellingState[key] = [];

            upsellingState[key].push({
                id: itemId,
                name: itemName,
                pax: pax
            });

            selectEl.value = "";
            paxEl.value = "";
            renderUpsellingTable(session, type, code);
        };

        // 3. Remove Item dengan parameter CODE
        window.removeUpsellingItem = function(session, type, index, code) {
            const key = `${session}_${type}_${code}`;
            upsellingState[key].splice(index, 1);
            renderUpsellingTable(session, type, code);
        };

        // 4. Render Table dengan parameter CODE
        function renderUpsellingTable(session, type, code) {
            const key = `${session}_${type}_${code}`;
            const data = upsellingState[key] || [];

            // Update Hidden Input (ID Unik: input-lunch-food-NGN)
            const hiddenInputId = `input-${session}-${type}-${code}`;
            const hiddenInput = document.getElementById(hiddenInputId);
            if (hiddenInput) hiddenInput.value = JSON.stringify(data);

            // Render List (ID Unik: list-lunch-food-NGN)
            const listId = `list-${session}-${type}-${code}`;
            const listEl = document.getElementById(listId);

            if (listEl) {
                listEl.innerHTML = "";
                data.forEach((item, index) => {
                    const li = document.createElement('li');
                    li.className = "list-group-item d-flex justify-content-between align-items-center p-2";
                    li.innerHTML = `
                    <div><span class="fw-bold">${item.name}</span> <span class="badge bg-primary rounded-pill ms-2">${item.pax} Pax</span></div>
                    <button type="button" class="btn btn-sm btn-link-danger p-0"
                        onclick="removeUpsellingItem('${session}', '${type}', ${index}, '${code}')">
                        <i class="ti ti-x"></i>
                    </button>
                `;
                    listEl.appendChild(li);
                });
            }
        }

        // --- VIP REMARKS MANAGER SCRIPT ---

        // Variabel global untuk menyimpan state data VIP
        let vipState = {};

        /**
         * 1. Init dengan parameter CODE
         */
        window.initVip = function(session, initialData, code) {
            const key = `${session}_${code}`; // Key unik: lunch_NGN

            let safeData = [];
            if (initialData) {
                if (Array.isArray(initialData)) safeData = initialData;
                else if (typeof initialData === 'object') safeData = Object.values(initialData);
                else if (typeof initialData === 'string') try {
                    safeData = JSON.parse(initialData);
                } catch (e) {}
            }

            vipState[key] = safeData;
            renderVipTable(session, code);
        };

        /**
         * 2. Add Item dengan parameter CODE
         */
        window.addVipItem = function(session, code) {
            // ID Unik: vip-name-lunch-NGN
            const nameId = `vip-name-${session}-${code}`;
            const posId = `vip-pos-${session}-${code}`;

            const nameEl = document.getElementById(nameId);
            const posEl = document.getElementById(posId);

            if (!nameEl || !posEl) return;

            const nameVal = nameEl.value.trim();
            const posVal = posEl.value.trim();

            if (!nameVal) {
                alert("Please enter Guest Name.");
                return;
            }
            if (!posVal) {
                alert("Please enter Position/Title.");
                return;
            }

            const key = `${session}_${code}`;
            if (!vipState[key]) vipState[key] = [];

            vipState[key].push({
                name: nameVal,
                position: posVal
            });

            nameEl.value = "";
            posEl.value = "";
            nameEl.focus();
            renderVipTable(session, code);
        };

        /**
         * 3. Remove Item dengan parameter CODE
         */
        window.removeVipItem = function(session, index, code) {
            const key = `${session}_${code}`;
            vipState[key].splice(index, 1);
            renderVipTable(session, code);
        };

        /**
         * 4. Render Table dengan parameter CODE
         */
        function renderVipTable(session, code) {
            const key = `${session}_${code}`;
            const data = vipState[key] || [];

            // Update Hidden Input (ID Unik: input-vip-lunch-NGN)
            const hiddenInputId = `input-vip-${session}-${code}`;
            const hiddenInput = document.getElementById(hiddenInputId);
            if (hiddenInput) hiddenInput.value = JSON.stringify(data);

            // Render List (ID Unik: list-vip-lunch-NGN)
            const listId = `list-vip-${session}-${code}`;
            const listEl = document.getElementById(listId);

            if (listEl) {
                listEl.innerHTML = "";
                data.forEach((item, index) => {
                    const li = document.createElement('li');
                    li.className =
                        "list-group-item d-flex justify-content-between align-items-start p-2 bg-light mb-1 border rounded";
                    li.innerHTML = `
                    <div class="ms-2 me-auto">
                        <div class="fw-bold">${item.name}</div>
                        <span class="small text-muted">${item.position}</span>
                    </div>
                    <button type="button" class="btn btn-sm btn-link-danger p-0"
                        onclick="removeVipItem('${session}', ${index}, '${code}')">
                        <i class="ti ti-x fs-4"></i>
                    </button>
                `;
                    listEl.appendChild(li);
                });
            }
        }

        // --- STAFF ON DUTY MANAGER SCRIPT ---
        let staffState = {};

        /**
         * 1. Init dengan parameter CODE
         */
        window.initStaff = function(session, initialData, code) {
            const key = `${session}_${code}`; // Key unik

            let safeData = [];
            if (initialData) {
                if (Array.isArray(initialData)) safeData = initialData;
                else if (typeof initialData === 'object') safeData = Object.values(initialData);
                else if (typeof initialData === 'string') try {
                    safeData = JSON.parse(initialData);
                } catch (e) {}
            }

            staffState[key] = safeData;
            renderStaffTable(session, code);
        };

        /**
         * 2. Add Item dengan parameter CODE
         */
        window.addStaffItem = function(session, code) {
            // ID Unik: select-staff-lunch-NGN
            const selectId = `select-staff-${session}-${code}`;
            const selectEl = document.getElementById(selectId);

            if (!selectEl) return;

            const staffId = selectEl.value;
            const staffName = selectEl.options[selectEl.selectedIndex].text;

            if (!staffId) {
                alert("Please select a staff member.");
                return;
            }

            const key = `${session}_${code}`;
            if (!staffState[key]) staffState[key] = [];

            // Cek Duplikasi
            if (staffState[key].includes(staffName)) {
                alert("Staff member already added.");
                return;
            }

            staffState[key].push(staffName);
            selectEl.value = ""; // Reset dropdown
            renderStaffTable(session, code);
        };

        /**
         * 3. Remove Item dengan parameter CODE
         */
        window.removeStaffItem = function(session, index, code) {
            const key = `${session}_${code}`;
            staffState[key].splice(index, 1);
            renderStaffTable(session, code);
        };

        /**
         * 4. Render Table dengan parameter CODE
         */
        function renderStaffTable(session, code) {
            const key = `${session}_${code}`;
            const data = staffState[key] || [];

            // Update Hidden Input (ID Unik: input-staff-lunch-NGN)
            const hiddenInputId = `input-staff-${session}-${code}`;
            const hiddenInput = document.getElementById(hiddenInputId);
            if (hiddenInput) hiddenInput.value = JSON.stringify(data);

            // Render List (ID Unik: list-staff-lunch-NGN)
            const listId = `list-staff-${session}-${code}`;
            const listEl = document.getElementById(listId);

            if (listEl) {
                listEl.innerHTML = "";
                data.forEach((name, index) => {
                    const badge = document.createElement('span');
                    badge.className = "badge bg-light-primary text-primary me-1 mb-1 fs-6";
                    badge.innerHTML = `
                    ${name}
                    <i class="ti ti-x ms-1" style="cursor:pointer"
                       onclick="removeStaffItem('${session}', ${index}, '${code}')"></i>
                `;
                    listEl.appendChild(badge);
                });
            }
        }

        // --- GROUP MANAGER SCRIPT ---
        let groupState = {};

        window.initGroup = function(session, initialData, code) {
            const key = session + '_' + code;
            let safeData = [];
            if (initialData) {
                if (Array.isArray(initialData)) safeData = initialData;
                else if (typeof initialData === 'object') safeData = Object.values(initialData);
                else if (typeof initialData === 'string') try { safeData = JSON.parse(initialData); } catch (e) {}
            }
            groupState[key] = safeData;
            renderGroupList(document.getElementById('list-group-' + session + '-' + code), safeData, session, code);
        };

        window.addGroupItem = function(session, code) {
            const nameEl = document.getElementById('group-name-' + session + '-' + code);
            const qtyEl = document.getElementById('group-qty-' + session + '-' + code);
            const name = nameEl.value.trim();
            const qty = qtyEl.value.trim();
            if (!name) { alert('Please enter group name.'); return; }
            if (!qty || parseInt(qty) <= 0) { alert('Please enter a valid quantity.'); return; }
            const key = session + '_' + code;
            if (!groupState[key]) groupState[key] = [];
            groupState[key].push({ name: name, qty: parseInt(qty) });
            nameEl.value = '';
            qtyEl.value = '';
            nameEl.focus();
            const hiddenInput = document.getElementById('input-group-' + session + '-' + code);
            if (hiddenInput) hiddenInput.value = JSON.stringify(groupState[key]);
            renderGroupList(document.getElementById('list-group-' + session + '-' + code), groupState[key], session, code);
        };

        window.removeGroupItem = function(session, code, index) {
            const key = session + '_' + code;
            groupState[key].splice(index, 1);
            const hiddenInput = document.getElementById('input-group-' + session + '-' + code);
            if (hiddenInput) hiddenInput.value = JSON.stringify(groupState[key]);
            renderGroupList(document.getElementById('list-group-' + session + '-' + code), groupState[key], session, code);
        };

        function renderGroupList(listEl, items, session, code) {
            if (!listEl) return;
            if (!items || items.length === 0) { listEl.innerHTML = ''; return; }
            let html = '';
            items.forEach(function(item, index) {
                html += '<li class="list-group-item d-flex justify-content-between align-items-center py-1 px-2">' +
                    '<span class="small"><strong>' + escapeHtml(item.name || '') + '</strong> <span class="badge bg-secondary ms-1">' + (item.qty || 0) + ' pax</span></span>' +
                    '<button class="btn btn-sm btn-link text-danger p-0" onclick="removeGroupItem(\'' + session + '\', \'' + code + '\', ' + index + ')" title="Remove"><i class="ti ti-x"></i></button></li>';
            });
            listEl.innerHTML = html;
        }

        // --- OCCASION OTHERS MANAGER SCRIPT ---
        let occOthersState = {};

        window.initOccOthers = function(session, initialData, code) {
            const key = session + '_' + code;
            let safeData = [];
            if (initialData) {
                if (Array.isArray(initialData)) safeData = initialData;
                else if (typeof initialData === 'object') safeData = Object.values(initialData);
                else if (typeof initialData === 'string') try { safeData = JSON.parse(initialData); } catch (e) {}
            }
            occOthersState[key] = safeData;
            renderOccOthersList(document.getElementById('list-occothers-' + session + '-' + code), safeData, session, code);
        };

        window.addOccOthers = function(session, code) {
            const nameEl = document.getElementById('occothers-name-' + session + '-' + code);
            const paxEl = document.getElementById('occothers-pax-' + session + '-' + code);
            const name = nameEl.value.trim();
            const pax = paxEl.value.trim();
            if (!name) { alert('Please enter occasion name.'); return; }
            if (!pax || parseInt(pax) <= 0) { alert('Please enter a valid pax number.'); return; }
            const key = session + '_' + code;
            if (!occOthersState[key]) occOthersState[key] = [];
            occOthersState[key].push({ name: name, pax: parseInt(pax) });
            nameEl.value = '';
            paxEl.value = '';
            nameEl.focus();
            const hiddenInput = document.getElementById('input-occothers-' + session + '-' + code);
            if (hiddenInput) hiddenInput.value = JSON.stringify(occOthersState[key]);
            renderOccOthersList(document.getElementById('list-occothers-' + session + '-' + code), occOthersState[key], session, code);
        };

        window.removeOccOthers = function(session, code, index) {
            const key = session + '_' + code;
            occOthersState[key].splice(index, 1);
            const hiddenInput = document.getElementById('input-occothers-' + session + '-' + code);
            if (hiddenInput) hiddenInput.value = JSON.stringify(occOthersState[key]);
            renderOccOthersList(document.getElementById('list-occothers-' + session + '-' + code), occOthersState[key], session, code);
        };

        function renderOccOthersList(listEl, items, session, code) {
            if (!listEl) return;
            if (!items || items.length === 0) { listEl.innerHTML = ''; return; }
            let html = '';
            items.forEach(function(item, index) {
                html += '<li class="list-group-item d-flex justify-content-between align-items-center py-1 px-2">' +
                    '<span class="small"><strong>' + escapeHtml(item.name || '') + '</strong> <span class="badge bg-primary ms-1">' + (item.pax || 0) + ' pax</span></span>' +
                    '<button class="btn btn-sm btn-link text-danger p-0" onclick="removeOccOthers(\'' + session + '\', \'' + code + '\', ' + index + ')" title="Remove"><i class="ti ti-x"></i></button></li>';
            });
            listEl.innerHTML = html;
        }

        // --- PROMO OTHERS MANAGER SCRIPT ---
        let promoOthersState = {};

        window.initPromoOthers = function(session, initialData, code) {
            const key = session + '_' + code;
            let safeData = [];
            if (initialData) {
                if (Array.isArray(initialData)) safeData = initialData;
                else if (typeof initialData === 'object') safeData = Object.values(initialData);
                else if (typeof initialData === 'string') try { safeData = JSON.parse(initialData); } catch (e) {}
            }
            promoOthersState[key] = safeData;
            renderPromoOthersList(document.getElementById('list-promoothers-' + session + '-' + code), safeData, session, code);
        };

        window.addPromoOthers = function(session, code) {
            const nameEl = document.getElementById('promoothers-name-' + session + '-' + code);
            const qtyEl = document.getElementById('promoothers-qty-' + session + '-' + code);
            const name = nameEl.value.trim();
            const qty = qtyEl.value.trim();
            if (!name) { alert('Please enter promo name.'); return; }
            if (!qty || parseInt(qty) <= 0) { alert('Please enter a valid quantity.'); return; }
            const key = session + '_' + code;
            if (!promoOthersState[key]) promoOthersState[key] = [];
            promoOthersState[key].push({ name: name, qty: parseInt(qty) });
            nameEl.value = '';
            qtyEl.value = '';
            nameEl.focus();
            const hiddenInput = document.getElementById('input-promoothers-' + session + '-' + code);
            if (hiddenInput) hiddenInput.value = JSON.stringify(promoOthersState[key]);
            renderPromoOthersList(document.getElementById('list-promoothers-' + session + '-' + code), promoOthersState[key], session, code);
        };

        window.removePromoOthers = function(session, code, index) {
            const key = session + '_' + code;
            promoOthersState[key].splice(index, 1);
            const hiddenInput = document.getElementById('input-promoothers-' + session + '-' + code);
            if (hiddenInput) hiddenInput.value = JSON.stringify(promoOthersState[key]);
            renderPromoOthersList(document.getElementById('list-promoothers-' + session + '-' + code), promoOthersState[key], session, code);
        };

        function renderPromoOthersList(listEl, items, session, code) {
            if (!listEl) return;
            if (!items || items.length === 0) { listEl.innerHTML = ''; return; }
            let html = '';
            items.forEach(function(item, index) {
                html += '<li class="list-group-item d-flex justify-content-between align-items-center py-1 px-2">' +
                    '<span class="small"><strong>' + escapeHtml(item.name || '') + '</strong> <span class="badge bg-success ms-1">' + (item.qty || 0) + ' pax</span></span>' +
                    '<button class="btn btn-sm btn-link text-danger p-0" onclick="removePromoOthers(\'' + session + '\', \'' + code + '\', ' + index + ')" title="Remove"><i class="ti ti-x"></i></button></li>';
            });
            listEl.innerHTML = html;
        }

        function escapeHtml(text) {
            if (!text) return '';
            var div = document.createElement('div');
            div.appendChild(document.createTextNode(text));
            return div.innerHTML;
        }

        // --- OCCASION ITEMS MANAGER SCRIPT ---
        let occasionState = {};

        window.initOccasion = function(session, initialData, code) {
            const key = session + '_' + code;
            let safeData = [];
            if (initialData) {
                if (Array.isArray(initialData)) safeData = initialData;
                else if (typeof initialData === 'object') safeData = Object.values(initialData);
                else if (typeof initialData === 'string') try { safeData = JSON.parse(initialData); } catch (e) {}
            }
            occasionState[key] = safeData;
            const hiddenInput = document.getElementById('input-occasion-' + session + '-' + code);
            if (hiddenInput) hiddenInput.value = JSON.stringify(safeData);
            renderOccasionList(document.getElementById('list-occasion-' + session + '-' + code), safeData, session, code);
        };

        window.addOccasionItem = function(session, code) {
            const typeEl = document.getElementById('occasion-type-' + session + '-' + code);
            const nameEl = document.getElementById('occasion-name-' + session + '-' + code);
            const paxEl = document.getElementById('occasion-pax-' + session + '-' + code);
            const revEl = document.getElementById('occasion-revenue-' + session + '-' + code);

            const type = typeEl.value;
            const name = nameEl.value.trim();
            const pax = paxEl.value.trim();
            let revenue = revEl.value.replace(/\./g, '').trim();

            if (!type) { alert('Please select an occasion type.'); return; }
            if (!name) { alert('Please enter a name.'); return; }
            if (!pax || parseInt(pax) <= 0) { alert('Please enter valid pax.'); return; }

            const key = session + '_' + code;
            if (!occasionState[key]) occasionState[key] = [];
            occasionState[key].push({
                type: type,
                name: name,
                pax: parseInt(pax),
                revenue: revenue ? parseInt(revenue) : 0
            });

            typeEl.value = '';
            nameEl.value = '';
            paxEl.value = '';
            revEl.value = '';
            typeEl.focus();

            const hiddenInput = document.getElementById('input-occasion-' + session + '-' + code);
            if (hiddenInput) hiddenInput.value = JSON.stringify(occasionState[key]);
            renderOccasionList(document.getElementById('list-occasion-' + session + '-' + code), occasionState[key], session, code);
        };

        window.removeOccasionItem = function(session, code, index) {
            const key = session + '_' + code;
            occasionState[key].splice(index, 1);
            const hiddenInput = document.getElementById('input-occasion-' + session + '-' + code);
            if (hiddenInput) hiddenInput.value = JSON.stringify(occasionState[key]);
            renderOccasionList(document.getElementById('list-occasion-' + session + '-' + code), occasionState[key], session, code);
        };

        function renderOccasionList(listEl, items, session, code) {
            if (!listEl) return;
            if (!items || items.length === 0) { listEl.innerHTML = ''; return; }
            let html = '';
            items.forEach(function(item, index) {
                const rev = item.revenue ? 'Rp ' + Number(item.revenue).toLocaleString('id-ID') : '';
                html += '<li class="list-group-item d-flex justify-content-between align-items-center py-1 px-2">' +
                    '<span class="small"><span class="badge bg-primary me-1">' + escapeHtml(item.type || '') + '</span>' +
                    '<strong>' + escapeHtml(item.name || '') + '</strong>' +
                    ' <span class="badge bg-secondary ms-1">' + (item.pax || 0) + ' pax</span>' +
                    (rev ? ' <span class="badge bg-success ms-1">' + rev + '</span>' : '') +
                    '</span>' +
                    '<button class="btn btn-sm btn-link text-danger p-0" onclick="removeOccasionItem(\'' + session + '\', \'' + code + '\', ' + index + ')" title="Remove"><i class="ti ti-x"></i></button>' +
                    '</li>';
            });
            listEl.innerHTML = html;
        }

        // --- PROMO ITEMS MANAGER SCRIPT ---
        let promoItemState = {};

        window.initPromoItems = function(session, initialData, code) {
            const key = session + '_' + code;
            let safeData = [];
            if (initialData) {
                if (Array.isArray(initialData)) safeData = initialData;
                else if (typeof initialData === 'object') safeData = Object.values(initialData);
                else if (typeof initialData === 'string') try { safeData = JSON.parse(initialData); } catch (e) {}
            }
            promoItemState[key] = safeData;
            const hiddenInput = document.getElementById('input-promo-' + session + '-' + code);
            if (hiddenInput) hiddenInput.value = JSON.stringify(safeData);
            renderPromoItemList(document.getElementById('list-promo-' + session + '-' + code), safeData, session, code);
        };

        window.addPromoItem = function(session, code) {
            const typeEl = document.getElementById('promo-type-' + session + '-' + code);
            const paxEl = document.getElementById('promo-pax-' + session + '-' + code);
            const revEl = document.getElementById('promo-revenue-' + session + '-' + code);

            const type = typeEl.value;
            const pax = paxEl.value.trim();
            let revenue = revEl.value.replace(/\./g, '').trim();

            if (!type) { alert('Please select a promo type.'); return; }
            if (!pax || parseInt(pax) <= 0) { alert('Please enter valid pax.'); return; }

            const key = session + '_' + code;
            if (!promoItemState[key]) promoItemState[key] = [];
            promoItemState[key].push({
                type: type,
                pax: parseInt(pax),
                revenue: revenue ? parseInt(revenue) : 0
            });

            typeEl.value = '';
            paxEl.value = '';
            revEl.value = '';
            typeEl.focus();

            const hiddenInput = document.getElementById('input-promo-' + session + '-' + code);
            if (hiddenInput) hiddenInput.value = JSON.stringify(promoItemState[key]);
            renderPromoItemList(document.getElementById('list-promo-' + session + '-' + code), promoItemState[key], session, code);
        };

        window.removePromoItem = function(session, code, index) {
            const key = session + '_' + code;
            promoItemState[key].splice(index, 1);
            const hiddenInput = document.getElementById('input-promo-' + session + '-' + code);
            if (hiddenInput) hiddenInput.value = JSON.stringify(promoItemState[key]);
            renderPromoItemList(document.getElementById('list-promo-' + session + '-' + code), promoItemState[key], session, code);
        };

        function renderPromoItemList(listEl, items, session, code) {
            if (!listEl) return;
            if (!items || items.length === 0) { listEl.innerHTML = ''; return; }
            let html = '';
            items.forEach(function(item, index) {
                const rev = item.revenue ? 'Rp ' + Number(item.revenue).toLocaleString('id-ID') : '';
                html += '<li class="list-group-item d-flex justify-content-between align-items-center py-1 px-2">' +
                    '<span class="small"><span class="badge bg-success me-1">' + escapeHtml(item.type || '') + '</span>' +
                    ' <span class="badge bg-secondary ms-1">' + (item.pax || 0) + ' pax</span>' +
                    (rev ? ' <span class="badge bg-primary ms-1">' + rev + '</span>' : '') +
                    '</span>' +
                    '<button class="btn btn-sm btn-link text-danger p-0" onclick="removePromoItem(\'' + session + '\', \'' + code + '\', ' + index + ')" title="Remove"><i class="ti ti-x"></i></button>' +
                    '</li>';
            });
            listEl.innerHTML = html;
        }
    </script>
@endsection
