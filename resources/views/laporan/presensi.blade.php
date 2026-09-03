@extends('layouts.app')
@section('titlepage', 'Laporan Presensi & Gaji')

@section('content')
@section('navigasi')
    <span>Laporan Presensi & Gaji</span>
@endsection
<div class="row">
    <!-- Form Bulanan -->
    <div class="col-lg-6 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center py-2" style="background-color: var(--theme-color-1) !important; color: white !important; min-height: 50px;">
                <div class="d-flex align-items-center">
                    <i class="ti ti-printer me-2 fs-5"></i>
                    <h6 class="card-title mb-0 text-white">Laporan Gaji & Presensi (Bulanan)</h6>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('laporan.cetakpresensi') }}" method="POST" target="_blank" id="formBulanan" class="mt-2">
                    @csrf
                    <input type="hidden" name="jenis_upah" value="Bulanan">
                    <div class="form-group mb-3">
                        <select name="kode_cabang" id="kode_cabang_bulanan" class="form-select select2Bulanan">
                            <option value="">Semua Cabang</option>
                            @foreach ($cabang as $d)
                                <option value="{{ $d->kode_cabang }}">{{ textUpperCase($d->nama_cabang) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <select name="kode_dept" id="kode_dept_bulanan" class="form-select select2Bulanan">
                            <option value="">Semua Departemen</option>
                            @foreach ($departemen as $d)
                                <option value="{{ $d->kode_dept }}">{{ textUpperCase($d->nama_dept) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <select name="nik" id="nik_bulanan" class="form-select select2Bulanan">
                            <option value="">Semua Karyawan (Bulanan)</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <select name="periode_laporan" id="periode_laporan_bulanan" class="form-select">
                            <option value="">Periode Laporan</option>
                            <option value="1" selected>Periode Gaji</option>
                            <option value="2">Bulan Berjalan</option>
                            <option value="3">Range Tanggal</option>
                        </select>
                    </div>
                    <div class="row" id="baris_tanggal_bulanan">
                        <div class="col-6">
                            <div class="form-group mb-3">
                                <input type="text" name="dari" id="dari_bulanan" class="form-control flatpickr-date" placeholder="Dari">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group mb-3">
                                <input type="text" name="sampai" id="sampai_bulanan" class="form-control flatpickr-date" placeholder="Sampai">
                            </div>
                        </div>
                    </div>
                    <div class="row" id="baris_bulan_bulanan">
                        <div class="col">
                            <div class="form-group mb-3">
                                <select name="bulan" id="bulan_bulanan" class="form-select">
                                    <option value="">Bulan</option>
                                    @foreach ($list_bulan as $d)
                                        <option {{ date('m') == $d['kode_bulan'] ? 'selected' : '' }} value="{{ $d['kode_bulan'] }}">
                                            {{ $d['nama_bulan'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="baris_tahun_bulanan">
                        <div class="col">
                            <div class="form-group mb-3">
                                <select name="tahun" id="tahun_bulanan" class="form-select">
                                    <option value="">Tahun</option>
                                    @for ($t = $start_year; $t <= date('Y'); $t++)
                                        <option {{ date('Y') == $t ? 'selected' : '' }} value="{{ $t }}">{{ $t }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <select name="format_laporan" id="format_laporan_bulanan" class="form-select">
                            <option value="">Format Laporan</option>
                            <option value="1" selected>Laporan Presensi</option>
                            <option value="2">Laporan Gaji</option>
                            <option value="3">Slip Gaji</option>
                        </select>
                    </div>

                    <div class="form-group mb-3" id="format_rekap_bulanan_wrapper">
                        <select name="format_rekap" id="format_rekap_bulanan" class="form-select">
                            <option value="1">Format 1 (Default)</option>
                            <option value="2">Format 2 (Struktur Baru)</option>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-lg-6 col-md-12 col-sm-12">
                            <button type="submit" name="submitButton" class="btn btn-primary w-100">
                                <i class="ti ti-printer me-1"></i> Cetak
                            </button>
                        </div>
                        <div class="col-lg-6 col-md-12 col-sm-12">
                            <button type="submit" name="exportButton" class="btn btn-success w-100">
                                <i class="ti ti-download me-1"></i> Export Excel
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Form Harian -->
    <div class="col-lg-6 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center py-2" style="background-color: var(--theme-color-1) !important; color: white !important; min-height: 50px;">
                <div class="d-flex align-items-center">
                    <i class="ti ti-printer me-2 fs-5"></i>
                    <h6 class="card-title mb-0 text-white">Laporan Gaji (Harian)</h6>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('laporan.cetakpresensi') }}" method="POST" target="_blank" id="formHarian" class="mt-2">
                    @csrf
                    <input type="hidden" name="jenis_upah" value="Harian">
                    <input type="hidden" name="periode_laporan" value="3"> <!-- Fix to Range Tanggal -->
                    
                    <div class="form-group mb-3">
                        <select name="kode_cabang" id="kode_cabang_harian" class="form-select select2Harian">
                            <option value="">Semua Cabang</option>
                            @foreach ($cabang as $d)
                                <option value="{{ $d->kode_cabang }}">{{ textUpperCase($d->nama_cabang) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <select name="kode_dept" id="kode_dept_harian" class="form-select select2Harian">
                            <option value="">Semua Departemen</option>
                            @foreach ($departemen as $d)
                                <option value="{{ $d->kode_dept }}">{{ textUpperCase($d->nama_dept) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group mb-3">
                                <input type="text" name="dari" id="dari_harian" class="form-control flatpickr-date" placeholder="Dari (Range Tanggal)">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group mb-3">
                                <input type="text" name="sampai" id="sampai_harian" class="form-control flatpickr-date" placeholder="Sampai (Range Tanggal)">
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <select name="nik" id="nik_harian" class="form-select select2Harian">
                            <option value="">Semua Karyawan (Harian)</option>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <select name="format_laporan" id="format_laporan_harian" class="form-select">
                            <option value="">Format Laporan</option>
                            <option value="1" selected>Laporan Presensi</option>
                            <option value="2">Laporan Gaji</option>
                            <option value="3">Slip Gaji</option>
                        </select>
                    </div>

                    <div class="form-group mb-3" id="format_rekap_harian_wrapper">
                        <select name="format_rekap" id="format_rekap_harian" class="form-select">
                            <option value="1">Format 1 (Default)</option>
                            <option value="2">Format 2 (Struktur Baru)</option>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-lg-6 col-md-12 col-sm-12">
                            <button type="submit" name="submitButton" class="btn btn-primary w-100">
                                <i class="ti ti-printer me-1"></i> Cetak
                            </button>
                        </div>
                        <div class="col-lg-6 col-md-12 col-sm-12">
                            <button type="submit" name="exportButton" class="btn btn-success w-100">
                                <i class="ti ti-download me-1"></i> Export Excel
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
        // Initialize Select2
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

        initSelect2(".select2Bulanan");
        initSelect2(".select2Harian");

        // Load Karyawan Function
        function loadKaryawan(type) {
            const kode_cabang = $(`#kode_cabang_${type}`).val();
            const kode_dept = $(`#kode_dept_${type}`).val();
            const jenis_upah = type === 'bulanan' ? 'Bulanan' : 'Harian';
            const tanggal = type === 'harian' ? $(`#dari_harian`).val() : null;
            const targetSelect = $(`#nik_${type}`);

            $.ajax({
                type: "GET",
                url: "{{ route('karyawan.getkaryawan') }}",
                data: {
                    kode_cabang: kode_cabang,
                    kode_dept: kode_dept,
                    jenis_upah: jenis_upah,
                    tanggal: tanggal
                },
                cache: false,
                success: function(respond) {
                    targetSelect.empty();
                    targetSelect.append(`<option value=''>Semua Karyawan (${jenis_upah})</option>`);
                    respond.forEach(function(item) {
                        targetSelect.append(`<option value="${item.nik}">${item.nik} - ${item.nama_karyawan}</option>`);
                    });
                }
            });
        }

        // Listeners Bulanan
        $("#kode_cabang_bulanan, #kode_dept_bulanan").change(function() {
            loadKaryawan('bulanan');
        });

        // Listeners Harian
        $("#kode_cabang_harian, #kode_dept_harian, #dari_harian").change(function() {
            loadKaryawan('harian');
        });

        // Initial load for both
        loadKaryawan('bulanan');
        loadKaryawan('harian');

        // Toggle logic for Bulanan
        function togglePeriodeBulanan() {
            const periode = $("#periode_laporan_bulanan").val();
            if (periode == "3") {
                $("#baris_tanggal_bulanan").show();
                $("#baris_bulan_bulanan, #baris_tahun_bulanan").hide();
            } else {
                $("#baris_tanggal_bulanan").hide();
                $("#baris_bulan_bulanan, #baris_tahun_bulanan").show();
            }
        }

        $("#periode_laporan_bulanan").change(togglePeriodeBulanan);
        togglePeriodeBulanan();

        function toggleFormatRekap() {
            const formatBulanan = $("#format_laporan_bulanan").val();
            const formatHarian = $("#format_laporan_harian").val();

            if (formatBulanan == "1") {
                $("#format_rekap_bulanan_wrapper").show();
            } else {
                $("#format_rekap_bulanan_wrapper").hide();
            }

            if (formatHarian == "1") {
                $("#format_rekap_harian_wrapper").show();
            } else {
                $("#format_rekap_harian_wrapper").hide();
            }
        }

        $("#format_laporan_bulanan, #format_laporan_harian").change(toggleFormatRekap);
        toggleFormatRekap();

        // Validation logic
        const validateForm = (formId) => {
            const form = $(formId);
            const format = form.find("[name='format_laporan']").val();
            const type = formId === '#formBulanan' ? 'bulanan' : 'harian';
            
            if (format === "") {
                Swal.fire('Warning', 'Format Laporan harus diisi!', 'warning');
                return false;
            }

            if (type === 'bulanan') {
                const periode = $("#periode_laporan_bulanan").val();
                if (periode === "") {
                    Swal.fire('Warning', 'Periode Laporan harus diisi!', 'warning');
                    return false;
                }
                if (periode === "3") {
                    if ($("#dari_bulanan").val() === "" || $("#sampai_bulanan").val() === "") {
                        Swal.fire('Warning', 'Range Tanggal harus diisi!', 'warning');
                        return false;
                    }
                } else {
                    if ($("#bulan_bulanan").val() === "" || $("#tahun_bulanan").val() === "") {
                        Swal.fire('Warning', 'Bulan/Tahun harus diisi!', 'warning');
                        return false;
                    }
                }
            } else {
                // Harian
                if ($("#dari_harian").val() === "" || $("#sampai_harian").val() === "") {
                    Swal.fire('Warning', 'Range Tanggal harus diisi!', 'warning');
                    return false;
                }
            }
            return true;
        };

        $("#formBulanan, #formHarian").submit(function(e) {
            if (!validateForm('#' + $(this).attr('id'))) {
                e.preventDefault();
                return false;
            }
        });
    });
</script>
@endpush
