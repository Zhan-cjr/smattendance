@extends('v2.layouts.app')

@section('titlepage', 'Rancang Approval Layer')
@section('page_title', 'Rancang Alur Persetujuan')

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
@endphp

<div class="space-y-5">

    <!-- ================= 1. PAGE HEADER ================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-1">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="ti ti-git-fork text-2xl" style="color: {{ $themeColor1 }};"></i>
                <span>Rancang Alur Persetujuan</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Tentukan target cabang, departemen, jabatan, dan susun urutan role bertingkat yang memberikan persetujuan
            </p>
        </div>

        <div class="flex flex-col md:items-end gap-2.5">
            <!-- Breadcrumbs -->
            <nav class="flex items-center text-xs text-slate-400 font-medium">
                <a href="{{ route('dashboard.index') }}" class="hover:text-slate-700 transition flex items-center gap-1">
                    <i class="ti ti-home text-sm"></i>
                    <span>Dashboard</span>
                </a>
                <span class="mx-2 text-slate-300">/</span>
                <span class="text-slate-500 flex items-center gap-1">
                    <i class="ti ti-settings text-sm"></i>
                    <span>Konfigurasi</span>
                </span>
                <span class="mx-2 text-slate-300">/</span>
                <a href="{{ route('approvallayer.index') }}" class="hover:text-slate-700 transition">
                    <span>Approval Layer</span>
                </a>
                <span class="mx-2 text-slate-300">/</span>
                <span class="font-bold text-slate-800">Tambah Alur</span>
            </nav>

            <a href="{{ route('approvallayer.index') }}" 
               class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 text-xs font-bold transition active:scale-95 shadow-2xs">
                <i class="ti ti-arrow-left text-sm"></i>
                <span>Kembali ke Daftar</span>
            </a>
        </div>
    </div>

    <!-- ================= 2. BUILDER FORM ================= -->
    <form action="{{ route('approvallayer.store') }}" method="POST" id="form-approval-builder" class="space-y-5" novalidate>
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
            
            <!-- Left Column: Scope & Target (Card 1) -->
            <div class="lg:col-span-5 space-y-4">
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-3 bg-gradient-to-r from-slate-50/80 to-white">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center text-base font-bold text-white shadow-xs shrink-0"
                             style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                            <i class="ti ti-adjustments"></i>
                        </div>
                        <div>
                            <h3 class="text-xs font-bold text-slate-800 tracking-tight">1. Target Lingkup Karyawan</h3>
                            <p class="text-[11px] text-slate-400">Pilih modul fitur dan unit target approval</p>
                        </div>
                    </div>

                    <div class="p-5 space-y-4">
                        <!-- Fitur / Modul -->
                        <div class="space-y-1.5">
                            <label for="feature" class="block text-xs font-bold text-slate-700">
                                Modul Fitur <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <select name="feature" id="feature" class="w-full select2-v2" style="width: 100%;">
                                    <option value="IZIN" {{ ($feature ?? 'IZIN') === 'IZIN' ? 'selected' : '' }}>IZIN & ABSENSI (Cuti / Sakit / Izin Keluar)</option>
                                    <option value="REIMBURSEMENT" {{ ($feature ?? '') === 'REIMBURSEMENT' ? 'selected' : '' }}>REIMBURSEMENT (Klaim Finansial)</option>
                                </select>
                            </div>
                            <span class="text-[10.5px] text-slate-400">Pilih jenis pengajuan yang akan menggunakan alur ini.</span>
                        </div>

                        <!-- Cabang -->
                        <div class="space-y-1.5">
                            <label for="kode_cabang" class="block text-xs font-bold text-slate-700">
                                Cabang
                            </label>
                            <div class="relative">
                                <select name="kode_cabang" id="kode_cabang" class="w-full select2-v2" style="width: 100%;">
                                    <option value="">Semua Cabang (Global Seluruh Kantor)</option>
                                    @foreach ($cabang as $c)
                                        <option value="{{ $c->kode_cabang }}">{{ $c->nama_cabang }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <span class="text-[10.5px] text-slate-400">Biarkan global jika berlaku untuk seluruh kantor cabang.</span>
                        </div>

                        <!-- Departemen -->
                        <div class="space-y-1.5">
                            <label for="kode_dept" class="block text-xs font-bold text-slate-700">
                                Departemen
                            </label>
                            <div class="relative">
                                <select name="kode_dept" id="kode_dept" class="w-full select2-v2" style="width: 100%;">
                                    <option value="">Semua Departemen (Global)</option>
                                    @foreach ($departemen as $d)
                                        <option value="{{ $d->kode_dept }}">{{ $d->nama_dept }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Jabatan -->
                        <div class="space-y-1.5">
                            <label for="kode_jabatan" class="block text-xs font-bold text-slate-700">
                                Jabatan Spesifik (Opsional)
                            </label>
                            <div class="relative">
                                <select name="kode_jabatan" id="kode_jabatan" class="w-full select2-v2" style="width: 100%;">
                                    <option value="">Semua Jabatan di Departemen Terpilih</option>
                                    @foreach ($jabatan as $j)
                                        <option value="{{ $j->kode_jabatan }}">{{ $j->nama_jabatan }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <span class="text-[10.5px] text-slate-400">Gunakan bila ingin alur khusus bagi posisi struktural tertentu.</span>
                        </div>
                    </div>
                </div>

                <!-- Info Help Card -->
                <div class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-200/80 flex items-start gap-3">
                    <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center shrink-0 text-base">
                        <i class="ti ti-info-circle"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-emerald-950">Cara Kerja Approval Bertingkat</h4>
                        <p class="text-[11px] text-emerald-800 mt-0.5 leading-relaxed">
                            Pengajuan yang diajukan oleh karyawan dengan kriteria target di atas akan diteruskan secara berurutan mulai dari <b>Level 1</b>. Setelah Level 1 menyetujui, pengajuan akan naik ke level berikutnya secara otomatis hingga level akhir.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Right Column: Flow Level Builder (Card 2) -->
            <div class="lg:col-span-7 space-y-4">
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden flex flex-col justify-between min-h-[500px]">
                    
                    <!-- Header -->
                    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between gap-3 bg-gradient-to-r from-slate-50/80 to-white">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-sky-100 text-sky-800 flex items-center justify-center text-base font-bold shadow-xs shrink-0">
                                <i class="ti ti-git-merge"></i>
                            </div>
                            <div>
                                <h3 class="text-xs font-bold text-slate-800 tracking-tight">2. Susunan Tahapan Level Persetujuan</h3>
                                <p class="text-[11px] text-slate-400">Tambahkan dan atur urutan hierarki approval</p>
                            </div>
                        </div>

                        <span id="step-counter" class="px-3 py-1 rounded-full text-xs font-black bg-emerald-50 text-emerald-800 border border-emerald-200 shadow-2xs">
                            0 Langkah
                        </span>
                    </div>

                    <!-- Role Picker Action Bar -->
                    <div class="p-5 border-b border-slate-100 bg-slate-50/40">
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            Pilih Role Pemberi Persetujuan <span class="text-rose-500">*</span>
                        </label>
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
                            <div class="flex-1 min-w-0">
                                <select id="role_picker" class="w-full select2-v2" style="width: 100%;">
                                    <option value="">-- Pilih Role Pemberi Persetujuan --</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->name }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="button" 
                                    id="btn-add-role" 
                                    class="px-4 py-2.5 text-xs font-bold text-white rounded-xl shadow-xs flex items-center justify-center gap-1.5 transition active:scale-95 cursor-pointer shrink-0 hover:opacity-95"
                                    style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                                <i class="ti ti-plus text-sm"></i>
                                <span>Tambah ke Alur</span>
                            </button>
                        </div>
                        <div class="text-rose-500 text-[11px] font-medium mt-1.5 hidden" id="err_role_picker">Silakan pilih role terlebih dahulu.</div>
                    </div>

                    <!-- Steps Timeline Container -->
                    <div class="p-5 flex-1 space-y-3" id="flow-container">
                        <!-- Empty State Illustration -->
                        <div class="empty-state py-16 text-center text-slate-400">
                            <div class="w-16 h-16 mx-auto rounded-2xl bg-slate-100 text-slate-300 flex items-center justify-center text-3xl mb-3">
                                <i class="ti ti-git-branch"></i>
                            </div>
                            <h4 class="text-xs font-bold text-slate-700">Belum Ada Tahapan Approval</h4>
                            <p class="text-[11px] text-slate-400 max-w-sm mx-auto mt-1 leading-relaxed">
                                Pilih role dari dropdown di atas dan klik <b>"Tambah ke Alur"</b> untuk menambahkan tahapan persetujuan level 1, level 2, dan seterusnya.
                            </p>
                        </div>
                    </div>

                    <!-- Card Footer Actions -->
                    <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between gap-3">
                        <span class="text-[11px] text-slate-400 font-medium pl-1 hidden sm:inline">
                            Pastikan minimal ada 1 level approval
                        </span>

                        <div class="flex items-center gap-2.5 ml-auto">
                            <a href="{{ route('approvallayer.index') }}" 
                               class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-200 text-xs font-bold transition active:scale-95 cursor-pointer">
                                Batal
                            </a>
                            <button type="button" 
                                    id="btn-submit-config" 
                                    class="px-5 py-2.5 text-xs font-bold text-white rounded-xl shadow-md flex items-center justify-center gap-1.5 transition active:scale-95 cursor-pointer hover:opacity-95"
                                    style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                                <i class="ti ti-device-floppy text-sm"></i>
                                <span>Simpan Konfigurasi</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@endsection

@push('myscript')
<script>
    $(document).ready(function() {
        // Initialize Select2 V2 with theme
        if (typeof $ !== 'undefined' && $.fn.select2) {
            $('#feature, #kode_cabang, #kode_dept, #kode_jabatan, #role_picker').select2({
                theme: 'v2',
                allowClear: false,
                width: '100%'
            });
        }

        let currentRoles = [];
        const flowContainer = $('#flow-container');
        const emptyState = $('.empty-state');
        const rolePicker = $('#role_picker');
        const btnAddRole = $('#btn-add-role');
        const stepCounter = $('#step-counter');

        function renderFlow() {
            flowContainer.find('.flow-item').remove();

            if (currentRoles.length === 0) {
                emptyState.show();
                stepCounter.text('0 Langkah');
                return;
            }

            emptyState.hide();
            stepCounter.text(currentRoles.length + ' Langkah');

            currentRoles.forEach((role, index) => {
                const level = index + 1;
                const html = `
                    <div class="flow-item flex items-center justify-between p-3.5 rounded-xl bg-white border border-slate-200/90 shadow-2xs transition hover:border-slate-300 group" data-index="${index}">
                        <input type="hidden" name="role_names[]" value="${role}">
                        <div class="flex items-center gap-3 min-w-0 pr-2">
                            <div class="w-8 h-8 rounded-xl text-white font-black text-xs flex items-center justify-center shadow-xs shrink-0"
                                 style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                                ${level}
                            </div>
                            <div class="min-w-0">
                                <div class="text-xs font-bold text-slate-800 truncate">${role}</div>
                                <div class="text-[10.5px] text-slate-400 font-medium">Pemberi Persetujuan Tahap ke-${level}</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-1.5 shrink-0">
                            <button type="button" class="btn-remove-role w-8 h-8 rounded-xl bg-rose-50 border border-rose-100 text-rose-600 hover:bg-rose-100 flex items-center justify-center text-sm transition cursor-pointer active:scale-95" title="Hapus Tahap Ini">
                                <i class="ti ti-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
                flowContainer.append(html);
            });
        }

        btnAddRole.on('click', function() {
            const role = rolePicker.val();
            if (!role) {
                $('#err_role_picker').removeClass('hidden');
                Swal.fire({
                    icon: 'warning',
                    title: 'Pilih Role',
                    text: 'Silakan pilih role pemberi persetujuan terlebih dahulu dari daftar!',
                    confirmButtonText: 'OK',
                    customClass: { confirmButton: 'rounded-xl px-4 py-2 text-xs font-bold' }
                });
                return;
            }

            $('#err_role_picker').addClass('hidden');
            currentRoles.push(role);
            rolePicker.val('').trigger('change');
            renderFlow();
        });

        flowContainer.on('click', '.btn-remove-role', function() {
            const index = $(this).closest('.flow-item').data('index');
            currentRoles.splice(index, 1);
            renderFlow();
        });

        $('#btn-submit-config').on('click', function(e) {
            e.preventDefault();
            if (currentRoles.length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Tahapan Approval Kosong',
                    text: 'Alur approval harus memiliki minimal 1 role pemberi persetujuan!',
                    confirmButtonText: 'OK',
                    customClass: { confirmButton: 'rounded-xl px-4 py-2 text-xs font-bold' }
                });
                return;
            }

            Swal.fire({
                title: 'Simpan Konfigurasi?',
                text: 'Kombinasi target dan urutan tahapan approval akan disimpan ke sistem.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Simpan',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    confirmButton: 'rounded-xl px-4 py-2 text-xs font-bold',
                    cancelButton: 'rounded-xl px-4 py-2 text-xs font-bold'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#form-approval-builder').submit();
                }
            });
        });
    });
</script>
@endpush
