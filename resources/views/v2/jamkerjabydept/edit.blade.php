@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
@endphp

<form action="{{ route('jamkerjabydept.update', Crypt::encrypt($jamkerjabydept->kode_jk_dept)) }}" method="POST" id="formSetJamkerja" class="space-y-4">
    @csrf
    @method('PUT')

    <!-- Detail Header (Cabang & Dept Info Card) -->
    <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-2xs">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-white border border-slate-200 text-emerald-700 flex items-center justify-center font-bold text-base shadow-xs">
                <i class="ti ti-hierarchy-2"></i>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h4 class="text-sm font-black text-slate-900 uppercase tracking-tight">
                        {{ $jamkerjabydept->nama_dept }}
                    </h4>
                    <span class="px-2 py-0.5 rounded-md text-[11px] font-mono font-bold bg-slate-200/70 text-slate-700">
                        {{ $jamkerjabydept->kode_jk_dept }}
                    </span>
                </div>
                <p class="text-xs text-slate-500 font-medium flex items-center gap-1.5 mt-0.5">
                    <i class="ti ti-building text-sky-600"></i>
                    <span>Cabang {{ $jamkerjabydept->nama_cabang }} ({{ $jamkerjabydept->kode_cabang }})</span>
                </p>
            </div>
        </div>
        <div class="text-[11px] text-slate-400 font-medium text-right sm:block hidden">
            <span>Edit Jadwal Harian</span>
        </div>
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
                                        @php
                                            $isSelected = array_key_exists($hari, $detailjamkerjabydept) && $detailjamkerjabydept[$hari] == $d->kode_jam_kerja;
                                        @endphp
                                        <option value="{{ $d->kode_jam_kerja }}" {{ $isSelected ? 'selected' : '' }}>
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
            <span>Simpan Perubahan</span>
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

        $("#formSetJamkerja").on("submit", function() {
            $("#btnSimpan").prop("disabled", true).html(`
                <i class="ti ti-loader animate-spin text-sm"></i>
                <span>Menyimpan...</span>
            `);
        });
    });
</script>
