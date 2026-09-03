<?php

use App\Http\Controllers\BpjskesehatanController;
use App\Http\Controllers\BpjstenagakerjaController;
use App\Http\Controllers\CabangController;
use App\Http\Controllers\CutiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DendaController;
use App\Http\Controllers\DepartemenController;
use App\Http\Controllers\FacerecognitionController;
use App\Http\Controllers\GajipokokController;
use App\Http\Controllers\GeneralsettingController;
use App\Http\Controllers\GrupController;
use App\Http\Controllers\HariliburController;
use App\Http\Controllers\IzinabsenController;
use App\Http\Controllers\IzincutiController;
use App\Http\Controllers\IzindinasController;
use App\Http\Controllers\IzinsakitController;
use App\Http\Controllers\JabatanController;
use App\Http\Controllers\JamkerjabydeptController;
use App\Http\Controllers\JamkerjaController;
use App\Http\Controllers\KontrakController;
use App\Http\Controllers\KpiEmployeeController;
use App\Http\Controllers\KpiIndicatorController;
use App\Http\Controllers\MesinFingerprintController;
use App\Http\Controllers\PinjamanController;

use App\Http\Controllers\KpiPeriodController;
use App\Http\Controllers\KunjunganController;
use App\Http\Controllers\TrackingKunjunganController;
use App\Http\Controllers\JenistunjanganController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\LemburController;
use App\Http\Controllers\LemburaturanController;
use App\Http\Controllers\PengajuanizinController;
use App\Http\Controllers\PelanggaranController;
use App\Http\Controllers\PenyesuaiangajiController;
use App\Http\Controllers\Permission_groupController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\PresensispcController;
use App\Http\Controllers\PresensiistirahatController;
use App\Http\Controllers\PresensiistirahatlemburController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SlipgajiController;
use App\Http\Controllers\SlipgajiHarianController;
use App\Http\Controllers\ShortcutController;
use App\Http\Controllers\KaryawanApprovalController;
use App\Http\Controllers\TunjanganController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WagatewayController;
use App\Http\Controllers\FacerecognitionpresensiController;
use App\Http\Controllers\IconGeneratorController;
use App\Http\Controllers\BersihkanfotoController;
use App\Http\Controllers\UserloginlogController;
use App\Http\Controllers\TrackingPresensiController;
use App\Http\Controllers\MonitoringLokasiController;
use App\Http\Controllers\AktivitasKaryawanController;
use App\Http\Controllers\ResetDataController;
use App\Http\Controllers\UpdateController;
use App\Http\Controllers\Admin\UpdateManagementController;
use App\Http\Controllers\StatuskawinController;
use App\Http\Controllers\StatuskaryawanController;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Role;

/* |-------------------------------------------------------------------------- | Web Routes |-------------------------------------------------------------------------- | | Here is where you can register web routes for your application. These | routes are loaded by the RouteServiceProvider and all of them will | be assigned to the "web" middleware group. Make something great! | */

Route::middleware('guest')->group(function () {
    Route::get('/', function () {
            $agent = new \Jenssegers\Agent\Agent();
            if ($agent->isMobile()) {
                return view('auth.loginusermobile');
            }
            return view('auth.loginuser');
        }
        )->name('loginuser');
    });

// Face Recognition Presensi Routes (Public - No Login Required)
Route::controller(FacerecognitionpresensiController::class)->group(function () {
    Route::get('/facerecognition-presensi', 'index')->name('facerecognition-presensi.index');
    Route::get('/facerecognition-presensi/scan/{nik}', 'scan')->name('facerecognition-presensi.scan');
    Route::get('/facerecognition-presensi/scanall', 'scanAny')->name('facerecognition-presensi.scan_any');
    Route::post('/facerecognition-presensi/store', 'store')->name('facerecognition-presensi.store');
    Route::get('/facerecognition-presensi/generate/{nik}', 'getKaryawan')->name('facerecognition-presensi.generate');
    Route::get('/facerecognition/getallwajah', 'getAllWajah')->name('facerecognition.getallwajah');
});

