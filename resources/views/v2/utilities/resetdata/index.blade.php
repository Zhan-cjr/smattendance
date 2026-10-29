@extends('v2.layouts.app')

@section('titlepage', 'Reset Data Sistem')
@section('page_title', 'Reset Data Sistem')

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
@endphp

<div class="space-y-5">

    <!-- ================= 1. PAGE HEADER (TITLE & BREADCRUMBS) ================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-1">
        <!-- Title & Subtitle -->
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="ti ti-database-off text-2xl text-rose-600"></i>
                <span>Reset Data Sistem</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Utilitas pengosongan data transaksi, master karyawan, dan catatan sistem untuk persiapan inisialisasi ulang
            </p>
        </div>

        <!-- Breadcrumbs -->
        <nav class="flex items-center text-xs text-slate-400 font-medium self-start md:self-auto">
            <a href="{{ route('dashboard.index') }}" class="hover:text-slate-700 transition flex items-center gap-1">
                <i class="ti ti-home text-sm"></i>
                <span>Dashboard</span>
            </a>
            <span class="mx-2 text-slate-300">/</span>
            <span class="text-slate-500 flex items-center gap-1">
                <i class="ti ti-tool text-sm"></i>
                <span>Utilities</span>
            </span>
            <span class="mx-2 text-slate-300">/</span>
            <span class="font-bold text-slate-800">Reset Data</span>
        </nav>
    </div>

    <!-- ================= 2. CRITICAL WARNING BANNER ================= -->
    <div class="p-4 sm:p-5 rounded-2xl bg-rose-50/90 border border-rose-200 text-rose-950 shadow-2xs space-y-3">
        <div class="flex items-start gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center text-xl shrink-0 mt-0.5 border border-rose-200">
                <i class="ti ti-alert-triangle"></i>
            </div>
            <div class="space-y-1 text-xs flex-1">
                <h4 class="text-sm font-black text-rose-900 tracking-tight">Peringatan Kritis: Tindakan Permanen & Tidak Dapat Dibatalkan!</h4>
                <p class="text-rose-900/90 leading-relaxed text-[11.5px]">
                    Fitur ini akan mengosongkan <strong>SEMUA data transaksi dan data master</strong> dari database. Pastikan Anda telah membuat cadangan database (<a href="{{ \Illuminate\Support\Facades\Route::has('backup.index') ? route('backup.index') : '#' }}" class="underline font-bold text-rose-800 hover:text-rose-950">Download Backup</a>) sebelum melanjutkan proses ini.
                </p>
            </div>
        </div>

        <!-- Protected Data Chips -->
        <div class="pt-3 border-t border-rose-200/70">
            <span class="text-[11px] font-extrabold uppercase tracking-wider text-rose-900 block mb-2">
                <i class="ti ti-shield-check text-emerald-600 text-xs mr-1"></i> Data Inti yang Tetap Aman (TIDAK Dihapus):
            </span>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-2">
                <div class="bg-white/80 border border-rose-200/80 rounded-lg p-2 text-center shadow-2xs">
                    <span class="text-[11px] font-bold text-slate-700 block">Super Admin User</span>
                </div>
                <div class="bg-white/80 border border-rose-200/80 rounded-lg p-2 text-center shadow-2xs">
                    <span class="text-[11px] font-bold text-slate-700 block">Roles & Permissions</span>
                </div>
                <div class="bg-white/80 border border-rose-200/80 rounded-lg p-2 text-center shadow-2xs">
                    <span class="text-[11px] font-bold text-slate-700 block">Permission Groups</span>
                </div>
                <div class="bg-white/80 border border-rose-200/80 rounded-lg p-2 text-center shadow-2xs">
                    <span class="text-[11px] font-bold text-slate-700 block">Pengaturan Umum</span>
                </div>
                <div class="bg-white/80 border border-rose-200/80 rounded-lg p-2 text-center shadow-2xs">
                    <span class="text-[11px] font-bold text-slate-700 block">Status Pernikahan</span>
                </div>
                <div class="bg-white/80 border border-rose-200/80 rounded-lg p-2 text-center shadow-2xs">
                    <span class="text-[11px] font-bold text-slate-700 block">Aturan Denda</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= 3. MAIN SECTION: SCOPE LIST & CONFIRMATION BOX ================= -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
        
        <!-- Left: Data Categories that will be deleted (8 cols) -->
        <div class="lg:col-span-8 bg-white border border-slate-200/90 rounded-2xl shadow-xs overflow-hidden">
            <!-- Solid Header -->
            <div class="px-5 py-3.5 flex items-center justify-between text-white border-0"
                 style="background-color: {{ $themeColor1 }};">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-white/20 text-white flex items-center justify-center text-xs font-bold border border-white/20 shadow-2xs backdrop-blur-xs">
                        <i class="ti ti-list-check"></i>
                    </div>
                    <h3 class="text-sm font-extrabold text-white tracking-tight">
                        Cakupan Data yang Akan Dihapus
                    </h3>
                </div>
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-white/20 text-white border border-white/30 shadow-2xs backdrop-blur-xs">
                    Semua Tabel Terkait
                </span>
            </div>

            <!-- Body Cards Grid -->
            <div class="p-5 sm:p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5">
                
                <!-- 1. Master Organisasi & Karyawan -->
                <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50/40 space-y-2">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-rose-100 text-rose-700 flex items-center justify-center text-xs font-bold shadow-2xs">
                            <i class="ti ti-users"></i>
                        </div>
                        <h4 class="text-xs font-extrabold text-slate-800 tracking-tight">1. Master & Organisasi</h4>
                    </div>
                    <ul class="space-y-1 text-[11px] text-slate-600 font-medium">
                        <li class="flex items-center gap-1.5"><i class="ti ti-circle-filled text-[4px] text-rose-500"></i> Karyawan, Kontrak & Status</li>
                        <li class="flex items-center gap-1.5"><i class="ti ti-circle-filled text-[4px] text-rose-500"></i> Cabang, Dept, & Jabatan</li>
                        <li class="flex items-center gap-1.5"><i class="ti ti-circle-filled text-[4px] text-rose-500"></i> Grup & Mesin Fingerprint</li>
                        <li class="flex items-center gap-1.5"><i class="ti ti-circle-filled text-[4px] text-rose-500"></i> Akun User Non-Super Admin</li>
                    </ul>
                </div>

                <!-- 2. Presensi, Izin & Koreksi -->
                <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50/40 space-y-2">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center text-xs font-bold shadow-2xs">
                            <i class="ti ti-clock"></i>
                        </div>
                        <h4 class="text-xs font-extrabold text-slate-800 tracking-tight">2. Presensi & Kedisiplinan</h4>
                    </div>
                    <ul class="space-y-1 text-[11px] text-slate-600 font-medium">
                        <li class="flex items-center gap-1.5"><i class="ti ti-circle-filled text-[4px] text-rose-500"></i> Log Presensi (Check-in/Out)</li>
                        <li class="flex items-center gap-1.5"><i class="ti ti-circle-filled text-[4px] text-rose-500"></i> Koreksi Jam Presensi</li>
                        <li class="flex items-center gap-1.5"><i class="ti ti-circle-filled text-[4px] text-rose-500"></i> Izin, Cuti, Sakit, & Dinas</li>
                        <li class="flex items-center gap-1.5"><i class="ti ti-circle-filled text-[4px] text-rose-500"></i> Log Mesin & Face Biometric</li>
                    </ul>
                </div>

                <!-- 3. Proyek & Task Management -->
                <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50/40 space-y-2">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center text-xs font-bold shadow-2xs">
                            <i class="ti ti-folder"></i>
                        </div>
                        <h4 class="text-xs font-extrabold text-slate-800 tracking-tight">3. Proyek & Tugas</h4>
                    </div>
                    <ul class="space-y-1 text-[11px] text-slate-600 font-medium">
                        <li class="flex items-center gap-1.5"><i class="ti ti-circle-filled text-[4px] text-rose-500"></i> Data Proyek & Kategori</li>
                        <li class="flex items-center gap-1.5"><i class="ti ti-circle-filled text-[4px] text-rose-500"></i> Tugas (Tasks) & Anggota Tim</li>
                        <li class="flex items-center gap-1.5"><i class="ti ti-circle-filled text-[4px] text-rose-500"></i> Lampiran & Komentar Task</li>
                        <li class="flex items-center gap-1.5"><i class="ti ti-circle-filled text-[4px] text-rose-500"></i> Master Klien / Pelanggan</li>
                    </ul>
                </div>

                <!-- 4. Reimbursement & Klaim -->
                <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50/40 space-y-2">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-cyan-100 text-cyan-700 flex items-center justify-center text-xs font-bold shadow-2xs">
                            <i class="ti ti-receipt-2"></i>
                        </div>
                        <h4 class="text-xs font-extrabold text-slate-800 tracking-tight">4. Klaim Reimbursement</h4>
                    </div>
                    <ul class="space-y-1 text-[11px] text-slate-600 font-medium">
                        <li class="flex items-center gap-1.5"><i class="ti ti-circle-filled text-[4px] text-rose-500"></i> Pengajuan Reimbursement</li>
                        <li class="flex items-center gap-1.5"><i class="ti ti-circle-filled text-[4px] text-rose-500"></i> Rincian Nota & Kwitansi</li>
                        <li class="flex items-center gap-1.5"><i class="ti ti-circle-filled text-[4px] text-rose-500"></i> Saldo Klaim per Karyawan</li>
                        <li class="flex items-center gap-1.5"><i class="ti ti-circle-filled text-[4px] text-rose-500"></i> Master Jenis Reimbursement</li>
                    </ul>
                </div>

                <!-- 5. Payroll, Harian, PPh21 & Lembur -->
                <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50/40 space-y-2">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs font-bold shadow-2xs">
                            <i class="ti ti-wallet"></i>
                        </div>
                        <h4 class="text-xs font-extrabold text-slate-800 tracking-tight">5. Payroll, Pajak & Gaji</h4>
                    </div>
                    <ul class="space-y-1 text-[11px] text-slate-600 font-medium">
                        <li class="flex items-center gap-1.5"><i class="ti ti-circle-filled text-[4px] text-rose-500"></i> Slip Gaji Bulanan & Harian</li>
                        <li class="flex items-center gap-1.5"><i class="ti ti-circle-filled text-[4px] text-rose-500"></i> Rincian PPh 21 Slip Gaji</li>
                        <li class="flex items-center gap-1.5"><i class="ti ti-circle-filled text-[4px] text-rose-500"></i> Pinjaman & Rencana Cicilan</li>
                        <li class="flex items-center gap-1.5"><i class="ti ti-circle-filled text-[4px] text-rose-500"></i> Lembur & Aturan Khusus</li>
                    </ul>
                </div>

                <!-- 6. Jadwal, Roster, KPI & Multi-Approval -->
                <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50/40 space-y-2">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-purple-100 text-purple-700 flex items-center justify-center text-xs font-bold shadow-2xs">
                            <i class="ti ti-calendar-event"></i>
                        </div>
                        <h4 class="text-xs font-extrabold text-slate-800 tracking-tight">6. Roster, KPI & Approval</h4>
                    </div>
                    <ul class="space-y-1 text-[11px] text-slate-600 font-medium">
                        <li class="flex items-center gap-1.5"><i class="ti ti-circle-filled text-[4px] text-rose-500"></i> Pola Shift & Roster Jadwal</li>
                        <li class="flex items-center gap-1.5"><i class="ti ti-circle-filled text-[4px] text-rose-500"></i> Periode & Nilai KPI Karyawan</li>
                        <li class="flex items-center gap-1.5"><i class="ti ti-circle-filled text-[4px] text-rose-500"></i> Riwayat & Layer Approval</li>
                        <li class="flex items-center gap-1.5"><i class="ti ti-circle-filled text-[4px] text-rose-500"></i> Log Login, Notif & Device Token</li>
                    </ul>
                </div>

            </div>
        </div>

        <!-- Right: Reset Confirmation Action Card (4 cols) -->
        <div class="lg:col-span-4 bg-white border-2 border-rose-400 rounded-2xl shadow-md overflow-hidden">
            <!-- Header -->
            <div class="px-5 py-3.5 flex items-center gap-2.5 bg-rose-600 text-white">
                <div class="w-7 h-7 rounded-lg bg-white/20 flex items-center justify-center text-xs font-bold border border-white/20">
                    <i class="ti ti-trash"></i>
                </div>
                <h3 class="text-sm font-extrabold tracking-tight">
                    Eksekusi Reset Data
                </h3>
            </div>

            <!-- Body Form -->
            <div class="p-5 sm:p-6 space-y-5">
                <div class="text-center space-y-2">
                    <div class="w-14 h-14 mx-auto rounded-2xl bg-rose-50 border border-rose-200 flex items-center justify-center text-rose-600 text-2xl">
                        <i class="ti ti-shield-lock"></i>
                    </div>
                    <p class="text-xs text-slate-600 font-medium leading-relaxed">
                        Untuk keamanan dan mencegah ketidaksengajaan, ketik kata konfirmasi di bawah ini:
                    </p>
                </div>

                <div class="space-y-2">
                    <label for="konfirmasi" class="block text-xs font-extrabold text-slate-800 uppercase tracking-wider">
                        Ketik: <span class="text-rose-600 font-mono tracking-normal">RESET DATA</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                            <i class="ti ti-keyboard text-base"></i>
                        </span>
                        <input type="text" id="konfirmasi" name="konfirmasi" placeholder="RESET DATA" autocomplete="off"
                               class="w-full h-11 pl-10 pr-4 bg-slate-50 focus:bg-white border-2 border-slate-300 rounded-xl text-xs font-black tracking-wider text-slate-900 focus:outline-none transition">
                    </div>
                    <p class="text-[11px] text-slate-400">
                        * Karakter harus persis huruf besar seluruhnya.
                    </p>
                </div>

                <!-- Submit Button -->
                <button type="button" id="btnResetDataV2" disabled
                        class="w-full h-11 px-4 text-white font-bold text-xs rounded-xl shadow-sm transition inline-flex items-center justify-center gap-2 cursor-not-allowed bg-rose-600 opacity-40">
                    <i class="ti ti-trash text-base"></i>
                    <span>Reset Semua Data Sistem</span>
                </button>
            </div>
        </div>

    </div>

