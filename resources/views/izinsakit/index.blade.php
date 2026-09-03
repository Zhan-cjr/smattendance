@extends('layouts.app')
@section('titlepage', 'Izin sakit')

@section('content')
@section('navigasi')
    <span>Izin sakit</span>
@endsection
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <div class="nav-align-top nav-tabs-shadow mb-4">
            @include('layouts.navigation.nav_pengajuan_absen')
            <div class="tab-content">
                <div class="tab-pane fade active show" id="navs-justified-home" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            @can('izinsakit.create')
                                <a href="#" class="btn btn-primary" id="btnCreate"><i class="fa fa-plus me-2"></i>
                                    Tambah Data</a>
                            @endcan
                        </div>
                        <div>
                            @can('approvallayer.index')
                                <a href="{{ route('approvallayer.index') }}" class="btn btn-info"><i class="fa fa-cog me-2"></i>
                                    Konfigurasi Approval</a>
                            @endcan
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-12">
                            <form action="{{ route('izinsakit.index') }}">
                                <div class="row g-2">
                                    <div class="col-lg-6 col-sm-12 col-md-12">
                                        <x-input-with-icon label="Dari" value="{{ Request('dari') }}" name="dari" icon="ti ti-calendar"
                                            datepicker="flatpickr-date" hideLabel />
                                    </div>
                                    <div class="col-lg-6 col-sm-12 col-md-12">
                                        <x-input-with-icon label="Sampai" value="{{ Request('sampai') }}" name="sampai" icon="ti ti-calendar"
                                            datepicker="flatpickr-date" hideLabel />
                                    </div>
                                </div>
                                <div class="row g-2">
                                    <div class="col-12">
                                        <x-input-with-icon label="Nama Karyawan" value="{{ Request('nama_karyawan') }}" name="nama_karyawan"
                                            icon="ti ti-search" hideLabel />
                                    </div>
                                </div>
                                <div class="row g-2">
                                    <div class="col-lg-3 col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <select name="status" id="status" class="form-select">
                                                <option value="">Status</option>
                                                <option value="0" {{ Request('status') === '0' ? 'selected' : '' }}>Pending</option>
                                                <option value="1" {{ Request('status') == '1' ? 'selected' : '' }}>Disetujui</option>
                                                <option value="2" {{ Request('status') == '2' ? 'selected' : '' }}>Ditolak</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6 col-sm-12">
                                        <x-select label="Cabang" name="kode_cabang" :data="$cabang" key="kode_cabang" textShow="nama_cabang"
                                            selected="{{ Request('kode_cabang') }}" upperCase="true" hideLabel />
                                    </div>
                                    <div class="col-lg-4 col-md-6 col-sm-12">
                                        <x-select label="Departemen" name="kode_dept" :data="$departemen" key="kode_dept" textShow="nama_dept"
                                            selected="{{ Request('kode_dept') }}" upperCase="true" hideLabel />
                                    </div>
                                    <div class="col-lg-1 col-md-12 col-sm-12">
                                        <button class="btn btn-primary w-100"><i class="ti ti-search"></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-12">
                            @forelse ($izinsakit as $d)
                                @php
                                    $lama = hitungHari($d->dari, $d->sampai);
                                @endphp
                                <div class="card mb-2 shadow-sm border">
                                    <div class="card-body p-2">
                                        <div class="row align-items-center">
                                            <!-- Avatar -->
                                            <div class="col-md-1 text-center" style="width: 60px;">
                                                @php
                                                    $path = Storage::url('karyawan/'.$d->foto);
                                                @endphp
                                                @if (!empty($d->foto) && Storage::disk('public')->exists('/karyawan/' . $d->foto))
                                                    <img src="{{ $path }}" alt="Avatar"
                                                        class="rounded-circle"
                                                        style="width: 40px; height: 40px; object-fit: cover;">
                                                @else
                                                    <img src="{{ asset('assets/img/avatars/No_Image_Available.jpg') }}"
                                                        alt="No Image" class="rounded-circle"
                                                        style="width: 40px; height: 40px; object-fit: cover;">
                                                @endif
                                            </div>
                                            <!-- Identity -->
                                            <div class="col-md-4">
                                                <div class="fw-bold text-dark" style="font-size: 14px;">{{ $d->nama_karyawan }} <span class="text-muted fw-normal" style="font-size: 12px;">({{ $d->nik_show ?? $d->nik }})</span></div>
                                                <div class="mt-1">
                                                    <span class="badge bg-label-success" style="font-size: 10px;">{{ $d->nama_jabatan }}</span>
                                                    <span class="badge bg-label-info" style="font-size: 10px;">{{ $d->nama_dept }}</span>
                                                    <span class="badge bg-label-warning" style="font-size: 10px;">{{ $d->nama_cabang }}</span>
                                                </div>
                                                @if (!empty($d->doc_sid))
                                                    <div class="mt-1 d-block d-md-none">
                                                        @if (Storage::disk('public')->exists('/uploads/sid/' . $d->doc_sid))
                                                            <a href="{{ url('storage/uploads/sid/'.$d->doc_sid) }}" target="_blank" class="text-primary" style="font-size: 11px;">
                                                                <i class="ti ti-file-text me-1"></i>SID
                                                            </a>
                                                        @else
                                                             <span class="text-danger" style="font-size: 11px;" title="File tidak ditemukan">
                                                                <i class="ti ti-file-x me-1"></i>SID
                                                            </span>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                            <!-- Date & Generic Info -->
                                            <div class="col-md-3 border-start border-end d-none d-md-block text-center">
                                                 <div class="fw-bold text-dark" style="font-size: 13px;">{{ date('d-m-Y', strtotime($d->dari)) }} s/d {{ date('d-m-Y', strtotime($d->sampai)) }}</div>
                                                 <div class="text-muted" style="font-size: 11px;">
                                                    {{ $d->kode_izin_sakit }} <span class="mx-1">•</span> {{ $lama }} Hari
                                                    @if (!empty($d->doc_sid))
                                                        <span class="mx-1">•</span>
                                                        @if (Storage::disk('public')->exists('/uploads/sid/' . $d->doc_sid))
                                                            <a href="{{ url('storage/uploads/sid/'.$d->doc_sid) }}" target="_blank" class="text-primary" title="Lihat SID">
                                                                <i class="ti ti-file-text me-1"></i>SID
                                                            </a>
                                                        @else
                                                            <span class="text-danger" title="File tidak ditemukan">
                                                                <i class="ti ti-file-x me-1"></i>SID
                                                            </span>
                                                        @endif
                                                    @endif
                                                 </div>
                                            </div>
                                            
                                            <!-- Status -->
                                            <div class="col-md-2 text-center">
                                                @if ($d->status == 0)
                                                    @php
                                                        $nextLayer = $d->getNextApprovalLayer();
                                                    @endphp
                                                    <span class="badge bg-label-warning py-1 px-2" style="font-size: 11px;">
                                                        <i class="ti ti-hourglass-empty me-1"></i> Pending
                                                    </span>
                                                    @if ($nextLayer)
                                                        <div class="text-muted mt-1" style="font-size: 10px; line-height: 1;">
                                                            Menunggu: {{ $nextLayer->role_name }}
                                                        </div>
                                                    @endif
                                                @elseif ($d->status == 1)
                                                    <span class="badge bg-success py-1 px-2" style="font-size: 11px;">Disetujui</span>
                                                @elseif ($d->status == 2)
                                                    <span class="badge bg-danger py-1 px-2" style="font-size: 11px;">Ditolak</span>
                                                @endif
                                            </div>

                                            <!-- Actions -->
                                            <div class="col-md-2 text-end">
                                               <div class="btn-group" role="group">
                                                @can('izinsakit.approve')
                                                    @if ($d->status == 0)
                                                        @php
                                                            $nextLayer = $d->getNextApprovalLayer();
                                                            $userRole = auth()->user()->getRoleNames()->first();
                                                            $canApprove = false;
                                                            if(auth()->user()->hasRole('super admin') || ($nextLayer && $nextLayer->role_name == $userRole)){
                                                                $canApprove = true;
                                                            }
                                                            $canCancel = false;
                                                            if($d->approval_step > 1) {
                                                                $lastStep = $d->approval_step - 1;
                                                                $lastApproval = $d->approvals->where('level', $lastStep)->where('user_id', auth()->id())->first();
                                                                if($lastApproval) {
                                                                    $canCancel = true;
                                                                }
                                                            }
                                                        @endphp
                                                        
                                                        @if($canApprove)
                                                            <a href="#" class="btn btn-sm btn-outline-primary btnApprove py-1 px-2 rounded-0"
                                                                kode_izin_sakit="{{ Crypt::encrypt($d->kode_izin_sakit) }}">
                                                                <i class="ti ti-external-link"></i>
                                                            </a>
                                                        @endif

                                                        @if($canCancel)
                                                            <form method="POST" name="deleteform" class="deleteform d-inline"
                                                                action="{{ route('izinsakit.cancelapprove', Crypt::encrypt($d->kode_izin_sakit)) }}">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-outline-warning cancel-confirm py-1 px-2 rounded-0" title="Batalkan Approval">
                                                                    <i class="ti ti-arrow-back-up"></i>
                                                                </button>
                                                            </form>
                                                        @endif

                                                    @elseif($d->status == 1)
                                                        <form method="POST" name="deleteform" class="deleteform d-inline"
                                                            action="{{ route('izinsakit.cancelapprove', Crypt::encrypt($d->kode_izin_sakit)) }}">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger cancel-confirm py-1 px-2 rounded-0">
                                                                <i class="ti ti-circle-minus"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endcan
                                                @can('izinsakit.edit')
                                                    @if ($d->status == 0)
                                                        <a href="#" class="btn btn-sm btn-outline-success btnEdit py-1 px-2 rounded-0"
                                                            kode_izin_sakit="{{ Crypt::encrypt($d->kode_izin_sakit) }}"><i
                                                                class="ti ti-edit"></i></a>
                                                    @endif
                                                @endcan
                                                @can('izinsakit.index')
                                                    <a href="#" class="btn btn-sm btn-outline-info btnShow py-1 px-2 rounded-0"
                                                        kode_izin_sakit="{{ Crypt::encrypt($d->kode_izin_sakit) }}"><i
                                                            class="ti ti-file-description"></i></a>
                                                @endcan
                                                @can('izinsakit.delete')
                                                    @if ($d->status == 0)
                                                        <form method="POST" name="deleteform" class="deleteform d-inline"
                                                            action="{{ route('izinsakit.delete', Crypt::encrypt($d->kode_izin_sakit)) }}">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger delete-confirm py-1 px-2 rounded-0">
                                                                <i class="ti ti-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endcan
                                               </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="card">
                                    <div class="card-body text-center p-5">
                                        <div class="mb-3">
                                            <i class="ti ti-file-x text-muted" style="font-size: 6rem;"></i>
                                        </div>
                                        <h4 class="mb-1 text-muted">Belum ada data</h4>
                                        <p class="text-secondary">Data izin sakit belum tersedia untuk periode atau filter yang dipilih.</p>
                                    </div>
                                </div>
                            @endforelse
                            <div style="float: right;">
                                {{ $izinsakit->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<x-modal-form id="modal" size="" show="loadmodal" title="" />
@endsection
@push('myscript')
<script>
    $(function() {


        function loading() {
            $("#loadmodal").html(
                `<div class="sk-wave sk-primary" style="margin:auto">
            <div class="sk-wave-rect"></div>
            <div class="sk-wave-rect"></div>
            <div class="sk-wave-rect"></div>
            <div class="sk-wave-rect"></div>
            <div class="sk-wave-rect"></div>
            </div>`
            );
        }

        function loading() {
            $("#loadmodal").html(
                `<div class="sk-wave sk-primary" style="margin:auto">
            <div class="sk-wave-rect"></div>
            <div class="sk-wave-rect"></div>
            <div class="sk-wave-rect"></div>
            <div class="sk-wave-rect"></div>
            <div class="sk-wave-rect"></div>
            </div>`
            );
        }

        $("#btnCreate").click(function() {
            $("#modal").modal("show");
            loading();
            $("#modal").find(".modal-title").text("Buat Izin Sakit");
            $("#loadmodal").load("/izinsakit/create");
        });

        $(".btnApprove").click(function() {
            const kode_izin_sakit = $(this).attr("kode_izin_sakit");
            $("#modal").modal("show");
            loading();
            $("#modal").find(".modal-title").text("Approve Izin Sakit");
            $("#loadmodal").load(`/izinsakit/${kode_izin_sakit}/approve`);
        });

        $(".btnShow").click(function() {
            const kode_izin_sakit = $(this).attr("kode_izin_sakit");
            $("#modal").modal("show");
            loading();
            $("#modal").find(".modal-title").text("Detail Izin Sakit");
            $("#loadmodal").load(`/izinsakit/${kode_izin_sakit}/show`);
        });


        $(".btnEdit").click(function() {
            const kode_izin_sakit = $(this).attr("kode_izin_sakit");
            $("#modal").modal("show");
            loading();
            $("#modal").find(".modal-title").text("Edit Izin Sakit");
            $("#loadmodal").load(`/izinsakit/${kode_izin_sakit}/edit`);
        });
    });
</script>
@endpush
