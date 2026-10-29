@extends('v2.layouts.app')

@section('titlepage', 'Riwayat Pesan WhatsApp')
@section('page_title', 'Riwayat Pesan WhatsApp')

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
                <i class="ti ti-message-dots text-2xl" style="color: {{ $themeColor1 }};"></i>
                <span>Riwayat Log Pesan WhatsApp</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Audit trail dan log pengiriman pesan otomatis notifikasi, OTP, dan pesan sistem
            </p>
        </div>

        <!-- Breadcrumbs & Back Action -->
        <div class="flex items-center gap-3 self-start md:self-auto">
            <a href="{{ route('wagateway.index') }}" 
               class="h-9 px-3.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition inline-flex items-center gap-1.5 border border-slate-200">
                <i class="ti ti-arrow-left text-sm"></i>
                <span>Kembali ke Gateway</span>
            </a>
            <nav class="hidden sm:flex items-center text-xs text-slate-400 font-medium">
                <a href="{{ route('dashboard.index') }}" class="hover:text-slate-700 transition flex items-center gap-1">
                    <i class="ti ti-home text-sm"></i>
                    <span>Dashboard</span>
                </a>
                <span class="mx-2 text-slate-300">/</span>
                <span class="font-bold text-slate-800">Riwayat Pesan</span>
            </nav>
        </div>
    </div>

    <!-- ================= 2. SEAMLESS SOLID TABLE ================= -->
    <div class="bg-white border border-slate-200/90 rounded-2xl shadow-xs overflow-hidden">
        <!-- Seamless Solid Header -->
        <div class="px-5 py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-white border-0"
             style="background-color: {{ $themeColor1 }};">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-white/20 text-white flex items-center justify-center text-xs font-bold border border-white/20 shadow-2xs backdrop-blur-xs">
                    <i class="ti ti-messages"></i>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-white tracking-tight">
                        Log Antrean & Hasil Kirim Pesan
                    </h3>
                </div>
            </div>
            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-white/20 text-white border border-white/30 shadow-2xs backdrop-blur-xs self-start sm:self-auto">
                {{ $messages->total() }} Total Pesan
            </span>
        </div>

        <!-- Table Container -->
        <div class="overflow-x-auto w-full" style="background-color: {{ $themeColor1 }};">
            <table class="w-full text-left text-xs border-0 border-collapse">
                <thead class="text-white font-bold uppercase tracking-wider text-[11px] border-0 border-t-0 border-b border-black/15"
                       style="background-color: {{ $themeColor1 }};">
                    <tr class="border-0 border-t-0 text-white/90">
                        <th class="py-2.5 px-3.5 text-center w-14 text-white/90 border-0 border-t-0">No</th>
                        <th class="py-2.5 px-3.5 text-center w-32 text-white/90 border-0 border-t-0">Pengirim</th>
                        <th class="py-2.5 px-3.5 text-center w-32 text-white/90 border-0 border-t-0">Penerima</th>
                        <th class="py-2.5 px-4 text-white/90 border-0 border-t-0 min-w-[240px]">Isi Pesan</th>
                        <th class="py-2.5 px-3.5 text-center w-28 text-white/90 border-0 border-t-0">Status</th>
                        <th class="py-2.5 px-4 text-white/90 border-0 border-t-0 min-w-[140px]">Message ID</th>
                        <th class="py-2.5 px-3.5 text-center w-36 text-white/90 border-0 border-t-0">Waktu Kirim</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700 font-medium bg-white">
                    @forelse($messages as $index => $message)
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <!-- No -->
                        <td class="py-2.5 px-3.5 text-center text-slate-400 font-bold">
                            {{ $messages->firstItem() + $index }}
                        </td>

                        <!-- Pengirim -->
                        <td class="py-2.5 px-3.5 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md font-mono text-[11px] font-bold bg-slate-100 text-slate-800 border border-slate-200">
                                {{ $message->pengirim }}
                            </span>
                        </td>

                        <!-- Penerima -->
                        <td class="py-2.5 px-3.5 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md font-mono text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                {{ $message->penerima }}
                            </span>
                        </td>

                        <!-- Pesan -->
                        <td class="py-2.5 px-4">
                            <div class="max-w-md">
                                <p class="text-[11.5px] text-slate-700 leading-snug line-clamp-2 break-words">
                                    {{ $message->pesan }}
                                </p>
                                @if (strlen($message->pesan) > 80)
                                    <button type="button" class="btn-show-full-msg text-[11px] font-bold text-emerald-700 hover:text-emerald-800 mt-1 inline-flex items-center gap-1 cursor-pointer"
                                            data-message="{{ $message->pesan }}"
                                            data-sender="{{ $message->pengirim }}"
                                            data-target="{{ $message->penerima }}">
                                        <i class="ti ti-eye text-xs"></i> Lihat Lengkap
                                    </button>
                                @endif
                            </div>
                        </td>

                        <!-- Status -->
                        <td class="py-2.5 px-3.5 text-center">
                            @if ($message->status === 'success')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <i class="ti ti-check text-[10px]"></i> Berhasil
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200"
                                      title="{{ $message->error_message }}">
                                    <i class="ti ti-x text-[10px]"></i> Gagal
                                </span>
                            @endif
                        </td>

                        <!-- Message ID / Error -->
                        <td class="py-2.5 px-4">
                            @if ($message->message_id)
                                <span class="font-mono text-[10.5px] text-emerald-800 bg-slate-50 px-1.5 py-0.5 rounded border border-slate-200 block truncate max-w-[160px]">
                                    {{ $message->message_id }}
                                </span>
                            @elseif($message->error_message)
                                <span class="text-[10.5px] text-rose-600 italic block truncate max-w-[160px]" title="{{ $message->error_message }}">
                                    {{ $message->error_message }}
                                </span>
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>

                        <!-- Waktu -->
                        <td class="py-2.5 px-3.5 text-center">
                            <div class="inline-flex flex-col items-center">
                                <span class="font-bold text-slate-800 text-xs">
                                    {{ $message->created_at->format('d M Y') }}
                                </span>
                                <span class="inline-flex items-center gap-1 font-mono text-[10.5px] font-bold text-emerald-700 mt-0.5">
                                    <i class="ti ti-clock text-[10px]"></i>
                                    {{ $message->created_at->format('H:i:s') }}
                                </span>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-10 text-center text-slate-400">
                            <i class="ti ti-messages-off text-3xl mb-2 block text-slate-300"></i>
                            <span>Belum ada riwayat pesan WhatsApp yang dikirimkan.</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($messages->hasPages())
            <div class="px-5 py-3.5 bg-white border-t border-slate-100">
                {{ $messages->links('v2.components.pagination') }}
            </div>
        @endif
    </div>