// Public Kiosk Attendance Routes (RFID + Camera)
Route::controller(App\Http\Controllers\PublicPresensiController::class)->group(function () {
    Route::get('/public/presensi', 'index')->name('public.presensi.index');
    Route::post('/public/presensi/check-rfid', 'checkRfid')->name('public.presensi.check-rfid');
    Route::post('/public/presensi/store', 'store')->name('public.presensi.store');
});

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    // Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    // Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    //Setings
    //Role

    Route::controller(ProfileController::class)->group(function () {
            Route::get('/profile', 'index')->name('profile.index');
            Route::put('/profile', [ProfileController::class , 'update'])->name('profile.update');
            Route::get('/profile/editprofile', 'editprofile')->name('profile.editprofile');
            Route::post('/profile/updateprofile', 'updateprofile')->name('profile.updateprofile');
        }
        );

        Route::controller(DashboardController::class)->group(function () {
            Route::get('/dashboard', 'index')->name('dashboard.index');
            Route::post('/dashboard/kirim-ucapan-birthday', 'kirimUcapanBirthday')->name('dashboard.kirim.ucapan.birthday');
        }
        );

        Route::controller(ShortcutController::class)->group(function () {
            Route::get('/shortcut', 'index')->name('shortcut.index');
            Route::get('/myschedule', 'myschedule')->name('myschedule.index');
            Route::get('/shortcut/pinjaman', 'mypinjaman')->name('shortcut.mypinjaman');
        }
        );

        Route::controller(KaryawanApprovalController::class)->group(function () {
            Route::get('/karyawan-approval', 'index')->name('karyawan-approval.index');

            // Delegation approve routes (bypass izin*.approve permission, validated via delegation)
            Route::get('/karyawan-approval/izinabsen/{kode_izin}/approve', 'approveIzinAbsen')->name('karyawan-approval.izinabsen.approve');
            Route::post('/karyawan-approval/izinabsen/{kode_izin}/storeapprove', 'storeApproveIzinAbsen')->name('karyawan-approval.izinabsen.storeapprove');
            Route::delete('/karyawan-approval/izinabsen/{kode_izin}/cancelapprove', 'cancelApproveIzinAbsen')->name('karyawan-approval.izinabsen.cancelapprove');

            Route::get('/karyawan-approval/izinsakit/{kode_izin_sakit}/approve', 'approveIzinSakit')->name('karyawan-approval.izinsakit.approve');
            Route::post('/karyawan-approval/izinsakit/{kode_izin_sakit}/storeapprove', 'storeApproveIzinSakit')->name('karyawan-approval.izinsakit.storeapprove');
            Route::delete('/karyawan-approval/izinsakit/{kode_izin_sakit}/cancelapprove', 'cancelApproveIzinSakit')->name('karyawan-approval.izinsakit.cancelapprove');

            Route::get('/karyawan-approval/izincuti/{kode_izin_cuti}/approve', 'approveIzinCuti')->name('karyawan-approval.izincuti.approve');
            Route::post('/karyawan-approval/izincuti/{kode_izin_cuti}/storeapprove', 'storeApproveIzinCuti')->name('karyawan-approval.izincuti.storeapprove');
            Route::delete('/karyawan-approval/izincuti/{kode_izin_cuti}/cancelapprove', 'cancelApproveIzinCuti')->name('karyawan-approval.izincuti.cancelapprove');

            Route::get('/karyawan-approval/izindinas/{kode_izin_dinas}/approve', 'approveIzinDinas')->name('karyawan-approval.izindinas.approve');
            Route::post('/karyawan-approval/izindinas/{kode_izin_dinas}/storeapprove', 'storeApproveIzinDinas')->name('karyawan-approval.izindinas.storeapprove');
            Route::delete('/karyawan-approval/izindinas/{kode_izin_dinas}/cancelapprove', 'cancelApproveIzinDinas')->name('karyawan-approval.izindinas.cancelapprove');
        }
        );
        Route::middleware('role:super admin')->controller(RoleController::class)->group(function () {
            Route::get('/roles', 'index')->name('roles.index');
            Route::get('/roles/create', 'create')->name('roles.create');
            Route::post('/roles', 'store')->name('roles.store');
            Route::get('/roles/{id}/edit', 'edit')->name('roles.edit');
            Route::put('/roles/{id}/update', 'update')->name('roles.update');
            Route::delete('/roles/{id}/delete', 'destroy')->name('roles.delete');
            Route::get('/roles/{id}/createrolepermission', 'createrolepermission')->name('roles.createrolepermission');
            Route::post('/roles/{id}/storerolepermission', 'storerolepermission')->name('roles.storerolepermission');
        }
        );


        Route::middleware('role:super admin')->controller(Permission_groupController::class)->group(function () {
            Route::get('/permissiongroups', 'index')->name('permissiongroups.index');
            Route::get('/permissiongroups/create', 'create')->name('permissiongroups.create');
            Route::post('/permissiongroups', 'store')->name('permissiongroups.store');
            Route::get('/permissiongroups/{id}/edit', 'edit')->name('permissiongroups.edit');
            Route::put('/permissiongroups/{id}/update', 'update')->name('permissiongroups.update');
            Route::delete('/permissiongroups/{id}/delete', 'destroy')->name('permissiongroups.delete');
        }
        );


        Route::middleware('role:super admin')->controller(PermissionController::class)->group(function () {
            Route::get('/permissions', 'index')->name('permissions.index');
            Route::get('/permissions/create', 'create')->name('permissions.create');
            Route::post('/permissions', 'store')->name('permissions.store');
            Route::get('/permissions/{id}/edit', 'edit')->name('permissions.edit');
            Route::put('/permissions/{id}/update', 'update')->name('permissions.update');
            Route::delete('/permissions/{id}/delete', 'destroy')->name('permissions.delete');
        }
        );

        Route::middleware('role:super admin')->controller(UserController::class)->group(function () {
            Route::get('/users', 'index')->name('users.index');
            Route::get('/users/create', 'create')->name('users.create');
            Route::post('/users', 'store')->name('users.store');
            Route::get('/users/{id}/edit', 'edit')->name('users.edit');
            Route::put('/users/{id}/update', 'update')->name('users.update');
            Route::delete('/users/{id}/delete', 'destroy')->name('users.delete');
        }
        );

        Route::controller(UserController::class)->group(function () {
            Route::get('/users/{id}/editpassword', 'editpassword')->name('users.editpassword');
            Route::put('/users/{id}/updatepassword', 'updatepassword')->name('users.updatepassword');
        }
        );

        //Data Master
        //Dat Karyawan
        Route::controller(KaryawanController::class)->group(function () {
            Route::get('/karyawan', 'index')->name('karyawan.index')->can('karyawan.index');
            Route::get('/karyawan/create', 'create')->name('karyawan.create')->can('karyawan.create');
            Route::post('/karyawan', 'store')->name('karyawan.store')->can('karyawan.create');
            Route::get('/karyawan/import', 'import')->name('karyawan.import')->can('karyawan.create');
            Route::get('/karyawan/download-template', 'download_template')->name('karyawan.download_template')->can('karyawan.create');
            Route::get('/karyawan/export', 'export')->name('karyawan.export')->can('karyawan.index');
            Route::post('/karyawan/import', 'import_proses')->name('karyawan.import_proses')->can('karyawan.create');
            Route::get('/karyawan/{nik}/edit', 'edit')->name('karyawan.edit')->can('karyawan.edit');
            Route::put('/karyawan/{nik}', 'update')->name('karyawan.update')->can('karyawan.edit');
            Route::delete('/karyawan/{nik}', 'destroy')->name('karyawan.delete')->can('karyawan.delete');
            Route::get('/karyawan/{nik}/show', 'show')->name('karyawan.show')->can('karyawan.show');
            Route::get('/karyawan/{nik}/setjamkerja', 'setjamkerja')->name('karyawan.setjamkerja')->can('karyawan.setjamkerja');
            Route::post('/karyawan/{nik}/storejamkerjabyday', 'storejamkerjabyday')->name('karyawan.storejamkerjabyday')->can('karyawan.setjamkerja');
            Route::get('/karyawan/{nik}/setcabang', 'setcabang')->name('karyawan.setcabang')->can('karyawan.setcabang');
            Route::post('/karyawan/{nik}/storecabang', 'storecabang')->name('karyawan.storecabang')->can('karyawan.setcabang');
            Route::post('/karyawan/storejamkerjabydate', 'storejamkerjabydate')->name('karyawan.storejamkerjabydate')->can('karyawan.setjamkerja');

            Route::post('/karyawan/getjamkerjabydate', 'getjamkerjabydate')->name('karyawan.getjamkerjabydate')->can('karyawan.setjamkerja');
            Route::post('/karyawan/deletejamkerjabydate', 'deletejamkerjabydate')->name('karyawan.deletejamkerjabydate')->can('karyawan.setjamkerja');

            Route::get('/karyawan/{nik}/createuser', 'createuser')->name('karyawan.createuser')->can('users.create');
            Route::get('/karyawan/generatealluser', 'generateAllUser')->name('karyawan.generatealluser')->can('users.create');
            Route::get('/karyawan/deletealluser', 'deleteAllUser')->name('karyawan.deletealluser')->can('users.create');
            Route::get('/karyawan/{nik}/deleteuser', 'deleteuser')->name('karyawan.deleteuser')->can('users.create');
            Route::get('/karyawan/{nik}/lockunlocklocation', 'lockunlocklocation')->name('karyawan.lockunlocklocation')->can('karyawan.edit');
            Route::get('/karyawan/{nik}/lockunlockjamkerja', 'lockunlockjamkerja')->name('karyawan.lockunlockjamkerja')->can('karyawan.edit');
            Route::get('/karyawan/{nik}/lockunlockfacerecognition', 'lockunlockfacerecognition')->name('karyawan.lockunlockfacerecognition')->can('karyawan.edit');
            Route::get('/karyawan/{nik}/idcard', 'idcard')->name('karyawan.idcard');

            Route::get('/karyawan/getkaryawan', 'getkaryawan')->name('karyawan.getkaryawan');
            Route::get('/karyawan/getkaryawantable', 'getkaryawantable')->name('karyawan.getkaryawantable');
        }
        );

        Route::controller(KontrakController::class)->group(function () {
            Route::get('/kontrak/template', 'template')->name('kontrak.template')->can('kontrak.create');
            Route::post('/kontrak/template', 'updateTemplate')->name('kontrak.updateTemplate')->can('kontrak.create');
            Route::get('/kontrak', 'index')->name('kontrak.index')->can('kontrak.index');
            Route::get('/kontrak/{id}/show', 'show')->name('kontrak.show')->can('kontrak.index');
            Route::get('/kontrak/create', 'create')->name('kontrak.create')->can('kontrak.create');
            Route::post('/kontrak', 'store')->name('kontrak.store')->can('kontrak.create');
            Route::get('/kontrak/karyawan/{nik}/latest', 'getEmployeeLatest')->name('kontrak.karyawan.latest')->can('kontrak.create');
            Route::get('/kontrak/{id}/edit', 'edit')->name('kontrak.edit')->can('kontrak.edit');
            Route::put('/kontrak/{id}', 'update')->name('kontrak.update')->can('kontrak.edit');
            Route::delete('/kontrak/{id}/delete', 'destroy')->name('kontrak.delete')->can('kontrak.delete');
            Route::get('/kontrak/{id}/print', 'print')->name('kontrak.print')->can('kontrak.index');
        }
        );

        Route::controller(DepartemenController::class)->group(function () {
            Route::get('/departemen', 'index')->name('departemen.index')->can('departemen.index');
            Route::get('/departemen/create', 'create')->name('departemen.create')->can('departemen.create');
            Route::post('/departemen', 'store')->name('departemen.store')->can('departemen.create');
            Route::get('/departemen/{nik}', 'edit')->name('departemen.edit')->can('departemen.edit');
            Route::put('/departemen/{nik}', 'update')->name('departemen.update')->can('departemen.edit');
            Route::delete('/departemen/{nik}/delete', 'destroy')->name('departemen.delete')->can('departemen.delete');
        }
        );

        Route::controller(StatuskawinController::class)->group(function () {
            Route::get('/statuskawin', 'index')->name('statuskawin.index')->can('statuskawin.index');
            Route::get('/statuskawin/create', 'create')->name('statuskawin.create')->can('statuskawin.create');
            Route::post('/statuskawin', 'store')->name('statuskawin.store')->can('statuskawin.create');
            Route::get('/statuskawin/{kode_status_kawin}/edit', 'edit')->name('statuskawin.edit')->can('statuskawin.edit');
            Route::put('/statuskawin/{kode_status_kawin}', 'update')->name('statuskawin.update')->can('statuskawin.edit');
            Route::delete('/statuskawin/{kode_status_kawin}', 'destroy')->name('statuskawin.destroy')->can('statuskawin.delete');
        }
        );

        Route::controller(StatuskaryawanController::class)->group(function () {
            Route::get('/statuskaryawan', 'index')->name('statuskaryawan.index')->can('statuskaryawan.index');
            Route::get('/statuskaryawan/create', 'create')->name('statuskaryawan.create')->can('statuskaryawan.create');
            Route::post('/statuskaryawan', 'store')->name('statuskaryawan.store')->can('statuskaryawan.create');
            Route::get('/statuskaryawan/{kode_status_karyawan}/edit', 'edit')->name('statuskaryawan.edit')->can('statuskaryawan.edit');
            Route::put('/statuskaryawan/{kode_status_karyawan}', 'update')->name('statuskaryawan.update')->can('statuskaryawan.edit');
            Route::delete('/statuskaryawan/{kode_status_karyawan}', 'destroy')->name('statuskaryawan.destroy')->can('statuskaryawan.delete');
        }
        );

        Route::controller(GrupController::class)->group(function () {
            Route::get('/grup', 'index')->name('grup.index')->can('grup.index');
            Route::get('/grup/create', 'create')->name('grup.create')->can('grup.create');
            Route::post('/grup', 'store')->name('grup.store')->can('grup.create');

            // Route pencarian karyawan di grup (letakkan sebelum route parameter)
            Route::get('/grup/search-karyawan', 'searchKaryawan')->name('grup.searchKaryawan');
            // Form karyawan baru di grup (hindari tertangkap oleh {kode_grup})
            Route::get('/grup/{kode_grup}/create-karyawan-form', 'createKaryawanForm')->name('grup.createKaryawanForm')->can('grup.detail');
            // Get anggota grup untuk AJAX update
            Route::get('/grup/{kode_grup}/get-anggota', 'getAnggotaGrup')->name('grup.getAnggotaGrup');
            // Set jam kerja grup
            Route::get('/grup/{kode_grup}/set-jam-kerja', 'setJamKerja')->name('grup.setJamKerja')->can('grup.setJamKerja');
            Route::match (['PUT', 'POST'], '/grup/{kode_grup}/update-jam-kerja', 'updateJamKerja')->name('grup.updateJamKerja')->can('grup.setJamKerja');
            Route::delete('/grup/delete-jam-kerja-bydate', 'deleteJamKerjaBydate')->name('grup.deleteJamKerjaBydate')->can('grup.setJamKerja');
            Route::post('/grup/{kode_grup}/get-jam-kerja-bydate', 'getJamKerjaBydate')->name('grup.getJamKerjaBydate')->can('grup.setJamKerja');
            // Detail grup (letakkan sebelum {kode_grup})
            Route::get('/grup/{kode_grup}/detail', 'detail')->name('grup.detail')->can('grup.detail');
            // Tambah karyawan ke grup (hindari tertangkap oleh {kode_grup})
            Route::post('/grup/add-karyawan', 'addKaryawan')->name('grup.addKaryawan')->can('grup.detail');
            // Hapus karyawan dari grup (hindari tertangkap oleh {kode_grup})
            Route::delete('/grup/remove-karyawan', 'removeKaryawan')->name('grup.removeKaryawan')->can('grup.detail');

            // Route manipulasi data grup (setelah route spesifik di atas)
            Route::get('/grup/{kode_grup}', 'edit')->name('grup.edit')->can('grup.edit');
            Route::delete('/grup/{kode_grup}/delete', 'delete')->name('grup.delete')->can('grup.delete');
            Route::put('/grup/{kode_grup}', 'update')->name('grup.update')->can('grup.edit');
        }
        );

        Route::controller(JabatanController::class)->group(function () {
            Route::get('/jabatan', 'index')->name('jabatan.index')->can('jabatan.index');
            Route::get('/jabatan/create', 'create')->name('jabatan.create')->can('jabatan.create');
            Route::post('/jabatan', 'store')->name('jabatan.store')->can('jabatan.create');
            Route::get('/jabatan/{kode_jabatan}', 'edit')->name('jabatan.edit')->can('jabatan.edit');
            Route::put('/jabatan/{kode_jabatan}', 'update')->name('jabatan.update')->can('jabatan.edit');
            Route::delete('/jabatan/{kode_jabatan}/delete', 'destroy')->name('jabatan.delete')->can('jabatan.delete');
        }
        );


        Route::controller(CabangController::class)->group(function () {
            Route::get('/cabang', 'index')->name('cabang.index')->can('cabang.index');
            Route::get('/cabang/create', 'create')->name('cabang.create')->can('cabang.create');
            Route::post('/cabang', 'store')->name('cabang.store')->can('cabang.create');
            Route::get('/cabang/{kode_cabang}', 'edit')->name('cabang.edit')->can('cabang.edit');
            Route::put('/cabang/{kode_cabang}', 'update')->name('cabang.update')->can('cabang.edit');
            Route::delete('/cabang/{kode_cabang}/delete', 'destroy')->name('cabang.delete')->can('cabang.delete');
        }
        );

        Route::controller(CutiController::class)->group(function () {
            Route::get('/cuti', 'index')->name('cuti.index')->can('cuti.index');
            Route::get('/cuti/create', 'create')->name('cuti.create')->can('cuti.create');
            Route::post('/cuti', 'store')->name('cuti.store')->can('cuti.create');
            Route::get('/cuti/{kode_cuti}', 'edit')->name('cuti.edit')->can('cuti.edit');
            Route::put('/cuti/{kode_cuti}', 'update')->name('cuti.update')->can('cuti.edit');
            Route::delete('/cuti/{kode_cuti}/delete', 'destroy')->name('cuti.delete')->can('cuti.delete');
        }
        );

        Route::controller(JamkerjaController::class)->group(function () {
            Route::get('/jamkerja', 'index')->name('jamkerja.index')->can('jamkerja.index');
            Route::get('/jamkerja/create', 'create')->name('jamkerja.create')->can('jamkerja.create');
            Route::post('/jamkerja', 'store')->name('jamkerja.store')->can('jamkerja.create');
            Route::get('/jamkerja/{kode_jam_kerja}/edit', 'edit')->name('jamkerja.edit')->can('jamkerja.edit');
            Route::put('/jamkerja/{kode_jam_kerja}/update', 'update')->name('jamkerja.update')->can('jamkerja.edit');
            Route::delete('/jamkerja/{kode_jam_kerja}/delete', 'destroy')->name('jamkerja.delete')->can('jamkerja.delete');
        }
        );

        Route::controller(MesinFingerprintController::class)->group(function () {
            Route::get('/mesin-fingerprint', 'index')->name('mesin-fingerprint.index');
            Route::get('/mesin-fingerprint/create', 'create')->name('mesin-fingerprint.create');
            Route::post('/mesin-fingerprint', 'store')->name('mesin-fingerprint.store');
            Route::post('/mesin-fingerprint/edit', 'edit')->name('mesin-fingerprint.edit');
            Route::put('/mesin-fingerprint/{id}', 'update')->name('mesin-fingerprint.update');
            Route::delete('/mesin-fingerprint/{id}', 'destroy')->name('mesin-fingerprint.delete');
        }
        );

        Route::controller(GajipokokController::class)->group(function () {
            Route::get('/gajipokok', 'index')->name('gajipokok.index')->can('gajipokok.index');
            Route::get('/gajipokok/create', 'create')->name('gajipokok.create')->can('gajipokok.create');
            Route::post('/gajipokok', 'store')->name('gajipokok.store')->can('gajipokok.create');
            Route::get('/gajipokok/{kode_gaji}/edit', 'edit')->name('gajipokok.edit')->can('gajipokok.edit');
            Route::put('/gajipokok/{kode_gaji}/update', 'update')->name('gajipokok.update')->can('gajipokok.edit');
            Route::delete('/gajipokok/{kode_gaji}/delete', 'destroy')->name('gajipokok.delete')->can('gajipokok.delete');
        }
        );

        Route::controller(JenistunjanganController::class)->group(function () {
            Route::get('/jenistunjangan', 'index')->name('jenistunjangan.index')->can('jenistunjangan.index');
            Route::get('/jenistunjangan/create', 'create')->name('jenistunjangan.create')->can('jenistunjangan.create');
            Route::post('/jenistunjangan', 'store')->name('jenistunjangan.store')->can('jenistunjangan.create');
            Route::get('/jenistunjangan/{kode_jenis_tunjangan}/edit', 'edit')->name('jenistunjangan.edit')->can('jenistunjangan.edit');
            Route::put('/jenistunjangan/{kode_jenis_tunjangan}/update', 'update')->name('jenistunjangan.update')->can('jenistunjangan.edit');
            Route::delete('/jenistunjangan/{kode_jenis_tunjangan}/delete', 'destroy')->name('jenistunjangan.delete')->can('jenistunjangan.delete');
        }
        );


        Route::controller(TunjanganController::class)->group(function () {
            Route::get('/tunjangan', 'index')->name('tunjangan.index')->can('tunjangan.index');
            Route::get('/tunjangan/create', 'create')->name('tunjangan.create')->can('tunjangan.create');
            Route::post('/tunjangan', 'store')->name('tunjangan.store')->can('tunjangan.create');
            Route::get('/tunjangan/{kode_tunjangan}/edit', 'edit')->name('tunjangan.edit')->can('tunjangan.edit');
            Route::put('/tunjangan/{kode_tunjangan}/update', 'update')->name('tunjangan.update')->can('tunjangan.edit');
            Route::delete('/tunjangan/{kode_tunjangan}/delete', 'destroy')->name('tunjangan.delete')->can('tunjangan.delete');
        }
        );


        Route::controller(BpjskesehatanController::class)->group(function () {
            Route::get('/bpjskesehatan', 'index')->name('bpjskesehatan.index')->can('bpjskesehatan.index');
            Route::get('/bpjskesehatan/create', 'create')->name('bpjskesehatan.create')->can('bpjskesehatan.create');
            Route::post('/bpjskesehatan', 'store')->name('bpjskesehatan.store')->can('bpjskesehatan.create');
            Route::get('/bpjskesehatan/{kode_bpjs_kesehatan}/edit', 'edit')->name('bpjskesehatan.edit')->can('bpjskesehatan.edit');
            Route::put('/bpjskesehatan/{kode_bpjs_kesehatan}/update', 'update')->name('bpjskesehatan.update')->can('bpjskesehatan.edit');
            Route::delete('/bpjskesehatan/{kode_bpjs_kesehatan}/delete', 'destroy')->name('bpjskesehatan.delete')->can('bpjskesehatan.delete');
        }
        );

        Route::controller(BpjstenagakerjaController::class)->group(function () {
            Route::get('/bpjstenagakerja', 'index')->name('bpjstenagakerja.index')->can('bpjstenagakerja.index');
            Route::get('/bpjstenagakerja/create', 'create')->name('bpjstenagakerja.create')->can('bpjstenagakerja.create');
            Route::post('/bpjstenagakerja', 'store')->name('bpjstenagakerja.store')->can('bpjstenagakerja.create');
            Route::get('/bpjstenagakerja/{kode_bpjs_tk}/edit', 'edit')->name('bpjstenagakerja.edit')->can('bpjstenagakerja.edit');
            Route::put('/bpjstenagakerja/{kode_bpjs_tk}/update', 'update')->name('bpjstenagakerja.update')->can('bpjstenagakerja.edit');
            Route::delete('/bpjstenagakerja/{kode_bpjs_tk}/delete', 'destroy')->name('bpjstenagakerja.delete')->can('bpjstenagakerja.delete');
        }
        );


        Route::controller(PenyesuaiangajiController::class)->group(function () {
            Route::get('/penyesuaiangaji', 'index')->name('penyesuaiangaji.index')->can('penyesuaiangaji.index');
            Route::get('/penyesuaiangaji/create', 'create')->name('penyesuaiangaji.create')->can('penyesuaiangaji.create');
            Route::post('/penyesuaiangaji', 'store')->name('penyesuaiangaji.store')->can('penyesuaiangaji.create');
            Route::get('/penyesuaiangaji/{kode_penyesuaian_gaji}/edit', 'edit')->name('penyesuaiangaji.edit')->can('penyesuaiangaji.edit');
            Route::get('/penyesuaiangaji/{kode_penyesuaian_gaji}/setkaryawan', 'setkaryawan')->name('penyesuaiangaji.setkaryawan')->can('penyesuaiangaji.edit');
            Route::get('/penyesuaiangaji/{kode_penyesuaian_gaji}/addkaryawan', 'addkaryawan')->name('penyesuaiangaji.addkaryawan')->can('penyesuaiangaji.edit');
            Route::post('/penyesuaiangaji/{kode_penyesuaian_gaji}/storekaryawan', 'storekaryawan')->name('penyesuaiangaji.storekaryawan')->can('penyesuaiangaji.edit');
            Route::get('/penyesuaiangaji/{kode_penyesuaian_gaji}/{nik}/editkaryawan', 'editkaryawan')->name('penyesuaiangaji.editkaryawan')->can('penyesuaiangaji.edit');
            Route::put('/penyesuaiangaji/{kode_penyesuaian_gaji}/{nik}/updatekaryawan', 'updatekaryawan')->name('penyesuaiangaji.updatekaryawan')->can('penyesuaiangaji.edit');
            Route::put('/penyesuaiangaji/{kode_penyesuaian_gaji}/update', 'update')->name('penyesuaiangaji.update')->can('penyesuaiangaji.edit');
            Route::delete('/penyesuaiangaji/{kode_penyesuaian_gaji}/delete', 'destroy')->name('penyesuaiangaji.delete')->can('penyesuaiangaji.delete');
            Route::delete('/penyesuaiangaji/{kode_penyesuaian_gaji}/{nik}/deletekaryawan', 'destroykaryawan')->name('penyesuaiangaji.deletekaryawan')->can('penyesuaiangaji.delete');
        }
        );


        Route::controller(SlipgajiController::class)->group(function () {
            Route::get('/slipgaji', 'index')->name('slipgaji.index')->can('slipgaji.index');
            Route::get('/slipgaji/create', 'create')->name('slipgaji.create')->can('slipgaji.create');
            Route::post('/slipgaji/store', 'store')->name('slipgaji.store')->can('slipgaji.create');
            Route::get('/slipgaji/{kode_slip}/show', 'show')->name('slipgaji.show')->can('slipgaji.index');
            Route::get('/slipgaji/{kode_slip}/edit', 'edit')->name('slipgaji.edit')->can('slipgaji.edit');
            Route::put('/slipgaji/{kode_slip}/update', 'update')->name('slipgaji.update')->can('slipgaji.edit');
            Route::delete('/slipgaji/{kode_slip}/delete', 'destroy')->name('slipgaji.delete')->can('slipgaji.delete');
            Route::get('/slipgaji/{nik}/{bulan}/{tahun}/cetakslip', 'cetakslipgaji')->name('slipgaji.cetakslip')->can('slipgaji.index');
        }
        );

        Route::controller(SlipgajiHarianController::class)->group(function () {
            Route::get('/slipgajiharian/create', 'create')->name('slipgajiharian.create')->can('slipgaji.create');
            Route::post('/slipgajiharian/store', 'store')->name('slipgajiharian.store')->can('slipgaji.create');
            Route::get('/slipgajiharian/{kode_slip}/edit', 'edit')->name('slipgajiharian.edit')->can('slipgaji.edit');
            Route::get('/slipgajiharian/{kode_slip}/show', 'show')->name('slipgajiharian.show')->can('slipgaji.index');
            Route::post('/slipgajiharian/cetak', 'cetak')->name('slipgajiharian.cetak')->can('slipgaji.index');
            Route::put('/slipgajiharian/{kode_slip}/update', 'update')->name('slipgajiharian.update')->can('slipgaji.edit');
            Route::delete('/slipgajiharian/{kode_slip}/delete', 'destroy')->name('slipgajiharian.delete')->can('slipgaji.delete');
        }
        );

        Route::controller(PinjamanController::class)->group(function () {
            Route::get('/pinjaman', 'index')->name('pinjaman.index')->can('pinjaman.index');
            Route::get('/pinjaman/create', 'create')->name('pinjaman.create')->can('pinjaman.create');
            Route::post('/pinjaman', 'store')->name('pinjaman.store')->can('pinjaman.create');
            Route::post('/pinjaman/preview', 'preview')->name('pinjaman.preview')->can('pinjaman.create');
            Route::get('/pinjaman/generate-pembayaran', 'generatePembayaran')->name('pinjaman.generate')->can('pinjaman.generate');
            Route::post('/pinjaman/generate-pembayaran', 'prosesGeneratePembayaran')->name('pinjaman.prosesgenerate')->can('pinjaman.generate');
            Route::get('/pinjaman/{id}/show', 'show')->name('pinjaman.show')->can('pinjaman.index');
            Route::get('/pinjaman/{id}/edit', 'edit')->name('pinjaman.edit')->can('pinjaman.edit');
            Route::put('/pinjaman/{id}', 'update')->name('pinjaman.update')->can('pinjaman.edit');
            Route::delete('/pinjaman/{id}/delete', 'destroy')->name('pinjaman.delete')->can('pinjaman.delete');
            Route::get('/pinjaman/{id}/pembayaran', 'createPembayaranManual')->name('pinjaman.pembayaran')->can('pinjaman.pembayaran');
            Route::post('/pinjaman/{id}/pembayaran', 'storePembayaranManual')->name('pinjaman.storepembayaran')->can('pinjaman.pembayaran');
            Route::delete('/pinjaman/pembayaran/{id}/delete', 'destroyPembayaran')->name('pinjaman.deletepembayaran')->can('pinjaman.pembayaran');
            Route::get('/pinjaman/history/{id}', 'showHistoryGenerate')->name('pinjaman.history.show')->can('pinjaman.generate');
            Route::delete('/pinjaman/history/{id}', 'destroyHistoryGenerate')->name('pinjaman.history.delete')->can('pinjaman.generate');
        }
        );

        Route::controller(HariliburController::class)->group(function () {
            Route::get('/harilibur', 'index')->name('harilibur.index')->can('harilibur.index');
            Route::get('/harilibur/create', 'create')->name('harilibur.create')->can('harilibur.create');
            Route::post('/harilibur', 'store')->name('harilibur.store')->can('harilibur.create');
            Route::get('/harilibur/{kode_libur}/edit', 'edit')->name('harilibur.edit')->can('harilibur.edit');
            Route::put('/harilibur/{kode_libur}', 'update')->name('harilibur.update')->can('harilibur.edit');
            Route::delete('/harilibur/{kode_libur}/delete', 'destroy')->name('harilibur.delete')->can('harilibur.delete');
            Route::get('/harilibur/{kode_libur}/aturharilibur', 'aturharilibur')->name('harilibur.aturharilibur')->can('harilibur.setharilibur');
            Route::get('/harilibur/{kode_libur}/getkaryawanlibur', 'getkaryawanlibur')->name('harilibur.getkaryawanlibur')->can('harilibur.setharilibur');
            Route::get('/harilibur/{kode_libur}/aturkaryawan', 'aturkaryawan')->name('harilibur.aturkaryawan')->can('harilibur.setharilibur');
            Route::post('/harilibur/getkaryawan', 'getkaryawan')->name('harilibur.getkaryawan')->can('harilibur.setharilibur');
            Route::post('/harilibur/updateliburkaryawan', 'updateliburkaryawan')->name('harilibur.updateliburkaryawan')->can('harilibur.setharilibur');
            Route::post('/harilibur/deletekaryawanlibur', 'deletekaryawanlibur')->name('harilibur.deletekaryawanlibur')->can('harilibur.setharilibur');
            Route::post('/harilibur/tambahkansemua', 'tambahkansemua')->name('harilibur.tambahkansemua')->can('harilibur.setharilibur');
            Route::post('/harilibur/batalkansemua', 'batalkansemua')->name('harilibur.batalkansemua')->can('harilibur.setharilibur');
        }
        );

        Route::controller(PresensiController::class)->group(function () {
            Route::get('/presensi', 'index')->name('presensi.index')->can('presensi.index');
            Route::get('/presensi/histori', 'histori')->name('presensi.histori')->can('presensi.index');
            Route::get('/presensi/create', 'create')->name('presensi.create')->can('presensi.create');
            Route::post('/presensi', 'store')->name('presensi.store')->can('presensi.create');
            Route::get('/presensi/check-approval-izin', 'checkApprovalIzinAPI')->name('presensi.check-approval-izin')->can('presensi.create');
            Route::post('/presensi/edit', 'edit')->name('presensi.edit')->can('presensi.edit');
            Route::post('/presensi/update', 'update')->name('presensi.update')->can('presensi.edit');
            Route::delete('/presensi/{id}/delete', 'destroy')->name('presensi.delete')->can('presensi.delete');
            Route::get('/presensi/{id}/{status}/show', 'show')->name('presensi.show');
            Route::post('/presensi/edit', 'edit')->name('presensi.edit')->can('presensi.edit');

            Route::post('/presensi/getdatamesin', 'getdatamesin')->name('presensi.getdatamesin');
            Route::post('/presensi/{pin}/{status_scan}/updatefrommachine', 'updatefrommachine')->name('presensi.updatefrommachine');
        }
        );

        Route::controller(JamkerjabydeptController::class)->group(function () {
            Route::get('/jamkerjabydept', 'index')->name('jamkerjabydept.index')->can('jamkerjabydept.index');
            Route::get('/jamkerjabydept/create', 'create')->name('jamkerjabydept.create')->can('jamkerjabydept.create');
            Route::post('/jamkerjabydept', 'store')->name('jamkerjabydept.store')->can('jamkerjabydept.create');
            Route::get('/jamkerjabydept/{kode_jk_dept}/edit', 'edit')->name('jamkerjabydept.edit')->can('jamkerjabydept.edit');
            Route::put('/jamkerjabydept/{kode_jk_dept}', 'update')->name('jamkerjabydept.update')->can('jamkerjabydept.edit');
            Route::delete('/jamkerjabydept/{kode_jk_dept}/delete', 'destroy')->name('jamkerjabydept.delete')->can('jamkerjabydept.delete');
        }
        );

        Route::controller(IzinabsenController::class)->group(function () {
            Route::get('/izinabsen', 'index')->name('izinabsen.index')->can('izinabsen.index');
            Route::get('/izinabsen/create', 'create')->name('izinabsen.create')->can('izinabsen.create');
            Route::post('/izinabsen', 'store')->name('izinabsen.store')->can('izinabsen.create');
            Route::get('/izinabsen/{kode_izin}/approve', 'approve')->name('izinabsen.approve')->can('izinabsen.approve');
            Route::delete('/izinabsen/{kode_izin}/cancelapprove', 'cancelapprove')->name('izinabsen.cancelapprove')->can('izinabsen.approve');
            Route::post('/izinabsen/{kode_izin}/storeapprove', 'storeapprove')->name('izinabsen.storeapprove')->can('izinabsen.approve');
            Route::get('/izinabsen/{id}/edit', 'edit')->name('izinabsen.edit')->can('izinabsen.edit');
            Route::put('/izinabsen/{id}', 'update')->name('izinabsen.update')->can('izinabsen.edit');
            Route::get('/izinabsen/{kode_izin}/show', 'show')->name('izinabsen.show')->can('izinabsen.index');
            Route::delete('/izinabsen/{id}/delete', 'destroy')->name('izinabsen.delete')->can('izinabsen.delete');
        }
        );

        Route::controller(IzinsakitController::class)->group(function () {
            Route::get('/izinsakit', 'index')->name('izinsakit.index')->can('izinsakit.index');
            Route::get('/izinsakit/create', 'create')->name('izinsakit.create')->can('izinsakit.create');
            Route::post('/izinsakit', 'store')->name('izinsakit.store')->can('izinsakit.create');
            Route::get('/izinsakit/{kode_izin_sakit}/edit', 'edit')->name('izinsakit.edit')->can('izinsakit.edit');
            Route::put('/izinsakit/{kode_izin_sakit}', 'update')->name('izinsakit.update')->can('izinsakit.edit');
            Route::get('/izinsakit/{kode_izin_sakit}/show', 'show')->name('izinsakit.show')->can('izinsakit.index');
            Route::delete('/izinsakit/{kode_izin_sakit}/delete', 'destroy')->name('izinsakit.delete')->can('izinsakit.delete');

            Route::get('/izinsakit/{kode_izin_sakit}/approve', 'approve')->name('izinsakit.approve')->can('izinsakit.approve');
            Route::delete('/izinsakit/{kode_izin_sakit}/cancelapprove', 'cancelapprove')->name('izinsakit.cancelapprove')->can('izinsakit.approve');
            Route::post('/izinsakit/{kode_izin_sakit}/storeapprove', 'storeapprove')->name('izinsakit.storeapprove')->can('izinsakit.approve');
        }
        );


        Route::controller(IzincutiController::class)->group(function () {
            Route::get('/izincuti', 'index')->name('izincuti.index')->can('izincuti.index');
            Route::get('/izincuti/create', 'create')->name('izincuti.create')->can('izincuti.create');
            Route::post('/izincuti', 'store')->name('izincuti.store')->can('izincuti.create');
            Route::get('/izincuti/{kode_izin_cuti}/edit', 'edit')->name('izincuti.edit')->can('izincuti.edit');
            Route::put('/izincuti/{kode_izin_cuti}', 'update')->name('izincuti.update')->can('izincuti.edit');
            Route::get('/izincuti/{kode_izin_cuti}/show', 'show')->name('izincuti.show')->can('izincuti.index');
            Route::delete('/izincuti/{kode_izin_cuti}/delete', 'destroy')->name('izincuti.delete')->can('izincuti.delete');

            Route::get('/izincuti/{kode_izin_cuti}/approve', 'approve')->name('izincuti.approve')->can('izincuti.approve');
            Route::delete('/izincuti/{kode_izin_cuti}/cancelapprove', 'cancelapprove')->name('izincuti.cancelapprove')->can('izincuti.approve');
            Route::post('/izincuti/{kode_izin_cuti}/storeapprove', 'storeapprove')->name('izincuti.storeapprove')->can('izincuti.approve');
            Route::get('/izincuti/getsisaharicuti', 'getsisaharicuti')->name('izincuti.getsisaharicuti');
        }
        );

        Route::controller(IzindinasController::class)->group(function () {
            Route::get('/izindinas', 'index')->name('izindinas.index')->can('izindinas.index');
            Route::get('/izindinas/create', 'create')->name('izindinas.create')->can('izindinas.create');
            Route::post('/izindinas', 'store')->name('izindinas.store')->can('izindinas.create');
            Route::get('/izindinas/{kode_izin_cuti}/edit', 'edit')->name('izindinas.edit')->can('izindinas.edit');
            Route::put('/izindinas/{kode_izin_cuti}', 'update')->name('izindinas.update')->can('izindinas.edit');
            Route::get('/izindinas/{kode_izin_cuti}/show', 'show')->name('izindinas.show')->can('izindinas.index');
            Route::delete('/izindinas/{kode_izin_cuti}/delete', 'destroy')->name('izindinas.delete')->can('izindinas.delete');

            Route::get('/izindinas/{kode_izin_cuti}/approve', 'approve')->name('izindinas.approve')->can('izindinas.approve');
            Route::delete('/izindinas/{kode_izin_cuti}/cancelapprove', 'cancelapprove')->name('izindinas.cancelapprove')->can('izindinas.approve');
            Route::post('/izindinas/{kode_izin_cuti}/storeapprove', 'storeapprove')->name('izindinas.storeapprove')->can('izindinas.approve');
        }
        );

        Route::controller(LemburController::class)->group(function () {
            Route::get('/lembur', 'index')->name('lembur.index')->can('lembur.index');
            Route::get('/lembur/create', 'create')->name('lembur.create')->can('lembur.create');
            Route::post('/lembur', 'store')->name('lembur.store')->can('lembur.create');
            Route::get('/lembur/{id}/edit', 'edit')->name('lembur.edit')->can('lembur.edit');
            Route::put('/lembur/{id}', 'update')->name('lembur.update')->can('lembur.edit');
            Route::delete('/lembur/{id}/delete', 'destroy')->name('lembur.delete')->can('lembur.delete');
            Route::get('/lembur/{id}/approve', 'approve')->name('lembur.approve')->can('lembur.approve');
            Route::get('/lembur/{id}/show', 'show')->name('lembur.show')->can('lembur.index');
            Route::delete('/lembur/{id}/cancelapprove', 'cancelapprove')->name('lembur.cancelapprove')->can('lembur.approve');
            Route::post('/lembur/{id}/storeapprove', 'storeapprove')->name('lembur.storeapprove')->can('lembur.approve');
            Route::get('/lembur/{id}/createpresensi', 'createpresensi')->name('lembur.createpresensi');
            Route::post('/lembur/storepresensi', 'storepresensi')->name('lembur.storepresensi');
        }
        );

        Route::controller(PengajuanizinController::class)->group(function () {
            Route::get('/pengajuanizin', 'index')->name('pengajuanizin.index');
        }
        );

        Route::controller(PelanggaranController::class)->group(function () {
            Route::get('/pelanggaran', 'index')->name('pelanggaran.index')->can('pelanggaran.index');
            Route::get('/pelanggaran/create', 'create')->name('pelanggaran.create')->can('pelanggaran.create');
            Route::post('/pelanggaran', 'store')->name('pelanggaran.store')->can('pelanggaran.create');
            Route::get('/pelanggaran/{no_sp}/show', 'show')->name('pelanggaran.show')->can('pelanggaran.index');
            Route::get('/pelanggaran/{no_sp}/edit', 'edit')->name('pelanggaran.edit')->can('pelanggaran.edit');
            Route::put('/pelanggaran/{no_sp}', 'update')->name('pelanggaran.update')->can('pelanggaran.edit');
            Route::delete('/pelanggaran/{no_sp}/delete', 'destroy')->name('pelanggaran.delete')->can('pelanggaran.delete');
            Route::get('/pelanggaran/{no_sp}/print', 'print')->name('pelanggaran.print')->can('pelanggaran.index');
        }
        );

        Route::controller(PresensiistirahatController::class)->group(function () {
            Route::get('/presensiistirahat/create', 'create')->name('presensiistirahat.create');
            Route::post('/presensiistirahat', 'store')->name('presensiistirahat.store');
        }
        );

        Route::controller(PresensiistirahatlemburController::class)->group(function () {
            Route::get('/presensiistirahatlembur/create', 'create')->name('presensiistirahatlembur.create');
            Route::post('/presensiistirahatlembur', 'store')->name('presensiistirahatlembur.store');
        }
        );

        // Route::controller(PresensispcController::class)->group(function () {
        //     Route::get('/presensispc/create', 'create')->name('presensispc.create');
        //     Route::post('/presensispc/unlock', 'unlock')->name('presensispc.unlock');
        //     Route::post('/presensispc/getKaryawanName', 'getKaryawanName')->name('presensispc.getKaryawanName');
        //     Route::post('/presensispc', 'store')->name('presensispc.store');
        // });


        Route::middleware('role:super admin')->controller(GeneralsettingController::class)->group(function () {
            Route::get('/generalsetting', 'index')->name('generalsetting.index')->can('generalsetting.index');
            Route::put('/generalsetting/{id}', 'update')->name('generalsetting.update')->can('generalsetting.edit');
        }
        );

        Route::middleware('role:super admin')->controller(LemburaturanController::class)->group(function () {
            Route::get('/lemburaturan', 'index')->name('lemburaturan.index');
            Route::get('/lemburaturan/create', 'create')->name('lemburaturan.create');
            Route::post('/lemburaturan', 'store')->name('lemburaturan.store');
            Route::get('/lemburaturan/edit', 'edit')->name('lemburaturan.edit');
            Route::put('/lemburaturan/{id}', 'update')->name('lemburaturan.update');
            Route::delete('/lemburaturan/{id}', 'destroy')->name('lemburaturan.delete');
        });

        // PWA Icon Generator Routes
        Route::controller(IconGeneratorController::class)->group(function () {
            Route::post('/generate-pwa-icons', 'generate')->name('pwa.generate-icons');
            Route::get('/preview-pwa-icons', 'preview')->name('pwa.preview-icons');
            Route::delete('/clear-pwa-icons', 'clear')->name('pwa.clear-icons');
        }
        );

        Route::controller(DendaController::class)->group(function () {
            Route::get('/denda', 'index')->name('denda.index')->can('denda.index');
            Route::get('/denda/create', 'create')->name('denda.create')->can('denda.create');
            Route::post('/denda', 'store')->name('denda.store')->can('denda.create');
            Route::get('/denda/{id}/edit', 'edit')->name('denda.edit')->can('denda.edit');
            Route::put('/denda/{id}', 'update')->name('denda.update')->can('denda.edit');
            Route::delete('/denda/{id}/delete', 'destroy')->name('denda.delete')->can('denda.delete');
        }
        );

        Route::controller(LaporanController::class)->group(function () {
            Route::get('/laporan/presensi', 'presensi')->name('laporan.presensi')->can('laporan.presensi');
            Route::post('/laporan/cetakpresensi', 'cetakpresensi')->name('laporan.cetakpresensi')->can('laporan.presensi');
            Route::post('/laporan/kuncilaporan', 'kunciLaporan')->name('laporan.kuncilaporan')->can('laporan.presensi');
            Route::post('/laporan/batalkankuncilaporan', 'batalkanKunciLaporan')->name('laporan.batalkankuncilaporan')->can('laporan.presensi');
            Route::get('/laporan/cetakslipgaji', 'cetakpresensi');
            Route::get('/laporan/kehadiran', 'kehadiran')->name('laporan.kehadiran')->can('laporan.kehadiran');
            Route::get('/laporan/kehadiran/cetak', 'kehadiranCetak')->name('laporan.kehadiran.cetak')->can('laporan.kehadiran');
            Route::get('/laporan/kehadiran/export', 'kehadiranExport')->name('laporan.kehadiran.export')->can('laporan.kehadiran');
            Route::get('/laporan/kehadiran/api/karyawan', 'getKaryawanByCabang')->name('laporan.kehadiran.api.karyawan');
            Route::get('/laporan/cuti', 'cuti')->name('laporan.cuti')->can('laporan.cuti');
            Route::post('/laporan/cetakcuti', 'cetakcuti')->name('laporan.cetakcuti')->can('laporan.cuti');
            Route::get('/laporan/jadwal', 'jadwal')->name('laporan.jadwal')->can('laporan.jadwal');
            Route::post('/laporan/cetakjadwal', 'cetakjadwal')->name('laporan.cetakjadwal')->can('laporan.jadwal');
            Route::get('/laporan/{nik}/{dari}/{sampai}/lemburdetail', 'lemburdetail')->name('laporan.lemburdetail');
        }
        );

        Route::controller(FacerecognitionController::class)->group(function () {
            Route::post('/facerecognition/hapus-semua/{nik}', 'destroyAll')->name('facerecognition.destroyAll')->can('karyawan.edit');
            Route::get('/facerecognition/{nik}/create', 'create')->name('facerecognition.create');
            Route::get('/karyawan/daftarkan-wajah', 'createKaryawan')->name('facerecognition.karyawan.create');
            Route::get('/karyawan/preview-wajah', 'previewKaryawan')->name('facerecognition.karyawan.preview');
            Route::post('/karyawan/hapus-wajah', 'destroyAllKaryawan')->name('facerecognition.karyawan.destroyAll');
            Route::post('/facerecognition/store', 'store')->name('facerecognition.store');
            Route::delete('/facerecognition/{id}/delete', 'destroy')->name('facerecognition.delete');

            Route::get('/facerecognition/getwajah', 'getWajah')->name('facerecognition.getwajah');
        }
        );

        Route::middleware('role:super admin')->controller(WagatewayController::class)->group(function () {
            Route::get('/wagateway', 'index')->name('wagateway.index');
            Route::get('/wagateway/messages', 'messages')->name('wagateway.messages');
            Route::post('/wagateway/add-device', 'addDevice')->name('wagateway.add-device');
            Route::post('/wagateway/toggle-device-status/{id}', 'toggleDeviceStatus')->name('wagateway.toggle-device-status');
            Route::post('/wagateway/generate-qr', 'generateQR')->name('wagateway.generate-qr');
            Route::post('/wagateway/check-device-status', 'checkDeviceStatus')->name('wagateway.check-device-status');
            Route::post('/wagateway/test-send-message', 'testSendMessage')->name('wagateway.test-send-message');
            Route::post('/wagateway/disconnect-device', 'disconnectDevice')->name('wagateway.disconnect-device');
            Route::post('/wagateway/fetch-groups', 'fetchGroups')->name('wagateway.fetch-groups');
            Route::delete('/wagateway/delete-device/{id}', 'deleteDevice')->name('wagateway.delete-device');
        }
        );

        // Bersihkan Foto Routes
        Route::middleware('role:super admin')->controller(BersihkanfotoController::class)->group(function () {
            Route::get('/bersihkanfoto', 'index')->name('bersihkanfoto.index')->can('bersihkanfoto.index');
            Route::post('/bersihkanfoto', 'destroy')->name('bersihkanfoto.destroy')->can('bersihkanfoto.delete');
        }
        );

        // User Login Log Routes
        Route::middleware('role:super admin')->controller(UserloginlogController::class)->group(function () {
            Route::get('/userloginlog', 'index')->name('userloginlog.index')->can('userloginlog.index');
        }
        );

        // Reset Data Routes
        Route::middleware('role:super admin')->controller(ResetDataController::class)->group(function () {
            Route::get('/resetdata', 'index')->name('resetdata.index');
            Route::post('/resetdata', 'reset')->name('resetdata.reset');
        }
        );

        // Tracking Presensi Routes
        Route::middleware('permission:trackingpresensi.index')->controller(TrackingPresensiController::class)->group(function () {
            Route::get('/trackingpresensi', 'index')->name('trackingpresensi.index');
            Route::get('/trackingpresensi/getData', 'getData')->name('trackingpresensi.getData');
        }
        );

        // Monitoring Lokasi Routes (Live Location Tracking)
        Route::middleware('permission:presensi.index')->controller(MonitoringLokasiController::class)->group(function () {
            Route::get('/monitoring-lokasi', 'index')->name('monitoring-lokasi.index');
            Route::get('/monitoring-lokasi/getData', 'getData')->name('monitoring-lokasi.getData');
            Route::get('/monitoring-lokasi/getSpyEmployees', 'getSpyEmployees')->name('monitoring-lokasi.getSpyEmployees');
            Route::get('/monitoring-lokasi/getLocationHistory', 'getLocationHistory')->name('monitoring-lokasi.getLocationHistory');
        }
        );

        // Presensi Status Check for Live Location Tracking
        Route::middleware('auth')->controller(PresensiController::class)->group(function () {
            Route::get('/presensi/check-status', 'checkStatus')->name('presensi.checkStatus');
        }
        );

        // Monitoring Master Routes (Manage Spy Employees)
        Route::controller(\App\Http\Controllers\MonitoringMasterController::class)->group(function () {
            Route::get('/monitoring-master', 'index')->name('monitoring-master.index')->can('presensi.index');
            Route::get('/monitoring-master/search', 'search')->name('monitoring-master.search')->can('presensi.index');
            Route::post('/monitoring-master/add', 'add')->name('monitoring-master.add')->can('presensi.index');
            Route::post('/monitoring-master/remove', 'remove')->name('monitoring-master.remove')->can('presensi.index');
        }
        );

        // Save Location Route (for employees with spy=1 to share their location)
        Route::middleware('auth')->controller(MonitoringLokasiController::class)->group(function () {
            Route::post('/monitoring-lokasi/saveLocation', 'saveLocation')->name('monitoring-lokasi.saveLocation');
        }
        );

        // Aktivitas Karyawan Routes
        Route::controller(AktivitasKaryawanController::class)->group(function () {
            Route::get('/aktivitaskaryawan', 'index')->name('aktivitaskaryawan.index')->can('aktivitaskaryawan.index');
            Route::get('/aktivitaskaryawan/create', 'create')->name('aktivitaskaryawan.create')->can('aktivitaskaryawan.create');
            Route::post('/aktivitaskaryawan', 'store')->name('aktivitaskaryawan.store')->can('aktivitaskaryawan.create');
            Route::get('/aktivitaskaryawan/{aktivitaskaryawan}', 'show')->name('aktivitaskaryawan.show')->can('aktivitaskaryawan.index');
            Route::get('/aktivitaskaryawan/{aktivitaskaryawan}/edit', 'edit')->name('aktivitaskaryawan.edit')->can('aktivitaskaryawan.edit');
            Route::put('/aktivitaskaryawan/{aktivitaskaryawan}', 'update')->name('aktivitaskaryawan.update')->can('aktivitaskaryawan.edit');
            Route::delete('/aktivitaskaryawan/{aktivitaskaryawan}', 'destroy')->name('aktivitaskaryawan.destroy')->can('aktivitaskaryawan.delete');
            Route::get('/aktivitaskaryawan/export/pdf', 'exportPdf')->name('aktivitaskaryawan.export.pdf')->can('aktivitaskaryawan.index');
        }
        );

        // Kunjungan Routes
        Route::controller(KunjunganController::class)->group(function () {
            Route::get('/kunjungan', 'index')->name('kunjungan.index')->can('kunjungan.index');
            Route::get('/kunjungan/create', 'create')->name('kunjungan.create')->can('kunjungan.create');
            Route::post('/kunjungan', 'store')->name('kunjungan.store')->can('kunjungan.create');
            Route::get('/kunjungan/{kunjungan}', 'show')->name('kunjungan.show')->can('kunjungan.index');
            Route::get('/kunjungan/{kunjungan}/edit', 'edit')->name('kunjungan.edit')->can('kunjungan.edit');
            Route::put('/kunjungan/{kunjungan}', 'update')->name('kunjungan.update')->can('kunjungan.edit');
            Route::delete('/kunjungan/{kunjungan}', 'destroy')->name('kunjungan.destroy')->can('kunjungan.delete');
            Route::get('/kunjungan/export/pdf', 'exportPdf')->name('kunjungan.export.pdf')->can('kunjungan.index');
        }
        );

        // Tracking Kunjungan Routes
        Route::controller(TrackingKunjunganController::class)->group(function () {
            Route::get('/tracking-kunjungan', 'index')->name('tracking-kunjungan.index')->can('kunjungan.index');
        }
        );

        // Update Routes (Hanya untuk Super Admin)
        Route::middleware('role:super admin')->controller(UpdateController::class)->group(function () {
            Route::get('/update', 'index')->name('update.index');
            Route::post('/update/check', 'checkUpdate')->name('update.check');
            Route::post('/update/{version}/download', 'downloadUpdate')->name('update.download');
            Route::post('/update/{version}/install', 'installUpdate')->name('update.install');
            Route::post('/update/{version}/update-now', 'updateNow')->name('update.update-now');
            Route::get('/update/history', 'history')->name('update.history');
            Route::get('/update/log/{id}', 'showLog')->name('update.log');
            Route::get('/update/progress/{id}', 'getProgress')->name('update.progress');
        }
        );

        // Admin Update Management (CRUD Update)
        Route::middleware('role:super admin')->prefix('admin/update')->name('admin.update.')->controller(UpdateManagementController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{id}', 'show')->name('show');
            Route::get('/{id}/edit', 'edit')->name('edit');
            Route::put('/{id}', 'update')->name('update');
            Route::delete('/{id}', 'destroy')->name('destroy');
            Route::post('/{id}/toggle-active', 'toggleActive')->name('toggle-active');
        }
        );

        // Pengumuman Routes
        Route::controller(App\Http\Controllers\PengumumanController::class)->group(function () {
            Route::get('/pengumuman', 'index')->name('pengumuman.index');
            Route::get('/pengumuman/create', 'create')->name('pengumuman.create');
            Route::post('/pengumuman', 'store')->name('pengumuman.store');
            Route::get('/pengumuman/{id}/show', 'show')->name('pengumuman.show');
            Route::delete('/pengumuman/{id}', 'delete')->name('pengumuman.delete');
        }
        );

        // Approval Layer
        // Approval Layer
        Route::get('/approvallayer/group/edit', [App\Http\Controllers\ApprovalLayerController::class , 'editGroup'])->name('approvallayer.editGroup');
        Route::post('/approvallayer/group/update', [App\Http\Controllers\ApprovalLayerController::class , 'updateGroup'])->name('approvallayer.updateGroup');
        Route::delete('/approvallayer/group/destroy', [App\Http\Controllers\ApprovalLayerController::class , 'destroyGroup'])->name('approvallayer.destroyGroup');
        Route::resource('approvallayer', App\Http\Controllers\ApprovalLayerController::class);

        // Mutasi Karyawan
        Route::resource('mutasi', App\Http\Controllers\MutasiKaryawanController::class);
        Route::get('/mutasi/{nik}/getKaryawan', [App\Http\Controllers\MutasiKaryawanController::class , 'getKaryawan'])->name('mutasi.getKaryawan');
    });


