@extends('layouts.app')
@section('titlepage', 'Aturan Lembur')

@section('content')
@section('navigasi')
    <div class="d-flex justify-content-between align-items-center w-100">
        <div>
            Aturan Lembur
            <div class="text-muted mt-1" style="font-size: 0.75rem; font-weight: normal; text-transform: none; letter-spacing: 0px;">
                Manajemen faktor pengali upah lembur berjenjang.
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
                        <i class="ti ti-settings ti-xs me-1"></i> Konfigurasi
                    </a>
                </li>
                <li class="breadcrumb-item active">
                    <i class="ti ti-clock-play ti-xs me-1"></i> Aturan Lembur
                </li>
            </ol>
        </nav>
    </div>
@endsection

{{-- Info Banner --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="rounded-3 px-4 py-3 d-flex align-items-start gap-3"
             style="background: #f8fafc; border: 1px solid #e2e8f0;">
            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 mt-1"
                 style="width: 36px; height: 36px; background: var(--theme-color-1); box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                <i class="ti ti-bulb text-white" style="font-size: 18px;"></i>
            </div>
            <div>
                <div style="font-weight: 600; font-size: 0.85rem; color: #1e293b; margin-bottom: 2px;">Tentang Aturan Lembur</div>
                <div style="font-size: 0.8rem; color: #475569; line-height: 1.5;">
                    Aturan ini menentukan faktor pengali untuk menghitung <strong style="color: var(--theme-color-1);">"Jam Netto"</strong> lembur.
                    Contoh: jika karyawan lembur 2 jam di hari kerja, maka jam netto = <strong style="color: var(--theme-color-1);">(1×1.5) + (1×2.0) = 3.5 jam</strong>.
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- ═══════════════════════════════════════════ --}}
    {{-- Aturan Hari Kerja --}}
    {{-- ═══════════════════════════════════════════ --}}
    <div class="col-lg-6 col-md-12">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; overflow: hidden;">
            {{-- Card Header --}}
            <div class="px-4 py-3 d-flex justify-content-between align-items-center"
                 style="background: var(--theme-color-1); min-height: 60px;">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-2 d-flex align-items-center justify-content-center"
                         style="width: 34px; height: 34px; background: rgba(255,255,255,0.2);">
                        <i class="ti ti-briefcase text-white" style="font-size: 18px;"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 text-white" style="font-weight: 600; font-size: 0.95rem;">Hari Kerja</h6>
                        <small style="color: rgba(255,255,255,0.8); font-size: 0.7rem;">Senin - Sabtu (Hari kerja normal)</small>
                    </div>
                </div>
                <a href="#" class="btn btn-sm d-flex align-items-center gap-1 btnCreate" data-tipe="1"
                   style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3); color: #fff; border-radius: 8px; padding: 6px 14px; font-size: 0.78rem; font-weight: 500; transition: all 0.2s;">
                    <i class="ti ti-plus" style="font-size: 14px;"></i> Tambah
                </a>
            </div>

            {{-- Table --}}
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0" style="font-size: 0.85rem;">
                        <thead>
                            <tr style="background: var(--theme-color-1);">
                                <th class="py-2 px-4" style="color: white; font-weight: 600; font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: none;">Rentang Jam</th>
                                <th class="py-2 px-4 text-center" style="color: white; font-weight: 600; font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: none;">Faktor</th>
                                <th class="py-2 px-4 text-center" style="color: white; font-weight: 600; font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: none; width: 90px;">#</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($aturan_kerja as $d)
                                <tr style="transition: background 0.15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                                    <td class="py-2 px-4" style="border-bottom: 1px solid #f1f5f9;">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-2 d-flex align-items-center justify-content-center"
                                                 style="width: 28px; height: 28px; background: #eff6ff; flex-shrink: 0;">
                                                <i class="ti ti-clock" style="font-size: 14px; color: var(--theme-color-1);"></i>
                                            </div>
                                            <div>
                                                <span style="font-weight: 600; color: #1e293b;">{{ number_format($d->jam_dari, 1) }} jam</span>
                                                <span style="color: #94a3b8; margin: 0 4px;">→</span>
                                                <span style="font-weight: 600; color: #1e293b;">{{ $d->jam_sampai < 99 && $d->jam_sampai > 0 ? number_format($d->jam_sampai, 1) . ' jam' : 'Seterusnya' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-2 px-4 text-center" style="border-bottom: 1px solid #f1f5f9;">
                                        <span class="d-inline-flex align-items-center justify-content-center"
                                              style="background: var(--theme-color-1); color: #fff; font-weight: 700; font-size: 0.85rem; padding: 3px 12px; border-radius: 4px; min-width: 55px; letter-spacing: 0.3px;">
                                            {{ $d->faktor }}×
                                        </span>
                                    </td>
                                    <td class="py-2 px-4 text-center" style="border-bottom: 1px solid #f1f5f9;">
                                        <div class="d-inline-flex gap-1">
                                            <a href="#" class="btnEdit d-flex align-items-center justify-content-center"
                                                data-id="{{ $d->id }}" title="Edit"
                                                style="width: 28px; height: 28px; border-radius: 6px; background: #f1f5f9; color: #64748b; transition: all 0.2s; border: 1px solid #e2e8f0; text-decoration: none;">
                                                <i class="ti ti-pencil" style="font-size: 14px;"></i>
                                            </a>
                                            <form method="POST" class="deleteform m-0"
                                                action="{{ route('lemburaturan.delete', $d->id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="delete-confirm d-flex align-items-center justify-content-center"
                                                    title="Hapus"
                                                    style="width: 28px; height: 28px; border-radius: 6px; background: #f1f5f9; color: #ef4444; transition: all 0.2s; border: 1px solid #e2e8f0; cursor: pointer;">
                                                    <i class="ti ti-trash" style="font-size: 14px;"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-5">
                                        <div class="d-flex flex-column align-items-center gap-2">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                                 style="width: 48px; height: 48px; background: #f1f5f9;">
                                                <i class="ti ti-clipboard-list" style="font-size: 22px; color: #94a3b8;"></i>
                                            </div>
                                            <span style="color: #94a3b8; font-size: 0.82rem;">Belum ada aturan hari kerja</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Card Footer Summary --}}
            @if($aturan_kerja->count() > 0)
            <div class="px-4 py-2" style="background: #f8fafc; border-top: 1px solid #e2e8f0;">
                <div class="d-flex align-items-center gap-2">
                    <i class="ti ti-info-circle" style="font-size: 14px; color: #64748b;"></i>
                    <span style="font-size: 0.75rem; color: #64748b;">
                        Total <strong style="color: #1e293b;">{{ $aturan_kerja->count() }}</strong> tier aturan
                    </span>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- ═══════════════════════════════════════════ --}}
    {{-- Aturan Hari Libur --}}
    {{-- ═══════════════════════════════════════════ --}}
    <div class="col-lg-6 col-md-12">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; overflow: hidden;">
            {{-- Card Header --}}
            <div class="px-4 py-3 d-flex justify-content-between align-items-center"
                 style="background: #ea580c; min-height: 60px;">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-2 d-flex align-items-center justify-content-center"
                         style="width: 34px; height: 34px; background: rgba(255,255,255,0.2);">
                        <i class="ti ti-calendar-off text-white" style="font-size: 18px;"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 text-white" style="font-weight: 600; font-size: 0.95rem;">Hari Libur</h6>
                        <small style="color: rgba(255,255,255,0.8); font-size: 0.7rem;">Minggu & Hari libur nasional</small>
                    </div>
                </div>
                <a href="#" class="btn btn-sm d-flex align-items-center gap-1 btnCreate" data-tipe="2"
                   style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3); color: #fff; border-radius: 8px; padding: 6px 14px; font-size: 0.78rem; font-weight: 500; transition: all 0.2s;">
                    <i class="ti ti-plus" style="font-size: 14px;"></i> Tambah
                </a>
            </div>

            {{-- Table --}}
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0" style="font-size: 0.85rem;">
                        <thead>
                            <tr style="background: #ea580c;">
                                <th class="py-2 px-4" style="color: white; font-weight: 600; font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: none;">Rentang Jam</th>
                                <th class="py-2 px-4 text-center" style="color: white; font-weight: 600; font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: none;">Faktor</th>
                                <th class="py-2 px-4 text-center" style="color: white; font-weight: 600; font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: none; width: 90px;">#</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($aturan_libur as $d)
                                <tr style="transition: background 0.15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                                    <td class="py-2 px-4" style="border-bottom: 1px solid #f1f5f9;">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-2 d-flex align-items-center justify-content-center"
                                                 style="width: 28px; height: 28px; background: #fff7ed; flex-shrink: 0;">
                                                <i class="ti ti-clock" style="font-size: 14px; color: #ea580c;"></i>
                                            </div>
                                            <div>
                                                <span style="font-weight: 600; color: #1e293b;">{{ number_format($d->jam_dari, 1) }} jam</span>
                                                <span style="color: #94a3b8; margin: 0 4px;">→</span>
                                                <span style="font-weight: 600; color: #1e293b;">{{ $d->jam_sampai < 99 && $d->jam_sampai > 0 ? number_format($d->jam_sampai, 1) . ' jam' : 'Seterusnya' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-2 px-4 text-center" style="border-bottom: 1px solid #f1f5f9;">
                                        <span class="d-inline-flex align-items-center justify-content-center"
                                              style="background: #ea580c; color: #fff; font-weight: 700; font-size: 0.85rem; padding: 3px 12px; border-radius: 4px; min-width: 55px; letter-spacing: 0.3px;">
                                            {{ $d->faktor }}×
                                        </span>
                                    </td>
                                    <td class="py-2 px-4 text-center" style="border-bottom: 1px solid #f1f5f9;">
                                        <div class="d-inline-flex gap-1">
                                            <a href="#" class="btnEdit d-flex align-items-center justify-content-center"
                                                data-id="{{ $d->id }}" title="Edit"
                                                style="width: 28px; height: 28px; border-radius: 6px; background: #f1f5f9; color: #64748b; transition: all 0.2s; border: 1px solid #e2e8f0; text-decoration: none;">
                                                <i class="ti ti-pencil" style="font-size: 14px;"></i>
                                            </a>
                                            <form method="POST" class="deleteform m-0"
                                                action="{{ route('lemburaturan.delete', $d->id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="delete-confirm d-flex align-items-center justify-content-center"
                                                    title="Hapus"
                                                    style="width: 28px; height: 28px; border-radius: 6px; background: #f1f5f9; color: #ef4444; transition: all 0.2s; border: 1px solid #e2e8f0; cursor: pointer;">
                                                    <i class="ti ti-trash" style="font-size: 14px;"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-5">
                                        <div class="d-flex flex-column align-items-center gap-2">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                                 style="width: 48px; height: 48px; background: #f1f5f9;">
                                                <i class="ti ti-clipboard-list" style="font-size: 22px; color: #94a3b8;"></i>
                                            </div>
                                            <span style="color: #94a3b8; font-size: 0.82rem;">Belum ada aturan hari libur</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Card Footer Summary --}}
            @if($aturan_libur->count() > 0)
            <div class="px-4 py-2" style="background: #f8fafc; border-top: 1px solid #e2e8f0;">
                <div class="d-flex align-items-center gap-2">
                    <i class="ti ti-info-circle" style="font-size: 14px; color: #64748b;"></i>
                    <span style="font-size: 0.75rem; color: #64748b;">
                        Total <strong style="color: #1e293b;">{{ $aturan_libur->count() }}</strong> tier aturan
                    </span>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Simulasi Perhitungan --}}
