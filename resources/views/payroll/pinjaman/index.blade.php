@extends('layouts.app')
@section('titlepage', 'Pinjaman Karyawan')

@section('content')
@section('navigasi')
    <div class="d-flex justify-content-between align-items-center w-100">
        <div>
            Pinjaman Karyawan
            <div class="text-muted mt-1" style="font-size: 0.75rem; font-weight: normal; text-transform: none; letter-spacing: 0px;">
                Manajemen pinjaman, cicilan, dan histori pembayaran karyawan.
            </div>
        </div>
        <nav aria-label="breadcrumb" class="d-none d-md-block" style="font-size: 0.75rem;">
            <ol class="breadcrumb breadcrumb-style1 mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard.index') }}">
                        <i class="ti ti-home-2 ti-xs"></i>
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="javascript:void(0);">
                        <i class="ti ti-cash ti-xs me-1"></i> Payroll
                    </a>
                </li>
                <li class="breadcrumb-item active">
                    <i class="ti ti-credit-card ti-xs me-1"></i> Pinjaman
                </li>
            </ol>
        </nav>
    </div>
@endsection

<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            @can('pinjaman.create')
                <a href="#" class="btn btn-primary" id="btnCreate">
                    <i class="ti ti-plus me-1 text-white"></i> Tambah Pinjaman
                </a>
            @endcan
            <a href="{{ route('pinjaman.generate') }}" class="btn btn-label-info">
                <i class="ti ti-settings me-1"></i> Generate Pembayaran Massal
            </a>
        </div>
    </div>
