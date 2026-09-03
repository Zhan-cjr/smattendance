<form action="{{ route('pinjaman.update', Crypt::encrypt($pinjaman->id)) }}" method="POST" id="formPinjaman">
    @csrf
    @method('PUT')
    <div class="row">
        <div class="col-12">
            <!-- Hidden NIK -->
            <input type="hidden" name="nik" id="nik" value="{{ $pinjaman->nik }}">
            
            <!-- Employee Information (Read Only) -->
            <div class="form-group mb-3 text-start">
                <label class="form-label fw-bold">Karyawan</label>
                <div class="input-group">
                    <input type="text" class="form-control bg-light" id="nama_karyawan" value="{{ $pinjaman->nik }} - {{ $pinjaman->karyawan->nama_karyawan }}" readonly disabled>
                    <button class="btn btn-primary" type="button" disabled>
                        <i class="ti ti-search me-1"></i> Cari
                    </button>
                </div>
            </div>

            <!-- Tanggal Pinjaman (FlatPickr) -->
            <div class="form-group mb-3 text-start">
                <label class="form-label fw-bold">Tanggal Pinjaman <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text"><i class="ti ti-calendar"></i></span>
                    <input type="text" name="tanggal_pinjaman" id="tanggal_pinjaman" class="form-control flatpickr" placeholder="yyyy-mm-dd" value="{{ $pinjaman->tanggal_pinjaman }}">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <!-- Jumlah Pinjaman -->
                    <div class="form-group mb-3 text-start">
                        <label class="form-label fw-bold">Jumlah Pinjaman <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" name="jumlah_pinjaman" id="jumlah_pinjaman" class="form-control money text-end fw-bold" value="{{ number_format($pinjaman->jumlah_pinjaman, 0, ',', '.') }}">
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <!-- Tenor (Bulan) -->
                    <div class="form-group mb-3 text-start">
                        <label class="form-label fw-bold">Tenor <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="jumlah_cicilan" id="jumlah_cicilan" class="form-control" placeholder="Contoh: 10" value="{{ $pinjaman->jumlah_cicilan }}">
                            <span class="input-group-text">Bulan</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3 text-start">
                        <label class="form-label fw-bold">Mulai Cicilan (Bulan) <span class="text-danger">*</span></label>
                        <select name="bulan_mulai_cicilan" id="bulan_mulai_cicilan" class="form-select">
                           @for($m=1; $m<=12; $m++)
                               <option value="{{ $m }}" {{ $pinjaman->bulan_mulai_cicilan == $m ? 'selected' : '' }}>{{ getNamabulan($m) }}</option>
                           @endfor
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-3 text-start">
                        <label class="form-label fw-bold">Mulai Cicilan (Tahun) <span class="text-danger">*</span></label>
                        <select name="tahun_mulai_cicilan" id="tahun_mulai_cicilan" class="form-select">
                            @for($t=date('Y')-1; $t<=date('Y')+3; $t++)
                                <option value="{{ $t }}" {{ $pinjaman->tahun_mulai_cicilan == $t ? 'selected' : '' }}>{{ $t }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
            </div>

            <!-- Keterangan -->
            <div class="form-group mb-4 text-start">
                <label class="form-label fw-bold">Keterangan</label>
                <textarea name="keterangan" id="keterangan" class="form-control" rows="2" placeholder="Tujuan pinjaman...">{{ $pinjaman->keterangan }}</textarea>
            </div>

            <div class="d-grid gap-2">
                <button type="button" id="btnPreview" class="btn btn-info">
                    <i class="ti ti-layout-grid me-1"></i> Sesuaikan Ulang Rencana Cicilan
                </button>
                <button type="submit" class="btn btn-primary" id="btnSimpan">
                    <i class="ti ti-device-floppy me-1"></i> Update Pinjaman
                </button>
            </div>
        </div>
    </div>
</form>

<div id="loadPreview" class="mt-4">
    <!-- Initial Load of Current Installments -->
    <div class="card shadow-sm" style="border-color: var(--theme-color-1) !important;">
        <div class="card-header py-2" style="background-color: var(--theme-color-1) !important; color: white !important;">
            <h6 class="mb-0 text-white"><i class="ti ti-list-numbers me-1"></i> Rencana Cicilan Pinjaman (Saat Ini)</h6>
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
                        @foreach($pinjaman->rencana_cicilan as $r)
                        @php $total += $r->jumlah_cicilan; @endphp
                        <tr>
                            <td class="text-center fw-bold py-2">{{ $r->cicilan_ke }}</td>
                            <td class="text-start px-3 py-2">
                                {{ getNamabulan($r->bulan) }} {{ $r->tahun }}
                                <input type="hidden" name="rencana_bulan[]" value="{{ $r->bulan }}">
                                <input type="hidden" name="rencana_tahun[]" value="{{ $r->tahun }}">
                            </td>
                            <td class="text-end px-3 py-2">
                                <input type="text" name="rencana_nominal[]" class="form-control form-control-sm text-end fw-bold rencana_nominal mny" 
                                    value="{{ number_format($r->jumlah_cicilan, 0, ',', '.') }}">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-dark">
                        <tr>
                            <th colspan="2" class="text-center py-2">TOTAL RENCANA</th>
                            <th class="text-end px-3 py-2">
                                <span id="labelTotalRencana">Rp {{ number_format($total, 0, ',', '.') }}</span>
                                <input type="hidden" id="totalRencanaValue" value="{{ $total }}">
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        // Initialize MaskMoney
        $(".money, .mny").maskMoney({
            thousands: '.', decimal: ',', precision: 0, allowZero: true
        });

        // Initialize FlatPickr
        $(".flatpickr").flatpickr({
            altInput: true,
            altFormat: "Y-m-d", 
            dateFormat: "Y-m-d",
            allowInput: true
        });

        // Update total secara real-time saat nominal diedit
        $(document).on('keyup change', '.rencana_nominal', function() {
            let total = 0;
            $(".rencana_nominal").each(function() {
                let val = $(this).val().replace(/\./g, '') || 0;
                total += parseInt(val);
            });
            $("#labelTotalRencana").text("Rp " + total.toLocaleString('id-ID'));
            $("#totalRencanaValue").val(total);
        });

        // Preview Cicilan Logic (Use AJAX like Create Page)
        $("#btnPreview").click(function() {
            let jumlah = $("#jumlah_pinjaman").val().replace(/\./g, '');
            let tenor = $("#jumlah_cicilan").val();
            let tanggal = $("#tanggal_pinjaman").val();
            let nik = $("#nik").val();

            if (nik == "" || tanggal == "" || jumlah == "" || jumlah == 0 || tenor == "" || tenor == 0) {
                Swal.fire({ title: 'Oops!', text: 'Silakan lengkapi data pinjaman terlebih dahulu', icon: 'warning' });
                return false;
            }

            $.ajax({
                type: 'POST',
                url: '{{ route("pinjaman.preview") }}',
                data: {
                    _token: "{{ csrf_token() }}",
                    jumlah_pinjaman: jumlah,
                    tenor: tenor,
                    tanggal: tanggal
                },
                cache: false,
                success: function(respond) {
                    $("#loadPreview").html(respond);
                    // Re-initialize MaskMoney on new content
                    $(".mny").maskMoney({
                        thousands: '.', decimal: ',', precision: 0, allowZero: true
                    });
                }
            });
        });

        // Form Submit Validation
        $("#formPinjaman").submit(function(e) {
            let jumlah_pinjaman = parseInt($("#jumlah_pinjaman").val().replace(/\./g, '')) || 0;
            let total_rencana = parseInt($("#totalRencanaValue").val()) || 0;

            if ($(".rencana_nominal").length == 0) {
                e.preventDefault();
                Swal.fire({ title: 'Perhatian', text: 'Silakan klik "Sesuaikan Ulang" terlebih dahulu', icon: 'warning' });
                return false;
            }

            if (jumlah_pinjaman !== total_rencana) {
                e.preventDefault();
                Swal.fire({
                    title: 'Total Tidak Sesuai!',
                    text: 'Total rencana cicilan (' + total_rencana.toLocaleString('id-ID') + ') harus sama dengan jumlah pinjaman (' + jumlah_pinjaman.toLocaleString('id-ID') + ')',
                    icon: 'error'
                });
                return false;
            }

            $("#btnSimpan").prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Menyimpan...');
        });
    });
</script>