</div>

@endsection

@push('myscript')
<script>
$(document).ready(function() {

    // Validasi input konfirmasi secara real-time
    $('#konfirmasi').on('input', function() {
        let val = $(this).val().trim();
        let btn = $('#btnResetDataV2');

        if (val === 'RESET DATA') {
            btn.prop('disabled', false)
               .removeClass('opacity-40 cursor-not-allowed')
               .addClass('hover:bg-rose-700 active:scale-95 cursor-pointer shadow-md');
            $(this).removeClass('border-slate-300 border-rose-500 bg-slate-50').addClass('border-emerald-500 bg-emerald-50/20 text-emerald-800');
        } else {
            btn.prop('disabled', true)
               .addClass('opacity-40 cursor-not-allowed')
               .removeClass('hover:bg-rose-700 active:scale-95 cursor-pointer shadow-md');
            
            if (val.length > 0) {
                $(this).removeClass('border-slate-300 border-emerald-500 bg-emerald-50/20 text-emerald-800').addClass('border-rose-400 bg-rose-50/20 text-slate-900');
            } else {
                $(this).removeClass('border-emerald-500 border-rose-400 bg-emerald-50/20 bg-rose-50/20 text-emerald-800').addClass('border-slate-300 bg-slate-50 text-slate-900');
            }
        }
    });

    // Handle tombol klik konfirmasi SweetAlert2
    $('#btnResetDataV2').on('click', function(e) {
        e.preventDefault();

        if ($(this).prop('disabled')) return;

        Swal.fire({
            title: 'Konfirmasi Terakhir!',
            html: `
                <div class="text-xs text-slate-600 text-left space-y-2 mt-2">
                    <p class="font-bold text-slate-800">Apakah Anda benar-benar yakin ingin mengosongkan seluruh database?</p>
                    <div class="p-3 bg-rose-50 border border-rose-200 rounded-xl space-y-1.5 text-rose-900">
                        <div>&bull; <strong>Semua data karyawan, presensi, gaji, dan transaksi</strong> akan dihapus permanen.</div>
                        <div>&bull; Hanya akun Super Admin dan hak akses role yang akan dipertahankan.</div>
                    </div>
                    <p class="text-slate-500 italic">* Pastikan Anda sudah mengunduh backup database sebelum menekan tombol di bawah.</p>
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Reset Data Sekarang!',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'px-5 py-2.5 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl shadow-md transition active:scale-95 cursor-pointer',
                cancelButton: 'px-4 py-2.5 text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition active:scale-95 mr-2 cursor-pointer'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                executeReset();
            }
        });
    });

    function executeReset() {
        Swal.fire({
            title: 'Sedang Mereset Data...',
            html: `
                <div class="text-xs text-slate-500 space-y-2 py-2">
                    <p>Mohon tunggu beberapa saat. Server sedang mengosongkan tabel database...</p>
                    <p class="text-[11px] text-rose-500 font-semibold">Jangan tutup atau refresh jendela browser ini.</p>
                </div>
            `,
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: '{{ route("resetdata.reset") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                konfirmasi: $('#konfirmasi').val().trim()
            },
            dataType: 'json',
            timeout: 300000,
            success: function(response) {
                if (response && response.success) {
                    let detailTables = '';
                    if (response.deleted_tables && response.deleted_tables.length > 0) {
                        detailTables = `
                            <div class="mt-3 text-left max-h-48 overflow-y-auto p-3 bg-slate-50 rounded-xl border border-slate-200 text-[11px] font-mono space-y-1">
                                <div class="font-bold text-slate-700 mb-1">Tabel Berhasil Dikosongkan:</div>
                                ${response.deleted_tables.map(t => `<div class="text-slate-600">&bull; ${t.table} (${t.count} records)</div>`).join('')}
                            </div>
                        `;
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Reset Berhasil!',
                        html: `
                            <div class="text-xs text-slate-600">
                                <p class="font-bold text-slate-800">${response.success}</p>
                                ${detailTables}
                            </div>
                        `,
                        confirmButtonText: 'Selesai & Muat Ulang',
                        customClass: {
                            confirmButton: 'px-5 py-2.5 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-md cursor-pointer'
                        },
                        buttonsStyling: false,
                        allowOutsideClick: false
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Mereset!',
                        text: response.error || 'Terjadi kesalahan saat mereset data.',
                        confirmButtonText: 'Tutup'
                    });
                }
            },
            error: function(xhr) {
                let msg = 'Terjadi kesalahan sistem saat menghubungi server.';
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    msg = xhr.responseJSON.error;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Memproses!',
                    html: `<div class="text-xs text-slate-600">${msg}</div>`,
                    confirmButtonText: 'Tutup'
                });
            }
        });
    }

});
</script>
@endpush
