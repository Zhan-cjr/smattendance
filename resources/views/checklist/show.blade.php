@extends('layouts.mobile.modern')
@section('title', 'Detail Laporan ' . ucfirst($laporan->tipe))

@section('header_left')
    <a href="{{ route('checklist.index') }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/10 text-white active:scale-95 transition-all">
        <ion-icon name="chevron-back-outline" class="text-lg"></ion-icon>
    </a>
@endsection

@push('mystyle')
<style>
    .grid-img-card {
        border-radius: 18px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        background: #0f172a;
    }
    .dark .grid-img-card {
        border-color: rgba(255, 255, 255, 0.1);
    }
    .item-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
    }
    .dark .item-card {
        background: linear-gradient(180deg, #131d31 0%, #0f172a 100%) !important;
        border-color: rgba(255, 255, 255, 0.1) !important;
    }
    .item-title {
        color: #0f172a !important;
        font-weight: 700;
        font-size: 13px;
    }
    .dark .item-title {
        color: #f8fafc !important;
    }
</style>
@endpush

@section('content')
<div class="px-3 pt-2 pb-24 max-w-md mx-auto">
    <!-- Header Card -->
    <div class="mb-4 rounded-3xl p-4 text-white shadow-md {{ $laporan->tipe == 'opening' ? 'bg-gradient-to-r from-amber-600 via-amber-700 to-slate-900' : 'bg-gradient-to-r from-indigo-700 via-indigo-800 to-slate-900' }}">
        <div class="flex items-center justify-between mb-2">
            <span class="text-[10px] font-extrabold uppercase px-2.5 py-0.5 rounded-full bg-white/20 text-white">
                LAPORAN {{ strtoupper($laporan->tipe) }}
            </span>
            <span class="text-[11px] font-semibold text-emerald-200 flex items-center gap-1">
                <i class="fa-solid fa-circle-check"></i>
                <span>Terverifikasi</span>
            </span>
        </div>
        <h3 class="text-base font-extrabold text-white">{{ $laporan->cabang->nama_cabang ?? $laporan->kode_cabang }}</h3>
        <p class="text-xs text-white/80 mt-0.5">
            Petugas: <strong>{{ $laporan->karyawan->nama_karyawan ?? $laporan->nik }}</strong>
        </p>

        <div class="mt-3 pt-2.5 border-t border-white/15 grid grid-cols-2 gap-2 text-[11px]">
            <div>
                <span class="text-white/60 block text-[9.5px]">Waktu Selesai:</span>
                <span class="font-bold text-white">{{ \Carbon\Carbon::parse($laporan->tanggal)->translatedFormat('d M Y') }} &bull; {{ date('H:i', strtotime($laporan->jam)) }} WIB</span>
            </div>
            <div>
                <span class="text-white/60 block text-[9.5px]">Kode Dokumen:</span>
                <span class="font-mono text-amber-200 font-bold">{{ $laporan->kode_laporan }}</span>
            </div>
        </div>
    </div>

    <!-- WHATSAPP SHARE CARD (HIGHLIGHT) -->
    <div class="mb-5 p-4 rounded-3xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 shadow-sm">
        <div class="flex items-center gap-2.5 mb-2.5">
            <div class="w-9 h-9 rounded-xl bg-emerald-500 text-white flex items-center justify-center text-lg shrink-0 shadow-sm">
                <i class="fa-brands fa-whatsapp"></i>
            </div>
            <div>
                <h4 class="text-xs font-extrabold text-emerald-950 dark:text-emerald-100">Kirim ke Grup WhatsApp</h4>
                <p class="text-[10.5px] text-emerald-800 dark:text-emerald-300">Bagikan pesan laporan beserta foto grid 4-in-1 langsung ke WhatsApp.</p>
            </div>
        </div>

        <!-- WhatsApp Text Preview Box -->
        <div class="mb-3 p-2.5 rounded-xl bg-white dark:bg-slate-900 border border-emerald-100 dark:border-emerald-800/60 max-h-36 overflow-y-auto">
            <pre class="text-[10.5px] font-sans text-slate-700 dark:text-slate-300 whitespace-pre-wrap leading-relaxed select-all" id="waTextContent">{{ $waText }}</pre>
        </div>

        <!-- Main Share Button: Otomatis Lampirkan Foto Grid + Teks -->
        <button type="button" id="btnShareWa" onclick="shareToWhatsApp()" class="w-full py-3 px-4 mb-2 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white text-xs font-extrabold flex items-center justify-center gap-2 shadow-md active:scale-98 transition-all">
            <i class="fa-brands fa-whatsapp text-base"></i>
            <span>Bagikan ke WhatsApp (Foto Grid + Teks)</span>
        </button>

        <div class="grid grid-cols-2 gap-2 text-center">
            <button type="button" onclick="copyWaText()" class="py-2 px-3 rounded-xl bg-white dark:bg-slate-800 border border-emerald-200 dark:border-emerald-700 text-emerald-800 dark:text-emerald-200 text-[11px] font-bold flex items-center justify-center gap-1.5 active:scale-95 transition-all">
                <i class="fa-solid fa-copy text-xs"></i>
                <span id="copyBtnText">Salin Teks Saja</span>
            </button>

            <a href="{{ $waUrl }}" target="_blank" class="py-2 px-3 rounded-xl bg-emerald-100 dark:bg-emerald-900/60 text-emerald-900 dark:text-emerald-100 text-[11px] font-bold flex items-center justify-center gap-1.5 active:scale-95 transition-all text-decoration-none">
                <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                <span>Buka Chat Saja</span>
            </a>
        </div>
    </div>

    <!-- 4-IN-1 COLLAGES SECTION -->
    <div class="mb-6">
        <div class="flex items-center justify-between mb-3">
            <div>
                <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-800 dark:text-slate-100">
                    Foto Gabungan 4-in-1 (Grid WhatsApp)
                </h4>
                <p class="text-[10.5px] text-slate-400">Tersusun otomatis per 4 checkpoint</p>
            </div>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-teal-50 dark:bg-teal-950/60 text-teal-700 dark:text-teal-300 border border-teal-200 dark:border-teal-800">
                {{ $laporan->collages->count() }} Gambar Grid
            </span>
        </div>

        <div class="space-y-4">
            @foreach($laporan->collages as $col)
                @php
                    $colExists = $col->foto_collage && Storage::disk('public')->exists($col->foto_collage);
                @endphp
                <div class="grid-img-card shadow-sm">
                    <div class="p-2.5 bg-slate-800 text-white flex items-center justify-between text-xs">
                        <span class="font-bold flex items-center gap-1.5">
                            <i class="fa-solid fa-layer-group text-amber-400"></i>
                            <span>Grid Bagian #{{ $col->batch_no }} ({{ $col->total_items }} Titik)</span>
                        </span>
                        @if($colExists)
                            <a href="{{ asset('storage/' . $col->foto_collage) }}" download="Grid_{{ $laporan->tipe }}_b{{ $col->batch_no }}.jpg" class="px-2.5 py-1 rounded-lg bg-teal-600 hover:bg-teal-700 text-white text-[10.5px] font-bold flex items-center gap-1 active:scale-95 transition-all text-decoration-none">
                                <i class="fa-solid fa-download"></i>
                                <span>Simpan ke HP</span>
                            </a>
                        @else
                            <span class="text-[10px] text-amber-400 font-semibold">Foto Kedaluwarsa (>60 hr)</span>
                        @endif
                    </div>
                    @if($colExists)
                        <img src="{{ asset('storage/' . $col->foto_collage) }}" alt="Collage Grid" class="w-full h-auto object-cover">
                    @else
                        <div class="p-6 bg-slate-900 text-slate-400 text-center flex flex-col items-center justify-center">
                            <i class="fa-solid fa-clock-rotate-left text-2xl text-slate-500 mb-1"></i>
                            <span class="text-xs font-semibold">Foto grid telah dibersihkan (>60 hari)</span>
                            <span class="text-[10px] text-slate-500">File fisik dibersihkan otomatis sesuai kebijakan retensi</span>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <!-- INDIVIDUAL CHECKPOINT PHOTOS ACCORDION / LIST -->
    <div class="mb-6">
        <div class="flex items-center justify-between mb-3">
            <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-800 dark:text-slate-100">
                Detail Tiap Titik Checkpoint ({{ $laporan->details->count() }})
            </h4>
        </div>

        <div class="space-y-3">
            @foreach($laporan->details as $idx => $det)
                @php
                    $detExists = $det->foto && Storage::disk('public')->exists($det->foto);
                @endphp
                <div class="item-card p-3 rounded-2xl shadow-2xs">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2">
                            <span class="w-5 h-5 rounded-md bg-teal-50 dark:bg-teal-950/60 text-teal-700 dark:text-teal-300 font-bold text-[10.5px] flex items-center justify-center">
                                {{ $idx + 1 }}
                            </span>
                            <h5 class="item-title">
                                {{ $det->nama_checkpoint }}
                            </h5>
                        </div>
                        <span class="text-[9.5px] font-bold text-emerald-600 bg-emerald-50 dark:bg-emerald-950/60 px-2 py-0.5 rounded-full">
                            [OK] Terverifikasi
                        </span>
                    </div>

                    <div class="aspect-video rounded-xl overflow-hidden bg-slate-900 mb-2 border border-slate-200 dark:border-slate-700">
                        @if($detExists)
                            <img src="{{ asset('storage/' . $det->foto) }}" alt="{{ $det->nama_checkpoint }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex flex-col items-center justify-center p-4 text-center bg-slate-900 text-slate-400">
                                <i class="fa-solid fa-clock-rotate-left text-xl text-slate-500 mb-1"></i>
                                <span class="text-xs font-semibold">Foto telah dibersihkan (>60 hari)</span>
                                <span class="text-[10px] text-slate-500">File fisik dibersihkan otomatis sesuai kebijakan retensi</span>
                            </div>
                        @endif
                    </div>

                    @if($det->catatan)
                        <div class="text-[10.5px] text-slate-600 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/80 p-2 rounded-lg">
                            <i class="fa-solid fa-comment-dots text-slate-400 mr-1"></i>
                            <span>{{ $det->catatan }}</span>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <!-- Back Button -->
    <div class="pt-2">
        <a href="{{ route('checklist.index') }}" class="w-full py-3 rounded-2xl bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-200 font-bold text-xs flex items-center justify-center gap-1.5 active:scale-95 transition-all text-decoration-none">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Menu Checklist</span>
        </a>
    </div>
</div>
@endsection

@push('myscript')
<script>
    const collageUrls = @json($laporan->collages->map(fn($c) => asset('storage/' . $c->foto_collage)));
    const waText = @json($waText);
    const waUrl = @json($waUrl);

    function copyWaText() {
        const text = document.getElementById('waTextContent').innerText;
        navigator.clipboard.writeText(text).then(() => {
            const btn = document.getElementById('copyBtnText');
            btn.innerText = 'Tersalin!';
            setTimeout(() => { btn.innerText = 'Salin Teks Saja'; }, 2000);
        });
    }

    async function shareToWhatsApp() {
        const btn = document.getElementById('btnShareWa');
        const originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner animate-spin text-base"></i> <span>Menyiapkan Foto Grid...</span>';

        try {
            // Fetch collage image files
            const filesToShare = [];
            for (let i = 0; i < collageUrls.length; i++) {
                try {
                    const res = await fetch(collageUrls[i]);
                    const blob = await res.blob();
                    const file = new File([blob], `Grid_{{ $laporan->tipe }}_b${i+1}.jpg`, { type: 'image/jpeg' });
                    filesToShare.push(file);
                } catch (fetchErr) {
                    console.warn('Could not fetch collage blob:', fetchErr);
                }
            }

            // Web Share API Level 2 (Direct Share to WhatsApp with Attached Images + Text)
            if (filesToShare.length > 0 && navigator.canShare && navigator.canShare({ files: filesToShare })) {
                await navigator.share({
                    title: 'Laporan {{ ucfirst($laporan->tipe) }} Toko',
                    text: waText,
                    files: filesToShare
                });
                btn.innerHTML = originalHtml;
                btn.disabled = false;
                return;
            }
        } catch (e) {
            console.warn('Web Share API error or cancelled:', e);
            if (e.name === 'AbortError') {
                // User simply cancelled the share dialog
                btn.innerHTML = originalHtml;
                btn.disabled = false;
                return;
            }
        }

        // Fallback for browsers without direct file sharing (PC or older mobile browsers)
        try {
            await navigator.clipboard.writeText(waText);
        } catch(e) {}

        // Trigger automatic download of grid images
        collageUrls.forEach((url, idx) => {
            const a = document.createElement('a');
            a.href = url;
            a.download = `Grid_{{ $laporan->tipe }}_b${idx+1}.jpg`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        });

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'info',
                title: 'Membuka WhatsApp...',
                text: 'Teks laporan telah disalin & foto grid otomatis diunduh ke galeri. Silakan tempel teks dan lampirkan foto di WhatsApp.',
                timer: 2500,
                showConfirmButton: false
            });
        }

        setTimeout(() => {
            window.open(waUrl, '_blank');
            btn.innerHTML = originalHtml;
            btn.disabled = false;
        }, 1200);
    }
</script>
@endpush
