@php
    $authUser = auth()->user();
    $fullName = $authUser->name ?? 'Pengguna';
    $userName = explode(' ', $fullName)[0];
    $userEmail = $authUser->email ?? '-';
    $userPhoto = null;
    $userRoleText = $authUser->getRoleNames()->first() ?? 'Admin';

    $userKaryawan = \App\Models\Userkaryawan::where('id_user', $authUser->id)->first();
    if ($userKaryawan) {
        $sidebarKaryawan = \App\Models\Karyawan::where('nik', $userKaryawan->nik)->first();
        if ($sidebarKaryawan && $sidebarKaryawan->foto && \Illuminate\Support\Facades\Storage::disk('public')->exists('/karyawan/' . $sidebarKaryawan->foto)) {
            $userPhoto = getfotoKaryawan($sidebarKaryawan->foto);
        }
    }
@endphp

<!-- Navbar: h-[70px] matches sidebar logo section exactly, border-b continues the same line -->
<header class="h-[70px] min-h-[70px] bg-white dark:bg-slate-900 border-b border-slate-200/80 dark:border-slate-800 sticky top-0 z-30 shadow-[0_1px_3px_0_rgba(0,0,0,0.02)] transition-colors duration-200">
    <div class="h-full px-4 sm:px-6 flex items-center justify-between gap-2">
        <!-- Left Area: Collapse Toggle & Spacious Search Box -->
        <div class="flex items-center gap-2 sm:gap-4 min-w-0">
            <!-- Mobile Toggle -->
            <button @click="mobileSidebarOpen = true" class="lg:hidden text-slate-600 dark:text-slate-300 hover:text-slate-800 dark:hover:text-white p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition shrink-0" title="Buka Menu">
                <i class="ti ti-menu-2 text-xl"></i>
            </button>

            <!-- Sidebar Collapse Icon (Desktop) -->
            <button class="hidden lg:flex items-center justify-center w-9 h-9 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition shrink-0">
                <i class="ti ti-layout-sidebar-left-collapse text-lg"></i>
            </button>
            
            <!-- Search in HRMS button (Trigger Quick Menu Search Modal) - Desktop -->
            <div class="hidden sm:block relative w-64 md:w-80 cursor-pointer" onclick="window.openQuickMenuSearch && window.openQuickMenuSearch()">
                <i class="ti ti-search text-base text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                <input type="text" 
                       readonly
                       placeholder="Cari Menu / Akses Cepat..." 
                       class="w-full bg-slate-50 dark:bg-slate-800/80 hover:bg-slate-100/80 dark:hover:bg-slate-800 focus:bg-white dark:focus:bg-slate-900 text-xs text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 pl-10 pr-20 py-2.5 rounded-xl border border-slate-200/90 dark:border-slate-700/80 focus:border-emerald-500 focus:outline-none shadow-2xs transition cursor-pointer">
                <div class="absolute right-2.5 top-1/2 -translate-y-1/2 text-[10px] text-slate-400 dark:text-slate-500 font-semibold bg-white dark:bg-slate-800 px-2 py-0.5 rounded-lg border border-slate-200 dark:border-slate-700 shadow-2xs flex items-center gap-1 pointer-events-none">
                    <span>⌘/Ctrl</span>
                    <span>K</span>
                </div>
            </div>

            <!-- Mobile Quick Search Trigger Icon -->
            <button class="sm:hidden w-9 h-9 flex items-center justify-center rounded-lg text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-100 hover:bg-slate-100 dark:hover:bg-slate-800 transition" 
                    title="Cari Menu" onclick="window.openQuickMenuSearch && window.openQuickMenuSearch()">
                <i class="ti ti-search text-lg"></i>
            </button>
        </div>

        <!-- Right Area: Icons (Expand, Grid, Bell, Messages, Profile) -->
        <div class="flex items-center gap-1 sm:gap-2 shrink-0">
            
            <!-- Fullscreen Button -->
            <button class="hidden sm:flex w-9 h-9 items-center justify-center rounded-lg text-slate-500 hover:text-slate-800 hover:bg-slate-100 dark:text-slate-400 dark:hover:text-slate-100 dark:hover:bg-slate-800 transition cursor-pointer" 
                    title="Full Screen" onclick="document.fullscreenElement ? document.exitFullscreen() : document.documentElement.requestFullscreen()">
                <i class="ti ti-maximize text-lg"></i>
            </button>

            {{-- Dark / Light Mode Toggle Button (Dinonaktifkan sementara) --}}
            {{--
            <button type="button"
                    id="btnThemeToggle"
                    onclick="window.toggleDarkMode && window.toggleDarkMode()"
                    class="w-9 h-9 flex items-center justify-center rounded-lg text-slate-500 hover:text-slate-800 hover:bg-slate-100 dark:text-amber-400 dark:hover:text-amber-300 dark:hover:bg-slate-800 transition cursor-pointer" 
                    title="Ganti Tema Gelap / Terang">
                <i class="ti ti-sun text-lg hidden dark:block"></i>
                <i class="ti ti-moon text-lg block dark:hidden"></i>
            </button>
            --}}

            <!-- Apps Grid -->
            <button class="hidden sm:flex w-9 h-9 items-center justify-center rounded-lg text-slate-500 hover:text-slate-800 hover:bg-slate-100 dark:text-slate-400 dark:hover:text-slate-100 dark:hover:bg-slate-800 transition" title="Apps">
                <i class="ti ti-layout-grid text-lg"></i>
            </button>

            @php
                $totalNotifCount = ($notifikasi_ajuan_absen ?? 0) + ($notifikasi_reimbursement ?? 0) + (auth()->user()->unreadNotifications->where('type', '!=', 'App\Notifications\PengumumanNotification')->count() ?? 0);
            @endphp

            <!-- Notification Bell with Dropdown Menu -->
            <div class="relative" x-data="{ notifOpen: false }" id="navbarNotifContainer">
                <button @click="notifOpen = !notifOpen" 
                        type="button"
                        id="btnNavbarNotifBell"
                        class="relative w-9 h-9 flex items-center justify-center rounded-lg text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-100 hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer" 
                        title="Notifikasi & Ajuan">
                    <i data-lucide="bell" class="w-4 h-4"></i>
                    @if($totalNotifCount > 0)
                        <span class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] px-1 bg-rose-500 text-white font-extrabold text-[10px] rounded-full flex items-center justify-center ring-2 ring-white dark:ring-slate-900 shadow-xs">
                            {{ $totalNotifCount > 99 ? '99+' : $totalNotifCount }}
                        </span>
                    @endif
                </button>

                <!-- Notification Dropdown Panel -->
                <div x-show="notifOpen" 
                     @click.away="notifOpen = false" 
                     x-cloak
                     id="navbarNotifDropdownPanel"
                     class="absolute right-0 mt-2 w-80 sm:w-96 bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200/90 dark:border-slate-800 z-50 text-xs overflow-hidden">
                    
                    <!-- Header -->
                    <div class="px-4 py-3 bg-slate-50/80 dark:bg-slate-800/80 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-md bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 flex items-center justify-center font-bold">
                                <i data-lucide="bell-ring" class="w-3.5 h-3.5"></i>
                            </div>
                            <h3 class="font-bold text-slate-800 dark:text-slate-100 text-xs">Pemberitahuan &amp; Ajuan</h3>
                        </div>
                        @if($totalNotifCount > 0)
                            <span class="px-2 py-0.5 rounded-full bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 font-extrabold text-[10px] border border-rose-200 dark:border-rose-900/50">
                                {{ $totalNotifCount }} Baru
                            </span>
                        @endif
                    </div>

                    <!-- Scrollable Notification List -->
                    <div class="max-h-80 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-800">
                        
                        <!-- System / Generic Unread Notifications -->
                        @if(auth()->user()->unreadNotifications->where('type', '!=', 'App\Notifications\PengumumanNotification')->count() > 0)
                            @foreach (auth()->user()->unreadNotifications->where('type', '!=', 'App\Notifications\PengumumanNotification') as $notification)
                                @php
                                    $notifUrl = $notification->data['url'] ?? '#';
                                    if ($notifUrl === '/karyawan-approval' && !auth()->user()->hasRole('karyawan')) {
                                        $notifMsg = strtolower($notification->data['message'] ?? '');
                                        if (str_contains($notifMsg, 'sakit')) {
                                            $notifUrl = route('izinsakit.index');
                                        } elseif (str_contains($notifMsg, 'cuti')) {
                                            $notifUrl = route('izincuti.index');
                                        } elseif (str_contains($notifMsg, 'dinas')) {
                                            $notifUrl = route('izindinas.index');
                                        } elseif (str_contains($notifMsg, 'reimbursement') && \Illuminate\Support\Facades\Route::has('reimbursement.index')) {
                                            $notifUrl = route('reimbursement.index');
                                        } elseif (str_contains($notifMsg, 'koreksi') && \Illuminate\Support\Facades\Route::has('koreksipresensi.index')) {
                                            $notifUrl = route('koreksipresensi.index');
                                        } else {
                                            $notifUrl = route('izinabsen.index');
                                        }
                                    }
                                @endphp
                                <a href="{{ $notifUrl }}" class="p-3 flex items-start gap-3 hover:bg-slate-50/80 dark:hover:bg-slate-800/80 transition group block">
                                    <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0 border border-indigo-100 dark:border-indigo-900/50 font-bold text-xs mt-0.5">
                                        <i data-lucide="bell" class="w-4 h-4"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-bold text-slate-800 dark:text-slate-100 text-xs truncate group-hover:text-emerald-700 dark:group-hover:text-emerald-400 transition">
                                            {{ $notification->data['title'] ?? 'Pemberitahuan' }}
                                        </h4>
                                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 leading-snug line-clamp-2">
                                            {{ $notification->data['message'] ?? '' }}
                                        </p>
                                        <span class="text-[9.5px] font-semibold text-slate-400 dark:text-slate-500 mt-1 block">
                                            {{ $notification->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                </a>
                            @endforeach
                        @endif

                        <!-- Ajuan Izin / Sakit / Cuti / Dinas -->
                        @if(isset($data_izin) && count($data_izin) > 0)
                            @foreach ($data_izin as $d)
                                @php
                                    $link = '#';
                                    $statusBadge = 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/50 dark:text-blue-400 dark:border-blue-900/50';
                                    $statusLabel = 'Izin Absen';
                                    $iconName = 'calendar-clock';
                                    if ($d->status == 'i') {
                                        $statusLabel = 'Izin Absen';
                                        $statusBadge = 'bg-sky-50 text-sky-700 border-sky-200 dark:bg-sky-950/50 dark:text-sky-400 dark:border-sky-900/50';
                                        $link = route('izinabsen.index');
                                        $iconName = 'clock';
                                    } elseif ($d->status == 's') {
                                        $statusLabel = 'Izin Sakit';
                                        $statusBadge = 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/50 dark:text-amber-400 dark:border-amber-900/50';
                                        $link = route('izinsakit.index');
                                        $iconName = 'activity';
                                    } elseif ($d->status == 'c') {
                                        $statusLabel = 'Izin Cuti';
                                        $statusBadge = 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/50 dark:text-emerald-400 dark:border-emerald-900/50';
                                        $link = route('izincuti.index');
                                        $iconName = 'calendar-heart';
                                    } elseif ($d->status == 'd') {
                                        $statusLabel = 'Izin Dinas';
                                        $statusBadge = 'bg-purple-50 text-purple-700 border-purple-200 dark:bg-purple-950/50 dark:text-purple-400 dark:border-purple-900/50';
                                        $link = route('izindinas.index');
                                        $iconName = 'briefcase';
                                    }
                                @endphp
                                <a href="{{ $link }}" class="p-3 flex items-start gap-3 hover:bg-slate-50/80 dark:hover:bg-slate-800/80 transition group block">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 font-black text-xs mt-0.5 border {{ $statusBadge }}">
                                        {{ strtoupper($d->status) }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between gap-1.5">
                                            <h4 class="font-bold text-slate-800 dark:text-slate-100 text-xs truncate group-hover:text-emerald-700 dark:group-hover:text-emerald-400 transition">
                                                {{ $d->nama_karyawan }}
                                            </h4>
                                            <span class="text-[9.5px] px-1.5 py-0.2 rounded font-bold border {{ $statusBadge }}">
                                                {{ $statusLabel }}
                                            </span>
                                        </div>
                                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 leading-snug">
                                            Mengajukan permohonan <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $statusLabel }}</span>
                                        </p>
                                        <span class="text-[9.5px] font-semibold text-slate-400 dark:text-slate-500 mt-1 block">
                                            {{ \Carbon\Carbon::parse($d->created_at)->diffForHumans() }}
                                        </span>
                                    </div>
                                </a>
                            @endforeach
                        @endif

                        <!-- Ajuan Reimbursement Pending -->
                        @if(isset($data_reimbursement_pending) && count($data_reimbursement_pending) > 0 && \Illuminate\Support\Facades\Route::has('reimbursement.index'))
                            @foreach ($data_reimbursement_pending as $dr)
                                <a href="{{ route('reimbursement.index') }}" class="p-3 flex items-start gap-3 hover:bg-slate-50/80 dark:hover:bg-slate-800/80 transition group block">
                                    <div class="w-8 h-8 rounded-lg bg-rose-50 dark:bg-rose-950/50 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-900/50 flex items-center justify-center shrink-0 font-black text-[11px] mt-0.5">
                                        RM
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between gap-1.5">
                                            <h4 class="font-bold text-slate-800 dark:text-slate-100 text-xs truncate group-hover:text-emerald-700 dark:group-hover:text-emerald-400 transition">
                                                {{ $dr->nama_karyawan }}
                                            </h4>
                                            <span class="text-[9.5px] px-1.5 py-0.2 rounded font-bold bg-rose-50 dark:bg-rose-950/50 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-900/50">
                                                Reimbursement
                                            </span>
                                        </div>
                                        <p class="text-[11px] text-slate-600 dark:text-slate-300 mt-0.5 leading-snug">
                                            Nominal: <span class="font-bold text-slate-800 dark:text-slate-100">Rp {{ number_format($dr->total_nominal, 0, ',', '.') }}</span>
                                        </p>
                                        <span class="text-[9.5px] font-semibold text-slate-400 dark:text-slate-500 mt-1 block">
                                            {{ \Carbon\Carbon::parse($dr->created_at)->diffForHumans() }}
                                        </span>
                                    </div>
                                </a>
                            @endforeach
                        @endif

                        <!-- Empty State -->
                        @if($totalNotifCount == 0 && (!isset($data_izin) || count($data_izin) == 0) && (!isset($data_reimbursement_pending) || count($data_reimbursement_pending) == 0))
                            <div class="p-6 text-center">
                                <div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center mx-auto mb-2">
                                    <i data-lucide="bell-off" class="w-5 h-5"></i>
                                </div>
                                <p class="text-xs font-bold text-slate-700 dark:text-slate-200">Tidak ada notifikasi baru</p>
                                <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5">Semua ajuan dan pemberitahuan telah diproses.</p>
                            </div>
                        @endif

                    </div>

                    <!-- Footer Link -->
                    <div class="px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border-t border-slate-100 dark:border-slate-800 text-center">
                        <a href="{{ route('izinabsen.index') }}" class="text-[11px] font-bold text-emerald-700 dark:text-emerald-400 hover:text-emerald-800 dark:hover:text-emerald-300 hover:underline inline-flex items-center gap-1">
                            <span>Kelola Semua Ajuan</span>
                            <i data-lucide="arrow-right" class="w-3 h-3"></i>
                        </a>
                    </div>

                </div>
            </div>

            <!-- Message Icon -->
            <button class="w-9 h-9 flex items-center justify-center rounded-lg text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-100 hover:bg-slate-100 dark:hover:bg-slate-800 transition" title="Messages">
                <i data-lucide="mail" class="w-4 h-4"></i>
            </button>

            <div class="h-6 w-px bg-slate-200 dark:bg-slate-800 mx-1.5"></div>

            <!-- User Profile Avatar with Initial Circle -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center gap-2.5 px-2 py-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                    <div class="w-8 h-8 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 flex items-center justify-center font-bold text-xs border border-slate-300 dark:border-slate-600 overflow-hidden shadow-2xs">
                        @if($userPhoto)
                            <img src="{{ $userPhoto }}" alt="{{ $fullName }}" class="w-full h-full object-cover">
                        @else
                            {{ substr($fullName, 0, 1) }}
                        @endif
                    </div>
                    <div class="hidden md:flex flex-col text-left">
                        <span class="text-xs font-bold text-slate-800 dark:text-slate-100 leading-tight">{{ $userName }}</span>
                        <span class="text-[10px] text-slate-400 dark:text-slate-500 capitalize leading-none">{{ $userRoleText }}</span>
                    </div>
                    <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400 ml-0.5"></i>
                </button>

                <!-- Dropdown Menu -->
                <div x-show="open" 
                     @click.away="open = false" 
                     x-cloak
                     class="absolute right-0 mt-2 w-52 bg-white dark:bg-slate-900 rounded-xl shadow-lg border border-slate-100 dark:border-slate-800 py-1.5 z-50 text-xs">
                    <div class="px-3.5 py-2 border-b border-slate-100 dark:border-slate-800">
                        <p class="font-bold text-slate-800 dark:text-slate-100">{{ $fullName }}</p>
                        <p class="text-slate-400 dark:text-slate-500 truncate">{{ $userEmail }}</p>
                    </div>
                    <a href="{{ route('profile.editprofile') }}" class="flex items-center gap-2 px-3.5 py-2 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800">
                        <i data-lucide="user" class="w-4 h-4 text-slate-400"></i>
                        Profile
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="border-t border-slate-100 dark:border-slate-800 pt-1">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-2 px-3.5 py-2 text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40">
                            <i data-lucide="log-out" class="w-4 h-4 text-rose-500"></i>
                            Logout
                        </button>
                    </form>
                </div>
            </div>

    </div>
</header>
