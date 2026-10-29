@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
@endphp

<div class="space-y-4">
    <!-- Search Bar & Statistics Pill -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
        <!-- Live Instant Search Input -->
        <div class="relative w-full sm:max-w-md">
            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                <i class="ti ti-search text-base"></i>
            </span>
            <input type="text" id="searchKaryawanV2" 
                   placeholder="Cari nama karyawan atau NIK..." 
                   autocomplete="off" 
                   value="{{ request('q') }}"
                   class="w-full h-10 pl-10 pr-9 text-xs sm:text-sm font-semibold text-slate-800 bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl focus:border-emerald-600 focus:ring-2 focus:ring-emerald-600/20 outline-none transition shadow-2xs">
            @if(request('q'))
                <button type="button" id="btnClearSearchKaryawanV2" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 cursor-pointer">
                    <i class="ti ti-x text-xs"></i>
                </button>
            @endif
        </div>

        <div class="text-[11px] font-bold text-slate-500 flex items-center gap-1.5 self-end sm:self-center">
            <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <span>Ditemukan <strong class="text-slate-800">{{ $karyawan->count() }}</strong> karyawan</span>
        </div>
    </div>

    <!-- Scrollable Modern Table Container with Seamless Header -->
    <div class="bg-white border border-slate-200/90 rounded-2xl shadow-xs overflow-hidden">
        
        <!-- Seamless Solid Card Header -->
        <div class="px-5 py-3 flex items-center justify-between gap-2 text-white border-0"
             style="background-color: {{ $themeColor1 }};">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-white/20 text-white flex items-center justify-center text-xs font-bold border border-white/20 shadow-2xs backdrop-blur-xs">
                    <i class="ti ti-users"></i>
                </div>
                <div>
                    <h3 class="text-xs sm:text-sm font-extrabold text-white tracking-tight">Daftar Karyawan Aktif</h3>
                </div>
            </div>
            <span class="px-2.5 py-0.5 rounded-full text-[10.5px] font-bold bg-white/20 text-white border border-white/30 shadow-2xs backdrop-blur-xs">
                {{ $karyawan->count() }} Data
            </span>
        </div>

        <!-- Seamless Table Container -->
        <div class="overflow-x-auto w-full max-h-[48vh] custom-scrollbar" style="background-color: {{ $themeColor1 }};">
            <table class="w-full text-left text-xs border-0 border-collapse whitespace-nowrap" id="tableKaryawanLookupV2">
                <thead class="text-white font-bold uppercase tracking-wider text-[11px] sticky top-0 z-20 border-0 border-t-0 border-b border-black/15"
                       style="background-color: {{ $themeColor1 }};">
                    <tr class="border-0 border-t-0 text-white/90">
                        <th class="py-2.5 px-4 text-center w-12 text-white/90 border-0">Foto</th>
                        <th class="py-2.5 px-4 text-white/90 border-0">NIK</th>
                        <th class="py-2.5 px-4 text-white/90 border-0">Nama Karyawan</th>
                        <th class="py-2.5 px-4 text-white/90 border-0">Jabatan & Dept</th>
                        <th class="py-2.5 px-4 text-white/90 border-0">Cabang Penempatan</th>
                        <th class="py-2.5 px-4 text-center w-24 text-white/90 border-0">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($karyawan as $d)
                        @php
                            $fotoUrl = (!empty($d->foto) && Storage::disk('public')->exists('/karyawan/' . $d->foto))
                                ? getfotoKaryawan($d->foto)
                                : null;
                        @endphp
                        <tr class="hover:bg-emerald-50/40 transition-colors group cursor-pointer itemRowKaryawan"
                            data-nik="{{ $d->nik }}"
                            data-nama="{{ $d->nama_karyawan }}">
                            
                            <!-- Foto -->
                            <td class="py-2.5 px-4 text-center">
                                <div class="w-9 h-10 rounded-xl overflow-hidden bg-slate-100 border border-slate-200/80 mx-auto flex items-center justify-center shadow-2xs shrink-0 group-hover:scale-105 transition">
                                    @if ($fotoUrl)
                                        <img src="{{ $fotoUrl }}" alt="{{ $d->nama_karyawan }}" class="w-full h-full object-cover">
                                    @else
                                        <span class="font-extrabold text-slate-700 text-xs">
                                            {{ strtoupper(substr($d->nama_karyawan, 0, 1)) }}
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- NIK -->
                            <td class="py-2.5 px-4 font-mono font-bold text-slate-700">
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 border border-slate-200 text-[11px] text-slate-800">
                                    {{ $d->nik }}
                                </span>
                            </td>

                            <!-- Nama & Status -->
                            <td class="py-2.5 px-4">
                                <div class="font-extrabold text-slate-900 group-hover:text-emerald-700 transition flex items-center gap-1.5">
                                    <span>{{ $d->nama_karyawan }}</span>
                                    @if($d->status_aktif_karyawan == '1')
                                        <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block" title="Aktif"></span>
                                    @else
                                        <span class="w-2 h-2 rounded-full bg-rose-500 inline-block" title="Nonaktif"></span>
                                    @endif
                                </div>
                                <span class="text-[10.5px] text-slate-400 block mt-0.5">
                                    {{ $d->status_karyawan == 'K' ? 'Kontrak (PKWT)' : ($d->status_karyawan == 'T' ? 'Tetap (PKWTT)' : 'Pegawai') }}
                                </span>
                            </td>

                            <!-- Jabatan & Dept -->
                            <td class="py-2.5 px-4">
                                <div class="font-bold text-slate-800 text-xs">
                                    {{ $d->jabatan->nama_jabatan ?? '-' }}
                                </div>
                                <span class="text-[10.5px] text-slate-500 flex items-center gap-1 mt-0.5">
                                    <i class="ti ti-building text-slate-400"></i>
                                    {{ $d->departemen->nama_dept ?? '-' }}
                                </span>
                            </td>

                            <!-- Cabang -->
                            <td class="py-2.5 px-4">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10.5px] font-bold bg-slate-50 text-slate-700 border border-slate-200/80">
                                    <i class="ti ti-map-pin text-slate-400 text-xs"></i>
                                    {{ $d->cabang->nama_cabang ?? '-' }}
                                </span>
                            </td>

                            <!-- Aksi Pilih -->
                            <td class="py-2.5 px-4 text-center">
                                <button type="button" 
                                        class="btnPilihKaryawan inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-white shadow-2xs transition-all duration-150 active:scale-95 cursor-pointer hover:opacity-95"
                                        style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});"
                                        data-nik="{{ $d->nik }}" 
                                        data-nama="{{ $d->nama_karyawan }}">
                                    <i class="ti ti-check text-xs"></i>
                                    <span>Pilih</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2 text-xl">
                                    <i class="ti ti-users"></i>
                                </div>
                                <h5 class="text-xs font-bold text-slate-700">Data Karyawan Tidak Ditemukan</h5>
                                <p class="text-[11px] text-slate-400 mt-0.5">Coba gunakan kata kunci pencarian yang berbeda</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Footer Info -->
        <div class="px-4 py-2.5 bg-slate-50/80 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400">
            <span>Menampilkan maksimal 20 karyawan terbaru</span>
            <span class="hidden sm:inline">Klik baris atau tombol <strong>Pilih</strong> untuk memilih</span>
        </div>
    </div>
</div>

<script>
    $(function() {
        var typingTimer;
        var doneTypingInterval = 300;

        $("#searchKaryawanV2").on('keyup', function() {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(performSearchV2, doneTypingInterval);
        });

        $('#btnClearSearchKaryawanV2').on('click', function() {
            $("#searchKaryawanV2").val('');
            performSearchV2();
        });

        function performSearchV2() {
            var q = $("#searchKaryawanV2").val();
            var targetContainer = $("#loadLookupKaryawanV2").length ? $("#loadLookupKaryawanV2") : $("#loadLookupKaryawan");
            targetContainer.load("{{ route('karyawan.getkaryawantable') }}?q=" + encodeURIComponent(q));
        }

        // Click row to select
        $('.itemRowKaryawan').on('click', function(e) {
            if ($(e.target).closest('button').length) return;
            var nik = $(this).data('nik');
            var nama = $(this).data('nama');
            if (nik && nama) {
                $("#v2_nik").val(nik);
                $("#v2_nama_karyawan").val(nik + ' - ' + nama);
                $("#modalLookupKaryawanV2").addClass('hidden');
            }
        });

        // Auto focus
        setTimeout(function() {
            $("#searchKaryawanV2").focus();
        }, 150);
    });
</script>
