<form action="{{ route('pinjaman.storepembayaran', Crypt::encrypt($pinjaman->id)) }}" method="POST" id="formPembayaran">
    @csrf
    <div class="row">
        <div class="col-12 text-start">
            <!-- Info Balance -->
            <div class="card border-0 mb-4 shadow-sm" style="background-color: var(--theme-color-1) !important; border-radius: 12px;">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-white text-uppercase d-block mb-1 fw-bold" style="letter-spacing: 0.5px; opacity: 0.85;">SISA PINJAMAN</small>
                            <h4 class="mb-0 fw-bold text-white" id="sisaPinjamanLabel">Rp {{ number_format($pinjaman->sisa_pinjaman, 0, ',', '.') }}</h4>
                            <input type="hidden" id="sisa_asli" value="{{ $pinjaman->sisa_pinjaman }}">
                        </div>
                        <div class="text-end text-white">
                            <h6 class="mb-0 fw-bold text-white">{{ $pinjaman->karyawan->nama_karyawan }}</h6>
                            <small class="text-white" style="opacity: 0.85;">{{ $pinjaman->nik }}</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12 mb-3">
                    <div class="form-group">
                        <label class="form-label fw-bold">Tanggal Bayar <span class="text-danger">*</span></label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="ti ti-calendar"></i></span>
                            <input type="text" name="tanggal_bayar" id="tanggal_bayar" class="form-control flatpickr" value="{{ date('Y-m-d') }}" placeholder="Pilih Tanggal">
                        </div>
                    </div>
                </div>
                <div class="col-md-12 mb-3">
                    <div class="form-group">
                        <label class="form-label fw-bold">Jumlah Bayar <span class="text-danger">*</span></label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="ti ti-moneybag"></i></span>
                            <input type="text" name="jumlah_bayar" id="jumlah_bayar" class="form-control money text-end" placeholder="0">
                        </div>
                    </div>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label fw-bold">Jenis Pembayaran <span class="text-danger">*</span></label>
                    <div class="d-flex gap-2 mt-1">
                        <div class="form-check custom-option-item {{ $pinjaman->sisa_pinjaman > 0 ? '' : 'disabled' }}" style="flex: 1">
                            <input class="form-check-input d-none" type="radio" name="jenis_pembayaran" value="M" id="jenisManual" checked>
                            <label class="form-check-label custom-option-label p-2 text-center border rounded w-100" for="jenisManual">
                                <i class="ti ti-cash fs-4 d-block mb-1"></i>
                                <span class="fw-bold d-block small">Manual</span>
                            </label>
                        </div>
                        <div class="form-check custom-option-item" style="flex: 1">
                            <input class="form-check-input d-none" type="radio" name="jenis_pembayaran" value="P" id="jenisPelunasan">
                            <label class="form-check-label custom-option-label p-2 text-center border rounded w-100" for="jenisPelunasan">
                                <i class="ti ti-discount-check fs-4 d-block mb-1"></i>
                                <span class="fw-bold d-block small">Pelunasan</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label fw-bold">Keterangan</label>
                    <textarea name="keterangan" class="form-control" rows="2" placeholder="Contoh: Pembayaran melalui kasir / potong bonus / dll"></textarea>
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn text-white" id="btnSimpan" style="background-color: var(--theme-color-1) !important; border-color: var(--theme-color-1) !important;">
                    <i class="ti ti-device-floppy me-1"></i> Simpan Pembayaran
                </button>
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
            </div>
        </div>
    </div>
</form>

<script>
    $(function() {
        $(".money").maskMoney({
            prefix: 'Rp ',
            thousands: '.',
            decimal: ',',
            precision: 0
        });

        $(".flatpickr").flatpickr({
            altInput: true,
            altFormat: "d-m-Y",
            dateFormat: "Y-m-d",
            allowInput: true
        });

        function toNumber(rupiah) {
            if (!rupiah) return 0;
            let cleanString = rupiah.replace(/[^0-9]/g, "");
            return parseInt(cleanString) || 0;
        }

        function formatRupiah(angka) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(angka).replace(/\u00A0/g, " ");
        }

        // Handle Radio Change
        $('input[name="jenis_pembayaran"]').change(function() {
            if (this.value === 'P') {
                let sisa = Math.round($("#sisa_asli").val());
                if ($("#jumlah_bayar").length > 0) {
                   $("#jumlah_bayar").val(formatRupiah(sisa)).trigger('mask.maskMoney');
                   $("#jumlah_bayar").prop('readonly', true).addClass('bg-light');
                }
            } else {
                if ($("#jumlah_bayar").length > 0) {
                    $("#jumlah_bayar").val('');
                    $("#jumlah_bayar").prop('readonly', false).removeClass('bg-light').focus();
                }
            }
        });

        $("#formPembayaran").submit(function(e) {
            let jumlahInput = $("#jumlah_bayar").val();
            let jumlah = toNumber(jumlahInput);
            let sisaInput = $("#sisa_asli").val();
            let sisa = Math.round(parseFloat(sisaInput)) || 0;

            if (jumlah <= 0) {
                Swal.fire('Error', 'Jumlah bayar tidak boleh kosong', 'error');
                return false;
            }

            if (jumlah > sisa) {
                Swal.fire('Error', 'Jumlah bayar (' + formatRupiah(jumlah) + ') melebihi sisa pinjaman (' + formatRupiah(sisa) + ')', 'error');
                return false;
            }

            $("#btnSimpan").prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Menyimpan...');
        });
    });
</script>

<style>
    .custom-option-item input:checked + .custom-option-label {
        border-color: var(--theme-color-1) !important;
        background-color: var(--theme-color-1-light, rgba(0, 0, 0, 0.05)) !important;
        color: var(--theme-color-1) !important;
    }
    .custom-option-label {
        cursor: pointer;
        transition: all 0.2s;
    }
    .custom-option-label:hover {
        background-color: #f8f9fa;
    }
</style>
