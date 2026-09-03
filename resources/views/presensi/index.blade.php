@extends('layouts.app')
@section('titlepage', 'Monitoring Presensi')

@section('navigasi')
    <div class="d-flex justify-content-between align-items-center w-100">
        <div>
            <h5 class="mb-0 fw-bold text-dark">Monitoring Presensi</h5>
            <div class="text-muted" style="font-size: 0.75rem;">
                Pantau kehadiran dan status absensi seluruh karyawan secara real-time.
            </div>
        </div>
    </div>
@endsection

@section('content')
{{-- Filter Form Bar --}}
<div class="row mb-3">
    <div class="col-12">
        <div class="card shadow-sm border-0" style="border-radius: 12px;">
            <div class="card-body p-3">
                <form action="{{ route('presensi.index') }}" method="GET" class="m-0">
                    <div class="row g-2 align-items-center">
                        <div class="col-12 col-sm-6 col-md-3 col-lg-2">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="ti ti-calendar"></i></span>
                                <input type="text" class="form-control flatpickr-date" name="tanggal" 
                                    value="{{ Request('tanggal') ?? date('Y-m-d') }}" placeholder="Tanggal" />
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-3 col-lg-2">
                            <select name="kode_cabang" id="kode_cabang" class="form-select select2Kodecabangsearch">
                                <option value="">Semua Cabang</option>
                                @foreach ($cabang as $c)
                                    <option value="{{ $c->kode_cabang }}" @selected(Request('kode_cabang') == $c->kode_cabang)>
                                        {{ strtoupper($c->nama_cabang) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-3 col-lg-2">
                            <select name="kode_dept" id="kode_dept" class="form-select select2Kodedeptsearch">
                                <option value="">Semua Departemen</option>
                                @foreach ($departemen as $d)
                                    <option value="{{ $d->kode_dept }}" @selected(Request('kode_dept') == $d->kode_dept)>
                                        {{ strtoupper($d->nama_dept) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-3 col-lg-4">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="ti ti-search"></i></span>
                                <input type="text" class="form-control" name="nama_karyawan" 
                                    value="{{ Request('nama_karyawan') }}" placeholder="Cari Nama Karyawan..." />
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-2 d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1" title="Cari Data">
                                <i class="ti ti-search me-1"></i> Cari
                            </button>
                            <a href="{{ route('presensi.index') }}" class="btn btn-label-secondary" title="Reset Filter">
                                <i class="ti ti-refresh"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Employee Presensi Cards --}}
<div class="row">
    <div class="col-12">
        <div class="row g-2">
            @forelse ($karyawan as $d)
                @php
                    $tanggal_presensi = !empty(Request('tanggal')) ? Request('tanggal') : date('Y-m-d');
                    $jam_masuk = $tanggal_presensi . ' ' . $d->jam_masuk;
                    $terlambat = hitungjamterlambat($d->jam_in, $jam_masuk);
                    $potongan_tidak_hadir = $d->status == 'a' ? $d->total_jam : 0;
                    $pulangcepat = hitungpulangcepat(
                        $tanggal_presensi,
                        $d->jam_out,
                        $d->jam_pulang,
                        $d->istirahat,
                        $d->jam_awal_istirahat,
                        $d->jam_akhir_istirahat,
                        $d->lintashari,
                    );

                    // Jika denda sudah ada di tabel presensi
                    if ($d->denda !== null) {
                        $denda = $d->denda;
                        if ($terlambat != null) {
                            $potongan_jam_terlambat = $terlambat['desimal_terlambat'] >= 1 ? $terlambat['desimal_terlambat'] : 0;
                        } else {
                            $potongan_jam_terlambat = 0;
                        }
                    } else {
                        if ($terlambat != null) {
                            if ($terlambat['desimal_terlambat'] < 1) {
                                $potongan_jam_terlambat = 0;
                                $denda = hitungdenda($denda_list, $terlambat['menitterlambat']);
                            } else {
                                $potongan_jam_terlambat = $terlambat['desimal_terlambat'];
                                $denda = 0;
                            }
                        } else {
                            $potongan_jam_terlambat = 0;
                            $denda = 0;
                        }
                    }
                    
                    $total_potongan_jam = $pulangcepat + $potongan_jam_terlambat + $potongan_tidak_hadir;
                @endphp
                <div class="col-12">
                    <div class="card shadow-xs border card-hover" style="border-radius: 12px;">
                        <div class="card-body p-3">
                            {{-- Header Baris 1: Identitas & Status --}}
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2 pb-2 border-bottom">
                                <div class="d-flex align-items-center flex-wrap gap-2">
                                    <div class="avatar avatar-sm rounded-circle bg-label-primary d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                        <i class="ti ti-user fs-5"></i>
                                    </div>
                                    <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                        <span class="fw-bold text-dark" style="font-size: 13.5px;">{{ $d->nama_karyawan }}</span>
                                        <span class="badge bg-label-secondary py-0.5 px-1.5 text-muted" style="font-size: 11px;">
                                            <i class="ti ti-id me-1"></i>{{ $d->nik_show ?? $d->nik }}
                                        </span>
                                        <span class="badge bg-label-info py-0.5 px-2 rounded-pill" style="font-size: 10.5px;">
                                            <i class="ti ti-building me-1"></i>{{ $d->kode_dept }}
                                        </span>
                                        <span class="badge bg-label-warning py-0.5 px-2 rounded-pill" style="font-size: 10.5px;">
                                            <i class="ti ti-map-pin me-1"></i>{{ $d->kode_cabang }}
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="d-flex align-items-center gap-2">
                                    {{-- Status Absen --}}
                                    <div>
                                        @if ($d->status == 'h')
                                            <span class="badge bg-label-success rounded-pill px-2.5 py-1 fw-bold" style="font-size: 11.5px;">
                                                <i class="ti ti-check me-1"></i>Hadir
                                            </span>
                                        @elseif($d->status == 'i')
                                            <span class="badge bg-label-info rounded-pill px-2.5 py-1 fw-bold" style="font-size: 11.5px;">
                                                <i class="ti ti-file-info me-1"></i>Izin
                                            </span>
                                        @elseif($d->status == 's')
                                            <span class="badge bg-label-warning rounded-pill px-2.5 py-1 fw-bold" style="font-size: 11.5px;">
                                                <i class="ti ti-ambulance me-1"></i>Sakit
                                            </span>
                                        @elseif($d->status == 'a')
                                            <span class="badge bg-label-danger rounded-pill px-2.5 py-1 fw-bold" style="font-size: 11.5px;">
                                                <i class="ti ti-x me-1"></i>Alpa
                                            </span>
                                        @elseif($d->status == 'c')
                                            <span class="badge bg-label-primary rounded-pill px-2.5 py-1 fw-bold" style="font-size: 11.5px;">
                                                <i class="ti ti-calendar-event me-1"></i>Cuti
                                            </span>
                                        @else
                                            <span class="badge bg-label-secondary rounded-pill px-2.5 py-1 fw-semibold text-muted" style="font-size: 11.5px;">
                                                <i class="ti ti-minus me-1"></i>Belum Absen
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Actions (Outlined Buttons like screenshot) --}}
                                    <div class="d-flex align-items-center gap-1">
                                        @if (isset($d->status_potongan))
                                            <button class="btn btn-xs btn-icon btn-label-dark" disabled title="Terkunci"><i class="ti ti-lock fs-6"></i></button>
                                        @else
                                            <a href="#" class="btn btn-xs btn-icon btn-outline-success koreksiPresensi rounded-2" 
                                                nik="{{ Crypt::encrypt($d->nik) }}" tanggal="{{ $tanggal_presensi }}" title="Koreksi Presensi">
                                                <i class="ti ti-edit fs-6"></i>
                                            </a>

                                            @if(!empty($d->id))
                                            <form action="{{ route('presensi.delete', $d->id) }}" method="POST" style="display:inline-block;" class="delete-form m-0">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-xs btn-icon btn-outline-danger delete-confirm rounded-2" title="Hapus Data">
                                                    <i class="ti ti-trash fs-6"></i>
                                                </button>
                                            </form>
                                            @endif
                                        @endif

                                        <a href="#" class="btn btn-xs btn-icon btn-outline-primary btngetDatamesin rounded-2" 
                                            pin="{{ $d->pin }}" tanggal="{{ !empty(Request('tanggal')) ? Request('tanggal') : date('Y-m-d') }}" title="Log Mesin">
                                            <i class="ti ti-device-desktop fs-6"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            {{-- Row 2: Metrics Grid --}}
                            <div class="row g-2 align-items-center text-muted" style="font-size: 0.825rem;">
                                {{-- Jadwal --}}
                                <div class="col-6 col-sm-4 col-md-2 d-flex align-items-center border-end">
                                    <div class="avatar avatar-xs me-2 bg-light rounded text-muted d-flex align-items-center justify-content-center">
                                        <i class="ti ti-clock fs-6"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <small class="d-block text-muted" style="font-size: 10px;">Jadwal</small>
                                        <span class="fw-semibold text-dark text-truncate d-block">
                                            @if ($d->kode_jam_kerja != null)
                                                {{ date('H:i', strtotime($d->jam_masuk)) }} - {{ date('H:i', strtotime($d->jam_pulang)) }}
                                            @else
                                                -
                                            @endif
                                        </span>
                                    </div>
                                </div>

                                {{-- Masuk --}}
                                <div class="col-6 col-sm-4 col-md-2 d-flex align-items-center border-end">
                                    <div class="avatar avatar-xs me-2 bg-success-subtle rounded text-success d-flex align-items-center justify-content-center">
                                        <i class="ti ti-login fs-6"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <small class="d-block text-muted" style="font-size: 10px;">Masuk</small>
                                        @if ($d->jam_in != null)
                                            <a href="#" class="btnShowpresensi_in fw-bold text-dark text-decoration-none" id="{{ $d->id }}" status="in">
                                                {{ date('H:i', strtotime($d->jam_in)) }}
                                            </a>
                                            @if (!empty($d->foto_in))
                                                <i class="ti ti-photo text-primary ms-1" style="font-size:11px" title="Ada Foto"></i>
                                            @endif
                                            @if ($potongan_jam_terlambat > 0)
                                                <span class="text-danger ms-0.5 fw-bold" style="font-size: 10px;">(-{{ $potongan_jam_terlambat }})</span>
                                            @endif
                                        @else
                                            <span class="fw-medium text-dark">-</span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Pulang --}}
                                <div class="col-6 col-sm-4 col-md-2 d-flex align-items-center border-end">
                                    <div class="avatar avatar-xs me-2 bg-danger-subtle rounded text-danger d-flex align-items-center justify-content-center">
                                        <i class="ti ti-logout fs-6"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <small class="d-block text-muted" style="font-size: 10px;">Pulang</small>
                                        @if ($d->jam_out != null)
                                            <a href="#" class="btnShowpresensi_out fw-bold text-dark text-decoration-none" id="{{ $d->id }}" status="out">
                                                {{ date('H:i', strtotime($d->jam_out)) }}
                                            </a>
                                            @if (!empty($d->foto_out))
                                                <i class="ti ti-photo text-primary ms-1" style="font-size:11px" title="Ada Foto"></i>
                                            @endif
                                            @if ($pulangcepat > 0)
                                                <span class="text-danger ms-0.5 fw-bold" style="font-size: 10px;">(-{{ $pulangcepat }})</span>
                                            @endif
                                        @else
                                            <span class="fw-medium text-dark">-</span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Terlambat --}}
                                <div class="col-6 col-sm-4 col-md-2 d-flex align-items-center border-end">
                                    <div class="avatar avatar-xs me-2 bg-warning-subtle rounded text-warning d-flex align-items-center justify-content-center">
                                        <i class="ti ti-clock-exclamation fs-6"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <small class="d-block text-muted" style="font-size: 10px;">Terlambat</small>
                                        @if($terlambat != null)
                                            <span class="fw-bold text-danger">{!! $terlambat['show'] !!}</span>
                                        @else
                                            <span class="text-success fw-semibold"><i class="ti ti-check"></i> Tepat Waktu</span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Denda --}}
                                <div class="col-6 col-sm-4 col-md-2 d-flex align-items-center border-end">
                                    <div class="avatar avatar-xs me-2 bg-danger-subtle rounded text-danger d-flex align-items-center justify-content-center">
                                        <i class="ti ti-coin fs-6"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <small class="d-block text-muted" style="font-size: 10px;">Denda</small>
                                        <span class="fw-bold {{ empty($denda) ? 'text-dark' : 'text-danger' }}">
                                            {{ empty($denda) ? '0' : formatAngka($denda) }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Potongan --}}
                                <div class="col-6 col-sm-4 col-md-2 d-flex align-items-center">
                                    <div class="avatar avatar-xs me-2 bg-dark-subtle rounded text-dark d-flex align-items-center justify-content-center">
                                        <i class="ti ti-cut fs-6"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <small class="d-block text-muted" style="font-size: 10px;">Potongan</small>
                                        @if ($total_potongan_jam > 0)
                                            <span class="badge bg-danger py-0.5 px-2 rounded-pill">
                                                {{ formatAngkaDesimal($total_potongan_jam) }} Jam
                                            </span>
                                        @else
                                            <span class="text-success fw-semibold">0 Jam</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card shadow-sm border text-center py-5">
                        <div class="text-muted">
                            <i class="ti ti-calendar-off fs-1 d-block mb-2 text-secondary"></i>
                            <h6 class="text-muted mb-0">Tidak ada data presensi untuk filter yang dipilih.</h6>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>

        <div class="mt-3 d-flex justify-content-end">
            {{ $karyawan->links() }}
        </div>
    </div>
