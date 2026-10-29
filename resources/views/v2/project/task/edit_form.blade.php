@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
@endphp

<form action="{{ route('project.task.update', Crypt::encrypt($task->id)) }}" method="POST" id="formEditTaskV2" class="space-y-5" novalidate>
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
        
        <!-- Left Section: Detail Spesifikasi & Timeline Tugas (7 Cols) -->
        <div class="lg:col-span-7 space-y-4">
            
            <!-- Section Header -->
            <div class="flex items-center gap-2.5 pb-2.5 border-b border-slate-100">
                <div class="w-8 h-8 rounded-xl flex items-center justify-center text-white text-xs font-bold shadow-2xs shrink-0"
                     style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                    <i class="ti ti-checkbox text-sm"></i>
                </div>
                <div>
                    <h3 class="text-xs font-bold text-slate-800">Detail & Rincian Tugas ({{ $task->kode_task }})</h3>
                    <p class="text-[11px] text-slate-400">Judul pekerjaan, deskripsi, dan hierarki sub-task</p>
                </div>
            </div>

            <!-- Judul Task -->
            <div class="space-y-1">
                <label for="edit_task_judul" class="block text-xs font-bold text-slate-700">
                    Judul Task <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="ti ti-edit text-sm"></i>
                    </span>
                    <input type="text" name="judul" id="edit_task_judul" value="{{ old('judul', $task->judul) }}" required
                           placeholder="Contoh: Implementasi Desain Layout Dashboard"
                           class="w-full pl-10 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:border-transparent transition-all font-semibold"
                           style="--tw-ring-color: {{ $themeColor1 }};">
                </div>
                <span class="text-[11px] text-rose-500 font-medium hidden error-feedback mt-1 block" id="err_edit_task_judul"></span>
            </div>

            <!-- Sub-task Dari (Parent Task) -->
            <div class="space-y-1">
                <label for="edit_task_parent_id" class="block text-xs font-bold text-slate-700">
                    Sub-task Dari (Parent Task)
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="ti ti-git-branch text-sm"></i>
                    </span>
                    <select name="parent_id" id="edit_task_parent_id"
                            class="w-full pl-10 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:outline-none focus:ring-2 focus:border-transparent transition-all font-medium cursor-pointer"
                            style="--tw-ring-color: {{ $themeColor1 }};">
                        <option value="">-- Tanpa Parent (Tugas Utama) --</option>
                        @foreach ($parentTasks as $parent)
                            @if($parent->id !== $task->id)
                                <option value="{{ $parent->id }}" {{ old('parent_id', $task->parent_id) == $parent->id ? 'selected' : '' }}>
                                    {{ $parent->kode_task }} - {{ $parent->judul }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <p class="text-[10.5px] text-slate-400">Pilih jika task ini merupakan turunan dari tugas utama.</p>
            </div>

            <!-- Tanggal Mulai & Target Deadline (Flatpickr Datepicker) -->
            <div class="p-3.5 rounded-2xl bg-slate-50/90 border border-slate-200/80 space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-700 flex items-center gap-1.5">
                        <i class="ti ti-calendar-event text-emerald-600"></i> Rentang Waktu Pengerjaan
                    </span>
                    <span class="text-[10px] text-slate-400">Jadwal mulai & batas akhir tugas</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label for="edit_task_start_date" class="block text-[11px] font-bold text-slate-600">
                            Tanggal Mulai
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i class="ti ti-calendar text-sm"></i>
                            </span>
                            <input type="text" name="start_date" id="edit_task_start_date" 
                                   value="{{ old('start_date', $task->start_date ? $task->start_date->format('Y-m-d') : '') }}"
                                   placeholder="Pilih tanggal mulai"
                                   autocomplete="off"
                                   class="w-full pl-10 pr-9 py-2.5 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 font-semibold focus:outline-none focus:ring-2 focus:border-transparent transition-all cursor-pointer flatpickr-task-edit-date"
                                   style="--tw-ring-color: {{ $themeColor1 }};">
                            <span class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                                <i class="ti ti-chevron-down text-xs"></i>
                            </span>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label for="edit_task_due_date" class="block text-[11px] font-bold text-slate-600">
                            Target Deadline
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i class="ti ti-calendar-due text-sm"></i>
                            </span>
                            <input type="text" name="due_date" id="edit_task_due_date" 
                                   value="{{ old('due_date', $task->due_date ? $task->due_date->format('Y-m-d') : '') }}"
                                   placeholder="Pilih target deadline"
                                   autocomplete="off"
                                   class="w-full pl-10 pr-9 py-2.5 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 font-semibold focus:outline-none focus:ring-2 focus:border-transparent transition-all cursor-pointer flatpickr-task-edit-date"
                                   style="--tw-ring-color: {{ $themeColor1 }};">
                            <span class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                                <i class="ti ti-chevron-down text-xs"></i>
                            </span>
                        </div>
                        <span class="text-[11px] text-rose-500 font-medium hidden error-feedback mt-1 block" id="err_edit_task_due_date"></span>
                    </div>
                </div>
            </div>

            <!-- Deskripsi Tugas -->
            <div class="space-y-1">
                <label for="edit_task_deskripsi" class="block text-xs font-bold text-slate-700">Deskripsi & Instruksi Tugas</label>
                <textarea name="deskripsi" id="edit_task_deskripsi" rows="2"
                          placeholder="Masukkan detail instruksi, kriteria penerimaan, atau keterangan pengerjaan..."
                          class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:border-transparent transition-all font-medium leading-relaxed"
                          style="--tw-ring-color: {{ $themeColor1 }};">{{ old('deskripsi', $task->deskripsi) }}</textarea>
            </div>
        </div>

        <!-- Right Section: Status, Progres & Penugasan Personil (5 Cols) -->
        <div class="lg:col-span-5 space-y-4">
            
            <!-- Section Header -->
            <div class="flex items-center gap-2.5 pb-2.5 border-b border-slate-100">
                <div class="w-8 h-8 rounded-xl flex items-center justify-center text-white text-xs font-bold shadow-2xs shrink-0"
                     style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                    <i class="ti ti-users text-sm"></i>
                </div>
                <div>
                    <h3 class="text-xs font-bold text-slate-800">Status & Delegasi Tim</h3>
                    <p class="text-[11px] text-slate-400">Prioritas, status pelaksanaan, dan personil pelaksana</p>
                </div>
            </div>

            <!-- Prioritas & Status -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div class="space-y-1">
                    <label for="edit_task_prioritas" class="block text-xs font-bold text-slate-700">
                        Prioritas <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="ti ti-flag text-sm"></i>
                        </span>
                        <select name="prioritas" id="edit_task_prioritas" required
                                class="w-full pl-10 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:outline-none focus:ring-2 focus:border-transparent transition-all font-semibold cursor-pointer"
                                style="--tw-ring-color: {{ $themeColor1 }};">
                            <option value="low" {{ old('prioritas', $task->prioritas) == 'low' ? 'selected' : '' }}>Rendah (Low)</option>
                            <option value="medium" {{ old('prioritas', $task->prioritas) == 'medium' ? 'selected' : '' }}>Sedang (Medium)</option>
                            <option value="high" {{ old('prioritas', $task->prioritas) == 'high' ? 'selected' : '' }}>Tinggi (High)</option>
                            <option value="critical" {{ old('prioritas', $task->prioritas) == 'critical' ? 'selected' : '' }}>Kritis (Critical)</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-1">
                    <label for="edit_task_status" class="block text-xs font-bold text-slate-700">
                        Status <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="ti ti-progress-check text-sm"></i>
                        </span>
                        <select name="status" id="edit_task_status" required
                                class="w-full pl-10 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:outline-none focus:ring-2 focus:border-transparent transition-all font-semibold cursor-pointer"
                                style="--tw-ring-color: {{ $themeColor1 }};">
                            <option value="todo" {{ old('status', $task->status) == 'todo' ? 'selected' : '' }}>To Do</option>
                            <option value="in_progress" {{ old('status', $task->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="review" {{ old('status', $task->status) == 'review' ? 'selected' : '' }}>Review</option>
                            <option value="completed" {{ old('status', $task->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ old('status', $task->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Progres (%) & Urutan -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div class="space-y-1">
                    <label for="edit_task_progress" class="block text-xs font-bold text-slate-700">Capaian Progres (%)</label>
                    <div class="relative">
                        <input type="number" name="progress" id="edit_task_progress" min="0" max="100" value="{{ old('progress', $task->progress) }}" required
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:outline-none focus:ring-2 focus:border-transparent transition-all font-semibold"
                               style="--tw-ring-color: {{ $themeColor1 }};">
                        <span class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-xs font-bold text-slate-400">
                            %
                        </span>
                    </div>
                </div>

                <div class="space-y-1">
                    <label for="edit_task_urutan" class="block text-xs font-bold text-slate-700">Urutan Tampil</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="ti ti-arrows-sort text-sm"></i>
                        </span>
                        <input type="number" name="urutan" id="edit_task_urutan" min="0" value="{{ old('urutan', $task->urutan) }}"
                               class="w-full pl-10 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:outline-none focus:ring-2 focus:border-transparent transition-all font-medium"
                               style="--tw-ring-color: {{ $themeColor1 }};">
                    </div>
                </div>
            </div>

            <!-- Ditugaskan Kepada (Searchable Member Checklist) -->
            <div class="space-y-1">
                <div class="flex items-center justify-between">
                    <label class="block text-xs font-bold text-slate-700">
                        Ditugaskan Kepada (Anggota Tim)
                    </label>
                    <div class="flex items-center gap-2">
                        <button type="button" id="btnSelectAllEditTaskMembers" class="text-[10px] font-bold text-emerald-700 hover:underline cursor-pointer">
                            Pilih Semua
                        </button>
                        <span class="text-slate-300 text-[10px]">|</span>
                        <button type="button" id="btnDeselectAllEditTaskMembers" class="text-[10px] font-bold text-slate-500 hover:text-rose-600 cursor-pointer">
                            Reset
                        </button>
                    </div>
                </div>

                <!-- Live Search for Task Members -->
                <div class="relative mb-2">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i class="ti ti-search text-xs"></i>
                    </span>
                    <input type="text" id="searchEditTaskMemberInput" placeholder="Cari personil tugas..."
                           class="w-full pl-8 pr-3 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-1 focus:border-transparent transition-all"
                           style="--tw-ring-color: {{ $themeColor1 }};">
                </div>

                <div class="border border-slate-200 rounded-xl bg-slate-50/70 p-2 max-h-40 overflow-y-auto space-y-1.5" id="editTaskMemberChecklistContainer">
                    @forelse ($project->members as $member)
                        @if($member->karyawan)
                            <label class="edit-task-member-item flex items-center gap-2.5 p-2 rounded-lg bg-white border border-slate-200/80 hover:bg-slate-50 transition cursor-pointer text-xs select-none"
                                   data-search="{{ strtolower($member->karyawan->nama_karyawan . ' ' . $member->nik . ' ' . ($member->karyawan->nik_show ?? '') . ' ' . ($member->karyawan->jabatan ? $member->karyawan->jabatan->nama_jabatan : '')) }}">
                                <input type="checkbox" name="members[]" value="{{ $member->nik }}"
                                       {{ in_array($member->nik, old('members', $assignedNiks)) ? 'checked' : '' }}
                                       class="edit-task-member-checkbox rounded text-emerald-600 focus:ring-emerald-500 w-4 h-4 border-slate-300">
                                <div class="min-w-0 flex-1">
                                    <span class="font-bold text-slate-800 block truncate">{{ $member->karyawan->nama_karyawan }}</span>
                                    <span class="text-[10px] text-slate-400 block truncate">{{ !empty($member->karyawan->nik_show) ? $member->karyawan->nik_show : $member->nik }} • {{ $member->karyawan->jabatan ? $member->karyawan->jabatan->nama_jabatan : 'Staf' }}</span>
                                </div>
                            </label>
                        @endif
                    @empty
                        <div class="text-xs text-slate-400 text-center py-3">Belum ada anggota tim dalam project ini.</div>
                    @endforelse
                    <div id="noEditTaskMembersListFound" class="hidden text-xs text-slate-400 text-center py-3">
                        Tidak ada anggota yang cocok dengan pencarian
                    </div>
                </div>
                <p class="text-[10.5px] text-slate-400">Centang anggota tim yang ditugaskan mengerjakan tugas ini.</p>
            </div>
        </div>

    </div>

    <!-- Modal Footer Actions -->
    <div class="flex items-center justify-between gap-2.5 pt-4 border-t border-slate-100">
        <div>
            @can('project.task.delete')
                <button type="button" id="btnDeleteTaskV2"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-rose-200/80 bg-rose-50/50 text-rose-600 hover:bg-rose-100 text-xs font-bold transition shadow-2xs cursor-pointer">
                    <i class="ti ti-trash text-sm"></i>
                    <span>Hapus Task</span>
                </button>
            @endcan
        </div>

        <div class="flex items-center gap-2">
            <button type="button" class="btnCloseModalTask px-5 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 text-xs font-bold transition shadow-2xs cursor-pointer">
                Batal
            </button>
            <button type="submit" id="btnSubmitEditTaskV2"
                    class="inline-flex items-center gap-2 px-6 py-2.5 text-white font-bold rounded-xl text-xs shadow-xs transition-all duration-200 active:scale-95 cursor-pointer hover:opacity-95"
                    style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                <i class="ti ti-device-floppy text-sm"></i>
                <span>Simpan Perubahan</span>
            </button>
        </div>
    </div>
</form>

<form id="formDeleteTaskV2" action="{{ route('project.task.delete', Crypt::encrypt($task->id)) }}" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

<script>
$(document).ready(function() {
    // 1. Inisialisasi Flatpickr Tanggal Task (Edit)
    if (typeof flatpickr !== 'undefined') {
        const fpEditTaskStart = flatpickr("#edit_task_start_date", {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "d M Y",
            allowInput: false,
            locale: (typeof flatpickr.l10ns !== 'undefined' && flatpickr.l10ns.id) ? flatpickr.l10ns.id : 'default',
            onChange: function(selectedDates, dateStr) {
                if (fpEditTaskDue && selectedDates[0]) {
                    fpEditTaskDue.set('minDate', dateStr);
                }
                validateEditTaskField('#edit_task_start_date');
            }
        });

        const fpEditTaskDue = flatpickr("#edit_task_due_date", {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "d M Y",
            allowInput: false,
            minDate: $("#edit_task_start_date").val() || null,
            locale: (typeof flatpickr.l10ns !== 'undefined' && flatpickr.l10ns.id) ? flatpickr.l10ns.id : 'default',
            onChange: function() {
                validateEditTaskField('#edit_task_due_date');
            }
        });
    }

    // 2. Status & Progress Sync
    $('#edit_task_status').on('change', function() {
        if ($(this).val() === 'completed') {
            $('#edit_task_progress').val(100);
        } else if ($(this).val() === 'todo' && $('#edit_task_progress').val() == 100) {
            $('#edit_task_progress').val(0);
        }
    });

    // 3. Live Search Task Members (Edit)
    $('#searchEditTaskMemberInput').on('input', function() {
        const q = $(this).val().toLowerCase().trim();
        let matchCount = 0;

        $('#editTaskMemberChecklistContainer .edit-task-member-item').each(function() {
            const search = $(this).data('search') || $(this).text().toLowerCase();
            if (!q || search.includes(q)) {
                $(this).removeClass('hidden').show();
                matchCount++;
            } else {
                $(this).addClass('hidden').hide();
            }
        });

        if (matchCount === 0 && q) {
            $('#noEditTaskMembersListFound').removeClass('hidden');
        } else {
            $('#noEditTaskMembersListFound').addClass('hidden');
        }
    });

    // 4. Select All / Deselect All Task Members (Edit)
    $('#btnSelectAllEditTaskMembers').on('click', function() {
        $('#editTaskMemberChecklistContainer .edit-task-member-item:visible .edit-task-member-checkbox').prop('checked', true);
    });

    $('#btnDeselectAllEditTaskMembers').on('click', function() {
        $('#editTaskMemberChecklistContainer .edit-task-member-checkbox').prop('checked', false);
    });

    // 5. Delete Confirmation
    $('#btnDeleteTaskV2').on('click', function() {
        Swal.fire({
            title: 'Hapus Tugas Ini?',
            text: 'Tugas beserta lampiran dan komentar di dalamnya akan dihapus permanen!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            customClass: {
                popup: 'rounded-2xl shadow-xl border border-slate-100',
                confirmButton: 'px-4 py-2 rounded-xl text-xs font-bold shadow-xs',
                cancelButton: 'px-4 py-2 rounded-xl text-xs font-bold'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $('#formDeleteTaskV2').submit();
            }
        });
    });

    // Helper: Validasi Konsisten
    function showEditTaskError(selector, msg) {
        const $el = $(selector);
        const errId = '#err_' + $el.attr('id');
        $(errId).text(msg).removeClass('hidden');
        
        $el.addClass('border-rose-500');
        if ($el.next('.flatpickr-input').length) {
            $el.next('.flatpickr-input').addClass('border-rose-500');
        }
    }

    function clearEditTaskError(selector) {
        const $el = $(selector);
        const errId = '#err_' + $el.attr('id');
        $(errId).text('').addClass('hidden');
        $el.removeClass('border-rose-500');
        if ($el.next('.flatpickr-input').length) {
            $el.next('.flatpickr-input').removeClass('border-rose-500');
        }
    }

    function validateEditTaskField(selector) {
        const $el = $(selector);
        const id = $el.attr('id');
        const val = ($el.val() || '').trim();

        if (id === 'edit_task_judul') {
            if (!val) {
                showEditTaskError('#edit_task_judul', 'Judul task wajib diisi.');
                return false;
            }
            clearEditTaskError('#edit_task_judul');
            return true;
        }

        if (id === 'edit_task_due_date') {
            const startVal = $('#edit_task_start_date').val();
            if (startVal && val && val < startVal) {
                showEditTaskError('#edit_task_due_date', 'Target deadline tidak boleh sebelum tanggal mulai.');
                return false;
            }
            clearEditTaskError('#edit_task_due_date');
            return true;
        }

        return true;
    }

    $('#edit_task_judul').on('input change blur', function() {
        validateEditTaskField(this);
    });

    // Form Submit Handler
    $('#formEditTaskV2').on('submit', function(e) {
        let isValid = true;
        let firstInvalidEl = null;

        const fields = ['#edit_task_judul', '#edit_task_due_date'];
        fields.forEach(function(f) {
            if (!validateEditTaskField(f)) {
                isValid = false;
                if (!firstInvalidEl) {
                    firstInvalidEl = $(f);
                }
            }
        });

        if (!isValid) {
            e.preventDefault();
            if (firstInvalidEl) {
                firstInvalidEl.focus();
                if (firstInvalidEl.next('.flatpickr-input').length) {
                    firstInvalidEl.next('.flatpickr-input').focus();
                }
            }
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Formulir Belum Lengkap',
                    text: 'Silakan lengkapi field yang ditandai merah sebelum menyimpan perubahan.',
                    customClass: {
                        popup: 'rounded-2xl shadow-xl'
                    }
                });
            }
            return false;
        }

        const btn = $('#btnSubmitEditTaskV2');
        btn.prop('disabled', true).addClass('opacity-70 cursor-not-allowed').html(`
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
            <span>Menyimpan Perubahan...</span>
        `);
    });
});
</script>
