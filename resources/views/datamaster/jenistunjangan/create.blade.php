<form action="{{ route('jenistunjangan.store') }}" method="POST" id="formcreateJenisTunjangan">
    @csrf
    <div class="form-group mb-3">
        <label for="kode_jenis_tunjangan" class="form-label" style="font-weight: 600;">
            Kode Jenis Tunjangan <span class="text-danger">*</span>
        </label>
        <div class="input-group input-group-merge">
            <span class="input-group-text"><i class="ti ti-barcode"></i></span>
            <input type="text" class="form-control" id="kode_jenis_tunjangan" name="kode_jenis_tunjangan"
                placeholder="Contoh: TJ01 (Maksimal 4 karakter)" maxlength="4" autocomplete="off" />
        </div>
    </div>
    <div class="form-group mb-3">
        <label for="jenis_tunjangan" class="form-label" style="font-weight: 600;">
            Jenis Tunjangan <span class="text-danger">*</span>
        </label>
        <div class="input-group input-group-merge">
            <span class="input-group-text"><i class="ti ti-file-description"></i></span>
            <input type="text" class="form-control" id="jenis_tunjangan" name="jenis_tunjangan"
                placeholder="Contoh: Tunjangan Transport (Maksimal 50 karakter)" maxlength="50" autocomplete="off" />
        </div>
    </div>
    <div class="form-group mb-3">
        <button type="submit" class="btn btn-primary w-100" id="btnSimpan"><i class="ti ti-send me-1"></i> Submit</button>
    </div>
</form>


<script>
    $(document).ready(function() {
        // Auto uppercase untuk kode_jenis_tunjangan
        $('#kode_jenis_tunjangan').on('input', function() {
            $(this).val($(this).val().toUpperCase().replace(/[^A-Z0-9]/g, ''));
        });

        // Validasi panjang real-time untuk jenis_tunjangan
        $('#jenis_tunjangan').on('input', function() {
            const value = $(this).val();
            if (value.length > 50) {
                $(this).val(value.substring(0, 50));
            }
        });

        // Validasi form
        $("#formcreateJenisTunjangan").submit(function(e) {
            const form = $(this);
            const kode_jenis_tunjangan = form.find('input[name="kode_jenis_tunjangan"]').val().trim();
            const jenis_tunjangan = form.find('input[name="jenis_tunjangan"]').val().trim();

            if (!kode_jenis_tunjangan) {
                e.preventDefault();
                Swal.fire({
                    icon: "warning",
                    title: "Peringatan!",
                    text: 'Kode Jenis Tunjangan wajib diisi',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6',
                    didClose: () => {
                        form.find('input[name="kode_jenis_tunjangan"]').focus();
                    }
                });
                return false;
            }

            if (kode_jenis_tunjangan.length > 4) {
                e.preventDefault();
                Swal.fire({
                    icon: "warning",
                    title: "Peringatan!",
                    text: 'Kode Jenis Tunjangan maksimal 4 karakter',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6',
                    didClose: () => {
                        form.find('input[name="kode_jenis_tunjangan"]').focus();
                    }
                });
                return false;
            }

            if (!jenis_tunjangan) {
                e.preventDefault();
                Swal.fire({
                    icon: "warning",
                    title: "Peringatan!",
                    text: 'Jenis Tunjangan wajib diisi',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6',
                    didClose: () => {
                        form.find('input[name="jenis_tunjangan"]').focus();
                    }
                });
                return false;
            }

            if (jenis_tunjangan.length > 50) {
                e.preventDefault();
                Swal.fire({
                    icon: "warning",
                    title: "Peringatan!",
                    text: 'Jenis Tunjangan maksimal 50 karakter',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6',
                    didClose: () => {
                        form.find('input[name="jenis_tunjangan"]').focus();
                    }
                });
                return false;
            }

            $("#btnSimpan").prop("disabled", true);
            $("#btnSimpan").html("<i class='ti ti-loader me-1'></i> Menyimpan...");
        });
    });
</script>
