@extends('v2.layouts.app')

@section('titlepage', 'WhatsApp Gateway')
@section('page_title', 'WhatsApp Gateway')

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
                <i class="ti ti-brand-whatsapp text-2xl text-emerald-600"></i>
                <span>WhatsApp Gateway Manager</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Koneksikan nomor sender WhatsApp, scan QR autentikasi sesi, uji pengiriman pesan, dan kelola perangkat
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
            <span class="font-bold text-slate-800">WA Gateway</span>
        </nav>
    </div>

    <!-- ================= 2. TOP CARDS: ADD DEVICE & CONFIG INFO ================= -->
    <div class="grid grid-cols-1 md:grid-cols-12 gap-5 items-stretch">
        
        <!-- Left: Form Add Device (7 cols) -->
        <div class="md:col-span-7 bg-white border border-slate-200/90 rounded-2xl shadow-xs overflow-hidden flex flex-col justify-between">
            <!-- Solid Header -->
            <div class="px-5 py-3.5 flex items-center justify-between text-white border-0"
                 style="background-color: {{ $themeColor1 }};">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-white/20 text-white flex items-center justify-center text-xs font-bold border border-white/20 shadow-2xs backdrop-blur-xs">
                        <i class="ti ti-device-mobile-plus"></i>
                    </div>
                    <h3 class="text-sm font-extrabold text-white tracking-tight">
                        Registrasi Perangkat WhatsApp
                    </h3>
                </div>
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-white/20 text-white border border-white/30 shadow-2xs backdrop-blur-xs">
                    Format: 628xxxxxxxxxx
                </span>
            </div>

            <!-- Body Form with Consistent Real-Time Validation -->
            <form id="addDeviceForm" class="p-5 sm:p-6 space-y-4 flex-1 flex flex-col justify-between" novalidate>
                @csrf
                <div class="space-y-1.5 field-container">
                    <label for="sender" class="block text-xs font-extrabold text-slate-800 uppercase tracking-wider">
                        Nomor WhatsApp Sender <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="input-icon absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center transition-colors">
                            <i class="ti ti-phone text-base"></i>
                        </span>
                        <input type="text" name="sender" id="sender" placeholder="Contoh: 6282298859671" autocomplete="off"
                               class="w-full h-11 pl-10 pr-4 bg-slate-50/50 hover:bg-slate-50/90 focus:bg-white border border-slate-300/90 rounded-xl text-xs font-bold text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition" />
                    </div>
                    <p class="text-[11px] text-slate-400 help-text">
                        Gunakan awalan kode negara <span class="font-bold text-slate-600">62</span> tanpa tanda plus atau angka 0 di awal.
                    </p>
                    <p class="error-feedback text-[11px] font-semibold text-rose-500 hidden items-center gap-1.5 mt-1" id="err_sender">
                        <i class="ti ti-alert-circle text-sm"></i>
                        <span class="err-text"></span>
                    </p>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-end">
                    <button type="submit" id="btnAddDevice"
                            class="w-full sm:w-auto px-5 h-10 text-white font-bold text-xs rounded-xl shadow-xs transition inline-flex items-center justify-center gap-2 cursor-pointer hover:opacity-95 active:scale-95"
                            style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-plus text-sm"></i>
                        <span>Tambah Perangkat</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Right: Config Status Info (5 cols) -->
        <div class="md:col-span-5 bg-white border border-slate-200/90 rounded-2xl shadow-xs overflow-hidden flex flex-col justify-between">
            <!-- Solid Header -->
            <div class="px-5 py-3.5 flex items-center justify-between text-white border-0 bg-slate-800">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-white/20 text-white flex items-center justify-center text-xs font-bold border border-white/20 shadow-2xs backdrop-blur-xs">
                        <i class="ti ti-settings"></i>
                    </div>
                    <h3 class="text-sm font-extrabold text-white tracking-tight">
                        Konfigurasi Server Gateway
                    </h3>
                </div>
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-white/20 text-white border border-white/30 shadow-2xs backdrop-blur-xs">
                    Setting Terkoneksi
                </span>
            </div>

            <!-- Body -->
            <div class="p-5 sm:p-6 space-y-4 flex-1 flex flex-col justify-between">
                <div class="space-y-3">
                    <div class="p-3 bg-slate-50 border border-slate-200/80 rounded-xl space-y-1">
                        <span class="text-[10.5px] font-bold text-slate-400 uppercase tracking-wider block">Domain API Gateway</span>
                        <div class="font-mono text-xs font-extrabold text-slate-800 break-all flex items-center gap-1.5">
                            <i class="ti ti-world text-emerald-600 text-sm"></i>
                            <span>{{ $generalsetting->domain_wa_gateway ?? 'Belum dikonfigurasi' }}</span>
                        </div>
                    </div>

                    <div class="p-3 bg-slate-50 border border-slate-200/80 rounded-xl space-y-1">
                        <span class="text-[10.5px] font-bold text-slate-400 uppercase tracking-wider block">API Secret Key</span>
                        <div class="font-mono text-xs font-extrabold text-slate-800 flex items-center gap-1.5">
                            <i class="ti ti-key text-amber-600 text-sm"></i>
                            <span>{{ $generalsetting->wa_api_key ? '••••••••' . substr($generalsetting->wa_api_key, -4) : 'Belum dikonfigurasi' }}</span>
                        </div>
                    </div>
                </div>

                <div class="text-[11px] text-slate-400 flex items-center gap-1.5">
                    <i class="ti ti-info-circle text-slate-500"></i>
                    <span>Untuk mengubah domain/key, buka menu <a href="{{ route('generalsetting.index') }}" class="font-bold text-emerald-700 underline">Pengaturan Umum</a>.</span>
                </div>
            </div>
        </div>

    </div>

    <!-- ================= 3. SEAMLESS SOLID TABLE: DEVICES ================= -->
    <div class="bg-white border border-slate-200/90 rounded-2xl shadow-xs overflow-hidden">
        <!-- Seamless Solid Header -->
        <div class="px-5 py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-white border-0"
             style="background-color: {{ $themeColor1 }};">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-white/20 text-white flex items-center justify-center text-xs font-bold border border-white/20 shadow-2xs backdrop-blur-xs">
                    <i class="ti ti-brand-whatsapp"></i>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-white tracking-tight">
                        Daftar Perangkat WhatsApp Sender
                    </h3>
                </div>
            </div>
            
            <div class="flex items-center gap-2 self-start sm:self-auto">
                <a href="{{ route('wagateway.messages') }}"
                   class="h-8 px-3 rounded-lg bg-white/20 hover:bg-white/30 text-white text-xs font-bold inline-flex items-center gap-1.5 border border-white/30 shadow-2xs backdrop-blur-xs transition active:scale-95">
                    <i class="ti ti-message-dots text-sm"></i>
                    <span>Riwayat Pesan</span>
                </a>
                <button type="button" id="refreshDeviceInfo"
                        class="h-8 px-3 rounded-lg bg-white/20 hover:bg-white/30 text-white text-xs font-bold inline-flex items-center gap-1.5 border border-white/30 shadow-2xs backdrop-blur-xs transition active:scale-95 cursor-pointer">
                    <i class="ti ti-refresh text-sm"></i>
                    <span>Refresh Sesi</span>
                </button>
            </div>
        </div>

        <!-- Table Container -->
        <div class="overflow-x-auto w-full" style="background-color: {{ $themeColor1 }};">
            <table class="w-full text-left text-xs border-0 border-collapse" id="devicesTable">
                <thead class="text-white font-bold uppercase tracking-wider text-[11px] border-0 border-t-0 border-b border-black/15"
                       style="background-color: {{ $themeColor1 }};">
                    <tr class="border-0 border-t-0 text-white/90">
                        <th class="py-2.5 px-3.5 text-center w-14 text-white/90 border-0 border-t-0">No</th>
                        <th class="py-2.5 px-4 text-white/90 border-0 border-t-0 min-w-[180px]">Nomor WhatsApp</th>
                        <th class="py-2.5 px-3.5 text-center w-32 text-white/90 border-0 border-t-0">Status Sistem</th>
                        <th class="py-2.5 px-3.5 text-center w-36 text-white/90 border-0 border-t-0">Koneksi Sesi WA</th>
                        <th class="py-2.5 px-3.5 text-center w-36 text-white/90 border-0 border-t-0">Terdaftar Pada</th>
                        <th class="py-2.5 px-4 text-center w-60 text-white/90 border-0 border-t-0">Aksi & Integrasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700 font-medium bg-white">
                    @forelse($devices as $index => $device)
                    <tr class="hover:bg-slate-50/70 transition-colors" data-device-number="{{ $device->number }}">
                        <!-- No -->
                        <td class="py-2.5 px-3.5 text-center text-slate-400 font-bold">
                            {{ $index + 1 }}
                        </td>

                        <!-- Nomor WhatsApp -->
                        <td class="py-2.5 px-4">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-xl flex items-center justify-center font-black text-sm text-emerald-700 bg-emerald-50 border border-emerald-200 shrink-0 shadow-2xs">
                                    <i class="ti ti-brand-whatsapp"></i>
                                </div>
                                <div>
                                    <span class="font-mono font-extrabold text-slate-900 text-xs sm:text-sm tracking-tight block">
                                        {{ $device->number }}
                                    </span>
                                    <span class="text-[10px] text-slate-400 block font-normal">
                                        Sender Device
                                    </span>
                                </div>
                            </div>
                        </td>

                        <!-- Status Sistem (Toggleable) -->
                        <td class="py-2.5 px-3.5 text-center">
                            @if($device->status == 1)
                                <span class="badge-status-sistem inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <i class="ti ti-check text-[10px]"></i> Aktif
                                </span>
                            @else
                                <span class="badge-status-sistem inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                    <i class="ti ti-x text-[10px]"></i> Tidak Aktif
                                </span>
                            @endif
                        </td>

                        <!-- Status Koneksi Sesi WA (Async Checking) -->
                        <td class="py-2.5 px-3.5 text-center">
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[11px] font-bold bg-slate-100 text-slate-600 border border-slate-200 shadow-2xs"
                                  id="connection-status-{{ $device->id }}">
                                <i class="ti ti-loader animate-spin text-xs"></i> Checking...
                            </span>
                        </td>

                        <!-- Tanggal Dibuat -->
                        <td class="py-2.5 px-3.5 text-center">
                            <div class="inline-flex flex-col items-center">
                                <span class="font-bold text-slate-800 text-xs">
                                    {{ $device->created_at->format('d M Y') }}
                                </span>
                                <span class="text-[10.5px] text-slate-400 font-mono mt-0.5">
                                    {{ $device->created_at->format('H:i') }} WIB
                                </span>
                            </div>
                        </td>

                        <!-- Aksi Buttons -->
                        <td class="py-2.5 px-4 text-center">
                            <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                <!-- Toggle Status Button -->
                                <button type="button"
                                        class="toggle-status h-7 px-2 rounded-lg text-[11px] font-bold border transition active:scale-90 cursor-pointer inline-flex items-center gap-1 {{ $device->status == 1 ? 'bg-amber-50 text-amber-700 border-amber-200 hover:bg-amber-100' : 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' }}"
                                        data-id="{{ $device->id }}" data-status="{{ $device->status }}"
                                        title="{{ $device->status == 1 ? 'Nonaktifkan Pengiriman' : 'Jadikan Sender Aktif' }}">
                                    <i class="ti {{ $device->status == 1 ? 'ti-toggle-left' : 'ti-toggle-right' }} text-xs"></i>
                                    <span>{{ $device->status == 1 ? 'Nonaktif' : 'Aktifkan' }}</span>
                                </button>

                                <!-- QR Code Modal Trigger -->
                                <button type="button" 
                                        class="generate-qr w-7 h-7 rounded-lg bg-sky-50 hover:bg-sky-100 text-sky-600 border border-sky-200 flex items-center justify-center transition active:scale-90 cursor-pointer"
                                        data-device="{{ $device->number }}" data-id="{{ $device->id }}"
                                        title="Scan QR Code WhatsApp">
                                    <i class="ti ti-qrcode text-xs"></i>
                                </button>

                                <!-- Test Kirim Pesan -->
                                <button type="button" 
                                        class="test-send-message w-7 h-7 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-600 border border-emerald-200 flex items-center justify-center transition active:scale-90 cursor-pointer"
                                        data-device="{{ $device->number }}" data-id="{{ $device->id }}"
                                        title="Uji Kirim Pesan WA">
                                    <i class="ti ti-send text-xs"></i>
                                </button>

                                <!-- Fetch Groups -->
                                <button type="button" 
                                        class="fetch-groups-device w-7 h-7 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-600 border border-indigo-200 flex items-center justify-center transition active:scale-90 cursor-pointer"
                                        data-device="{{ $device->number }}" data-id="{{ $device->id }}"
                                        title="Ambil Data Grup WhatsApp">
                                    <i class="ti ti-users text-xs"></i>
                                </button>

                                <!-- Disconnect Device -->
                                <button type="button" 
                                        class="disconnect-device w-7 h-7 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 flex items-center justify-center transition active:scale-90 cursor-pointer"
                                        data-device="{{ $device->number }}" data-id="{{ $device->id }}"
                                        title="Disconnect Sesi WhatsApp">
                                    <i class="ti ti-logout text-xs"></i>
                                </button>

                                <!-- Delete Device -->
                                <button type="button" 
                                        class="delete-device w-7 h-7 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 flex items-center justify-center transition active:scale-90 cursor-pointer"
                                        data-device="{{ $device->number }}" data-id="{{ $device->id }}"
                                        title="Hapus Perangkat">
                                    <i class="ti ti-trash text-xs"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-10 text-center text-slate-400">
                            <i class="ti ti-device-mobile-off text-3xl mb-2 block text-slate-300"></i>
                            <span>Belum ada perangkat WhatsApp yang terdaftar di sistem.</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- ================= MODALS (STANDARD CONSISTENT DESIGN) ================= -->

