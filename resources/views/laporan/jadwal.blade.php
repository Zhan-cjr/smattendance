@extends('layouts.app')
@section('titlepage', 'Laporan Jadwal Karyawan')

@section('content')
@section('navigasi')
    <span>Laporan Jadwal Karyawan</span>
@endsection
<div class="row">
    <div class="col-lg-6 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center py-2" style="background-color: var(--theme-color-1) !important; color: white !important; min-height: 50px;">
                <div class="d-flex align-items-center">
                    <i class="ti ti-printer me-2 fs-5"></i>
                    <h6 class="card-title mb-0 text-white">Cetak Laporan Jadwal Karyawan</h6>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('laporan.cetakjadwal') }}" method="POST" target="_blank" id="formJadwal" class="mt-2">
                    @csrf
                    <div class="form-group mb-3">
                        <select name="kode_cabang" id="kode_cabang_jadwal" class="form-select select2Jadwal">
                            <option value="">Semua Cabang</option>
                            @foreach ($cabang as $d)
                                <option value="{{ $d->kode_cabang }}">{{ textUpperCase($d->nama_cabang) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <select name="kode_dept" id="kode_dept_jadwal" class="form-select select2Jadwal">
                            <option value="">Semua Departemen</option>
                            @foreach ($departemen as $d)
                                <option value="{{ $d->kode_dept }}">{{ textUpperCase($d->nama_dept) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <select name="nik" id="nik_jadwal" class="form-select select2Jadwal">
                            <option value="">Semua Karyawan</option>
                        </select>
                    </div>
                    
                    <div class="row" id="baris_tanggal">
                        <div class="col-6">
                            <div class="form-group mb-3">
                                <input type="text" name="dari" id="dari" class="form-control flatpickr-date"
                                    placeholder="Dari" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group mb-3">
                                <input type="text" name="sampai" id="sampai" class="form-control flatpickr-date"
                                    placeholder="Sampai" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <button type="submit" name="submitButton" class="btn btn-primary w-100" id="submitButton">
                                <i class="ti ti-printer me-1"></i> Cetak Jadwal
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection
@push('myscript')
<script>
    $(function() {
        const initSelect2 = (selector) => {
            $(selector).each(function() {
                var $this = $(this);
                var placeholder = $this.find("option:first").text();
                $this.wrap('<div class="position-relative"></div>').select2({
                    placeholder: placeholder,
                    allowClear: true,
                    dropdownParent: $this.parent()
                });
            });
        };

        initSelect2(".select2Jadwal");

        function loadKaryawan() {
            const kode_cabang = $("#kode_cabang_jadwal").val();
            const kode_dept = $("#kode_dept_jadwal").val();
            
            $.ajax({
                type: "GET",
                url: "{{ route('karyawan.getkaryawan') }}",
                data: {
                    kode_cabang: kode_cabang,
                    kode_dept: kode_dept
                },
                cache: false,
                success: function(respond) {
                    $("#nik_jadwal").empty();
                    $("#nik_jadwal").append("<option value=''>Semua Karyawan</option>");
                    respond.forEach(function(item) {
                        $("#nik_jadwal").append("<option value='" + item.nik + "'>" + item.nik + " - " + item
                            .nama_karyawan +
                            "</option>");
                    });
                }
            });
        }

        $("#kode_cabang_jadwal, #kode_dept_jadwal").change(function() {
            loadKaryawan();
        });

        $("#formJadwal").submit(function(e) {
            const dari = $("#dari").val();
            const sampai = $("#sampai").val();
            
            if (dari == "") {
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'Tanggal Dari harus diisi!',
                    showConfirmButton: true,
                    didClose: () => {
                        $("#dari").focus();
                    }
                });
                return false;
            } else if (sampai == "") {
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'Tanggal Sampai harus diisi!',
                    showConfirmButton: true,
                    didClose: () => {
                        $("#sampai").focus();
                    }
                });
                return false;
            }
        });
    });
</script>
@endpush
