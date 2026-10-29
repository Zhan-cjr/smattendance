@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
    $pathFoto = 'uploads/aktivitas/' . $aktivitaskaryawan->foto;
    $hasFoto = !empty($aktivitaskaryawan->foto) && (Storage::disk('public')->exists($pathFoto) || Storage::exists('public/' . $pathFoto));
    $fotoUrl = $hasFoto ? asset('storage/' . $pathFoto) : '';
    $namaKaryawan = $aktivitaskaryawan->nama_karyawan ?? ($aktivitaskaryawan->karyawan->nama_karyawan ?? 'Karyawan N/A');
@endphp

<div class="space-y-4">
    <!-- Header Card Karyawan -->
    <div class="p-3.5 sm:p-4 rounded-xl bg-slate-50 border border-slate-200/90 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            @if(!empty($aktivitaskaryawan->karyawan->foto) && Storage::disk('public')->exists('/karyawan/' . $aktivitaskaryawan->karyawan->foto))
                <img src="{{ url('/storage/karyawan/' . $aktivitaskaryawan->karyawan->foto) }}" alt="{{ $namaKaryawan }}" class="w-12 h-12 rounded-xl object-cover border border-slate-200 shadow-2xs">
            @else
                <div class="w-12 h-12 rounded-xl text-white flex items-center justify-center font-black text-sm shadow-sm"
                     style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                    {{ strtoupper(substr($namaKaryawan, 0, 2)) }}
                </div>
            @endif

            <div>
                <h4 class="text-sm font-black text-slate-900 tracking-tight mb-0.5">{{ $namaKaryawan }}</h4>
                <div class="flex items-center gap-1.5 flex-wrap text-xs text-slate-500">
                    <span class="font-mono font-bold text-slate-700 bg-white px-1.5 py-0.2 rounded border border-slate-200 text-[11px]">{{ $aktivitaskaryawan->nik }}</span>
                    <span>&bull;</span>
                    <span>{{ $aktivitaskaryawan->karyawan->jabatan->nama_jabatan ?? '-' }}</span>
                    <span>&bull;</span>
                    <span class="font-semibold text-slate-700">{{ $aktivitaskaryawan->karyawan->departemen->nama_dept ?? '-' }}</span>
                </div>
            </div>
        </div>

        <div class="text-left sm:text-right w-full sm:w-auto border-t sm:border-t-0 pt-2 sm:pt-0 border-slate-200">
            <span class="text-[11px] font-bold text-slate-400 block">Tanggal & Waktu</span>
            <div class="text-xs font-black text-slate-800 flex items-center sm:justify-end gap-1.5 mt-0.5">
                <i class="ti ti-calendar text-emerald-600"></i>
                <span>{{ $aktivitaskaryawan->created_at ? $aktivitaskaryawan->created_at->translatedFormat('d F Y') : '-' }}</span>
                <span class="text-slate-300">|</span>
                <span class="font-mono">{{ $aktivitaskaryawan->created_at ? $aktivitaskaryawan->created_at->format('H:i:s') : '-' }}</span>
            </div>
        </div>
    </div>

    <!-- 2 Kolom: Kiri (Deskripsi Aktivitas & Peta), Kanan (Foto Dokumentasi) -->
    <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
        
        <!-- Left Side: Deskripsi & Lokasi Koordinat (7 Cols) -->
        <div class="md:col-span-7 space-y-3">
            <!-- Deskripsi Box -->
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700">
                    Deskripsi / Rincian Aktivitas
                </label>
                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-700 leading-relaxed min-h-[90px] whitespace-pre-line">
                    {{ $aktivitaskaryawan->aktivitas ?: 'Tidak ada uraian deskripsi yang dicatat.' }}
                </div>
            </div>

            <!-- Lokasi & Mini Peta -->
            <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                    <label class="block text-xs font-bold text-slate-700">
                        Titik Koordinat Lokasi
                    </label>
                    @if ($aktivitaskaryawan->lokasi)
                        <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($aktivitaskaryawan->lokasi) }}" target="_blank" class="text-[11px] font-bold text-sky-700 hover:text-sky-800 inline-flex items-center gap-1">
                            <i class="ti ti-external-link text-xs"></i>
                            <span>Buka Google Maps</span>
                        </a>
                    @endif
                </div>

                @if ($aktivitaskaryawan->lokasi)
                    <div class="rounded-xl overflow-hidden border border-slate-200 relative shadow-2xs">
                        <div id="showDetailAktivitasMap" style="height: 200px; width: 100%;"></div>
                        <div class="p-2 bg-white border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-600">
                            <span class="font-mono font-bold"><i class="ti ti-map-pin text-sky-600 mr-1"></i>{{ $aktivitaskaryawan->lokasi }}</span>
                        </div>
                    </div>
                @else
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-center text-slate-400 text-xs">
                        <i class="ti ti-map-pin-off text-2xl block mb-1"></i>
                        <span>Tidak ada data koordinat lokasi untuk aktivitas ini</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Side: Foto Bukti Dokumentasi (5 Cols) -->
        <div class="md:col-span-5 space-y-1.5">
            <label class="block text-xs font-bold text-slate-700">
                Foto Dokumentasi
            </label>

            <div class="rounded-xl border border-slate-200 bg-slate-50 p-3 text-center flex flex-col items-center justify-center min-h-[290px] shadow-2xs">
                @if ($hasFoto)
                    <div class="relative group w-full flex flex-col items-center">
                        <img src="{{ $fotoUrl }}" 
                             alt="Foto Aktivitas" 
                             class="max-h-[220px] w-auto max-w-full rounded-lg object-contain shadow-xs cursor-pointer hover:opacity-95 transition"
                             onclick="showImageModal('{{ $fotoUrl }}', 'Foto Aktivitas - {{ addslashes($namaKaryawan) }}')">
                        
                        <div class="mt-3 w-full flex items-center justify-center gap-2">
                            <button type="button" 
                                    onclick="showImageModal('{{ $fotoUrl }}', 'Foto Aktivitas - {{ addslashes($namaKaryawan) }}')" 
                                    class="px-3 py-1.5 bg-slate-200 hover:bg-slate-300 text-slate-800 font-bold rounded-lg text-xs transition inline-flex items-center gap-1 cursor-pointer">
                                <i class="ti ti-zoom-in text-sm"></i>
                                <span>Perbesar</span>
                            </button>
                            <a href="{{ $fotoUrl }}" download class="px-3 py-1.5 text-white font-bold rounded-lg text-xs transition inline-flex items-center gap-1 cursor-pointer"
                               style="background-color: {{ $themeColor1 }};">
                                <i class="ti ti-download text-sm"></i>
                                <span>Download</span>
                            </a>
                        </div>
                    </div>
                @else
                    <div class="py-12 text-slate-400 text-center">
                        <i class="ti ti-photo-off text-4xl block mb-2 opacity-50"></i>
                        <p class="text-xs font-semibold text-slate-500 mb-0">Tidak ada foto bukti terlampir</p>
                    </div>
                @endif
            </div>
        </div>

    </div>

    <!-- Modal Footer Actions -->
    <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
        <div class="text-xs text-slate-400">
            ID Aktivitas: #{{ $aktivitaskaryawan->id }}
        </div>
        <div class="flex items-center gap-2">
            @can('aktivitaskaryawan.edit')
                <button type="button" 
                        onclick="$('#modalAktivitasBody').load('/aktivitaskaryawan/{{ $aktivitaskaryawan->id }}/edit')"
                        class="px-4 py-2 bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold rounded-xl text-xs transition inline-flex items-center gap-1 cursor-pointer">
                    <i class="ti ti-edit text-sm"></i>
                    <span>Edit Data</span>
                </button>
            @endcan
            <button type="button" 
                    class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition active:scale-95 cursor-pointer" 
                    data-bs-dismiss="modal">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    @if ($aktivitaskaryawan->lokasi)
        const lokasiStr = "{{ $aktivitaskaryawan->lokasi }}";
        const parts = lokasiStr.split(',');
        const lat = parseFloat(parts[0]);
        const lng = parseFloat(parts[1]);

        if (!isNaN(lat) && !isNaN(lng)) {
            setTimeout(function() {
                const mapEl = document.getElementById('showDetailAktivitasMap');
                if (mapEl) {
                    const detailMap = L.map('showDetailAktivitasMap').setView([lat, lng], 15);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '© OpenStreetMap'
                    }).addTo(detailMap);

                    L.marker([lat, lng]).addTo(detailMap)
                        .bindPopup(`<strong class="text-xs">{{ addslashes($namaKaryawan) }}</strong><br><span class="text-[11px] text-slate-500">${lokasiStr}</span>`)
                        .openPopup();

                    setTimeout(() => {
                        detailMap.invalidateSize();
                    }, 200);
                }
            }, 300);
        }
    @endif
});
</script>
