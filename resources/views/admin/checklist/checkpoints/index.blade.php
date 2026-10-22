@extends('layouts.app')
@section('titlepage', 'Master Checkpoint Buka & Tutup Toko')

@section('content')
@section('navigasi')
    <span>Master Checkpoint Toko</span>
@endsection

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header d-flex justify-content-between align-items-center pb-2">
                <div>
                    <h5 class="mb-0 fw-bold"><i class="ti ti-checkbox me-2 text-primary"></i>Daftar Checkpoint Opening & Closing</h5>
                    <small class="text-muted">Kelola titik inspeksi fisik, area rawan penyusup, dan inventaris toko per cabang</small>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.checklist.monitoring.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-report-analytics me-1"></i>Monitoring Laporan
                    </a>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahCheckpoint">
                        <i class="ti ti-plus me-1"></i>Tambah Checkpoint
                    </button>
                </div>
            </div>

            <div class="card-body">
                <!-- Filter Section -->
                <form action="{{ route('admin.checklist.checkpoints.index') }}" method="GET" class="mb-3">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <select name="tipe" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="">Semua Tipe (Opening & Closing)</option>
                                <option value="opening" {{ request('tipe') == 'opening' ? 'selected' : '' }}>Khusus Opening</option>
                                <option value="closing" {{ request('tipe') == 'closing' ? 'selected' : '' }}>Khusus Closing</option>
                                <option value="both" {{ request('tipe') == 'both' ? 'selected' : '' }}>Keduanya (Opening & Closing)</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select name="kode_cabang" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="">Semua Cabang / Berlaku Global</option>
                                @foreach($cabangs as $cb)
                                    <option value="{{ $cb->kode_cabang }}" {{ request('kode_cabang') == $cb->kode_cabang ? 'selected' : '' }}>
                                        {{ $cb->nama_cabang }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <a href="{{ route('admin.checklist.checkpoints.index') }}" class="btn btn-sm btn-outline-secondary w-100">Reset</a>
                        </div>
                    </div>
                </form>

                <!-- Checkpoints Table -->
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th>Nama Checkpoint & Instruksi</th>
                                <th style="width: 120px;">Tipe</th>
                                <th style="width: 220px;">Berlaku Untuk Cabang</th>
                                <th style="width: 90px;" class="text-center">Urutan</th>
                                <th style="width: 90px;" class="text-center">Status</th>
                                <th style="width: 120px;" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($checkpoints as $idx => $cp)
                                <tr>
                                    <td>{{ $idx + 1 }}</td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $cp->nama_checkpoint }}</div>
                                        @if($cp->deskripsi)
                                            <small class="text-muted d-block mt-0.5">{{ $cp->deskripsi }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($cp->tipe == 'opening')
                                            <span class="badge text-white px-2.5 py-1.5 fw-bold" style="background-color: #d97706;"><i class="ti ti-door-enter me-1"></i>Opening</span>
                                        @elseif($cp->tipe == 'closing')
                                            <span class="badge text-white px-2.5 py-1.5 fw-bold" style="background-color: #3730a3;"><i class="ti ti-lock me-1"></i>Closing</span>
                                        @else
                                            <span class="badge text-white px-2.5 py-1.5 fw-bold" style="background-color: #0f766e;"><i class="ti ti-arrows-exchange me-1"></i>Keduanya</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($cp->all_cabang)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle">
                                                <i class="ti ti-world me-1"></i>Semua Cabang
                                            </span>
                                        @else
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach($cp->cabangs as $cab)
                                                    <span class="badge bg-secondary">{{ $cab->nama_cabang }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </td>
                                    <td class="text-center font-monospace">{{ $cp->urutan }}</td>
                                    <td class="text-center">
                                        @if($cp->is_active)
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-danger">Non-Aktif</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="d-inline-flex gap-1">
                                            <button type="button" class="btn btn-sm btn-icon btn-outline-warning" onclick="editCheckpoint({{ $cp->id }})" title="Edit Checkpoint">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            <form action="{{ route('admin.checklist.checkpoints.delete', $cp->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus checkpoint ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-icon btn-outline-danger" title="Hapus">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="ti ti-mood-empty fs-2 d-block mb-1"></i>
                                        Belum ada checkpoint untuk filter ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Checkpoint -->
<div class="modal fade" id="modalTambahCheckpoint" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.checklist.checkpoints.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Tambah Checkpoint Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Checkpoint <span class="text-danger">*</span></label>
                        <input type="text" name="nama_checkpoint" class="form-control" placeholder="Contoh: Toilet & Kamar Mandi" required>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-7">
                            <label class="form-label fw-bold">Tipe Sesi <span class="text-danger">*</span></label>
                            <select name="tipe" class="form-select" required>
                                <option value="closing">Closing (Tutup Toko / Anti-Maling)</option>
                                <option value="opening">Opening (Buka Toko)</option>
                                <option value="both">Keduanya (Opening & Closing)</option>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-bold">Nomor Urut</label>
                            <input type="number" name="urutan" class="form-control" value="0" min="0">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Instruksi Pemeriksaan Foto</label>
                        <textarea name="deskripsi" class="form-control" rows="2" placeholder="Contoh: Periksa belakang pintu, kolong meja, pastikan steril dari penyusup lalu kunci."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Penerapan Cabang</label>
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="all_cabang" id="allCabangToggle" value="1" checked onchange="toggleCabangList(this, 'cabangSelectionTambah')">
                            <label class="form-check-label fw-semibold" for="allCabangToggle">Berlaku untuk SEMUA Cabang</label>
                        </div>
                        <div id="cabangSelectionTambah" class="d-none border rounded p-2" style="max-height: 150px; overflow-y: auto;">
                            <small class="text-muted d-block mb-1">Centang cabang yang wajib:</small>
                            @foreach($cabangs as $cb)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="cabang_ids[]" value="{{ $cb->kode_cabang }}" id="cbTambah_{{ $cb->kode_cabang }}">
                                    <label class="form-check-label" for="cbTambah_{{ $cb->kode_cabang }}">
                                        {{ $cb->nama_cabang }} ({{ $cb->kode_cabang }})
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="ti ti-check me-1"></i>Simpan Checkpoint</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Checkpoint -->
<div class="modal fade" id="modalEditCheckpoint" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formEditCheckpoint" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Edit Checkpoint</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Checkpoint <span class="text-danger">*</span></label>
                        <input type="text" name="nama_checkpoint" id="editNama" class="form-control" required>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-7">
                            <label class="form-label fw-bold">Tipe Sesi <span class="text-danger">*</span></label>
                            <select name="tipe" id="editTipe" class="form-select" required>
                                <option value="closing">Closing (Tutup Toko)</option>
                                <option value="opening">Opening (Buka Toko)</option>
                                <option value="both">Keduanya (Opening & Closing)</option>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-bold">Nomor Urut</label>
                            <input type="number" name="urutan" id="editUrutan" class="form-control" min="0">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Instruksi Pemeriksaan Foto</label>
                        <textarea name="deskripsi" id="editDeskripsi" class="form-control" rows="2"></textarea>
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold">Status Keaktifan</label>
                            <select name="is_active" id="editIsActive" class="form-select">
                                <option value="1">Aktif</option>
                                <option value="0">Non-Aktif (Arsip)</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Penerapan Cabang</label>
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="all_cabang" id="editAllCabangToggle" value="1" onchange="toggleCabangList(this, 'cabangSelectionEdit')">
                            <label class="form-check-label fw-semibold" for="editAllCabangToggle">Berlaku untuk SEMUA Cabang</label>
                        </div>
                        <div id="cabangSelectionEdit" class="d-none border rounded p-2" style="max-height: 150px; overflow-y: auto;">
                            <small class="text-muted d-block mb-1">Centang cabang yang wajib:</small>
                            @foreach($cabangs as $cb)
                                <div class="form-check">
                                    <input class="form-check-input check-edit-cabang" type="checkbox" name="cabang_ids[]" value="{{ $cb->kode_cabang }}" id="cbEdit_{{ $cb->kode_cabang }}">
                                    <label class="form-check-label" for="cbEdit_{{ $cb->kode_cabang }}">
                                        {{ $cb->nama_cabang }} ({{ $cb->kode_cabang }})
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="ti ti-check me-1"></i>Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('myscript')
<script>
    function toggleCabangList(el, containerId) {
        const container = document.getElementById(containerId);
        if (el.checked) {
            container.classList.add('d-none');
        } else {
            container.classList.remove('d-none');
        }
    }

    function editCheckpoint(id) {
        fetch(`/admin/checklist/checkpoints/${id}/edit`)
            .then(res => res.json())
            .then(data => {
                const cp = data.checkpoint;
                document.getElementById('formEditCheckpoint').action = `/admin/checklist/checkpoints/${id}`;
                document.getElementById('editNama').value = cp.nama_checkpoint;
                document.getElementById('editTipe').value = cp.tipe;
                document.getElementById('editUrutan').value = cp.urutan;
                document.getElementById('editDeskripsi').value = cp.deskripsi || '';
                document.getElementById('editIsActive').value = cp.is_active ? 1 : 0;

                const allToggle = document.getElementById('editAllCabangToggle');
                allToggle.checked = cp.all_cabang;
                toggleCabangList(allToggle, 'cabangSelectionEdit');

                // Check branches
                document.querySelectorAll('.check-edit-cabang').forEach(ch => {
                    ch.checked = data.selected_cabangs.includes(ch.value);
                });

                new bootstrap.Modal(document.getElementById('modalEditCheckpoint')).show();
            });
    }
</script>
@endpush
