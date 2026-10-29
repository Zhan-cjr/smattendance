@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
@endphp

<form action="{{ route('jamkerjabydept.store') }}" method="POST" id="formSetJamkerja" class="space-y-4">
    @csrf

    <!-- Form Selection: Cabang & Departemen -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <!-- Cabang -->
        <div>
            <label for="modal_kode_cabang" class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center gap-1">
                <span>Cabang / Unit</span>
                <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                    <i class="ti ti-building text-base"></i>
                </span>
                <select name="kode_cabang" id="modal_kode_cabang" 
                        class="w-full h-10 pl-10 pr-9 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition appearance-none cursor-pointer">
                    <option value="">-- Pilih Cabang --</option>
                    @foreach ($cabang as $c)
                        <option value="{{ $c->kode_cabang }}">{{ textUpperCase($c->nama_cabang) }} ({{ $c->kode_cabang }})</option>
                    @endforeach
                </select>
                <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                    <i class="ti ti-chevron-down text-xs"></i>
                </span>
            </div>
            <p class="error-feedback text-[11px] text-rose-500 mt-1 hidden font-medium" id="error_kode_cabang"></p>
        </div>

        <!-- Departemen -->
        <div>
            <label for="modal_kode_dept" class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center gap-1">
                <span>Departemen</span>
                <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                    <i class="ti ti-hierarchy-2 text-base"></i>
                </span>
                <select name="kode_dept" id="modal_kode_dept" 
                        class="w-full h-10 pl-10 pr-9 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition appearance-none cursor-pointer">
                    <option value="">-- Pilih Departemen --</option>
                    @foreach ($departemen as $d)
                        <option value="{{ $d->kode_dept }}">{{ textUpperCase($d->nama_dept) }} ({{ $d->kode_dept }})</option>
                    @endforeach
                </select>
                <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                    <i class="ti ti-chevron-down text-xs"></i>
                </span>
            </div>
            <p class="error-feedback text-[11px] text-rose-500 mt-1 hidden font-medium" id="error_kode_dept"></p>
        </div>
    </div>

    <!-- Info Helper Banner -->
    <div class="p-3 bg-emerald-50/70 border border-emerald-100 rounded-xl flex items-start gap-2.5 text-xs text-slate-600">
        <i class="ti ti-info-circle text-base text-emerald-700 shrink-0 mt-0.5"></i>
        <span>Tentukan jadwal jam kerja harian untuk departemen terpilih (Senin s/d Minggu). Kosongkan hari jika merupakan hari libur rutin.</span>
    </div>

    <!-- Table of Days -->
    <div class="border border-slate-200 rounded-xl overflow-hidden shadow-2xs">
        <table class="w-full text-xs text-left">
            <thead style="background-color: {{ $themeColor1 }};" class="text-white font-bold uppercase tracking-wider text-[11px]">
                <tr>
                    <th class="py-2.5 px-4 w-32 border-0">Hari</th>
                    <th class="py-2.5 px-4 border-0">Pilihan Jam Kerja</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
                @php
                    $nama_hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
                @endphp
                @foreach ($nama_hari as $hari)
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="py-2.5 px-4 font-bold text-slate-800">
                            <input type="hidden" name="hari[]" value="{{ $hari }}">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full {{ in_array($hari, ['Sabtu', 'Minggu']) ? 'bg-rose-400' : 'bg-emerald-500' }}"></span>
                                <span>{{ $hari }}</span>
                            </div>
                        </td>
                        <td class="py-2.5 px-4">
                            <div class="relative">
                                <select name="kode_jam_kerja[]" 
                                        class="w-full h-9 pl-3 pr-8 bg-slate-50 hover:bg-white focus:bg-white border border-slate-300 rounded-lg text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition appearance-none cursor-pointer">
                                    <option value="">-- Libur / Tidak Ada Jam Kerja --</option>
                                    @foreach ($jamkerja as $d)
                                        <option value="{{ $d->kode_jam_kerja }}">
                                            {{ $d->nama_jam_kerja }} ({{ $d->jam_masuk }} - {{ $d->jam_pulang }})
                                        </option>
                                    @endforeach
                                </select>
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                                    <i class="ti ti-chevron-down text-[10px]"></i>
                                </span>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Action Buttons -->
    <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
        <button type="button" class="btn-close-modal px-4 py-2 text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition cursor-pointer">
            Batal
        </button>
        <button type="submit" id="btnSimpan" 
                class="px-5 py-2 text-xs font-bold text-white rounded-xl shadow-xs transition inline-flex items-center gap-2 cursor-pointer hover:opacity-95 active:scale-95"
                style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
            <i class="ti ti-device-floppy text-sm"></i>
            <span>Simpan Konfigurasi</span>
        </button>
    </div>
</form>

<script>
    $(document).ready(function() {
        $(".btn-close-modal").on("click", function() {
            $("#modalBackdrop").removeClass("opacity-100").addClass("opacity-0");
            $("#modalPanel").removeClass("opacity-100 scale-100").addClass("opacity-0 scale-95");
            setTimeout(function() {
                $("#modal").addClass("hidden");
                $("#loadmodal").html("");
            }, 250);
        });

        $("#formSetJamkerja").on("submit", function(e) {
            let isValid = true;
            $(".error-feedback").addClass("hidden").text("");
            $("select").removeClass("border-rose-500");

            const kodeCabang = $("#modal_kode_cabang").val();
            const kodeDept = $("#modal_kode_dept").val();

            if (!kodeCabang) {
                isValid = false;
                $("#modal_kode_cabang").addClass("border-rose-500");
                $("#error_kode_cabang").removeClass("hidden").text("Silakan pilih Cabang / Unit Kerja.");
            }

            if (!kodeDept) {
                isValid = false;
                $("#modal_kode_dept").addClass("border-rose-500");
                $("#error_kode_dept").removeClass("hidden").text("Silakan pilih Departemen.");
            }

            if (!isValid) {
                e.preventDefault();
                return false;
            }

            $("#btnSimpan").prop("disabled", true).html(`
                <i class="ti ti-loader animate-spin text-sm"></i>
                <span>Menyimpan...</span>
            `);
        });
    });
</script>
