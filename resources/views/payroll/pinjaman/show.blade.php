@extends('layouts.app')
@section('titlepage', 'Detail Pinjaman')

@section('content')
@section('navigasi')
    <div class="d-flex justify-content-between align-items-center w-100">
        <div>
            Detail Pinjaman: {{ $pinjaman->no_pinjaman }}
            <div class="text-muted mt-1" style="font-size: 0.75rem; font-weight: normal; text-transform: none; letter-spacing: 0px;">
                Informasi detail rencana cicilan dan riwayat pembayaran karyawan.
            </div>
        </div>
        <nav aria-label="breadcrumb" class="d-none d-md-block" style="font-size: 0.75rem;">
            <ol class="breadcrumb breadcrumb-style1 mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i class="ti ti-home-2 ti-xs"></i></a></li>
                <li class="breadcrumb-item"><a href="{{ route('pinjaman.index') }}">Pinjaman</a></li>
                <li class="breadcrumb-item active">Detail</li>
            </ol>
        </nav>
    </div>
@endsection

<div class="row">
    <!-- Header Info -->
    <div class="col-lg-12 mb-4">
        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="card-body p-0">
                <div class="row g-0">
                    <!-- Profile Section (Primary Theme Background) -->
                    <div class="col-md-7 px-4 py-3 text-white position-relative d-flex flex-column justify-content-center" style="background-color: var(--theme-color-1) !important; background-image: radial-gradient(circle at top right, rgba(255,255,255,0.1), transparent);">
                        <div class="d-flex align-items-center mb-2">
                            @php
                                $path = Storage::url('karyawan/'.$pinjaman->karyawan->foto);
                                // Get Initials
                                $words = explode(" ", $pinjaman->karyawan->nama_karyawan);
                                $initials = "";
                                foreach ($words as $w) {
                                    if(isset($w[0])) $initials .= $w[0];
                                }
                                $initials = strtoupper(substr($initials, 0, 2));
                                
                                // Assign random background color for initials
                                $colors = ['bg-primary', 'bg-success', 'bg-info', 'bg-warning', 'bg-danger'];
                                $randomColor = $colors[array_rand($colors)];

                                // Masa Kerja Calculation
                                $tgl_masuk = $pinjaman->karyawan->tanggal_masuk ? \Carbon\Carbon::parse($pinjaman->karyawan->tanggal_masuk) : null;
                                $masa_kerja = '-';
                                if ($tgl_masuk) {
                                    $diff = $tgl_masuk->diff(\Carbon\Carbon::now());
                                    $parts = [];
                                    if ($diff->y > 0) $parts[] = $diff->y . ' Thn';
                                    if ($diff->m > 0) $parts[] = $diff->m . ' Bln';
                                    $masa_kerja = implode(', ', $parts) ?: 'Baru';
                                }

                                $repayment_percentage = $pinjaman->jumlah_pinjaman > 0 
                                    ? round(($pinjaman->total_dibayar / $pinjaman->jumlah_pinjaman) * 100) 
                                    : 0;
                            @endphp
                            <div class="avatar avatar-xl me-4 shadow-sm border border-4 border-white border-opacity-10 rounded-circle">
                                @if (empty($pinjaman->karyawan->foto) || !Storage::disk('public')->exists('karyawan/'.$pinjaman->karyawan->foto))
                                    <span class="avatar-initial rounded-circle {{ $randomColor }} border border-3 border-white shadow-sm" style="font-size: 1.5rem;">{{ $initials }}</span>
                                @else
                                    <img src="{{ $path }}" class="rounded-circle shadow-sm border border-3 border-white" style="object-fit: cover;">
                                @endif
                            </div>
                            <div>
                                <h3 class="mb-1 fw-bold text-white ls-minus-1">{{ $pinjaman->karyawan->nama_karyawan }}</h3>
                                <div class="d-flex align-items-center flex-wrap gap-2">
                                    <span class="badge bg-white text-primary px-2 py-1 shadow-sm" style="font-size: 0.75rem;"><i class="ti ti-id me-1"></i> {{ $pinjaman->nik }}</span>
                                    <span class="badge bg-white text-primary px-2 py-1 shadow-sm" style="font-size: 0.75rem;"><i class="ti ti-user-star me-1"></i> {{ $pinjaman->karyawan->jabatan->nama_jabatan ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Single Horizontal Row for Details -->
                        <div class="d-flex flex-wrap gap-4 align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-xs bg-white rounded-circle me-2 d-flex align-items-center justify-content-center shadow-sm">
                                    <i class="ti ti-building-community text-primary fs-6"></i>
                                </div>
                                <div style="line-height: 1.1">
                                    <small class="d-block text-white opacity-50" style="font-size: 0.65rem; text-uppercase; letter-spacing: 0.5px">Dept / Cabang</small>
                                    <span class="fw-bold text-white" style="font-size: 0.85rem;">{{ $pinjaman->karyawan->departemen->nama_dept ?? '-' }} / {{ $pinjaman->karyawan->cabang->nama_cabang ?? '-' }}</span>
                                </div>
                            </div>
                            
                            <div class="d-flex align-items-center border-start border-white border-opacity-10 ps-4">
                                <div class="avatar avatar-xs bg-white rounded-circle me-2 d-flex align-items-center justify-content-center shadow-sm">
                                    <i class="ti ti-calendar-event text-primary fs-6"></i>
                                </div>
                                <div style="line-height: 1.1">
                                    <small class="d-block text-white opacity-50" style="font-size: 0.65rem; text-uppercase; letter-spacing: 0.5px">Masa Kerja</small>
                                    <span class="fw-bold text-white" style="font-size: 0.85rem;">{{ $masa_kerja }}</span>
                                </div>
                            </div>

                            <div class="d-flex align-items-center border-start border-white border-opacity-10 ps-4">
                                <div class="avatar avatar-xs bg-white rounded-circle me-2 d-flex align-items-center justify-content-center shadow-sm">
                                    <i class="ti ti-briefcase text-primary fs-6"></i>
                                </div>
                                <div style="line-height: 1.1">
                                    <small class="d-block text-white opacity-50" style="font-size: 0.65rem; text-uppercase; letter-spacing: 0.5px">Status</small>
                                    <span class="fw-bold text-white" style="font-size: 0.85rem;">
                                        @php
                                            $status_map = ['T' => 'Tetap', 'K' => 'Kontrak', 'O' => 'Outs'];
                                            echo $status_map[$pinjaman->karyawan->status_karyawan] ?? 'Lainnya';
                                        @endphp
                                    </span>
                                </div>
                            </div>

                            <div class="d-flex align-items-center border-start border-white border-opacity-10 ps-4">
                                <div class="avatar avatar-xs bg-white rounded-circle me-2 d-flex align-items-center justify-content-center shadow-sm">
                                    <i class="ti ti-device-mobile text-primary fs-6"></i>
                                </div>
                                <div style="line-height: 1.1">
                                    <small class="d-block text-white opacity-50" style="font-size: 0.65rem; text-uppercase; letter-spacing: 0.5px">Kontak</small>
                                    <span class="fw-bold text-white" style="font-size: 0.85rem;">{{ $pinjaman->karyawan->no_hp ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Financial Section -->
                    <div class="col-md-5 px-4 py-3 d-flex flex-column justify-content-center border-start">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <small class="text-muted d-block mb-0 text-uppercase ls-1" style="font-size: 0.7rem;">Progress Pelunasan</small>
                                <h3 class="mb-0 fw-bold">{{ $repayment_percentage }}%</h3>
                            </div>
                            <div class="d-flex gap-2">
                                @if($pinjaman->sisa_pinjaman > 0)
                                    @can('pinjaman.pembayaran')
                                        <a href="#" class="btn btn-sm btn-success shadow-xs px-3" id="btnPembayaran" title="Bayar Manual">
                                            <i class="ti ti-cash ti-xs me-1"></i> Bayar Manual
                                        </a>
                                    @endcan
                                @endif
                                <a href="{{ route('pinjaman.index') }}" class="btn btn-icon btn-sm btn-outline-secondary" title="Kembali">
                                    <i class="ti ti-arrow-left ti-xs"></i>
                                </a>
                            </div>
                        </div>

                        <div class="progress mb-3 rounded-pill" style="height: 8px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: {{ $repayment_percentage }}%" aria-valuenow="{{ $repayment_percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>

                        <div class="row g-2">
                            <div class="col-4">
                                <small class="text-muted d-block opacity-75" style="font-size: 0.65rem;">Pinjaman</small>
                                <span class="d-block fw-bold" style="font-size: 0.8rem;">Rp{{ number_format($pinjaman->jumlah_pinjaman, 0, ',', '.') }}</span>
                            </div>
                            <div class="col-4 text-center border-start">
                                <small class="text-success d-block opacity-75" style="font-size: 0.65rem;">Dibayar</small>
                                <span class="d-block fw-bold text-success" style="font-size: 0.8rem;">Rp{{ number_format($pinjaman->total_dibayar, 0, ',', '.') }}</span>
                            </div>
                            <div class="col-4 text-end border-start">
                                <small class="text-danger d-block opacity-75" style="font-size: 0.65rem;">Sisa</small>
                                <span class="d-block fw-bold text-danger" style="font-size: 0.8rem;">Rp{{ number_format($pinjaman->sisa_pinjaman, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs Detail -->
    <div class="col-lg-12">
        <div class="nav-align-top mb-4">
            <ul class="nav nav-tabs nav-fill mb-0 border-0 p-1 custom-nav-tabs" role="tablist" style="background-color: var(--theme-color-1) !important; border-radius: 12px 12px 0 0; overflow: hidden; gap: 4px;">
                <li class="nav-item border-0">
                    <button type="button" class="nav-link active py-3 border-0" role="tab" data-bs-toggle="tab" data-bs-target="#navs-rencana" aria-controls="navs-rencana" aria-selected="true">
                        <i class="tf-icons ti ti-calendar-event me-1"></i> Rencana Cicilan
                    </button>
                </li>
                <li class="nav-item border-0">
                    <button type="button" class="nav-link py-3 border-0 text-white" role="tab" data-bs-toggle="tab" data-bs-target="#navs-histori" aria-controls="navs-histori" aria-selected="false">
                        <i class="tf-icons ti ti-history me-1"></i> Histori Pembayaran
                    </button>
                </li>
            </ul>
            <div class="tab-content border-0 p-0 shadow-none">
                <!-- Data Rencana Cicilan -->
                <div class="tab-pane fade show active" id="navs-rencana" role="tabpanel">
                    <div class="card overflow-hidden">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead style="background-color: var(--theme-color-1) !important; color: white !important;">
                                    <tr>
                                        <th class="text-white">KE</th>
                                        <th class="text-white">PERIODE</th>
                                        <th class="text-white text-end">NOMINAL</th>
                                        <th class="text-white text-end">REALISASI</th>
                                        <th class="text-white text-center">STATUS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pinjaman->rencana_cicilan as $r)
                                        @php
                                            // Hitung realisasi berdasarkan bulan dan tahun gaji
                                            $realisasi = $pinjaman->pembayaran_pinjaman
                                                ->where('bulan_gaji', $r->bulan)
                                                ->where('tahun_gaji', $r->tahun)
                                                ->sum('jumlah_bayar');
                                            
                                            $kurang_bayar = $r->jumlah_cicilan - $realisasi;
                                        @endphp
                                        <tr>
                                            <td>{{ $r->cicilan_ke }}</td>
                                            <td>{{ getNamabulan($r->bulan) }} {{ $r->tahun }}</td>
                                            <td class="text-end fw-bold">Rp {{ number_format($r->jumlah_cicilan, 0, ',', '.') }}</td>
                                            <td class="text-end fw-bold text-success">
                                                @if($realisasi > 0)
                                                    Rp {{ number_format($realisasi, 0, ',', '.') }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($realisasi <= 0)
                                                    <span class="badge bg-label-secondary">Belum Bayar</span>
                                                @elseif($realisasi < $r->jumlah_cicilan)
                                                    <span class="badge bg-label-warning">Dibayar Sebagian</span>
                                                @else
                                                    <span class="badge bg-label-success">Sudah Bayar</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Data Histori Pembayaran -->
                <div class="tab-pane fade" id="navs-histori" role="tabpanel">
                    <div class="card overflow-hidden">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead style="background-color: var(--theme-color-1) !important; color: white !important;">
                                <thead style="background-color: var(--theme-color-1) !important; color: white !important;">
                                    <tr>
                                        <th class="text-white">NO BUKTI</th>
                                        <th class="text-white">TANGGAL</th>
                                        <th class="text-white">JENIS</th>
                                        <th class="text-white text-end">TOTAL</th>
                                        <th class="text-white text-end">KET</th>
                                        <th class="text-white text-center">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $pembayaran_grouped = $pinjaman->pembayaran_pinjaman->groupBy(function($item) {
                                            return $item->no_bukti ?: ($item->history_generate_id ? 'GEN-'.$item->history_generate_id : $item->id);
                                        });
                                    @endphp
                                    @forelse($pembayaran_grouped as $key => $group)
                                        @php 
                                            $h = $group->first();
                                            $total_bayar = $group->sum('jumlah_bayar');
                                            $isLocked = $group->contains(function($item) use ($generated_periods) {
                                                return $item->history_generate_id != null || in_array($item->bulan_gaji . '-' . $item->tahun_gaji, $generated_periods);
                                            });
                                        @endphp
                                        <tr>
                                            <td class="fw-bold">{{ $h->no_bukti ?? '-' }}</td>
                                            <td>{{ date('d-m-Y', strtotime($h->tanggal_bayar)) }}</td>
                                            <td>
                                                @if($h->jenis_pembayaran == 'C')
                                                    <span class="badge bg-label-info">Cicilan</span>
                                                @elseif($h->jenis_pembayaran == 'P')
                                                    <span class="badge bg-label-primary">Pelunasan</span>
                                                @else
                                                    <span class="badge bg-label-secondary">Manual</span>
                                                @endif
                                            </td>
                                            <td class="text-end fw-bold text-success">Rp {{ number_format($total_bayar, 0, ',', '.') }}</td>
                                            <td class="text-end"><small>{{ $h->keterangan ?? '-' }}</small></td>
                                            <td class="text-center">
                                                @can('pinjaman.pembayaran')
                                                    @if(!$isLocked)
                                                        <form method="POST" class="deleteform m-0" action="{{ route('pinjaman.deletepembayaran', Crypt::encrypt($h->id)) }}">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-icon delete-confirm" title="Batalkan Pembayaran">
                                                                <i class="ti ti-trash text-danger"></i>
                                                            </button>
                                                        </form>
                                                    @else
                                                        <span class="badge bg-label-secondary" data-bs-toggle="tooltip" data-bs-placement="left" title="{{ $h->history_generate_id != null ? 'Hasil Generate' : 'Periode Sudah Digenerate' }}">
                                                            <i class="ti ti-lock-square"></i>
                                                        </span>
                                                    @endif
                                                @endcan
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">Belum ada riwayat pembayaran.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('myscript')
<script>
    $(function() {
        function loading() {
            $("#loadmodalpembayaran").html(`<div class="sk-wave sk-primary" style="margin:auto">
                <div class="sk-wave-rect"></div>
                <div class="sk-wave-rect"></div>
                <div class="sk-wave-rect"></div>
                <div class="sk-wave-rect"></div>
                <div class="sk-wave-rect"></div>
                </div>`);
        };

        $("#btnPembayaran").click(function(e) {
            e.preventDefault();
            loading();
            $("#modalPembayaran").modal("show");
            $("#loadmodalpembayaran").load("{{ route('pinjaman.pembayaran', Crypt::encrypt($pinjaman->id)) }}");
        });

        $(".delete-confirm").click(function(e) {
            var form = $(this).closest("form");
            e.preventDefault();
            Swal.fire({
                title: 'Batalkan Pembayaran?',
                text: "Saldo pinjaman akan bertambah kembali!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Batalkan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
<x-modal-form id="modalPembayaran" size="modal-md" show="loadmodalpembayaran" title="Pembayaran Manual / Pelunasan" />

<style>
    .custom-nav-tabs .nav-link {
        color: rgba(255, 255, 255, 0.7) !important;
        border-radius: 8px !important;
        transition: all 0.3s ease;
    }
    .custom-nav-tabs .nav-link:hover {
        background-color: rgba(255, 255, 255, 0.1) !important;
        color: white !important;
    }
    .custom-nav-tabs .nav-link.active {
        background-color: white !important;
        color: var(--theme-color-1) !important;
        font-weight: bold !important;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1) !important;
    }
</style>
@endpush
