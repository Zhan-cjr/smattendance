 <!-- Menu -->

 <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
     <div class="app-brand demo" style="height: 85px !important">
         <a href="{{ route('dashboard.index') }}" class="app-brand-link">
             <span class="app-brand-logo rounded-circle demo d-flex align-items-center justify-content-center"
                 style="background: var(--theme-color-2); width: 46px; height: 46px !important; overflow: hidden;">
                 @if (!empty($general_setting->logo) && Storage::disk('public')->exists('logo/' . $general_setting->logo))
                     <img src="{{ asset('storage/logo/' . $general_setting->logo) }}" alt="Logo" class="w-100 h-100"
                         style="object-fit: cover;">
                 @else
                     <img src="{{ asset('assets/login/images/logoweb-1.png') }}" alt="Logo" class="w-100 h-100"
                         style="object-fit: cover;">
                 @endif
             </span>
             <span class="app-brand-text demo menu-text fw-bold d-flex flex-column ms-2"
                 style="letter-spacing: 1px; color: #fff;">
                 <span style="font-size: 18px;">SMATT</span>
                 <small class="mt-1" style="font-size: 11px; letter-spacing: 0.5px; color: rgba(255, 255, 255, 0.7);">
                     Aplikasi Presensi Online
                 </small>
                 <small class="mt-1">Version 3.0.1</small>
             </span>
         </a>

         <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
             <i class="ti menu-toggle-icon d-none d-xl-block ti-sm align-top mb-4"></i>
             <i class="ti ti-x d-block d-xl-none ti-sm align-middle"></i>
         </a>
     </div>

     @php
         $authUser = auth()->user();
         $fullName = $authUser->name ?? 'Pengguna';
         $userName = explode(' ', $fullName)[0]; // Ambil nama depan saja
         $userEmail = $authUser->email ?? '-';
         $userPhoto = null;
         $userRoleText = $authUser->getRoleNames()->first() ?? 'User';

         $userKaryawan = \App\Models\Userkaryawan::where('id_user', $authUser->id)->first();
         if ($userKaryawan) {
             $sidebarKaryawan = \App\Models\Karyawan::where('nik', $userKaryawan->nik)->first();
             if (
                 $sidebarKaryawan &&
                 $sidebarKaryawan->foto &&
                 \Illuminate\Support\Facades\Storage::disk('public')->exists('/karyawan/' . $sidebarKaryawan->foto)
             ) {
                 $userPhoto = getfotoKaryawan($sidebarKaryawan->foto);
             }
         }
     @endphp

     <div class="px-3 pb-3 py-3">
         <div class="d-flex align-items-center rounded-3 p-3 shadow-sm"
             style="background: var(--theme-color-2); border: 1px solid rgba(0,0,0,0.05);">
             <div class="flex-shrink-0 position-relative">
                 @if ($userPhoto)
                     <div class="rounded-circle border border-3 shadow"
                         style="width: 48px; height: 48px; background-image: url('{{ $userPhoto }}'); background-size: cover; background-position: center; border-color: rgba(0,0,0,0.08) !important;">
                     </div>
                 @else
                     <div class="rounded-circle d-flex align-items-center justify-content-center shadow"
                         style="width: 48px; height: 48px; font-size: 22px; background: #fff; color: var(--theme-color-2) !important;">
                         <i class="ti ti-user"></i>
                     </div>
                 @endif
             </div>
             <div class="flex-grow-1 ms-3">
                 <div class="fw-bold mb-0" style="font-size: 14px; color: #fff;">{{ $userName }}</div>
                 <small class="text-uppercase"
                     style="letter-spacing: 0.5px; font-size: 11px; color: rgba(255,255,255,0.8); font-weight: 500;">{{ $userRoleText }}</small>
             </div>
             <a href="{{ route('profile.editprofile') }}" class="btn btn-sm rounded-2 shadow-sm"
                 style="background: {{ $general_setting->theme_color_1 }}; border: none; color: #fff; padding: 6px 12px;"
                 data-bs-toggle="tooltip" title="Edit Profile">
                 <i class="ti ti-settings" style="font-size: 16px;"></i>
             </a>
         </div>
     </div>

     <div class="menu-inner-shadow"></div>

     <ul class="menu-inner py-1">
         <!-- Dashboards -->
         <li class="menu-item {{ request()->is(['dashboard', 'dashboard/*']) ? 'active' : '' }}">
             <a href="{{ route('dashboard.index') }}" class="menu-link">
                 <i class="menu-icon tf-icons ti ti-home"></i>
                 <div>Dashboard</div>
             </a>
         </li>
         @if (auth()->user()->hasAnyPermission([
                     'karyawan.index',
                     'departemen.index',
                     'cabang.index',
                     'cuti.index',
                     'jabatan.index',
                     'grup.index',
                    'statuskawin.index',
                    'statuskaryawan.index',
                 ]))
             <li
                 class="menu-item {{ request()->is(['karyawan', 'karyawan/*', 'departemen', 'departemen/*', 'cabang', 'cuti', 'jabatan', 'grup', 'grup/*', 'statuskawin', 'statuskawin/*', 'statuskaryawan', 'statuskaryawan/*', 'monitoring-master', 'monitoring-master/*']) ? 'open' : '' }}">
                 <a href="javascript:void(0);" class="menu-link menu-toggle">
                     <i class="menu-icon tf-icons ti ti-database"></i>
                     <div>Data Master</div>

                 </a>
                 <ul class="menu-sub">
                     @can('karyawan.index')
                         <li class="menu-item {{ request()->is(['karyawan', 'karyawan/*']) ? 'active' : '' }}">
                             <a href="{{ route('karyawan.index') }}" class="menu-link">
                                 <div>Karyawan</div>
                             </a>
                         </li>
                     @endcan
                     @can('departemen.index')
                         <li class="menu-item {{ request()->is(['departemen', 'departemen/*']) ? 'active' : '' }}">
                             <a href="{{ route('departemen.index') }}" class="menu-link">
                                 <div>Departemen</div>
                             </a>
                         </li>
                     @endcan
                     @can('grup.index')
                         <li class="menu-item {{ request()->is(['grup', 'grup/*']) ? 'active' : '' }}">
                             <a href="{{ route('grup.index') }}" class="menu-link">
                                 <div>Grup</div>
                             </a>
                         </li>
                     @endcan
                     @can('jabatan.index')
                         <li class="menu-item {{ request()->is(['jabatan', 'jabatan/*']) ? 'active' : '' }}">
                             <a href="{{ route('jabatan.index') }}" class="menu-link">
                                 <div>Jabatan</div>
                             </a>
                         </li>
                     @endcan
                     @can('cabang.index')
                         <li class="menu-item {{ request()->is(['cabang', 'cabang/*']) ? 'active' : '' }}">
                             <a href="{{ route('cabang.index') }}" class="menu-link">
                                 <div>Cabang</div>
                             </a>
                         </li>
                     @endcan
                     @can('cuti.index')
                         <li class="menu-item {{ request()->is(['cuti', 'cuti/*']) ? 'active' : '' }}">
                             <a href="{{ route('cuti.index') }}" class="menu-link">
                                 <div>Cuti</div>
                             </a>
                         </li>
                     @endcan
                     @can('statuskawin.index')
                         <li class="menu-item {{ request()->is(['statuskawin', 'statuskawin/*']) ? 'active' : '' }}">
                             <a href="{{ route('statuskawin.index') }}" class="menu-link">
                                 <div>Status Kawin</div>
                             </a>
                         </li>
                     @endcan
                     @can('statuskaryawan.index')
                         <li class="menu-item {{ request()->is(['statuskaryawan', 'statuskaryawan/*']) ? 'active' : '' }}">
                             <a href="{{ route('statuskaryawan.index') }}" class="menu-link">
                                 <div>Status Karyawan</div>
                             </a>
                         </li>
                     @endcan
                     @can('monitoring.master.index')
                         <li class="menu-item {{ request()->is(['monitoring-master', 'monitoring-master/*']) ? 'active' : '' }}">
                             <a href="{{ route('monitoring-master.index') }}" class="menu-link">
                                 <div>Monitoring Master</div>
                             </a>
                         </li>
                     @endcan


                 </ul>
             </li>
         @endif
        @if (auth()->user()->hasAnyPermission(['presensi.index', 'trackingpresensi.index']))
             <li
                 class="menu-item {{ request()->is(['presensi', 'presensi/*', 'trackingpresensi', 'trackingpresensi/*', 'monitoring-lokasi', 'monitoring-lokasi/*']) ? 'open' : '' }}">
                 <a href="javascript:void(0);" class="menu-link menu-toggle">
                     <i class="menu-icon tf-icons ti ti-device-desktop"></i>
                     <div>Presensi</div>
                 </a>
                 <ul class="menu-sub">
                     @can('presensi.index')
                         <li class="menu-item {{ request()->is(['presensi', 'presensi/*']) ? 'active' : '' }}">
                             <a href="{{ route('presensi.index') }}" class="menu-link">
                                 <div>Monitoring Presensi</div>
                             </a>
                         </li>
                     @endcan
                     @can('trackingpresensi.index')
                         <li class="menu-item {{ request()->is(['trackingpresensi', 'trackingpresensi/*']) ? 'active' : '' }}">
                             <a href="{{ route('trackingpresensi.index') }}" class="menu-link">
                                 <div>Tracking Presensi</div>
                             </a>
                         </li>
                     @endcan
                     @can('livelocation.index')
                         <li class="menu-item {{ request()->is(['monitoring-lokasi', 'monitoring-lokasi/*']) ? 'active' : '' }}">
                             <a href="{{ route('monitoring-lokasi.index') }}" class="menu-link">
                                 <div>Monitoring Lokasi</div>
                             </a>
                         </li>
                     @endcan
                 </ul>
             </li>
         @endif
         @if (auth()->user()->hasAnyPermission([
                     'gajipokok.index',
                     'jenistunjangan.index',
                     'tunjangan.index',
                     'bpjskesehatan.index',
                     'bpjstenagakerja.index',
                     'penyesuaiangaji.index',
                 ]))
             <li
                 class="menu-item {{ request()->is([
                     'gajipokok',
                     'jenistunjangan',
                     'tunjangan',
                     'bpjskesehatan',
                     'bpjstenagakerja',
                     'penyesuaiangaji',
                     'penyesuaiangaji/*',
                     'slipgaji',
                     'slipgaji/*',
                 ])
                     ? 'open'
                     : '' }}">
                 <a href="javascript:void(0);" class="menu-link menu-toggle">
                     <i class="menu-icon tf-icons ti ti-moneybag"></i>
                     <div>Payroll</div>

                 </a>
                 <ul class="menu-sub">
                     @can('jenistunjangan.index')
                         <li
                             class="menu-item {{ request()->is(['jenistunjangan', 'jenistunjangan/*']) ? 'active' : '' }}">
                             <a href="{{ route('jenistunjangan.index') }}" class="menu-link">
                                 <div>Jenis Tunjangan</div>
                             </a>
                         </li>
                     @endcan
                     @can('gajipokok.index')
                         <li class="menu-item {{ request()->is(['gajipokok', 'gajipokok/*']) ? 'active' : '' }}">
                             <a href="{{ route('gajipokok.index') }}" class="menu-link">
                                 <div>Gaji Pokok</div>
                             </a>
                         </li>
                     @endcan
                     @can('tunjangan.index')
                         <li class="menu-item {{ request()->is(['tunjangan', 'tunjangan/*']) ? 'active' : '' }}">
                             <a href="{{ route('tunjangan.index') }}" class="menu-link">
                                 <div>Tunjangan</div>
                             </a>
                         </li>
                     @endcan
                     @can('bpjskesehatan.index')
                         <li class="menu-item {{ request()->is(['bpjskesehatan', 'bpjskesehatan/*']) ? 'active' : '' }}">
                             <a href="{{ route('bpjskesehatan.index') }}" class="menu-link">
                                 <div>BPJS Kesehatan</div>
                             </a>
                         </li>
                     @endcan
                     @can('bpjstenagakerja.index')
                         <li
                             class="menu-item {{ request()->is(['bpjstenagakerja', 'bpjstenagakerja/*']) ? 'active' : '' }}">
                             <a href="{{ route('bpjstenagakerja.index') }}" class="menu-link">
                                 <div>BPJS Tenaga Kerja</div>
                             </a>
                         </li>
                     @endcan
                     @can('penyesuaiangaji.index')
                         <li
                             class="menu-item {{ request()->is(['penyesuaiangaji', 'penyesuaiangaji/*']) ? 'active' : '' }}">
                             <a href="{{ route('penyesuaiangaji.index') }}" class="menu-link">
                                 <div>Penyesuaian Gaji</div>
                             </a>
                         </li>
                     @endcan
                     @can('slipgaji.index')
                         <li class="menu-item {{ request()->is(['slipgaji', 'slipgaji/*']) ? 'active' : '' }}">
                             <a href="{{ route('slipgaji.index') }}" class="menu-link">
                                 <div>Slip Gaji</div>
                             </a>
                         </li>
                     @endcan
                 </ul>
             </li>
         @endif
 
         @can('pinjaman.index')
             <li class="menu-item {{ request()->is(['pinjaman', 'pinjaman/*']) ? 'active' : '' }}">
                 <a href="{{ route('pinjaman.index') }}" class="menu-link">
                     <i class="menu-icon tf-icons ti ti-credit-card"></i>
                     <div>Pinjaman Karyawan</div>
                 </a>
             </li>
         @endcan
         @if (auth()->user()->hasAnyPermission(['kpi.period.index', 'kpi.indicator.index', 'kpi.employee.index']))
             <li class="menu-item {{ request()->is(['kpi', 'kpi/*']) ? 'open' : '' }}">
                 <a href="javascript:void(0);" class="menu-link menu-toggle">
                     <i class="menu-icon tf-icons ti ti-chart-bar"></i>
                     <div>Manajemen KPI</div>
                 </a>
                 <ul class="menu-sub">
                     @can('kpi.period.index')
                         <li class="menu-item {{ request()->is(['kpi/periods', 'kpi/periods/*']) ? 'active' : '' }}">
                             <a href="{{ route('kpi.periods.index') }}" class="menu-link">
                                 <div>Periode Penilaian</div>
                             </a>
                         </li>
                     @endcan
                     @can('kpi.indicator.index')
                         <li
                             class="menu-item {{ request()->is(['kpi/indicators', 'kpi/indicators/*']) ? 'active' : '' }}">
                             <a href="{{ route('kpi.indicators.index') }}" class="menu-link">
                                 <div>Indikator KPI</div>
                             </a>
                         </li>
                     @endcan
                     @can('kpi.employee.index')
                         <li
                             class="menu-item {{ request()->is(['kpi/transactions', 'kpi/transactions/*']) ? 'active' : '' }}">
                             <a href="{{ route('kpi.transactions.index') }}" class="menu-link">
                                 <div>Input Penilaian</div>
                             </a>
                         </li>
                     @endcan
                 </ul>
             </li>
         @endif
         @can('kunjungan.index')
             <li
                 class="menu-item {{ request()->is(['kunjungan', 'kunjungan/*', 'tracking-kunjungan', 'tracking-kunjungan/*']) ? 'open' : '' }}">
                 <a href="javascript:void(0);" class="menu-link menu-toggle">
                     <i class="menu-icon tf-icons ti ti-map-pin"></i>
                     <div>Kunjungan</div>
                 </a>
                 <ul class="menu-sub">
                     <li class="menu-item {{ request()->is(['kunjungan', 'kunjungan/*']) ? 'active' : '' }}">
                         <a href="{{ route('kunjungan.index') }}" class="menu-link">
                             <div>Data Kunjungan</div>
                         </a>
                     </li>
                     <li class="menu-item {{ request()->is(['tracking-kunjungan', 'tracking-kunjungan/*']) ? 'active' : '' }}">
                         <a href="{{ route('tracking-kunjungan.index') }}" class="menu-link">
                             <div>Tracking Kunjungan</div>
                         </a>
                     </li>
                 </ul>
             </li>
         @endcan
        

        
         @can('kontrak.index')
             <li class="menu-item {{ request()->is(['kontrak', 'kontrak/*']) ? 'active' : '' }}">
                 <a href="{{ route('kontrak.index') }}" class="menu-link">
                     <i class="menu-icon tf-icons ti ti-file-certificate"></i>
                     <div>Kontrak</div>
                 </a>
             </li>
         @endcan
         @can('mutasi.index')
             <li class="menu-item {{ request()->is(['mutasi', 'mutasi/*']) ? 'active' : '' }}">
                 <a href="{{ route('mutasi.index') }}" class="menu-link">
                     <i class="menu-icon tf-icons ti ti-exchange"></i>
                     <div>Mutasi & Promosi</div>
                 </a>
             </li>
         @endcan


         
         
         @if (auth()->user()->hasAnyPermission(['izinabsen.index', 'izinsakit.index', 'izincuti.index', 'izindinas.index']))
             <li
                 class="menu-item {{ request()->is(['izinabsen', 'izinabsen/*', 'izinsakit', 'izincuti', 'izindinas']) ? 'active' : '' }}">
                 <a href="{{ route('izinabsen.index') }}" class="menu-link">
                     <i class="menu-icon tf-icons ti ti-folder-check"></i>
                     <div>Pengajuan</div>
                     @if (!empty($notifikasi_ajuan_absen))
                         <div class="badge bg-danger rounded-pill ms-auto">{{ $notifikasi_ajuan_absen }}</div>
                     @endif
                 </a>
             </li>
         @endif
         {{-- @can('ajuanjadwal.index')
             <li class="menu-item {{ request()->is(['ajuanjadwal', 'ajuanjadwal/*']) ? 'active' : '' }}">
                 <a href="{{ route('ajuanjadwal.index') }}" class="menu-link">
                     <i class="menu-icon tf-icons ti ti-calendar-stats"></i>
                     <div>Ajuan Jadwal</div>
                 </a>
             </li>
         @endcan --}}
         
         @if (auth()->user()->hasAnyPermission(['lembur.index']))
             <li class="menu-item {{ request()->is(['lembur', 'lembur/*']) ? 'active' : '' }}">
                 <a href="{{ route('lembur.index') }}" class="menu-link">
                     <i class="menu-icon tf-icons ti ti-clock"></i>
                     <div>Lembur</div>
                     @if (!empty($notifikasi_lembur))
                         <div class="badge bg-danger rounded-pill ms-auto">{{ $notifikasi_lembur }}</div>
                     @endif
                 </a>
             </li>
         @endif
          @can('aktivitaskaryawan.index')
             <li class="menu-item {{ request()->is(['aktivitaskaryawan', 'aktivitaskaryawan/*']) ? 'active' : '' }}">
                 <a href="{{ route('aktivitaskaryawan.index') }}" class="menu-link">
                     <i class="menu-icon tf-icons ti ti-activity"></i>
                     <div>Aktivitas Karyawan</div>
                 </a>
             </li>
         @endcan
         
         @can('pelanggaran.index')
             <li class="menu-item {{ request()->is(['pelanggaran', 'pelanggaran/*']) ? 'active' : '' }}">
                 <a href="{{ route('pelanggaran.index') }}" class="menu-link">
                     <i class="menu-icon tf-icons ti ti-alert-triangle"></i>
                     <div>Pelanggaran</div>
                 </a>
             </li>
         @endcan
         @can('pengumuman.index')
             <li class="menu-item {{ request()->is(['pengumuman', 'pengumuman/*']) ? 'active' : '' }}">
                 <a href="{{ route('pengumuman.index') }}" class="menu-link">
                     <i class="menu-icon tf-icons ti ti-bell"></i>
                     <div>Pengumuman</div>
                 </a>
             </li>
         @endcan
         @if (auth()->user()->hasAnyPermission([
                     'harilibur.index',
                     'jamkerjabydept.index',
                     'generalsetting.index',
                     'denda.index',
                     'jamkerja.index',
                     'approvalfeature.index',
                     'mesinfingerprint.index',
                 ]))
             <li
                 class="menu-item {{ request()->is(['harilibur', 'harilibur/*', 'jamkerjabydept', 'jamkerjabydept/*', 'generalsetting', 'denda', 'jamkerja', 'jamkerja/*', 'approvalfeature', 'approvalfeature/*', 'mesin-fingerprint', 'mesin-fingerprint/*', 'lemburaturan', 'lemburaturan/*']) ? 'open' : '' }}">
                 <a href="javascript:void(0);" class="menu-link menu-toggle">
                     <i class="menu-icon tf-icons ti ti-settings"></i>
                     <div>Konfigurasi</div>
                 </a>
                 <ul class="menu-sub">
                     @can('generalsetting.index')
                         <li
                             class="menu-item {{ request()->is(['generalsetting', 'generalsetting/*']) ? 'active' : '' }}">
                             <a href="{{ route('generalsetting.index') }}" class="menu-link">
                                 <div>General Setting</div>
                             </a>
                         </li>
                     @endcan
                     <li class="menu-item {{ request()->is(['lemburaturan', 'lemburaturan/*']) ? 'active' : '' }}">
                         <a href="{{ route('lemburaturan.index') }}" class="menu-link">
                             <div>Aturan Lembur</div>
                         </a>
                     </li>
                     @can('denda.index')
                         @if ($general_setting->denda)
                             <li class="menu-item {{ request()->is(['denda', 'denda/*']) ? 'active' : '' }}">
                                 <a href="{{ route('denda.index') }}" class="menu-link">
                                     <div>Denda</div>
                                 </a>
                             </li>
                         @endif
                     @endcan
                     @can('harilibur.index')
                         <li class="menu-item {{ request()->is(['harilibur', 'harilibur/*']) ? 'active' : '' }}">
                             <a href="{{ route('harilibur.index') }}" class="menu-link">
                                 <div>Hari Libur</div>
                             </a>
                         </li>
                     @endcan
                     @can('jamkerjabydept.index')
                         <li
                             class="menu-item {{ request()->is(['jamkerjabydept', 'jamkerjabydept/*']) ? 'active' : '' }}">
                             <a href="{{ route('jamkerjabydept.index') }}" class="menu-link">
                                 <div>Jam Kerja Departemen</div>
                             </a>
                         </li>
                     @endcan
                     @can('jamkerja.index')
                         <li class="menu-item {{ request()->is(['jamkerja', 'jamkerja/*']) ? 'active' : '' }}">
                             <a href="{{ route('jamkerja.index') }}" class="menu-link">
                                 <div>Jam Kerja</div>
                             </a>
                         </li>
                     @endcan
                     @can('mesinfingerprint.index')
                         <li
                             class="menu-item {{ request()->is(['mesin-fingerprint', 'mesin-fingerprint/*']) ? 'active' : '' }}">
                             <a href="{{ route('mesin-fingerprint.index') }}" class="menu-link">
                                 <div>Mesin Fingerprint</div>
                             </a>
                         </li>
                     @endcan
                 </ul>
             </li>
         @endif
         @if (auth()->user()->hasAnyPermission(['laporan.presensi', 'laporan.cuti', 'laporan.jadwal', 'laporan.kehadiran']))
             <li class="menu-item {{ request()->is(['laporan', 'laporan/*']) ? 'open' : '' }} ">
                 <a href="javascript:void(0);" class="menu-link menu-toggle">
                     <i class="menu-icon tf-icons ti ti-adjustments-alt"></i>
                     <div>Laporan</div>
                 </a>
                 <ul class="menu-sub">
                     @can('laporan.presensi')
                         <li class="menu-item {{ request()->is(['laporan/presensi']) ? 'active' : '' }}">
                             <a href="{{ route('laporan.presensi') }}" class="menu-link">
                                 <div>Presensi & Gaji</div>
                             </a>
                         </li>
                     @endcan
                     @can('laporan.kehadiran')
                         <li class="menu-item {{ request()->is(['laporan/kehadiran']) ? 'active' : '' }}">
                             <a href="{{ route('laporan.kehadiran') }}" class="menu-link">
                                 <div>Kehadiran</div>
                             </a>
                         </li>
                     @endcan
                     @can('laporan.jadwal')
                         <li class="menu-item {{ request()->is(['laporan/jadwal']) ? 'active' : '' }}">
                             <a href="{{ route('laporan.jadwal') }}" class="menu-link">
                                 <div>Laporan Jadwal</div>
                             </a>
                         </li>
                     @endcan
                     @can('laporan.cuti')
                         <li class="menu-item {{ request()->is(['laporan/cuti']) ? 'active' : '' }}">
                             <a href="{{ route('laporan.cuti') }}" class="menu-link">
                                 <div>Laporan Cuti</div>
                             </a>
                         </li>
                     @endcan
                 </ul>
             </li>
         @endif
         @if (auth()->user()->hasRole(['super admin']))
             <li
                 class="menu-item {{ request()->is(['roles', 'roles/*', 'permissiongroups', 'permissiongroups/*', 'permissions', 'permissions/*', 'users', 'users/*', 'bersihkanfoto', 'bersihkanfoto/*', 'resetdata', 'resetdata/*', 'userloginlog', 'userloginlog/*']) ? 'open' : '' }} ">
                 <a href="javascript:void(0);" class="menu-link menu-toggle">
                     <i class="menu-icon tf-icons ti ti-adjustments-alt"></i>
                     <div>Utilities</div>
                 </a>
                 <ul class="menu-sub">
                     <li class="menu-item {{ request()->is(['users', 'users/*']) ? 'active' : '' }}">
                         <a href="{{ route('users.index') }}" class="menu-link">
                             <div>User</div>
                         </a>
                     </li>
                     <li class="menu-item {{ request()->is(['roles', 'roles/*']) ? 'active' : '' }}">
                         <a href="{{ route('roles.index') }}" class="menu-link">
                             <div>Role</div>
                         </a>
                     </li>
                     <li class="menu-item {{ request()->is(['permissions', 'permissions/*']) ? 'active' : '' }}"">
                         <a href="{{ route('permissions.index') }}" class="menu-link">
                             <div>Permission</div>
                         </a>
                     </li>
                     <li
                         class="menu-item  {{ request()->is(['permissiongroups', 'permissiongroups/*']) ? 'active' : '' }}">
                         <a href="{{ route('permissiongroups.index') }}" class="menu-link">
                             <div>Group Permission</div>
                         </a>
                     </li>
                     @can('bersihkanfoto.index')
                         <li class="menu-item {{ request()->is(['bersihkanfoto', 'bersihkanfoto/*']) ? 'active' : '' }}">
                             <a href="{{ route('bersihkanfoto.index') }}" class="menu-link">
                                 <div>Bersihkan Foto</div>
                             </a>
                         </li>
                     @endcan
                     @can('userloginlog.index')
                         <li class="menu-item {{ request()->is(['userloginlog', 'userloginlog/*']) ? 'active' : '' }}">
                             <a href="{{ route('userloginlog.index') }}" class="menu-link">
                                 <div>Log Login</div>
                             </a>
                         </li>
                     @endcan
                     <li class="menu-item {{ request()->is(['resetdata', 'resetdata/*']) ? 'active' : '' }}">
                         <a href="{{ route('resetdata.index') }}" class="menu-link">
                             <div>Reset Data</div>
                         </a>
                     </li>
                 </ul>
             </li>
         @endif
         @if (auth()->user()->hasRole(['super admin']))
             <li class="menu-item {{ request()->is(['wagateway', 'wagateway/*']) ? 'active' : '' }}">
                 <a href="{{ route('wagateway.index') }}" class="menu-link">
                     <i class="menu-icon tf-icons ti ti-brand-whatsapp"></i>
                     <div>WA Gateway</div>
                 </a>
             </li>
         @endif
     </ul>
 </aside>
 <!-- / Menu -->