</div>

<!-- Modal Full Message -->
<div id="fullMessageModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl overflow-hidden border border-slate-100 transform transition-all">
        <div class="px-5 py-4 flex items-center justify-between text-white" style="background-color: {{ $themeColor1 }};">
            <div class="flex items-center gap-2">
                <i class="ti ti-message-2 text-lg"></i>
                <h3 class="text-sm font-black tracking-tight">Detail Isi Pesan WhatsApp</h3>
            </div>
            <button type="button" class="close-msg-modal text-white/80 hover:text-white cursor-pointer">
                <i class="ti ti-x text-lg"></i>
            </button>
        </div>
        <div class="p-6 space-y-4">
            <div class="grid grid-cols-2 gap-3 p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                <div>
                    <span class="text-slate-400 block text-[10.5px]">Dari (Sender):</span>
                    <span id="detailSender" class="font-mono font-bold text-slate-800"></span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[10.5px]">Kepada (Target):</span>
                    <span id="detailTarget" class="font-mono font-bold text-emerald-700"></span>
                </div>
            </div>
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700">Konten Pesan:</label>
                <div id="detailMessageContent" class="p-3.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 font-medium whitespace-pre-wrap leading-relaxed max-h-60 overflow-y-auto"></div>
            </div>
        </div>
        <div class="px-5 py-3 bg-slate-50 border-t border-slate-100 flex justify-end">
            <button type="button" class="close-msg-modal px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-bold rounded-xl transition cursor-pointer">
                Tutup
            </button>
        </div>
    </div>
</div>

@endsection

@push('myscript')
<script>
$(document).ready(function() {
    $('.btn-show-full-msg').on('click', function() {
        let msg = $(this).data('message');
        let sender = $(this).data('sender');
        let target = $(this).data('target');

        $('#detailSender').text(sender);
        $('#detailTarget').text(target);
        $('#detailMessageContent').text(msg);

        $('#fullMessageModal').removeClass('hidden');
    });

    $('.close-msg-modal').on('click', function() {
        $('#fullMessageModal').addClass('hidden');
    });

    $('#fullMessageModal').on('click', function(e) {
        if (e.target === this) {
            $(this).addClass('hidden');
        }
    });
});
</script>
@endpush
