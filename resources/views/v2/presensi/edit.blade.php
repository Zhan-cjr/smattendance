<div class="space-y-4">
    <!-- Header Ringkasan Karyawan -->
    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/90 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center gap-3.5">
            <div class="relative shrink-0">
                @if (!empty($karyawan->foto) && Storage::disk('public')->exists('/karyawan/' . $karyawan->foto))
                    <img src="{{ getfotoKaryawan($karyawan->foto) }}" alt="{{ $karyawan->nama_karyawan }}" class="w-11 h-13 rounded-xl object-cover border border-slate-200 shadow-2xs">
                @else
                    <div class="w-11 h-13 rounded-xl bg-orange-500 text-white border border-orange-600/30 flex items-center justify-center font-black text-base shadow-2xs">
                        {{ strtoupper(substr($karyawan->nama_karyawan, 0, 1)) }}
                    </div>
                @endif
            </div>
            <div>
                <h6 class="font-extrabold text-slate-900 text-xs sm:text-sm mb-0">{{ $karyawan->nama_karyawan }}</h6>
                <div class="flex items-center gap-2 text-[11px] text-slate-500 mt-0.5">
                    <span class="font-mono font-bold text-slate-800">{{ $karyawan->nik }}</span>
                    <span>•</span>
                    <span>{{ $karyawan->kode_dept }}</span>
                    <span>•</span>
                    <span class="font-semibold text-slate-700">{{ $karyawan->kode_cabang }}</span>
                </div>
            </div>
        </div>
        <div class="text-right sm:border-l sm:border-slate-200 sm:pl-4">
            <span class="text-[10.5px] font-bold text-slate-400 block uppercase">Tanggal Presensi</span>
            <span class="font-bold text-slate-800 text-xs mt-0.5 block">{{ DateToIndo($tanggal) }}</span>
        </div>
    </div>

    <!-- Form Koreksi Presensi -->
    <form action="{{ route('presensi.update') }}" method="POST" id="formEditPresensiV2" class="space-y-4" novalidate>
        @csrf
        <input type="hidden" value="{{ Crypt::encrypt($karyawan->nik) }}" name="nik">
        <input type="hidden" value="{{ $tanggal }}" name="tanggal">

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
            
            <!-- Status Kehadiran -->
            <div class="space-y-1.5">
                <label for="status" class="block text-xs font-bold text-slate-700">
                    Status Presensi <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                        <i class="ti ti-activity text-base"></i>
                    </span>
                    <select name="status" id="status_koreksi" class="w-full h-10 pl-10 pr-8 text-xs sm:text-sm font-semibold text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition appearance-none cursor-pointer">
                        <option value="">Pilih Status</option>
                        <option value="h" {{ $presensi != null && $presensi->status == 'h' ? 'selected' : '' }}>Hadir (H)</option>
                        <option value="i" {{ $presensi != null && $presensi->status == 'i' ? 'selected' : '' }}>Izin (I)</option>
                        <option value="s" {{ $presensi != null && $presensi->status == 's' ? 'selected' : '' }}>Sakit (S)</option>
                        <option value="c" {{ $presensi != null && $presensi->status == 'c' ? 'selected' : '' }}>Cuti (C)</option>
                        <option value="a" {{ $presensi != null && $presensi->status == 'a' ? 'selected' : '' }}>Alpha / Tidak Hadir (A)</option>
                    </select>
                    <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                        <i class="ti ti-chevron-down text-xs"></i>
                    </span>
                </div>
            </div>

            <!-- Jam Kerja -->
            <div class="space-y-1.5">
                <label for="kode_jam_kerja" class="block text-xs font-bold text-slate-700">
                    Shift / Jam Kerja <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                        <i class="ti ti-clock text-base"></i>
                    </span>
                    <select name="kode_jam_kerja" id="kode_jam_kerja_koreksi" class="w-full h-10 pl-10 pr-8 text-xs sm:text-sm font-semibold text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition appearance-none cursor-pointer">
                        <option value="">Pilih Jam Kerja</option>
                        @foreach ($jam_kerja as $d)
                            <option value="{{ $d->kode_jam_kerja }}"
                                {{ $presensi != null && $presensi->kode_jam_kerja == $d->kode_jam_kerja ? 'selected' : '' }}>
                                {{ $d->kode_jam_kerja }} - {{ $d->nama_jam_kerja }} ({{ $d->jam_masuk }} - {{ $d->jam_pulang }})
                            </option>
                        @endforeach
                    </select>
                    <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                        <i class="ti ti-chevron-down text-xs"></i>
                    </span>
                </div>
            </div>

        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
            <!-- Jam Masuk -->
            <div class="space-y-1.5">
                <label for="jam_in" class="block text-xs font-bold text-slate-700">
                    Jam Absen Masuk
                </label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                        <i class="ti ti-login text-base text-emerald-600"></i>
                    </span>
                    <input type="text" 
                           name="jam_in" 
                           id="jam_in" 
                           value="{{ $presensi != null ? $presensi->jam_in : '' }}" 
                           placeholder="YYYY-MM-DD HH:mm:ss"
                           class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
                </div>
            </div>

            <!-- Jam Pulang -->
            <div class="space-y-1.5">
                <label for="jam_out" class="block text-xs font-bold text-slate-700">
                    Jam Absen Pulang
                </label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                        <i class="ti ti-logout text-base text-rose-600"></i>
                    </span>
                    <input type="text" 
                           name="jam_out" 
                           id="jam_out" 
                           value="{{ $presensi != null ? $presensi->jam_out : '' }}" 
                           placeholder="YYYY-MM-DD HH:mm:ss"
                           class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
            <!-- Jam Mulai Istirahat -->
            <div class="space-y-1.5">
                <label for="istirahat_out" class="block text-xs font-bold text-slate-700">
                    Jam Mulai Istirahat
                </label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                        <i class="ti ti-coffee text-base text-amber-600"></i>
                    </span>
                    <input type="text" 
                           name="istirahat_out" 
                           id="istirahat_out" 
                           value="{{ $presensi != null ? $presensi->istirahat_out : '' }}" 
                           placeholder="YYYY-MM-DD HH:mm:ss"
                           class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
                </div>
            </div>

            <!-- Jam Selesai Istirahat -->
            <div class="space-y-1.5">
                <label for="istirahat_in" class="block text-xs font-bold text-slate-700">
                    Jam Selesai Istirahat
                </label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                        <i class="ti ti-coffee text-base text-blue-600"></i>
                    </span>
                    <input type="text" 
                           name="istirahat_in" 
                           id="istirahat_in" 
                           value="{{ $presensi != null ? $presensi->istirahat_in : '' }}" 
                           placeholder="YYYY-MM-DD HH:mm:ss"
                           class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
                </div>
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
                    id="btnSubmitKoreksiPresensiV2" 
                    class="px-5 py-2.5 text-xs font-bold text-white rounded-xl shadow-md flex items-center justify-center gap-1.5 transition active:scale-95 cursor-pointer"
                    style="background: linear-gradient(135deg, var(--theme-color-1, #0a5c41), var(--theme-color-2, #117956));">
                <i class="ti ti-device-floppy text-sm"></i>
                <span>Simpan Perubahan</span>
            </button>
        </div>
    </form>
