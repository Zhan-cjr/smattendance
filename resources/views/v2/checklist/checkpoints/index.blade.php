@extends('v2.layouts.app')
@section('titlepage', 'Master Checkpoint Buka & Tutup Toko')
@section('page_title', 'Master Checkpoint Toko')

@section('content')
@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
@endphp

<div class="space-y-4">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-1">
        <div>
            <h1 class="text-lg sm:text-xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                <i class="ti ti-checkbox text-xl" style="color: {{ $themeColor1 }};"></i>
                <span>Daftar Checkpoint Opening & Closing</span>
            </h1>
            <p class="text-[11px] text-slate-500 mt-0.5">
                Kelola titik inspeksi fisik, area rawan penyusup, dan inventaris toko per departemen
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.checklist.monitoring.index') }}"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 text-xs font-bold shadow-2xs transition">
                <i class="ti ti-report-analytics text-sm text-emerald-600"></i>
                <span>Monitoring Laporan</span>
            </a>
            <button type="button" data-bs-toggle="modal" data-bs-target="#modalTambahCheckpoint"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-white text-xs font-bold shadow-2xs transition hover:opacity-95 cursor-pointer"
                    style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                <i class="ti ti-plus text-sm"></i>
                <span>Tambah Checkpoint</span>
            </button>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-4">
        <form action="{{ route('admin.checklist.checkpoints.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
            <div class="sm:col-span-5">
                <select name="tipe" onchange="this.form.submit()"
                        class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:border-emerald-600">
                    <option value="">Semua Tipe (Opening & Closing)</option>
                    <option value="opening" {{ request('tipe') == 'opening' ? 'selected' : '' }}>Khusus Opening</option>
                    <option value="closing" {{ request('tipe') == 'closing' ? 'selected' : '' }}>Khusus Closing</option>
                    <option value="both" {{ request('tipe') == 'both' ? 'selected' : '' }}>Keduanya (Opening & Closing)</option>
                </select>
            </div>
            <div class="sm:col-span-5">
                <select name="kode_dept" onchange="this.form.submit()"
                        class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:border-emerald-600">
                    <option value="">Semua Departemen / Berlaku Global</option>
                    @foreach($departemens as $dp)
                        <option value="{{ $dp->kode_dept }}" {{ request('kode_dept') == $dp->kode_dept ? 'selected' : '' }}>
                            {{ $dp->nama_dept }} ({{ $dp->kode_dept }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="sm:col-span-2">
                <a href="{{ route('admin.checklist.checkpoints.index') }}"
                   class="w-full h-10 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold flex items-center justify-center transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 text-slate-600 uppercase text-[11px] font-bold border-b border-slate-100">
                    <tr>
                        <th class="px-4 py-3 w-12">No</th>
                        <th class="px-4 py-3">Nama Checkpoint & Instruksi</th>
                        <th class="px-4 py-3 w-32">Tipe</th>
                        <th class="px-4 py-3 w-64">Berlaku Untuk Departemen</th>
                        <th class="px-4 py-3 w-20 text-center">Urutan</th>
                        <th class="px-4 py-3 w-24 text-center">Status</th>
                        <th class="px-4 py-3 w-28 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($checkpoints as $idx => $cp)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-4 py-3 font-semibold text-slate-500">{{ $idx + 1 }}</td>
                            <td class="px-4 py-3">
                                <div class="font-bold text-slate-800">{{ $cp->nama_checkpoint }}</div>
                                @if($cp->deskripsi)
                                    <div class="text-[11px] text-slate-500 mt-0.5">{{ $cp->deskripsi }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($cp->tipe == 'opening')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-600 text-white text-[11px] font-bold">
                                        <i class="ti ti-door-enter"></i>Opening
                                    </span>
                                @elseif($cp->tipe == 'closing')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-indigo-800 text-white text-[11px] font-bold">
                                        <i class="ti ti-lock"></i>Closing
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-teal-700 text-white text-[11px] font-bold">
                                        <i class="ti ti-arrows-exchange"></i>Keduanya
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($cp->all_dept)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200 text-[11px] font-bold">
                                        <i class="ti ti-world"></i>Semua Departemen
                                    </span>
                                @else
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($cp->departemens as $dp)
                                            <span class="px-2 py-0.5 rounded-md bg-sky-600 text-white text-[10.5px] font-bold">{{ $dp->nama_dept }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center font-mono font-bold text-slate-700">{{ $cp->urutan }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($cp->is_active)
                                    <span class="px-2.5 py-0.5 rounded-md bg-emerald-600 text-white text-[10.5px] font-bold">Aktif</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-md bg-rose-600 text-white text-[10.5px] font-bold">Non-Aktif</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    <button type="button" onclick="editCheckpoint({{ $cp->id }})"
                                            class="w-8 h-8 rounded-lg bg-amber-50 hover:bg-amber-500 text-amber-600 hover:text-white border border-amber-200 inline-flex items-center justify-center transition cursor-pointer"
                                            title="Edit Checkpoint">
                                        <i class="ti ti-edit text-sm"></i>
                                    </button>
                                    <form action="{{ route('admin.checklist.checkpoints.delete', $cp->id) }}" method="POST"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus checkpoint ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="w-8 h-8 rounded-lg bg-rose-50 hover:bg-rose-600 text-rose-600 hover:text-white border border-rose-200 inline-flex items-center justify-center transition cursor-pointer"
                                                title="Hapus">
                                            <i class="ti ti-trash text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-slate-400">
                                <i class="ti ti-mood-empty text-3xl block mb-1.5 opacity-50"></i>
                                <span class="text-xs font-semibold">Belum ada checkpoint untuk filter ini.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Checkpoint -->
<div class="modal fade" id="modalTambahCheckpoint" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.checklist.checkpoints.store') }}" method="POST">
                @csrf
                <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between">
                    <h5 class="text-sm font-bold text-slate-800">Tambah Checkpoint Baru</h5>
                    <button type="button" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center cursor-pointer" data-bs-dismiss="modal">
                        <i class="ti ti-x"></i>
                    </button>
                </div>
                <div class="p-4 sm:p-5 space-y-3.5 text-xs">
                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Nama Checkpoint <span class="text-rose-500">*</span></label>
                        <input type="text" name="nama_checkpoint" required placeholder="Contoh: Area Display & Gondola"
                               class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl font-semibold text-slate-800 focus:outline-none focus:border-emerald-600">
                    </div>

                    <div class="grid grid-cols-12 gap-3">
                        <div class="col-span-7">
                            <label class="font-bold text-slate-700 block mb-1">Tipe Sesi <span class="text-rose-500">*</span></label>
                            <select name="tipe" required
                                    class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl font-semibold text-slate-800 focus:outline-none focus:border-emerald-600">
                                <option value="closing">Closing (Tutup Toko / Anti-Maling)</option>
                                <option value="opening">Opening (Buka Toko)</option>
                                <option value="both">Keduanya (Opening & Closing)</option>
                            </select>
                        </div>
                        <div class="col-span-5">
                            <label class="font-bold text-slate-700 block mb-1">Nomor Urut</label>
                            <input type="number" name="urutan" value="0" min="0"
                                   class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl font-semibold text-slate-800 focus:outline-none focus:border-emerald-600">
                        </div>
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Instruksi Pemeriksaan Foto</label>
                        <textarea name="deskripsi" rows="2" placeholder="Contoh: Periksa kerapian display produk, lorong bersih, steril dari barang mencurigakan."
                                  class="w-full p-3 bg-white border border-slate-300 rounded-xl font-medium text-slate-800 focus:outline-none focus:border-emerald-600"></textarea>
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 block mb-1.5">Penerapan Departemen</label>
                        <label class="inline-flex items-center gap-2 cursor-pointer mb-2">
                            <input type="checkbox" name="all_dept" id="allDeptToggle" value="1" checked
                                   onchange="toggleDeptList(this, 'deptSelectionTambah')"
                                   class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                            <span class="font-semibold text-slate-700">Berlaku untuk SEMUA Departemen</span>
                        </label>
                        <div id="deptSelectionTambah" class="hidden border border-slate-200 rounded-xl p-3 space-y-1.5 max-h-40 overflow-y-auto bg-slate-50">
                            <span class="text-[11px] text-slate-500 block mb-1">Centang departemen yang wajib:</span>
                            @foreach($departemens as $dp)
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="dept_ids[]" value="{{ $dp->kode_dept }}" id="dpTambah_{{ $dp->kode_dept }}"
                                           class="rounded border-slate-300 text-emerald-600">
                                    <span class="text-slate-700 font-medium">{{ $dp->nama_dept }} ({{ $dp->kode_dept }})</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="px-5 py-3.5 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" class="px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-600 text-xs font-bold cursor-pointer" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl text-white text-xs font-bold shadow-2xs cursor-pointer"
                            style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-check mr-1"></i>Simpan Checkpoint
                    </button>
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
                <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between">
                    <h5 class="text-sm font-bold text-slate-800">Edit Checkpoint</h5>
                    <button type="button" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center cursor-pointer" data-bs-dismiss="modal">
                        <i class="ti ti-x"></i>
                    </button>
                </div>
                <div class="p-4 sm:p-5 space-y-3.5 text-xs">
                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Nama Checkpoint <span class="text-rose-500">*</span></label>
                        <input type="text" name="nama_checkpoint" id="editNama" required
                               class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl font-semibold text-slate-800 focus:outline-none focus:border-emerald-600">
                    </div>

                    <div class="grid grid-cols-12 gap-3">
                        <div class="col-span-7">
                            <label class="font-bold text-slate-700 block mb-1">Tipe Sesi <span class="text-rose-500">*</span></label>
                            <select name="tipe" id="editTipe" required
                                    class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl font-semibold text-slate-800 focus:outline-none focus:border-emerald-600">
                                <option value="closing">Closing (Tutup Toko)</option>
                                <option value="opening">Opening (Buka Toko)</option>
                                <option value="both">Keduanya (Opening & Closing)</option>
                            </select>
                        </div>
                        <div class="col-span-5">
                            <label class="font-bold text-slate-700 block mb-1">Nomor Urut</label>
                            <input type="number" name="urutan" id="editUrutan" min="0"
                                   class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl font-semibold text-slate-800 focus:outline-none focus:border-emerald-600">
                        </div>
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Instruksi Pemeriksaan Foto</label>
                        <textarea name="deskripsi" id="editDeskripsi" rows="2"
                                  class="w-full p-3 bg-white border border-slate-300 rounded-xl font-medium text-slate-800 focus:outline-none focus:border-emerald-600"></textarea>
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Status Keaktifan</label>
                        <select name="is_active" id="editIsActive"
                                class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl font-semibold text-slate-800 focus:outline-none focus:border-emerald-600">
                            <option value="1">Aktif</option>
                            <option value="0">Non-Aktif (Arsip)</option>
                        </select>
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 block mb-1.5">Penerapan Departemen</label>
                        <label class="inline-flex items-center gap-2 cursor-pointer mb-2">
                            <input type="checkbox" name="all_dept" id="editAllDeptToggle" value="1"
                                   onchange="toggleDeptList(this, 'deptSelectionEdit')"
                                   class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                            <span class="font-semibold text-slate-700">Berlaku untuk SEMUA Departemen</span>
                        </label>
                        <div id="deptSelectionEdit" class="hidden border border-slate-200 rounded-xl p-3 space-y-1.5 max-h-40 overflow-y-auto bg-slate-50">
                            <span class="text-[11px] text-slate-500 block mb-1">Centang departemen yang wajib:</span>
                            @foreach($departemens as $dp)
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="dept_ids[]" value="{{ $dp->kode_dept }}" id="dpEdit_{{ $dp->kode_dept }}"
                                           class="check-edit-dept rounded border-slate-300 text-emerald-600">
                                    <span class="text-slate-700 font-medium">{{ $dp->nama_dept }} ({{ $dp->kode_dept }})</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="px-5 py-3.5 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" class="px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-600 text-xs font-bold cursor-pointer" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl text-white text-xs font-bold shadow-2xs cursor-pointer"
                            style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-check mr-1"></i>Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('myscript')
<script>
    function toggleDeptList(el, containerId) {
        const container = document.getElementById(containerId);
        if (el.checked) {
            container.classList.add('hidden');
        } else {
            container.classList.remove('hidden');
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

                const allToggle = document.getElementById('editAllDeptToggle');
                allToggle.checked = cp.all_dept;
                toggleDeptList(allToggle, 'deptSelectionEdit');

                document.querySelectorAll('.check-edit-dept').forEach(ch => {
                    ch.checked = data.selected_depts ? data.selected_depts.includes(ch.value) : false;
                });

                new bootstrap.Modal(document.getElementById('modalEditCheckpoint')).show();
            });
    }
</script>
@endpush

