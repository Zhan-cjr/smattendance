<form action="{{ route('penjadwalancard.update', Crypt::encrypt($jadwalcard->id)) }}" method="POST" id="formEditJadwalCardV2" class="space-y-4" novalidate>
    @csrf
    @method('PUT')

    <div class="space-y-3.5">
        <!-- Banner Info Ketentuan Edit Periode Card -->
        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 flex items-start gap-3">
            <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm shrink-0">
                <i class="ti ti-info-circle"></i>
            </div>
            <div>
                <h5 class="text-xs font-bold text-slate-800">Edit Pengaturan Periode Jadwal</h5>
                <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">
                    Perubahan rentang tanggal atau cakupan departemen/cabang akan menyesuaikan ketersediaan kartu jadwal karyawan pada periode ini.
                </p>
            </div>
        </div>

        <!-- Nama Jadwal -->
        <div class="space-y-1.5">
            <label for="nama_jadwal_edit" class="block text-xs font-bold text-slate-700">
                Nama Jadwal / Keterangan Periode <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                    <i class="ti ti-typography text-base"></i>
                </span>
                <input type="text" 
                       id="nama_jadwal_edit" 
                       name="nama_jadwal" 
                       value="{{ $jadwalcard->nama_jadwal }}" 
                       required
                       autocomplete="off"
                       class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
            </div>
            <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_nama_jadwal_edit"></div>
        </div>

        <!-- Rentang Tanggal (Dari & Sampai) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <!-- Dari Tanggal -->
            <div class="space-y-1.5">
                <label for="dari_card_edit" class="block text-xs font-bold text-slate-700">
                    Dari Tanggal <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center z-10">
                        <i class="ti ti-calendar text-base"></i>
                    </span>
                    <input type="text" 
                           id="dari_card_edit" 
                           name="dari" 
                           value="{{ $jadwalcard->dari }}" 
                           placeholder="Pilih tanggal mulai" 
                           required
                           class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
                </div>
                <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_dari_edit"></div>
            </div>

            <!-- Sampai Tanggal -->
            <div class="space-y-1.5">
                <label for="sampai_card_edit" class="block text-xs font-bold text-slate-700">
                    Sampai Tanggal <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center z-10">
                        <i class="ti ti-calendar-event text-base"></i>
                    </span>
                    <input type="text" 
                           id="sampai_card_edit" 
                           name="sampai" 
                           value="{{ $jadwalcard->sampai }}" 
                           placeholder="Pilih tanggal selesai" 
                           required
                           class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
                </div>
                <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_sampai_edit"></div>
            </div>
        </div>

        <!-- Scope: Cabang & Departemen -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <!-- Cabang (Opsional) -->
            <div class="space-y-1.5">
                <label for="kode_cabang_edit" class="block text-xs font-bold text-slate-700">
                    Cabang Target <span class="text-slate-400 font-normal">(Opsional)</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                        <i class="ti ti-map-pin text-base"></i>
                    </span>
                    <select id="kode_cabang_edit" 
                            name="kode_cabang" 
                            class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition cursor-pointer">
                        <option value="">Semua Cabang</option>
                        @foreach ($cabang as $c)
                            <option value="{{ $c->kode_cabang }}" {{ $jadwalcard->kode_cabang == $c->kode_cabang ? 'selected' : '' }}>
                                {{ $c->nama_cabang }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Departemen (Opsional) -->
            <div class="space-y-1.5">
                <label for="kode_dept_edit" class="block text-xs font-bold text-slate-700">
                    Departemen Target <span class="text-slate-400 font-normal">(Opsional)</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                        <i class="ti ti-building text-base"></i>
                    </span>
                    <select id="kode_dept_edit" 
                            name="kode_dept" 
                            class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition cursor-pointer">
                        <option value="">Semua Departemen</option>
                        @foreach ($departemen as $d)
                            <option value="{{ $d->kode_dept }}" {{ $jadwalcard->kode_dept == $d->kode_dept ? 'selected' : '' }}>
                                {{ $d->nama_dept }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Catatan / Keterangan -->
        <div class="space-y-1.5">
            <label for="keterangan_card_edit" class="block text-xs font-bold text-slate-700">
                Catatan Tambahan <span class="text-slate-400 font-normal">(Opsional)</span>
            </label>
            <textarea id="keterangan_card_edit" 
                      name="keterangan" 
                      rows="2" 
                      placeholder="Catatan tambahan untuk periode ini (opsional)..." 
                      class="w-full p-3 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">{{ $jadwalcard->keterangan }}</textarea>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="pt-3 flex items-center justify-end gap-2.5 border-t border-slate-100">
        <button type="button" 
                class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-bold transition active:scale-95 cursor-pointer" 
                data-bs-dismiss="modal">
            Batal
        </button>
        <button type="submit" 
                id="btnUpdateCardV2" 
                class="px-5 py-2.5 text-xs font-bold text-white rounded-xl shadow-md flex items-center justify-center gap-1.5 transition active:scale-95 cursor-pointer hover:opacity-95"
                style="background: linear-gradient(135deg, var(--theme-color-1, #0a5c41), var(--theme-color-2, #117956));">
            <i class="ti ti-device-floppy text-sm"></i>
            <span>Simpan Perubahan</span>
        </button>
    </div>
</form>

<script>
    (function() {
        const form = document.getElementById('formEditJadwalCardV2');

        if (typeof flatpickr !== 'undefined') {
            flatpickr("#dari_card_edit", {
                locale: "id",
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d F Y"
            });
            flatpickr("#sampai_card_edit", {
                locale: "id",
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d F Y"
            });
        }

        $(form).on('submit', function(e) {
            const nama = $('#nama_jadwal_edit').val().trim();
            const dari = $('#dari_card_edit').val().trim();
            const sampai = $('#sampai_card_edit').val().trim();
            let hasError = false;

            $('.error-feedback').addClass('hidden').text('');
            $('#nama_jadwal_edit, #dari_card_edit, #sampai_card_edit').removeClass('border-rose-500');

            if (!nama) {
                $('#err_nama_jadwal_edit').text('Nama jadwal wajib diisi').removeClass('hidden');
                $('#nama_jadwal_edit').addClass('border-rose-500');
                hasError = true;
            }

            if (!dari) {
                $('#err_dari_edit').text('Tanggal mulai periode wajib diisi').removeClass('hidden');
                $('#dari_card_edit').addClass('border-rose-500');
                hasError = true;
            }

            if (!sampai) {
                $('#err_sampai_edit').text('Tanggal akhir periode wajib diisi').removeClass('hidden');
                $('#sampai_card_edit').addClass('border-rose-500');
                hasError = true;
            }

            if (hasError) {
                e.preventDefault();
                return false;
            }

            const $btn = $('#btnUpdateCardV2');
            $btn.prop('disabled', true).html('<div style="width: 14px; height: 14px; border: 2px solid #ffffff; border-top-color: transparent; border-radius: 50%; animation: spin 0.8s linear infinite; display: inline-block;"></div><span>Menyimpan...</span>');
        });
    })();
</script>
