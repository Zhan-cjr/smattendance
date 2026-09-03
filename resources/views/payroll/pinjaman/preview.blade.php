<div class="card shadow-sm" style="border-color: var(--theme-color-1) !important;">
    <div class="card-header py-2" style="background-color: var(--theme-color-1) !important; color: white !important;">
        <h6 class="mb-0 text-white"><i class="ti ti-list-numbers me-1"></i> Rencana Cicilan Pinjaman</h6>
    </div>
    <div class="card-body p-0 text-start">
        <div class="table-responsive">
            <table class="table table-sm table-striped table-hover mb-0">
                <thead style="background-color: var(--theme-color-1) !important; color: white !important;">
                    <tr>
                        <th class="text-center text-white py-2">Cicilan Ke</th>
                        <th class="text-start px-3 text-white py-2">Bulan / Tahun</th>
                        <th class="text-end px-3 text-white py-2">Nominal Cicilan</th>
                    </tr>
                </thead>
                <tbody>
                    @php $total = 0; @endphp
                    @foreach($preview as $p)
                    @php $total += $p['nominal']; @endphp
                    <tr>
                        <td class="text-center fw-bold py-2">{{ $p['cicilan_ke'] }}</td>
                        <td class="text-start px-3 py-2">
                            {{ getNamabulan($p['bulan']) }} {{ $p['tahun'] }}
                            <!-- Hidden Inputs for Store -->
                            <input type="hidden" name="rencana_bulan[]" value="{{ $p['bulan'] }}">
                            <input type="hidden" name="rencana_tahun[]" value="{{ $p['tahun'] }}">
                        </td>
                        <td class="text-end px-3 py-2">
                            <input type="text" name="rencana_nominal[]" class="form-control form-control-sm text-end fw-bold rencana_nominal mny" 
                                value="{{ number_format($p['nominal'], 0, ',', '.') }}">
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background-color: var(--theme-color-1) !important;">
                        <th colspan="2" class="py-3 px-4 border-0">
                            <div class="d-flex align-items-center">
                                <div class="bg-white rounded-circle p-1 me-2 d-flex align-items-center justify-content-center" style="width:30px; height:30px;">
                                    <i class="ti ti-calculator fs-5" style="color: var(--theme-color-1) !important;"></i>
                                </div>
                                <span class="text-white fw-bold" style="letter-spacing: 1px; font-size: 0.85rem;">TOTAL RENCANA CICILAN</span>
                            </div>
                        </th>
                        <th class="text-end py-3 px-4 border-0">
                            <div class="d-inline-flex align-items-center bg-white rounded-pill px-3 py-1 shadow-sm">
                                <span id="labelTotalRencana" class="fw-bolder fs-5" style="color: var(--theme-color-1) !important;">{{ number_format($total, 0, ',', '.') }}</span>
                                <input type="hidden" id="totalRencanaValue" value="{{ $total }}">
                            </div>
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<div class="alert alert-info d-flex align-items-center mt-3" role="alert">
    <i class="ti ti-info-circle me-2 fs-4 text-info"></i>
    <div style="font-size: 0.85rem">
        Anda dapat mengedit nominal cicilan di atas. Pastikan total rencana (<strong>Rp {{ number_format($total, 0, ',', '.') }}</strong>) tetap sama dengan jumlah pinjaman yang Anda inputkan di form.
    </div>
</div>

<script>
    $(function() {
        $(".mny").maskMoney({
            thousands: '.', decimal: ',', precision: 0, allowZero: true
        });

        // Update total secara real-time saat nominal diedit
        $(".rencana_nominal").on('keyup change', function() {
            let total = 0;
            $(".rencana_nominal").each(function() {
                let val = $(this).val().replace(/\./g, '') || 0;
                total += parseInt(val);
            });
            $("#labelTotalRencana").text(total.toLocaleString('id-ID'));
            $("#totalRencanaValue").val(total);
        });
    });
</script>
