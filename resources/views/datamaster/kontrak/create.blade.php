<form action="{{ route('kontrak.store') }}" method="POST" id="formKontrak">
    @csrf
    <div class="row">

        <x-input-with-icon label="No Kontrak" name="no_kontrak" icon="ti ti-file-certificate" :disabled="true" placeholder="Auto" />
        <x-input-with-icon label="Tanggal Kontrak" name="tanggal" icon="ti ti-calendar" datepicker="flatpickr-date" value="{{ old('tanggal') }}" />
        
        <div class="form-group mb-1">
            <select name="jenis_kontrak" id="jenis_kontrak" class="form-select">
                <option value="" disabled selected>Pilih Status Kontrak</option>
                <option value="K" @selected(old('jenis_kontrak') == 'K')>Kontrak (PKWT)</option>
                <option value="T" @selected(old('jenis_kontrak') == 'T')>Tetap (PKWTT)</option>
            </select>
             @error('jenis_kontrak')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div id="periode_kontrak">
            <x-input-with-icon label="Tanggal Mulai" name="dari" icon="ti ti-calendar" datepicker="flatpickr-date" value="{{ old('dari') }}" />
            <x-input-with-icon label="Tanggal Selesai" name="sampai" icon="ti ti-calendar" datepicker="flatpickr-date" value="{{ old('sampai') }}" />
        </div>
        <div class="form-group mb-1">
            <select name="nik" id="nik" class="form-select select2" data-placeholder="Pilih Karyawan">
                <option value="">Pilih Karyawan</option>
                @foreach ($karyawans as $karyawan)
                    <option value="{{ $karyawan->nik }}" @selected(old('nik') == $karyawan->nik) data-kode_cabang="{{ $karyawan->kode_cabang }}"
                        data-kode_dept="{{ $karyawan->kode_dept }}" data-kode_jabatan="{{ $karyawan->kode_jabatan }}">
                        {{ $karyawan->nik }} - {{ $karyawan->nama_karyawan }}
                    </option>
                @endforeach
            </select>
            @error('nik')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>


        <div class="form-group mb-1">
            <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="ti ti-briefcase"></i></span>
                <select name="kode_cabang" id="kode_cabang" class="form-select">
                    <option value="">Pilih Cabang</option>
                    @foreach ($cabangs as $cabang)
                        <option value="{{ $cabang->kode_cabang }}" @selected(old('kode_cabang') == $cabang->kode_cabang)>
                            {{ $cabang->nama_cabang }}
                        </option>
                    @endforeach
                </select>
            </div>
            @error('kode_cabang')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>


        <div class="form-group mb-1">
            <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="ti ti-layout-grid"></i></span>
                <select name="kode_dept" id="kode_dept" class="form-select">
                    <option value="">Pilih Departemen</option>
                    @foreach ($departemens as $dept)
                        <option value="{{ $dept->kode_dept }}" @selected(old('kode_dept') == $dept->kode_dept)>
                            {{ $dept->nama_dept }}
                        </option>
                    @endforeach
                </select>
            </div>
            @error('kode_dept')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
        <div class="form-group mb-1">
            <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="ti ti-layout-grid"></i></span>
                <select name="kode_jabatan" id="kode_jabatan" class="form-select">
                    <option value="">Pilih Jabatan</option>
                    @foreach ($jabatans as $jabatan)
                        <option value="{{ $jabatan->kode_jabatan }}" @selected(old('kode_jabatan') == $jabatan->kode_jabatan)>
                            {{ $jabatan->nama_jabatan }}
                        </option>
                    @endforeach
                </select>
            </div>
            @error('kode_jabatan')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <input type="hidden" name="status_kontrak" value="1">
        <input type="hidden" name="kode_gaji" id="kode_gaji" value="{{ old('kode_gaji') }}">
        <input type="hidden" name="kode_tunjangan" id="kode_tunjangan" value="{{ old('kode_tunjangan') }}">
        <x-input-with-icon label="Gaji Pokok Kontrak (opsional)" name="nominal_gaji" icon="ti ti-currency-dollar" money="true" align="right"
            value="{{ old('nominal_gaji') }}" />

        @if ($jenisTunjangans->count())
            @foreach ($jenisTunjangans as $index => $jenis)
                <input type="hidden" name="kode_jenis_tunjangan[]" value="{{ $jenis->kode_jenis_tunjangan }}">
                <x-input-with-icon label="{{ $jenis->jenis_tunjangan }}" name="nominal_tunjangan_detail[]" icon="ti ti-cash" money="true"
                    align="right" value="{{ old('nominal_tunjangan_detail.' . $index) }}" />
            @endforeach
        @endif
    </div>

    <div class="form-group mb-2">
        <button class="btn btn-primary w-100" id="btnSimpanKontrak">
            <i class="ti ti-send me-1"></i> Simpan Kontrak
        </button>
    </div>
