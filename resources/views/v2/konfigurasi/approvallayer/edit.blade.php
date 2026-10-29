@extends('v2.layouts.app')

@section('titlepage', 'Ubah Approval Layer')
@section('page_title', 'Ubah Alur Persetujuan')

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
                <i class="ti ti-edit text-2xl" style="color: {{ $themeColor1 }};"></i>
                <span>Ubah Alur Persetujuan</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Sesuaikan kombinasi target karyawan atau ubah urutan level role pemberi persetujuan
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
                <a href="{{ route('approvallayer.index') }}" class="hover:text-slate-700 transition">
                    <span>Approval Layer</span>
                </a>
                <span class="mx-2 text-slate-300">/</span>
                <span class="font-bold text-slate-800">Edit Alur</span>
            </nav>

            <a href="{{ route('approvallayer.index') }}" 
               class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 text-xs font-bold transition active:scale-95 shadow-2xs">
                <i class="ti ti-arrow-left text-sm"></i>
                <span>Kembali ke Daftar</span>
            </a>
        </div>
    </div>

    <!-- ================= 2. BUILDER FORM ================= -->
    <form action="{{ route('approvallayer.updateGroup') }}" method="POST" id="form-approval-builder-edit" class="space-y-5">
        @csrf
        <!-- Keep track of original targets in case user changes them -->
        <input type="hidden" name="old_kode_cabang" value="{{ $kode_cabang ?? 'ALL' }}">
        <input type="hidden" name="old_kode_dept" value="{{ $kode_dept ?? 'ALL' }}">
        <input type="hidden" name="old_kode_jabatan" value="{{ $kode_jabatan ?? 'ALL' }}">
        <input type="hidden" name="feature" value="{{ $feature }}">

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
            <!-- Left Column: Scope & Target -->
            <div class="lg:col-span-5 space-y-4">
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                    <div class="p-4 border-b border-slate-100 flex items-center gap-3 bg-slate-50/50">
                        <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center text-base">
                            <i class="ti ti-adjustments"></i>
                        </div>
                        <div>
                            <h3 class="text-xs font-bold text-slate-800">Cakupan Target Karyawan</h3>
                            <p class="text-[10.5px] text-slate-400">Modul & kelompok karyawan yang berlaku</p>
                        </div>
                    </div>

                    <div class="p-5 space-y-4">
                        <!-- Fitur / Modul (Readonly badge) -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700">
                                Modul Fitur
                            </label>
                            <div class="w-full h-10 px-3.5 flex items-center justify-between text-xs font-bold text-slate-700 bg-slate-100 border border-slate-200 rounded-xl">
                                <span>{{ $feature === 'REIMBURSEMENT' ? 'REIMBURSEMENT (Klaim Keuangan)' : 'IZIN & ABSENSI (Cuti/Sakit/Izin)' }}</span>
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase {{ $feature === 'REIMBURSEMENT' ? 'bg-sky-200 text-sky-900' : 'bg-emerald-200 text-emerald-900' }}">
                                    {{ $feature }}
                                </span>
                            </div>
                        </div>

                        <!-- Cabang -->
                        <div class="space-y-1.5">
                            <label for="kode_cabang" class="block text-xs font-bold text-slate-700">
                                Cabang
                            </label>
                            <select name="kode_cabang" id="kode_cabang" class="w-full select2-v2" style="width: 100%;">
                                <option value="">Semua Cabang (Global)</option>
                                @foreach ($cabang as $c)
                                    <option value="{{ $c->kode_cabang }}" {{ $kode_cabang == $c->kode_cabang ? 'selected' : '' }}>
                                        {{ $c->nama_cabang }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Departemen -->
                        <div class="space-y-1.5">
                            <label for="kode_dept" class="block text-xs font-bold text-slate-700">
                                Departemen
                            </label>
                            <select name="kode_dept" id="kode_dept" class="w-full select2-v2" style="width: 100%;">
                                <option value="">Semua Departemen (Global)</option>
                                @foreach ($departemen as $d)
                                    <option value="{{ $d->kode_dept }}" {{ $kode_dept == $d->kode_dept ? 'selected' : '' }}>
                                        {{ $d->nama_dept }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Jabatan -->
                        <div class="space-y-1.5">
                            <label for="kode_jabatan" class="block text-xs font-bold text-slate-700">
                                Jabatan Spesifik
                            </label>
                            <select name="kode_jabatan" id="kode_jabatan" class="w-full select2-v2" style="width: 100%;">
                                <option value="">Semua Jabatan</option>
                                @foreach ($jabatan as $j)
                                    <option value="{{ $j->kode_jabatan }}" {{ $kode_jabatan == $j->kode_jabatan ? 'selected' : '' }}>
                                        {{ $j->nama_jabatan }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-[11px] text-slate-400">Kosongkan jika alur berlaku umum untuk semua posisi di departemen tersebut.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Flow Level Builder -->
            <div class="lg:col-span-7 space-y-4">
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden flex flex-col justify-between min-h-[460px]">
                    <!-- Header -->
                    <div class="p-4 border-b border-slate-100 flex items-center justify-between gap-3 bg-slate-50/50">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-sky-100 text-sky-800 flex items-center justify-center text-base">
                                <i class="ti ti-git-merge"></i>
                            </div>
                            <div>
                                <h3 class="text-xs font-bold text-slate-800">Struktur Level Persetujuan</h3>
                                <p class="text-[10.5px] text-slate-400">Sesuaikan urutan level role approver</p>
                            </div>
                        </div>

                        <span id="step-counter" class="px-2.5 py-1 rounded-full text-xs font-black bg-emerald-50 text-emerald-700 border border-emerald-200">
                            {{ count($approvalLayers) }} Langkah
                        </span>
                    </div>

                    <!-- Role Selector Toolbar -->
                    <div class="p-5 border-b border-slate-100 bg-slate-50/30">
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            Pilih Role Pemberi Persetujuan
                        </label>
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                            <div class="flex-1">
                                <select id="role_picker" class="w-full select2-v2" style="width: 100%;">
                                    <option value="">-- Pilih Role Pemberi Persetujuan --</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->name }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="button" 
                                    id="btn-add-role" 
                                    class="px-4 py-2.5 text-xs font-bold text-white rounded-xl shadow-xs flex items-center justify-center gap-1.5 transition active:scale-95 cursor-pointer shrink-0"
                                    style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                                <i class="ti ti-plus text-sm"></i>
                                <span>Tambah ke Alur</span>
                            </button>
                        </div>
                    </div>

                    <!-- Dynamic Steps List -->
                    <div class="p-5 flex-1 space-y-3" id="flow-container">
                        <!-- Empty State -->
                        <div class="empty-state py-12 text-center text-slate-400" style="display: none;">
                            <div class="w-14 h-14 mx-auto rounded-2xl bg-slate-100 text-slate-300 flex items-center justify-center text-3xl mb-2.5">
                                <i class="ti ti-git-branch"></i>
                            </div>
                            <h4 class="text-xs font-bold text-slate-600">Belum Ada Step Approval</h4>
                            <p class="text-[11px] text-slate-400 max-w-xs mx-auto mt-0.5">
                                Pilih role dari dropdown di atas dan klik tombol "Tambah ke Alur" untuk mulai menyusun urutan level.
                            </p>
                        </div>
                    </div>

                    <!-- Footer Action Buttons -->
                    <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2.5">
                        <a href="{{ route('approvallayer.index') }}" 
                           class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-200 text-xs font-bold transition active:scale-95 cursor-pointer">
                            Batal
                        </a>
                        <button type="button" 
                                id="btn-submit-config" 
                                class="px-5 py-2.5 text-xs font-bold text-white rounded-xl shadow-md flex items-center justify-center gap-1.5 transition active:scale-95 cursor-pointer"
                                style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                            <i class="ti ti-refresh text-sm"></i>
                            <span>Perbarui Konfigurasi</span>
                        </button>
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
        // Init Select2
        if (typeof $ !== 'undefined' && $.fn.select2) {
            $('#kode_cabang, #kode_dept, #kode_jabatan, #role_picker').select2({
                theme: 'v2',
                allowClear: true,
                width: '100%'
            });
        }

        let currentRoles = [
            @foreach($approvalLayers as $l)
                "{{ $l->role_name }}",
            @endforeach
        ];

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
                    <div class="flow-item flex items-center justify-between p-3 rounded-xl bg-white border border-slate-200 shadow-2xs transition hover:border-slate-300" data-index="${index}">
                        <input type="hidden" name="role_names[]" value="${role}">
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 rounded-lg text-white font-black text-xs flex items-center justify-center shadow-2xs shrink-0"
                                 style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                                ${level}
                            </div>
                            <div>
                                <div class="text-xs font-bold text-slate-800">${role}</div>
                                <div class="text-[10.5px] text-slate-400">Pemberi Persetujuan Tahap ke-${level}</div>
                            </div>
                        </div>
                        <button type="button" class="btn-remove-role w-7 h-7 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 flex items-center justify-center text-xs transition cursor-pointer" title="Hapus Step Ini">
                            <i class="ti ti-trash"></i>
                        </button>
                    </div>
                `;
                flowContainer.append(html);
            });
        }

        renderFlow();

        btnAddRole.on('click', function() {
            const role = rolePicker.val();
            if (!role) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pilih Role',
                    text: 'Silakan pilih role pemberi persetujuan terlebih dahulu!',
                    confirmButtonText: 'OK',
                    customClass: { confirmButton: 'rounded-xl px-4 py-2 text-xs font-bold' }
                });
                return;
            }

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
                    title: 'Alur Belum Ditentukan',
                    text: 'Alur approval minimal harus memiliki minimal 1 role pemberi persetujuan!',
                    confirmButtonText: 'OK',
                    customClass: { confirmButton: 'rounded-xl px-4 py-2 text-xs font-bold' }
                });
                return;
            }

            Swal.fire({
                title: 'Perbarui Konfigurasi?',
                text: 'Pastikan kombinasi target dan urutan level sudah sesuai.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Perbarui',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    confirmButton: 'rounded-xl px-4 py-2 text-xs font-bold',
                    cancelButton: 'rounded-xl px-4 py-2 text-xs font-bold'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#form-approval-builder-edit').submit();
                }
            });
        });
    });
</script>
@endpush
