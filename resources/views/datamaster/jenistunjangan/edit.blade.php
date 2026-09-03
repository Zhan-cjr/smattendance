<form action="{{ route('jenistunjangan.update', Crypt::encrypt($jenistunjangan->kode_jenis_tunjangan)) }}" method="POST" id="formcreateJenisTunjangan">
    @csrf
    @method('PUT')
    <div class="form-group mb-3">
        <label for="kode_jenis_tunjangan" class="form-label" style="font-weight: 600;">
            Kode Jenis Tunjangan
        </label>
        <div class="input-group input-group-merge">
            <span class="input-group-text"><i class="ti ti-barcode"></i></span>
            <input type="text" 
                class="form-control" 
                id="kode_jenis_tunjangan" 
                name="kode_jenis_tunjangan"
                value="{{ $jenistunjangan->kode_jenis_tunjangan }}" 
                readonly 
                autocomplete="off" />
        </div>
    </div>
    <div class="form-group mb-3">
        <label for="jenis_tunjangan" class="form-label" style="font-weight: 600;">
            Jenis Tunjangan <span class="text-danger">*</span>
        </label>
        <div class="input-group input-group-merge">
            <span class="input-group-text"><i class="ti ti-file-description"></i></span>
            <input type="text" 
                class="form-control" 
                id="jenis_tunjangan" 
                name="jenis_tunjangan" 
                value="{{ $jenistunjangan->jenis_tunjangan }}" 
                placeholder="Contoh: Tunjangan Transport (Maksimal 50 karakter)" 
                maxlength="50" 
                required 
                autocomplete="off" />
        </div>
    </div>
    <div class="form-group mb-3">
        <button type="submit" class="btn btn-primary w-100" id="btnSimpan"><i class="ti ti-send me-1"></i> Submit</button>
    </div>
</form>


<script>
    $(document).ready(function() {
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
            const jenis_tunjangan = form.find('input[name="jenis_tunjangan"]').val().trim();
            
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
