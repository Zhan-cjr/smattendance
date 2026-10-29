@extends('v2.layouts.app')

@section('titlepage', 'Aktivitas Karyawan')
@section('page_title', 'Laporan Aktivitas Harian')

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
@endphp

<div class="space-y-5">

    <!-- ================= 1. PAGE HEADER (TITLE & ACTIONS) ================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-1">
        <!-- Title & Subtitle -->
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="ti ti-activity text-2xl" style="color: {{ $themeColor1 }};"></i>
                <span>Data Aktivitas Karyawan</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Pantau laporan aktivitas, *daily work log*, dan progres pekerjaan harian karyawan beserta bukti foto & lokasi
            </p>
        </div>

        <!-- Right Side: Breadcrumbs & Action Buttons -->
        <div class="flex flex-col md:items-end gap-2.5">
            <!-- Breadcrumb Navigation -->
            <nav class="flex items-center text-xs text-slate-400 font-medium">
                <a href="{{ route('dashboard.index') }}" class="hover:text-slate-700 transition flex items-center gap-1">
                    <i class="ti ti-home text-sm"></i>
                    <span>Dashboard</span>
                </a>
                <span class="mx-2 text-slate-300">/</span>
                <span class="text-slate-500 flex items-center gap-1">
                    <i class="ti ti-calendar text-sm"></i>
                    <span>Aktivitas</span>
                </span>
                <span class="mx-2 text-slate-300">/</span>
                <span class="font-bold text-slate-800">Aktivitas Karyawan</span>
            </nav>

            <!-- Action Buttons -->
            <div class="flex flex-wrap items-center gap-2">
                @can('aktivitaskaryawan.index')
                    @if (request('nik') || auth()->user()->hasRole('karyawan'))
                        <a href="{{ route('aktivitaskaryawan.export.pdf', request()->query()) }}" target="_blank"
                           class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white border border-slate-300/90 text-rose-600 hover:bg-rose-50 font-bold rounded-xl text-xs shadow-2xs transition active:scale-95 cursor-pointer">
                            <i class="ti ti-file-type-pdf text-sm"></i>
                            <span>Export PDF</span>
                        </a>
                    @else
                        <button type="button" 
                                onclick="Swal.fire({title:'Pilih Karyawan', text:'Silakan pilih karyawan terlebih dahulu pada filter untuk export laporan PDF.', icon:'info', confirmButtonColor:'{{ $themeColor1 }}', customClass:{popup:'rounded-2xl'}})"
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-slate-100 text-slate-400 font-bold rounded-xl text-xs shadow-2xs cursor-not-allowed opacity-80"
                                title="Pilih karyawan pada filter terlebih dahulu untuk export PDF">
                            <i class="ti ti-file-type-pdf text-sm"></i>
                            <span>Export PDF</span>
                        </button>
                    @endif
                @endcan

                @can('aktivitaskaryawan.create')
                    <button type="button" id="btnCreateAktivitas" 
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-white font-bold rounded-xl text-xs shadow-xs transition-all duration-200 active:scale-95 cursor-pointer hover:opacity-95"
                            style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-plus text-sm"></i>
                        <span>Tambah Aktivitas</span>
                    </button>
                @endcan
            </div>
        </div>
    </div>

    <!-- ================= 2. FILTER BAR STANDAR V2 ================= -->
    <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-2xs">
        <form action="{{ route('aktivitaskaryawan.index') }}" method="GET" class="space-y-3">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-2.5 sm:gap-3 items-center">
                
                <!-- Select Karyawan (Jika bukan role karyawan) -->
                @if (!auth()->user()->hasRole('karyawan'))
                    <div class="md:col-span-4">
                        <select name="nik" id="filter_nik_aktivitas" class="w-full px-3 py-2.5 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-medium shadow-2xs focus:outline-none focus:border-emerald-600 transition cursor-pointer select2NikAktivitas">
                            <option value="">Semua Karyawan</option>
                            @foreach ($karyawans as $karyawan)
                                <option value="{{ $karyawan->nik }}" {{ request('nik') == $karyawan->nik ? 'selected' : '' }}>
                                    {{ $karyawan->nik }} - {{ $karyawan->nama_karyawan }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <!-- Tanggal Awal -->
                <div class="{{ auth()->user()->hasRole('karyawan') ? 'md:col-span-5' : 'md:col-span-3' }} relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center z-10">
                        <i class="ti ti-calendar text-sm"></i>
                    </span>
                    <input type="text" 
                           id="tanggal_awal" 
                           name="tanggal_awal" 
                           value="{{ request('tanggal_awal') }}" 
                           placeholder="Tanggal Awal"
                           class="w-full h-10 pl-9 pr-3 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-medium flatpickr-date outline-none transition cursor-pointer">
                </div>

                <!-- Tanggal Akhir -->
                <div class="{{ auth()->user()->hasRole('karyawan') ? 'md:col-span-5' : 'md:col-span-3' }} relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center z-10">
                        <i class="ti ti-calendar text-sm"></i>
                    </span>
                    <input type="text" 
                           id="tanggal_akhir" 
                           name="tanggal_akhir" 
                           value="{{ request('tanggal_akhir') }}" 
                           placeholder="Tanggal Akhir"
                           class="w-full h-10 pl-9 pr-3 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-medium flatpickr-date outline-none transition cursor-pointer">
                </div>

                <!-- Tombol Filter & Reset -->
                <div class="{{ auth()->user()->hasRole('karyawan') ? 'md:col-span-2' : 'md:col-span-2' }} flex items-center gap-1.5">
                    <button type="submit" 
                            class="w-full px-4 py-2.5 text-white font-bold text-xs rounded-xl shadow-xs transition inline-flex items-center justify-center gap-1.5 cursor-pointer hover:opacity-95 active:scale-95 whitespace-nowrap"
                            style="background-color: {{ $themeColor1 }};">
                        <i class="ti ti-search text-sm"></i>
                        <span>Filter</span>
                    </button>
                    @if(request()->hasAny(['nik', 'tanggal_awal', 'tanggal_akhir']))
                        <a href="{{ route('aktivitaskaryawan.index') }}" 
                           class="px-3 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-xl transition inline-flex items-center gap-1 active:scale-95 shrink-0"
                           title="Reset Filter">
                            <i class="ti ti-rotate text-sm"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>

        <!-- Counter Information Bar -->
        <div class="mt-3.5 pt-3 border-t border-slate-100 flex flex-wrap items-center justify-between gap-2 text-xs">
            <div class="text-slate-500 text-[11.5px]">
                Menampilkan <strong>{{ $aktivitas->firstItem() ?? 0 }} - {{ $aktivitas->lastItem() ?? 0 }}</strong> dari <strong>{{ $aktivitas->total() }}</strong> catatan aktivitas
            </div>
            <div class="text-slate-400 text-[11px] font-medium">
                Pembaruan data aktivitas real-time
            </div>
        </div>
    </div>

    <!-- ================= 3. LIST CARD DATA AKTIVITAS MODERN V2 ================= -->
    <div class="space-y-3">
        @forelse ($aktivitas as $item)
            @php
                $pathFoto = 'uploads/aktivitas/' . $item->foto;
                $hasFoto = !empty($item->foto) && (Storage::disk('public')->exists($pathFoto) || Storage::exists('public/' . $pathFoto));
                $fotoUrl = $hasFoto ? asset('storage/' . $pathFoto) : '';
                $namaKaryawan = $item->nama_karyawan ?? ($item->karyawan->nama_karyawan ?? 'Karyawan N/A');
            @endphp
            <div class="bg-white rounded-2xl border border-slate-200/90 hover:border-emerald-300 px-4 py-3.5 sm:px-5 sm:py-3.5 shadow-2xs hover:shadow-sm transition-all duration-200 group">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 sm:gap-4">
                    
                    <!-- Left Section: Avatar / Foto Bukti, Identity, Activity Description & Badges -->
                    <div class="flex items-center gap-3 sm:gap-3.5 min-w-0 flex-1">
                        
                        <!-- Row Index Badge -->
                        <div class="hidden sm:flex w-6 h-6 rounded-lg bg-slate-100 text-slate-500 text-[10.5px] font-bold items-center justify-center shrink-0 border border-slate-200/80">
                            {{ $loop->iteration + $aktivitas->firstItem() - 1 }}
                        </div>

                        <!-- Foto Aktivitas Thumbnail with click to preview -->
                        <div class="relative shrink-0">
                            @if ($hasFoto)
                                <img src="{{ $fotoUrl }}" 
                                     alt="Bukti Foto" 
                                     class="w-11 h-11 rounded-xl object-cover border border-slate-200 shadow-2xs cursor-pointer hover:scale-105 transition-transform"
                                     onclick="showImageModal('{{ $fotoUrl }}', 'Foto Aktivitas - {{ addslashes($namaKaryawan) }}')"
                                     title="Klik untuk memperbesar foto">
                                <span class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full bg-emerald-600 text-white flex items-center justify-center text-[9px] ring-2 ring-white" title="Ada Foto Bukti">
                                    <i class="ti ti-camera"></i>
                                </span>
                            @else
                                <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-400 border border-slate-200 flex items-center justify-center text-base shadow-2xs">
                                    <i class="ti ti-photo-off"></i>
                                </div>
                            @endif
                        </div>

                        <!-- Details Block -->
                        <div class="min-w-0 flex-1 space-y-1">
                            <!-- Line 1: Nama, NIK, Tanggal & Jam Input -->
                            <div class="flex items-center flex-wrap gap-1.5 sm:gap-2">
                                <h3 class="text-xs sm:text-sm font-black text-slate-900 tracking-tight group-hover:text-emerald-700 transition truncate">
                                    {{ $namaKaryawan }}
                                </h3>
                                
                                <span class="px-1.5 py-0.2 rounded text-[11px] font-mono font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $item->nik }}
                                </span>

                                <span class="text-slate-300 text-xs hidden sm:inline">&bull;</span>

                                <!-- Tanggal Aktivitas -->
                                <div class="inline-flex items-center gap-1 text-[11.5px] text-slate-700 font-bold">
                                    <i class="ti ti-calendar text-emerald-600 text-xs"></i>
                                    <span>{{ $item->created_at ? $item->created_at->translatedFormat('d M Y') : '-' }}</span>
                                </div>

                                <!-- Waktu Input -->
                                <span class="px-1.5 py-0.2 rounded text-[10px] font-mono font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                    <i class="ti ti-clock text-[10px] text-slate-400 mr-0.5"></i>{{ $item->created_at ? $item->created_at->format('H:i') : '-' }}
                                </span>
                            </div>

                            <!-- Line 2: Deskripsi Aktivitas & Lokasi GPS Chip -->
                            <div class="flex items-center flex-wrap gap-2 text-xs">
                                @if($item->aktivitas)
                                    <div class="text-slate-600 text-[11.5px] line-clamp-1 max-w-xl font-normal" title="{{ $item->aktivitas }}">
                                        <i class="ti ti-notes text-slate-400 text-xs mr-0.5"></i>
                                        <span>{{ Str::limit($item->aktivitas, 90) }}</span>
                                    </div>
                                @else
                                    <span class="text-slate-400 text-[11px] italic">Tidak ada catatan aktivitas</span>
                                @endif

                                @if ($item->lokasi)
                                    <span class="text-slate-300 text-xs hidden sm:inline">|</span>
                                    <button type="button" 
                                            onclick="showMapModal('{{ $item->lokasi }}', 'Lokasi - {{ addslashes($namaKaryawan) }}')"
                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-sky-50 hover:bg-sky-100 text-sky-800 border border-sky-200 text-[10.5px] font-bold transition active:scale-95 cursor-pointer shadow-2xs"
                                            title="Lihat Peta Koordinat">
                                        <i class="ti ti-map-pin text-sky-600 text-xs"></i>
                                        <span>{{ Str::limit($item->lokasi, 25) }}</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Right Section: Action Buttons Toolbar -->
                    <div class="flex items-center gap-1.5 shrink-0 pt-2 lg:pt-0 border-t lg:border-t-0 border-slate-100 w-full lg:w-auto justify-end">
                        
                        <!-- Show Detail -->
                        @can('aktivitaskaryawan.index')
                            <button type="button" 
                                    class="btnShowAktivitas w-8 h-8 rounded-lg bg-sky-50 hover:bg-sky-100 text-sky-700 border border-sky-200 flex items-center justify-center transition active:scale-95 cursor-pointer shadow-2xs" 
                                    data-id="{{ $item->id }}" 
                                    title="Lihat Detail Aktivitas">
                                <i class="ti ti-eye text-sm"></i>
                            </button>
                        @endcan

                        <!-- Edit Data -->
                        @can('aktivitaskaryawan.edit')
                            <button type="button" 
                                    class="btnEditAktivitas w-8 h-8 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 border border-blue-200 flex items-center justify-center transition active:scale-95 cursor-pointer shadow-2xs"
                                    data-id="{{ $item->id }}"
                                    title="Edit Data Aktivitas">
                                <i class="ti ti-edit text-sm"></i>
                            </button>
                        @endcan

                        <!-- Delete Data -->
                        @can('aktivitaskaryawan.delete')
                            <form action="{{ route('aktivitaskaryawan.destroy', $item->id) }}" method="POST" class="formDelete inline m-0">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="w-8 h-8 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 flex items-center justify-center transition active:scale-95 cursor-pointer shadow-2xs"
                                        title="Hapus Aktivitas">
                                    <i class="ti ti-trash text-sm"></i>
                                </button>
                            </form>
                        @endcan
                    </div>

                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-slate-200/90 p-12 text-center shadow-2xs">
                <div class="w-16 h-16 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center text-3xl mx-auto mb-3 shadow-inner">
                    <i class="ti ti-activity-heartbeat"></i>
                </div>
                <h4 class="text-sm font-bold text-slate-800 mb-1">Tidak Ada Data Aktivitas</h4>
                <p class="text-xs text-slate-500 max-w-sm mx-auto mb-4">
                    Belum ada riwayat laporan aktivitas harian yang tercatat sesuai filter yang dipilih.
                </p>
                @can('aktivitaskaryawan.create')
                    <button type="button" onclick="$('#btnCreateAktivitas').click();" 
                            class="inline-flex items-center gap-1.5 px-4 py-2.5 text-white font-bold rounded-xl text-xs shadow-xs transition hover:opacity-95 active:scale-95 cursor-pointer"
                            style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-plus text-sm"></i>
                        <span>Tambah Aktivitas Sekarang</span>
                    </button>
                @endcan
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if ($aktivitas->hasPages())
        <div class="pt-2">
            {{ $aktivitas->appends(request()->query())->links('v2.components.pagination') }}
        </div>
    @endif

</div>

<!-- ================= 4. MODAL FORM UNIVERSAL V2 (CREATE / EDIT / SHOW) ================= -->
<div class="modal fade" id="modalAktivitas" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-2xl border-0 shadow-2xl overflow-hidden bg-white">
            <!-- Modal Header Konsisten V2 -->
            <div class="px-6 py-4 bg-white border-b border-slate-100 flex items-center justify-between text-slate-900">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg shrink-0 font-bold text-white shadow-sm"
                         style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-activity"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-sm sm:text-base font-extrabold text-slate-900 tracking-tight truncate mb-0" id="modalAktivitasTitle">Formulir Aktivitas</h5>
                        <p class="text-xs text-slate-500 mb-0 font-medium" id="modalAktivitasSubtitle">Kelola laporan kegiatan dan *work log* harian karyawan</p>
                    </div>
                </div>
                <button type="button" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center transition-colors cursor-pointer shrink-0" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x text-lg"></i>
                </button>
            </div>

            <!-- Modal Body Container (AJAX Loaded) -->
            <div class="p-6 bg-white" id="modalAktivitasBody">
                <div class="py-12 text-center text-slate-400">
                    <div style="width: 24px; height: 24px; border: 3px solid {{ $themeColor1 }}; border-top-color: transparent; border-radius: 50%; animation: spin 0.8s linear infinite; display: inline-block;"></div>
                    <p class="text-xs font-semibold text-slate-500 mt-2">Memuat formulir...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ================= 5. MODAL PREVIEW FOTO V2 ================= -->
<div class="modal fade" id="modalImagePreview" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-2xl border-0 shadow-2xl overflow-hidden bg-white">
            <div class="px-6 py-4 bg-white border-b border-slate-100 flex items-center justify-between text-slate-900">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center text-base font-bold">
                        <i class="ti ti-camera"></i>
                    </div>
                    <h5 class="text-sm sm:text-base font-extrabold text-slate-900 tracking-tight mb-0" id="modalImageTitle">Foto Aktivitas</h5>
                </div>
                <button type="button" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center transition-colors cursor-pointer shrink-0" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x text-lg"></i>
                </button>
            </div>
            <div class="p-4 sm:p-6 bg-slate-900/5 text-center flex flex-col items-center justify-center min-h-[300px]">
                <img id="previewImageEl" src="" alt="Foto Aktivitas" class="max-h-[500px] w-auto max-w-full rounded-xl object-contain shadow-lg">
            </div>
            <div class="px-6 py-3.5 bg-white border-t border-slate-100 flex items-center justify-between">
                <a id="btnDownloadImage" href="" download class="inline-flex items-center gap-1.5 px-3.5 py-2 text-white font-bold rounded-xl text-xs shadow-xs transition hover:opacity-95 cursor-pointer"
                   style="background-color: {{ $themeColor1 }};">
                    <i class="ti ti-download text-sm"></i>
                    <span>Download Foto</span>
                </a>
                <button type="button" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition cursor-pointer" data-bs-dismiss="modal">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ================= 6. MODAL PETA INTERAKTIF V2 ================= -->
<div class="modal fade" id="modalMapPreview" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-2xl border-0 shadow-2xl overflow-hidden bg-white">
            <div class="px-6 py-4 bg-white border-b border-slate-100 flex items-center justify-between text-slate-900">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-sky-50 text-sky-700 flex items-center justify-center text-base font-bold">
                        <i class="ti ti-map-pin"></i>
                    </div>
                    <div>
                        <h5 class="text-sm sm:text-base font-extrabold text-slate-900 tracking-tight mb-0" id="modalMapTitle">Lokasi Aktivitas</h5>
                        <p class="text-xs text-slate-500 font-mono mb-0" id="modalMapCoord">-</p>
                    </div>
                </div>
                <button type="button" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center transition-colors cursor-pointer shrink-0" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x text-lg"></i>
                </button>
            </div>
            <div class="p-0 bg-slate-100 relative">
                <div id="mapContainerModal" style="height: 420px; width: 100%; z-index: 1;"></div>
            </div>
            <div class="px-6 py-3.5 bg-white border-t border-slate-100 flex items-center justify-between">
                <a id="btnOpenGmaps" href="" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-sky-50 hover:bg-sky-100 text-sky-700 border border-sky-200 font-bold rounded-xl text-xs shadow-2xs transition active:scale-95 cursor-pointer">
                    <i class="ti ti-external-link text-sm"></i>
                    <span>Buka di Google Maps</span>
                </a>
                <button type="button" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition cursor-pointer" data-bs-dismiss="modal">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('myscript')
<script>
let modalLeafletMap = null;
let modalLeafletMarker = null;

function showImageModal(imageSrc, title) {
    document.getElementById('modalImageTitle').textContent = title || 'Foto Aktivitas';
    document.getElementById('previewImageEl').src = imageSrc;
    document.getElementById('btnDownloadImage').href = imageSrc;
    const modal = new bootstrap.Modal(document.getElementById('modalImagePreview'));
    modal.show();
}

function showMapModal(lokasi, title) {
    if (!lokasi) return;
    
    document.getElementById('modalMapTitle').textContent = title || 'Lokasi Aktivitas';
    document.getElementById('modalMapCoord').textContent = lokasi;
    document.getElementById('btnOpenGmaps').href = `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(lokasi)}`;
    
    const modalEl = document.getElementById('modalMapPreview');
    const modal = new bootstrap.Modal(modalEl);
    modal.show();

    const parts = lokasi.split(',');
    const lat = parseFloat(parts[0]);
    const lng = parseFloat(parts[1]);

    if (isNaN(lat) || isNaN(lng)) return;

    modalEl.addEventListener('shown.bs.modal', function () {
        if (modalLeafletMap) {
            modalLeafletMap.remove();
            modalLeafletMap = null;
        }

        modalLeafletMap = L.map('mapContainerModal').setView([lat, lng], 16);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(modalLeafletMap);

        modalLeafletMarker = L.marker([lat, lng]).addTo(modalLeafletMap)
            .bindPopup(`<strong class="text-xs">${title || 'Lokasi Aktivitas'}</strong><br><span class="text-[11px] text-slate-500">${lokasi}</span>`)
            .openPopup();
        
        setTimeout(() => {
            modalLeafletMap.invalidateSize();
        }, 150);
    }, { once: true });
}

$(document).ready(function() {
    // Inisialisasi Flatpickr Native
    if (typeof flatpickr !== 'undefined') {
        const elAwal = document.getElementById('tanggal_awal');
        const elAkhir = document.getElementById('tanggal_akhir');
        if (elAwal) {
            flatpickr(elAwal, {
                locale: 'id',
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'd F Y'
            });
        }
        if (elAkhir) {
            flatpickr(elAkhir, {
                locale: 'id',
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'd F Y'
            });
        }
    }

    // Inisialisasi Select2 Filter
    $('.select2NikAktivitas').select2({
        placeholder: 'Semua Karyawan',
        allowClear: true,
        width: '100%'
    });

    const modalAktivitasEl = document.getElementById('modalAktivitas');
    const modalAktivitas = new bootstrap.Modal(modalAktivitasEl);

    function showLoading() {
        $('#modalAktivitasBody').html(`
            <div class="py-12 text-center text-slate-400">
                <div style="width: 28px; height: 28px; border: 3px solid {{ $themeColor1 }}; border-top-color: transparent; border-radius: 50%; animation: spin 0.8s linear infinite; display: inline-block;"></div>
                <p class="text-xs font-semibold text-slate-500 mt-2">Memuat formulir...</p>
            </div>
        `);
    }

    // 1. Create Aktivitas
    $('#btnCreateAktivitas').click(function() {
        $('#modalAktivitasTitle').text('Tambah Catatan Aktivitas');
        $('#modalAktivitasSubtitle').text('Catat laporan kegiatan, foto dokumentasi, dan titik lokasi pengerjaan');
        showLoading();
        modalAktivitas.show();

        $('#modalAktivitasBody').load("{{ route('aktivitaskaryawan.create') }}");
    });

    // 2. Edit Aktivitas
    $('.btnEditAktivitas').click(function() {
        const id = $(this).data('id');
        $('#modalAktivitasTitle').text('Edit Catatan Aktivitas');
        $('#modalAktivitasSubtitle').text('Perbarui rincian kegiatan, foto dokumentasi, atau lokasi pengerjaan');
        showLoading();
        modalAktivitas.show();

        $('#modalAktivitasBody').load(`/aktivitaskaryawan/${id}/edit`);
    });

    // 3. Show Detail Aktivitas
    $('.btnShowAktivitas').click(function() {
        const id = $(this).data('id');
        $('#modalAktivitasTitle').text('Detail Aktivitas Karyawan');
        $('#modalAktivitasSubtitle').text('Rincian lengkap kegiatan harian, foto bukti, dan peta lokasi');
        showLoading();
        modalAktivitas.show();

        $('#modalAktivitasBody').load(`/aktivitaskaryawan/${id}`);
    });

    // 4. Delete Confirmation
    $('.formDelete').submit(function(e) {
        e.preventDefault();
        const form = this;
        Swal.fire({
            title: 'Hapus Data Aktivitas?',
            text: 'Catatan aktivitas ini beserta foto dokumentasi akan dihapus secara permanen.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            customClass: { popup: 'rounded-2xl shadow-xl' }
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>
@endpush