<!-- 1. Modal QR Code -->
<div class="modal fade" id="qrCodeModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border-0 shadow-2xl rounded-2xl overflow-hidden bg-white">
            <div class="px-6 py-4 bg-white border-b border-slate-100 flex items-center justify-between text-slate-900">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center text-lg shrink-0 font-bold text-white shadow-sm"
                         style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-qrcode"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-sm sm:text-base font-extrabold text-slate-900 tracking-tight truncate mb-0">
                            Scan WhatsApp QR Code
                        </h5>
                        <p class="text-xs text-slate-500 mb-0 font-medium">
                            Tautkan sesi WhatsApp dengan memindai QR code
                        </p>
                    </div>
                </div>
                <button type="button" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center transition-colors cursor-pointer shrink-0" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x text-lg"></i>
                </button>
            </div>
            <div class="p-6 text-center">
                <div id="qrCodeContent" class="min-h-[220px] flex flex-col items-center justify-center space-y-3">
                    <i class="ti ti-loader animate-spin text-3xl text-emerald-600"></i>
                    <p class="text-xs font-semibold text-slate-600">Sedang memproses QR Code dari server gateway...</p>
                </div>
            </div>
            <div class="px-6 py-3.5 bg-slate-50 border-t border-slate-100 flex items-center justify-between gap-2">
                <button type="button" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-bold transition active:scale-95" data-bs-dismiss="modal">
                    Tutup
                </button>
                <button type="button" id="downloadQR" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-xs transition inline-flex items-center gap-1.5 cursor-pointer hidden">
                    <i class="ti ti-download text-sm"></i>
                    <span>Download QR</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- 2. Modal Test Kirim Pesan -->
