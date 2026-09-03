<form action="{{ route('tunjangan.store') }}" id="formcreateTunjangan" method="POST">
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
    @foreach ($jenis_tunjangan as $d)
        <input type="hidden" name="kode_jenis_tunjangan[]" value="{{ $d->kode_jenis_tunjangan }}">
        <div class="form-group mb-3">
            <label for="jumlah_{{ $d->kode_jenis_tunjangan }}" class="form-label" style="font-weight: 600;">
                {{ $d->jenis_tunjangan }} <span class="text-danger">*</span>
            </label>
            <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="ti ti-moneybag"></i></span>
                <input type="text" 
                    class="form-control money" 
                    id="jumlah_{{ $d->kode_jenis_tunjangan }}" 
                    name="jumlah[]" 
                    placeholder="Contoh: 500000 (Minimal 0, Maksimal 999.999.999)" 
                    style="text-align: right;" 
                    autocomplete="off" />
            </div>
        </div>
    @endforeach
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
        $("#formcreateTunjangan").submit(function(e) {
            const form = $(this);
            const nik = form.find("select[name=nik]").val();
            const jumlah = form.find("input[name='jumlah[]']");
            const tanggal_berlaku = form.find("input[name=tanggal_berlaku]").val();
            let cekJumlahkosong = false;
            let jumlahKosongIndex = -1;
            
            jumlah.each(function(index) {
                const val = $(this).val().trim();
                if (val == "") {
                    cekJumlahkosong = true;
                    jumlahKosongIndex = index;
                    return false;
                }
            });
            
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
            
            if (cekJumlahkosong) {
                e.preventDefault();
                Swal.fire({
                    icon: "warning",
                    title: "Peringatan!",
                    text: 'Semua Jumlah Tunjangan wajib diisi dan minimal 0',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6',
                    didClose: () => {
                        if (jumlahKosongIndex >= 0) {
                            jumlah.eq(jumlahKosongIndex).focus();
                        } else {
                            jumlah.first().focus();
                        }
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
