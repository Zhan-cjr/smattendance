@extends('v2.layouts.app')
@section('titlepage', 'Monitoring Master')
@section('page_title', 'Monitoring Master')

@section('content')
@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
@endphp

<!-- Password authentication modal -->
<div class="modal fade" id="monitoringAuthModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="p-5 border-b border-slate-100 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white"
                     style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                    <i class="ti ti-lock text-lg"></i>
                </div>
                <div>
                    <h5 class="text-sm font-bold text-slate-800">Autentikasi Monitoring Master</h5>
                    <p class="text-[11px] text-slate-500">Masukkan password otorisasi untuk mengakses menu ini</p>
                </div>
            </div>
            <div class="p-5 space-y-3">
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1.5">Password Akses</label>
                    <input type="password" id="monitoringPasswordInput"
                           class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:border-emerald-600"
                           placeholder="Masukkan password...">
                </div>
                <div id="monitoringPasswordError" class="text-rose-600 text-xs font-bold hidden">
                    Password salah. Coba lagi.
                </div>
            </div>
            <div class="px-5 py-3.5 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" id="monitoringPasswordBack"
                        class="px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-bold cursor-pointer">
                    Kembali
                </button>
                <button type="button" id="monitoringPasswordSubmit"
                        class="px-4 py-2 rounded-xl text-white text-xs font-bold shadow-2xs cursor-pointer"
                        style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                    Masuk
                </button>
            </div>
        </div>
    </div>
</div>

<div id="monitoringMasterContent" style="display: none;" class="space-y-4">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-1">
        <div>
            <h1 class="text-lg sm:text-xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                <i class="ti ti-eye text-xl" style="color: {{ $themeColor1 }};"></i>
                <span>Monitoring Master</span>
            </h1>
            <p class="text-[11px] text-slate-500 mt-0.5">
                Kelola daftar karyawan yang akan dipantau lokasi real-time nya
            </p>
        </div>
        <div>
            <button type="button" data-bs-toggle="modal" data-bs-target="#searchModal"
                    class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-white text-xs font-bold shadow-2xs transition hover:opacity-95 cursor-pointer"
                    style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                <i class="ti ti-plus text-sm"></i>
                <span>Tambah Karyawan</span>
            </button>
        </div>
    </div>

    <!-- Main Card -->
    <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs overflow-hidden">
        <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between bg-slate-50/60">
            <h5 class="text-xs sm:text-sm font-bold text-slate-800 flex items-center gap-2">
                <i class="ti ti-radar-2 text-emerald-600 text-base"></i>
                <span>Karyawan Dipantau ({{ $spyEmployees->count() }})</span>
            </h5>
        </div>
        <div class="p-0">
            @if($spyEmployees->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-slate-50 text-slate-600 uppercase text-[11px] font-bold border-b border-slate-100">
                            <tr>
                                <th class="px-4 py-3 w-12">No</th>
                                <th class="px-4 py-3">NIK</th>
                                <th class="px-4 py-3">Nama Karyawan</th>
                                <th class="px-4 py-3">Jabatan</th>
                                <th class="px-4 py-3">Departemen</th>
                                <th class="px-4 py-3 text-center w-24">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($spyEmployees as $i => $emp)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="px-4 py-3 font-semibold text-slate-500">{{ $i + 1 }}</td>
                                    <td class="px-4 py-3">
                                        <span class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 font-mono font-bold text-[11px]">
                                            {{ $emp->nik }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 font-bold text-slate-800">{{ $emp->nama_karyawan }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ $emp->jabatan->nama_jabatan ?? '-' }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ $emp->departemen->nama_dept ?? '-' }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <button type="button"
                                                onclick="hapus('{{ $emp->nik }}', '{{ addslashes($emp->nama_karyawan) }}')"
                                                class="w-8 h-8 rounded-lg bg-rose-50 hover:bg-rose-600 text-rose-600 hover:text-white border border-rose-200 inline-flex items-center justify-center transition cursor-pointer"
                                                title="Hapus dari Monitoring">
                                            <i class="ti ti-trash text-sm"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12 text-slate-400">
                    <i class="ti ti-inbox text-4xl block mb-2 opacity-40"></i>
                    <p class="text-xs font-semibold">Belum ada karyawan yang dipantau</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal Search -->
    <div class="modal fade" id="searchModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between">
                    <h5 class="text-sm font-bold text-slate-800">Tambah Karyawan ke Monitoring</h5>
                    <button type="button" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center cursor-pointer" data-bs-dismiss="modal">
                        <i class="ti ti-x"></i>
                    </button>
                </div>
                <div class="p-4 sm:p-5 space-y-3">
                    <div>
                        <input type="text" id="searchInput"
                               class="w-full h-10 px-3.5 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:border-emerald-600"
                               placeholder="Ketik nama atau NIK karyawan (minimal 2 karakter)...">
                    </div>
                    <div id="searchResults" class="space-y-2 max-h-80 overflow-y-auto"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('myscript')
<script>
const CSRF = '{{ csrf_token() }}';

function escapeQuotes(str) {
    return String(str).replace(/'/g, "\\'");
}

function hapus(nik, nama) {
    Swal.fire({
        icon: 'warning',
        title: 'Konfirmasi Hapus',
        text: 'Hapus ' + nama + ' dari monitoring?',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (!result.isConfirmed) return;

        fetch('{{ route("monitoring-master.remove") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF
            },
            body: JSON.stringify({ nik: nik })
        })
        .then(r => r.json())
        .then(data => {
            if (data.ok) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Karyawan berhasil dihapus dari monitoring',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => location.reload());
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: data.error || 'Terjadi kesalahan'
                });
            }
        })
        .catch(e => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: e.message
            });
        });
    });
}

