{{-- V2 Modal Body: Rincian & Cetak Karyawan Slip Gaji Harian --}}
<form action="{{ route('slipgajiharian.cetak') }}" method="POST" target="_blank" id="formCetakHarianDetailV2" class="space-y-4">
    @csrf
    <input type="hidden" name="dari" value="{{ $slipgaji->dari }}">
    <input type="hidden" name="sampai" value="{{ $slipgaji->sampai }}">

    <!-- Header Info & Print Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-3.5 bg-slate-50 border border-slate-200/90 rounded-xl">
        <div>
            <div class="text-xs font-bold text-slate-800">
                Kode Batch: <span class="font-mono text-emerald-700">{{ $slipgaji->kode_slip_gaji_harian }}</span>
            </div>
            <div class="text-[11px] text-slate-500 mt-0.5">
                Periode: <strong>{{ date('d/m/Y', strtotime($slipgaji->dari)) }}</strong> s/d <strong>{{ date('d/m/Y', strtotime($slipgaji->sampai)) }}</strong> &bull; Total: <strong class="text-slate-800">{{ $detail->count() }} Karyawan</strong>
            </div>
        </div>
        <button type="submit" name="cetakButton" class="inline-flex items-center justify-center gap-1.5 px-4 py-2 text-white font-bold rounded-xl text-xs shadow-xs transition active:scale-95 cursor-pointer hover:opacity-95 shrink-0"
                style="background: linear-gradient(135deg, {{ $themeColor1 ?? '#0a5c41' }}, {{ $themeColor2 ?? '#117956' }});">
            <i class="ti ti-printer text-sm"></i>
            <span>Cetak Slip Terpilih</span>
        </button>
    </div>

    <!-- Table of Selected Employees -->
    <div class="border border-slate-200/90 rounded-xl overflow-hidden shadow-2xs">
        <div class="overflow-x-auto max-h-[380px] overflow-y-auto">
            <table class="w-full text-left text-xs border-collapse" id="tableDetailHarianV2">
                <thead class="bg-slate-100 text-slate-700 font-bold uppercase tracking-wider text-[10.5px] sticky top-0 z-10 border-b border-slate-200">
                    <tr>
                        <th width="40" class="py-2.5 px-3 text-center">
                            <input type="checkbox" id="checkAllHarianDetailV2" class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300 cursor-pointer">
                        </th>
                        <th class="py-2.5 px-3">NIK</th>
                        <th class="py-2.5 px-3">Nama Karyawan</th>
                        <th class="py-2.5 px-3">Jabatan</th>
                        <th class="py-2.5 px-3">Departemen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white text-slate-700 text-xs">
                    @forelse ($detail as $d)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-2 px-3 text-center whitespace-nowrap">
                                <input type="checkbox" name="nik[]" value="{{ $d->nik }}" class="checkItemHarianDetailV2 w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300 cursor-pointer">
                            </td>
                            <td class="py-2 px-3 whitespace-nowrap font-mono text-slate-800 font-bold">{{ $d->nik }}</td>
                            <td class="py-2 px-3 whitespace-nowrap font-bold text-slate-900">{{ $d->nama_karyawan }}</td>
                            <td class="py-2 px-3 whitespace-nowrap text-slate-600">{{ $d->nama_jabatan ?? '-' }}</td>
                            <td class="py-2 px-3 whitespace-nowrap text-slate-600">{{ $d->nama_dept ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-slate-400 text-xs">
                                Tidak ada data karyawan pada batch ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Action Bar -->
    <div class="pt-2 flex items-center justify-end">
        <button type="button" class="btnCloseModalHarian px-4 py-2 text-slate-600 hover:bg-slate-100 rounded-xl text-xs font-bold transition">
            Tutup
        </button>
    </div>
</form>

<script>
    $(function() {
        // Initial state: check all
        $("#checkAllHarianDetailV2").prop('checked', true);
        $(".checkItemHarianDetailV2").prop('checked', true);

        // Check All Handle
        $("#checkAllHarianDetailV2").click(function() {
            $(".checkItemHarianDetailV2").prop('checked', $(this).prop('checked'));
        });

        // Toggle checkAll state when single items are clicked
        $(".checkItemHarianDetailV2").click(function() {
            if (!$(this).prop("checked")) {
                $("#checkAllHarianDetailV2").prop("checked", false);
            }
            if ($(".checkItemHarianDetailV2:checked").length == $(".checkItemHarianDetailV2").length) {
                $("#checkAllHarianDetailV2").prop("checked", true);
            }
        });

        // Form Validation before submit
        $("#formCetakHarianDetailV2").submit(function(e) {
            if ($(".checkItemHarianDetailV2:checked").length == 0) {
                Swal.fire({
                    title: "Oops!",
                    text: 'Pilih minimal 1 karyawan untuk dicetak !',
                    icon: "warning",
                    showConfirmButton: true,
                    customClass: {
                        popup: 'rounded-2xl shadow-xl border border-slate-100',
                        confirmButton: 'px-4 py-2 rounded-xl text-xs font-bold shadow-xs'
                    }
                });
                return false;
            }
        });
    });
</script>