Route::get('/createrolepermission', function () {

    try {
        Role::create(['name' => 'super admin']);
        // Permission::create(['name' => 'view-karyawan']);
        // Permission::create(['name' => 'view-departemen']);
        echo "Sukses";
    }
    catch (\Exception $e) {
        echo "Error";
    }
});

Route::group(['middleware' => ['auth']], function () { // Removed userAkses:admin as it doesn't exist. Permissions handle access control.
    Route::group(['middleware' => ['permission:kpi.period.index']], function () {
            Route::get('/kpi/periods', [KpiPeriodController::class , 'index'])->name('kpi.periods.index');
            Route::get('/kpi/periods/create', [KpiPeriodController::class , 'create'])->name('kpi.periods.create');
            Route::post('/kpi/periods/store', [KpiPeriodController::class , 'store'])->name('kpi.periods.store');
            Route::post('/kpi/periods/edit', [KpiPeriodController::class , 'edit'])->name('kpi.periods.edit');
            Route::post('/kpi/periods/{id}/update', [KpiPeriodController::class , 'update'])->name('kpi.periods.update');
            Route::delete('/kpi/periods/{id}/delete', [KpiPeriodController::class , 'destroy'])->name('kpi.periods.delete');
        }
        );

        Route::group(['middleware' => ['permission:kpi.indicator.index']], function () {
            Route::get('/kpi/indicators', [KpiIndicatorController::class , 'index'])->name('kpi.indicators.index');
            Route::get('/kpi/indicators/create', [KpiIndicatorController::class , 'create'])->name('kpi.indicators.create');
            Route::post('/kpi/indicators/store', [KpiIndicatorController::class , 'store'])->name('kpi.indicators.store');
            Route::get('/kpi/indicators/{id}/edit', [KpiIndicatorController::class , 'edit'])->name('kpi.indicators.edit');
            Route::put('/kpi/indicators/{id}/update', [KpiIndicatorController::class , 'update'])->name('kpi.indicators.update');
            Route::delete('/kpi/indicators/{id}/delete', [KpiIndicatorController::class , 'destroy'])->name('kpi.indicators.destroy');
        }
        );

        Route::group(['middleware' => ['permission:kpi.employee.index']], function () {
            Route::get('/kpi/transactions', [KpiEmployeeController::class , 'index'])->name('kpi.transactions.index');
            Route::get('/kpi/transactions/{nik}/settarget', [KpiEmployeeController::class , 'settarget'])->name('kpi.transactions.settarget');
            Route::post('/kpi/transactions/store', [KpiEmployeeController::class , 'store'])->name('kpi.transactions.store');
            Route::get('/kpi/transactions/{id}/show', [KpiEmployeeController::class , 'show'])->name('kpi.transactions.show');
            Route::post('/kpi/transactions/{id}/update', [KpiEmployeeController::class , 'update'])->name('kpi.transactions.update');
            Route::post('/kpi/transactions/{id}/approve', [KpiEmployeeController::class , 'approve'])->name('kpi.transactions.approve');
            Route::get('/kpi/transactions/{id}/print', [KpiEmployeeController::class , 'print'])->name('kpi.transactions.print');
            Route::delete('/kpi/transactions/{id}/delete', [KpiEmployeeController::class , 'destroy'])->name('kpi.transactions.delete');
        }
        );

        Route::get('/kpi/myscore', [KpiEmployeeController::class , 'myScore'])->name('kpi.transactions.myscore');
    });