function tambah(nik, nama) {
    Swal.fire({
        icon: 'warning',
        title: 'Konfirmasi Tambah',
        text: 'Tambahkan ' + nama + ' ke monitoring?',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Tambahkan',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (!result.isConfirmed) return;

        fetch('{{ route("monitoring-master.add") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF
            },
            body: JSON.stringify({ nik: nik })
        })
        .then(r => r.json())
        .then(data => {
            if (data.ok) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Karyawan berhasil ditambahkan ke monitoring',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => location.reload());
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: data.error || 'Terjadi kesalahan'
                });
            }
        })
        .catch(e => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: e.message
            });
        });
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const pagePassword = 'eagleeye';
    const authModalEl = document.getElementById('monitoringAuthModal');
    const authPasswordInput = document.getElementById('monitoringPasswordInput');
    const authError = document.getElementById('monitoringPasswordError');
    const authSubmit = document.getElementById('monitoringPasswordSubmit');
    const authBack = document.getElementById('monitoringPasswordBack');
    const contentWrapper = document.getElementById('monitoringMasterContent');

    const authModal = new bootstrap.Modal(authModalEl, {
        backdrop: 'static',
        keyboard: false
    });

    authModal.show();
    setTimeout(() => authPasswordInput.focus(), 200);

    function unlockMonitoringPage() {
        authModal.hide();
        contentWrapper.style.display = 'block';
    }

    authSubmit.addEventListener('click', function() {
        const entered = authPasswordInput.value.trim();
        if (entered === pagePassword) {
            unlockMonitoringPage();
            return;
        }
        authError.classList.remove('hidden');
        authPasswordInput.focus();
    });

    authPasswordInput.addEventListener('keypress', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            authSubmit.click();
        }
    });

    authBack.addEventListener('click', function() {
        window.location.href = '{{ route("dashboard.index") }}';
    });

    const searchModal = document.getElementById('searchModal');
    if (searchModal) {
        searchModal.addEventListener('show.bs.modal', function() {
            document.getElementById('searchInput').value = '';
            document.getElementById('searchResults').innerHTML = '';
        });
    }

    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const q = e.target.value.trim();
            if (q.length < 2) {
                document.getElementById('searchResults').innerHTML = '';
                return;
            }

            document.getElementById('searchResults').innerHTML = '<p class="text-xs text-slate-400">Mencari...</p>';

            fetch('{{ route("monitoring-master.search") }}?q=' + encodeURIComponent(q))
                .then(r => r.json())
                .then(data => {
                    if (!data.results || data.results.length === 0) {
                        document.getElementById('searchResults').innerHTML = '<p class="text-xs text-slate-400">Tidak ada hasil ditemukan</p>';
                        return;
                    }

                    let html = '<div class="divide-y divide-slate-100 border border-slate-200 rounded-xl overflow-hidden">';
                    data.results.forEach(emp => {
                        const nikEscaped = escapeQuotes(emp.nik);
                        const namaEscaped = escapeQuotes(emp.nama);
                        html += `
                            <div class="p-3 bg-white hover:bg-slate-50 flex items-center justify-between gap-3">
                                <div>
                                    <div class="text-xs font-bold text-slate-800">${emp.nama}</div>
                                    <div class="text-[11px] text-slate-500">NIK: ${emp.nik} | ${emp.jabatan} | ${emp.dept}</div>
                                </div>
                                <button type="button" onclick="tambah('${nikEscaped}', '${namaEscaped}')"
                                        class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold inline-flex items-center gap-1 cursor-pointer">
                                    <i class="ti ti-plus"></i> Tambah
                                </button>
                            </div>
                        `;
                    });
                    html += '</div>';
                    document.getElementById('searchResults').innerHTML = html;
                })
                .catch(e => {
                    document.getElementById('searchResults').innerHTML = '<p class="text-xs text-rose-600">Gagal memuat data</p>';
                });
        });
    }
});
</script>
@endpush

