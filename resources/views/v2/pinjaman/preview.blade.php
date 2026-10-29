@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
@endphp

<div class="rounded-2xl border border-slate-200/90 overflow-hidden shadow-2xs">
    <div class="px-4 py-3 flex items-center justify-between text-white border-0"
         style="background-color: {{ $themeColor1 }};">
        <h5 class="text-xs font-black text-white flex items-center gap-2">
            <i class="ti ti-list-numbers"></i>
            <span>Rencana Cicilan Pinjaman</span>
        </h5>
        <span class="text-[10.5px] font-bold bg-white/20 text-white px-2 py-0.5 rounded-full border border-white/25">Simulasi Otomatis</span>
    </div>

    <div class="overflow-x-auto w-full" style="background-color: {{ $themeColor1 }};">
        <table class="w-full text-xs text-left border-0 border-collapse">
            <thead class="text-white font-bold uppercase tracking-wider text-[11px] border-0 border-t-0 border-b border-black/15"
                   style="background-color: {{ $themeColor1 }};">
                <tr class="border-0 border-t-0 text-white/90">
                    <th class="py-2.5 px-4 text-center w-16 text-white/90 border-0 border-t-0">No</th>
                    <th class="py-2.5 px-4 text-white/90 border-0 border-t-0">Periode Bulan / Tahun</th>
                    <th class="py-2.5 px-4 text-right text-white/90 border-0 border-t-0">Nominal Angsuran (Rp)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
                @php $total = 0; @endphp
                @foreach($preview as $p)
                @php $total += $p['nominal']; @endphp
                <tr class="hover:bg-slate-50/70 transition-colors">
                    <td class="py-2 px-4 text-center font-bold text-slate-700">{{ $p['cicilan_ke'] }}</td>
                    <td class="py-2 px-4 font-semibold text-slate-800">
                        {{ getNamabulan($p['bulan']) }} {{ $p['tahun'] }}
                        <input type="hidden" name="rencana_bulan[]" value="{{ $p['bulan'] }}">
                        <input type="hidden" name="rencana_tahun[]" value="{{ $p['tahun'] }}">
                    </td>
                    <td class="py-2 px-4 text-right">
                        <input type="text" name="rencana_nominal[]" 
                               class="w-36 px-2.5 py-1.5 text-xs text-right font-bold bg-slate-50 border border-slate-300 rounded-lg text-slate-800 focus:outline-none focus:border-emerald-600 rencana_nominal mny shadow-2xs" 
                               value="{{ number_format($p['nominal'], 0, ',', '.') }}">
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-slate-50/90 border-t-2 border-slate-200 font-bold">
                <tr>
                    <td colspan="2" class="py-3 px-4 text-xs font-black text-slate-700 uppercase tracking-wider">
                        Total Rencana Cicilan
                    </td>
                    <td class="py-3 px-4 text-right">
                        <span id="labelTotalRencana" class="text-sm font-black text-emerald-700">Rp {{ number_format($total, 0, ',', '.') }}</span>
                        <input type="hidden" id="totalRencanaValue" value="{{ $total }}">
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<div class="p-3.5 rounded-xl bg-sky-50 border border-sky-200 text-sky-800 text-[11.5px] flex items-start gap-2.5 mt-3">
    <i class="ti ti-info-circle text-base text-sky-600 shrink-0 mt-0.5"></i>
    <div>
        Anda dapat menyesuaikan nominal per cicilan di atas. Pastikan total rencana cicilan tetap sesuai dengan nominal pokok pinjaman yang diajukan.
    </div>
</div>

<script>
    $(function() {
        if (typeof $.fn.maskMoney !== 'undefined') {
            $(".mny").maskMoney({
                thousands: '.', decimal: ',', precision: 0, allowZero: true
            });
        }

        $(".rencana_nominal").on('keyup change', function() {
            let total = 0;
            $(".rencana_nominal").each(function() {
                let val = $(this).val().replace(/\./g, '') || 0;
                total += parseInt(val);
            });
            $("#labelTotalRencana").text('Rp ' + total.toLocaleString('id-ID'));
            $("#totalRencanaValue").val(total);
        });
    });
</script>
