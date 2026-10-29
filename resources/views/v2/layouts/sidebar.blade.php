@php
    $authUser = auth()->user();
    $fullName = $authUser->name ?? 'Pengguna';
    $userName = explode(' ', $fullName)[0];
    $userEmail = $authUser->email ?? '-';
    $userPhoto = null;
    $userRoleText = $authUser->getRoleNames()->first() ?? 'Super Admin';
    $userDeptName = null;
    $userCabangName = null;
    $userNik = null;

    $userKaryawan = \App\Models\Userkaryawan::where('id_user', $authUser->id)->first();
    if ($userKaryawan) {
        $sidebarKaryawan = \App\Models\Karyawan::where('nik', $userKaryawan->nik)
            ->leftJoin('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->leftJoin('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->select('karyawan.*', 'departemen.nama_dept', 'cabang.nama_cabang')
            ->first();

        if ($sidebarKaryawan) {
            $userNik = $sidebarKaryawan->nik;
            $userDeptName = $sidebarKaryawan->nama_dept;
            $userCabangName = $sidebarKaryawan->nama_cabang;
            if ($sidebarKaryawan->foto && \Illuminate\Support\Facades\Storage::disk('public')->exists('/karyawan/' . $sidebarKaryawan->foto)) {
                $userPhoto = getfotoKaryawan($sidebarKaryawan->foto);
            }
        }
    }
@endphp

<!-- Mobile Backdrop -->
<div x-show="mobileSidebarOpen" 
     x-transition:enter="transition-opacity ease-linear duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-linear duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @click="mobileSidebarOpen = false" 
     class="fixed inset-0 bg-slate-900/40 z-40 lg:hidden">
</div>

<!-- Sidebar Container (Clean, Minimalist & Ultra-Elegant Style) -->
<aside id="v2Sidebar"
       :class="mobileSidebarOpen ? 'translate-x-0 !visible' : '-translate-x-full lg:translate-x-0'"
       class="fixed lg:static inset-y-0 left-0 z-50 w-64 h-screen max-h-screen border-r border-white/10 flex flex-col flex-shrink-0 transition-transform duration-300 ease-in-out shadow-xl select-none relative overflow-hidden -translate-x-full lg:translate-x-0" 
       style="background: linear-gradient(180deg, {{ $general_setting->theme_color_1 ?? '#0a5c41' }} 0%, {{ $general_setting->theme_color_2 ?? '#117956' }} 100%); color: #ffffff;">
    
    <!-- Ultra-subtle ambient background illumination (Smooth & Minimal) -->
    <div class="pointer-events-none absolute -top-24 -left-24 w-64 h-64 rounded-full bg-white/[0.06] blur-3xl"></div>
    <div class="pointer-events-none absolute bottom-0 right-0 w-48 h-48 rounded-full bg-black/10 blur-2xl"></div>

    <!-- Top Fixed Logo & Header Box -->
    <div class="h-[70px] min-h-[70px] flex items-center justify-between px-5 flex-shrink-0 relative z-10 border-b border-white/[0.08]">
            <a href="{{ route('dashboard.index') }}" class="flex items-center gap-3 group">
                @if (!empty($general_setting->logo) && Storage::disk('public')->exists('logo/' . $general_setting->logo))
                    <img src="{{ asset('storage/logo/' . $general_setting->logo) }}" alt="Logo" class="max-h-10 max-w-[44px] w-auto h-auto object-contain shrink-0 group-hover:scale-105 transition-transform duration-200">
                @else
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0">
                        <i class="ti ti-layers text-2xl text-white"></i>
                    </div>
                @endif
                <div class="flex flex-col min-w-0">
                    <span class="font-extrabold text-[15px] text-white tracking-tight leading-tight truncate">
                        {{ $general_setting->nama_aplikasi ?? 'GAWE' }}
                    </span>
                    <span class="text-[9.5px] font-semibold text-white/65 tracking-wider truncate leading-tight uppercase mt-0.5">
                        {{ $general_setting->nama_perusahaan ?? 'HRMS & Presensi' }}
                    </span>
                </div>
            </a>
            <button class="text-white/60 hover:text-white p-1.5 rounded-lg lg:hidden" @click="mobileSidebarOpen = false">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <style>
            /* Custom sleek scrollbar for colored sidebar */
            aside, aside * {
                scrollbar-width: thin;
                scrollbar-color: rgba(255, 255, 255, 0.2) transparent;
            }
            aside::-webkit-scrollbar,
            aside *::-webkit-scrollbar {
                width: 4px;
            }
            aside::-webkit-scrollbar-track,
            aside *::-webkit-scrollbar-track {
                background: transparent;
            }
            aside::-webkit-scrollbar-thumb,
            aside *::-webkit-scrollbar-thumb {
                background: rgba(255, 255, 255, 0.2);
                border-radius: 4px;
            }
            aside::-webkit-scrollbar-thumb:hover,
            aside *::-webkit-scrollbar-thumb:hover {
                background: rgba(255, 255, 255, 0.35);
            }
            /* Tree Line Submenu Navigation Styles */
            .sidebar-tree-container {
                position: relative;
                margin-left: 1.15rem; /* ~18px, aligned under icon center */
                padding-left: 1rem;   /* 16px space for the horizontal branch */
                margin-top: 0.1rem;
                margin-bottom: 0.2rem;
            }
            
            /* Continuous vertical spine line */
            .sidebar-tree-container::before {
                content: '';
                position: absolute;
                left: 0;
                top: 0;
                bottom: 13px; /* stops cleanly at the vertical center of the last child item */
                width: 1px;
                background-color: rgba(255, 255, 255, 0.25);
            }

            /* Individual tree item branch */
            .sidebar-tree-branch {
                position: relative;
                display: flex;
                align-items: center;
                min-height: 26px;
            }

            /* The curved elbow branch connecting the spine to the item */
            .sidebar-tree-branch::before {
                content: '';
                position: absolute;
                left: -1rem; /* -16px, reaches exactly back to the spine at left: 0 */
                top: 0;
                height: 13px; /* reaches the vertical center */
                width: 14px;  /* extends horizontally towards the item */
                border-left: 1px solid rgba(255, 255, 255, 0.25);
                border-bottom: 1px solid rgba(255, 255, 255, 0.25);
                border-bottom-left-radius: 6px;
                pointer-events: none;
            }
        </style>

        <!-- Menu Navigation Section -->
        <div id="sidebarNavContainer" class="flex-1 min-h-0 overflow-y-auto px-3 py-2.5 space-y-3 relative z-10">
            
            <!-- Minimalist Clean User Info -->
            <div class="px-2.5 py-2 rounded-xl bg-white/[0.06] border border-white/10 flex items-center justify-between gap-2.5 hover:bg-white/[0.09] transition-colors">
                <div class="flex items-center gap-2.5 min-w-0 flex-1">
                    <!-- Avatar with Simple Status Dot -->
                    <div class="relative shrink-0">
                        <div class="w-9 h-9 rounded-lg overflow-hidden bg-white/15 border border-white/20 shadow-xs flex items-center justify-center font-extrabold text-xs text-white">
                            @if($userPhoto)
                                <img src="{{ $userPhoto }}" alt="{{ $fullName }}" class="w-full h-full object-cover">
                            @else
                                <span class="text-xs font-black">{{ strtoupper(substr($fullName, 0, 1)) }}</span>
                            @endif
                        </div>
                        <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 bg-emerald-400 border-2 border-[#0a5c41] rounded-full"></span>
                    </div>

                    <!-- Text Details -->
                    <div class="min-w-0 flex-1">
                        <h4 class="text-[12.5px] font-bold text-white truncate leading-tight">
                            {{ $fullName }}
                        </h4>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <span class="inline-flex items-center px-1.5 py-0.2 rounded bg-white/15 text-white/90 font-bold text-[8.5px] uppercase tracking-wider">
                                {{ $userRoleText }}
                            </span>
                            @if($userDeptName)
                                <span class="text-[10px] text-white/70 truncate">• {{ $userDeptName }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Gear / Settings Icon Link to Profile Page -->
                <a href="{{ route('profile.editprofile') }}" 
                   class="w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 text-white/70 hover:text-white flex items-center justify-center transition-all duration-150 shrink-0"
                   title="Edit Profil Saya">
                    <i data-lucide="settings" class="w-3.5 h-3.5"></i>
                </a>
            </div>

            <!-- Elegant Gradient Divider -->
            <div class="h-px bg-gradient-to-r from-transparent via-white/20 to-transparent mx-2"></div>

            <!-- MAIN & DASHBOARD -->
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-white/50 px-2 block mb-1">
                    MENU UTAMA
                </span>
                
                <div class="space-y-0.5">
                    <!-- Dashboard -->
                    @php
                        $isDashboardActive = request()->is(['dashboard', 'dashboard/*']);
                    @endphp
                    <a href="{{ route('dashboard.index') }}" 
                       class="w-full flex items-center justify-between px-2.5 py-1.5 rounded-lg text-[13px] transition duration-150 {{ $isDashboardActive ? 'bg-white/20 text-white font-semibold shadow-xs' : 'text-white/80 hover:bg-white/10 hover:text-white font-medium' }} group">
                        <div class="flex items-center gap-2.5">
                            <i data-lucide="layout-grid" class="w-4 h-4 {{ $isDashboardActive ? 'text-white' : 'text-white/70 group-hover:text-white' }}"></i>
                            <span>Dashboard</span>
                        </div>
                    </a>
                </div>
            </div>

            <!-- DATA MASTER -->
            @if (auth()->user()->hasAnyPermission([
                'karyawan.index',
                'departemen.index',
                'cabang.index',
                'cuti.index',
                'jabatan.index',
                'grup.index',
                'statuskawin.index',
                'statuskaryawan.index',
                'kategori_resign.index',
                'contohdata.index'
            ]))
            @php
                $isMasterOpen = request()->is([
                    'karyawan', 'karyawan/*',
                    'departemen', 'departemen/*',
                    'cabang', 'cabang/*',
                    'cuti', 'cuti/*',
                    'jabatan', 'jabatan/*',
                    'grup', 'grup/*',
                    'statuskawin', 'statuskawin/*',
                    'statuskaryawan', 'statuskaryawan/*',
                    'kategori-resign', 'kategori-resign/*',
                    'monitoring-master', 'monitoring-master/*'
                ]);
            @endphp
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-white/50 px-2 block mb-1">
                    DATA MASTER
                </span>
                
                <div x-data="{ open: {{ $isMasterOpen ? 'true' : 'false' }} }" class="space-y-0.5">
                    <button @click="open = !open" 
                            :class="open ? 'bg-white/15 text-white font-semibold' : 'text-white/80 hover:bg-white/10 hover:text-white font-medium'"
                            class="w-full flex items-center justify-between px-2.5 py-1.5 rounded-lg text-[13px] transition duration-150 group">
                        <div class="flex items-center gap-2.5">
                            <i data-lucide="database" class="w-4 h-4 text-white/70 group-hover:text-white"></i>
                            <span>Data Master</span>
                        </div>
                        <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-white/60 transition-transform duration-200" :class="open ? 'rotate-180 text-white' : 'text-white/60'"></i>
                    </button>
                    
                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="sidebar-tree-container space-y-0.5">
                        @can('karyawan.index')
                        <div class="sidebar-tree-branch">
                            <a href="{{ route('karyawan.index') }}" 
                               class="w-full flex items-center justify-between px-2.5 py-1 text-xs rounded-lg transition duration-150 {{ request()->is(['karyawan', 'karyawan/*']) ? 'bg-white/20 text-white font-semibold' : 'text-white/75 hover:text-white hover:bg-white/10 font-medium' }}">
                                <span>Karyawan</span>
                                @if(request()->is(['karyawan', 'karyawan/*']))
                                    <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-white/80"></i>
                                @endif
                            </a>
                        </div>
                        @endcan

                        @can('departemen.index')
                        <div class="sidebar-tree-branch">
                            <a href="{{ route('departemen.index') }}" 
                               class="w-full flex items-center justify-between px-2.5 py-1 text-xs rounded-lg transition duration-150 {{ request()->is(['departemen', 'departemen/*']) ? 'bg-white/20 text-white font-semibold' : 'text-white/75 hover:text-white hover:bg-white/10 font-medium' }}">
                                <span>Departemen</span>
                                @if(request()->is(['departemen', 'departemen/*']))
                                    <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-white/80"></i>
                                @endif
                            </a>
                        </div>
                        @endcan

                        @can('grup.index')
                        <div class="sidebar-tree-branch">
                            <a href="{{ route('grup.index') }}" 
                               class="w-full flex items-center justify-between px-2.5 py-1 text-xs rounded-lg transition duration-150 {{ request()->is(['grup', 'grup/*']) ? 'bg-white/20 text-white font-semibold' : 'text-white/75 hover:text-white hover:bg-white/10 font-medium' }}">
                                <span>Grup</span>
                                @if(request()->is(['grup', 'grup/*']))
                                    <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-white/80"></i>
                                @endif
                            </a>
                        </div>
                        @endcan

                        @can('jabatan.index')
                        <div class="sidebar-tree-branch">
                            <a href="{{ route('jabatan.index') }}" 
                               class="w-full flex items-center justify-between px-2.5 py-1 text-xs rounded-lg transition duration-150 {{ request()->is(['jabatan', 'jabatan/*']) ? 'bg-white/20 text-white font-semibold' : 'text-white/75 hover:text-white hover:bg-white/10 font-medium' }}">
                                <span>Jabatan</span>
                                @if(request()->is(['jabatan', 'jabatan/*']))
                                    <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-white/80"></i>
                                @endif
                            </a>
                        </div>
                        @endcan

                        @can('cabang.index')
                        <div class="sidebar-tree-branch">
                            <a href="{{ route('cabang.index') }}" 
                               class="w-full flex items-center justify-between px-2.5 py-1 text-xs rounded-lg transition duration-150 {{ request()->is(['cabang', 'cabang/*']) ? 'bg-white/20 text-white font-semibold' : 'text-white/75 hover:text-white hover:bg-white/10 font-medium' }}">
                                <span>Cabang</span>
                                @if(request()->is(['cabang', 'cabang/*']))
                                    <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-white/80"></i>
                                @endif
                            </a>
                        </div>
                        @endcan

                        @can('cuti.index')
                        <div class="sidebar-tree-branch">
                            <a href="{{ route('cuti.index') }}" 
                               class="w-full flex items-center justify-between px-2.5 py-1 text-xs rounded-lg transition duration-150 {{ request()->is(['cuti', 'cuti/*']) ? 'bg-white/20 text-white font-semibold' : 'text-white/75 hover:text-white hover:bg-white/10 font-medium' }}">
                                <span>Cuti</span>
                                @if(request()->is(['cuti', 'cuti/*']))
                                    <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-white/80"></i>
                                @endif
                            </a>
                        </div>
                        @endcan

                        @can('statuskawin.index')
                        <div class="sidebar-tree-branch">
                            <a href="{{ route('statuskawin.index') }}" 
                               class="w-full flex items-center justify-between px-2.5 py-1 text-xs rounded-lg transition duration-150 {{ request()->is(['statuskawin', 'statuskawin/*']) ? 'bg-white/20 text-white font-semibold' : 'text-white/75 hover:text-white hover:bg-white/10 font-medium' }}">
                                <span>Status Kawin</span>
                                @if(request()->is(['statuskawin', 'statuskawin/*']))
                                    <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-white/80"></i>
                                @endif
                            </a>
                        </div>
                        @endcan

                        @can('statuskaryawan.index')
                        <div class="sidebar-tree-branch">
                            <a href="{{ route('statuskaryawan.index') }}" 
                               class="w-full flex items-center justify-between px-2.5 py-1 text-xs rounded-lg transition duration-150 {{ request()->is(['statuskaryawan', 'statuskaryawan/*']) ? 'bg-white/20 text-white font-semibold' : 'text-white/75 hover:text-white hover:bg-white/10 font-medium' }}">
                                <span>Status Karyawan</span>
                                @if(request()->is(['statuskaryawan', 'statuskaryawan/*']))
                                    <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-white/80"></i>
                                @endif
                            </a>
                        </div>
                        @endcan

                        @if(\Illuminate\Support\Facades\Route::has('kategori_resign.index'))
                        @can('kategori_resign.index')
                        <div class="sidebar-tree-branch">
                            <a href="{{ route('kategori_resign.index') }}" 
                               class="w-full flex items-center justify-between px-2.5 py-1 text-xs rounded-lg transition duration-150 {{ request()->is(['kategori-resign', 'kategori-resign/*']) ? 'bg-white/20 text-white font-semibold' : 'text-white/75 hover:text-white hover:bg-white/10 font-medium' }}">
                                <span>Kategori Resign</span>
                                @if(request()->is(['kategori-resign', 'kategori-resign/*']))
                                    <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-white/80"></i>
                                @endif
                            </a>
                        </div>
                        @endcan
                        @endif

                        @can('presensi.index')
                        <div class="sidebar-tree-branch">
                            <a href="{{ route('monitoring-master.index') }}" 
                               class="w-full flex items-center justify-between px-2.5 py-1 text-xs rounded-lg transition duration-150 {{ request()->is(['monitoring-master', 'monitoring-master/*']) ? 'bg-white/20 text-white font-semibold' : 'text-white/75 hover:text-white hover:bg-white/10 font-medium' }}">
                                <span>Monitoring Master</span>
                                @if(request()->is(['monitoring-master', 'monitoring-master/*']))
                                    <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-white/80"></i>
                                @endif
                            </a>
                        </div>
                        @endcan
                    </div>
                </div>
            </div>
            @endif

            <!-- PRESENSI & OPERASIONAL -->
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-white/50 px-2 block mb-1">
                    PRESENSI & OPERASIONAL
                </span>
                
                <div class="space-y-0.5">
                    <!-- Presensi Dropdown -->
                    @if (auth()->user()->hasAnyPermission(['presensi.index', 'trackingpresensi.index']))
                    @php
                        $isPresensiOpen = request()->is(['presensi', 'presensi/*', 'trackingpresensi', 'trackingpresensi/*', 'monitoring-lokasi', 'monitoring-lokasi/*']);
                    @endphp
                    <div x-data="{ open: {{ $isPresensiOpen ? 'true' : 'false' }} }" class="space-y-0.5">
                        <button @click="open = !open" 
                                :class="open ? 'bg-white/15 text-white font-semibold' : 'text-white/80 hover:bg-white/10 hover:text-white font-medium'"
                                class="w-full flex items-center justify-between px-2.5 py-1.5 rounded-lg text-[13px] transition duration-150 group">
                            <div class="flex items-center gap-2.5">
                                <i data-lucide="map-pin" class="w-4 h-4 text-white/70 group-hover:text-white"></i>
                                <span>Presensi</span>
                            </div>
                            <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-white/60 transition-transform duration-200" :class="open ? 'rotate-180 text-white' : 'text-white/60'"></i>
                        </button>
                        
                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="sidebar-tree-container space-y-0.5">
                            @can('presensi.index')
                            <div class="sidebar-tree-branch">
                                <a href="{{ route('presensi.index') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1 text-xs rounded-lg transition duration-150 {{ request()->is(['presensi', 'presensi/*']) ? 'bg-white/20 text-white font-semibold' : 'text-white/75 hover:text-white hover:bg-white/10 font-medium' }}">
                                    <span>Monitoring Presensi</span>
                                    @if(request()->is(['presensi', 'presensi/*']))
                                        <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-white/80"></i>
                                    @endif
                                </a>
                            </div>
                            @endcan

                            @can('trackingpresensi.index')
                            <div class="sidebar-tree-branch">
                                <a href="{{ route('trackingpresensi.index') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1 text-xs rounded-lg transition duration-150 {{ request()->is(['trackingpresensi', 'trackingpresensi/*']) ? 'bg-white/20 text-white font-semibold' : 'text-white/75 hover:text-white hover:bg-white/10 font-medium' }}">
                                    <span>Tracking Presensi</span>
                                    @if(request()->is(['trackingpresensi', 'trackingpresensi/*']))
                                        <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-white/80"></i>
                                    @endif
                                </a>
                            </div>
                            @endcan

                            @can('presensi.index')
                            <div class="sidebar-tree-branch">
                                <a href="{{ route('monitoring-lokasi.index') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1 text-xs rounded-lg transition duration-150 {{ request()->is(['monitoring-lokasi', 'monitoring-lokasi/*']) ? 'bg-white/20 text-white font-semibold' : 'text-white/75 hover:text-white hover:bg-white/10 font-medium' }}">
                                    <span>Monitoring Lokasi</span>
                                    @if(request()->is(['monitoring-lokasi', 'monitoring-lokasi/*']))
                                        <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-white/80"></i>
                                    @endif
                                </a>
                            </div>
                            @endcan
                        </div>
                    </div>
                    @endif

                    <!-- Checklist Toko Dropdown -->
                    @if (auth()->user()->hasRole(['super admin']) || auth()->user()->hasAnyPermission(['presensi.index']))
                    @php
                        $isChecklistOpen = request()->is(['admin/checklist', 'admin/checklist/*']);
                    @endphp
                    <div x-data="{ open: {{ $isChecklistOpen ? 'true' : 'false' }} }" class="space-y-0.5">
                        <button @click="open = !open" 
                                :class="open ? 'bg-white/15 text-white font-semibold' : 'text-white/80 hover:bg-white/10 hover:text-white font-medium'"
                                class="w-full flex items-center justify-between px-2.5 py-1.5 rounded-lg text-[13px] transition duration-150 group">
                            <div class="flex items-center gap-2.5">
                                <i data-lucide="clipboard-check" class="w-4 h-4 text-white/70 group-hover:text-white"></i>
                                <span>Checklist Toko</span>
                            </div>
                            <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-white/60 transition-transform duration-200" :class="open ? 'rotate-180 text-white' : 'text-white/60'"></i>
                        </button>
                        
                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="sidebar-tree-container space-y-0.5">
                            <div class="sidebar-tree-branch">
                                <a href="{{ route('admin.checklist.monitoring.index') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1 text-xs rounded-lg transition duration-150 {{ request()->is(['admin/checklist/monitoring', 'admin/checklist/monitoring/*']) ? 'bg-white/20 text-white font-semibold' : 'text-white/75 hover:text-white hover:bg-white/10 font-medium' }}">
                                    <span>Monitoring Checklist</span>
                                    @if(request()->is(['admin/checklist/monitoring', 'admin/checklist/monitoring/*']))
                                        <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-white/80"></i>
                                    @endif
                                </a>
                            </div>
                            <div class="sidebar-tree-branch">
                                <a href="{{ route('admin.checklist.checkpoints.index') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1 text-xs rounded-lg transition duration-150 {{ request()->is(['admin/checklist/checkpoints', 'admin/checklist/checkpoints/*']) ? 'bg-white/20 text-white font-semibold' : 'text-white/75 hover:text-white hover:bg-white/10 font-medium' }}">
                                    <span>Master Checkpoint</span>
                                    @if(request()->is(['admin/checklist/checkpoints', 'admin/checklist/checkpoints/*']))
                                        <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-white/80"></i>
                                    @endif
                                </a>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Pengajuan (Izin / Cuti / Sakit / Dinas / Koreksi) -->
                    @if (auth()->user()->hasAnyPermission(['izinabsen.index', 'izinsakit.index', 'izincuti.index', 'izindinas.index', 'koreksi.index']))
                    @php
                        $isPengajuanActive = request()->is(['izinabsen', 'izinabsen/*', 'izinsakit', 'izinsakit/*', 'izincuti', 'izincuti/*', 'izindinas', 'izindinas/*', 'koreksi', 'koreksi/*']);
                    @endphp
                    <a href="{{ route('izinabsen.index') }}" 
                       class="flex items-center justify-between px-2.5 py-1.5 rounded-lg text-[13px] transition duration-150 {{ $isPengajuanActive ? 'bg-white/20 text-white font-semibold shadow-xs' : 'text-white/80 hover:bg-white/10 hover:text-white font-medium' }} group">
                        <div class="flex items-center gap-2.5">
                            <i data-lucide="folder-check" class="w-4 h-4 {{ $isPengajuanActive ? 'text-white' : 'text-white/70 group-hover:text-white' }}"></i>
                            <span>Pengajuan</span>
                        </div>
                        @if (!empty($notifikasi_ajuan_absen))
                            <span class="px-1.5 py-0.5 text-[10px] font-bold text-white bg-rose-500 rounded-full leading-none">{{ $notifikasi_ajuan_absen }}</span>
                        @endif
                    </a>
                    @endif

                    <!-- Jadwal Kerja Dropdown -->
                    @if (auth()->user()->hasAnyPermission([
                        'roster.index',
                        'penjadwalancard.index',
                        'jadwalperiode.index',
                        'shiftpattern.index',
                        'ajuanjadwal.index',
                        'minpersonelshift.index'
                    ]))
                    @php
                        $isJadwalOpen = request()->is([
                            'rosterboard', 'rosterboard/*',
                            'penjadwalancard', 'penjadwalancard/*',
                            'jadwalperiode', 'jadwalperiode/*',
                            'shiftpattern', 'shiftpattern/*',
                            'ajuanjadwal', 'ajuanjadwal/*',
                            'minpersonel', 'minpersonel/*'
                        ]);
                    @endphp
                    <div x-data="{ open: {{ $isJadwalOpen ? 'true' : 'false' }} }" class="space-y-0.5">
                        <button @click="open = !open" 
                                :class="open ? 'bg-white/15 text-white font-semibold' : 'text-white/80 hover:bg-white/10 hover:text-white font-medium'"
                                class="w-full flex items-center justify-between px-2.5 py-1.5 rounded-lg text-[13px] transition duration-150 group">
                            <div class="flex items-center gap-2.5">
                                <i data-lucide="calendar-clock" class="w-4 h-4 text-white/70 group-hover:text-white"></i>
                                <span>Jadwal Kerja</span>
                            </div>
                            <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-white/60 transition-transform duration-200" :class="open ? 'rotate-180 text-white' : 'text-white/60'"></i>
                        </button>
                        
                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="sidebar-tree-container space-y-0.5">
                            @if(\Illuminate\Support\Facades\Route::has('rosterboard.index'))
                            @can('roster.index')
                            <div class="sidebar-tree-branch">
                                <a href="{{ route('rosterboard.index') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1 text-xs rounded-lg transition duration-150 {{ request()->is(['rosterboard', 'rosterboard/*']) ? 'bg-white/20 text-white font-semibold' : 'text-white/75 hover:text-white hover:bg-white/10 font-medium' }}">
                                    <span>Roster Board</span>
                                    @if(request()->is(['rosterboard', 'rosterboard/*']))
                                        <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-white/80"></i>
                                    @endif
                                </a>
                            </div>
                            @endcan
                            @endif

                            @if(\Illuminate\Support\Facades\Route::has('penjadwalancard.index'))
                            @can('penjadwalancard.index')
                            <div class="sidebar-tree-branch">
                                <a href="{{ route('penjadwalancard.index') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1 text-xs rounded-lg transition duration-150 {{ request()->is(['penjadwalancard', 'penjadwalancard/*']) ? 'bg-white/20 text-white font-semibold' : 'text-white/75 hover:text-white hover:bg-white/10 font-medium' }}">
                                    <span>Penjadwalan Card (Shift)</span>
                                    @if(request()->is(['penjadwalancard', 'penjadwalancard/*']))
                                        <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-white/80"></i>
                                    @endif
                                </a>
                            </div>
                            @endcan
                            @endif

                            @if(\Illuminate\Support\Facades\Route::has('jadwalperiode.index'))
                            @can('jadwalperiode.index')
                            <div class="sidebar-tree-branch">
                                <a href="{{ route('jadwalperiode.index') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1 text-xs rounded-lg transition duration-150 {{ request()->is(['jadwalperiode', 'jadwalperiode/*']) ? 'bg-white/20 text-white font-semibold' : 'text-white/75 hover:text-white hover:bg-white/10 font-medium' }}">
                                    <span>Periode Jadwal</span>
                                    @if(request()->is(['jadwalperiode', 'jadwalperiode/*']))
                                        <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-white/80"></i>
                                    @endif
                                </a>
                            </div>
                            @endcan
                            @endif

                            @if(\Illuminate\Support\Facades\Route::has('shiftpattern.index'))
                            @can('shiftpattern.index')
                            <div class="sidebar-tree-branch">
                                <a href="{{ route('shiftpattern.index') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1 text-xs rounded-lg transition duration-150 {{ request()->is(['shiftpattern', 'shiftpattern/*']) ? 'bg-white/20 text-white font-semibold' : 'text-white/75 hover:text-white hover:bg-white/10 font-medium' }}">
                                    <span>Pola Shift</span>
                                    @if(request()->is(['shiftpattern', 'shiftpattern/*']))
                                        <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-white/80"></i>
                                    @endif
                                </a>
                            </div>
                            @endcan
                            @endif

                            @can('ajuanjadwal.index')
                            <div class="sidebar-tree-branch">
                                <a href="{{ route('ajuanjadwal.index') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1 text-xs rounded-lg transition duration-150 {{ request()->is(['ajuanjadwal', 'ajuanjadwal/*']) ? 'bg-white/20 text-white font-semibold' : 'text-white/75 hover:text-white hover:bg-white/10 font-medium' }}">
                                    <span>Ajuan Jadwal</span>
                                    @if(request()->is(['ajuanjadwal', 'ajuanjadwal/*']))
                                        <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-white/80"></i>
                                    @endif
                                </a>
                            </div>
                            @endcan

                            @if(\Illuminate\Support\Facades\Route::has('minpersonel.index'))
                            @can('minpersonelshift.index')
                            <div class="sidebar-tree-branch">
                                <a href="{{ route('minpersonel.index') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1 text-xs rounded-lg transition duration-150 {{ request()->is(['minpersonel', 'minpersonel/*']) ? 'bg-white/20 text-white font-semibold' : 'text-white/75 hover:text-white hover:bg-white/10 font-medium' }}">
                                    <span>Min. Personel Shift</span>
                                    @if(request()->is(['minpersonel', 'minpersonel/*']))
                                        <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-white/80"></i>
                                    @endif
                                </a>
                            </div>
                            @endcan
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Lembur -->
                    @if (auth()->user()->hasAnyPermission(['lembur.index']))
                    @php
                        $isLemburActive = request()->is(['lembur', 'lembur/*']);
                    @endphp
                    <a href="{{ route('lembur.index') }}" 
                       class="flex items-center justify-between px-2.5 py-1.5 rounded-lg text-[13px] transition duration-150 {{ $isLemburActive ? 'bg-white/20 text-white font-semibold shadow-xs' : 'text-white/80 hover:bg-white/10 hover:text-white font-medium' }} group">
                        <div class="flex items-center gap-2.5">
                            <i data-lucide="clock" class="w-4 h-4 {{ $isLemburActive ? 'text-white' : 'text-white/70 group-hover:text-white' }}"></i>
                            <span>Lembur</span>
                        </div>
                        @if (!empty($notifikasi_lembur))
                            <span class="px-1.5 py-0.5 text-[10px] font-bold text-white bg-rose-500 rounded-full leading-none">{{ $notifikasi_lembur }}</span>
                        @endif
                    </a>
                    @endif

                    <!-- Kunjungan Dropdown -->
                    @can('kunjungan.index')
                    @php
                        $isKunjunganOpen = request()->is(['kunjungan', 'kunjungan/*', 'tracking-kunjungan', 'tracking-kunjungan/*']);
                    @endphp
                    <div x-data="{ open: {{ $isKunjunganOpen ? 'true' : 'false' }} }" class="space-y-0.5">
                        <button @click="open = !open" 
                                :class="open ? 'bg-white/15 text-white font-semibold' : 'text-white/80 hover:bg-white/10 hover:text-white font-medium'"
                                class="w-full flex items-center justify-between px-2.5 py-1.5 rounded-lg text-[13px] transition duration-150 group">
                            <div class="flex items-center gap-2.5">
                                <i data-lucide="navigation" class="w-4 h-4 text-white/70 group-hover:text-white"></i>
                                <span>Kunjungan</span>
                            </div>
                            <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-white/60 transition-transform duration-200" :class="open ? 'rotate-180 text-white' : 'text-white/60'"></i>
                        </button>
                        
                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="sidebar-tree-container space-y-0.5">
                            <div class="sidebar-tree-branch">
                                <a href="{{ route('kunjungan.index') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1 text-xs rounded-lg transition duration-150 {{ request()->is(['kunjungan', 'kunjungan/*']) ? 'bg-white/20 text-white font-semibold' : 'text-white/75 hover:text-white hover:bg-white/10 font-medium' }}">
                                    <span>Data Kunjungan</span>
                                    @if(request()->is(['kunjungan', 'kunjungan/*']))
                                        <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-white/80"></i>
                                    @endif
                                </a>
                            </div>
                            <div class="sidebar-tree-branch">
                                <a href="{{ route('tracking-kunjungan.index') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1 text-xs rounded-lg transition duration-150 {{ request()->is(['tracking-kunjungan', 'tracking-kunjungan/*']) ? 'bg-white/20 text-white font-semibold' : 'text-white/75 hover:text-white hover:bg-white/10 font-medium' }}">
                                    <span>Tracking Kunjungan</span>
                                    @if(request()->is(['tracking-kunjungan', 'tracking-kunjungan/*']))
                                        <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-white/80"></i>
                                    @endif
                                </a>
                            </div>
                        </div>
                    </div>
                    @endcan

                    <!-- Aktivitas Karyawan -->
                    @can('aktivitaskaryawan.index')
                    @php
                        $isAktivitasActive = request()->is(['aktivitaskaryawan', 'aktivitaskaryawan/*']);
                    @endphp
                    <a href="{{ route('aktivitaskaryawan.index') }}" 
                       class="flex items-center justify-between px-2.5 py-1.5 rounded-lg text-[13px] transition duration-150 {{ $isAktivitasActive ? 'bg-white/20 text-white font-semibold shadow-xs' : 'text-white/80 hover:bg-white/10 hover:text-white font-medium' }} group">
                        <div class="flex items-center gap-2.5">
                            <i data-lucide="activity" class="w-4 h-4 {{ $isAktivitasActive ? 'text-white' : 'text-white/70 group-hover:text-white' }}"></i>
                            <span>Aktivitas Karyawan</span>
                        </div>
                    </a>
                    @endcan

                    <!-- Pelanggaran -->
                    @can('pelanggaran.index')
                    @php
                        $isPelanggaranActive = request()->is(['pelanggaran', 'pelanggaran/*']);
                    @endphp
                    <a href="{{ route('pelanggaran.index') }}" 
                       class="flex items-center justify-between px-2.5 py-1.5 rounded-lg text-[13px] transition duration-150 {{ $isPelanggaranActive ? 'bg-white/20 text-white font-semibold shadow-xs' : 'text-white/80 hover:bg-white/10 hover:text-white font-medium' }} group">
                        <div class="flex items-center gap-2.5">
                            <i data-lucide="alert-triangle" class="w-4 h-4 {{ $isPelanggaranActive ? 'text-white' : 'text-white/70 group-hover:text-white' }}"></i>
                            <span>Pelanggaran</span>
                        </div>
                    </a>
                    @endcan

                    <!-- Pengumuman -->
                    @can('pengumuman.index')
                    @php
                        $isPengumumanActive = request()->is(['pengumuman', 'pengumuman/*']);
                    @endphp
                    <a href="{{ route('pengumuman.index') }}" 
                       class="flex items-center justify-between px-2.5 py-1.5 rounded-lg text-[13px] transition duration-150 {{ $isPengumumanActive ? 'bg-white/20 text-white font-semibold shadow-xs' : 'text-white/80 hover:bg-white/10 hover:text-white font-medium' }} group">
                        <div class="flex items-center gap-2.5">
                            <i data-lucide="bell" class="w-4 h-4 {{ $isPengumumanActive ? 'text-white' : 'text-white/70 group-hover:text-white' }}"></i>
                            <span>Pengumuman</span>
                        </div>
                    </a>
                    @endcan
                </div>
            </div>

            <!-- PAYROLL & FINANSIAL -->
            @if (auth()->user()->hasAnyPermission([
                'gajipokok.index',
                'jenistunjangan.index',
                'tunjangan.index',
                'bpjskesehatan.index',
                'bpjstenagakerja.index',
                'penyesuaiangaji.index',
                'slipgaji.index',
                'periodegaji.index',
                'pinjaman.index',
                'reimbursement.index'
            ]))
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-white/50 px-2 block mb-1">
                    PAYROLL & FINANSIAL
                </span>
                
                <div class="space-y-0.5">
                    <!-- Payroll Dropdown -->
                    @if (auth()->user()->hasAnyPermission([
                        'gajipokok.index',
                        'jenistunjangan.index',
                        'tunjangan.index',
                        'bpjskesehatan.index',
                        'bpjstenagakerja.index',
                        'penyesuaiangaji.index',
                        'slipgaji.index',
                        'periodegaji.index',
                    ]))
                    @php
                        $isPayrollOpen = request()->is([
                            'gajipokok', 'gajipokok/*',
                            'jenistunjangan', 'jenistunjangan/*',
                            'tunjangan', 'tunjangan/*',
                            'bpjskesehatan', 'bpjskesehatan/*',
                            'bpjstenagakerja', 'bpjstenagakerja/*',
                            'penyesuaiangaji', 'penyesuaiangaji/*',
                            'slipgaji', 'slipgaji/*',
                            'periodegaji', 'periodegaji/*'
                        ]);
                    @endphp
                    <div x-data="{ open: {{ $isPayrollOpen ? 'true' : 'false' }} }" class="space-y-0.5">
                        <button @click="open = !open" 
                                :class="open ? 'bg-white/15 text-white font-semibold' : 'text-white/80 hover:bg-white/10 hover:text-white font-medium'"
                                class="w-full flex items-center justify-between px-2.5 py-1.5 rounded-lg text-[13px] transition duration-150 group">
                            <div class="flex items-center gap-2.5">
                                <i data-lucide="wallet" class="w-4 h-4 text-white/70 group-hover:text-white"></i>
                                <span>Payroll</span>
                            </div>
                            <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-white/60 transition-transform duration-200" :class="open ? 'rotate-180 text-white' : 'text-white/60'"></i>
                        </button>
                        
                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="sidebar-tree-container space-y-0.5">
                            @can('jenistunjangan.index')
                            <div class="sidebar-tree-branch">
                                <a href="{{ route('jenistunjangan.index') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1 text-xs rounded-lg transition duration-150 {{ request()->is(['jenistunjangan', 'jenistunjangan/*']) ? 'bg-white/20 text-white font-semibold' : 'text-white/75 hover:text-white hover:bg-white/10 font-medium' }}">
                                    <span>Jenis Tunjangan</span>
                                    @if(request()->is(['jenistunjangan', 'jenistunjangan/*']))
                                        <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-white/80"></i>
                                    @endif
                                </a>
                            </div>
                            @endcan

                            @can('gajipokok.index')
                            <div class="sidebar-tree-branch">
                                <a href="{{ route('gajipokok.index') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1 text-xs rounded-lg transition duration-150 {{ request()->is(['gajipokok', 'gajipokok/*']) ? 'bg-white/20 text-white font-semibold' : 'text-white/75 hover:text-white hover:bg-white/10 font-medium' }}">
                                    <span>Gaji Pokok</span>
                                    @if(request()->is(['gajipokok', 'gajipokok/*']))
                                        <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-white/80"></i>
                                    @endif
                                </a>
                            </div>
                            @endcan

                            @can('tunjangan.index')
                            <div class="sidebar-tree-branch">
                                <a href="{{ route('tunjangan.index') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1 text-xs rounded-lg transition duration-150 {{ request()->is(['tunjangan', 'tunjangan/*']) ? 'bg-white/20 text-white font-semibold' : 'text-white/75 hover:text-white hover:bg-white/10 font-medium' }}">
                                    <span>Tunjangan</span>
                                    @if(request()->is(['tunjangan', 'tunjangan/*']))
                                        <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-white/80"></i>
                                    @endif
                                </a>
                            </div>
                            @endcan

                            @can('bpjskesehatan.index')
                            <div class="sidebar-tree-branch">
                                <a href="{{ route('bpjskesehatan.index') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1 text-xs rounded-lg transition duration-150 {{ request()->is(['bpjskesehatan', 'bpjskesehatan/*']) ? 'bg-white/20 text-white font-semibold' : 'text-white/75 hover:text-white hover:bg-white/10 font-medium' }}">
                                    <span>BPJS Kesehatan</span>
                                    @if(request()->is(['bpjskesehatan', 'bpjskesehatan/*']))
                                        <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-white/80"></i>
                                    @endif
                                </a>
                            </div>
                            @endcan

                            @can('bpjstenagakerja.index')
                            <div class="sidebar-tree-branch">
                                <a href="{{ route('bpjstenagakerja.index') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1 text-xs rounded-lg transition duration-150 {{ request()->is(['bpjstenagakerja', 'bpjstenagakerja/*']) ? 'bg-white/20 text-white font-semibold' : 'text-white/75 hover:text-white hover:bg-white/10 font-medium' }}">
                                    <span>BPJS Tenaga Kerja</span>
                                    @if(request()->is(['bpjstenagakerja', 'bpjstenagakerja/*']))
                                        <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-white/80"></i>
                                    @endif
                                </a>
                            </div>
                            @endcan

                            @can('penyesuaiangaji.index')
                            <div class="sidebar-tree-branch">
                                <a href="{{ route('penyesuaiangaji.index') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1 text-xs rounded-lg transition duration-150 {{ request()->is(['penyesuaiangaji', 'penyesuaiangaji/*']) ? 'bg-white/20 text-white font-semibold' : 'text-white/75 hover:text-white hover:bg-white/10 font-medium' }}">
                                    <span>Penyesuaian Gaji</span>
                                    @if(request()->is(['penyesuaiangaji', 'penyesuaiangaji/*']))
                                        <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-white/80"></i>
                                    @endif
                                </a>
                            </div>
                            @endcan

                            @can('slipgaji.index')
                            <div class="sidebar-tree-branch">
                                <a href="{{ route('slipgaji.index') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1 text-xs rounded-lg transition duration-150 {{ request()->is(['slipgaji', 'slipgaji/*']) ? 'bg-white/20 text-white font-semibold' : 'text-white/75 hover:text-white hover:bg-white/10 font-medium' }}">
                                    <span>Slip Gaji</span>
                                    @if(request()->is(['slipgaji', 'slipgaji/*']))
                                        <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-white/80"></i>
                                    @endif
                                </a>
                            </div>
                            @endcan

                            @if(\Illuminate\Support\Facades\Route::has('periodegaji.index'))
                            @can('periodegaji.index')
                            <div class="sidebar-tree-branch">
                                <a href="{{ route('periodegaji.index') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1 text-xs rounded-lg transition duration-150 {{ request()->is(['periodegaji', 'periodegaji/*']) ? 'bg-white/20 text-white font-semibold' : 'text-white/75 hover:text-white hover:bg-white/10 font-medium' }}">
                                    <span>Periode Gaji</span>
                                    @if(request()->is(['periodegaji', 'periodegaji/*']))
                                        <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-white/80"></i>
                                    @endif
                                </a>
                            </div>
                            @endcan
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Pinjaman Karyawan -->
                    @can('pinjaman.index')
                    @php
                        $isPinjamanActive = request()->is(['pinjaman', 'pinjaman/*']);
                    @endphp
                    <a href="{{ route('pinjaman.index') }}" 
                       class="flex items-center justify-between px-2.5 py-1.5 rounded-lg text-[13px] transition duration-150 {{ $isPinjamanActive ? 'bg-white/20 text-white font-semibold shadow-xs' : 'text-white/80 hover:bg-white/10 hover:text-white font-medium' }} group">
                        <div class="flex items-center gap-2.5">
                            <i data-lucide="credit-card" class="w-4 h-4 {{ $isPinjamanActive ? 'text-white' : 'text-white/70 group-hover:text-white' }}"></i>
                            <span>Pinjaman Karyawan</span>
                        </div>
                    </a>
                    @endcan

                    <!-- Reimbursement Dropdown -->
                    @if(\Illuminate\Support\Facades\Route::has('reimbursement.index'))
                    @can('reimbursement.index')
                    @php
                        $isReimburseOpen = request()->is(['reimbursement', 'reimbursement/*', 'jenisreimbursement', 'jenisreimbursement/*']);
                    @endphp
                    <div x-data="{ open: {{ $isReimburseOpen ? 'true' : 'false' }} }" class="space-y-0.5">
                        <button @click="open = !open" 
                                :class="open ? 'bg-white/15 text-white font-semibold' : 'text-white/80 hover:bg-white/10 hover:text-white font-medium'"
                                class="w-full flex items-center justify-between px-2.5 py-1.5 rounded-lg text-[13px] transition duration-150 group">
                            <div class="flex items-center gap-2.5">
                                <i data-lucide="receipt" class="w-4 h-4 text-white/70 group-hover:text-white"></i>
                                <span>Reimbursement</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                @if (!empty($notifikasi_reimbursement))
                                    <span class="px-1.5 py-0.5 text-[10px] font-bold text-white bg-rose-500 rounded-full leading-none">{{ $notifikasi_reimbursement }}</span>
                                @endif
                                <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-white/60 transition-transform duration-200" :class="open ? 'rotate-180 text-white' : 'text-white/60'"></i>
                            </div>
                        </button>
                        
                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="sidebar-tree-container space-y-0.5">
                            @can('jenisreimbursement.index')
                            <div class="sidebar-tree-branch">
                                <a href="{{ route('jenisreimbursement.index') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1 text-xs rounded-lg transition duration-150 {{ request()->is(['jenisreimbursement', 'jenisreimbursement/*']) ? 'bg-white/20 text-white font-semibold' : 'text-white/75 hover:text-white hover:bg-white/10 font-medium' }}">
                                    <span>Jenis & Aturan</span>
                                    @if(request()->is(['jenisreimbursement', 'jenisreimbursement/*']))
                                        <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-white/80"></i>
                                    @endif
                                </a>
                            </div>
                            @endcan

                            <div class="sidebar-tree-branch">
                                <a href="{{ route('reimbursement.index') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1 text-xs rounded-lg transition duration-150 {{ request()->is(['reimbursement', 'reimbursement/*']) ? 'bg-white/20 text-white font-semibold' : 'text-white/75 hover:text-white hover:bg-white/10 font-medium' }}">
                                    <span>Pengajuan</span>
                                    <div class="flex items-center gap-1.5">
                                        @if (!empty($notifikasi_reimbursement))
                                            <span class="px-1.5 py-0.5 text-[10px] font-bold text-white bg-rose-500 rounded-full leading-none">{{ $notifikasi_reimbursement }}</span>
                                        @endif
                                        @if(request()->is(['reimbursement', 'reimbursement/*']))
                                            <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-white/80"></i>
                                        @endif
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                    @endcan
                    @endif
                </div>
            </div>
            @endif

            <!-- PERFORMANCE & PROJECT MANAGEMENT -->
            @if (auth()->user()->hasAnyPermission([
                'kpi.period.index',
                'kpi.indicator.index',
                'kpi.employee.index',
                'project.index',
                'projectcategory.index',
                'kontrak.index',
                'mutasi.index'
            ]) || (request()->is(['resign', 'resign/*']) && \Illuminate\Support\Facades\Route::has('resign.index')))
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-white/50 px-2 block mb-1">
                    SDM & MANAJEMEN
                </span>
                
                <div class="space-y-0.5">
                    <!-- Manajemen KPI Dropdown -->
                    @if (auth()->user()->hasAnyPermission(['kpi.period.index', 'kpi.indicator.index', 'kpi.employee.index']))
                    @php
                        $isKpiOpen = request()->is(['kpi', 'kpi/*']);
                    @endphp
                    <div x-data="{ open: {{ $isKpiOpen ? 'true' : 'false' }} }" class="space-y-0.5">
                        <button @click="open = !open" 
                                :class="open ? 'bg-white/15 text-white font-semibold' : 'text-white/80 hover:bg-white/10 hover:text-white font-medium'"
                                class="w-full flex items-center justify-between px-2.5 py-1.5 rounded-lg text-[13px] transition duration-150 group">
                            <div class="flex items-center gap-2.5">
                                <i data-lucide="bar-chart-2" class="w-4 h-4 text-white/70 group-hover:text-white"></i>
                                <span>Manajemen KPI</span>
                            </div>
                            <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-white/60 transition-transform duration-200" :class="open ? 'rotate-180 text-white' : 'text-white/60'"></i>
                        </button>
                        
                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="sidebar-tree-container space-y-0.5">
                            @can('kpi.period.index')
                            <div class="sidebar-tree-branch">
                                <a href="{{ route('kpi.periods.index') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1 text-xs rounded-lg transition duration-150 {{ request()->is(['kpi/periods', 'kpi/periods/*']) ? 'bg-white/20 text-white font-semibold' : 'text-white/75 hover:text-white hover:bg-white/10 font-medium' }}">
                                    <span>Periode Penilaian</span>
                                    @if(request()->is(['kpi/periods', 'kpi/periods/*']))
                                        <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-white/80"></i>
                                    @endif
                                </a>
                            </div>
                            @endcan

                            @can('kpi.indicator.index')
                            <div class="sidebar-tree-branch">
                                <a href="{{ route('kpi.indicators.index') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1 text-xs rounded-lg transition duration-150 {{ request()->is(['kpi/indicators', 'kpi/indicators/*']) ? 'bg-white/20 text-white font-semibold' : 'text-white/75 hover:text-white hover:bg-white/10 font-medium' }}">
                                    <span>Indikator KPI</span>
                                    @if(request()->is(['kpi/indicators', 'kpi/indicators/*']))
                                        <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-white/80"></i>
                                    @endif
                                </a>
                            </div>
                            @endcan

                            @can('kpi.employee.index')
                            <div class="sidebar-tree-branch">
                                <a href="{{ route('kpi.transactions.index') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1 text-xs rounded-lg transition duration-150 {{ request()->is(['kpi/transactions', 'kpi/transactions/*']) ? 'bg-white/20 text-white font-semibold' : 'text-white/75 hover:text-white hover:bg-white/10 font-medium' }}">
                                    <span>Input Penilaian</span>
                                    @if(request()->is(['kpi/transactions', 'kpi/transactions/*']))
                                        <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-white/80"></i>
                                    @endif
                                </a>
                            </div>
                            @endcan
                        </div>
                    </div>
                    @endif

                    <!-- Project Management Dropdown -->
                    @if (\Illuminate\Support\Facades\Route::has('project.index') && auth()->user()->hasAnyPermission(['project.index', 'projectcategory.index']))
                    @php
                        $isProjectOpen = request()->is(['project', 'project/*', 'projectcategory', 'projectcategory/*']);
                    @endphp
                    <div x-data="{ open: {{ $isProjectOpen ? 'true' : 'false' }} }" class="space-y-0.5">
                        <button @click="open = !open" 
                                :class="open ? 'bg-white/15 text-white font-semibold' : 'text-white/80 hover:bg-white/10 hover:text-white font-medium'"
                                class="w-full flex items-center justify-between px-2.5 py-1.5 rounded-lg text-[13px] transition duration-150 group">
                            <div class="flex items-center gap-2.5">
                                <i data-lucide="briefcase" class="w-4 h-4 text-white/70 group-hover:text-white"></i>
                                <span>Project Management</span>
                            </div>
                            <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-white/60 transition-transform duration-200" :class="open ? 'rotate-180 text-white' : 'text-white/60'"></i>
                        </button>
                        
                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="sidebar-tree-container space-y-0.5">
                            @can('project.index')
                            <div class="sidebar-tree-branch">
                                <a href="{{ route('project.index') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1 text-xs rounded-lg transition duration-150 {{ request()->is(['project', 'project/*']) ? 'bg-white/20 text-white font-semibold' : 'text-white/75 hover:text-white hover:bg-white/10 font-medium' }}">
                                    <span>Projects</span>
                                    @if(request()->is(['project', 'project/*']))
                                        <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-white/80"></i>
                                    @endif
                                </a>
                            </div>
                            @endcan

                            @can('projectcategory.index')
                            <div class="sidebar-tree-branch">
                                <a href="{{ route('projectcategory.index') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1 text-xs rounded-lg transition duration-150 {{ request()->is(['projectcategory', 'projectcategory/*']) ? 'bg-white/20 text-white font-semibold' : 'text-white/75 hover:text-white hover:bg-white/10 font-medium' }}">
                                    <span>Kategori Project</span>
                                    @if(request()->is(['projectcategory', 'projectcategory/*']))
                                        <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-white/80"></i>
                                    @endif
                                </a>
                            </div>
                            @endcan
                        </div>
                    </div>
                    @endif

                    <!-- Kontrak -->
                    @can('kontrak.index')
                    @php
                        $isKontrakActive = request()->is(['kontrak', 'kontrak/*']);
                    @endphp
                    <a href="{{ route('kontrak.index') }}" 
                       class="flex items-center justify-between px-2.5 py-1.5 rounded-lg text-[13px] transition duration-150 {{ $isKontrakActive ? 'bg-white/20 text-white font-semibold shadow-xs' : 'text-white/80 hover:bg-white/10 hover:text-white font-medium' }} group">
                        <div class="flex items-center gap-2.5">
                            <i data-lucide="file-badge" class="w-4 h-4 {{ $isKontrakActive ? 'text-white' : 'text-white/70 group-hover:text-white' }}"></i>
                            <span>Kontrak</span>
                        </div>
                    </a>
                    @endcan

                    <!-- Mutasi & Promosi -->
                    @can('mutasi.index')
                    @php
                        $isMutasiActive = request()->is(['mutasi', 'mutasi/*']);
                    @endphp
                    <a href="{{ route('mutasi.index') }}" 
                       class="flex items-center justify-between px-2.5 py-1.5 rounded-lg text-[13px] transition duration-150 {{ $isMutasiActive ? 'bg-white/20 text-white font-semibold shadow-xs' : 'text-white/80 hover:bg-white/10 hover:text-white font-medium' }} group">
                        <div class="flex items-center gap-2.5">
                            <i data-lucide="arrow-left-right" class="w-4 h-4 {{ $isMutasiActive ? 'text-white' : 'text-white/70 group-hover:text-white' }}"></i>
                            <span>Mutasi & Promosi</span>
                        </div>
                    </a>
                    @endcan

                    <!-- Resign Karyawan -->
                    @if(\Illuminate\Support\Facades\Route::has('resign.index'))
                    @php
                        $isResignActive = request()->is(['resign', 'resign/*']);
                    @endphp
                    <a href="{{ route('resign.index') }}" 
                       class="flex items-center justify-between px-2.5 py-1.5 rounded-lg text-[13px] transition duration-150 {{ $isResignActive ? 'bg-white/20 text-white font-semibold shadow-xs' : 'text-white/80 hover:bg-white/10 hover:text-white font-medium' }} group">
                        <div class="flex items-center gap-2.5">
                            <i data-lucide="user-x" class="w-4 h-4 {{ $isResignActive ? 'text-white' : 'text-white/70 group-hover:text-white' }}"></i>
                            <span>Resign Karyawan</span>
                        </div>
                    </a>
                    @endif
                </div>
            </div>
            @endif

            <!-- LAPORAN -->
            @if (auth()->user()->hasAnyPermission([
                'laporan.presensi',
                'laporan.kehadiran',
                'laporan.gaji',
                'laporan.cuti',
                'laporan.jadwal',
                'reimbursement.laporan',
                'pinjaman.laporan'
            ]))
            @php
                $isLaporanOpen = request()->is(['laporan', 'laporan/*']);
            @endphp
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-white/50 px-2 block mb-1">
                    LAPORAN
                </span>
                
                <div x-data="{ open: {{ $isLaporanOpen ? 'true' : 'false' }} }" class="space-y-0.5">
                    <button @click="open = !open" 
                            :class="open ? 'bg-white/15 text-white font-semibold' : 'text-white/80 hover:bg-white/10 hover:text-white font-medium'"
                            class="w-full flex items-center justify-between px-2.5 py-1.5 rounded-lg text-[13px] transition duration-150 group">
                        <div class="flex items-center gap-2.5">
                            <i data-lucide="file-pie-chart" class="w-4 h-4 text-white/70 group-hover:text-white"></i>
                            <span>Laporan</span>
                        </div>
                        <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-white/60 transition-transform duration-200" :class="open ? 'rotate-180 text-white' : 'text-white/60'"></i>
                    </button>
                    
                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="sidebar-tree-container space-y-0.5">
                        @can('laporan.presensi')
                        <div class="sidebar-tree-branch">
                            <a href="{{ route('laporan.presensi') }}" 
                               class="w-full flex items-center justify-between px-2.5 py-1 text-xs rounded-lg transition duration-150 {{ request()->is(['laporan/presensi']) ? 'bg-white/20 text-white font-semibold' : 'text-white/75 hover:text-white hover:bg-white/10 font-medium' }}">
                                <span>Laporan Presensi</span>
                                @if(request()->is(['laporan/presensi']))
                                    <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-white/80"></i>
                                @endif
                            </a>
                        </div>
                        @endcan

                        @can('laporan.kehadiran')
                        <div class="sidebar-tree-branch">
                            <a href="{{ route('laporan.kehadiran') }}" 
                               class="w-full flex items-center justify-between px-2.5 py-1 text-xs rounded-lg transition duration-150 {{ request()->is(['laporan/kehadiran']) ? 'bg-white/20 text-white font-semibold' : 'text-white/75 hover:text-white hover:bg-white/10 font-medium' }}">
                                <span>Laporan Kehadiran</span>
                                @if(request()->is(['laporan/kehadiran']))
                                    <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-white/80"></i>
                                @endif
                            </a>
                        </div>
                        @endcan

                        @if(\Illuminate\Support\Facades\Route::has('laporan.gaji'))
                        @can('laporan.gaji')
                        <div class="sidebar-tree-branch">
                            <a href="{{ route('laporan.gaji') }}" 
                               class="w-full flex items-center justify-between px-2.5 py-1 text-xs rounded-lg transition duration-150 {{ request()->is(['laporan/gaji']) ? 'bg-white/20 text-white font-semibold' : 'text-white/75 hover:text-white hover:bg-white/10 font-medium' }}">
                                <span>Laporan Gaji</span>
                                @if(request()->is(['laporan/gaji']))
                                    <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-white/80"></i>
                                @endif
                            </a>
                        </div>
                        @endcan
                        @endif

                        @can('laporan.jadwal')
                        <div class="sidebar-tree-branch">
                            <a href="{{ route('laporan.jadwal') }}" 
                               class="w-full flex items-center justify-between px-2.5 py-1 text-xs rounded-lg transition duration-150 {{ request()->is(['laporan/jadwal']) ? 'bg-white/20 text-white font-semibold' : 'text-white/75 hover:text-white hover:bg-white/10 font-medium' }}">
                                <span>Laporan Jadwal</span>
                                @if(request()->is(['laporan/jadwal']))
                                    <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-white/80"></i>
                                @endif
                            </a>
                        </div>
                        @endcan

                        @can('laporan.cuti')
                        <div class="sidebar-tree-branch">
                            <a href="{{ route('laporan.cuti') }}" 
                               class="w-full flex items-center justify-between px-2.5 py-1 text-xs rounded-lg transition duration-150 {{ request()->is(['laporan/cuti']) ? 'bg-white/20 text-white font-semibold' : 'text-white/75 hover:text-white hover:bg-white/10 font-medium' }}">
                                <span>Laporan Cuti</span>
                                @if(request()->is(['laporan/cuti']))
                                    <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-white/80"></i>
                                @endif
                            </a>
                        </div>
                        @endcan

                        @if(\Illuminate\Support\Facades\Route::has('laporan.reimbursement'))
                        @can('reimbursement.laporan')
                        <div class="sidebar-tree-branch">
                            <a href="{{ route('laporan.reimbursement') }}" 
                               class="w-full flex items-center justify-between px-2.5 py-1 text-xs rounded-lg transition duration-150 {{ request()->is(['laporan/reimbursement']) ? 'bg-white/20 text-white font-semibold' : 'text-white/75 hover:text-white hover:bg-white/10 font-medium' }}">
                                <span>Laporan Reimbursement</span>
                                @if(request()->is(['laporan/reimbursement']))
                                    <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-white/80"></i>
                                @endif
                            </a>
                        </div>
                        @endcan
                        @endif

                        @if(\Illuminate\Support\Facades\Route::has('laporan.pinjaman'))
                        @can('pinjaman.laporan')
                        <div class="sidebar-tree-branch">
                            <a href="{{ route('laporan.pinjaman') }}" 
                               class="w-full flex items-center justify-between px-2.5 py-1 text-xs rounded-lg transition duration-150 {{ request()->is(['laporan/pinjaman']) ? 'bg-white/20 text-white font-semibold' : 'text-white/75 hover:text-white hover:bg-white/10 font-medium' }}">
                                <span>Laporan Pinjaman</span>
                                @if(request()->is(['laporan/pinjaman']))
                                    <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-white/80"></i>
                                @endif
                            </a>
                        </div>
                        @endcan
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- KONFIGURASI & UTILITIES (SUPER ADMIN / ADMIN) -->
            @if (auth()->user()->hasAnyPermission([
                'harilibur.index',
                'jamkerjabydept.index',
                'generalsetting.index',
                'denda.index',
                'jamkerja.index',
                'approvalfeature.index',
                'approvallayer.index',
                'mesinfingerprint.index',
            ]) || auth()->user()->hasRole(['super admin']))
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-white/50 px-2 block mb-1">
                    PENGATURAN & SISTEM
                </span>
                
                <div class="space-y-0.5">
                    <!-- Konfigurasi Dropdown -->
                    @if (auth()->user()->hasAnyPermission([
                        'harilibur.index',
                        'jamkerjabydept.index',
                        'generalsetting.index',
                        'denda.index',
                        'jamkerja.index',
                        'approvalfeature.index',
                        'approvallayer.index',
                        'mesinfingerprint.index',
                    ]))
                    @php
                        $isKonfigOpen = request()->is([
                            'harilibur', 'harilibur/*',
                            'jamkerjabydept', 'jamkerjabydept/*',
                            'generalsetting', 'generalsetting/*',
                            'denda', 'denda/*',
                            'jamkerja', 'jamkerja/*',
                            'shiftpattern', 'shiftpattern/*',
                            'approvalfeature', 'approvalfeature/*',
                            'approvallayer', 'approvallayer/*',
                            'mesin-fingerprint', 'mesin-fingerprint/*',
                            'lemburaturan', 'lemburaturan/*',
                            'pph21', 'pph21/*'
                        ]);
                    @endphp
                    <div x-data="{ open: {{ $isKonfigOpen ? 'true' : 'false' }} }" class="space-y-0.5">
                        <button @click="open = !open" 
                                :class="open ? 'bg-white/15 text-white font-semibold' : 'text-white/80 hover:bg-white/10 hover:text-white font-medium'"
                                class="w-full flex items-center justify-between px-2.5 py-1.5 rounded-lg text-[13px] transition duration-150 group">
                            <div class="flex items-center gap-2.5">
                                <i data-lucide="sliders" class="w-4 h-4 text-white/70 group-hover:text-white"></i>
                                <span>Konfigurasi</span>
                            </div>
                            <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-white/60 transition-transform duration-200" :class="open ? 'rotate-180 text-white' : 'text-white/60'"></i>
                        </button>
                        
                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="sidebar-tree-container space-y-0.5">
                            @can('generalsetting.index')
                            <div class="sidebar-tree-branch">
                                <a href="{{ route('generalsetting.index') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1 text-xs rounded-lg transition duration-150 {{ request()->is(['generalsetting', 'generalsetting/*']) ? 'bg-white/20 text-white font-semibold' : 'text-white/75 hover:text-white hover:bg-white/10 font-medium' }}">
                                    <span>General Setting</span>
                                    @if(request()->is(['generalsetting', 'generalsetting/*']))
                                        <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-white/80"></i>
                                    @endif
                                </a>
                            </div>
                            @endcan

                            @can('approvallayer.index')
                            <div class="sidebar-tree-branch">
                                <a href="{{ route('approvallayer.index') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1 text-xs rounded-lg transition duration-150 {{ request()->is(['approvallayer', 'approvallayer/*']) ? 'bg-white/20 text-white font-semibold' : 'text-white/75 hover:text-white hover:bg-white/10 font-medium' }}">
                                    <span>Approval Layer</span>
                                    @if(request()->is(['approvallayer', 'approvallayer/*']))
                                        <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-white/80"></i>
                                    @endif
                                </a>
                            </div>
                            @endcan

                            <div class="sidebar-tree-branch">
                                <a href="{{ route('lemburaturan.index') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1 text-xs rounded-lg transition duration-150 {{ request()->is(['lemburaturan', 'lemburaturan/*']) ? 'bg-white/20 text-white font-semibold' : 'text-white/75 hover:text-white hover:bg-white/10 font-medium' }}">
                                    <span>Aturan Lembur</span>
                                    @if(request()->is(['lemburaturan', 'lemburaturan/*']))
                                        <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-white/80"></i>
                                    @endif
                                </a>
                            </div>

                            @can('denda.index')
                            @if (!empty($general_setting->denda))
                            <div class="sidebar-tree-branch">
                                <a href="{{ route('denda.index') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1 text-xs rounded-lg transition duration-150 {{ request()->is(['denda', 'denda/*']) ? 'bg-white/20 text-white font-semibold' : 'text-white/75 hover:text-white hover:bg-white/10 font-medium' }}">
                                    <span>Denda</span>
                                    @if(request()->is(['denda', 'denda/*']))
                                        <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-white/80"></i>
                                    @endif
                                </a>
                            </div>
                            @endif
                            @endcan

                            @can('harilibur.index')
                            <div class="sidebar-tree-branch">
                                <a href="{{ route('harilibur.index') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1 text-xs rounded-lg transition duration-150 {{ request()->is(['harilibur', 'harilibur/*']) ? 'bg-white/20 text-white font-semibold' : 'text-white/75 hover:text-white hover:bg-white/10 font-medium' }}">
                                    <span>Hari Libur</span>
                                    @if(request()->is(['harilibur', 'harilibur/*']))
                                        <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-white/80"></i>
                                    @endif
                                </a>
                            </div>
                            @endcan

                            @can('jamkerjabydept.index')
                            <div class="sidebar-tree-branch">
                                <a href="{{ route('jamkerjabydept.index') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1 text-xs rounded-lg transition duration-150 {{ request()->is(['jamkerjabydept', 'jamkerjabydept/*']) ? 'bg-white/20 text-white font-semibold' : 'text-white/75 hover:text-white hover:bg-white/10 font-medium' }}">
                                    <span>Jam Kerja Dept</span>
                                    @if(request()->is(['jamkerjabydept', 'jamkerjabydept/*']))
                                        <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-white/80"></i>
                                    @endif
                                </a>
                            </div>
                            @endcan

                            @can('jamkerja.index')
                            <div class="sidebar-tree-branch">
                                <a href="{{ route('jamkerja.index') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1 text-xs rounded-lg transition duration-150 {{ request()->is(['jamkerja', 'jamkerja/*']) ? 'bg-white/20 text-white font-semibold' : 'text-white/75 hover:text-white hover:bg-white/10 font-medium' }}">
                                    <span>Jam Kerja</span>
                                    @if(request()->is(['jamkerja', 'jamkerja/*']))
                                        <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-white/80"></i>
                                    @endif
                                </a>
                            </div>
                            @endcan

                            @can('mesinfingerprint.index')
                            <div class="sidebar-tree-branch">
                                <a href="{{ route('mesin-fingerprint.index') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1 text-xs rounded-lg transition duration-150 {{ request()->is(['mesin-fingerprint', 'mesin-fingerprint/*']) ? 'bg-white/20 text-white font-semibold' : 'text-white/75 hover:text-white hover:bg-white/10 font-medium' }}">
                                    <span>Mesin Fingerprint</span>
                                    @if(request()->is(['mesin-fingerprint', 'mesin-fingerprint/*']))
                                        <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-white/80"></i>
                                    @endif
                                </a>
                            </div>
                            @endcan

                            @if(\Illuminate\Support\Facades\Route::has('pph21.index'))
                            @can('pph21.index')
                            <div class="sidebar-tree-branch">
                                <a href="{{ route('pph21.index') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1 text-xs rounded-lg transition duration-150 {{ request()->is(['pph21', 'pph21/*']) ? 'bg-white/20 text-white font-semibold' : 'text-white/75 hover:text-white hover:bg-white/10 font-medium' }}">
                                    <span>PPh21</span>
                                    @if(request()->is(['pph21', 'pph21/*']))
                                        <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-white/80"></i>
                                    @endif
                                </a>
                            </div>
                            @endcan
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Utilities Dropdown (Super Admin only) -->
                    @if (auth()->user()->hasRole(['super admin']))
                    @php
                        $isUtilitiesOpen = request()->is([
                            'roles', 'roles/*',
                            'permissiongroups', 'permissiongroups/*',
                            'permissions', 'permissions/*',
                            'users', 'users/*',
                            'bersihkanfoto', 'bersihkanfoto/*',
                            'resetdata', 'resetdata/*',
                            'backup', 'backup/*',
                            'userloginlog', 'userloginlog/*',
                            'logmesin', 'logmesin/*',
                            'push-subscription', 'push-subscription/*'
                        ]);
                    @endphp
                    <div x-data="{ open: {{ $isUtilitiesOpen ? 'true' : 'false' }} }" class="space-y-0.5">
                        <button @click="open = !open" 
                                :class="open ? 'bg-white/15 text-white font-semibold' : 'text-white/80 hover:bg-white/10 hover:text-white font-medium'"
                                class="w-full flex items-center justify-between px-2.5 py-1.5 rounded-lg text-[13px] transition duration-150 group">
                            <div class="flex items-center gap-2.5">
                                <i data-lucide="wrench" class="w-4 h-4 text-white/70 group-hover:text-white"></i>
                                <span>Utilities</span>
                            </div>
                            <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-white/60 transition-transform duration-200" :class="open ? 'rotate-180 text-white' : 'text-white/60'"></i>
                        </button>
                        
                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="sidebar-tree-container space-y-0.5">
                            
                            <div class="sidebar-tree-branch">
                                <a href="{{ route('users.index') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1 text-xs rounded-lg transition duration-150 {{ request()->is(['users', 'users/*']) ? 'bg-white/20 text-white font-semibold' : 'text-white/75 hover:text-white hover:bg-white/10 font-medium' }}">
                                    <span>User</span>
                                    @if(request()->is(['users', 'users/*']))
                                        <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-white/80"></i>
                                    @endif
                                </a>
                            </div>

                            <div class="sidebar-tree-branch">
                                <a href="{{ route('roles.index') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1 text-xs rounded-lg transition duration-150 {{ request()->is(['roles', 'roles/*']) ? 'bg-white/20 text-white font-semibold' : 'text-white/75 hover:text-white hover:bg-white/10 font-medium' }}">
                                    <span>Role</span>
                                    @if(request()->is(['roles', 'roles/*']))
                                        <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-white/80"></i>
                                    @endif
                                </a>
                            </div>

                            <div class="sidebar-tree-branch">
                                <a href="{{ route('permissions.index') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1 text-xs rounded-lg transition duration-150 {{ request()->is(['permissions', 'permissions/*']) ? 'bg-white/20 text-white font-semibold' : 'text-white/75 hover:text-white hover:bg-white/10 font-medium' }}">
                                    <span>Permission</span>
                                    @if(request()->is(['permissions', 'permissions/*']))
                                        <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-white/80"></i>
                                    @endif
                                </a>
                            </div>

                            <div class="sidebar-tree-branch">
                                <a href="{{ route('permissiongroups.index') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1 text-xs rounded-lg transition duration-150 {{ request()->is(['permissiongroups', 'permissiongroups/*']) ? 'bg-white/20 text-white font-semibold' : 'text-white/75 hover:text-white hover:bg-white/10 font-medium' }}">
                                    <span>Group Permission</span>
                                    @if(request()->is(['permissiongroups', 'permissiongroups/*']))
                                        <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-white/80"></i>
                                    @endif
                                </a>
                            </div>

                            @can('bersihkanfoto.index')
                            <div class="sidebar-tree-branch">
                                <a href="{{ route('bersihkanfoto.index') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1 text-xs rounded-lg transition duration-150 {{ request()->is(['bersihkanfoto', 'bersihkanfoto/*']) ? 'bg-white/20 text-white font-semibold' : 'text-white/75 hover:text-white hover:bg-white/10 font-medium' }}">
                                    <span>Bersihkan Foto</span>
                                    @if(request()->is(['bersihkanfoto', 'bersihkanfoto/*']))
                                        <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-white/80"></i>
                                    @endif
                                </a>
                            </div>
                            @endcan

                            @can('userloginlog.index')
                            <div class="sidebar-tree-branch">
                                <a href="{{ route('userloginlog.index') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1 text-xs rounded-lg transition duration-150 {{ request()->is(['userloginlog', 'userloginlog/*']) ? 'bg-white/20 text-white font-semibold' : 'text-white/75 hover:text-white hover:bg-white/10 font-medium' }}">
                                    <span>Log Login</span>
                                    @if(request()->is(['userloginlog', 'userloginlog/*']))
                                        <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-white/80"></i>
                                    @endif
                                </a>
                            </div>
                            @endcan

                            @if(\Illuminate\Support\Facades\Route::has('logmesin.index'))
                            @can('logmesin.index')
                            <div class="sidebar-tree-branch">
                                <a href="{{ route('logmesin.index') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1 text-xs rounded-lg transition duration-150 {{ request()->is(['logmesin', 'logmesin/*']) ? 'bg-white/20 text-white font-semibold' : 'text-white/75 hover:text-white hover:bg-white/10 font-medium' }}">
                                    <span>Log Mesin Presensi</span>
                                    @if(request()->is(['logmesin', 'logmesin/*']))
                                        <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-white/80"></i>
                                    @endif
                                </a>
                            </div>
                            @endcan
                            @endif

                            @if(\Illuminate\Support\Facades\Route::has('backup.index'))
                            <div class="sidebar-tree-branch">
                                <a href="{{ route('backup.index') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1 text-xs rounded-lg transition duration-150 {{ request()->is(['backup', 'backup/*']) ? 'bg-white/20 text-white font-semibold' : 'text-white/75 hover:text-white hover:bg-white/10 font-medium' }}">
                                    <span>Backup & Restore</span>
                                    @if(request()->is(['backup', 'backup/*']))
                                        <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-white/80"></i>
                                    @endif
                                </a>
                            </div>
                            @endif

                            <div class="sidebar-tree-branch">
                                <a href="{{ route('resetdata.index') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1 text-xs rounded-lg transition duration-150 {{ request()->is(['resetdata', 'resetdata/*']) ? 'bg-white/20 text-white font-semibold' : 'text-white/75 hover:text-white hover:bg-white/10 font-medium' }}">
                                    <span>Reset Data</span>
                                    @if(request()->is(['resetdata', 'resetdata/*']))
                                        <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-white/80"></i>
                                    @endif
                                </a>
                            </div>

                            @if(\Illuminate\Support\Facades\Route::has('push-subscription.index'))
                            <div class="sidebar-tree-branch">
                                <a href="{{ route('push-subscription.index') }}" 
                                   class="w-full flex items-center justify-between px-2.5 py-1 text-xs rounded-lg transition duration-150 {{ request()->is(['push-subscription', 'push-subscription/*']) ? 'bg-white/20 text-white font-semibold' : 'text-white/75 hover:text-white hover:bg-white/10 font-medium' }}">
                                    <span>Push Subscriptions</span>
                                    @if(request()->is(['push-subscription', 'push-subscription/*']))
                                        <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-white/80"></i>
                                    @endif
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- WA Gateway (Super Admin only) -->
                    @if (auth()->user()->hasRole(['super admin']))
                    @php
                        $isWaActive = request()->is(['wagateway', 'wagateway/*']);
                    @endphp
                    <a href="{{ route('wagateway.index') }}" 
                       class="flex items-center justify-between px-2.5 py-1.5 rounded-lg text-[13px] transition duration-150 {{ $isWaActive ? 'bg-white/20 text-white font-semibold shadow-xs' : 'text-white/80 hover:bg-white/10 hover:text-white font-medium' }} group">
                        <div class="flex items-center gap-2.5">
                            <i data-lucide="message-square" class="w-4 h-4 {{ $isWaActive ? 'text-white' : 'text-white/70 group-hover:text-white' }}"></i>
                            <span>WA Gateway</span>
                        </div>
                    </a>
                    @endif
                </div>
            </div>
            @endif

        </div>

    <!-- Bottom User Profile Pill (Luxury Glassmorphism Finish) -->
    <div class="p-3 border-t border-white/10 bg-black/20 backdrop-blur-md flex-shrink-0 flex items-center justify-between relative z-10">
        <div class="flex items-center gap-2.5 min-w-0">
            <div class="w-8 h-8 rounded-xl overflow-hidden bg-white/15 border border-white/25 flex items-center justify-center font-bold text-xs text-white shrink-0 shadow-xs">
                @if($userPhoto)
                    <img src="{{ $userPhoto }}" alt="{{ $fullName }}" class="w-full h-full object-cover">
                @else
                    {{ substr($fullName, 0, 1) }}
                @endif
            </div>
            <div class="flex flex-col min-w-0">
                <span class="text-[12px] font-bold text-white leading-tight truncate">{{ $userName }}</span>
                <span class="text-[10px] text-white/60 capitalize mt-0.5 truncate">{{ $userRoleText }}</span>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}" class="shrink-0">
            @csrf
            <button type="submit" class="text-white/60 hover:text-rose-300 p-1.5 rounded-lg hover:bg-rose-500/20 transition-all duration-150" title="Logout">
                <i data-lucide="log-out" class="w-4 h-4"></i>
            </button>
        </form>
    </div>
</aside>

<script>
    (function() {
        const sidebarNav = document.getElementById('sidebarNavContainer');
        if (!sidebarNav) return;

        function restoreScroll() {
            const savedScroll = sessionStorage.getItem('sidebar_scroll_pos');
            if (savedScroll !== null) {
                const pos = parseInt(savedScroll, 10);
                sidebarNav.scrollTop = pos;
            } else {
                const activeLink = sidebarNav.querySelector('a.bg-white\\/20, a.text-white.font-semibold, .sidebar-tree-branch a.bg-white\\/20');
                if (activeLink) {
                    activeLink.scrollIntoView({ block: 'center', behavior: 'instant' });
                }
            }
        }

        // 1. Immediate restore on script execution
        restoreScroll();

        // 2. Restore on DOMContentLoaded and Load
        document.addEventListener('DOMContentLoaded', restoreScroll);
        window.addEventListener('load', restoreScroll);

        // 3. Restore after Alpine finishes rendering transitions
        document.addEventListener('alpine:initialized', function() {
            setTimeout(restoreScroll, 50);
            setTimeout(restoreScroll, 200);
        });

        // 4. Repeated check within first 400ms to override any dynamic content expansion
        [30, 80, 150, 300].forEach(function(delay) {
            setTimeout(restoreScroll, delay);
        });

        // Save on scrolling
        sidebarNav.addEventListener('scroll', function() {
            sessionStorage.setItem('sidebar_scroll_pos', sidebarNav.scrollTop);
        }, { passive: true });

        // Save immediately when clicking any link inside sidebar
        sidebarNav.addEventListener('click', function(e) {
            const link = e.target.closest('a');
            if (link) {
                sessionStorage.setItem('sidebar_scroll_pos', sidebarNav.scrollTop);
            }
        }, true);

        // Save before page unload
        window.addEventListener('beforeunload', function() {
            if (sidebarNav) {
                sessionStorage.setItem('sidebar_scroll_pos', sidebarNav.scrollTop);
            }
        });
    })();
</script>
