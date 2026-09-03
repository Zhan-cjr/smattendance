<form action="{{ route('gajipokok.store') }}" id="formcreateGajiPokok" method="POST">
    @csrf
    <div class="form-group mb-3">
        <label for="nik" class="form-label" style="font-weight: 600;">
            Karyawan <span class="text-danger">*</span>
        </label>
        <select name="nik" id="nik" class="form-select select2Nik" required>
            <option value="">Pilih Karyawan</option>
            @foreach ($karyawan as $d)
                <option value="{{ $d->nik }}">{{ $d->nik_show ?? $d->nik }} - {{ $d->nama_karyawan }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group mb-3">
        <label for="jumlah" class="form-label" style="font-weight: 600;">
            Gaji Pokok <span class="text-danger">*</span>
        </label>
        <div class="input-group input-group-merge">
            <span class="input-group-text"><i class="ti ti-moneybag"></i></span>
            <input type="text" 
                class="form-control money" 
                id="jumlah" 
                name="jumlah" 
                placeholder="Contoh: 5000000 (Minimal 1, Maksimal 999.999.999)" 
                style="text-align: right;" 
                autocomplete="off" />
        </div>
    </div>
    <div class="form-group mb-3">
        <label for="jenis_upah" class="form-label" style="font-weight: 600;">
            Jenis Upah <span class="text-danger">*</span>
        </label>
        <select name="jenis_upah" id="jenis_upah" class="form-select" required>
            <option value="Bulanan">Bulanan</option>
            <option value="Harian">Harian</option>
        </select>
    </div>
    <div class="form-group mb-3">
        <label for="tanggal_berlaku" class="form-label" style="font-weight: 600;">
            Tanggal Berlaku <span class="text-danger">*</span>
        </label>
        <div class="input-group input-group-merge">
            <span class="input-group-text"><i class="ti ti-calendar"></i></span>
            <input type="text" 
                class="form-control flatpickr-date" 
                id="tanggal_berlaku" 
                name="tanggal_berlaku" 
                placeholder="Pilih tanggal berlaku" 
                autocomplete="off" />
        </div>
    </div>
    <div class="form-group">
        <button class="btn btn-primary w-100" id="btnSimpan" type="submit">
            <i class="ti ti-send me-1"></i>
            Submit
        </button>
    </div>
</form>


<script>
    $(document).ready(function() {
        $(".select2Nik").each(function() {
            var $this = $(this);
            $this.wrap('<div class="position-relative"></div>').select2({
                placeholder: 'Pilih Karyawan',
                allowClear: true,
                dropdownParent: $this.parent()
            });
        });
        $(".money").maskMoney();
        $(".flatpickr-date").flatpickr({
            dateFormat: 'Y-m-d'
        });

        // Validasi form
        $("#formcreateGajiPokok").submit(function(e) {
            const form = $(this);
            const nik = form.find("select[name=nik]").val();
            const jumlah = form.find("input[name=jumlah]").val();
            const jenis_upah = form.find("select[name=jenis_upah]").val();
            const tanggal_berlaku = form.find("input[name=tanggal_berlaku]").val();
            
            if (!nik || nik == "") {
                e.preventDefault();
                Swal.fire({
                    icon: "warning",
                    title: "Peringatan!",
                    text: 'Karyawan wajib dipilih',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6',
                    didClose: () => {
                        $("#nik").focus();
                    }
                });
                return false;
            }
            
            if (!jumlah || jumlah == "" || jumlah == "0") {
                e.preventDefault();
                Swal.fire({
                    icon: "warning",
                    title: "Peringatan!",
                    text: 'Gaji Pokok wajib diisi dan minimal 1',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6',
                    didClose: () => {
                        $("#jumlah").focus();
                    }
                });
                return false;
            }
            
            if (!jenis_upah || jenis_upah == "") {
                e.preventDefault();
                Swal.fire({
                    icon: "warning",
                    title: "Peringatan!",
                    text: 'Jenis Upah wajib dipilih',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6',
                    didClose: () => {
                        $("#jenis_upah").focus();
                    }
                });
                return false;
            }

            if (!tanggal_berlaku || tanggal_berlaku == "") {
                e.preventDefault();
                Swal.fire({
                    icon: "warning",
                    title: "Peringatan!",
                    text: 'Tanggal Berlaku wajib diisi',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6',
                    didClose: () => {
                        $("#tanggal_berlaku").focus();
                    }
                });
                return false;
            }
            
            $("#btnSimpan").prop('disabled', true);
            $("#btnSimpan").html("<i class='ti ti-loader me-1'></i> Menyimpan...");
        });
    });
</script>
