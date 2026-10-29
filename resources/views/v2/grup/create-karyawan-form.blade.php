<input type="hidden" id="kode_grup_add" value="{{ $grup->kode_grup }}">

<div class="space-y-4">
    <!-- Search & Filter Controls -->
    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/90 space-y-3">
        <!-- Search Input -->
        <div class="relative w-full">
            <i class="ti ti-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-base pointer-events-none"></i>
            <input type="text" 
                   id="searchKaryawanV2" 
                   placeholder="Cari berdasarkan NIK, nama lengkap, atau jabatan..." 
                   autocomplete="off"
                   class="w-full pl-10 pr-4 py-2 text-xs font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
        </div>

        <!-- Filter Selects -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
            <div>
                <select id="filterDepartemenV2" class="w-full px-3 py-2 text-xs font-medium bg-white border border-slate-300 rounded-xl text-slate-700 outline-none focus:border-emerald-500 transition">
                    <option value="">Semua Departemen</option>
                    @foreach ($departemen as $dept)
                        <option value="{{ $dept->kode_dept }}">{{ $dept->nama_dept }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select id="filterCabangV2" class="w-full px-3 py-2 text-xs font-medium bg-white border border-slate-300 rounded-xl text-slate-700 outline-none focus:border-emerald-500 transition">
                    <option value="">Semua Cabang</option>
                    @foreach ($cabang as $cab)
                        <option value="{{ $cab->kode_cabang }}">{{ $cab->nama_cabang }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <p class="text-[11px] text-slate-400 flex items-center gap-1">
            <i class="ti ti-info-circle text-xs text-emerald-600"></i>
            <span>Karyawan yang sudah terdaftar di grup lain otomatis tidak ditampilkan.</span>
        </p>
    </div>

    <!-- Loading Indicator -->
    <div id="loadingIndicatorV2" class="hidden text-center py-8">
        <div class="w-8 h-8 border-3 border-emerald-500/20 border-t-emerald-600 rounded-full animate-spin mx-auto mb-2"></div>
        <p class="text-xs text-slate-500 font-medium">Mencari calon anggota...</p>
    </div>

    <!-- Search Results Container -->
    <div id="searchResultsV2" class="max-h-[360px] overflow-y-auto pr-1">
        <!-- Results rendered via JS -->
    </div>
</div>

<script>
    (function() {
        const kodeGrup = $('#kode_grup_add').val();
        let searchTimeout;

        function searchKaryawan(term) {
            $('#loadingIndicatorV2').removeClass('hidden');
            $('#searchResultsV2').html('');

            $.ajax({
                url: "{{ route('grup.searchKaryawan') }}",
                method: "GET",
                data: {
                    search: term || '',
                    kode_grup: kodeGrup,
                    kode_dept: $('#filterDepartemenV2').val(),
                    kode_cabang: $('#filterCabangV2').val()
                },
                success: function(response) {
                    $('#loadingIndicatorV2').addClass('hidden');
                    if (response && response.karyawan) {
                        displayResults(response.karyawan, response.count);
                    }
                },
                error: function(xhr) {
                    $('#loadingIndicatorV2').addClass('hidden');
                    $('#searchResultsV2').html(`
                        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs text-center font-medium">
                            Gagal memuat data pencarian karyawan.
                        </div>
                    `);
                }
            });
        }

        function displayResults(karyawan, count) {
            if (count === 0) {
                $('#searchResultsV2').html(`
                    <div class="text-center py-10 text-slate-400">
                        <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-lg mx-auto mb-2">
                            <i class="ti ti-user-x"></i>
                        </div>
                        <p class="text-xs font-bold text-slate-700">Tidak ada karyawan yang tersedia</p>
                        <p class="text-[11px] text-slate-400 mt-0.5">Semua karyawan aktif sesuai filter mungkin sudah terdaftar di grup.</p>
                    </div>
                `);
                return;
            }

            let html = `<div class="text-[11px] font-bold text-slate-400 mb-2">Ditemukan ${count} karyawan:</div>`;
            html += `<div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">`;

            karyawan.forEach(function(k) {
                let avatarHtml = k.foto && k.foto_exists
                    ? `<img src="{{ asset('storage/karyawan') }}/${k.foto}" class="w-9 h-9 rounded-xl object-cover border border-slate-200 shrink-0">`
                    : `<div class="w-9 h-9 rounded-xl bg-slate-100 border border-slate-200 text-slate-600 font-bold text-xs flex items-center justify-center shrink-0">${k.nama_karyawan ? k.nama_karyawan.charAt(0).toUpperCase() : '?'}</div>`;

                html += `
                    <div class="p-3 bg-white hover:bg-slate-50 border border-slate-200/90 rounded-xl flex items-center justify-between gap-2.5 transition group" id="card-cand-${k.nik}">
                        <div class="flex items-center gap-2.5 min-w-0 flex-1">
                            ${avatarHtml}
                            <div class="min-w-0 flex-1">
                                <h5 class="text-xs font-bold text-slate-900 truncate group-hover:text-emerald-800 transition" title="${k.nama_karyawan}">${k.nama_karyawan}</h5>
                                <div class="text-[10.5px] text-slate-500 truncate mt-0.5">
                                    <span class="font-mono font-bold text-slate-600">${k.nik}</span> • ${k.nama_dept || '-'}
                                </div>
                            </div>
                        </div>
                        <button type="button" 
                                class="w-8 h-8 rounded-lg bg-emerald-50 hover:bg-emerald-600 text-emerald-700 hover:text-white border border-emerald-200 flex items-center justify-center transition active:scale-95 btn-add-candidate shrink-0 cursor-pointer shadow-2xs"
                                data-nik="${k.nik}"
                                data-nama="${k.nama_karyawan}"
                                title="Tambahkan ke Grup">
                            <i class="ti ti-plus text-xs"></i>
                        </button>
                    </div>
                `;
            });

            html += `</div>`;
            $('#searchResultsV2').html(html);
        }

        // Search Events
        $('#searchKaryawanV2').on('input', function() {
            const term = $(this).val().trim();
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                searchKaryawan(term);
            }, 300);
        });

        $('#filterDepartemenV2, #filterCabangV2').on('change', function() {
            searchKaryawan($('#searchKaryawanV2').val().trim());
        });

        // Add candidate to grup action
        $(document).off('click', '.btn-add-candidate').on('click', '.btn-add-candidate', function(e) {
            e.preventDefault();
            const btn = $(this);
            const nik = btn.data('nik');
            const nama = btn.data('nama');

            btn.prop('disabled', true).html('<i class="ti ti-loader animate-spin text-xs"></i>');

            $.ajax({
                url: "{{ route('grup.addKaryawan') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    kode_grup: kodeGrup,
                    nik: nik
                },
                success: function(response) {
                    $(`#card-cand-${nik}`).fadeOut(200, function() {
                        $(this).remove();
                    });

                    if (window.toast) {
                        window.toast.success(`${nama} berhasil ditambahkan ke grup.`);
                    }

                    if (window.loadAnggotaGrupV2) {
                        window.loadAnggotaGrupV2();
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html('<i class="ti ti-plus text-xs"></i>');
                    let msg = 'Gagal menambahkan karyawan ke grup.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    if (window.toast) {
                        window.toast.error(msg);
                    }
                }
            });
        });

        // Initial Load
        searchKaryawan('');
    })();
</script>
