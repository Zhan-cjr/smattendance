<form action="{{ route('mutasi.store') }}" method="POST" enctype="multipart/form-data" id="formMutasi">
    @csrf
    <div class="row">
        <div class="col-md-6 col-sm-12">
            <div class="form-group mb-3">
                <label class="form-label fw-bold">Tanggal Mutasi</label>
                <input type="date" name="tanggal_mutasi" class="form-control" value="{{ date('Y-m-d') }}" required>
            </div>
        </div>
        <div class="col-md-6 col-sm-12">
            <div class="form-group mb-3">
                <label class="form-label fw-bold">Jenis Mutasi</label>
                <select name="jenis_mutasi" class="form-select" required>
                    <option value="">Pilih Jenis</option>
                    <option value="MUTASI">MUTASI (Pindah Lokasi/Dept)</option>
                    <option value="PROMOSI">PROMOSI (Naik Jabatan)</option>
                    <option value="DEMOSI">DEMOSI (Turun Jabatan)</option>
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group mb-3">
                <label class="form-label fw-bold">Pilih Karyawan</label>
                <select name="nik" id="nik" class="form-select select2-mutasi" required>
                    <option value="">Pilih Karyawan</option>
                    @foreach ($karyawan as $k)
                        <option value="{{ $k->nik }}">{{ $k->nik }} - {{ $k->nama_karyawan }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div id="current_data" class="alert alert-info d-none mb-3 py-2 px-3">
        <div class="d-flex align-items-center">
            <i class="ti ti-info-circle me-2"></i>
            <div>
                <strong>Data Saat Ini:</strong>
                <span class="ms-1">
                    Cabang: <span id="cur_cabang" class="fw-bold"></span> |
                    Dept: <span id="cur_dept" class="fw-bold"></span> |
                    Jabatan: <span id="cur_jabatan" class="fw-bold"></span>
                </span>
            </div>
        </div>
    </div>

    <div class="divider">
        <div class="divider-text">Data Baru</div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="form-group mb-3">
                <label class="form-label fw-bold">Cabang Baru</label>
                <select name="kode_cabang_baru" id="kode_cabang_baru" class="form-select select2-mutasi" required>
                    <option value="">Pilih Cabang</option>
                    @foreach ($cabang as $c)
                        <option value="{{ $c->kode_cabang }}">{{ $c->nama_cabang }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group mb-3">
                <label class="form-label fw-bold">Departemen Baru</label>
                <select name="kode_dept_baru" id="kode_dept_baru" class="form-select select2-mutasi" required>
                    <option value="">Pilih Departemen</option>
                    @foreach ($departemen as $d)
                        <option value="{{ $d->kode_dept }}">{{ $d->nama_dept }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group mb-3">
                <label class="form-label fw-bold">Jabatan Baru</label>
                <select name="kode_jabatan_baru" id="kode_jabatan_baru" class="form-select select2-mutasi" required>
                    <option value="">Pilih Jabatan</option>
                    @foreach ($jabatan as $j)
                        <option value="{{ $j->kode_jabatan }}">{{ $j->nama_jabatan }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group mb-3">
                <label class="form-label fw-bold">Status Karyawan Baru (Opsional)</label>
                <select name="status_karyawan_baru" class="form-select">
                    <option value="">Pilih (Opsional)</option>
                    @foreach ($status_karyawan as $s)
                        <option value="{{ $s->kode_status_karyawan }}">{{ $s->nama_status_karyawan }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group mb-3">
                <label class="form-label fw-bold">Keterangan</label>
                <textarea name="keterangan" class="form-control" rows="3"></textarea>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group mb-3">
                <label class="form-label fw-bold">Upload SK (Opsional)</label>
                <input type="file" name="doc_sk" class="form-control">
                <small class="text-muted">Format: PDF, JPG, PNG. Max 2MB</small>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-12">
            <button type="submit" class="btn btn-primary w-100"><i class="ti ti-send me-1"></i> Simpan Mutasi</button>
        </div>
    </div>
</form>

<script>
    $(function() {
        $(".select2-mutasi").select2({
            dropdownParent: $('#modalInput')
        });

        $("#nik").change(function() {
            var nik = $(this).val();
            if (nik) {
                $.ajax({
                    url: '/mutasi/' + nik + '/getKaryawan',
                    type: 'GET',
                    success: function(res) {
                        if (res) {
                            $("#current_data").removeClass('d-none');
                            $("#cur_cabang").text(res.cabang ? res.cabang.nama_cabang : res.kode_cabang);
                            $("#cur_dept").text(res.departemen ? res.departemen.nama_dept : res.kode_dept);
                            $("#cur_jabatan").text(res.jabatan ? res.jabatan.nama_jabatan : res.kode_jabatan);

                            // Auto fill new data with old data as default
                            $("#kode_cabang_baru").val(res.kode_cabang).trigger('change');
                            $("#kode_dept_baru").val(res.kode_dept).trigger('change');
                            $("#kode_jabatan_baru").val(res.kode_jabatan).trigger('change');
                        }
                    }
                });
            } else {
                $("#current_data").addClass('d-none');
            }
        });
    });
</script>
