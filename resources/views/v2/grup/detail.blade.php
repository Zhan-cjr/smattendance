@extends('v2.layouts.app')

@section('titlepage', 'Kelola Anggota - ' . $grup->nama_grup)
@section('page_title', 'Kelola Anggota Grup')

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
    $totalAnggota = $karyawanInGrup->count();
@endphp

<div class="space-y-6" style="font-family: 'Plus Jakarta Sans', sans-serif;">
    <!-- Breadcrumb & Back Navigation -->
    <div class="flex items-center justify-between flex-wrap gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('grup.index') }}" 
               class="w-9 h-9 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-900 flex items-center justify-center transition-all shadow-sm">
                <i class="ti ti-arrow-left text-lg"></i>
            </a>
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-500">
                    <a href="{{ route('grup.index') }}" class="hover:text-emerald-600 transition-colors">Data Grup</a>
                    <i class="ti ti-chevron-right text-[10px]"></i>
                    <span class="text-slate-700">Kelola Anggota</span>
                </div>
                <h1 class="text-xl font-bold text-slate-900 tracking-tight mt-0.5">Anggota Grup: {{ $grup->nama_grup }}</h1>
            </div>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('grup.setJamKerja', Crypt::encrypt($grup->kode_grup)) }}" 
               class="px-4 py-2 text-xs font-semibold text-slate-700 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 shadow-sm transition-all flex items-center gap-1.5">
                <i class="ti ti-calendar-time text-base text-amber-600"></i>
                <span>Atur Jadwal Kerja</span>
            </a>
            @can('grup.detail')
                <button type="button" 
                        id="btnTambahKaryawanV2" 
                        class="px-4 py-2 text-xs font-bold text-white rounded-xl shadow-sm flex items-center gap-1.5 transition active:scale-95 cursor-pointer"
                        style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                    <i class="ti ti-user-plus text-sm"></i>
                    <span>Tambah Anggota</span>
                </button>
            @endcan
        </div>
    </div>

    <!-- Solid / Gradient Hero Card for Group Overview -->
    <div class="rounded-2xl border border-white/15 shadow-xl overflow-hidden relative" 
         style="background: linear-gradient(135deg, {{ $themeColor1 }} 0%, {{ $themeColor2 }} 100%);">
        
        <!-- Watermark icon -->
        <div class="absolute -right-6 -bottom-10 text-white/5 pointer-events-none select-none">
            <i class="ti ti-users text-[180px]"></i>
        </div>

        <div class="p-6 sm:p-7 relative z-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-5 text-white">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-white/20 border border-white/25 flex items-center justify-center text-2xl font-black shadow-inner backdrop-blur-xs">
                    <i class="ti ti-users-group"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2.5">
                        <h2 class="text-xl font-black text-white tracking-tight">{{ $grup->nama_grup }}</h2>
                        <span class="px-2.5 py-0.5 rounded-lg text-xs font-black bg-white/20 border border-white/30 text-white font-mono">
                            {{ $grup->kode_grup }}
                        </span>
                    </div>
                    <p class="text-xs text-white/85 font-medium mt-1">
                        Regu kerja operasional dan jadwal kerja shift terpadu
                    </p>
                </div>
            </div>

            <!-- Total Member Badge -->
            <div class="bg-white/15 border border-white/20 backdrop-blur-md rounded-xl px-4 py-3 flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-white/20 flex items-center justify-center text-lg">
                    <i class="ti ti-id-badge-2"></i>
                </div>
                <div>
                    <div class="text-[10.5px] font-bold uppercase tracking-wider text-emerald-100">Total Personel</div>
                    <div class="text-xl font-black text-white tracking-tight leading-none mt-0.5" id="memberCountBadge">
                        {{ $totalAnggota }} Pegawai
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Member List Container (Live AJAX Rendered) -->
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-sm font-extrabold text-slate-900 tracking-tight">Daftar Anggota Tim</h3>
                <p class="text-xs text-slate-500">Personel yang terdaftar dan mengikuti alur presensi grup ini</p>
            </div>
            <button type="button" 
                    onclick="loadAnggotaGrupV2()" 
                    class="text-xs font-bold text-slate-600 hover:text-emerald-700 flex items-center gap-1 transition"
                    title="Segarkan data">
                <i class="ti ti-refresh text-sm"></i>
                <span>Refresh</span>
            </button>
        </div>

        <div id="anggotaGrupListV2">
            @include('v2.grup.partials.anggota-list', ['karyawanInGrup' => $karyawanInGrup])
        </div>
    </div>