<div class="modal fade" id="testSendMessageModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border-0 shadow-2xl rounded-2xl overflow-hidden bg-white">
            <div class="px-6 py-4 bg-white border-b border-slate-100 flex items-center justify-between text-slate-900">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center text-lg shrink-0 font-bold text-white shadow-sm"
                         style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-send"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-sm sm:text-base font-extrabold text-slate-900 tracking-tight truncate mb-0">
                            Uji Coba Kirim Pesan
                        </h5>
                        <p class="text-xs text-slate-500 mb-0 font-medium">
                            Tes pengiriman pesan WhatsApp ke nomor tujuan
                        </p>
                    </div>
                </div>
                <button type="button" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center transition-colors cursor-pointer shrink-0" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x text-lg"></i>
                </button>
            </div>
            <form id="formTestSendMessage" class="p-6 space-y-4" novalidate>
                <div class="space-y-1.5">
                    <label for="senderNumber" class="block text-xs font-bold text-slate-700">Nomor Pengirim (Sender)</label>
                    <input type="text" id="senderNumber" readonly class="w-full h-10 px-3.5 bg-slate-100 border border-slate-200 rounded-xl text-xs font-mono font-bold text-slate-700 cursor-not-allowed">
                </div>
                <div class="space-y-1.5 field-container">
                    <label for="targetNumber" class="block text-xs font-bold text-slate-700">Nomor Tujuan <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <span class="input-icon absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center transition-colors">
                            <i class="ti ti-phone text-base"></i>
                        </span>
                        <input type="text" id="targetNumber" placeholder="Contoh: 62888xxxx" autocomplete="off"
                               class="w-full h-10 pl-10 pr-4 bg-slate-50/50 hover:bg-slate-50/90 focus:bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition">
                    </div>
                    <p class="error-feedback text-[11px] font-semibold text-rose-500 hidden items-center gap-1.5 mt-1" id="err_targetNumber">
                        <i class="ti ti-alert-circle text-sm"></i>
                        <span class="err-text"></span>
                    </p>
                </div>
                <div class="space-y-1.5 field-container">
                    <label for="messageText" class="block text-xs font-bold text-slate-700">Isi Pesan WhatsApp <span class="text-rose-500">*</span></label>
                    <textarea id="messageText" rows="3" placeholder="Tuliskan pesan uji coba di sini..."
                              class="w-full p-3 bg-slate-50/50 hover:bg-slate-50/90 focus:bg-white border border-slate-300 rounded-xl text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition"></textarea>
                    <p class="error-feedback text-[11px] font-semibold text-rose-500 hidden items-center gap-1.5 mt-1" id="err_messageText">
                        <i class="ti ti-alert-circle text-sm"></i>
                        <span class="err-text"></span>
                    </p>
                </div>
                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
                    <button type="button" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-bold transition active:scale-95" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="button" id="btnTestSendMessage" class="px-5 py-2.5 text-white text-xs font-bold rounded-xl shadow-xs transition inline-flex items-center gap-1.5 cursor-pointer hover:opacity-95" style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-send text-sm"></i>
                        <span>Kirim Pesan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 3. Modal Groups -->
