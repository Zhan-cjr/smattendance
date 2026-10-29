@if (auth()->user()->hasAnyPermission(['izinabsen.index', 'izinsakit.index', 'izincuti.index', 'izindinas.index', 'koreksi.index', 'ajuanjadwal.index']))
    @php
        $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
        $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
    @endphp

    <div class="bg-white p-1.5 rounded-2xl border border-slate-200/80 shadow-2xs overflow-x-auto no-scrollbar">
        <div class="flex items-center gap-1.5 min-w-max">
            @can('izinabsen.index')
                <a href="{{ route('izinabsen.index') }}" 
                   class="tab-nav-v2 px-4 py-2.5 rounded-xl text-xs font-bold inline-flex items-center gap-2 transition-all {{ request()->is(['izinabsen', 'izinabsen/*']) ? 'active' : '' }}">
                    <i class="ti ti-file-description text-base"></i>
                    <span>Izin Absen</span>
                    @if (!empty($notifikasi_izinabsen))
                        <span class="px-1.5 py-0.5 text-[10px] font-black rounded-full {{ request()->is(['izinabsen', 'izinabsen/*']) ? 'bg-white/25 text-white' : 'bg-rose-100 text-rose-700' }}">
                            {{ $notifikasi_izinabsen }}
                        </span>
                    @endif
                </a>
            @endcan

            @can('izinsakit.index')
                <a href="{{ route('izinsakit.index') }}" 
                   class="tab-nav-v2 px-4 py-2.5 rounded-xl text-xs font-bold inline-flex items-center gap-2 transition-all {{ request()->is(['izinsakit', 'izinsakit/*']) ? 'active' : '' }}">
                    <i class="ti ti-first-aid-kit text-base"></i>
                    <span>Izin Sakit</span>
                    @if (!empty($notifikasi_izinsakit))
                        <span class="px-1.5 py-0.5 text-[10px] font-black rounded-full {{ request()->is(['izinsakit', 'izinsakit/*']) ? 'bg-white/25 text-white' : 'bg-rose-100 text-rose-700' }}">
                            {{ $notifikasi_izinsakit }}
                        </span>
                    @endif
                </a>
            @endcan

            @can('izincuti.index')
                <a href="{{ route('izincuti.index') }}" 
                   class="tab-nav-v2 px-4 py-2.5 rounded-xl text-xs font-bold inline-flex items-center gap-2 transition-all {{ request()->is(['izincuti', 'izincuti/*']) ? 'active' : '' }}">
                    <i class="ti ti-calendar-event text-base"></i>
                    <span>Izin Cuti</span>
                    @if (!empty($notifikasi_izincuti))
                        <span class="px-1.5 py-0.5 text-[10px] font-black rounded-full {{ request()->is(['izincuti', 'izincuti/*']) ? 'bg-white/25 text-white' : 'bg-rose-100 text-rose-700' }}">
                            {{ $notifikasi_izincuti }}
                        </span>
                    @endif
                </a>
            @endcan

            @can('izindinas.index')
                <a href="{{ route('izindinas.index') }}" 
                   class="tab-nav-v2 px-4 py-2.5 rounded-xl text-xs font-bold inline-flex items-center gap-2 transition-all {{ request()->is(['izindinas', 'izindinas/*']) ? 'active' : '' }}">
                    <i class="ti ti-briefcase text-base"></i>
                    <span>Izin Dinas</span>
                    @if (!empty($notifikasi_izin_dinas))
                        <span class="px-1.5 py-0.5 text-[10px] font-black rounded-full {{ request()->is(['izindinas', 'izindinas/*']) ? 'bg-white/25 text-white' : 'bg-rose-100 text-rose-700' }}">
                            {{ $notifikasi_izin_dinas }}
                        </span>
                    @endif
                </a>
            @endcan

            @can('ajuanjadwal.index')
                <a href="{{ route('ajuanjadwal.index') }}" 
                   class="tab-nav-v2 px-4 py-2.5 rounded-xl text-xs font-bold inline-flex items-center gap-2 transition-all {{ request()->is(['ajuanjadwal', 'ajuanjadwal/*']) ? 'active' : '' }}">
                    <i class="ti ti-calendar-stats text-base"></i>
                    <span>Ajuan Jadwal</span>
                    @if (!empty($notifikasi_ajuan_jadwal))
                        <span class="px-1.5 py-0.5 text-[10px] font-black rounded-full {{ request()->is(['ajuanjadwal', 'ajuanjadwal/*']) ? 'bg-white/25 text-white' : 'bg-rose-100 text-rose-700' }}">
                            {{ $notifikasi_ajuan_jadwal }}
                        </span>
                    @endif
                </a>
            @endcan

            @can('koreksi.index')
                <a href="{{ (\Illuminate\Support\Facades\Route::has('koreksi.index') ? route('koreksi.index') : '#') }}" 
                   class="tab-nav-v2 px-4 py-2.5 rounded-xl text-xs font-bold inline-flex items-center gap-2 transition-all {{ request()->is(['koreksi', 'koreksi/*']) ? 'active' : '' }}">
                    <i class="ti ti-clock-edit text-base"></i>
                    <span>Koreksi Absen</span>
                    @if (!empty($notifikasi_koreksi))
                        <span class="px-1.5 py-0.5 text-[10px] font-black rounded-full {{ request()->is(['koreksi', 'koreksi/*']) ? 'bg-white/25 text-white' : 'bg-rose-100 text-rose-700' }}">
                            {{ $notifikasi_koreksi }}
                        </span>
                    @endif
                </a>
            @endcan
        </div>
    </div>
@endif
