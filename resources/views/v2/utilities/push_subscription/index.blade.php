@extends('v2.layouts.app')

@section('titlepage', 'Push Subscriptions')
@section('page_title', 'Push Subscriptions')

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
                <i class="ti ti-bell-ringing text-2xl" style="color: {{ $themeColor1 }};"></i>
                <span>Push Notification Subscriptions</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Monitoring dan manajemen endpoint Web Push Notification browser terdaftar milik pengguna
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
            <span class="font-bold text-slate-800">Push Subscriptions</span>
        </nav>
    </div>

    <!-- ================= 2. FILTER TOOLBAR ================= -->
    <div class="bg-white rounded-2xl p-3.5 sm:p-4 border border-slate-200/90 shadow-2xs">
        <form action="{{ route('push-subscription.index') }}" method="GET" class="w-full">
            <div class="flex flex-col sm:flex-row items-center gap-2.5 sm:gap-3 w-full">
                <!-- Search Input -->
                <div class="relative flex-1 w-full">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                        <i class="ti ti-search text-base"></i>
                    </span>
                    <input type="text" name="cari" id="cari"
                           value="{{ request('cari') }}"
                           placeholder="Cari berdasarkan nama karyawan, username, atau NIK..."
                           class="w-full h-10 pl-10 pr-4 bg-slate-50/50 hover:bg-slate-50/90 focus:bg-white border border-slate-300/90 rounded-xl text-xs font-semibold text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition" />
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <button type="submit" 
                            class="flex-1 sm:flex-initial h-10 px-5 text-white font-bold text-xs rounded-xl shadow-xs transition inline-flex items-center justify-center gap-1.5 cursor-pointer hover:opacity-95 active:scale-95 whitespace-nowrap"
                            style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-search text-sm"></i>
                        <span>Cari</span>
                    </button>
                    @if(request()->filled('cari'))
                        <a href="{{ route('push-subscription.index') }}" 
                           class="h-10 px-3 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-xl transition inline-flex items-center justify-center border border-slate-200"
                           title="Reset Filter">
                            <i class="ti ti-rotate text-sm"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- ================= 3. SEAMLESS SOLID TABLE ================= -->
    <div class="bg-white border border-slate-200/90 rounded-2xl shadow-xs overflow-hidden">
        <!-- Seamless Solid Header -->
        <div class="px-5 py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-white border-0"
             style="background-color: {{ $themeColor1 }};">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-white/20 text-white flex items-center justify-center text-xs font-bold border border-white/20 shadow-2xs backdrop-blur-xs">
                    <i class="ti ti-devices"></i>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-white tracking-tight">
                        Daftar Perangkat Push Notification Terdaftar
                    </h3>
                </div>
            </div>
            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-white/20 text-white border border-white/30 shadow-2xs backdrop-blur-xs self-start sm:self-auto">
                {{ $subscriptions->total() }} Perangkat Aktif
            </span>
        </div>

        <!-- Table Container -->
        <div class="overflow-x-auto w-full" style="background-color: {{ $themeColor1 }};">
            <table class="w-full text-left text-xs border-0 border-collapse">
                <thead class="text-white font-bold uppercase tracking-wider text-[11px] border-0 border-t-0 border-b border-black/15"
                       style="background-color: {{ $themeColor1 }};">
                    <tr class="border-0 border-t-0 text-white/90">
                        <th class="py-2.5 px-3.5 text-center w-14 text-white/90 border-0 border-t-0">No</th>
                        <th class="py-2.5 px-4 text-white/90 border-0 border-t-0 min-w-[200px]">Pengguna</th>
                        <th class="py-2.5 px-3.5 text-white/90 border-0 border-t-0 min-w-[130px]">Info Akun</th>
                        <th class="py-2.5 px-4 text-white/90 border-0 border-t-0 min-w-[260px]">Browser & Endpoint</th>
                        <th class="py-2.5 px-3.5 text-center w-36 text-white/90 border-0 border-t-0">Terakhir Update</th>
                        <th class="py-2.5 px-3.5 text-center w-28 text-white/90 border-0 border-t-0">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700 font-medium bg-white">
                    @forelse ($subscriptions as $idx => $sub)
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <!-- No -->
                        <td class="py-2.5 px-3.5 text-center text-slate-400 font-bold">
                            {{ $subscriptions->firstItem() + $idx }}
                        </td>

                        <!-- Pengguna -->
                        <td class="py-2.5 px-4">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-xl flex items-center justify-center font-black text-xs text-white shrink-0 shadow-2xs"
                                     style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                                    {{ strtoupper(substr($sub->nama_karyawan ?? $sub->user_name ?? '?', 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <span class="font-extrabold text-slate-900 text-xs block">
                                        {{ $sub->nama_karyawan ?? $sub->user_name ?? 'Pengguna Tidak Diketahui' }}
                                    </span>
                                    @if($sub->user_name && $sub->nama_karyawan)
                                        <span class="text-[10.5px] text-slate-400 block truncate">
                                            {{ $sub->user_name }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <!-- Info Akun -->
                        <td class="py-2.5 px-3.5">
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md font-mono text-[11px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                <i class="ti ti-id-badge text-xs text-slate-500"></i>
                                {{ $sub->nik_show ?? $sub->username ?? '-' }}
                            </span>
                        </td>

                        <!-- Browser & Endpoint -->
                        <td class="py-2.5 px-4">
                            @php
                                $browser = 'Unknown Browser';
                                $browserIcon = 'ti-world';
                                $badgeColor = 'bg-slate-100 text-slate-700 border-slate-200';

                                if (str_contains($sub->endpoint, 'android.googleapis.com') || str_contains($sub->endpoint, 'fcm.googleapis.com')) {
                                    $browser = 'Google Chrome / Android';
                                    $browserIcon = 'ti-brand-chrome';
                                    $badgeColor = 'bg-amber-50 text-amber-700 border-amber-200';
                                } elseif (str_contains($sub->endpoint, 'updates.push.services.mozilla.com')) {
                                    $browser = 'Mozilla Firefox';
                                    $browserIcon = 'ti-brand-firefox';
                                    $badgeColor = 'bg-orange-50 text-orange-700 border-orange-200';
                                } elseif (str_contains($sub->endpoint, 'web.push.apple.com')) {
                                    $browser = 'Apple Safari';
                                    $browserIcon = 'ti-brand-apple';
                                    $badgeColor = 'bg-sky-50 text-sky-700 border-sky-200';
                                }
                            @endphp
                            <div class="space-y-1">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10.5px] font-bold border {{ $badgeColor }}">
                                    <i class="ti {{ $browserIcon }} text-xs"></i>
                                    {{ $browser }}
                                </span>
                                <p class="text-[10.5px] text-slate-400 font-mono break-all line-clamp-1 max-w-sm" title="{{ $sub->endpoint }}">
                                    {{ $sub->endpoint }}
                                </p>
                            </div>
                        </td>

                        <!-- Terakhir Update -->
                        <td class="py-2.5 px-3.5 text-center">
                            <div class="inline-flex flex-col items-center">
                                <span class="font-bold text-slate-800 text-xs">
                                    {{ \Carbon\Carbon::parse($sub->updated_at)->format('d M Y') }}
                                </span>
                                <span class="inline-flex items-center gap-1 font-mono text-[10.5px] font-bold text-emerald-700 mt-0.5">
                                    <i class="ti ti-clock text-[10px]"></i>
                                    {{ \Carbon\Carbon::parse($sub->updated_at)->format('H:i:s') }} WIB
                                </span>
                            </div>
                        </td>

                        <!-- Aksi -->
                        <td class="py-2.5 px-3.5 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <!-- Test Notification -->
                                <form action="{{ route('push-subscription.test', $sub->id) }}" method="POST" class="test-form">
                                    @csrf
                                    <button type="button" 
                                            class="w-7 h-7 rounded-lg bg-sky-50 hover:bg-sky-100 text-sky-600 border border-sky-200 flex items-center justify-center transition active:scale-90 cursor-pointer btn-test-sub-v2"
                                            title="Kirim Tes Push Notifikasi">
                                        <i class="ti ti-bell-ringing text-xs"></i>
                                    </button>
                                </form>

                                <!-- Delete Device -->
                                <form action="{{ route('push-subscription.deleteAdmin', $sub->id) }}" method="POST" class="delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" 
                                            class="w-7 h-7 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 flex items-center justify-center transition active:scale-90 cursor-pointer btn-delete-sub-v2"
                                            title="Hapus Perangkat">
                                        <i class="ti ti-trash text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-10 text-center text-slate-400">
                            <i class="ti ti-bell-off text-3xl mb-2 block text-slate-300"></i>
                            <span>Tidak ada data push subscription perangkat yang terdaftar.</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($subscriptions->hasPages())
            <div class="px-5 py-3.5 bg-white border-t border-slate-100">
                {{ $subscriptions->links('v2.components.pagination') }}
            </div>
        @endif
    </div>

</div>

@endsection

@push('myscript')
<script>
$(document).ready(function() {

    // Test Push Notification SweetAlert2
    $('.btn-test-sub-v2').on('click', function(e) {
        e.preventDefault();
        let form = $(this).closest('form');

        Swal.fire({
            title: 'Kirim Notifikasi Uji Coba?',
            text: 'Notifikasi push tes akan dikirimkan langsung ke perangkat browser ini.',
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: 'Ya, Kirim Sekarang',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'px-5 py-2.5 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-md transition active:scale-95 cursor-pointer',
                cancelButton: 'px-4 py-2.5 text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition active:scale-95 mr-2 cursor-pointer'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Sedang Mengirim...',
                    text: 'Menghubungkan ke Web Push Server...',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                form.submit();
            }
        });
    });

    // Delete Subscription SweetAlert2
    $('.btn-delete-sub-v2').on('click', function(e) {
        e.preventDefault();
        let form = $(this).closest('form');

        Swal.fire({
            title: 'Hapus Langganan Perangkat?',
            text: 'Perangkat ini tidak akan menerima push notifikasi lagi sampai pengguna mengaktifkannya kembali.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'px-5 py-2.5 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl shadow-md transition active:scale-95 cursor-pointer',
                cancelButton: 'px-4 py-2.5 text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition active:scale-95 mr-2 cursor-pointer'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

});
</script>
@endpush