<div class="modal fade" id="groupsModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-2xl rounded-2xl overflow-hidden bg-white">
            <div class="px-6 py-4 bg-white border-b border-slate-100 flex items-center justify-between text-slate-900">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center text-lg shrink-0 font-bold text-white shadow-sm"
                         style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-users"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-sm sm:text-base font-extrabold text-slate-900 tracking-tight truncate mb-0">
                            Daftar Grup WhatsApp
                        </h5>
                        <p class="text-xs text-slate-500 mb-0 font-medium">
                            Grup WhatsApp yang terhubung pada nomor sender
                        </p>
                    </div>
                </div>
                <button type="button" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center transition-colors cursor-pointer shrink-0" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x text-lg"></i>
                </button>
            </div>
            <div class="p-6 max-h-[60vh] overflow-y-auto">
                <div id="groupsContent">
                    <div class="text-center py-8 space-y-2">
                        <i class="ti ti-loader animate-spin text-3xl text-emerald-600"></i>
                        <p class="text-xs text-slate-500 font-semibold">Mengambil daftar grup WhatsApp...</p>
                    </div>
                </div>
            </div>
            <div class="px-6 py-3.5 bg-slate-50 border-t border-slate-100 flex justify-end">
                <button type="button" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-bold transition active:scale-95" data-bs-dismiss="modal">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('myscript')