</div>
<x-modal-form id="modal" size="modal-xl" show="loadmodal" title="" />
@endsection
@push('myscript')
<script>
    $(function() {
        if ($.fn.select2) {
            $('.select2Kodecabangsearch, .select2Kodedeptsearch').select2({
                width: '100%'
            });
        }

        $(document).on('click', '.koreksiPresensi', function() {
            let nik = $(this).attr('nik');
            let tanggal = $(this).attr('tanggal');
            $.ajax({
                type: 'POST',
                url: "{{ route('presensi.edit') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    nik: nik,
                    tanggal: tanggal
                },
                cache: false,
                success: function(res) {
                    $('#modal').modal('show');
                    $('#modal').find('.modal-title').text('Koreksi Presensi');
                    $('#loadmodal').html(res);
                }
            });
        });

        $(".btnShowpresensi_in, .btnShowpresensi_out").click(function(e) {
            e.preventDefault();
            const id = $(this).attr("id");
            const status = $(this).attr("status");
            $("#loadmodal").html(`<div class="sk-wave sk-primary" style="margin:auto">
                <div class="sk-wave-rect"></div>
                <div class="sk-wave-rect"></div>
                <div class="sk-wave-rect"></div>
                <div class="sk-wave-rect"></div>
                <div class="sk-wave-rect"></div>
            </div>`);
            $("#modal").modal("show");
            $(".modal-title").text("Data Presensi");
            $("#loadmodal").load(`/presensi/${id}/${status}/show`);
        });

        $(".btngetDatamesin").click(function(e) {
            e.preventDefault();
            var pin = $(this).attr("pin");
            var tanggal = $(this).attr("tanggal");
            $("#loadmodal").html(`<div class="sk-wave sk-primary" style="margin:auto">
            <div class="sk-wave-rect"></div>
            <div class="sk-wave-rect"></div>
            <div class="sk-wave-rect"></div>
            <div class="sk-wave-rect"></div>
            <div class="sk-wave-rect"></div>
            </div>`);
            $("#modal").modal("show");
            $(".modal-title").text("Get Data Mesin");
            $.ajax({
                type: 'POST',
                url: '/presensi/getdatamesin',
                data: {
                    _token: "{{ csrf_token() }}",
                    pin: pin,
                    tanggal: tanggal
                },
                cache: false,
                success: function(respond) {
                    console.log(respond);
                    $("#loadmodal").html(respond);
                }
            });
        });
        
        $(".delete-confirm").click(function(e) {
            var form = $(this).closest('form');
            e.preventDefault();
            Swal.fire({
                title: 'Apakah Anda Yakin Data Ini Akan Dihapus ?',
                text: "Jika Dihapus Maka Data Akan Hilang ",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus Saja!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            })
        });
    });
</script>
@endpush
