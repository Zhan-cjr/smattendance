@extends('layouts.app')
@section('titlepage', 'Monitoring Presensi')

@section('content')
@section('navigasi')
    <span>Monitoring Presensi</span>
@endsection
<div class="row">
    <div class="col-lg-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-header">
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <form action="{{ route('presensi.index') }}" method="GET">
                            <div class="row">
                                <div class="col-lg-2 col-md-12 col-sm-12">
                                    <x-input-with-icon label="" value="{{ Request('tanggal') }}" name="tanggal" icon="ti ti-calendar"
                                        datepicker="flatpickr-date" placeholder="Tanggal" />
                                </div>
                                <div class="col-lg-2 col-md-12 col-sm-12">
                                    <div class="form-group mb-3">
                                        <x-select label="" name="kode_cabang" :data="$cabang" key="kode_cabang" textShow="nama_cabang"
                                            selected="{{ Request('kode_cabang') }}" upperCase="true" select2="select2Kodecabangsearch"
                                            placeholder="Cabang" />
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-12 col-sm-12">
                                    <div class="form-group mb-3">
                                        <x-select label="" name="kode_dept" :data="$departemen" key="kode_dept" textShow="nama_dept"
                                            selected="{{ Request('kode_dept') }}" upperCase="true" select2="select2Kodedeptsearch"
                                            placeholder="Departemen" />
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-12 col-sm-12">
                                    <x-input-with-icon label="" value="{{ Request('nama_karyawan') }}" name="nama_karyawan" icon="ti ti-search"
                                        placeholder="Cari Nama Karyawan" />
                                </div>
                                <div class="col-lg-1 col-md-12 col-sm-12">
                                    <div class="form-group mb-3">
                                        <button type="submit" class="btn btn-primary w-100"><i class="ti ti-icons ti-search me-1"></i></button>
                                    </div>
                                </div>
                                <div class="col-lg-1 col-md-12 col-sm-12">
                                    <div class="form-group mb-3">
                                        <a href="{{ route('presensi.index') }}" class="btn btn-secondary w-100"><i class="ti ti-refresh me-1"></i></a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="row">
                            @foreach ($karyawan as $d)
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

                                    // Jika denda sudah ada di tabel presensi (laporan sudah dikunci), gunakan nilai tersebut
                                    if ($d->denda !== null) {
                                        $denda = $d->denda;
                                        // Tetap hitung potongan jam terlambat untuk display
                                        if ($terlambat != null) {
                                            $potongan_jam_terlambat =
                                                $terlambat['desimal_terlambat'] >= 1 ? $terlambat['desimal_terlambat'] : 0;
                                        } else {
                                            $potongan_jam_terlambat = 0;
                                        }
                                    } else {
                                        // Jika denda belum ada, hitung dengan rumus
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
                                    <div class="card mb-2 shadow-sm border card-hover">
                                        <div class="card-body p-3">
                                            {{-- Row 1: Header Info & Status --}}
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <div class="d-flex align-items-center flex-wrap gap-2">
                                                    <span class="avatar avatar-xs rounded bg-primary-subtle text-primary">
                                                        <i class="ti ti-user fs-5"></i>
                                                    </span>
                                                    <span class="fw-bold text-dark">{{ $d->nama_karyawan }}</span>
                                                    <span class="text-muted" style="font-size: 0.85rem;"><i class="ti ti-id me-1"></i>{{ $d->nik_show ?? $d->nik }}</span>
                                                    <span class="badge bg-label-secondary rounded-pill"><i class="ti ti-building me-1"></i>{{ $d->kode_dept }}</span>
                                                    <span class="badge bg-label-secondary rounded-pill"><i class="ti ti-map-pin me-1"></i>{{ $d->kode_cabang }}</span>
                                                </div>
                                                
                                                <div class="d-flex align-items-center gap-2">
                                                    <div>
                                                        @if ($d->status == 'h')
                                                            <span class="badge bg-success bg-glow"><i class="ti ti-check me-1"></i>Hadir</span>
                                                        @elseif($d->status == 'i')
                                                            <span class="badge bg-info bg-glow"><i class="ti ti-file-info me-1"></i>Izin</span>
                                                        @elseif($d->status == 's')
                                                            <span class="badge bg-warning bg-glow"><i class="ti ti-ambulance me-1"></i>Sakit</span>
                                                        @elseif($d->status == 'a')
                                                            <span class="badge bg-danger bg-glow"><i class="ti ti-x me-1"></i>Alpa</span>
                                                        @elseif($d->status == 'c')
                                                            <span class="badge bg-primary bg-glow"><i class="ti ti-calendar-event me-1"></i>Cuti</span>
                                                        @else
                                                            <span class="badge bg-secondary"><i class="ti ti-minus me-1"></i>Belum Absen</span>
                                                        @endif
                                                    </div>

                                                    {{-- Actions --}}
                                                    <div class="d-flex gap-1">
                                                        @if (isset($d->status_potongan))
                                                            <button class="btn btn-sm btn-icon btn-dark" disabled title="Terkunci"><i class="ti ti-lock"></i></button>
                                                        @else
                                                            <a href="#" class="btn btn-sm btn-icon btn-outline-success koreksiPresensi" nik="{{ Crypt::encrypt($d->nik) }}"
                                                                tanggal="{{ $tanggal_presensi }}" title="Koreksi"><i class="ti ti-edit"></i></a>

                                                            @if(!empty($d->id))
                                                            <form action="{{ route('presensi.delete', $d->id) }}" method="POST"
                                                                style="display:inline-block;" class="delete-form">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-icon btn-outline-danger delete-confirm"
                                                                    title="Hapus"><i class="ti ti-trash"></i></button>
                                                            </form>
                                                            @endif
                                                        @endif
    
                                                        <a href="#" class="btn btn-sm btn-icon btn-outline-primary btngetDatamesin" pin="{{ $d->pin }}"
                                                            tanggal="{{ !empty(Request('tanggal')) ? Request('tanggal') : date('Y-m-d') }}" title="Log Mesin"> <i
                                                                class="ti ti-device-desktop"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>

                                            <hr class="my-2 border-dashed">

                                            {{-- Row 2: Details Grid --}}
                                            <div class="row g-2 text-muted" style="font-size: 0.85rem;">
                                                {{-- Jadwal --}}
                                                <div class="col-6 col-md-2 d-flex align-items-center border-end">
                                                    <div class="avatar avatar-xs me-2 bg-light rounded text-muted">
                                                        <i class="ti ti-clock"></i>
                                                    </div>
                                                    <div>
                                                        <small class="d-block text-muted">Jadwal</small>
                                                        <span class="fw-medium text-dark">
                                                            @if ($d->kode_jam_kerja != null)
                                                                <span class="d-block text-primary small fw-bold">{{ $d->nama_jam_kerja }}</span>
                                                                {{ date('H:i', strtotime($d->jam_masuk)) }} - {{ date('H:i', strtotime($d->jam_pulang)) }}
                                                            @else
                                                                -
                                                            @endif
                                                        </span>
                                                    </div>
                                                </div>

                                                {{-- Jam Masuk --}}
                                                <div class="col-6 col-md-2 d-flex align-items-center border-end">
                                                    <div class="avatar avatar-xs me-2 bg-success-subtle rounded text-success">
                                                        <i class="ti ti-login"></i>
                                                    </div>
                                                    <div>
                                                        <small class="d-block text-muted">Masuk</small>
                                                        @if ($d->jam_in != null)
                                                            <a href="#" class="btnShowpresensi_in fw-bold text-dark text-decoration-none" id="{{ $d->id }}" status="in">
                                                                {{ date('H:i', strtotime($d->jam_in)) }}
                                                            </a>
                                                            @if (!empty($d->foto_in))
                                                                <i class="ti ti-photo text-primary ms-1" style="font-size:10px" title="Ada Foto"></i>
                                                            @endif
                                                            <span class="text-danger ms-1" style="font-size:10px">
                                                                @if ($potongan_jam_terlambat > 0)
                                                                    (-{{ $potongan_jam_terlambat }})
                                                                @endif
                                                            </span>
                                                        @else
                                                            -
                                                        @endif
                                                    </div>
                                                </div>

                                                {{-- Jam Pulang --}}
                                                <div class="col-6 col-md-2 d-flex align-items-center border-end">
                                                    <div class="avatar avatar-xs me-2 bg-danger-subtle rounded text-danger">
                                                        <i class="ti ti-logout"></i>
                                                    </div>
                                                    <div>
                                                        <small class="d-block text-muted">Pulang</small>
                                                        @if ($d->jam_out != null)
                                                            <a href="#" class="btnShowpresensi_out fw-bold text-dark text-decoration-none" id="{{ $d->id }}" status="out">
                                                                {{ date('H:i', strtotime($d->jam_out)) }}
                                                            </a>
                                                            @if (!empty($d->foto_out))
                                                                <i class="ti ti-photo text-primary ms-1" style="font-size:10px" title="Ada Foto"></i>
                                                            @endif
                                                            <span class="text-danger ms-1" style="font-size:10px">
                                                                @if ($pulangcepat > 0)
                                                                    (-{{ $pulangcepat }})
                                                                @endif
                                                            </span>
                                                        @else
                                                            -
                                                        @endif
                                                    </div>
                                                </div>

                                                 {{-- Terlambat --}}
                                                 <div class="col-6 col-md-2 d-flex align-items-center border-end">
                                                    <div class="avatar avatar-xs me-2 bg-warning-subtle rounded text-warning">
                                                        <i class="ti ti-clock-exclamation"></i>
                                                    </div>
                                                    <div>
                                                        <small class="d-block text-muted">Terlambat</small>
                                                        @if($terlambat != null)
                                                            <span class="fw-medium text-danger">{!! $terlambat['show'] !!}</span>
                                                        @else
                                                             <span class="text-success fw-medium"><i class="ti ti-check"></i> Tepat Waktu</span>
                                                        @endif
                                                    </div>
                                                </div>

                                                {{-- Denda --}}
                                                <div class="col-6 col-md-2 d-flex align-items-center border-end">
                                                    <div class="avatar avatar-xs me-2 bg-danger-subtle rounded text-danger">
                                                        <i class="ti ti-coin"></i>
                                                    </div>
                                                    <div>
                                                        <small class="d-block text-muted">Denda</small>
                                                        <span class="fw-bold text-danger">{{ empty($denda) ? '0' : formatAngka($denda) }}</span>
                                                    </div>
                                                </div>

                                                {{-- Potongan Jam --}}
                                                <div class="col-6 col-md-2 d-flex align-items-center">
                                                    <div class="avatar avatar-xs me-2 bg-dark-subtle rounded text-dark">
                                                        <i class="ti ti-cut"></i>
                                                    </div>
                                                    <div>
                                                        <small class="d-block text-muted">Potongan</small>
                                                        @if ($total_potongan_jam > 0)
                                                            <span class="badge bg-danger rounded-pill">
                                                                {{ formatAngkaDesimal($total_potongan_jam) }} Jam
                                                            </span>
                                                        @else
                                                            <span class="text-success fw-medium">0 Jam</span>
                                                        @endif
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div style="float: right;">
                            {{ $karyawan->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<x-modal-form id="modal" size="modal-xl" show="loadmodal" title="" />
@endsection
@push('myscript')
<script>
    $(function() {
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
