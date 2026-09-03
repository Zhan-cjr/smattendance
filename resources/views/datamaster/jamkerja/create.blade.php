<form action="{{ route('jamkerja.store') }}" id="formcreateJamKerja" method="POST">
    @csrf
    <x-input-with-icon icon="ti ti-barcode" label="Kode Jam Kerja" name="kode_jam_kerja" maxlength="4" placeholder="Contoh: JK01 (Maksimal 4 karakter)"
        required />
    <x-input-with-icon icon="ti ti-file-text" label="Nama Jam Kerja" name="nama_jam_kerja" maxlength="50"
        placeholder="Contoh: Jam Kerja Pagi (Maksimal 50 karakter)" required />
    <div class="row">
        <div class="col-lg-6 col-md-12 col-sm-12">
            <x-input-with-icon icon="ti ti-clock" label="Jam Masuk" name="jam_masuk" required />
        </div>
        <div class="col-lg-6 col-md-12 col-sm-12">
            <x-input-with-icon icon="ti ti-clock" label="Jam Pulang" name="jam_pulang" required />
        </div>
    </div>
    <div class="form-group mb-3">
        <label for="istirahat" class="form-label" style="font-weight: 600;">
            Istirahat <span class="text-danger">*</span>
        </label>
        <select name="istirahat" id="istirahat" class="form-select" required>
            <option value="">Pilih Istirahat</option>
            <option value="1">Ya</option>
            <option value="0">Tidak</option>
        </select>
    </div>
    <div class="row" id="sectionIstirahat">
        <div class="col-lg-6 col-md-12 col-sm-12">
            <x-input-with-icon icon="ti ti-clock" label="Jam Awal Istirahat" name="jam_awal_istirahat" />
        </div>
        <div class="col-lg-6 col-md-12 col-sm-12">
            <x-input-with-icon icon="ti ti-clock" label="Jam Akhir Istirahat" name="jam_akhir_istirahat" />
        </div>
    </div>
    <x-input-with-icon icon="ti ti-clock" label="Total Jam" name="total_jam" type="number" placeholder="Contoh: 8 (Minimal 1, Maksimal 24 jam)"
        min="1" max="24" required />
    <x-input-with-icon icon="ti ti-file-text" label="Keterangan" name="keterangan" maxlength="255"
        placeholder="Contoh: Jam kerja untuk shift pagi (Opsional, maksimal 255 karakter)" />
    <x-input-with-icon icon="ti ti-palette" label="Warna (Untuk Laporan)" name="color" type="color" placeholder="Pilih Warna" />
    <div class="form-group mb-3">
        <label for="lintashari" class="form-label" style="font-weight: 600;">
            Lintas Hari <span class="text-danger">*</span>
        </label>
        <select name="lintashari" id="lintashari" class="form-select" required>
            <option value="">Pilih Lintas Hari</option>
            <option value="1">Ya</option>
            <option value="0">Tidak</option>
        </select>
    </div>
    <div class="row">
        <div class="col">
            <button type="submit" class="btn btn-primary w-100" id="btnSimpan"><i class="ti ti-send me-1"></i> Simpan</button>
        </div>
    </div>
</form>
<script src="{{ asset('assets/js/pages/jamkerja.js') }}"></script>
<script src="{{ asset('assets/js/jquery.mask.min.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
<script>
    $(document).ready(function() {
        // function toogleIstirahat() {
        //     if ($('#istirahat').val() == 1) {
        //         $('#sectionIstirahat').show();
        //     } else {
        //         $('#sectionIstirahat').hide();
        //     }
        // }
        // toogleIstirahat();

        // $('#istirahat').on('change', function() {
        //     toogleIstirahat();
        // });

        $("#jam_masuk,#jam_pulang,#jam_awal_istirahat,#jam_akhir_istirahat").mask("00:00");
    });
</script>
