<form action="{{ route('pelanggaran.update', Crypt::encrypt($pelanggaran->no_sp)) }}" method="POST" id="formEditPelanggaran">
    @csrf
    @method('PUT')
    <div class="row">
                        <div class="col-12">
                            <div class="form-group mb-3">
                                <label for="nik_edit" class="form-label" style="font-weight: 600;">Karyawan <span class="text-danger">*</span></label>
                                <select name="nik" id="nik_edit" class="form-select select2Nik @error('nik') is-invalid @enderror" required>
                                    <option value="">Pilih Karyawan</option>
                                    @foreach ($karyawans as $karyawan)
                                        <option value="{{ $karyawan->nik }}" {{ old('nik', $pelanggaran->nik) == $karyawan->nik ? 'selected' : '' }}>
                                            {{ $karyawan->nik_show ?? $karyawan->nik }} - {{ $karyawan->nama_karyawan }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('nik')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group mb-3">
                                <label for="tanggal_edit" class="form-label" style="font-weight: 600;">Tanggal <span class="text-danger">*</span></label>
                                <x-input-with-icon icon="ti ti-calendar" label="" name="tanggal" datepicker="flatpickr-date"
                                    value="{{ old('tanggal', $pelanggaran->tanggal->format('Y-m-d')) }}" id="tanggal_edit" />
                                @error('tanggal')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group mb-3">
                                <label for="dari_edit" class="form-label" style="font-weight: 600;">Dari <span class="text-danger">*</span></label>
                                <x-input-with-icon icon="ti ti-calendar" label="" name="dari" datepicker="flatpickr-date"
                                    value="{{ old('dari', $pelanggaran->dari->format('Y-m-d')) }}" placeholder="Pilih Tanggal Dari" id="dari_edit" />
                                @error('dari')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group mb-3">
                                <label for="sampai_edit" class="form-label" style="font-weight: 600;">Sampai <span class="text-danger">*</span></label>
                                <x-input-with-icon icon="ti ti-calendar" label="" name="sampai" datepicker="flatpickr-date"
                                    value="{{ old('sampai', $pelanggaran->sampai->format('Y-m-d')) }}" placeholder="Pilih Tanggal Sampai" id="sampai_edit" />
                                @error('sampai')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group mb-3">
                                <label for="jenis_sp_edit" class="form-label" style="font-weight: 600;">Jenis SP <span class="text-danger">*</span></label>
                                <select name="jenis_sp" id="jenis_sp_edit" class="form-select @error('jenis_sp') is-invalid @enderror" required>
                                    <option value="">Pilih Jenis SP</option>
                                    <option value="SP1" {{ old('jenis_sp', $pelanggaran->jenis_sp) == 'SP1' ? 'selected' : '' }}>SP1</option>
                                    <option value="SP2" {{ old('jenis_sp', $pelanggaran->jenis_sp) == 'SP2' ? 'selected' : '' }}>SP2</option>
                                    <option value="SP3" {{ old('jenis_sp', $pelanggaran->jenis_sp) == 'SP3' ? 'selected' : '' }}>SP3</option>
                                </select>
                                @error('jenis_sp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group mb-3">
                                <label for="no_dokumen" class="form-label" style="font-weight: 600;">No Dokumen</label>
                                <input type="text" class="form-control @error('no_dokumen') is-invalid @enderror" id="no_dokumen" name="no_dokumen"
                                    value="{{ old('no_dokumen', $pelanggaran->no_dokumen) }}" placeholder="Masukkan nomor dokumen" maxlength="255">
                                @error('no_dokumen')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-12 col-sm-12 col-md-12">
                            <div class="form-group mb-3">
                                <label for="keterangan_edit" class="form-label" style="font-weight: 600;">Keterangan <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('keterangan') is-invalid @enderror" id="keterangan_edit" name="keterangan" rows="4"
                                    placeholder="Masukkan keterangan pelanggaran" maxlength="255">{{ old('keterangan', $pelanggaran->keterangan) }}</textarea>
                                @error('keterangan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary w-100" id="btnSimpan">
                                <i class="ti ti-device-floppy me-2"></i>Update
                            </button>
                        </div>
                    </div>
</form>

<script>
    $(function() {
        // Initialize select2 for karyawan
        const select2Nik = $(".select2Nik");
        if (select2Nik.length) {
            select2Nik.each(function() {
                var $this = $(this);
                $this.wrap('<div class="position-relative"></div>').select2({
                    placeholder: 'Pilih Karyawan',
                    allowClear: true,
                    dropdownParent: $('#mdlEditPelanggaran')
                });
            });
        }

        // Initialize flatpickr for date inputs
        $('.flatpickr-date').flatpickr({
            dateFormat: 'Y-m-d',
            allowInput: false
        });

        function buttonDisabled() {
            $("#btnSimpan").prop('disabled', true);
            $("#btnSimpan").html(`
            <div class="spinner-border spinner-border-sm text-white me-2" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            Loading..`);
        }

        $("#formEditPelanggaran").submit(function(e) {
            const nik = $("#nik_edit").val();
            const tanggal = $("#tanggal_edit").val();
            const dari = $("#dari_edit").val();
            const sampai = $("#sampai_edit").val();
            const jenis_sp = $("#jenis_sp_edit").val();
            const keterangan = $("#keterangan_edit").val();

            if (nik == '') {
                Swal.fire({
                    title: "Oops!",
                    text: "Karyawan harus diisi !",
                    icon: "warning",
                    showConfirmButton: true,
                    didClose: () => {
                        $("#nik_edit").focus();
                    }
                });
                return false;
            } else if (tanggal == '') {
                Swal.fire({
                    title: "Oops!",
                    text: 'Tanggal Harus Diisi !',
                    icon: "warning",
                    showConfirmButton: true,
                    didClose: () => {
                        $("#tanggal_edit").focus();
                    }
                });
                return false;
            } else if (dari == '') {
                Swal.fire({
                    title: "Oops!",
                    text: 'Dari Harus Diisi !',
                    icon: "warning",
                    showConfirmButton: true,
                    didClose: () => {
                        $("#dari_edit").focus();
                    }
                });
                return false;
            } else if (sampai == '') {
                Swal.fire({
                    title: "Oops!",
                    text: 'Sampai Harus Diisi !',
                    icon: "warning",
                    showConfirmButton: true,
                    didClose: () => {
                        $("#sampai_edit").focus();
                    }
                });
                return false;
            } else if (sampai < dari) {
                Swal.fire({
                    title: "Oops!",
                    text: 'Tanggal Sampai Tidak Boleh Lebih Kecil Dari Tanggal Dari !',
                    icon: "warning",
                    showConfirmButton: true,
                    didClose: () => {
                        $("#sampai_edit").focus();
                    }
                });
                return false;
            } else if (jenis_sp == '') {
                Swal.fire({
                    title: "Oops!",
                    text: 'Jenis SP Harus Dipilih !',
                    icon: "warning",
                    showConfirmButton: true,
                    didClose: () => {
                        $("#jenis_sp_edit").focus();
                    }
                });
                return false;
            } else if (keterangan == '') {
                Swal.fire({
                    title: "Oops!",
                    text: 'Keterangan Harus Diisi !',
                    icon: "warning",
                    showConfirmButton: true,
                    didClose: () => {
                        $("#keterangan_edit").focus();
                    }
                });
                return false;
            } else {
                buttonDisabled();
            }
        });
    });
</script>

