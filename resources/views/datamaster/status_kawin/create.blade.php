<form action="{{ route('statuskawin.store') }}" method="POST" id="formStatusKawin">
    @csrf
    <div class="row">
        <div class="col-12">
            <x-input-with-icon label="Kode Status Kawin" name="kode_status_kawin" icon="ti ti-barcode" maxlength="5" />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-input-with-icon label="Status Kawin" name="status_kawin" icon="ti ti-heart" />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <button class="btn btn-primary w-100" type="submit">
                <i class="ti ti-send me-1"></i> Simpan
            </button>
        </div>
    </div>
</form>

<script>
    $(function() {
        $("#formStatusKawin").submit(function() {
            const kode_status_kawin = $("#kode_status_kawin").val();
            const status_kawin = $("#status_kawin").val();
            if (kode_status_kawin == "") {
                Swal.fire({
                    title: 'Oops!',
                    text: 'Kode Status Kawin Harus Diisi!',
                    icon: 'warning',
                    showConfirmButton: true
                });
                return false;
            } else if (status_kawin == "") {
                Swal.fire({
                    title: 'Oops!',
                    text: 'Status Kawin Harus Diisi!',
                    icon: 'warning',
                    showConfirmButton: true
                });
                return false;
            }
        });
    });
</script>
