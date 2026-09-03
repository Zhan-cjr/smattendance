<form action="{{ route('slipgajiharian.update', Crypt::encrypt($slipgaji->kode_slip_gaji_harian)) }}" method="POST" id="formSlipGajiHarianEdit">
    @csrf
    @method('PUT')
    <div class="row">
        <div class="col-12">
            <div class="form-group mb-3">
                <label class="form-label fw-bold">Tanggal Slip <span class="text-danger">*</span></label>
                <input type="text" name="tanggal_slip" id="tanggal_slip_edit" class="form-control flatpickr-date-modal" placeholder="Pilih Tanggal Slip" value="{{ $slipgaji->tanggal_slip }}" required>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-6">
            <div class="form-group mb-3">
                <label class="form-label fw-bold">Dari <span class="text-danger">*</span></label>
                <input type="text" name="dari" id="dari_harian_edit" class="form-control flatpickr-date-modal" placeholder="Dari Tanggal" value="{{ $slipgaji->dari }}" required>
            </div>
        </div>
        <div class="col-6">
            <div class="form-group mb-3">
                <label class="form-label fw-bold">Sampai <span class="text-danger">*</span></label>
                <input type="text" name="sampai" id="sampai_harian_edit" class="form-control flatpickr-date-modal" placeholder="Sampai Tanggal" value="{{ $slipgaji->sampai }}" required>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group mb-3">
                <label class="form-label fw-bold">Pilih Karyawan <span class="text-danger">*</span></label>
                <select name="nik[]" id="nik_harian_edit" class="form-select" multiple="multiple" style="width: 100%;" data-placeholder="Pilih Karyawan">
                    <option value=""></option>
                    <!-- Loaded via AJAX, pre-selected from DB -->
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group mb-3">
                <label class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                <select name="status" id="status_harian_edit" class="form-select">
                    <option value="">Pilih Status</option>
                    <option value="1" {{ $slipgaji->status == 1 ? 'selected' : '' }}>Publish</option>
                    <option value="0" {{ $slipgaji->status == 0 ? 'selected' : '' }}>Pending</option>
                </select>
            </div>
        </div>
    </div>
    <div class="row mt-2">
        <div class="col-12">
            <div class="form-group mb-3">
                <button type="submit" name="submitButton" class="btn btn-primary w-100" id="btnSimpanHarian">
                    <i class="ti ti-send me-1"></i> Update
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

        // Data karyawan yang sudah dipilih sebelumnya
        var selectedNik = @json($selected_nik);

        // Init Select2 for multi-select inside modal
        $("#nik_harian_edit").select2({
            placeholder: "Pilih Karyawan",
            allowClear: true,
            dropdownParent: $('#modalHarian')
        });

        // Load karyawan based on tanggal dari
        function loadKaryawanHarianEdit() {
            var dari = $("#dari_harian_edit").val();
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
                    var target = $("#nik_harian_edit");
                    target.empty();
                    target.append('<option value=""></option>');
                    respond.forEach(function(item) {
                        var isSelected = selectedNik.some(s => s.nik === item.nik);
                        target.append(`<option value="${item.nik}" ${isSelected ? 'selected' : ''}>${item.nik} - ${item.nama_karyawan}</option>`);
                    });
                    target.trigger('change');
                }
            });
        }

        // Reload karyawan when date changes
        $("#dari_harian_edit").on("change", function() {
            loadKaryawanHarianEdit();
        });

        // Initial load
        loadKaryawanHarianEdit();

        // Validation
        const form = $('#formSlipGajiHarianEdit');

        function buttonDisable() {
            $("#btnSimpanHarian").prop('disabled', true);
            $("#btnSimpanHarian").html(`
            <div class="spinner-border spinner-border-sm text-white me-2" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            Loading..
         `);
        }

        form.submit(function(e) {
            let tanggal_slip = form.find('#tanggal_slip_edit').val();
            let dari = form.find('#dari_harian_edit').val();
            let sampai = form.find('#sampai_harian_edit').val();
            let status = form.find('#status_harian_edit').val();
            let nik = form.find('#nik_harian_edit').val();

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
            buttonDisable();
        });
    });
</script>
