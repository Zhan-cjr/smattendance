<form action="{{ route('statuskaryawan.update', Crypt::encrypt($statuskaryawan->kode_status_karyawan)) }}" method="POST" id="formStatusKaryawan">
    @csrf
    @method('PUT')
    <div class="row">
        <div class="col-12">
            <x-input-with-icon label="Kode Status Karyawan" name="kode_status_karyawan" icon="ti ti-barcode" maxlength="5" value="{{ $statuskaryawan->kode_status_karyawan }}" />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-input-with-icon label="Nama Status Karyawan" name="nama_status_karyawan" icon="ti ti-id-badge" value="{{ $statuskaryawan->nama_status_karyawan }}" />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <button class="btn btn-primary w-100" type="submit">
                <i class="ti ti-rotate me-1"></i> Update
            </button>
        </div>
    </div>
</form>

<script>
    $(function() {
        $("#formStatusKaryawan").submit(function() {
            const kode_status_karyawan = $("#kode_status_karyawan").val();
            const nama_status_karyawan = $("#nama_status_karyawan").val();
            if (kode_status_karyawan == "") {
                Swal.fire({
                    title: 'Oops!',
                    text: 'Kode Status Karyawan Harus Diisi!',
                    icon: 'warning',
                    showConfirmButton: true
                });
                return false;
            } else if (nama_status_karyawan == "") {
                Swal.fire({
                    title: 'Oops!',
                    text: 'Nama Status Karyawan Harus Diisi!',
                    icon: 'warning',
                    showConfirmButton: true
                });
                return false;
            }
        });
    });
</script>