</div>

        <!-- Tab Navigation (Segmented Control Style) -->
        <div class="d-flex justify-content-center mb-0">
            <div class="nav-pills-container p-1 bg-label-primary rounded-pill shadow-sm">
                <ul class="nav nav-pills border-0" id="pinjamanTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active rounded-pill px-4" id="data-tab" data-bs-toggle="tab" data-bs-target="#data-pane" type="button" role="tab" aria-controls="data-pane" aria-selected="true">
                            <i class="ti ti-list me-2"></i> Data Pinjaman
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-pill px-4" id="history-tab" data-bs-toggle="tab" data-bs-target="#history-pane" type="button" role="tab" aria-controls="history-pane" aria-selected="false">
                            <i class="ti ti-history me-2"></i> Histori Generate
                        </button>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Filter Section (Above Card) -->
        <div class="tab-content border-0 p-0" id="filterTabsContent">
            <!-- Filter Data Pinjaman -->
            <div class="tab-pane fade show active" id="data-filter-pane" role="tabpanel">
                <form action="{{ route('pinjaman.index') }}" method="GET" class="px-2 pt-4 pb-3">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <x-input-with-icon label="Cari Nama Karyawan" value="{{ request('nama_karyawan') }}" name="nama_karyawan" icon="ti ti-search" hideLabel />
                        </div>
                        <div class="col-md-4">
                            <select name="status" class="form-select select2">
                                <option value="">Semua Status</option>
                                <option value="A" {{ request('status') == 'A' ? 'selected' : '' }}>Aktif</option>
                                <option value="L" {{ request('status') == 'L' ? 'selected' : '' }}>Lunas</option>
                                <option value="B" {{ request('status') == 'B' ? 'selected' : '' }}>Batal</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="ti ti-search me-1 text-white"></i> Cari
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <!-- Filter Histori Generate -->
            <div class="tab-pane fade" id="history-filter-pane" role="tabpanel">
                <form action="{{ route('pinjaman.index') }}" method="GET" class="px-2 pt-4 pb-3">
                    <div class="row g-2">
                        <div class="col-md-5">
                            <select name="bulan_search" class="form-select select2">
                                <option value="">Semua Bulan</option>
                                @foreach ($list_bulan as $d)
                                    <option value="{{ $d['kode_bulan'] }}" {{ request('bulan_search') == $d['kode_bulan'] ? 'selected' : '' }}>{{ $d['nama_bulan'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <select name="tahun_search" class="form-select select2">
                                <option value="">Semua Tahun</option>
                                @for ($i = date('Y'); $i >= $start_year; $i--)
                                    <option value="{{ $i }}" {{ request('tahun_search') == $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="ti ti-search me-1 text-white"></i> Cari
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="tab-content border-0 p-0" id="pinjamanTabsContent">
                    <!-- Tab Data Pinjaman -->
                    <div class="tab-pane fade show active border-0" id="data-pane" role="tabpanel" aria-labelledby="data-tab" tabindex="0">
                        <div class="pinjaman-card-container px-2 py-4">
                            @forelse ($pinjaman as $d)
                                @php
                                    $path = Storage::url('karyawan/'.$d->karyawan->foto);
                                    // Get Initials
                                    $words = explode(" ", $d->karyawan->nama_karyawan);
                                    $initials = "";
                                    foreach ($words as $w) {
                                        if(isset($w[0])) $initials .= $w[0];
                                    }
                                    $initials = strtoupper(substr($initials, 0, 2));
                                    
                                    // Assign random background color for initials
                                    $colors = ['bg-label-primary', 'bg-label-success', 'bg-label-info', 'bg-label-warning', 'bg-label-danger'];
                                    $randomColor = $colors[array_rand($colors)];
                                @endphp
                                <div class="card mb-3 border-0 shadow-sm hover-shadow transition-all" style="border-radius: 12px; border: 1px solid #eee !important;">
                                    <div class="card-body p-3">
                                        <div class="row align-items-center g-3">
                                            <!-- Profile Identity Section -->
                                            <div class="col-md-3 border-end px-3">
                                                <div class="d-flex align-items-center">
                                                    @if (empty($d->karyawan->foto) || !Storage::disk('public')->exists('karyawan/'.$d->karyawan->foto))
                                                        <div class="avatar avatar-md me-3 shadow-xs">
                                                            <span class="avatar-initial rounded-circle {{ $randomColor }}" style="font-size: 0.75rem;">{{ $initials }}</span>
                                                        </div>
                                                    @else
                                                        <div class="avatar avatar-md me-3 shadow-xs">
                                                            <img src="{{ $path }}" class="rounded-circle" style="object-fit: cover;">
                                                        </div>
                                                    @endif
                                                    <div class="overflow-hidden">
                                                        <h6 class="mb-0 fw-bold text-dark text-truncate" style="font-size: 0.85rem;">{{ $d->karyawan->nama_karyawan }}</h6>
                                                        <small class="text-muted d-block" style="font-size: 0.75rem;">{{ $d->nik }}</small>
                                                        <div class="mt-1">
                                                            <span class="badge bg-label-primary px-2 py-1" style="font-size: 0.65rem; letter-spacing: 0.5px;">{{ $d->no_pinjaman }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Financial Dashboard Section -->
                                            <div class="col-md-5 border-end px-md-4">
                                                <div class="row text-center g-2">
                                                    <div class="col-4 border-end">
                                                        <small class="text-muted d-block mb-1 text-uppercase fw-bold" style="font-size: 0.6rem; letter-spacing: 1px;">TOTAL</small>
                                                        <span class="fw-bold d-block text-dark" style="font-size: 0.8rem;">Rp{{ number_format($d->jumlah_pinjaman, 0, ',', '.') }}</span>
                                                    </div>
                                                    <div class="col-4 border-end">
                                                        <small class="text-muted d-block mb-1 text-uppercase fw-bold" style="font-size: 0.6rem; letter-spacing: 1px;">DIBAYAR</small>
                                                        <span class="fw-bold d-block text-success" style="font-size: 0.8rem;">Rp{{ number_format($d->total_dibayar, 0, ',', '.') }}</span>
                                                    </div>
                                                    <div class="col-4">
                                                        <small class="text-muted d-block mb-1 text-uppercase fw-bold" style="font-size: 0.6rem; letter-spacing: 1px;">SISA</small>
                                                        <span class="fw-bold d-block text-danger" style="font-size: 0.8rem;">Rp{{ number_format($d->sisa_pinjaman, 0, ',', '.') }}</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Metadata & Action Section -->
                                            <div class="col-md-4 ps-md-4 pe-3">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div class="meta-info">
                                                        <div class="mb-2">
                                                            <i class="ti ti-calendar ti-xs me-1 text-muted"></i>
                                                            <small class="fw-bold text-dark" style="font-size: 0.75rem;">{{ date('d-m-Y', strtotime($d->tanggal_pinjaman)) }}</small>
                                                        </div>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <span class="badge bg-label-info rounded-pill" style="font-size: 0.65rem;">{{ $d->jumlah_cicilan }} Bln</span>
                                                            <div style="transform: scale(0.85); transform-origin: left;">
                                                                {!! $d->status_label !!}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="action-buttons">
                                                        <div class="d-inline-flex border rounded-pill overflow-hidden shadow-xs bg-white">
                                                            <a href="{{ route('pinjaman.show', Crypt::encrypt($d->id)) }}" class="btn btn-sm px-3 py-1 border-0" title="Detail">
                                                                <i class="ti ti-eye fs-6 text-info"></i>
                                                            </a>
                                                            @if($d->total_dibayar == 0)
                                                                @can('pinjaman.edit')
                                                                    <a href="#" class="btn btn-sm btnEdit px-3 py-1 border-0 border-start" id="{{ Crypt::encrypt($d->id) }}" title="Edit">
                                                                        <i class="ti ti-edit fs-6 text-success"></i>
                                                                    </a>
                                                                @endcan
                                                                @can('pinjaman.delete')
                                                                    <form method="POST" class="deleteform m-0 d-inline" action="{{ route('pinjaman.delete', Crypt::encrypt($d->id)) }}">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="btn btn-sm delete-confirm px-3 py-1 border-0 border-start" title="Hapus">
                                                                            <i class="ti ti-trash fs-6 text-danger"></i>
                                                                        </button>
                                                                    </form>
                                                                @endcan
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-5 card border-0 shadow-none">
                                    <div class="d-flex flex-column align-items-center opacity-75">
                                        <i class="ti ti-ghost fs-1 text-muted mb-3"></i>
                                        <p class="text-muted mb-0 fw-medium">Belum ada data pinjaman.</p>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                        <div class="p-4 border-top">
                            {{ $pinjaman->links() }}
                        </div>
                    </div>

                    <!-- Tab Histori Generate -->
                    <div class="tab-pane fade border-0" id="history-pane" role="tabpanel" aria-labelledby="history-tab" tabindex="0">
                        <div class="history-card-container px-2 py-4">
                            @forelse ($history_generate as $h)
                                <div class="card mb-3 border-0 shadow-sm hover-shadow transition-all" style="border-radius: 12px; border: 1px solid #eee !important;">
                                    <div class="card-body p-3">
                                        <div class="row align-items-center g-3">
                                            <!-- Batch Identity Section -->
                                            <div class="col-md-3 border-end">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-md me-3 shadow-xs">
                                                        <span class="avatar-initial rounded-circle bg-label-info">
                                                            <i class="ti ti-history fs-5"></i>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.6rem; letter-spacing: 1px;">KODE GENERATE</small>
                                                        <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.85rem;">{{ $h->kode_generate ?? '-' }}</h6>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Period & Date Section -->
                                            <div class="col-md-5 border-end px-md-4">
                                                <div class="row align-items-center">
                                                    <div class="col-6 border-end">
                                                        <small class="text-muted d-block mb-1 text-uppercase fw-bold" style="font-size: 0.6rem; letter-spacing: 1px;">PERIODE</small>
                                                        <span class="fw-bold d-block text-dark" style="font-size: 0.8rem;">
                                                            {{ config('global.nama_bulan')[(int)$h->bulan] }} {{ $h->tahun }}
                                                        </span>
                                                    </div>
                                                    <div class="col-6">
                                                        <small class="text-muted d-block mb-1 text-uppercase fw-bold" style="font-size: 0.6rem; letter-spacing: 1px;">WAKTU GENERATE</small>
                                                        <span class="text-muted" style="font-size: 0.75rem;">
                                                            <i class="ti ti-calendar-event ti-xs me-1"></i> {{ date('d-m-Y H:i', strtotime($h->created_at)) }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- User & Action Section -->
                                            <div class="col-md-4 ps-md-4 pe-3">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div class="user-info">
                                                        <small class="text-muted d-block mb-1 text-uppercase fw-bold" style="font-size: 0.6rem; letter-spacing: 1px;">GENERATED BY</small>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar avatar-xs me-2">
                                                                <span class="avatar-initial rounded-circle bg-label-secondary" style="font-size: 0.55rem;">
                                                                    {{ strtoupper(substr($h->user->name, 0, 1)) }}
                                                                </span>
                                                            </div>
                                                            <span class="fw-bold text-dark" style="font-size: 0.75rem;">{{ $h->user->name }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="action-buttons">
                                                        <div class="d-inline-flex border rounded-pill overflow-hidden shadow-xs bg-white">
                                                            <a href="#" class="btn btn-sm btnDetailHistory px-3 py-1 border-0" id="{{ Crypt::encrypt($h->id) }}" title="Detail">
                                                                <i class="ti ti-eye fs-6 text-info"></i>
                                                            </a>
                                                            @can('pinjaman.generate')
                                                                <form method="POST" class="deleteform m-0 d-inline" action="{{ route('pinjaman.history.delete', Crypt::encrypt($h->id)) }}">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-sm delete-confirm px-3 py-1 border-0 border-start" title="Hapus">
                                                                        <i class="ti ti-trash fs-6 text-danger"></i>
                                                                    </button>
                                                                </form>
                                                            @endcan
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-5 card border-0 shadow-none">
                                    <div class="d-flex flex-column align-items-center opacity-75">
                                        <i class="ti ti-history fs-1 text-muted mb-3"></i>
                                        <p class="text-muted mb-0 fw-medium">Belum ada history generate.</p>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
    </div>
</div>
</div>

<x-modal-form id="modalPinjaman" size="modal-md" show="loadmodal" />

<!-- Modal Lookup Karyawan (Global for this page) -->
<div class="modal fade" id="modalLookupKaryawan" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cari Data Karyawan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="loadLookupKaryawan">
                <!-- Content loaded via AJAX -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('myscript')
<style>
    .nav-pills-container {
        display: inline-block;
        background: #f1f3f9;
        padding: 4px;
        border-radius: 50px !important;
    }

    .nav-pills .nav-link {
        color: #677788;
        font-weight: 500;
        transition: all 0.3s ease;
        border: none !important;
    }

    .nav-pills .nav-link.active {
        background: #ffffff !important;
        color: var(--bs-primary) !important;
        box-shadow: 0 2px 6px 0 rgba(105, 108, 255, 0.15) !important;
    }

    .nav-pills .nav-link:hover:not(.active) {
        color: var(--bs-primary);
    }
</style>
<script>
    $(function() {
        function loading() {
            $("#loadmodal").html(`<div class="sk-wave sk-primary" style="margin:auto">
                <div class="sk-wave-rect"></div>
                <div class="sk-wave-rect"></div>
                <div class="sk-wave-rect"></div>
                <div class="sk-wave-rect"></div>
                <div class="sk-wave-rect"></div>
                </div>`);
        };

        // Dynamic Title & Filter Update
        $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
            var target = $(e.target).data('bs-target');
            var title = $(e.target).text().trim();
            $("#tabTitle").text(title);
            
            // Toggle Filter Panes
            if (target === '#data-pane') {
                $('#data-filter-pane').addClass('show active');
                $('#history-filter-pane').removeClass('show active');
            } else {
                $('#history-filter-pane').addClass('show active');
                $('#data-filter-pane').removeClass('show active');
            }
        });

        $("#btnCreate").click(function(e) {
            e.preventDefault();
            loading();
            $("#modalPinjaman").find(".modal-dialog").removeClass("modal-lg").addClass("modal-md");
            $("#modalPinjaman").modal("show");
            $(".modal-title").text("Tambah Pinjaman");
            $("#loadmodal").load("{{ route('pinjaman.create') }}");
        });

        $(".btnEdit").click(function(e) {
            e.preventDefault();
            loading();
            var id = $(this).attr("id");
            $("#modalPinjaman").find(".modal-dialog").removeClass("modal-lg").addClass("modal-md");
            $("#modalPinjaman").modal("show");
            $(".modal-title").text("Edit Pinjaman");
            $("#loadmodal").load("/pinjaman/" + id + "/edit");
        });

        $(".btnDetailHistory").click(function(e) {
            e.preventDefault();
            loading();
            var id = $(this).attr("id");
            $("#modalPinjaman").find(".modal-dialog").removeClass("modal-md").addClass("modal-xl");
            $("#modalPinjaman").modal("show");
            $(".modal-title").text("Detail History Generate");
            $("#loadmodal").load("/pinjaman/history/" + id);
        });

        $(".delete-confirm").click(function(e) {
            var form = $(this).closest("form");
            e.preventDefault();
            Swal.fire({
                title: 'Apakah Anda Yakin?',
                text: "Data Pinjaman akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush
