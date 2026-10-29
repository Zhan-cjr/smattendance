@if (auth()->user()->hasAnyPermission(['izinabsen.index', 'izinsakit.index', 'izincuti.index', 'izindinas.index', 'ajuanjadwal.index', 'koreksi.index']))
    @php
        $navThemeColor = $general_setting->theme_color_1 ?? '#0a5c41';
    @endphp
    <div class="flex items-center gap-1.5 overflow-x-auto pb-1 no-scrollbar text-xs font-semibold">
        @can('izinabsen.index')
            <a href="{{ route('izinabsen.index') }}" 
               class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border transition-all whitespace-nowrap {{ request()->is(['izinabsen', 'izinabsen/*']) ? 'bg-white text-slate-900 border-slate-300 shadow-2xs font-bold' : 'bg-transparent text-slate-500 border-transparent hover:text-slate-800 hover:bg-white/60' }}"
               @if(request()->is(['izinabsen', 'izinabsen/*'])) style="border-bottom: 2px solid {{ $navThemeColor }}; color: {{ $navThemeColor }};" @endif>
                <i class="ti ti-file-description text-sm"></i>
                <span>Izin Absen</span>
                @if (!empty($notifikasi_izinabsen))
                    <span class="px-1.5 py-0.5 text-[10px] font-black text-white bg-rose-500 rounded-full leading-none">
                        {{ $notifikasi_izinabsen }}
                    </span>
                @endif
            </a>
        @endcan

        @can('izinsakit.index')
            <a href="{{ route('izinsakit.index') }}" 
               class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border transition-all whitespace-nowrap {{ request()->is(['izinsakit', 'izinsakit/*']) ? 'bg-white text-slate-900 border-slate-300 shadow-2xs font-bold' : 'bg-transparent text-slate-500 border-transparent hover:text-slate-800 hover:bg-white/60' }}"
               @if(request()->is(['izinsakit', 'izinsakit/*'])) style="border-bottom: 2px solid {{ $navThemeColor }}; color: {{ $navThemeColor }};" @endif>
                <i class="ti ti-first-aid-kit text-sm"></i>
                <span>Izin Sakit</span>
                @if (!empty($notifikasi_izinsakit))
                    <span class="px-1.5 py-0.5 text-[10px] font-black text-white bg-rose-500 rounded-full leading-none">
                        {{ $notifikasi_izinsakit }}
                    </span>
                @endif
            </a>
        @endcan

        @can('izincuti.index')
            <a href="{{ route('izincuti.index') }}" 
               class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border transition-all whitespace-nowrap {{ request()->is(['izincuti', 'izincuti/*']) ? 'bg-white text-slate-900 border-slate-300 shadow-2xs font-bold' : 'bg-transparent text-slate-500 border-transparent hover:text-slate-800 hover:bg-white/60' }}"
               @if(request()->is(['izincuti', 'izincuti/*'])) style="border-bottom: 2px solid {{ $navThemeColor }}; color: {{ $navThemeColor }};" @endif>
                <i class="ti ti-calendar-event text-sm"></i>
                <span>Izin Cuti</span>
                @if (!empty($notifikasi_izincuti))
                    <span class="px-1.5 py-0.5 text-[10px] font-black text-white bg-rose-500 rounded-full leading-none">
                        {{ $notifikasi_izincuti }}
                    </span>
                @endif
            </a>
        @endcan

        @can('izindinas.index')
            <a href="{{ route('izindinas.index') }}" 
               class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border transition-all whitespace-nowrap {{ request()->is(['izindinas', 'izindinas/*']) ? 'bg-white text-slate-900 border-slate-300 shadow-2xs font-bold' : 'bg-transparent text-slate-500 border-transparent hover:text-slate-800 hover:bg-white/60' }}"
               @if(request()->is(['izindinas', 'izindinas/*'])) style="border-bottom: 2px solid {{ $navThemeColor }}; color: {{ $navThemeColor }};" @endif>
                <i class="ti ti-briefcase text-sm"></i>
                <span>Izin Dinas</span>
                @if (!empty($notifikasi_izin_dinas))
                    <span class="px-1.5 py-0.5 text-[10px] font-black text-white bg-rose-500 rounded-full leading-none">
                        {{ $notifikasi_izin_dinas }}
                    </span>
                @endif
            </a>
        @endcan

        @can('ajuanjadwal.index')
            <a href="{{ route('ajuanjadwal.index') }}" 
               class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border transition-all whitespace-nowrap {{ request()->is(['ajuanjadwal', 'ajuanjadwal/*']) ? 'bg-white text-slate-900 border-slate-300 shadow-2xs font-bold' : 'bg-transparent text-slate-500 border-transparent hover:text-slate-800 hover:bg-white/60' }}"
               @if(request()->is(['ajuanjadwal', 'ajuanjadwal/*'])) style="border-bottom: 2px solid {{ $navThemeColor }}; color: {{ $navThemeColor }};" @endif>
                <i class="ti ti-calendar-stats text-sm"></i>
                <span>Ajuan Jadwal</span>
                @if (!empty($notifikasi_ajuan_jadwal))
                    <span class="px-1.5 py-0.5 text-[10px] font-black text-white bg-rose-500 rounded-full leading-none">
                        {{ $notifikasi_ajuan_jadwal }}
                    </span>
                @endif
            </a>
        @endcan

        @can('koreksi.index')
            <a href="{{ (\Illuminate\Support\Facades\Route::has('koreksi.index') ? route('koreksi.index') : '#') }}" 
               class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border transition-all whitespace-nowrap {{ request()->is(['koreksi', 'koreksi/*']) ? 'bg-white text-slate-900 border-slate-300 shadow-2xs font-bold' : 'bg-transparent text-slate-500 border-transparent hover:text-slate-800 hover:bg-white/60' }}"
               @if(request()->is(['koreksi', 'koreksi/*'])) style="border-bottom: 2px solid {{ $navThemeColor }}; color: {{ $navThemeColor }};" @endif>
                <i class="ti ti-clock-edit text-sm"></i>
                <span>Koreksi Absen</span>
                @if (!empty($notifikasi_koreksi))
                    <span class="px-1.5 py-0.5 text-[10px] font-black text-white bg-rose-500 rounded-full leading-none">
                        {{ $notifikasi_koreksi }}
                    </span>
                @endif
            </a>
        @endcan
    </div>
@endif
