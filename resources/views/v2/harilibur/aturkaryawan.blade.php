@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
@endphp

<form action="#" id="frmKaryawanV2" class="space-y-4">
    <!-- Top Action Bar (Bulk Add / Remove) -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 p-3.5 rounded-xl bg-slate-50 border border-slate-200/80">
        <div class="text-xs text-slate-600 font-medium">
            Atur status libur massal untuk semua karyawan pada cabang/departemen yang dipilih:
        </div>
        <div class="flex items-center gap-2 shrink-0 w-full sm:w-auto">
            <button type="button" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 px-3.5 py-2 text-white font-bold rounded-xl text-xs shadow-xs transition active:scale-95 cursor-pointer hover:opacity-95"
                    style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});"
                    id="tambahkansemua">
                <i class="ti ti-user-plus text-sm"></i>
                <span>Tambahkan Semua</span>
            </button>
            <button type="button" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 px-3.5 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold rounded-xl text-xs border border-rose-200 shadow-2xs transition active:scale-95 cursor-pointer"
                    id="batalkansemua">
                <i class="ti ti-user-minus text-sm"></i>
                <span>Batalkan Semua</span>
            </button>
        </div>
    </div>

    <!-- Filter Bar (Departemen & Search Input) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <!-- Departemen Filter -->
        <div class="space-y-1">
            <label class="block text-xs font-bold text-slate-700">Filter Departemen</label>
            <div class="relative">
                <select name="kode_dept" id="kode_dept_modal" 
                        class="w-full h-10 px-3 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300 rounded-xl text-slate-800 font-medium shadow-2xs focus:outline-none focus:border-emerald-600 transition select2-dept-v2">
                    <option value="">Semua Departemen</option>
                    @foreach ($departemen as $dep)
                        <option value="{{ $dep->kode_dept }}">
                            {{ textUpperCase($dep->nama_dept) }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Search Input -->
        <div class="space-y-1">
            <label class="block text-xs font-bold text-slate-700">Cari Nama Karyawan</label>
            <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                    <i class="ti ti-search text-base"></i>
                </span>
                <input type="text" 
                       name="nama_karyawan" 
                       id="nama_karyawan_modal" 
                       placeholder="Ketik nama karyawan..." 
                       class="w-full h-10 pl-10 pr-3.5 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300 rounded-xl text-slate-800 font-medium placeholder-slate-400 shadow-2xs focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition">
            </div>
        </div>
    </div>

    <!-- Master Karyawan Selection Table -->
    <div class="border border-slate-200/90 rounded-xl overflow-hidden shadow-2xs">
        <div class="overflow-x-auto max-h-[380px] overflow-y-auto">
            <table class="w-full text-left text-xs border-collapse" id="tabelkaryawan">
                <thead class="bg-slate-100/90 sticky top-0 border-b border-slate-200 text-[11px] font-bold text-slate-600 uppercase tracking-wider z-10 backdrop-blur-xs">
                    <tr>
                        <th class="py-2.5 px-3 text-center w-12">No</th>
                        <th class="py-2.5 px-3 w-36">NIK</th>
                        <th class="py-2.5 px-3">Nama Karyawan</th>
                        <th class="py-2.5 px-3 w-36">Departemen</th>
                        <th class="py-2.5 px-3 text-center w-28">Status Libur</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700 font-medium bg-white" id="loadkaryawan">
                    <tr>
                        <td colspan="5" class="py-10 text-center text-slate-400">
                            <div class="w-8 h-8 border-3 border-emerald-500/20 border-t-emerald-600 rounded-full animate-spin mx-auto mb-2"></div>
                            <p class="text-xs font-medium">Memuat master data karyawan...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Footer -->
    <div class="pt-2 flex items-center justify-between text-xs text-slate-400 border-t border-slate-100">
        <span>Klik ikon (+) untuk menambahkan atau (-) untuk membatalkan libur karyawan.</span>
        <button type="button" data-bs-dismiss="modal" 
                class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-bold transition cursor-pointer">
            Selesai
        </button>
    </div>
</form>

<script>
    (function() {
        const form = $('#frmKaryawanV2');

        function loadkaryawan() {
            const kode_libur = "{{ Crypt::encrypt($harilibur->kode_libur) }}";
            const kode_dept = form.find("#kode_dept_modal").val();
            const nama_karyawan = form.find("#nama_karyawan_modal").val();
            
            $("#loadkaryawan").html(`
                <tr>
                    <td colspan="5" class="py-10 text-center text-slate-400">
                        <div class="w-8 h-8 border-3 border-emerald-500/20 border-t-emerald-600 rounded-full animate-spin mx-auto mb-2"></div>
                        <p class="text-xs font-medium">Memuat data...</p>
                    </td>
                </tr>
            `);

            $.ajax({
                type: 'POST',
                url: `/harilibur/getkaryawan`,
                data: {
                    _token: "{{ csrf_token() }}",
                    kode_libur: kode_libur,
                    kode_dept: kode_dept,
                    nama_karyawan: nama_karyawan
                },
                cache: false,
                success: function(respond) {
                    $("#loadkaryawan").html(respond);
                    if (typeof loadliburkaryawan === "function") {
                        loadliburkaryawan();
                    }
                }
            });
        }

        loadkaryawan();

        form.find("#kode_dept_modal").change(function() {
            loadkaryawan();
        });

        let typingTimer;
        form.find("#nama_karyawan_modal").keyup(function() {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(loadkaryawan, 300);
        });

        // Toggle Single Karyawan Libur Status
        $(document).off('click', '#tabelkaryawan .updateLibur').on('click', '#tabelkaryawan .updateLibur', function(e) {
            e.preventDefault();
            const $btn = $(this);
            const nik = $btn.attr('nik');
            const kode_libur = "{{ $harilibur->kode_libur }}";
            
            $btn.html('<div class="w-3.5 h-3.5 border-2 border-current border-t-transparent rounded-full animate-spin inline-block"></div>');
            
            $.ajax({
                type: 'POST',
                url: `/harilibur/updateliburkaryawan`,
                data: {
                    _token: "{{ csrf_token() }}",
                    nik: nik,
                    kode_libur: kode_libur
                },
                cache: false,
                success: function(respond) {
                    if (respond.success == true) {
                        loadkaryawan();
                    } else {
                        Swal.fire('Peringatan', respond.message || 'Gagal mengubah status', 'warning');
                    }
                }
            });
        });

        // Tambahkan Semua
        $("#tambahkansemua").click(function(e) {
            e.preventDefault();
            const kode_libur = "{{ $harilibur->kode_libur }}";
            const kode_dept = form.find("#kode_dept_modal").val();
            
            $("#loadkaryawan").html(`
                <tr>
                    <td colspan="5" class="py-10 text-center text-slate-400">
                        <div class="w-8 h-8 border-3 border-emerald-500/20 border-t-emerald-600 rounded-full animate-spin mx-auto mb-2"></div>
                        <p class="text-xs font-medium">Menambahkan semua karyawan...</p>
                    </td>
                </tr>
            `);

            $.ajax({
                type: 'POST',
                url: `/harilibur/tambahkansemua`,
                data: {
                    _token: "{{ csrf_token() }}",
                    kode_libur: kode_libur,
                    kode_dept: kode_dept
                },
                cache: false,
                success: function(respond) {
                    if (respond.success == true) {
                        loadkaryawan();
                    } else {
                        Swal.fire('Peringatan', respond.message, 'warning');
                    }
                }
            });
        });

        // Batalkan Semua
        $("#batalkansemua").click(function(e) {
            e.preventDefault();
            const kode_libur = "{{ $harilibur->kode_libur }}";
            const kode_dept = form.find("#kode_dept_modal").val();
            
            $("#loadkaryawan").html(`
                <tr>
                    <td colspan="5" class="py-10 text-center text-slate-400">
                        <div class="w-8 h-8 border-3 border-emerald-500/20 border-t-emerald-600 rounded-full animate-spin mx-auto mb-2"></div>
                        <p class="text-xs font-medium">Membatalkan semua karyawan...</p>
                    </td>
                </tr>
            `);

            $.ajax({
                type: 'POST',
                url: `/harilibur/batalkansemua`,
                data: {
                    _token: "{{ csrf_token() }}",
                    kode_libur: kode_libur,
                    kode_dept: kode_dept
                },
                cache: false,
                success: function(respond) {
                    if (respond.success == true) {
                        loadkaryawan();
                    } else {
                        Swal.fire('Peringatan', respond.message, 'warning');
                    }
                }
            });
        });
    })();
</script>
