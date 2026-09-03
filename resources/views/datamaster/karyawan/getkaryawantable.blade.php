<div class="card border-0 shadow-none">
    <!-- Unified Search Header -->
    <div class="card-header border-0 pb-3" style="background-color: var(--theme-color-1) !important; color: white !important; border-radius: 8px 8px 0 0;">
        <div class="row align-items-center">
            <div class="col-md-6 mb-2 mb-md-0 text-start">
                <small class="d-block opacity-75 text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 1px;">PILIH KARYAWAN</small>
                <h5 class="mb-0 text-white fw-bold">Cari Data Karyawan</h5>
            </div>
            <div class="col-md-6">
                <div class="input-group input-group-merge shadow-sm">
                    <span class="input-group-text border-0 bg-white"><i class="ti ti-search text-primary"></i></span>
                    <input type="text" id="searchKaryawan" class="form-control border-0" placeholder="Ketik Nama atau NIK..." autocomplete="off" value="{{ request('q') }}">
                </div>
            </div>
        </div>
    </div>

    <!-- Unified Table -->
    <div class="table-responsive" style="max-height: 450px; overflow-y: auto;">
        <table class="table table-hover mb-0 text-nowrap align-middle" id="tableKaryawanLookup">
            <thead class="sticky-top" style="background-color: var(--theme-color-1) !important; color: white !important; top: -1px; z-index: 10;">
                <tr>
                    <th class="text-white border-0 py-2 ps-4" style="font-size: 0.7rem; letter-spacing: 0.5px;" width="50">FOTO</th>
                    <th class="text-white border-0 py-2" style="font-size: 0.7rem; letter-spacing: 0.5px;" width="100">NIK</th>
                    <th class="text-white border-0 py-2" style="font-size: 0.7rem; letter-spacing: 0.5px;">NAMA KARYAWAN</th>
                    <th class="text-white border-0 py-2" style="font-size: 0.7rem; letter-spacing: 0.5px;">JABATAN</th>
                    <th class="text-white border-0 py-2" style="font-size: 0.7rem; letter-spacing: 0.5px;">DEPARTEMEN</th>
                    <th class="text-white border-0 py-2" style="font-size: 0.7rem; letter-spacing: 0.5px;">CABANG</th>
                    <th class="text-white border-0 py-2 text-center pe-4" style="font-size: 0.7rem; letter-spacing: 0.5px;" width="50">PILIH</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @forelse($karyawan as $d)
                @php
                    $path = Storage::url('karyawan/'.$d->foto);
                    // Get Initials
                    $words = explode(" ", $d->nama_karyawan);
                    $initials = "";
                    foreach ($words as $w) {
                        if(isset($w[0])) $initials .= $w[0];
                    }
                    $initials = strtoupper(substr($initials, 0, 2));
                    
                    // Assign random background color for initials
                    $colors = ['bg-label-primary', 'bg-label-success', 'bg-label-info', 'bg-label-warning', 'bg-label-danger'];
                    $randomColor = $colors[array_rand($colors)];
                @endphp
                <tr>
                    <td class="py-2 ps-4 text-center">
                        @if (empty($d->foto) || !Storage::disk('public')->exists('karyawan/'.$d->foto))
                            <div class="avatar avatar-sm">
                                 <span class="avatar-initial rounded-circle {{ $randomColor }}" style="font-size: 0.6rem;">{{ $initials }}</span>
                            </div>
                        @else
                            <div class="avatar avatar-sm">
                                <img src="{{ $path }}" class="rounded-circle" style="object-fit: cover;">
                            </div>
                        @endif
                    </td>
                    <td class="py-2">
                        <span class="fw-bold text-body" style="font-size: 0.85rem;">{{ $d->nik }}</span>
                    </td>
                    <td class="py-2">
                         <span class="fw-bold text-dark d-block" style="font-size: 0.85rem;">{{ $d->nama_karyawan }}</span>
                    </td>
                    <td class="py-2">
                        <small class="text-muted" style="font-size: 0.8rem;">{{ $d->jabatan->nama_jabatan ?? '-' }}</small>
                    </td>
                    <td class="py-2">
                        <small class="text-muted" style="font-size: 0.8rem;">{{ $d->departemen->nama_dept ?? '-' }}</small>
                    </td>
                    <td class="py-2">
                        <div class="d-flex align-items-center gap-1 text-muted">
                            <i class="ti ti-map-pin ti-xxs"></i>
                            <span style="font-size: 0.8rem;">{{ $d->cabang->nama_cabang ?? '-' }}</span>
                        </div>
                    </td>
                    <td class="py-2 text-center pe-4">
                        <a href="javascript:void(0)" class="btnPilihKaryawan btn btn-sm btn-icon btn-label-primary rounded-circle" 
                            data-nik="{{ $d->nik }}" 
                            data-nama="{{ $d->nama_karyawan }}"
                            title="Pilih Karyawan">
                            <i class="ti ti-check fs-5"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5">
                        <div class="d-flex flex-column align-items-center opacity-50">
                            <i class="ti ti-user-x fs-1 mb-2"></i>
                            <span class="fw-bold">Data Karyawan tidak ditemukan</span>
                            <small>Coba kata kunci pencarian lain</small>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Info Footer -->
    <div class="card-footer border-0 py-2 text-center" style="background-color: var(--theme-color-1) !important; color: white !important; border-top: 1px solid rgba(255,255,255,0.1) !important;">
        <small class="text-white opacity-75" style="font-size: 0.65rem;">Menampilkan maksimal 20 hasil pencarian terbaru</small>
    </div>
</div>

<style>
    .avatar-sm {
        width: 32px !important;
        height: 32px !important;
    }
</style>

<script>
    $(function() {
        var typingTimer;
        var doneTypingInterval = 300; // Debounce 300ms

        $("#searchKaryawan").on('keyup', function() {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(performSearch, doneTypingInterval);
        });

        // Focus search on modal open
        setTimeout(function() {
            $("#searchKaryawan").focus();
        }, 500);

        function performSearch() {
            var q = $("#searchKaryawan").val();
            // Show subtle loading state if you want, but AJAX is generally fast enough
            $("#loadLookupKaryawan").load("{{ route('karyawan.getkaryawantable') }}?q=" + encodeURIComponent(q));
        }

        // Maintain focus after search refresh
        var val = $("#searchKaryawan").val();
        $("#searchKaryawan").val('').focus().val(val);
    });
</script>