</div>

<!-- Modal Tambah Anggota V2 -->
<div class="modal fade" id="modalTambahKaryawanV2" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border border-slate-200/90 shadow-2xl rounded-2xl bg-white overflow-hidden">
            <div class="px-6 py-4 bg-white border-b border-slate-100 flex items-center justify-between text-slate-900">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center text-lg shrink-0 font-bold text-white shadow-sm"
                         style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-user-plus"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-sm sm:text-base font-extrabold text-slate-900 tracking-tight truncate mb-0">Tambah Anggota ke Grup</h5>
                        <p class="text-xs text-slate-500 mb-0 font-medium">Pilih karyawan aktif untuk didaftarkan ke grup {{ $grup->nama_grup }}</p>
                    </div>
                </div>
                <button type="button" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center transition-colors cursor-pointer shrink-0" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x text-lg"></i>
                </button>
            </div>
            <div class="modal-body p-6 bg-white" id="loadTambahKaryawanV2">
                <!-- Content loaded via AJAX -->
            </div>
        </div>
    </div>
</div>

@endsection

@push('myscript')
<script>
    const modalTambahEl = document.getElementById('modalTambahKaryawanV2');
    const modalTambahInstance = new bootstrap.Modal(modalTambahEl);

    function loadAnggotaGrupV2() {
        $.ajax({
            url: "{{ route('grup.getAnggotaGrup', Crypt::encrypt($grup->kode_grup)) }}",
            method: 'GET',
            success: function(response) {
                if (response.html) {
                    $('#anggotaGrupListV2').html(response.html);
                }
                if (response.count !== undefined) {
                    $('#memberCountBadge').text(`${response.count} Pegawai`);
                }
            }
        });
    }
    window.loadAnggotaGrupV2 = loadAnggotaGrupV2;

    $(function() {
        // Open Add Member Modal
        $('#btnTambahKaryawanV2').click(function(e) {
            e.preventDefault();
            modalTambahInstance.show();
            $('#loadTambahKaryawanV2').html(`
                <div class="py-10 flex flex-col items-center justify-center text-center">
                    <div class="w-10 h-10 border-4 border-emerald-500/20 border-t-emerald-600 rounded-full animate-spin mb-3"></div>
                    <p class="text-xs text-slate-500 font-medium">Memuat form pencarian anggota...</p>
                </div>
            `);
            $('#loadTambahKaryawanV2').load("{{ route('grup.createKaryawanForm', Crypt::encrypt($grup->kode_grup)) }}");
        });

        // Delete Member from Grup Action
        $(document).off('click', '.btn-delete-member').on('click', '.btn-delete-member', function(e) {
            e.preventDefault();
            const form = $(this).closest('form');
            const btn = $(this);

            Swal.fire({
                title: 'Hapus Anggota?',
                text: 'Karyawan ini akan dikeluarkan dari grup kerja ini.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    btn.prop('disabled', true);
                    $.ajax({
                        url: form.attr('action'),
                        method: 'DELETE',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            id: form.find('input[name="id"]').val()
                        },
                        success: function(response) {
                            if (window.toast) {
                                window.toast.success('Anggota berhasil dihapus dari grup.');
                            }
                            loadAnggotaGrupV2();
                        },
                        error: function(xhr) {
                            btn.prop('disabled', false);
                            let msg = 'Terjadi kesalahan saat menghapus anggota.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                msg = xhr.responseJSON.message;
                            }
                            if (window.toast) {
                                window.toast.error(msg);
                            }
                        }
                    });
                }
            });
        });
    });
</script>
@endpush
