<form action="{{ route('kpi.periods.store') }}" id="formcreatePeriode" method="POST">
    @csrf
    <x-input-with-icon-label icon="ti ti-file-text" label="Nama Periode" name="nama_periode" placeholder="Contoh: Januari 2024"  required="true"/>
    
    <div class="row">
        <div class="col-6">
            <x-input-with-icon-label icon="ti ti-calendar" label="Tanggal Mulai" datepicker="flatpickr-date" name="start_date" required="true"/>
        </div>
        <div class="col-6">
             <x-input-with-icon-label icon="ti ti-calendar" label="Tanggal Selesai" datepicker="flatpickr-date" name="end_date" required="true"/>
        </div>
    </div>

    <div class="form-group mb-3">
        <label style="font-weight: 600" class="form-label">Status</label>
        <div class="form-check form-switch">
             <input class="form-check-input" type="checkbox" name="is_active" checked>
             <span class="form-check-label">Set Aktif</span>
        </div>
    </div>

    <div class="form-group">
        <button class="btn btn-primary w-100" type="submit">
            <i class="ti ti-send me-1"></i>
            Simpan
        </button>
    </div>
</form>

<script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
<script>
    $(function() {
        $(".flatpickr-date").flatpickr();
        
        $("#formcreatePeriode").submit(function(){
            var nama_periode = $(this).find("input[name='nama_periode']").val();
            var start_date = $(this).find("input[name='start_date']").val();
            var end_date = $(this).find("input[name='end_date']").val();

            if(nama_periode == ""){
                Swal.fire({
                    title: 'Warning!',
                    text: 'Nama Periode Harus Diisi !',
                    icon: 'warning',
                    confirmButtonText: 'Ok',
                    didClose: () => {
                        $(this).find("input[name='nama_periode']").focus();
                    }
                });
                return false;
            }
             if(start_date == ""){
                Swal.fire({
                    title: 'Warning!',
                    text: 'Tanggal Mulai Harus Diisi !',
                    icon: 'warning',
                    confirmButtonText: 'Ok',
                    didClose: () => {
                        $(this).find("input[name='start_date']").focus();
                    }
                });
                return false;
            }
             if(end_date == ""){
                Swal.fire({
                    title: 'Warning!',
                    text: 'Tanggal Selesai Harus Diisi !',
                    icon: 'warning',
                    confirmButtonText: 'Ok',
                    didClose: () => {
                        $(this).find("input[name='end_date']").focus();
                    }
                });
                return false;
            }
        });
    });
</script>