</form>

<script>
    $(function() {
        const modal = $('#modalKontrak');
        const latestUrlTemplate = "{{ route('kontrak.karyawan.latest', ':nik') }}";
        const $summary = $('#latestCompensationSummary');
        const $salaryAmount = $('#latestSalaryAmount');
        const $salaryMeta = $('#latestSalaryMeta');
        const $salaryCode = $('#latestSalaryCode');
        const $allowanceAmount = $('#latestAllowanceTotal');
        const $allowanceMeta = $('#latestAllowanceMeta');
        const $allowanceCode = $('#latestAllowanceCode');
        const $inputSalaryNominal = $('input[name="nominal_gaji"]');

        function formatRupiah(value) {
            if (!value) {
                return '-';
            }
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(value);
        }

        function resetSummary() {
            $summary.hide();
            $salaryAmount.text('-');
            $salaryMeta.text('-');
            $salaryCode.text('-');
            $allowanceAmount.text('-');
            $allowanceMeta.text('-');
            $allowanceCode.text('-');
        }

        $('.select2').select2({
            dropdownParent: modal,
            width: '100%'
        });
        $('.flatpickr-date').flatpickr();
        if ($.fn.maskMoney) {
            $('.money').maskMoney();
        }

        $('#nik').on('change', function() {
            const option = $(this).find(':selected');
            const nik = $(this).val();
            const cabang = option.data('kode_cabang');
            const dept = option.data('kode_dept');
            const jabatan = option.data('kode_jabatan');

            if (cabang) {
                $("#formKontrak").find('#kode_cabang').val(cabang).trigger('change');
            }
            if (dept) {
                $("#formKontrak").find('#kode_dept').val(dept).trigger('change');
            }
            if (jabatan) {
                $("#formKontrak").find('#kode_jabatan').val(jabatan).trigger('change');
            }

            if (!nik) {
                resetSummary();
                return;
            }

            $.get(latestUrlTemplate.replace(':nik', nik))
                .done(function(res) {
                    let hasData = false;
                    if (res.salary) {
                        hasData = true;
                        $salaryAmount.text(formatRupiah(res.salary.jumlah));
                        $salaryMeta.text(res.salary.tanggal || '-');
                        $salaryCode.text(res.salary.kode || '-');
                        if (res.salary.kode) {
                            $('#kode_gaji').val(res.salary.kode).trigger('change');
                        }
                        // Set nilai gaji pokok dari data terakhir berdasarkan tanggal berlaku
                        if (res.salary.jumlah) {
                            $inputSalaryNominal.val(res.salary.jumlah);
                            // Trigger maskMoney untuk update format tampilan
                            if ($.fn.maskMoney) {
                                $inputSalaryNominal.maskMoney('mask');
                            }
                        }
                    } else {
                        $salaryAmount.text('-');
                        $salaryMeta.text('-');
                        $salaryCode.text('-');
                        $inputSalaryNominal.val('');
                        if ($.fn.maskMoney) {
                            $inputSalaryNominal.maskMoney('mask');
                        }
                    }

                    if (res.allowance) {
                        hasData = true;
                        $allowanceAmount.text(formatRupiah(res.allowance.total));
                        $allowanceMeta.text(res.allowance.tanggal || '-');
                        $allowanceCode.text(res.allowance.kode || '-');
                        if (res.allowance.kode) {
                            $('#kode_tunjangan').val(res.allowance.kode).trigger('change');
                        }

                        // Isi detail tunjangan per jenis
                        if (res.allowance.details && res.allowance.details.length > 0) {
                            // Loop melalui semua hidden input kode_jenis_tunjangan
                            $('input[name="kode_jenis_tunjangan[]"]').each(function(index) {
                                const kodeJenis = $(this).val();
                                // Cari detail yang sesuai
                                const detail = res.allowance.details.find(d => d.kode_jenis_tunjangan === kodeJenis);
                                if (detail) {
                                    // Ambil input nominal_tunjangan_detail yang sesuai dengan index
                                    const $inputDetail = $('input[name="nominal_tunjangan_detail[]"]').eq(index);
                                    $inputDetail.val(detail.jumlah);
                                    // Trigger maskMoney untuk update format tampilan
                                    if ($.fn.maskMoney) {
                                        $inputDetail.maskMoney('mask');
                                    }
                                }
                            });
                        }
                    } else {
                        $allowanceAmount.text('-');
                        $allowanceMeta.text('-');
                        $allowanceCode.text('-');
                        // Kosongkan semua field tunjangan detail
                        $('input[name="nominal_tunjangan_detail[]"]').each(function() {
                            $(this).val('');
                            if ($.fn.maskMoney) {
                                $(this).maskMoney('mask');
                            }
                        });
                    }

                    if (hasData) {
                        $summary.slideDown();
                    } else {
                        resetSummary();
                    }
                })
                .fail(function() {
                    resetSummary();
                });
        });

        // Trigger once if already selected (old input)
        const preselectedNik = $('#nik').val();
        if (preselectedNik) {
            $('#nik').trigger('change');
        }

        function togglePeriodeKontrak() {
            const status = $('#jenis_kontrak').val();
            if (status === 'T') {
                $('#periode_kontrak').hide();
            } else {
                $('#periode_kontrak').show();
            }
        }
        $('#jenis_kontrak').on('change', togglePeriodeKontrak);
        
        // Trigger on load if old value exists
        togglePeriodeKontrak();

        $("#formKontrak").on('submit', function(e) {
            e.preventDefault();

            // Ambil nilai form
            const tanggal = $('#tanggal').val();
            const dari = $('#dari').val();
            const sampai = $('#sampai').val();
            const nik = $('#nik').val();
            const kode_cabang = $('#kode_cabang').val();
            const kode_dept = $('#kode_dept').val();
            const kode_jabatan = $('#kode_jabatan').val();
            const jenis_kontrak = $('#jenis_kontrak').val();

            // Validasi
            let errors = [];

            if (!jenis_kontrak) {
                errors.push('Status Kontrak harus dipilih');
            }

            if (!tanggal) {
                errors.push('Tanggal Kontrak harus diisi');
            }

            if (jenis_kontrak === 'K') {
                if (!dari) {
                    errors.push('Tanggal Mulai harus diisi');
                }

                if (!sampai) {
                    errors.push('Tanggal Selesai harus diisi');
                }

                 // Validasi tanggal
                if (dari && sampai) {
                    const dateDari = new Date(dari);
                    const dateSampai = new Date(sampai);
                    if (dateSampai < dateDari) {
                        errors.push('Tanggal Selesai harus lebih besar atau sama dengan Tanggal Mulai');
                    }
                }
            }
            
            if (!nik) {
                errors.push('Karyawan harus dipilih');
            }

            if (!kode_cabang) {
                errors.push('Cabang harus dipilih');
            }

            if (!kode_dept) {
                errors.push('Departemen harus dipilih');
            }

            if (!kode_jabatan) {
                errors.push('Jabatan harus dipilih');
            }

            // Jika ada error, tampilkan Sweet Alert
            if (errors.length > 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal',
                    html: '<ul style="text-align: left; margin-top: 10px;">' +
                        errors.map(error => '<li>' + error + '</li>').join('') +
                        '</ul>',
                    confirmButtonText: 'OK'
                });
                return false;
            }

            // Jika validasi berhasil, submit form
            $("#btnSimpanKontrak").attr('disabled', true).html('<i class="ti ti-loader me-1"></i> Menyimpan...');
            this.submit();
        });
    });
</script>
