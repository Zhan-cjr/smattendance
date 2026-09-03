<form action="{{ route('jabatan.update', ['kode_jabatan' => Crypt::encrypt($jabatan->kode_jabatan)]) }}" method="POST" id="formJabatan">
    @csrf
    @method('PUT')
    <x-input-with-icon label="Kode Jabatan" name="kode_jabatan" icon="ti ti-barcode" :value="$jabatan->kode_jabatan" maxlength="3" placeholder="Contoh: J01 (Maksimal 3 karakter)" />
    <x-input-with-icon label="Nama Jabatan" name="nama_jabatan" icon="ti ti-building" :value="$jabatan->nama_jabatan" maxlength="30" placeholder="Contoh: Manager (Maksimal 30 karakter)" required />
    <div class="form-group mb-3">
        <button type="submit" class="btn btn-primary w-100" id="btnSimpan"><i class="ti ti-send me-1"></i> Simpan Perubahan</button>
    </div>
</form>
<script>
    $(document).ready(function() {
        // Auto uppercase untuk kode_jabatan
        $('#kode_jabatan').on('input', function() {
            $(this).val($(this).val().toUpperCase().replace(/[^A-Z0-9]/g, ''));
        });

        // Validasi panjang real-time untuk nama_jabatan
        $('#nama_jabatan').on('input', function() {
            const value = $(this).val();
            if (value.length > 30) {
                $(this).val(value.substring(0, 30));
            }
        });

        // Validasi form
        $("#formJabatan").submit(function(e) {
            let kode_jabatan = $(this).find("#kode_jabatan").val().trim();
            let nama_jabatan = $(this).find("#nama_jabatan").val().trim();
            
            if (!kode_jabatan) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'Kode Jabatan harus diisi!',
                    didClose: () => {
                        $(this).find("#kode_jabatan").focus();
                    }
                });
                return false;
            }
            
            if (kode_jabatan.length > 3) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'Kode Jabatan maksimal 3 karakter!',
                    didClose: () => {
                        $(this).find("#kode_jabatan").focus();
                    }
                });
                return false;
            }
            
            if (!nama_jabatan) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'Nama Jabatan harus diisi!',
                    didClose: () => {
                        $(this).find("#nama_jabatan").focus();
                    }
                });
                return false;
            }
            
            if (nama_jabatan.length > 30) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'Nama Jabatan maksimal 30 karakter!',
                    didClose: () => {
                        $(this).find("#nama_jabatan").focus();
                    }
                });
                return false;
            }
            
                $("#btnSimpan").attr('disabled', true);
                $("#btnSimpan").html('<i class="ti ti-spinner me-1"></i> Menyimpan...');
        });
    });
</script>