<script>
$(document).ready(function() {

    // Helper functions for consistent real-time field validation
    function setFieldError($input, $err, msg) {
        $input.addClass('border-rose-500 bg-rose-50/30 focus:border-rose-500 focus:ring-rose-500/20').removeClass('border-slate-300 focus:border-emerald-600 focus:ring-emerald-500/20');
        $input.closest('.field-container').find('.input-icon i').addClass('text-rose-500').removeClass('text-slate-400 text-emerald-600');
        $err.find('.err-text').text(msg);
        $err.removeClass('hidden').addClass('flex');
    }

    function clearFieldError($input, $err) {
        $input.removeClass('border-rose-500 bg-rose-50/30 focus:border-rose-500 focus:ring-rose-500/20').addClass('border-slate-300 focus:border-emerald-600 focus:ring-emerald-500/20');
        $input.closest('.field-container').find('.input-icon i').removeClass('text-rose-500').addClass('text-slate-400');
        $err.addClass('hidden').removeClass('flex');
        $err.find('.err-text').text('');
    }

    // Real-time validation for Add Device
    function validateSender() {
        let val = $('#sender').val().trim();
        if (!val) {
            setFieldError($('#sender'), $('#err_sender'), 'Nomor WhatsApp sender wajib diisi.');
            return false;
        }
        if (!/^[0-9]+$/.test(val)) {
            setFieldError($('#sender'), $('#err_sender'), 'Nomor WhatsApp hanya boleh berisi angka (tanpa spasi/tanda hubung).');
            return false;
        }
        if (!val.startsWith('62')) {
            setFieldError($('#sender'), $('#err_sender'), 'Gunakan format kode negara 62 (contoh: 6282298859671).');
            return false;
        }
        clearFieldError($('#sender'), $('#err_sender'));
        return true;
    }

    $('#sender').on('input blur change', function(e) {
        if (e.type === 'blur' || $('#err_sender').is(':visible') || $(this).val().trim() !== '') {
            validateSender();
        }
    });

    // Load device info initially
    loadAllDeviceInfo();

    // Auto-refresh every 30 seconds
    setInterval(function() {
        loadAllDeviceInfo();
    }, 30000);

    // Refresh button
    $('#refreshDeviceInfo').on('click', function() {
        const btn = $(this);
        const originalHtml = btn.html();

        btn.html('<i class="ti ti-loader animate-spin text-sm"></i> Refreshing...').prop('disabled', true);

        $('[id^="connection-status-"]')
            .removeClass('bg-emerald-50 text-emerald-700 border-emerald-200 bg-rose-50 text-rose-700 border-rose-200')
            .addClass('bg-slate-100 text-slate-600 border-slate-200')
            .html('<i class="ti ti-loader animate-spin text-xs"></i> Checking...');

        loadAllDeviceInfo();

        setTimeout(function() {
            btn.html(originalHtml).prop('disabled', false);
        }, 2000);
    });

    // Add Device Form Submit
    $('#addDeviceForm').on('submit', function(e) {
        e.preventDefault();

        if (!validateSender()) {
            $('#sender').focus();
            return false;
        }

        const btnAddDevice = $('#btnAddDevice');
        const originalText = btnAddDevice.html();
        const form = $(this);

        btnAddDevice.prop('disabled', true).html('<i class="ti ti-loader animate-spin text-sm"></i> Menambahkan...');

        $.ajax({
            url: '{{ route("wagateway.add-device") }}',
            type: 'POST',
            data: form.serialize(),
            timeout: 30000,
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message || 'Device berhasil ditambahkan',
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'px-5 py-2.5 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-md cursor-pointer'
                        },
                        buttonsStyling: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: response.message || 'Gagal menambahkan device'
                    });
                }
            },
            error: function(xhr) {
                let msg = 'Terjadi kesalahan saat menambahkan device.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: msg
                });
            },
            complete: function() {
                btnAddDevice.prop('disabled', false).html(originalText);
            }
        });
    });

    // Toggle Status Device
    $('.toggle-status').on('click', function() {
        const deviceId = $(this).data('id');
        const button = $(this);
        const originalText = button.html();

        button.prop('disabled', true).html('<i class="ti ti-loader animate-spin text-xs"></i>');

        $.ajax({
            url: '{{ route("wagateway.toggle-device-status", ":id") }}'.replace(':id', deviceId),
            type: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            timeout: 15000,
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: response.message || 'Gagal mengubah status device'
                    });
                    button.prop('disabled', false).html(originalText);
                }
            },
            error: function(xhr) {
                let msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Terjadi kesalahan sistem.';
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: msg
                });
                button.prop('disabled', false).html(originalText);
            }
        });
    });

    // Generate QR Code
    $('.generate-qr').on('click', function() {
        const deviceNumber = $(this).data('device');
        const deviceId = $(this).data('id');
        const button = $(this);
        const originalText = button.html();

        button.prop('disabled', true).html('<i class="ti ti-loader animate-spin text-xs"></i>');

        $('#qrCodeModal').modal('show');

        $('#qrCodeContent').html(`
            <i class="ti ti-loader animate-spin text-3xl text-emerald-600"></i>
            <p class="text-xs font-semibold text-slate-600">Sedang memproses QR Code dari server...</p>
        `);
        $('#downloadQR').addClass('hidden');

        generateQRCode(deviceNumber, deviceId, button, originalText);
    });

    function generateQRCode(deviceNumber, deviceId, button, originalText) {
        $.ajax({
            url: '{{ route("wagateway.generate-qr") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                device: deviceNumber
            },
            timeout: 60000,
            success: function(response) {
                if (response.success) {
                    if (response.data && response.data.qrcode) {
                        $('#qrCodeContent').html(`
                            <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl text-xs font-bold text-emerald-800 mb-2">
                                <i class="ti ti-check text-sm mr-1"></i> QR Code Berhasil Dibuat
                            </div>
                            <div class="p-3 bg-slate-50 border border-slate-200 rounded-2xl inline-block shadow-xs">
                                <img src="${response.data.qrcode}" class="w-64 h-64 object-contain rounded-xl" alt="QR Code WhatsApp">
                            </div>
                            <p class="text-xs text-slate-500 font-medium mt-2">Buka WhatsApp &gt; Perangkat Tertaut &gt; Scan QR di atas</p>
                            <div class="mt-2 flex items-center justify-center gap-1.5 text-xs text-emerald-700 font-bold">
                                <i class="ti ti-loader animate-spin"></i>
                                <span>Menunggu sesi terhubung...</span>
                            </div>
                        `);

                        $('#downloadQR').removeClass('hidden').off('click').on('click', function() {
                            downloadQRCode(response.data.qrcode, deviceNumber);
                        });

                        checkDeviceStatus(deviceNumber, deviceId);
                    } else if (response.data && response.data.status === 'processing') {
                        setTimeout(() => {
                            generateQRCode(deviceNumber, deviceId, button, originalText);
                        }, 2000);
                    } else if (response.data && (response.data.status === 'connected' || response.data.msg === 'Device already connected!' || response.message === 'Device sudah terhubung')) {
                        $('#qrCodeContent').html(`
                            <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-center space-y-2">
                                <div class="w-12 h-12 mx-auto rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-2xl">
                                    <i class="ti ti-check"></i>
                                </div>
                                <h4 class="text-sm font-extrabold text-emerald-900">Device Sudah Terhubung!</h4>
                                <p class="text-xs text-emerald-700 font-mono">${deviceNumber}</p>
                            </div>
                        `);
                        $('#downloadQR').addClass('hidden');
                    } else {
                        $('#qrCodeContent').html(`
                            <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-xs font-bold text-amber-800">
                                ${response.data.msg || response.message || 'Status tidak diketahui'}
                            </div>
                        `);
                    }
                } else {
                    $('#qrCodeContent').html(`
                        <div class="p-3 bg-rose-50 border border-rose-200 rounded-xl text-xs font-bold text-rose-800">
                            ${response.message || 'Gagal generate QR Code'}
                        </div>
                    `);
                }
            },
            error: function(xhr) {
                let msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Terjadi kesalahan saat memproses QR Code.';
                $('#qrCodeContent').html(`
                    <div class="p-3 bg-rose-50 border border-rose-200 rounded-xl text-xs font-bold text-rose-800">
                        ${msg}
                    </div>
                `);
            },
            complete: function() {
                button.prop('disabled', false).html(originalText);
            }
        });
    }

    function checkDeviceStatus(deviceNumber, deviceId) {
        $.ajax({
            url: '{{ route("wagateway.check-device-status") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                device: deviceNumber
            },
            timeout: 30000,
            success: function(response) {
                if (response.success && response.data && response.data.device_info && response.data.device_info.info && response.data.device_info.info.length > 0) {
                    const deviceInfo = response.data.device_info.info[0];
                    if (deviceInfo.status === 'Connected') {
                        $('#qrCodeContent').html(`
                            <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-center space-y-2">
                                <div class="w-12 h-12 mx-auto rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-2xl">
                                    <i class="ti ti-check"></i>
                                </div>
                                <h4 class="text-sm font-extrabold text-emerald-900">Device Berhasil Terhubung!</h4>
                                <p class="text-xs text-emerald-700 font-mono">${deviceNumber}</p>
                            </div>
                        `);
                        $('#downloadQR').addClass('hidden');
                        loadAllDeviceInfo();
                        return;
                    }
                }

                if ($('#qrCodeModal').is(':visible')) {
                    setTimeout(() => {
                        checkDeviceStatus(deviceNumber, deviceId);
                    }, 3000);
                }
            }
        });
    }

    // Load Device Info for badges
    function loadAllDeviceInfo() {
        $('tr[data-device-number]').each(function() {
            const deviceNumber = $(this).data('device-number');
            const deviceId = $(this).find('.toggle-status').data('id');
            loadDeviceInfo(deviceNumber, deviceId);
        });
    }

    function loadDeviceInfo(deviceNumber, deviceId) {
        $.ajax({
            url: '{{ route("wagateway.check-device-status") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                device: deviceNumber
            },
            timeout: 10000,
            success: function(response) {
                const statusBadge = $(`#connection-status-${deviceId}`);
                if (response.success && response.data && response.data.device_info && response.data.device_info.info && response.data.device_info.info.length > 0) {
                    const deviceInfo = response.data.device_info.info[0];
                    if (deviceInfo.status === 'Connected') {
                        statusBadge.removeClass('bg-slate-100 text-slate-600 border-slate-200 bg-rose-50 text-rose-700 border-rose-200')
                                   .addClass('bg-emerald-50 text-emerald-700 border-emerald-200')
                                   .html('<i class="ti ti-circle-check text-xs"></i> Connected');
                    } else {
                        statusBadge.removeClass('bg-slate-100 text-slate-600 border-slate-200 bg-emerald-50 text-emerald-700 border-emerald-200')
                                   .addClass('bg-rose-50 text-rose-700 border-rose-200')
                                   .html('<i class="ti ti-circle-x text-xs"></i> Disconnected');
                    }
                } else {
                    statusBadge.removeClass('bg-slate-100 text-slate-600 border-slate-200 bg-emerald-50 text-emerald-700 border-emerald-200')
                               .addClass('bg-rose-50 text-rose-700 border-rose-200')
                               .html('<i class="ti ti-circle-x text-xs"></i> Not Found');
                }
            },
            error: function() {
                const statusBadge = $(`#connection-status-${deviceId}`);
                statusBadge.removeClass('bg-slate-100 text-slate-600 border-slate-200 bg-emerald-50 text-emerald-700 border-emerald-200')
                           .addClass('bg-rose-50 text-rose-700 border-rose-200')
                           .html('<i class="ti ti-alert-triangle text-xs"></i> Offline');
            }
        });
    }

    // Test Send Message Modal Handlers
    $(document).on('click', '.test-send-message', function() {
        const deviceNumber = $(this).data('device');
        $('#senderNumber').val(deviceNumber);
        $('#targetNumber').val('');
        $('#messageText').val('');
        clearFieldError($('#targetNumber'), $('#err_targetNumber'));
        clearFieldError($('#messageText'), $('#err_messageText'));
        $('#testSendMessageModal').modal('show');
    });

    $('#targetNumber').on('input blur change', function() {
        if ($(this).val().trim()) {
            clearFieldError($(this), $('#err_targetNumber'));
        }
    });

    $('#messageText').on('input blur change', function() {
        if ($(this).val().trim()) {
            clearFieldError($(this), $('#err_messageText'));
        }
    });

    $('#btnTestSendMessage').on('click', function() {
        const btn = $(this);
        const originalHtml = btn.html();
        const targetNumber = $('#targetNumber').val().trim();
        const messageText = $('#messageText').val().trim();

        let isValid = true;
        if (!targetNumber) {
            setFieldError($('#targetNumber'), $('#err_targetNumber'), 'Nomor tujuan WhatsApp wajib diisi.');
            isValid = false;
        } else {
            clearFieldError($('#targetNumber'), $('#err_targetNumber'));
        }

        if (!messageText) {
            setFieldError($('#messageText'), $('#err_messageText'), 'Isi pesan WhatsApp wajib diisi.');
            isValid = false;
        } else {
            clearFieldError($('#messageText'), $('#err_messageText'));
        }

        if (!isValid) return;

        btn.html('<i class="ti ti-loader animate-spin text-sm"></i> Mengirim...').prop('disabled', true);

        $.ajax({
            url: '{{ route("wagateway.test-send-message") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                sender: $('#senderNumber').val(),
                number: targetNumber,
                message: messageText
            },
            timeout: 30000,
            success: function(response) {
                btn.html(originalHtml).prop('disabled', false);
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message || 'Pesan berhasil dikirim',
                        customClass: {
                            confirmButton: 'px-5 py-2.5 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-md cursor-pointer'
                        },
                        buttonsStyling: false
                    });
                    $('#testSendMessageModal').modal('hide');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: response.message || 'Gagal mengirim pesan'
                    });
                }
            },
            error: function(xhr) {
                btn.html(originalHtml).prop('disabled', false);
                let msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Terjadi kesalahan sistem.';
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: msg
                });
            }
        });
    });

    // Disconnect Device Confirmation (Using SweetAlert2 standard)
    $(document).on('click', '.disconnect-device', function(e) {
        e.preventDefault();
        const deviceNumber = $(this).data('device');

        Swal.fire({
            title: 'Putuskan Sesi WhatsApp?',
            html: `
                <div class="text-xs text-slate-600 text-left space-y-2 mt-2">
                    <p>Anda akan memutuskan sesi WhatsApp pada nomor:</p>
                    <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl font-mono text-xs font-bold text-amber-900">
                        ${deviceNumber}
                    </div>
                    <p class="text-slate-400 italic">* Perangkat tidak dapat mengirim pesan otomatis hingga dilakukan scan QR ulang.</p>
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Putuskan Sesi',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'px-5 py-2.5 text-xs font-bold text-white bg-amber-600 hover:bg-amber-700 rounded-xl shadow-md transition active:scale-95 cursor-pointer',
                cancelButton: 'px-4 py-2.5 text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition active:scale-95 mr-2 cursor-pointer'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Sedang Memutuskan...',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => { Swal.showLoading(); }
                });

                $.ajax({
                    url: '{{ route("wagateway.disconnect-device") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        sender: deviceNumber
                    },
                    timeout: 30000,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message || 'Sesi device berhasil diputuskan',
                                customClass: {
                                    confirmButton: 'px-5 py-2.5 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-md cursor-pointer'
                                },
                                buttonsStyling: false
                            });
                            loadAllDeviceInfo();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: response.message || 'Gagal memutuskan device'
                            });
                        }
                    },
                    error: function(xhr) {
                        let msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Terjadi kesalahan.';
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: msg
                        });
                    }
                });
            }
        });
    });

    // Delete Device Confirmation (Using SweetAlert2 standard)
    $(document).on('click', '.delete-device', function(e) {
        e.preventDefault();
        const deviceNumber = $(this).data('device');
        const deviceId = $(this).data('id');

        Swal.fire({
            title: 'Hapus Perangkat WhatsApp?',
            html: `
                <div class="text-xs text-slate-600 text-left space-y-2 mt-2">
                    <p>Perangkat ini akan dihapus secara permanen dari sistem:</p>
                    <div class="p-3 bg-rose-50 border border-rose-200 rounded-xl font-mono text-xs font-bold text-rose-900">
                        ${deviceNumber}
                    </div>
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus Permanen',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'px-5 py-2.5 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl shadow-md transition active:scale-95 cursor-pointer',
                cancelButton: 'px-4 py-2.5 text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition active:scale-95 mr-2 cursor-pointer'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Sedang Menghapus...',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => { Swal.showLoading(); }
                });

                $.ajax({
                    url: '{{ route("wagateway.delete-device", ":id") }}'.replace(':id', deviceId),
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    timeout: 30000,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message || 'Device berhasil dihapus',
                                customClass: {
                                    confirmButton: 'px-5 py-2.5 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-md cursor-pointer'
                                },
                                buttonsStyling: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: response.message || 'Gagal menghapus device'
                            });
                        }
                    },
                    error: function(xhr) {
                        let msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Terjadi kesalahan saat menghapus.';
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: msg
                        });
                    }
                });
            }
        });
    });

    // Fetch Groups Handler
    $(document).on('click', '.fetch-groups-device', function() {
        const deviceNumber = $(this).data('device');
        const btn = $(this);
        const originalHtml = btn.html();

        btn.html('<i class="ti ti-loader animate-spin text-xs"></i>').prop('disabled', true);
        $('#groupsModal').modal('show');

        $('#groupsContent').html(`
            <div class="text-center py-8 space-y-2">
                <i class="ti ti-loader animate-spin text-3xl text-emerald-600"></i>
                <p class="text-xs text-slate-500 font-semibold">Mengambil data grup WhatsApp dari device ${deviceNumber}...</p>
            </div>
        `);

        $.ajax({
            url: '{{ route("wagateway.fetch-groups") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                number: deviceNumber
            },
            timeout: 30000,
            success: function(response) {
                btn.html(originalHtml).prop('disabled', false);
                if (response.success && response.data) {
                    displayGroups(response.data);
                } else {
                    $('#groupsContent').html(`
                        <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl text-xs font-bold text-rose-800 text-center">
                            ${response.message || 'Gagal mengambil data grup WhatsApp'}
                        </div>
                    `);
                }
            },
            error: function(xhr) {
                btn.html(originalHtml).prop('disabled', false);
                let msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Terjadi kesalahan sistem.';
                $('#groupsContent').html(`
                    <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl text-xs font-bold text-rose-800 text-center">
                        ${msg}
                    </div>
                `);
            }
        });
    });

    function displayGroups(data) {
        let html = '';
        if (data.groups && data.groups.length > 0) {
            html += `
                <div class="mb-4 flex flex-wrap items-center justify-between gap-2 p-3 bg-slate-50 rounded-xl border border-slate-200 text-xs">
                    <span class="font-bold text-slate-800">Total Grup: <span class="text-emerald-700 font-mono font-extrabold">${data.total_groups || data.groups.length}</span></span>
                    <span class="font-bold text-slate-800">Device: <span class="font-mono text-slate-600">${data.device_number || '-'}</span></span>
                </div>
                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-100 font-bold text-slate-700 uppercase tracking-wider text-[10.5px]">
                            <tr>
                                <th class="p-2.5 text-center w-10">No</th>
                                <th class="p-2.5 min-w-[150px]">Nama Grup</th>
                                <th class="p-2.5 min-w-[180px]">Group ID</th>
                                <th class="p-2.5 text-center w-24">Anggota</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
            `;
            data.groups.forEach((group, index) => {
                html += `
                    <tr class="hover:bg-slate-50">
                        <td class="p-2.5 text-center text-slate-400 font-bold">${index + 1}</td>
                        <td class="p-2.5 font-extrabold text-slate-800">${group.group_name || '-'}</td>
                        <td class="p-2.5 font-mono text-[11px] text-slate-500">${group.group_id || '-'}</td>
                        <td class="p-2.5 text-center">
                            <span class="px-2 py-0.5 rounded-full text-[10.5px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                ${group.total_participants || 0}
                            </span>
                        </td>
                    </tr>
                `;
            });
            html += `
                        </tbody>
                    </table>
                </div>
            `;
        } else {
            html = `
                <div class="p-6 text-center text-slate-400">
                    <i class="ti ti-users-minus text-3xl mb-1 block"></i>
                    <span>Tidak ada grup WhatsApp yang ditemukan pada perangkat ini.</span>
                </div>
            `;
        }
        $('#groupsContent').html(html);
    }

    function downloadQRCode(qrCodeData, deviceNumber) {
        const link = document.createElement('a');
        link.href = qrCodeData;
        link.download = `wa-qr-${deviceNumber}.png`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

});
</script>
@endpush