<div class="row mt-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
            <div class="px-4 py-3 d-flex align-items-center gap-2" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                <div class="rounded-2 d-flex align-items-center justify-content-center"
                     style="width: 30px; height: 30px; background: #f1f5f9;">
                    <i class="ti ti-calculator" style="font-size: 16px; color: #64748b;"></i>
                </div>
                <h6 class="mb-0" style="font-weight: 600; font-size: 0.88rem; color: #1e293b;">Contoh Simulasi Perhitungan</h6>
            </div>
            <div class="card-body px-4 py-3">
                <div class="row g-4">
                    {{-- Contoh Hari Kerja --}}
                    <div class="col-md-6">
                        <div class="rounded-3 p-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="ti ti-briefcase" style="color: var(--theme-color-1); font-size: 16px;"></i>
                                <span style="font-weight: 600; font-size: 0.82rem; color: #1e293b;">Lembur 3 Jam — Hari Kerja</span>
                            </div>
                            <div style="font-size: 0.78rem; color: #475569; line-height: 1.7;">
                                @if($aturan_kerja->count() > 0)
                                    @php
                                        $contoh_jam = 3;
                                        $total_netto = 0;
                                        $detail_parts = [];
                                        foreach($aturan_kerja as $rule) {
                                            $start = $rule->jam_dari; 
                                            $end = $rule->jam_sampai ?: 99;
                                            
                                            $jam_di_tier_ini = max(0, min($contoh_jam, $end) - $start);
                                            
                                            if ($jam_di_tier_ini > 0) {
                                                $netto = $jam_di_tier_ini * $rule->faktor;
                                                $total_netto += $netto;
                                                $detail_parts[] = 'Jam ' . number_format($start, 1) . '-' . number_format($end, 1) . ' (' . number_format($jam_di_tier_ini, 1) . ' jam) × ' . $rule->faktor . ' = ' . number_format($netto, 1);
                                            }
                                        }
                                    @endphp
                                    @foreach($detail_parts as $part)
                                        <div>• {{ $part }}</div>
                                    @endforeach
                                    <div class="mt-2 pt-2" style="border-top: 1px dashed #e2e8f0;">
                                        <strong style="color: var(--theme-color-1);">Jam Netto = {{ number_format($total_netto, 1) }} jam</strong>
                                    </div>
                                @else
                                    <span class="text-muted">Tambahkan aturan untuk melihat simulasi.</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    {{-- Contoh Hari Libur --}}
                    <div class="col-md-6">
                        <div class="rounded-3 p-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="ti ti-calendar-off" style="color: #ea580c; font-size: 16px;"></i>
                                <span style="font-weight: 600; font-size: 0.82rem; color: #1e293b;">Lembur 9 Jam — Hari Libur</span>
                            </div>
                            <div style="font-size: 0.78rem; color: #475569; line-height: 1.7;">
                                @if($aturan_libur->count() > 0)
                                    @php
                                        $contoh_jam = 9;
                                        $total_netto = 0;
                                        $detail_parts = [];
                                        foreach($aturan_libur as $rule) {
                                            $start = $rule->jam_dari; 
                                            $end = $rule->jam_sampai ?: 99;
                                            
                                            $jam_di_tier_ini = max(0, min($contoh_jam, $end) - $start);
                                            
                                            if ($jam_di_tier_ini > 0) {
                                                $netto = $jam_di_tier_ini * $rule->faktor;
                                                $total_netto += $netto;
                                                $detail_parts[] = 'Jam ' . number_format($start, 1) . '-' . number_format($end, 1) . ' (' . number_format($jam_di_tier_ini, 1) . ' jam) × ' . $rule->faktor . ' = ' . number_format($netto, 1);
                                            }
                                        }
                                    @endphp
                                    @foreach($detail_parts as $part)
                                        <div>• {{ $part }}</div>
                                    @endforeach
                                    <div class="mt-2 pt-2" style="border-top: 1px dashed #e2e8f0;">
                                        <strong style="color: #ea580c;">Jam Netto = {{ number_format($total_netto, 1) }} jam</strong>
                                    </div>
                                @else
                                    <span class="text-muted">Tambahkan aturan untuk melihat simulasi.</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<x-modal-form id="modal" show="loadmodal" />
@endsection

@push('myscript')
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

        $(".btnCreate").click(function(e) {
            e.preventDefault();
            loading();
            const tipe = $(this).data("tipe");
            $('#modal').modal("show");
            $(".modal-title").text("Tambah Aturan Lembur");
            $("#loadmodal").load('/lemburaturan/create?tipe_hari=' + tipe);
        });

        $(".btnEdit").click(function(e) {
            e.preventDefault();
            loading();
            var id = $(this).data("id");
            $('#modal').modal("show");
            $(".modal-title").text("Edit Aturan Lembur");
            $("#loadmodal").load('/lemburaturan/edit?id=' + id);
        });
    });
</script>
@endpush