// Ajuan Jadwal Routes
Route::group(['middleware' => ['permission:ajuanjadwal.index']], function () {
    Route::get('/ajuanjadwal', [App\Http\Controllers\AjuanJadwalController::class , 'index'])->name('ajuanjadwal.index');
});

Route::group(['middleware' => ['permission:ajuanjadwal.create']], function () {
    Route::get('/ajuanjadwal/create', [App\Http\Controllers\AjuanJadwalController::class , 'create'])->name('ajuanjadwal.create');
    Route::post('/ajuanjadwal/store', [App\Http\Controllers\AjuanJadwalController::class , 'store'])->name('ajuanjadwal.store');
    Route::delete('/ajuanjadwal/{id}/delete', [App\Http\Controllers\AjuanJadwalController::class , 'destroy'])->name('ajuanjadwal.delete');
});

Route::group(['middleware' => ['permission:ajuanjadwal.approve']], function () {
    Route::post('/ajuanjadwal/{id}/approve', [App\Http\Controllers\AjuanJadwalController::class , 'approve'])->name('ajuanjadwal.approve');
    Route::post('/ajuanjadwal/{id}/reject', [App\Http\Controllers\AjuanJadwalController::class , 'reject'])->name('ajuanjadwal.reject');
    Route::post('/ajuanjadwal/{id}/cancelapprove', [App\Http\Controllers\AjuanJadwalController::class , 'cancelapprove'])->name('ajuanjadwal.cancelapprove');
});
// Route::get('/storage/{path}', function ($path) {
//     return response()->file(storage_path('app/public/' . $path));
// })->where('path', '.*');

// Endpoint khusus untuk menangani mesin fingerprint ADMS / X100C tanpa prefix /api
Route::any('/iclock/cdata', [\App\Http\Controllers\Api\AdmsController::class , 'receiveX100c']);


require __DIR__ . '/auth.php';
