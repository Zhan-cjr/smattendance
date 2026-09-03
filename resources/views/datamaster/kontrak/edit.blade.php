<form action="{{ route('kontrak.update', Crypt::encrypt($kontrak->id)) }}" method="POST" id="formKontrakEdit">
    @csrf
    @method('PUT')
    <div class="row">
        <x-input-with-icon label="No Kontrak" name="no_kontrak_display" icon="ti ti-file-certificate" :readonly="true"
            value="{{ $kontrak->no_kontrak }}" />
        <input type="hidden" name="no_kontrak" value="{{ old('no_kontrak', $kontrak->no_kontrak) }}">

        <x-input-with-icon label="Tanggal Kontrak" name="tanggal" icon="ti ti-calendar" datepicker="flatpickr-date"
            value="{{ old('tanggal', $kontrak->tanggal) }}" />
        
        <div class="form-group mb-1">
            <select name="jenis_kontrak" id="jenis_kontrak" class="form-select">
                <option value="" disabled>Pilih Status Kontrak</option>
                <option value="K" @selected(old('jenis_kontrak', $kontrak->jenis_kontrak) == 'K')>Kontrak (PKWT)</option>
                <option value="T" @selected(old('jenis_kontrak', $kontrak->jenis_kontrak) == 'T')>Tetap (PKWTT)</option>
            </select>
             @error('jenis_kontrak')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div id="periode_kontrak">
            <x-input-with-icon label="Tanggal Mulai" name="dari" icon="ti ti-calendar" datepicker="flatpickr-date"
                value="{{ old('dari', $kontrak->dari) }}" />
            <x-input-with-icon label="Tanggal Selesai" name="sampai" icon="ti ti-calendar" datepicker="flatpickr-date"
                value="{{ old('sampai', $kontrak->sampai) }}" />
        </div>

        <div class="form-group mb-1">
            <select name="nik" id="nik" class="form-select select2" data-placeholder="Pilih Karyawan">
                <option value="">Pilih Karyawan</option>
                @foreach ($karyawans as $karyawan)
                    <option value="{{ $karyawan->nik }}" @selected(old('nik', $kontrak->nik) == $karyawan->nik) data-kode_cabang="{{ $karyawan->kode_cabang }}"
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
                        <option value="{{ $cabang->kode_cabang }}" @selected(old('kode_cabang', $kontrak->kode_cabang) == $cabang->kode_cabang)>
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
                        <option value="{{ $dept->kode_dept }}" @selected(old('kode_dept', $kontrak->kode_dept) == $dept->kode_dept)>
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
                        <option value="{{ $jabatan->kode_jabatan }}" @selected(old('kode_jabatan', $kontrak->kode_jabatan) == $jabatan->kode_jabatan)>
                            {{ $jabatan->nama_jabatan }}
                        </option>
                    @endforeach
                </select>
            </div>
            @error('kode_jabatan')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <input type="hidden" name="status_kontrak" value="{{ old('status_kontrak', $kontrak->status_kontrak) }}">
        <input type="hidden" name="kode_gaji" id="kode_gaji" value="{{ old('kode_gaji', $kontrak->kode_gaji) }}">
        <input type="hidden" name="kode_tunjangan" id="kode_tunjangan" value="{{ old('kode_tunjangan', $kontrak->kode_tunjangan) }}">

        <x-input-with-icon label="Gaji Pokok Kontrak (opsional)" name="nominal_gaji" icon="ti ti-currency-dollar" money="true" align="right"
            value="{{ old('nominal_gaji', $kontrak->nominal_gaji) }}" />

        @if ($jenisTunjangans->count())
            @foreach ($jenisTunjangans as $index => $jenis)
                <input type="hidden" name="kode_jenis_tunjangan[]" value="{{ $jenis->kode_jenis_tunjangan }}">
                <x-input-with-icon label="{{ $jenis->jenis_tunjangan }}" name="nominal_tunjangan_detail[]" icon="ti ti-cash" money="true"
                    align="right" value="{{ old('nominal_tunjangan_detail.' . $index, $tunjanganValues[$jenis->kode_jenis_tunjangan] ?? null) }}" />
            @endforeach
        @endif
    </div>

    <div class="form-group mb-2">
        <button class="btn btn-primary w-100" id="btnUpdateKontrak">
            <i class="ti ti-device-floppy me-1"></i> Update Kontrak
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
            $('.money').maskMoney('mask');
        }

        $('#nik').on('change', function() {
            const option = $(this).find(':selected');
            const nik = $(this).val();
            const cabang = option.data('kode_cabang');
            const dept = option.data('kode_dept');
            const jabatan = option.data('kode_jabatan');

            if (cabang) {
                $("#formKontrakEdit").find('#kode_cabang').val(cabang).trigger('change');
            }
            if (dept) {
                $("#formKontrakEdit").find('#kode_dept').val(dept).trigger('change');
            }
            if (jabatan) {
                $("#formKontrakEdit").find('#kode_jabatan').val(jabatan).trigger('change');
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
                        if (res.salary.jumlah) {
                            $inputSalaryNominal.val(res.salary.jumlah);
                            if ($.fn.maskMoney) {
                                $inputSalaryNominal.maskMoney('mask');
                            }
                        }
                    } else {
                        $salaryAmount.text('-');
                        $salaryMeta.text('-');
                        $salaryCode.text('-');
                    }

                    if (res.allowance) {
                        hasData = true;
                        $allowanceAmount.text(formatRupiah(res.allowance.total));
                        $allowanceMeta.text(res.allowance.tanggal || '-');
                        $allowanceCode.text(res.allowance.kode || '-');
                        if (res.allowance.kode) {
                            $('#kode_tunjangan').val(res.allowance.kode).trigger('change');
                        }
                        if (res.allowance.details && res.allowance.details.length > 0) {
                            $('input[name="kode_jenis_tunjangan[]"]').each(function(index) {
                                const kodeJenis = $(this).val();
                                const detail = res.allowance.details.find(d => d.kode_jenis_tunjangan === kodeJenis);
                                if (detail) {
                                    const $inputDetail = $('input[name="nominal_tunjangan_detail[]"]').eq(index);
                                    $inputDetail.val(detail.jumlah);
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
                .fail(resetSummary);
        });

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
        
        // Trigger on load
        togglePeriodeKontrak();

        $("#formKontrakEdit").on('submit', function(e) {
            e.preventDefault();

            const tanggal = $('#tanggal').val();
            const dari = $('#dari').val();
            const sampai = $('#sampai').val();
            const nik = $('#nik').val();
            const kode_cabang = $('#kode_cabang').val();
            const kode_dept = $('#kode_dept').val();
            const kode_jabatan = $('#kode_jabatan').val();
            const no_kontrak = $('input[name="no_kontrak"]').val();
            const jenis_kontrak = $('#jenis_kontrak').val();

            let errors = [];

            if (!no_kontrak) {
                errors.push('Nomor Kontrak harus diisi');
            }
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

            $("#btnUpdateKontrak").attr('disabled', true).html('<i class="ti ti-loader me-1"></i> Menyimpan...');
            this.submit();
        });
    });
</script>
