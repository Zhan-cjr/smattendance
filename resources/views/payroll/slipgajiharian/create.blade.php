<form action="{{ route('slipgajiharian.store') }}" method="POST" id="formSlipGajiHarian">
    @csrf
    <div class="row">
        <div class="col-12">
            <div class="form-group mb-3">
                <label class="form-label fw-bold">Tanggal Slip <span class="text-danger">*</span></label>
                <input type="text" name="tanggal_slip" id="tanggal_slip_create" class="form-control flatpickr-date-modal" placeholder="Pilih Tanggal Slip" required>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-6">
            <div class="form-group mb-3">
                <label class="form-label fw-bold">Dari <span class="text-danger">*</span></label>
                <input type="text" name="dari" id="dari_harian" class="form-control flatpickr-date-modal" placeholder="Dari Tanggal" required>
            </div>
        </div>
        <div class="col-6">
            <div class="form-group mb-3">
                <label class="form-label fw-bold">Sampai <span class="text-danger">*</span></label>
                <input type="text" name="sampai" id="sampai_harian" class="form-control flatpickr-date-modal" placeholder="Sampai Tanggal" required>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group mb-3">
                <label class="form-label fw-bold">Pilih Karyawan <span class="text-danger">*</span></label>
                <select name="nik[]" id="nik_harian_create" class="form-select" multiple="multiple" style="width: 100%;" data-placeholder="Pilih Karyawan">
                    <option value=""></option>
                    <!-- Loaded via AJAX -->
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group mb-3">
                <label class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                <select name="status" id="status_harian" class="form-select">
                    <option value="">Pilih Status</option>
                    <option value="1">Publish</option>
                    <option value="0">Pending</option>
                </select>
            </div>
        </div>
    </div>
    <div class="row mt-2">
        <div class="col-12">
            <div class="form-group mb-3">
                <button type="submit" name="submitButton" class="btn btn-primary w-100" id="submitButtonSlipGajiHarian">
                    <i class="ti ti-send me-1"></i> Buat Slip Gaji Harian
                </button>
            </div>
        </div>
    </div>
</form>
<script>
    $(function() {
        // Init flatpickr
        $(".flatpickr-date-modal").flatpickr({
            dateFormat: "Y-m-d"
        });

        // Init Select2 for multi-select inside modal
        $("#nik_harian_create").select2({
            placeholder: "Pilih Karyawan",
            allowClear: true,
            dropdownParent: $('#modalHarian')
        });

        // Load karyawan based on tanggal dari
        function loadKaryawanHarian() {
            var dari = $("#dari_harian").val();
            var tanggal = dari || null;
            $.ajax({
                type: "GET",
                url: "/karyawan/getkaryawan",
                data: {
                    jenis_upah: 'Harian',
                    tanggal: tanggal
                },
                cache: false,
                success: function(respond) {
                    var target = $("#nik_harian_create");
                    target.empty();
                    target.append('<option value=""></option>');
                    respond.forEach(function(item) {
                        target.append(`<option value="${item.nik}">${item.nik} - ${item.nama_karyawan}</option>`);
                    });
                }
            });
        }

        // Reload karyawan when date changes
        $("#dari_harian").on("change", function() {
            loadKaryawanHarian();
        });

        // Initial load
        loadKaryawanHarian();

        // Validation
        const form = $('#formSlipGajiHarian');
        form.submit(function(e) {
            let tanggal_slip = form.find('#tanggal_slip_create').val();
            let dari = form.find('#dari_harian').val();
            let sampai = form.find('#sampai_harian').val();
            let status = form.find('#status_harian').val();
            let nik = form.find('#nik_harian_create').val();
            if (tanggal_slip == "") {
                Swal.fire({
                    title: "Oops!",
                    text: 'Tanggal Slip Harus Diisi !',
                    icon: "warning",
                    showConfirmButton: true,
                });
                return false;
            } else if (dari == "") {
                Swal.fire({
                    title: "Oops!",
                    text: 'Tanggal Dari Harus Diisi !',
                    icon: "warning",
                    showConfirmButton: true,
                });
                return false;
            } else if (sampai == "") {
                Swal.fire({
                    title: "Oops!",
                    text: 'Tanggal Sampai Harus Diisi !',
                    icon: "warning",
                    showConfirmButton: true,
                });
                return false;
            } else if (!nik || nik.length == 0) {
                Swal.fire({
                    title: "Oops!",
                    text: 'Pilih minimal 1 karyawan !',
                    icon: "warning",
                    showConfirmButton: true,
                });
                return false;
            } else if (status == "") {
                Swal.fire({
                    title: "Oops!",
                    text: 'Status Harus Diisi !',
                    icon: "warning",
                    showConfirmButton: true,
                });
                return false;
            }
        });
    });
</script>
