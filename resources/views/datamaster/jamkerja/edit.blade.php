<form action="{{ route('jamkerja.update', Crypt::encrypt($jamkerja->kode_jam_kerja)) }}" id="formeditJamKerja" method="POST">
    @csrf
    @method('PUT')
    <x-input-with-icon icon="ti ti-barcode" label="Kode Jam Kerja" name="kode_jam_kerja" :value="$jamkerja->kode_jam_kerja" readonly />
    <x-input-with-icon icon="ti ti-file-text" label="Nama Jam Kerja" name="nama_jam_kerja" :value="$jamkerja->nama_jam_kerja" maxlength="50" placeholder="Contoh: Jam Kerja Pagi (Maksimal 50 karakter)" />
    <div class="row">
        <div class="col-lg-6 col-md-12 col-sm-12">
            <x-input-with-icon icon="ti ti-clock" label="Jam Masuk" name="jam_masuk" :value="$jamkerja->jam_masuk" required />
        </div>
        <div class="col-lg-6 col-md-12 col-sm-12">
            <x-input-with-icon icon="ti ti-clock" label="Jam Pulang" name="jam_pulang" :value="$jamkerja->jam_pulang" required />
        </div>
    </div>
    <div class="form-group mb-3">
        <label for="istirahat" class="form-label" style="font-weight: 600;">
            Istirahat <span class="text-danger">*</span>
        </label>
        <select name="istirahat" id="istirahat" class="form-select" required>
            <option value="">Pilih Istirahat</option>
            <option value="1" @selected($jamkerja->istirahat == 1)>Ya</option>
            <option value="0" @selected($jamkerja->istirahat == 0)>Tidak</option>
        </select>
    </div>
    <div class="row" id="sectionIstirahat">
        <div class="col-lg-6 col-md-12 col-sm-12">
            <x-input-with-icon icon="ti ti-clock" label="Jam Awal Istirahat" name="jam_awal_istirahat" :value="$jamkerja->jam_awal_istirahat" />
        </div>
        <div class="col-lg-6 col-md-12 col-sm-12">
            <x-input-with-icon icon="ti ti-clock" label="Jam Akhir Istirahat" name="jam_akhir_istirahat" :value="$jamkerja->jam_akhir_istirahat" />
        </div>
    </div>
    <x-input-with-icon icon="ti ti-clock" label="Total Jam" name="total_jam" :value="$jamkerja->total_jam" type="number" placeholder="Contoh: 8 (Minimal 1, Maksimal 24 jam)" min="1" max="24" required />
    <x-input-with-icon icon="ti ti-file-text" label="Keterangan" name="keterangan" :value="$jamkerja->keterangan" maxlength="255" placeholder="Contoh: Jam kerja untuk shift pagi (Opsional, maksimal 255 karakter)" />
    <x-input-with-icon icon="ti ti-palette" label="Warna (Untuk Laporan)" name="color" type="color" :value="$jamkerja->color" placeholder="Pilih Warna" />
    <div class="form-group mb-3">
        <label for="lintashari" class="form-label" style="font-weight: 600;">
            Lintas Hari <span class="text-danger">*</span>
        </label>
        <select name="lintashari" id="lintashari" class="form-select" required>
            <option value="">Pilih Lintas Hari</option>
            <option value="1" @selected($jamkerja->lintashari == 1)>Ya</option>
            <option value="0" @selected($jamkerja->lintashari == 0)>Tidak</option>
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
        function toogleIstirahat() {
            if ($('#istirahat').val() == 1) {
                $('#sectionIstirahat').show();
            } else {
                $('#sectionIstirahat').hide();
            }
        }
        toogleIstirahat();

        $('#istirahat').on('change', function() {
            toogleIstirahat();
        });

        $("#jam_masuk,#jam_pulang,#jam_awal_istirahat,#jam_akhir_istirahat").mask("00:00");
    });
</script>
