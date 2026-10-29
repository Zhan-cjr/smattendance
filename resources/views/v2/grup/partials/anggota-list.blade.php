@php
    use Illuminate\Support\Facades\Crypt;
@endphp

@if ($karyawanInGrup->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3.5">
        @foreach ($karyawanInGrup as $index => $detail)
            <div class="bg-white border border-slate-200/90 hover:border-emerald-300/80 rounded-2xl p-4 shadow-2xs hover:shadow-sm transition-all duration-200 flex items-center justify-between gap-3 group">
                <div class="flex items-center gap-3 min-w-0 flex-1">
                    <!-- Avatar Thumbnail with Online Dot -->
                    <div class="relative shrink-0">
                        @if (!empty($detail->foto ?? null) && $detail->foto_exists)
                            <img src="{{ asset('storage/karyawan/' . $detail->foto) }}" 
                                 alt="{{ $detail->nama_karyawan }}" 
                                 class="w-12 h-12 rounded-xl object-cover border border-slate-200 shadow-2xs">
                        @else
                            <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-600 border border-slate-200 flex items-center justify-center font-bold text-sm shadow-2xs">
                                {{ strtoupper(substr($detail->nama_karyawan, 0, 1)) }}
                            </div>
                        @endif
                        <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 rounded-full bg-emerald-500 border-2 border-white" title="Aktif"></span>
                    </div>

                    <!-- Details -->
                    <div class="min-w-0 flex-1">
                        <h4 class="text-xs sm:text-sm font-bold text-slate-900 truncate group-hover:text-emerald-800 transition" title="{{ $detail->nama_karyawan }}">
                            {{ $detail->nama_karyawan }}
                        </h4>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <span class="font-mono text-[10.5px] font-bold text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded border border-slate-200/80">
                                {{ $detail->nik }}
                            </span>
                        </div>
                        <div class="flex items-center gap-1.5 text-[11px] text-slate-500 mt-1 truncate">
                            <span class="truncate font-medium">{{ $detail->nama_jabatan ?? '-' }}</span>
                            <span>•</span>
                            <span class="text-slate-400 truncate">{{ $detail->nama_dept ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Delete Action -->
                <div class="shrink-0">
                    <form method="POST" action="{{ route('grup.removeKaryawan') }}" class="m-0 delete-member-form">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="id" value="{{ Crypt::encrypt($detail->id) }}">
                        <button type="button" 
                                class="w-8 h-8 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 flex items-center justify-center transition active:scale-95 btn-delete-member cursor-pointer shadow-2xs" 
                                title="Hapus dari grup">
                            <i class="ti ti-trash text-xs"></i>
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="py-14 text-center bg-white rounded-2xl border border-slate-200 shadow-2xs">
        <div class="w-14 h-14 rounded-2xl bg-slate-100 text-slate-400 mx-auto flex items-center justify-center text-2xl mb-3">
            <i class="ti ti-users-off"></i>
        </div>
        <h4 class="text-sm font-bold text-slate-800">Belum ada anggota di grup ini</h4>
        <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
            Klik tombol <strong class="text-slate-600">"+ Tambah Anggota"</strong> di atas untuk menambahkan karyawan ke grup kerja ini.
        </p>
    </div>
@endif