</div>

<script>
    (function() {
        // Flatpickr init for date time
        if (typeof flatpickr !== 'undefined') {
            flatpickr("#jam_in, #jam_out, #istirahat_out, #istirahat_in", {
                enableTime: true,
                enableSeconds: true,
                dateFormat: "Y-m-d H:i:S",
                time_24hr: true,
                allowInput: true
            });
        }

        const statusSelect = document.getElementById('status_koreksi');
        const timeInputs = document.querySelectorAll('#jam_in, #jam_out, #istirahat_out, #istirahat_in');

        function toggleInputs() {
            if (statusSelect && statusSelect.value !== 'h') {
                timeInputs.forEach(input => {
                    input.disabled = true;
                    input.classList.add('bg-slate-100', 'cursor-not-allowed');
                });
            } else {
                timeInputs.forEach(input => {
                    input.disabled = false;
                    input.classList.remove('bg-slate-100', 'cursor-not-allowed');
                });
            }
        }

        if (statusSelect) {
            statusSelect.addEventListener('change', toggleInputs);
            toggleInputs();
        }

        $('#formEditPresensiV2').on('submit', function(e) {
            const status = $('#status_koreksi').val();
            const kodeJam = $('#kode_jam_kerja_koreksi').val();

            if (!status) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Status Presensi Wajib Diisi',
                        text: 'Silakan tentukan status kehadiran karyawan.',
                        confirmButtonText: 'Mengerti',
                        customClass: { confirmButton: 'px-4 py-2 rounded-xl text-xs font-bold bg-emerald-600 text-white' }
                    });
                } else {
                    alert('Status Presensi Wajib Diisi');
                }
                e.preventDefault();
                return false;
            }

            if (!kodeJam) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Shift / Jam Kerja Wajib Diisi',
                        text: 'Silakan tentukan jadwal kerja untuk presensi tanggal ini.',
                        confirmButtonText: 'Mengerti',
                        customClass: { confirmButton: 'px-4 py-2 rounded-xl text-xs font-bold bg-emerald-600 text-white' }
                    });
                } else {
                    alert('Shift / Jam Kerja Wajib Diisi');
                }
                e.preventDefault();
                return false;
            }

            const $btn = $('#btnSubmitKoreksiPresensiV2');
            $btn.prop('disabled', true).html('<div style="width: 14px; height: 14px; border: 2px solid #ffffff; border-top-color: transparent; border-radius: 50%; animation: spin 0.8s linear infinite; display: inline-block;"></div><span>Menyimpan...</span>');
        });
    })();
</script>
