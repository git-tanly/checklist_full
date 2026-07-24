@extends('layouts.mantis')

@section('header')
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h5 class="m-b-10">Edit Daily Report (Draft)</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('daily-reports.index') }}">Dashboard</a></li>
                        <li class="breadcrumb-item" aria-current="page">Edit Report</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- ERROR SYSTEM --}}
            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            {{-- FORM EDIT: Menggunakan Method PUT --}}
            <form action="{{ route('daily-reports.update', $dailyReport->id) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- CARD 1: General Info --}}
                <div class="card">
                    <div class="card-header">
                        <h5>General Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Restaurant</label>
                                    {{-- Disabled karena resto tidak boleh diganti saat edit agar form tidak rusak --}}
                                    <input type="text" class="form-control" value="{{ $dailyReport->restaurant->name }}"
                                        disabled>
                                    {{-- Kirim ID sebagai hidden jika perlu, tapi di controller kita pakai data lama --}}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Report Date & Time</label>

                                    {{-- GANTI JADI EDITABLE --}}
                                    <input type="datetime-local" name="date" class="form-control" {{-- Format tanggal dari database harus diubah ke format HTML5 --}}
                                        value="{{ old('date', $dailyReport->date->format('Y-m-d\TH:i')) }}" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- INCLUDE PARTIAL FORM SESUAI KODE RESTO --}}
                {{-- Logika: Kita include langsung file berdasarkan kode restoran --}}

                <div id="form-container">
                    @php
                        $code = $dailyReport->restaurant->code; // 209, NGN, XFH
                        $viewName = 'daily-reports.partials.form-' . strtolower($code === 'NJR' ? 'nagano' : $code);
                        $chamasName = 'daily-reports.partials.form-' . strtolower($code === 'CHA' ? 'chamas' : $code);
                        // Note: sesuaikan logika nama file Anda di sini.
                        // Jika code 209 -> form-209.blade.php
                        // Jika code NGN -> form-nagano.blade.php (jika namanya itu)
                    @endphp

                    @if (View::exists($viewName))
                        @include($viewName)
                    @elseif (View::exists($chamasName))
                        @include($chamasName)
                    @else
                        @include('daily-reports.partials.form-' . strtolower($dailyReport->restaurant->code))
                    @endif
                </div>

                {{-- ACTION BUTTONS --}}
                <div class="card mt-3">
                    <div class="card-body text-end">
                        <a href="{{ route('daily-reports.index') }}" class="btn btn-light me-2">Cancel</a>

                        <button type="submit" name="action" value="draft" class="btn btn-secondary me-2">
                            <i class="ti ti-device-floppy"></i> Update Draft
                        </button>
                        <button type="submit" name="action" value="submit" class="btn btn-primary">
                            <i class="ti ti-send"></i> Submit Final Report
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>

    <script>
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
            const itemName = selectEl.tagName === 'SELECT' ? selectEl.options[selectEl.selectedIndex].text : selectEl.value;
            const pax = parseInt(paxEl.value, 10);

            if (!itemId) {
                alert("Please " + (selectEl.tagName === 'SELECT' ? "select" : "enter") + " a menu item.");
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

        window.toggleOccasionOther = function(session, code) {
            const typeEl = document.getElementById('occasion-type-' + session + '-' + code);
            const otherEl = document.getElementById('occasion-other-' + session + '-' + code);
            if (otherEl) {
                if (typeEl.value === 'Other') {
                    otherEl.classList.remove('d-none');
                } else {
                    otherEl.classList.add('d-none');
                    otherEl.value = '';
                }
            }
        };

        window.addOccasionItem = function(session, code) {
            const typeEl = document.getElementById('occasion-type-' + session + '-' + code);
            const otherEl = document.getElementById('occasion-other-' + session + '-' + code);
            const nameEl = document.getElementById('occasion-name-' + session + '-' + code);
            const paxEl = document.getElementById('occasion-pax-' + session + '-' + code);
            const revEl = document.getElementById('occasion-revenue-' + session + '-' + code);

            let type = typeEl.value;
            if (type === 'Other' && otherEl) {
                type = otherEl.value.trim();
                if (!type) { alert('Please enter occasion type.'); return; }
            }

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
            if (otherEl) {
                otherEl.value = '';
                otherEl.classList.add('d-none');
            }
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

        window.togglePromoOther = function(session, code) {
            const typeEl = document.getElementById('promo-type-' + session + '-' + code);
            const otherEl = document.getElementById('promo-other-' + session + '-' + code);
            if (otherEl) {
                if (typeEl.value === 'Other') {
                    otherEl.classList.remove('d-none');
                } else {
                    otherEl.classList.add('d-none');
                    otherEl.value = '';
                }
            }
        };

        window.addPromoItem = function(session, code) {
            const typeEl = document.getElementById('promo-type-' + session + '-' + code);
            const otherEl = document.getElementById('promo-other-' + session + '-' + code);
            const paxEl = document.getElementById('promo-pax-' + session + '-' + code);
            const revEl = document.getElementById('promo-revenue-' + session + '-' + code);

            let type = typeEl.value;
            if (type === 'Other' && otherEl) {
                type = otherEl.value.trim();
                if (!type) { alert('Please enter promo name.'); return; }
            }

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
            if (otherEl) {
                otherEl.value = '';
                otherEl.classList.add('d-none');
            }
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
