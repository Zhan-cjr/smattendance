<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ResetDataController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return view('utilities.resetdata.index');
    }

    /**
     * Reset all data except users, permissions, and roles
     */
    public function reset(Request $request)
    {
        // Validasi konfirmasi
        if ($request->konfirmasi !== 'RESET DATA') {
            return response()->json([
                'error' => 'Konfirmasi tidak valid. Silakan ketik "RESET DATA" dengan benar.'
            ], 400);
        }

        $deletedTables = [];
        $errors = [];

        try {
            // Nonaktifkan foreign key checks sementara
            // Catatan: TRUNCATE tidak bisa di-rollback, jadi kita tidak menggunakan transaksi
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            // Urutan penghapusan berdasarkan foreign key
            // Tahap 1: Tabel yang memiliki foreign key ke presensi (approve tables)
            $this->deleteTable('presensi_izinabsen_approve', $deletedTables, $errors);
            $this->deleteTable('presensi_izincuti_approve', $deletedTables, $errors);
            $this->deleteTable('presensi_izinsakit_approve', $deletedTables, $errors);
            $this->deleteTable('approvals', $deletedTables, $errors);

            // Tahap 2: Tabel izin yang memiliki foreign key ke presensi dan karyawan
            $this->deleteTable('presensi_izinabsen', $deletedTables, $errors);
            $this->deleteTable('presensi_izincuti', $deletedTables, $errors);
            $this->deleteTable('presensi_izinsakit', $deletedTables, $errors);
            $this->deleteTable('presensi_izindinas', $deletedTables, $errors);

            // Tahap 3: Tabel presensi
            $this->deleteTable('presensi', $deletedTables, $errors);

            // Tahap 4: Tabel detail yang memiliki foreign key ke tabel lain
            $this->deleteTable('karyawan_tunjangan_detail', $deletedTables, $errors);
            $this->deleteTable('karyawan_penyesuaian_gaji_detail', $deletedTables, $errors);
            $this->deleteTable('presensi_jamkerja_bydept_detail', $deletedTables, $errors);
            $this->deleteTable('hari_libur_detail', $deletedTables, $errors);
            $this->deleteTable('grup_detail', $deletedTables, $errors);
            $this->deleteTable('grup_jamkerja_bydate', $deletedTables, $errors);

            // Tahap 5: Tabel yang memiliki foreign key ke karyawan (transaksi)
            $this->deleteTable('aktivitas_karyawan', $deletedTables, $errors);
            $this->deleteTable('kunjungan', $deletedTables, $errors);
            $this->deleteTable('karyawan_wajah', $deletedTables, $errors);
            $this->deleteTable('users_karyawan', $deletedTables, $errors);
            $this->deleteTable('presensi_jamkerja_byday', $deletedTables, $errors);
            $this->deleteTable('presensi_jamkerja_bydate', $deletedTables, $errors);
            $this->deleteTable('pelanggaran', $deletedTables, $errors);
            $this->deleteTable('ajuan_jadwal', $deletedTables, $errors);
            $this->deleteTable('mutasi_karyawan', $deletedTables, $errors);

            // Tahap 5.5: Tabel KPI (Performance)
            $this->deleteTable('kpi_details', $deletedTables, $errors);
            $this->deleteTable('kpi_employees', $deletedTables, $errors);
            $this->deleteTable('kpi_indicator_details', $deletedTables, $errors);
            $this->deleteTable('kpi_indicators', $deletedTables, $errors);
            $this->deleteTable('kpi_periods', $deletedTables, $errors);

            // Tahap 6: Tabel payroll yang memiliki foreign key ke karyawan
            $this->deleteTable('karyawan_tunjangan', $deletedTables, $errors);
            $this->deleteTable('karyawan_gaji_pokok', $deletedTables, $errors);
            $this->deleteTable('karyawan_bpjskesehatan', $deletedTables, $errors);
            $this->deleteTable('karyawan_bpjstenagakerja', $deletedTables, $errors);
            $this->deleteTable('karyawan_penyesuaian_gaji', $deletedTables, $errors);
            $this->deleteTable('slip_gaji', $deletedTables, $errors);
            $this->deleteTable('lembur', $deletedTables, $errors);
            $this->deleteTable('log_absen', $deletedTables, $errors);

            // Tahap 7: Tabel master yang memiliki foreign key ke master lain
            $this->deleteTable('karyawan', $deletedTables, $errors);
            $this->deleteTable('kontrak', $deletedTables, $errors);

            // Tahap 8: Tabel master lainnya
            $this->deleteTable('grup', $deletedTables, $errors);
            $this->deleteTable('departemen', $deletedTables, $errors);
            $this->deleteTable('cabang', $deletedTables, $errors);
            $this->deleteTable('jabatan', $deletedTables, $errors);
            // $this->deleteTable('cuti', $deletedTables, $errors);
            $this->deleteTable('presensi_jamkerja', $deletedTables, $errors);
            $this->deleteTable('jenis_tunjangan', $deletedTables, $errors);
            $this->deleteTable('hari_libur', $deletedTables, $errors);

            // Tahap 9: Tabel konfigurasi
            $this->deleteTable('presensi_jamkerja_bydept', $deletedTables, $errors);

            // Tahap 10: Tabel lainnya yang tidak memiliki foreign key penting
            $this->deleteTable('wamessages', $deletedTables, $errors);
            $this->deleteTable('messages', $deletedTables, $errors);
            $this->deleteTable('devices', $deletedTables, $errors);
            $this->deleteTable('update_logs', $deletedTables, $errors);
            $this->deleteTable('pengumuman', $deletedTables, $errors);

            // Tahap 11: Tabel users (Kecuali Super Admin)
            $superAdminIds = DB::table('model_has_roles')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->where('roles.name', 'super admin')
                ->pluck('model_id');
            
            $userCount = DB::table('users')->whereNotIn('id', $superAdminIds)->count();
            DB::table('users')->whereNotIn('id', $superAdminIds)->delete();
            $deletedTables[] = [
                'table' => 'users (Non-Super Admin)',
                'count' => $userCount
            ];

            // Jangan hapus: roles, permissions, permission_groups, pengaturan_umum, status_kawin, denda, model_has_roles, model_has_permissions, role_has_permissions

            // Aktifkan kembali foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            // Cek apakah ada error
            if (count($errors) > 0) {
                return response()->json([
                    'error' => 'Terjadi kesalahan saat menghapus data: ' . implode(', ', $errors),
                    'deleted_tables' => $deletedTables,
                    'warning' => 'Beberapa tabel mungkin sudah terhapus sebelum terjadi error.'
                ], 500);
            }

            return response()->json([
                'success' => 'Berhasil mereset semua data. Total tabel yang dihapus: ' . count($deletedTables),
                'deleted_tables' => $deletedTables
            ]);
        } catch (\Exception $e) {
            // Aktifkan kembali foreign key checks jika terjadi error
            try {
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            } catch (\Exception $ex) {
                // Ignore
            }
            
            Log::error('Reset Data Error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'deleted_tables' => $deletedTables,
                'warning' => count($deletedTables) > 0 ? 'Beberapa tabel mungkin sudah terhapus sebelum terjadi error.' : null
            ], 500);
        }
    }

    /**
     * Delete table data safely
     */
    private function deleteTable($tableName, &$deletedTables, &$errors)
    {
        try {
            if (DB::getSchemaBuilder()->hasTable($tableName)) {
                $count = DB::table($tableName)->count();
                // Gunakan DELETE instead of TRUNCATE untuk kompatibilitas dengan transaksi
                // Tapi karena foreign key checks sudah dinonaktifkan, kita bisa gunakan TRUNCATE untuk performa lebih baik
                DB::statement("TRUNCATE TABLE `{$tableName}`");
                $deletedTables[] = [
                    'table' => $tableName,
                    'count' => $count
                ];
            }
        } catch (\Exception $e) {
            $errors[] = "$tableName: " . $e->getMessage();
            Log::error("Error deleting table $tableName: " . $e->getMessage());
        }
    }
}

