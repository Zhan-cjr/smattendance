<form action="{{ route('pinjaman.store') }}" method="POST" id="formPinjaman">
    @csrf
    <div class="row">
        <div class="col-12">
            <!-- Hidden NIK -->
            <input type="hidden" name="nik" id="nik">
            
            <!-- Employee Selection (Lookup) -->
            <div class="form-group mb-3 text-start">
                <label class="form-label fw-bold">Karyawan <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="text" class="form-control" id="nama_karyawan" placeholder="Pilih Karyawan" readonly>
                    <button class="btn btn-primary" type="button" id="btnLookupKaryawan">
                        <i class="ti ti-search me-1"></i> Cari
                    </button>
                </div>
            </div>

            <!-- Tanggal Pinjaman (FlatPickr) -->
            <div class="form-group mb-3 text-start">
                <label class="form-label fw-bold">Tanggal Pinjaman <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text"><i class="ti ti-calendar"></i></span>
                    <input type="text" name="tanggal_pinjaman" id="tanggal_pinjaman" class="form-control flatpickr" placeholder="yyyy-mm-dd">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <!-- Jumlah Pinjaman -->
                    <div class="form-group mb-3 text-start">
                        <label class="form-label fw-bold">Jumlah Pinjaman <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" name="jumlah_pinjaman" id="jumlah_pinjaman" class="form-control money text-end fw-bold">
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <!-- Tenor (Bulan) -->
                    <div class="form-group mb-3 text-start">
                        <label class="form-label fw-bold">Tenor <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="jumlah_cicilan" id="jumlah_cicilan" class="form-control" placeholder="Contoh: 10">
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
                               <option {{ date('m') == $m ? 'selected' : '' }} value="{{ $m }}">{{ getNamabulan($m) }}</option>
                           @endfor
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-3 text-start">
                        <label class="form-label fw-bold">Mulai Cicilan (Tahun) <span class="text-danger">*</span></label>
                        <select name="tahun_mulai_cicilan" id="tahun_mulai_cicilan" class="form-select">
                            @for($t=date('Y'); $t<=date('Y')+1; $t++)
                                <option value="{{ $t }}">{{ $t }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
            </div>

            <!-- Keterangan -->
            <div class="form-group mb-4 text-start">
                <label class="form-label fw-bold">Keterangan</label>
                <textarea name="keterangan" id="keterangan" class="form-control" rows="2" placeholder="Tujuan pinjaman..."></textarea>
            </div>

            <div class="d-grid gap-2">
                <button type="button" id="btnPreview" class="btn btn-info">
                    <i class="ti ti-layout-grid me-1"></i> Generate Preview Rencana Cicilan
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="ti ti-device-floppy me-1"></i> Simpan Pinjaman
                </button>
            </div>
            <div id="loadPreview" class="mt-4">
                <!-- Area untuk menampilkan tabel cicilan via AJAX -->
            </div>
        </div>
    </div>
</form>

<script>
    $(function() {
        // Initialize MaskMoney
        $(".money").maskMoney({
            thousands: '.', decimal: ',', precision: 0, allowZero: true
        });

        // Initialize FlatPickr with yyyy-mm-dd format
        $(".flatpickr").flatpickr({
            altInput: true,
            altFormat: "Y-m-d", 
            dateFormat: "Y-m-d",
            allowInput: true
        });

        // Open Lookup Karyawan
        $("#btnLookupKaryawan").click(function() {
            $("#modalLookupKaryawan").modal("show");
            $("#loadLookupKaryawan").load("{{ route('karyawan.getkaryawantable') }}");
        });

        // Event for selecting employee
        $(document).off('click', '.btnPilihKaryawan').on('click', '.btnPilihKaryawan', function(e) {
            e.preventDefault();
            let nik = $(this).attr('data-nik');
            let nama = $(this).attr('data-nama');
            
            if (nik && nama) {
                $("#formPinjaman #nik").val(nik);
                $("#formPinjaman #nama_karyawan").val(nama);
                $("#modalLookupKaryawan").modal("hide");
            } else {
                console.error("Data Karyawan tidak ditemukan pada tombol pilihan");
                Swal.fire({ title: 'Error', text: 'Data Karyawan tidak valid!', icon: 'error' });
            }
        });

        // Preview Cicilan Logic
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
                    tanggal: tanggal,
                    bulan_mulai_cicilan: $("#bulan_mulai_cicilan").val(),
                    tahun_mulai_cicilan: $("#tahun_mulai_cicilan").val()
                },
                cache: false,
                success: function(respond) {
                    $("#loadPreview").html(respond);
                }
            });
        });

        // Form Submit Validation
        $("#formPinjaman").submit(function(e) {
            let nik = $("#nik").val();
            let jumlah_pinjaman = parseInt($("#jumlah_pinjaman").val().replace(/\./g, '')) || 0;
            let total_rencana = parseInt($("#totalRencanaValue").val()) || 0;

            if ($(".rencana_nominal").length == 0) {
                e.preventDefault();
                Swal.fire({ title: 'Perhatian', text: 'Silakan klik "Generate Preview" terlebih dahulu', icon: 'warning' });
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
        });
    });
</script>